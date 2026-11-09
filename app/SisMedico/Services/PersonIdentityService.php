<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;

class PersonIdentityService
{
    public function sync($record, $role, $identificationColumn, $kind = 'NATURAL', $companyId = null)
    {
        if (!Schema::hasTable('personas') || !Schema::hasTable('persona_empresa')) return null;
        $identification = $this->normalize($record->getAttribute($identificationColumn));
        if ($identification === '') return null;

        return DB::transaction(function () use ($record, $role, $identification, $kind, $companyId) {
            $person = DB::table('personas')->where('identificacion', $identification)->lockForUpdate()->first();
            $values = $this->values($record, $kind);
            if (!$person) {
                $values['identificacion'] = $identification;
                $values['created_at'] = date('Y-m-d H:i:s');
                $personId = DB::table('personas')->insertGetId($values);
            } else {
                $personId = $person->id;
                // Un rol con menos datos nunca debe borrar información ya
                // consolidada por otro (por ejemplo, usuario vs. paciente).
                $changes = array_filter($values, function ($value, $column) {
                    return in_array($column, array('updated_at', 'ip_modificacion', 'id_usuariomod'), true)
                        || ($value !== null && $value !== '');
                }, ARRAY_FILTER_USE_BOTH);
                if (!empty($person->tipo_persona)) unset($changes['tipo_persona']);
                DB::table('personas')->where('id', $personId)->update($changes);
            }

            if ($record->getAttribute('persona_id') != $personId) {
                DB::table($record->getTable())->where($record->getKeyName(), $record->getKey())
                    ->update(array('persona_id' => $personId));
                $record->setAttribute('persona_id', $personId);
            }

            $companyId = trim((string) ($companyId ?: session('id_empresa')));
            if ($companyId !== '') {
                app(PersonRoleTenantService::class)->setRole(
                    $personId, $role, true, $companyId,
                    auth()->check() ? auth()->id() : null,
                    app()->runningInConsole() ? '127.0.0.1' : request()->ip()
                );
            }
            return $personId;
        });
    }

    private function values($record, $kind)
    {
        return array(
            'tipo_documento' => $record->getAttribute('tipo_documento') ?: $record->getAttribute('tipo'),
            'tipo_persona' => $kind,
            'nombre1' => $record->getAttribute('nombre1'), 'nombre2' => $record->getAttribute('nombre2'),
            'apellido1' => $record->getAttribute('apellido1'), 'apellido2' => $record->getAttribute('apellido2'),
            'razon_social' => $record->getAttribute('razonsocial') ?: $record->getAttribute('nombre'),
            'nombre_comercial' => $record->getAttribute('nombrecomercial'),
            'fecha_nacimiento' => $record->getAttribute('fecha_nacimiento') ?: null,
            'sexo' => $record->getAttribute('sexo'),
            'email' => $record->getAttribute('email') ?: ($record->getAttribute('email_representante') ?: $record->getAttribute('mail_opcional')),
            'telefono1' => $record->getAttribute('telefono1') ?: $record->getAttribute('telefono1_representante'),
            'telefono2' => $record->getAttribute('telefono2') ?: $record->getAttribute('telefono2_representante'),
            'direccion' => $record->getAttribute('direccion') ?: $record->getAttribute('direccion_representante'),
            'id_usuariomod' => auth()->check() ? auth()->id() : null,
            'ip_modificacion' => app()->runningInConsole() ? '127.0.0.1' : request()->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
        );
    }

    private function normalize($value)
    {
        return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', trim((string) $value)));
    }
}
