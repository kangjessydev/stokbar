<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanBaku extends Model
{
    use Auditable;

    protected $table = 'bahan_bakus';
    protected $fillable = ['gudang_id', 'name', 'stock', 'unit', 'safety_stock'];

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class);
    }

    public function resepBoms(): HasMany
    {
        return $this->hasMany(ResepBom::class, 'bahan_baku_id');
    }
}
