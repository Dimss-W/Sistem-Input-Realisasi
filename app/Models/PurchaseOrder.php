<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_number',
        'project_id',
        'project_name',
        'vendor_name',
        'po_title',
        'description',
        'po_amount',
        'term_of_payment',
        'payment_due_date',
        'prognosa_periode',
        'prognosa_tahun',
        'order_date',
        'delivery_deadline',
        'status',
        'contract_file',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'po_amount'         => 'decimal:2',
        'term_of_payment'   => 'integer',
        'payment_due_date'  => 'date',
        'prognosa_tahun'    => 'integer',
        'order_date'        => 'date',
        'delivery_deadline' => 'date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function kontrak()
    {
        return $this->belongsTo(Kontrak::class, 'project_id', 'project_id');
    }

    public function prognosa()
    {
        return $this->hasOne(Prognosa::class, 'po_id');
    }

    public function bastos()
    {
        return $this->hasMany(Basto::class, 'project_id', 'project_id');
    }

    /**
     * Hitung total realisasi pengeluaran aktual terkait proyek PO ini.
     */
    public function getActualSpendAttribute(): float
    {
        return (float) Realisasi::where('project_id', $this->project_id)
            ->where('vendor', $this->vendor_name)
            ->sum('realisasi_biaya_final');
    }

    /**
     * Hitung selisih hemat atau over-budget (Pagu PO - Realisasi).
     */
    public function getCostVarianceAttribute(): float
    {
        return (float) $this->po_amount - $this->actual_spend;
    }
}
