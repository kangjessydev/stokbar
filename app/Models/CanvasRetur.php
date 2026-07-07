<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanvasRetur extends Model
{
    use Auditable;

    protected $table = 'canvas_returs';
    protected $fillable = ['sales_car_id', 'barang_id', 'qty_returned', 'tanggal'];

    protected static function booted()
    {
        static::created(function ($retur) {
            // Ketika retur sisa mobil dicatat di sore hari, tambahkan kembali stok barang jadi ke Gudang Utama
            $retur->barang->increment('stock', $retur->qty_returned);
        });
    }

    public function salesCar(): BelongsTo
    {
        return $this->belongsTo(SalesCar::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
