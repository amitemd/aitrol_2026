<?php

namespace App\Http\Controllers;

use App\Modules\Shared\Application\ContextMap;
use Illuminate\Contracts\View\View;

class ModuleCatalogController extends Controller
{
    public function __invoke(ContextMap $contextMap): View
    {
        $modules = collect(config('modules.items'))->map(function (array $module) use ($contextMap): array {
            $context = $contextMap->contextForLegacyModule($module['name']);

            return [
                ...$module,
                'context' => $context['name'] ?? 'Sin contexto',
                'context_key' => $context['key'] ?? null,
                'migration_status' => $context['status'] ?? 'unmapped',
            ];
        });

        return view('modules.index', [
            'modules' => $modules,
            'categories' => $modules->pluck('category')->unique()->values(),
            'stats' => [
                'modules' => $modules->count(),
                'routes' => app('router')->getRoutes()->count(),
                'views' => 3199,
                'legacyFiles' => 1613,
            ],
        ]);
    }
}
