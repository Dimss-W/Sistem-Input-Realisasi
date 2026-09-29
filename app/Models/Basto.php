<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Basto extends Model
{
    protected $table = 'bastos';

    protected $fillable = [
        'basto_number',
        'project_id',
        'project_name',
        'cost_no',
        'cost_value',
        'cost_date_start',
        'cost_date_end',
        'cost_based',
        'supply_chain',
        'dmo_user_id',
        'sm_user_id',
        'status',
        'submitted_at',
        'reviewed_at',
        'rejection_reason',
        'notes',
        'attachment_file',
        'qc_user_id',
        'qc_status',
        'qc_notes',
        'qc_checklist',
        'qc_verification_code',
        'qc_verified_at',
    ];

    protected $casts = [
        'submitted_at'    => 'datetime',
        'reviewed_at'     => 'datetime',
        'qc_verified_at'  => 'datetime',
        'qc_checklist'    => 'array',
        'cost_value'      => 'decimal:2',
        'cost_based'      => 'decimal:2',
        'cost_date_start' => 'date',
        'cost_date_end'   => 'date',
    ];

    /**
     * Relasi ke DMO (User yang mengajukan).
     */
    public function dmo()
    {
        return $this->belongsTo(User::class, 'dmo_user_id');
    }

    /**
     * Relasi ke Service Manager (User yang meng-approve).
     */
    public function sm()
    {
        return $this->belongsTo(User::class, 'sm_user_id');
    }

    /**
     * Relasi ke QC Officer.
     */
    public function qcUser()
    {
        return $this->belongsTo(User::class, 'qc_user_id');
    }

    /**
     * Relasi ke Kontrak (project_id).
     */
    public function kontrak()
    {
        return $this->belongsTo(Kontrak::class, 'project_id', 'project_id');
    }

    /**
     * Scope Filter.
     */
    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('basto_number', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('project_id', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['qc_status'])) {
            if ($filters['qc_status'] === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('qc_status')->orWhere('qc_status', 'pending');
                });
            } else {
                $query->where('qc_status', $filters['qc_status']);
            }
        }

        if (!empty($filters['dmo_user_id'])) {
            $query->where('dmo_user_id', $filters['dmo_user_id']);
        }

        if (!empty($filters['sm_user_id'])) {
            $query->where('sm_user_id', $filters['sm_user_id']);
        }

        return $query;
    }
}
