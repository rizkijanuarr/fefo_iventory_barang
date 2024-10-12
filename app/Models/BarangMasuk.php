<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangMasuk extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $barangMasuks) {
            $barangMasuks->user_id = auth()->id();

            // Saat barang masuk dibuat, tambahkan quantity ke stok barang
            $barang = \App\Models\Barang::find($barangMasuks->barang_id);
            if ($barang) {
                $barang->stock_quantity += $barangMasuks->quantity;
                $barang->save();
            }
        });

        static::deleting(function (self $barangMasuks) {
            // Saat barang masuk dihapus, kurangi stok barang
            $barang = \App\Models\Barang::find($barangMasuks->barang_id);
            if ($barang) {
                $barang->stock_quantity -= $barangMasuks->quantity;
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
