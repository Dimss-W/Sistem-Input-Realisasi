<?php

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

echo "=== STARTING INVOICE & PAYMENT INTEGRATION TEST ===" . PHP_EOL;

// 1. Authenticate as Finance
$credentials = ['username' => 'finance', 'password' => 'password'];
if (!Auth::attempt($credentials)) {
    echo "❌ Error: Failed to login as finance." . PHP_EOL;
    exit(1);
}
echo "✔ Success: Logged in as Finance Officer." . PHP_EOL;

// 2. Create Invoice
$invoiceNum = "TEST-INV-" . time();
$invoice = Invoice::create([
    'invoice_number' => $invoiceNum,
    'project_id'     => 'PS-024-00',
    'project_name'   => 'Pekerjaan Jasa Operasi dan Pemeliharaan Gas',
    'customer'       => 'PGN',
    'sales_user_id'  => 5, // ULFA
    'finance_user_id'=> auth()->id(),
    'invoice_date'   => now(),
    'due_date'       => now()->addDays(30),
    'invoice_amount' => 100000000.00, // 100jt
    'payment_amount' => 0.00,
    'payment_status' => 'unpaid',
]);
echo "✔ Success: Created mock invoice #{$invoice->invoice_number} senilai " . $invoice->invoice_amount_formatted . PHP_EOL;
echo "✔ Outstanding check: " . $invoice->outstanding_formatted . " (Expected: Rp 100.000.000)" . PHP_EOL;

// 3. Record Partial Payment (30jt)
$payment = Payment::create([
    'invoice_id'           => $invoice->id,
    'payment_date'         => now(),
    'payment_amount'       => 30000000.00,
    'payment_reference'    => 'REF-PARTIAL',
    'payment_method'       => 'TRANSFER',
    'processed_by_user_id' => auth()->id(),
]);

$invoice->update([
    'payment_amount' => 30000000.00,
    'payment_status' => 'partial',
]);

$invoice = $invoice->fresh();
echo "✔ Success: Recorded partial payment of " . $payment->payment_amount_formatted . PHP_EOL;
echo "✔ Updated Status: " . strtoupper($invoice->payment_status) . " (Expected: PARTIAL)" . PHP_EOL;
echo "✔ Updated Outstanding: " . $invoice->outstanding_formatted . " (Expected: Rp 70.000.000)" . PHP_EOL;

// 4. Record Full Payment (70jt remaining)
Payment::create([
    'invoice_id'           => $invoice->id,
    'payment_date'         => now(),
    'payment_amount'       => 70000000.00,
    'payment_reference'    => 'REF-FULL',
    'payment_method'       => 'TRANSFER',
    'processed_by_user_id' => auth()->id(),
]);

$invoice->update([
    'payment_amount' => 100000000.00,
    'payment_status' => 'paid',
]);

$invoice = $invoice->fresh();
echo "✔ Success: Recorded remaining payment of Rp 70.000.000" . PHP_EOL;
echo "✔ Updated Status: " . strtoupper($invoice->payment_status) . " (Expected: PAID)" . PHP_EOL;
echo "✔ Updated Outstanding: " . $invoice->outstanding_formatted . " (Expected: Rp 0)" . PHP_EOL;

// Clean up
$invoice->delete();
echo "✔ Cleanup: Mock invoice deleted." . PHP_EOL;

echo "=== INVOICE & PAYMENT INTEGRATION TEST COMPLETED ===" . PHP_EOL;
