<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class PersonMaintenanceService
{
    public function paginated($request)
    {
        $query = DB::table('personas as person')->select(array(
            'person.id', 'person.identificacion', 'person.tipo_documento', 'person.tipo_persona',
            'person.nombre1', 'person.nombre2', 'person.apellido1', 'person.apellido2',
            'person.razon_social', 'person.nombre_comercial', 'person.email', 'person.telefono1',
            'person.telefono2', 'person.updated_at',
            DB::raw('(SELECT COUNT(DISTINCT link.id_empresa) FROM persona_empresa link WHERE link.persona_id = person.id AND link.estado = 1) as empresas_count'),
            DB::raw("(SELECT GROUP_CONCAT(DISTINCT link.rol ORDER BY link.rol SEPARATOR ', ') FROM persona_empresa link WHERE link.persona_id = person.id AND link.estado = 1) as roles"),
        ));
        $recordsTotal = DB::table('personas')->count();
        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($filter) use ($search) {
                $term = '%'.$search.'%';
                $filter->where('person.identificacion', 'like', $term)
                    ->orWhere('person.razon_social', 'like', $term)
                    ->orWhere('person.nombre_comercial', 'like', $term)
                    ->orWhere('person.email', 'like', $term)
                    ->orWhereRaw("CONCAT_WS(' ', person.apellido1, person.apellido2, person.nombre1, person.nombre2) LIKE ?", array($term));
            });
        }
        $recordsFiltered = (clone $query)->count('person.id');
        $columns = array('person.identificacion', 'person.tipo_persona', 'person.apellido1', 'person.email',
            'person.identificacion', 'person.identificacion', 'person.updated_at', 'person.id');
        $column = (int) $request->input('order.0.column', 0);
        $direction = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query->orderBy(isset($columns[$column]) ? $columns[$column] : 'person.identificacion', $direction);
        $length = max(10, min(100, (int) $request->input('length', 25)));
        $start = max(0, (int) $request->input('start', 0));
        $rows = $query->offset($start)->limit($length)->get()->map(function ($person) {
            $person->nombre = $person->tipo_persona === 'JURIDICA'
                ? trim($person->razon_social.' '.$person->nombre_comercial)
                : trim($person->apellido1.' '.$person->apellido2.' '.$person->nombre1.' '.$person->nombre2);
            return $person;
        });
        return array('draw' => (int) $request->input('draw', 1), 'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered, 'data' => $rows);
    }

    public function find($id)
    {
        $person = DB::table('personas')->where('id', $id)->first();
        if (!$person) return null;
        $person->vinculos = DB::table('persona_empresa as link')
            ->leftJoin('empresa as company', 'company.id', '=', 'link.id_empresa')
            ->where('link.persona_id', $id)->where('link.estado', 1)
            ->orderBy('company.nombrecomercial')->orderBy('link.rol')
            ->get(array('link.id_empresa', 'link.rol', 'company.nombrecomercial'));
        return $person;
    }

    public function save(array $input, $actorId, $ip)
    {
        $id = isset($input['id']) ? (int) $input['id'] : 0;
        $identification = $this->normalize($input['identificacion']);
        $existing = $id ? DB::table('personas')->where('id', $id)->first() : null;
        if ($id && !$existing) throw new \InvalidArgumentException('La persona que intenta editar ya no existe.');
        if ($existing && $existing->identificacion !== $identification && $this->hasSourceLinks($id)) {
            throw new \InvalidArgumentException('La identificación no puede cambiar porque la persona está vinculada con registros del sistema.');
        }
        $duplicate = DB::table('personas')->where('identificacion', $identification);
        if ($id) $duplicate->where('id', '<>', $id);
        if ($duplicate->exists()) throw new \InvalidArgumentException('Ya existe una persona con esta identificación.');

        $now = date('Y-m-d H:i:s');
        $email = $this->nullable($input, 'email');
        $values = array(
            'identificacion' => $identification,
            'tipo_documento' => $this->nullable($input, 'tipo_documento'),
            'tipo_persona' => strtoupper((string) $input['tipo_persona']),
            'nombre1' => $this->upper($input, 'nombre1'), 'nombre2' => $this->upper($input, 'nombre2'),
            'apellido1' => $this->upper($input, 'apellido1'), 'apellido2' => $this->upper($input, 'apellido2'),
            'razon_social' => $this->upper($input, 'razon_social'),
            'nombre_comercial' => $this->upper($input, 'nombre_comercial'),
            'fecha_nacimiento' => $this->nullable($input, 'fecha_nacimiento'),
            'sexo' => $this->nullable($input, 'sexo'), 'email' => $email === null ? null : mb_strtolower($email, 'UTF-8'),
            'telefono1' => $this->nullable($input, 'telefono1'), 'telefono2' => $this->nullable($input, 'telefono2'),
            'direccion' => $this->upper($input, 'direccion'), 'id_usuariomod' => $actorId,
            'ip_modificacion' => $ip, 'updated_at' => $now,
        );

        return DB::transaction(function () use ($id, $values, $actorId, $ip, $now) {
            if ($id) {
                DB::table('personas')->where('id', $id)->update($values);
                $personId = $id;
            } else {
                $values['id_usuariocrea'] = $actorId;
                $values['ip_creacion'] = $ip;
                $values['created_at'] = $now;
                $personId = DB::table('personas')->insertGetId($values);
            }
            $this->propagate($personId, $values, $actorId, $ip);
            return $personId;
        });
    }

    private function propagate($personId, array $person, $actorId, $ip)
    {
        $displayName = $person['tipo_persona'] === 'JURIDICA'
            ? ($person['razon_social'] ?: $person['nombre_comercial'])
            : trim(implode(' ', array_filter(array($person['nombre1'], $person['nombre2'], $person['apellido1'], $person['apellido2']))));
        $person['_display_name'] = $displayName ?: null;
        $definitions = array(
            'users' => array('nombre1'=>'nombre1','nombre2'=>'nombre2','apellido1'=>'apellido1','apellido2'=>'apellido2','fecha_nacimiento'=>'fecha_nacimiento','email'=>'email','telefono1'=>'telefono1','telefono2'=>'telefono2','direccion'=>'direccion','tipo_documento'=>'tipo_documento'),
            'paciente' => array('nombre1'=>'nombre1','nombre2'=>'nombre2','apellido1'=>'apellido1','apellido2'=>'apellido2','fecha_nacimiento'=>'fecha_nacimiento','sexo'=>'sexo','mail_opcional'=>'email','telefono1'=>'telefono1','telefono2'=>'telefono2','direccion'=>'direccion','tipo_documento'=>'tipo_documento'),
            'proveedor' => array('razonsocial'=>'_display_name','nombrecomercial'=>'nombre_comercial','email'=>'email','telefono1'=>'telefono1','telefono2'=>'telefono2','direccion'=>'direccion'),
            'ct_clientes' => array('nombre'=>'_display_name','telefono1_representante'=>'telefono1','telefono2_representante'=>'telefono2','direccion'=>'direccion'),
        );
        foreach ($definitions as $table => $mapping) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'persona_id')) continue;
            $changes = array();
            foreach ($mapping as $target => $source) {
                if (Schema::hasColumn($table, $target)) $changes[$target] = $person[$source];
            }
            foreach (array('id_usuariomod'=>$actorId, 'ip_modificacion'=>$ip, 'updated_at'=>date('Y-m-d H:i:s')) as $column => $value) {
                if (Schema::hasColumn($table, $column)) $changes[$column] = $value;
            }
            if ($changes) DB::table($table)->where('persona_id', $personId)->update($changes);
        }
    }

    private function hasSourceLinks($personId)
    {
        foreach (array('users', 'paciente', 'proveedor', 'ct_clientes') as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'persona_id')
                && DB::table($table)->where('persona_id', $personId)->exists()) return true;
        }
        return false;
    }

    private function normalize($value) { return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', trim((string) $value))); }
    private function nullable(array $input, $key) { $value = trim((string) (isset($input[$key]) ? $input[$key] : '')); return $value === '' ? null : $value; }
    private function upper(array $input, $key) { $value = $this->nullable($input, $key); return $value === null ? null : mb_strtoupper($value, 'UTF-8'); }
}
