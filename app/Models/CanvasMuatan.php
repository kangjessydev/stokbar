<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanvasMuatan extends Model
{
    use Auditable;

    protected $table = 'canvas_muatans';
    protected $fillable = ['sales_car_id', 'barang_id', 'qty_loaded', 'tanggal', 'status'];

    protected static function booted()
    {
        static::updating(function ($muatan) {
            // Ketika muatan dikonfirmasi oleh sales/gudang, potong stok barang jadi di Gudang Utama
            if ($muatan->isDirty('status') && $muatan->status === 'confirmed') {
                $muatan->barang->decrement('stock', $muatan->qty_loaded);
            }
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
