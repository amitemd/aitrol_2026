<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        if (! $app->environment('testing')) {
            throw new RuntimeException('Pruebas detenidas. Laravel arrancó fuera del entorno testing. Revisa la caché de configuración.');
        }

        return $app;
    }
}
