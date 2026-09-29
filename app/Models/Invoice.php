<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'invoice_number',
        'project_id',
        'project_name',
        'customer',
        'sales_user_id',
        'finance_user_id',
        'bupot_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_ppn_percent',
        'tax_ppn_amount',
        'tax_pph_percent',
        'tax_pph_amount',
        'total_after_tax',
        'invoice_amount',
        'payment_amount',
        'pph23_deducted',
        'payment_status',
        'notes',
        'sales_followup_notes',
        'last_followup_at',
    ];

    protected $casts = [
        'invoice_date'     => 'date',
        'due_date'         => 'date',
        'subtotal'         => 'decimal:2',
        'tax_ppn_percent'  => 'decimal:2',
        'tax_ppn_amount'   => 'decimal:2',
        'tax_pph_percent'  => 'decimal:2',
        'tax_pph_amount'   => 'decimal:2',
        'total_after_tax'  => 'decimal:2',
        'invoice_amount'   => 'decimal:2',
        'payment_amount'   => 'decimal:2',
        'pph23_deducted'   => 'decimal:2',
        'outstanding'      => 'decimal:2',
        'last_followup_at' => 'datetime',
    ];

    /**
     * Relasi ke Sales (User).
     */
    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_user_id');
    }

    /**
     * Relasi ke Finance (User) yang memproses.
     */
    public function finance()
    {
        return $this->belongsTo(User::class, 'finance_user_id');
    }

    /**
     * Relasi ke data pembayaran (Payments).
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    /**
     * Relasi ke Kontrak (relasi manual menggunakan project_id).
     */
    public function kontrak()
    {
        return $this->belongsTo(Kontrak::class, 'project_id', 'project_id');
    }

    public function getVendorAttribute(): ?string
    {
        return $this->customer;
    }

    public function setVendorAttribute($value)
    {
        $this->attributes['customer'] = $value;
    }

    /**
     * Scope Filter.
     */
    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('project_id', 'like', "%{$search}%")
                  ->orWhere('customer', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['sales_user_id'])) {
            $query->where('sales_user_id', $filters['sales_user_id']);
        }

        return $query;
    }

    /**
     * Formatted Accessors.
     */
    public function getInvoiceAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->invoice_amount, 0, ',', '.');
    }

    public function getPaymentAmountFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->payment_amount, 0, ',', '.');
    }

    public function getOutstandingFormattedAttribute(): string
    {
        $outstanding = max(0, (float)$this->invoice_amount - (float)$this->payment_amount - (float)($this->pph23_deducted ?? 0));
        return 'Rp ' . number_format($outstanding, 0, ',', '.');
    }

    public function getPaidAmountAttribute(): float
    {
        return (float)($this->payment_amount ?? 0);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float)$this->invoice_amount - (float)$this->payment_amount - (float)($this->pph23_deducted ?? 0));
    }

    public function getPph23DeductedFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->pph23_deducted ?? 0, 0, ',', '.');
    }

    public function getAgingDaysAttribute(): int
    {
        if ($this->payment_status === 'paid' || !$this->due_date || !$this->due_date->isPast()) {
            return 0;
        }
        return (int)now()->diffInDays($this->due_date);
    }

    public function getAgingCategoryAttribute(): string
    {
        if ($this->payment_status === 'paid') {
            return 'paid';
        }
        if (!$this->due_date || !$this->due_date->isPast()) {
            return 'lancar';
        }
        $days = (int)now()->diffInDays($this->due_date);
        if ($days <= 30) return '1_30';
        if ($days <= 60) return '31_60';
        return 'over_60';
    }

    public function getAgingLabelAttribute(): string
    {
        if ($this->payment_status === 'paid') {
            return 'LUNAS';
        }
        if (!$this->due_date || !$this->due_date->isPast()) {
            return 'Lancar (< Jatuh Tempo)';
        }
        $days = (int)now()->diffInDays($this->due_date);
        if ($days <= 30) return '1 - 30 Hari';
        if ($days <= 60) return '31 - 60 Hari';
        return '> 60 Hari (Macet)';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->isOverdue();
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->payment_status !== 'paid' && $this->due_date && $this->due_date->isPast();
    }
}

