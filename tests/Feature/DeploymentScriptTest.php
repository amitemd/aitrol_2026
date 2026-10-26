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

    public function test_git_helper_installer_uses_a_restricted_sudo_rule_without_unsafe_permissions(): void
    {
        $script = file_get_contents(base_path('install-git-helper.sh'));

        $this->assertIsString($script);
        $this->assertStringContainsString('visudo -cf', $script);
        $this->assertStringContainsString('NOPASSWD:', $script);
        $this->assertStringContainsString('--check', $script);
        $this->assertStringContainsString("upsert_env 'DEPLOY_GIT_HELPER'", $script);
        $this->assertStringContainsString("upsert_env 'DEPLOY_GIT_USER'", $script);
        $this->assertStringNotContainsString('chmod 777', $script);
    }
}
