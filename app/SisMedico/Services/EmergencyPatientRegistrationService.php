<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Paciente;
use Sis_medico\User;

class EmergencyPatientRegistrationService
{
    public function registerMinimal(array $data, Request $request, array $context)
    {
        Validator::make($data, [
            'without_document'=>'nullable|boolean',
            'registration_token'=>'required_if:without_document,1|nullable|uuid',
            'id_paciente'=>'required|string|min:3|max:13|regex:/^[A-Za-z0-9-]+$/',
            'nombre1'=>'nullable|string|max:60', 'nombre2'=>'nullable|string|max:60',
            'apellido1'=>'nullable|string|max:60', 'apellido2'=>'nullable|string|max:60',
            'sexo'=>'nullable|in:1,2', 'tipo_documento'=>'nullable|in:1,2,3',
        ])->validate();
        $withoutDocument = !empty($data['without_document']);
        if ($withoutDocument) {
            $data['id_paciente'] = strtoupper(trim((string) $data['id_paciente']));
            Validator::make($data, [
                'id_paciente' => ['required', 'regex:/^(?=.*[A-Z])(?=.*[0-9])[A-Z0-9]{6}$/'],
            ], [
                'id_paciente.regex' => 'El código provisional debe contener exactamente 6 caracteres alfanuméricos en mayúscula.',
            ])->validate();
            // Stable on retry, different for each explicit new-patient action.
            $token = hash('sha256', $context['company_id'].'|'.$context['actor_id'].'|'.$data['registration_token']);
            $data['tipo_documento'] = 3;
            $data['emergency_registration_token'] = $token;
            $data['nombre1'] = 'N/A';
            $data['apellido1'] = 'N/A';
            $existing = Paciente::withoutGlobalScope('active_company')->where('id', $data['id_paciente'])->first();
            if ($existing && $existing->emergency_registration_token !== $token) {
                throw new \DomainException('No se pudo asignar el código provisional. Abra un nuevo registro.');
            }
        } else {
            $person = $this->findPerson($data['id_paciente']);
            if ($person) {
                foreach (['nombre1', 'nombre2', 'apellido1', 'apellido2', 'sexo', 'tipo_documento'] as $field) {
                    if (!isset($data[$field]) || trim((string) $data[$field]) === '') $data[$field] = $person->$field;
                }
            }
        }
        $data['emergency_identity_pending'] = $withoutDocument || empty(trim($data['nombre1'] ?? '')) || empty(trim($data['apellido1'] ?? ''));
        $data['nombre1'] = trim($data['nombre1'] ?? '') ?: 'POR IDENTIFICAR';
        $data['apellido1'] = trim($data['apellido1'] ?? '') ?: 'PACIENTE';
        return $this->register($data, $request, $context);
    }

    public function findPerson($identification)
    {
        return DB::table('personas')->where('identificacion', $this->identification($identification))
            ->where('tipo_persona', 'NATURAL')->first();
    }

    public function updateAdmissionIdentity(Paciente $patient, array $data, Request $request, array $context)
    {
        $identity = Validator::make($data, [
            'nombre1' => 'required|string|max:60', 'nombre2' => 'nullable|string|max:60',
            'apellido1' => 'required|string|max:60', 'apellido2' => 'nullable|string|max:60',
        ])->validate();
        foreach ($identity as $field => $value) {
            $identity[$field] = mb_strtoupper(trim((string) $value), 'UTF-8');
        }

        $before = trim($patient->nombre1.' '.$patient->nombre2.' '.$patient->apellido1.' '.$patient->apellido2);
        $changed = false;
        foreach (['nombre1', 'nombre2', 'apellido1', 'apellido2'] as $field) {
            $value = $identity[$field] ?? '';
            if ((string) $patient->$field === $value) continue;
            $patient->$field = $value;
            $changed = true;
        }
        if (!$changed) return $patient;

        $patient->id_usuariomod = $context['actor_id'];
        $patient->ip_modificacion = $request->ip();
        $patient->save();
        if ($patient->persona_id) {
            DB::table('personas')->where('id', $patient->persona_id)->update($identity + [
                'id_usuariomod' => $context['actor_id'], 'ip_modificacion' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        DB::table('log_usuario')->insert([
            'id_usuario' => $context['actor_id'], 'ip_usuario' => $request->ip(),
            'descripcion' => 'ACTUALIZA IDENTIDAD EN INGRESO DE EMERGENCIA',
            'dato_ant1' => $patient->id, 'dato1' => $patient->id,
            'dato_ant2' => $before,
            'dato2' => trim($patient->nombre1.' '.$patient->nombre2.' '.$patient->apellido1.' '.$patient->apellido2),
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return $patient;
    }

    private function identification($value)
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim((string) $value)));
    }

    public function register(array $data, Request $request, array $context)
    {
        foreach (['id_paciente', 'nombre1', 'nombre2', 'apellido1', 'apellido2'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = mb_strtoupper(trim($data[$field]), 'UTF-8');
            }
        }
        Validator::make($data, [
            'id_paciente' => 'required|string|min:3|max:13|regex:/^[A-Z0-9-]+$/',
            'tipo_documento' => 'nullable|integer|in:1,2,3',
            'nombre1' => 'required|string|max:60', 'apellido1' => 'required|string|max:60',
            'nombre2' => 'nullable|string|max:60', 'apellido2' => 'nullable|string|max:60',
            'sexo' => 'nullable|in:1,2',
        ])->validate();

        return DB::transaction(function () use ($data, $request, $context) {
            $person = DB::table('personas')->where('identificacion', $this->identification($data['id_paciente']))
                ->lockForUpdate()->first();
            if ($person) {
                $linkedPatient = Paciente::withoutGlobalScope('active_company')
                    ->where('persona_id', $person->id)->lockForUpdate()->first();
                if ($linkedPatient) {
                    if (!app(PatientTenantService::class)->isAccessible($linkedPatient->id, $context['company_id'])) {
                        app(PatientTenantService::class)->associate(
                            $linkedPatient->id, $context['company_id'], $context['actor_id'], $request->ip()
                        );
                    }
                    return $linkedPatient;
                }
            }

            // Compatibilidad con fichas antiguas que aún no tengan persona_id.
            $patient = Paciente::withoutGlobalScope('active_company')
                ->where('id', $data['id_paciente'])->lockForUpdate()->first();
            if ($patient) {
                if (!app(PatientTenantService::class)->isAccessible($patient->id, $context['company_id'])) {
                    app(PatientTenantService::class)->associate(
                        $patient->id, $context['company_id'], $context['actor_id'], $request->ip()
                    );
                }
                return $patient;
            }

            $insuranceId = \Sis_medico\Seguro::query()->where('inactivo', 1)->orderBy('id')->value('id');
            if (!$insuranceId) throw new \InvalidArgumentException('Configure un seguro activo antes del ingreso.');
            $audit = ['id_usuariocrea' => $context['actor_id'], 'id_usuariomod' => $context['actor_id'],
                'ip_creacion' => $request->ip(), 'ip_modificacion' => $request->ip()];
            $names = ['nombre1' => $data['nombre1'], 'apellido1' => $data['apellido1'],
                'nombre2' => $data['nombre2'] ?? ($person->nombre2 ?? ''),
                'apellido2' => $data['apellido2'] ?? ($person->apellido2 ?? '')];
            $documentType = $data['tipo_documento'] ?? 1;
            // Reutilizar la cuenta de la persona sin modificar credenciales ni permisos.
            if (!User::withoutGlobalScope('active_company')->where('id', $data['id_paciente'])->exists()) {
                User::create($names + $audit + ['id' => $data['id_paciente'],
                    'tipo_documento' => $documentType, 'password' => bcrypt(bin2hex(random_bytes(24))),
                    'imagen_url' => ' ', 'uso_sistema' => 0,
                    'id_tipo_usuario' => app(\Sis_medico\Services\v3\PatientUserType::class)->id()]);
            }
            $knownData = $person ? ['fecha_nacimiento' => $person->fecha_nacimiento,
                'telefono1' => $person->telefono1, 'telefono2' => $person->telefono2,
                'direccion' => $person->direccion, 'mail_opcional' => $person->email] : [];
            // Paciente::saved sincroniza Personas y persona_empresa dentro de esta transacción.
            return Paciente::create($names + $audit + $knownData + ['id' => $data['id_paciente'],
                'tipo_documento' => $documentType, 'id_seguro' => $insuranceId,
                'imagen_url' => ' ', 'id_usuario' => $data['id_paciente'],
                'parentesco' => 'Principal', 'parentescofamiliar' => 'Principal',
                'menoredad' => !empty($knownData['fecha_nacimiento']) && strtotime($knownData['fecha_nacimiento'].' +18 years') > time() ? 1 : 0,
                'sexo' => $data['sexo'] ?? null, 'registro_emergencia_pendiente' => 1,
                'emergency_identity_pending' => !empty($data['emergency_identity_pending']),
                'emergency_registration_token' => $data['emergency_registration_token'] ?? null]);
        }, 3);
    }
}
