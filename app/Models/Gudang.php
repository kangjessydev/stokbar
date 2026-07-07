<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gudang extends Model
{
    use Auditable;

    protected $fillable = ['name', 'type'];

    public function bahanBakus(): HasMany
    {
        return $this->hasMany(BahanBaku::class);
    }

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
