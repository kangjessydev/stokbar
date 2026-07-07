<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HutangPayment extends Model
{
    protected $table = 'hutang_payments';
    protected $fillable = ['hutang_id', 'amount_paid', 'payment_date', 'payment_method', 'reference_no'];

    protected static function booted()
    {
        static::created(function ($payment) {
            // Ketika pembayaran hutang dicatat, perbarui nominal terbayar dan status lunas pada kartu hutang
            $hutang = $payment->hutang;
            $newPaidAmount = $hutang->paid_amount + $payment->amount_paid;
            
            $hutang->update([
                'paid_amount' => $newPaidAmount,
                'status' => ($newPaidAmount >= $hutang->amount) ? 'lunas' : 'belum_lunas',
            ]);
        });
    }

    public function hutang(): BelongsTo
    {
        return $this->belongsTo(Hutang::class);
    }
}
