<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kontrak;
use App\Models\Realisasi;
use App\Models\PurchaseOrder;
use App\Models\Prognosa;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OperationalEnhancementsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Feature 1: Admin can quick-update vendor invoice status to PAID in 1-click.
     */
    public function test_admin_can_quick_update_vendor_invoice_status()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Find or create temporary test realisasi within transaction
        $kontrak = Kontrak::first();
        $realisasi = Realisasi::create([
            'project_id' => $kontrak ? $kontrak->project_id : 'PROJ-TEST-OP',
            'project_name' => 'Project Op Test',
            'periode' => 'JANUARI',
            'tahun' => 2026,
            'item_biaya' => 'Vendor Test Invoice',
            'status' => 'UNPAID',
            'realisasi_biaya_final' => 50000000,
        ]);

        $response = $this->post(route('monitoring.invoice-vendor.update-status', $realisasi->id), [
            'status' => 'PAID',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('PAID', $realisasi->fresh()->status);
    }

    /**
     * Test unauthorized role (e.g. QC) cannot quick-update vendor invoice status.
     */
    public function test_non_admin_cannot_quick_update_vendor_invoice_status()
    {
        $qc = User::where('role', 'osm_qc')->first();
        if ($qc) {
            $this->actingAs($qc);

            $kontrak = Kontrak::first();
            $realisasi = Realisasi::create([
                'project_id' => $kontrak ? $kontrak->project_id : 'PROJ-TEST-OP',
                'project_name' => 'Project Op Test',
                'periode' => 'JANUARI',
                'tahun' => 2026,
                'status' => 'UNPAID',
            ]);

            $response = $this->post(route('monitoring.invoice-vendor.update-status', $realisasi->id), [
                'status' => 'PAID',
            ]);

            $response->assertStatus(403);
            $this->assertEquals('UNPAID', $realisasi->fresh()->status);
        }
    }

    /**
     * Test Feature 4: Early warning for vendor invoice aging > 90 days.
     */
    public function test_vendor_invoice_overdue_90_days_flag_and_filter()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $kontrak = Kontrak::first();

        // Invoice from earlier period (> 90 days ago) that is unpaid
        $overdueInvoice = Realisasi::create([
            'project_id' => $kontrak ? $kontrak->project_id : 'PROJ-TEST-OP',
            'project_name' => 'Project Op Test',
            'periode' => 'JANUARI',
            'tahun' => 2026,
            'item_biaya' => 'Vendor Old Invoice',
            'status' => 'UNPAID',
            'realisasi_biaya_final' => 75000000,
        ]);

        // Model attribute calculation
        $this->assertTrue($overdueInvoice->is_overdue_90_days);

        // Filter request
        $response = $this->get(route('monitoring.invoice-vendor', ['risk_aging' => '1']));
        $response->assertStatus(200);
        $response->assertSee('Risiko Hangus');
    }

    /**
     * Test Feature 2: Procurement PO creation with Term of Payment (TOP) auto-projects due date & Prognosa.
     */
    public function test_procurement_po_creation_with_top_and_prognosa_sync()
    {
        $procurement = User::where('role', 'procurement')->first();
        $this->actingAs($procurement);

        $kontrak = Kontrak::first();
        $this->assertNotNull($kontrak, 'Kontrak must exist in database');

        $poDate = Carbon::create(2026, 1, 15);
        $topDays = 120; // 4 months -> due in May 2026 (MEI 2026)

        $response = $this->post(route('procurement.orders.store'), [
            'project_id' => $kontrak->project_id,
            'po_number' => 'PO-TEST-OP-120',
            'po_title' => 'Pengadaan Switch Core Datacenter',
            'vendor_name' => 'PT Mitra Pengadaan Sukses',
            'order_date' => $poDate->toDateString(),
            'po_amount' => 125000000,
            'term_of_payment' => $topDays,
            'description' => 'Pengadaan Perangkat Jaringan',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check PO record
        $po = PurchaseOrder::where('po_number', 'PO-TEST-OP-120')->first();
        $this->assertNotNull($po);
        $this->assertEquals(120, $po->term_of_payment);
        $this->assertEquals('MEI', $po->prognosa_periode);
        $this->assertEquals(2026, $po->prognosa_tahun);

        // Check auto-created Prognosa record
        $prognosa = Prognosa::where('po_id', $po->id)->first();
        $this->assertNotNull($prognosa);
        $this->assertEquals('MEI', $prognosa->periode);
        $this->assertEquals(2026, $prognosa->tahun_normalized);
        $this->assertEquals(125000000, $prognosa->prognosa_biaya);
        $this->assertEquals('procurement_po', $prognosa->data_source);
        $this->assertFalse((bool) $prognosa->is_overridden);
    }

    /**
     * Test Feature 3: Service Manager can reconcile & override prognosa using PO data.
     */
    public function test_service_manager_can_reconcile_prognosa_with_po()
    {
        $sm = User::where('role', 'osm_service_manager')->first();
        $this->actingAs($sm);

        $kontrak = Kontrak::first();

        // Create PO
        $po = PurchaseOrder::create([
            'project_id' => $kontrak->project_id,
            'po_number' => 'PO-RECON-001',
            'po_title' => 'Pengadaan Kabel Fiber Optik',
            'vendor_name' => 'PT Vendor Reconcile',
            'order_date' => '2026-02-01',
            'po_amount' => 150000000,
            'term_of_payment' => 60,
            'payment_due_date' => '2026-04-02',
            'prognosa_periode' => 'APRIL',
            'prognosa_tahun' => 2026,
            'status' => 'approved',
        ]);

        // Create manual SM prognosa with lower rough estimate
        $prognosa = Prognosa::create([
            'project_id' => $kontrak->project_id,
            'project_name' => $kontrak->project_name,
            'periode' => 'APRIL',
            'tahun_normalized' => 2026,
            'prognosa_biaya' => 80000000, // SM initial rough estimate
            'data_source' => 'sm_manual',
            'po_id' => $po->id,
            'is_overridden' => false,
        ]);

        // SM executes reconcile override
        $response = $this->post(route('prognosa.reconcile', $prognosa->id), [
            'action' => 'use_po',
            'po_id' => $po->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $prognosa->refresh();
        $this->assertTrue((bool) $prognosa->is_overridden);
        $this->assertEquals(150000000, $prognosa->prognosa_biaya);
        $this->assertEquals('procurement_po', $prognosa->data_source);

        // SM can also reset back if needed
        $resetResponse = $this->post(route('prognosa.reconcile', $prognosa->id), [
            'action' => 'reset_sm',
        ]);
        $resetResponse->assertRedirect();
        $prognosa->refresh();
        $this->assertFalse((bool) $prognosa->is_overridden);
        $this->assertEquals('sm_manual', $prognosa->data_source);
    }

    /**
     * Test executive summary displays all projects (consolidated) when no SM is filtered.
     */
    public function test_executive_summary_shows_all_projects_when_not_filtered()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get(route('sm.executive-summary'));
        $response->assertStatus(200);
        $response->assertSee('Seluruh Service Manager (Konsolidasi)');
        $response->assertViewHas('projectItems', function ($items) {
            return count($items) === Kontrak::count();
        });
    }

    /**
     * Test executive summary filters by specific SM when parameter is given.
     */
    public function test_executive_summary_filters_by_specific_sm()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get(route('sm.executive-summary', ['service_manager' => 'GILANG']));
        $response->assertStatus(200);
        $response->assertSee('GILANG');
        $response->assertViewHas('projectItems', function ($items) {
            $expectedCount = Kontrak::where('service_manager', 'GILANG')->count();
            return count($items) === $expectedCount;
        });
    }
}
