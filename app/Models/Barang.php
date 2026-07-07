<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use Auditable;

    protected $fillable = ['gudang_id', 'name', 'stock', 'unit', 'price'];

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function resepBoms(): HasMany
    {
        return $this->hasMany(ResepBom::class);
    }

    public function produksiItems(): HasMany
    {
        return $this->hasMany(ProduksiItem::class);
    }

    public function canvasMuatans(): HasMany
    {
        return $this->hasMany(CanvasMuatan::class);
    }

    public function canvasReturs(): HasMany
    {
        return $this->hasMany(CanvasRetur::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
