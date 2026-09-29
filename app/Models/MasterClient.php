<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterClient extends Model
{
    protected $table = 'master_clients';

    protected $fillable = [
        'kode_client',
        'nama_client',
        'kategori',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
