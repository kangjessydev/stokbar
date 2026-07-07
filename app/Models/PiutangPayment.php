<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PiutangPayment extends Model
{
    protected $table = 'piutang_payments';
    protected $fillable = ['piutang_id', 'amount_paid', 'payment_date', 'payment_method', 'reference_no'];

    protected static function booted()
    {
        static::created(function ($payment) {
            // Ketika pembayaran piutang dicatat, perbarui nominal terbayar dan status lunas pada kartu piutang
            $piutang = $payment->piutang;
            $newPaidAmount = $piutang->paid_amount + $payment->amount_paid;
            
            $piutang->update([
                'paid_amount' => $newPaidAmount,
                'status' => ($newPaidAmount >= $piutang->amount) ? 'lunas' : 'belum_lunas',
            ]);
        });
    }

    public function piutang(): BelongsTo
    {
        return $this->belongsTo(Piutang::class);
    }
}
