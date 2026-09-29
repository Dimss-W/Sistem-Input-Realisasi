<?php

namespace App\Services;

use App\Models\Realisasi;
use App\Models\RealisasiLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class RealisasiImportService
{
    /**
     * Header kolom yang diharapkan di file Excel (case-insensitive).
     */
    protected array $expectedHeaders = [
        'source_row',
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

    /**
     * Field yang di-UPPERCASE saat normalisasi.
     */
    protected array $uppercaseFields = [
        'project_id', 'item_biaya', 'satuan_kerja', 'pic',
        'periode', 'status', 'vendor', 'sifat', 'currency', 'data_flag',
    ];

    /**
     * Periode valid.
     */
    protected array $validPeriode = [
        'JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI',
        'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER',
    ];

    /**
     * Proses import file Excel.
     *
     * @return array Summary hasil import
     */
    public function import(string $filePath, ?int $userId = null, ?string $ip = null): array
    {
        $summary = [
            'total'   => 0,
            'inserted' => 0,
            'updated'  => 0,
            'skipped'  => 0,
            'errors'   => [],
        ];

        $spreadsheet = IOFactory::load($filePath);
        if (in_array('Realisasi_2026', $spreadsheet->getSheetNames())) {
            $sheet = $spreadsheet->getSheetByName('Realisasi_2026');
        } else {
            $sheet = $spreadsheet->getActiveSheet();
        }
        $rows  = $sheet->toArray(null, true, true, true); // A,B,C... keys

        if (empty($rows)) {
            $summary['errors'][] = 'File Excel kosong.';
            return $summary;
        }

        // Baca header dari baris pertama
        $headerRow = array_shift($rows);
        $headerMap = $this->buildHeaderMap($headerRow);

        // Validasi header
        $missingHeaders = $this->validateHeaders($headerMap);
        if (!empty($missingHeaders)) {
            $summary['errors'][] = 'Header kolom tidak lengkap. Kolom yang tidak ditemukan: ' . implode(', ', $missingHeaders);
            return $summary;
        }

        $summary['total'] = count($rows);

        DB::beginTransaction();

        try {
            $projectNamesCache = [];
            if (in_array('MASTER_PROJECT', $spreadsheet->getSheetNames())) {
                $masterSheet = $spreadsheet->getSheetByName('MASTER_PROJECT');
                $masterRows = $masterSheet->toArray(null, true, true, true);
                foreach ($masterRows as $mIdx => $mRow) {
                    if ($mIdx === 1) continue; // Skip header
                    $pName = trim($mRow['D'] ?? '');
                    if ($pName !== '') {
                        if (!empty($mRow['A'])) {
                            $projectNamesCache[trim($mRow['A'])] = $pName;
                        }
                        if (!empty($mRow['B'])) {
                            $projectNamesCache[trim($mRow['B'])] = $pName;
                        }
                    }
                }
            }

            foreach ($rows as $rowIndex => $rowData) {
                $lineNumber = $rowIndex + 2; // +1 header, +1 base-1

                try {
                    $mapped = $this->mapRow($rowData, $headerMap);

                    // Skip baris yang benar-benar kosong
                    if ($this->isEmptyRow($mapped)) {
                        $summary['skipped']++;
                        continue;
                    }

                    $normalized = $this->normalizeData($mapped, $lineNumber);

                    if (empty($normalized['project_id'])) {
                        $summary['errors'][] = "Baris {$lineNumber}: Project ID kosong, baris dilewati.";
                        $summary['skipped']++;
                        continue;
                    }

                    if (empty($normalized['project_name'])) {
                        $projectId = $normalized['project_id'];
                        if (isset($projectNamesCache[$projectId])) {
                            $normalized['project_name'] = $projectNamesCache[$projectId];
                        } else {
                            $dbProjectName = Realisasi::where('project_id', $projectId)
                                ->whereNotNull('project_name')
                                ->where('project_name', '!=', '')
                                ->value('project_name');
                            if ($dbProjectName) {
                                $normalized['project_name'] = $dbProjectName;
                                $projectNamesCache[$projectId] = $dbProjectName;
                            }
                        }
                    }

                    if (empty($normalized['project_name'])) {
                        $summary['errors'][] = "Baris {$lineNumber}: Project Name kosong untuk Project ID '{$normalized['project_id']}', baris dilewati.";
                        $summary['skipped']++;
                        continue;
                    }

                    // Cache the project name for subsequent rows
                    $projectNamesCache[$normalized['project_id']] = $normalized['project_name'];

                    $recordKey = Realisasi::generateRecordKey($normalized);
                    $normalized['record_key'] = $recordKey;

                    // UPSERT: cari berdasarkan record_key
                    $existing = Realisasi::where('record_key', $recordKey)->first();

                    if ($existing) {
                        // UPDATE — ambil old data untuk log
                        $oldData = $existing->toArray();
                        $existing->fill($normalized);
                        $existing->save();

                        RealisasiLog::record(
                            $existing->id, 'IMPORT',
                            $oldData, $existing->toArray(),
                            $userId, $ip
                        );

                        $summary['updated']++;
                    } else {
                        // INSERT baru
                        $newRecord = Realisasi::create($normalized);

                        RealisasiLog::record(
                            $newRecord->id, 'IMPORT',
                            null, $newRecord->toArray(),
                            $userId, $ip
                        );

                        $summary['inserted']++;
                    }
                } catch (\Exception $e) {
                    $summary['errors'][] = "Baris {$lineNumber}: " . $e->getMessage();
                    Log::error("Import error at row {$lineNumber}: " . $e->getMessage());
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $summary['errors'][] = 'Error fatal: ' . $e->getMessage() . ' — semua perubahan dibatalkan (rollback).';
            Log::error('Import fatal error: ' . $e->getMessage());
        }

        return $summary;
    }

    /**
     * Build header map: nama_kolom_lowercase => kolom_excel (A, B, C...)
     */
    protected function buildHeaderMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $col => $value) {
            if (!is_null($value)) {
                $key = strtolower(trim(str_replace(' ', '_', $value)));
                $map[$key] = $col;
            }
        }
        return $map;
    }

    /**
     * Validasi bahwa semua header penting ada.
     * Hanya wajibkan project_id, project_name, periode, tahun.
     */
    protected function validateHeaders(array $headerMap): array
    {
        $required = ['project_id', 'project_name', 'periode', 'tahun'];
        $missing  = [];
        foreach ($required as $h) {
            if (!array_key_exists($h, $headerMap)) {
                $missing[] = $h;
            }
        }
        return $missing;
    }

    /**
     * Map row data ke array asosiatif berdasarkan header map.
     */
    protected function mapRow(array $rowData, array $headerMap): array
    {
        $result = [];
        foreach ($this->expectedHeaders as $field) {
            if (isset($headerMap[$field])) {
                $col = $headerMap[$field];
                $result[$field] = $rowData[$col] ?? null;
            } else {
                $result[$field] = null;
            }
        }
        return $result;
    }

    /**
     * Cek apakah baris benar-benar kosong.
     */
    protected function isEmptyRow(array $data): bool
    {
        $important = ['project_id', 'project_name', 'periode', 'tahun'];
        foreach ($important as $field) {
            if (!empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Normalisasi data:
     * - Trim whitespace
     * - Uppercase field tertentu
     * - Parse angka (decimal, scientific notation)
     * - Normalisasi periode
     * - Pastikan tahun valid
     */
    protected function normalizeData(array $data, int $lineNumber): array
    {
        // Trim semua nilai string
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        // Uppercase field tertentu
        foreach ($this->uppercaseFields as $field) {
            if (!empty($data[$field]) && is_string($data[$field])) {
                $data[$field] = strtoupper($data[$field]);
            }
        }

        // Normalisasi project_name — pertahankan case asli, hanya trim
        if (!empty($data['project_name'])) {
            $data['project_name'] = trim($data['project_name']);
        }

        // Normalisasi link_evidence — pertahankan case asli
        // (jangan uppercase)

        // Parse angka
        $numericFields = ['realisasi_biaya_original', 'realisasi_biaya_idr', 'realisasi_biaya_final'];
        foreach ($numericFields as $field) {
            $data[$field] = $this->parseNumber($data[$field]);
        }

        // Parse source_row dan tahun
        $data['source_row'] = !empty($data['source_row']) ? (int) $data['source_row'] : null;
        $data['tahun']      = !empty($data['tahun']) ? (int) $data['tahun'] : null;

        // Validasi & normalisasi periode
        if (!empty($data['periode'])) {
            $periode = strtoupper(trim($data['periode']));
            if (!in_array($periode, $this->validPeriode)) {
                // Coba mapping nama bulan pendek
                $periodeMap = [
                    'JAN' => 'JANUARI', 'FEB' => 'FEBRUARI', 'MAR' => 'MARET',
                    'APR' => 'APRIL',   'MEI' => 'MEI',       'JUN' => 'JUNI',
                    'JUL' => 'JULI',    'AGU' => 'AGUSTUS',   'SEP' => 'SEPTEMBER',
                    'OKT' => 'OKTOBER', 'NOV' => 'NOVEMBER',  'DES' => 'DESEMBER',
                ];
                $data['periode'] = $periodeMap[$periode] ?? $periode;
            } else {
                $data['periode'] = $periode;
            }
        }

        // Default currency & exchange rate conversion
        if (empty($data['currency'])) {
            $data['currency'] = 'IDR';
        }

        $fxService = new ExchangeRateService();
        $fxData    = $fxService->getLiveRates();
        $kursRates = $fxData['rates'];

        // Convert foreign currency to IDR if realisasi_biaya_idr is null/empty
        if (is_null($data['realisasi_biaya_idr']) && !is_null($data['realisasi_biaya_original'])) {
            $rate = $kursRates[$data['currency']] ?? 1.0;
            $data['realisasi_biaya_idr'] = round($data['realisasi_biaya_original'] * $rate, 2);
        }

        if (is_null($data['realisasi_biaya_final'])) {
            if (!is_null($data['realisasi_biaya_idr'])) {
                $data['realisasi_biaya_final'] = $data['realisasi_biaya_idr'];
            } elseif (!is_null($data['realisasi_biaya_original'])) {
                $rate = $kursRates[$data['currency']] ?? 1.0;
                $data['realisasi_biaya_final'] = round($data['realisasi_biaya_original'] * $rate, 2);
            }
        }

        return $data;
    }

    /**
     * Parse nilai numerik dari berbagai format:
     * - "1.234.567" (format Indonesia)
     * - "1,234,567" (format EN)
     * - "1234567.89"
     * - "8.63E+8" (scientific notation)
     * - null/kosong
     */
    protected function parseNumber(mixed $value): ?float
    {
        if (is_null($value) || $value === '' || $value === '-') {
            return null;
        }

        if (is_float($value) || is_int($value)) {
            return (float) $value;
        }

        $value = trim((string) $value);

        // Hapus karakter Rp, IDR, USD, spasi
        $value = preg_replace('/[Rp\s]/i', '', $value);
        $value = preg_replace('/[A-Z]{3}/i', '', $value);

        $hasComma = strpos($value, ',') !== false;
        $hasDot = strpos($value, '.') !== false;

        if ($hasComma && $hasDot) {
            $lastComma = strrpos($value, ',');
            $lastDot = strrpos($value, '.');
            if ($lastComma > $lastDot) {
                // Koma desimal: 1.234,56
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                // Titik desimal: 1,234.56
                $value = str_replace(',', '', $value);
            }
        } elseif ($hasComma) {
            // Hanya ada koma desimal: 48529974,61
            $value = str_replace(',', '.', $value);
        } elseif ($hasDot) {
            // Hanya ada titik. Jika ada lebih dari satu titik, itu ribuan: 1.234.567
            if (substr_count($value, '.') > 1) {
                $value = str_replace('.', '', $value);
            }
        }

        return is_numeric($value) ? (float) $value : null;
    }
}
