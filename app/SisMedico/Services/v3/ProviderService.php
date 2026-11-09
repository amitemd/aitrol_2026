<?php

namespace Sis_medico\Services\v3;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Sis_medico\v3\contable\Auditorias;
use Sis_medico\v3\contable\LogConfig;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\Services\PersonIdentityService;
use Sis_medico\Services\PersonRoleTenantService;
use Throwable;

class ProviderService
{
    /**
     * Busca primero proveedores habilitados para la empresa actual. Cuando no
     * hay coincidencias, ofrece personas canónicas que todavía pueden asumir
     * el rol de proveedor.
     */
    public function searchForExpense(string $term): array
    {
        $term = trim($term);
        $companyId = trim((string) session('id_empresa'));
        if (mb_strlen($term) < 2 || $companyId === '') return [];

        $providers = DB::table('proveedor as provider')
            ->join('personas as person', 'person.id', '=', 'provider.persona_id')
            ->join('persona_empresa as membership', function ($join) use ($companyId) {
                $join->on('membership.persona_id', '=', 'person.id')
                    ->where('membership.id_empresa', '=', $companyId)
                    ->where('membership.rol', '=', 'PROVEEDOR')
                    ->where('membership.estado', '=', 1);
            })
            ->where('provider.estado', 1)
            ->where(function ($query) use ($term) {
                $query->where('person.identificacion', 'like', '%' . $term . '%')
                    ->orWhere('person.razon_social', 'like', '%' . $term . '%')
                    ->orWhere('person.nombre_comercial', 'like', '%' . $term . '%')
                    ->orWhere('person.nombre1', 'like', '%' . $term . '%')
                    ->orWhere('person.apellido1', 'like', '%' . $term . '%');
            })
            ->select([
                'provider.id',
                'person.id as persona_id',
                'person.identificacion',
                'person.razon_social',
                'person.nombre_comercial',
                'person.nombre1',
                'person.nombre2',
                'person.apellido1',
                'person.apellido2',
            ])
            ->orderBy('person.razon_social')
            ->limit(20)
            ->get();

        if ($providers->isNotEmpty()) {
            return $providers->map(function ($row) {
                return [
                    'id' => (string) $row->id,
                    'persona_id' => (int) $row->persona_id,
                    'text' => $this->personOptionText($row),
                    'source' => 'provider',
                ];
            })->values()->all();
        }

        return DB::table('personas as person')
            ->whereRaw('CHAR_LENGTH(person.identificacion) <= 13')
            ->where(function ($query) use ($term) {
                $query->where('person.identificacion', 'like', '%' . $term . '%')
                    ->orWhere('person.razon_social', 'like', '%' . $term . '%')
                    ->orWhere('person.nombre_comercial', 'like', '%' . $term . '%')
                    ->orWhere('person.nombre1', 'like', '%' . $term . '%')
                    ->orWhere('person.apellido1', 'like', '%' . $term . '%');
            })
            ->select([
                'person.id as persona_id',
                'person.identificacion',
                'person.razon_social',
                'person.nombre_comercial',
                'person.nombre1',
                'person.nombre2',
                'person.apellido1',
                'person.apellido2',
            ])
            ->orderBy('person.razon_social')
            ->limit(20)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => 'person:' . $row->persona_id,
                    'persona_id' => (int) $row->persona_id,
                    'text' => $this->personOptionText($row) . ' (persona registrada)',
                    'source' => 'person',
                ];
            })->values()->all();
    }

    public function linkPersonForExpense(int $personId): array
    {
        return DB::transaction(function () use ($personId) {
            $person = DB::table('personas')->where('id', $personId)->lockForUpdate()->first();
            if (!$person) throw new \DomainException('La persona seleccionada ya no existe.');
            return $this->ensureExpenseProvider($person);
        });
    }

    public function createBasicForExpense(Request $request): array
    {
        return DB::transaction(function () use ($request) {
            $identification = strtoupper((string) preg_replace(
                '/[^A-Za-z0-9]/',
                '',
                trim((string) $request->input('identificacion'))
            ));
            $now = date('Y-m-d H:i:s');
            $actorId = Auth::id();
            $ip = Utilidades::getRealIP();
            $person = DB::table('personas')->where('identificacion', $identification)->lockForUpdate()->first();
            $values = [
                'tipo_documento' => $request->input('tipo_documento'),
                'tipo_persona' => 'JURIDICA',
                'razon_social' => $request->input('razon_social'),
                'nombre_comercial' => $request->input('nombre_comercial') ?: $request->input('razon_social'),
                'email' => $request->input('email') ?: null,
                'telefono1' => $request->input('telefono1') ?: null,
                'direccion' => $request->input('direccion') ?: null,
                'id_usuariomod' => $actorId,
                'ip_modificacion' => $ip,
                'updated_at' => $now,
            ];

            if (!$person) {
                $values['identificacion'] = $identification;
                $values['id_usuariocrea'] = $actorId;
                $values['ip_creacion'] = $ip;
                $values['created_at'] = $now;
                $personId = DB::table('personas')->insertGetId($values);
            } else {
                $personId = (int) $person->id;
                $changes = array_filter($values, function ($value, $column) {
                    return in_array($column, ['id_usuariomod', 'ip_modificacion', 'updated_at'], true)
                        || ($value !== null && $value !== '');
                }, ARRAY_FILTER_USE_BOTH);
                if (!empty($person->tipo_persona)) unset($changes['tipo_persona']);
                DB::table('personas')->where('id', $personId)->update($changes);
            }

            $person = DB::table('personas')->where('id', $personId)->first();
            return $this->ensureExpenseProvider($person);
        });
    }

    public function save(Request $request): string
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $origin = $request->input();

        try {
            $data = $request->only([
                'estado',
                'ciudad',
                'tipo',
                'id',
                'razonsocial',
                'nombrecomercial',
                'email',
                'email2',
                'telefono1',
                'telefono2',
                'id_cuentas',
                'id_tipoproveedor',
                'id_ciudad',
                'direccion',
                'urbanizacion',
                'etapa',
                'manzana',
                'villa',
                'referencia',
                'id_banco',
                'cuenta',
                'beneficiario',
                'identificacion',
                'tipo_cuenta',
            ]);
            $data['id_banco'] = !empty($data['id_banco']) ? $data['id_banco'] : null;
            $data['provisiones'] = json_encode(
                array_values((array) $request->input('provisiones', [])),
                JSON_UNESCAPED_UNICODE
            );

            $provider = Proveedor::withoutGlobalScope('active_company')->find($data['id']);
            $ip = Utilidades::getRealIP();

            if ($provider === null) {
                $data['id_usuariocrea'] = Auth::id();
                $data['id_usuariomod'] = Auth::id();
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = (string) $data['id'];
                Proveedor::insert($data);
                $operation = 'Se creo el proveedor con id.: ' . $id;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::id();
                $data['ip_modificacion'] = $ip;
                Proveedor::withoutGlobalScope('active_company')->whereKey($id)->update($data);
                $operation = 'Se actualizo el proveedor con id.: ' . $id;
            }

            $savedProvider = Proveedor::withoutGlobalScope('active_company')->find($id);
            if (!$savedProvider) throw new \RuntimeException('No fue posible recuperar el proveedor guardado.');
            app(PersonIdentityService::class)->sync($savedProvider, 'PROVEEDOR', 'id', 'JURIDICA');

            $this->prepareAudit($request, $origin, $operation);
            Auditorias::saveAuditoria($request, DB::getQueryLog());
            DB::commit();

            return json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => ['id' => $id],
                'reload' => '',
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();
            $reference = 'PRV-' . date('Ymd-His') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 6));

            Log::error('Error al guardar proveedor [' . $reference . ']', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'user_id' => Auth::id(),
            ]);

            $this->prepareAudit($request, $origin, 'No se pudo guardar el proveedor. Referencia: ' . $reference);
            try {
                Auditorias::saveAuditoria($request, DB::getQueryLog());
            } catch (Throwable $auditException) {
                Log::warning('No se pudo registrar la auditoría del proveedor [' . $reference . ']', [
                    'message' => $auditException->getMessage(),
                ]);
            }

            return json_encode([
                'status' => 'error',
                'code' => 500,
                'message' => 'No se pudo guardar el proveedor. Inténtelo nuevamente. Código de referencia: ' . $reference,
            ]);
        }
    }

    private function prepareAudit(Request $request, array $origin, string $operation): void
    {
        $request->c = 'Proveedores';
        $request->s = 'saveProveedor';
        $request->d = $origin['d'] ?? '|';
        $request->m = 'Proveedor';
        $request->o = $operation;
    }

    private function ensureExpenseProvider($person): array
    {
        $companyId = trim((string) session('id_empresa'));
        if ($companyId === '') throw new \DomainException('Seleccione una empresa antes de crear el proveedor.');

        $identification = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', (string) $person->identificacion));
        if ($identification === '' || mb_strlen($identification) > 13) {
            throw new \DomainException('La identificación de la persona no es válida para un proveedor.');
        }

        $provider = DB::table('proveedor')
            ->where('persona_id', $person->id)
            ->orWhere('id', $identification)
            ->lockForUpdate()
            ->first();
        $now = date('Y-m-d H:i:s');
        $actorId = Auth::id();
        $ip = Utilidades::getRealIP();
        $payableAccount = LogConfig::busqueda('2.01.03.01.01');
        if (trim((string) $payableAccount) === '') {
            throw new \DomainException('Configure la cuenta por pagar predeterminada antes de crear proveedores rápidos.');
        }

        if (!$provider) {
            $providerType = DB::table('tipoproveedor')->where('estado', 1)->orderBy('id')->value('id');
            if (!$providerType) {
                throw new \DomainException('No existe un tipo de proveedor activo para completar el registro.');
            }

            $legalName = $this->personDisplayName($person);
            DB::table('proveedor')->insert([
                'id' => $identification,
                'persona_id' => $person->id,
                // El detalle canónico permanece en personas; estos campos se
                // reflejan únicamente por compatibilidad con módulos antiguos.
                'razonsocial' => $legalName,
                'nombrecomercial' => $person->nombre_comercial ?: $legalName,
                'ciudad' => '',
                'direccion' => $person->direccion ?: '',
                'email' => $person->email ?: '',
                'email2' => null,
                'telefono1' => $person->telefono1 ?: '',
                'telefono2' => $person->telefono2 ?: '',
                'tipo' => $this->legacyDocumentType($person->tipo_documento),
                'estado' => 1,
                'id_tipoproveedor' => $providerType,
                'visualizar' => 1,
                'id_cuentas' => $payableAccount,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'provisiones' => '[]',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $providerId = $identification;
        } else {
            $providerId = (string) $provider->id;
            $providerChanges = [
                'persona_id' => $person->id,
                'estado' => 1,
                'visualizar' => 1,
                'id_usuariomod' => $actorId,
                'ip_modificacion' => $ip,
                'updated_at' => $now,
            ];
            if (trim((string) $provider->id_cuentas) === '') {
                $providerChanges['id_cuentas'] = $payableAccount;
            }
            DB::table('proveedor')->where('id', $providerId)->update($providerChanges);
        }

        app(PersonRoleTenantService::class)->setRole(
            $person->id,
            'PROVEEDOR',
            true,
            $companyId,
            $actorId,
            $ip
        );

        return [
            'id' => $providerId,
            'persona_id' => (int) $person->id,
            'text' => $this->personOptionText($person),
            'source' => 'provider',
        ];
    }

    private function personOptionText($person): string
    {
        return trim((string) $person->identificacion) . ' | ' . $this->personDisplayName($person);
    }

    private function personDisplayName($person): string
    {
        $name = trim((string) ($person->nombre_comercial ?: $person->razon_social));
        if ($name !== '') return $name;
        $name = trim(implode(' ', array_filter([
            $person->nombre1 ?? null,
            $person->nombre2 ?? null,
            $person->apellido1 ?? null,
            $person->apellido2 ?? null,
        ])));
        return $name !== '' ? $name : (string) $person->identificacion;
    }

    private function legacyDocumentType($documentType): int
    {
        $documentType = strtoupper(trim((string) $documentType));
        if (ctype_digit($documentType)) return (int) $documentType;
        if (strpos($documentType, 'RUC') !== false) return 4;
        if (strpos($documentType, 'CED') !== false) return 5;
        if (strpos($documentType, 'PAS') !== false) return 6;
        return 6;
    }
}
