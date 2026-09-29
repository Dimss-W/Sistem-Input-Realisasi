<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $table = 'import_logs';

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'type',
        'total_rows',
        'success_rows',
        'error_rows',
        'errors_json',
        'imported_at',
    ];

    protected $casts = [
        'errors_json' => 'array',
        'imported_at' => 'datetime',
    ];

    /**
     * Relasi ke User yang mengupload.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
