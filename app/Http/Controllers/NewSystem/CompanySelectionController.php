<?php

namespace App\Http\Controllers\NewSystem;

use App\Http\Controllers\Controller;
use App\Services\NewSystem\CompanyAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Sis_medico\User;

class CompanySelectionController extends Controller
{
    public function index(): View
    {
        return view('new-system.companies.index', [
            'title' => __('new_system.companies.title'),
        ]);
    }

    public function store(Request $request, CompanyAccess $companyAccess): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'string', 'max:13'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $company = $companyAccess->accessibleFor($user)->firstWhere('id', $validated['company_id']);

        abort_if($company === null, 403, __('new_system.companies.access_denied'));

        $request->session()->put('id_empresa', $company->id);

        return redirect()
            ->route('new-system.dashboard')
            ->with('status', __('new_system.companies.updated'));
    }
}
