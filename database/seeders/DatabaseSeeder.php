<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\NotificationMessage;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['sku' => 'ATK-001', 'name' => 'Kertas A4', 'category' => 'ATK', 'unit' => 'rim', 'current_stock' => 24, 'minimum_stock' => 10, 'location' => 'Gudang A'],
            ['sku' => 'ELK-014', 'name' => 'Kabel HDMI', 'category' => 'Elektronik', 'unit' => 'pcs', 'current_stock' => 8, 'minimum_stock' => 12, 'location' => 'Rak B2'],
            ['sku' => 'KBN-007', 'name' => 'Tinta Printer Hitam', 'category' => 'Kebutuhan Kantor', 'unit' => 'botol', 'current_stock' => 15, 'minimum_stock' => 6, 'location' => 'Lemari 1'],
        ];

        foreach ($items as $data) {
            $item = Item::firstOrCreate(['sku' => $data['sku']], $data);

            StockMovement::firstOrCreate([
                'item_id' => $item->id,
                'type' => 'masuk',
                'quantity' => $data['current_stock'],
                'occurred_at' => now()->subDays(2),
            ], [
                'actor' => 'Admin Gudang',
                'note' => 'Stok awal',
            ]);
        }

        NotificationMessage::firstOrCreate([
            'title' => 'Stok Kabel HDMI menipis',
            'recipient' => 'Tim Pengadaan',
        ], [
            'channel' => 'internal',
            'message' => 'Mohon proses pengadaan ulang untuk Kabel HDMI.',
            'status' => 'draft',
        ]);
    }
}
