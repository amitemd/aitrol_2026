<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class UsuarioController
{
    public function index(Request $request): View|JsonResponse
    {
        $datos = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'page' => ['sometimes', 'integer', 'min:1', 'max:1000000']]);
        $consulta = User::query()->select(['id', 'id_persona', 'name', 'email', 'is_admin'])->with('persona');
        $busqueda = $datos['q'] ?? '';
        if ($busqueda !== '') {
            $consulta->where(fn ($query) => $query->whereLike('name', '%'.$busqueda.'%')->orWhereLike('email', '%'.$busqueda.'%'));
        }
        $usuarios = $consulta->orderBy('name')->orderBy('id')->paginate(20)->appends(['q' => $busqueda]);

        if ($request->routeIs('api.*')) {
            return UsuarioResource::collection($usuarios)->response()->header('Cache-Control', 'no-store');
        }

        return view('configuracion::usuarios', ['usuarios' => $usuarios]);
    }

    public function edit(User $usuario): View
    {
        return view('configuracion::usuario', ['usuario' => $usuario->load('persona'), 'tipos' => TipoIdentificacion::cases()]);
    }

    public function show(User $usuario): JsonResponse
    {
        return (new UsuarioResource($usuario))->response()->header('Cache-Control', 'no-store');
    }

    public function update(Request $request, User $usuario): RedirectResponse|JsonResponse
    {
        abort_if($request->routeIs('api.*') && $usuario->is_admin, 403, 'Edita las cuentas administradoras desde Configuraciones generales.');
        if (is_string($request->input('email'))) {
            $request->merge(['email' => Str::lower($request->input('email'))]);
        }

        $datos = $request->validate([
            'name' => ['required_without:nombres', 'nullable', 'string', 'max:255'],
            'nombres' => ['sometimes', 'required', 'string', 'max:255'],
            'apellidos' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email:filter', 'max:255', Rule::unique(User::class)->ignore($usuario)],
            'email_contacto' => ['nullable', 'email:filter', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            ...Persona::reglasPerfil(),
            'tipo_identificacion' => ['exclude_if:identificacion,null', 'required_with:identificacion', 'nullable', Rule::enum(TipoIdentificacion::class)],
            'identificacion' => ['nullable', 'string', 'max:30'],
        ]);

        $identificacion = null;
        if (! $request->routeIs('api.*') && isset($datos['identificacion'])) {
            try {
                $identificacion = new IdentificacionFiscal(TipoIdentificacion::from($datos['tipo_identificacion']), $datos['identificacion']);
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages(['identificacion' => $exception->getMessage()]);
            }
        }

        $nombre = isset($datos['nombres']) ? trim($datos['nombres'].' '.($datos['apellidos'] ?? '')) : $datos['name'];
        if (mb_strlen($nombre) > 255) {
            throw ValidationException::withMessages(['nombres' => 'El nombre completo debe tener hasta 255 caracteres.']);
        }

        try {
            $usuario = $usuario->getConnection()->transaction(function () use ($usuario, $datos, $identificacion, $nombre): User {
                $cuenta = User::query()->lockForUpdate()->findOrFail($usuario->id);
                $persona = $cuenta->persona()->lockForUpdate()->first();
                $perfilAnterior = $persona;

                if ($identificacion !== null) {
                    $documento = ['id_tipo_identificacion' => $identificacion->tipo->idCatalogo(), 'identificacion' => $identificacion->numero];
                    if ($persona?->identificacion !== null && ($persona->identificacion !== $documento['identificacion'] || (int) $persona->id_tipo_identificacion !== $documento['id_tipo_identificacion'])) {
                        throw ValidationException::withMessages(['identificacion' => 'La cuenta ya está vinculada a una identificación.']);
                    }
                    $personaEncontrada = Persona::query()->where($documento)->lockForUpdate()->first();
                    if ($personaEncontrada !== null && User::query()->where('id_persona', $personaEncontrada->id)->whereKeyNot($cuenta->id)->exists()) {
                        throw ValidationException::withMessages(['identificacion' => 'Esta persona ya tiene una cuenta de usuario.']);
                    }
                    $persona = $personaEncontrada ?? ($persona ?? new Persona(['nombre' => $nombre]))->fill($documento);
                }

                $persona ??= new Persona(['nombre' => $nombre, 'email' => $datos['email']]);
                $vinculaPersonaExistente = $persona->exists && $persona->id !== $perfilAnterior?->id;
                if (isset($datos['nombres']) && (! $vinculaPersonaExistente
                    || $datos['nombres'] !== $perfilAnterior?->nombre || ($datos['apellidos'] ?? null) !== $perfilAnterior?->apellido)) {
                    $persona->nombre = $datos['nombres'];
                    $persona->apellido = $datos['apellidos'] ?? null;
                } elseif (! $vinculaPersonaExistente && $cuenta->name !== $nombre) {
                    $persona->nombre = $nombre;
                    $persona->apellido = null;
                }
                $extras = array_keys(Persona::reglasPerfil());
                $camposPerfil = ['email_contacto' => 'email', 'telefono' => 'telefono', 'direccion' => 'direccion'] + array_combine($extras, $extras);
                $perfilAnteriorDatos = $perfilAnterior?->perfil() ?? [];
                foreach ($camposPerfil as $campo => $columna) {
                    $anterior = $perfilAnteriorDatos[$columna] ?? $perfilAnterior?->{$columna};
                    if (array_key_exists($campo, $datos) && (! $vinculaPersonaExistente || $datos[$campo] !== $anterior)) {
                        $persona->{$columna} = $datos[$campo];
                    }
                }
                $persona->save();

                $cuenta->persona()->associate($persona);
                $cuenta->fill(['name' => $nombre, 'email' => $datos['email']]);
                if ($cuenta->isDirty('email')) {
                    $cuenta->email_verified_at = null;
                    $cuenta->setRememberToken(Str::random(60));
                }
                $cuenta->save();

                return $cuenta;
            });
        } catch (UniqueConstraintViolationException $exception) {
            $restriccion = (string) ($exception->errorInfo[2] ?? '');
            if ($identificacion !== null && str_contains($restriccion, 'id_persona')) {
                throw ValidationException::withMessages(['identificacion' => 'Esta persona ya tiene una cuenta de usuario.']);
            }
            if ($identificacion !== null && str_contains($restriccion, 'personas')) {
                throw ValidationException::withMessages(['identificacion' => 'Ya existe una persona con esa identificación. Vuelve a guardar para vincularla.']);
            }

            throw ValidationException::withMessages(['email' => 'Ya existe una cuenta con ese correo.']);
        }

        if ($request->expectsJson() || $request->routeIs('api.*')) {
            return (new UsuarioResource($usuario))->additional(['message' => 'Usuario actualizado.'])
                ->response()->header('Cache-Control', 'no-store');
        }

        return to_route('configuracion.usuarios.index')->with('estado', 'Usuario actualizado.');
    }
}
