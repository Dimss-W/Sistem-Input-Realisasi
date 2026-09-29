<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ActivityLog;

class AuditLogTest extends TestCase
{
    public function test_admin_can_filter_audit_logs_by_date_range()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'status' => 'active']);

        // Create log in past
        $pastLog = new ActivityLog();
        $pastLog->user_id = $admin->id;
        $pastLog->user_role = 'admin';
        $pastLog->action = 'LOGIN';
        $pastLog->module = 'Auth';
        $pastLog->description = 'UniquePastLogDescription';
        $pastLog->timestamps = false;
        $pastLog->created_at = '2026-08-01 10:00:00';
        $pastLog->save();

        // Create log in target range
        $targetLog = new ActivityLog();
        $targetLog->user_id = $admin->id;
        $targetLog->user_role = 'admin';
        $targetLog->action = 'CREATE';
        $targetLog->module = 'Project';
        $targetLog->description = 'UniqueTargetLogInRange';
        $targetLog->timestamps = false;
        $targetLog->created_at = '2026-09-10 10:00:00';
        $targetLog->save();

        // Create log in future
        $futureLog = new ActivityLog();
        $futureLog->user_id = $admin->id;
        $futureLog->user_role = 'admin';
        $futureLog->action = 'UPDATE';
        $futureLog->module = 'Cost';
        $futureLog->description = 'UniqueFutureLogOutOfRange';
        $futureLog->timestamps = false;
        $futureLog->created_at = '2026-09-25 10:00:00';
        $futureLog->save();

        $response = $this->actingAs($admin)->get(route('audit.log', [
            'date_from' => '2026-09-01',
            'date_to' => '2026-09-15',
        ]));

        $response->assertStatus(200);
        $response->assertSee('UniqueTargetLogInRange');
        $response->assertDontSee('UniqueFutureLogOutOfRange');
        $response->assertDontSee('UniquePastLogDescription');
        $response->assertSee('Dari Tanggal');
        $response->assertSee('Hingga Tanggal');

        // Cleanup
        $pastLog->delete();
        $targetLog->delete();
        $futureLog->delete();
    }
}
