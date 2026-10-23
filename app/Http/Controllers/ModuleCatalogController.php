<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ModuleCatalogController extends Controller
{
    public function __invoke(): View
    {
        $modules = collect(config('modules.items'));

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
