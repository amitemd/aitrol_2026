<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestQueryCache;
use Sis_medico\Support\RequestSchema as Schema;

class InitialSetupService
{
    /**
     * Determine whether the minimum schema required by the installer exists.
     *
     * @return bool
     */
    public function schemaIsReady()
    {
        foreach ($this->bootstrapTables() as $table) {
            try {
                if (!Schema::hasTable($table)) {
                    return false;
                }
            } catch (\Exception $exception) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the two initial accounts already exist.
     *
     * @return bool
     */
    public function bootstrapIsReady()
    {
        return RequestQueryCache::remember(__METHOD__, function () {
            return $this->checkBootstrap();
        }, null, false, ['users']);
    }

    private function checkBootstrap()
    {
        return $this->exists($this->bootstrapTables(), function () {
            $systemsId = config('installation.systems.id', '0000000001');
            $doctorId = config('installation.doctor.id', '0000000003');

            return DB::table('users')
                    ->where('id', $systemsId)
                    ->where('id_tipo_usuario', 1)
                    ->where('estado', 1)
                    ->exists()
                && DB::table('users')
                    ->where('id', $doctorId)
                    ->where('id_tipo_usuario', 3)
                    ->where('estado', 1)
                    ->exists();
        });
    }

    public function status()
    {
        return RequestQueryCache::remember(__METHOD__, function () {
            return $this->loadStatus();
        }, null, false, ['users', 'empresa', 'plan_cuentas_empresa', 'hospital', 'sala', 'seguros', 'subseguro', 'ct_sucursales']);
    }

    private function loadStatus()
    {
        $bootstrap = $this->bootstrapIsReady();
        $companyId = $this->setupCompanyId();

        $company = !is_null($companyId);

        $chartOfAccounts = $this->exists(array('empresa', 'plan_cuentas', 'plan_cuentas_empresa'), function () use ($companyId) {
            return !is_null($companyId)
                && DB::table('plan_cuentas_empresa')
                    ->where('id_empresa', $companyId)
                    ->where('estado', '>', 0)
                    ->exists();
        });

        $location = $this->exists('hospital', function () use ($companyId) {
            if (is_null($companyId)) return false;
            $query = DB::table('hospital')->where('estado', 1);
            if (Schema::hasColumn('hospital', 'id_empresa')) return $query->where('id_empresa', $companyId)->exists();
            if (!Schema::hasTable('company_hospital_contexts')) return false;
            return $query->join('company_hospital_contexts as chc', 'chc.hospital_id', '=', 'hospital.id')
                ->where('chc.company_id', $companyId)->where('chc.active', 1)->exists();
        });

        $room = $this->exists(array('hospital', 'sala'), function () use ($companyId) {
            if (is_null($companyId)) return false;
            $query = DB::table('sala')
                ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
                ->where('sala.estado', 1)
                ->where('hospital.estado', 1);
            if (Schema::hasColumn('sala', 'id_empresa')) $query->where('sala.id_empresa', $companyId);
            if (Schema::hasColumn('hospital', 'id_empresa')) {
                $query->where('hospital.id_empresa', $companyId);
            } elseif (Schema::hasTable('company_hospital_contexts')) {
                $query->join('company_hospital_contexts as chc', 'chc.hospital_id', '=', 'hospital.id')
                    ->where('chc.company_id', $companyId)
                    ->where('chc.active', 1);
            } else {
                return false;
            }
            return $query->exists();
        });

        $insurance = $this->exists(array('seguros', 'subseguro'), function () {
            return DB::table('subseguro')
                ->join('seguros', 'seguros.id', '=', 'subseguro.id_seguro')
                ->where('subseguro.estado', 1)
                ->where('seguros.inactivo', 1)
                ->exists();
        });

        $establishment = $this->exists(array('empresa', 'ct_sucursales'), function () use ($companyId) {
            return !is_null($companyId) && DB::table('ct_sucursales')
                ->where('id_empresa', $companyId)->where('estado', 1)->exists();
        });

        return array(
            'bootstrap' => $bootstrap,
            'company' => $company,
            'chart_of_accounts' => $chartOfAccounts,
            'location' => $location,
            'room' => $room,
            'insurance' => $insurance,
            'establishment' => $establishment,
        );
    }

    public function profile()
    {
        $profiles = config('company_profiles.profiles', array());
        $default = config('company_profiles.default', 'hospitalario');
        $key = $default;
        $company = $this->setupCompany();
        if (!is_null($company) && Schema::hasColumn('empresa', 'tipo_empresa')
            && isset($profiles[$company->tipo_empresa])) {
            $key = $company->tipo_empresa;
        }
        $profile = isset($profiles[$key]) ? $profiles[$key] : array(
            'label' => 'Hospitalario',
            'description' => '',
            'steps' => array('company', 'chart_of_accounts', 'location', 'room', 'insurance'),
            'modules' => array(),
        );
        $profile['key'] = $key;
        return $profile;
    }

    public function requiredStatusKeys()
    {
        return array_merge(array('bootstrap'), $this->profile()['steps']);
    }

    public function isComplete()
    {
        $status = $this->status();

        foreach ($this->requiredStatusKeys() as $key) {
            if (empty($status[$key])) {
                return false;
            }
        }
        return true;
    }

    public function progress()
    {
        $status = $this->status();
        $keys = $this->requiredStatusKeys();
        $complete = 0;
        foreach ($keys as $key) {
            $complete += !empty($status[$key]) ? 1 : 0;
        }
        return (int) round(($complete / max(count($keys), 1)) * 100);
    }

    /**
     * Prepare an initial chart for the active company. Existing global
     * accounts are reused; on a new database a safe starter chart is created.
     */
    public function prepareChartOfAccounts($userId, $ip)
    {
        $companyId = $this->setupCompanyId();
        if (is_null($companyId)) {
            throw new \RuntimeException('Primero debes seleccionar o registrar una empresa activa.');
        }

        return DB::transaction(function () use ($companyId, $userId, $ip) {
            if (!DB::table('plan_cuentas')->where('estado', '>', 0)->exists()) {
                $this->createBaseChart($userId, $ip);
            }

            $accounts = DB::table('plan_cuentas')
                ->where('estado', '>', 0)
                ->orderByRaw('LENGTH(id) ASC')
                ->orderBy('id')
                ->get();

            foreach ($accounts as $account) {
                $exists = DB::table('plan_cuentas_empresa')
                    ->where('id_empresa', $companyId)
                    ->where('id_plan', $account->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('plan_cuentas_empresa')->insert(array(
                    'nombre' => $account->nombre,
                    'descripcion' => $account->descripcion,
                    'estado' => $account->estado,
                    'id_padre' => $account->id_padre,
                    'id_usuariocrea' => $userId,
                    'id_usuariomod' => $userId,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                    'naturaleza' => $account->naturaleza,
                    'naturaleza_2' => $account->naturaleza_2,
                    'id_empresa' => $companyId,
                    'id_plan' => $account->id,
                    'plan' => $account->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ));
            }

            return $companyId;
        });
    }

    private function createBaseChart($userId, $ip)
    {
        $accounts = array(
            array('1', 'ACTIVO', null, 1, 1),
            array('2', 'PASIVO', null, 1, 0),
            array('3', 'PATRIMONIO', null, 1, 0),
            array('4', 'INGRESOS', null, 1, 0),
            array('5', 'COSTOS', null, 1, 1),
            array('6', 'GASTOS', null, 1, 1),
            array('1.01', 'ACTIVO CORRIENTE', '1', 1, 1),
            array('2.01', 'PASIVO CORRIENTE', '2', 1, 0),
            array('3.01', 'CAPITAL', '3', 1, 0),
            array('4.01', 'INGRESOS OPERACIONALES', '4', 1, 0),
            array('5.01', 'COSTOS OPERACIONALES', '5', 1, 1),
            array('6.01', 'GASTOS OPERACIONALES', '6', 1, 1),
            array('1.01.01', 'EFECTIVO Y EQUIVALENTES', '1.01', 1, 1),
            array('2.01.01', 'CUENTAS POR PAGAR', '2.01', 2, 0),
            array('3.01.01', 'CAPITAL SOCIAL', '3.01', 2, 0),
            array('4.01.01', 'INGRESOS POR SERVICIOS MEDICOS', '4.01', 2, 0),
            array('5.01.01', 'COSTOS DE SERVICIOS MEDICOS', '5.01', 2, 1),
            array('6.01.01', 'GASTOS ADMINISTRATIVOS', '6.01', 2, 1),
            array('1.01.01.01', 'CAJA GENERAL', '1.01.01', 2, 1),
            array('1.01.01.02', 'BANCOS', '1.01.01', 2, 1),
        );

        foreach ($accounts as $account) {
            DB::table('plan_cuentas')->insert(array(
                'id' => $account[0],
                'nombre' => $account[1],
                'descripcion' => $account[1],
                'estado' => $account[3],
                'id_padre' => $account[2],
                'id_usuariocrea' => $userId,
                'id_usuariomod' => $userId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'naturaleza' => $account[4],
                'naturaleza_2' => $account[4] ? 0 : 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ));
        }
    }

    private function principalCompany()
    {
        return RequestQueryCache::remember(__METHOD__, function () {
            return $this->loadPrincipalCompany();
        }, null, false, ['empresa']);
    }

    private function loadPrincipalCompany()
    {
        if (!Schema::hasTable('empresa')) {
            return null;
        }
        $company = DB::table('empresa')->where('estado', 1)->where('prioridad', 1)->first();
        if (is_null($company)) {
            $company = DB::table('empresa')->where('estado', 1)->orderBy('id', 'asc')->first();
        }
        return $company;
    }

    /**
     * Company whose setup is being reviewed. In normal authenticated flows it
     * is always the tenant selected in the session; the principal company is
     * only a bootstrap fallback for installation requests without a tenant.
     */
    private function setupCompany()
    {
        $sessionCompanyId = $this->sessionCompanyId();
        if (!is_null($sessionCompanyId) && Schema::hasTable('empresa')) {
            $company = DB::table('empresa')
                ->where('id', $sessionCompanyId)
                ->where('estado', 1)
                ->first();

            if (!is_null($company)) {
                return $company;
            }

            return null;
        }

        return $this->principalCompany();
    }

    private function setupCompanyId()
    {
        $company = $this->setupCompany();
        return is_null($company) ? null : $company->id;
    }

    private function sessionCompanyId()
    {
        $request = app()->bound('request') ? app('request') : null;
        if (!$request instanceof Request || !$request->hasSession()) {
            return null;
        }

        $companyId = trim((string) $request->session()->get('id_empresa', ''));
        return $companyId === '' ? null : $companyId;
    }

    private function exists($tables, \Closure $callback)
    {
        try {
            foreach ((array) $tables as $table) {
                if (!Schema::hasTable($table)) {
                    return false;
                }
            }

            return (bool) $callback();
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function bootstrapTables()
    {
        return array(
            'users', 'tipousuario', 'empresa', 'hospital', 'sala',
            'seguros', 'subseguro', 'opcion', 'opcion_usuario',
        );
    }
}
