<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    public function barangMasuks(): HasMany
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function barangKeluars(): HasMany
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public function barangKeluarDetails(): HasMany
    {
        return $this->hasMany(BarangKeluarDetail::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
