<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'invoice_id',
        'payment_date',
        'payment_amount',
        'pph23_amount',
        'payment_reference',
        'bupot_number',
        'payment_method',
        'processed_by_user_id',
        'notes',
    ];

    protected $casts = [
        'payment_date'   => 'date',
        'payment_amount' => 'decimal:2',
        'pph23_amount'   => 'decimal:2',
    ];

    /**
     * Relasi ke Invoice.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Relasi ke User yang memproses.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    /**
     * Accessor format Rupiah.
     */
    public function getPaymentAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->payment_amount, 0, ',', '.');
    }
}
