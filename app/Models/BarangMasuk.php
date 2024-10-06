<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => \App\Enums\Status::class,
        'payment_method' => \App\Enums\PaymentMethod::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $barang_keluars) {
            $barang_keluars->user_id = auth()->id();
        });
    }

    public function barangMasukDetails(): HasMany
    {
        return $this->hasMany(BarangMasukDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
