<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\RealisasiImportService;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with REAL data from Excel.
     */
    public function run(): void
    {
        $file = storage_path('Excel/Book1.xlsx');
        if (!file_exists($file)) {
            $this->command->error("Excel data file not found at: {$file}");
            return;
        }

        $this->command->info("Starting real data import from Excel: Book1.xlsx...");

        // 1. Truncate tables
        DB::table('realisasi')->truncate();
        DB::table('kontrak')->truncate();
        DB::table('prognosa')->truncate();
        DB::table('realisasi_logs')->truncate();

        $spreadsheet = IOFactory::load($file);

        // 2. Import Realisasi
        $importService = app(RealisasiImportService::class);
        $result = $importService->import($file, null, '127.0.0.1');
        $this->command->info("Realisasi: {$result['inserted']} rows inserted.");

        // Helpers
        $parseNum = function($v) {
            if (is_null($v) || $v === '' || $v === '-') return null;
            if (is_float($v) || is_int($v)) return (float) $v;
            $v = trim((string) $v);
            $v = preg_replace('/[Rp\s]/i', '', $v);
            $v = preg_replace('/[A-Z]{3}/i', '', $v);
            $hasComma = strpos($v, ',') !== false;
            $hasDot   = strpos($v, '.') !== false;
            if ($hasComma && $hasDot) {
                if (strrpos($v, ',') > strrpos($v, '.')) {
                    $v = str_replace('.', '', $v); $v = str_replace(',', '.', $v);
                } else { $v = str_replace(',', '', $v); }
            } elseif ($hasComma) {
                $v = str_replace(',', '.', $v);
            } elseif ($hasDot && substr_count($v, '.') > 1) {
                $v = str_replace('.', '', $v);
            }
            return is_numeric($v) ? (float) $v : null;
        };

        $parseDate = function($v) {
            if (is_null($v) || $v === '') return null;
            if ($v instanceof \DateTime) return $v->format('Y-m-d');
            $v = trim((string) $v);
            $v = preg_replace('/\s+\d+:\d+.*$/', '', $v);
            $formats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y'];
            foreach ($formats as $fmt) {
                $dt = \DateTime::createFromFormat($fmt, $v);
                if ($dt) return $dt->format('Y-m-d');
            }
            return null;
        };

        // 3. Import Kontrak
        $kontrakSheet = $spreadsheet->getSheetByName('KONTRAK_2026');
        $kontrakRows = $kontrakSheet->toArray(null, true, true, true);
        $kInserted = 0;
        foreach ($kontrakRows as $idx => $row) {
            if ($idx === 1) continue;
            $pid = trim($row['A'] ?? '');
            if ($pid === '') continue;

            DB::table('kontrak')->insert([
                'project_id'            => $pid,
                'tahun'                 => (int) ($row['B'] ?? 2026) ?: null,
                'status'                => trim($row['C'] ?? '') ?: null,
                'service_manager'       => trim($row['D'] ?? '') ?: null,
                'project_client'        => trim($row['E'] ?? '') ?: null,
                'project_classification'=> trim($row['F'] ?? '') ?: null,
                'project_name'          => trim($row['G'] ?? '') ?: null,
                'contract_number'       => trim($row['H'] ?? '') ?: null,
                'category_contract'     => trim($row['I'] ?? '') ?: null,
                'skema'                 => trim($row['J'] ?? '') ?: null,
                'date_of_contract'      => $parseDate($row['K'] ?? null),
                'start_date'            => $parseDate($row['L'] ?? null),
                'end_date'              => $parseDate($row['M'] ?? null),
                'project_value'         => $parseNum($row['N'] ?? null),
                'costbased'             => $parseNum($row['O'] ?? null),
                'actual_cost_konfirmasi'=> $parseNum($row['P'] ?? null),
                'actual_cost_admin'     => $parseNum($row['Q'] ?? null),
                'persentase'            => $parseNum($row['R'] ?? null),
                'amandemen'             => trim($row['S'] ?? '') ?: null,
                'tkdn'                  => $parseNum($row['T'] ?? null),
                'description'           => trim($row['U'] ?? '') ?: null,
                'resource_management'   => trim($row['V'] ?? '') ?: null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);
            $kInserted++;
        }
        $this->command->info("Kontrak: {$kInserted} rows inserted.");

        // 4. Import Prognosa
        $prognosaSheet = $spreadsheet->getSheetByName('PROGNOSA_2026');
        $prognosaRows = $prognosaSheet->toArray(null, true, true, true);
        $pInserted = 0;
        foreach ($prognosaRows as $idx => $row) {
            if ($idx === 1) continue;
            $pid = trim($row['B'] ?? '');
            if ($pid === '') continue;

            DB::table('prognosa')->insert([
                'source_row'       => (int) ($row['A'] ?? 0) ?: null,
                'project_id'       => $pid,
                'project_name'     => trim($row['C'] ?? '') ?: null,
                'activity'         => trim($row['D'] ?? '') ?: null,
                'satuan_kerja'     => trim($row['E'] ?? '') ?: null,
                'pic'              => trim($row['F'] ?? '') ?: null,
                'prognosa_biaya'   => $parseNum($row['G'] ?? null),
                'periode'          => strtoupper(trim($row['H'] ?? '')) ?: null,
                'partner'          => trim($row['I'] ?? '') ?: null,
                'keterangan'       => trim($row['J'] ?? '') ?: null,
                'tahun_original'   => (int) ($row['K'] ?? 0) ?: null,
                'tahun_normalized' => (int) ($row['L'] ?? 0) ?: null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
            $pInserted++;
        }
        $this->command->info("Prognosa: {$pInserted} rows inserted.");

        // 5. Seed Users for Auth/RBAC
        $this->command->info("Seeding users...");
        DB::table('users')->truncate();

        $users = [
            [
                'name' => 'Masyita Mustikas',
                'username' => 'masyita.mustikas',
                'email' => 'masyita.mustikas@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mekar Fauzia',
                'username' => 'mekar.fauzia',
                'email' => 'mekar.fauzia@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GILANG',
                'username' => 'gilang',
                'email' => 'gilang@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'JIHAD',
                'username' => 'jihad',
                'email' => 'jihad@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'JODY',
                'username' => 'jody',
                'email' => 'jody@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'RIKI',
                'username' => 'riki',
                'email' => 'riki@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'RIZKY',
                'username' => 'rizky',
                'email' => 'rizky@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SANDI',
                'username' => 'sandi',
                'email' => 'sandi@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SUKMA',
                'username' => 'sukma',
                'email' => 'sukma@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TAUFAN',
                'username' => 'taufan',
                'email' => 'taufan@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'TITA',
                'username' => 'tita',
                'email' => 'tita@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'YIYIP',
                'username' => 'yiyip',
                'email' => 'yiyip@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AHMAD',
                'username' => 'ahmad',
                'email' => 'ahmad@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_service_manager',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'QC Inspector',
                'username' => 'qc',
                'email' => 'qc@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'osm_qc',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'DMO Staff',
                'username' => 'dmo',
                'email' => 'dmo@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'dmo',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ULFA',
                'username' => 'ulfa',
                'email' => 'ulfa@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'sales',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance Officer',
                'username' => 'finance',
                'email' => 'finance@pgncom.co.id',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'finance',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
        $this->command->info("Users successfully seeded: masyita.mustikas, mekar.fauzia, gilang, jihad, jody, riki, rizky, sandi, sukma, taufan, tita, yiyip, ahmad, qc, dmo, ulfa, finance (password: 'password').");

        $this->command->info("✅ Database successfully seeded with 100% REAL data and Users.");
    }
}
