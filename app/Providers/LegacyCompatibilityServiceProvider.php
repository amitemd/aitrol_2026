<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LegacyCompatibilityServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    private array $phpExcelAliases = [
        'PHPExcel' => Spreadsheet::class,
        'PHPExcel_Cell' => Cell::class,
        'PHPExcel_Cell_DataType' => DataType::class,
        'PHPExcel_Cell_DefaultValueBinder' => DefaultValueBinder::class,
        'PHPExcel_Cell_IValueBinder' => IValueBinder::class,
        'PHPExcel_IOFactory' => IOFactory::class,
        'PHPExcel_Shared_Date' => Date::class,
        'PHPExcel_Style_Alignment' => Alignment::class,
        'PHPExcel_Style_Border' => Border::class,
        'PHPExcel_Style_Color' => Color::class,
        'PHPExcel_Style_Fill' => Fill::class,
        'PHPExcel_Style_NumberFormat' => NumberFormat::class,
        'PHPExcel_Worksheet_Drawing' => Drawing::class,
    ];

    public function register(): void
    {
        AliasLoader::getInstance()->alias('Excel', Excel::class);

        foreach ($this->phpExcelAliases as $legacy => $modern) {
            if (! class_exists($legacy, false) && ! interface_exists($legacy, false)) {
                class_alias($modern, $legacy);
            }
        }
    }
}
