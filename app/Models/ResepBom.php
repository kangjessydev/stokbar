<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepBom extends Model
{
    use Auditable;

    protected $table = 'resep_boms';
    protected $fillable = ['barang_id', 'bahan_baku_id', 'qty_needed'];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }
}
