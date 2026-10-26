<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyRuntimeTablesMigrationTest extends TestCase
{
    protected function tearDown(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('legacy_runtime_tables');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_runtime_tables_are_added_without_recreating_or_deleting_legacy_users(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('email');
        });
        DB::table('users')->insert([
            'id' => '0921605895',
            'email' => 'administrador@example.test',
        ]);

        $migration = require database_path('migrations/2026_10_26_164508_create_legacy_runtime_tables.php');

        $migration->up();
        $migration->up();

        $this->assertTrue(Schema::hasColumns('sessions', [
            'id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity',
        ]));
        $this->assertTrue(Schema::hasColumns('cache', ['key', 'value', 'expiration']));
        $this->assertTrue(Schema::hasColumns('cache_locks', ['key', 'owner', 'expiration']));
        $this->assertTrue(Schema::hasColumns('password_reset_tokens', ['email', 'token', 'created_at']));
        $this->assertTrue(Schema::hasColumns('jobs', [
            'id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at',
        ]));
        $this->assertTrue(Schema::hasColumns('job_batches', [
            'id', 'name', 'total_jobs', 'pending_jobs', 'failed_jobs', 'failed_job_ids',
            'options', 'cancelled_at', 'created_at', 'finished_at',
        ]));
        $this->assertTrue(Schema::hasColumns('failed_jobs', [
            'id', 'uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at',
        ]));
        $this->assertSame(1, DB::table('users')->count());

        $migration->down();

        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertTrue(Schema::hasTable('cache_locks'));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertTrue(Schema::hasTable('jobs'));
        $this->assertTrue(Schema::hasTable('job_batches'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
        $this->assertSame(1, DB::table('users')->count());
    }
}
