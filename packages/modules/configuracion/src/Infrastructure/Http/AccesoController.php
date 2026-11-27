<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use App\Models\User;
use App\Services\EmpresaActiva;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AccesoController
{
    public function __construct(private readonly EmpresaActiva $empresaActiva) {}

    private function deEmpresa(string $tabla): Builder
    {
        return DB::table($tabla)->where($tabla.'.id_empresa', $this->empresaActiva->id());
    }

    public function index(Request $request): View
    {
        $id = $request->integer('editar');

        return view('configuracion::roles', [
            'roles' => $this->deEmpresa('roles')
                ->leftJoin('users as creador', 'creador.id', '=', 'roles.id_usuario_creacion')
                ->leftJoin('users as editor', 'editor.id', '=', 'roles.id_usuario_modificacion')
                ->orderBy('roles.nombre')
                ->get(['roles.*', 'creador.name as creado_por', 'editor.name as modificado_por']),
            'rol' => $id ? ($this->deEmpresa('roles')->find($id) ?? abort(404)) : null,
            'seleccion' => $id ? $this->deEmpresa('permiso_rol')->where('id_rol', $id)->pluck('id_permiso')->map(fn ($id) => (int) $id)->all() : [],
            'opciones' => $this->opciones(),
        ]);
    }

    public function guardarRol(Request $request, ?int $rol = null): JsonResponse|RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique('roles', 'nombre')->where('id_empresa', $this->empresaActiva->id())->ignore($rol)],
            'permisos' => ['sometimes', 'array'],
            'permisos.*' => ['integer', 'distinct', Rule::exists('permisos', 'id')->where('id_empresa', $this->empresaActiva->id())],
        ]);

        $autor = $request->user()->id;
        $permisos = array_map('intval', $datos['permisos'] ?? []);
        sort($permisos);

        try {
            DB::transaction(function () use ($rol, $datos, $autor, $permisos): void {
                if ($rol === null) {
                    $id = DB::table('roles')->insertGetId([
                        'nombre' => $datos['nombre'],
                        'id_empresa' => $this->empresaActiva->id(),
                        'id_usuario_creacion' => $autor,
                        'created_at' => now(),
                    ]);
                } else {
                    $actual = $this->deEmpresa('roles')->lockForUpdate()->find($rol) ?? abort(404);
                    $asignados = $this->deEmpresa('permiso_rol')->where('id_rol', $rol)
                        ->orderBy('id_permiso')->pluck('id_permiso')->map(fn ($id) => (int) $id)->all();
                    if ($actual->nombre === $datos['nombre'] && $asignados === $permisos) {
                        return;
                    }

                    $this->deEmpresa('roles')->where('id', $rol)->update([
                        'nombre' => $datos['nombre'],
                        'id_usuario_modificacion' => $autor,
                        'updated_at' => now(),
                    ]);
                    $id = $rol;
                }

                $this->deEmpresa('permiso_rol')->where('id_rol', $id)->delete();
                foreach (array_chunk($permisos, 200) as $grupo) {
                    DB::table('permiso_rol')->insert(array_map(fn ($permiso) => ['id_empresa' => $this->empresaActiva->id(), 'id_rol' => $id, 'id_permiso' => $permiso], $grupo));
                }
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['nombre' => 'Ya existe un rol con ese nombre.']);
        }

        return $this->resultado($request, 'Rol guardado.', 'configuracion.roles.index');
    }

    public function eliminarRol(Request $request, int $rol): JsonResponse|RedirectResponse
    {
        abort_unless($this->deEmpresa('roles')->where('id', $rol)->delete(), 404);

        return $this->resultado($request, 'Rol eliminado.', 'configuracion.roles.index');
    }

    public function permisosUsuario(User $usuario): View
    {
        $this->empresaActiva->verificar($usuario);
        abort_if($usuario->is_admin, 403);

        return view('configuracion::permisos-usuario', [
            'usuario' => $usuario,
            'roles' => $this->deEmpresa('roles')->orderBy('nombre')->get(),
            'asignados' => $this->deEmpresa('rol_usuario')->where('id_usuario', $usuario->id)->pluck('id_rol')->map(fn ($id) => (int) $id)->all(),
            'directos' => $this->deEmpresa('permiso_usuario')->where('id_usuario', $usuario->id)->pluck('permitido', 'id_permiso')->all(),
            'opciones' => $this->opciones(),
        ]);
    }

    public function guardarUsuario(Request $request, User $usuario): JsonResponse|RedirectResponse
    {
        $this->empresaActiva->verificar($usuario);
        $datos = $request->validate([
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['integer', 'distinct', Rule::exists('roles', 'id')->where('id_empresa', $this->empresaActiva->id())],
            'directos' => ['sometimes', 'array'],
            'directos.*' => ['nullable', 'in:0,1'],
        ]);
        $directos = array_filter($datos['directos'] ?? [], fn ($valor) => $valor !== null && $valor !== '');
        $ids = $this->deEmpresa('permisos')->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (array_diff(array_keys($directos), $ids) !== []) {
            throw ValidationException::withMessages(['directos' => 'Hay permisos que ya no existen.']);
        }

        DB::transaction(function () use ($usuario, $datos, $directos): void {
            $cuenta = User::query()->where('id_empresa', $this->empresaActiva->id())->lockForUpdate()->findOrFail($usuario->id);
            abort_if($cuenta->is_admin, 403);

            $this->deEmpresa('rol_usuario')->where('id_usuario', $cuenta->id)->delete();
            foreach (array_chunk($datos['roles'] ?? [], 200) as $grupo) {
                DB::table('rol_usuario')->insert(array_map(fn ($rol) => ['id_empresa' => $this->empresaActiva->id(), 'id_usuario' => $cuenta->id, 'id_rol' => $rol], $grupo));
            }
            $this->deEmpresa('permiso_usuario')->where('id_usuario', $cuenta->id)->delete();
            foreach (array_chunk($directos, 200, true) as $grupo) {
                $filas = [];
                foreach ($grupo as $permiso => $permitido) {
                    $filas[] = ['id_empresa' => $this->empresaActiva->id(), 'id_usuario' => $cuenta->id, 'id_permiso' => $permiso, 'permitido' => (bool) $permitido];
                }
                DB::table('permiso_usuario')->insert($filas);
            }
        });

        return $this->resultado($request, 'Permisos actualizados.', 'configuracion.usuarios.index');
    }

    private function opciones(): array
    {
        $permisos = $this->deEmpresa('permisos')->get()->groupBy('opcion');
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
