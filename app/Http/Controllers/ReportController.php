<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('laporan.index', $this->reportData($request));
    }

    public function print(Request $request): View
    {
        return view('laporan.print', $this->reportData($request));
    }

    private function reportData(Request $request): array
    {
        $from = $request->date('from')?->startOfDay();
        $to = $request->date('to')?->endOfDay();

        $movements = StockMovement::with('item')
            ->when($from, fn ($query) => $query->where('occurred_at', '>=', $from))
            ->when($to, fn ($query) => $query->where('occurred_at', '<=', $to))
            ->latest('occurred_at')
            ->get();

        return [
            'from' => $from,
            'to' => $to,
            'items' => Item::orderBy('name')->get(),
            'movements' => $movements,
            'stockIn' => $movements->where('type', 'masuk')->sum('quantity'),
            'stockOut' => $movements->where('type', 'keluar')->sum('quantity'),
            'adjustments' => $movements->where('type', 'penyesuaian')->count(),
        ];
    }
}
