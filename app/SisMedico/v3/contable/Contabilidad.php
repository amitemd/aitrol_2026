<?php

namespace Sis_medico\v3\contable;

use DateTime;
use Dompdf\Dompdf;
use Exception;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Empresa;
use Sis_medico\v3\contable\LogAsiento;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\v3\contable\Usuarios;
use Sis_medico\v3\contable\ComprobanteEgreso;
use Sis_medico\v3\contable\ComprobanteEgresoVarios;
use Sis_medico\v3\contable\EgresosMasivos;
use Sis_medico\v3\contable\ComprobanteIngreso;
use Sis_medico\v3\contable\DepositoBancario;
use Sis_medico\v3\contable\NotaDebitoBancaria;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\ClientesRetenciones;
use Sis_medico\v3\contable\DetalleDepositoBancario;
use Sis_medico\v3\contable\TransferenciasBancarias;
use Sis_medico\v3\contable\AsientosDetalles;
use Sis_medico\v3\contable\NotadeDebito;
use Sis_medico\v3\contable\NotaCredito;
use Sis_medico\v3\contable\DebitoAcreedores;
use Sis_medico\v3\contable\CruceValores;
use Sis_medico\v3\contable\CreditoAcreedores;
use Sis_medico\v3\contable\Ventas;
use Sis_medico\v3\contable\CruceValoresClientes;
use Sis_medico\v3\contable\ChequePost;
use Sis_medico\v3\contable\CruceCuentasClientes;
use Sis_medico\v3\contable\NotaCreditoClientes;

class Contabilidad extends Model
{
    public static function excelResultadoMensual($r)
    {
        $fecha_desde = Utilidades::convertidorFecha($r['d']);
        $fecha_hasta_ = Utilidades::convertidorFecha($r['h']);
        $fecha_hasta = date("Y-m-d", strtotime($fecha_hasta_ . "+ 1 days"));
        $id_empresa   = $r->session()->get('id_empresa');
        $empresa = Empresas::where('id', $id_empresa)->first();
        $anio = date('Y', strtotime($r->d));
        $sql = "SELECT 
                p.id_padre, 
                p.id, 
                pe.id_empresa AS id_empresa, 
                pe.plan, 
                p.nombre, 
                " . $anio . " AS anio, 
                COALESCE(SUM(ad.debe), 0) AS debe, 
                COALESCE(SUM(ad.haber), 0) AS haber, 
                IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS saldo_deudor, 
                IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS saldo_acreedor, 
                (IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) - IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)) AS saldo, 
                IF(SUBSTRING(pe.plan, 1, 1) = '4', 'INGRESOS', IF(SUBSTRING(pe.plan, 1, 3) = '5.1', 'COSTOS', 'GASTOS')) AS tipo, 
                pe.estado,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 1 THEN ad.debe - ad.haber END), 0.00) AS enero,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 2 THEN ad.debe - ad.haber END), 0.00) AS febrero,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 3 THEN ad.debe - ad.haber END), 0.00) AS marzo,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 4 THEN ad.debe - ad.haber END), 0.00) AS abril,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 5 THEN ad.debe - ad.haber END), 0.00) AS mayo,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 6 THEN ad.debe - ad.haber END), 0.00) AS junio,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 7 THEN ad.debe - ad.haber END), 0.00) AS julio,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 8 THEN ad.debe - ad.haber END), 0.00) AS agosto,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 9 THEN ad.debe - ad.haber END), 0.00) AS septiembre,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 10 THEN ad.debe - ad.haber END), 0.00) AS octubre,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 11 THEN ad.debe - ad.haber END), 0.00) AS noviembre,
                IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 12 THEN ad.debe - ad.haber END), 0.00) AS diciembre,
                (
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 1 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 2 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 3 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 4 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 5 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 6 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 7 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 8 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 9 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 10 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 11 THEN ad.debe - ad.haber END), 0) +
                    IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) = 12 THEN ad.debe - ad.haber END), 0)
                ) AS total
            FROM 
                plan_cuentas AS p 
                INNER JOIN plan_cuentas_empresa AS pe ON p.id = pe.id_plan AND pe.id_empresa = " . session('id_empresa') . " 
                LEFT JOIN ct_asientos_detalle AS ad ON pe.id_plan = ad.id_plan_cuenta 
                LEFT JOIN ct_asientos_cabecera AS ac ON ad.id_asiento_cabecera = ac.id AND ac.id_empresa = " . session('id_empresa') . "
            WHERE 
                SUBSTRING(pe.plan, 1, 1) IN (4, 5) 
                AND ac.fecha_asiento BETWEEN '" . $r->d . " 00:00:00' AND '" . $r->h . " 23:59:59' 
            GROUP BY pe.plan;";
        $estadoIntegral = DB::select($sql);
        $tabla = '';
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
                    <div>
                        <div>
                            <img src="' . base_path() . '/storage/app/logo/' . $empresa->logo . '" alt="logo" />
                        </div>
                        <br/><br/><br/>
                        <div>
                            <span /><strong>Estado de resultado integral de la empresa ' . $empresa->nombrecomercial . ' del dia ' . $fecha_desde . ' hasta ' . $fecha_hasta_ . '</strong></span>
                        </div>
                    </div>
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
                if ($val == 'INGRESOS') {
                    $arrayIngreso++;
                }
                if ($val == 'COSTOS') {
                    $arrayCosto++;
                }
                if ($val == 'GASTOS') {
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
                if ($row->tipo == 'INGRESOS') {
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
                    $ingresos_total += $row->total;
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
                if (substr($row->plan, 0, 1) > 4 && $arrayIngreso == $ingreso1 && $countiN < 1) {
                    $tabla .= $tablaTotalIngresos;
                    $countiN++;
                }
                if ($row->tipo == 'COSTOS') {
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
                if (substr($row->plan, 0, 3) >= '5.2' && $arrayCosto == $costo1 && $countiN1 < 1) {
                    $tabla .= $tablaTotalCostos;
                    $countiN1++;
                }
                if ($row->tipo == 'GASTOS') {
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
            $utilidadBruta = ($ingresos_total * -1) - ($costos_total + $gastos_total);
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
                <td align="right"><b>' . number_format($utilidadBruta, 2, ',', '') . '</b></td>
                </tr>';
            $deEmpresa = DeEmpresas::where('id_empresa', $empresa->id)->first();
            $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime($fecha_desde)));

            $participacionTrabajadores = 0;
            if ($utilidadBruta > 0)
                $participacionTrabajadores = $utilidadBruta * 0.15;
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
            $utilidadContable = $utilidadBruta - $participacionTrabajadores;
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
            $total_gravable = self::utilidad_gravable_2($fecha_desde, $fecha_hasta, $id_empresa, null, 0, 0);
            $utilidadGravable = $total_gravable;
            if ($utilidadBruta > 0) {
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
            }
            $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $fecha_desde, $fecha_hasta_, $utilidadBruta, $participacionTrabajadores, $ingresos_total, $utilidadGravable);
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
            if ($r->r == 'xls') {
                /*header("Expires: 0");
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=$filename.xls");
                header("Pragma: no-cache");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");*/
                echo $tabla;
                exit;
            } elseif ($r->r == 'pdf') {
                $data['fecha_desde'] = $fecha_desde;
                $data['fecha_hasta'] = $fecha_hasta;
                $data['fecha_hasta_'] = $fecha_hasta_;
                $data['estadoIntegral'] = $estadoIntegral;
                $data['reporte_mensual'] = $r->r;
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
    public static function utilidad_gravable_2($desde, $hasta, $id_empresa, $afecha = null, $mostrar_cierre = 0, $mostrar_acumulado = 0)
    {
        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01';
        }
        $cierre = DB::table('log_cierre_de_anio')
            ->where('id_empresa', $id_empresa)
            ->where('fecha_asiento', '>', $desde . ' 00:00:00')
            ->where('fecha_asiento', '<', $hasta . ' 00:00:00')
            ->orderBy('fecha_asiento', 'Desc')
            ->first();
        if (!is_null($cierre)) {
            $desde = date("Y-m-d", strtotime($cierre->fecha_asiento . "+ 1 days"));
        }
        $totpyg = 0;
        $toting = 0;
        $totcos = 0;
        $totgas = 0;
        $toting = self::detalle_total_cuenta($desde, $hasta, 'I', $afecha, 1, $mostrar_cierre);
        $totcos = self::detalle_total_cuenta($desde, $hasta, 'C', $afecha, 1, $mostrar_cierre);
        $totgas = self::detalle_total_cuenta($desde, $hasta, 'G', $afecha, 1, $mostrar_cierre);
        $totpyg = $toting - ($totcos + $totgas);
        $empleados = Nomina::where('id_empresa', $id_empresa)->where('fecha_ingreso', '<=', $hasta)->count();
        $trabajadores = 0;
        if ($empleados > 0) {
            $trabajadores = self::trabajadores($desde, $hasta, $id_empresa, $afecha);
            if ($totpyg < 0) {
                $trabajadores = 0;
            }
        }
        $total = $totpyg - $trabajadores;
        $cierre_2 = DB::table('log_cierre_de_anio')
            ->where('id_empresa', $id_empresa)
            ->where('fecha_asiento', $hasta . ' 23:59:59')
            ->orderBy('fecha_asiento', 'Desc')
            ->first();
        if (!is_null($cierre_2) && $mostrar_cierre == 0) {
            return 0;
        }
        return $total;
    }
    public static function kardex_asiento($idVenta)
    {
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $idusuario       = Auth::user()->id;
        $detalle_asiento = Ct_detalle_venta::where('id_ct_ventas', $idVenta)->get();
        $coste = 0;
        $dangers = "";
        DB::beginTransaction();
        //$msj = "no";
        try {
            foreach ($detalle_asiento as $x) {
                $id_producto = $x->id_ct_productos;
                $xx = Ct_productos::where('codigo', $id_producto)->first();
                $verificar = Ct_productos_insumos::where('id_producto', $xx->id)->first();
                if (!is_null($verificar)) {
                    $producto = Producto::find($verificar->id_insumo);
                    if (!is_null($producto)) {
                        $inventario = InvInventario::where('id_producto', $producto->id)->first(); //bodega
                        if (!is_null($inventario)) {
                            $coste += $inventario->costo_promedio;
                        } else {
                            $dangers .= "No tiene productos en esta bodega -" . $x->bodega . " Codigo:  " . $producto->codigo;
                        }
                    }
                } else {
                    $dangers .= "No existe ligue con el producto -" . $id_producto;
                }
            }
            if ($coste == 0) {
                $coste = 1; //arreglar
            }
            $id_empresa   = session()->get('id_empresa');
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId([
                'fecha_asiento'   => date('Y-m-d H:i:s'),
                'fact_numero'     => '0',
                'id_empresa'      => $id_empresa,
                'observacion'     => 'COSTES DE MERCADERIA POR PRODUCTO',
                'valor'           => $coste,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
            $plan_cuentas = Ct_Globales::where('id_modulo', '2')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_cuentas->haber,
                'descripcion'         => $plan_cuentas->haberc->nombre,
                'fecha'               => date('Y-m-d H:i:s'),
                'haber'                => '0',
                'debe'               => $coste,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_cuentas->debe,
                'descripcion'         => $plan_cuentas->debec->nombre,
                'fecha'               => date('Y-m-d H:i:s'),
                'debe'               => '0',
                'haber'                => $coste,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $dangers = $e->getMessage();
        }

        $response['state'] = '1';
        $response['observations'] = $dangers;
        return $response;
    }
    public static function trabajadores($desde, $hasta, $id_empresa, $afecha = null, $trabajadores = [], $cierre = 0, $mostrar_acumulado = 0)
    {
        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01 00:00:00';
        }
        $anio1 = date('Y', strtotime($desde));
        $anio2 = date('Y', strtotime($hasta));
        if ($afecha != 2) {
            $anio1 = date('Y', strtotime($desde));
            $anio2 = date('Y', strtotime($hasta));
        } else {
            $anio1  = $desde;
            $anio2  = $hasta;
            $desde  = $desde . '-01-01';
            $hasta  = $hasta . '-12-31';
            $afecha = null;
        }
        $anios = $anio2 - $anio1;
        $datos           = 0;
        $anio_inicio     = $anio1;
        $renta_acumulada = 0;
        $t_trabajadores  = 0;
        for ($i = 0; $i <= $anios; $i++) {
            $fecha_valida1 = $anio_inicio . '-01-01';
            $fecha_valida2 = $anio_inicio . '-12-31';
            if ($desde >= $fecha_valida1) {
                $fecha_buscar_desde = $desde;
            } else {
                $fecha_buscar_desde = $fecha_valida1;
            }
            if ($hasta <= $fecha_valida2) {
                $fecha_buscar_hasta = $hasta;
            } else {
                $fecha_buscar_hasta = $fecha_valida2;
            }
            $totpyg = 0;

            if ($cierre == 1 && $fecha_buscar_hasta == $hasta) {
                $toting = self::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'I', $afecha, 0, $cierre);
                $totcos = self::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'C', $afecha, 0, $cierre);
                $totgas = self::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'G', $afecha, 0, $cierre);
            } else {
                $toting = self::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'I', $afecha);
                $totcos = self::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'C', $afecha);
                $totgas = self::detalle_total_cuenta($fecha_buscar_desde, $fecha_buscar_hasta, 'G', $afecha);
            }
            $totpyg_veri = $toting - ($totcos + $totgas);
            $empleados = Nomina::where('id_empresa', $id_empresa)->where('fecha_ingreso', '<=', $fecha_buscar_hasta)->count();
            $totpyg    = 0;
            $total = 0;
            if ($empleados > 0) {
                $totpyg = round(($toting - ($totcos + $totgas)) * 0.15, 2);
                $total  = ($toting - ($totcos + $totgas));
                if ($total < 0) {
                    $totpyg = 0;
                }
            }
            $t_trabajadores += $totpyg;
            $anio_inicio++;
        }
        return $t_trabajadores;
    }
    public static function detalle_total_cuenta($desde, $hasta, $tipo, $afecha = null, $tipo_2 = 0, $cierre = 0, $mostrar_acumulado = 0)
    {
        $id_empresa = Session::get('id_empresa');
        $balance = array();
        if ($tipo == 'I') {
            $condicion = '4';
        } elseif ($tipo == 'C') {
            $condicion = '5';
        } else {
            $condicion = '6';
        }
        if ($mostrar_acumulado == 1) {
            $desde = substr($desde, 0, 4) . '-01-01 00:00:00';
        }
        $plans = Plan_Cuentas::where('p.estado', '<>', 0)
            ->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')
            ->whereRaw('character_length(p.plan) = 1')
            ->where('p.plan', "$condicion")
            ->where('p.id_empresa', $id_empresa)
            ->select('p.plan as id', 'p.nombre as nombre', 'plan_cuentas.naturaleza', 'plan_cuentas.naturaleza_2')
            ->orderBy('p.plan', 'asc')
            ->get();
        $i = 0;
        $saldo = 0;
        $lastDay = date('t', strtotime("$hasta-12"));
        if ($afecha == '2') {
            $desde = "$desde-01-01";
            $hasta = "$hasta-12-31";
        }
        if ($cierre == 1) {
            $hasta = $hasta . ' 23:59:58';
        } else {
            $hasta = $hasta . ' 23:59:59';
        }
        foreach ($plans as $plan) {
            // $saldo = 0;
            if ($plan->id != "") {
                $saldo2  = 0;
                $asiento = AsientosDetalles::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas_empresa as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id_plan')
                    ->where('p.plan', 'like', $plan->id . '%')
                    ->where('p.id_empresa', $id_empresa)
                    ->where('c.id_empresa', $id_empresa);
                if ($tipo_2 == 1) {
                    $asiento = $asiento->where('aparece_sri', 1);
                }
                if (strpos($plan->nombre, '-)') == false) {
                    $asiento  = $asiento->where('p.nombre', 'not like', '%-)%');
                    $asiento2 = AsientosDetalles::join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas_empresa as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id_plan')
                        ->where('p.plan', 'like', $plan->id . '%')
                        ->where('p.id_empresa', $id_empresa)
                        ->where('c.id_empresa', $id_empresa)
                        ->where('p.nombre', 'like', '%-)%')
                        ->whereBetween('c.fecha_asiento', ["$desde 00:00:00", "$hasta"]);
                    if ($tipo_2 == 1) {
                        $asiento2 = $asiento2->where('aparece_sri', 1);
                    }
                    if ($plan->naturaleza_2 == 1) {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                            ->get();
                    } else {
                        $asiento2 = $asiento2->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                            ->get();
                    }
                    foreach ($asiento2 as $row) {
                        $saldo2 = $row->saldo;
                    }
                }
                $asiento = $asiento->whereBetween('c.fecha_asiento', [$desde . ' 00:00:00', "$hasta"]);
                if ($plan->naturaleza_2 == 1) {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(haber-debe),0) as saldo'))
                        ->get();
                } else {
                    $asiento = $asiento->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();
                }
                // $saldo = 0;

                foreach ($asiento as $row) {
                    $saldo = $row->saldo - $saldo2;
                }
            }
        }
        return $saldo;
    }
    public static function asiento($request, $sucursal)
    {
        $arrayRespuesta = [];
        try {
            $input_cabecera = [
                'observacion'     => $request['concepto'],
                'fecha_asiento'   => $request['fecha_asiento'],
                'id_empresa'      => $request['empresa'],
                'valor'           => $request['total'],
                'id_usuariocrea'  => $request['id_usuariocrea'],
                'id_usuariomod'   => $request['id_usuariomod'],
                'ip_creacion'     => $request['ip_creacion'],
                'ip_modificacion' => $request['ip_modificacion'],
                'sucursal'        => $sucursal,
            ];
            $id_asiento_cabecera = AsientosCabecera::insertGetId($input_cabecera);
            $asiento_detalle = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $request['id_cuenta_origen'],
                'descripcion'         => $request['glosa'],
                'fecha'               => $request['fecha_asiento'],
                'haber'               => $request['total'],
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $request['id_usuariocrea'],
                'id_usuariomod'       => $request['id_usuariomod'],
                'ip_creacion'         => $request['ip_creacion'],
                'ip_modificacion'     => $request['ip_modificacion'],
            ];
            AsientosDetalles::insert($asiento_detalle);
            if ($request['comision_tarjeta'] != 0) {
                $id_plan_confg = LogConfig::busqueda('5.2.03.03.03');
                $planCuentas = Plan_Cuentas::find($id_plan_confg);
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $id_plan_confg,
                    'descripcion'         => $request['glosa'],
                    'fecha'               => $request['fecha_asiento'],
                    'debe'                => $request['comision_tarjeta'],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $request['id_usuariocrea'],
                    'id_usuariomod'       => $request['id_usuariomod'],
                    'ip_creacion'         => $request['ip_creacion'],
                    'ip_modificacion'     => $request['ip_modificacion'],
                ];
                AsientosDetalles::insert($asiento_detalle);
            }
            if ($request['retencion_fuente'] != 0) {
                //TODO: Asiento con el movimento del debe + (Retencion a la fuente)
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request['id_base_ret_fuente'], // cuenta de retencion a la fuente
                    'descripcion'         => $request['glosa'],
                    'fecha'               => $request['fecha_asiento'],
                    'debe'                => $request['retencion_fuente'],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $request['id_usuariocrea'],
                    'id_usuariomod'       => $request['id_usuariomod'],
                    'ip_creacion'         => $request['ip_creacion'],
                    'ip_modificacion'     => $request['ip_modificacion'],
                ];
                AsientosDetalles::insert($asiento_detalle);
            }
            if ($request['iva_retenido'] != 0) {
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request['id_base_iva'], // cuenta de retencion a la fuente
                    'descripcion'         => $request['glosa'],
                    'fecha'               => $request['fecha_asiento'],
                    'debe'                => $request['iva_retenido'],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $request['id_usuariocrea'],
                    'id_usuariomod'       => $request['id_usuariomod'],
                    'ip_creacion'         => $request['ip_creacion'],
                    'ip_modificacion'     => $request['ip_modificacion'],
                ];
                AsientosDetalles::insert($asiento_detalle);
            }
            $asiento_detalle = [
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $request['id_cuenta_destino'],
                'descripcion'         => $request['glosa'],
                'fecha'               => $request['fecha_asiento'],
                'haber'               => '0',
                'debe'                => $request['valor_destino'],
                'estado'              => '1',
                'id_usuariocrea'      => $request['id_usuariocrea'],
                'id_usuariomod'       => $request['id_usuariomod'],
                'ip_creacion'         => $request['ip_creacion'],
                'ip_modificacion'     => $request['ip_modificacion'],
            ];
            AsientosDetalles::insert($asiento_detalle);
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 500,
                'message' => 'Guardado Correctamente',
                'id_asiento' => $id_asiento_cabecera,
                'data' => $request,
            ];
        } catch (\Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $request,
            ];
        }
        return $arrayRespuesta;
    }
    public static function getLibroMayor($r)
    {
        $sql = '';
        $sql_ = '';
        $id_empresa = session('id_empresa');
        $cuentas = Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->get();
        $desde = $r->d == '' ? '2010-01-01' : $r->d;
        $desde = date('Y-m-d', strtotime(str_replace('/', '-', $desde)));
        $hasta = date('Y-m-d', strtotime(str_replace('/', '-', $r->h)));
        if (substr($r->cuentadesde, 0, 1) == 1 || substr($r->cuentadesde, 0, 1) == 2 || substr($r->cuentadesde, 0, 1) == 3) {
            $sql_ = "(select IFNULL(ac.fecha_asiento,'') as fecha_asiento,
                'Saldo anterior' as informacion,
                IFNULL(pe.plan,'') as plan,'' as id_asiento_cabecera,IFNULL(pe.nombre,'Saldo anterior') as nombre,'Saldo anterior' as observacion,IFNULL(sum(ad.debe),0) as debe,IFNULL(sum(ad.haber),0) as haber,IFNULL(pe.naturaleza,'') as naturaleza,IFNULL(pe.naturaleza_2,'') as naturaleza_2,'' as identificacion,'' as socio
                from ct_asientos_detalle ad 
                join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id 
                left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa 
                left join plan_cuentas pc on ad.id_plan_cuenta=pc.id 
                where ac.id_empresa=$id_empresa 
                 and ac.fecha_asiento>='2010-01-01' 
                and ac.fecha_asiento<='$desde' 
                and pe.plan like '$r->cuentadesde%' 
                group by pe.plan) UNION ALL";
        }
        $sql = $sql_ . "(select ac.fecha_asiento,
        /*COMPRAS*/
        IF((select IFNULL(id,0) from ct_compras where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'COMPRAS',
        IF((select IFNULL(id,0) from ct_retenciones where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'RETENCION-A',
        IF((select IFNULL(id,0) from ct_comprobante_egreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'EGRESO-A',
        IF((select IFNULL(id,0) from ct_comprobante_egreso_varios where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'EGRESOV-A',
        IF((select IFNULL(id,0) from ct_comprobante_egreso_masivo where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'EGRESOM-A',
        IF((select IFNULL(id,0) from ct_debito_bancario where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'DEBITOB-A',
        IF((select IFNULL(id,0) from ct_debito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'DEBITOAC-A',
        IF((select IFNULL(id,0) from ct_cruce_valores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CRUCE-A',
        IF((select IFNULL(id,0) from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CREDITOA-A',
        /*COMPRAS*/ 
        /*VENTAS*/ 
        IF((select IFNULL(id,0) from ct_ventas where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'VENTA',
        IF((select IFNULL(id,0) from ct_comprobante_ingreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'INGRESO-C',
        IF((select IFNULL(id,0) from ct_cliente_retencion where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'RETENCION-C',
        IF((select IFNULL(id,0) from ct_cruce_valores_cliente where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CRUCE-C',
        IF((select IFNULL(id,0) from ct_cheques_post where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CHEQUEPOST-C',
        IF((select IFNULL(id,0) from ct_cruce_cuentas_clientes where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CRUCECUENTAS-C',
        IF((select IFNULL(id,0) from ct_nota_credito_clientes where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CREDITOCLIENTES-C',
        /*VENTAS*/
        /*BANCOS*/
        IF((select IFNULL(id,0) from ct_nota_debito_cabecera where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'NOTA DE DEBITO-B',
        IF((select IFNULL(id,0) from ct_nota_credito_cabecera where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'NOTA DE CREDITO-B',
        IF((select IFNULL(id,0) from ct_transferencia_bancaria where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'TRANSFERENCIA-B',
        IF((select IFNULL(id,0) from ct_deposito_bancario where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'DEPOSITO-B','')
        )))
        /*BANCOS*/
        ))))))))))))))))
        as informacion,pe.plan,ad.id_asiento_cabecera,pe.nombre,ac.observacion,sum(ad.debe) as debe, sum(ad.haber) as haber,
        pe.naturaleza,pe.naturaleza_2,
        IF((select IFNULL(id,0) from ct_comprobante_egreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select id_proveedor from ct_comprobante_egreso where id_asiento_cabecera=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_compras where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select proveedor from ct_compras where id_asiento_cabecera=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_debito_bancario where id_asiento=ad.id_asiento_cabecera limit 1)!=0,(select id_acreedor from ct_debito_bancario where id_asiento=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select id_proveedor from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_ventas where id_asiento=ad.id_asiento_cabecera limit 1)!=0,(select id_cliente from ct_ventas where id_asiento=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_comprobante_ingreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select id_cliente from ct_comprobante_ingreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1),'')
        ))))) as identificacion,
        IF((select IFNULL(id,0) from ct_comprobante_egreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select IFNULL(nombrecomercial,razonsocial) from ct_comprobante_egreso join proveedor on proveedor.id=ct_comprobante_egreso.id_proveedor where id_asiento_cabecera=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_compras where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select IFNULL(nombrecomercial,razonsocial) from ct_compras join proveedor on proveedor.id=ct_compras.proveedor where id_asiento_cabecera=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_debito_bancario where id_asiento=ad.id_asiento_cabecera limit 1)!=0,(select IFNULL(nombrecomercial,razonsocial) from ct_debito_bancario join proveedor on proveedor.id=ct_debito_bancario.id_acreedor where ct_debito_bancario.id_asiento=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select id_proveedor from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_ventas where id_asiento=ad.id_asiento_cabecera limit 1)!=0,(select nombre from ct_ventas join ct_clientes on id_cliente=ct_clientes.identificacion where id_asiento=ad.id_asiento_cabecera),
        IF((select IFNULL(id,0) from ct_comprobante_ingreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select nombre from ct_comprobante_ingreso join ct_clientes on id_cliente=ct_clientes.identificacion where id_asiento_cabecera=ad.id_asiento_cabecera limit 1),'')
        ))))) as socio
        from ct_asientos_detalle ad
        join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
        left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
        left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
        where ac.id_empresa=$id_empresa
        and ac.fecha_asiento>='$desde'
        and ac.fecha_asiento<='$hasta'
        and pe.plan like '$r->cuentadesde%'
        group by ad.id_asiento_cabecera
        order by ac.fecha_asiento)";
        $sql = 'select *,
        (SUM(debe - haber) OVER (ORDER BY fecha_asiento ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW)) AS saldo,
        (SUM(debe) OVER (ORDER BY fecha_asiento ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW)) AS total_debe,
        (SUM(haber) OVER (ORDER BY fecha_asiento ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW)) AS total_haber
        from(' . $sql . ') as info order by info.fecha_asiento asc';
        return DB::select($sql);
    }
    public static function sumarF($array, $cuentaBase)
    {
        $saldo1 = 0;
        $saldo2 = 0;

        $cuentaBaseLongitud = strlen($cuentaBase["plan"]);

        foreach ($array as $val) {
            if ($val->plan === $cuentaBase["plan"] || substr($val->plan, 0, $cuentaBaseLongitud) === $cuentaBase["plan"]) {
                $saldo1 += $val['saldo_deudor1'] - $val["saldo_acreedor1"]; //añio anterior
                $saldo2 += $val["saldo_deudor2"] - $val["saldo_acreedor2"]; //añio actual
            }
        }
        if (!$tieneHijas) {
            $saldo1 += $elementoActual['saldo_deudor1'] - $elementoActual["saldo_acreedor1"];
            $saldo2 += $elementoActual["saldo_deudor2"] - $elementoActual["saldo_acreedor2"];
        }

        return ["saldo1" => $saldo1, 'saldo2' => $saldo2];
    }
    public static function detalleRatiosFinanciero($r)
    {
        $perido = ($r->a - 1);
        $actual = $r->a;
        $tipo = $r->t;
        $arrayFechas = [
            'd' => $r->a . '-01-01',
            'h' => $r->a . '-12-31',
        ];
        $r = (object)$arrayFechas;

        $financiero1 = (Contabilidad::detalleBalanceFinanciero($r, 'return', 'financiero'));
        if (is_array($financiero1)) {
        } else {
            $financiero1 = (Contabilidad::detalleBalanceFinanciero($r, 'return', 'financiero'))->toArray();
        }
        $resultado1 = Contabilidad::detalleBalanceFinanciero($r, 'return', 'resultados');
        if (is_array($resultado1)) {
        } else {
            $resultado1 = (Contabilidad::detalleBalanceFinanciero($r, 'return', 'resultados'))->toArray();
        }
        $resultado1 = (Contabilidad::detalleBalanceFinanciero($r, 'return', 'resultados'))->toArray();
        $arrayFechas = [
            'd' => $perido . '-01-01',
            'h' => $perido . '-12-31',
        ];
        $r = (object)$arrayFechas;
        $financiero2 = Contabilidad::detalleBalanceFinanciero($r, 'return', 'financiero');
        if (is_array($financiero2)) {
        } else {
            $financiero2 = (Contabilidad::detalleBalanceFinanciero($r, 'return', 'financiero'))->toArray();
        }
        $resultado2 = Contabilidad::detalleBalanceFinanciero($r, 'return', 'resultados');
        if (is_array($resultado2)) {
        } else {
            $resultado2 = (Contabilidad::detalleBalanceFinanciero($r, 'return', 'resultados'))->toArray();
        }

        if ($tipo == 'FI') {
            $new_Financiero = [];
            $arreglo_modificado = [];
            foreach ($financiero2 as &$item) {
                $key = $item->plan;
                $arreglo_modificado[$key] = $item;
            }
            foreach ($financiero1 as $val) {
                $search = array_search($val->plan, array_column($financiero2, "plan"));
                if ($search !== false) {
                    //  1.01.02.05.06
                    $new_Financiero[] = [
                        'plan' => $val->plan,
                        'nombre' => $val->nombre,
                        'saldo_deudor1' => $val->saldo_deudor,
                        'saldo_acreedor1' => $val->saldo_acreedor,
                        'saldo_deudor2' => $arreglo_modificado[array_column($arreglo_modificado, "plan")[$search]]->saldo_deudor,
                        'saldo_acreedor2' => $arreglo_modificado[array_column($arreglo_modificado, "plan")[$search]]->saldo_acreedor,
                    ];
                } else {
                    $new_Financiero[] = [
                        'plan' => $val->plan,
                        'nombre' => $val->nombre,
                        'saldo_deudor1' => $val->saldo_deudor,
                        'saldo_acreedor1' => $val->saldo_acreedor,
                        'saldo_deudor2' => 0,
                        'saldo_acreedor2' => 0,
                    ];
                }
            }

            $htmlFinanciero = ' <div style="display: flex; gap: 10px; justify-content: center;">
            <table class="table table-bordered table-striped" style="font-size: 10px;width: 80%;">
            <thead>
                <tr>
                    <th style="vertical-align:middle;">Cuentas</th>
                    <th style="text-align:center;">' . $perido . '</th>
                    <th style="text-align:center;">' . $actual . '</th>
                    <th style="text-align: center; padding:0px;" colspan="2"><table style="width:100%;"><tr><td colspan="2">Analisis horizontal</td></tr><tr><td id="valABS">Valor ABS</td><td id="valREL">Valor REL</td></tr></table></th>
                    <th style="text-align: center;" colspan="2">Analisis vertical</th>
                </tr>
            </thead>
            <tbody>';
            foreach ($new_Financiero as $key => $row) {
                // 0   , 
                $sumado = self::sumarF(array_slice($new_Financiero, $key, count($new_Financiero) - 1), $row);
                $valorABS = 0;
                $valorREL = 0;
                $saldo1 = $row['saldo_deudor1'] - $row["saldo_acreedor1"]; //añio anterior
                $saldo2 = $row["saldo_deudor2"] - $row["saldo_acreedor2"]; //añio actual
                try {
                    $valorABS = $saldo1 - $saldo2;
                } catch (Exception $e) {
                }
                try {
                    $valorREL = ($valorABS / $saldo2) * 100;
                } catch (Exception $e) {
                    $valorREL = 0;
                }
                $htmlFinanciero .= '<tr>
                <td>' . $row["nombre"] . '</td>
                <td style="text-align:right;">' . $sumado["saldo1"] . '</td>
                <td style="text-align:right;">' . $sumado["saldo2"] . '</td>
                <td style="text-align:right;width:150px;" class="valABS">' . $valorABS . '</td>
                <td style="text-align:right;width:150px;" class="valREL">' . number_format($valorREL, 2, ',', '.') . '%</td>
                <td style="text-align:right;"></td>
                <td style="text-align:right;"></td>
            </tr>';
            }
            $htmlFinanciero .= '</tbody>
            </table></div>
            <script>
            $(document).ready(function() {
                var valABS=$(\'.valABS\').width();
                var valREL=$(\'.valREL\').width();
                $(\'#valABS\').width(valABS);
                $(\'#valREL\').width(valREL);
            });
            </script>';
        } elseif ($tipo == 'RE') {
            $new_Resultados = [];
            foreach ($resultado1 as $val) {
                $search = array_search($val->plan, array_column($resultado2, "plan"));
                if ($search !== false) {
                    $new_Resultados[] = [
                        'nombre' => $val->nombre,
                        'saldo_deudor1' => $val->deudor,
                        'saldo_acreedor1' => $val->acreedor,
                        'saldo_deudor2' => $resultado2[$search]->deudor,
                        'saldo_acreedor2' => $resultado2[$search]->acreedor,
                    ];
                } else {
                    $new_Resultados[] = [
                        'nombre' => $val->nombre,
                        'saldo_deudor1' => $val->deudor,
                        'saldo_acreedor1' => $val->acreedor,
                        'saldo_deudor2' => 0,
                        'saldo_acreedor2' => 0,
                    ];
                }
            }
            $htmlResuldados = '<div style="display: flex; gap: 10px; justify-content: center;">
            <table class="table table-bordered table-striped" style="font-size: 10px;width: 80%;">
            <thead>
                <tr>
                    <th style="vertical-align:middle;">Cuentas</th>
                    <th style="text-align:center;">' . $perido . '</th>
                    <th style="text-align:center;">' . $actual . '</th>
                    <th style="text-align: center; padding:0px;" colspan="2"><table style="width:100%;"><tr><td colspan="2">Analisis horizontal</td></tr><tr><td id="valABS">Valor ABS</td><td id="valREL">Valor REL</td></tr></table></th>
                    <th style="text-align: center;" colspan="2">Analisis vertical</th>
                </tr>
            </thead>
            <tbody>';
            foreach ($new_Resultados as $row) {
                $valorABS = 0;
                $valorREL = 0;
                $saldo1 = $row['saldo_deudor1'] - $row["saldo_acreedor1"];
                $saldo2 = $row["saldo_deudor2"] - $row["saldo_acreedor2"];
                try {
                    $valorABS = $saldo1 - $saldo2;
                } catch (Exception $e) {
                }
                try {
                    $valorREL = ($valorABS / $saldo2) * 100;
                } catch (Exception $e) {
                    $valorREL = 0;
                }
                $htmlResuldados .= '<tr>
                <td>' . $row["nombre"] . '</td>
                <td style="text-align:right;">' . $saldo2 . '</td>
                <td style="text-align:right;">' . $saldo1 . '</td>
                <td style="text-align:right;width:150px;" class="valABS">' . $valorABS . '</td>
                <td style="text-align:right;width:150px;" class="valREL">' . number_format($valorREL, 2, ',', '.') . '%</td>
                <td style="text-align:right;"></td>
                <td style="text-align:right;"></td>
            </tr>';
            }
            $htmlResuldados .= '</tbody>
            </table></div>
            <script>
            $(document).ready(function() {
                var valABS=$(\'.valABS\').width();
                var valREL=$(\'.valREL\').width();
                $(\'#valABS\').width(valABS);
                $(\'#valREL\').width(valREL);
            });
            </script>';
            echo $htmlResuldados;
            exit;
        } else {
            $htmlRatios = '';
            echo $htmlRatios;
            exit;
        }
    }
    public static function valorUtilidad()
    {
        $arrayParametros = [
            'd' => session('periodo') . '-01-01',
            'h' => session('periodo') . '-12-31',
        ];
        return Contabilidad::getBalanceResultadosMensual((object)$arrayParametros, 'return');
    }
    public static function getBalanceResultadosMensual($r)
    {
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', $r->d);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        return DB::table('plan_cuentas as p')
            ->select(
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=1 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) ene'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=2 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) feb'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=3 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) mar'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=4 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) abr'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=5 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) may'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=6 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) jun'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=7 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) jul'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=8 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) ago'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=9 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) sep'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=10 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) oct'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=11 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) nov'),
                DB::raw('IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento)=12 THEN (IF(ad.debe < ad.haber, ad.haber - ad.debe, 0)-IF(ad.debe > ad.haber, ad.debe - ad.haber, 0)) END),0.00) dic')
            )
            ->leftJoin('plan_cuentas_empresa as pe', 'p.id', '=', 'pe.id_plan')
            ->leftJoin('ct_asientos_detalle as ad', 'p.id', '=', 'ad.id_plan_cuenta')
            ->leftJoin('ct_asientos_cabecera as ac', 'ad.id_asiento_cabecera', '=', 'ac.id')
            ->where('pe.id_empresa', $empresa)
            ->where('ac.id_empresa', $empresa)
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), [4, 5])
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->whereNotNull('pe.plan')
            ->get();
    }
    public static function traerTodo($array, $cuentaBase)
    {
        $acumulador = 0;
        $cuentaBaseLongitud = strlen($cuentaBase);
        foreach ($array as $val) {
            if ($val->plan === $cuentaBase || substr($val->plan, 0, $cuentaBaseLongitud) === $cuentaBase) {
                $acumulador += $val->saldo;
            }
        }
        return $acumulador;
    }
    public static function getFechaInicioSistema()
    {
        $empresa = session('id_empresa');
        $fecha = DB::table('ct_asientos_cabecera')
            ->where('id_empresa', $empresa)
            ->whereNotNull('fecha_asiento')
            ->orderBy('fecha_asiento', 'asc')
            ->value('fecha_asiento');
        return $fecha ? date('Y-m-d', strtotime($fecha)) : '2010-01-01';
    }
    public static function updateSaldos(&$data)
    {
        $index = [];
        // Convertir la estructura a un índice por id para facilitar el acceso
        foreach ($data as $item) {
            $index[$item->id] = $item;
        }

        // Función recursiva para actualizar los saldos
        function sumarSaldos($id, &$index)
        {
            $debe = 0.0;
            $haber = 0.0;
            $saldo_deudor = 0.0;
            $saldo_acreedor = 0.0;

            foreach ($index as $item) {
                if ($item->id_padre === $id) {
                    $resultados_hijos = sumarSaldos($item->id, $index);
                    $debe += $resultados_hijos['debe'] + $item->debe;
                    $haber += $resultados_hijos['haber'] + $item->haber;
                    $saldo_deudor += $resultados_hijos['saldo_deudor'] + $item->saldo_deudor;
                    $saldo_acreedor += $resultados_hijos['saldo_acreedor'] + $item->saldo_acreedor;
                }
            }

            if (isset($index[$id])) {
                $index[$id]->debe += $debe;
                $index[$id]->haber += $haber;
                $index[$id]->saldo_deudor += $saldo_deudor;
                $index[$id]->saldo_acreedor += $saldo_acreedor;
            }

            return [
                'debe' => $debe,
                'haber' => $haber,
                'saldo_deudor' => $saldo_deudor,
                'saldo_acreedor' => $saldo_acreedor
            ];
        }

        // Comenzar desde los nodos raíz (sin id_padre)
        foreach ($index as $item) {
            if (empty($item->id_padre)) {
                sumarSaldos($item->id, $index);
            }
        }
    }
    public static function detalleBalanceFinanciero($r, $armar = '', $tipo = '')
    {
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', isset($r->d) ? $r->d : '');
        if (trim($desde) == '' || strtotime($desde) === false) {
            $desde = self::getFechaInicioSistema();
        }
        $desde = date('Y-m-d', strtotime($desde));
        $periodo = date('Y', strtotime(trim(isset($r->d) ? $r->d : '') == '' ? $r->h : $desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        $htmlActivos = '';
        $htmlPasivos = '';
        $htmlPatrimonio = '';
        $planCuenta = '';
        $totalActivos = 0;
        $totalActivos_ = 0;
        $totalPasivos = 0;
        $totalPasivo_ = 0;
        $totalPatrimonio = 0;
        $totalPatrimonio_ = 0;
        $utilidadGravable = 0;
        $participacionTrabajadores = 0;
        $utilidadPerdida = 0;
        $totalPorcentajeActivo = 0;
        $totalPorcentajePasivo = 0;
        $totalPorcentajePatrimonio = 0;
        $utilidadContable_ = 0;
        $utilidadContable = 0;
        $perdida = 0;
        $ganacia = 0;
        $impuestoPagar = 0;
        $trabajadores = 0;
        $datos = Contabilidad::getBalanceComprobacion($r, 'return');
        $totalActivos_ = $datos->where('tipo', 'ACTIVOS')->sum('saldo');
        $totalPasivo_ = $datos->where('tipo', 'PASIVOS')->sum('saldo');
        $totalPatrimonio_ = $datos->where('tipo', 'PATRIMONIO')->sum('saldo');
        $totalIngresos = $datos->where('tipo', 'INGRESOS')->sum('saldo');
        $totalCostos = $datos->where('tipo', 'COSTOS')->sum('saldo');
        $totalGastos = $datos->where('tipo', 'GASTOS')->sum('saldo');
        $datos_ = $datos;
        $index = [];
        foreach ($datos as $item) {
            $index[$item->id] = $item;
        }
        $datos = $index;
        $resultado = Contabilidad::getBalanceResultados($r, 'return');
        if ($armar != '') {
            if ($tipo == 'financiero')
                return $datos;
            elseif ($tipo == 'resultados')
                return $resultado;
        }
        $totalIngresos = $resultado->where('tipo', 'INGRESOS')->sum('saldo');
        $totalCostos = $resultado->where('tipo', 'COSTOS')->sum('saldo');
        $totalGastos = $resultado->where('tipo', 'GASTOS')->sum('saldo');
        $utilidadBruta = $totalIngresos + ($totalCostos + $totalGastos);
        if ($utilidadBruta > 0) {
            $participacionTrabajadores = $utilidadBruta * 0.15;
            $utilidadContable = $utilidadBruta - $participacionTrabajadores;
        }
        $utilidadContable_ = $utilidadContable;
        $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime(trim(isset($r->d) ? $r->d : '') == '' ? $r->h : $r->d)));
        if ($rentaEmpresa != '') {
            if ($rentaEmpresa->regimen_especial == 1) {
                $utilidadGravable = DB::table('plan_cuentas as p')
                    ->leftJoin('plan_cuentas_empresa as pe', 'p.id', '=', 'pe.id_plan')
                    ->leftJoin('ct_asientos_detalle as ad', 'p.id', '=', 'ad.id_plan_cuenta')
                    ->leftJoin('ct_asientos_cabecera as ac', 'ad.id_asiento_cabecera', '=', 'ac.id')
                    ->select(
                        'pe.plan',
                        'p.nombre',
                        'pe.id_padre',
                        'ac.fecha_asiento',
                        DB::raw("$periodo as anio"),
                        'ad.debe',
                        'ad.haber',
                        DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 3) = "5.1", "COSTOS", "GASTOS")) AS tipo'),
                        'ac.aparece_sri'
                    )
                    ->where('pe.id_empresa', $empresa)
                    ->where('ac.id_empresa', $empresa)
                    ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['5'])
                    ->whereBetween('ac.fecha_asiento', ["$desde 00:00:00", "$hasta 23:59:59"])
                    ->whereNotNull('pe.plan')
                    ->orderBy('pe.plan')
                    ->orderBy('ac.fecha_asiento')
                    ->get();
                $utilidadGravable = $utilidadContable_ + $utilidadGravable->where('tipo', 'GASTOS')->where('aparece_sri', 0)->sum('debe') - $utilidadGravable->where('tipo', 'GASTOS')->where('aparece_sri', 0)->sum('haber');
            }
        } else {
            //Devolver que no hay renta configurada
        }
        $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $r->d, $r->h, $utilidadBruta, $participacionTrabajadores, $totalIngresos, $utilidadGravable);
        $utilidadPerdida = $utilidadBruta - $participacionTrabajadores - $impuestoGenerado;
        if (count($datos) > 0) {
            $htmlActivos .= '<h2>Activos</h2>
            <table style="border-collapse:collapse; width: 100%;">
                <tr class="tr">
                    <th class="th">Cuenta</th>
                    <th class="th">Nombre</th>
                    <th class="th">Saldo</th>
                    <th class="th">%</th>
                </tr>';
            $htmlPasivos .= '<h2>Pasivos</h2>
                <table style="border-collapse:collapse; width: 100%;">
                    <tr class="tr">
                        <th class="th">Cuenta</th>
                        <th class="th">Nombre</th>
                        <th class="th">Saldo</th>
                        <th class="th">%</th>
                    </tr>';
            $htmlPatrimonio .= '<h2>Patrimonio</h2>
                <table style="border-collapse:collapse; width: 100%;">
                    <tr class="tr">
                        <th class="th">Cuenta</th>
                        <th class="th">Nombre</th>
                        <th class="th">Saldo</th>
                        <th class="th">%</th>
                    </tr>';
            foreach ($datos as $key => $row) {
                $porcentaje = 0;
                if ($row->tipo == 'ACTIVOS') {
                    $saldo = self::traerTodo($datos_->where("tipo", "ACTIVOS"), $row->id);
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalActivos_) * 100;
                    }
                    $totalPorcentajeActivo += $porcentaje;
                    if ($saldo < 0) {
                        $saldo_ = '(' . number_format(($saldo * -1), 2, ',', '.') . ')';
                    } else {
                        $saldo_ =  number_format($saldo, 2, ',', '.');
                    }
                    $totalActivos += $saldo;
                    $cantidadP = "";
                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }
                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {

                        $htmlActivos .= '<tr>';
                        if ($row->estado == 2) {
                            $htmlActivos .= '<td class="td"><a href="v3/reportes/contables/libromayor?desde=' . $desde . '&hasta=' . $hasta . '&cuentadesde=' . urlencode($row->plan) . '" target="_blank">' . $cantidadP . $row->plan . '</a></td>';
                        } else {
                            $htmlActivos .= '<td class="td">' . $cantidadP . $row->plan . '</td>';
                        }
                        $htmlActivos .=
                            '<td class="td">' . $row->nombre . '</td>
                            <td class="td" style="text-align: right;">$' . $saldo_ . '</td>
                            <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                            </tr>';
                    }
                    $porcentaje = 0;
                }
                if ($row->tipo == 'PASIVOS') {
                    $saldo = self::traerTodo($datos_->where("tipo", "PASIVOS"), $row->id);
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalPasivo_) * 100;
                    }
                    $totalPorcentajePasivo += $porcentaje;
                    if ($impuestoGenerado > 0) {
                        $arrayBusqueda = [
                            'IMPUESTO SRI',
                            'PAGAR'
                        ];
                        $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda);
                        if ($planCuenta != '') {
                            if ($planCuenta->plan == $row->plan) {
                                $saldo = $row->saldo + $impuestoGenerado * -1;
                                $totalPasivo_ = $totalPasivo_ + $saldo - $participacionTrabajadores - $row->saldo;
                            }
                        } else {
                            $arrayBusqueda = [
                                'impuesto a la renta',
                                'renta',
                                'pagar'
                            ];
                            $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda);
                            if ($planCuenta != '') {
                                if ($planCuenta->plan == $row->plan) {
                                    $saldo = $row->saldo + $impuestoGenerado * -1;
                                    $totalPasivo_ = $totalPasivo_ + $saldo - $participacionTrabajadores - $row->saldo;
                                }
                            }
                        }
                        $arrayBusqueda = [
                            'participacion',
                            '15'
                        ];
                        $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda, 'PASIVOS');
                        if ($planCuenta != '') {
                            if ($planCuenta->plan == $row->plan) {
                                $saldo = $row->saldo + $participacionTrabajadores * -1;
                                $totalPasivo_ = $totalPasivo_;
                            }
                        }
                    }
                    if ($saldo < 0) {
                        $saldo_ = number_format(($saldo * -1), 2, ',', '.');
                    } else {
                        $saldo_ =  number_format($saldo, 2, ',', '.');
                    }
                    $totalPasivos += $saldo;
                    $cantidadP = "";

                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }
                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
                        $htmlPasivos .= '<tr>';
                        if ($row->estado == 2) {
                            $htmlPasivos .= '<td class="td"><a href="v3/reportes/contables/libromayor?desde=' . $desde . '&hasta=' . $hasta . '&cuentadesde=' . urlencode($row->plan) . '" target="_blank">' . $cantidadP . $row->plan . '</a></td>';
                        } else {
                            $htmlPasivos .= '<td class="td">' . $cantidadP . $row->plan . '</td>';
                        }
                        $htmlPasivos .=
                            '<td class="td">' . $row->nombre . '</td>
                    <td class="td" style="text-align: right;">$' . $saldo_ . '</td>
                    <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                    }
                    $porcentaje = 0;
                }
                if ($row->tipo == 'PATRIMONIO') {
                    $saldo = self::traerTodo($datos_->where("tipo", "PATRIMONIO"), $row->id);
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalPatrimonio_) * 100;
                    }
                    $totalPorcentajePatrimonio += $porcentaje;
                    if ($utilidadPerdida < 0) {
                        $arrayBusqueda = [
                            'PERDIDA',
                            'NETA',
                            'PERIODO'
                        ];
                        $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda, 'PERDIDA');
                        if ($planCuenta != '') {
                            if ($planCuenta->plan == $row->plan) {
                                $saldo = $utilidadPerdida * -1;
                                $perdida = $saldo;
                            }
                        }
                    }
                    if ($utilidadPerdida > 0) {
                        $arrayBusqueda = [
                            'GANANCIA',
                            'NETA',
                            'PERIODO'
                        ];
                        $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda);
                        if ($planCuenta != '') {
                            if ($planCuenta->plan == $row->plan) {
                                $saldo = $utilidadPerdida * -1;
                                $ganacia = $saldo;
                            }
                        }
                    }
                    $cadena = strtoupper($row->nombre);
                    $buscar = "PERDIDA";
                    $resultado = strpos($cadena, $buscar);
                    if ($saldo < 0) {
                        $saldo_ = number_format(($saldo * -1), 2, ',', '.');
                    } else {
                        $saldo_ = '(' . number_format($saldo, 2, ',', '.') . ')';
                    }
                    $totalPatrimonio += $saldo;
                    $cantidadP = "";
                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }

                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
                        $htmlPatrimonio .= '<tr>';
                        if ($row->estado == 2) {
                            $htmlPatrimonio .= '<td class="td"><a href="v3/reportes/contables/libromayor?desde=' . $desde . '&hasta=' . $hasta . '&cuentadesde=' . urlencode($row->plan) . '" target="_blank">' . $cantidadP . $row->plan . '</a></td>';
                        } else {
                            $htmlPatrimonio .= '<td class="td">' . $cantidadP . $row->plan . '</td>';
                        }
                        $htmlPatrimonio .= '<td class="td">' . $row->nombre . '</td>
                    <td class="td" style="text-align: right;">$' . $saldo_ . '</td>
                    <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                    }
                    $porcentaje = 0;
                }
            }
            $totalPatrimonio_ = $totalPatrimonio_ + $perdida + $ganacia;
            $htmlActivos .= '<tr class="tr">
                    <td>Total Activos</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;" id="totalActivos">$ ' . number_format($totalActivos_, 2, ',', '.') . '</td>
                    <td style="text-align: right;"></td>
                </tr>
            </table>';


            $htmlPasivos .= '<tr class="tr">
                    <td>Total Pasivos</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">$ ' . number_format($totalPasivo_ * -1, 2, ',', '.') . '</td>
                    <td style="text-align: right;"></td>
                </tr>
            </table>';
            $htmlPatrimonio .= '<tr class="tr">
                    <td>Total Patrimonio</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">$ ' . number_format($totalPatrimonio_ * -1, 2, ',', '.') . '</td>
                    <td style="text-align: right;"></td>
                </tr>
            </table>';
        }
        $totalPasivos = ($totalPasivos * -1);
        $totalPatrimonio = ($totalPatrimonio * -1);
        $diferencia = $totalActivos_ - (($totalPasivo_ * -1) + ($totalPatrimonio_ * -1));
        return $htmlActivos . '|' . $htmlPasivos . '|' . $htmlPatrimonio . '|' . number_format($totalActivos_, 2, ',', '.') . '|' . number_format(($totalPasivo_ * -1), 2, ',', '.') . '|' . number_format(($totalPatrimonio_ * -1), 2, ',', '.') . '|' . number_format($diferencia, 2, ',', '.');
    }
    public static function detalleBalanceFinanciero__($r, $armar = '', $tipo = '')
    {
        $htmlActivos = '';
        $htmlPasivos = '';
        $htmlPatrimonio = '';
        $planCuenta = '';
        $totalActivos = 0;
        $totalActivos_ = 0;
        $totalPasivos = 0;
        $totalPasivo_ = 0;
        $totalPatrimonio = 0;
        $totalPatrimonio_ = 0;
        $participacionTrabajadores = 0;
        $utilidadPerdida = 0;
        $totalPorcentajeActivo = 0;
        $totalPorcentajePasivo = 0;
        $totalPorcentajePatrimonio = 0;
        $datos = Contabilidad::getBalanceComprobacion($r, 'return');
        //Contabilidad::updateSaldos($datos);
        $index = [];
        foreach ($datos as $item) {
            $index[$item->id] = $item;
        }
        // Iniciar el proceso desde los nodos raíz (sin id_padre)
        foreach ($index as $item) {
            if (empty($item->id_padre)) {
                Contabilidad::sumarSaldos($item->id, $index);
            }
        }
        $datos = $index;
        $resultado = Contabilidad::getBalanceResultados($r, 'return');
        $totalIngresos = $resultado->where('tipo', 'INGRESOS')->sum('saldo');
        $totalCostos = $resultado->where('tipo', 'COSTOS')->sum('saldo');
        $totalGastos = $resultado->where('tipo', 'GASTOS')->sum('saldo');
        $utilidadBruta = $totalIngresos + ($totalCostos + $totalGastos);
        $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime($r->d)));
        if ($rentaEmpresa != '') {
            if ($rentaEmpresa->regimen_especial == 1) {
                $htmlUtilidadGravable = '<td>Utilidad gravable</td><td style="text-align: right;">$ 0.00</td>';
            }
        } else {
            //Devolver que no hay renta configurada
        }
        $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $r->d, $r->h, $utilidadBruta, $participacionTrabajadores, $totalIngresos);
        $utilidadPerdida = $utilidadBruta - $impuestoGenerado;
        if (count($datos) > 0) {
            $htmlActivos .= '<h2>Activos</h2>
            <table style="border-collapse:collapse; width: 100%;">
                <tr class="tr">
                    <th class="th">Cuenta</th>
                    <th class="th">Nombre</th>
                    <th class="th">Saldo</th>
                    <th class="th">%</th>
                </tr>';
            $htmlPasivos .= '<h2>Pasivos</h2>
                <table style="border-collapse:collapse; width: 100%;">
                    <tr class="tr">
                        <th class="th">Cuenta</th>
                        <th class="th">Nombre</th>
                        <th class="th">Saldo</th>
                        <th class="th">%</th>
                    </tr>';
            $htmlPatrimonio .= '<h2>Patrimonio</h2>
                <table style="border-collapse:collapse; width: 100%;">
                    <tr class="tr">
                        <th class="th">Cuenta</th>
                        <th class="th">Nombre</th>
                        <th class="th">Saldo</th>
                        <th class="th">%</th>
                    </tr>';
            foreach ($datos as $row) {
                $porcentaje = 0;
                $saldo_ = $row->saldo_deudor - $row->saldo_acreedor;
                if ($row->tipo == 'ACTIVOS') {
                    $htmlActivos .= '<tr>
                <td class="td">' . $row->plan . '</td>
                <td class="td">' . $row->nombre . '</td>
                <td class="td" style="text-align: right;">$' . $saldo_ . '</td>
                <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                </tr>';
                }
                if ($row->tipo == 'PASIVOS') {
                    $htmlPasivos .= '<tr>
                    <td class="td">' . $row->plan . '</td>
                    <td class="td">' . $row->nombre . '</td>
                    <td class="td" style="text-align: right;">$' . $saldo_ . '</td>
                    <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                }
                if ($row->tipo == 'PATRIMONIO') {
                    $htmlPatrimonio .= '<tr>
                    <td class="td">' . $row->plan . '</td>
                    <td class="td">' . $row->nombre . '</td>
                    <td class="td" style="text-align: right;">$' . $saldo_ . '</td>
                    <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                }
            }
            $htmlActivos .= '<tr class="tr">
                    <td>Total Activos</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;" id="totalActivos">$ ' . number_format($totalActivos, 2, ',', '.') . '</td>
                    <td style="text-align: right;">' . number_format($totalPorcentajeActivo, 2) . '</td>
                </tr>
            </table>';
            $htmlPasivos .= '<tr class="tr">
                    <td>Total Pasivos</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">$ ' . number_format($totalPasivos * -1, 2, ',', '.') . '</td>
                    <td style="text-align: right;">' . number_format($totalPorcentajePasivo, 2) . '</td>
                </tr>
            </table>';
            $htmlPatrimonio .= '<tr class="tr">
                    <td>Total Patrimonio</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">$ ' . number_format($totalPatrimonio * -1, 2, ',', '.') . '</td>
                    <td style="text-align: right;">' . number_format($totalPorcentajePatrimonio, 2) . '</td>
                </tr>
            </table>';
        }
        $totalPasivos = ($totalPasivos * -1);
        $totalPatrimonio = ($totalPatrimonio * -1);
        $diferencia = $totalActivos - ($totalPasivos + $totalPatrimonio);
        return $htmlActivos . '|' . $htmlPasivos . '|' . $htmlPatrimonio . '|' . number_format($totalActivos, 2, ',', '.') . '|' . number_format($totalPasivos, 2, ',', '.') . '|' . number_format($totalPatrimonio, 2, ',', '.') . '|' . number_format($diferencia, 2, ',', '.');
    }
    public static function detalleBalanceFinanciero___($r, $armar = '', $tipo = '')
    {
        $htmlActivos = '';
        $htmlPasivos = '';
        $htmlPatrimonio = '';
        $planCuenta = '';
        $totalActivos = 0;
        $totalActivos_ = 0;
        $totalPasivos = 0;
        $totalPasivo_ = 0;
        $totalPatrimonio = 0;
        $totalPatrimonio_ = 0;
        $participacionTrabajadores = 0;
        $utilidadPerdida = 0;
        $totalPorcentajeActivo = 0;
        $totalPorcentajePasivo = 0;
        $totalPorcentajePatrimonio = 0;
        $datos = Contabilidad::getBalanceComprobacion($r, 'return');

        //Contabilidad::updateSaldos($datos);

        $index = [];
        foreach ($datos as $item) {
            $index[$item->id] = $item;
        }
        // Iniciar el proceso desde los nodos raíz (sin id_padre)
        foreach ($index as $item) {
            if (empty($item->id_padre)) {
                Contabilidad::sumarSaldos($item->id, $index);
            }
        }

        $datos = $index;




        $resultado = Contabilidad::getBalanceResultados($r, 'return');
        if ($armar != '') {
            if ($tipo == 'financiero')
                return $datos;
            elseif ($tipo == 'resultados')
                return $resultado;
        }
        $totalIngresos = $resultado->where('tipo', 'INGRESOS')->sum('saldo');
        $totalCostos = $resultado->where('tipo', 'COSTOS')->sum('saldo');
        $totalGastos = $resultado->where('tipo', 'GASTOS')->sum('saldo');
        $utilidadBruta = $totalIngresos + ($totalCostos + $totalGastos);
        foreach ($datos as $row) {
            if ($row->tipo == 'ACTIVOS') {
                $saldo = $row->saldo_deudor - $row->saldo_acreedor;
                $totalActivos_ += $saldo;
            }
        }
        foreach ($datos as $row) {
            if ($row->tipo == 'PASIVOS') {
                $saldo = $row->saldo_deudor - $row->saldo_acreedor;
                $totalPasivo_ += $saldo;
            }
        }
        foreach ($datos as $row) {
            if ($row->tipo == 'PATRIMONIO') {
                $saldo = $row->saldo_deudor - $row->saldo_acreedor;
                $totalPatrimonio_ += $saldo;
            }
        }
        if ($utilidadBruta > 0) {
            $participacionTrabajadores = $utilidadBruta * 0.15;
            $utilidadContable = $utilidadBruta - $participacionTrabajadores;
        }
        $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime($r->d)));
        if ($rentaEmpresa != '') {
            if ($rentaEmpresa->regimen_especial == 1) {
                $htmlUtilidadGravable = '<td>Utilidad gravable</td><td style="text-align: right;">$ 0.00</td>';
            }
        } else {
            //Devolver que no hay renta configurada
        }
        $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $r->d, $r->h, $utilidadBruta, $participacionTrabajadores, $totalIngresos);
        $utilidadPerdida = $utilidadBruta - $impuestoGenerado;
        if (count($datos) > 0) {
            $htmlActivos .= '<h2>Activos</h2>
            <table style="border-collapse:collapse; width: 100%;">
                <tr class="tr">
                    <th class="th">Cuenta</th>
                    <th class="th">Nombre</th>
                    <th class="th">Saldo</th>
                    <th class="th">%</th>
                </tr>';
            $htmlPasivos .= '<h2>Pasivos</h2>
                <table style="border-collapse:collapse; width: 100%;">
                    <tr class="tr">
                        <th class="th">Cuenta</th>
                        <th class="th">Nombre</th>
                        <th class="th">Saldo</th>
                        <th class="th">%</th>
                    </tr>';
            $htmlPatrimonio .= '<h2>Patrimonio</h2>
                <table style="border-collapse:collapse; width: 100%;">
                    <tr class="tr">
                        <th class="th">Cuenta</th>
                        <th class="th">Nombre</th>
                        <th class="th">Saldo</th>
                        <th class="th">%</th>
                    </tr>';
            foreach ($datos->toArray() as $key => $row) {
                $porcentaje = 0;
                if ($row->tipo == 'ACTIVOS') {
                    $saldo = self::traerTodo(array_filter($datos->toArray(), function ($element) {
                        return $element->tipo === 'ACTIVOS';
                    }), $row->id);
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalActivos_) * 100;
                    }

                    $totalPorcentajeActivo += $porcentaje;
                    if ($saldo < 0) {
                        $saldo_ = '(' . number_format(($saldo * -1), 2, ',', '.') . ')';
                    } else {
                        $saldo_ =  number_format($saldo, 2, ',', '.');
                    }
                    $totalActivos += $saldo;
                    $cantidadP = "";

                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }

                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
                        $htmlActivos .= '<tr>
                    <td class="td">' . $cantidadP . $row->plan . '</td>
                    <td class="td">' . $row->nombre . '</td>
                    <td class="td" style="text-align: right;">$' . $saldo_ . '</td>
                    <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                    }
                    $porcentaje = 0;
                }
                if ($row->tipo == 'PASIVOS') {
                    $saldo = self::traerTodo(array_filter($datos->toArray(), function ($element) {
                        return $element->tipo === 'PASIVOS';
                    }), $row->id);
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalPasivo_) * 100;
                    }
                    $totalPorcentajePasivo += $porcentaje;
                    if ($impuestoGenerado > 0) {
                        $arrayBusqueda = [
                            'IMPUESTO SRI',
                            'PAGAR'
                        ];
                        $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda);
                    }
                    if ($planCuenta != '') {

                        if ($planCuenta->plan == $row->plan) {
                            $saldo = $impuestoGenerado * -1;
                        }
                    }
                    if ($saldo < 0) {
                        $saldo_ = number_format(($saldo * -1), 2, ',', '.');
                    } else {
                        $saldo_ =  number_format($saldo, 2, ',', '.');
                    }
                    $totalPasivos += $saldo;
                    $cantidadP = "";

                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }
                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
                        $htmlPasivos .= '<tr>
                    <td class="td">' . $cantidadP . $row->plan . '</td>
                    <td class="td">' . $row->nombre . '</td>
                    <td class="td" style="text-align: right;">$' . $saldo_ . '</td>
                    <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                    }
                    $porcentaje = 0;
                }
                if ($row->tipo == 'PATRIMONIO') {
                    // dd(array_filter($datos->toArray(), function ($element) {
                    //     return $element->tipo === 'PATRIMONIO';
                    // }));
                    $saldo = self::traerTodo(array_filter($datos->toArray(), function ($element) {
                        return $element->tipo === 'PATRIMONIO';
                    }), $row->id);

                    //$saldo = $row->saldo_deudor - $row->saldo_acreedor;
                    if ($saldo != 0) {
                        $porcentaje = ($saldo / $totalPatrimonio_) * 100;
                    }
                    $totalPorcentajePatrimonio += $porcentaje;
                    $arrayBusqueda = [
                        'PERDIDA',
                        'NETA',
                        'PERIODO'
                    ];
                    $planCuenta = PlanCuentas::buscarCuenta($arrayBusqueda, 'PERDIDA');
                    if ($planCuenta != '') {
                        if ($planCuenta->plan == $row->plan) {
                            $saldo = $utilidadPerdida * -1;
                        }
                    }
                    $cadena = strtoupper($row->nombre);
                    $buscar = "PERDIDA";
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        //$saldo = $saldo * -1;
                    }
                    if ($saldo < 0) {
                        $saldo_ = number_format(($saldo * -1), 2, ',', '.');
                    } else {
                        $saldo_ = '(' . number_format($saldo, 2, ',', '.') . ')';
                    }
                    $totalPatrimonio += $saldo;
                    $cantidadP = "";

                    for ($i = 0; $i < substr_count($row->plan, '.'); $i++) {
                        $cantidadP .= "&nbsp;";
                    }



                    if ($saldo_ !== "(0,00)" && $saldo_ !==  "0,00") {
                        $htmlPatrimonio .= '<tr>
                    <td class="td">' . $cantidadP . $row->plan . '</td>
                    <td class="td">' . $row->nombre . '</td>
                    <td class="td" style="text-align: right;">$' . $saldo . '</td>
                    <td class="td" style="text-align: right;">' . number_format($porcentaje, 4) . '</td>
                    </tr>';
                    }
                    $porcentaje = 0;
                }
            }
            $htmlActivos .= '<tr class="tr">
                    <td>Total Activos</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;" id="totalActivos">$ ' . number_format($totalActivos, 2, ',', '.') . '</td>
                    <td style="text-align: right;">' . number_format($totalPorcentajeActivo, 2) . '</td>
                </tr>
            </table>';
            $htmlPasivos .= '<tr class="tr">
                    <td>Total Pasivos</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">$ ' . number_format($totalPasivos * -1, 2, ',', '.') . '</td>
                    <td style="text-align: right;">' . number_format($totalPorcentajePasivo, 2) . '</td>
                </tr>
            </table>';
            $htmlPatrimonio .= '<tr class="tr">
                    <td>Total Patrimonio</td>
                    <td>&nbsp;</td>
                    <td style="text-align: right;">$ ' . number_format($totalPatrimonio * -1, 2, ',', '.') . '</td>
                    <td style="text-align: right;">' . number_format($totalPorcentajePatrimonio, 2) . '</td>
                </tr>
            </table>';
        }
        $totalPasivos = ($totalPasivos * -1);
        $totalPatrimonio = ($totalPatrimonio * -1);
        $diferencia = $totalActivos - ($totalPasivos + $totalPatrimonio);
        return $htmlActivos . '|' . $htmlPasivos . '|' . $htmlPatrimonio . '|' . number_format($totalActivos, 2, ',', '.') . '|' . number_format($totalPasivos, 2, ',', '.') . '|' . number_format($totalPatrimonio, 2, ',', '.') . '|' . number_format($diferencia, 2, ',', '.');
    }
    public static function getBalanceResultados($r, $armar = '')
    {
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', isset($r->d) ? $r->d : '');
        if (trim($desde) == '' || strtotime($desde) === false) {
            $desde = self::getFechaInicioSistema();
        }
        $desde = date('Y-m-d', strtotime($desde));
        $periodo = date('Y', strtotime(trim(isset($r->d) ? $r->d : '') == '' ? $r->h : $desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        $logCierre = '';
        if (isset($r->c) && $r->c == 1) {
            $logCierre = LogCierreAnio::where('anio', $periodo)->first();
        }
        $impuestoPagado = 0;
        $idAsientoImpuesto = '';
        if ($logCierre != '') {
            $impu = AsientosCabecera::whereYear('fecha_asiento', $periodo)
                ->where('id_empresa', $empresa)
                ->where('observacion', 'like', '%GASTO IMPUES%')
                ->first();
            if ($impu != '') {
                $impuestoPagado = $impu->valor;
                $idAsientoImpuesto = $impu->id;
            }
        }
        $balance = DB::table('plan_cuentas as p')
            ->leftJoin('plan_cuentas_empresa as pe', 'p.id', '=', 'pe.id_plan')
            ->leftJoin('ct_asientos_detalle as ad', 'p.id', '=', 'ad.id_plan_cuenta')
            ->leftJoin('ct_asientos_cabecera as ac', 'ad.id_asiento_cabecera', '=', 'ac.id')
            ->select(
                'pe.plan',
                'p.nombre',
                'pe.id_padre',
                'ac.fecha_asiento',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(SUM(ad.debe), 0) AS debe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) AS haber'),
                DB::raw('IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS deudor'),
                DB::raw('IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS acreedor'),
                DB::raw('(IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0)-IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)) saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 3) = "5.1", "COSTOS", "GASTOS")) AS tipo'),
                'ac.aparece_sri'
            )
            ->where('pe.id_empresa', $empresa)
            ->where('ac.id_empresa', $empresa)
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['4', '5'])
            ->whereBetween('ac.fecha_asiento', ["$desde 00:00:00", "$hasta 23:59:59"])
            ->whereNotNull('pe.plan')
            ->where(function ($q) use ($logCierre, $idAsientoImpuesto) {
                if ($logCierre != '') {
                    $q->where(DB::raw('ad.id_asiento_cabecera'), '!=', $logCierre->id_asiento);
                }
                if ($idAsientoImpuesto != '') {
                    $q->where(DB::raw('ad.id_asiento_cabecera'), '!=', $idAsientoImpuesto);
                }
            })
            ->groupBy('pe.plan')
            ->orderBy('pe.plan')
            ->orderBy('ac.fecha_asiento')
            ->get();
        if ($armar != '') {
            return $balance;
        }
        $totalIngresos = 0;
        $totalCostos = 0;
        $totalGastos = 0;
        $utilidadBruta = 0;
        $participacionTrabajadores = 0;
        $utilidadContable = 0;
        $utilidadGravable = 0;
        $impuestoGenerado = 0;
        $utilidadPerdida = 0;
        $htmlIngresos = '';
        $htmlCostos = '';
        $htmlGastos = '';
        $htmlUtilidadGravable = '';
        $utilidadContable_ = 0;
        $rentaEmpresa = PorcentajeRenta::getDatoRentaAnual(date('Y', strtotime($desde)));
        if (count($balance) > 0) {
            $totalIngresos = $balance->where('tipo', 'INGRESOS')->sum('saldo');
            $totalCostos = $balance->where('tipo', 'COSTOS')->sum('saldo');
            $totalGastos = $balance->where('tipo', 'GASTOS')->sum('saldo');
            $utilidadBruta = $totalIngresos + ($totalCostos + $totalGastos);
            if ($utilidadBruta > 0) {
                $participacionTrabajadores = $utilidadBruta * 0.15;
                $utilidadContable = $utilidadBruta - $participacionTrabajadores;
            }
            $utilidadContable_ = $utilidadContable;
            if ($rentaEmpresa != '') {
                if ($rentaEmpresa->regimen_especial == 1) {
                    $utilidadGravable = DB::table('plan_cuentas as p')
                        ->leftJoin('plan_cuentas_empresa as pe', 'p.id', '=', 'pe.id_plan')
                        ->leftJoin('ct_asientos_detalle as ad', 'p.id', '=', 'ad.id_plan_cuenta')
                        ->leftJoin('ct_asientos_cabecera as ac', 'ad.id_asiento_cabecera', '=', 'ac.id')
                        ->select(
                            'pe.plan',
                            'p.nombre',
                            'pe.id_padre',
                            'ac.fecha_asiento',
                            DB::raw("$periodo as anio"),
                            'ad.debe',
                            'ad.haber',
                            DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 3) = "5.1", "COSTOS", "GASTOS")) AS tipo'),
                            'ac.aparece_sri'
                        )
                        ->where('pe.id_empresa', $empresa)
                        ->where('ac.id_empresa', $empresa)
                        ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['5'])
                        ->whereBetween('ac.fecha_asiento', ["$desde 00:00:00", "$hasta 23:59:59"])
                        ->whereNotNull('pe.plan')
                        ->orderBy('pe.plan')
                        ->orderBy('ac.fecha_asiento')
                        ->get();
                    $utilidadGravable = $utilidadContable_ + $utilidadGravable->where('tipo', 'GASTOS')->where('aparece_sri', 0)->sum('debe') - $utilidadGravable->where('tipo', 'GASTOS')->where('aparece_sri', 0)->sum('haber');
                    if ($utilidadBruta > 0) {
                        $htmlUtilidadGravable = '<td>Utilidad gravable</td><td style="text-align: right;">$ ' . number_format($utilidadGravable, 2) . '</td>';
                    }
                }
            } else {
                //Devolver que no hay renta configurada
            }
            $impuestoGenerado = PorcentajeRenta::impuestoCausado($rentaEmpresa, $desde, $hasta, $utilidadBruta, $participacionTrabajadores, $totalIngresos, $utilidadGravable);
            if ($utilidadBruta < 0) {
                $utilidadPerdida = $utilidadBruta - $impuestoGenerado;
                $utilidadPerdida = '$ (' . number_format(($utilidadPerdida * -1), 2, ', ', '.') . ')';
            } else {
                $utilidadPerdida = $utilidadContable - $impuestoGenerado;
                $utilidadPerdida = '$ ' . number_format($utilidadPerdida, 2, ', ', '.');
            }
            $participacionTrabajadores = '$ ' . number_format($participacionTrabajadores, 2, ',', '.');
            $utilidadContable = '$ ' . number_format($utilidadContable, 2, ',', '.');
            if ($utilidadBruta < 0) {
                $utilidadBruta = '(' . number_format($utilidadBruta * -1, 2) . ')';
            }
            if ($totalCostos < 0) {
                $totalCostos = $totalCostos * -1;
            }
            if ($totalGastos < 0) {
                $totalGastos = $totalGastos * -1;
            }
            $utilidadBruta = '$ ' . $utilidadBruta;
            $impuestoGenerado = '$ ' . number_format($impuestoGenerado, 2, ',', '.');
            $htmlIngresos .= '<center><div style="float: center;width: 60%;"><h3 style="background-color: #F3F3F3 !important;" data-toggle="collapse" href="#ingresoExample" role="button" aria-expanded="false" aria-controls="ingresoExample">Ingresos: $ ' . str_replace(',', '', number_format($totalIngresos, 2)) . '</h3><div class="collapse" id="ingresoExample"><div class="card card-body"><table style="border-collapse:collapse; width: 100%;"><thead><tr><th class="th">Cuenta</th><th class="th">Nombre</th><th class="th">Saldo</th></tr></thead><tbody id="bodyIngresos">';
            foreach ($balance as $row) {
                if ($row->tipo == 'INGRESOS' && $row->saldo != '0.00') {
                    if ($row->saldo < 0) {
                        $saldo = '(' . number_format(($row->saldo * -1), 2, ',', '.') . ')';
                    } else {
                        $saldo = number_format($row->saldo, 2, ',', '.');
                    }
                    $htmlIngresos .= '<tr><td class="td">' . $row->plan . '</td><td class="td">' . $row->nombre . '</td><td class="td" style="text-align:right;">$ ' . $saldo . '</td></tr>';
                }
            }
            $htmlIngresos .= '</tbody><tfoot><tr><td class="td">Total ingresos</td><td class="td">&nbsp;</td><td class="td" style="text-align: right;">$ ' . str_replace(',', '', number_format($totalIngresos, 2)) . '</td></tr></tfoot></table></div></div></div></center>';
            $htmlCostos .= '<center><div style="float: center;width: 60%;"><h3 style="background-color: #F3F3F3 !important;" data-toggle="collapse" href="#costosExample" role="button" aria-expanded="false" aria-controls="costosExample">Costos: $ ' . str_replace(',', '', number_format($totalCostos, 2)) . '</h3><div class="collapse" id="costosExample"><div class="card card-body"><table style="border-collapse:collapse; width: 100%;"><thead><tr><th class="th">Cuenta</th><th class="th">Nombre</th><th class="th">Saldo</th></tr></thead><tbody id="bodyIngresos">';
            foreach ($balance as $row) {
                if ($row->tipo == 'COSTOS' && $row->saldo != '0.00') {
                    if ($row->saldo < 0) {
                        $saldo =  number_format(($row->saldo * -1), 2, ',', '.');
                    } else {
                        $saldo = number_format($row->saldo, 2, ',', '.');
                    }
                    $htmlCostos .= '<tr><td class="td">' . $row->plan . '</td><td class="td">' . $row->nombre . '</td><td class="td" style="text-align:right;">$ ' . $saldo . '</td></tr>';
                }
            }
            $htmlCostos .= '</tbody><tfoot><tr><td class="td">Total costos</td><td class="td">&nbsp;</td><td class="td" style="text-align: right;">$ ' . str_replace(',', '', number_format($totalCostos, 2)) . '</td></tr></tfoot></table></div></div></div></center>';
            $htmlGastos .= '<center><div style="float: center;width: 60%;"><h3 style="background-color: #F3F3F3 !important;" data-toggle="collapse" href="#gastosExample" role="button" aria-expanded="false" aria-controls="gastosExample">Gastos: $ ' . str_replace(',', '', number_format($totalGastos, 2)) . '</h3><div class="collapse" id="gastosExample"><div class="card card-body"><table style="border-collapse:collapse; width: 100%;"><thead><tr><th class="th">Cuenta</th><th class="th">Nombre</th><th class="th">Saldo</th></tr></thead><tbody id="bodyIngresos">';
            foreach ($balance as $row) {
                if ($row->tipo == 'GASTOS' && $row->saldo != '0.00') {
                    if ($row->saldo < 0) {
                        $saldo = number_format(($row->saldo * -1), 2, ',', '.');
                    } else {
                        $saldo = number_format($row->saldo, 2, ',', '.');
                    }
                    $htmlGastos .= '<tr>
                    <td class="td">' . $row->plan . '</td>
                    <td class="td">' . $row->nombre . '</td>
                    <td class="td" style="text-align:right;">$ ' . $saldo . '</td>
                    </tr>';
                }
            }
            $htmlGastos .= '</tbody><tfoot><tr><td class="td">Total gastos</td><td class="td">&nbsp;</td><td class="td" style="text-align: right;">$ ' . str_replace(',', '', number_format($totalGastos, 2)) . '</td></tr></tfoot></table></div></div></div></center>';
        }
        return $htmlIngresos . '|' . $htmlCostos . '|' . $htmlGastos . '|' . $utilidadBruta . '|' . $participacionTrabajadores . '|' . $utilidadContable . '|' . $htmlUtilidadGravable . '|' . $impuestoGenerado . '|' . $utilidadPerdida;
    }
    // Función recursiva para sumar saldos de hijos a padres
    public static function sumarSaldos($id, &$index)
    {
        $debe = 0.0;
        $haber = 0.0;
        $saldo_deudor = 0.0;
        $saldo_acreedor = 0.0;

        foreach ($index as $item) {
            if ($item->id_padre == $id) {
                // Llamar recursivamente para obtener saldos de los hijos
                $saldos = Contabilidad::sumarSaldos($item->id, $index);
                $debe += $saldos['debe'];
                $haber += $saldos['haber'];
                $saldo_deudor += $saldos['saldo_deudor'];
                $saldo_acreedor += $saldos['saldo_acreedor'];
            }
        }

        // Obtener valores del nodo actual
        if (isset($index[$id])) {
            $index[$id]->debe += $debe;
            $index[$id]->haber += $haber;
            $index[$id]->saldo_deudor += $saldo_deudor;
            $index[$id]->saldo_acreedor += $saldo_acreedor;
            $debe = $index[$id]->debe;
            $haber = $index[$id]->haber;
            $saldo_deudor = $index[$id]->saldo_deudor;
            $saldo_acreedor = $index[$id]->saldo_acreedor;
        }

        return [
            'debe' => $debe,
            'haber' => $haber,
            'saldo_deudor' => $saldo_deudor,
            'saldo_acreedor' => $saldo_acreedor,
        ];
    }
    public static function getBalanceComprobacionSolo($r, $armar = '')
    {
        $periodo = date('Y', strtotime($r->d));
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', $r->d);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        $hasta_ = date("Y-m-d", strtotime($hasta . "+ 1 days"));
        $finan1 = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(ad.debe,0) as debe'),
                DB::raw('COALESCE(ad.haber, 0) AS haber'),
                DB::raw('0 AS saldo_deudor'),
                DB::raw('0 AS saldo_acreedor'),
                DB::raw('0 AS saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 1, "ACTIVOS", IF(SUBSTRING(pe.plan, 1, 1) = 2, "PASIVOS", "PATRIMONIO")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q1) use ($empresa) {
                $q1->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['1', '2', '3'])
            ->whereNull('ac.fecha_asiento')
            ->where(function ($q2) {
                $q2->whereNull('ad.debe')
                    ->whereNull('ad.haber');
            })
            ->where(function ($q3) {
                $q3->where('p.nombre', 'NOT LIKE', '%Impuesto a la Renta  por pagar%')
                    ->where('p.nombre', 'NOT LIKE', '%Ganancia Neta Del Periodo%')
                    ->where('p.nombre', 'NOT LIKE', '%PERDIDA NETA DEL %');
                //AND (p.nombre NOT LIKE '%Impuesto a la Renta  por pagar%' AND p.nombre NOT LIKE '%Ganancia Neta Del Periodo%' AND p.nombre NOT LIKE '%PERDIDA NETA DEL %')
            });
        $finan2 = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(SUM(ad.debe), 0) AS debe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) AS haber'),
                DB::raw('IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS saldo_deudor'),
                DB::raw('IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS saldo_acreedor'),
                DB::raw('(IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)-IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0)) saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 1, "ACTIVOS", IF(SUBSTRING(pe.plan, 1, 1) = 2, "PASIVOS", "PATRIMONIO")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['1', '2', '3'])
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where(function ($q3) {
                $q3->where('p.nombre', 'NOT LIKE', '%Impuesto a la Renta  por pagar%')
                    ->where('p.nombre', 'NOT LIKE', '%Ganancia Neta Del Periodo%')
                    ->where('p.nombre', 'NOT LIKE', '%PERDIDA NETA DEL %');
                //AND (p.nombre NOT LIKE '%Impuesto a la Renta  por pagar%' AND p.nombre NOT LIKE '%Ganancia Neta Del Periodo%' AND p.nombre NOT LIKE '%PERDIDA NETA DEL %')
            })
            ->groupBy('pe.plan');

        $finanAnterior = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo-1 as anio"),
                DB::raw('COALESCE(SUM(ad.debe), 0) AS debe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) AS haber'),
                DB::raw('IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS saldo_deudor'),
                DB::raw('IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS saldo_acreedor'),
                DB::raw('(IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)-IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0)) saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 1, "ACTIVOS", IF(SUBSTRING(pe.plan, 1, 1) = 2, "PASIVOS", "PATRIMONIO")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['1', '2', '3'])
            ->where('ac.fecha_asiento', '<', $desde)
            ->where(function ($q3) {
                $q3->where('p.nombre', 'NOT LIKE', '%Impuesto a la Renta  por pagar%')
                    ->where('p.nombre', 'NOT LIKE', '%Ganancia Neta Del Periodo%')
                    ->where('p.nombre', 'NOT LIKE', '%PERDIDA NETA DEL %');
                //AND (p.nombre NOT LIKE '%Impuesto a la Renta  por pagar%' AND p.nombre NOT LIKE '%Ganancia Neta Del Periodo%' AND p.nombre NOT LIKE '%PERDIDA NETA DEL %')
            })
            ->groupBy('pe.plan')
            ->get();
        $financiero = $finan2->orderBy('plan', 'asc')->get();
        $resul1 = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(ad.debe,0) as debe'),
                DB::raw('COALESCE(ad.haber, 0) AS haber'),
                DB::raw('0 AS saldo_deudor'),
                DB::raw('0 AS saldo_acreedor'),
                DB::raw('0 AS saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 4) = 5.1, "COSTOS", "GASTOS")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['4', '5'])
            ->whereNull('ac.fecha_asiento')
            ->where(function ($q) {
                $q->whereNull('ad.debe')
                    ->whereNull('ad.haber');
            });
        $resul2 = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(SUM(ad.debe), 0) AS debe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) AS haber'),
                DB::raw('IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS saldo_deudor'),
                DB::raw('IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS saldo_acreedor'),
                DB::raw('(IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0)-IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)) saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 4) = 5.1, "COSTOS", "GASTOS")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['4', '5'])
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->groupBy('pe.plan');

        $resultado = $resul2->orderBy('plan', 'asc')->get();
        $balance = $financiero->merge($resultado->merge($finanAnterior));

        if ($armar != '') {
            return $balance;
        }
        $html = '';
        $totalDebe = 0;
        $totalHaber = 0;
        $totalSaldoDebe = 0;
        $totalSaldoHaber = 0;
        if (count($balance) > 0) {
            foreach ($balance as $b) {
                $totalDebe += $b->debe;
                $totalHaber += $b->haber;
                $totalSaldoDebe += $b->saldo_deudor;
                $totalSaldoHaber += $b->saldo_acreedor;
                $html .= '<tr>';
                $html .= '<td>' . $b->plan . '</td>';
                $html .= '<td>' . $b->nombre . '</td>';
                $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->debe, 2)) . '</td>';
                $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->haber, 2)) . '</td>';
                $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->saldo_deudor, 2)) . '</td>';
                $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->saldo_acreedor, 2)) . '</td>';
                $html .= '</tr>';
            }
        }
        return $html . '|' . $totalDebe . '|' . $totalHaber . '|' . $totalSaldoDebe . '|' . $totalSaldoHaber;
    }
    public static function getBalanceComprobacion($r, $armar = '')
    {
        DB::enableQueryLog();
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', isset($r->d) ? $r->d : '');
        if (trim($desde) == '' || strtotime($desde) === false) {
            $desde = self::getFechaInicioSistema();
        }
        $desde = date('Y-m-d', strtotime($desde));
        $periodo = date('Y', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        $hasta_ = date("Y-m-d", strtotime($hasta . "+ 1 days"));
        $finan1 = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(ad.debe,0) as debe'),
                DB::raw('COALESCE(ad.haber, 0) AS haber'),
                DB::raw('0 AS saldo_deudor'),
                DB::raw('0 AS saldo_acreedor'),
                DB::raw('0 AS saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 1, "ACTIVOS", IF(SUBSTRING(pe.plan, 1, 1) = 2, "PASIVOS", "PATRIMONIO")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q1) use ($empresa) {
                $q1->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['1', '2', '3'])
            ->whereNull('ac.fecha_asiento')
            ->where(function ($q2) {
                $q2->whereNull('ad.debe')
                    ->whereNull('ad.haber');
            })
            ->where(function ($q3) {
                $q3->where('p.nombre', 'NOT LIKE', '%Impuesto a la Renta  por pagar%')
                    ->where('p.nombre', 'NOT LIKE', '%Ganancia Neta Del Periodo%')
                    ->where('p.nombre', 'NOT LIKE', '%PERDIDA NETA DEL %');
                //AND (p.nombre NOT LIKE '%Impuesto a la Renta  por pagar%' AND p.nombre NOT LIKE '%Ganancia Neta Del Periodo%' AND p.nombre NOT LIKE '%PERDIDA NETA DEL %')
            });
        $finan2 = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(SUM(ad.debe), 0) AS debe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) AS haber'),
                DB::raw('IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS saldo_deudor'),
                DB::raw('IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS saldo_acreedor'),
                DB::raw('(IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)-IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0)) saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 1, "ACTIVOS", IF(SUBSTRING(pe.plan, 1, 1) = 2, "PASIVOS", "PATRIMONIO")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['1', '2', '3'])
            ->whereBetween('ac.fecha_asiento', [self::getFechaInicioSistema() . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where(function ($q3) {
                $q3->where('p.nombre', 'NOT LIKE', '%Impuesto a la Renta  por pagar%')
                    ->where('p.nombre', 'NOT LIKE', '%Ganancia Neta Del Periodo%')
                    ->where('p.nombre', 'NOT LIKE', '%PERDIDA NETA DEL %');
                //AND (p.nombre NOT LIKE '%Impuesto a la Renta  por pagar%' AND p.nombre NOT LIKE '%Ganancia Neta Del Periodo%' AND p.nombre NOT LIKE '%PERDIDA NETA DEL %')
            })
            ->groupBy('pe.plan');
        if ($armar != '')
            $financiero = $finan1->union($finan2)->orderBy('plan', 'asc')->get();
        else
            $financiero = $finan2->orderBy('plan', 'asc')->get();
        $resul1 = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(ad.debe,0) as debe'),
                DB::raw('COALESCE(ad.haber, 0) AS haber'),
                DB::raw('0 AS saldo_deudor'),
                DB::raw('0 AS saldo_acreedor'),
                DB::raw('0 AS saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 4) = 5.1, "COSTOS", "GASTOS")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['4', '5'])
            ->whereNull('ac.fecha_asiento')
            ->where(function ($q) {
                $q->whereNull('ad.debe')
                    ->whereNull('ad.haber');
            });
        $resul2 = DB::table('plan_cuentas as p')
            ->select(
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(SUM(ad.debe), 0) AS debe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) AS haber'),
                DB::raw('IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS saldo_deudor'),
                DB::raw('IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS saldo_acreedor'),
                DB::raw('(IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0)-IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)) saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 4) = 5.1, "COSTOS", "GASTOS")) AS tipo'),
                'pe.estado'
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['4', '5'])
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->groupBy('pe.plan');
        if ($armar != '')
            $resultado = $resul1->union($resul2)->orderBy('plan', 'asc')->get();
        else
            $resultado = $resul2->orderBy('plan', 'asc')->get();
        $balance = $financiero->merge($resultado);
        if ($armar != '') {
            return $balance;
        }
        $html = '';
        $totalDebe = 0;
        $totalHaber = 0;
        $totalSaldoDebe = 0;
        $totalSaldoHaber = 0;
        if (count($balance) > 0) {
            foreach ($balance as $b) {
                $totalDebe += $b->debe;
                $totalHaber += $b->haber;
                $totalSaldoDebe += $b->saldo_deudor;
                $totalSaldoHaber += $b->saldo_acreedor;
                $html .= '<tr>';
                $html .= '<td>' . $b->plan . '</td>';
                $html .= '<td>' . $b->nombre . '</td>';
                $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->debe, 2)) . '</td>';
                $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->haber, 2)) . '</td>';
                $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->saldo_deudor, 2)) . '</td>';
                $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->saldo_acreedor, 2)) . '</td>';
                $html .= '</tr>';
            }
        }
        return $html . '|' . $totalDebe . '|' . $totalHaber . '|' . $totalSaldoDebe . '|' . $totalSaldoHaber;
    }
    /*
    $libros=['ventas','compras','bancos','inventarios','importaciones','cajas','pagos', 'cliente','proveedor']
    */
    public static function apiCrearAsiento($libro = '', $tipo = '', $datos = [], $r, $tipo_venta = '', $recrear = 0, $idRetencion = '')
    {
        DB::beginTransaction();
        try {
            $input_cabecera = "";
            $idAsiento = "";
            if ($recrear == 0) {
                $input_cabecera =
                    [
                        'sucursal' => $datos['sucursal'],
                        'punto_emision' => isset($datos['punto_emision']) ? $datos['punto_emision'] : null,
                        'fecha_asiento' => $datos['fecha_asiento'],
                        'fact_numero' => $datos['fact_numero'],
                        'id_empresa' => $datos['id_empresa'],
                        'observacion' => $datos['observacion'],
                        'valor' => $datos['valor'],
                        'id_usuariocrea' => $datos['id_usuariocrea'],
                        'id_usuariomod' => $datos['id_usuariomod'],
                        'ip_creacion' => $datos['ip_creacion'],
                        'ip_modificacion' => $datos['ip_modificacion'],
                    ];
                $idAsiento = AsientosCabecera::insertGetId($input_cabecera);
            }
            $arrayRespuesta = [];
            $arrayCuentas = [];
            $arrayFinal = [];
            switch ($libro) {
                case 'ventas':
                    $arrayCuentaIngresoConImpuesto  = [];
                    $arrayCuentaIngresoSinImpuesto  = [];
                    $arrayCuentaIva = [];
                    $arrayCuentaPorCobrar = [];
                    $arrayCuentaCaja = '';
                    $arrayCuentaDescuento = [];
                    $planIva = '';
                    $planIvaNombre = '';
                    $planIngresoConImpuesto = '';
                    $planIngresoConIngresoNombre = '';
                    $planIngresoSinImpuesto = '';
                    $planIngresoSinIngresoNombre = '';
                    $planCuentasxCobrar = '';
                    $planCuentasxCobrarNombre = '';
                    $planCaja = '';
                    $planCajaNombre = '';
                    $planCuentasDescuento = '';
                    $planCuentasDescuentoNombre = '';
                    $iva = 0;
                    $ingresoConImpuesto = 0;
                    $ingresoSinImpuesto = 0;
                    $cuentaPorCobrar = 0;
                    $descuento = 0;
                    $cajaGeneral = 0;

                    if ($tipo == 'ingreso') {

                        if ($tipo_venta == 'venta') {
                            //  si existe el descuento
                            if (isset($r->descuentototal)) {
                                if ($r['descuentototal'] > 0) {
                                    $descuento = $r['descuentototal'];
                                    $planDescuento = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'descuentos%')
                                                ->Where('plan_cuentas_empresa.nombre', 'like', '%venta%');
                                        })->first();
                                    $planCuentasDescuento = $planDescuento->plan;
                                    $planCuentasDescuentoNombre = $planDescuento->nombre;
                                }
                            }

                            /*Fin Descuento en Ventas */
                            for ($i = 0; $i < count($r->cart_items); $i++) {
                                /*Inicio Cuentas por Cobrar */

                                $cuentaPorCobrar = $datos['valor'];
                                $planCuentaPorCobrar  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'cuenta%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%cliente%');
                                            });
                                    })->first();
                                $planCuentasxCobrar = $planCuentaPorCobrar->plan;
                                $planCuentasxCobrarNombre = $planCuentaPorCobrar->nombre;

                                /*Inicio Ingreso Venta sin Iva*/
                                $ingresoSinImpuesto += (float)$r->cart_items[$i]->price;
                                $planCuentaIngresoSinImpuesto = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'venta%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%0%');
                                            });
                                    })->first();
                                $planIngresoSinImpuesto = $planCuentaIngresoSinImpuesto->plan;
                                $planIngresoSinIngresoNombre = $planCuentaIngresoSinImpuesto->nombre;
                                /*Fin Ingreso Venta sin Iva*/

                                $arrayCuentaPorCobrar = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planCuentasxCobrar,
                                    'descripcion' => $planCuentasxCobrarNombre,
                                    'fecha' => $datos['fecha_asiento'],
                                    'debe' => $cuentaPorCobrar,
                                    'haber' => 0,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];

                                $arrayCuentaDescuento = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planCuentasDescuento,
                                    'descripcion' => $planCuentasDescuentoNombre,
                                    'fecha' => $datos['fecha_asiento'],
                                    'debe' => $descuento,
                                    'haber' => 0,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $arrayCuentaIngresoConImpuesto = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planIngresoConImpuesto,
                                    'descripcion' => $planIngresoConIngresoNombre,
                                    'fecha' => $datos['fecha_asiento'],
                                    'debe' => 0,
                                    'haber' => $ingresoConImpuesto,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $arrayCuentaIngresoSinImpuesto = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planIngresoSinImpuesto,
                                    'descripcion' => $planIngresoSinIngresoNombre,
                                    'fecha' => $datos['fecha_asiento'],
                                    'debe' => 0,
                                    'haber' => $ingresoSinImpuesto + $descuento,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $arrayCuentaIva = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planIva,
                                    'descripcion' => $planIvaNombre,
                                    'debe' => 0,
                                    'haber' => $iva,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                            $arrayTodasLasCuentas = [];
                            $arrayCuentas = [$arrayCuentaPorCobrar, $arrayCuentaDescuento, $arrayCuentaIngresoConImpuesto, $arrayCuentaIngresoSinImpuesto, $arrayCuentaIva];
                            for ($k = 0; $k < count($arrayCuentas); $k++) {

                                if (!empty($arrayCuentas[$k]['id_plan_cuenta'])) {
                                    $arrayFinal[] = $arrayCuentas[$k];

                                    AsientosDetalles::insert($arrayCuentas[$k]);
                                    array_push($arrayTodasLasCuentas, $arrayCuentas[$k]);
                                }
                            }
                        } elseif ($tipo_venta == 'comprobante_ingreso') {
                            $planCuentaPorCobrar  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'cuenta%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%cliente%');
                                        });
                                })->first();
                            if ($planCuentaPorCobrar == '') {
                                throw new Exception("No se encontro la cuenta de clientes");
                            }
                            $planCuentasxCobrar = $planCuentaPorCobrar->plan;
                            $planCuentasxCobrarNombre = $planCuentaPorCobrar->nombre;
                            $fechaCreacion = date('Y-m-d H:i:s');
                            $arrayClientesComerciales = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => $planCuentasxCobrar,
                                'descripcion'         => $planCuentasxCobrarNombre,
                                'fecha'               => $datos['fecha_asiento'],
                                'haber'               => $datos['valor'],
                                'debe'                => 0,
                                'estado'              => 1,
                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                'id_usuariomod' => $datos['id_usuariomod'],
                                'ip_creacion' => $datos['ip_creacion'],
                                'ip_modificacion' => $datos['ip_modificacion'],
                                'created_at' => $fechaCreacion,
                                'updated_at' => $fechaCreacion,
                            ];
                            AsientosDetalles::insert($arrayClientesComerciales);
                            $cajaGeneral = $datos["valor"];
                            $planCuentaCaja  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'caja%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'cajas%')
                                                ->orWhere('plan_cuentas_empresa.nombre', 'like', '%general%');
                                        });
                                })->first();
                            $planCaja = $planCuentaCaja->plan;
                            $planCajaNombre = $planCuentaCaja->nombre;
                            $arrayCuentaCaja = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planCaja,
                                'descripcion' => $planCajaNombre,
                                'fecha' => $datos['fecha_asiento'],
                                'debe' => $cajaGeneral,
                                'haber' => 0,
                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                'id_usuariomod' => $datos['id_usuariomod'],
                                'ip_creacion' => $datos['ip_creacion'],
                                'ip_modificacion' => $datos['ip_modificacion'],
                                'created_at' => $fechaCreacion,
                                'updated_at' => $fechaCreacion,
                            ];
                            AsientosDetalles::insert($arrayCuentaCaja);
                        }
                    }
                    break;
            }
            DB::commit();
            return $idAsiento;
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r,
            ];
            return $arrayRespuesta;
        }
    }
    public static function crearAsiento($libro = '', $tipo = '', $datos = [], $r, $tipo_venta = '', $recrear = 0, $idRetencion = '')
    {
        DB::beginTransaction();
        try {
            $input = $r->input();
            $input_cabecera = "";
            $idAsiento = "";
            if ($recrear == 0) {
                $input_cabecera = [
                    'sucursal' => $datos['sucursal'],
                    'punto_emision' => isset($datos['punto_emision']) ? $datos['punto_emision'] : null,
                    'fecha_asiento' => $datos['fecha_asiento'],
                    'fact_numero' => $datos['fact_numero'],
                    'id_empresa' => $datos['id_empresa'],
                    'observacion' => $datos['observacion'],
                    'valor' => $datos['valor'],
                    'id_usuariocrea' => $datos['id_usuariocrea'],
                    'id_usuariomod' => $datos['id_usuariomod'],
                    'ip_creacion' => $datos['ip_creacion'],
                    'ip_modificacion' => $datos['ip_modificacion'],
                ];
                $idAsiento = AsientosCabecera::insertGetId($input_cabecera);
            }
            $arrayRespuesta = [];
            $arrayCuentas = [];
            $arrayFinal = [];
            switch ($libro) {
                case 'activoFijo':
                    if ($tipo == 'compra') {

                        $globales = Globales::where('id_modulo', 2)->where('id_empresa', session("id_empresa"))->first();
                        $global_trans = Globales::where('id_modulo', 4)->where('id_empresa', session("id_empresa"))->first();

                        if (!is_null($global_trans)) {
                            if (!is_null($globales)) {
                                if (floatval($r['tarifa_iva1']) > 0) {
                                    if (isset($r->transporte) or !is_null($r->transporte) or isset($r->gasto) or !is_null($r->gasto)) {
                                        $plan_cuentas = Plan_Cuentas::find($globales->debe);
                                        $sum_trans = 0;
                                        $sum_gastos = 0;
                                        if (isset($r['val_iva_trans'])) {
                                            for ($trans = 0; $trans < count($r['val_iva_trans']); $trans++) {
                                                $sum_trans += $r['val_iva_trans'][$trans];
                                            }
                                        }
                                        if (isset($r['val_iva_gasto'])) {
                                            for ($gast = 0; $gast < count($r['val_iva_gasto']); $gast++) {
                                                $sum_gastos += $r['val_iva_gasto'][$gast];
                                            }
                                        }
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $plan_cuentas->id,
                                            'descripcion'         => $plan_cuentas->nombre,
                                            'fecha'               => $r['fecha_compra'],
                                            'debe'                => $r['tarifa_iva1'] - $sum_trans - $sum_gastos,
                                            'haber'               => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea'      => Auth::user()->id,
                                            'id_usuariomod'       => Auth::user()->id,
                                            'ip_creacion'         => Utilidades::getRealIP(),
                                            'ip_modificacion'     => Utilidades::getRealIP(),
                                        ]);


                                        $plan_trans = Plan_Cuentas::find($global_trans->debe);
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $plan_trans->id,
                                            'descripcion'         => $plan_trans->nombre,
                                            'fecha'               => $r['fecha_compra'],
                                            'debe'                => $sum_trans + $sum_gastos,
                                            'haber'               => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea'      => Auth::user()->id,
                                            'id_usuariomod'       => Auth::user()->id,
                                            'ip_creacion'         => Utilidades::getRealIP(),
                                            'ip_modificacion'     => Utilidades::getRealIP(),
                                        ]);
                                    } else {
                                        $plan_cuentas = Plan_Cuentas::find($globales->debe);;
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $plan_cuentas->id,
                                            'descripcion'         => $plan_cuentas->nombre,
                                            'fecha'               => $r['fecha_compra'],
                                            'debe'                => $r['tarifa_iva1'],
                                            'haber'               => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea'      => Auth::user()->id,
                                            'id_usuariomod'       => Auth::user()->id,
                                            'ip_creacion'         => Utilidades::getRealIP(),
                                            'ip_modificacion'     => Utilidades::getRealIP(),
                                        ]);
                                    }

                                    if ($r['total1'] != 0) {
                                        $plan = Configuraciones::where('id', '101')->first();
                                        for ($i = 0; $i < count($r['codigo']); $i++) {
                                            $a = $r["mdtiposid"][$i];
                                            $id_tipo = $r["mdtipo{$a}"];
                                            $tipo = AF_Tipo::find($id_tipo);
                                            if (!is_null($tipo)) {
                                                AsientosDetalles::insert([
                                                    'id_asiento_cabecera' => $idAsiento,
                                                    'id_plan_cuenta'      => $tipo->cuentamayor,
                                                    'descripcion'         => strtoupper($tipo->cuenta_mayor->nombre), //aqui puse el nombre de la cuenta del acreedor
                                                    'fecha'               => $r['fecha_compra'],
                                                    'debe'                => $r['precioneto'][$i] + $r['desc'][$i],
                                                    'haber'               => '0',
                                                    'estado'              => '1',
                                                    'id_usuariocrea'      => Auth::user()->id,
                                                    'id_usuariomod'       => Auth::user()->id,
                                                    'ip_creacion'         => Utilidades::getRealIP(),
                                                    'ip_modificacion'     => Utilidades::getRealIP(),
                                                ]);
                                            }
                                        }
                                        $cuentaMovilizacionesTransporte = PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre')
                                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                            ->where('plan_cuentas_empresa.plan', 'like', '5%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', 'MOVILIZACIONES Y TRANSPORTE%')
                                                    ->where(function ($query) {
                                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%MOVILIZACIONES Y%')
                                                            ->orWhere('plan_cuentas_empresa.nombre', 'like', '%TRANSPORTE%');
                                                    });
                                            })->first();


                                        if (!isset($cuentaMovilizacionesTransporte)) {
                                            throw new Exception("La cuenta movilizaciones y transporte no existe");
                                        }

                                        $plan = Plan_Cuentas_Empresa::where('id_plan', $cuentaMovilizacionesTransporte->plan)->first();
                                        if (floatval($r['precioneto_trans']) > 0) {
                                            $sum_trans = 0;
                                            for ($trans = 0; $trans < count($r['precioneto_trans']); $trans++) {
                                                $sum_trans += $r['precioneto_trans'][$trans];
                                            }

                                            AsientosDetalles::insert([
                                                'id_asiento_cabecera' => $idAsiento,
                                                'id_plan_cuenta'      => $cuentaMovilizacionesTransporte->plan,
                                                'descripcion'         => $plan->nombre,
                                                'fecha'               => $r['fecha_compra'],
                                                'haber'               => '0',
                                                'debe'                => $sum_trans,
                                                'estado'              => '1',
                                                'id_usuariocrea'      => Auth::user()->id,
                                                'id_usuariomod'       => Auth::user()->id,
                                                'ip_creacion'         => Utilidades::getRealIP(),
                                                'ip_modificacion'     => Utilidades::getRealIP(),
                                            ]);
                                        }
                                        if (floatval($r['precioneto_gasto']) > 0) {
                                            for ($val_gasto = 0; $val_gasto < count($r->id_plan); $val_gasto++) {
                                                $plancuenta = Plan_Cuentas_Empresa::where("id", $r->id_plan[$val_gasto])->first();
                                                $plancuenta = Plan_Cuentas::find(is_null($plancuenta->id_plan) ? $plancuenta->plan : $plancuenta->id_plan);


                                                AsientosDetalles::insert([
                                                    'id_asiento_cabecera' => $idAsiento,
                                                    'id_plan_cuenta'      => $plancuenta->id,
                                                    'descripcion'         => $plancuenta->nombre,
                                                    'fecha'               => $r['fecha_compra'],
                                                    'haber'               => '0',
                                                    'debe'                => $r['precioneto_gasto'][$val_gasto],
                                                    'estado'              => '1',
                                                    'id_usuariocrea'      => Auth::user()->id,
                                                    'id_usuariomod'       => Auth::user()->id,
                                                    'ip_creacion'         => Utilidades::getRealIP(),
                                                    'ip_modificacion'     => Utilidades::getRealIP(),
                                                ]);
                                            }
                                        }
                                    }
                                } else {
                                    if ($r['total1'] != 0) {
                                        for ($i = 0; $i < count($r->codigo); $i++) {
                                            $a = $r["mdtiposid"][$i];
                                            $id_tipo = $r["mdtipo{$a}"];
                                            $tipo = AF_Tipo::find($id_tipo);
                                            if (!is_null($tipo)) {
                                                $trans = 0;
                                                AsientosDetalles::insert([
                                                    'id_asiento_cabecera' => $idAsiento,
                                                    'id_plan_cuenta'      => $tipo->cuentamayor,
                                                    'descripcion'         => strtoupper($tipo->cuenta_mayor->nombre),
                                                    'fecha'               => $r['fecha_compra'],
                                                    'debe'                => $r['precioneto'][$i] + $r['desc'][$i],
                                                    'haber'               => '0',
                                                    'estado'              => '1',
                                                    'id_usuariocrea'      => Auth::user()->id,
                                                    'id_usuariomod'       => Auth::user()->id,
                                                    'ip_creacion'         => Utilidades::getRealIP(),
                                                    'ip_modificacion'     => Utilidades::getRealIP(),
                                                ]);
                                            } else {
                                                throw new Exception("La cuenta tipo: $id_tipo no existe.");
                                            }
                                        }
                                    }
                                    $valor_trans = 0;
                                    if (isset($r->codigo_trans)) {
                                        if (count($r->codigo_trans) > 0) {
                                            for ($o = 0; $o < count($r->codigo_trans); $o++) {
                                                $valor_trans += $r['precioneto_trans'][$o];
                                            }
                                        }
                                    }

                                    $cuentaMovilizacionesTransporte = PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '5%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'MOVILIZACIONES Y TRANSPORTE%')
                                                ->where(function ($query) {
                                                    $query->where('plan_cuentas_empresa.nombre', 'like', '%MOVILIZACIONES Y%')
                                                        ->orWhere('plan_cuentas_empresa.nombre', 'like', '%TRANSPORTE%');
                                                });
                                        })->first();


                                    if (!isset($cuentaMovilizacionesTransporte)) {
                                        throw new Exception("La cuenta movilizaciones y transporte no existe");
                                    }

                                    if ($valor_trans > 0) {
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $cuentaMovilizacionesTransporte->plan,
                                            'descripcion'         => $cuentaMovilizacionesTransporte->nombre,
                                            'fecha'               => $r['fecha_compra'],
                                            'haber'               => '0',
                                            'debe'                => $valor_trans,
                                            'estado'              => '1',
                                            'id_usuariocrea'      => Auth::user()->id,
                                            'id_usuariomod'       => Auth::user()->id,
                                            'ip_creacion'         => Utilidades::getRealIP(),
                                            'ip_modificacion'     => Utilidades::getRealIP(),
                                        ]);
                                    }

                                    if (isset($r->gasto) or !is_null($r->gasto)) {
                                        for ($i = 0; $i < count($r["id_plan"]); $i++) {
                                            $plancuenta = PlanCuentas::where("id", $r->id_plan[$i])->first();
                                            $plancuenta = Plan_Cuentas::find(is_null($plancuenta->id_plan) ? $plancuenta->plan : $plancuenta->id_plan);
                                            AsientosDetalles::insert([
                                                'id_asiento_cabecera' => $idAsiento,
                                                'id_plan_cuenta'      => $plancuenta->id,
                                                'descripcion'         => $plancuenta->nombre,
                                                'fecha'               => $r['fecha_compra'],
                                                'haber'               => '0',
                                                'debe'                => $r['precioneto_gasto'][$i],
                                                'estado'              => '1',
                                                'id_usuariocrea'      => Auth::user()->id,
                                                'id_usuariomod'       => Auth::user()->id,
                                                'ip_creacion'         => Utilidades::getRealIP(),
                                                'ip_modificacion'     => Utilidades::getRealIP(),
                                            ]);
                                        }
                                    }
                                }
                            } else {
                                throw new Exception("La cuenta iva del modelo globales no existe");
                            }
                        } else {
                            throw new Exception("La cuenta iva transporte AF del modelo globales no existe");
                        }

                        if ($r['total1'] > 0) {
                            $cuentaProveedoresLocales = PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '2%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'Proveedores Locales%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%Proveedores%')
                                                ->orWhere('plan_cuentas_empresa.nombre', 'like', '%Locales%');
                                        });
                                })->first();
                            if (!isset($cuentaProveedoresLocales)) {
                                throw new Exception("La cuenta proveedores locales no existe");
                            }

                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => $cuentaProveedoresLocales->plan,
                                'descripcion'         => strtoupper($cuentaProveedoresLocales->nombre),
                                'fecha'               => $r['fecha_compra'],
                                'debe'                => '0',
                                'haber'               => $r['total1'],
                                'estado'              => '1',
                                'id_usuariocrea'      => Auth::user()->id,
                                'id_usuariomod'       => Auth::user()->id,
                                'ip_creacion'         => Utilidades::getRealIP(),
                                'ip_modificacion'     => Utilidades::getRealIP(),
                            ]);
                            if ($r['descuento1'] > 0) {
                                $cuentaDescuentoVenta = PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'DESCUENTOS EN VENTAS%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%DESCUENTOS%')
                                                    ->orWhere('plan_cuentas_empresa.nombre', 'like', '%EN VENTAS%');
                                            });
                                    })->first();

                                if (!isset($cuentaDescuentoVenta)) {
                                    throw new Exception("La cuenta descuentos en ventas no existe");
                                }

                                AsientosDetalles::insert([
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta'      => $cuentaDescuentoVenta->plan,
                                    'descripcion'         => strtoupper($cuentaDescuentoVenta->nombre),
                                    'fecha'               => $r['fecha_compra'],
                                    'debe'                => '0',
                                    'haber'               => $r['descuento1'],
                                    'estado'              => '1',
                                    'id_usuariocrea'      => Auth::user()->id,
                                    'id_usuariomod'       => Auth::user()->id,
                                    'ip_creacion'         => Utilidades::getRealIP(),
                                    'ip_modificacion'     => Utilidades::getRealIP(),
                                ]);
                            }
                        }
                    }
                    break;

                case 'ventas':
                    $arrayCuentaIngresoConImpuesto  = [];
                    $arrayCuentaIngresoSinImpuesto  = [];
                    $arrayCuentaIva = [];
                    $arrayCuentaPorCobrar = [];
                    $arrayCuentaCaja = '';
                    $arrayCuentaDescuento = [];
                    $planIva = '';
                    $planIvaNombre = '';
                    $planIngresoConImpuesto = '';
                    $planIngresoConIngresoNombre = '';
                    $planIngresoSinImpuesto = '';
                    $planIngresoSinIngresoNombre = '';
                    $planCuentasxCobrar = '';
                    $planCuentasxCobrarNombre = '';
                    $planCaja = '';
                    $planCajaNombre = '';
                    $planCuentasDescuento = '';
                    $planCuentasDescuentoNombre = '';
                    $iva = 0;
                    $ingresoConImpuesto = 0;
                    $ingresoSinImpuesto = 0;
                    $cuentaPorCobrar = 0;
                    $descuento = 0;
                    $cajaGeneral = 0;
                    if ($tipo == 'ingreso') {
                        /*Inicio Descuento en Ventas */
                        if ($tipo_venta == 'conglomerada') {
                            if ($input['descuento1'] > 0) {
                                $descuento = $input['descuento1'];
                                $planDescuento = PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre', 'p.id')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'descuento%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%descuentos%')
                                                    ->orWhere('plan_cuentas_empresa.nombre', 'like', '%venta%');
                                            });
                                    })
                                    ->first();
                                $planCuentasDescuento = $planDescuento->id;
                                $planCuentasDescuentoNombre = $planDescuento->nombre;
                            }


                            /*Fin Descuento en Ventas */
                            for ($i = 0; $i < count($input['precio']); $i++) {

                                /*Inicio Cuentas por Cobrar */
                                $cuentaPorCobrar = $input['total1'];
                                $planCuentaPorCobrar  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_plan')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'cuenta%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%cliente%');
                                            });
                                    })->first();
                                $planCuentasxCobrar = $planCuentaPorCobrar->id_plan;
                                $planCuentasxCobrarNombre = $planCuentaPorCobrar->nombre;

                                /*Fin Cuentas por Cobrar */
                                $dproduto = isset($input['valoriva'][$i]) ? $input['valoriva'][$i] : null;

                                if (!is_null($dproduto)) {
                                    /*Inicio Ingreso Venta con Iva*/
                                    $ingresoConImpuesto += (float)$input['totalFinal'][$i];
                                    $planCuentaIngresoConImpuesto = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_plan')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'venta%')
                                                ->where(function ($query) {
                                                    $query->where('plan_cuentas_empresa.nombre', 'like', '%12%');
                                                });
                                        })->first();
                                    $planIngresoConImpuesto = $planCuentaIngresoConImpuesto->id_plan;
                                    $planIngresoConIngresoNombre = $planCuentaIngresoConImpuesto->nombre;
                                    /*Fin Ingreso Venta con Iva*/

                                    /*Inicio Iva*/
                                    $iva += isset($input['valoriva'][$i]) ?  (float)$input['valoriva'][$i] : 0.00;

                                    $planCuentaIva = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_plan')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '2%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'iva venta%')
                                                ->where(function ($query) {
                                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'iva en%')
                                                        ->orWhere('plan_cuentas_empresa.nombre', 'like', '%12%');
                                                });
                                        })
                                        ->first();
                                    $planIva = $planCuentaIva->id_plan;
                                    $planIvaNombre = $planCuentaIva->nombre;
                                    /*Fin Iva*/
                                } else {

                                    /*Inicio Ingreso Venta sin Iva*/

                                    $ingresoSinImpuesto += (float)$input['totalFinal'][$i];

                                    $planCuentaIngresoSinImpuesto = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_plan')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'venta%')
                                                ->where(function ($query) {
                                                    $query->where('plan_cuentas_empresa.nombre', 'like', '%0%');
                                                });
                                        })->first();
                                    $planIngresoSinImpuesto = $planCuentaIngresoSinImpuesto->id_plan;
                                    $planIngresoSinIngresoNombre = $planCuentaIngresoSinImpuesto->nombre;
                                    /*Fin Ingreso Venta sin Iva*/
                                }
                            }

                            $arrayCuentaPorCobrar[] = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planCuentasxCobrar,
                                'descripcion' => $planCuentasxCobrarNombre,
                                'fecha' => $datos['fecha_asiento'],
                                'debe' => $cuentaPorCobrar,
                                'haber' => 0,
                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                'id_usuariomod' => $datos['id_usuariomod'],
                                'ip_creacion' => $datos['ip_creacion'],
                                'ip_modificacion' => $datos['ip_modificacion'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            $arrayCuentaDescuento[] = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planCuentasDescuento,
                                'descripcion' => $planCuentasDescuentoNombre,
                                'fecha' => $datos['fecha_asiento'],
                                'debe' => $descuento,
                                'haber' => 0,
                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                'id_usuariomod' => $datos['id_usuariomod'],
                                'ip_creacion' => $datos['ip_creacion'],
                                'ip_modificacion' => $datos['ip_modificacion'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];

                            $arrayCuentaIngresoConImpuesto[] = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planIngresoConImpuesto,
                                'descripcion' => $planIngresoConIngresoNombre,
                                'fecha' => $datos['fecha_asiento'],
                                'debe' => 0,
                                'haber' => $ingresoConImpuesto,
                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                'id_usuariomod' => $datos['id_usuariomod'],
                                'ip_creacion' => $datos['ip_creacion'],
                                'ip_modificacion' => $datos['ip_modificacion'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];

                            $arrayCuentaIngresoSinImpuesto[] = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planIngresoSinImpuesto,
                                'descripcion' => $planIngresoSinIngresoNombre,
                                'fecha' => $datos['fecha_asiento'],
                                'debe' => 0,
                                'haber' => $ingresoSinImpuesto + $descuento,
                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                'id_usuariomod' => $datos['id_usuariomod'],
                                'ip_creacion' => $datos['ip_creacion'],
                                'ip_modificacion' => $datos['ip_modificacion'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];

                            $arrayCuentaIva[] = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planIva,
                                'descripcion' => $planIvaNombre,
                                'debe' => 0,
                                'haber' => $iva,
                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                'id_usuariomod' => $datos['id_usuariomod'],
                                'ip_creacion' => $datos['ip_creacion'],
                                'ip_modificacion' => $datos['ip_modificacion'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];



                            $arrayTodasLasCuentas = [];
                            $arrayCuentas = [$arrayCuentaPorCobrar, $arrayCuentaDescuento, $arrayCuentaIngresoConImpuesto, $arrayCuentaIngresoSinImpuesto, $arrayCuentaIva];

                            for ($k = 0; $k < count($arrayCuentas); $k++) {

                                if (!empty($arrayCuentas[$k][0]['id_plan_cuenta'])) {
                                    $arrayFinal[] = $arrayCuentas[$k][0];
                                    AsientosDetalles::insert($arrayCuentas[$k][0]);
                                    array_push($arrayTodasLasCuentas, $arrayCuentas[$k][0]);
                                }
                            }
                        } else if ($tipo_venta == 'venta') {
                            if ($input['descuentototal'] > 0) {
                                $descuento = $input['descuentototal'];
                                $planDescuento = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_plan')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'descuento%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%descuentos%')
                                                    ->orWhere('plan_cuentas_empresa.nombre', 'like', '%venta%');
                                            });
                                    })->first();
                                $planCuentasDescuento = $planDescuento->id_plan;
                                $planCuentasDescuentoNombre = $planDescuento->nombre;
                            }
                            /*Fin Descuento en Ventas */
                            for ($i = 0; $i < count($input['producto']); $i++) {
                                /*Inicio Cuentas por Cobrar */
                                //DB::enableQueryLog();
                                $cuentaPorCobrar = $input['total_pagar'];
                                $planCuentaPorCobrar  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_plan')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                    ->where('plan_cuentas_empresa.estado', 2)
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'cuenta%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%cliente%');
                                            });
                                    })
                                    ->first();
                                $planCuentasxCobrar = $planCuentaPorCobrar->id_plan;
                                $planCuentasxCobrarNombre = $planCuentaPorCobrar->nombre;
                                /*Fin Cuentas por Cobrar */
                                $dproduto = explode('|', $input['producto'][$i]);
                                if ($dproduto[3] == 1) {
                                    /*Inicio Ingreso Venta con Iva*/
                                    //DB::enableQueryLog();
                                    $ingresoConImpuesto += (float)$input['total'][$i];
                                    $planCuentaIngresoConImpuesto = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_plan')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                        ->where('plan_cuentas_empresa.estado', 2)
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'venta%')
                                                ->where('plan_cuentas_empresa.nombre', 'like', '%12%');
                                        })->first();

                                    if ($planCuentaIngresoConImpuesto == '') {
                                        throw new Exception("No se encuentra la cuenta para la venta con impuesto en ingresos");
                                    }

                                    $planIngresoConImpuesto = $planCuentaIngresoConImpuesto->id_plan;
                                    $planIngresoConIngresoNombre = $planCuentaIngresoConImpuesto->nombre;
                                    /*Fin Ingreso Venta con Iva*/
                                    /*Inicio Iva*/
                                    $iva += (float)$input['iva_fila'][$i];
                                    $planCuentaIva = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_plan')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '2%')
                                        ->where('plan_cuentas_empresa.estado', 2)
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'iva%')
                                                ->Where(function ($query) {
                                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'iva en%')
                                                        ->orWhere('plan_cuentas_empresa.nombre', 'like', '%12%');
                                                });
                                        })
                                        ->first();
                                    if ($planCuentaIngresoConImpuesto == '') {
                                        throw new Exception("No se encuentra la cuenta para la venta con impuesto en el pasivo");
                                    }
                                    $planIva = $planCuentaIva->id_plan;
                                    $planIvaNombre = $planCuentaIva->nombre;
                                    /*Fin Iva*/
                                } else {
                                    /*Inicio Ingreso Venta sin Iva*/
                                    $ingresoSinImpuesto += (float)$input['total'][$i];
                                    /*$planCuentaIngresoSinImpuesto = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'venta%')
                                                ->where(function ($query) {
                                                    $query->where('plan_cuentas_empresa.nombre', 'like', '%0%');
                                                });
                                        })->first();*/
                                    $planCuentaIngresoSinImpuesto = DB::table('plan_cuentas_empresa')
                                        ->join('plan_cuentas as p', function ($join) {
                                            $join->on('plan_cuentas_empresa.id_plan', '=', 'p.id')
                                                ->where('plan_cuentas_empresa.id_empresa', '=', session('id_empresa'));
                                        })
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', function ($query) {
                                            $query->select(DB::raw('MIN(plan_cuentas_empresa.plan)'))
                                                ->from('plan_cuentas_empresa')
                                                ->join('plan_cuentas as p', function ($join) {
                                                    $join->on('plan_cuentas_empresa.id_plan', '=', 'p.id')
                                                        ->where('plan_cuentas_empresa.id_empresa', '=', session('id_empresa'));
                                                })
                                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                                ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                                ->where(function ($query) {
                                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'alquile%')
                                                        ->orWhere('plan_cuentas_empresa.nombre', 'like', 'venta%');
                                                })
                                                ->where('plan_cuentas_empresa.nombre', 'like', '%0%');
                                        })
                                        ->where('plan_cuentas_empresa.estado', 2)
                                        ->select('plan_cuentas_empresa.plan', 'p.nombre', 'plan_cuentas_empresa.id_empresa', 'plan_cuentas_empresa.id_plan')
                                        ->first();
                                    /*DB::enableQueryLog();
                                    echo '<pre>';
                                    print_r($planCuentaIngresoSinImpuesto);
                                    exit;*/
                                    if ($planCuentaIngresoSinImpuesto == '') {
                                        throw new Exception("No se encuentra la cuenta para la venta sin impuesto en el pasivo");
                                    }
                                    $planIngresoSinImpuesto = $planCuentaIngresoSinImpuesto->id_plan;
                                    $planIngresoSinIngresoNombre = $planCuentaIngresoSinImpuesto->nombre;
                                    /*Fin Ingreso Venta sin Iva*/
                                }
                                $arrayCuentaPorCobrar = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planCuentasxCobrar,
                                    'descripcion' => $planCuentasxCobrarNombre,
                                    'fecha' => $datos['fecha_asiento'],
                                    'debe' => $cuentaPorCobrar,
                                    'haber' => 0,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $arrayCuentaDescuento = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planCuentasDescuento,
                                    'descripcion' => $planCuentasDescuentoNombre,
                                    'fecha' => $datos['fecha_asiento'],
                                    'debe' => $descuento,
                                    'haber' => 0,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $arrayCuentaIngresoConImpuesto = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planIngresoConImpuesto,
                                    'descripcion' => $planIngresoConIngresoNombre,
                                    'fecha' => $datos['fecha_asiento'],
                                    'debe' => 0,
                                    'haber' => $ingresoConImpuesto,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $arrayCuentaIngresoSinImpuesto = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planIngresoSinImpuesto,
                                    'descripcion' => $planIngresoSinIngresoNombre,
                                    'fecha' => $datos['fecha_asiento'],
                                    'debe' => 0,
                                    'haber' => $ingresoSinImpuesto + $descuento,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                                $arrayCuentaIva = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta' => $planIva,
                                    'descripcion' => $planIvaNombre,
                                    'debe' => 0,
                                    'haber' => $iva,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ];
                            }
                            $arrayTodasLasCuentas = [];
                            $arrayCuentas = [$arrayCuentaPorCobrar, $arrayCuentaDescuento, $arrayCuentaIngresoConImpuesto, $arrayCuentaIngresoSinImpuesto, $arrayCuentaIva];
                            $asi = AsientosCabecera::find($idAsiento);
                            for ($k = 0; $k < count($arrayCuentas); $k++) {
                                if (!empty($arrayCuentas[$k]['id_plan_cuenta'])) {
                                    $arrayFinal[] = $arrayCuentas[$k];
                                    AsientosDetalles::insert($arrayCuentas[$k]);
                                  


                                    array_push($arrayTodasLasCuentas, $arrayCuentas[$k]);
                                }
                            }
                        }
                      
                        if ($tipo_venta == 'anularVenta') {
                            $registro_venta = Ventas::find($r->id);

                            $consulta_cabecera_ctventas = AsientosCabecera::where('estado', '1')
                                ->where('id', $registro_venta->id_asiento)->first();

                            $consulta_detalle = AsientosDetalles::where('id_asiento_cabecera', $consulta_cabecera_ctventas->id)->get();
                            if (!empty($consulta_detalle)) {

                                foreach ($consulta_detalle as $value) {

                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera' => $idAsiento,
                                        'id_plan_cuenta'      => $value->id_plan_cuenta,
                                        'descripcion'         => $value->descripcion,
                                        'fecha'               => $consulta_cabecera_ctventas->fecha_asiento,
                                        'haber'               => $value->debe,
                                        'debe'                => $value->haber,
                                        'id_usuariocrea'      => Auth::user()->id,
                                        'id_usuariomod'       => Auth::user()->id,
                                        'ip_creacion'         => $datos['ip_creacion'],
                                        'ip_modificacion'     => $datos['ip_modificacion'],
                                        'created_at'          => date('Y-m-d H:i:s'),
                                        'updated_at'          => date('Y-m-d H:i:s'),

                                    ]);
                                }
                            }
                        }
                    } elseif ($tipo == 'comprobante_ingreso') {
                        $planCuentaPorCobrar  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'cuenta%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%cliente%');
                                    });
                            })->first();
                        if ($planCuentaPorCobrar == '') {
                            throw new Exception("No se encontro la cuenta de clientes");
                        }
                        $planCuentasxCobrar = $planCuentaPorCobrar->plan;
                        $planCuentasxCobrarNombre = $planCuentaPorCobrar->nombre;
                        $fechaCreacion = date('Y-m-d H:i:s');
                        $arrayClientesComerciales = [
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $planCuentasxCobrar,
                            'descripcion'         => $planCuentasxCobrarNombre,
                            'fecha'               => $datos['fecha_asiento'],
                            'haber'               => $datos['valor'],
                            'debe'                => 0,
                            'estado'              => 1,
                            'id_usuariocrea' => $datos['id_usuariocrea'],
                            'id_usuariomod' => $datos['id_usuariomod'],
                            'ip_creacion' => $datos['ip_creacion'],
                            'ip_modificacion' => $datos['ip_modificacion'],
                            'created_at' => $fechaCreacion,
                            'updated_at' => $fechaCreacion,
                        ];
                        AsientosDetalles::insert($arrayClientesComerciales);
                        $cajaGeneral = isset($input['total_pagar']) ? $input["total_pagar"] : $input["total1"];

                        $valorTotSin = 0;
                        foreach ($r->id_pago as $key => $value) {
                            if ($value != 7) {
                                $valorTotSin += $r["valor"][$key];
                            }
                        }
                        $planCuentaCaja  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'caja%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'cajas%')
                                            ->orWhere('plan_cuentas_empresa.nombre', 'like', '%general%');
                                    });
                            })->first();
                        $planCaja = $planCuentaCaja->plan;
                        $planCajaNombre = $planCuentaCaja->nombre;
                        $arrayCuentaCaja = [
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta' => $planCaja,
                            'descripcion' => $planCajaNombre,
                            'fecha' => $datos['fecha_asiento'],
                            'debe' => $valorTotSin,
                            'haber' => 0,
                            'id_usuariocrea' => $datos['id_usuariocrea'],
                            'id_usuariomod' => $datos['id_usuariomod'],
                            'ip_creacion' => $datos['ip_creacion'],
                            'ip_modificacion' => $datos['ip_modificacion'],
                            'created_at' => $fechaCreacion,
                            'updated_at' => $fechaCreacion,
                        ];
                        AsientosDetalles::insert($arrayCuentaCaja);
                    } else if ($tipo == 'facturadeventa') {
                        $idusuario = Auth::user()->id;
                        $ip_cliente = $_SERVER["REMOTE_ADDR"];

                        if ($r['total1'] > 0) {
                            $planCuentasxCobrarC  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'CUENTAS POR COBRAR%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%CLIENTES COMERCIALES%');
                                        });
                                })->first();

                            if ($planCuentasxCobrarC == '') {
                                throw new Exception("No se encontro la cuenta: CUENTAS POR COBRAR CLIENTES COMERCIALES");
                            }
                            $planCuentasxCobrar = $planCuentasxCobrarC->plan;
                            $planCuentasxCobrarNombre = $planCuentasxCobrarC->nombre;
                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => $planCuentasxCobrar,
                                'descripcion'         => $planCuentasxCobrarNombre,
                                'fecha'               => $r["fecha"],
                                'debe'                => $r['total1'],
                                'haber'               => '0',
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,
                                'ip_creacion'         => $ip_cliente,
                                'ip_modificacion'     => $ip_cliente,
                            ]);
                        }
                        if ($r['tarifa_iva1'] > 0) {
                            $planCuentasxIvaC  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '2%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'IVA VENTA%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%TARIFA  12%%');
                                        });
                                })->first();
                            if ($planCuentasxIvaC == '') {
                                throw new Exception("No se encontro la cuenta: IVA  VENTA TARIFA  12%");
                            }
                            $planCuentasxIva = $planCuentasxIvaC->plan;
                            $planCuentasxIvaNombre = $planCuentasxIvaC->nombre;
                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planCuentasxIva,
                                'descripcion' => $planCuentasxIvaNombre,
                                'fecha' => $r["fecha"],
                                'debe' => '0',
                                'haber' => $r['tarifa_iva1'],
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ]);
                        }

                        if ($r['subtotal_121'] > 0) {
                            $planCuentasxVentaC  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'VENTAS TARIFAS 12%%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%VENTAS TARIFAS 12%%');
                                        });
                                })->first();
                            if ($planCuentasxVentaC == '') {
                                throw new Exception("No se encontro la cuenta: VENTAS TARIFAS 12%");
                            }
                            $planCuentasxIva = $planCuentasxVentaC->plan;
                            $planCuentasxIvaNombre = $planCuentasxVentaC->nombre;
                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planCuentasxIva,
                                'descripcion' => $planCuentasxIvaNombre,
                                'fecha' => $r["fecha"],
                                'debe' => '0',
                                'haber' => $r['subtotal_121'],
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ]);
                        }

                        if ($r['subtotal_01'] > 0) {
                            $planCuentasxVentaZC  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '4%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'VENTAS TARIFA 0%%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%VENTAS TARIFA 0%%');
                                        });
                                })->first();
                            if ($planCuentasxVentaZC == '') {
                                throw new Exception("No se encontro la cuenta: VENTAS TARIFA 0%");
                            }
                            $planCuentasxVentaZ = $planCuentasxVentaZC->plan;
                            $planCuentasxVentaZNombre = $planCuentasxVentaZC->nombre;

                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planCuentasxVentaZ,
                                'descripcion' => $planCuentasxVentaZNombre,
                                'fecha' => $r["fecha"],
                                'debe' => '0',
                                'haber' => $r['subtotal_01'],
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ]);
                        }

                        $valor_descuento = $r['descuento1'];

                        if ($valor_descuento > 0) {

                            $planCuentasxDescuentoZC  = Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'DESCUENTOS EN VENTAS%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%DESCUENTOS EN VENTAS%');
                                        });
                                })->first();
                            if ($planCuentasxDescuentoZC == '') {
                                throw new Exception("No se encontro la cuenta: DESCUENTOS EN VENTAS%");
                            }
                            $planCuentasxDescuentosTZ = $planCuentasxDescuentoZC->plan;
                            $planCuentasxVentaDescuentosNombre = $planCuentasxDescuentoZC->nombre;

                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta' => $planCuentasxDescuentosTZ,
                                'descripcion' => $planCuentasxVentaDescuentosNombre,
                                'fecha' => $r["fecha"],
                                'haber' => '0',
                                'debe' => $r['descuento1'],
                                'id_usuariocrea' => $idusuario,
                                'id_usuariomod' => $idusuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                            ]);
                        }
                    }
                    break;
                case 'compras':
                    if ($tipo == 'retencion') {
                        $proveedor = Proveedor::getProveedores($r['id_proveedor_']);
                        $nombre_plan = Plan_Cuentas::where('id', $proveedor->id_cuentas)->first();
                        if (!is_null($nombre_plan)) {
                            $arrayDetalleAsiento = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => $proveedor->id_cuentas,
                                'descripcion'         => $nombre_plan->nombre,
                                'fecha'               => $r['fecha_retencion'],
                                'debe'                => $r['total'],
                                'haber'               => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $datos['id_usuariocrea'],
                                'id_usuariomod'       => $datos['id_usuariocrea'],
                                'ip_creacion'         => $datos['ip_modificacion'],
                                'ip_modificacion'     => $datos['ip_modificacion'],
                            ];
                            AsientosDetalles::insert($arrayDetalleAsiento);
                            for ($i = 0; $i < count($r->tipo_retencion); $i++) {
                                if (($r["codigo_retencion"][$i] != null && $r["porcentaje_retencion"][$i])) {
                                    if ($r["tipo_retencion"][$i] == 2) {
                                        $consulta_plan_debe = PorcentajeRetenciones::where('id', $r["codigo_retencion"][$i])->where('estado', 1)->where('tipo', 2)->first();
                                        if (!is_null($consulta_plan_debe)) {
                                            AsientosDetalles::insert([
                                                'id_asiento_cabecera' => $idAsiento,
                                                'id_plan_cuenta'      => $consulta_plan_debe->cuenta_acreedores,
                                                'descripcion'         => $consulta_plan_debe->nombre,
                                                'fecha'               => $r['fecha_retencion'],
                                                'haber'               => $r["valor_retencion"][$i],
                                                'debe'                => '0',
                                                'estado'              => '1',
                                                'id_usuariocrea'      => Auth::user()->id,
                                                'id_usuariomod'       => Auth::user()->id,
                                                'ip_creacion'         => $_SERVER["REMOTE_ADDR"],
                                                'ip_modificacion'     => $_SERVER["REMOTE_ADDR"],
                                            ]);
                                        } else {
                                            throw new Exception("La cuenta porcentaje retención no existe");
                                        }
                                    }
                                    if ($r["tipo_retencion"][$i] == 1) {
                                        $consulta_plan_debe = PorcentajeRetenciones::where('id', $r["codigo_retencion"][$i])->where('estado', 1)->where('tipo', 1)->first();
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $consulta_plan_debe->cuenta_acreedores,
                                            'descripcion'         => $consulta_plan_debe->nombre,
                                            'fecha'               => $r['fecha_retencion'],
                                            'haber'               => $r["valor_retencion"][$i],
                                            'debe'                => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea'      => Auth::user()->id,
                                            'id_usuariomod'       => Auth::user()->id,
                                            'ip_creacion'         => $_SERVER["REMOTE_ADDR"],
                                            'ip_modificacion'     => $_SERVER["REMOTE_ADDR"],
                                        ]);
                                    }
                                }
                            }
                        } else {
                            throw new Exception('La cuenta del proveedor: ' . $proveedor->id_cuentas . ', no existe en el plan de cuentas');
                        }
                    } else if ($tipo == 'compra') {
                        $globales = Globales::where('id_modulo', 1)->where('id_empresa', session("id_empresa"))->first();
                        $valorDescuento = $input["descuento"];
                        $base1 = $input['total'];
                        $total1 = $input['total_pagar'];
                        if ($valorDescuento > 0) {
                            $base1  = (float)$base1 + (float)$valorDescuento;
                            $total1 = $total1 + $valorDescuento;
                        }
                        //$id_plan_confg = Utilidades::busqueda('1.01.03.01.02');
                        $id_plan_confg = PlanCuentas::buscarIdCuentaPlan('PROVEEDORES LOCAL');
                        $planMercaderiaTransito = Plan_Cuentas::find($id_plan_confg->id_plan);
                        if (!is_null($planMercaderiaTransito)) {
                            $total_iva = array_sum($input["iva_fila_sin"]);
                            if ($total_iva > 0) {
                                $global = Globales::where('id_modulo', 20)->where('id_empresa', session("id_empresa"))->first();
                                if (!is_null($global)) {
                                    if ($input["tipoC"] == 0) {
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $planMercaderiaTransito->id,
                                            'descripcion'         => $planMercaderiaTransito->nombre,
                                            'fecha'               => $input["fecha"],
                                            'debe'                => $base1,
                                            'haber'               => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea' => $datos['id_usuariocrea'],
                                            'id_usuariomod' => $datos['id_usuariomod'],
                                            'ip_creacion' => $datos['ip_creacion'],
                                            'ip_modificacion' => $datos['ip_modificacion'],
                                        ]);
                                    } else {
                                        foreach ($input["codigo"] as $key => $value) {
                                            //       plan_cuentas_empresa 

                                            $menuE = Plan_Cuentas::join("plan_cuentas_empresa", "plan_cuentas.id", "plan_cuentas_empresa.id_plan")
                                                ->where("plan", $value)
                                                ->first([
                                                    "plan_cuentas.id as plan",
                                                    "plan_cuentas_empresa.nombre"
                                                ]);
                                            AsientosDetalles::insert([
                                                'id_asiento_cabecera' => $idAsiento,
                                                'id_plan_cuenta'      => $menuE->plan,
                                                'descripcion'         => $menuE->nombre,
                                                'fecha'               => $input["fecha"],
                                                'debe'                => $input["totald"][$key],
                                                'haber'               => '0',
                                                'estado'              => '1',
                                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                                'id_usuariomod' => $datos['id_usuariomod'],
                                                'ip_creacion' => $datos['ip_creacion'],
                                                'ip_modificacion' => $datos['ip_modificacion'],
                                            ]);
                                        }
                                    }
                                    $plan_cuentas = Plan_Cuentas::find($globales->debe);
                                    if ($input["iva"] > 0) {
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $plan_cuentas->id,
                                            'descripcion'         => $plan_cuentas->nombre,
                                            'fecha'               => $input["fecha"],
                                            'debe'                => $input["iva"],
                                            'haber'               => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea' => $datos['id_usuariocrea'],
                                            'id_usuariomod' => $datos['id_usuariomod'],
                                            'ip_creacion' => $datos['ip_creacion'],
                                            'ip_modificacion' => $datos['ip_modificacion'],
                                        ]);
                                    }
                                    if ($input["iva_mc"] > 0) {
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $plan_cuentas->id,
                                            'descripcion'         => $plan_cuentas->nombre,
                                            'fecha'               => $input["fecha"],
                                            'debe'                => $input["iva_mc"],
                                            'haber'               => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea' => $datos['id_usuariocrea'],
                                            'id_usuariomod' => $datos['id_usuariomod'],
                                            'ip_creacion' => $datos['ip_creacion'],
                                            'ip_modificacion' => $datos['ip_modificacion'],
                                        ]);
                                    }
                                } else {
                                    throw new Exception("No existe la cuenta importacion de gasto");
                                }
                            } else {
                                if ($input["tipoC"] == 0) {
                                    $global = Globales::where('id_modulo', 20)->where('id_empresa', session("id_empresa"))->first();

                                    if (!is_null($global)) {
                                        AsientosDetalles::insert([

                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $planMercaderiaTransito->id,
                                            'descripcion'         => $planMercaderiaTransito->nombre,
                                            'fecha'               => date("Y-m-d"),
                                            'debe'                => $total1,
                                            'haber'               => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea' => $datos['id_usuariocrea'],
                                            'id_usuariomod' => $datos['id_usuariomod'],
                                            'ip_creacion' => $datos['ip_creacion'],
                                            'ip_modificacion' => $datos['ip_modificacion'],
                                        ]);
                                    } else {
                                        throw new Exception("No existe la cuenta asientos fc compras");
                                    }
                                } else {

                                    foreach ($input["codigo"] as $key => $value) {
                                        $menuE = Plan_Cuentas::join("plan_cuentas_empresa", "plan_cuentas.id", "plan_cuentas_empresa.id_plan")
                                            ->where("plan", $value)
                                            ->first([
                                                "plan_cuentas.id as plan",
                                                "plan_cuentas_empresa.nombre"
                                            ]);
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $menuE->plan,
                                            'descripcion'         => $menuE->nombre,
                                            'fecha'               => $input["fecha"],
                                            'debe'                => $input["totald"][$key],
                                            'haber'               => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea' => $datos['id_usuariocrea'],
                                            'id_usuariomod' => $datos['id_usuariomod'],
                                            'ip_creacion' => $datos['ip_creacion'],
                                            'ip_modificacion' => $datos['ip_modificacion'],
                                        ]);
                                    }
                                }
                            }
                        } else {
                            throw new Exception("No existe la cuenta mercaderias en transito");
                        }

                        if ($valorDescuento > 0) {
                            $cuenta_proveedor = $input['proveedor'];
                            if (!is_null($cuenta_proveedor)) {
                                $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                                if (!is_null($consulta_en_proveedor)) {
                                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera' => $idAsiento,
                                        'id_plan_cuenta'      => $desc_cuenta->id,
                                        'descripcion'         => $desc_cuenta->nombre,
                                        'fecha'               => $input["fecha"],
                                        'haber'               => $input['total_pagar'],
                                        'debe'                => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea' => $datos['id_usuariocrea'],
                                        'id_usuariomod' => $datos['id_usuariomod'],
                                        'ip_creacion' => $datos['ip_creacion'],
                                        'ip_modificacion' => $datos['ip_modificacion'],
                                    ]);
                                }
                            }
                            $id_plan_confg = Utilidades::busqueda('2.01.03.01.01');
                            $planDescuentoCompra = Plan_Cuentas::find($id_plan_confg);
                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => $planDescuentoCompra->id,
                                'descripcion'         => $planDescuentoCompra->nombre,
                                'fecha'               => $input["fecha"],
                                'debe'                => '0',
                                'haber'               => $input["descuento"],
                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                'id_usuariomod' => $datos['id_usuariomod'],
                                'ip_creacion' => $datos['ip_creacion'],
                                'ip_modificacion' => $datos['ip_modificacion'],
                            ]);
                        } else {
                            $cuenta_proveedor = $input['proveedor'];
                            if (!is_null($cuenta_proveedor)) {
                                $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                                if (!is_null($consulta_en_proveedor)) {
                                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                                    if (!is_null($desc_cuenta)) {
                                        AsientosDetalles::insert([
                                            'id_asiento_cabecera' => $idAsiento,
                                            'id_plan_cuenta'      => $desc_cuenta->id,
                                            'descripcion'         => $desc_cuenta->nombre,
                                            'fecha'               => $input["fecha"],
                                            'haber'               => $input['total_pagar'],
                                            'debe'                => '0',
                                            'estado'              => '1',
                                            'id_usuariocrea' => $datos['id_usuariocrea'],
                                            'id_usuariomod' => $datos['id_usuariomod'],
                                            'ip_creacion' => $datos['ip_creacion'],
                                            'ip_modificacion' => $datos['ip_modificacion'],
                                        ]);
                                    } else {
                                        $id_plan_confg = Utilidades::busqueda('2.01.03.01.01');
                                        $planProvedoresLocales = Plan_Cuentas::find($id_plan_confg);
                                        if (!is_null($planProvedoresLocales)) {
                                            AsientosDetalles::insert([
                                                'id_asiento_cabecera' => $idAsiento,
                                                'id_plan_cuenta'      => $planProvedoresLocales->id,
                                                'descripcion'         => $planProvedoresLocales->nombre,
                                                'fecha'               => $input["fecha"],
                                                'haber'               => $input['total_pagar'],
                                                'debe'                => '0',
                                                'estado'              => '1',
                                                'id_usuariocrea' => $datos['id_usuariocrea'],
                                                'id_usuariomod' => $datos['id_usuariomod'],
                                                'ip_creacion' => $datos['ip_creacion'],
                                                'ip_modificacion' => $datos['ip_modificacion'],
                                            ]);
                                        } else {
                                            throw new Exception("No existe la cuenta mercaderias en transito");
                                        }
                                    }
                                } else {
                                    //$id_plan_confg = Utilidades::busqueda('2.01.03.01.01');
                                    $busPlan = PlanCuentas::where('id_empresa', session('id_empresa'))
                                        ->where('nombre', 'LIKE', '%prove%')
                                        ->where('nombre', 'LIKE', '%loc%')
                                        ->where('id_padre', 'LIKE', '2%')
                                        ->first();
                                    if ($busPlan == '') {
                                        throw new Exception("La cuenta proveedores locales no se encuentra en el plan de cuentas");
                                    }
                                    $planProvedoresLocales = Plan_Cuentas::find($busPlan->id_plan);
                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera' => $idAsiento,
                                        'id_plan_cuenta'      => $planProvedoresLocales->id,
                                        'descripcion'         => $planProvedoresLocales->nombre,
                                        'fecha'               => $input["fecha"],
                                        'haber'               => $input['descuento'],
                                        'debe'                => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea' => $datos['id_usuariocrea'],
                                        'id_usuariomod' => $datos['id_usuariomod'],
                                        'ip_creacion' => $datos['ip_creacion'],
                                        'ip_modificacion' => $datos['ip_modificacion'],
                                    ]);
                                }
                            }
                        }
                    }
                    break;
                case 'cliente':
                    if ($tipo == 'comprobante_ingreso') {
                        if (!is_null($input['restante'])) {
                            if ($input["restante"] > 0) {
                                $rest = $input['valor_total'] - $input['restante'];
                                if ($rest < 0) {
                                    $rest = $rest * (-1);
                                }

                                $planCuentaCaja =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '2%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'ANTICIPO%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%CLIENTES%');
                                            });
                                    })->first();

                                if (is_null($planCuentaCaja)) {
                                    throw new Exception("La cuenta anticipos clientes no se encuentra en el plan de cuentas");
                                }
                                $planCuentasxCobrar = $planCuentaCaja->plan;
                                $planCuentasxCobrarNombre = $planCuentaCaja->nombre;
                                $fechaCreacion = date('Y-m-d H:i:s');
                                $arrayDetalleUno = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta'      => $planCuentasxCobrar,
                                    'descripcion'         => $planCuentasxCobrarNombre,
                                    'fecha'               => $datos['fecha_asiento'],
                                    'haber'               => $input['restante'],
                                    'debe'                => 0,
                                    'estado'              => 1,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => $fechaCreacion,
                                    'updated_at' => $fechaCreacion,
                                ];
                                AsientosDetalles::insert($arrayDetalleUno);


                                $planClientesComer =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'CUENTAS POR COBRAR%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%CLIENTES COMERCIALES%');
                                            });
                                    })->first();

                                if (is_null($planCuentaCaja)) {
                                    throw new Exception("La cuenta cuentas por cobrar a clientes comerciales no se encuentra en el plan de cuentas");
                                }

                                $planCuentasxComer = $planClientesComer->plan;
                                $planCuentasxCobrarComer = $planClientesComer->nombre;
                                $fechaCreacion = date('Y-m-d H:i:s');
                                $arrayDetalleUno = [
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta'      => $planCuentasxComer,
                                    'descripcion'         => $planCuentasxCobrarComer,
                                    'fecha'               => $datos['fecha_asiento'],
                                    'haber'               => $rest,
                                    'debe'                => 0,
                                    'estado'              => 1,
                                    'id_usuariocrea' => $datos['id_usuariocrea'],
                                    'id_usuariomod' => $datos['id_usuariomod'],
                                    'ip_creacion' => $datos['ip_creacion'],
                                    'ip_modificacion' => $datos['ip_modificacion'],
                                    'created_at' => $fechaCreacion,
                                    'updated_at' => $fechaCreacion,
                                ];
                                AsientosDetalles::insert($arrayDetalleUno);
                            } else {
                                $planCuentaCaja =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'CUENTAS POR COBRAR CLIENTES COMERCIALES%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%CUENTAS POR COBRAR CLIENTES COMERCIALES%');
                                            });
                                    })->first();
                                if (is_null($planCuentaCaja)) {
                                    throw new Exception("La cuentas por cobrar clientes comerciales no se encuentra en el plan de cuentas");
                                }
                                $planCuentasxCobrarClientes = $planCuentaCaja->plan;
                                $planCuentasxCobrarNombreClientes = $planCuentaCaja->nombre;
                                AsientosDetalles::insert([
                                    'id_asiento_cabecera' => $idAsiento,
                                    'id_plan_cuenta'      => $planCuentasxCobrarClientes,
                                    'descripcion'         => $planCuentasxCobrarNombreClientes,
                                    'fecha'               => $datos['fecha_asiento'],
                                    'haber'               => $datos['valor'],
                                    'debe'                => '0',
                                    'estado'              => '1',
                                    'id_usuariocrea'      => $datos['id_usuariocrea'],
                                    'id_usuariomod'       => $datos['id_usuariocrea'],
                                    'ip_creacion'         => $datos['ip_creacion'],
                                    'ip_modificacion'     => $datos['ip_creacion'],
                                ]);
                            }
                        } else {
                            $planCuentaCaja =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'CUENTAS POR COBRAR CLIENTES COMERCIALES%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%CUENTAS POR COBRAR CLIENTES COMERCIALES%');
                                        });
                                })->first();
                            if (is_null($planCuentaCaja)) {
                                throw new Exception("La cuentas por cobrar clientes comerciales no se encuentra en el plan de cuentas");
                            }
                            $planCuentasxCobrarClientes = $planCuentaCaja->plan;
                            $planCuentasxCobrarNombreClientes = $planCuentaCaja->nombre;
                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => $planCuentasxCobrarClientes,
                                'descripcion'         => $planCuentasxCobrarNombreClientes,
                                'fecha'               => $datos['fecha_asiento'],
                                'haber'               => $datos['valor'],
                                'debe'                => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $datos['id_usuariocrea'],
                                'id_usuariomod'       => $datos['id_usuariocrea'],
                                'ip_creacion'         => $datos['ip_creacion'],
                                'ip_modificacion'     => $datos['ip_creacion'],
                            ]);
                        }


                        $planCuentaCajaGeneralP =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'CAJA GENERAL%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%CAJA GENERAL%');
                                    });
                            })->first();

                        if (is_null($planCuentaCaja)) {
                            throw new Exception("La cuenta caja general no se encuentra en el plan de cuentas");
                        }
                        $planCuentaCajaGeneral = $planCuentaCajaGeneralP->plan;
                        $planCuentasCajaGeneral = $planCuentaCajaGeneralP->nombre;
                        $fechaCreacion = date('Y-m-d H:i:s');
                        $arrayDetalleD = [
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $planCuentaCajaGeneral,
                            'descripcion'         => $planCuentasCajaGeneral,
                            'fecha'               => $datos['fecha_asiento'],
                            'haber'               => 0,
                            'debe'                => $datos['valor'],
                            'estado'              => 1,
                            'id_usuariocrea' => $datos['id_usuariocrea'],
                            'id_usuariomod' => $datos['id_usuariomod'],
                            'ip_creacion' => $datos['ip_creacion'],
                            'ip_modificacion' => $datos['ip_modificacion'],
                            'created_at' => $fechaCreacion,
                            'updated_at' => $fechaCreacion,
                        ];
                        AsientosDetalles::insert($arrayDetalleD);
                    } else if ($tipo == 'retencionCliente') {
                        $id_asiento_cab = Retenciones_Clientes::find($idRetencion);
                        $consulta_retenciones   = DetalleClienteRetencion::where('id_cliente_retencion', $idRetencion)->where('estado', '1')->get();
                        $valorsf = $input['total_r'];
                        foreach ($consulta_retenciones as $value) {
                            if ($value->codigo != null && $value->id_porcentaje != null) {
                                if ($value->tipo == 'RENTA') {
                                    $consulta_plan_debe  = PorcentajeRetenciones::where('id', $value->id_porcentaje)->first();
                                    $valors = $value->totales;
                                    $arrayRetencionCliente = [
                                        'id_asiento_cabecera' => $id_asiento_cab->id_asiento_cabecera,
                                        'id_plan_cuenta'      => $consulta_plan_debe->cuenta_deudora,
                                        'descripcion'         => $consulta_plan_debe->nombre,
                                        'fecha'               => $input['fecha_retencion'],
                                        'debe'                => $valors,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => Auth::user()->id,
                                        'id_usuariomod'       => Auth::user()->id,
                                        'ip_creacion'         => Utilidades::getRealIP(),
                                        'ip_modificacion'     => Utilidades::getRealIP(),
                                    ];
                                    AsientosDetalles::insert($arrayRetencionCliente);
                                }
                                if ($value->tipo == 'IVA') {
                                    $consulta_plan_debe  = PorcentajeRetenciones::where('id', $value->id_porcentaje)->first();
                                    $valors = $value->totales;
                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera' => $id_asiento_cab->id_asiento_cabecera,
                                        'id_plan_cuenta'      => $consulta_plan_debe->cuenta_deudora,
                                        'descripcion'         => $consulta_plan_debe->nombre,
                                        'fecha'               => $input['fecha_retencion'],
                                        'debe'                => $valors,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => Auth::user()->id,
                                        'id_usuariomod'       => Auth::user()->id,
                                        'ip_creacion'         => Utilidades::getRealIP(),
                                        'ip_modificacion'     => Utilidades::getRealIP(),
                                    ]);
                                }
                            }
                        }
                        $cta_x_cob_cli  = Configuraciones::where('id', 5)->first();
                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $id_asiento_cab->id_asiento_cabecera,
                            'id_plan_cuenta'      => $cta_x_cob_cli->id_plan,
                            'descripcion'         => $cta_x_cob_cli->cuentaM->nombre,
                            'fecha'               => $input['fecha_retencion'],
                            'debe'                => '0',
                            'haber'               => $valorsf,
                            'estado'              => '1',
                            'id_usuariocrea'      => Auth::user()->id,
                            'id_usuariomod'       => Auth::user()->id,
                            'ip_creacion'         => Utilidades::getRealIP(),
                            'ip_modificacion'     => Utilidades::getRealIP(),
                        ]);
                        $idAsiento = $id_asiento_cab->id_asiento_cabecera;
                    } else if ($tipo == 'cruce_cuenta') {

                        for ($i = 0; $i < count($input['detalle']); $i++) {
                            $plan_cuenta = Plan_Cuentas::find($input['cuenta'][$i]);
                            if (!is_null($plan_cuenta)) {
                                AsientosDetalles::insert([
                                    'id_asiento_cabecera'           => $idAsiento,
                                    'id_plan_cuenta'                => $input['cuenta'][$i],
                                    'descripcion'                   => $plan_cuenta->nombre,
                                    'fecha'                         => $input['fecha'],
                                    'debe'                         => $input['valor'][$i],
                                    'haber'                          => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => Auth::user()->id,
                                    'id_usuariomod'                 => Auth::user()->id,
                                    'ip_creacion'                   => Utilidades::getRealIP(),
                                    'ip_modificacion'               => Utilidades::getRealIP(),
                                ]);
                            } else {
                                throw new Exception("La cuenta" . $input["cuenta"][$i] . "no existe en el plan de cuenta");
                            }
                        }

                        $planCuentaPorCobrar =  PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'CUENTAS POR COBRAR%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%CLIENTES COMERCIALES%');
                                    });
                            })->first();
                        if (!is_null($planCuentaPorCobrar)) {
                            AsientosDetalles::insert([
                                'id_asiento_cabecera'           => $idAsiento,
                                'id_plan_cuenta'                => $planCuentaPorCobrar->plan,
                                'descripcion'                   => $planCuentaPorCobrar->nombre,
                                'fecha'                         => $input['fecha'],
                                'haber'                          => $input['total'],
                                'debe'                         => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => Auth::user()->id,
                                'id_usuariomod'                 => Auth::user()->id,
                                'ip_creacion'                   => Utilidades::getRealIP(),
                                'ip_modificacion'               => Utilidades::getRealIP(),
                            ]);
                        }
                    } else if ($tipo == 'comprobanteIngresoDeficit') {

                        $rest = $r->total_suma - $r->total_favor;

                        if ($rest < 0) {
                            $rest = $rest * (-1);
                        }

                        $planAnticipoCliente =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '2%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'anticipo%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%de clientes%');
                                    });
                            })->first();

                        if (is_null($planAnticipoCliente)) {
                            throw new Exception("No se encontro la cuenta anticipo clientes");
                        }

                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $planAnticipoCliente->plan,
                            'descripcion'         => $planAnticipoCliente->nombre,
                            'fecha'               => $r->fecha,
                            'haber'                => $r->total_favor,
                            'debe'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $datos["id_usuariocrea"],
                            'id_usuariomod'       => $datos["id_usuariomod"],
                            'ip_creacion'         => $datos["ip_modificacion"],
                            'ip_modificacion'     => $datos["ip_creacion"],
                        ]);

                        $planCobrarxClientes =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'cuentas por cobrar clientes comerciales%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%cuentas por cobrar clientes comerciales%');
                                    });
                            })->first();


                        if (is_null($planCobrarxClientes)) {
                            throw new Exception("No se encontro la cuenta por cobrar clientes comerciales");
                        }

                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $planCobrarxClientes->plan,
                            'descripcion'         => $planCobrarxClientes->nombre,
                            'fecha'               => $r->fecha,
                            'haber'               => $rest,
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $datos["id_usuariocrea"],
                            'id_usuariomod'       => $datos["id_usuariomod"],
                            'ip_creacion'         => $datos["ip_modificacion"],
                            'ip_modificacion'     => $datos["ip_creacion"],
                        ]);

                        $planCajaGeneral =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'caja%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%general%');
                                    });
                            })->first();

                        if (is_null($planCajaGeneral)) {
                            throw new Exception("No se encontro la cuenta caja general");
                        }

                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $planCajaGeneral->plan,
                            'descripcion'         => $planCajaGeneral->nombre,
                            'fecha'               => $r->fecha,
                            'debe'                => $r->total_suma,
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $datos["id_usuariocrea"],
                            'id_usuariomod'       => $datos["id_usuariomod"],
                            'ip_creacion'         => $datos["ip_modificacion"],
                            'ip_modificacion'     => $datos["ip_creacion"],
                        ]);
                    } else if ($tipo == 'notacreditoclientes') {


                        $CuentaxCobrarClientesComerciales =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'CUENTAS POR COBRAR%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%CLIENTES COMERCIALES%');
                                    });
                            })->first();
                        if (is_null($CuentaxCobrarClientesComerciales)) {
                            throw new Exception("La cuentas por cobrar clientes comerciales por la empresa no existe");
                        }
                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $CuentaxCobrarClientesComerciales->plan,
                            'descripcion'         => $CuentaxCobrarClientesComerciales->nombre,
                            'fecha'               => $r["fecha"],
                            'haber'               => $r['total1'],
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => Auth::user()->id,
                            'id_usuariomod'       => Auth::user()->id,
                            'ip_creacion'         => Utilidades::getRealIP(),
                            'ip_modificacion'     => Utilidades::getRealIP(),
                        ]);


                        if ($r['impuesto1'] > 0) {
                            $CuentaAnticipoClientes =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '2%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'ANTICIPO%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%CLIENTES%');
                                        });
                                })->first();
                            if (is_null($CuentaAnticipoClientes)) {
                                throw new Exception("La cuentas anticipio clientes por la empresa no existe");
                            }
                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => $CuentaAnticipoClientes->plan,
                                'descripcion'         => $CuentaAnticipoClientes->nombre,
                                'fecha'               => $r["fecha"],
                                'debe'                => $r['impuesto1'],
                                'haber'               => '0',
                                'id_usuariocrea'      => Auth::user()->id,
                                'id_usuariomod'       => Auth::user()->id,
                                'ip_creacion'         => Utilidades::getRealIP(),
                                'ip_modificacion'     => Utilidades::getRealIP(),
                            ]);
                        }

                        $CuentaDevolucionVentas =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '4%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'DEVOLUCIONES%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%EN VENTAS%');
                                    });
                            })->first();
                        if (is_null($CuentaDevolucionVentas)) {
                            throw new Exception("La cuentas devolución de venta por la empresa no existe");
                        }

                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $CuentaDevolucionVentas->plan,
                            'descripcion'         => $CuentaDevolucionVentas->nombre,
                            'fecha'               => $r["fecha"],
                            'debe'                => $r['subtotal1'],
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => Auth::user()->id,
                            'id_usuariomod'       => Auth::user()->id,
                            'ip_creacion'         => Utilidades::getRealIP(),
                            'ip_modificacion'     => Utilidades::getRealIP(),
                        ]);
                    } else if ($tipo == 'cruce_valores') {

                        $CuentaxCobrarClientesComerciales =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'CUENTAS POR COBRAR%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%CLIENTES COMERCIALES%');
                                    });
                            })->first();
                        if (is_null($CuentaxCobrarClientesComerciales)) {
                            throw new Exception("La cuentas por cobrar clientes comerciales por la empresa no existe");
                        }
                        AsientosDetalles::insert([

                            'id_asiento_cabecera'           => $idAsiento,
                            'id_plan_cuenta'                => $CuentaxCobrarClientesComerciales->plan,
                            'descripcion'                   => $CuentaxCobrarClientesComerciales->nombre,
                            'fecha'                         => $r->fecha,
                            'haber'                         => $r->total_anticipos,
                            'debe'                          => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                => Auth::user()->id,
                            'id_usuariomod'                 => Auth::user()->id,
                            'ip_creacion'                   => Utilidades::getRealIP(),
                            'ip_modificacion'               => Utilidades::getRealIP(),
                        ]);

                        $CuentaAnticipoClientes =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '2%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'ANTICIPO%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%CLIENTES%');
                                    });
                            })->first();
                        if (is_null($CuentaAnticipoClientes)) {
                            throw new Exception("La cuentas anticipio clientes por la empresa no existe");
                        }
                        AsientosDetalles::insert([
                            'id_asiento_cabecera'           => $idAsiento,
                            'id_plan_cuenta'                => $CuentaAnticipoClientes->plan,
                            'descripcion'                   => $CuentaAnticipoClientes->nombre,
                            'fecha'                         => $r->fecha,
                            'debe'                          => $r->total_anticipos,
                            'haber'                         => '0',
                            'estado'                        => '1',
                            'id_usuariocrea'                 => Auth::user()->id,
                            'id_usuariomod'                 => Auth::user()->id,
                            'ip_creacion'                   => Utilidades::getRealIP(),
                            'ip_modificacion'               => Utilidades::getRealIP(),
                        ]);
                    }
                    break;
                case 'cajaBanco':
                    if ($tipo == 'deposito') {
                        $planCuentaCaja =  Plan_Cuentas_Empresa::select('plan_cuentas_empresa.plan', 'p.nombre')
                            ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                            ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                            ->where('plan_cuentas_empresa.plan', 'like', '1%')
                            ->where(function ($query) {
                                $query->where('plan_cuentas_empresa.nombre', 'like', 'caja%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', '%general%');
                                    });
                            })->first();

                        $total_final = 0;
                        if (!is_null($input['total_efectivo'])) {
                            $total_final = $input['total_efectivo'];
                        }
                        if (!is_null($input['total_pap_deposito'])) {
                            $total_final = $input['total_pap_deposito'];
                        }
                        if (!is_null($input['total_cheques'])) {
                            $total_final = $input['total_cheques'];
                        }
                        if (!is_null($input['total_tarjetas'])) {
                            $total_final = $input['total_tarjetas'];
                        }
                        if (!is_null($input['total_datalink'])) {
                            $total_final = $input['total_datalink'];
                        }
                        if (!is_null($input['total_crucecuentas'])) {
                            $total_final = $input['total_crucecuentas'];
                        }
                        if (!is_null($input['total_deposito'])) {
                            $total_final = $input['total_deposito'];
                        }
                        $fechaCreacion = date('Y-m-d H:i:s');
                        $planCaja = $planCuentaCaja->plan;
                        $planCajaNombre = $planCuentaCaja->nombre;
                        $arrayCuentaCaja = [
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta' => $planCaja,
                            'descripcion' => $planCajaNombre,
                            'fecha' => $datos['fecha_asiento'],
                            'haber' => $total_final,
                            'debe' => 0,
                            'estado' => 1,
                            'id_usuariocrea' => $datos['id_usuariocrea'],
                            'id_usuariomod' => $datos['id_usuariomod'],
                            'ip_creacion' => $datos['ip_creacion'],
                            'ip_modificacion' => $datos['ip_modificacion'],
                            'created_at' => $fechaCreacion,
                            'updated_at' => $fechaCreacion,
                        ];
                        AsientosDetalles::insert($arrayCuentaCaja);
                        $id_cuenta_destino = Plan_Cuentas_Empresa::where('id_plan', $input['id_cuenta_destino'])
                            ->where('id_empresa', session('id_empresa'))->first();
                        AsientosDetalles::insert([
                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $id_cuenta_destino->id_plan,
                            'descripcion'         => $input['concepto'],
                            'fecha'               => $input['fecha_asiento'],
                            'haber'               => '0',
                            'debe'                => $input['valor1'],
                            'estado'              => '1',
                            'id_usuariocrea'      => $datos['id_usuariomod'],
                            'id_usuariomod'       => $datos['id_usuariomod'],
                            'ip_creacion'         => $datos['ip_creacion'],
                            'ip_modificacion'     => $datos['ip_creacion'],
                        ]);
                    } else if ($tipo == 'notadebito') {

                        foreach ($r["debe"] as $key => $value) {

                            $cuentaV = explode("|", $r["id_det_nd"][$key]);
                            $as_detalle = [
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => trim($cuentaV[0]),
                                'descripcion'         => trim($cuentaV[1]),
                                'fecha'               => $r["fecha"],
                                'debe'                => $value,
                                'haber'               => '0.00',
                                'id_usuariocrea'      => Auth::user()->id,
                                'id_usuariomod'       => Auth::user()->id,
                                'ip_creacion'         => Utilidades::getRealIP(),
                                'ip_modificacion'     => Utilidades::getRealIP(),
                            ];
                            AsientosDetalles::insert($as_detalle);
                        }
                        $cuenta_banco = CajaBanco::where('id', $r['id_banco'])->first();
                        $plan_c      = Plan_Cuentas::where('id', $cuenta_banco['cuenta_mayor'])->first();
                        $as_cuenta_g = [

                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $plan_c['id'],
                            'descripcion'         => $plan_c['nombre'],
                            'fecha'               => $r["fecha"],
                            'debe'                => '0.00',
                            'haber'               => $r['total_detalle'],
                            'id_usuariocrea'      => Auth::user()->id,
                            'id_usuariomod'       => Auth::user()->id,
                            'ip_creacion'         => Utilidades::getRealIP(),
                            'ip_modificacion'     => Utilidades::getRealIP(),
                        ];
                        AsientosDetalles::insert($as_cuenta_g);
                    } else if ($tipo == 'anularanotadebito') {
                        $nota_debito_cj = NotadeDebito::where('id', $r->id)->first();
                        $consulta_cabecera_ctventas = AsientosCabecera::where('estado', '1')
                            ->where('id', $nota_debito_cj['id_asiento'])->first();
                        $consulta_detalle = AsientosDetalles::where('id_asiento_cabecera', $nota_debito_cj->id_asiento)->get();
                        foreach ($consulta_detalle as $value) {
                            AsientosDetalles::insert([
                                'id_asiento_cabecera' => $idAsiento,
                                'id_plan_cuenta'      => $value->id_plan_cuenta,
                                'descripcion'         => 'ANULACION DE NOTA DE DEBITO',
                                'fecha'               => $consulta_cabecera_ctventas->fecha_asiento,
                                'haber'               => $value->debe,
                                'debe'                => $value->haber,
                                'id_usuariocrea'      => Auth::user()->id,
                                'id_usuariomod'       => Auth::user()->id,
                                'ip_creacion'         =>  Utilidades::getRealIP(),
                                'ip_modificacion'     =>  Utilidades::getRealIP(),
                            ]);
                        }
                        LogContable::insert([
                            'tipo'           => 'ND',
                            'valor_ant'      => $consulta_cabecera_ctventas->valor,
                            'valor'          => $consulta_cabecera_ctventas->valor,
                            'id_usuariocrea' => Auth::user()->id,
                            'id_usuariomod'  => Auth::user()->id,
                            'observacion'    => $consulta_cabecera_ctventas->concepto,
                            'id_ant'         => $consulta_cabecera_ctventas->id,
                            'id_referencia'  => $idAsiento,
                        ]);
                    } else if ($tipo == 'notadedebitobancaria') {
                        $id_proveedor = $r['id_acreedor'];
                        $cuentas      = Proveedor::where('id', $id_proveedor)->first();
                        for ($i = 0; $i < count($r['fecha_vencimiento']); $i++) {
                            $desc_cuenta = Plan_Cuentas::where('id', $cuentas->id_cuentas)->first();
                            $consulta_compra = Compras::where('id', $r['id_compra'][$i])->where('id_empresa', session("id_empresa"))->where('estado', '<>', '0')->first();
                            if ($r['abono'][$i] > 0) {
                                if ($consulta_compra != null) {
                                    if ($r['abono'][$i] > 0) {
                                        if (!is_null($desc_cuenta)) {
                                            $asiento_detalle = [
                                                'id_asiento_cabecera' => $idAsiento,
                                                'id_plan_cuenta'      => $desc_cuenta->id,
                                                'descripcion'         => $desc_cuenta->nombre,
                                                'fecha'               => $r['fecha'],
                                                'debe'                => $r['abono'][$i],
                                                'haber'               => '0',
                                                'estado'              => '1',
                                                'id_usuariocrea'      => Auth::user()->id,
                                                'id_usuariomod'       => Auth::user()->id,
                                                'ip_creacion'         => Utilidades::getRealIP(),
                                                'ip_modificacion'     => Utilidades::getRealIP(),
                                            ];
                                            AsientosDetalles::insert($asiento_detalle);
                                        }
                                    }
                                }
                            }
                        }
                        if ($r['debito_favor'] > 0) {
                            $planAnticipoProveedor = PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre')
                                ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                ->where('plan_cuentas_empresa.plan', 'like', '1%')
                                ->where(function ($query) {
                                    $query->where('plan_cuentas_empresa.nombre', 'like', 'ANTICIPO A PROVEEDORES%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', '%PROVEEDORES LOCALES%')
                                                ->orWhere('plan_cuentas_empresa.nombre', 'like', '%PROVEEDORES LOCALES%');
                                        });
                                })->first();
                            if (!isset($planAnticipoProveedor)) {
                                throw new Exception("La cuenta anticipos proveedores no existe");
                            }
                            $asiento_detalle = [
                                'id_asiento_cabecera'           => $idAsiento,
                                'id_plan_cuenta'                => $planAnticipoProveedor->plan, //cambiar a anticipo proveedores
                                'descripcion'                   => $planAnticipoProveedor->nombre,
                                'fecha'                         => $r['fecha'],
                                'debe'                          => $r["valor"],
                                'haber'                         => '0',
                                'estado'                        => '1',
                                'id_usuariocrea'                => Auth::user()->id,
                                'id_usuariomod'                 => Auth::user()->id,
                                'ip_creacion'                   => Utilidades::getRealIP(),
                                'ip_modificacion'               => Utilidades::getRealIP(),

                            ];

                            AsientosDetalles::insert($asiento_detalle);
                        }

                        $cuenta_banco = CajaBanco::where('id', $r['id_banco'])->first();
                        $plan_c = Plan_Cuentas::where('id', $cuenta_banco['cuenta_mayor'])->first();
                        $as_cuenta_g = [

                            'id_asiento_cabecera' => $idAsiento,
                            'id_plan_cuenta'      => $plan_c['id'],
                            'descripcion'         => $plan_c['nombre'],
                            'fecha'               => $r["fecha"],
                            'debe'                => '0.00',
                            'haber'               => $r['valor'],
                            'id_usuariocrea'      => Auth::user()->id,
                            'id_usuariomod'       => Auth::user()->id,
                            'ip_creacion'         => Utilidades::getRealIP(),
                            'ip_modificacion'     => Utilidades::getRealIP(),

                        ];
                        AsientosDetalles::insert($as_cuenta_g);
                    }
                    break;
                case 'proveedores':

                    if ($tipo == 'comprobanteegreso') {
                        $nuevo_saldof = $input['total_favor'] == null ? 0 : $input['total_favor'];
                        $superavit = (int) $input['superavit'];
                        $cuentas = Proveedor::where('id', $input["nombre_acreedores"])->first();
                        if ($superavit != 0) {


                            if (($input["fecha"]) != null) {
                                $consulta_db_cajab = CajaBanco::where('id', $input["banco"])->first();
                                AsientosDetalles::create([
                                    'id_asiento_cabecera'           => $idAsiento,
                                    'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                                    'descripcion'                   => $consulta_db_cajab->nombre,
                                    'fecha'                         => $input['fecha'],
                                    'haber'                         => $nuevo_saldof,
                                    'debe'                          => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => Auth::user()->id,
                                    'id_usuariomod'                 => Auth::user()->id,
                                    'ip_creacion'                   => $_SERVER["REMOTE_ADDR"],
                                    'ip_modificacion'               => $_SERVER["REMOTE_ADDR"],
                                ]);

                                $saldos_ret = 0;
                                $rt_asumida_valor = 0;
                                $rt_estado = 0;

                                for ($i = 0; $i <= $input['contador_total']; $i++) {
                                    if (isset($input["valorasumir" . $i])) {
                                        if ($input["valorasumir" . $i] != null || $input["valorasumir" . $i] != "" &&  $input["valorasumir" . $i] > 0) {

                                            $saldos_ret += $input["valorasumir" . $i];
                                        }
                                    }
                                }
                                if ($input["nombre_acreedores"] != null) {
                                    $globales = Globales::where('id_empresa', session("id_empresa"))->where('id_modulo', 18)->first();
                                    $desc_cuenta = Plan_Cuentas::where('id', $globales->debe)->first();
                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera'           => $idAsiento,
                                        'id_plan_cuenta'                => $desc_cuenta->id,
                                        'descripcion'                   => $desc_cuenta->nombre,
                                        'fecha'                         => $input['fecha'],
                                        'debe'                          => $saldos_ret,
                                        'haber'                         => '0',
                                        'estado'                        => '1',
                                        'id_usuariocrea'                => Auth::user()->id,
                                        'id_usuariomod'                 => Auth::user()->id,
                                        'ip_creacion'                   => $_SERVER["REMOTE_ADDR"],
                                        'ip_modificacion'               => $_SERVER["REMOTE_ADDR"],
                                    ]);
                                }

                                if ($saldos_ret > 0) {
                                    $rt_estado = 1;
                                    $rt_cuenta = Plan_Cuentas::where('id', '5.2.02.16.17')->first();

                                    if (!isset($rt_cuenta)) {
                                        throw new Exception("La cuenta retenciones asumidas por la empresa no existe");
                                    }
                                    $arrayAsiento = [
                                        'id_asiento_cabecera'           => $idAsiento,
                                        'id_plan_cuenta'                => $rt_cuenta->id,
                                        'descripcion'                   => $rt_cuenta->nombre,
                                        'fecha'                         => $input['fecha'],
                                        'debe'                          => $saldos_ret,
                                        'haber'                         => '0',
                                        'estado'                        => '1',
                                        'id_usuariocrea'                => Auth::user()->id,
                                        'id_usuariomod'                 => Auth::user()->id,
                                        'ip_creacion'                   => $_SERVER["REMOTE_ADDR"],
                                        'ip_modificacion'               => $_SERVER["REMOTE_ADDR"],
                                    ];

                                    AsientosDetalles::insert($arrayAsiento);
                                }
                            }
                        } else {
                            if ($input['contador_total'] != null) {
                                $rt_asimida_creado = 0;
                                if (($input["banco"]) != null) {
                                    $nuevo_saldof = $input['valor'];
                                    $consulta_db_cajab = CajaBanco::where('id', $input["banco"])->first();
                                    $desc_cuenta = Plan_Cuentas::where('id', $cuentas->id_cuentas)->first();
                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera'           => $idAsiento,
                                        'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                                        'descripcion'                   => $consulta_db_cajab->nombre,
                                        'fecha'                         => $input['fecha'],
                                        'haber'                         => $nuevo_saldof,
                                        'debe'                          => '0',
                                        'estado'                        => '1',
                                        'id_usuariocrea'                => Auth::user()->id,
                                        'id_usuariomod'                 => Auth::user()->id,
                                        'ip_creacion'                   => $_SERVER["REMOTE_ADDR"],
                                        'ip_modificacion'               => $_SERVER["REMOTE_ADDR"],
                                    ]);
                                }


                                $planDescuento = PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre')
                                    ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                    ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                    ->where('plan_cuentas_empresa.plan', 'like', '2%')
                                    ->where(function ($query) {
                                        $query->where('plan_cuentas_empresa.nombre', 'like', 'proveedores%')
                                            ->where(function ($query) {
                                                $query->where('plan_cuentas_empresa.nombre', 'like', '%proveedores%')
                                                    ->orWhere('plan_cuentas_empresa.nombre', 'like', '%lovales%');
                                            });
                                    })->first();
                                if (!isset($planDescuento)) {
                                    throw new Exception("La cuenta proveedores locales no existe");
                                }

                                $planCuentasDescuento = $planDescuento->plan;
                                $planCuentasDescuentoNombre = $planDescuento->nombre;
                                for ($i = 0; $i <= $input['contador_total']; $i++) {
                                    if (isset($input['abono' . $i])) {
                                        if ($input['abono' . $i] > 0) {
                                            if ($input["nombre_acreedores"] != null) {
                                                $nuevo_saldof = $input['abono' . $i];
                                                if ($input["valorasumir" . $i] != '0.00') {
                                                    if ($rt_asimida_creado == 0) {
                                                        $nuevo_saldof = $input["saldo" . $i] + $input["valorasumir" . $i];
                                                        $rt_asimida_creado = 1;
                                                    }
                                                }
                                                AsientosDetalles::insert([
                                                    'id_asiento_cabecera'           => $idAsiento,
                                                    'id_plan_cuenta'                => $planCuentasDescuento,
                                                    'descripcion'                   => $planCuentasDescuentoNombre,
                                                    'fecha'                         => $input['fecha'],
                                                    'debe'                          => $nuevo_saldof,
                                                    'haber'                         => '0',
                                                    'estado'                        => '1',
                                                    'id_usuariocrea'                => Auth::user()->id,
                                                    'id_usuariomod'                 => Auth::user()->id,
                                                    'ip_creacion'                   => $_SERVER["REMOTE_ADDR"],
                                                    'ip_modificacion'               => $_SERVER["REMOTE_ADDR"],
                                                ]);
                                            }
                                        }
                                    }
                                }
                                $nuevosaldox = $input['total_favor'];
                                $rt_asumidas = 0;
                                for ($i = 0; $i <= $input['contador_total']; $i++) {
                                    if (isset($input["valorasumir" . $i])) {
                                        if ($input["valorasumir" . $i] != null || $input["valorasumir" . $i] != "" &&  $input["valorasumir" . $i] > 0) {

                                            $rt_asumidas += $input["valorasumir" . $i];
                                        }
                                    }
                                }
                                if ($rt_asimida_creado == 1) {

                                    $planRetencionesAsumidasPorLaEmpresa = PlanCuentas::select('plan_cuentas_empresa.plan', 'p.nombre')
                                        ->join('plan_cuentas as p', 'plan_cuentas_empresa.id_plan', 'p.id')
                                        ->where('plan_cuentas_empresa.id_empresa', session('id_empresa'))
                                        ->where('plan_cuentas_empresa.plan', 'like', '5%')
                                        ->where(function ($query) {
                                            $query->where('plan_cuentas_empresa.nombre', 'like', 'retenciones%')
                                                ->where(function ($query) {
                                                    $query->where('plan_cuentas_empresa.nombre', 'like', '%retenciones%')
                                                        ->orWhere('plan_cuentas_empresa.nombre', 'like', '%asumidas por la empresa%');
                                                });
                                        })->first();
                                    if (!isset($planDescuento)) {
                                        throw new Exception("La cuenta retenciones asumidas por empresa no existe");
                                    }

                                    $planRetencionesAsumidasPorId = $planRetencionesAsumidasPorLaEmpresa->plan;
                                    $planRetencionesAsumidasPorNombre = $planRetencionesAsumidasPorLaEmpresa->nombre;

                                    AsientosDetalles::insert([
                                        'id_asiento_cabecera'           => $idAsiento,
                                        'id_plan_cuenta'                => $planRetencionesAsumidasPorId,
                                        'descripcion'                   => $planRetencionesAsumidasPorNombre,
                                        'fecha'                         => $input['fecha'],
                                        'debe'                          => $rt_asumidas,
                                        'haber'                         => '0',
                                        'estado'                        => '1',
                                        'id_usuariocrea'                => Auth::user()->id,
                                        'id_usuariomod'                 => Auth::user()->id,
                                        'ip_creacion'                   => $_SERVER["REMOTE_ADDR"],
                                        'ip_modificacion'               => $_SERVER["REMOTE_ADDR"],
                                    ]);
                                    $rt_asimida_creado = 1;
                                }
                                if ((int)$input['comprobarx'] == 1) {
                                    if ($rt_asimida_creado == 0) {
                                        $globales = Globales::where('id_empresa', session("id_empresa"))->where('id_modulo', 18)->first();
                                        AsientosDetalles::create([
                                            'id_asiento_cabecera'           => $idAsiento,
                                            'id_plan_cuenta'                => $globales->debe,
                                            'descripcion'                   => 'Anticipo a Proveedores',
                                            'fecha'                         => $input['fecha'],
                                            'debe'                          => $input['total_favor'] == null ? 0 : $input['total_favor'],
                                            'haber'                         => '0',
                                            'estado'                        => '1',
                                            'id_usuariocrea'                => Auth::user()->id,
                                            'id_usuariomod'                 => Auth::user()->id,
                                            'ip_creacion'                   => $_SERVER["REMOTE_ADDR"],
                                            'ip_modificacion'               => $_SERVER["REMOTE_ADDR"],
                                        ]);
                                    }
                                }
                            }
                        }
                    } else if ($tipo == 'crearComprobanteEgresovarios') {
                        if (!is_null($r->banco)) {
                            $consulta_db_cajab = CajaBanco::where('id', $r->banco)->first();
                            $desc_cuenta = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();

                            if (!isset($desc_cuenta)) {
                                throw new Exception("La cuenta del banco por la empresa no existe");
                            } else {
                                AsientosDetalles::insert([
                                    'id_asiento_cabecera'           => $idAsiento,
                                    'id_plan_cuenta'                => $consulta_db_cajab->cuenta_mayor,
                                    'descripcion'                   => $consulta_db_cajab->nombre,
                                    'fecha'                         => $r->fecha_hoy,
                                    'haber'                         => $r->valor,
                                    'debe'                          => '0',
                                    'estado'                        => '1',
                                    'id_usuariocrea'                => Auth::user()->id,
                                    'id_usuariomod'                 => Auth::user()->id,
                                    'ip_creacion'                   => $_SERVER["REMOTE_ADDR"],
                                    'ip_modificacion'               => $_SERVER["REMOTE_ADDR"],
                                ]);
                            }
                        }


                        for ($i = 0; $i < count($r->debe); $i++) {
                            $nuevo_saldof = $r->debe[$i];
                            if (!is_null($r["cuenta"][$i])) {

                                $desc_cuenta = Plan_Cuentas::where('id', $r['cuenta'][$i])->first();
                                if ($desc_cuenta != null) {
                                    if ($r['visibilidad'][$i] == 1 || $r['visibilidad'][$i] == '1') {
                                        if (!is_null($r['debe'][$i]) && $r['debe'][$i] > 0) {
                                            AsientosDetalles::insert([
                                                'id_asiento_cabecera' => $idAsiento,
                                                'id_plan_cuenta'      => $r['cuenta'][$i],
                                                'descripcion'         => $desc_cuenta->nombre,
                                                'fecha'               => $r['fecha_hoy'],
                                                'debe'                => $nuevo_saldof,
                                                'haber'               => '0',
                                                'estado'              => '1',
                                                'id_usuariocrea'      => Auth::user()->id,
                                                'id_usuariomod'       => Auth::user()->id,
                                                'ip_creacion'         => $_SERVER["REMOTE_ADDR"],
                                                'ip_modificacion'     => $_SERVER["REMOTE_ADDR"],
                                            ]);
                                        }
                                        if (!is_null($r['haber'][$i]) && $r['haber'][$i] > 0) {
                                            $nuevo_saldof = $r['haber'][$i];
                                            AsientosDetalles::insert([
                                                'id_asiento_cabecera' => $idAsiento,
                                                'id_plan_cuenta'      => $r['cuenta'][$i],
                                                'descripcion'         => $desc_cuenta->nombre,
                                                'fecha'               => $r['fecha_hoy'],
                                                'haber'               => $nuevo_saldof,
                                                'debe'                => '0',
                                                'estado'              => '1',
                                                'id_usuariocrea'      => Auth::user()->id,
                                                'id_usuariomod'       => Auth::user()->id,
                                                'ip_creacion'         => $_SERVER["REMOTE_ADDR"],
                                                'ip_modificacion'     => $_SERVER["REMOTE_ADDR"],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
            DB::commit();
            return $idAsiento;
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage() . ' | ' . $e->getLine() . ' | ' . $e->getFile(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            return $arrayRespuesta;
        }
    }
    public static function verAsiento($id)
    {
        $asientos = AsientosDetalles::join('ct_asientos_cabecera as ac', 'ct_asientos_detalle.id_asiento_cabecera', 'ac.id')
            ->join('plan_cuentas as pc', 'ct_asientos_detalle.id_plan_cuenta', 'pc.id')
            ->join("users", "users.id", "ac.id_usuariocrea")
            ->join('plan_cuentas_empresa as pe', function ($q) {
                $q->on('pc.id', 'pe.id_plan')
                    ->on('pe.id_empresa', 'ac.id_empresa');
            })
            ->where('id_asiento_cabecera', $id)
            ->get([
                'ct_asientos_detalle.id_asiento_cabecera',
                'ac.sucursal',
                'ac.modulo',
                'ac.fecha_asiento',
                'ac.estado',
                'ac.valor',
                'ac.observacion',
                'pe.plan',
                'pc.nombre',
                'ct_asientos_detalle.descripcion',
                'ct_asientos_detalle.debe',
                'ct_asientos_detalle.haber',
                DB::raw('concat(users.nombre1," ",users.apellido1," ",users.apellido2) as nombreC'),
            ]);
        $html = '';
        if (count($asientos) > 0) {
            $estado = 'Activo';
            if ($asientos[0]->estado == 0) {
                $estado = 'Inactivo';
            }
            $checked = $asientos[0]->aparece_sri != 1 ? 'checked' : '';
            $html .= '<table style="width:100%;">';
            $html .= '<tr>
                <td colspan="5">Número de asiento: ' . $asientos[0]->id_asiento_cabecera . '</td>
            </tr>
            <tr>
                <td colspan="5">Sucursal: ' . $asientos[0]->sucursal . '      Modulo: ' . $asientos[0]->modulo . ' </td>
            </tr>
            <tr>
                <td colspan="5">Fecha de registro: ' . date('Y-m-d', strtotime($asientos[0]->fecha_asiento)) . ' </td>
            </tr>
            <tr>
                <td colspan="5">Estado: ' . $estado . '      Aparece en SRI: <input type="checkbox" ' . $checked . ' disabled/></td>
            </tr>
            <tr>
                <td colspan="5">Valor registrado: ' . $asientos[0]->valor  . ' </td>
            </tr>
            <tr>
                <td colspan="5">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5">Detalle: ' . $asientos[0]->observacion  . ' </td>
            </tr>';
            $html .= '<tr>
                    <th>Código</th>
                    <th>Cuenta</th>
                    <th>Detalle</th>
                    <th>Debe</th>
                    <th>Haber</th>
                </tr>';
            $sumDebe = 0;
            $sumHaber = 0;
            foreach ($asientos as $asiento) {
                $sumDebe += $asiento->debe;
                $sumHaber += $asiento->haber;
                $html .= '<tr>
                    <td>' . $asiento->plan . '</td>
                    <td>' . $asiento->nombre . '</td>
                    <td>' . $asiento->descripcion . '</td>
                    <td style="text-align:right;">' . $asiento->debe . '</td>
                    <td style="text-align:right;">' . $asiento->haber . '</td>
                </tr>';
            }
            $html .= '<tr>
            <th colspan="3">Totales</th>
            <th style="text-align:right;">' . number_format($sumDebe, 4) . '</th>
            <th style="text-align:right;">' . number_format($sumHaber, 4) . '</th>
            </tr>';
            $html .= '<span id="datoUsuario" style="font-size:x-small;display:none;">Creado por: ' . ucwords(strtolower($asiento->nombreC)) . ' impreso: ' . date('Y-m-d H:i:s') . '</span>';
            $html .= '</table>';
        }
        return $html;
    }
    public static function getSecuencia($tipo, $tipo_comprobante = "1")
    {
        $id_empresa = session('id_empresa');
        $idusuario  = Auth::user()->id;

        $ct_secuencia = ComprobanteSecuencia::where('empresa', $id_empresa)->where('tipo', $tipo)->where("tipo_comprobante", $tipo_comprobante)->first();

        if (is_null($ct_secuencia)) {
            LogAsiento::secuenciaEmpresa($id_empresa, $tipo, $tipo_comprobante);
            return "0000000001";
        } else {
            $nro_secuencia = intval($ct_secuencia->secuencia);
            $nro_secuencia++;
            $nro_secuencia = str_pad($nro_secuencia, 10, "0", STR_PAD_LEFT);
            $ct_secuencia->secuencia = $nro_secuencia;
            $ct_secuencia->id_usuariomod = $idusuario;
            $ct_secuencia->save();
            return $nro_secuencia;
        }
    }
    public static function detalleBalanceComprobacionAnulacion($r, $armar = '')
    {
        $periodo = date('Y', strtotime($r->d));
        $empresa = session('id_empresa');
        $hasta = str_replace('/', '-', $r->h);
        $desde = str_replace('/', '-', $r->d);
        $desde = date('Y-m-d', strtotime($desde));
        $hasta = date('Y-m-d', strtotime($hasta));
        $hasta_ = date("Y-m-d", strtotime($hasta . "+ 1 days"));
        $finan1 = DB::table('plan_cuentas as p')
            ->select(
                'log_contable.id as logver',
                'ac.id as id_asiento',
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(ad.debe,0) as debe'),
                DB::raw('COALESCE(ad.haber, 0) AS haber'),
                DB::raw('0 AS saldo_deudor'),
                DB::raw('0 AS saldo_acreedor'),
                DB::raw('0 AS saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 1, "ACTIVOS", IF(SUBSTRING(pe.plan, 1, 1) = 2, "PASIVOS", "PATRIMONIO")) AS tipo')
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->leftjoin('log_contable', 'ac.id', 'log_contable.id_ant')
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['1', '2', '3'])
            ->whereNull('ac.fecha_asiento')
            ->where(function ($q) {
                $q->whereNull('ad.debe')
                    ->whereNull('ad.haber');
            });
        $finan2 = DB::table('plan_cuentas as p')
            ->select(
                'log_contable.id as logver',
                'ac.id as id_asiento',
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(SUM(ad.debe), 0) AS debe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) AS haber'),
                DB::raw('IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS saldo_deudor'),
                DB::raw('IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS saldo_acreedor'),
                DB::raw('(IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)-IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0)) saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 1, "ACTIVOS", IF(SUBSTRING(pe.plan, 1, 1) = 2, "PASIVOS", "PATRIMONIO")) AS tipo')
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->leftjoin('log_contable', 'ac.id', 'log_contable.id_ant')
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['1', '2', '3'])
            ->whereBetween('ac.fecha_asiento', [self::getFechaInicioSistema() . ' 00:00:00', $hasta . ' 23:59:59'])
            ->groupBy('pe.plan');
        if ($armar != '')
            $financiero = $finan1->union($finan2)->orderBy('plan', 'asc')->get();
        else
            $financiero = $finan2->orderBy('plan', 'asc')->get();

        $resul1 = DB::table('plan_cuentas as p')
            ->select(
                'log_contable.id as logver',
                'ac.id as id_asiento',
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(ad.debe,0) as debe'),
                DB::raw('COALESCE(ad.haber, 0) AS haber'),
                DB::raw('0 AS saldo_deudor'),
                DB::raw('0 AS saldo_acreedor'),
                DB::raw('0 AS saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 4) = 5.1, "COSTOS", "GASTOS")) AS tipo')
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->leftjoin('log_contable', 'ac.id', 'log_contable.id_ant')
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['4', '5'])
            ->whereNull('ac.fecha_asiento')
            ->where(function ($q) {
                $q->whereNull('ad.debe')
                    ->whereNull('ad.haber');
            });

        $resul2 = DB::table('plan_cuentas as p')
            ->select(
                'log_contable.id as logver',
                'ac.id as id_asiento',
                'p.id_padre',
                'p.id',
                'pe.id_empresa as id_empresa',
                'pe.plan',
                'p.nombre',
                DB::raw("$periodo as anio"),
                DB::raw('COALESCE(SUM(ad.debe), 0) AS debe'),
                DB::raw('COALESCE(SUM(ad.haber), 0) AS haber'),
                DB::raw('IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0) AS saldo_deudor'),
                DB::raw('IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0) AS saldo_acreedor'),
                DB::raw('(IF(SUM(ad.debe) < SUM(ad.haber), SUM(ad.haber) - SUM(ad.debe), 0)-IF(SUM(ad.debe) > SUM(ad.haber), SUM(ad.debe) - SUM(ad.haber), 0)) saldo'),
                DB::raw('IF(SUBSTRING(pe.plan, 1, 1) = 4, "INGRESOS", IF(SUBSTRING(pe.plan, 1, 4) = 5.1, "COSTOS", "GASTOS")) AS tipo')
            )
            ->join('plan_cuentas_empresa as pe', function ($q) use ($empresa) {
                $q->on('p.id', 'pe.id_plan')
                    ->where('pe.id_empresa', $empresa);
            })
            ->leftjoin('ct_asientos_detalle as ad', 'pe.id_plan', 'ad.id_plan_cuenta')
            ->leftjoin('ct_asientos_cabecera as ac', function ($q) use ($empresa) {
                $q->on('ad.id_asiento_cabecera', 'ac.id')
                    ->where('ac.id_empresa', $empresa);
            })
            ->leftjoin('log_contable', 'ac.id', 'log_contable.id_ant')
            ->whereIn(DB::raw('SUBSTRING(pe.plan, 1, 1)'), ['4', '5'])
            ->whereBetween('ac.fecha_asiento', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->groupBy('pe.plan');
        if ($armar != '')
            $resultado = $resul1->union($resul2)->orderBy('plan', 'asc')->get();
        else
            $resultado = $resul2->orderBy('plan', 'asc')->get();
        $balance = $financiero->merge($resultado);
        if ($armar != '') {
            return $balance;
        }
        $html = '';
        $totalDebe = 0;
        $totalHaber = 0;
        $totalSaldoDebe = 0;
        $totalSaldoHaber = 0;
        if (count($balance) > 0) {
            foreach ($balance as $b) {
                if (is_null($b->logver)) {
                    $totalDebe += $b->debe;
                    $totalHaber += $b->haber;
                    $totalSaldoDebe += $b->saldo_deudor;
                    $totalSaldoHaber += $b->saldo_acreedor;
                    $html .= '<tr>';
                    $html .= '<td>' . $b->plan . '</td>';
                    $html .= '<td>' . $b->nombre . '</td>';
                    $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->debe, 2)) . '</td>';
                    $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->haber, 2)) . '</td>';
                    $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->saldo_deudor, 2)) . '</td>';
                    $html .= '<td style="text-align:right;">' . str_replace(',', '', number_format($b->saldo_acreedor, 2)) . '</td>';
                    $html .= '</tr>';
                }
            }
        }
        return $html . '|' . $totalDebe . '|' . $totalHaber . '|' . $totalSaldoDebe . '|' . $totalSaldoHaber;
    }
    public static function excelDeudasClientes($r)
    {

        $data = DB::table('ct_ventas as v')
            ->join('ct_clientes as c', 'v.id_cliente', '=', 'c.identificacion')
            ->select(
                'v.id',
                'c.identificacion',
                'c.nombre',
                'v.fecha',
                DB::raw('DATE_ADD(v.fecha, INTERVAL COALESCE(v.dias_plazo, 0) DAY) as vencimiento'),
                DB::raw('DATEDIFF(NOW(), DATE_ADD(v.fecha, INTERVAL COALESCE(v.dias_plazo, 0) DAY)) AS dias_transcurridos'),
                'v.tipo',
                'v.id_asiento',
                'v.nro_comprobante',
                DB::raw('ROUND(v.total_final,2) as total_final'),
                DB::raw('IFNULL((SELECT SUM(COALESCE(di.total, 0)) 
                      FROM ct_detalle_comprobante_ingreso di 
                      JOIN ct_comprobante_ingreso ci ON di.id_comprobante = ci.id 
                      WHERE di.id_factura = v.id AND ci.estado != 0), 0) as ingresos'),
                DB::raw('IFNULL((SELECT COALESCE(valor_fuente, 0) + COALESCE(valor_iva, 0) 
                      FROM ct_cliente_retencion 
                      WHERE id_factura = v.id AND estado != 0), 0) as retenciones'),
                DB::raw('IFNULL((SELECT SUM(COALESCE(valor_contable, 0)) 
                      FROM ct_debito_clientes 
                      WHERE id_factura = v.id AND estado != 0), 0) as debitos'),
                DB::raw('IFNULL((SELECT SUM(COALESCE(dc.total, 0)) 
                      FROM ct_detalle_cruce_clientes dc 
                      JOIN ct_cruce_valores_cliente cv ON dc.id_comprobante = cv.id 
                      WHERE dc.id_factura = v.id AND cv.estado != 0), 0) 
                      + IFNULL((SELECT SUM(COALESCE(total, 0)) 
                      FROM ct_cruce_cuentas_clientes 
                      WHERE id_factura = v.id AND estado != 0), 0) as cruces'),
                DB::raw('IFNULL((SELECT SUM(total_credito) 
                      FROM ct_nota_credito_clientes 
                      WHERE id_factura = v.id AND estado != 0), 0) as n_c'),
                DB::raw('ROUND(v.valor_contable,2) as valor_contable'),
                DB::raw('ROUND((SELECT SUM(v2.valor_contable)
            FROM ct_ventas v2
            WHERE v2.fecha BETWEEN "2024-01-01 00:00:00" AND "2024-07-29 23:59:59"
            AND v2.valor_contable > 0
            AND v2.tipo != "N-D"
            AND v2.estado != 0),2) as total_global')
            )
            ->whereBetween('v.fecha', [$r->d . ' 00:00:00', $r->h . ' 23:59:59'])
            ->when($r->c, function ($query) use ($r) {
                return $query->where('identificacion', explode("|", $r->c)[0]);
            })
            ->where('v.valor_contable', '>', 0)
            ->where('v.tipo', '!=', 'N-D')
            ->where('v.estado', '!=', 0)
            ->orderBy('c.nombre')
            ->get();

        $titulos = array("Fecha", "Vencimiento", "Días", "Tipo", "Número", "Detalle", "Valor", "IN", "RT", "DB", "CRUCE", "N/C", "Saldo");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U");
        $empresa = Empresa::where("id", session("id_empresa"))->first();
        return Excel::create('Deudas Cliente', function ($excel) use ($posicion, $titulos, $empresa, $r, $data) {
            $excel->sheet('Deudas Cliente', function ($sheet) use ($posicion, $titulos, $empresa, $r, $data) {
                $sheet->mergeCells('A1:M1');
                $sheet->cell('A1', function ($cell) use ($r, $empresa) {
                    $cell->setValue(' Deudas Pendientes' . ' ' . $empresa->razonsocial . ' ' . $r->d . ' - ' . $r->h);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setAlignment('center');
                });
                $currentRazonSocial = null;
                $totalValor = 0;
                $comienzo = 3;
                $totalAfavor = 0;
                foreach ($data as $data) {
                    if ($currentRazonSocial !== $data->identificacion) {
                        if ($currentRazonSocial !== null) {

                            $sheet->cell('F' . $comienzo, function ($cell) use ($totalValor) {
                                $cell->setValue('Totales');
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#FFFF00');
                                $cell->setAlignment('left');
                            });
                            $sheet->cell('M' . $comienzo, function ($cell) use ($totalValor) {
                                $cell->setValue(number_format($totalValor, 2, '.', ','));
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#FFFF00');
                                $cell->setAlignment('center');
                            });
                            $comienzo++;
                            $totalValor = 0;
                        }
                        $nombreCliente = Ct_Clientes::where("identificacion", $data->identificacion)->first();

                        $sheet->cell('A' . $comienzo, function ($cell) use ($data, $nombreCliente) {
                            $cell->setValue(isset($nombreCliente) ? $nombreCliente->nombre . ' ' . $data->identificacion : $data->identificacion);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#CED5DC');
                            $cell->setFontSize(16);
                            $cell->setAlignment('center');
                        });
                        $sheet->mergeCells('A' . $comienzo . ':M' . $comienzo);
                        $comienzo++;
                        // Escribir los títulos de las columnas
                        for ($i = 0; $i < count($titulos); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                                $cell->setValue($titulos[$i]);
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#CCE1A7');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $comienzo++;
                        $currentRazonSocial = $data->identificacion;
                    }
                    $datos_excel = [
                        $data->fecha,
                        $data->vencimiento,
                        $data->dias_transcurridos,
                        $data->tipo,
                        $data->nro_comprobante,
                        "Id Fact No.: " . $data->id . " | Asiento No.: " . $data->id_asiento,
                        $data->total_final,
                        $data->ingresos,
                        $data->retenciones,
                        $data->debitos,
                        $data->cruces,
                        $data->n_c,
                        $data->valor_contable
                    ];

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $totalValor += $data->valor_contable;
                    $comienzo++;
                    $totalAfavor += $data->valor_contable;;
                }
                if ($currentRazonSocial !== null) {
                    $sheet->cell('F' . $comienzo, function ($cell) {
                        $cell->setValue('Totales');
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#FFFF00');
                        $cell->setAlignment('left');
                    });
                    $sheet->cell('M' . $comienzo, function ($cell) use ($totalValor) {
                        $cell->setValue(number_format($totalValor, 2, '.', ','));
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#FFFF00');
                        $cell->setAlignment('right');
                    });
                }
                $comienzo += 2;
                $sheet->cell('F' . $comienzo, function ($cell) {
                    $cell->setValue('Totales');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#a2e3ff');
                    $cell->setAlignment('left');
                });
                $sheet->cell('M' . $comienzo, function ($cell) use ($totalAfavor) {
                    $cell->setValue(number_format($totalAfavor, 2, '.', ','));
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#a2e3ff');
                    $cell->setAlignment('right');
                });
            });
        })->export('xlsx');
    }
    public static function buscarDescuadrados($r)
    {
        $anio_actual = date('Y');
        if (isset($r->anio) or count($r->all()) > 0 or !is_null($r->anio)) {
            $anio_actual = $r->anio;
        }
        $id_empresa = session('id_empresa');
        $busqueda = ["id_empresa" => $id_empresa, "anio" => $anio_actual];
        $descuadrados = Contabilidad::recovery_by_model('O', 'ASIENTO', $busqueda);
        $descuadrados = json_encode($descuadrados);
        $descuadrados = json_decode($descuadrados, true);
        // Extraer los datos de los asientos
        $asientos = $descuadrados['original']['asientos'];
        // Formatear datos para DataTables
        $data = array_map(function ($item) {
            // Determina si el módulo es "compra" o "venta" y extrae los datos
            $modulo = isset($item['modulo']['original']['compra']['module']) ? $item['modulo']['original']['compra']['module'] : (isset($item['modulo']['original']['venta']['module']) ? $item['modulo']['original']['venta']['module'] : '');

            return [
                'id_asiento' => $item['id_asiento'],
                'modulo' => $modulo,
                'debe' => $item['debe'],
                'haber' => $item['haber'],
                'diferencia' => number_format($item['debe'] - $item['haber'], 4)
            ];
        }, $asientos);
        return $data;
    }
    public static function recovery_by_model($type = "", $module = "", $id = "")
    {
        $message = "";
        if ($type != "") {
            if ($type == 'O') {
                if ($module == 'EG') {
                    $egreso = ComprobanteEgreso::find($id);
                    $arraymodules['egreso'] = $egreso;
                    $arraymodules['detalles'] = $egreso->detalles;
                    //dd($egreso->detalles);
                    $xp = [];
                    foreach ($egreso->detalles as $z) {
                        array_push($xp, $z->compras);
                    }
                    $arraymodules['compras'] = $xp;
                    //dd($xp);    
                    return response()->json($arraymodules);
                } else if ($module == 'EGV') {
                    $varios = ComprobanteEgresoVarios::find($id);
                    $arraymodules['egreso'] = $varios;
                    $arraymodules['detalles'] = $varios->detalles;
                    return response()->json($arraymodules);
                } else if ($module == "CI") {
                    $ingresos =  ComprobanteIngreso::find($id);
                    $arraymodules['ingreso'] = $ingresos;
                    $arraymodules['detalles'] = $ingresos->detalle;
                    $xp = [];
                    foreach ($ingresos->detalle as $z) {
                        array_push($xp, $z->ventas);
                    }
                    $arraymodules['ventas'] = $xp;
                    return response()->json($arraymodules);
                } else if ($module == "DEP") {
                    $deposito = DepositoBancario::find($id);
                    $arraymodules['deposito'] = $deposito;
                    $arraymodules['detalle'] = $deposito->detalles;
                    $xp = [];
                    foreach ($deposito->detalles as $z) {
                        if (isset($z->ingreso->cabecera_ingreso)) {
                            array_push($xp, $z->ingreso->cabecera_ingreso);
                        } else if (isset($z->ingreso->cabecera_ingresov)) {
                            array_push($xp, $z->ingreso->cabecera_ingresov);
                        }
                    }
                    $arraymodules['ingresos'] = $xp;
                    return response()->json($arraymodules);
                } else if ($module == "DB") {
                    $egreso = NotaDebitoBancaria::find($id);
                    $arraymodules['egreso'] = $egreso;
                    $arraymodules['detalles'] = $egreso->detalles;
                    //dd($egreso->detalles);
                    $xp = [];
                    foreach ($egreso->detalles as $z) {
                        array_push($xp, $z->compras);
                    }
                    $arraymodules['compras'] = $xp;
                    return response()->json($arraymodules);
                } else if ($module == "C") {
                    //show all registers that we have
                    $compras = Compras::find($id);
                    $arraymodules['id'] = $compras->id;
                    $arraymodules['nro_comprobante'] = $compras->numero;
                    $arraymodules['asiento'] = $compras->id_asiento_cabecera;
                    if ($compras->tipo == 1) {
                        $arraymodules['tipo'] = 'COM-FA';
                    } else if ($compras->tipo == 2) {
                        $arraymodules['tipo'] = 'COM-FACT';
                    }
                    $arraymodules['proveedor'] = $compras->proveedorf->razonsocial;
                    $arraymodules['observacion'] = $compras->observacion;
                    $arraymodules['total_final'] = $compras->total_final;
                    $arraymodules['valor_contable'] = $compras->valor_contable;
                    $arraymodules['usuariocrea'] = $compras->usuario->nombre1 . ' ' . $compras->usuario->apellido1;
                    $arraymodules['creado'] = $compras->created_at;
                    $arraymodules['actualizado'] = $compras->updated_at;
                    if (isset($compras->retenciones)) {
                        if (count($compras->retenciones) > 0) {
                            foreach ($compras->retenciones as $x) {
                                if ($x->estado == 1) {
                                    $arraymodules['retencion'] = $x->id;
                                }
                            }
                        }
                    }
                    $xps = [];
                    if (isset($compras->egresos)) {
                        if (count($compras->egresos) > 0) {
                            foreach ($compras->egresos as $x) {
                                if (isset($x->comp_egreso)) {
                                    if ($x->comp_egreso->estado == 1) {
                                        array_push($xps, $x->comp_egreso->id);
                                    }
                                }
                            }
                            $arraymodules['egresos'] = $xps;
                        }
                    }
                    $pl = [];
                    if (isset($compras->cruce)) {
                        if (count($compras->cruce) > 0) {
                            foreach ($compras->cruce as $t) {
                                if (isset($t->cabecera)) {
                                    if ($t->cabecera->estado == 1) {
                                        array_push($pl, $t->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['cruce'] = $pl;
                        }
                    }
                    $pl2 = [];
                    if (isset($compras->cruce_cuentas)) {
                        if (count($compras->cruce_cuentas) > 0) {
                            foreach ($compras->cruce_cuentas as $t) {

                                array_push($pl2, $t->id);
                            }
                            $arraymodules['cruce_cuentas'] = $pl2;
                        }
                    }
                    $lx = [];
                    if (isset($compras->bndebito)) {
                        if (count($compras->bndebito) > 0) {
                            foreach ($compras->bndebito as $d) {
                                if (isset($d->cabecera)) {
                                    if ($d->cabecera->estado == 1) {
                                        array_push($lx, $d->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['debito'] = $lx;
                        }
                    }
                    $deb = [];
                    if (isset($compras->debitoacreedor)) {
                        if (count($compras->debitoacreedor) > 0) {
                            foreach ($compras->debitoacreedor as $d) {
                                if (isset($d->cabecera)) {
                                    if ($d->cabecera->estado == 1) {
                                        array_push($deb, $d->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['debito_acreedor'] = $deb;
                        }
                    }
                    $xp = [];
                    if (isset($compras->credito_acreedor)) {
                        if (count($compras->credito_acreedor) > 0) {
                            foreach ($compras->credito_acreedor as $x) {
                                if ($x->estado == 1) {
                                    array_push($xp, $x->id);
                                }
                            }
                            $arraymodules['credito_acreedores'] = $xp;
                        }
                    }
                    $masv = [];
                    if (isset($compras->masivos)) {
                        if (count($compras->masivos) > 0) {
                            foreach ($compras->masivos as $c) {
                                if (isset($c->comp_egreso)) {
                                    if ($c->comp_egreso->estado == 1) {
                                        array_push($masv, $c->comp_egreso->id);
                                    }
                                }
                            }
                            $arraymodules['masivos'] = $masv;
                        }
                    }

                    return response()->json($arraymodules);
                } else if ($module == "V") {
                    $ventas = Ventas::find($id);
                    $arraymodules = [];
                    $arraymodules['id'] = $ventas->id;
                    $arraymodules['comprobante'] = $ventas->nro_comprobante;
                    $arraymodules['cliente'] = $ventas->cliente->nombre;
                    $arraymodules['asiento'] = $ventas->id_asiento;
                    $arraymodules['total_final'] = $ventas->total_final;
                    $arraymodules['valor_contable'] = $ventas->valor_contable;
                    $arraymodules['usuariocrea'] = $ventas->usuario->nombre1 . ' ' . $ventas->usuario->apellido1;
                    $arraymodules['creado'] = $ventas->created_at;
                    $arraymodules['actualizado'] = $ventas->updated_at;
                    if ($ventas->omni == 1) {
                        $arraymodules['informacion'] = "OMNI";
                    }
                    if (isset($ventas->retenciones)) {
                        $rete = ClientesRetenciones::where('id_factura', $id)->get();
                        $contador = 0;
                        foreach ($rete as $value) {
                            if (($value->estado == 1)) {
                                $arraymodules['retencion' . $contador] = $value->id;
                            } else {
                                $arraymodules['retencionAnulado' . $contador] = $value->id;
                            }
                            $contador++;
                        }
                    }
                    $ing = [];
                    if (isset($ventas->comp_ingreso)) {
                        if (count($ventas->comp_ingreso) > 0) {
                            foreach ($ventas->comp_ingreso as $ig) {
                                if (isset($ig->ingreso)) {
                                    if ($ig->ingreso->estado == 1) {
                                        array_push($ing, $ig->ingreso->id);
                                    }
                                }
                            }
                            $arraymodules['ingreso'] = $ing;
                        }
                    }
                    $cruce = [];
                    if (isset($ventas->cruce)) {
                        if (count($ventas->cruce) > 0) {
                            foreach ($ventas->cruce as $x) {
                                if (isset($x->cabecera)) {
                                    if ($x->cabecera->estado == 1) {
                                        array_push($cruce, $x->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['cruce'] = $cruce;
                        }
                    }
                    $cheque = [];
                    if (isset($ventas->chequepost)) {
                        if (count($ventas->chequepost) > 0) {
                            foreach ($ventas->chequepost as $ch) {
                                if (isset($ch->cabecera)) {
                                    if ($ch->cabecera->estado == 1) {
                                        array_push($cheque, $ch->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['chequepost'] = $cheque;
                        }
                    }
                    $xp = [];
                    if (isset($ventas->cruce_cuentas)) {
                        if (count($ventas->cruce_cuentas) > 0) {
                            foreach ($ventas->cruce_cuentas as $x) {
                                if ($x->estado != 0)
                                    array_push($xp, $x->id);
                            }
                            $arraymodules['cruce_cuentas'] = $xp;
                        }
                    }
                    $credi = [];
                    if (isset($ventas->credito)) {
                        if (count($ventas->credito) > 0) {
                            foreach ($ventas->credito as $c) {
                                if (isset($c->cabecera)) {
                                    if ($c->cabecera->estado == 1) {
                                        array_push($cheque, $c->cabecera->id);
                                    }
                                }
                            }
                            $arraymodules['credito'] = $credi;
                        }
                    }
                    return response()->json($arraymodules);
                } else if ($module == "DEP-I") {
                    //$detalle= Ct_Detalle_Pago_Ingreso::find($id);
                    $deposito = DetalleDepositoBancario::where('id_ingreso', $id)->first();
                    // dd($deposito);
                    $arraymodules['deposito'] = $deposito->deposito_bancario->id;
                    $arraymodules['ingreso'] = $deposito->ingreso->id;
                    return response()->json($arraymodules);
                } else if ($module == "ASIENTO") {
                    //dd($id['anio']);

                    $arraymodules['anio'] = $id['anio'];
                    $sql = "SELECT
                            id_asiento_cabecera,
                            fecha_asiento as fecha,
                            SUM(debe) AS debe_n,
                            SUM(haber) AS haber_n
                        FROM ct_asientos_detalle, ct_asientos_cabecera
                            WHERE id_asiento_cabecera <> 24904 AND
                            ct_asientos_detalle.id_asiento_cabecera = ct_asientos_cabecera.id
                            AND YEAR(fecha_asiento) =" . $id['anio'] . "
                            AND ct_asientos_cabecera.id_empresa=" . $id['id_empresa'] . "
                        GROUP BY id_asiento_cabecera
                        HAVING SUM(debe) <> SUM(haber)
                        ORDER BY id_asiento_cabecera DESC";
                    //dd($sql);
                    $asientos = DB::select(DB::raw($sql));
                    $arraynew = [];
                    foreach ($asientos as $a) {
                        $xl['id_asiento'] = $a->id_asiento_cabecera;
                        $xl['modulo'] = Contabilidad::recovery_by_asiento($a->id_asiento_cabecera);
                        $xl['debe'] = $a->debe_n;
                        $xl['haber'] = $a->haber_n;
                        $xl['fecha'] = $a->fecha;
                        array_push($arraynew, $xl);
                    }
                    $arraymodules['total'] = count($asientos);
                    $arraymodules['asientos'] = $arraynew;
                    return response()->json($arraymodules);
                }
            }
        }
        return response()->json(['state' => '1', 'content' => $message]);
    }
    public static function recovery_by_asiento($id)
    {
        $modulos = [];
        //compras
        $compras = Contabilidad::compras($id);
        $ventas = Contabilidad::ventas($id);
        $bancos = Contabilidad::bancos($id);
        $modulos['compra'] = $compras;
        $modulos['venta'] = $ventas;
        $modulos['bancos'] = $bancos;
        return response()->json($modulos);
    }
    public static function bancos($id)
    {
        $models = [];
        $debito = NotadeDebito::where('id_asiento', $id)->first();
        if (!is_null($debito)) {
            $models['module'] = 'NOTA DE DEBITO-B';
            $models['id'] = $debito->id;
            $models['concepto'] = $debito->concepto;
            $models['empresa'] = $debito->empresa;
            $models['total'] = $debito->valor;
        }
        $debito = NotaCredito::where('id_asiento', $id)->first();
        if (!is_null($debito)) {
            $models['module'] = 'NOTA DE CREDITO-B';
            $models['id'] = $debito->id;
            $models['concepto'] = $debito->concepto;
            $models['empresa'] = $debito->empresa;
            $models['total'] = $debito->valor;
        }
        $transferencia = TransferenciasBancarias::where('id_asiento', $id)->first();
        if (!is_null($transferencia)) {
            $models['module'] = 'TRANSFERENCIA-B';
            $models['id'] = $transferencia->id;
            $models['concepto'] = $transferencia->concepto;
            $models['empresa'] = $transferencia->empresa;
            $models['total'] = $transferencia->total;
        }
        $deposito = DepositoBancario::where('id_asiento', $id)->first();
        if (!is_null($deposito)) {
            $models['module'] = 'DEPOSITO-B';
            $models['id'] = $deposito->id;
            $models['concepto'] = $deposito->concepto;
            $models['empresa'] = $deposito->empresa;
            $models['total'] = $deposito->total;
        }
        return $models;
    }
    public static function compras($id)
    {
        $models = [];
        $compra = Compras::where('id_asiento_cabecera', $id)->first();
        if (!is_null($compra)) {
            $models['module'] = 'COMPRAS';
            $models['id'] = $compra->id;
            $models['concepto'] = $compra->descripcion;
            $models['empresa'] = $compra->id_empresa;
            $models['comprobante'] = $compra->nro_comprobante;
            $models['total'] = $compra->valor_contable;
            $models['sucursal'] = $compra->sucursal;
            $models['punto_emision'] = $compra->punto_emision;
        }
        $retenciones = Retenciones::where('id_asiento_cabecera', $id)->first();
        if (!is_null($retenciones)) {
            $models['module'] = 'RETENCION-A';
            $models['id'] = $retenciones->id;
            $x = $retenciones->valor_fuente;
            $y = $retenciones->valor_iva;
            $models['concepto'] = $retenciones->descripcion;
            $models['empresa'] = $retenciones->id_empresa;
            $models['comprobante'] = $retenciones->nro_comprobante;
            $models['total'] = $x + $y;
            $models['sucursal'] = $retenciones->sucursal;
            $models['punto_emision'] = $retenciones->punto_emision;
        }
        $egresos =  ComprobanteEgreso::where('id_asiento_cabecera', $id)->first();
        if (!is_null($egresos)) {
            $models['module'] = 'EGRESO-A';
            $models['id'] = $egresos->id;
            $models['fecha'] = $egresos->fecha;
            $models['concepto'] = $egresos->descripcion;
            $models['comprobante'] = $egresos->secuencia;
            $models['empresa'] = $egresos->id_empresa;
            $models['total'] = $egresos->valor_pago;
        }
        $egresosv =  ComprobanteEgresoVarios::where('id_asiento_cabecera', $id)->first();
        if (!is_null($egresosv)) {
            $models['module'] = 'EGRESOV-A';
            $models['id'] = $egresosv->id;
            $models['fecha'] = $egresosv->fecha;
            $models['empresa'] = $egresosv->id_empresa;
            $models['concepto'] = $egresosv->descripcion;
            $models['comprobante'] = $egresosv->secuencia;
            $models['total'] = $egresosv->valor_pago;
        }
        $egresosm =  EgresosMasivos::where('id_asiento_cabecera', $id)->first();
        if (!is_null($egresosm)) {
            $models['module'] = 'EGRESOM-A';
            $models['id'] = $egresosm->id;
            $models['fecha'] = $egresosm->fecha_comprobante;
            $models['empresa'] = $egresosm->id_empresa;
            $models['concepto'] = $egresosm->descripcion;
            $models['comprobante'] = $egresosm->secuencia;
            $models['total'] = $egresosm->valor_pago;
        }

        $debito =  NotaDebitoBancaria::where('id_asiento', $id)->first();
        if (!is_null($debito)) {
            $models['module'] = 'DEBITOB-A';
            $models['id'] = $debito->id;
            $models['fecha'] = $debito->fecha;
            $models['empresa'] = $debito->empresa;
            $models['concepto'] = $debito->concepto;
            $models['comprobante'] = $debito->secuencia;
            $models['total'] = $debito->valor;
        }
        $debitoacreedor = DebitoAcreedores::where('id_asiento_cabecera', $id)->first();
        if (!is_null($debitoacreedor)) {
            $models['module'] = 'DEBITOAC-A';
            $models['id'] = $debitoacreedor->id;
            $models['empresa'] = $debitoacreedor->id_empresa;
            $models['concepto'] = $debitoacreedor->concepto;
            $models['total'] = $debitoacreedor->valor_contable;
            $models['fecha'] = $debitoacreedor->fecha_factura;
        }
        $cruce = CruceValores::where('id_asiento_cabecera', $id)->first();
        if (!is_null($cruce)) {
            $models['module'] = 'CRUCE-A';
            $models['id'] = $cruce->id;
            $models['empresa'] = $cruce->id_empresa;
            $models['concepto'] = $cruce->detalle;
            $models['total'] = $cruce->total_disponible;
            $models['fecha'] = $cruce->fecha_pago;
        }
        $credito = CreditoAcreedores::where('id_asiento_cabecera', $id)->first();
        if (!is_null($credito)) {
            $models['module'] = 'CREDITOA-A';
            $models['empresa'] = $credito->id_empresa;
            $models['id'] = $credito->id;
            $models['concepto'] = $credito->detalle;
            $models['total'] = $credito->valor_contable;
            $models['fecha'] = $credito->fecha;
        }
        return $models;
    }
    public static function ventas($id)
    {
        $models = [];
        $compra = Ventas::where('id_asiento', $id)->first();
        if (!is_null($compra)) {
            $models['module'] = 'VENTA';
            $models['id'] = $compra->id;
            $models['empresa'] = $compra->id_empresa;
            $models['concepto'] = $compra->observaciones;
            $models['total'] = $compra->total_ingreso;
            $models['fecha'] = $compra->fecha;
            $models['sucursal'] = $compra->sucursal;
            $models['punto_emision'] = $compra->punto_emision;
        }
        $ingreso = ComprobanteIngreso::where('id_asiento_cabecera', $id)->first();
        if (!is_null($ingreso)) {
            $models['module'] = 'INGRESO-C';
            $models['id'] = $ingreso->id;
            $models['empresa'] = $ingreso->id_empresa;
            $models['concepto'] = $ingreso->observaciones;
            $models['total'] = $ingreso->total_ingreso;
            $models['fecha'] = $ingreso->fecha;
        }
        $retenciones = ClientesRetenciones::join('ct_ventas as v', 'ct_cliente_retencion.id_factura', 'v.id')->where('id_asiento_cabecera', $id)->first([
            'ct_cliente_retencion.id',
            'ct_cliente_retencion.id_empresa',
            'ct_cliente_retencion.valor_fuente',
            'ct_cliente_retencion.valor_iva',
            'ct_cliente_retencion.descripcion',
            'ct_cliente_retencion.nro_comprobante',
            'v.nro_comprobante as venta_comprobante'
        ]);
        if (!is_null($retenciones)) {
            $models['module'] = 'RETENCION-C';
            $models['id'] = $retenciones->id;
            $models['empresa'] = $retenciones->id_empresa;
            $x = $retenciones->valor_fuente;
            $y = $retenciones->valor_iva;
            $models['concepto'] = $retenciones->descripcion;
            $models['comprobante'] = $retenciones->nro_comprobante;
            $models['total'] = $x + $y;
            $models['nro_comprobante'] = $retenciones->venta_comprobante;
        }
        $cruce = CruceValoresClientes::where('id_asiento_cabecera', $id)->first();
        if (!is_null($cruce)) {
            $models['module'] = 'CRUCE-C';
            $models['id'] = $cruce->id;
            $models['empresa'] = $cruce->id_empresa;
            $models['concepto'] = $cruce->detalle;
            $models['total'] = $cruce->total_disponible;
            $models['fecha'] = $cruce->fecha_pago;
        }
        $chequepost = ChequePost::where('id_asiento_cabecera', $id)->first();
        if (!is_null($chequepost)) {
            $models['module'] = 'CHEQUEPOST-C';
            $models['id'] = $chequepost->id;
            $models['empresa'] = $chequepost->id_empresa;
            $models['concepto'] = $chequepost->observaciones;
            $models['total'] = $chequepost->total_ingreso;
            $models['fecha'] = $chequepost->fecha;
        }
        $cruce_cuentas = CruceCuentasClientes::where('id_asiento_cabecera', $id)->first();
        if (!is_null($cruce_cuentas)) {
            $models['module'] = 'CRUCECUENTAS-C';
            $models['id'] = $cruce_cuentas->id;
            $models['empresa'] = $cruce_cuentas->id_empresa;
            $models['concepto'] = $cruce_cuentas->observaciones;
            $models['total'] = $cruce_cuentas->total;
            $models['fecha'] = $cruce_cuentas->fecha;
        }
        $credito = NotaCreditoClientes::where('id_asiento_cabecera', $id)->first();
        if (!is_null($credito)) {
            $models['module'] = 'CREDITOCLIENTES-C';
            $models['id'] = $credito->id;
            $models['empresa'] = $credito->id_empresa;
            $models['concepto'] = $credito->concepto;
            $models['total'] = $credito->total_credito;
            $models['fecha'] = $credito->fecha;
        }
        return $models;
    }
}
