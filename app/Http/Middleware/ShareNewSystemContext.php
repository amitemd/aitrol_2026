<?php

namespace App\Http\Middleware;

use App\Services\NewSystem\CompanyAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Sis_medico\User;
use Symfony\Component\HttpFoundation\Response;

class ShareNewSystemContext
{
    public function __construct(private CompanyAccess $companyAccess) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        $companies = $this->companyAccess->accessibleFor($user);
        $currentCompany = $companies->firstWhere('id', session('id_empresa'));

        if ($currentCompany === null && $companies->isNotEmpty()) {
            $currentCompany = $companies->first();
            session(['id_empresa' => $currentCompany->id]);
        } elseif ($currentCompany === null) {
            session()->forget('id_empresa');
        }

        View::share([
            'newSystemUser' => $user,
            'newSystemCompanies' => $companies,
            'newSystemCurrentCompany' => $currentCompany,
        ]);

        return $next($request);
    }
}
