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

    public function test_direct_git_installer_uses_acl_without_sudo_or_unsafe_permissions(): void
    {
        $script = file_get_contents(base_path('install-git-direct.sh'));

        $this->assertIsString($script);
        $this->assertStringContainsString('setfacl', $script);
        $this->assertStringContainsString('git -C "$PROJECT_PATH" ls-files -z', $script);
        $this->assertStringContainsString('u:$WEB_USER:rwX', $script);
        $this->assertStringContainsString('u:$WEB_USER:r--', $script);
        $this->assertStringNotContainsString('sudo -', $script);
        $this->assertStringNotContainsString('visudo', $script);
        $this->assertStringNotContainsString('NOPASSWD', $script);
        $this->assertStringNotContainsString('chmod 777', $script);
    }
}
