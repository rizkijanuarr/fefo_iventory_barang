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

            // Cek apakah expiration_date adalah hari ini atau telah lewat
            if ($barangMasuks->expiration_date <= now()->toDateString()) {
                $barangMasuks->is_returned = true; // Tandai sebagai return jika sudah expired
            } else {
                $barangMasuks->is_returned = false; // Jika belum expired, set false
            }

            // Panggil fungsi untuk mengecek barang expired yang ada
            self::checkExpiredItems(); // Memanggil fungsi pengecekan expired secara otomatis
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

    // Fungsi untuk mengecek barang yang expired tapi belum terjual
    public static function checkExpiredItems(): void
    {
        // Ambil barang yang sudah expired dan belum terjual
        $expiredBarang = self::where('expiration_date', '<', now())
            ->where('quantity', '>', 0)
            ->where('is_returned', false)  // Barang yang belum ditandai sebagai return
            ->get();

        foreach ($expiredBarang as $barang) {
            // Tandai barang sebagai return
            $barang->is_returned = true;
            $barang->save();

            // Update stok barang di tabel Barang
            $barangModel = \App\Models\Barang::find($barang->barang_id);
            if ($barangModel) {
                $barangModel->stock_quantity -= $barang->quantity;
                $barangModel->save();
            }
        }
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
