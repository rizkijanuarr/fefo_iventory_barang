<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKeluar extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $barangKeluars) {
            $barangKeluars->user_id = auth()->id();

            // Saat barang keluar dibuat, tambahkan quantity ke stok barang
            $barang = \App\Models\Barang::find($barangKeluars->barang_id);
            if ($barang) {
                $barang->stock_quantity -= $barangKeluars->quantity;
                $barang->save();
            }
        });

        static::deleting(function (self $barangKeluars) {
            // Saat barang keluar dihapus, kurangi stok barang
            $barang = \App\Models\Barang::find($barangKeluars->barang_id);
            if ($barang) {
                $barang->stock_quantity += $barangKeluars->quantity;
                $barang->save();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
