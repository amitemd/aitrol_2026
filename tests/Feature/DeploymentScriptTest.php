<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeploymentScriptTest extends TestCase
{
    public function test_linux_deployment_tests_and_builds_the_release_by_default(): void
    {
        $script = file_get_contents(base_path('deploy.sh'));

        $this->assertIsString($script);
        $this->assertMatchesRegularExpression('/^RUN_TESTS=1$/m', $script);
        $this->assertStringContainsString('npm run build', $script);
        $this->assertStringContainsString('public/build/manifest.json', $script);
        $this->assertStringContainsString('artisan test --compact', $script);
        $this->assertSame(2, substr_count($script, 'artisan up'));
    }

    public function test_windows_deployment_tests_and_builds_the_release_by_default(): void
    {
        $script = file_get_contents(base_path('deploy.ps1'));

        $this->assertIsString($script);
        $this->assertStringContainsString('$shouldRunTests = -not $SkipTests', $script);
        $this->assertStringContainsString("@('run', 'build')", $script);
        $this->assertStringContainsString('public\\build\\manifest.json', $script);
        $this->assertStringContainsString("@('artisan', 'test', '--compact')", $script);
        $this->assertSame(2, substr_count($script, "@('artisan', 'up')"));
    }
}
