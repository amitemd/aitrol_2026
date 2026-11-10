<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $catalogTables = ['especialidad', 'seguros', 'ct_tipo_pago'];

    public function up(): void
    {
        $existingTables = array_values(array_filter(
            $this->catalogTables,
            fn (string $tableName): bool => Schema::hasTable($tableName),
        ));

        if ($existingTables === []) {
            return;
        }

        $defaultCompanyId = $this->resolveDefaultCompanyId();

        foreach ($existingTables as $tableName) {
            $this->addCompanyColumn($tableName);
            $this->assignUnambiguousCreatorCompanies($tableName);

            DB::table($tableName)
                ->where(function ($query): void {
                    $query->whereNull('id_empresa')->orWhere('id_empresa', '');
                })
                ->update(['id_empresa' => $defaultCompanyId]);
        }
    }

    /**
     * Keep catalog ownership because removing it would merge tenant data again.
     */
    public function down(): void {}

    private function resolveDefaultCompanyId(): string
    {
        if (! Schema::hasTable('empresa')) {
            throw new RuntimeException('No se pueden preparar los catálogos clínicos porque no existe la tabla empresa.');
        }

        $query = DB::table('empresa')->where('estado', 1);

        if (Schema::hasColumn('empresa', 'prioridad')) {
            $query->orderBy('prioridad');
        }

        $companyId = $query->orderBy('id')->value('id')
            ?? DB::table('empresa')->orderBy('id')->value('id');

        if ($companyId === null) {
            throw new RuntimeException('No se pueden preparar los catálogos clínicos porque no existe una empresa.');
        }

        return (string) $companyId;
    }

    private function addCompanyColumn(string $tableName): void
    {
        if (Schema::hasColumn($tableName, 'id_empresa')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            $table->string('id_empresa', 13)->nullable()->index($tableName.'_empresa_idx');
        });
    }

    private function assignUnambiguousCreatorCompanies(string $tableName): void
    {
        if (! Schema::hasTable('usuario_empresa') || ! Schema::hasColumn($tableName, 'id_usuariocrea')) {
            return;
        }

        $owners = DB::table('usuario_empresa')
            ->select('id_usuario', DB::raw('MIN(id_empresa) AS id_empresa'))
            ->groupBy('id_usuario')
            ->havingRaw('COUNT(DISTINCT id_empresa) = 1')
            ->get();

        foreach ($owners as $owner) {
            DB::table($tableName)
                ->where('id_usuariocrea', $owner->id_usuario)
                ->where(function ($query): void {
                    $query->whereNull('id_empresa')->orWhere('id_empresa', '');
                })
                ->update(['id_empresa' => (string) $owner->id_empresa]);
        }
    }
};
