<?php

namespace Tests\Feature;

use Tests\TestCase;

class NewSystemNavigationTest extends TestCase
{
    public function test_new_system_layouts_load_shared_navigation_before_adminlte(): void
    {
        $layouts = [
            resource_path('views/components/new-system/layout.blade.php'),
            resource_path('views/layouts/app-template.blade.php'),
        ];

        foreach ($layouts as $layoutPath) {
            $layout = file_get_contents($layoutPath);

            $this->assertIsString($layout);
            $navigationPosition = strpos($layout, 'js/new-system-navigation.js');
            $adminLtePosition = strpos($layout, 'js/custom.js');

            $this->assertNotFalse($navigationPosition);
            $this->assertNotFalse($adminLtePosition);
            $this->assertTrue($navigationPosition < $adminLtePosition);
        }
    }

    public function test_shared_navigation_enables_the_tree_filter_and_accessible_state(): void
    {
        $scriptPath = public_path('js/new-system-navigation.js');

        $this->assertFileExists($scriptPath);

        $script = file_get_contents($scriptPath);

        $this->assertIsString($script);
        $this->assertStringContainsString('options.enableControlTreeView = true;', $script);
        $this->assertStringContainsString("search.addEventListener('input'", $script);
        $this->assertStringContainsString("search.addEventListener('keydown'", $script);
        $this->assertStringContainsString('aria-expanded', $script);
    }
}
