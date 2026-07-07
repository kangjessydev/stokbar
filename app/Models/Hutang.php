<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hutang extends Model
{
    use Auditable;

    protected $table = 'hutangs';
    protected $fillable = ['supplier_name', 'amount', 'paid_amount', 'status', 'tanggal', 'due_date'];

    public function hutangPayments(): HasMany
    {
        return $this->hasMany(HutangPayment::class);
    }
}
