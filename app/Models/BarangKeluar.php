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

            // Implementasi FEFO saat barang keluar
            $barang_id = $barangKeluars->barang_id;
            $sisaJumlah = $barangKeluars->quantity;

            // Ambil barang masuk dengan expired date terdekat yang belum di-return
            $barangMasukTerdekat = BarangMasuk::where('barang_id', $barang_id)
                ->where('is_returned', false)
                ->where('expiration_date', '>=', now())
                ->orderBy('expiration_date', 'asc')  // Urutkan berdasarkan tanggal expired terdekat
                ->get();  // Ambil semua barang yang cocok

            // Proses barang masuk yang memenuhi kriteria
            foreach ($barangMasukTerdekat as $barangMasuk) {
                if ($barangMasuk->quantity >= $sisaJumlah) {
                    // Jika stok cukup, keluarkan barang dan update stok
                    $barangMasuk->quantity -= $sisaJumlah;
                    $barangMasuk->save();
                    break;  // Stop setelah mengambil barang yang cukup
                } else {
                    // Jika stok kurang, keluarkan seluruh barang dari entry ini
                    $sisaJumlah -= $barangMasuk->quantity;
                    $barangMasuk->quantity = 0;
                    $barangMasuk->save();
                }
            }

            // Update stok total barang di tabel Barang
            $barang = \App\Models\Barang::find($barangKeluars->barang_id);
            if ($barang) {
                $barang->stock_quantity -= $barangKeluars->quantity;
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
