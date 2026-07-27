<?php

namespace App\Http\Controllers;

use App\Models\CrmCustomer;
use App\Services\MinioService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CrmController extends Controller
{
    protected MinioService $minio;

    public function __construct(MinioService $minio)
    {
        $this->minio = $minio;
    }

    public function index(Request $request): View
    {
        $allCustomers = CrmCustomer::with('orders')->get();
        
        $totalLeads = 0;
        $totalActive = 0;
        $totalVip = 0;
        $totalRevenue = 0;

        foreach ($allCustomers as $c) {
            $seg = $c->segment;
            if ($seg === 'Lead') {
                $totalLeads++;
            } elseif ($seg === 'VIP') {
                $totalVip++;
            } else {
                $totalActive++;
            }
            $totalRevenue += $c->monetary;
        }

        $customers = CrmCustomer::query()
            ->with(['orders'])
            ->when($request->search, function ($query, string $search) {
                $query->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Map Min.io URLs and Segment-based WhatsApp Campaign templates
        foreach ($customers as $customer) {
            if ($customer->document_path) {
                $customer->document_url = $this->minio->getUrl($customer->document_path);
            } else {
                $customer->document_url = null;
            }

            if ($customer->phone) {
                $phone = preg_replace('/[^0-9]/', '', $customer->phone);
                if (str_starts_with($phone, '08')) {
                    $phone = '628' . substr($phone, 2);
                }

                $seg = $customer->segment;
                if ($seg === 'Lead') {
                    $msg = "Halo {$customer->name}, terima kasih telah mendaftar di Licita Store! Dapatkan penawaran menarik dan diskon khusus untuk pesanan pertama Anda hari ini. Cek katalog kami di: http://localhost:8000/licitastore";
                } elseif ($seg === 'VIP') {
                    $msg = "Halo {$customer->name}, kami sangat berterima kasih atas loyalitas Anda berbelanja di Licita Store! Sebagai pelanggan VIP, nikmati layanan prioritas dan diskon tambahan untuk pesanan Anda berikutnya.";
                } elseif ($seg === 'Churn Alert') {
                    $msg = "Halo {$customer->name}, kami merindukan Anda di Licita Store! Sudah cukup lama sejak pesanan terakhir Anda. Ada banyak produk baru menarik di katalog kami, mari berkunjung kembali: http://localhost:8000/licitastore";
                } else {
                    $msg = "Halo {$customer->name}, bagaimana pengalaman berbelanja Anda di Licita Store baru-baru ini? Umpan balik Anda sangat berharga bagi kami.";
                }

                $customer->whatsapp_link = "https://wa.me/{$phone}?text=" . urlencode($msg);
            } else {
                $customer->whatsapp_link = null;
            }
        }

        return view('admin.crm.index', compact('customers', 'totalLeads', 'totalActive', 'totalVip', 'totalRevenue'));
    }

    public function updateNotes(Request $request, CrmCustomer $customer): RedirectResponse
    {
        $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        $customer->update([
            'notes' => $request->input('notes'),
        ]);

        return redirect()->route('admin.crm.index')->with('status', 'Catatan CRM untuk pelanggan ' . $customer->name . ' berhasil diperbarui.');
    }
}
