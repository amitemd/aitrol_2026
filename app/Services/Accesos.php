<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class Accesos
{
    private array $cargados = [];

    public function permite(User $usuario, string $opcion, string $accion): bool
    {
        if ($usuario->id_empresa === null) return false;

        if ($usuario->is_admin) {
            return true;
        }

        [$directos, $roles] = $this->cargados[$usuario->id] ??= $this->cargar($usuario);
        foreach ([$directos, $roles] as $reglas) {
            $nivel = $opcion;
            do {
                if (isset($reglas[$nivel][$accion])) {
                    return (bool) $reglas[$nivel][$accion];
                }

                $ultimoPunto = strrpos($nivel, '.');
                $nivel = $ultimoPunto === false ? '' : substr($nivel, 0, $ultimoPunto);
            } while ($nivel !== '');
        }

        return false;
    }

    public function muestra(User $usuario, string $ruta): bool
    {
        return match ($ruta) {
            'dashboard' => true,
            'configuracion.mantenimiento.index' => $usuario->can('administrar-servidor'),
            'configuracion.personas.importaciones.index' => $usuario->can('administrar-personas'),
            'configuracion.menu.index', 'configuracion.ubicaciones.index' => $usuario->is_admin_global === true,
            'clientes.index' => $this->permite($usuario, 'clientes.lista', 'leer'),
            'clientes.create' => $this->permite($usuario, 'clientes.lista', 'escribir'),
            'proveedores.index' => $this->permite($usuario, 'proveedores.lista', 'leer'),
            'proveedores.create' => $this->permite($usuario, 'proveedores.lista', 'escribir'),
            'configuracion.empresas.index' => $this->permite($usuario, 'empresas', 'leer'),
            default => array_key_exists($ruta, config('accesos'))
                ? $this->permite($usuario, $ruta, 'leer')
                : $usuario->is_admin === true,
        };
    }

    private function cargar(User $usuario): array
    {
        $directos = [];
        foreach (DB::table('permiso_usuario')->join('permisos', 'permisos.id', '=', 'permiso_usuario.id_permiso')
            ->where('permiso_usuario.id_empresa', $usuario->id_empresa)->where('permisos.id_empresa', $usuario->id_empresa)
            ->where('id_usuario', $usuario->id)->get(['opcion', 'accion', 'permitido']) as $regla) {
            $directos[$regla->opcion][$regla->accion] = (bool) $regla->permitido;
        }

        $roles = [];
        foreach (DB::table('rol_usuario')->join('permiso_rol', 'permiso_rol.id_rol', '=', 'rol_usuario.id_rol')
            ->join('permisos', 'permisos.id', '=', 'permiso_rol.id_permiso')
            ->where('rol_usuario.id_empresa', $usuario->id_empresa)->where('permiso_rol.id_empresa', $usuario->id_empresa)
            ->where('permisos.id_empresa', $usuario->id_empresa)
            ->where('id_usuario', $usuario->id)->distinct()->get(['opcion', 'accion']) as $regla) {
            $roles[$regla->opcion][$regla->accion] = true;
        }

        return [$directos, $roles];
    }
}
