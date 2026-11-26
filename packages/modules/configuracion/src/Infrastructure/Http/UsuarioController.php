<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class UsuarioController
{
    public function index(): View
    {
        return view('configuracion::usuarios', [
            'usuarios' => User::query()->orderBy('name')->orderBy('id')->paginate(20),
        ]);
    }

    public function edit(User $usuario): View
    {
        return view('configuracion::usuario', ['usuario' => $usuario]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
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

        return to_route('configuracion.usuarios.index')->with('estado', 'Usuario actualizado.');
    }
}