<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kontrak;

class RoleAndPillarsTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;
    public function test_admin_can_access_monitoring_pillars()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $this->get(route('monitoring.cost-kontrak'))->assertStatus(200);
        $this->get(route('monitoring.invoice-vendor'))->assertStatus(200);
        $this->get(route('monitoring.basto'))->assertStatus(200);
    }

    public function test_admin_cannot_create_project_dmo_only()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->post(route('admin.master-data.projects.store'), [
            'project_id' => 'TEST-ADMIN-01',
            'project_name' => 'Project by Admin Test',
            'project_value' => '500000000',
        ]);

        // Expect 403 Forbidden due to role:dmo middleware
        $response->assertStatus(403);
    }

    public function test_procurement_can_access_procurement_hub_and_pillars()
    {
        $procurement = User::where('role', 'procurement')->first();
        $this->actingAs($procurement);

        $this->get(route('procurement.dashboard'))->assertStatus(200);
        $this->get(route('procurement.vendors'))->assertStatus(200);
        $this->get(route('procurement.orders'))->assertStatus(200);
        $this->get(route('procurement.verification'))->assertStatus(200);
        $this->get(route('monitoring.cost-kontrak'))->assertStatus(200);
        $this->get(route('monitoring.invoice-vendor'))->assertStatus(200);
    }

    public function test_qc_and_qc2_can_access_qc_inspection()
    {
        $qc1 = User::where('username', 'qc')->first();
        $this->actingAs($qc1);
        $this->get(route('basto.index'))->assertStatus(200);

        $qc2 = User::where('username', 'qc2')->first();
        $this->actingAs($qc2);
        $this->get(route('basto.index'))->assertStatus(200);
    }

    public function test_dmo_can_access_master_data_and_pillars()
    {
        $dmo = User::where('role', 'dmo')->first();
        $this->actingAs($dmo);

        $this->get(route('admin.master-data.index', ['tab' => 'projects']))->assertStatus(200);
        $this->get(route('monitoring.cost-kontrak'))->assertStatus(200);
        $this->get(route('monitoring.invoice-vendor'))->assertStatus(200);
        $this->get(route('monitoring.basto'))->assertStatus(200);
    }

    public function test_admin_can_access_vendor_invoice_and_obsolete_sales_finance_routes_not_found()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Admin can access Vendor Invoice management and creation
        $this->get('/invoice')->assertStatus(200);
        $this->get('/invoice/create')->assertStatus(200);

        // Obsolete sales and finance specific paths remain not found
        $this->get('/sales/dashboard')->assertStatus(404);
        $this->get('/finance/dashboard')->assertStatus(404);
    }

    public function test_procurement_login_with_username_redirect()
    {
        $response = $this->post('/login', [
            'login' => 'procurement',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('procurement.dashboard'));
    }

    public function test_procurement_login_with_email_redirect()
    {
        $response = $this->post('/login', [
            'login' => 'reza@pgncom.co.id',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('procurement.dashboard'));
    }

    public function test_procurement_accessing_general_dashboard_redirects_to_procurement_dashboard()
    {
        $procurement = User::where('role', 'procurement')->first();
        $this->actingAs($procurement);

        $this->get('/dashboard')->assertRedirect(route('procurement.dashboard'));
        $this->get('/')->assertRedirect(route('procurement.dashboard'));
    }

    public function test_admin_users_displays_procurement_role_and_reza_email()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.users.index', ['search' => 'reza@pgncom.co.id']));
        $response->assertStatus(200);
        $response->assertSee('reza@pgncom.co.id');
        $response->assertSee('Procurement');
    }

    public function test_sales_and_finance_roles_removed_from_ui_and_validation()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // 1. Check admin user management index does not have sales or finance options
        $responseIndex = $this->get(route('admin.users.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertDontSee('value="sales"', false);
        $responseIndex->assertDontSee('value="finance"', false);

        // 2. Check create user form does not have sales or finance options
        $responseCreate = $this->get(route('admin.users.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertDontSee('value="sales"', false);
        $responseCreate->assertDontSee('value="finance"', false);

        // 3. Check dashboard does not contain obsolete sales or finance cockpits
        $responseDashboard = $this->get(route('dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertDontSee('Sales Revenue &amp; AR Aging Cockpit', false);
        $responseDashboard->assertDontSee('Finance Treasury &amp; Cash Inflow Cockpit', false);

        // 4. Validate that creating a user with sales or finance role fails
        $responseStore = $this->post(route('admin.users.store'), [
            'name' => 'Old Sales User',
            'username' => 'oldsales',
            'email' => 'sales@pgncom.co.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'sales',
        ]);
        $responseStore->assertSessionHasErrors(['role']);
    }

    public function test_admin_can_create_vendor_invoice()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $kontrak = \App\Models\Kontrak::first();
        $testInvNumber = 'INV-TEST-' . time();

        $response = $this->post(route('invoice.store'), [
            'invoice_number' => $testInvNumber,
            'project_id'     => $kontrak ? $kontrak->project_id : 'PROJ-001',
            'customer'       => 'PT Vendor Telekomunikasi Mitra',
            'invoice_date'   => now()->toDateString(),
            'due_date'       => now()->addDays(30)->toDateString(),
            'subtotal'       => 10000000,
            'tax_ppn_percent'=> 11,
            'tax_pph_percent'=> 2,
            'invoice_amount' => 10900000,
            'notes'          => 'Tagihan termin 1 pekerjaan instalasi jaringan',
        ]);

        $response->assertRedirect(route('invoice.index'));
        $this->assertDatabaseHas('invoices', [
            'invoice_number' => $testInvNumber,
            'customer'       => 'PT Vendor Telekomunikasi Mitra',
        ]);
    }
}
