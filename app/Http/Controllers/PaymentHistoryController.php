<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PaymentHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:finance,admin');
    }

    /**
     * Display listing of payment transactions with filters.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $paymentMethod = $request->input('payment_method');

        $query = Payment::with(['invoice', 'processedBy']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($iq) use ($search) {
                      $iq->where('invoice_number', 'like', "%{$search}%")
                         ->orWhere('customer', 'like', "%{$search}%")
                         ->orWhere('project_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($fromDate) {
            $query->whereDate('payment_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('payment_date', '<=', $toDate);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15)->withQueryString();
        
        // Total stats
        $totalPaidAmount = Payment::sum('payment_amount');
        $totalFilteredAmount = (clone $query)->sum('payment_amount');

        // Distinct methods for filter dropdown
        $availableMethods = Payment::select('payment_method')
            ->distinct()
            ->whereNotNull('payment_method')
            ->pluck('payment_method');

        return view('payment.history', compact(
            'payments',
            'search',
            'fromDate',
            'toDate',
            'paymentMethod',
            'totalPaidAmount',
            'totalFilteredAmount',
            'availableMethods'
        ));
    }

    /**
     * Display printable official digital payment receipt (Kuitansi Resmi).
     */
    public function receipt($id)
    {
        $payment = Payment::with(['invoice.sales', 'invoice.finance', 'processedBy'])->findOrFail($id);
        $terbilangAmount = self::terbilangRupiah($payment->payment_amount);

        return view('payment.receipt', compact('payment', 'terbilangAmount'));
    }

    /**
     * Export payment transactions to formatted Excel (.xlsx).
     */
    public function export(Request $request)
    {
        $search = $request->input('search');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $paymentMethod = $request->input('payment_method');

        $query = Payment::with(['invoice', 'processedBy']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($iq) use ($search) {
                      $iq->where('invoice_number', 'like', "%{$search}%")
                         ->orWhere('customer', 'like', "%{$search}%")
                         ->orWhere('project_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($fromDate) {
            $query->whereDate('payment_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('payment_date', '<=', $toDate);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap_Kas_Masuk');

        // Document Title Block
        $sheet->setCellValue('A1', 'PT PERTAMINA GAS / PGNCOM — DIVISI KEUANGAN & TREASURY');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF0F2A4A'));

        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI KAS MASUK & PEMBAYARAN INVOICE');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new Color('FF475569'));

        $filterDesc = [];
        if ($fromDate && $toDate) {
            $filterDesc[] = "Periode: {$fromDate} s/d {$toDate}";
        } elseif ($fromDate) {
            $filterDesc[] = "Mulai: {$fromDate}";
        } elseif ($toDate) {
            $filterDesc[] = "Sampai: {$toDate}";
        }
        if ($paymentMethod) {
            $filterDesc[] = "Metode: {$paymentMethod}";
        }
        $infoText = 'Tanggal Ekspor: ' . now()->translatedFormat('d F Y H:i') . (!empty($filterDesc) ? ' | ' . implode(' | ', $filterDesc) : '');
        $sheet->setCellValue('A3', $infoText);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9)->setColor(new Color('FF64748B'));

        // Table Header
        $headers = [
            'A5' => 'No',
            'B5' => 'Tanggal Masuk',
            'C5' => 'No. Referensi',
            'D5' => 'No. Invoice',
            'E5' => 'Customer / Klien',
            'F5' => 'Nama Proyek',
            'G5' => 'Metode Bayar',
            'H5' => 'Kas Masuk (IDR)',
            'I5' => 'Potongan PPh 23 (IDR)',
            'J5' => 'No. Bukti Potong',
            'K5' => 'Total Diselesaikan (IDR)',
            'L5' => 'Diproses Oleh',
            'M5' => 'Catatan / Keterangan',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Header Styling
        $headerRange = 'A5:M5';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new Color('FFFFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F2A4A');
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(5)->setRowHeight(26);

        // Populate Data
        $rowNum = 6;
        foreach ($payments as $index => $payment) {
            $pphAmount = (float)($payment->pph23_amount ?? 0);
            $payAmount = (float)$payment->payment_amount;
            $settledAmount = $payAmount + $pphAmount;

            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-');
            $sheet->setCellValue('C' . $rowNum, $payment->payment_reference ?: '-');
            $sheet->setCellValue('D' . $rowNum, $payment->invoice ? $payment->invoice->invoice_number : '-');
            $sheet->setCellValue('E' . $rowNum, $payment->invoice ? $payment->invoice->customer : '-');
            $sheet->setCellValue('F' . $rowNum, $payment->invoice ? ($payment->invoice->project_name ?: '-') : '-');
            $sheet->setCellValue('G' . $rowNum, $payment->payment_method ?: 'TRANSFER');
            $sheet->setCellValue('H' . $rowNum, $payAmount);
            $sheet->setCellValue('I' . $rowNum, $pphAmount);
            $sheet->setCellValue('J' . $rowNum, $payment->bupot_number ?: '-');
            $sheet->setCellValue('K' . $rowNum, $settledAmount);
            $sheet->setCellValue('L' . $rowNum, $payment->processedBy ? $payment->processedBy->name : 'System');
            $sheet->setCellValue('M' . $rowNum, $payment->notes ?: '-');

            // Zebra styling
            if ($rowNum % 2 == 0) {
                $sheet->getStyle("A{$rowNum}:M{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getRowDimension($rowNum)->setRowHeight(20);
            $rowNum++;
        }

        // Number Formatting for Currency columns H, I, K
        $sheet->getStyle('H6:I' . ($rowNum))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('K6:K' . ($rowNum))->getNumberFormat()->setFormatCode('#,##0');

        // Total Row
        $sheet->setCellValue('A' . $rowNum, 'TOTAL PENERIMAAN & PENYELESAIAN');
        $sheet->mergeCells("A{$rowNum}:G{$rowNum}");
        if ($rowNum > 6) {
            $sheet->setCellValue('H' . $rowNum, "=SUM(H6:H" . ($rowNum - 1) . ")");
            $sheet->setCellValue('I' . $rowNum, "=SUM(I6:I" . ($rowNum - 1) . ")");
            $sheet->setCellValue('J' . $rowNum, '');
            $sheet->setCellValue('K' . $rowNum, "=SUM(K6:K" . ($rowNum - 1) . ")");
        } else {
            $sheet->setCellValue('H' . $rowNum, 0);
            $sheet->setCellValue('I' . $rowNum, 0);
            $sheet->setCellValue('J' . $rowNum, '');
            $sheet->setCellValue('K' . $rowNum, 0);
        }
        $sheet->setCellValue('L' . $rowNum, '');
        $sheet->setCellValue('M' . $rowNum, '');

        $totalRange = "A{$rowNum}:M{$rowNum}";
        $sheet->getStyle($totalRange)->getFont()->setBold(true);
        $sheet->getStyle($totalRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($rowNum)->setRowHeight(24);

        // Borders
        $dataRange = 'A5:M' . $rowNum;
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFCBD5E1');

        // Alignment
        $sheet->getStyle('A6:A' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B6:B' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C6:C' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D6:D' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G6:G' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H6:I' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('J6:J' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K6:K' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('L6:L' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-fit column widths
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Rekap_Kas_Masuk_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Indonesian number to words conversion.
     */
    public static function terbilang($angka)
    {
        $angka = abs((float) $angka);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';

        if ($angka < 12) {
            $temp = $huruf[(int)$angka];
        } elseif ($angka < 20) {
            $temp = self::terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            $mod = (int)$angka % 10;
            $temp = self::terbilang((int)($angka / 10)) . ' Puluh' . ($mod > 0 ? ' ' . self::terbilang($mod) : '');
        } elseif ($angka < 200) {
            $mod = (int)$angka - 100;
            $temp = 'Seratus' . ($mod > 0 ? ' ' . self::terbilang($mod) : '');
        } elseif ($angka < 1000) {
            $mod = (int)$angka % 100;
            $temp = self::terbilang((int)($angka / 100)) . ' Ratus' . ($mod > 0 ? ' ' . self::terbilang($mod) : '');
        } elseif ($angka < 2000) {
            $mod = (int)$angka - 1000;
            $temp = 'Seribu' . ($mod > 0 ? ' ' . self::terbilang($mod) : '');
        } elseif ($angka < 1000000) {
            $mod = fmod($angka, 1000);
            $temp = self::terbilang((int)($angka / 1000)) . ' Ribu' . ($mod > 0 ? ' ' . self::terbilang($mod) : '');
        } elseif ($angka < 1000000000) {
            $mod = fmod($angka, 1000000);
            $temp = self::terbilang((int)($angka / 1000000)) . ' Juta' . ($mod > 0 ? ' ' . self::terbilang($mod) : '');
        } elseif ($angka < 1000000000000) {
            $mod = fmod($angka, 1000000000);
            $temp = self::terbilang((int)($angka / 1000000000)) . ' Miliar' . ($mod > 0 ? ' ' . self::terbilang($mod) : '');
        } elseif ($angka < 1000000000000000) {
            $mod = fmod($angka, 1000000000000);
            $temp = self::terbilang((int)($angka / 1000000000000)) . ' Triliun' . ($mod > 0 ? ' ' . self::terbilang($mod) : '');
        }

        return trim(preg_replace('/\s+/', ' ', $temp));
    }

    /**
     * Indonesian number to words with 'Rupiah' suffix.
     */
    public static function terbilangRupiah($angka)
    {
        if ((float) $angka == 0) {
            return 'Nol Rupiah';
        }
        return trim(self::terbilang($angka)) . ' Rupiah';
    }
}
