<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    private function attachMedia(array $data, ?Item $item = null): array
    {
        unset($data['image_path'], $data['video_path']);

        if (request()->hasFile('image_path')) {
            $file = request()->file('image_path');
            if ($file && $file->isValid()) {
                if ($item?->image_path) {
                    Item::deleteStoredFile($item->image_path);
                }
                $path = $file->store('items/images', 'public');
                $data['image_path'] = $path ? '/storage/'.$path : null;
            } elseif ($item) {
                $data['image_path'] = $item->image_path;
            }
        } elseif ($item) {
            $data['image_path'] = $item->image_path;
        }

        if (request()->hasFile('video_path')) {
            $file = request()->file('video_path');
            if ($file && $file->isValid()) {
                if ($item?->video_path) {
                    Item::deleteStoredFile($item->video_path);
                }
                $path = $file->store('items/videos', 'public');
                $data['video_path'] = $path ? '/storage/'.$path : null;
            } elseif ($item) {
                $data['video_path'] = $item->video_path;
            }
        } elseif ($item) {
            $data['video_path'] = $item->video_path;
        }

        return $data;
    }

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
        $data = $this->validatedItem($request);

        $data = $this->attachMedia($data);

        Item::create($data);

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
        $data = $this->validatedItem($request, $pencatatan->id);

        $data = $this->attachMedia($data, $pencatatan);

        $pencatatan->update($data);

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
        // Pastikan ignoreId valid untuk rule unique PostgreSQL
        $ignoreId = $ignoreId !== null ? (int) $ignoreId : null;

        return $request->validate([
            'sku' => ['required', 'string', 'max:50', $ignoreId === null ? 'unique:items,sku' : 'unique:items,sku,'.$ignoreId],
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:30'],
            'price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:120'],
            // Hindari rule 'image'/'mimetypes' yang butuh guesser fileinfo (bisa error kalau disabled)
            'image_path' => ['nullable', 'file', 'max:4096'],
            'video_path' => ['nullable', 'file', 'max:10240'],
        ]);
    }
}
