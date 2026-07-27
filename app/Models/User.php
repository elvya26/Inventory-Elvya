<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    // Pastikan kolom ini menyesuaikan tabel users kamu yang sudah ada.
    // Karena kamu bilang tabel users sudah ada, mapping dibuat aman dengan nullable.
    protected $fillable = [
        'google_id',
        'name',
        'email',
        'avatar',
        'role',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}

