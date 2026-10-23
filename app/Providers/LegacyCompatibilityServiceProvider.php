<?php

namespace App\Providers;

use App\Compatibility\LegacyUnavailableController;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LegacyCompatibilityServiceProvider extends ServiceProvider
{
    /** @var list<class-string> */
    private array $missingLegacyControllers = [
        'Sis_medico\Http\Controllers\CamillaManagementController',
        'Sis_medico\Http\Controllers\FinancieroController',
        'Sis_medico\Http\Controllers\FormularioController',
        'Sis_medico\Http\Controllers\ReporteComisionesController',
        'Sis_medico\Http\Controllers\BiopsiaPacienteController',
        'Sis_medico\Http\Controllers\hc4\ResultadoBiopsiasController',
        'Sis_medico\Http\Controllers\hc4\EgresoController',
        'Sis_medico\Http\Controllers\archivo_plano\CrudMedicamentosController',
        'Sis_medico\Http\Controllers\archivo_plano\CrudInsumosController',
        'Sis_medico\Http\Controllers\PaginaController',
        'Sis_medico\Http\Controllers\contable\LiquidacionCompraController',
        'Sis_medico\Http\Controllers\contable\MantenimientoPrestamosController',
        'Sis_medico\Http\Controllers\contable\ReporteFlujoController',
        'Sis_medico\Http\Controllers\contable\PrecioProductoAprobadoController',
    ];

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

        foreach ($this->missingLegacyControllers as $controller) {
            if (! class_exists($controller)) {
                class_alias(LegacyUnavailableController::class, $controller);
            }
        }
    }
}
