<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\KiriminajaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminOrderController extends Controller
{
    protected KiriminajaService $kiriminaja;

    public function __construct(KiriminajaService $kiriminaja)
    {
        $this->kiriminaja = $kiriminaja;
    }

    public function index(Request $request): View
    {
        $orders = Order::query()
            ->when($request->status, function ($query, string $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function ($query, string $search) {
                $query->where('order_number', 'ilike', "%{$search}%")
                    ->orWhere('customer_name', 'ilike', "%{$search}%");
            })
            ->with(['payment'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.item', 'payment']);

        $trackingInfo = null;
        if ($order->waybill) {
            $trackingInfo = $this->kiriminaja->trackShipment($order->waybill);
        }

        return view('admin.orders.show', compact('order', 'trackingInfo'));
    }

    public function ship(Order $order): RedirectResponse
    {
        if ($order->status !== 'paid') {
            return back()->with('error', 'Hanya pesanan berstatus LUNAS (paid) yang dapat diproses pengirimannya.');
        }

        // Book shipment via Kiriminaja API
        $bookingData = [
            'courier' => $order->shipping_courier,
            'destination' => $order->shipping_address,
            'name' => $order->customer_name,
            'phone' => $order->customer_phone,
        ];

        $result = $this->kiriminaja->requestShipping($bookingData);
        $waybill = $result['waybill'] ?? ('KJA' . mt_rand(100000000, 999999999));

        $order->update([
            'status' => 'shipped',
            'waybill' => $waybill,
        ]);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Pengiriman pesanan berhasil diproses. Resi otomatis diterbitkan: ' . $waybill);
    }
}
