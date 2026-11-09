<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class V3HumanResourcesLegacyImportService
{
    public function pendingCount($companyId)
    {
        if (!$this->isAvailable()) return 0;

        return DB::table('ct_nomina as n')
            ->where('n.id_empresa', $companyId)
            ->whereNotExists(function ($query) use ($companyId) {
                $query->select(DB::raw(1))->from('v3_hr_employees as e')
                    ->where('e.id_empresa', $companyId)
                    ->where(function ($employee) {
                        $employee->whereColumn('e.id_usuario', 'n.id_user')
                            ->orWhereColumn('e.identificacion', 'n.id_user');
                    });
            })->count();
    }

    public function import($companyId)
    {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('No están disponibles las tablas de nómina V2 y RRHH V3 requeridas para la importación.');
        }

        $result = array('employees' => 0, 'positions' => 0, 'contracts' => 0, 'skipped' => 0);

        DB::transaction(function () use ($companyId, &$result) {
            $rows = $this->legacyEmployees($companyId);
            foreach ($rows as $row) {
                $identification = trim((string) $row->id_user);
                if ($identification === '') {
                    $result['skipped']++;
                    continue;
                }

                $employee = DB::table('v3_hr_employees')->where('id_empresa', $companyId)
                    ->where(function ($query) use ($identification) {
                        $query->where('id_usuario', $identification)->orWhere('identificacion', $identification);
                    })->lockForUpdate()->first();

                if ($employee) {
                    $result['skipped']++;
                    continue;
                }

                $positionId = $this->position($companyId, $row, $result);
                $startDate = $this->validDate($row->fecha_ant) ?: $this->validDate($row->fecha_ingreso)
                    ?: $this->validDate($row->legacy_created_at) ?: date('Y-m-d');
                $now = date('Y-m-d H:i:s');
                $employeeId = DB::table('v3_hr_employees')->insertGetId(array(
                    'id_empresa' => $companyId,
                    'identificacion' => $identification,
                    'nombres' => $this->names($row),
                    'apellidos' => $this->surnames($row),
                    'email' => $this->nullable($row->mail_opcional ?: $row->user_email),
                    'telefono' => $this->nullable($row->telefono2 ?: $row->telefono1),
                    'fecha_nacimiento' => $this->validDate($row->fecha_nacimiento),
                    'fecha_ingreso' => $startDate,
                    'genero' => $this->gender($row->sexo),
                    'direccion' => $this->nullable($row->direccion),
                    'position_id' => $positionId,
                    'id_sucursal' => $this->nullable($row->id_sucursal),
                    'estado' => (int) $row->estado === 1 ? 'activo' : 'inactivo',
                    'cuenta_bancaria' => $this->nullable($row->numero_cuenta),
                    'banco' => $this->nullable($row->banco_nombre ?: $row->banco),
                    'id_usuario' => $identification,
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
                $result['employees']++;

                $salary = round(max(0, (float) $row->sueldo_neto), 2);
                if ($salary <= 0) continue;

                DB::table('v3_hr_contracts')->insert(array(
                    'id_empresa' => $companyId,
                    'employee_id' => $employeeId,
                    'tipo' => 'indefinido',
                    'fecha_inicio' => $startDate,
                    'sueldo' => $salary,
                    'horas_semana' => $this->weeklyHours($row->jornada),
                    'modalidad' => 'presencial',
                    'estado' => 'borrador',
                    'observacion' => 'Importado desde Nómina V2. Revise sueldo, jornada y datos SUT antes de aprobar.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ));
                $result['contracts']++;
            }
        }, 3);

        return $result;
    }

    private function legacyEmployees($companyId)
    {
        $query = DB::table('ct_nomina as n')->leftJoin('users as u', 'u.id', '=', 'n.id_user');
        if (Schema::hasTable('ct_rh_area')) $query->leftJoin('ct_rh_area as ar', 'ar.id', '=', 'n.area');
        if (Schema::hasTable('ct_bancos')) $query->leftJoin('ct_bancos as b', 'b.id', '=', 'n.banco');

        $select = array(
            'n.id_user', 'n.nombres as nomina_nombres', 'n.sexo', 'n.cargo', 'n.fecha_ingreso',
            'n.fecha_ant', 'n.mail_opcional', 'n.banco', 'n.numero_cuenta', 'n.sueldo_neto',
            'n.estado', 'n.id_sucursal', 'n.jornada', 'n.created_at as legacy_created_at',
            'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'u.email as user_email',
            'u.telefono1', 'u.telefono2', 'u.fecha_nacimiento', 'u.direccion',
        );
        $select[] = Schema::hasTable('ct_rh_area') ? 'ar.descripcion as area_nombre' : DB::raw('NULL as area_nombre');
        $select[] = Schema::hasTable('ct_bancos') ? 'b.nombre as banco_nombre' : DB::raw('NULL as banco_nombre');

        return $query->where('n.id_empresa', $companyId)->orderBy('n.id')->get($select);
    }

    private function position($companyId, $row, &$result)
    {
        $name = trim((string) $row->cargo);
        if ($name === '') return null;

        $code = 'V2-' . strtoupper(substr(sha1($name), 0, 12));
        $position = DB::table('v3_hr_positions')->where('id_empresa', $companyId)
            ->where(function ($query) use ($code, $name) {
                $query->where('codigo', $code)->orWhere('nombre', $name);
            })->first();
        if ($position) return $position->id;

        $now = date('Y-m-d H:i:s');
        $id = DB::table('v3_hr_positions')->insertGetId(array(
            'id_empresa' => $companyId, 'codigo' => $code, 'nombre' => $name,
            'area' => $this->nullable($row->area_nombre), 'salario_minimo' => 0,
            'funciones' => null, 'estado' => 1, 'created_at' => $now, 'updated_at' => $now,
        ));
        $result['positions']++;
        return $id;
    }

    private function names($row)
    {
        $value = trim(implode(' ', array_filter(array(trim((string) $row->nombre1), trim((string) $row->nombre2)))));
        if ($value !== '') return $value;
        return trim((string) $row->nomina_nombres) ?: 'SIN NOMBRES';
    }

    private function surnames($row)
    {
        $value = trim(implode(' ', array_filter(array(trim((string) $row->apellido1), trim((string) $row->apellido2)))));
        return $value !== '' ? $value : 'SIN APELLIDOS';
    }

    private function gender($value)
    {
        $value = strtoupper(trim((string) $value));
        if (in_array($value, array('M', '1', 'MASCULINO'), true)) return 'masculino';
        if (in_array($value, array('F', '2', 'FEMENINO'), true)) return 'femenino';
        return null;
    }

    private function weeklyHours($journey)
    {
        if ((string) $journey === '1') return 20;
        if ((string) $journey === '2') return 30;
        return 40;
    }

    private function validDate($value)
    {
        if (!$value || $value === '0000-00-00') return null;
        $time = strtotime((string) $value);
        return $time === false ? null : date('Y-m-d', $time);
    }

    private function nullable($value)
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function isAvailable()
    {
        foreach (array('ct_nomina', 'users', 'v3_hr_employees', 'v3_hr_positions', 'v3_hr_contracts') as $table) {
            if (!Schema::hasTable($table)) return false;
        }
        return true;
    }
}
