<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class InstallationReferenceDataService
{
    public function seed($actorId, $ip = '127.0.0.1')
    {
        if (empty($actorId) || !Schema::hasTable('users') || !DB::table('users')->where('id', $actorId)->exists()) {
            return array('accounts' => 0, 'company_accounts' => 0, 'procedures' => 0, 'specialties' => 0,
                'insurances' => 0, 'banks' => 0, 'payment_methods' => 0);
        }
        $result = $this->seedChartOfAccounts($this->load('chart_of_accounts.json'), $actorId, $ip);
        return array_merge($result, $this->seedCatalogs($this->load('catalogs.json'), $actorId, $ip));
    }

    private function seedChartOfAccounts(array $accounts, $actorId, $ip)
    {
        if (!Schema::hasTable('plan_cuentas')) return array('accounts' => 0, 'company_accounts' => 0);
        $now = date('Y-m-d H:i:s');
        $codes = array();
        foreach ($accounts as $account) $codes[(string) $account['code']] = true;
        usort($accounts, function ($left, $right) {
            $depth = substr_count($left['code'], '.') - substr_count($right['code'], '.');
            return $depth === 0 ? strnatcasecmp($left['code'], $right['code']) : $depth;
        });

        $existing = DB::table('plan_cuentas')->whereIn('id', array_keys($codes))->pluck('id')->all();
        $existing = array_fill_keys(array_map('strval', $existing), true);
        $masterRows = array();
        foreach ($accounts as $account) {
            $code = (string) $account['code'];
            if (isset($existing[$code])) continue;
            $nature = $this->debitNature($code) ? 1 : 0;
            $masterRows[] = array('id' => $code, 'nombre' => $account['name'], 'descripcion' => $account['name'],
                'estado' => $this->hasChildren($code, $codes) ? 1 : 2, 'id_padre' => $this->parentCode($code),
                'naturaleza' => $nature, 'naturaleza_2' => $nature ? 0 : 1,
                'id_usuariocrea' => $actorId, 'id_usuariomod' => $actorId, 'ip_creacion' => $ip,
                'ip_modificacion' => $ip, 'created_at' => $now, 'updated_at' => $now);
        }
        foreach (array_chunk($masterRows, 200) as $chunk) DB::table('plan_cuentas')->insert($chunk);

        $companyCreated = 0;
        if (Schema::hasTable('plan_cuentas_empresa') && Schema::hasTable('empresa')) {
            foreach (DB::table('empresa')->where('estado', 1)->get(array('id')) as $company) {
                $companyExisting = DB::table('plan_cuentas_empresa')->where('id_empresa', $company->id)
                    ->whereIn('id_plan', array_keys($codes))->pluck('id_plan')->all();
                $companyExisting = array_fill_keys(array_map('strval', $companyExisting), true);
                $companyRows = array();
                foreach ($accounts as $account) {
                    $code = (string) $account['code'];
                    if (isset($companyExisting[$code])) continue;
                    $nature = $this->debitNature($code) ? 1 : 0;
                    $companyRows[] = array('id_empresa' => $company->id, 'id_plan' => $code, 'plan' => $code,
                        'nombre' => $account['name'], 'descripcion' => $account['name'],
                        'estado' => $this->hasChildren($code, $codes) ? 1 : 2, 'id_padre' => $this->parentCode($code),
                        'naturaleza' => $nature, 'naturaleza_2' => $nature ? 0 : 1,
                        'estado_cierre_ano' => in_array(substr($code, 0, 1), array('4', '5', '6'), true) ? 1 : 0,
                        'id_usuariocrea' => $actorId, 'id_usuariomod' => $actorId, 'ip_creacion' => $ip,
                        'ip_modificacion' => $ip, 'created_at' => $now, 'updated_at' => $now);
                }
                foreach (array_chunk($companyRows, 200) as $chunk) DB::table('plan_cuentas_empresa')->insert($chunk);
                $companyCreated += count($companyRows);
            }
        }
        return array('accounts' => count($masterRows), 'company_accounts' => $companyCreated);
    }

    private function seedCatalogs(array $catalogs, $actorId, $ip)
    {
        $now = date('Y-m-d H:i:s');
        $audit = array('id_usuariocrea' => $actorId, 'id_usuariomod' => $actorId, 'ip_creacion' => $ip,
            'ip_modificacion' => $ip, 'created_at' => $now, 'updated_at' => $now);
        $result = array('procedures' => 0, 'specialties' => 0, 'insurances' => 0, 'banks' => 0, 'payment_methods' => 0);

        if (Schema::hasTable('procedimiento')) {
            $known = $this->normalizedSet(DB::table('procedimiento')->pluck('nombre')->all());
            $rows = array();
            foreach ($catalogs['procedures'] as $item) {
                $name = $this->upper($item['nombre']);
                if ($name === '' || isset($known[$name])) continue;
                $known[$name] = true;
                $rows[] = array_merge(array('nombre' => $name,
                    'observacion' => mb_substr((string) $item['observacion'], 0, 255, 'UTF-8'),
                    'id_grupo_procedimiento' => null, 'cardiologia' => $this->yes($item['cardiologia']),
                    'laboratorio' => $this->yes($item['laboratorio']), 'quirofano' => $this->yes($item['quirofano']),
                    'estado' => $this->active($item['estado']), 'deleted_at' => null), $audit);
            }
            foreach (array_chunk($rows, 200) as $chunk) DB::table('procedimiento')->insert($chunk);
            $result['procedures'] = count($rows);
        }

        if (Schema::hasTable('especialidad')) {
            $known = $this->normalizedSet(\Sis_medico\Especialidad::query()->pluck('nombre')->all());
            foreach ($catalogs['specialties'] as $item) {
                $name = $this->upper($item['nombre']);
                if ($name === '' || isset($known[$name])) continue;
                $description = mb_strtolower(trim((string) $item['descripcion']), 'UTF-8') === 'prueba'
                    ? $name : trim((string) $item['descripcion']);
                \Sis_medico\Especialidad::create(array_merge(array(
                    'id_empresa' => app(CompanyCatalogService::class)->companyId(true), 'nombre' => $name,
                    'descripcion' => $description, 'estado' => $this->active($item['estado'])), $audit));
                $known[$name] = true; $result['specialties']++;
            }
        }

        app(InitialLoadCatalogService::class)->ensureInsuranceTypes($actorId, $ip);
        if (Schema::hasTable('seguros') && Schema::hasTable('seguro_tipos')) {
            $known = $this->normalizedSet(\Sis_medico\Seguro::query()->pluck('nombre')->all());
            $types = array(); foreach (DB::table('seguro_tipos')->get() as $type) $types[$this->upper($type->nombre)] = $type;
            foreach ($catalogs['insurances'] as $item) {
                $name = $this->upper($item['nombre']); $typeName = $this->upper($item['tipo_seguro']);
                if ($name === '' || isset($known[$name]) || !isset($types[$typeName])) continue;
                \Sis_medico\Seguro::create(array_merge(array(
                    'id_empresa' => app(CompanyCatalogService::class)->companyId(true),
                    'nombre' => $name, 'descripcion' => $item['descripcion'],
                    'tipo' => $typeName === 'PUBLICO' ? 0 : 1, 'color' => strtoupper($item['color_hex']),
                    'ruc_cliente' => $this->insuranceRuc($item['ruc_cliente']),
                    'email' => $this->insuranceEmail($item['email']), 'copago' => $this->yes($item['copago']),
                    'visualizar' => $this->yes($item['visible']), 'inactivo' => $this->active($item['estado']),
                    'id_seguro_tipos' => $types[$typeName]->id, 'estado_labs' => 1), $audit));
                $known[$name] = true; $result['insurances']++;
            }
        }

        if (Schema::hasTable('ct_bancos')) {
            $known = $this->normalizedSet(DB::table('ct_bancos')->pluck('nombre')->all());
            foreach ($catalogs['banks'] as $item) {
                $name = $this->upper($item['nombre']);
                if ($name === '' || isset($known[$name])) continue;
                DB::table('ct_bancos')->insert(array_merge(array('nombre' => $name,
                    'estado' => $this->active($item['estado'])), $audit));
                $known[$name] = true; $result['banks']++;
            }
        }

        if (Schema::hasTable('ct_tipo_pago')) {
            $known = $this->normalizedSet(\Sis_medico\Ct_Tipo_Pago::query()->pluck('codigo')->all());
            $hasBankRequirement = Schema::hasColumn('ct_tipo_pago', 'requiere_banco');
            $hasReferenceRequirement = Schema::hasColumn('ct_tipo_pago', 'requiere_referencia');
            foreach ($catalogs['paymentMethods'] as $item) {
                $code = $this->upper($item['codigo']);
                if ($code === '') continue;

                $requirements = array();
                if ($hasBankRequirement) $requirements['requiere_banco'] = $this->yes($item['requiere_banco']);
                if ($hasReferenceRequirement) $requirements['requiere_referencia'] = $this->yes($item['requiere_referencia']);

                // BootstrapUsersSeeder can run before the migration that adds
                // the requirement columns. Insert only the columns that exist;
                // a later idempotent seed completes them after the migration.
                if (isset($known[$code])) {
                    if ($requirements) {
                        $paymentMethod = \Sis_medico\Ct_Tipo_Pago::query()
                            ->whereRaw('UPPER(codigo) = ?', array($code))->first();
                        if ($paymentMethod) app(CompanyCatalogService::class)->updateForCompany($paymentMethod, $requirements);
                    }
                    continue;
                }

                $values = array('nombre' => $item['descripcion'], 'codigo' => $code,
                    'codigo_sri' => '01', 'porcentaje' => 0,
                    'estado' => $this->active($item['estado']));
                \Sis_medico\Ct_Tipo_Pago::create(array_merge(array(
                    'id_empresa' => app(CompanyCatalogService::class)->companyId(true),
                ), $values, $requirements, $audit));
                $known[$code] = true; $result['payment_methods']++;
            }
        }
        return $result;
    }

    private function load($name)
    {
        $path = database_path('data/initial_install/' . $name);
        $data = is_file($path) ? json_decode(file_get_contents($path), true) : null;
        if (!is_array($data)) throw new \RuntimeException('No se pudo leer el catálogo inicial ' . $name . '.');
        return $data;
    }

    private function normalizedSet(array $values) { $set = array(); foreach ($values as $value) $set[$this->upper($value)] = true; return $set; }
    private function upper($value) { return mb_strtoupper(trim((string) $value), 'UTF-8'); }
    private function yes($value) { return $this->upper($value) === 'SI' ? 1 : 0; }
    private function active($value) { return $this->upper($value) === 'ACTIVO' ? 1 : 0; }
    private function parentCode($code) { $at = strrpos($code, '.'); return $at === false ? null : substr($code, 0, $at); }
    private function hasChildren($code, array $codes) { foreach ($codes as $candidate => $unused) if (strpos($candidate, $code . '.') === 0) return true; return false; }
    private function debitNature($code) { return in_array(substr($code, 0, 1), array('1', '5', '6'), true); }
    private function plainIdentifier($value) { return preg_replace('/^=\"(.*)\"$/', '$1', trim((string) $value)); }
    private function insuranceRuc($value) { $value = $this->plainIdentifier($value); return $value === '0999999999001' ? null : $value; }
    private function insuranceEmail($value) { $value = mb_strtolower(trim((string) $value), 'UTF-8'); return substr($value, -12) === '@example.org' ? null : $value; }
}
