<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    protected $table = 'kontrak';

    protected $fillable = [
        'project_id', 'tahun', 'status', 'service_manager', 'project_client',
        'project_classification', 'project_name', 'contract_number',
        'category_contract', 'skema', 'date_of_contract', 'start_date', 'end_date',
        'project_value', 'costbased', 'actual_cost_konfirmasi', 'actual_cost_admin',
        'persentase', 'amandemen', 'tkdn', 'description', 'resource_management',
    ];

    protected $casts = [
        'project_value'           => 'decimal:2',
        'costbased'               => 'decimal:2',
        'actual_cost_konfirmasi'  => 'decimal:2',
        'actual_cost_admin'       => 'decimal:2',
        'persentase'              => 'decimal:6',
        'tkdn'                    => 'decimal:6',
        'date_of_contract'        => 'date',
        'start_date'              => 'date',
        'end_date'                => 'date',
        'tahun'                   => 'integer',
    ];
}
