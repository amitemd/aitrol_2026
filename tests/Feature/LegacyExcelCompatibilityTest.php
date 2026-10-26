<?php

namespace Tests\Feature;

use App\Compatibility\Excel\LegacyWorkbook;
use Tests\TestCase;

class LegacyExcelCompatibilityTest extends TestCase
{
    public function test_legacy_excel_builder_creates_a_modern_workbook(): void
    {
        $workbook = new LegacyWorkbook('reporte-prueba');

        $workbook->sheet('Datos', function ($sheet): void {
            $sheet->cell('A1', fn ($cell) => $cell
                ->setValue('Paciente')
                ->setFontWeight('bold')
                ->setBackground('#0e7490'));
            $sheet->row(2, ['0950000000', 'Paciente de prueba']);
        });

        $sheet = $workbook->spreadsheet()->getActiveSheet();

        $this->assertSame('Datos', $sheet->getTitle());
        $this->assertSame('Paciente', $sheet->getCell('A1')->getValue());
        $this->assertTrue($sheet->getStyle('A1')->getFont()->getBold());
        $this->assertSame('0950000000', $sheet->getCell('A2')->getValue());
    }
}
