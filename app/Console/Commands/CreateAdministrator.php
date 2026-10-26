<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

#[Signature('app:create-admin
    {--id= : Cédula o identificador del administrador, máximo 13 caracteres}
    {--email= : Correo utilizado para iniciar sesión}
    {--company-id= : RUC de la empresa inicial cuando la base no tiene empresas}
    {--company-name=AITROL : Nombre de la empresa inicial}')]
#[Description('Crea de forma segura el primer superadministrador y la empresa mínima de AITROL')]
class CreateAdministrator extends Command
{
    public function handle(): int
    {
        $administratorId = trim((string) ($this->option('id') ?: $this->ask('Cédula o identificador')));
        $email = trim((string) ($this->option('email') ?: $this->ask('Correo para iniciar sesión')));

        if (! preg_match('/^[A-Za-z0-9-]{1,13}$/', $administratorId)) {
            $this->error('El identificador debe tener entre 1 y 13 caracteres alfanuméricos o guiones.');

            return self::FAILURE;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 191) {
            $this->error('Ingrese un correo válido de máximo 191 caracteres.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('Contraseña (mínimo 12 caracteres)');
        $passwordConfirmation = (string) $this->secret('Repita la contraseña');

        if (strlen($password) < 12) {
            $this->error('La contraseña debe contener al menos 12 caracteres.');

            return self::FAILURE;
        }

        if (! hash_equals($password, $passwordConfirmation)) {
            $this->error('Las contraseñas no coinciden.');

            return self::FAILURE;
        }

        if (DB::table('users')
            ->where('id', $administratorId)
            ->orWhere('cedula', $administratorId)
            ->orWhere('email', $email)
            ->exists()) {
            $this->error('Ya existe un usuario con ese identificador, cédula o correo.');

            return self::FAILURE;
        }

        $company = DB::table('empresa')->where('prioridad', 1)->first();
        $companyId = $company?->id;
        $companyName = trim((string) $this->option('company-name'));

        if ($companyId === null) {
            $companyId = trim((string) ($this->option('company-id') ?: $this->ask('RUC de la empresa inicial')));

            if (! preg_match('/^[A-Za-z0-9-]{1,13}$/', $companyId)) {
                $this->error('El RUC debe tener entre 1 y 13 caracteres alfanuméricos o guiones.');

                return self::FAILURE;
            }

            if ($companyName === '' || strlen($companyName) > 255) {
                $this->error('El nombre de la empresa debe tener entre 1 y 255 caracteres.');

                return self::FAILURE;
            }

            if (DB::table('empresa')->where('id', $companyId)->exists()) {
                $this->error('Ya existe una empresa con ese RUC, pero no está marcada como prioritaria.');

                return self::FAILURE;
            }
        }

        $this->components->info('Se creará el usuario '.$email.' como superadministrador.');

        if (! $this->confirm('¿Desea continuar?', true)) {
            $this->warn('Operación cancelada.');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($administratorId, $email, $password, $company, $companyId, $companyName): void {
                $now = now();

                DB::table('users')->insert([
                    'id' => $administratorId,
                    'nombre1' => 'ADMINISTRADOR',
                    'nombre2' => null,
                    'apellido1' => 'AITROL',
                    'apellido2' => 'SISTEMA',
                    'tipo_documento' => 1,
                    'email' => $email,
                    'estado' => 1,
                    'password' => Hash::make($password),
                    'imagen_url' => 'avatar.jpg',
                    'id_tipo_usuario' => 1,
                    'uso_sistema' => 1,
                    'id_usuariocrea' => $administratorId,
                    'id_usuariomod' => $administratorId,
                    'ip_creacion' => 'CLI',
                    'ip_modificacion' => 'CLI',
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                    'nombres' => 'ADMINISTRADOR',
                    'apellidos' => 'AITROL SISTEMA',
                    'pais' => 1,
                    'institucion' => 'AITROL',
                    'tipoUsuario' => 1,
                    'cedula' => $administratorId,
                ]);

                if (! DB::table('tipousuario')->where('id', 1)->exists()) {
                    DB::table('tipousuario')->insert([
                        'id' => 1,
                        'nombre' => 'SUPERADMINISTRADOR',
                        'descripcion' => 'Administrador general del sistema',
                        'estado' => 1,
                        'id_usuariocrea' => $administratorId,
                        'id_usuariomod' => $administratorId,
                        'ip_creacion' => 'CLI',
                        'ip_modificacion' => 'CLI',
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ]);
                }

                if ($company === null) {
                    DB::table('empresa')->insert([
                        'id' => $companyId,
                        'razonsocial' => $companyName,
                        'nombrecomercial' => $companyName,
                        'nombre_corto' => $companyName,
                        'ciudad' => 'QUITO',
                        'direccion' => 'POR CONFIGURAR',
                        'email' => $email,
                        'telefono1' => '0000000000',
                        'telefono2' => '0000000000',
                        'estado' => 1,
                        'id_usuariocrea' => $administratorId,
                        'id_usuariomod' => $administratorId,
                        'ip_creacion' => 'CLI',
                        'ip_modificacion' => 'CLI',
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                        'prioridad' => 1,
                        'compras' => 0,
                    ]);
                }

                DB::table('usuario_empresa')->insert([
                    'id_empresa' => $companyId,
                    'id_usuario' => $administratorId,
                    'id_usuariocrea' => $administratorId,
                    'id_usuariomod' => $administratorId,
                    'ip_creacion' => 'CLI',
                    'ip_modificacion' => 'CLI',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->error('No se pudo crear el administrador. Revise storage/logs/laravel.log.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->success('Superadministrador creado correctamente.');
        $this->line('Correo de acceso: '.$email);
        $this->line('Empresa asociada: '.$companyId);

        return self::SUCCESS;
    }
}
