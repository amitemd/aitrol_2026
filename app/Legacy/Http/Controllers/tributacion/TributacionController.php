<?php

namespace Sis_medico\Http\Controllers\tributacion;

use Exception;
use DB;
use Illuminate\Http\Request;
use Sis_medico\Ct_Comision_Cabecera;
use Sis_medico\Ct_compras;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_Formulario107;
use Sis_medico\Ct_Guia_Remision_Cabecera;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_Porcentaje_Retenciones;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Documentos_Electronicos;
use Sis_medico\De_Log_Error;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\contable\EstadoResultadosController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\EmisionDocumentosController;
use Sis_medico\Http\Controllers\sri_electronico\ClaveAcceso;
use Sis_medico\LogConfig;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;
use SoapClient;

class TributacionController extends Controller
{

    public function index(Request $r)
    {
        if (!isset(Auth::user()->id) || Auth::user()->id == '')
            return redirect('login');
        if ($r->opcion == 'obtenerDatos') {
            $sueldoMensual = 0;
            $sobreSueldo = 0;
            $decimoTercero = 0;
            $decimoCuarto = 0;
            $fondoReserva = 0;
            $aportePersonal = 0;
            $roles = Ct_Rol_Pagos::where('id_user', $r->cedula)
                ->where('anio', $r->anio)
                ->where('estado', 1)
                ->get();
            if (count($roles) > 0) {
                foreach ($roles as $row) {
                    foreach ($row->detalle as $rowdeta) {
                        $sueldoMensual += $rowdeta->base_iess;
                        $sobreSueldo += $rowdeta->sobre_tiempo50 + $rowdeta->sobre_tiempo100 + $rowdeta->bonificacion + $rowdeta->alimentacion + $rowdeta->transporte + $rowdeta->bono_imputable;
                        $decimoTercero += $rowdeta->decimo_tercero;
                        $decimoCuarto += $rowdeta->decimo_cuarto;
                        $fondoReserva += $rowdeta->fondo_reserva;
                        $aportePersonal += $rowdeta->porcentaje_iess;
                    }
                }
            }
            return json_encode([
                'sueldo' => $sueldoMensual,
                'sobreSueldo' => $sobreSueldo,
                'decimoTercero' => $decimoTercero,
                'decimoCuarto' => $decimoCuarto,
                'fondoReserva' => $fondoReserva,
                'aportePersonal' => $aportePersonal
            ]);
        } elseif ($r->opcion == 'formulario_107') {
            $data['empresa'] = Empresa::where('id', session('id_empresa'))->where('estado', '1')->first();
        } elseif ($r->opcion == 'agregar') {
            $data['empresa'] = Empresa::where('id', session('id_empresa'))->where('estado', '1')->first();
            return view('tributacion/view_formulario_107', $data);
        } elseif ($r->opcion == 'buscar_empreados_107') {
            $dataImpo = Ct_Nomina::where('estado', '1')->where('id_user', 'LIKE', '%' . $r->term . '%')->get();
            foreach ($dataImpo as $value) {
                $data[] = array('value' => $value->id_user . ' | ' . $value->nombres, 'id' => $value->id_user);
            }
            if (count($dataImpo) > 0) {
                return $data;
            } else {
                return ['value' => 'No se encontraron resultados', 'id' => ''];
            }
        } elseif ($r->opcion == 'exportarXML') {
            echo Ct_Formulario107::generarXML($r);
        } elseif ($r->opcion == 'saveForm107') {
            $datos = json_encode($r->input());
            $num = Ct_Formulario107::where('id_empresa', session('id_empresa'))->max('num_formulario');
            if ($num == '')
                $num = 1;
            else
                $num += 1;
            $num = str_pad($num, 10, 0, STR_PAD_LEFT);
            $formulario = Ct_Formulario107::where('id_empresa', session('id_empresa'))
                ->where('num_formulario', $r->numero)
                ->first();
            $periodo = $r->periodo1 . $r->periodo2 . $r->periodo3 . $r->periodo4;
            $fechaPresentacion = $r->anio1 . $r->anio2 . $r->anio3 . $r->anio4 . '-' . $r->mes1 . $r->mes2 . '-' . $r->dia1 . $r->dia2;
            DB::beginTransaction();
            try {
                if ($formulario == '') {
                    $arrayFormulario = [
                        'identificacion' => $r->cedula,
                        'periodo' => $periodo,
                        'fecha_presentacion' => $fechaPresentacion,
                        'num_formulario' => $num,
                        'datos' => $datos,
                        'id_empresa' => session('id_empresa'),
                        'id_usuariocrea' => Auth::user()->id,
                        'id_usuariomod' => Auth::user()->id,
                        'ip_creacion' => $_SERVER['SERVER_ADDR'],
                        'ip_modificacion' => $_SERVER['SERVER_ADDR'],
                        'estado' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    Ct_Formulario107::insert($arrayFormulario);
                } else {
                    $arrayFormulario = [
                        'identificacion' => $r->cedula,
                        'periodo' => $periodo,
                        'fecha_presentacion' => $fechaPresentacion,
                        'num_formulario' => $num,
                        'datos' => $datos,
                        'id_empresa' => session('id_empresa'),
                        'id_usuariocrea' => Auth::user()->id,
                        'id_usuariomod' => Auth::user()->id,
                        'ip_modificacion' => $_SERVER['SERVER_ADDR'],
                        'estado' => 1
                    ];
                    Ct_Formulario107::where('id', $formulario->id)->update($arrayFormulario);
                }
                DB::commit();
                return redirect('tributacion/formulario_107');
            } catch (Exception $e) {
                DB::rollBack();
                echo '<pre>';
                echo $e->getMessage() . ' - ' . $e->getLine();
                exit;
            }
        } elseif ($r->opcion == 'anularFormulario107') {
            $arrayFormulario107 = [
                'estado' => 0
            ];
            Ct_Formulario107::where('id_empresa', session('id_empresa'))
                ->where('num_formulario', $r->num)
                ->update($arrayFormulario107);
        } elseif ($r->opcion == 'verificarDocumento') {
            $dato = EmisionDocumentosController::obtenercomprobanteSRI($r->clave, 'datos');
            echo '<pre>';
            print_r($dato);
            exit;
        } elseif ($r->opcion == 'logDocumento') {
            $html = '';
            $doc = DB::table($r->tabla)->where('id', $r->id)->first();
            if ($doc != '') {
                $claveAcceso = isset($doc->clave_accesso) ? $doc->clave_accesso : (isset($doc->nro_autorizacion) ? $doc->nro_autorizacion : (isset($doc->autorizacion) ? $doc->autorizacion : ''));
                if ($claveAcceso != '') {
                    $log = DB::table('de_log_facturacion as f')
                        ->select(
                            'f.informacion',
                            'f.created_at',
                            'p.nombre',
                            'f.id_pasos'
                        )
                        ->join('de_pasos as p', 'f.id_pasos', 'p.id')
                        ->where('f.clave_acceso', $claveAcceso)
                        ->where('f.id_documento', $r->id)
                        ->orderBy('f.id', 'desc')
                        ->get();
                    if (count($log) > 0) {
                        $html .= '<table style="width:100%" align="center">
                        <tr>
                        <td>No</td>
                        <td>Fecha</td>
                        <td>Paso</td>
                        <td>Información</td>
                        </tr>';
                        $informacion = '';
                        $dinfo = [];
                        $coutn = 0;
                        foreach ($log as $row) {
                            $coutn++;
                            $dinfo = json_decode($row->informacion);
                            if (isset($dinfo->comprobantes)) {
                                $informacion = $dinfo->comprobantes[0]->mensajes[0]->mensaje;
                            } else {
                                $informacion = $row->informacion;
                            }
                            $html .= '<tr>
                            <td>' . $coutn . '</td>
                            <td>' . $row->created_at . '</td>
                            <td>' . $row->id_pasos . ' | ' . $row->nombre . '</td>
                            <td>' . $informacion . '</td>
                            </tr>';
                        }
                        $html .= '</table>';
                    }
                }
            }
            return $html;
        } elseif ($r->opcion == 'verComprobanteAntes') {
            $html = '';
            $doc = DB::table($r->tabla)->where('id', $r->id)->first();
            $claveAcceso = isset($doc->clave_accesso) ? $doc->clave_accesso : (isset($doc->nro_autorizacion) ? $doc->nro_autorizacion : (isset($doc->autorizacion) ? $doc->autorizacion : ''));
            $arregloDatos = [
                'tabla' => $r->tabla,
                'id' => $r->id,
            ];
            $electro = new EmisionDocumentosController;
            if ($r->tabla == 'ct_ventas')
                $electro = $electro->getFactura(null, 'FACELECTRO', $arregloDatos);
            else if ($r->tabla == 'ct_compras')
                $electro = $electro->getLiquidacion(null, 'FACELECTRO', $arregloDatos);
            $log = DB::table('de_log_facturacion as f')
                ->select(
                    'f.informacion',
                    'f.created_at',
                    'p.nombre',
                    'f.id_pasos'
                )
                ->join('de_pasos as p', 'f.id_pasos', 'p.id')
                ->where('f.clave_acceso', $claveAcceso)
                ->where('f.id_documento', $r->id)
                ->orderBy('f.id', 'desc')
                ->first();
            $documentoElec = De_Documentos_Electronicos::where('id_documento', $r->id)->first();
            $logError = De_Log_Error::where('id_de_documentos_electronicos', $documentoElec->id)->first();
            if ($logError != '') {
                $logError = json_decode($logError->descripcion_error);
            }
            $mensajeError = isset($logError->comprobantes[0]->mensajes[0]->mensaje) ? $logError->comprobantes[0]->mensajes[0]->mensaje : '';
            $botonAccion = '';
            $colorMensaje = '';
            if ($mensajeError == 'ERROR SECUENCIAL REGISTRADO') {
                $colorMensaje = 'style="color:red;"';
                $botonAccion = '<a href="reenviar?id=' . $r->id . '&tabla=' . $r->tabla . '" class="btn btn-warning">Reenviar</a>';
            }
            $esta = isset($logError->estado) ? $logError->estado : '';
            echo '<a href="javascript:;" ' . $colorMensaje . '>' . $esta . ' | ' . $mensajeError . '</a>';
            echo '<pre>';
            print_r($electro);
            echo $botonAccion;
            exit;
        } elseif ($r->opcion == 'reprocesarDocumento') {
            $resultado = new EmisionDocumentosController;
            $doc = DB::table($r->tabla)->where('id', $r->id)->first();
            if ($doc != '') {
                $claveAcceso = isset($doc->clave_accesso) ? $doc->clave_accesso : (isset($doc->nro_autorizacion) ? $doc->nro_autorizacion : (isset($doc->autorizacion) ? $doc->autorizacion : ''));
                if ($claveAcceso != '') {
                    $resultado = $resultado->leerXml($r->claveAcceso);
                    echo '<pre>';
                    print_r($resultado);
                    exit;
                }
            }
        } elseif ($r->opcion == 'balance') {
            $idEmpresa = session('id_empresa');
            //$balance = DB::select('CALL sp_balance ("' . $idEmpresa . '","' . $r->d . '","' . $r->h . '")');
            $balance = DB::select(
                "select * from (
                    (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
                    1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
                    from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,1) and plan BETWEEN 1 AND 3)
                    UNION ALL
                    (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
                    1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
                    from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,4) and plan!=SUBSTRING(plan,1,1) AND (plan LIKE '1.%' OR plan LIKE '2.%'))
                    UNION ALL
                    (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
                    1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
                    from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,7) and plan!=SUBSTRING(plan,1,4)  and plan!=SUBSTRING(plan,1,1) AND (plan LIKE '1.%' OR plan LIKE '2.%'))
                    UNION ALL
                    (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
                    1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
                    from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,9) and plan!=SUBSTRING(plan,1,7) and plan!=SUBSTRING(plan,1,4)  and plan!=SUBSTRING(plan,1,1) AND (plan LIKE '1.%' OR plan LIKE '2.%'))
                    UNION ALL
                    (select plan,nombre,0 as debe,0 as haber,1 as saldo_deudor,2 as saldo_acreedor,0 as naturaleza,0 as naturaleza_2,
                    1 as total,nombre as tipo,SUBSTRING(plan,1,1) as id_clase,'' as clase,'' as id_grupo, '' as grupo,'' as id_cuenta,'' as cuenta,'' as id_subcuenta,'' as subcuenta
                    from plan_cuentas_empresa where id_empresa=$idEmpresa and plan=SUBSTRING(plan,1,10) and plan!=SUBSTRING(plan,1,9) and plan!=SUBSTRING(plan,1,7) and plan!=SUBSTRING(plan,1,4)  and plan!=SUBSTRING(plan,1,1) AND (plan LIKE '1.%' OR plan LIKE '2.%'))
                    UNION ALL
                    (SELECT pe.plan,pe.nombre,SUM(debe) AS debe,SUM(haber) AS haber,
                    IF(pe.naturaleza_2=1,SUM(IFNULL(haber,0)-IFNULL(debe,0)),0) AS saldo_acreedor,
                    IF(pe.naturaleza_2=0,SUM(IFNULL(debe,0)-IFNULL(haber,0)),0) AS saldo_deudor,
                    pe.naturaleza,pe.naturaleza_2,
                    (IFNULL(SUM(CASE WHEN MONTH(ac.fecha_asiento) BETWEEN 1 AND 12 THEN (IF(pe.naturaleza_2=1,debe-haber,haber-debe)) END),0)) AS total,
                    IF(SUBSTRING(pe.plan,1,1)='1','ACTIVOS',IF(SUBSTRING(pe.plan,1,1)='2','PASIVOS','PATRIMONIO')) AS tipo,
                    COALESCE((SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,1) AND id BETWEEN 1 AND 3 LIMIT 1),'') AS id_clase,
                    COALESCE((SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,1) AND id BETWEEN 1 AND 3 LIMIT 1),'') AS clase,
                    COALESCE((SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,4) LIMIT 1),'') AS id_grupo,
                    COALESCE((SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,4) LIMIT 1),'') AS grupo,
                    COALESCE((SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,7) LIMIT 1),'') AS id_cuenta,
                    COALESCE((SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,7) LIMIT 1),'') AS cuenta,
                    COALESCE((SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,10) LIMIT 1),(SELECT id FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,9) LIMIT 1),'') AS id_subcuenta,
                    COALESCE((SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,10) LIMIT 1),(SELECT nombre FROM plan_cuentas WHERE id=SUBSTRING(pe.plan,1,9) LIMIT 1),'') AS subcuenta
                    FROM ct_asientos_detalle ad
                    JOIN ct_asientos_cabecera ac ON ad.id_asiento_cabecera=ac.id
                    LEFT JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa=ac.id_empresa
                    LEFT JOIN plan_cuentas pc ON ad.id_plan_cuenta=pc.id
                    WHERE ac.id_empresa=$idEmpresa
                    AND ac.fecha_asiento<'$r->h'
                    AND (pe.plan LIKE '1.%' OR pe.plan LIKE '2.%' OR pe.plan LIKE '3.%')
                    GROUP BY pe.plan
                    ORDER BY pe.plan)
                    UNION ALL
                    (SELECT '2.01.07.05.01','PARTICIPACION 15%',SUM(debe) AS debe,SUM(haber) AS haber,IF(pe.naturaleza_2=1,SUM(IFNULL(haber,0)-IFNULL(debe,0)),0) AS saldo_acreedor,IF(IF(pe.naturaleza_2=0,(SUM(IFNULL(debe,0)-IFNULL(haber,0))*0.15)*-1,0)>0,IF(pe.naturaleza_2=0,(SUM(IFNULL(debe,0)-IFNULL(haber,0))*0.15)*-1,0),0) AS participacion,pe.naturaleza,pe.naturaleza_2,'total','PASIVOS','2','PASIVOS','2.01','','2.01.07','','2.01.07.05',''
                    FROM ct_asientos_detalle ad
                    JOIN ct_asientos_cabecera ac ON ad.id_asiento_cabecera=ac.id
                    LEFT JOIN plan_cuentas_empresa pe ON ad.id_plan_cuenta=pe.id_plan AND pe.id_empresa=ac.id_empresa
                    LEFT JOIN plan_cuentas pc ON ad.id_plan_cuenta=pc.id
                    WHERE ac.id_empresa=$idEmpresa
                    AND ac.fecha_asiento>='2021-01-01'
                    AND ac.fecha_asiento<'$r->h'
                    AND (pe.plan LIKE '4.%' OR pe.plan LIKE '5.%'))
                    UNION ALL
                    (select '3.07.01','UTILIDAD DEL EJERCICIO',IFNULL(sum(debe),0) as debe,IFNULL(sum(haber),0) as haber,
                    if(pe.naturaleza_2=1,sum(IFNULL(haber,0)-IFNULL(debe,0)),0) as saldo_acreedor,
                    IFNULL(iF((select tipo_rimpe from de_empresa where id_empresa=ac.id_empresa)=0,'0',
                    iF((select tipo_rimpe from de_empresa where id_empresa=ac.id_empresa)=1,                
                    (select
                    (((IF(pe.naturaleza_2=0,(sum(IFNULL(debe,0)-IFNULL(haber,0)))*-1,0)-inicio)*porcentaje)/100)+valor_fraccion
                    from ct_rango_impuesto_sri where inicio<=IF(pe.naturaleza_2=0,(sum(IFNULL(debe,0)-IFNULL(haber,0)))*-1,0) and fin>=IF(pe.naturaleza_2=0,(sum(IFNULL(debe,0)-IFNULL(haber,0)))*-1,0))
                    ,
                    iF((select tipo_rimpe from de_empresa where id_empresa=ac.id_empresa)=2,
                    (select ((IF(pe.naturaleza_2=0,(sum(IFNULL(debe,0)-IFNULL(haber,0)))*-1,0)-IF(pe.naturaleza_2=0,(sum(IFNULL(debe,0)-IFNULL(haber,0))*0.15)*-1,0))*porcentaje)/100 from ct_porcentaje_renta where id_empresa=ac.id_empresa and anio=YEAR($r->d)),
                    ''))),0) as impuestoCausado,                
                    pe.naturaleza,pe.naturaleza_2,'total','PATRIMONIO','3','PATRIMONIO','3.01','','3.01.07','','3.01.07.05',''
                    from ct_asientos_detalle ad
                    join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
                    left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
                    left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
                    where ac.id_empresa=$idEmpresa
                    and ac.fecha_asiento>='$r->d'
                    and ac.fecha_asiento<'$r->h'
                    and (pe.plan like '4.%' or pe.plan like '5.%'))) as con where con.total!=0 order by con.plan -- con.id_clase,con.id_grupo,con.id_cuenta,con.id_subcuenta;"
            );
            $resultadoActivos = explode('|', $this->armarBalance($balance, 1));
            $resultadoPasivos = explode('|', $this->armarBalance($balance, 2));
            $resultadoPatrimonio = explode('|', $this->armarBalance($balance, 3));
            $totalPasivoPatrimonio = ((float)$resultadoPasivos[1] + (float)$resultadoPatrimonio[1]) * -1;
            $html = '<table style="width:100%">
            <tr>
                <td style="width:50%" valign="top">
                    <table style="width:100%">
                    <tr>
                    <td>Cuenta</td>
                    <td>Detalle</td>
                    <td>Saldo</td>
                    <td>%</td>
                    </tr>
                    ' . $resultadoActivos[0] . '
                    </table>
                </td>
                <td style="width:50%" valign="top">
                    <table style="width:100%">
                        <tr>
                        <td>Cuenta</td>
                        <td>Detalle</td>
                        <td>Saldo</td>
                        <td>%</td>
                        </tr>
                        ' . $resultadoPasivos[0] . '
                        ' . $resultadoPatrimonio[0] . '
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table style="width:100%">
                        <tr>
                            <td style="width:25%">Total Activos</td>
                            <td style="width:25%" align="right">' . $resultadoActivos[1] . '</td>
                            <td style="width:25%">Total Pasivos + Patrimonio</td>
                            <td style="width:25%" align="right">' . $totalPasivoPatrimonio . '</td>
                        </tr>
                    </table>
                </td>
            </tr>
            ';

            $html .= '</table>';

            $html .= '<br/>            
            <a href="javascript:;">' . $this->temporizador() . '</a>';
            echo $html;
            exit;
        } elseif ($r->opcion == 'reenviar') {
            DB::beginTransaction();
            try {
                if ($r->tabla == 'ct_ventas') {
                    $arrayDocumento = [
                        'numero' => null,
                        'nro_comprobante' => null,
                        'nro_autorizacion' => null,
                        'doc_electronico' => 0,
                        'estado_electronica' => 0
                    ];
                    Ct_ventas::where('id', $r->id)->update($arrayDocumento);
                    DB::commit();
                    return redirect('tributacion/estadoDocumentos');
                    //exit;
                }
            } catch (Exception $e) {
                DB::rollBack();
                $arrayError = [
                    'error' => $e->getMessage(),
                    'linea' => $e->getLine()
                ];
                return json_encode($arrayError);
            }
        } elseif ($r->opcion == 'confirmarRetenciones') {
            if ($r->d != '' && $r->h != '') {
                $data['retenciones'] = Ct_Retenciones::join('proveedor as p', 'ct_retenciones.id_proveedor', 'p.id')
                    ->where('ct_retenciones.id_empresa', session('id_empresa'))
                    ->where('ct_retenciones.fecha', '>=', date('Y-m-d', strtotime($r->d)))
                    ->where('ct_retenciones.fecha', '<=', date('Y-m-d', strtotime($r->h)))
                    ->where('ct_retenciones.estado', 1)
                    ->get([
                        'ct_retenciones.id',
                        'ct_retenciones.fecha',
                        'p.razonsocial',
                        'p.id as ruc',
                        'ct_retenciones.autorizacion',
                        'ct_retenciones.nro_comprobante',
                        'ct_retenciones.valor_fuente',
                        'ct_retenciones.valor_iva'

                    ]);
            }
            $data['d'] = $r->d;
            $data['h'] = $r->h;
            return view('tributacion/view_retenciones_sistema_vs_sri', $data);
        } elseif ($r->opcion == 'getDatosSriImport') {
            $data = DB::table('log_importacion_sri')->get();
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            return json_encode($results);
        } elseif ($r->opcion == 'reporteSucursales') {
            $plan = Plan_Cuentas_Empresa::where('id_empresa', session('id_empresa'))
                ->whereRaw('plan=SUBSTRING(plan,1,7)')
                ->whereRaw('plan!=SUBSTRING(plan,1,4)')
                ->where(function ($q) {
                    $q->orWhere('plan', 'like', '1.%');
                    $q->orWhere('plan', 'like', '2.%');
                    $q->orWhere('plan', 'like', '3.%');
                    $q->orWhere('plan', 'like', '4.%');
                    $q->orWhere('plan', 'like', '5.%');
                })
                ->get([
                    'plan',
                    'nombre'
                ]);
            $combo = '<option value="">--Seleccionar--</option>';
            foreach ($plan as $row) {
                $combo .= '<option value="' . $row->plan . '">' . $row->plan . ' | ' . $row->nombre . '</option>';
            }
            echo '<form method="get" action="' . url('/') . '/tributacion/reporteSucursales">
            <table>
            <tr>
            <td>Fecha: <input id="fecha" name="fecha" type="date" /></td>
            </tr>
            <tr>
            <td>Cuenta: <select id="cuenta" name="cuenta">' . $combo . '</select></td>
            </tr>
            <tr>
            <td><input type="submit" value="Buscar" /></td>
            </tr>
            </table>
            </form>';
            if (count($r->input()) > 0) {
                $fecha_actual = date("Y-m-d", strtotime($r->fecha . "+ 1 days"));
                $asientos = Ct_Asientos_Detalle::join('ct_asientos_cabecera as ac', 'ct_asientos_detalle.id_asiento_cabecera', 'ac.id')
                    ->leftjoin('plan_cuentas_empresa as pe', function ($q) {
                        $q->on('ct_asientos_detalle.id_plan_cuenta', 'pe.id_plan');
                        $q->on('pe.id_empresa', 'ac.id_empresa');
                    })
                    ->leftjoin('plan_cuentas as pc', 'ct_asientos_detalle.id_plan_cuenta', 'pc.id')
                    ->where('ac.id_empresa', session('id_empresa'))
                    //->where('ct_asientos_detalle.fecha', '>=', '2022-01-01')
                    ->where('ct_asientos_detalle.fecha', '<', $fecha_actual)
                    ->where('pe.plan', 'like', $r->cuenta . '%')
                    ->orderBy('ct_asientos_detalle.fecha')
                    ->orderBy('pe.plan')
                    ->get([
                        'debe',
                        'haber',
                        'ct_asientos_detalle.fecha as fecha',
                        'pe.nombre as nombre',
                        'pe.plan',
                        'observacion',
                        'ac.id'
                    ]);
                $html = '<style>
            .color:hover{background:gray;}
            </style>
            <table>
            <tr>
            <td>Cuenta: </td>
            <td>' . $asientos[0]->plan . '</td>
            </tr>
            <tr>
            <td>Nombre: </td>
            <td>' . $asientos[0]->nombre . '</td>
            </tr>
            </table>
            <hr>
            <table style="width:100%;"><tr>
            <td>No.</td>
            <td>Fecha</td>
            <td>Sucursal</td>
            <td>Punto Emision</td>
            <td>Debe</td>
            <td>Haber</td>
            </tr>';
                if (count($asientos) > 0) {
                    $count = 0;
                    $sumDebe = 0;
                    $sumHaber = 0;
                    foreach ($asientos as $row) {
                        $dsucursal = $this->obtenerDatosDocumento($row->id);
                        $count++;
                        $sumDebe += $row->debe;
                        $sumHaber += $row->haber;
                        $sucursal = isset($dsucursal['sucursal']) ? $dsucursal['sucursal'] : '';
                        $puntoEmision = isset($dsucursal['punto_emision']) ? $dsucursal['punto_emision'] : '';
                        $html .= '<tr class="color">
                    <td>' . $count . '</td>
                    <td>' . $row->fecha . '</td>
                    <td>' . $sucursal . '</td>
                    <td>' . $puntoEmision . '</td>
                    <td>' . $row->observacion . '</td>
                    <td align="right">' . number_format($row->debe, 2) . '</td>
                    <td align="right">' . number_format($row->haber, 2) . '</td>
                    </tr>';
                    }
                    $html .= '<tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right">' . number_format($sumDebe, 2) . '</td>
                <td align="right">' . number_format($sumHaber, 2) . '</td>
                </tr>';
                }
                $html .= '</table>';
                $html .= '<table>
                <tr>
                    <td>Total Debe: </td>
                    <td align="right">' . number_format($sumDebe, 2) . '</td>
                </tr>
                <tr>
                <td>Total Haber: </td>
                <td align="right">' . number_format($sumHaber, 2) . '</td>
                </tr>
                <tr>
                <td>Total Saldo A/D: </td>
                <td align="right">' . number_format($sumDebe - $sumHaber, 2) . '</td>
                </tr>
                </table>';
                echo $html;
            }
        } elseif ($r->opcion == 'confirmarDocumento') {
            $claveObj = new ClaveAcceso();
            $campos = [
                'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($r->fecha))),
                'tipo_comprobante' => '01',
                'ruc' => session('id_empresa'),
                'tipo_ambiente' => 2,
                'punto_establecimiento' => '001',
                'punto_emision' => '100',
                'numero_comprobante' => $r->num,
                'codigo_numerico' => $r->num,
                'tipo_emision' => 1
            ];
            $claveObj->llenarDatos($campos);
            ini_set('max_execution_time', 300);
            $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
            $client = new SoapClient($url);
            $param = array(
                'claveAccesoComprobante' => $claveObj->clave_acceso
            );
            return json_encode($client->autorizacionComprobante($param));
        } else {
            $html = '';
            $num = Ct_ventas::where('id_empresa', session('id_empresa'))
                ->where('sucursal', '001')
                ->where('punto_emision', '100')
                ->where('nro_autorizacion', '<>', '')
                ->groupBy('numero')
                ->get([
                    'numero',
                ]);
            if (count($num) > 0) {
                $cont = 0;
                $numeros = [];
                foreach ($num as $row) {
                    $cont++;
                    $numeros[$cont] = (int)$row->numero;
                }
                $arrayRange = range(1, max($numeros));
                $missingValues = array_diff($arrayRange, $numeros);
                if (count($missingValues) > 0) {
                    $html = '<table>';
                    foreach ($missingValues as $row) {
                        $html .= '<tr><td>' . $row . '</td><td><input type="text" id="doc_' . $row . '" name="doc_' . $row . '" /></td><td><a href="javascript:;" onclick="confirmarDocumento(\'' . $row . '\')">validar</a></td></tr>';
                    }
                    $html .= '</table>
                    <script src="../bower_components/jquery/dist/jquery.min.js"></script>
                    <script>
                    function confirmarDocumento(num)
                    {
                        $.ajax({
                            url:"../tributacion/confirmarDocumento",
                            type:"get",
                            data:{
                                num:num,
                                fecha:$("#doc_"+num).val(),
                            },
                            dataType:"json",
                            success:function(data){
                                alert(data);
                            }
                        });
                    }                  
                    </script>';
                }
                echo $html;
            }
            $num = Ct_Retenciones::where('id_empresa', session('id_empresa'))
                ->where('sucursal', '001')
                ->where('punto_emision', '100')
                ->where('nro_autorizacion', '<>', null)
                ->groupBy('nro_secuencial')
                ->get([
                    'nro_secuencial',
                ]);
            if (count($num) > 0) {
                $cont = 0;
                $numeros = [];
                foreach ($num as $row) {
                    $cont++;
                    $numeros[$cont] = (int)$row->nro_secuencial;
                }
                $arrayRange = range(1, max($numeros));
                $missingValues = array_diff($arrayRange, $numeros);
                echo 'Retenciones';
                print_r($missingValues);
            }
            exit;
        }
    }
    public function obtenerDatosDocumento($id)
    {
        $dsucursal = [];
        $dsucursal = Contable::compras($id);
        if (count($dsucursal) == 0) {
            $dsucursal = Contable::bancos($id);
        }
        if (count($dsucursal) == 0) {
            $dsucursal = Contable::ventas($id);
        }
        return $dsucursal;
    }
    public function confirmarRetenciones($d, $h)
    {
        $data['retenciones'] = Ct_Retenciones::join('proveedor as p', 'ct_retenciones.id_proveedor', 'p.id')
            ->where('ct_retenciones.fecha', '>=', date('Y-m-d', strtotime($d)))
            ->where('ct_retenciones.fecha', '<=', date('Y-m-d', strtotime($h)))
            ->where('ct_retenciones.estado', 1)
            ->get([
                'ct_retenciones.id',
                'ct_retenciones.fecha',
                'p.razonsocial',
                'p.id as ruc',
                'ct_retenciones.autorizacion',
                'ct_retenciones.nro_comprobante',
                'ct_retenciones.valor_fuente',
                'ct_retenciones.valor_iva'

            ]);
        if (count($data['retenciones']) > 0) {
            return view('tributacion/view_retenciones_sistema_vs_sri', $data);
        }
    }
    public function armarBalance($dato, $tipo)
    {
        $html = '';
        $total = 0;
        $activo = 0;
        $porcentaje = 100;
        if (count($dato) > 0) {
            foreach ($dato as $row) {
                if ($row->id_clase == $tipo) {
                    $activo = (float)$row->debe - (float)$row->haber;
                    $saldo = $activo;
                    $total += $saldo;
                }
            }
            $mostrar = 0;
            foreach ($dato as $row) {
                if ($row->id_clase == $tipo) {
                    $activo = (float)$row->debe - (float)$row->haber;
                    $saldo = $activo;

                    if (strlen($row->plan) == 1) {
                        $saldo = $total;
                    } else {
                        if ($saldo != 0)
                            $porcentaje = ($saldo * 100) / $total;
                        else
                            $porcentaje = 0;
                    }
                    if ($row->id_clase == 2 || $row->id_clase == 3) {
                        if ($saldo < 0)
                            $saldo = $saldo * -1;
                    }
                    if ($saldo < 0) {
                        $saldo = $saldo * -1;
                        $saldo = '(' . $saldo . ')';
                    }
                    if ($porcentaje < 0)
                        $porcentaje = $porcentaje * -1;
                    $sangria = '';
                    if (strlen($row->plan) == 4)
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    if (strlen($row->plan) == 7)
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    if (strlen($row->plan) == 9 || strlen($row->plan) == 10)
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    if (strlen($row->plan) == 12 || strlen($row->plan) == 13)
                        $sangria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';


                    $html .= '<tr>
                            <td>' . $row->plan . '</td>
                            <td>' . $sangria . $row->nombre . '</td>
                            <td align="right">' . $saldo . '</td>
                            <td align="right">' . number_format($porcentaje, 2) . ' %</td>
                            </tr>';
                    $sangria = '';
                }
            }
        }
        return $html . '|' . $total;
    }
    public function temporizador()
    {
        $mi_temporizador = microtime();
        $partes_de_la_hora_actual = explode(' ', $mi_temporizador);
        $hora_actual = $partes_de_la_hora_actual[1] + $partes_de_la_hora_actual[0];
        $hora_al_empezar = $hora_actual;

        $hora_actual = $partes_de_la_hora_actual[1] + $partes_de_la_hora_actual[0];
        $hora_al_terminar = $hora_actual;
        $tiempo_total_en_segundos = round(($hora_al_terminar - $hora_al_empezar), 4);
        return 'La pagina fue generada en ' . $tiempo_total_en_segundos . ' segundos.';
    }
    public function buscar_empreados_107(Request $r)
    {
        /*  $data[] = array('value' => $usuario->apellido1 . ' ' . $usuario->apellido2 . ' ' . $usuario->nombre1 . ' ' . $usuario->nombre2, 'id' => $usuario->id); */
        $dataImpo = Ct_Nomina::where('estado', '1')->where('id_user', 'LIKE', '%' . $r->term . '%')->get();
        foreach ($dataImpo as $value) {
            $data[] = array('value' => $value->nombres, 'id' => $value->id_user);
        }
        if (count($dataImpo) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function formulario_107(Request $r)
    {

        if (!isset(Auth::user()->id))
            return redirect('login');
        $data['datos'] = Ct_Formulario107::where('id_empresa', session('id_empresa'))->get();
        $data['empresa'] = Empresa::where('id', session('id_empresa'))->where('estado', '1')->first();
        $data['periodos'] = Ct_Formulario107::groupBy('periodo')->get(['periodo']);
        if (!isset($r->id)) {
            return view('tributacion/view_formularios_107', $data);
        } else {
            $data['info'] = Ct_Formulario107::where('id', $r->id)->first();
            return view('tributacion/view_formulario_107', $data);
        }
    }
    public function estado_documento_sri(Request $r)
    {
        if (!isset(Auth::user()->id) || Auth::user()->id == '')
            return redirect('login');
        $id_empresa = session('id_empresa');
        $data['empresa'] = Empresa::where('id', $id_empresa)->first();
        $ventas = Ct_ventas::selectRaw('ct_ventas.id,1 as documento,fecha,sucursal,punto_emision,numero,nro_comprobante,nro_autorizacion,doc_electronico,id_cliente as ruc,c.nombre,p.nombre as estado')
            ->join('ct_clientes as c', 'ct_ventas.id_cliente', 'c.identificacion')
            ->leftjoin('de_pasos as p', 'ct_ventas.doc_electronico', 'p.id')
            ->where('id_empresa', session('id_empresa'))
            ->whereYear('fecha', '>=', 2022)
            ->where('ct_ventas.estado', '!=', 0)
            ->where('electronica', 1)
            ->orderBy('fecha', 'desc')
            ->limit(600);
        $compras = Ct_compras::selectRaw('ct_compras.id,3 as documento,fecha,sucursal,punto_emision,secuencia_f,numero,nro_autorizacion,doc_electronico,proveedor as ruc,IFNULL(razonsocial,nombrecomercial) as nombre,p.nombre as estado')
            ->join('proveedor as pr', 'ct_compras.proveedor', 'pr.id')
            ->leftjoin('de_pasos as p', 'ct_compras.doc_electronico', 'p.id')
            ->where('id_empresa', session('id_empresa'))
            ->whereYear('fecha', '>=', 2022)
            ->where('ct_compras.estado', '!=', 0)
            ->where('electronica', 1)
            ->orderBy('fecha', 'desc')
            ->limit(100);
        $nc = Ct_Nota_Credito_Clientes::selectRaw('ct_nota_credito_clientes.id,4 as documento,fecha,sucursal,punto_emision,secuencia,nro_comprobante,nro_autorizacion,doc_electronico,id_cliente as ruc,c.nombre,p.nombre as estado')
            ->join('ct_clientes as c', 'ct_nota_credito_clientes.id_cliente', 'c.identificacion')
            ->leftjoin('de_pasos as p', 'ct_nota_credito_clientes.doc_electronico', 'p.id')
            ->where('id_empresa', session('id_empresa'))
            ->whereYear('fecha', '>=', 2022)
            ->where('ct_nota_credito_clientes.estado', '!=', 0)
            ->where('electronica', 1)
            ->orderBy('fecha', 'desc')
            ->limit(100);
        $guias = Ct_Guia_Remision_Cabecera::selectRaw('ct_cabecera_remision.id,6 as documento,fecha_emision_documento,establecimiento,punto_emision,LPAD(num_secuencial,9,0) as secuencia,concat(establecimiento,"-",punto_emision,"-",LPAD(num_secuencial,9,0)) as nro_comprobante,clave_acceso as nro_autorizacion,ct_cabecera_remision.estado as doc_electronico,ci_destinatario as ruc,razon_social_destinatario as nombre,p.nombre as estado')
            ->leftjoin('de_pasos as p', 'ct_cabecera_remision.estado', 'p.id')
            ->where('id_empresa', session('id_empresa'))
            ->orderBy('fecha_emision_documento', 'desc')
            ->limit(100);
        $retenciones = Ct_Retenciones::selectRaw('ct_retenciones.id,7 as documento,fecha,sucursal,punto_emision,secuencia,nro_comprobante,ct_retenciones.autorizacion as nro_autorizacion,doc_electronico,id_proveedor as ruc,IFNULL(razonsocial,nombrecomercial) as nombre,p.nombre as estado')
            ->join('proveedor as pr', 'ct_retenciones.id_proveedor', 'pr.id')
            ->leftjoin('de_pasos as p', 'ct_retenciones.doc_electronico', 'p.id')
            ->where('id_empresa', session('id_empresa'))
            ->whereYear('fecha', '>=', 2022)
            ->where('ct_retenciones.estado', '!=', 0)
            ->where('electronica', 1)
            ->orderBy('fecha', 'desc')
            ->limit(100)
            ->unionAll($ventas)
            ->unionAll($compras)
            ->unionAll($nc)
            ->unionAll($guias)
            ->get();
        $data['documentos'] = $retenciones;
        return view('tributacion/view_estado_documentos', $data);
    }
    public function descargar_xml(Request $request)
    {
        dd("asdasdsds");
    }
}
