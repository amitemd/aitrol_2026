<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModuleCatalogTest extends TestCase
{
    public function test_module_catalog_describes_the_migrated_system(): void
    {
        $response = $this->get(route('modules.index'));

        $response
            ->assertOk()
            ->assertSee('El sistema completo')
            ->assertSee('Historia clínica y admisión')
            ->assertSee('Contabilidad')
            ->assertSee('Laboratorio')
            ->assertSee('Sensores');
    }
}
