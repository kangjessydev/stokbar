<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PiutangPayment extends Model
{
    protected $table = 'piutang_payments';
    protected $fillable = ['piutang_id', 'amount_paid', 'payment_date', 'payment_method', 'reference_no'];

    protected static function booted()
    {
        static::saved(function ($payment) {
            $payment->piutang->recalculatePaidAmount();
        });

        static::deleted(function ($payment) {
            $payment->piutang->recalculatePaidAmount();
        });
    }

    public function piutang(): BelongsTo
    {
        return $this->belongsTo(Piutang::class);
    }

    public function barangSisas(): HasMany
    {
        return $this->hasMany(PiutangPaymentBarangSisa::class, 'piutang_payment_id');
    }
}
