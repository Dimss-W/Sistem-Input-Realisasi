<?php

namespace Tests\Feature;

use App\Models\Basto;
use App\Models\Invoice;
use App\Models\Kontrak;
use App\Models\Realisasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossRoleSyncTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    /**
     * Test sinkronisasi BASTO -> Kontrak & Realisasi SPK/Vendor
     */
    public function test_basto_submission_and_approval_syncs_to_kontrak_and_realisasi()
    {
        $dmo = User::where('role', 'dmo')->first() ?? User::factory()->create(['role' => 'dmo']);
        $sm = User::where('role', 'osm_service_manager')->first() ?? User::factory()->create(['role' => 'osm_service_manager']);

        // Buat Proyek & Realisasi tanpa SPK
        $projectId = 'SYNC-TEST-' . time();
        Kontrak::create([
            'project_id'      => $projectId,
            'project_name'    => 'Proyek Uji Sinkronisasi',
            'service_manager' => $sm->name,
            'project_value'   => 50000000,
        ]);

        $realisasi = Realisasi::create([
            'project_id'               => $projectId,
            'project_name'             => 'Proyek Uji Sinkronisasi',
            'item_biaya'               => 'Pekerjaan Jaringan',
            'satuan_kerja'             => 'OSM',
            'pic'                      => 'TEST',
            'periode'                  => 'OKTOBER',
            'tahun'                    => 2026,
            'currency'                 => 'IDR',
            'realisasi_biaya_original' => 20000000,
            'realisasi_biaya_idr'      => 20000000,
            'realisasi_biaya_final'    => 20000000,
            'status'                   => 'UNPAID',
            'record_key'               => md5($projectId . 'Pekerjaan Jaringan'),
        ]);

        // DMO submit BASTO dengan SPK dan Supply Chain
        $spkNumber = 'SPK-SYNC-001';
        $vendorName = 'PT SINERGI TEST';

        $response = $this->actingAs($dmo)->post(route('basto.store'), [
            'project_id'       => $projectId,
            'project_name'     => 'Proyek Uji Sinkronisasi',
            'sm_user_id'       => $sm->id,
            'cost_no'          => $spkNumber,
            'supply_chain'     => $vendorName,
            'cost_value'       => 20000000,
            'cost_based'       => 20000000,
            'cost_date_start'  => '2026-10-01',
            'cost_date_end'    => '2026-10-31',
        ]);

        $response->assertSessionHasNoErrors();

        // Cek Kontrak ter-update
        $kontrak = Kontrak::where('project_id', $projectId)->first();
        $this->assertEquals($spkNumber, $kontrak->contract_number);
        $this->assertEquals($vendorName, $kontrak->resource_management);

        // Cek Realisasi ter-update vendor dan dynamic accessor no_spk
        $realisasi->refresh();
        $this->assertEquals($vendorName, $realisasi->vendor);
        $this->assertEquals($spkNumber, $realisasi->no_spk);
    }

    /**
     * Test sinkronisasi Invoice Vendor -> Realisasi (No Invoice, Vendor, Status LUNAS)
     */
    public function test_invoice_creation_and_payment_syncs_to_realisasi()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $projectId = 'INV-SYNC-' . time();
        Kontrak::create([
            'project_id'      => $projectId,
            'project_name'    => 'Proyek Invoice Sync',
            'service_manager' => 'SM UJI',
            'project_value'   => 100000000,
        ]);

        $realisasi = Realisasi::create([
            'project_id'               => $projectId,
            'project_name'             => 'Proyek Invoice Sync',
            'item_biaya'               => null,
            'satuan_kerja'             => 'OSM',
            'pic'                      => 'TEST',
            'periode'                  => 'OKTOBER',
            'tahun'                    => 2026,
            'currency'                 => 'IDR',
            'realisasi_biaya_original' => 30000000,
            'realisasi_biaya_idr'      => 30000000,
            'realisasi_biaya_final'    => 30000000,
            'status'                   => 'UNPAID',
            'record_key'               => md5($projectId . 'TEST'),
        ]);

        $invoiceNumber = 'INV-SYNC-' . time();
        $vendorName = 'PT MITRA VENDOR UTAMA';

        // Admin buat invoice vendor
        $response = $this->actingAs($admin)->post(route('invoice.store'), [
            'invoice_number'  => $invoiceNumber,
            'project_id'      => $projectId,
            'customer'        => $vendorName,
            'invoice_date'    => '2026-10-05',
            'due_date'        => '2026-11-05',
            'subtotal'        => 30000000,
            'invoice_amount'  => 30000000,
            'notes'           => 'Pengadaan Perangkat Server',
        ]);

        $response->assertSessionHasNoErrors();

        // Realisasi otomatis tersinkronisasi
        $realisasi->refresh();
        $this->assertEquals($vendorName, $realisasi->vendor);
        $this->assertEquals('Pengadaan Perangkat Server', $realisasi->item_biaya);
        $this->assertEquals($invoiceNumber, $realisasi->no_invoice);

        // Lunasi invoice dan cek status realisasi otomatis menjadi PAID
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
        $this->assertNotNull($invoice);

        $this->actingAs($admin)->post(route('invoice.payment.store', $invoice->id), [
            'payment_date'      => '2026-10-10',
            'payment_amount'    => $invoice->invoice_amount,
            'payment_reference' => 'TRF-BANK-' . time() . '-' . rand(100, 999),
        ]);

        $realisasi->refresh();
        $this->assertEquals('PAID', $realisasi->status);
    }
}
