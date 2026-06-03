<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $items = Item::query()
            ->when($request->search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('sku', 'ilike', "%{$search}%")
                        ->orWhere('name', 'ilike', "%{$search}%")
                        ->orWhere('category', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('pencatatan.index', compact('items'));
    }

    public function create(): View
    {
        return view('pencatatan.form', ['item' => new Item()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Item::create($this->validatedItem($request));

        return redirect()->route('pencatatan.index')->with('status', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $pencatatan): View
    {
        $pencatatan->load(['movements' => fn ($query) => $query->latest('occurred_at')]);

        return view('pencatatan.show', ['item' => $pencatatan]);
    }

    public function edit(Item $pencatatan): View
    {
        return view('pencatatan.form', ['item' => $pencatatan]);
    }

    public function update(Request $request, Item $pencatatan): RedirectResponse
    {
        $pencatatan->update($this->validatedItem($request, $pencatatan->id));

        return redirect()->route('pencatatan.show', $pencatatan)->with('status', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $pencatatan): RedirectResponse
    {
        $pencatatan->delete();

        return redirect()->route('pencatatan.index')->with('status', 'Data barang berhasil dihapus.');
    }

    public function storeMovement(Request $request, Item $item): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:masuk,keluar,penyesuaian'],
            'quantity' => ['required', 'integer', 'min:1'],
            'actor' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:500'],
            'occurred_at' => ['nullable', 'date'],
        ]);

        StockMovement::create([
            ...$data,
            'item_id' => $item->id,
            'occurred_at' => $data['occurred_at'] ?? now(),
        ]);

        $quantity = (int) $data['quantity'];
        $item->current_stock = match ($data['type']) {
            'masuk' => $item->current_stock + $quantity,
            'keluar' => max(0, $item->current_stock - $quantity),
            default => $quantity,
        };
        $item->save();

        return back()->with('status', 'Mutasi stok berhasil dicatat.');
    }

    private function validatedItem(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:items,sku,'.$ignoreId],
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:120'],
        ]);
    }
}
