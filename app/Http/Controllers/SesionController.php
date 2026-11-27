<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class SesionController extends Controller
{
    public function inicio(): View
    {
        $usuario = Auth::user();
        if ($usuario === null) return view('auth.login');

        return view($usuario->empresa()->exists() ? 'adminlte.dashboard-v2' : 'auth.empresa-inicial');
    }

    public function store(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'string', 'email:filter'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (! Auth::attempt($credenciales)) {
            throw ValidationException::withMessages(['email' => 'Las credenciales son incorrectas.'])
                ->redirectTo(route('dashboard'));
        }

        $request->session()->regenerate();

        if (! $request->user()->empresa()->exists()) return to_route('dashboard');

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('dashboard');
    }
}
