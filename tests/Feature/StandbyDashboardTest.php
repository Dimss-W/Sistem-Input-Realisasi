<?php

namespace Tests\Feature;

use Tests\TestCase;

class StandbyDashboardTest extends TestCase
{
    /**
     * Test that the Standby Kiosk page renders successfully with all 4 charts.
     */
    public function test_standby_page_renders_with_4_charts(): void
    {
        $response = $this->get('/standby');

        $response->assertStatus(200);
        $response->assertSee('Tren Penyerapan Bulanan');
        $response->assertSee('Rasio Penyerapan');
        $response->assertSee('Distribusi Kontrak per Personel');
        $response->assertSee('Top Vendor: Nilai Kontrak vs Realisasi');
        $response->assertSee('chartMonthlyTrend');
        $response->assertSee('chartBudgetDonut');
        $response->assertSee('chartOrangKontrak');
        $response->assertSee('chartTopVendorCompare');
    }

    /**
     * Test that the standby API returns valid JSON with all 4 chart datasets.
     */
    public function test_standby_api_returns_valid_json_with_all_metrics(): void
    {
        $response = $this->getJson('/api/standby-data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'server_time',
            'date_human',
            'data' => [
                'summary' => [
                    'total_pagu',
                    'total_realisasi',
                    'sisa_anggaran',
                    'serapan_pct',
                    'total_proyek',
                ],
                'charts' => [
                    'chart1_orang' => [
                        'sm' => ['labels', 'data'],
                        'pic' => ['labels', 'data'],
                        'total_sm',
                        'total_pic',
                    ],
                    'chart2_vendor_pagu' => [
                        'total_pagu_all',
                        'labels',
                        'data',
                        'contracts',
                    ],
                    'chart3_vendor_realisasi' => [
                        'total_realisasi_all',
                        'labels',
                        'data',
                        'contracts',
                    ],
                    'chart4_komparasi' => [
                        'global' => [
                            'total_pagu',
                            'total_realisasi',
                            'sisa',
                            'serapan_pct',
                        ],
                        'projects' => [
                            'labels',
                            'ids',
                            'pagu',
                            'realisasi',
                            'pct',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
