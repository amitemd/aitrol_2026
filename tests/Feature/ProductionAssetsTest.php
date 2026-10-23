<?php

namespace Tests\Feature;

use JsonException;
use Tests\TestCase;

class ProductionAssetsTest extends TestCase
{
    /** @throws JsonException */
    public function test_production_manifest_contains_every_module_catalog_entrypoint(): void
    {
        $manifestPath = public_path('build/manifest.json');

        $this->assertFileExists($manifestPath);

        $manifest = json_decode(
            file_get_contents($manifestPath),
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );

        $this->assertArrayHasKey('resources/css/app.css', $manifest);
        $this->assertArrayHasKey('resources/js/app.js', $manifest);
        $this->assertTrue($manifest['resources/css/app.css']['isEntry']);
        $this->assertTrue($manifest['resources/js/app.js']['isEntry']);
    }
}
