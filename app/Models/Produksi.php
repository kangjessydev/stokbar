<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Produksi extends Model
{
    use Auditable;

    protected $table = 'produksis';
    protected $fillable = ['kode_produksi', 'tanggal', 'status'];

    protected static function booted()
    {
        static::updating(function ($produksi) {
            // Jalankan pengurangan stok bahan baku dan penambahan barang jadi saat produksi diselesaikan
            if ($produksi->isDirty('status') && $produksi->status === 'selesai') {
                $produksi->completeProduction();
            }
        });
    }

    public function produksiItems(): HasMany
    {
        return $this->hasMany(ProduksiItem::class);
    }

    public function completeProduction()
    {
        DB::transaction(function () {
            foreach ($this->produksiItems as $item) {
                $barang = $item->barang;

                // 1. Tambah stok Barang Jadi di Gudang Utama
                $barang->increment('stock', $item->qty_produced);

                // 2. Potong stok Bahan Baku berdasarkan Resep (BOM)
                foreach ($barang->resepBoms as $bom) {
                    $bahanBaku = $bom->bahanBaku;
                    $qtyNeeded = $bom->qty_needed * $item->qty_produced;
                    
                    $bahanBaku->decrement('stock', $qtyNeeded);
                }
            }
        });
    }
}
