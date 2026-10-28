<?php

namespace App\Http\Controllers\Observability;

use App\Http\Controllers\Controller;
use App\Modules\SupportIntegrations\Application\Data\SystemLogFilters;
use App\Modules\SupportIntegrations\Application\Queries\BuildSystemObservabilityReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
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
        $filters = $this->filters($stored);
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

    public function filter(Request $request, BuildSystemObservabilityReport $query): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'file' => ['nullable', 'string', 'max:255', 'regex:/\A[A-Za-z0-9._-]+\.log\z/'],
            'levels' => ['nullable', 'array', 'max:8'],
            'levels.*' => ['string', Rule::in(self::LEVELS)],
            'search' => ['nullable', 'string', 'max:200'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'per_page' => ['required', 'integer', Rule::in([10, 25, 50, 100])],
        ]);

        $stored = [
            'file' => $this->nullableString($validated['file'] ?? null),
            'levels' => array_values(array_unique($validated['levels'] ?? [])),
            'search' => $this->nullableString($validated['search'] ?? null),
            'date_from' => $this->nullableString($validated['date_from'] ?? null),
            'date_to' => $this->nullableString($validated['date_to'] ?? null),
            'per_page' => (int) $validated['per_page'],
        ];

        $request->session()->put('observability.logs.filters', $stored);

        if ($request->expectsJson()) {
            $filters = $this->filters($stored);
            $report = $query->handle($filters, 1, $this->perPage($stored['per_page']));

            return response()->json([
                'flame_graph' => $report->logs->activityMap->flameGraph,
                'audit_by_process' => $report->auditByProcess,
                'filters' => [
                    'date_from' => $filters->dateFrom,
                    'date_to' => $filters->dateTo,
                ],
            ]);
        }

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

    /**
     * @param  array<string, mixed>  $stored
     */
    private function filters(array $stored): SystemLogFilters
    {
        $today = now(config('app.timezone'))->toDateString();

        return new SystemLogFilters(
            file: $this->nullableString($stored['file'] ?? null),
            levels: array_values(array_intersect(self::LEVELS, (array) ($stored['levels'] ?? []))),
            search: $this->nullableString($stored['search'] ?? null),
            dateFrom: array_key_exists('date_from', $stored)
                ? $this->nullableString($stored['date_from'])
                : $today,
            dateTo: array_key_exists('date_to', $stored)
                ? $this->nullableString($stored['date_to'])
                : $today,
        );
    }

    private function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
