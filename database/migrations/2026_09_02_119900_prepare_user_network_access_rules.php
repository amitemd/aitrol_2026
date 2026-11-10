<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_network_access_rules') || ! Schema::hasTable('users')) {
            return;
        }

        $usersCollation = $this->usersIdCollation();

        Schema::create('user_network_access_rules', function (Blueprint $table) use ($usersCollation): void {
            if ($usersCollation !== null) {
                $table->charset = explode('_', $usersCollation, 2)[0];
                $table->collation = $usersCollation;
            }

            $table->increments('id');
            $table->string('user_id', 13);
            $table->string('name', 120);
            $table->string('ip_rule', 100);
            $table->string('weekdays', 20)->default('1,2,3,4,5');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->string('created_by', 13);
            $table->string('updated_by', 13)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'active'], 'network_access_user_active_idx');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // La migración histórica propietaria conserva la reversión de esta tabla.
    }

    private function usersIdCollation(): ?string
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return null;
        }

        $column = DB::table('information_schema.COLUMNS')
            ->selectRaw('COLLATION_NAME AS collation_name')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', 'users')
            ->where('COLUMN_NAME', 'id')
            ->first();

        return $column?->collation_name;
    }
};
