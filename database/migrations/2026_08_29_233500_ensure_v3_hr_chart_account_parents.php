<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<string, array{name: string, parent: string|null, nature: int, counter_nature: int}>
     */
    private array $parentAccounts = [
        '1' => ['name' => 'ACTIVO', 'parent' => null, 'nature' => 1, 'counter_nature' => 0],
        '2' => ['name' => 'PASIVO', 'parent' => null, 'nature' => 0, 'counter_nature' => 1],
        '6' => ['name' => 'GASTOS', 'parent' => null, 'nature' => 1, 'counter_nature' => 0],
        '1.01' => ['name' => 'ACTIVO CORRIENTE', 'parent' => '1', 'nature' => 1, 'counter_nature' => 0],
        '2.01' => ['name' => 'PASIVO CORRIENTE', 'parent' => '2', 'nature' => 0, 'counter_nature' => 1],
        '6.01' => ['name' => 'GASTOS OPERACIONALES', 'parent' => '6', 'nature' => 1, 'counter_nature' => 0],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('plan_cuentas')) {
            return;
        }

        $actorId = $this->resolveActorId();
        $now = now();

        DB::transaction(function () use ($actorId, $now): void {
            foreach ($this->parentAccounts as $code => $account) {
                if (DB::table('plan_cuentas')->where('id', $code)->exists()) {
                    continue;
                }

                DB::table('plan_cuentas')->insert([
                    'id' => $code,
                    'nombre' => $account['name'],
                    'descripcion' => $account['name'],
                    'estado' => 1,
                    'id_padre' => $account['parent'],
                    'naturaleza' => $account['nature'],
                    'naturaleza_2' => $account['counter_nature'],
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => '127.0.0.1',
                    'ip_modificacion' => '127.0.0.1',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $this->installCompanyParents($actorId, $now);
        });
    }

    /**
     * The parent accounts remain because later accounting records may reference them.
     */
    public function down(): void {}

    private function resolveActorId(): string
    {
        if (! Schema::hasTable('users')) {
            throw new RuntimeException('No se pueden crear las cuentas padre de RRHH porque no existe la tabla users.');
        }

        $configuredActorId = (string) env('BOOTSTRAP_SYSTEMS_ID', '0000000001');

        if (DB::table('users')->where('id', $configuredActorId)->exists()) {
            return $configuredActorId;
        }

        $fallbackActorId = DB::table('users')->orderBy('id')->value('id');

        if ($fallbackActorId === null) {
            throw new RuntimeException('No se pueden crear las cuentas padre de RRHH porque no existe un usuario de auditoría.');
        }

        return (string) $fallbackActorId;
    }

    private function installCompanyParents(string $actorId, DateTimeInterface $now): void
    {
        if (! Schema::hasTable('plan_cuentas_empresa') || ! Schema::hasTable('empresa')) {
            return;
        }

        $hasYearEndStatus = Schema::hasColumn('plan_cuentas_empresa', 'estado_cierre_ano');

        foreach (DB::table('empresa')->where('estado', 1)->get(['id']) as $company) {
            foreach ($this->parentAccounts as $code => $account) {
                $exists = DB::table('plan_cuentas_empresa')
                    ->where('id_empresa', $company->id)
                    ->where('id_plan', $code)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $values = [
                    'id_empresa' => $company->id,
                    'id_plan' => $code,
                    'plan' => $code,
                    'nombre' => $account['name'],
                    'descripcion' => $account['name'],
                    'estado' => 1,
                    'id_padre' => $account['parent'],
                    'naturaleza' => $account['nature'],
                    'naturaleza_2' => $account['counter_nature'],
                    'id_usuariocrea' => $actorId,
                    'id_usuariomod' => $actorId,
                    'ip_creacion' => '127.0.0.1',
                    'ip_modificacion' => '127.0.0.1',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($hasYearEndStatus) {
                    $values['estado_cierre_ano'] = str_starts_with($code, '6') ? 1 : 0;
                }

                DB::table('plan_cuentas_empresa')->insert($values);
            }
        }
    }
};
