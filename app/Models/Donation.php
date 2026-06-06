<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    // Mendaftarkan kolom yang boleh diisi datanya
    protected $fillable = [
        'user_id',
        'restaurant_name',
        'portions',
        'pickup_address',
        'contact_number',
        'pickup_time',
        'status',
        'claimed_by_user_id',
    ];

    // Relasi: Donasi ini milik seorang User (HOREKA)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Donasi ini diklaim oleh seorang User (Komunitas)
    public function claimer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by_user_id');
    }
}
