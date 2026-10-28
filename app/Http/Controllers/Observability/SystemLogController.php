<?php

namespace App\Http\Controllers\Observability;

use App\Http\Controllers\Controller;
use App\Modules\SupportIntegrations\Application\Data\SystemLogFilters;
use App\Modules\SupportIntegrations\Application\Queries\BuildSystemObservabilityReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class SystemLogController extends Controller
{
    private const LEVELS = [
        'EMERGENCY',
        'ALERT',
        'CRITICAL',
        'ERROR',
        'WARNING',
        'NOTICE',
        'INFO',
        'DEBUG',
    ];

    public function index(Request $request, BuildSystemObservabilityReport $query): View
    {
        $stored = (array) $request->session()->get('observability.logs.filters', []);
        $filters = new SystemLogFilters(
            file: $this->nullableString($stored['file'] ?? null),
            levels: array_values(array_intersect(self::LEVELS, (array) ($stored['levels'] ?? []))),
            search: $this->nullableString($stored['search'] ?? null),
        );
        $perPage = $this->perPage($stored['per_page'] ?? config('observability.per_page', 25));
        $page = max(1, $request->integer('log_page', 1));

        $report = $query->handle($filters, $page, $perPage);

        return view('observability.logs', [
            'filters' => $filters,
            'levels' => self::LEVELS,
            'perPage' => $perPage,
            'result' => $report->logs,
            'auditByProcess' => $report->auditByProcess,
        ]);
    }

    public function filter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['nullable', 'string', 'max:255', 'regex:/\A[A-Za-z0-9._-]+\.log\z/'],
            'levels' => ['nullable', 'array', 'max:8'],
            'levels.*' => ['string', Rule::in(self::LEVELS)],
            'search' => ['nullable', 'string', 'max:200'],
            'per_page' => ['required', 'integer', Rule::in([10, 25, 50, 100])],
        ]);

        $request->session()->put('observability.logs.filters', [
            'file' => $this->nullableString($validated['file'] ?? null),
            'levels' => array_values(array_unique($validated['levels'] ?? [])),
            'search' => $this->nullableString($validated['search'] ?? null),
            'per_page' => (int) $validated['per_page'],
        ]);

        return redirect()->route('observability.logs.index');
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->session()->forget('observability.logs.filters');

        return redirect()->route('observability.logs.index');
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
