<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class ViewCacheCompatibilityTest extends TestCase
{
    public function test_the_counter_referral_template_can_be_compiled_by_blade(): void
    {
        $template = file_get_contents(resource_path('views/hc_admision/formato/contrareferencia.blade.php'));

        $this->assertIsString($template);
        $this->assertNotSame('', Blade::compileString($template));
    }
}
