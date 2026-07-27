<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit',
        'price',
        'current_stock',
        'minimum_stock',
        'location',
        'image_path',
        'video_path',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Item $item) {
            $item->deleteMediaFiles();
        });
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function deleteMediaFiles(): void
    {
        self::deleteStoredFile($this->image_path);
        self::deleteStoredFile($this->video_path);
    }

    public static function deleteStoredFile(?string $publicPath): void
    {
        if (! $publicPath) {
            return;
        }

        $relative = ltrim(str_replace('/storage/', '', $publicPath), '/');

        if ($relative !== '' && Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }
}
