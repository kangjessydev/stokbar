<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use Auditable;

    protected $fillable = ['no_invoice', 'customer_id', 'sales_car_id', 'total_price', 'payment_status', 'tanggal'];

    protected static function booted()
    {
        static::created(function ($invoice) {
            // Jika penjualan dilakukan secara kredit, buat kartu piutang baru secara otomatis
            if ($invoice->payment_status === 'kredit') {
                $customer = $invoice->customer;
                $period = $customer->credit_period ?? 30; // default tempo 30 hari
                
                $invoice->piutang()->create([
                    'amount' => $invoice->total_price,
                    'paid_amount' => 0.00,
                    'status' => 'belum_lunas',
                    'due_date' => now()->addDays($period)->toDateString(),
                ]);
            }
        });

        static::updated(function ($invoice) {
            if ($invoice->payment_status === 'kredit') {
                $piutang = $invoice->piutang;
                if ($piutang) {
                    $piutang->update([
                        'amount' => $invoice->total_price,
                        'status' => ($piutang->paid_amount >= $invoice->total_price) ? 'lunas' : 'belum_lunas',
                    ]);
                } else {
                    $customer = $invoice->customer;
                    $period = $customer->credit_period ?? 30;
                    $invoice->piutang()->create([
                        'amount' => $invoice->total_price,
                        'paid_amount' => 0.00,
                        'status' => 'belum_lunas',
                        'due_date' => now()->addDays($period)->toDateString(),
                    ]);
                }
            } else {
                // Jika status bayar berubah menjadi lunas tunai, hapus data piutang terkait
                $invoice->piutang()?->delete();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesCar(): BelongsTo
    {
        return $this->belongsTo(SalesCar::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function piutang(): HasOne
    {
        return $this->hasOne(Piutang::class);
    }
}
