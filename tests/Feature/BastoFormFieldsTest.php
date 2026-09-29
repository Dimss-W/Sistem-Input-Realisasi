<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Basto;
use App\Models\Kontrak;

class BastoFormFieldsTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_dmo_can_see_basto_create_form_with_cost_fields()
    {
        $dmo = User::where('role', 'dmo')->first();
        $this->assertNotNull($dmo, 'User with role dmo must exist');

        $response = $this->actingAs($dmo)->get(route('basto.create'));
        $response->assertStatus(200);
        $response->assertSee('Project ID');
        $response->assertSee('Nama Project');
        $response->assertSee('cost_no');
        $response->assertSee('cost_value');
        $response->assertSee('cost_date_start');
        $response->assertSee('cost_date_end');
        $response->assertSee('cost_based');
        $response->assertSee('supply_chain');
    }

    public function test_dmo_can_submit_basto_with_all_cost_and_supply_chain_fields()
    {
        $dmo = User::where('role', 'dmo')->first();
        $sm  = User::where('role', 'osm_service_manager')->first();
        $this->assertNotNull($dmo);
        $this->assertNotNull($sm);

        $testData = [
            'project_id'      => 'PROJ-TEST-BASTO-' . rand(100, 999),
            'project_name'    => 'Pengadaan Infrastruktur Jaringan Gas Wilayah 1',
            'cost_no'         => 'SPK-2026-PGN-0099',
            'cost_value'      => 450000000.00,
            'cost_date_start' => '2026-01-15',
            'cost_date_end'   => '2026-06-30',
            'cost_based'      => 400000000.00,
            'supply_chain'    => 'PT Rekan Prima Solusi',
            'sm_user_id'      => $sm->id,
            'notes'           => 'Pekerjaan telah rampung 100% dan lolos uji fungsi.',
        ];

        $response = $this->actingAs($dmo)->post(route('basto.store'), $testData);

        $basto = Basto::where('project_id', $testData['project_id'])->first();
        $this->assertNotNull($basto, 'BASTO record should be created in database');
        $response->assertRedirect(route('basto.show', $basto->id));

        // Assert all 8 fields match
        $this->assertEquals($testData['project_id'], $basto->project_id);
        $this->assertEquals($testData['project_name'], $basto->project_name);
        $this->assertEquals($testData['cost_no'], $basto->cost_no);
        $this->assertEquals($testData['cost_value'], (float) $basto->cost_value);
        $this->assertEquals($testData['cost_date_start'], $basto->cost_date_start->format('Y-m-d'));
        $this->assertEquals($testData['cost_date_end'], $basto->cost_date_end->format('Y-m-d'));
        $this->assertEquals($testData['cost_based'], (float) $basto->cost_based);
        $this->assertEquals($testData['supply_chain'], $basto->supply_chain);

        // Verify that basto.show displays all fields cleanly
        $showResponse = $this->actingAs($dmo)->get(route('basto.show', $basto->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($testData['cost_no']);
        $showResponse->assertSee($testData['supply_chain']);
        $showResponse->assertSee('Informasi Finansial &amp; Kontrak (Cost &amp; Supply Chain)', false);
    }
}
