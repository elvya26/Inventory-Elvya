<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\NotificationMessage;
use App\Models\StockMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'serviceName' => env('SERVICE_NAME', 'inventory'),
            'itemsCount' => Item::count(),
            'lowStockCount' => Item::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'movementCount' => StockMovement::count(),
            'pendingNotifications' => NotificationMessage::where('status', 'draft')->count(),
            'recentMovements' => StockMovement::with('item')->latest('occurred_at')->limit(5)->get(),
        ]);
    }
}
