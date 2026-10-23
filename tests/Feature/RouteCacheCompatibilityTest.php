<?php

namespace Tests\Feature;

use Illuminate\Routing\Route;
use Tests\TestCase;

class RouteCacheCompatibilityTest extends TestCase
{
    public function test_every_named_route_has_a_unique_name_so_routes_can_be_cached(): void
    {
        $duplicates = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn (Route $route): bool => filled($route->getName()))
            ->groupBy(fn (Route $route): string => (string) $route->getName())
            ->filter(fn ($routes): bool => $routes->count() > 1)
            ->map(fn ($routes): array => $routes
                ->map(fn (Route $route): string => implode('|', $route->methods()).' '.$route->uri())
                ->all())
            ->all();

        $this->assertSame([], $duplicates, 'Hay nombres de rutas duplicados: '.json_encode($duplicates));
    }
}
