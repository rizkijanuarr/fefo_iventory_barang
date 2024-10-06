<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => \App\Enums\Status::class,
        'payment_method' => \App\Enums\PaymentMethod::class,
    ];

    public function barangKeluarDetails(): HasMany
    {
        return $this->hasMany(BarangKeluarDetail::class, 'barang_keluar_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function getRouteKeyName(): string
    {
        return 'barang_keluar_number';
    }

    // Booted
    protected static function booted(): void
    {
        static::creating(function (self $barang_keluar) {
            $barang_keluar->user_id = auth()->id();
            $barang_keluar->total = 0;
        });


        static::saving(function ($barang_keluar) {
            if ($barang_keluar->isDirty('total')) {
                $barang_keluar->loadMissing('barangKeluarDetails.barang');

                $profitCalculation = $barang_keluar->barangKeluarDetails->reduce(function ($carry, $detail) {

                    $barangProfit = ($detail->price - $detail->barang->cost_price) * $detail->quantity;
                    return $carry + $barangProfit;
                }, 0);

                $barang_keluar->attributes['profit'] = $profitCalculation;
            }
        });
    }

    public function markAsComplete(): void
    {
        $this->status = \App\Enums\Status::COMPLETED;
        $this->save();
    }
}
