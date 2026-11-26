<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AccesoController
{
    public function index(Request $request): View
    {
        $id = $request->integer('editar');

        return view('configuracion::roles', [
            'roles' => DB::table('roles')->orderBy('nombre')->get(),
            'rol' => $id ? (DB::table('roles')->find($id) ?? abort(404)) : null,
            'seleccion' => $id ? DB::table('permiso_rol')->where('id_rol', $id)->pluck('id_permiso')->map(fn ($id) => (int) $id)->all() : [],
            'opciones' => $this->opciones(),
        ]);
    }

    public function guardarRol(Request $request, ?int $rol = null): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique('roles', 'nombre')->ignore($rol)],
            'permisos' => ['sometimes', 'array'],
            'permisos.*' => ['integer', 'distinct', 'exists:permisos,id'],
        ]);

        try {
            DB::transaction(function () use ($rol, $datos): void {
                if ($rol === null) {
                    $id = DB::table('roles')->insertGetId(['nombre' => $datos['nombre']]);
                } else {
                    abort_unless(DB::table('roles')->lockForUpdate()->find($rol), 404);
                    DB::table('roles')->where('id', $rol)->update(['nombre' => $datos['nombre']]);
                    $id = $rol;
                }

                DB::table('permiso_rol')->where('id_rol', $id)->delete();
                foreach (array_chunk($datos['permisos'] ?? [], 200) as $grupo) {
                    DB::table('permiso_rol')->insert(array_map(fn ($permiso) => ['id_rol' => $id, 'id_permiso' => $permiso], $grupo));
                }
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['nombre' => 'Ya existe un rol con ese nombre.']);
        }

        return $this->resultado($request, 'Rol guardado.', 'configuracion.roles.index');
    }

    public function eliminarRol(Request $request, int $rol): JsonResponse|RedirectResponse
    {
        abort_unless(DB::table('roles')->where('id', $rol)->delete(), 404);

        return $this->resultado($request, 'Rol eliminado.', 'configuracion.roles.index');
    }

    public function permisosUsuario(User $usuario): View
    {
        abort_if($usuario->is_admin, 403);

        return view('configuracion::permisos-usuario', [
            'usuario' => $usuario,
            'roles' => DB::table('roles')->orderBy('nombre')->get(),
            'asignados' => DB::table('rol_usuario')->where('id_usuario', $usuario->id)->pluck('id_rol')->map(fn ($id) => (int) $id)->all(),
            'directos' => DB::table('permiso_usuario')->where('id_usuario', $usuario->id)->pluck('permitido', 'id_permiso')->all(),
            'opciones' => $this->opciones(),
        ]);
    }

    public function guardarUsuario(Request $request, User $usuario): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['integer', 'distinct', 'exists:roles,id'],
            'directos' => ['sometimes', 'array'],
            'directos.*' => ['nullable', 'in:0,1'],
        ]);
        $directos = array_filter($datos['directos'] ?? [], fn ($valor) => $valor !== null && $valor !== '');
        $ids = DB::table('permisos')->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (array_diff(array_keys($directos), $ids) !== []) {
            throw ValidationException::withMessages(['directos' => 'Hay permisos que ya no existen.']);
        }

        DB::transaction(function () use ($usuario, $datos, $directos): void {
            $cuenta = User::query()->lockForUpdate()->findOrFail($usuario->id);
            abort_if($cuenta->is_admin, 403);

            DB::table('rol_usuario')->where('id_usuario', $cuenta->id)->delete();
            foreach (array_chunk($datos['roles'] ?? [], 200) as $grupo) {
                DB::table('rol_usuario')->insert(array_map(fn ($rol) => ['id_usuario' => $cuenta->id, 'id_rol' => $rol], $grupo));
            }
            DB::table('permiso_usuario')->where('id_usuario', $cuenta->id)->delete();
            foreach (array_chunk($directos, 200, true) as $grupo) {
                $filas = [];
                foreach ($grupo as $permiso => $permitido) {
                    $filas[] = ['id_usuario' => $cuenta->id, 'id_permiso' => $permiso, 'permitido' => (bool) $permitido];
                }
                DB::table('permiso_usuario')->insert($filas);
            }
        });

        return $this->resultado($request, 'Permisos actualizados.', 'configuracion.usuarios.index');
    }

    private function opciones(): array
    {
        $permisos = DB::table('permisos')->get()->groupBy('opcion');
        $opciones = [];
        foreach (config('accesos') as $codigo => $nombre) {
            $opciones[$nombre] = $permisos->get($codigo, collect())->keyBy('accion');
        }

        return $opciones;
    }

    private function resultado(Request $request, string $mensaje, string $ruta): JsonResponse|RedirectResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $mensaje])
            : to_route($ruta)->with('estado', $mensaje);
    }
}