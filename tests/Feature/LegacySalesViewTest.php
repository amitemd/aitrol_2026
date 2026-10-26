<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LegacySalesViewTest extends TestCase
{
    public function test_existing_pdf_link_uses_the_sale_being_rendered(): void
    {
        $template = file_get_contents(resource_path('views/contable/ventas/index.blade.php'));

        $this->assertIsString($template);
        $this->assertTrue(Route::has('pdf.visualizar'));
        $this->assertStringNotContainsString('$ow->id', $template);
        $this->assertStringNotContainsString("route('pdf.vidualizar'", $template);
        $this->assertStringContainsString("route('pdf.visualizar', ['id' => \$row->id])", $template);
    }

    public function test_sales_view_only_references_registered_named_routes(): void
    {
        $template = file_get_contents(resource_path('views/contable/ventas/index.blade.php'));
        $this->assertIsString($template);

        preg_match_all("/route\\(['\"]([^'\"]+)['\"]/", $template, $matches);
        $routeNames = array_values(array_unique($matches[1]));
        $missingRoutes = array_values(array_filter(
            $routeNames,
            fn (string $routeName): bool => ! Route::has($routeName),
        ));

        $this->assertNotEmpty($routeNames);
        $this->assertSame([], $missingRoutes, 'La vista de Ventas referencia rutas que no existen.');
    }
}
