<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterVendor extends Model
{
    protected $table = 'master_vendors';

    protected $fillable = [
        'kode_vendor',
        'nama_vendor',
        'pic_vendor',
        'kontak',
        'email',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
