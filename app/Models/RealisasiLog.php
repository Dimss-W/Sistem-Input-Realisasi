<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiLog extends Model
{
    protected $table = 'realisasi_logs';

    public $timestamps = true;
    public $updatedAt = null; // Hanya created_at yang relevan untuk log

    protected $fillable = [
        'realisasi_id',
        'action',
        'old_data',
        'new_data',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    /**
     * Relasi ke data realisasi.
     */
    public function realisasi()
    {
        return $this->belongsTo(Realisasi::class, 'realisasi_id');
    }

    /**
     * Simpan log dengan mudah.
     */
    public static function record(
        ?int $realisasiId,
        string $action,
        ?array $oldData = null,
        ?array $newData = null,
        ?int $userId = null,
        ?string $ip = null
    ): self {
        return self::create([
            'realisasi_id' => $realisasiId,
            'action'       => $action,
            'old_data'     => $oldData,
            'new_data'     => $newData,
            'user_id'      => $userId,
            'ip_address'   => $ip,
        ]);
    }
}
