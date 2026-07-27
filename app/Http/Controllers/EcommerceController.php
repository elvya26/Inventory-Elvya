<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\CrmCustomer;
use App\Services\KiriminajaService;
use App\Services\MinioService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EcommerceController extends Controller
{
    protected KiriminajaService $kiriminaja;
    protected MinioService $minio;

    public function __construct(KiriminajaService $kiriminaja, MinioService $minio)
    {
        $this->kiriminaja = $kiriminaja;
        $this->minio = $minio;
    }

    public function index(Request $request): View
    {
        $items = Item::query()
            ->when($request->search, function ($query, string $search) {
                $query->where('name', 'ilike', "%{$search}%")
                    ->orWhere('category', 'ilike', "%{$search}%");
            })
            ->where('current_stock', '>', 0)
            ->orderBy('name')
            ->paginate(12);

        return view('ecommerce.index', compact('items'));
    }

    public function showLogin(Request $request)
    {
        if ($request->session()->has('user_id')) {
            return redirect()->route('ecommerce.index');
        }

        return view('ecommerce.auth.login');
    }

    public function show(Item $item): View
    {
        return view('ecommerce.show', compact('item'));
    }

    public function cart(): View
    {
        $cart = session()->get('cart', []);
        return view('ecommerce.cart', compact('cart'));
    }

    public function addToCart(Request $request, Item $item): RedirectResponse
    {
        $cart = session()->get('cart', []);
        $qty = (int) $request->input('quantity', 1);

        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity'] += $qty;
        } else {
            $cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => $item->price,
                'unit' => $item->unit,
                'image_path' => $item->image_path,
                'quantity' => $qty,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('ecommerce.cart')->with('status', "{$item->name} berhasil ditambahkan ke keranjang.");
    }

    public function updateCart(Request $request): RedirectResponse
    {
        $cart = session()->get('cart', []);
        $action = $request->input('action');
        $itemId = $request->input('item_id');

        if ($itemId && isset($cart[$itemId])) {
            if ($action === 'remove') {
                unset($cart[$itemId]);
            } elseif ($action === 'update') {
                $qty = (int) $request->input('quantity', 1);
                if ($qty <= 0) {
                    unset($cart[$itemId]);
                } else {
                    $cart[$itemId]['quantity'] = $qty;
                }
            }
            session()->put('cart', $cart);
        }

        return redirect()->route('ecommerce.cart')->with('status', 'Keranjang berhasil diperbarui.');
    }

    public function checkout(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('ecommerce.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // Get shipping rates estimation (simulated destination)
        $shippingRates = $this->kiriminaja->getShippingRates('Sleman, DI Yogyakarta');

        return view('ecommerce.checkout', compact('cart', 'shippingRates'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('ecommerce.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $request->validate([
            'customer_name' => ['required', 'string', 'max:160'],
            'customer_email' => ['required', 'email', 'max:160'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string'],
            'shipping_courier' => ['required', 'string'],
        ]);

        // Get shipping cost
        $rates = $this->kiriminaja->getShippingRates('Sleman, DI Yogyakarta');
        $selectedCourier = $request->input('shipping_courier');
        $shippingCost = 0;
        foreach ($rates as $rate) {
            if ($rate['courier'] === $selectedCourier) {
                $shippingCost = $rate['cost'];
                break;
            }
        }

        // Calculate total amount
        $itemsSubtotal = 0;
        foreach ($cart as $cartItem) {
            $itemsSubtotal += $cartItem['price'] * $cartItem['quantity'];
        }
        $totalAmount = $itemsSubtotal + $shippingCost;

        // Create Order
        $userId = session()->get('user_id');
        $orderNumber = 'ORD-' . strtoupper(Str::random(10));

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => $orderNumber,
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_phone' => $request->input('customer_phone'),
            'shipping_address' => $request->input('shipping_address'),
            'shipping_courier' => $selectedCourier,
            'shipping_cost' => $shippingCost,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);

        // Create Order Items
        foreach ($cart as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $cartItem['id'],
                'quantity' => $cartItem['quantity'],
                'price' => $cartItem['price'],
            ]);
        }

        // Generate Virtual Account Number
        $vaBanks = ['bca' => '880', 'mandiri' => '896', 'bni' => '827', 'bri' => '802'];
        $bank = $request->input('payment_bank', 'bca');
        $prefix = $vaBanks[$bank] ?? '888';
        $vaNumber = $prefix . mt_rand(100000000000, 999999999999);

        // Create Payment
        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'Virtual Account',
            'bank_name' => strtoupper($bank),
            'va_number' => $vaNumber,
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);

        // Sync with CRM database
        CrmCustomer::updateOrCreate(
            ['email' => $order->customer_email],
            [
                'user_id' => $userId,
                'name' => $order->customer_name,
                'phone' => $order->customer_phone,
            ]
        );

        // Clear cart
        session()->forget('cart');

        return redirect()->route('ecommerce.payment', $order)->with('status', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.');
    }

    public function payment(Order $order): View
    {
        $order->load(['payment', 'items.item']);
        $dokuUrl = app(\App\Services\DokuService::class)->getPaymentUrl($order);
        return view('ecommerce.payment', compact('order', 'dokuUrl'));
    }

    public function customerPortal(): View
    {
        $userId = session()->get('user_id');
        $orders = collect();
        $crmCustomer = null;

        if ($userId) {
            $orders = Order::where('user_id', $userId)->with(['payment', 'items.item'])->latest()->get();
            $userEmail = \App\Models\User::find($userId)?->email;
            if ($userEmail) {
                $crmCustomer = CrmCustomer::where('email', $userEmail)->first();
            }
        }

        // Track active shipments
        $trackingData = [];
        foreach ($orders as $order) {
            if ($order->waybill) {
                $trackingData[$order->id] = $this->kiriminaja->trackShipment($order->waybill);
            }
        }

        return view('ecommerce.customer', compact('orders', 'crmCustomer', 'trackingData'));
    }

    public function uploadDoc(Request $request): RedirectResponse
    {
        $request->validate([
            'document' => ['required', 'file', 'max:5120'], // Max 5MB
        ]);

        $userId = session()->get('user_id');
        if (!$userId) {
            return back()->withErrors('Anda harus login terlebih dahulu.');
        }

        $user = \App\Models\User::findOrFail($userId);
        $crmCustomer = CrmCustomer::where('email', $user->email)->first();

        if (!$crmCustomer) {
            $crmCustomer = CrmCustomer::create([
                'user_id' => $user->id,
                'name' => $user->name ?? 'User',
                'email' => $user->email,
            ]);
        }

        if ($request->hasFile('document')) {
            $uploadedPath = $this->minio->uploadFile($request->file('document'), 'customer-docs');
            if ($uploadedPath) {
                $crmCustomer->update(['document_path' => $uploadedPath]);
                return back()->with('status', 'Berkas identitas berhasil diunggah ke Min.io Cloud Object Store.');
            }
        }

        return back()->withErrors('Gagal mengunggah berkas.');
    }

    public function trackOrder(Request $request): View
    {
        $waybill = $request->query('waybill');
        $trackingResult = null;
        $order = null;

        if ($waybill) {
            $order = Order::where('waybill', $waybill)
                ->orWhere('order_number', $waybill)
                ->with(['items.item', 'payment'])
                ->first();

            if ($order && $order->waybill) {
                $trackingResult = $this->kiriminaja->trackShipment($order->waybill);
            }
        }

        return view('ecommerce.track', compact('waybill', 'trackingResult', 'order'));
    }

    public function paymentStatus(Order $order): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => $order->status,
        ]);
    }
}
