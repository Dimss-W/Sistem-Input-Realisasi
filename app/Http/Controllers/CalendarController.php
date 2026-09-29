<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $file = storage_path('Excel/Book1.xlsx');
        $events = [];

        if (file_exists($file)) {
            try {
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getSheetByName('KONTRAK_2026');
                if ($sheet) {
                    $rows = $sheet->toArray(null, true, true, true);

                    $parseDate = function($v) {
                        if (is_null($v) || $v === '') return null;
                        if ($v instanceof \DateTime) return $v->format('Y-m-d');
                        $v = trim((string) $v);
                        $v = preg_replace('/\s+\d+:\d+.*$/', '', $v); // Remove time
                        $formats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'j/n/Y', 'n/j/Y', 'Y/m/d'];
                        foreach ($formats as $fmt) {
                            $dt = \DateTime::createFromFormat($fmt, $v);
                            if ($dt) return $dt->format('Y-m-d');
                        }
                        if (is_numeric($v)) {
                            $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$v);
                            return $dt->format('Y-m-d');
                        }
                        return null;
                    };

                    $parseNum = function($v) {
                        if (is_null($v) || $v === '' || $v === '-') return 0;
                        if (is_float($v) || is_int($v)) return (float) $v;
                        $v = trim((string) $v);
                        $v = str_replace([',', ' '], '', $v);
                        return is_numeric($v) ? (float) $v : 0;
                    };

                    foreach ($rows as $idx => $row) {
                        if ($idx === 1) continue; // Skip header row
                        $projectId = trim($row['A'] ?? '');
                        if ($projectId === '') continue;

                        $projectName = trim($row['G'] ?? '');
                        $client      = trim($row['E'] ?? '');
                        $value       = $parseNum($row['N'] ?? 0);
                        $endDateStr  = $row['M'] ?? null;
                        $endDate     = $parseDate($endDateStr);

                        if ($endDate) {
                            $formattedVal = 'Rp ' . number_format($value, 0, ',', '.');
                            $events[] = [
                                'id'          => $projectId . '_' . $idx,
                                'title'       => "$client - " . (strlen($projectName) > 30 ? substr($projectName, 0, 30) . '...' : $projectName) . " ($formattedVal)",
                                'start'       => $endDate,
                                'allDay'      => true,
                                'color'       => '#2563eb', // Premium Blue
                                'description' => "Project ID: $projectId\nNama Project: $projectName\nClient: $client\nNilai: $formattedVal\nTanggal Jatuh Tempo: " . date('d-m-Y', strtotime($endDate)),
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log or ignore to avoid breaking the page
                logger()->error("Calendar Excel import error: " . $e->getMessage());
            }
        }

        return view('calendar.index', compact('events'));
    }
}
