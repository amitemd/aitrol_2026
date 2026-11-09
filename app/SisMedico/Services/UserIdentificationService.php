<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Sis_medico\User;

class UserIdentificationService
{
    public function resolve(Request $request, $lock = false)
    {
        $self = filter_var($request->input('self_profile'), FILTER_VALIDATE_BOOLEAN);
        if ($self) $request->merge(array('id' => Auth::id(), 'id_original' => Auth::id()));
        $original = trim((string) $request->input('id_original'));
        $editing = $self || filter_var($request->input('edit_user'), FILTER_VALIDATE_BOOLEAN) || $original !== '';
        $user = null;
        if ($editing) {
            if ($original === '') $this->invalid('id_original', 'Falta la identificación original. Recargue el usuario antes de editar.');
            $query = $self ? User::withoutGlobalScope('active_company')
                : app(CompanyUserAssignmentService::class)->queryForCompanyAdministration(session('id_empresa'));
            if ($lock) $query->lockForUpdate();
            $user = $query->where('users.id', $original)->first();
            if (!$user) $this->invalid('id_original', 'El usuario original ya no existe o no pertenece a la empresa activa. Recargue el listado.');
        }
        $request->merge(array('id' => trim((string) $request->input('id'))));
        $canonical = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $request->input('id')));
        $equivalent = User::withoutGlobalScopes()
            ->whereRaw("UPPER(REPLACE(id, '-', '')) = ?", array($canonical));
        if ($lock) $equivalent->lockForUpdate();

        if (!$editing && $canonical !== '') {
            $existing = (clone $equivalent)->first();
            if ($existing) {
                $alreadyAssigned = app(CompanyUserAssignmentService::class)
                    ->queryForCompanyAdministration(session('id_empresa'))
                    ->where('users.id', $existing->id)
                    ->exists();
                if ($alreadyAssigned) {
                    $this->invalid('id', 'Este usuario ya pertenece a la empresa activa.');
                }

                // users representa la cuenta global y usuario_empresa su acceso
                // por empresa. En un alta para otra empresa se reutiliza la
                // cuenta existente en lugar de insertar una segunda identidad.
                $user = $existing;
                $request->merge(array('id' => (string) $existing->id));
            }
        }

        $duplicate = clone $equivalent;
        if ($user) $duplicate->where('id', '<>', $user->id);
        if ($canonical !== '' && $duplicate->exists()) {
            $this->invalid('id', 'La identificación ya pertenece a otro usuario.');
        }
        $unique = Rule::unique('users', 'id');
        if ($user) $unique->ignore($user->id, 'id');
        Validator::make($request->all(), array(
            'id' => array('required', 'string', 'max:13', 'regex:/^[A-Za-z0-9-]+$/', $unique),
        ), array('id.unique' => 'La identificación ya pertenece a otro usuario.'))->validate();
        return $user;
    }

    /** El llamador incluye usuario, relaciones y auditoría en la misma transacción. */
    public function update(User $user, array $values)
    {
        if (DB::transactionLevel() === 0) throw new \LogicException('La actualización del usuario requiere una transacción.');
        $old = (string) $user->id;
        $user = User::withoutGlobalScopes()->where('id', $old)->lockForUpdate()->first();
        if (!$user) $this->invalid('id_original', 'El usuario original ya no existe. Recargue el listado.');
        $new = (string) $values['id'];
        $references = $old !== $new ? $this->unconstrainedReferences() : array();
        $references = array_filter(is_array($references) ? $references : $references->all(), function ($reference) use ($old) {
            // MySQL compara texto no numérico con cero en columnas numéricas.
            return !isset($reference->DATA_TYPE) || ctype_digit($old)
                || !in_array(strtolower($reference->DATA_TYPE), array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'decimal', 'float', 'double'), true);
        });
        foreach ($references as $reference) {
            if (isset($reference->ENGINE) && strtoupper($reference->ENGINE) !== 'INNODB'
                && DB::table($reference->TABLE_NAME)->where($reference->COLUMN_NAME, $old)->exists()) {
                $this->invalid('id', 'No se puede cambiar la identificación: la tabla '.$reference->TABLE_NAME.' no admite transacciones.');
            }
        }
        if ($old !== $new) $this->updatePersonIdentification($user, $new, $values);
        if (\Sis_medico\Support\RequestSchema::hasColumn('users', 'updated_at')) $values['updated_at'] = date('Y-m-d H:i:s');
        // Las FK con ON UPDATE CASCADE actualizan las referencias declaradas.
        DB::table('users')->where('id', $old)->update($values);
        foreach ($references as $reference) {
            DB::table($reference->TABLE_NAME)->where($reference->COLUMN_NAME, $old)
                ->update(array($reference->COLUMN_NAME => $new));
        }
    }

    private function updatePersonIdentification(User $user, $new, array &$values)
    {
        if (!\Sis_medico\Support\RequestSchema::hasTable('personas') || !\Sis_medico\Support\RequestSchema::hasColumn('users', 'persona_id')) return;
        $normalize = function ($value) { return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $value)); };
        $person = $user->persona_id
            ? DB::table('personas')->where('id', $user->persona_id)->lockForUpdate()->first()
            : DB::table('personas')->where('identificacion', $normalize($user->id))->lockForUpdate()->first();
        $duplicate = DB::table('personas')->where('identificacion', $normalize($new));
        if ($person) $duplicate->where('id', '<>', $person->id);
        if ($duplicate->exists()) $this->invalid('id', 'La identificación ya pertenece a otra persona.');
        if ($person) {
            DB::table('personas')->where('id', $person->id)->update(array('identificacion' => $normalize($new)));
            $values['persona_id'] = $person->id;
        }
    }

    protected function unconstrainedReferences()
    {
        // Columnas de usuario conocidas; nunca modificar una FK a otra entidad.
        $columns = array('id_usuario', 'user_id', 'id_user', 'usuid', 'id_usuariocrea', 'id_usuariomod',
            'id_usuario_permiso', 'id_usuario_archivo', 'id_usuario_auditoria', 'id_usuario_creacion_menu',
            'id_usuario_modificacion_menu', 'id_usuario_creacion_permiso', 'id_usuario_modificacion_permiso',
            'id_usuario_creacion_archivo', 'id_usuario_modificacion_archivo', 'id_usuario_crea',
            'id_doctor', 'id_doctor1', 'id_doctor2', 'id_doctor3', 'id_doctor_ayudante',
            'id_doctor_firma', 'id_anestesiologo');
        $specific = array(
            'access_user_roles' => array('asignado_por'), 'access_user_permissions' => array('asignado_por'),
            'v3_registration_invitations' => array('used_by', 'created_by'),
            'user_network_access_rules' => array('created_by', 'updated_by'),
            'security_access_events' => array('user_id'),
            'initial_load_imports' => array('created_by'),
            'ticket_soporte_tecnico' => array('usuario_solicitante'),
            'usuarios_control_sintoma' => array('user'),
            'control_temperatura' => array('responsable'), 'control_temparea' => array('responsable'),
            'registro_nevera' => array('responsable'),
            'apps_plan_miembros' => array('id_referencia'),
        );
        // apps_orden.id_usuario almacena paciente.id (BigDataController).
        $patientTables = array('apps_orden');
        if (DB::connection()->getDriverName() === 'mysql') {
            $query = DB::table('information_schema.COLUMNS as c')
                ->join('information_schema.TABLES as t', function ($join) {
                    $join->on('t.TABLE_SCHEMA', '=', 'c.TABLE_SCHEMA')->on('t.TABLE_NAME', '=', 'c.TABLE_NAME');
                })->where('c.TABLE_SCHEMA', DB::connection()->getDatabaseName())->where('t.TABLE_TYPE', 'BASE TABLE')
                ->where(function ($query) use ($columns, $specific) {
                    $query->whereIn('c.COLUMN_NAME', $columns);
                    foreach ($specific as $table => $names) $query->orWhere(function ($q) use ($table, $names) {
                        $q->where('c.TABLE_NAME', $table)->whereIn('c.COLUMN_NAME', $names);
                    });
                })->whereNotExists(function ($key) {
                    $key->select(DB::raw(1))->from('information_schema.KEY_COLUMN_USAGE as k')
                        ->whereColumn('k.TABLE_SCHEMA', 'c.TABLE_SCHEMA')->whereColumn('k.TABLE_NAME', 'c.TABLE_NAME')
                        ->whereColumn('k.COLUMN_NAME', 'c.COLUMN_NAME')->whereNotNull('k.REFERENCED_TABLE_NAME');
                })->where(function ($query) use ($patientTables) {
                    $query->where('c.COLUMN_NAME', '<>', 'id_usuario')->orWhereNotIn('c.TABLE_NAME', $patientTables);
                });
            return $query->get(array('c.TABLE_NAME', 'c.COLUMN_NAME', 'c.DATA_TYPE', 't.ENGINE'));
        }
        $references = array();
        foreach (Schema::getTables() as $table) {
            $name = $table['name'];
            $foreignColumns = array();
            foreach (Schema::getForeignKeys($name) as $key) $foreignColumns = array_merge($foreignColumns, $key['columns']);
            foreach (array_intersect(\Sis_medico\Support\RequestSchema::getColumnListing($name), array_merge($columns, $specific[$name] ?? array())) as $column) {
                if ($column === 'id_usuario' && in_array($name, $patientTables, true)) continue;
                if (!in_array($column, $foreignColumns, true)) $references[] = (object) array('TABLE_NAME' => $name, 'COLUMN_NAME' => $column);
            }
        }
        return $references;
    }

    private function invalid($field, $message)
    {
        throw ValidationException::withMessages(array($field => $message));
    }
}
