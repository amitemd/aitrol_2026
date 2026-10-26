<?php

namespace App\Services\NewSystem;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Sis_medico\User;

class UserAdministrationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, string $actorId, string $ipAddress): User
    {
        return DB::transaction(function () use ($data, $actorId, $ipAddress): User {
            $now = now();

            DB::table('users')->insert([
                'id' => $data['id'],
                'nombre1' => $data['nombre1'],
                'nombre2' => $data['nombre2'] ?: null,
                'apellido1' => $data['apellido1'],
                'apellido2' => $data['apellido2'],
                'tipo_documento' => 1,
                'email' => $data['email'],
                'estado' => (int) $data['estado'],
                'password' => Hash::make($data['password']),
                'imagen_url' => 'avatar.jpg',
                'id_tipo_usuario' => (int) $data['id_tipo_usuario'],
                'uso_sistema' => 1,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ipAddress,
                'ip_modificacion' => $ipAddress,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
                'nombres' => trim($data['nombre1'].' '.($data['nombre2'] ?? '')),
                'apellidos' => trim($data['apellido1'].' '.$data['apellido2']),
                'pais' => 1,
                'institucion' => 'AITROL',
                'tipoUsuario' => (int) $data['id_tipo_usuario'],
                'cedula' => $data['id'],
            ]);

            $this->syncCompanies($data['id'], $data['company_ids'], $actorId, $ipAddress, $now);

            return User::query()->findOrFail($data['id']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data, string $actorId, string $ipAddress): void
    {
        DB::transaction(function () use ($user, $data, $actorId, $ipAddress): void {
            $now = now();
            $attributes = [
                'nombre1' => $data['nombre1'],
                'nombre2' => $data['nombre2'] ?: null,
                'apellido1' => $data['apellido1'],
                'apellido2' => $data['apellido2'],
                'email' => $data['email'],
                'estado' => (int) $data['estado'],
                'id_tipo_usuario' => (int) $data['id_tipo_usuario'],
                'id_usuariomod' => $actorId,
                'ip_modificacion' => $ipAddress,
                'updated_at' => $now,
                'nombres' => trim($data['nombre1'].' '.($data['nombre2'] ?? '')),
                'apellidos' => trim($data['apellido1'].' '.$data['apellido2']),
                'tipoUsuario' => (int) $data['id_tipo_usuario'],
            ];

            if (! empty($data['password'])) {
                $attributes['password'] = Hash::make($data['password']);
            }

            DB::table('users')->where('id', $user->getKey())->update($attributes);

            DB::table('usuario_empresa')->where('id_usuario', $user->getKey())->delete();
            $this->syncCompanies((string) $user->getKey(), $data['company_ids'], $actorId, $ipAddress, $now);
        });
    }

    /**
     * @param  list<string>  $companyIds
     */
    private function syncCompanies(
        string $userId,
        array $companyIds,
        string $actorId,
        string $ipAddress,
        CarbonInterface $timestamp,
    ): void {
        $rows = collect($companyIds)
            ->unique()
            ->map(fn (string $companyId): array => [
                'id_empresa' => $companyId,
                'id_usuario' => $userId,
                'id_usuariocrea' => $actorId,
                'id_usuariomod' => $actorId,
                'ip_creacion' => $ipAddress,
                'ip_modificacion' => $ipAddress,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])
            ->values()
            ->all();

        DB::table('usuario_empresa')->insert($rows);
    }
}
