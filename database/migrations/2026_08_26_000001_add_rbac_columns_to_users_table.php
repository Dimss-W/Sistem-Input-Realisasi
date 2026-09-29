<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 100)->nullable()->unique()->after('name');
            $table->enum('role', [
                'admin',
                'osm_service_manager',
                'osm_qc',
                'dmo',
                'sales',
                'finance',
            ])->default('finance')->after('username');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('avatar', 255)->nullable()->after('last_login_at');
            $table->string('phone', 30)->nullable()->after('avatar');
            $table->text('notes')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'status', 'last_login_at', 'avatar', 'phone', 'notes']);
        });
    }
};
