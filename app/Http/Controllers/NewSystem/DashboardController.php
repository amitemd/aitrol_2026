<?php

namespace App\Http\Controllers\NewSystem;

use App\Http\Controllers\Controller;
use App\Services\NewSystem\CompanyAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Empresa;
use Sis_medico\User;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CompanyAccess $companyAccess): View
    {
        $user = $request->user();

        if (! $user instanceof User) {
            $lockedUntil = (int) $request->session()->get('new_system.login_locked_until', 0);
            $loginLocked = $lockedUntil > now()->getTimestamp();

            if (! $loginLocked && $lockedUntil !== 0) {
                $request->session()->forget('new_system.login_locked_until');
            }

            return view('new-system.auth.login', [
                'loginLocked' => $loginLocked,
            ]);
        }

        $currentCompany = $companyAccess->currentFor($user);

        return view('new-system.dashboard', [
            'title' => 'Inicio',
            'stats' => [
                'activeUsers' => User::query()->where('estado', 1)->count(),
                'companies' => Empresa::query()->where('estado', 1)->count(),
                'companyUsers' => $currentCompany === null
                    ? 0
                    : DB::table('usuario_empresa')->where('id_empresa', $currentCompany->id)->distinct('id_usuario')->count('id_usuario'),
                'legacyRoutes' => app('router')->getRoutes()->count(),
            ],
        ]);
    }
}
