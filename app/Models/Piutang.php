<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Piutang extends Model
{
    use Auditable;

    protected $table = 'piutangs';
    protected $fillable = ['invoice_id', 'amount', 'paid_amount', 'status', 'due_date'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function piutangPayments(): HasMany
    {
        return $this->hasMany(PiutangPayment::class);
    }
}
