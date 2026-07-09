<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierMutasi extends Model
{
    protected $fillable = [
        'supplier_id',
        'barang_id',
        'tanggal',
        'jenis_transaksi',
        'qty_bal',
        'harga_satuan',
        'total_hutang',
        'keterangan',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    protected static function booted()
    {
        static::created(function ($mutasi) {
            if ($mutasi->barang) {
                if ($mutasi->jenis_transaksi === 'barang_masuk') {
                    $mutasi->barang->increment('stock', $mutasi->qty_bal);
                } elseif ($mutasi->jenis_transaksi === 'barang_retur') {
                    $mutasi->barang->decrement('stock', $mutasi->qty_bal);
                }
            }
        });

        static::updated(function ($mutasi) {
            if ($mutasi->barang) {
                // To safely update stock, first revert the original, then apply the new
                $originalQty = $mutasi->getOriginal('qty_bal');
                $originalType = $mutasi->getOriginal('jenis_transaksi');
                
                // Revert original
                if ($originalType === 'barang_masuk') {
                    $mutasi->barang->decrement('stock', $originalQty);
                } elseif ($originalType === 'barang_retur') {
                    $mutasi->barang->increment('stock', $originalQty);
                }
                
                // Apply new
                if ($mutasi->jenis_transaksi === 'barang_masuk') {
                    $mutasi->barang->increment('stock', $mutasi->qty_bal);
                } elseif ($mutasi->jenis_transaksi === 'barang_retur') {
                    $mutasi->barang->decrement('stock', $mutasi->qty_bal);
                }
            }
        });

        static::deleted(function ($mutasi) {
            if ($mutasi->barang) {
                if ($mutasi->jenis_transaksi === 'barang_masuk') {
                    $mutasi->barang->decrement('stock', $mutasi->qty_bal);
                } elseif ($mutasi->jenis_transaksi === 'barang_retur') {
                    $mutasi->barang->increment('stock', $mutasi->qty_bal);
                }
            }
        });
    }
}
