<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\NotificationMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('notifikasi.index', [
            'messages' => NotificationMessage::latest()->paginate(10),
            'lowStockItems' => Item::whereColumn('current_stock', '<=', 'minimum_stock')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'recipient' => ['required', 'string', 'max:160'],
            'channel' => ['required', 'in:email,whatsapp,internal'],
            'message' => ['required', 'string', 'max:1000'],
            'status' => ['required', 'in:draft,sent'],
        ]);

        NotificationMessage::create([
            ...$data,
            'sent_at' => $data['status'] === 'sent' ? now() : null,
        ]);

        return back()->with('status', 'Pesan komunikasi berhasil disimpan.');
    }

    public function updateStatus(NotificationMessage $notificationMessage): RedirectResponse
    {
        $notificationMessage->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return back()->with('status', 'Status pesan ditandai terkirim.');
    }
}
