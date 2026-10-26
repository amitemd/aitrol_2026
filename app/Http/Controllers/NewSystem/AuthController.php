<?php

namespace App\Http\Controllers\NewSystem;

use App\Http\Controllers\Controller;
use App\Services\NewSystem\CompanyAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Sis_medico\User;

class AuthController extends Controller
{
    public function login(Request $request, CompanyAccess $companyAccess): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:191'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (! Auth::attempt([
            'email' => Str::lower(trim($credentials['email'])),
            'password' => $credentials['password'],
            'estado' => 1,
        ], (bool) ($credentials['remember'] ?? false))) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales proporcionadas no son válidas o el usuario está inactivo.',
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();
        $companies = $companyAccess->accessibleFor($user);

        if ($companies->count() === 1) {
            $request->session()->put('id_empresa', $companies->first()->id);
        } else {
            $request->session()->forget('id_empresa');
        }

        return redirect()->route('new-system.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('new-system.dashboard');
    }
}
