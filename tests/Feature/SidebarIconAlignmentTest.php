<?php

namespace Tests\Feature;

use Tests\TestCase;

class SidebarIconAlignmentTest extends TestCase
{
    public function test_sidebar_styles_define_aligned_icon_columns_for_each_navigation_level(): void
    {
        $styles = file_get_contents(public_path('css/new-system-legacy.css'));

        $this->assertIsString($styles);
        $this->assertStringContainsString('--aitrol-menu-icon-offset: 18px;', $styles);
        $this->assertStringContainsString('--aitrol-menu-icon-offset: 38px;', $styles);
        $this->assertStringContainsString('--aitrol-menu-icon-offset: 58px;', $styles);
        $this->assertStringContainsString('--aitrol-menu-icon-offset: 78px;', $styles);
        $this->assertStringContainsString('.aitrol-shell .sidebar-menu li.container-clip > button', $styles);
        $this->assertStringContainsString('position: absolute;', $styles);
    }
}
