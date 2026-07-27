<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\NotificationMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function showCekVa(): View
    {
        return view('bank.cek_va');
    }

    public function searchVa(Request $request): View
    {
        $vaNumber = $request->input('va_number');
        $payment = Payment::where('va_number', $vaNumber)
            ->orWhereHas('order', function ($query) use ($vaNumber) {
                $query->where('order_number', $vaNumber);
            })
            ->with('order.items.item')
            ->first();

        return view('bank.cek_va', compact('payment', 'vaNumber'));
    }

    public function simulatePay(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status === 'completed') {
            return back()->with('error', 'Pembayaran untuk Virtual Account ini sudah diselesaikan sebelumnya.');
        }

        $this->processPaymentSuccess($payment);

        return back()->with('status', 'Pembayaran Virtual Account berhasil disimulasikan sebagai LUNAS.');
    }

    public function notification(Request $request): \Illuminate\Http\JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('DOKU Webhook Notification received: ' . json_encode($request->all()));

        $invoiceNumber = $request->input('order.invoice_number');
        $transactionStatus = $request->input('transaction.status');

        if (!$invoiceNumber) {
            return response()->json(['message' => 'Invoice number missing'], 400);
        }

        $order = Order::where('order_number', $invoiceNumber)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $payment = Payment::where('order_id', $order->id)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment record not found'], 404);
        }

        if (strtoupper($transactionStatus) === 'SUCCESS' || in_array('SUCCESS', $request->input('message', []))) {
            if ($payment->status !== 'completed') {
                $this->processPaymentSuccess($payment);
            }
            return response()->json(['message' => 'Notification processed successfully']);
        }

        return response()->json(['message' => 'Transaction status is not SUCCESS'], 400);
    }

    protected function processPaymentSuccess(Payment $payment): void
    {
        // Update Payment status
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // Update Order status & Automate shipment booking via Kiriminaja
        $order = $payment->order;
        $waybill = 'KJA' . mt_rand(100000000, 999999999);

        try {
            $kiriminaja = app(\App\Services\KiriminajaService::class);
            $bookingData = [
                'courier' => $order->shipping_courier,
                'destination' => $order->shipping_address,
                'name' => $order->customer_name,
                'phone' => $order->customer_phone,
            ];
            $result = $kiriminaja->requestShipping($bookingData);
            if (isset($result['waybill']) && $result['waybill']) {
                $waybill = $result['waybill'];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Kiriminaja auto booking failed: " . $e->getMessage());
        }

        $order->update([
            'status' => 'shipped',
            'waybill' => $waybill,
        ]);

        // Send automatic WhatsApp notification
        if ($order->customer_phone) {
            try {
                $waService = app(\App\Services\WhatsappService::class);
                $totalAmountFormatted = number_format($order->total_amount, 0, ',', '.');
                $message = "Halo {$order->customer_name}, pembayaran pesanan {$order->order_number} sebesar Rp.{$totalAmountFormatted} telah berhasil diverifikasi. Pesanan Anda telah otomatis dikirim via {$order->shipping_courier} dengan nomor resi: {$waybill}.";
                $waService->sendMessage($order->customer_phone, $message);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("WhatsApp auto notification failed: " . $e->getMessage());
            }
        }

        // Mutate Stock
        foreach ($order->items as $orderItem) {
            $item = $orderItem->item;
            $qty = $orderItem->quantity;

            // Reduce stock
            $item->current_stock = max(0, $item->current_stock - $qty);
            $item->save();

            // Record StockMovement
            StockMovement::create([
                'item_id' => $item->id,
                'type' => 'keluar',
                'quantity' => $qty,
                'actor' => $order->customer_name,
                'note' => 'Pembelian E-Commerce #' . $order->order_number,
                'occurred_at' => now(),
            ]);

            // If stock drops below minimum, trigger low stock notification
            if ($item->isLowStock()) {
                NotificationMessage::create([
                    'title' => 'Peringatan Stok Menipis: ' . $item->name,
                    'recipient' => 'Tim Pengadaan Gudang',
                    'channel' => 'internal',
                    'message' => "Stok barang '{$item->name}' (SKU: {$item->sku}) berkurang karena pembelian E-Commerce #{$order->order_number}. Stok saat ini tinggal {$item->current_stock} {$item->unit}. Mohon lakukan pengadaan ulang segera.",
                    'status' => 'draft',
                ]);
            }
        }

        // Record general payment notification
        NotificationMessage::create([
            'title' => 'Pembayaran Diterima: ' . $order->order_number,
            'recipient' => 'Admin Keuangan',
            'channel' => 'internal',
            'message' => "Pembayaran sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " melalui VA {$payment->bank_name} untuk pesanan #{$order->order_number} dari {$order->customer_name} telah berhasil diterima.",
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
