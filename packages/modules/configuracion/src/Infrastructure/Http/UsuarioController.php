<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use App\Models\Persona;
use App\Models\User;
use App\Services\EmpresaActiva;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class UsuarioController
{
    public function __construct(private readonly EmpresaActiva $empresaActiva) {}

    public function index(Request $request): View|JsonResponse
    {
        $datos = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'page' => ['sometimes', 'integer', 'min:1', 'max:1000000']]);
        $consulta = User::query()->where('id_empresa', $this->empresaActiva->id())
            ->select(['id', 'id_persona', 'name', 'email', 'is_admin'])->with('persona');
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
        $this->empresaActiva->verificar($usuario);

        return view('configuracion::usuario', ['usuario' => $usuario->load('persona'), 'tipos' => TipoIdentificacion::cases()]);
    }

    public function detalle(User $usuario): View
    {
        $this->empresaActiva->verificar($usuario);

        return view('partials.detalle-ficha-simple', ['titulo' => 'Detalle de usuario', 'volver' => route('configuracion.usuarios.index'), 'campos' => [
            'Nombre' => $usuario->name, 'Correo electrónico' => $usuario->email,
            'Tipo de cuenta' => $usuario->is_admin ? 'Administrador' : 'Usuario',
            'Identificación' => $usuario->persona?->identificacion, 'Teléfono' => $usuario->persona?->telefono,
        ]]);
    }

    public function create(): View
    {
        return view('configuracion::usuario', ['usuario' => new User, 'tipos' => TipoIdentificacion::cases()]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        return $this->guardar($request, new User);
    }

    public function show(User $usuario): JsonResponse
    {
        $this->empresaActiva->verificar($usuario);

        return (new UsuarioResource($usuario))->response()->header('Cache-Control', 'no-store');
    }

    public function foto(Request $request, User $usuario): StreamedResponse
    {
        $this->empresaActiva->verificar($usuario);
        abort_unless($request->user()->is_admin || $request->user()->is($usuario), 403);
        $disco = Storage::disk('local');
        abort_unless($usuario->foto && $disco->exists($usuario->foto), 404);

        return $disco->response($usuario->foto, headers: ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function update(Request $request, User $usuario): RedirectResponse|JsonResponse
    {
        $this->empresaActiva->verificar($usuario);
        abort_if($request->routeIs('api.*') && $usuario->is_admin, 403, 'Edita las cuentas administradoras desde Configuración.');

        return $this->guardar($request, $usuario);
    }

    private function guardar(Request $request, User $usuario): RedirectResponse|JsonResponse
    {
        $esNuevo = ! $usuario->exists;
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
            'foto' => [Rule::excludeIf($request->routeIs('api.*')), 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            ...Persona::reglasPerfil(),
            'tipo_identificacion' => ['exclude_if:identificacion,null', 'required_with:identificacion', 'nullable', Rule::enum(TipoIdentificacion::class)],
            'identificacion' => ['nullable', 'string', 'max:30'],
            ...($esNuevo ? ['password' => ['required', 'string', 'min:12', 'max:72', 'confirmed', function ($atributo, $valor, $fallar): void {
                if (is_string($valor) && (strlen($valor) > 72 || str_contains($valor, "\0"))) {
                    $fallar('La contraseña debe tener hasta 72 bytes y no admitir caracteres nulos.');
                }
            }]] : []),
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

        $fotoNueva = null;
        $fotoAnterior = null;
        $guardado = false;
        $disco = Storage::disk('local');
        try {
            $usuario = $this->empresaActiva->transaccion(function () use ($usuario, $datos, $identificacion, $nombre, $esNuevo, &$fotoNueva, &$fotoAnterior): User {
                $cuenta = $esNuevo ? $usuario : User::query()->lockForUpdate()->findOrFail($usuario->id);
                $persona = $cuenta->persona()->lockForUpdate()->first();
                $perfilAnterior = $persona;

                if ($identificacion !== null) {
                    $documento = ['id_tipo_identificacion' => $identificacion->tipo->idCatalogo(), 'identificacion' => $identificacion->numero];
                    if ($persona?->identificacion !== null && ($persona->identificacion !== $documento['identificacion'] || (int) $persona->id_tipo_identificacion !== $documento['id_tipo_identificacion'])) {
                        throw ValidationException::withMessages(['identificacion' => 'La cuenta ya está vinculada a una identificación.']);
                    }
                    $personaEncontrada = Persona::query()->where($documento)->lockForUpdate()->first();
                    if ($personaEncontrada !== null && User::query()->where('id_empresa', $this->empresaActiva->id())->where('id_persona', $personaEncontrada->id)
                        ->when(! $esNuevo, fn ($consulta) => $consulta->whereKeyNot($cuenta->id))->exists()) {
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
                if ($esNuevo) {
                    $cuenta->id_empresa = $this->empresaActiva->id();
                    $cuenta->password = $datos['password'];
                }
                if ($cuenta->isDirty('email')) {
                    $cuenta->email_verified_at = null;
                    $cuenta->setRememberToken(Str::random(60));
                }
                if (isset($datos['foto'])) {
                    $fotoAnterior = $cuenta->foto;
                    $fotoNueva = $datos['foto']->store('usuarios/fotos', 'local') ?: throw new RuntimeException('No se pudo guardar la foto.');
                    $cuenta->foto = $fotoNueva;
                }
                $cuenta->save();

                return $cuenta;
            }, intentos: 1);
            $guardado = true;
        } catch (UniqueConstraintViolationException $exception) {
            $restriccion = (string) ($exception->errorInfo[2] ?? '');
            if ($identificacion !== null && str_contains($restriccion, 'id_persona')) {
                throw ValidationException::withMessages(['identificacion' => 'Esta persona ya tiene una cuenta de usuario.']);
            }
            if ($identificacion !== null && str_contains($restriccion, 'personas')) {
                throw ValidationException::withMessages(['identificacion' => 'Ya existe una persona con esa identificación. Vuelve a guardar para vincularla.']);
            }

            throw ValidationException::withMessages(['email' => 'Ya existe una cuenta con ese correo.']);
        } finally {
            if (! $guardado && $fotoNueva !== null) $disco->delete($fotoNueva);
        }
        if ($fotoAnterior !== null) $disco->delete($fotoAnterior);

        $mensaje = $esNuevo ? 'Usuario creado.' : 'Usuario actualizado.';
        if ($request->expectsJson() || $request->routeIs('api.*')) {
            return (new UsuarioResource($usuario))->additional(['message' => $mensaje])
                ->response()->setStatusCode($esNuevo ? 201 : 200)->header('Cache-Control', 'no-store');
        }

        return to_route('configuracion.usuarios.index')->with('estado', $mensaje);
    }
}
