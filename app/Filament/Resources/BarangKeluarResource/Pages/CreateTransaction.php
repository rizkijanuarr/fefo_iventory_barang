<?php

namespace App\Filament\Resources\BarangKeluarResource\Pages;

use App\Enums\Status;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use App\Filament\Resources\BarangKeluarResource;

class CreateTransaction extends Page implements HasForms
{
    protected static string $resource = BarangKeluarResource::class;
    protected static string $view = 'filament.resources.barang-keluar-resource.pages.create-transaction';

    public \App\Models\BarangKeluar $record;
    public mixed $selectedBarang;
    public int $quantityValue = 0;
    public int $discount = 0;

    public function getTitle(): string
    {
        return "Barang Keluar: {$this->record->barang_keluar_number}";
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedBarang')
                ->label('Pilih Barang')
                ->searchable()
                ->preload()
                ->options(\App\Models\Barang::pluck('name', 'id')->toArray())
                ->live()
                ->afterStateUpdated(function ($state) {
                    $barang = \App\Models\Barang::find($state);

                    if ($barang) {
                        // Cek apakah detail barang sudah ada
                        $existingDetail = $this->record->barangKeluarDetails()->where('barang_id', $state)->first();
                        $newQuantity = $this->quantityValue;

                        // Jika detail sudah ada, update quantity-nya
                        if ($existingDetail) {
                            // Hitung jumlah barang yang akan ditambahkan
                            $newQuantity += $existingDetail->quantity;
                        }

                        // Cek ketersediaan stok
                        if ($barang->stock_quantity >= $newQuantity) {
                            // Update atau create BarangKeluarDetail
                            $this->record->barangKeluarDetails()->updateOrCreate(
                                [
                                    'barang_keluar_id' => $this->record->id,
                                    'barang_id' => $state,
                                ],
                                [
                                    'quantity' => $newQuantity,
                                    'price' => $barang->price,
                                    'subtotal' => $barang->price * $newQuantity,
                                ]
                            );

                            // Kurangi stok barang
                            $barang->stock_quantity -= $newQuantity;
                            $barang->save();
                        } else {
                            throw new \Exception('Stok tidak cukup untuk barang ini.');
                        }
                    }
                }),
        ];
    }

    // UPDATE QTY
    public function updateQty(\App\Models\BarangKeluarDetail $barangKeluarDetail, $quantity): void
    {
        Log::info('Updating quantity', [
            'barang_keluar_detail_id' => $barangKeluarDetail->id,
            'quantity' => $quantity,
        ]);

        // Cek apakah quantity lebih dari 0
        if ($quantity > 0) {
            // Hitung perbedaan quantity
            $quantityDifference = $quantity - $barangKeluarDetail->quantity;

            // Update quantity dan subtotal
            $barangKeluarDetail->update([
                'quantity' => $quantity,
                'subtotal' => $barangKeluarDetail->price * $quantity,
            ]);

            // Update stock quantity
            $barang = \App\Models\Barang::find($barangKeluarDetail->barang_id);
            if ($barang) {
                $barang->stock_quantity -= $quantityDifference;
                $barang->save();
            }
        }
    }

    // REMOVE BARANG
    public function removeProduct(\App\Models\BarangKeluarDetail $barangKeluarDetail): void
    {
        $barangKeluarDetail->delete();

        // Update stok barang yang dihapus
        $barang = \App\Models\Barang::find($barangKeluarDetail->barang_id);
        if ($barang) {
            $barang->stock_quantity += $barangKeluarDetail->quantity; // Kembalikan stock
            $barang->save();
        }

        $this->dispatch('barangRemoved');
    }

    // UPDATE ORDER
    public function updateBarangKeluar(): void
    {
        $barangKeluarDetailSum = $this->record->barangKeluarDetails->sum('subtotal');

        $this->record->update([
            'discount' => $this->discount,
            'total' => $barangKeluarDetailSum - $this->discount,
        ]);
    }

    // FINALIZE BARANG KELUAR
    public function finalize(): void
    {
        $this->updateBarangKeluar();
        $this->record->update(['status' => Status::COMPLETED]);
        $this->redirect('/barang-keluars');
    }

    public function saveAsDraft(): void
    {
        $this->updateBarangKeluar();
        $this->redirect('/barang-keluars');
    }
}
