<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiutangPaymentBarangSisa extends Model
{
    protected $fillable = [
        'piutang_payment_id',
        'barang_id',
        'qty_retur',
        'harga_jual',
        'subtotal_kredit',
    ];

    public function piutangPayment()
    {
        return $this->belongsTo(PiutangPayment::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    protected static function booted()
    {
        static::created(function ($retur) {
            if ($retur->piutangPayment && $retur->piutangPayment->piutang) {
                $retur->piutangPayment->piutang->recalculatePaidAmount();
            }
            if ($retur->barang) {
                $retur->barang->increment('stock', $retur->qty_retur);
            }
        });

        static::updated(function ($retur) {
            if ($retur->piutangPayment && $retur->piutangPayment->piutang) {
                $retur->piutangPayment->piutang->recalculatePaidAmount();
            }
            
            // Adjust stock if qty_retur changed
            $originalQty = $retur->getOriginal('qty_retur');
            $newQty = $retur->qty_retur;
            if ($originalQty !== $newQty && $retur->barang) {
                $retur->barang->increment('stock', $newQty - $originalQty);
            }
        });

        static::deleted(function ($retur) {
            if ($retur->piutangPayment && $retur->piutangPayment->piutang) {
                $retur->piutangPayment->piutang->recalculatePaidAmount();
            }
            if ($retur->barang) {
                $retur->barang->decrement('stock', $retur->qty_retur);
            }
        });
    }
}
