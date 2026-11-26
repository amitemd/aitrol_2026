<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class UsuarioController
{
    public function index(Request $request): View|JsonResponse
    {
        $datos = $request->validate(['q' => ['nullable', 'string', 'max:160'], 'page' => ['sometimes', 'integer', 'min:1', 'max:1000000']]);
        $consulta = User::query()->select(['id', 'name', 'email', 'is_admin']);
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
        return view('configuracion::usuario', ['usuario' => $usuario]);
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:filter', 'max:255', Rule::unique(User::class)->ignore($usuario)],
        ]);

        $usuario->fill($datos);
        if ($usuario->isDirty('email')) {
            $usuario->email_verified_at = null;
            $usuario->setRememberToken(Str::random(60));
        }

        try {
            $usuario->save();
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => 'Ya existe una cuenta con ese correo.']);
        }

        if ($request->expectsJson() || $request->routeIs('api.*')) {
            return (new UsuarioResource($usuario))->additional(['message' => 'Usuario actualizado.'])
                ->response()->header('Cache-Control', 'no-store');
        }

        return to_route('configuracion.usuarios.index')->with('estado', 'Usuario actualizado.');
    }
}
