<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesCar extends Model
{
    use Auditable;

    protected $table = 'sales_cars';
    protected $fillable = ['name', 'driver_name', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canvasMuatans(): HasMany
    {
        return $this->hasMany(CanvasMuatan::class);
    }

    public function canvasReturs(): HasMany
    {
        return $this->hasMany(CanvasRetur::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
