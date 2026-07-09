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

    public function recalculatePaidAmount()
    {
        $totalCash = $this->piutangPayments()->sum('amount_paid');
        $totalRetur = 0;
        
        foreach ($this->piutangPayments as $payment) {
            $totalRetur += $payment->barangSisas()->sum('subtotal_kredit');
        }

        $newPaidAmount = $totalCash + $totalRetur;
        
        $this->update([
            'paid_amount' => $newPaidAmount,
            'status' => ($newPaidAmount >= $this->amount) ? 'lunas' : 'belum_lunas',
        ]);
    }
}
