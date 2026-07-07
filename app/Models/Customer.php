<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use Auditable;

    protected $fillable = ['name', 'market', 'phone', 'credit_limit', 'credit_period'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
