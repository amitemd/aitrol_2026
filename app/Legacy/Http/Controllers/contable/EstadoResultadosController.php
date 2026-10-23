<?php

namespace Sis_medico\Http\Controllers\contable;

use Dompdf\Dompdf;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Porcentaje_Impuesto_Renta;
use Sis_medico\Ct_Porcentaje_Renta;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\EstadoResultado;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\LogAsiento;
use Sis_medico\LogCierreAnio;
use Sis_medico\Plan_Cuentas;
use Sis_medico\UsuarioEspecial;

class EstadoResultadosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 26)) == false) {
            return true;
        }
    }
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $estadopyg  = array();
        $ingresos   = array();
        $costos     = array();
        $gastos     = array();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }
        $tipo_dato = 0;
        if (isset($request['tipo'])) {
            $id_auth  = Auth::user()->id;
            $especial = UsuarioEspecial::where('id_usuario', $id_auth)->where('id_empresa', $id_empresa)->first();
            if (!is_null($especial)) {
                if ($request['tipo'] == 1) {
                    $tipo_dato = 1;
                }
            }
        }

        // $registros = Ct_Asientos_Cabecera::whereBetween('fecha_asiento', [$fecha . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->paginate(20);

        return view('contable/estado_resultados/index', ['empresa' => $empresa, 'estadopyg' => $estadopyg, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'ingresos' => $ingresos, 'costos' => $costos, 'gastos' => $gastos, 'tipo_dato' => $tipo_dato]);
    }
    public function convertidorFecha($fecha)
    {

        $fechaParsear           = str_replace('/', '-', $fecha);
        $timestamp              = \Carbon\Carbon::parse($fechaParsear)->timestamp;
        $fecha_desde            = date('Y-m-d', $timestamp);
        return $fecha_desde;
    }
    public function separarTodoAnio($ingresos)
    {

        $arrayContenedor = [];
        foreach ($ingresos as $key => $ingre) {

            $anio = explode("-", $ingre['fecha']);
            if (!array_key_exists($anio[0], $arrayContenedor)) {

                $arrayContenedor[$anio[0]] = [];
            }

            $positionInsert = array_search($anio[0], array_keys($arrayContenedor));
            $arrayDis = array_keys($arrayContenedor);
            $indice = $arrayDis[$positionInsert];

            array_push($arrayContenedor[$indice], $ingre);
        }


        return $arrayContenedor;
    }
    public function separarTodoMes($ingresos)
    {

        foreach ($ingresos as $key => $ingre) {
            $arrayContenedor = [];
            foreach ($ingre as $val) {

                $anio = explode("-", $val['fecha']);
                if (!array_key_exists($anio[1], $arrayContenedor)) {

                    $arrayContenedor[$anio[1]] = [];
                }

                $positionInsert = array_search($anio[1], array_keys($arrayContenedor));

                $arrayDis = array_keys($arrayContenedor);
                $indice = $arrayDis[$positionInsert];

                array_push($arrayContenedor[$indice], $val);
            }

            if (!empty($arrayContenedor)) {
                $ingresos[$key] = [];
                array_push($ingresos[$key], $arrayContenedor);
            }
        }



        return $ingresos;
    }
    function contCaracter($caracterParametro)
    {

        $caracter = ".";
        $cont = 0;

        for ($i = 0; $i < strlen($caracterParametro["cuenta"]); $i++) {
            if ($caracterParametro["cuenta"][$i] == $caracter) {
                $cont++;
            }
        }

        return $cont;
    }
    public function detalles_mensual($arrayCostos2, $comienzo, $sheet, $posicion, $i)
    {

        $arrayMeS = [

            '1' => 0.0,
            '2' => 0.0,
            '3' => 0.0,
            '4' => 0.0,
            '5' => 0.0,
            '6' => 0.0,
            '7' => 0.0,
            '8' => 0.0,
            '9' => 0.0,
            '10' => 0.0,
            '11' => 0.0,
            '12' => 0.0,

        ];

        $sheet->setColumnFormat(array(
            'C' => '$ 0.00',
            'D' => '$ 0.00',
            'E' => '$ 0.00',
            'F' => '$ 0.00',
            'G' => '$ 0.00',
            'H' => '$ 0.00',
            'I' => '$ 0.00',
            'J' => '$ 0.00',
            'K' => '$ 0.00',
            'L' => '$ 0.00',
            'M' => '$ 0.00',
            'N' => '$ 0.00',
        ));

        foreach ($arrayCostos2 as $val) {
            foreach ($val[0] as $tav) {
                foreach ($tav as $codig) {
                    $contU = $this->contCaracter($codig);

                    if ($contU > 1) {
                        $datos_excel = array();
                        $mes = explode("-", $codig['fecha']);

                        $mesCondicion = $mes[1];

                        if ($mesCondicion[0] == '0') {
                            $mes =  substr($mesCondicion, 1, 1);
                        }

                        array_push($datos_excel, $codig['cuenta'], $codig['saldo'], $mes, $codig['nombre']);

                        $sheet->cell('A' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[0]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('B' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[3]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });


                        for ($i = 1; $i <= 12; $i++) {
                            $cont = $i + 1;
                            if ($i == $datos_excel[2]) {
                                if (array_key_exists($datos_excel[2], $arrayMeS)) {


                                    $valorArray = $arrayMeS[$datos_excel[2]];

                                    $valorNuevo =  $valorArray + $datos_excel[1];

                                    $arrayMeS[$datos_excel[2]] = $valorNuevo;
                                }
                                $sheet->cell('' . $posicion[$cont] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                    $cell->setValue(number_format($datos_excel[1], 2, '.', ','));
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                            } else {
                                $sheet->cell('' . $posicion[$cont] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                    $cell->setValue("");
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                            }
                        }
                        $comienzo++;
                    }
                }
            }
        }



        return [$comienzo, $arrayMeS];
    }
    public function ParticipacionTrabajadores($fechas = [])
    {
        $array = [];
        $fecha_desde = $this->convertidorFecha($fechas['fecha_desde']);
        $fecha_hasta_ = $this->convertidorFecha($fechas['fecha_hasta']);
        $fecha_hasta = date("Y-m-d", strtotime($fecha_hasta_ . "+ 1 days"));
        $id_empresa   = session('id_empresa');
        $empresa      = Empresa::where('id', $id_empresa)->first();
        $sql = 'select pe.plan,pe.nombre,pe.estado,sum(debe),sum(haber),
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,
        pe.naturaleza,pe.naturaleza_2,ac.fecha_asiento,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) enero,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) febrero,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) marzo,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) abril,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) mayo,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) junio,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) julio,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) agosto,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) septiembre,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) octubre,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) noviembre,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) diciembre,
        (
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,
        IF(SUBSTRING(pe.plan,1,2)=\'4.\',\'INGRESO\',IF(SUBSTRING(pe.plan,1,3)=\'5.1\',\'COSTO\',\'GASTO\')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=' . session('id_empresa') . '
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=' . session('id_empresa') . '
        and ac.fecha_asiento>="' . $fecha_desde . '"
        and ac.fecha_asiento<"' . $fecha_hasta . '"
        and (pe.plan like "4.%" or pe.plan like "5.%")
        group by pe.plan;';
        $estadoIntegral = DB::select($sql);
        if (count($estadoIntegral) > 0) {
            $enero_ = 0;
            $febrero_ = 0;
            $marzo_ = 0;
            $abril_ = 0;
            $mayo_ = 0;
            $junio_ = 0;
            $julio_ = 0;
            $agosto_ = 0;
            $septiembre_ = 0;
            $octubre_ = 0;
            $noviembre_ = 0;
            $diciembre_ = 0;
            $totalMeses = 0;
            $total_ = 0;
            $total_i = 0;
            $ingresos_enero = 0;
            $ingresos_febrero = 0;
            $ingresos_marzo = 0;
            $ingresos_abril = 0;
            $ingresos_mayo = 0;
            $ingresos_junio = 0;
            $ingresos_julio = 0;
            $ingresos_agosto = 0;
            $ingresos_septiembre = 0;
            $ingresos_octubre = 0;
            $ingresos_noviembre = 0;
            $ingresos_diciembre = 0;
            $ingresos_total = 0;

            $costos_enero = 0;
            $costos_febrero = 0;
            $costos_marzo = 0;
            $costos_abril = 0;
            $costos_mayo = 0;
            $costos_junio = 0;
            $costos_julio = 0;
            $costos_agosto = 0;
            $costos_septiembre = 0;
            $costos_octubre = 0;
            $costos_noviembre = 0;
            $costos_diciembre = 0;
            $costos_total = 0;

            $gastos_enero = 0;
            $gastos_febrero = 0;
            $gastos_marzo = 0;
            $gastos_abril = 0;
            $gastos_mayo = 0;
            $gastos_junio = 0;
            $gastos_julio = 0;
            $gastos_agosto = 0;
            $gastos_septiembre = 0;
            $gastos_octubre = 0;
            $gastos_noviembre = 0;
            $gastos_diciembre = 0;
            $gastos_total = 0;
            $arrayIngreso = 0;
            $arrayCosto = 0;
            $arrayGasto = 0;
            $tr = array_column($estadoIntegral, "tipo");
            foreach ($tr as $val) {
                if ($val == 'INGRESO') {
                    $arrayIngreso++;
                }
                if ($val == 'COSTO') {
                    $arrayCosto++;
                }
                if ($val == 'GASTO') {
                    $arrayGasto++;
                }
            }
            $ingreso1 = 0;
            $costo1 = 0;
            $gasto1 = 0;
            $countiN = 0;
            $countiN1 = 0;
            $county = 0;
            $countSuma = 0;
            foreach ($estadoIntegral as $row) {
                $enero_ += $row->enero;
                $febrero_ += $row->febrero;
                $marzo_ += $row->marzo;
                $abril_ += $row->abril;
                $mayo_ += $row->mayo;
                $junio_ += $row->junio;
                $julio_ += $row->julio;
                $agosto_ += $row->agosto;
                $septiembre_ += $row->septiembre;
                $octubre_ += $row->octubre;
                $noviembre_ += $row->noviembre;
                $diciembre_ += $row->diciembre;
                $total_ += $row->total;
                $total_i += $row->total;
                if ($row->tipo == 'INGRESO') {
                    $ingreso1++;
                    $ingresos_enero += $row->enero;
                    $ingresos_febrero += $row->febrero;
                    $ingresos_marzo += $row->marzo;
                    $ingresos_abril += $row->abril;
                    $ingresos_mayo += $row->mayo;
                    $ingresos_junio += $row->junio;
                    $ingresos_julio += $row->julio;
                    $ingresos_agosto += $row->agosto;
                    $ingresos_septiembre += $row->septiembre;
                    $ingresos_octubre += $row->octubre;
                    $ingresos_noviembre += $row->noviembre;
                    $ingresos_diciembre += $row->diciembre;
                    $ingresos_total = $total_i;
                }
                if (substr($row->plan, 0, 1) > 4 && $arrayIngreso == $ingreso1 && $countiN < 1) {

                    $countiN++;
                }
                if ($row->tipo == 'COSTO') {
                    $costo1++;
                    $costos_enero += $row->enero;
                    $costos_febrero += $row->febrero;
                    $costos_marzo += $row->marzo;
                    $costos_abril += $row->abril;
                    $costos_mayo += $row->mayo;
                    $costos_junio += $row->junio;
                    $costos_julio += $row->julio;
                    $costos_agosto += $row->agosto;
                    $costos_septiembre += $row->septiembre;
                    $costos_octubre += $row->octubre;
                    $costos_noviembre += $row->noviembre;
                    $costos_diciembre += $row->diciembre;
                    $costos_total += $total_;
                    $countSuma++;
                }
                if (substr($row->plan, 0, 3) >= '5.2' && $arrayCosto == $costo1 && $countiN1 < 1) {
                    $countiN1++;
                }
                if ($row->tipo == 'GASTO') {
                    $gasto1++;
                    $gastos_enero += $row->enero;
                    $gastos_febrero += $row->febrero;
                    $gastos_marzo += $row->marzo;
                    $gastos_abril += $row->abril;
                    $gastos_mayo += $row->mayo;
                    $gastos_junio += $row->junio;
                    $gastos_julio += $row->julio;
                    $gastos_agosto += $row->agosto;
                    $gastos_septiembre += $row->septiembre;
                    $gastos_octubre += $row->octubre;
                    $gastos_noviembre += $row->noviembre;
                    $gastos_diciembre += $row->diciembre;
                    $gastos_total += $total_;
                }
                $totalMeses += $row->total;
                $total_ = 0;
            }
            if (substr($row->plan, 0, 3) == '5.2' && $arrayGasto == $gasto1 && $county < 1) {
                $county++;
            }
            $participacionTrabajadores = 0;
            if ($totalMeses > 0)
                $participacionTrabajadores = $totalMeses * 0.15;
            $utilidadContable = $totalMeses - $participacionTrabajadores;
            $total_gravable = EstadoResultado::utilidad_gravable_2($fecha_desde, $fecha_hasta, $id_empresa, null, 0, 0);
            $utilidadGravable = $total_gravable;
            $deEmpresa = De_Empresa::where('id_empresa', $empresa->id)->first();
            if ($deEmpresa->tipo_rimpe == 1) {
                $totalIngreso = $ingresos_total;
                $porcentaje = 0;
                $impuestoCalculado = 0;
                $impuesto = 0;
                if ($totalIngreso >= 0.00 && $totalIngreso <= 20000.00) {
                    $porcentaje = 0;
                    $impuesto = 60;
                    $impuestoCalculado = $impuesto;
                } elseif ($totalIngreso >= 20000.01 && $totalIngreso <= 50000.00) {
                    $porcentaje = 1;
                    $impuesto = 60;
                    $diferencia = $totalIngreso - 20000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($totalIngreso >= 50000.01 && $totalIngreso <= 75000.00) {
                    $porcentaje = 1.25;
                    $impuesto = 360;
                    $diferencia = $totalIngreso - 50000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($totalIngreso >= 75000.01 && $totalIngreso <= 100000.00) {
                    $porcentaje = 1.50;
                    $impuesto = 672.50;
                    $diferencia = $totalIngreso - 70000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($totalIngreso >= 100000.01 && $totalIngreso <= 200000.00) {
                    $porcentaje = 1.75;
                    $impuesto = 1047.50;
                    $diferencia = $totalIngreso - 100000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($totalIngreso >= 200000.01 && $totalIngreso <= 300000.00) {
                    $porcentaje = 2;
                    $impuesto = 2797.52;
                    $diferencia = $totalIngreso - 200000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                }
                $impuestoGenerado = $impuestoCalculado;
            } else {
                $fe = (int)date('Y', strtotime($fecha_desde));
                $impuesto = Ct_Porcentaje_Impuesto_Renta::where('anio', $fe)->first();
                $impuestoGenerado = ($utilidadGravable * $impuesto->porcentaje) / 100;
            }
            $utilidadNeta = $utilidadContable - $impuestoGenerado;
            $array = [
                'participacion' => $participacionTrabajadores,
                'utilidad' => $utilidadNeta
            ];
            return $array;
        }
    }
    public function excelMensual(Request $request)
    {

        $fecha_desde = $this->convertidorFecha($request['fecha_desde']);
        $fecha_hasta_ = $this->convertidorFecha($request['fecha_hasta']);
        $fecha_hasta = date("Y-m-d", strtotime($fecha_hasta_ . "+ 1 days"));
        $id_empresa   = $request->session()->get('id_empresa');
        $empresa      = Empresa::where('id', $id_empresa)->first();
        $sql = 'select pe.plan,pe.nombre,pe.estado,sum(debe),sum(haber),
        if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
        if(pe.naturaleza_2=0,sum(IFNULL(debe,0)-IFNULL(haber,0)),0) as saldo_deudor,
        pe.naturaleza,pe.naturaleza_2,ac.fecha_asiento,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) enero,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) febrero,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) marzo,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) abril,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) mayo,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) junio,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) julio,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) agosto,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) septiembre,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) octubre,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) noviembre,
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0.00) diciembre,
        (
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) +
        IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (if(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0) 
        ) as total,
        IF(SUBSTRING(pe.plan,1,2)=\'4.\',\'INGRESO\',IF(SUBSTRING(pe.plan,1,3)=\'5.1\',\'COSTO\',\'GASTO\')) as tipo
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=' . session('id_empresa') . '
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=' . session('id_empresa') . '
        and ac.fecha_asiento>="' . $fecha_desde . '"
        and ac.fecha_asiento<"' . $fecha_hasta . '"
        and (pe.plan like "4.%" or pe.plan like "5.%")
        group by pe.plan;';
        $estadoIntegral = DB::select($sql);

        $tabla = '';
        //echo url('/').'/imagenes/'.$empresa->logo;exit;
        if (count($estadoIntegral) > 0) {
            $arrayMeses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Semtiembre", "Octubre", "Noviembre", "Diciembre"];
            $filename = 'EstadoSituacionFinanciera-' . $empresa->nombrecomercial . '-' . $fecha_desde . '-al-' . $fecha_hasta_;
            $tabla = '<html>
            <head>
            <title>EstadoSituacionFinanciera-' . $fecha_desde . '-al-' . $fecha_hasta_ . '</title>
            </head>
            <body>
            <div align="center">
                <div style="position:relative;">
                    <!--<img src="' . url('/') . '/imagenes/' . $empresa->logo . '" style="width:100px" alt="logo" />-->
                </div>
                <div>
                    <span /><strong>Estado de resultado integral de la empresa ' . $empresa->nombrecomercial . '<br/> del dia ' . $fecha_desde . ' hasta ' . $fecha_hasta_ . '</strong></span>
                </div>
            </div>
            <table width="100%" border="1">
            <tr>
                <th>Cuenta</th>
                <th>Nombre cuenta</th>';
            for ($i = 0; $i < count($arrayMeses); $i++) {
                $tabla .= '<th>' . $arrayMeses[$i] . '</th>';
            }
            $tabla .= '<th>Total</th>';
            $tabla .= '</tr>';

            $enero_ = 0;
            $febrero_ = 0;
            $marzo_ = 0;
            $abril_ = 0;
            $mayo_ = 0;
            $junio_ = 0;
            $julio_ = 0;
            $agosto_ = 0;
            $septiembre_ = 0;
            $octubre_ = 0;
            $noviembre_ = 0;
            $diciembre_ = 0;
            $totalMeses = 0;
            $total = 0;
            $total_ = 0;

            $total_i = 0;

            $ingresos_enero = 0;
            $ingresos_febrero = 0;
            $ingresos_marzo = 0;
            $ingresos_abril = 0;
            $ingresos_mayo = 0;
            $ingresos_junio = 0;
            $ingresos_julio = 0;
            $ingresos_agosto = 0;
            $ingresos_septiembre = 0;
            $ingresos_octubre = 0;
            $ingresos_noviembre = 0;
            $ingresos_diciembre = 0;
            $ingresos_total = 0;

            $costos_enero = 0;
            $costos_febrero = 0;
            $costos_marzo = 0;
            $costos_abril = 0;
            $costos_mayo = 0;
            $costos_junio = 0;
            $costos_julio = 0;
            $costos_agosto = 0;
            $costos_septiembre = 0;
            $costos_octubre = 0;
            $costos_noviembre = 0;
            $costos_diciembre = 0;
            $costos_total = 0;

            $gastos_enero = 0;
            $gastos_febrero = 0;
            $gastos_marzo = 0;
            $gastos_abril = 0;
            $gastos_mayo = 0;
            $gastos_junio = 0;
            $gastos_julio = 0;
            $gastos_agosto = 0;
            $gastos_septiembre = 0;
            $gastos_octubre = 0;
            $gastos_noviembre = 0;
            $gastos_diciembre = 0;
            $gastos_total = 0;
            $arrayIngreso = 0;
            $arrayCosto = 0;
            $arrayGasto = 0;
            $tr = array_column($estadoIntegral, "tipo");

            foreach ($tr as $val) {

                if ($val == 'INGRESO') {
                    $arrayIngreso++;
                }
                if ($val == 'COSTO') {
                    $arrayCosto++;
                }

                if ($val == 'GASTO') {
                    $arrayGasto++;
                }
            }
            $ingreso1 = 0;
            $costo1 = 0;
            $gasto1 = 0;
            $countiN = 0;
            $countiN1 = 0;
            $county = 0;
            //suma valores 
            $countSuma = 0;
            foreach ($estadoIntegral as $row) {
                $enero_ += $row->enero;
                $enero = number_format($row->enero, 2, ',', '');
                $febrero_ += $row->febrero;
                $febrero = number_format($row->febrero, 2, ',', '');
                $marzo_ += $row->marzo;
                $marzo = number_format($row->marzo, 2, ',', '');
                $abril_ += $row->abril;
                $abril =  number_format($row->abril, 2, ',', '');
                $mayo_ += $row->mayo;
                $mayo = number_format($row->mayo, 2, ',', '');
                $junio_ += $row->junio;
                $junio = number_format($row->junio, 2, ',', '');
                $julio_ += $row->julio;
                $julio = number_format($row->julio, 2, ',', '');
                $agosto_ += $row->agosto;
                $agosto = number_format($row->agosto, 2, ',', '');
                $septiembre_ += $row->septiembre;
                $septiembre = number_format($row->septiembre, 2, ',', '');
                $octubre_ += $row->octubre;
                $octubre = number_format($row->octubre, 2, ',', '');
                $noviembre_ += $row->noviembre;
                $noviembre = number_format($row->noviembre, 2, ',', '');
                $diciembre_ += $row->diciembre;
                $diciembre = number_format($row->diciembre, 2, ',', '');
                $total_ += $row->total;
                $total_i += $row->total;
                $total = number_format($row->total, 2, ',', '');
                $color = '';
                if ($row->tipo == 'INGRESO') {
                    $ingreso1++;
                    $color = '';
                    $ingresos_enero += $row->enero;
                    $ingresos_febrero += $row->febrero;
                    $ingresos_marzo += $row->marzo;
                    $ingresos_abril += $row->abril;
                    $ingresos_mayo += $row->mayo;
                    $ingresos_junio += $row->junio;
                    $ingresos_julio += $row->julio;
                    $ingresos_agosto += $row->agosto;
                    $ingresos_septiembre += $row->septiembre;
                    $ingresos_octubre += $row->octubre;
                    $ingresos_noviembre += $row->noviembre;
                    $ingresos_diciembre += $row->diciembre;
                    $ingresos_total = $total_i;
                }
                $tablaTotalIngresos = '<tr>
                <td></td>
                <td>Total Ingresos</td>
                <td style="text-align:right">' . number_format($ingresos_enero, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_febrero, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_marzo, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_abril, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_mayo, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_junio, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_julio, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_agosto, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_septiembre, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_octubre, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_noviembre, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_diciembre, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($ingresos_total, 2, ',', '') . '</td>
                </tr>
                <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>';
                /*  $arrayIngreso = 0;
                $arrayCosto = 0;
                $arrayGasto = 0; */
                if (substr($row->plan, 0, 1) > 4 && $arrayIngreso == $ingreso1 && $countiN < 1) {

                    $tabla .= $tablaTotalIngresos;
                    $countiN++;
                }
                if ($row->tipo == 'COSTO') {
                    $costo1++;
                    $color = '';
                    $costos_enero += $row->enero;
                    $costos_febrero += $row->febrero;
                    $costos_marzo += $row->marzo;
                    $costos_abril += $row->abril;
                    $costos_mayo += $row->mayo;
                    $costos_junio += $row->junio;
                    $costos_julio += $row->julio;
                    $costos_agosto += $row->agosto;
                    $costos_septiembre += $row->septiembre;
                    $costos_octubre += $row->octubre;
                    $costos_noviembre += $row->noviembre;
                    $costos_diciembre += $row->diciembre;
                    $costos_total += $total_;
                    $countSuma++;
                }
                $tablaTotalCostos = '<tr>
                    <td></td>
                    <td>Total Costos</td>
                    <td style="text-align:right">' . number_format($costos_enero, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_febrero, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_marzo, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_abril, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_mayo, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_junio, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_julio, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_agosto, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_septiembre, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_octubre, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_noviembre, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_diciembre, 2, ',', '') . '</td>
                    <td style="text-align:right">' . number_format($costos_total, 2, ',', '') . '</td>
                    </tr>
                    <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    </tr>';
                /*  if (substr($row->plan, 0, 1) > 4 && $contIngreso == 0) { */
                if (substr($row->plan, 0, 3) >= '5.2' && $arrayCosto == $costo1 && $countiN1 < 1) {

                    $tabla .= $tablaTotalCostos;
                    $countiN1++;
                }
                if ($row->tipo == 'GASTO') {
                    $gasto1++;
                    $color = '';
                    $gastos_enero += $row->enero;
                    $gastos_febrero += $row->febrero;
                    $gastos_marzo += $row->marzo;
                    $gastos_abril += $row->abril;
                    $gastos_mayo += $row->mayo;
                    $gastos_junio += $row->junio;
                    $gastos_julio += $row->julio;
                    $gastos_agosto += $row->agosto;
                    $gastos_septiembre += $row->septiembre;
                    $gastos_octubre += $row->octubre;
                    $gastos_noviembre += $row->noviembre;
                    $gastos_diciembre += $row->diciembre;
                    $gastos_total += $total_;
                }
                $tablaTotalGastos = '<tr>
                <td></td>
                <td>Total Gastos</td>
                <td style="text-align:right">' . number_format($gastos_enero, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_febrero, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_marzo, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_abril, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_mayo, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_junio, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_julio, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_agosto, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_septiembre, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_octubre, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_noviembre, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_diciembre, 2, ',', '') . '</td>
                <td style="text-align:right">' . number_format($gastos_total, 2, ',', '') . '</td>
                </tr>
                <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>';
                $totalMeses += $row->total;
                //echo $totalMeses;exit;
                //$totalMeses_+=$enero_+$febrero_+$marzo_+$abril_+$mayo_+$junio_+$julio_+$agosto_+$septiembre_+$octubre_+$noviembre_+$diciembre_;
                $tabla .= '<tr>
                <td ' . $color . '>' . $row->plan . '</td>
                <td ' . $color . '>' . $row->nombre . '</td>
                <td ' . $color . ' align="right">' . $enero . '</td>
                <td ' . $color . ' align="right">' . $febrero . '</td>
                <td ' . $color . ' align="right">' . $marzo . '</td>
                <td ' . $color . ' align="right">' . $abril . '</td>
                <td ' . $color . ' align="right">' . $mayo . '</td>
                <td ' . $color . ' align="right">' . $junio . '</td>
                <td ' . $color . ' align="right">' . $julio . '</td>
                <td ' . $color . ' align="right">' . $agosto . '</td>
                <td ' . $color . ' align="right">' . $septiembre . '</td>
                <td ' . $color . ' align="right">' . $octubre . '</td>
                <td ' . $color . ' align="right">' . $noviembre . '</td>
                <td ' . $color . ' align="right">' . $diciembre . '</td>
                <td ' . $color . ' align="right">' . number_format($row->total, 2, ',', '') . '</td>
                </tr>';
                $total_ = 0;
            }
            if (substr($row->plan, 0, 3) == '5.2' && $arrayGasto == $gasto1 && $county < 1) {
                $tabla .= $tablaTotalGastos;
                $county++;
            }
            $tabla .= '<tr>
                <td></td>
                <td>Totales</td>
                <td align="right"><b>' . number_format($enero_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($febrero_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($marzo_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($abril_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($mayo_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($junio_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($julio_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($agosto_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($septiembre_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($octubre_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($noviembre_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($diciembre_, 2, ',', '') . '</b></td>
                <td align="right"><b>' . number_format($totalMeses, 2, ',', '') . '</b></td>
                </tr>';
            $participacionTrabajadores = 0;
            if ($totalMeses > 0)
                $participacionTrabajadores = $totalMeses * 0.15;
            $tabla .= '<tr>
                <td></td>
                <td>15% Participación a trabajadores</td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"><b>' . number_format($participacionTrabajadores, 2, ',', '') . '</b></td>
                </tr>';
            $utilidadContable = $totalMeses - $participacionTrabajadores;
            $tabla .= '<tr>
                <td></td>
                <td>Utilidad contable</td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"><b>' . number_format($utilidadContable, 2, ',', '') . '</b></td>
                </tr>';
            $total_gravable = EstadoResultado::utilidad_gravable_2($fecha_desde, $fecha_hasta, $id_empresa, null, 0, 0);
            $utilidadGravable = $total_gravable;
            $tabla .= '<tr>
                <td></td>
                <td>Utilidad gravable</td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"><b>' . number_format($utilidadGravable, 2, ',', '') . '</b></td>
                </tr>';
            $deEmpresa = De_Empresa::where('id_empresa', $empresa->id)->first();
            if ($deEmpresa->tipo_rimpe == 1) {
                $totalIngreso = $ingresos_total;
                $porcentaje = 0;
                $impuestoCalculado = 0;
                $impuesto = 0;
                if ($totalIngreso >= 0.00 && $totalIngreso <= 20000.00) {
                    $porcentaje = 0;
                    $impuesto = 60;
                    $impuestoCalculado = $impuesto;
                } elseif ($totalIngreso >= 20000.01 && $totalIngreso <= 50000.00) {
                    $porcentaje = 1;
                    $impuesto = 60;
                    $diferencia = $totalIngreso - 20000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($totalIngreso >= 50000.01 && $totalIngreso <= 75000.00) {
                    $porcentaje = 1.25;
                    $impuesto = 360;
                    $diferencia = $totalIngreso - 50000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($totalIngreso >= 75000.01 && $totalIngreso <= 100000.00) {
                    $porcentaje = 1.50;
                    $impuesto = 672.50;
                    $diferencia = $totalIngreso - 70000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($totalIngreso >= 100000.01 && $totalIngreso <= 200000.00) {
                    $porcentaje = 1.75;
                    $impuesto = 1047.50;
                    $diferencia = $totalIngreso - 100000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                } elseif ($totalIngreso >= 200000.01 && $totalIngreso <= 300000.00) {
                    $porcentaje = 2;
                    $impuesto = 2797.52;
                    $diferencia = $totalIngreso - 200000.01;
                    $impuestoCalculado = ($diferencia * $porcentaje) / 100 + $impuesto;
                }
                $impuestoGenerado = $impuestoCalculado;
            } else {
                $fe = (int)date('Y', strtotime($fecha_desde));
                $impuesto = Ct_Porcentaje_Impuesto_Renta::where('anio', $fe)->first();
                $impuestoGenerado = ($utilidadGravable * $impuesto->porcentaje) / 100;
            }
            $tabla .= '<tr>
                <td></td>
                <td>Impuesto generado</td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"><b>' . number_format($impuestoGenerado, 2, ',', '') . '</b></td>
                </tr>';
            $utilidadNeta = $utilidadContable - $impuestoGenerado;
            $tabla .= '<tr>
                <td></td>
                <td>Utilidad neta</td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"></td>
                <td align="right"><b>' . number_format($utilidadNeta, 2, ',', '') . '</b></td>
                </tr>';
            $tabla .= '</table>
                </body>
                </html>';
            if ($request->reporte_mensual == 'xls') {
                header("Expires: 0");
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=$filename.xls");
                header("Pragma: no-cache");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                echo $tabla;
                exit;
            } elseif ($request->reporte_mensual == 'pdf') {
                $data['fecha_desde'] = $fecha_desde;
                $data['fecha_hasta'] = $fecha_hasta;
                $data['fecha_hasta_'] = $fecha_hasta_;
                $data['estadoIntegral'] = $estadoIntegral;
                $data['reporte_mensual'] = $request->reporte_mensual;
                $data['tabla'] = $tabla;
                $customPaper = array(0, 0, 720, 1440);
                $dompdf = new Dompdf();
                $dompdf->loadHTML($tabla);
                $dompdf->setPaper($customPaper, 'landscape');
                $dompdf->render();
                return $dompdf->stream($filename . '.pdf');
                //return $pdf->download($tipoText . $esta . '-' . $emis . '-' . $num . '.pdf');
            }
        }
    }
    public function show(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        if (isset($request['reporte_mensual'])) {
            /* Devuelve true si existe o esta definida la variable en el req
             */
            $this->excelMensual($request);
        }
        if (!isset($request['imprimir'])) {
            //dd($request);            
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
                $tipo_dato   = 0;
                $rfecha_desde = $request['fecha_desde'];
                $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
                $timestamp = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
                $fecha_desde = date('Y-m-d', $timestamp);
                $rfecha_hasta = $request['fecha_hasta'];
                $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
                $timestamp = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
                $fecha_hasta = date('Y-m-d', $timestamp);
            } elseif (is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde  = '2010-01-01';
                $rfecha_desde = '01/01/2010';
                $tipo_dato    = 1;
                $fecha_hasta = $request['fecha_hasta'];
                $rfecha_hasta = $request['fecha_hasta'];
                $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
                $timestamp = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
                $fecha_hasta = date('Y-m-d', $timestamp);
            } else {
                $fecha_desde  = date('Y-m-d');
                $rfecha_desde = date('d/m/Y');
                $fecha_hasta  = date('Y-m-d');
                $rfecha_hasta = date('d/m/Y');
                $tipo_dato    = 0;
            }
            $cuentas_detalle = "";
            if (isset($request['cuentas_detalle'])) {
                $cuentas_detalle = 1;
            }
            $mostrar_detalle = "";
            if (isset($request['mostrar_detalle'])) {
                $mostrar_detalle = $request['mostrar_detalle'];
            }
            $mostrar_cierre = 0;
            if (isset($request['mostrar_cierre'])) {
                $mostrar_cierre = $request['mostrar_cierre'];
            }
            $mostrar_acumulado = 0;
            if (isset($request['mostrar_acumulado'])) {
                $mostrar_acumulado = $request['mostrar_acumulado'];
            }
            $ingresos = array();
            $gresos = array();
            $gastos = array();
            $ingresos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle, 0, $mostrar_cierre, $mostrar_acumulado);
            $toting = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'I', null, 0, $mostrar_cierre, $mostrar_acumulado);
            $costos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle, 0, $mostrar_cierre, $mostrar_acumulado);
            $totcos = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C', null, 0, $mostrar_cierre, $mostrar_acumulado);
            $gastos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle, 0, $mostrar_cierre, $mostrar_acumulado);
            $totgas = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G', null, 0, $mostrar_cierre, $mostrar_acumulado);
            $totpyg = EstadoResultado::detalle_total_pyg($fecha_desde, $fecha_hasta, null, $mostrar_cierre, $mostrar_acumulado);
            $trabajadores = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa, null, array(), $mostrar_cierre, $mostrar_acumulado);
            $total = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, null, null, $mostrar_cierre, $mostrar_acumulado);
            $total_gravable = EstadoResultado::utilidad_gravable_2($fecha_desde, $fecha_hasta, $id_empresa, null, $mostrar_cierre, $mostrar_acumulado);
            $renta_acumulada = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, null, $mostrar_cierre, $mostrar_acumulado, $ingresos);
            $cierre = LogCierreAnio::where('anio', date('Y', strtotime($fecha_desde)))->where('id_empresa', $id_empresa)->first();
            /*if (Auth::user()->id == '0950457978') {
                echo '<pre>';
                print_r($cierre);
                exit;
            }*/
            if ($cierre != '') {
                $renta_acumulada = 0;
            }
            $links = array();
            $fechadesde = $fecha_desde . " " . "00:00:00";
            $fechahasta = $fecha_hasta . " " . "00:00:00";
            $tipoRegimen = 2;
            $porcentaje_renta = Ct_Porcentaje_Renta::where('anio', date('Y', strtotime($fecha_desde)))->where('id_empresa', $id_empresa)->first();
            if ($porcentaje_renta != '')
                $tipoRegimen = $porcentaje_renta->regimen_especial;
            return view('contable/estado_resultados/index', ['tipoRegimen' => $tipoRegimen, 'fecha_desde' => $rfecha_desde, 'fecha_hasta' => $rfecha_hasta, 'empresa' => $empresa, 'ingresos' => $ingresos, 'totpyg' => $totpyg, 'costos' => $costos, 'gastos' => $gastos, 'cuentas_detalle' => $cuentas_detalle, 'mostrar_detalle' => $mostrar_detalle, 'mostrar_cierre' => $mostrar_cierre, 'mostrar_acumulado' => $mostrar_acumulado, 'trabajadores' => $trabajadores, 'total' => $total, 'renta_acumulada' => $renta_acumulada, 'tipo_dato' => $tipo_dato, 'total_gravable' => $total_gravable, 'links' => $links, 'r_espe' => 0, 'id_empresa' => $id_empresa]);
        } else {
            if (!is_null($request['filfecha_desde']) && !is_null($request['filfecha_hasta'])) {
                $fecha_desde = $request['filfecha_desde'];
                $fecha_hasta = $request['filfecha_hasta'];
                $rfecha_desde = $request['filfecha_desde'];
                $request['filfecha_desde'] = str_replace('/', '-', $request['filfecha_desde']);
                $timestamp = \Carbon\Carbon::parse($request['filfecha_desde'])->timestamp;
                $fecha_desde = date('Y-m-d', $timestamp);
                $rfecha_hasta = $request['filfecha_hasta'];
                $request['filfecha_hasta'] = str_replace('/', '-', $request['filfecha_hasta']);
                $timestamp = \Carbon\Carbon::parse($request['filfecha_hasta'])->timestamp;
                $fecha_hasta = date('Y-m-d', $timestamp);
            } else {
                $fecha_desde  = date('Y-m-d');
                $rfecha_desde = date('d/m/Y');
                $fecha_hasta  = date('Y-m-d');
                $rfecha_hasta = date('d/m/Y');
            }
            $cuentas_detalle = "";
            if (isset($request['filcuentas_detalle'])) {
                $cuentas_detalle = $request['filcuentas_detalle'];
            }
            $mostrar_detalle = "";
            if (isset($request['filmostrar_detalle'])) {
                $mostrar_detalle = 1;
            }
            $mostrar_cierre = 0;
            if (isset($request['mostrar_cierre'])) {
                $mostrar_cierre = $request['mostrar_cierre'];
            }
            $ingresos = array();
            $gresos   = array();
            $gastos   = array();
            $ingresos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'I', $cuentas_detalle, 0, $mostrar_cierre);
            $toting   = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'I', null, 0, $mostrar_cierre);
            $id_auth  = Auth::user()->id;
            $costos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'C', $cuentas_detalle, 0, $mostrar_cierre);
            $totcos = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'C', null, 0, $mostrar_cierre);
            $gastos = EstadoResultado::detalle_estadopg($fecha_desde, $fecha_hasta, 'G', $cuentas_detalle, 0, $mostrar_cierre);
            $totgas = EstadoResultado::detalle_total_cuenta($fecha_desde, $fecha_hasta, 'G', null, 0, $mostrar_cierre);
            $totpyg = EstadoResultado::detalle_total_pyg($fecha_desde, $fecha_hasta, null, $mostrar_cierre);
            $trabajadores = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa, null, array(), $mostrar_cierre);
            $participacion   = EstadoResultado::trabajadores($fecha_desde, $fecha_hasta, $id_empresa, null, array(), $mostrar_cierre);
            $totalf = EstadoResultado::utilidad_gravable($fecha_desde, $fecha_hasta, $id_empresa, null, null, $mostrar_cierre);
            $renta_acumulada = EstadoResultado::impuesto_causado($fecha_desde, $fecha_hasta, $id_empresa, null, $mostrar_cierre, 0, $ingresos);
            $total_gravable = EstadoResultado::utilidad_gravable_2($fecha_desde, $fecha_hasta, $id_empresa, null, $mostrar_cierre);
            $total = $totalf - $renta_acumulada;
            $periodo_desde = "";
            $periodo_hasta = "";
            $periodo_hasta = $this->fechaTexto($fecha_hasta);
            if ($request['exportar'] == 0) {
                $vistaurl = "contable/estado_resultados/print";
                $view = \View::make($vistaurl, compact('fecha_desde', 'fecha_hasta', 'empresa', 'ingresos', 'costos', 'gastos', 'totpyg', 'participacion', 'totalf', 'renta_acumulada', 'total_gravable'))->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->stream('EstadoResultado-' . $fecha_desde . '-' . $fecha_hasta . '.pdf');
            } else {
                Excel::create('EstadoSituacionFinanciera-' . $fecha_desde . '-al-' . $fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg, $participacion, $totalf, $renta_acumulada, $total, $total_gravable) {
                    $excel->sheet('EstadoSituacionFinanciera', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $periodo_hasta, $ingresos, $toting, $costos, $totcos, $gastos, $totgas, $totpyg, $participacion, $totalf, $renta_acumulada, $total, $total_gravable) {
                        $sheet->mergeCells('A1:F1');
                        $sheet->cell('A1', function ($cell) use ($empresa) {
                            // manipulate the cel
                            $cell->setValue($empresa->nombrecomercial);
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', '', 'thin');
                        });
                        $sheet->mergeCells('A2:F2');
                        $sheet->cell('A2', function ($cell) {
                            // manipulate the cel
                            $cell->setValue("ESTADO DE RESULTADO INTEGRAL");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('15');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('A3:F3');
                        $sheet->cell('A3', function ($cell) use ($periodo_hasta) {
                            // manipulate the cel
                            $cell->setValue("del 01 de enero al $periodo_hasta");
                            $cell->setFontWeight('bold');
                            $cell->setFontSize('12');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //$sheet->mergeCells('A4:A5');
                        $sheet->cell('A4', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CUENTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('B4:E4');
                        $sheet->cell('B4', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('DETALLE');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        // $sheet->mergeCells('C4:A5');
                        $sheet->cell('F4', function ($cell) {
                            // manipulate the cel
                            $cell->setValue('SALDO');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        // DETALLES

                        $sheet->setColumnFormat(array(
                            'F' => '0.00',
                        ));

                        $i = $this->setDetalles($ingresos, $sheet, 5);
                        $i = $this->setDetalles($costos, $sheet, $i);
                        $i = $this->setDetalles($gastos, $sheet, $i);
                        // $i = $this->setDetalles($pasivos, $sheet, $i);
                        // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" Utilidad / Perdida del Periodo ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $i . ':E' . $i);

                        $sheet->cell('F' . $i, function ($cell) use ($totpyg) {
                            $cell->setValue($totpyg);
                            if ($totpyg < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $par  = $i + 1;
                        $tf   = $i + 2;
                        $tfg  = $i + 3;
                        $ra   = $i + 4;
                        $totf = $i + 5;
                        $sheet->cell('A' . $par, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" 15% PARTICIPACION A TRABAJADORES: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $par . ':E' . $par);

                        $sheet->cell('F' . $par, function ($cell) use ($participacion) {
                            $cell->setValue($participacion);
                            if ($participacion < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('A' . $tf, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" UTILIDAD CONTABLE: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $tf . ':E' . $tf);

                        $sheet->cell('F' . $tf, function ($cell) use ($totalf) {
                            $cell->setValue($totalf);
                            if ($totalf < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('A' . $tfg, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" UTILIDAD GRAVABLE: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $tfg . ':E' . $tfg);

                        $sheet->cell('F' . $tfg, function ($cell) use ($total_gravable) {
                            $cell->setValue($total_gravable);
                            if ($total_gravable < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('A' . $ra, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" IMPUESTO GENERADO: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $ra . ':E' . $ra);

                        $sheet->cell('F' . $ra, function ($cell) use ($renta_acumulada) {
                            $cell->setValue($renta_acumulada);
                            if ($renta_acumulada < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('A' . $totf, function ($cell) {
                            // manipulate the cel
                            $cell->setValue(" UTILIDAD GRAVABLE: ");
                            $cell->setFontWeight('bold');
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A' . $totf . ':E' . $totf);

                        $sheet->cell('F' . $totf, function ($cell) use ($total) {
                            $cell->setValue($total);
                            if ($total < 0) {
                                $cell->setFontColor('#ff0000');
                            }
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        //  CONFIGURACION FINAL
                        $sheet->cells('A2:F2', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#0070C0');
                            // $cells->setFontSize('10');
                            $cells->setFontWeight('bold');
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            $cells->setValignment('center');
                        });

                        $sheet->cells('A4:F4', function ($cells) {
                            // manipulate the range of cells
                            $cells->setBackground('#cdcdcd');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize('12');
                        });

                        $sheet->setWidth(array(
                            'A' => 12,
                            'B' => 12,
                            'C' => 12,
                            'D' => 12,
                            'E' => 12,
                            'F' => 12,
                        ));
                    });
                })->export('xlsx');
            }
        }
    }
    public function setDetalles($data, $sheet, $i, $totpyg = "")
    {
        foreach ($data as $value) {
            $cont = substr_count($value['cuenta'], ".");
            if ($cont == 0) {
                $valor100 = $value['saldo'];
            }
            if ($value['saldo'] != 0) {
                $sheet->cell('A' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $cell->setValue(" " . $value['cuenta'] . " ");
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell);
                });

                $sheet->mergeCells('B' . $i . ':E' . $i);
                $sheet->cell('B' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $cell->setValue($value['nombre']);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $this->setSangria($cont, $cell, 1);
                });

                $sheet->cell('F' . $i, function ($cell) use ($value, $cont) {
                    // manipulate the cel
                    $this->setSangria($cont, $cell);
                    $cell->setValue($value['saldo']);
                    if ($value['saldo'] < 0) {
                        $cell->setFontColor('#ff0000');
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;
            }
        }
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);

        $sheet->cells('F5:F' . $i, function ($cells) {
            $cells->setAlignment('right');
        });
        return $i;
    }
    public function setSangria($cont, $cell, $indent = "")
    {
        switch ($cont) {
            case 0:
                $cell->setFontSize('12');
                $cell->setFontWeight('bold');
                break;
            case 1:
                $cell->setFontSize('11');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(2);
                }
                break;
            case 2:
                $cell->setFontSize('10');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(4);
                }
                break;
            case 3:
                $cell->setFontSize('10');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(6);
                }
                break;
            case 4:
                $cell->setFontSize('10');
                // $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(8);
                }
                break;
            default:
                $cell->setFontSize('10');
                // $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(10);
                }
                break;
        }
    }
    public function revisar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Asientos_Cabecera::findorfail($id);
        return view('contable/diario/asiento', ['registro' => $registro]);
    }
    public function crear()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $cuentas = plan_cuentas::where('estado', '2')->get();
        return view('contable/diario/create', ['cuentas' => $cuentas]);
    }
    public function buscar(Request $request)
    {
        $cuenta = plan_cuentas::find($request['nombre']);
        return view('contable/diario/unico', ['cuenta' => $cuenta, 'contador' => $request['contador']]);
    }
    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_asiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion'     => $request['observacion'],
            'fecha_asiento'   => $request['fecha_asiento'] . ' ' . date('H:i:s'),
            'valor'           => $request['total'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        for ($i = 1; $i <= $request['contador']; $i++) {
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento,
                'id_plan_cuenta'      => $request['id_plan' . $i],
                'debe'                => $request['debe' . $i],
                'haber'               => $request['haber' . $i],
                'fecha'               => $request['fecha_asiento'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
        }
        return redirect()->route('librodiario.index');
    }
    public function fechaTexto($fecha)
    {
        $fecha     = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia       = date('l', strtotime($fecha));
        $mes       = date('F', strtotime($fecha));
        $anio      = date('Y', strtotime($fecha));
        $dias_EN   = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $dias_ES   = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES  = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $meses_EN  = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        // return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
        return $numeroDia . " de " . $nombreMes . " de " . $anio;
    }
    public function modalcontableUtilidad(Request $request)
    {

        $desde = $request->fecha_desde;
        $hasta = $request->fecha_hasta;
        $mostrar_cierre = $request->mostrar_cierre;
        $mostrar_acumulado = $request->mostrar_acumulado;
        $cierre = 0;
        $afecha = null;
        $id_empresa = session("id_empresa");
        if ($mostrar_acumulado == 1) {

            $desde = substr($desde, 0, 4) . '-01-01';
        }

        $toting = collect(EstadoResultado::detalle_cuentas_contable($desde, $hasta, 'I', $afecha, 0, $cierre));
        $totcos = collect(EstadoResultado::detalle_cuentas_contable($desde, $hasta, 'C', $afecha, 0, $cierre));
        $totgas = collect(EstadoResultado::detalle_cuentas_contable($desde, $hasta, 'G', $afecha, 0, $cierre));
        $totingO = 0;
        if (!$toting->isEmpty()) {
            foreach ($toting["asientos"] as $asiento) {
                $diferencia = str_replace(',', '.', $asiento["debe"]) - str_replace(',', '.', $asiento["haber"]);
                $totingO += $diferencia;
            }
        }
        $totalDebeO = 0;
        if (!$totcos->isEmpty()) {
            foreach ($totcos["asientos"] as $asiento1) {
                $diferenciaq = str_replace(',', '.', $asiento1["debe"]) - str_replace(',', '.', $asiento1["haber"]);
                $totalDebeO += $diferenciaq;
            }
        }
        $totgasO = 0;
        if (!$totgas->isEmpty()) {
            foreach ($totgas["asientos"] as $asiento2) {
                $diferenciaw = str_replace(',', '.', $asiento2["debe"]) - str_replace(',', '.', $asiento2["haber"]);
                $totgasO += $diferenciaw;
            }
        }
        $total = 0;
        $total = ($totingO * -1) - ($totalDebeO + $totgasO);
        $resultados_combinados = [
            "ingreso" => $toting,
            "costo" => $totcos,
            "gasto" => $totgas,
            "totalIngreso" => $totingO,
            "totalCosto" => $totalDebeO,
            "totalGasto" => $totgasO,
        ];
        return view("contable/estado_resultados/modalcontableUtilidad", ["resultados_combinados" => $resultados_combinados, 'total' => $total]);
    }


    public function modalcontablegravable(Request $request)
    {

        $desde = $request->fecha_desde;
        $hasta = $request->fecha_hasta;
        $mostrar_cierre = $request->mostrar_cierre;
        $mostrar_acumulado = $request->mostrar_acumulado;
        $cierre = 0;
        $afecha = null;
        $id_empresa = session("id_empresa");
        if ($mostrar_acumulado == 1) {

            $desde = substr($desde, 0, 4) . '-01-01';
        }

        $toting = collect(EstadoResultado::detalle_cuentas_contable($desde, $hasta, 'I', $afecha, 1, $cierre));
        $totcos = collect(EstadoResultado::detalle_cuentas_contable($desde, $hasta, 'C', $afecha, 1, $cierre));
        $totgas = collect(EstadoResultado::detalle_cuentas_contable($desde, $hasta, 'G', $afecha, 1, $cierre));
        $totingO = 0;
        if (!$toting->isEmpty()) {
            foreach ($toting["asientos"] as $asiento) {
                $diferencia = str_replace(',', '.', $asiento["debe"]) - str_replace(',', '.', $asiento["haber"]);
                $totingO += $diferencia;
            }
        }
        $totalDebeO = 0;
        if (!$totcos->isEmpty()) {
            foreach ($totcos["asientos"] as $asiento1) {
                $diferenciaq = str_replace(',', '.', $asiento1["debe"]) - str_replace(',', '.', $asiento1["haber"]);
                $totalDebeO += $diferenciaq;
            }
        }
        $totgasO = 0;
        if (!$totgas->isEmpty()) {
            foreach ($totgas["asientos"] as $asiento2) {
                $diferenciaw = str_replace(',', '.', $asiento2["debe"]) - str_replace(',', '.', $asiento2["haber"]);
                $totgasO += $diferenciaw;
            }
        }
        $total = ($totingO * -1) - ($totalDebeO + $totgasO);
        $resultados_combinados = [
            "ingreso" => $toting,
            "costo" => $totcos,
            "gasto" => $totgas,
            "totalIngreso" => $totingO,
            "totalCosto" => $totalDebeO,
            "totalGasto" => $totgasO,
        ];

        return view("contable/estado_resultados/modalcontableGravable", ["resultados_combinados" => $resultados_combinados, 'total' => $total]);
    }
}
