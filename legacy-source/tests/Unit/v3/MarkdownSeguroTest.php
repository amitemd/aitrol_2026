<?php

namespace Tests\Unit\v3;

use PHPUnit\Framework\TestCase;
use Sis_medico\v3\contable\MarkdownSeguro;

/**
 * @group v3
 */
class MarkdownSeguroTest extends TestCase
{
    public function testRenderizaLaEstructuraDocumentalBasica()
    {
        $markdown = "# Titulo\n\n- Elemento\n\n| Columna | Valor |\n|---|---|\n| Uno | Dos |";
        $html = MarkdownSeguro::render($markdown);

        $this->assertContains('<h3', $html);
        $this->assertContains('<ul', $html);
        $this->assertContains('<table', $html);
        $this->assertContains('<td>Uno</td>', $html);
    }

    public function testEscapaHtmlIncluidoEnElDocumento()
    {
        $html = MarkdownSeguro::render("# Seguro\n\n<script>alert('x')</script>");

        $this->assertNotContains('<script>', $html);
        $this->assertContains('&lt;script&gt;', $html);
    }

    public function testConservaBloquesDeCodigoSinEjecutarlos()
    {
        $html = MarkdownSeguro::render("```\n<b>codigo</b>\n```");

        $this->assertContains('<pre', $html);
        $this->assertContains('&lt;b&gt;codigo&lt;/b&gt;', $html);
        $this->assertNotContains('<b>codigo</b>', $html);
    }
}
