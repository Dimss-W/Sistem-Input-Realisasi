<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prognosa extends Model
{
    protected $table = 'prognosa';

    protected $fillable = [
        'source_row', 'project_id', 'project_name', 'activity',
        'satuan_kerja', 'pic', 'prognosa_biaya', 'periode', 'partner',
        'keterangan', 'tahun_original', 'tahun_normalized',
        'data_source', 'po_id', 'is_overridden',
    ];

    protected $casts = [
        'prognosa_biaya'   => 'decimal:2',
        'source_row'       => 'integer',
        'tahun_original'   => 'integer',
        'tahun_normalized' => 'integer',
        'po_id'            => 'integer',
        'is_overridden'    => 'boolean',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }
}
