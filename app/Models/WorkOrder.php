<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $table = 'work_orders';

    protected $fillable = [
        'wo_number',
        'project_id',
        'project_name',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to_user_id',
        'created_by_user_id',
        'vendor',
        'start_date',
        'due_date',
        'completed_date',
        'estimated_cost',
        'actual_cost',
        'notes',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'due_date'       => 'date',
        'completed_date' => 'date',
    ];

    /**
     * Relasi ke User yang mengerjakan.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Relasi ke User pembuat WO.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Scope filter.
     */
    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('wo_number', 'like', "%{$s}%")
                  ->orWhere('title', 'like', "%{$s}%")
                  ->orWhere('project_name', 'like', "%{$s}%")
                  ->orWhere('project_id', 'like', "%{$s}%");
            });
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        return $query;
    }

    /**
     * Helpers.
     */
    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !in_array($this->status, ['closed', 'cancelled']);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'open'        => 'primary',
            'in_progress' => 'warning',
            'on_hold'     => 'secondary',
            'closed'      => 'success',
            'cancelled'   => 'danger',
            default       => 'dark',
        };
    }

    public function getPriorityBadgeColorAttribute(): string
    {
        return match ($this->priority) {
            'critical' => 'danger',
            'high'     => 'warning',
            'normal'   => 'primary',
            'low'      => 'secondary',
            default    => 'dark',
        };
    }
}
