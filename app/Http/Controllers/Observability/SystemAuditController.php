<?php

namespace App\Http\Controllers\Observability;

use App\Http\Controllers\Controller;
use App\Modules\SupportIntegrations\Application\Contracts\AuditTrailRepository;
use App\Modules\SupportIntegrations\Application\Data\AuditFilters;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class SystemAuditController extends Controller
{
    public function index(Request $request, AuditTrailRepository $repository): View
    {
        $stored = (array) $request->session()->get('observability.audit.filters', []);
        $filters = new AuditFilters(
            search: $this->nullableString($stored['search'] ?? null),
            userId: $this->nullableString($stored['user_id'] ?? null),
            companyId: $this->nullableString($stored['company_id'] ?? null),
            fromDate: $this->nullableString($stored['from_date'] ?? null),
            toDate: $this->nullableString($stored['to_date'] ?? null),
        );
        $perPage = $this->perPage($stored['per_page'] ?? config('observability.per_page', 25));

        return view('observability.audit', [
            'filters' => $filters,
            'perPage' => $perPage,
            'result' => $repository->read(
                $filters,
                max(1, $request->integer('activity_page', 1)),
                max(1, $request->integer('technical_page', 1)),
                $perPage,
            ),
        ]);
    }

    public function filter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'user_id' => ['nullable', 'string', 'max:30'],
            'company_id' => ['nullable', 'string', 'max:30'],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'per_page' => ['required', 'integer', Rule::in([10, 25, 50, 100])],
        ]);

        $request->session()->put('observability.audit.filters', [
            'search' => $this->nullableString($validated['search'] ?? null),
            'user_id' => $this->nullableString($validated['user_id'] ?? null),
            'company_id' => $this->nullableString($validated['company_id'] ?? null),
            'from_date' => $this->nullableString($validated['from_date'] ?? null),
            'to_date' => $this->nullableString($validated['to_date'] ?? null),
            'per_page' => (int) $validated['per_page'],
        ]);

        return redirect()->route('observability.audit.index');
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->session()->forget('observability.audit.filters');

        return redirect()->route('observability.audit.index');
    }

    private function perPage(mixed $value): int
    {
        $perPage = (int) $value;

        return in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
    }

    private function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
