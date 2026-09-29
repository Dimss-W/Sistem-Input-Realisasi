<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    use HasFactory;

    protected $table = 'realisasi';

    protected $fillable = [
        'source_row',
        'record_key',
        'project_id',
        'project_name',
        'item_biaya',
        'satuan_kerja',
        'pic',
        'periode',
        'realisasi_biaya_original',
        'currency',
        'realisasi_biaya_idr',
        'status',
        'vendor',
        'sifat',
        'link_evidence',
        'tahun',
        'data_flag',
        'realisasi_biaya_final',
    ];

    protected $casts = [
        'realisasi_biaya_original' => 'decimal:2',
        'realisasi_biaya_idr'      => 'decimal:2',
        'realisasi_biaya_final'    => 'decimal:2',
        'tahun'                    => 'integer',
        'source_row'               => 'integer',
    ];

    /**
     * Daftar bulan Indonesia dengan urutan nomor untuk sorting.
     */
    public static array $periodeOrder = [
        'JANUARI'   => 1,
        'FEBRUARI'  => 2,
        'MARET'     => 3,
        'APRIL'     => 4,
        'MEI'       => 5,
        'JUNI'      => 6,
        'JULI'      => 7,
        'AGUSTUS'   => 8,
        'SEPTEMBER' => 9,
        'OKTOBER'   => 10,
        'NOVEMBER'  => 11,
        'DESEMBER'  => 12,
    ];

    /**
     * Cek apakah invoice vendor berisiko hangus / kedaluwarsa (> 90 hari belum dibayar).
     */
    public function getIsOverdue90DaysAttribute(): bool
    {
        if (strtoupper($this->status ?? '') === 'PAID') {
            return false;
        }

        $month = self::$periodeOrder[strtoupper($this->periode ?? '')] ?? null;
        $year = (int) ($this->tahun ?: ($this->created_at ? $this->created_at->year : null));

        if (!$month || !$year) {
            return $this->created_at ? $this->created_at->diffInDays(now()) > 90 : false;
        }

        try {
            $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
            return now()->greaterThan($endDate->copy()->addDays(90));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate record_key dari kombinasi field transaksi.
     * Digunakan untuk UPSERT saat import — bukan unique constraint.
     */
    public static function generateRecordKey(array $data): string
    {
        $fields = [
            strtoupper(trim($data['project_id'] ?? '')),
            strtoupper(trim($data['item_biaya'] ?? '')),
            strtoupper(trim($data['satuan_kerja'] ?? '')),
            strtoupper(trim($data['pic'] ?? '')),
            strtoupper(trim($data['periode'] ?? '')),
            strtoupper(trim($data['vendor'] ?? '')),
            strtoupper(trim($data['sifat'] ?? '')),
            trim($data['tahun'] ?? ''),
            trim($data['source_row'] ?? ''), // Tambahkan source_row agar baris terpisah tetap memiliki key unik!
        ];

        return hash('sha256', implode('|', $fields));
    }

    /**
     * Scope untuk filter dinamis dari request.
     */
    public function scopeFilter($query, array $filters)
    {
        // Global search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('project_id', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('item_biaya', 'like', "%{$search}%")
                  ->orWhereHas('kontrak', function ($kq) use ($search) {
                      $kq->where('service_manager', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['service_manager'])) {
            $query->whereHas('kontrak', function ($kq) use ($filters) {
                $kq->where('service_manager', $filters['service_manager']);
            });
        }

        if (!empty($filters['project_id'])) {
            $query->where('project_id', 'like', "%{$filters['project_id']}%");
        }

        if (!empty($filters['project_name'])) {
            $query->where('project_name', 'like', "%{$filters['project_name']}%");
        }

        if (!empty($filters['pic'])) {
            $query->where('pic', 'like', "%{$filters['pic']}%");
        }

        if (!empty($filters['periode'])) {
            $query->where('periode', $filters['periode']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['vendor'])) {
            $query->where('vendor', 'like', "%{$filters['vendor']}%");
        }

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        if (!empty($filters['satuan_kerja'])) {
            $query->where('satuan_kerja', 'like', "%{$filters['satuan_kerja']}%");
        }

        if (!empty($filters['item_biaya'])) {
            $query->where('item_biaya', 'like', "%{$filters['item_biaya']}%");
        }

        return $query;
    }

    /**
     * Relasi ke model Kontrak berdasarkan project_id.
     */
    public function kontrak()
    {
        return $this->belongsTo(Kontrak::class, 'project_id', 'project_id');
    }

    /**
     * Scope untuk ordering periode berdasarkan urutan bulan (bukan alfabet).
     */
    public function scopeOrderByPeriode($query, string $direction = 'asc')
    {
        return $query->orderByRaw("FIELD(periode,
            'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
            'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
        ) {$direction}");
    }

    /**
     * Relasi ke log audit.
     */
    public function logs()
    {
        return $this->hasMany(RealisasiLog::class, 'realisasi_id');
    }

    /**
     * Accessor: format Realisasi Biaya Final dalam format Rupiah.
     */
    public function getRealisasiBiayaFinalFormattedAttribute(): string
    {
        if (is_null($this->realisasi_biaya_final)) {
            return '-';
        }
        return 'Rp ' . number_format($this->realisasi_biaya_final, 0, ',', '.');
    }

    /**
     * Accessor: format Realisasi Biaya IDR dalam format Rupiah.
     */
    public function getRealisasiBiayaIdrFormattedAttribute(): string
    {
        if (is_null($this->realisasi_biaya_idr)) {
            return '-';
        }
        return 'Rp ' . number_format($this->realisasi_biaya_idr, 0, ',', '.');
    }

    /**
     * Accessor dinamis: Nomor SPK / Kontrak Pekerjaan (dari BASTO atau Master Kontrak).
     */
    public function getNoSpkAttribute(): ?string
    {
        return \App\Models\Basto::where('project_id', $this->project_id)
            ->whereNotNull('cost_no')
            ->where('cost_no', '!=', '')
            ->value('cost_no')
            ?: \App\Models\Kontrak::where('project_id', $this->project_id)
                ->whereNotNull('contract_number')
                ->where('contract_number', '!=', '')
                ->value('contract_number');
    }

    /**
     * Accessor dinamis: Nomor Faktur Tagihan / Invoice Vendor (dari tabel Invoices).
     */
    public function getNoInvoiceAttribute(): ?string
    {
        return \App\Models\Invoice::where('project_id', $this->project_id)->value('invoice_number');
    }
}
