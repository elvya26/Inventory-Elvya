<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmCustomer extends Model
{
    protected $table = 'crm_customers';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'document_path',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function getRecencyAttribute()
    {
        $lastOrder = $this->orders()->whereIn('status', ['paid', 'shipped', 'completed'])->latest()->first();
        return $lastOrder ? $lastOrder->created_at : null;
    }

    public function getFrequencyAttribute()
    {
        return $this->orders()->whereIn('status', ['paid', 'shipped', 'completed'])->count();
    }

    public function getMonetaryAttribute()
    {
        return $this->orders()->whereIn('status', ['paid', 'shipped', 'completed'])->sum('total_amount');
    }

    public function getSegmentAttribute()
    {
        $freq = $this->frequency;
        $monetary = $this->monetary;
        $recency = $this->recency;

        if ($freq === 0) {
            return 'Lead';
        }

        if ($recency && $recency->diffInDays(now()) > 30) {
            return 'Churn Alert';
        }

        if ($freq > 3 || $monetary > 1500000) {
            return 'VIP';
        }

        return 'Active';
    }
}
