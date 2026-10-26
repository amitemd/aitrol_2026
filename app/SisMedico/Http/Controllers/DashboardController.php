<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Route;
use Carbon\Carbon;
use Sis_medico\Empresa;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Movimiento;
use Sis_medico\Producto;
use App;
use Sis_medico\Ct_ventas;
use Sis_medico\MenuUsuario;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\PresentacionTitulos;
use Sis_medico\reprocesos\AsientosNC;
use Sis_medico\User;

use Sis_medico\InvCabMovimientos;
use Sis_medico\reprocesos\FacturasServiciosProductos;
use Sis_medico\UsuarioEmpresa;
use Sis_medico\Proforma_Venta_Cab;

class DashboardController extends Controller
{
    const DRIVE_REGISTRO_SANITARIO_FOLDER_ID = '1LW28XTSRHxdCgVaRT8hM2hBgfSIAJyFU';

    const DRIVE_FICHA_TECNICA_FOLDER_ID = '18f26eCw0js0Z9yYczm5AUzHs3eLqVJU4';

    const DRIVE_FICHA_TECNICA_FOLDER_IDS = [
        '18f26eCw0js0Z9yYczm5AUzHs3eLqVJU4',
        '1oguUM8gJxsemd9WFimDlfHYVzAs6huK7',
    ];

    const TIPO_USUARIO_SISTEMA = 1;

    const USUARIO_DASHBOARD_GASTROCLINICA = '0923306963';

    const TIPOS_VENTA = ['VEN-FA', 'VENFA-CO'];

    const SERVICIOS = [
        'procedimientos' => 'Procedimientos',
        'imagenes' => 'Imágenes',
        'consultas' => 'Consultas',
    ];

    const  empresasPermitidas = [
            // santo
            '2390058384001',
            // quito
            '1793135579001',
            // manta
            '1391927177001',
            // milagro
            '0993375469001',
            //guayaquil
            '0992704152001'
        ];

    const COBERTURAS = [
        'particular' => 'Particular',
        'privado' => 'Seguro privado',
        'publico' => 'Público (IESS, ISSPOL, ISSFA)',
    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*$idusuario  = Auth::user()->id;
        $usuario = User::where('id', $idusuario)->first();
        if ($usuario->new_system == 1) {
            return redirect('v3/admin');
        }*/


        // if (Auth::user()->id == "0953905999") {
        //     $asientos = DB::table('ct_asientos_cabecera')->where('observacion', 'like', '%Salida de Consignacion%')->where('estado', '<>', '0')->get();
        //     foreach($asientos as $cabecera){
        //         $detalle = DB::table('ct_asientos_detalle')->where('id_asiento_cabecera', $cabecera->id)->get();
        //         dd($detalle);

        //     }
        //     //dd($locale);
        // }

        if ($request->opcion == 'detalleKardex') {
            return FacturasServiciosProductos::detalleKardex($request->id);
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');


        //informe JM valores por clientes de productos
        if ($idusuario == '0922729587') {
            //dd("okay gracias amigo");
        }

        if ($idusuario == '0921605895') {

            //26/08/2023 AOMS
            /*$data['ventas'] = FacturasServiciosProductos::reprocesoProductoServicios($request);
            return view('util/view_reporcesoPS', $data);*/

            //$id_empresa == '0993069299001' && 
            //AsientosNC::reprocesarAsientoNC($request);
            //Masivo para crear plan de cuenta AOMS
            /*$plan = Plan_Cuentas_Empresa::where('id_empresa', '0993265802001')->get(); //0922729587001
            if (count($plan) > 0) {
                DB::beginTransaction();
                foreach ($plan as $row) {
                    $arrayPlan = [
                        'nombre' => $row->nombre,
                        'descripcion' => $row->descripcion,
                        'estado' => $row->estado,
                        'id_padre' => $row->id_padre,
                        'id_usuariocrea' => '0921605895',
                        'id_usuariomod' => '0921605895',
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'naturaleza' => $row->naturaleza,
                        'naturaleza_2' => $row->naturaleza_2,
                        'id_empresa' => '0922729587001',
                        'id_plan' => $row->id_plan,
                        'plan' => $row->plan,
                        'estado_cierre_ano' => $row->estado_cierre_ano
                    ];
                    Plan_Cuentas_Empresa::insert($arrayPlan);
                }
                $plan = Plan_Cuentas_Empresa::where('id_empresa', '0922729587001')->get(); //0922729587001
                echo '<pre>';
                print_r($plan);
                DB::rollBack();
                DB::commit();
                exit;
            }*/
        }
        if ($idusuario == '0922729587') {

            //$datos = \Sis_medico\reprocesos\PlanillaReproceso::reprocesarplanilla();
            //dd("okay gracias amigo");
            //$datos = \Sis_medico\reprocesos\Compras::reprocesarcompras();

            /* prueba de planillas*/
            /*$empresa = \Sis_medico\Empresa::where('prioridad', 1)->first();
            $id_empresa = $empresa->id;
            $t = strtotime("-1 day");
            $panillas = \Sis_medico\Planilla::where('estado', 1)
                ->where('aprobado', 1)
                ->whereNull('id_asiento_cabecera')
                ->where('updated_at', '<=',  date('Y-m-d', $t) . ' 00:00:00')
                ->get();
            foreach ($panillas as $value) {
                $valor = \Sis_medico\Http\Controllers\Insumos\PlantillaControlController::generar_asiento($value->id, $id_empresa);
            }
            dd("okay gracias amigo");*/
            /*$datos = \Sis_medico\reprocesos\InvInventarioReproceso::reprocesarcostopromedio($id_empresa);
            $datos = \Sis_medico\reprocesos\Compras::reprocesarcompras();
            $datos = \Sis_medico\reprocesos\PlanillaReproceso::reprocesarplanilla();
            dd('terminado');*/
            //$datos = \Sis_medico\reprocesos\EgresoVarioReproceso::reprocesaregreso();
            //$datos = \Sis_medico\reprocesos\PlanillaReproceso::reprocesarplanilla();
            //dd($datos);

            //InvCabMovimientos::anularTransaccionBodega('1285');
            //InvCabMovimientos::anularTransaccionBodega('1273');
            //InvCabMovimientos::anularTransaccionBodega('1277');
        }
        /*if($idusuario == '0922729587'){
            $cardiologias = Producto::where('a.codigo_siempre', '1')->get();
            //Titulos excel
            $titulos = array("Nombre", "Codigo", "Registro Sanitario");
            //Posiciones en el excel
            $posicion = array("A", "B", "C");
            Excel::create('Excel Angelica', function ($excel) use ($titulos, $posicion, $cardiologias) {
                $excel->sheet('REPORTE DE Productos', function ($sheet) use ($titulos, $posicion, $cardiologias) {
                    $sheet->mergeCells('A1:1');
                    $sheet->cell('A1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('REPORTE DE Productos');
                        $cell->setFontSize(18);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo = 2;
                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#92CFEF');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    foreach ($cardiologias as $soporte) {
                        $datos_excel = array();
                        $est_cita = ["0" => "POR CONFIRMAR", "1" => "CONFIRMADA", "2" => "REAGENDADO", "3" => "SUSPENDIDO", "4" => "ADMISIONADO"];
                        $texto = strip_tags($soporte->cuadro_clinico);
                        $new = str_replace(array('&ntilde;', '&Ntilde;', '&aacute;', '&eacute;', '&iacute;', "&oacute;", "&uacute;", '&Aacute;', '&Eacute;', '&Iacute;', "&Oacute;", "&Uacute;", "&nbsp;"), array("ñ", "Ñ", "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", " "), $texto);
                        array_push($datos_excel, $soporte->id_agenda, $soporte->fechaini, $soporte->id_paciente, $soporte->nombre1paciente . ' ' . $soporte->apellidopaciente . ' ' . $soporte->apellidopaciente2, $soporte->fecha_nacimiento, "{$soporte->nombre1} {$soporte->apellido1} {$soporte->apellido2}", $est_cita["{$soporte->estado_cita}"], $soporte->motivo, $soporte->seguro, $new);
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                });
                $excel->getActiveSheet()->getColumnDimension("H")->setWidth(50)->setAutosize(false);
            })->export('xlsx');
        }*/
        /*if ($idusuario == '0922729587') {
            $panillas = \Sis_medico\Planilla::where('estado', 1)->where('aprobado', 1)->whereNull('id_asiento_cabecera')->get();
            foreach ($panillas as $value) {
                $valor = \Sis_medico\Http\Controllers\Insumos\PlantillaControlController::generar_asiento($value->id, $id_empresa);
            }

            dd('okay, gracias amigo');
        }*/

        //reparar comprobante de ingreso

        /*if ($idusuario == '0922729587') {
        $comprobantes = Ct_Detalle_Comprobante_Ingreso::whereRaw('total > total_factura')->where('id_usuariocrea', 'FACELECTRO')->get();
        foreach ($comprobantes as $key => $value) {
        $factura = Ct_ventas::find($value->id_factura);
        if (!is_null($factura)) {
        $comprobante_2                = Ct_Detalle_Comprobante_Ingreso::find($value->id);
        $comprobante_2->total_factura = $factura->total_final;
        $comprobante_2->save();
        }
        }
        dd('okay gracias amigo');
        }*/

        /*$medicina   = DB::table('vade_producto')->get();
        /*foreach ($medicina as $value) {
        $verificar = Medicina::where('nombre', 'like', '%' . $value->producto . '%')->first();
        if (is_null($verificar)) {
        $principios  = DB::table('vade_principio_producto')->where('id_producto', $value->id)->get();
        $id_medicina = DB::table('medicina')->insertGetId([
        'nombre'             => $value->producto,
        'dosis'              => $value->dosificacion,
        'presentacion'       => $value->presentacion,
        'publico_privado'    => 1,
        'estado'             => 1,
        'iess'               => 0,
        'laboratorio'        => $value->laboratorio,
        'precio_unitario'    => $value->precio_unitario,
        'precio_total'       => $value->precio_total,
        'atc'                => $value->atc,
        'indicaciones'       => $value->indicaciones,
        'contraindicaciones' => $value->contraindicaciones,
        'dosis_pediatrica'   => $value->dosis_pediatrica,
        'consep'             => $value->consep,
        'ip_creacion'        => $ip_cliente,
        'ip_modificacion'    => $ip_cliente,
        'id_usuariocrea'     => $idusuario,
        'id_usuariomod'      => $idusuario,
        ]);
        foreach ($principios as $principio) {
        $activo = DB::table('principio_activo')->where('nombre', 'like', $principio->nombre)->first();
        if (is_null($activo)) {
        $id_activo = DB::table('medicina')->insertGetId([
        'nombre'          => strtoupper($principio->nombre),
        'descripcion'     => strtoupper($principio->nombre),

        'estado'          => 1,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ]);
        } else {
        $id_activo = $activo->id;
        }
        DB::table('medicina_principio')->insert([
        'id_medicina'         => $id_medicina,
        'id_principio_activo' => $id_activo,
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        'id_usuariocrea'      => $idusuario,
        'id_usuariomod'       => $idusuario,
        ]);
        }
        } else {
        if (is_null($verificar->presentacion)) {
        $principios                    = DB::table('vade_principio_producto')->where('id_producto', $value->id)->get();
        $verificar->nombre             = $value->producto;
        $verificar->dosis              = $value->dosificacion;
        $verificar->presentacion       = $value->presentacion;
        $verificar->publico_privado    = 1;
        $verificar->estado             = 1;
        $verificar->iess               = 0;
        $verificar->laboratorio        = $value->laboratorio;
        $verificar->precio_unitario    = $value->precio_unitario;
        $verificar->precio_total       = $value->precio_total;
        $verificar->atc                = $value->atc;
        $verificar->indicaciones       = $value->indicaciones;
        $verificar->contraindicaciones = $value->contraindicaciones;
        $verificar->dosis_pediatrica   = $value->dosis_pediatrica;
        $verificar->consep             = $value->consep;
        $verificar->ip_modificacion    = $ip_cliente;
        $verificar->id_usuariomod      = $idusuario;
        $verificar->save();
        foreach ($principios as $principio) {
        $activo = DB::table('principio_activo')->where('nombre', 'like', $principio->nombre)->first();
        if (is_null($activo)) {
        $id_activo = DB::table('medicina')->insertGetId([
        'nombre'          => strtoupper($principio->nombre),
        'descripcion'     => strtoupper($principio->nombre),
        'estado'          => 1,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ]);
        } else {
        $id_activo = $activo->id;
        }
        $existente_relacion = DB::table('medicina_principio')->first();
        DB::table('medicina_principio')->insert([
        'id_medicina'         => $id_medicina,
        'id_principio_activo' => $id_activo,
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        'id_usuariocrea'      => $idusuario,
        'id_usuariomod'       => $idusuario,
        ]);
        }
        } elseif ($value->presentacion != $verificar->presentacion) {
        $principios  = DB::table('vade_principio_producto')->where('id_producto', $value->id)->get();
        $id_medicina = DB::table('medicina')->insertGetId([
        'nombre'             => $value->producto,
        'dosis'              => $value->dosificacion,
        'presentacion'       => $value->presentacion,
        'publico_privado'    => 1,
        'estado'             => 1,
        'iess'               => 0,
        'laboratorio'        => $value->laboratorio,
        'precio_unitario'    => $value->precio_unitario,
        'precio_total'       => $value->precio_total,
        'atc'                => $value->atc,
        'indicaciones'       => $value->indicaciones,
        'contraindicaciones' => $value->contraindicaciones,
        'dosis_pediatrica'   => $value->dosis_pediatrica,
        'consep'             => $value->consep,
        'ip_creacion'        => $ip_cliente,
        'ip_modificacion'    => $ip_cliente,
        'id_usuariocrea'     => $idusuario,
        'id_usuariomod'      => $idusuario,
        ]);
        foreach ($principios as $principio) {
        $activo = DB::table('principio_activo')->where('nombre', 'like', $principio->nombre)->first();
        if (is_null($activo)) {
        $id_activo = DB::table('medicina')->insertGetId([
        'nombre'          => strtoupper($principio->nombre),
        'descripcion'     => strtoupper($principio->nombre),
        'estado'          => 1,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ]);
        } else {
        $id_activo = $activo->id;
        }
        DB::table('medicina_principio')->insert([
        'id_medicina'         => $id_medicina,
        'id_principio_activo' => $id_activo,
        'ip_creacion'         => $ip_cliente,
        'ip_modificacion'     => $ip_cliente,
        'id_usuariocrea'      => $idusuario,
        'id_usuariomod'       => $idusuario,
        ]);
        }
        }
        }
        }
        dd('termino');*/

        $rolUsuario = Auth::user()->id_tipo_usuario;

        /*if (Auth::user()->id == '0922729587') {
        $retenciones = DB::table('ct_retenciones')
        ->where('id_empresa', '0993094072001')
        ->where('fecha', '>', '2022-01-01')
        ->get();

        foreach ($retenciones as $retencion) {
        $detalles = DB::table('ct_detalle_retenciones')
        ->where('id_retenciones', $retencion->id)
        ->get();
        foreach ($detalles as $detalle) {
        if ($detalle->porcentaje == '1.75') {
        $asiento_d = Ct_Asientos_Detalle::where('id_asiento_cabecera', $retencion->id_asiento_cabecera)
        ->where('id_plan_cuenta', '2.01.07.01.07')
        ->first();
        if (!is_null($asiento_d)) {
        $asiento_d->id_plan_cuenta = '2.01.07.01.13';
        $asiento_d->descripcion    = 'Retenciones Realizadas del Impuesto a la Renta 1.75%';
        $asiento_d->save();
        }

        }
        }
        }

        }*/



        $empresa_hos     = Empresa::where('prioridad', 2)->first();
        $usuario_empresa = UsuarioEmpresa::where('id_usuario', Auth::user()->id)->first();
        if (!is_null($usuario_empresa) && !is_null($empresa_hos)) {
            if ($usuario_empresa->id_empresa == $empresa_hos->id) {
                return redirect()->route("hospital.index");
            }
        }
        if (in_array($rolUsuario, array(23)) == true) {
            return redirect()->route("hospital.index");
        }
        if (in_array($rolUsuario, array(3)) == true) {
            return redirect()->route("nuevo.diseño");
        }

        $id_empresa = $request->session()->get('id_empresa');
        if (is_null($id_empresa)) {
            if ($rolUsuario == 10 || $rolUsuario == 12) {
                $n_empresa = Empresa::where('prioridad', 1)->first();
                $request->session()->put('id_empresa', $n_empresa->id);
            } else {
                $n_empresa = Empresa::where('prioridad', 1)->first();
                $request->session()->put('id_empresa', $n_empresa->id);
            }
        }

        if (in_array($rolUsuario, array(2)) == true) {
            return redirect()->route("paciente.historial_orden_lab_paciente");
        }

        /*
        $variable   = DB::table('vade_principio')->get();
        foreach ($variable as $value) {
        $existe = DB::table('principio_activo')->where('nombre', 'like', "'%" . $value->nombre . "%'")->get();
        if ($existe == '[]') {
        DB::table('principio_activo')->insert([
        'nombre'          => strtoupper($value->nombre),
        'descripcion'     => strtoupper($value->nombre),
        'estado'          => 1,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        ]);
        }
        }
        dd('salio');*/

        /*$planes = Plan_Cuentas::all();
        dd($planes);*/
        $month    = date('m');
        $year     = date('Y');
        $fecha    = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
        $fecha2   = date('m-d');
        $fecha3   = $fecha;
        $fecha3   = strtotime('+1 month', strtotime($fecha3));
        $variable = date('m', $fecha3);
        $fecha3   = date('m', $fecha3);
        $fecha    = date('m');

        $ch = DB::select("SELECT *
            FROM users
            WHERE
            id_tipo_usuario <> 2 AND
            estado = 1 AND
            fecha_nacimiento LIKE '%-" . $fecha2 . "'
            ORDER BY DAY(fecha_nacimiento) ASC");

        $ca = DB::select("SELECT *
            FROM users
            WHERE
            id_tipo_usuario <> 2 AND
            estado = 1 AND
            fecha_nacimiento LIKE '%-" . $fecha . "-%' AND
            fecha_nacimiento NOT LIKE '%" . $fecha2 . "'
            ORDER BY DAY(fecha_nacimiento) ASC");
        $pm = DB::select("SELECT *
            FROM users
            WHERE
            id_tipo_usuario <> 2 AND
            estado = 1 AND
            fecha_nacimiento LIKE '%-" . $fecha3 . "-%'
            ORDER BY DAY(fecha_nacimiento) ASC");
        $faltantes = DB::select("SELECT a.*, p.nombre1 as pnombre1, p.nombre2 as pnombre2, p.apellido1 as papellido1, p.apellido2 as papellido2, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2
            FROM agenda a, paciente p, users d
            WHERE
            a.estado_cita Like '2' AND
            a.id_paciente = p.id AND
            a.id_doctor1 = d.id AND
            a.estado like '-1' ");
        $agregados = DB::select("SELECT a.*, u.nombre1 as pnombre1, u.nombre2 as pnombre2, u.apellido1 as papellido1, u.apellido2 as papellido2, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2
            FROM agenda a, users u, users d
            WHERE
            a.estado_cita = 2 AND
            a.id_paciente = u.id AND
            a.id_doctor1 = d.id AND
            a.estado = 1");

        $stock = DB::select("SELECT * from producto p where p.cantidad < p.minimo");

        /*$fecha_cadu = DB::table('producto as p')
            ->leftjoin('movimiento as m', 'm.id_producto', 'p.id')
            ->leftjoin('pedido as ped', 'ped.id', 'm.id_pedido')
            ->leftjoin('bodega as b', 'b.id', 'm.id_bodega')
            ->leftjoin('proveedor as prov', 'prov.id', 'ped.id_proveedor')
            ->leftjoin('marca as mar', 'mar.id', 'p.id_marca')
            ->leftjoin('tipo as tip', 'tip.id', 'p.tipo_producto')
            ->where('m.cantidad', '>=', '1')
            ->where('m.estado', '1')
            ->where(function ($query) {
                $query->where('m.tipo', '2')
                    ->orWhere('m.tipo', '1');
            })
            ->where(function ($query) {
                $query->where('m.fecha_vencimiento', '<', 'now()')
                    ->orWhereRaw('m.fecha_vencimiento < DATE_ADD(NOW(), INTERVAL 3 MONTH)');
            })
            ->groupBy('m.serie')
            ->select(DB::raw('count(*) as cantidad, m.tipo'), 'p.codigo', 'p.nombre', 'p.descripcion', 'ped.pedido', 'm.serie', 'm.fecha_vencimiento', 'b.nombre as nombre_bodega', 'prov.nombrecomercial', 'mar.nombre as nombre_marca', 'tip.nombre as nombre_tipoproducto', 'm.id as id_movimiento')->get();*/

        if ($rolUsuario == 1) {
            /*$hc_log = Hc_Log::where('created_at', '>', '2020-05-16 00:00:00')
        ->where('created_at', '<', '2020-05-31 23:59:59')
        ->whereNotNull('id_procedimiento')
        ->distinct()
        ->get(['id_procedimiento']);
        //dd($hc_log);
        foreach ($hc_log as $llave => $value) {
        //dd($value);
        if ($llave > 16) {
        $log_1 = hc_log::where('id_procedimiento', $value->id_procedimiento)->get();
        if (count($log_1) > 2) {
        //echo $llave;
        //dd($hc_log);
        $parar = count($log_1) - 2;
        foreach ($log_1 as $key => $log_2) {
        if ($key == $parar) {
        break;
        }
        $log_2->delete();
        }
        }
        }
        }
        dd('termino');*/
        }

        /*if ($rolUsuario == 1) {
        $fecha_actual = date("Y-m-d");
        $n_fecha_v    = date("Y-m-d", strtotime($fecha_actual . "+6 month"));

        //dd(count($fecha_cadu));

        foreach ($fecha_cadu as $vvlv) {

        if ($vvlv->id_movimiento == 278) {
        dd($vvl);
        }
        if ($vvlv->fecha_vencimiento <= date('Y-m-d')) {
        //dd($vvlv);
        $affected = DB::table('movimiento')
        ->where('id', $vvlv->id_movimiento)
        ->update(['fecha_vencimiento' => $n_fecha_v]);

        //dd($affected);
        }
        }
        }*/

        /*$fecha_cadu_2 = DB::table('producto as p')
        ->leftjoin('movimiento as m', 'm.id_producto', 'p.id')
        ->leftjoin('pedido as ped', 'ped.id', 'm.id_pedido')
        ->leftjoin('bodega as b', 'b.id', 'm.id_bodega')
        ->leftjoin('proveedor as prov', 'prov.id', 'ped.id_proveedor')
        ->leftjoin('marca as mar', 'mar.id', 'p.id_marca')
        ->leftjoin('tipo as tip', 'tip.id', 'p.tipo_producto')
        ->where('m.cantidad', '>=', '1')
        ->where('m.estado', '0')
        ->where(function ($query) {
        $query->where('m.tipo', '2')
        ->orWhere('m.tipo', '1');})
        ->where(function ($query) {
        $query->where('m.fecha_vencimiento', '<', 'now()')
        ->orWhereRaw('m.fecha_vencimiento < DATE_ADD(NOW(), INTERVAL 3 MONTH)');})
        ->groupBy('m.serie')
        ->select(DB::raw('count(*) as cantidad, m.tipo'), 'p.codigo', 'p.nombre', 'p.descripcion', 'ped.pedido', 'm.serie', 'm.fecha_vencimiento', 'b.nombre as nombre_bodega', 'prov.nombrecomercial', 'mar.nombre as nombre_marca', 'tip.nombre as nombre_tipoproducto', 'm.id as id_movimiento')->get();

        if ($rolUsuario == 1) {
        $fecha_actual = date("Y-m-d");
        $n_fecha_v    = date("Y-m-d", strtotime($fecha_actual . "+6 month"));

        //dd(count($fecha_cadu_2));

        foreach ($fecha_cadu_2 as $vvlv) {

        if ($vvlv->id_movimiento == 278) {
        dd($vvl);
        }
        if ($vvlv->fecha_vencimiento <= date('Y-m-d')) {
        //dd($vvlv);
        $affected = DB::table('movimiento')
        ->where('id', $vvlv->id_movimiento)
        ->update(['fecha_vencimiento' => $n_fecha_v]);

        //dd($affected);
        }
        }
        }*/
        $id_empresa = $request->session()->get('id_empresa');
        /*  C    M   L   */
        if (Auth::user()->id == "5599887744") {
            //dd($id_empresa);

        }
        $caducado = InvDetMovimientos::where(function ($query) {
            $query->where('fecha_vence', '<', 'now()')
                ->orWhereRaw('fecha_vence < DATE_ADD(NOW(), INTERVAL 3 MONTH)');
        })
            ->leftJoin('inv_inventario', 'inv_inventario.id', '=', 'inv_det_movimientos.id_inv_inventario')
            ->where('inv_inventario.id_empresa', $id_empresa)
            ->where('inv_inventario.existencia', '>=', 1)
            ->paginate(10);

        $fecha_hoy = date('Y-m-d');

        $fecha_desde_ventas = date('Y-m-d', strtotime('-30 days'));
        $fecha_hasta_expira = date('Y-m-d', strtotime('+60 days'));


        $mes_actual  = (int) date('m');
        $anio_actual = (int) date('Y');
        $ventas_por_vendedor = $this->consultarVentasPorVendedor(
            $id_empresa,
            $mes_actual,
            $anio_actual,
            true // excluir Grupo Robles por defecto
        );


        $ventas_general = $this->consultarVentasGeneral(
            $id_empresa,
            $mes_actual,
            $anio_actual,
            true
        );

        $evolucion_ventas_general = $this->consultarEvolucionVentasGeneral(
            $id_empresa,
            $mes_actual,
            $anio_actual,
            true
        );

        // ==================== VENTAS POR PRODUCTO ====================

        $ventas_por_producto = $this->consultarVentasPorProducto(
            $id_empresa,
            $anio_actual,
            null
        );

        $productos_filtro_ventas = $this->consultarProductosFiltroVentas(
            $id_empresa,
            $anio_actual
        );

        // ========== PROMEDIO DE VENTAS AÑO PASADO POR PRODUCTO ==========
        // JOIN por producto.codigo porque ct_detalle_venta.id_ct_productos guarda el CÓDIGO
        $anio_pasado = (int) date('Y') - 1;

        $promedio_ventas_producto = DB::table('ct_detalle_venta as dv')
            ->join('ct_ventas as cv', 'cv.id', '=', 'dv.id_ct_ventas')
            ->join('producto as p', 'p.codigo', '=', 'dv.id_ct_productos')
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.estado', '<>', 0)
            ->where('dv.estado', '<>', 0)
            ->whereYear('cv.fecha', $anio_pasado)
            ->select(
                'p.id as id_producto',
                DB::raw('SUM(dv.cantidad) / 12.0 as promedio_mensual')
            )
            ->groupBy('p.id')
            ->pluck('promedio_mensual', 'id_producto');
        // Resultado: Collection con [id_producto => promedio_mensual]

        $accesorios_base_raw = DB::table('inv_det_movimientos as dm')
            ->join('inv_inventario as ii', 'ii.id', '=', 'dm.id_inv_inventario')
            ->join('producto as p', 'p.id', '=', 'ii.id_producto')
            ->leftJoin('bodega as b', 'b.id', '=', 'ii.id_bodega')
            ->where('ii.id_empresa', $id_empresa)
            ->where('ii.existencia', '>', 0)
            ->whereNotNull('dm.fecha_vence')
            ->whereDate('dm.fecha_vence', '<=', $fecha_hasta_expira)
            ->select(
                'dm.id as id_det_movimiento',
                'p.id as id_producto',
                'p.codigo',
                'p.nombre as producto',
                DB::raw("COALESCE(b.nombre, 'Sin bodega') as bodega"),
                'ii.id_bodega',
                'ii.existencia',
                'ii.existencia_min',
                'dm.lote',
                'dm.serie',
                'dm.fecha_vence'
            )
            ->orderBy('dm.fecha_vence', 'asc')
            ->limit(1000)
            ->get();

        $hoy = Carbon::today();
        $accesorios_base = collect($accesorios_base_raw)
            ->map(function ($row) use ($hoy, $promedio_ventas_producto) {
                $row->dias_para_vencer = $hoy->diffInDays(Carbon::parse($row->fecha_vence), false);

                // Promedio mensual del año pasado para este producto (0 si no tuvo ventas)
                $promedio_mensual = (float) ($promedio_ventas_producto[$row->id_producto] ?? 0);
                $row->promedio_mensual = round($promedio_mensual, 2);

                // Proyección: cuántas unidades se venderán antes de que caduque
                $meses_hasta_vencer = max($row->dias_para_vencer / 30, 0);
                $venta_proyectada  = $promedio_mensual * $meses_hasta_vencer;
                $row->venta_proyectada = round($venta_proyectada, 2);

                // Clasificación de color
                if ($row->dias_para_vencer < 0) {
                    $row->alerta_color = 'caducado';
                    $row->alerta_texto = 'Producto caducado';
                } elseif ($promedio_mensual <= 0) {
                    // Sin histórico de ventas: producto inmóvil con fecha de vencimiento → crítico
                    $row->alerta_color = 'critico';
                    $row->alerta_texto = 'Sin ventas año anterior';
                } elseif ($row->existencia >= ($venta_proyectada * 2)) {
                    $row->alerta_color = 'critico';
                    $row->alerta_texto = 'Stock > 2x proyección de ventas';
                } elseif ($row->existencia >= $venta_proyectada) {
                    $row->alerta_color = 'advertencia';
                    $row->alerta_texto = 'Stock similar a proyección';
                } else {
                    $row->alerta_color = 'normal';
                    $row->alerta_texto = 'Se venderá a tiempo';
                }

                return $row;
            })
            ->sortBy('dias_para_vencer')
            ->groupBy(function ($row) {
                return mb_strtolower(trim(preg_replace('/\s+/', ' ', $row->producto)))
                    . '|' . ($row->id_bodega ?? '0');
            })
            ->map(function ($items) {
                return $items->sortBy('dias_para_vencer')->first();
            })
            ->sortBy('dias_para_vencer')
            ->values();

        $accesorios_caducados = $accesorios_base
            ->filter(function ($row) {
                return $row->dias_para_vencer < 0;
            })
            ->values();

        $accesorios_por_expirar = $accesorios_base
            ->filter(function ($row) {
                return $row->dias_para_vencer >= 0;
            })
            ->values()
            ->take(50);

        $accesorios_tabla = collect($accesorios_caducados)
            ->merge($accesorios_por_expirar)
            ->sortBy('dias_para_vencer')
            ->values()
            ->take(80);

        $bodegas_accesorios = collect();

        if (isset($accesorios_base)) {
            $bodegas_accesorios = $accesorios_base
                ->pluck('bodega')
                ->filter()
                ->unique()
                ->sort()
                ->values();
        } else {
            $bodegas_accesorios = collect($accesorios_por_expirar)
                ->merge(isset($accesorios_caducados) ? $accesorios_caducados : collect())
                ->pluck('bodega')
                ->filter()
                ->unique()
                ->sort()
                ->values();
        }

        $total_inventario_accesorios = max((float) $accesorios_base->sum('existencia'), 1);

        $criticos_count = (float) $accesorios_por_expirar->where('alerta_color', 'critico')->sum('existencia');
        $advertencia_count = (float) $accesorios_por_expirar->where('alerta_color', 'advertencia')->sum('existencia');
        $normal_count = (float) $accesorios_por_expirar->where('alerta_color', 'normal')->sum('existencia');
        $caducados_count = (float) $accesorios_caducados->sum('existencia');

        $kpi_accesorios = collect([
            [
                'key' => 'critico',
                'titulo' => 'Críticos',
                'cantidad' => $criticos_count,
                'porcentaje' => round(($criticos_count / $total_inventario_accesorios) * 100, 1),
                'color' => '#dc3545',
            ],
            [
                'key' => 'advertencia',
                'titulo' => 'Advertencia',
                'cantidad' => $advertencia_count,
                'porcentaje' => round(($advertencia_count / $total_inventario_accesorios) * 100, 1),
                'color' => '#f0ad4e',
            ],
            [
                'key' => 'normal',
                'titulo' => 'Normal',
                'cantidad' => $normal_count,
                'porcentaje' => round(($normal_count / $total_inventario_accesorios) * 100, 1),
                'color' => '#28a745',
            ],
            [
                'key' => 'caducado',
                'titulo' => 'Caducados',
                'cantidad' => $caducados_count,
                'porcentaje' => null,
                'color' => '#8b1e1e',
            ],
        ]);

        $kpi_bodegas_caducados = $accesorios_caducados
            ->groupBy('bodega')
            ->map(function ($items, $bodega) {
                return (object)[
                    'bodega' => $bodega,
                    'cantidad' => (float) $items->sum('existencia'),
                ];
            })
            ->sortByDesc('cantidad')
            ->values()
            ->take(6);

        $rangos_alerta = [];
        foreach (['critico', 'advertencia', 'normal'] as $estado) {
            $dias = $accesorios_por_expirar
                ->where('alerta_color', $estado)
                ->pluck('dias_para_vencer');

            if ($dias->count() > 0) {
                $min = (int) $dias->min();
                $max = (int) $dias->max();
                $rangos_alerta[$estado] = [
                    'count' => $dias->count(),
                    'min'   => $min,
                    'max'   => $max,
                    'label' => $min === $max
                        ? $min . ' días'
                        : $min . ' a ' . $max . ' días',
                ];
            } else {
                $rangos_alerta[$estado] = [
                    'count' => 0,
                    'min'   => null,
                    'max'   => null,
                    'label' => 'sin items',
                ];
            }
        }

        $stock_minimos_venta = DB::table('inv_inventario as ii')
            ->join('producto as p', 'p.id', '=', 'ii.id_producto')
            ->leftJoin('bodega as b', 'b.id', '=', 'ii.id_bodega')
            ->where('ii.id_empresa', $id_empresa)
            ->where('ii.estado', '1')
            ->where('p.estado', 1)
            ->where('ii.existencia_min', '>', 0)
            ->select(
                'p.id as id_producto',
                'p.codigo',
                'p.nombre as producto',
                DB::raw("COALESCE(b.nombre, 'Sin bodega') as bodega"),
                'ii.id_bodega',
                'ii.existencia_min as stock_minimo',
                'ii.existencia as cantidad_actual',
                DB::raw("
                    CASE
                        WHEN ii.existencia <= 0
                            OR ii.existencia < (ii.existencia_min * 0.50)
                            THEN 'CRITICO'
                        WHEN ii.existencia < ii.existencia_min
                            THEN 'ADVERTENCIA'
                        ELSE 'NORMAL'
                    END AS estado_alerta
                "),
                DB::raw("
                    CASE
                        WHEN ii.existencia <= 0
                            OR ii.existencia < (ii.existencia_min * 0.50)
                            THEN 'critico'
                        WHEN ii.existencia < ii.existencia_min
                            THEN 'advertencia'
                        ELSE 'normal'
                    END AS estado_clase
                "),
                DB::raw("
                    CASE
                        WHEN ii.existencia <= 0
                            OR ii.existencia < (ii.existencia_min * 0.50)
                            THEN 1
                        WHEN ii.existencia < ii.existencia_min
                            THEN 2
                        ELSE 3
                    END AS prioridad_alerta
                ")
            )
            ->orderBy('prioridad_alerta', 'asc')
            ->orderBy('ii.existencia', 'asc')
            ->orderBy('p.nombre', 'asc')
            ->limit(80)
            ->get();

        $bodegas_stock_minimos = collect($stock_minimos_venta)
            ->pluck('bodega')
            ->map(function ($bodega) {
                return trim((string) $bodega);
            })
            ->filter(function ($bodega) {
                return $bodega !== '';
            })
            ->unique()
            ->sort()
            ->values();

        //dd($fecha_cadu);

        //redireccionar para el paciente
        /*if ($rolUsuario == 2) {
        return redirect()->route('agenda.index');
        }*/

        $menuUsuarios = MenuUsuario::where('id_usuario', $idusuario)->where('id_empresa', $id_empresa)->where('estado', 1)->get();
        $presentacion = PresentacionTitulos::where('estado', '1')->get();

        $id_empresa = $request->session()->get('id_empresa');
        //DASHBOARD FAUSTO JORGE KLEBER
     
        $mostrar_dashboard_gastritech = session('id_empresa') === '0993069299001';
        $mostrar_dashboard_gastroclinica = $this->puedeVerDashboardGastroclinica();
        $mostrar_dashboard_labs = ((Auth::user()->id_tipo_usuario == 1 || Auth::user()->id == '0923306963') && (in_array(session("id_empresa"), self::empresasPermitidas)));
        $tabsPermitidos = ['principal'];
        if ($mostrar_dashboard_gastroclinica) {
            $tabsPermitidos[] = 'gastroclinica';
        }

        if ($mostrar_dashboard_gastritech) {
            $tabsPermitidos[] = 'gastritech';
        }

        if ($mostrar_dashboard_labs) {
            $tabsPermitidos[] = 'labs';
        }
        $tabSolicitado = (string) $request->get('tab', 'principal');
        $tab_dashboard = in_array($tabSolicitado, $tabsPermitidos) ? $tabSolicitado : 'principal';
        $fechaHasta = Carbon::today()->format('Y-m-d');
        $fechaDesde = Carbon::today()->subYear()->format('Y-m-d');
        $seguros = collect();
        if ($mostrar_dashboard_gastroclinica) {
            $seguros = DB::table('seguros as s')
                ->join('ct_ventas as v', 'v.seguro_paciente', '=', 's.id')
                ->where('v.id_empresa', $id_empresa)
                ->where('v.estado', 1)
                ->whereIn('v.tipo', self::TIPOS_VENTA)
                ->select('s.id', 's.nombre', 's.tipo')
                ->distinct()
                ->orderBy('s.nombre')
                ->get();
        }

        $rucs_grupo_robles = [
            '2390058384001',
            '1793135579001',
            '1391927177001',
            '0992704152001',
            '1307189140001',
            '0993375469001',
        ];


        $p_venta = Proforma_Venta_Cab::query()
            ->leftJoin(
                'ct_ventas',
                'proforma_ventas_cab.id_venta',
                '=',
                'ct_ventas.id'
            )
            ->leftJoin(
                'users',
                'ct_ventas.ci_vendedor',
                '=',
                'users.id'
            )
            ->join(
                'ct_clientes',
                'proforma_ventas_cab.id_cliente',
                '=',
                'ct_clientes.identificacion'
            )
            ->whereNotIn("ct_ventas.id_cliente", $rucs_grupo_robles)
            ->whereBetween('proforma_ventas_cab.created_at', [
                date('Y-m-01') . ' 00:00:00',
                date('Y-m-t') . ' 23:59:59',
            ])
            ->select([
                DB::raw("
                    CONCAT_WS(
                        ' ',
                        users.nombre1,
                        users.apellido1
                    ) AS vendedor
                "),
                DB::raw('DATE(ct_ventas.fecha) as fecha_factura'),
                'proforma_ventas_cab.id',
                'proforma_ventas_cab.id_venta',
                'proforma_ventas_cab.estado',
                'proforma_ventas_cab.id_usuariocrea',
                'proforma_ventas_cab.created_at',
                'proforma_ventas_cab.fecha_exp',

                'ct_clientes.nombre AS cliente',
                'ct_ventas.total_final',

                DB::raw("
            CASE
                WHEN proforma_ventas_cab.id_venta IS NOT NULL
                    THEN 'Convertida'
                WHEN proforma_ventas_cab.estado = 1
                    THEN 'Aprobada sin facturar'
                WHEN proforma_ventas_cab.estado = 0
                    THEN 'Anulada'
                WHEN proforma_ventas_cab.estado = 2
                    THEN 'Pendiente'
                WHEN proforma_ventas_cab.estado = 3
                    THEN 'Caducada'
                ELSE 'Sin estado'
            END AS estado_conversion
        "),
            ])
            ->orderBy(
                'proforma_ventas_cab.created_at',
                'desc'
            )
            ->get();

        $totalCotizaciones = $p_venta->count();
        $totalAnuladas = $p_venta
            ->filter(function ($row) {
                return (int) $row->estado === 0;
            })
            ->count();
        $cotizacionesValidas = $p_venta
            ->filter(function ($row) {
                return (int) $row->estado !== 0;
            });

        $totalCotizacionesValidas = $cotizacionesValidas->count();
        $convertidasValidas = $cotizacionesValidas
            ->filter(function ($row) {
                return !empty($row->id_venta);
            })
            ->unique('id_venta');

        $totalConvertidas = $convertidasValidas->count();
        $totalNoConvertidas = $cotizacionesValidas
            ->filter(function ($row) {
                return empty($row->id_venta);
            })
            ->count();

        $totalAprobadasSinFacturar = $p_venta
            ->filter(function ($row) {
                return (int) $row->estado === 1
                    && empty($row->id_venta);
            })
            ->count();

        $totalPendientes = $p_venta
            ->filter(function ($row) {
                return (int) $row->estado === 2
                    && empty($row->id_venta);
            })
            ->count();

        $totalCaducadas = $p_venta
            ->filter(function ($row) {
                return (int) $row->estado === 3
                    && empty($row->id_venta);
            })
            ->count();
        $totalFacturado = $convertidasValidas->sum(function ($row) {
            return (float) $row->total_final;
        });

        $ticketPromedio = $totalConvertidas > 0
            ? round($totalFacturado / $totalConvertidas, 2)
            : 0;

        $tasaConversion = $totalCotizacionesValidas > 0
            ? round(
                ($totalConvertidas / $totalCotizacionesValidas) * 100,
                2
            )
            : 0;


        //END

        // kleber

        $fechaDesdeConversion = Carbon::today()->startOfYear()->format('Y-m-d');
        $fechaHastaConversion = Carbon::today()->format('Y-m-d');
        $conversion_cotizaciones_vendedor = $this->consultarConversionCotizacionesVendedor(
            $id_empresa,
            $fechaDesdeConversion,
            $fechaHastaConversion
        );
        $conversion_estados_proforma = $this->consultarEstadosConversionCotizaciones(
            $id_empresa,
            $fechaDesdeConversion,
            $fechaHastaConversion
        );



        //end


        return view('dashboard', [
            'p_venta' => $p_venta,
            'conversion_cotizaciones_vendedor' => $conversion_cotizaciones_vendedor,
            'conversion_estados_proforma' => $conversion_estados_proforma,
            'fecha_desde_conversion' => $fechaDesdeConversion,
            'fecha_hasta_conversion' => $fechaHastaConversion,
            'kpi_conversion' => [
                'total_cotizaciones'        => $totalCotizaciones,
                'cotizaciones_validas'      => $totalCotizacionesValidas,
                'convertidas'               => $totalConvertidas,
                'no_convertidas'            => $totalNoConvertidas,
                'aprobadas_sin_facturar'    => $totalAprobadasSinFacturar,
                'anuladas'                  => $totalAnuladas,
                'pendientes'                => $totalPendientes,
                'caducadas'                 => $totalCaducadas,
                'tasa_conversion'           => $tasaConversion,
                'total_facturado'           => $totalFacturado,
                'ticket_promedio'           => $ticketPromedio,
            ],
            'ch'         => $ch,
            'ca'         => $ca,
            'pm'              => $pm,
            'faltantes'        => $faltantes,
            'agregados' => $agregados,
            'stock' => $stock,
            'bodegas_stock_minimos' => $bodegas_stock_minimos,
            'fecha_hoy' => $fecha_hoy,
            'caducado' => $caducado,
            'menu_usuarios' => $menuUsuarios,
            'presentacion' => $presentacion,
            'evolucion_ventas_general' => $evolucion_ventas_general,
            'rangos_alerta' => $rangos_alerta,
            'kpi_accesorios' => $kpi_accesorios,
            'kpi_bodegas_caducados' => $kpi_bodegas_caducados,
            'total_inventario_accesorios' => $total_inventario_accesorios,
            'mes_actual'  => $mes_actual,
            'bodegas_accesorios' => $bodegas_accesorios,
            'anio_actual' => $anio_actual,
            'ventas_por_vendedor' => $ventas_por_vendedor,
            'ventas_general' => $ventas_general,
            'accesorios_por_expirar' => $accesorios_tabla,
            'stock_minimos_venta' => $stock_minimos_venta,
            'ventas_por_producto' => $ventas_por_producto,
            'productos_filtro_ventas' => $productos_filtro_ventas,
            'mostrar_dashboard_gastritech' => $mostrar_dashboard_gastritech,
            'mostrar_dashboard_gastroclinica' => $mostrar_dashboard_gastroclinica,
            'mostrar_dashboard_labs' => $mostrar_dashboard_labs,
            'tab_dashboard' => $tab_dashboard,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'seguros' => $seguros,
        ]);
    }

    private function consultarEstadosConversionCotizaciones($id_empresa, $fechaDesde, $fechaHasta, $idResponsable = '', $estado = '')
    {
        $conteos = [
            'anuladas' => 0,
            'aprobadas' => 0,
            'pendientes' => 0,
            'caducadas' => 0,
        ];

        $estados = [
            '0' => 'anuladas',
            '1' => 'aprobadas',
            '2' => 'pendientes',
            '3' => 'caducadas',
        ];

        $filas = $this->baseConversionCotizacionesQuery($id_empresa, $fechaDesde, $fechaHasta, $idResponsable, $estado)
            ->select(
                'pc.estado',
                DB::raw('COUNT(DISTINCT pc.id) as cantidad')
            )
            ->groupBy('pc.estado')
            ->get();

        foreach ($filas as $fila) {
            $key = isset($estados[(string) $fila->estado]) ? $estados[(string) $fila->estado] : null;
            if ($key !== null) {
                $conteos[$key] = (int) $fila->cantidad;
            }
        }

        return $conteos;
    }


      private function baseConversionCotizacionesQuery($id_empresa, $fechaDesde, $fechaHasta, $idResponsable = '', $estado = '')
    {
        $query = DB::table('proforma_ventas_cab as pc')
            ->leftJoin('ct_ventas as cv', 'pc.id_venta', '=', 'cv.id')
            ->leftJoin('ct_clientes as cc', 'pc.id_cliente', '=', 'cc.identificacion')
            ->leftJoin('users as u', 'cv.ci_vendedor', '=', 'u.id')
            ->leftJoin(
                DB::raw("(
                    SELECT
                        id_proforma,
                        SUM(IFNULL(total, 0)) as total_cotizado,
                        SUM(IFNULL(cantidad, 0)) as cantidad_cotizada
                    FROM proforma_venta_det
                    WHERE estado = 1
                    GROUP BY id_proforma
                ) as pd"),
                'pd.id_proforma',
                '=',
                'pc.id'
            )
            ->where('pc.id_empresa', $id_empresa)
            ->whereBetween('pc.created_at', [
                $fechaDesde . ' 00:00:00',
                $fechaHasta . ' 23:59:59',
            ]);

        if ($idResponsable !== '') {
            $query->where('cv.ci_vendedor', $idResponsable);
        }

        if ($estado !== '') {
            $query->where('pc.estado', $estado);
        }

        return $query;
    }

    private function consultarConversionCotizacionesVendedor($id_empresa, $fechaDesde, $fechaHasta, $idResponsable = '', $estado = '')
    {
        $vendedorSql = "
            COALESCE(
                NULLIF(TRIM(CONCAT_WS(' ', u.nombre1, u.apellido1)), ''),
                NULLIF(TRIM(cv.vendedor), ''),
                NULLIF(TRIM(cv.ci_vendedor), ''),
                'SIN VENDEDOR'
            )
        ";

        return $this->baseConversionCotizacionesQuery($id_empresa, $fechaDesde, $fechaHasta, $idResponsable, $estado)
            ->whereRaw("NULLIF(TRIM(cv.ci_vendedor), '') IS NOT NULL")
            ->where('u.id_tipo_usuario', 17)
            ->select(
                'cv.ci_vendedor as id_vendedor',
                DB::raw($vendedorSql . ' as vendedor'),
                DB::raw('COUNT(DISTINCT pc.id) as cant_cotizaciones'),
                DB::raw('COUNT(DISTINCT CASE WHEN pc.id_venta IS NOT NULL AND pc.id_venta <> "" THEN pc.id_venta END) as cant_facturas'),
                DB::raw('SUM(IFNULL(pd.total_cotizado, 0)) as total_cotizado'),
                DB::raw('SUM(CASE WHEN pc.id_venta IS NOT NULL AND pc.id_venta <> "" THEN IFNULL(cv.total_final, 0) ELSE 0 END) as total_facturado'),
                DB::raw('SUM(IFNULL(pd.cantidad_cotizada, 0)) as cantidad')
            )
            ->groupBy('cv.ci_vendedor', DB::raw($vendedorSql))
            ->orderBy('total_cotizado', 'desc')
            ->get()
            ->map(function ($row) {
                $cotizado = (float) $row->total_cotizado;
                $facturado = (float) $row->total_facturado;
                $row->conversion = $cotizado > 0 ? round(($facturado / $cotizado) * 100, 2) : 0;
                return $row;
            });
    }

    /**
     * Consulta ventas por vendedor con filtros de mes/año y exclusión del Grupo Robles.
     *
     * @param string $id_empresa            Empresa emisora (de sesión)
     * @param int    $mes                   Mes 1-12
     * @param int    $anio                  Año (ej: 2026)
     * @param bool   $excluir_grupo_robles  Si true, excluye facturas a Gastroclinica y CRM
     */
    private function consultarVentasPorVendedor(
        $id_empresa,
        $mes,
        $anio,
        $excluir_grupo_robles = true
    ) {
        $periodo = $this->obtenerPeriodoVentas($mes, $anio);

        $rucs_grupo_robles = [
            '2390058384001',
            '1793135579001',
            '1391927177001',
            '0992704152001',
            '1307189140001',
            '0993375469001',
        ];

        $vendedorSql = "
            COALESCE(
                NULLIF(TRIM(cv.vendedor), ''),
                NULLIF(TRIM(cv.ci_vendedor), ''),
                'SIN VENDEDOR'
            )
        ";

        $clienteSql = "
            COALESCE(
                NULLIF(TRIM(cc.nombre), ''),
                NULLIF(TRIM(cv.nombre_cliente), ''),
                NULLIF(TRIM(cv.id_cliente), ''),
                'SIN CLIENTE'
            )
        ";

        $query = DB::table('ct_ventas as cv')
            ->leftJoin(
                'ct_clientes as cc',
                'cc.identificacion',
                '=',
                'cv.id_cliente'
            )
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.estado', 1)
            ->where('cv.fecha', '>=', $periodo['inicio'])
            ->where('cv.fecha', '<', $periodo['fin'])
            ->select(
                DB::raw($vendedorSql . ' as vendedor_nombre'),
                DB::raw('COUNT(cv.id) as total_facturas'),
                DB::raw('COUNT(DISTINCT ' . $clienteSql . ') as total_empresas'),
                DB::raw('SUM(IFNULL(cv.total_final, 0)) as total_ventas')
            );

        if ($excluir_grupo_robles) {
            $query->where(function ($q) use ($rucs_grupo_robles) {
                foreach ($rucs_grupo_robles as $ruc) {
                    $q->where(function ($subQuery) use ($ruc) {
                        $subQuery
                            ->whereNull('cv.id_cliente')
                            ->orWhere(
                                'cv.id_cliente',
                                'NOT LIKE',
                                $ruc . '%'
                            );
                    });
                }
            });
        }

        return $query
            ->groupBy(DB::raw($vendedorSql))
            ->orderBy('total_ventas', 'desc')
            ->get();
    }

    private function consultarEvolucionVentasGeneral($id_empresa, $mes, $anio, $excluir_grupo_robles = true)
    {
        $rucs_grupo_robles = [
            '2390058384001',
            '1793135579001',
            '1391927177001',
            '0992704152001',
            '1307189140001',
            '0993375469001'
        ];

        $anios = [
            (int)$anio - 2,
            (int)$anio - 1,
            (int)$anio
        ];

        $queryTotales = DB::table('ct_ventas')
            ->selectRaw("
                YEAR(ct_ventas.fecha) as anio,
                COUNT(*) as total_facturas,
                SUM(IFNULL(ct_ventas.total_final, 0)) as total_ventas
            ")
            ->where('ct_ventas.id_empresa', $id_empresa)
            ->where('ct_ventas.estado', '<>', 0)
            ->whereMonth('ct_ventas.fecha', $mes)
            ->whereIn(DB::raw('YEAR(ct_ventas.fecha)'), $anios);

        if ($excluir_grupo_robles) {
            $queryTotales->where(function ($q) use ($rucs_grupo_robles) {
                foreach ($rucs_grupo_robles as $ruc) {
                    $q->where('ct_ventas.id_cliente', 'NOT LIKE', $ruc . '%');
                }
            });
        }

        $totales = $queryTotales
            ->groupBy(DB::raw('YEAR(ct_ventas.fecha)'))
            ->get()
            ->keyBy('anio');

        $clienteNombre = "COALESCE(NULLIF(ct_ventas.nombre_cliente, ''), ct_clientes.nombre, 'Sin cliente')";

        $queryClientes = DB::table('ct_ventas')
            ->leftJoin('ct_clientes', 'ct_clientes.identificacion', '=', 'ct_ventas.id_cliente')
            ->selectRaw("
                YEAR(ct_ventas.fecha) as anio,
                ct_ventas.id_cliente,
                ct_ventas.ruc_id_cliente,
                {$clienteNombre} as cliente,
                COUNT(*) as total_facturas_cliente,
                SUM(IFNULL(ct_ventas.total_final, 0)) as total_ventas_cliente
            ")
            ->where('ct_ventas.id_empresa', $id_empresa)
            ->where('ct_ventas.estado', '<>', 0)
            ->whereMonth('ct_ventas.fecha', $mes)
            ->whereIn(DB::raw('YEAR(ct_ventas.fecha)'), $anios);

        if ($excluir_grupo_robles) {
            $queryClientes->where(function ($q) use ($rucs_grupo_robles) {
                foreach ($rucs_grupo_robles as $ruc) {
                    $q->where('ct_ventas.id_cliente', 'NOT LIKE', $ruc . '%');
                }
            });
        }

        $clientesPorAnio = $queryClientes
            ->groupBy(
                DB::raw('YEAR(ct_ventas.fecha)'),
                'ct_ventas.id_cliente',
                'ct_ventas.ruc_id_cliente',
                DB::raw($clienteNombre)
            )
            ->orderBy(DB::raw('YEAR(ct_ventas.fecha)'), 'asc')
            ->orderBy('total_ventas_cliente', 'desc')
            ->get()
            ->groupBy('anio')
            ->map(function ($items) {
                return $items->first();
            });

        $resultado = collect();

        foreach ($anios as $y) {
            $total = isset($totales[$y]) ? $totales[$y] : null;
            $clienteTop = isset($clientesPorAnio[$y]) ? $clientesPorAnio[$y] : null;

            $resultado->push((object)[
                'anio' => $y,
                'total_facturas' => $total ? (int)$total->total_facturas : 0,
                'total_ventas' => $total ? (float)$total->total_ventas : 0,

                'cliente_top' => $clienteTop ? $clienteTop->cliente : null,
                'cliente_top_ruc' => $clienteTop ? ($clienteTop->ruc_id_cliente ?: $clienteTop->id_cliente) : null,
                'cliente_top_facturas' => $clienteTop ? (int)$clienteTop->total_facturas_cliente : 0,
                'cliente_top_ventas' => $clienteTop ? (float)$clienteTop->total_ventas_cliente : 0,
            ]);
        }

        return $resultado;
    }

    private function consultarConversionCotizacionesEmpresa($id_empresa, $fechaDesde, $fechaHasta, $idVendedor, $estado = '')
    {
        $clienteIdSql = "
            COALESCE(
                NULLIF(TRIM(cc.identificacion), ''),
                NULLIF(TRIM(pc.id_cliente), ''),
                'SIN IDENTIFICACION'
            )
        ";

        $empresaSql = "
            COALESCE(
                NULLIF(TRIM(cc.nombre), ''),
                NULLIF(TRIM(pc.id_cliente), ''),
                'SIN EMPRESA'
            )
        ";

        return $this->baseConversionCotizacionesQuery($id_empresa, $fechaDesde, $fechaHasta, $idVendedor, $estado)
            ->select(
                DB::raw($clienteIdSql . ' as cliente_id'),
                DB::raw($empresaSql . ' as empresa'),
                DB::raw('COUNT(DISTINCT pc.id) as cant_cotizaciones'),
                DB::raw('COUNT(DISTINCT CASE WHEN pc.id_venta IS NOT NULL AND pc.id_venta <> "" THEN pc.id_venta END) as cant_facturas'),
                DB::raw('SUM(IFNULL(pd.total_cotizado, 0)) as total_cotizado'),
                DB::raw('SUM(CASE WHEN pc.id_venta IS NOT NULL AND pc.id_venta <> "" THEN IFNULL(cv.total_final, 0) ELSE 0 END) as total_facturado')
            )
            ->groupBy(DB::raw($clienteIdSql), DB::raw($empresaSql))
            ->orderBy('total_cotizado', 'desc')
            ->get()
            ->map(function ($row) {
                $cotizado = (float) $row->total_cotizado;
                $facturado = (float) $row->total_facturado;
                $row->conversion = $cotizado > 0 ? round(($facturado / $cotizado) * 100, 2) : 0;
                return $row;
            });
    }

    public function buscarVendedoresConversion(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $search = trim((string) $request->input('search', ''));

        $vendedorSql = "
            COALESCE(
                NULLIF(TRIM(CONCAT_WS(' ', u.nombre1, u.apellido1)), ''),
                NULLIF(TRIM(cv.vendedor), ''),
                NULLIF(TRIM(cv.ci_vendedor), '')
            )
        ";

        $query = DB::table('ct_ventas as cv')
            ->leftJoin('users as u', 'cv.ci_vendedor', '=', 'u.id')
            ->whereRaw("NULLIF(TRIM(cv.ci_vendedor), '') IS NOT NULL")
            ->where('u.id_tipo_usuario', 17)
            ->where('cv.estado', '<>', '0');

        if (!empty($id_empresa)) {
            $query->where('cv.id_empresa', $id_empresa);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search, $vendedorSql) {
                $q->where('cv.ci_vendedor', 'LIKE', '%' . $search . '%')
                    ->orWhereRaw($vendedorSql . ' LIKE ?', ['%' . $search . '%']);
            });
        }

        $vendedores = $query
            ->select(
                'cv.ci_vendedor as id',
                DB::raw("CONCAT(cv.ci_vendedor, ' - ', " . $vendedorSql . ") as text")
            )
            ->groupBy('cv.ci_vendedor', DB::raw($vendedorSql))
            ->orderBy(DB::raw($vendedorSql))
            ->take(20)
            ->get();

        return response()->json($vendedores);
    }

    public function filtrarConversionCotizacionesVendedor(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fechaDesde = $request->input('f_desde', date('Y-01-01'));
        $fechaHasta = $request->input('f_hasta', date('Y-m-d'));
        $idResponsable = trim((string) $request->input('id_responsable', ''));
        $estado = trim((string) $request->input('estado', ''));

        if (empty($id_empresa) || $fechaDesde > $fechaHasta) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Filtros invalidos para consultar conversion de cotizaciones.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'vendedores' => $this->consultarConversionCotizacionesVendedor(
                $id_empresa,
                $fechaDesde,
                $fechaHasta,
                $idResponsable,
                $estado
            ),
            'estados' => $this->consultarEstadosConversionCotizaciones(
                $id_empresa,
                $fechaDesde,
                $fechaHasta,
                $idResponsable,
                $estado
            ),
        ]);
    }

    public function detalleConversionCotizacionesVendedor(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $fechaDesde = $request->input('f_desde', date('Y-01-01'));
        $fechaHasta = $request->input('f_hasta', date('Y-m-d'));
        $idVendedor = trim((string) $request->input('id_vendedor', ''));
        $estado = trim((string) $request->input('estado', ''));

        if (empty($id_empresa) || $idVendedor === '' || $fechaDesde > $fechaHasta) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Faltan datos para consultar empresas por vendedor.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'empresas' => $this->consultarConversionCotizacionesEmpresa(
                $id_empresa,
                $fechaDesde,
                $fechaHasta,
                $idVendedor,
                $estado
            ),
        ]);
    }
    public function filtrarVentasVendedor(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $mes = (int) $request->input('mes', 0);
        $anio = (int) $request->input('anio', date('Y'));

        $excluir = (int) $request->input(
            'excluir_grupo_robles',
            1
        ) === 1;

        $anioActual = (int) date('Y');
        $anioMinimo = $anioActual - 5;

        if ($mes < 0 || $mes > 12) {
            $mes = 0;
        }

        if ($anio < $anioMinimo || $anio > $anioActual) {
            $anio = $anioActual;
        }

        if (empty($id_empresa)) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No existe una empresa seleccionada.',
            ], 422);
        }

        if ((string) $id_empresa !== '0993069299001') {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Este filtro pertenece al dashboard Gastritech.',
            ], 403);
        }

        try {
            $ventas = $this->consultarVentasPorVendedor(
                $id_empresa,
                $mes,
                $anio,
                $excluir
            );

            return response()->json([
                'ok' => true,
                'ventas' => $ventas,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al filtrar ventas por vendedor', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'id_empresa' => $id_empresa,
                'mes' => $mes,
                'anio' => $anio,
            ]);

            return response()->json([
                'ok' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    private function obtenerPeriodoVentas($mes, $anio)
    {
        $mes = (int) $mes;
        $anio = (int) $anio;

        if ($mes >= 1 && $mes <= 12) {
            $fechaInicio = sprintf(
                '%04d-%02d-01 00:00:00',
                $anio,
                $mes
            );

            $fechaFin = date(
                'Y-m-d H:i:s',
                strtotime($fechaInicio . ' +1 month')
            );
        } else {
            // Mes 0 significa todo el año
            $fechaInicio = $anio . '-01-01 00:00:00';
            $fechaFin = ($anio + 1) . '-01-01 00:00:00';
        }

        return [
            'inicio' => $fechaInicio,
            'fin' => $fechaFin,
        ];
    }

    public function exportarVentasVendedor(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $mes = (int) $request->input('mes', 0);
        $anio = (int) $request->input('anio', date('Y'));

        $excluir = (int) $request->input(
            'excluir_grupo_robles',
            1
        ) === 1;

        if (empty($id_empresa)) {
            return redirect()->back()->with(
                'error',
                'No existe una empresa seleccionada.'
            );
        }

        $ventas = $this->consultarVentasPorVendedor(
            $id_empresa,
            $mes,
            $anio,
            $excluir
        );

        $meses = [
            0 => 'Todos',
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $nombreMes = isset($meses[$mes])
            ? $meses[$mes]
            : 'Todos';

        $datos = [];

        foreach ($ventas as $row) {
            $datos[] = [
                'Vendedor' => $row->vendedor_nombre,
                'Mes' => $nombreMes,
                'Año' => $anio,
                'Total' => (float) $row->total_ventas,
            ];
        }

        $nombreArchivo = 'ventas_por_vendedor_' .
            strtolower($nombreMes) .
            '_' .
            $anio;

        return Excel::create(
            $nombreArchivo,
            function ($excel) use ($datos) {
                $excel->sheet(
                    'Ventas por vendedor',
                    function ($sheet) use ($datos) {
                        $sheet->fromArray(
                            $datos,
                            null,
                            'A1',
                            true
                        );

                        $sheet->row(1, function ($row) {
                            $row->setFontWeight('bold');
                        });

                        $sheet->setAutoSize(true);

                        /*
                        * Columna D corresponde a Total.
                        */
                        $sheet->setColumnFormat([
                            'D' => '$#,##0.00',
                        ]);
                    }
                );
            }
        )->export('xlsx');
    }

    public function detalleVentasVendedor(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $vendedor = trim(
            (string) $request->input('vendedor', '')
        );

        $mes = (int) $request->input('mes', 0);
        $anio = (int) $request->input('anio', date('Y'));

        $excluir = (int) $request->input(
            'excluir_grupo_robles',
            1
        ) === 1;

        if (empty($id_empresa) || $vendedor === '') {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Faltan datos para consultar el vendedor.',
            ], 422);
        }

        $periodo = $this->obtenerPeriodoVentas($mes, $anio);

        $rucsGrupoRobles = [
            '2390058384001',
            '1793135579001',
            '1391927177001',
            '0992704152001',
            '1307189140001',
            '0993375469001',
        ];

        $vendedorSql = "
            COALESCE(
                NULLIF(TRIM(cv.vendedor), ''),
                NULLIF(TRIM(cv.ci_vendedor), ''),
                'SIN VENDEDOR'
            )
        ";

        $clienteSql = "
            COALESCE(
                NULLIF(TRIM(cc.nombre), ''),
                NULLIF(TRIM(cv.nombre_cliente), ''),
                NULLIF(TRIM(cv.id_cliente), ''),
                'SIN CLIENTE'
            )
        ";

        try {
            $query = DB::table('ct_ventas as cv')
                ->leftJoin(
                    'ct_clientes as cc',
                    'cc.identificacion',
                    '=',
                    'cv.id_cliente'
                )
                ->where('cv.id_empresa', $id_empresa)
                ->where('cv.estado', 1)
                ->where('cv.fecha', '>=', $periodo['inicio'])
                ->where('cv.fecha', '<', $periodo['fin'])
                ->whereRaw($vendedorSql . ' = ?', [$vendedor])
                ->select(
                    DB::raw($clienteSql . ' as empresa'),
                    DB::raw(
                        'SUM(IFNULL(cv.total_final, 0)) as total_ventas'
                    ),
                    DB::raw('COUNT(cv.id) as total_facturas')
                );

            if ($excluir) {
                $query->where(function ($q) use ($rucsGrupoRobles) {
                    foreach ($rucsGrupoRobles as $ruc) {
                        $q->where(function ($subQuery) use ($ruc) {
                            $subQuery
                                ->whereNull('cv.id_cliente')
                                ->orWhere(
                                    'cv.id_cliente',
                                    'NOT LIKE',
                                    $ruc . '%'
                                );
                        });
                    }
                });
            }

            $clientes = $query
                ->groupBy(DB::raw($clienteSql))
                ->orderBy('total_ventas', 'desc')
                ->get();

            return response()->json([
                'ok' => true,
                'vendedor' => $vendedor,
                'clientes' => $clientes,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error detalle ventas vendedor', [
                'mensaje' => $e->getMessage(),
                'vendedor' => $vendedor,
                'mes' => $mes,
                'anio' => $anio,
            ]);

            return response()->json([
                'ok' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    private function puedeVerDashboardGastroclinica()
    {
        if (!Auth::check()) {
            return false;
        }

        return (int) (Auth::user()->id_tipo_usuario === self::TIPO_USUARIO_SISTEMA
            || (string) Auth::user()->id === self::USUARIO_DASHBOARD_GASTROCLINICA) && (in_array(session("id_empresa"), self::empresasPermitidas));
    }

    private function autorizarDashboardGastroclinica()
    {
        if (!$this->puedeVerDashboardGastroclinica()) {
            abort(403, 'No tiene permisos para acceder al Dashboard Gastroclínica.');
        }
    }

    private function validarFiltros(Request $request, $conSeguro = false)
    {
        $reglas = [
            'fecha_desde' => 'required|date_format:Y-m-d',
            'fecha_hasta' => 'required|date_format:Y-m-d',
        ];

        if ($conSeguro) {
            $reglas['id_seguro'] = 'nullable|integer|exists:seguros,id';
        }

        $validator = Validator::make($request->all(), $reglas);
        $validator->after(function ($validator) use ($request) {
            if (!$request->fecha_desde || !$request->fecha_hasta) {
                return;
            }

            try {
                if (Carbon::createFromFormat('Y-m-d', $request->fecha_desde)
                    ->gt(Carbon::createFromFormat('Y-m-d', $request->fecha_hasta))
                ) {
                    $validator->errors()->add(
                        'fecha_desde',
                        'La fecha desde no puede ser posterior a la fecha hasta.'
                    );
                }
            } catch (\Exception $e) {
                // Las reglas date_format agregan el mensaje correspondiente.
            }
        });

        return $validator;
    }

    private function expresionServicio()
    {
        return "CASE
            WHEN a.proc_consul = 0 THEN 'consultas'
            WHEN a.proc_consul = 1
                AND (gp.tipo_procedimiento = 2 OR UPPER(COALESCE(gp.nombre, '')) = 'IMAGENES')
                THEN 'imagenes'
            ELSE 'procedimientos'
        END";
    }

    private function expresionDetalleServicio()
    {
        return "CASE
            WHEN a.proc_consul = 0 THEN 'Consulta médica'
            ELSE COALESCE(NULLIF(p.nombre, ''), NULLIF(p.observacion, ''), 'Sin procedimiento registrado')
        END";
    }

    private function expresionCobertura()
    {
        return "CASE
            WHEN UPPER(COALESCE(s.nombre, '')) IN ('IESS', 'ISSPOL', 'ISSFA') THEN 'publico'
            WHEN s.tipo = 1 THEN 'privado'
            WHEN s.tipo = 2 OR s.id IS NULL THEN 'particular'
            ELSE NULL
        END";
    }

    private function consultaBase($idEmpresa, $fechaDesde, $fechaHasta, $idSeguro = null)
    {
        $query = DB::table('ct_ventas as v')
            ->join('ct_orden_venta as ov', 'ov.id', '=', 'v.orden_venta')
            ->join('agenda as a', 'a.id', '=', 'ov.id_agenda')
            ->leftJoin('procedimiento as p', 'p.id', '=', 'a.id_procedimiento')
            ->leftJoin('grupo_procedimiento as gp', 'gp.id', '=', 'p.id_grupo_procedimiento')
            ->leftJoin('seguros as s', 's.id', '=', 'v.seguro_paciente')
            ->where('v.id_empresa', $idEmpresa)
            ->where('ov.id_empresa', $idEmpresa)
            ->where('v.estado', 1)
            ->where('ov.estado', 1)
            ->whereIn('v.tipo', self::TIPOS_VENTA)
            ->whereIn('a.proc_consul', [0, 1])
            ->whereBetween('a.fechaini', [
                $fechaDesde . ' 00:00:00',
                $fechaHasta . ' 23:59:59',
            ]);

        if ($idSeguro !== null && $idSeguro !== '') {
            $query->where('v.seguro_paciente', $idSeguro);
        }

        return $query;
    }

    private function datosRotacion($idEmpresa, $fechaDesde, $fechaHasta, $idSeguro = null)
    {
        $servicioSql = $this->expresionServicio();
        $detalleSql = $this->expresionDetalleServicio();

        $filas = $this->consultaBase($idEmpresa, $fechaDesde, $fechaHasta, $idSeguro)
            ->selectRaw($servicioSql . ' as servicio')
            ->selectRaw($detalleSql . ' as detalle')
            ->selectRaw('COUNT(DISTINCT v.id) as facturas')
            ->selectRaw('SUM(COALESCE(v.total_final, 0)) as valor_total')
            ->groupBy(DB::raw($servicioSql))
            ->groupBy(DB::raw($detalleSql))
            ->orderBy(DB::raw($servicioSql))
            ->orderBy('facturas', 'desc')
            ->get();

        $detalles = [];
        $resumen = [];
        foreach (self::SERVICIOS as $clave => $nombre) {
            $detalles[$clave] = [];
            $resumen[$clave] = [
                'clave' => $clave,
                'servicio' => $nombre,
                'facturas' => 0,
                'valor_total' => 0.0,
                'nivel_demanda' => 'neutral',
            ];
        }

        foreach ($filas as $fila) {
            if (!isset($detalles[$fila->servicio])) {
                continue;
            }

            $detalle = [
                'servicio' => $fila->detalle,
                'facturas' => (int) $fila->facturas,
                'valor_total' => round((float) $fila->valor_total, 2),
            ];

            $detalles[$fila->servicio][] = $detalle;
            $resumen[$fila->servicio]['facturas'] += $detalle['facturas'];
            $resumen[$fila->servicio]['valor_total'] = round(
                $resumen[$fila->servicio]['valor_total'] + $detalle['valor_total'],
                2
            );
        }

        $cantidades = array_column(array_values($resumen), 'facturas');
        $maximo = count($cantidades) ? max($cantidades) : 0;
        $minimo = count($cantidades) ? min($cantidades) : 0;

        foreach ($resumen as $clave => $fila) {
            if ($maximo !== $minimo && $fila['facturas'] === $maximo) {
                $resumen[$clave]['nivel_demanda'] = 'alta';
            } elseif ($maximo !== $minimo && $fila['facturas'] === $minimo) {
                $resumen[$clave]['nivel_demanda'] = 'baja';
            }
        }

        return [
            'resumen' => array_values($resumen),
            'detalles' => $detalles,
            'periodo' => [
                'desde' => $fechaDesde,
                'hasta' => $fechaHasta,
            ],
        ];
    }

    private function excelRotacion(array $datos, $fechaDesde, $fechaHasta)
    {
        return $this->libroRotacion($datos, $fechaDesde, $fechaHasta)->export('xlsx');
    }

    private function libroRotacion(array $datos, $fechaDesde, $fechaHasta)
    {
        $nombre = 'Rotacion_servicios_' . $fechaDesde . '_a_' . $fechaHasta;

        return Excel::create($nombre, function ($excel) use ($datos) {
            $excel->sheet('Resumen', function ($sheet) use ($datos) {
                $filas = [['Servicio', 'Facturas', 'Valor total']];
                foreach ($datos['resumen'] as $fila) {
                    $filas[] = [
                        $fila['servicio'],
                        (string) $fila['facturas'],
                        (string) $fila['valor_total'],
                    ];
                }
                $sheet->fromArray($filas, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#D9EAF7');
                });
                $sheet->setColumnFormat(['C' => '$#,##0.00']);
                $sheet->setAutoSize(true);
            });

            $excel->sheet('Detalle', function ($sheet) use ($datos) {
                $filas = [['Tipo de servicio', 'Servicio', 'Facturas', 'Valor total']];
                foreach (self::SERVICIOS as $clave => $nombreServicio) {
                    foreach ($datos['detalles'][$clave] as $fila) {
                        $filas[] = [
                            $nombreServicio,
                            $fila['servicio'],
                            (string) $fila['facturas'],
                            (string) $fila['valor_total'],
                        ];
                    }
                }
                $sheet->fromArray($filas, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#D9EAF7');
                });
                $sheet->setColumnFormat(['D' => '$#,##0.00']);
                $sheet->setAutoSize(true);
            });
        });
    }

    private function datosVentas($idEmpresa, $fechaDesde, $fechaHasta)
    {
        $servicioSql = $this->expresionServicio();
        $coberturaSql = $this->expresionCobertura();

        $filas = $this->consultaBase($idEmpresa, $fechaDesde, $fechaHasta)
            ->selectRaw($coberturaSql . ' as cobertura')
            ->selectRaw($servicioSql . ' as servicio')
            ->selectRaw("COALESCE(NULLIF(s.nombre, ''), 'Particular') as seguro")
            ->selectRaw('SUM(COALESCE(v.total_final, 0)) as valor_total')
            ->whereRaw('(' . $coberturaSql . ') IS NOT NULL')
            ->groupBy(DB::raw($coberturaSql))
            ->groupBy(DB::raw($servicioSql))
            ->groupBy('s.id', 's.nombre')
            ->get();

        $resumen = [];
        foreach (self::COBERTURAS as $clave => $nombre) {
            $resumen[$clave] = [
                'clave' => $clave,
                'cobertura' => $nombre,
                'procedimientos' => 0.0,
                'imagenes' => 0.0,
                'consultas' => 0.0,
            ];
        }

        $detalleMap = ['privado' => [], 'publico' => []];

        foreach ($filas as $fila) {
            if (!isset($resumen[$fila->cobertura]) || !isset(self::SERVICIOS[$fila->servicio])) {
                continue;
            }

            $valor = round((float) $fila->valor_total, 2);
            $resumen[$fila->cobertura][$fila->servicio] = round(
                $resumen[$fila->cobertura][$fila->servicio] + $valor,
                2
            );

            if (isset($detalleMap[$fila->cobertura])) {
                if (!isset($detalleMap[$fila->cobertura][$fila->seguro])) {
                    $detalleMap[$fila->cobertura][$fila->seguro] = [
                        'seguro' => $fila->seguro,
                        'procedimientos' => 0.0,
                        'imagenes' => 0.0,
                        'consultas' => 0.0,
                    ];
                }

                $detalleMap[$fila->cobertura][$fila->seguro][$fila->servicio] = round(
                    $detalleMap[$fila->cobertura][$fila->seguro][$fila->servicio] + $valor,
                    2
                );
            }
        }

        $detalles = [];
        foreach ($detalleMap as $cobertura => $seguros) {
            uasort($seguros, function ($a, $b) {
                $totalA = $a['procedimientos'] + $a['imagenes'] + $a['consultas'];
                $totalB = $b['procedimientos'] + $b['imagenes'] + $b['consultas'];
                return $totalA === $totalB ? 0 : ($totalA < $totalB ? 1 : -1);
            });

            $detalles[$cobertura] = array_values($seguros);
        }

        return [
            'resumen' => array_values($resumen),
            'detalles' => $detalles,
            'periodo' => [
                'desde' => $fechaDesde,
                'hasta' => $fechaHasta,
            ],
        ];
    }

    private function libroVentas(array $datos, $fechaDesde, $fechaHasta)
    {
        $nombre = 'Ventas_por_cobertura_' . $fechaDesde . '_a_' . $fechaHasta;

        return Excel::create($nombre, function ($excel) use ($datos) {
            $excel->sheet('Resumen', function ($sheet) use ($datos) {
                $filas = [['Cobertura', 'Procedimientos', 'Imágenes', 'Consultas']];
                foreach ($datos['resumen'] as $fila) {
                    $filas[] = [
                        $fila['cobertura'],
                        (string) $fila['procedimientos'],
                        (string) $fila['imagenes'],
                        (string) $fila['consultas'],
                    ];
                }
                $sheet->fromArray($filas, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#D9EAF7');
                });
                $sheet->setColumnFormat([
                    'B' => '$#,##0.00',
                    'C' => '$#,##0.00',
                    'D' => '$#,##0.00',
                ]);
                $sheet->setAutoSize(true);
            });

            $excel->sheet('Aseguradoras', function ($sheet) use ($datos) {
                $filas = [['Cobertura', 'Aseguradora', 'Procedimientos', 'Imágenes', 'Consultas']];
                foreach (['privado', 'publico'] as $cobertura) {
                    foreach ($datos['detalles'][$cobertura] as $fila) {
                        $filas[] = [
                            self::COBERTURAS[$cobertura],
                            $fila['seguro'],
                            (string) $fila['procedimientos'],
                            (string) $fila['imagenes'],
                            (string) $fila['consultas'],
                        ];
                    }
                }
                $sheet->fromArray($filas, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                    $row->setBackground('#D9EAF7');
                });
                $sheet->setColumnFormat([
                    'C' => '$#,##0.00',
                    'D' => '$#,##0.00',
                    'E' => '$#,##0.00',
                ]);
                $sheet->setAutoSize(true);
            });
        });
    }

    private function excelVentas(array $datos, $fechaDesde, $fechaHasta)
    {
        return $this->libroVentas($datos, $fechaDesde, $fechaHasta)->export('xlsx');
    }

    private function idEmpresa(Request $request)
    {
        $idEmpresa = $request->session()->get('id_empresa');

        if (!$idEmpresa) {
            $empresa = Empresa::where('prioridad', 1)->first();
            $idEmpresa = $empresa ? $empresa->id : null;
        }

        if (!$idEmpresa) {
            abort(404, 'No existe una empresa activa para generar el dashboard.');
        }

        return $idEmpresa;
    }

    public function exportar(Request $request, $analisis)
    {
        $this->autorizarDashboardGastroclinica();

        if (!in_array($analisis, ['rotacion', 'ventas'])) {
            abort(404);
        }

        $validator = $this->validarFiltros($request, $analisis === 'rotacion');
        if ($validator->fails()) {
            return redirect()
                ->route('dashboard.gastroclinica.index')
                ->withErrors($validator)
                ->withInput();
        }

        $idEmpresa = $this->idEmpresa($request);
        $fechaDesde = $request->fecha_desde;
        $fechaHasta = $request->fecha_hasta;

        if ($analisis === 'rotacion') {
            $datos = $this->datosRotacion(
                $idEmpresa,
                $fechaDesde,
                $fechaHasta,
                $request->id_seguro
            );

            return $this->excelRotacion($datos, $fechaDesde, $fechaHasta);
        }

        $datos = $this->datosVentas($idEmpresa, $fechaDesde, $fechaHasta);
        return $this->excelVentas($datos, $fechaDesde, $fechaHasta);
    }

    public function ventas(Request $request)
    {
        $this->autorizarDashboardGastroclinica();

        $validator = $this->validarFiltros($request);
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Revisa los filtros ingresados.',
                'errores' => $validator->errors(),
            ], 422);
        }

        return response()->json($this->datosVentas(
            $this->idEmpresa($request),
            $request->fecha_desde,
            $request->fecha_hasta
        ));
    }

    public function rotacion(Request $request)
    {
        $this->autorizarDashboardGastroclinica();

        $validator = $this->validarFiltros($request, true);
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Revisa los filtros ingresados.',
                'errores' => $validator->errors(),
            ], 422);
        }

        return response()->json($this->datosRotacion(
            $this->idEmpresa($request),
            $request->fecha_desde,
            $request->fecha_hasta,
            $request->id_seguro
        ));
    }

    public function modal_revisar($id)
    {
        $fecha = InvInventarioSerie::where('id', $id)->first();

        //dd($fecha);
        return view('modal_revisar', ['fecha' => $fecha]);
    }
    public function consulta(Request $request)
    {

        // dd($request->all());
        $msj = '';
        try {
            $variable1 = InvDetMovimientos::where('serie', $request['serie'])->update([
                'fecha_vence' => $request['fecha'],
            ]);
            $variable2 = InvInventarioSerie::where('serie', $request['serie'])->update([
                'fecha_vence' => $request['fecha'],
            ]);

            $variable3 = Movimiento::where('serie', $request['serie'])->update([
                'fecha_vencimiento' => $request['fecha'],
            ]);
            $variablem = Movimiento::where('id_producto', $request['id_producto'])->update([
                'fecha_vencimiento' => $request['fecha'],
            ]);
            $variablem1 = InvInventarioSerie::where('id_producto', $request['id_producto'])->update([
                'fecha_vence' => $request['fecha'],
            ]);
            $variablem2 = InvDetMovimientos::where('id_producto', $request['id_producto'])->update([
                'fecha_vence' => $request['fecha'],
            ]);

            $msj = 'ok';
        } catch (Exception $e) {
            return json_encode(strval($e));
        }

        return json_encode($msj);
    }
    private function consultarVentasGeneral($id_empresa, $mes, $anio, $excluir_grupo_robles = true)
    {
        $rucs_grupo_robles = [
            '2390058384001',
            '1793135579001',
            '1391927177001',
            '0992704152001',
            '1307189140001',
            '0993375469001'
        ];

        $query = DB::table('ct_ventas')
            ->selectRaw("
                DATE(ct_ventas.fecha) as fecha,
                COUNT(*) as total_facturas,
                SUM(IFNULL(ct_ventas.total_final, 0)) as total_ventas
            ")
            ->where('ct_ventas.id_empresa', $id_empresa)
            ->where('ct_ventas.estado', '<>', 0)
            ->whereYear('ct_ventas.fecha', $anio)
            ->whereMonth('ct_ventas.fecha', $mes);

        if ($excluir_grupo_robles) {
            $query->where(function ($q) use ($rucs_grupo_robles) {
                foreach ($rucs_grupo_robles as $ruc) {
                    $q->where('ct_ventas.id_cliente', 'NOT LIKE', $ruc . '%');
                }
            });
        }

        return $query
            ->groupBy(DB::raw('DATE(ct_ventas.fecha)'))
            ->orderBy('fecha', 'desc')
            ->get();
    }

    public function filtrarVentasGeneral(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $mes  = (int) $request->input('mes', date('m'));
        $anio = (int) $request->input('anio', date('Y'));
        $excluir = (bool) $request->input('excluir_grupo_robles', true);

        if ($mes < 1 || $mes > 12) {
            $mes = (int) date('m');
        }

        if ($anio < 2000 || $anio > 2100) {
            $anio = (int) date('Y');
        }

        $ventas = $this->consultarVentasGeneral($id_empresa, $mes, $anio, $excluir);

        $evolucion = $this->consultarEvolucionVentasGeneral(
            $id_empresa,
            $mes,
            $anio,
            $excluir
        );

        return response()->json([
            'ok'        => true,
            'ventas'    => $ventas,
            'evolucion' => $evolucion,
        ]);
    }

    /**
     * Consulta las ventas agrupadas por producto y cliente.
     *
     * @param string      $id_empresa
     * @param int         $anio
     * @param string|null $codigo_producto
     *
     * @return \Illuminate\Support\Collection
     */

    private function consultarVentasPorProducto($id_empresa,$anio, $codigo_producto = null) 
    {
        $anio = (int) $anio;

        $ficha_tecnica = 'ficha_tecnica';
        $registro_sanitario = 'registro_sanitario';
        $select_pdf_1 = $this->existeColumna('ct_ventas', $ficha_tecnica)
            ? "MAX(cv.`" . $ficha_tecnica . "`) as ficha_tecnica"
            : "'' as ficha_tecnica";

        $select_pdf_2 = $this->existeColumna('ct_ventas', $registro_sanitario)
            ? "MAX(cv.`" . $registro_sanitario . "`) as registro_sanitario"
            : "'' as registro_sanitario";

        $fecha_inicio = $anio . '-01-01 00:00:00';
        $fecha_fin = ($anio + 1) . '-01-01 00:00:00';

        $query = DB::table('ct_ventas as cv')
            ->join(
                'ct_detalle_venta as detv',
                'detv.id_ct_ventas',
                '=',
                'cv.id'
            )
            ->leftJoin(
                'ct_clientes as cc',
                'cc.identificacion',
                '=',
                'cv.id_cliente'
            )
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.estado', 1)
            ->where('cv.fecha', '>=', $fecha_inicio)
            ->where('cv.fecha', '<', $fecha_fin)
            ->whereNotNull('detv.id_ct_productos')
            ->whereRaw("TRIM(detv.id_ct_productos) <> ''")
            ->select(
                DB::raw('MIN(cv.id) as id_venta'),
                DB::raw('TRIM(detv.id_ct_productos) as id_ct_productos'),

                /*
                * Un mismo código tiene descripciones distintas.
                * MAX evita separar el código CDS11001 en varias filas.
                */
                DB::raw("MAX(NULLIF(TRIM(detv.nombre), '')) as producto"),

                DB::raw(
                    'SUM(IFNULL(detv.cantidad, 0)) as total_cantidad'
                ),

                DB::raw($anio . ' as anio'),

                DB::raw(
                    'SUM(IFNULL(detv.extendido, 0)) as total_venta'
                ),

                DB::raw("
                    COALESCE(
                        NULLIF(TRIM(cc.identificacion), ''),
                        NULLIF(TRIM(cv.id_cliente), ''),
                        NULLIF(TRIM(cv.ruc_id_cliente), ''),
                        'SIN IDENTIFICACION'
                    ) as identificacion
                "),

                DB::raw("
                    COALESCE(
                        NULLIF(TRIM(cc.nombre), ''),
                        NULLIF(TRIM(cv.nombre_cliente), ''),
                        'SIN CLIENTE'
                    ) as cliente
                "),

                DB::raw("
                    COALESCE(
                        GROUP_CONCAT(
                            DISTINCT NULLIF(TRIM(cv.vendedor), '')
                            ORDER BY cv.vendedor
                            SEPARATOR ', '
                        ),
                        'SIN VENDEDOR'
                    ) as vendedor
                "),

                DB::raw($select_pdf_1),
                DB::raw($select_pdf_2)
            );

        if (!empty($codigo_producto)) {
            $query->whereRaw(
                'TRIM(detv.id_ct_productos) = ?',
                [$codigo_producto]
            );
        }

        return $query
            ->groupBy(
                DB::raw('TRIM(detv.id_ct_productos)'),
                'cc.identificacion',
                'cc.nombre',
                'cv.id_cliente',
                'cv.ruc_id_cliente',
                'cv.nombre_cliente'
            )
            ->orderBy('total_venta', 'desc')
            ->get();
    }

    /**
     * Retorna los productos vendidos en el año seleccionado.
     *
     * @param string $id_empresa
     * @param int    $anio
     *
     * @return \Illuminate\Support\Collection
     */
    private function consultarProductosFiltroVentas($id_empresa,$anio) 
    {
        $anio = (int) $anio;

        $fecha_inicio = $anio . '-01-01 00:00:00';
        $fecha_fin = ($anio + 1) . '-01-01 00:00:00';

        return DB::table('ct_detalle_venta as detv')
            ->join(
                'ct_ventas as cv',
                'cv.id',
                '=',
                'detv.id_ct_ventas'
            )
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.estado', 1)
            ->where('cv.fecha', '>=', $fecha_inicio)
            ->where('cv.fecha', '<', $fecha_fin)
            ->whereNotNull('detv.id_ct_productos')
            ->whereRaw("TRIM(detv.id_ct_productos) <> ''")
            ->select(
                DB::raw(
                    'TRIM(detv.id_ct_productos) as id_ct_productos'
                ),
                DB::raw(
                    "MAX(NULLIF(TRIM(detv.nombre), '')) as producto"
                )
            )
            ->groupBy(
                DB::raw('TRIM(detv.id_ct_productos)')
            )
            ->orderBy('producto', 'asc')
            ->get();
    }

    /**
     * Filtra las ventas por producto mediante AJAX.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function filtrarVentasPorProducto(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $anio_actual = (int) date('Y');
        $anio_minimo = $anio_actual - 5;

        $anio = (int) $request->input(
            'anio',
            $anio_actual
        );

        $codigo_producto = trim(
            (string) $request->input('producto', '')
        );

        if ($anio < $anio_minimo || $anio > $anio_actual) {
            $anio = $anio_actual;
        }

        if ($codigo_producto === '') {
            $codigo_producto = null;
        }

        if (empty($id_empresa)) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No existe una empresa seleccionada en la sesión.',
            ], 422);
        }

        try {
            $ventas = $this->consultarVentasPorProducto(
                $id_empresa,
                $anio,
                $codigo_producto
            );

            $productos = $this->consultarProductosFiltroVentas(
                $id_empresa,
                $anio
            );

            return response()->json([
                'ok' => true,
                'ventas' => $ventas,
                'productos' => $productos,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al filtrar ventas por producto', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'id_empresa' => $id_empresa,
                'anio' => $anio,
                'producto' => $codigo_producto,
            ]);

            return response()->json([
                'ok' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

   public function obtenerLinkPdfVentaProducto(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $id_venta = (int) $request->input('id_venta', 0);
        $codigo_producto = trim((string) $request->input('codigo_producto', ''));
        $ficha_tecnica = 'ficha_tecnica';
        $registro_sanitario = 'registro_sanitario';

        if (empty($id_empresa) || $id_venta <= 0) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No se recibio la empresa o la venta.',
            ], 422);
        }

        if (
            !$this->existeColumna('ct_ventas', $ficha_tecnica) ||
            !$this->existeColumna('ct_ventas', $registro_sanitario)
        ) {
            return response()->json([
                'ok' => true,
                'link' => '',
                'link_2' => '',
                'mensaje' => 'Falta crear/configurar las columnas ' . $ficha_tecnica . ' y/o ' . $registro_sanitario . ' en ct_ventas.',
            ]);
        }

        $venta = DB::table('ct_ventas')
            ->where('id_empresa', $id_empresa)
            ->where('id', $id_venta)
            ->where('estado', 1)
            ->select(
                'id',
                DB::raw('`' . $ficha_tecnica . '` as ficha_tecnica'),
                DB::raw('`' . $registro_sanitario . '` as registro_sanitario')
            )
            ->first();

        if (!$venta) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No se encontro la venta seleccionada.',
            ], 404);
        }

        if ($codigo_producto !== '') {
            $links_producto = $this->obtenerLinksDriveProducto(
                $id_empresa,
                $codigo_producto
            );

            if (
                $links_producto['registro_sanitario'] !== '' ||
                $links_producto['ficha_tecnica'] !== ''
            ) {
                return response()->json([
                    'ok' => true,
                    'link' => $links_producto['registro_sanitario'],
                    'link_2' => $links_producto['ficha_tecnica'],
                ]);
            }

            return response()->json([
                'ok' => true,
                'link' => '',
                'link_2' => '',
            ]);
        }

        return response()->json([
            'ok' => true,
            'link' => $venta->registro_sanitario,
            'link_2' => $venta->ficha_tecnica,
        ]);
    }

    public function guardarLinkPdfVentaProducto(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $id_venta = (int) $request->input('id_venta', 0);
        $url = trim((string) $request->input('url', ''));
        $url_2 = trim((string) $request->input('url_2', ''));
        $ficha_tecnica = 'ficha_tecnica';
        $registro_sanitario = 'registro_sanitario';

        if ((string) $id_empresa !== '0993069299001') {
            abort(403);
        }

        if (empty($id_empresa) || $id_venta <= 0) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No se recibio la empresa o la venta.',
            ], 422);
        }

        if ($url === '' && $url_2 === '') {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Ingrese al menos un link valido.',
            ], 422);
        }

        if (
            $url !== '' &&
            (
                !filter_var($url, FILTER_VALIDATE_URL) ||
                (
                    stripos($url, 'http://') !== 0 &&
                    stripos($url, 'https://') !== 0
                )
            )
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El link 1 debe iniciar con http:// o https://.',
            ], 422);
        }

        if (
            $url_2 !== '' &&
            (
                !filter_var($url_2, FILTER_VALIDATE_URL) ||
                (
                    stripos($url_2, 'http://') !== 0 &&
                    stripos($url_2, 'https://') !== 0
                )
            )
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'El link 2 debe iniciar con http:// o https://.',
            ], 422);
        }

        if (
            !$this->existeColumna('ct_ventas', $ficha_tecnica) ||
            !$this->existeColumna('ct_ventas', $registro_sanitario)
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Falta crear/configurar las columnas '
                    . $ficha_tecnica
                    . ' y/o '
                    . $registro_sanitario
                    . ' en ct_ventas.',
            ], 422);
        }

        $venta = DB::table('ct_ventas')
            ->where('id_empresa', $id_empresa)
            ->where('id', $id_venta)
            ->where('estado', 1)
            ->first();

        if (!$venta) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No se encontro la venta seleccionada.',
            ], 404);
        }

        $datos_actualizar = [
            $ficha_tecnica => $url,
            $registro_sanitario => $url_2,
        ];

        if ($this->existeColumna('ct_ventas', 'id_usuariomod')) {
            $datos_actualizar['id_usuariomod'] = Auth::user()->id;
        }

        if ($this->existeColumna('ct_ventas', 'ip_modificacion')) {
            $datos_actualizar['ip_modificacion'] = $request->ip();
        }

        DB::table('ct_ventas')
            ->where('id_empresa', $id_empresa)
            ->where('id', $id_venta)
            ->where('estado', 1)
            ->update($datos_actualizar);

        return response()->json([
            'ok' => true,
            'mensaje' => 'Links guardados correctamente.',
            'link' => $url,
            'link_2' => $url_2,
        ]);
    }

    public function buscarProductosVentasProducto(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $texto = trim((string) $request->input('q', ''));
        $anio = (int) $request->input('anio', date('Y'));

        if (empty($id_empresa)) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No existe una empresa seleccionada.',
                'productos' => [],
            ], 422);
        }

        if (mb_strlen($texto) < 3) {
            return response()->json([
                'ok' => true,
                'productos' => [],
            ]);
        }

        $fecha_inicio = $anio . '-01-01 00:00:00';
        $fecha_fin = ($anio + 1) . '-01-01 00:00:00';

        try {
            $productos = DB::table('ct_detalle_venta as detv')
                ->join(
                    'ct_ventas as cv',
                    'cv.id',
                    '=',
                    'detv.id_ct_ventas'
                )
                ->where('cv.id_empresa', $id_empresa)
                ->where('cv.estado', 1)
                ->where('cv.fecha', '>=', $fecha_inicio)
                ->where('cv.fecha', '<', $fecha_fin)
                ->whereNotNull('detv.id_ct_productos')
                ->whereRaw("TRIM(detv.id_ct_productos) <> ''")
                ->where(function ($query) use ($texto) {
                    $query
                        ->where(
                            'detv.id_ct_productos',
                            'LIKE',
                            '%' . $texto . '%'
                        )
                        ->orWhere(
                            'detv.nombre',
                            'LIKE',
                            '%' . $texto . '%'
                        );
                })
                ->select(
                    DB::raw(
                        'TRIM(detv.id_ct_productos) as codigo'
                    ),
                    DB::raw(
                        "MAX(NULLIF(TRIM(detv.nombre), '')) as nombre"
                    )
                )
                ->groupBy(
                    DB::raw('TRIM(detv.id_ct_productos)')
                )
                ->orderBy('nombre', 'asc')
                ->limit(20)
                ->get();

            return response()->json([
                'ok' => true,
                'productos' => $productos,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error buscando productos para ventas', [
                'mensaje' => $e->getMessage(),
                'id_empresa' => $id_empresa,
                'anio' => $anio,
                'texto' => $texto,
            ]);

            return response()->json([
                'ok' => false,
                'mensaje' => $e->getMessage(),
                'productos' => [],
            ], 500);
        }
    }

    private function existeColumna($tabla, $columna)
    {
        return DB::table('information_schema.COLUMNS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', $tabla)
            ->where('COLUMN_NAME', $columna)
            ->count() > 0;
    }


    public function sincronizarLinksDriveVentasProducto(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $anio_actual = (int) date('Y');
        $anio = (int) $request->input('anio', $anio_actual);
        $registro_sanitario = 'registro_sanitario';
        $ficha_tecnica = 'ficha_tecnica';

        if (empty($id_empresa)) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No existe una empresa seleccionada en la sesion.',
            ], 422);
        }

        if ($anio < ($anio_actual - 5) || $anio > $anio_actual) {
            $anio = $anio_actual;
        }

        if (
            !$this->existeColumna('ct_ventas', $registro_sanitario) ||
            !$this->existeColumna('ct_ventas', $ficha_tecnica)
        ) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Falta crear/configurar las columnas '
                    . $registro_sanitario
                    . ' y/o '
                    . $ficha_tecnica
                    . ' en ct_ventas.',
            ], 422);
        }

        try {
            $drive = $this->crearServicioGoogleDrive();

            $archivos_registro = $this->listarArchivosPdfDrive(
                $drive,
                self::DRIVE_REGISTRO_SANITARIO_FOLDER_ID
            );

            $archivos_ficha = $this->listarArchivosFichaTecnicaDrive($drive);

            $resultado_registro = $this->actualizarLinksDrivePorCodigo(
                $id_empresa,
                $anio,
                $registro_sanitario,
                $archivos_registro,
                $request,
                $drive,
                $archivos_ficha
            );

            $resultado_ficha = $this->actualizarLinksDrivePorCodigo(
                $id_empresa,
                $anio,
                $ficha_tecnica,
                $archivos_ficha,
                $request
            );

            return response()->json([
                'ok' => true,
                'mensaje' => 'Sincronizacion finalizada.',
                'anio' => $anio,
                'registro_sanitario' => $resultado_registro,
                'ficha_tecnica' => $resultado_ficha,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al sincronizar links Drive ventas producto', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'id_empresa' => $id_empresa,
                'anio' => $anio,
            ]);

            return response()->json([
                'ok' => false,
                'mensaje' => $e->getMessage(),
            ], 500);
        }
    }

    private function crearServicioGoogleDrive()
    {
        $credenciales = storage_path('app/google/drive-service-account.json');

        if (!file_exists($credenciales)) {
            throw new \RuntimeException(
                'No se encontro el JSON de Google Drive en storage/app/google/drive-service-account.json.'
            );
        }

        if (!class_exists('\Google_Client')) {
            throw new \RuntimeException(
                'No esta disponible la libreria google/apiclient en vendor.'
            );
        }

        $client = new \Google_Client();
        $client->setAuthConfig($credenciales);
        $client->setScopes([\Google_Service_Drive::DRIVE_READONLY]);

        return new \Google_Service_Drive($client);
    }

    private function listarArchivosPdfDrive($drive, $folder_id)
    {
        $archivos = [];
        $carpetas = [$folder_id];

        while (!empty($carpetas)) {
            $carpeta_actual = array_shift($carpetas);
            $page_token = null;

            do {
                $opciones = [
                    'q' => "'" . str_replace("'", "\\'", $carpeta_actual) . "' in parents and trashed = false",
                    'fields' => 'nextPageToken, files(id, name, mimeType)',
                    'pageSize' => 1000,
                    'supportsAllDrives' => true,
                    'includeItemsFromAllDrives' => true,
                ];

                if (!empty($page_token)) {
                    $opciones['pageToken'] = $page_token;
                }

                $resultado = $drive->files->listFiles($opciones);

                foreach ($resultado->getFiles() as $archivo) {
                    $mime = (string) $archivo->getMimeType();
                    $nombre = (string) $archivo->getName();

                    if ($mime === 'application/vnd.google-apps.folder') {
                        $carpetas[] = $archivo->getId();
                        continue;
                    }

                    if (
                        $mime !== 'application/pdf' &&
                        !preg_match('/\.pdf$/i', $nombre)
                    ) {
                        continue;
                    }

                    $codigo = $this->normalizarCodigoDrive($nombre);

                    if ($codigo === '') {
                        continue;
                    }

                    $archivos[$codigo] = [
                        'codigo' => $codigo,
                        'nombre' => $nombre,
                        'id' => $archivo->getId(),
                        'link' => 'https://drive.google.com/file/d/'
                            . $archivo->getId()
                            . '/view?usp=sharing',
                    ];
                }

                $page_token = $resultado->getNextPageToken();
            } while (!empty($page_token));
        }

        return $archivos;
    }

    private function listarArchivosFichaTecnicaDrive($drive)
    {
        $archivos = [];

        foreach (self::DRIVE_FICHA_TECNICA_FOLDER_IDS as $folder_id) {
            $archivos = array_merge(
                $archivos,
                $this->listarArchivosPdfDrive($drive, $folder_id)
            );
        }

        return $archivos;
    }

    private function normalizarCodigoDrive($nombre)
    {
        $codigo = preg_replace('/\.pdf$/i', '', trim((string) $nombre));
        $codigo = preg_replace('/\s+/', ' ', $codigo);

        return trim($codigo);
    }

    private function actualizarLinksDrivePorCodigo(
        $id_empresa,
        $anio,
        $columna,
        array $archivos,
        Request $request,
        $drive = null,
        array $archivos_ficha = []
    ) {
        $resultado = [
            'archivos_drive' => count($archivos),
            'codigos_encontrados' => 0,
            'ventas_actualizadas' => 0,
            'ventas_sin_pdf' => 0,
            'codigos_sin_ventas' => [],
        ];

        $fecha_inicio = $anio . '-01-01 00:00:00';
        $fecha_fin = ($anio + 1) . '-01-01 00:00:00';
        $codigos_encontrados = [];
        $archivos_encontrados = [];
        $actualizaciones_con_pdf = [];

        $codigos_ventas = DB::table('ct_detalle_venta as detv')
            ->join('ct_ventas as cv', 'cv.id', '=', 'detv.id_ct_ventas')
            ->where('cv.id_empresa', $id_empresa)
            ->where('cv.estado', 1)
            ->where('cv.fecha', '>=', $fecha_inicio)
            ->where('cv.fecha', '<', $fecha_fin)
            ->whereNotNull('detv.id_ct_productos')
            ->whereRaw("TRIM(detv.id_ct_productos) <> ''")
            ->select(DB::raw('TRIM(detv.id_ct_productos) as codigo'))
            ->groupBy(DB::raw('TRIM(detv.id_ct_productos)'))
            ->get();

        foreach ($codigos_ventas as $codigo_venta) {
            $codigo = (string) $codigo_venta->codigo;
            $archivo = null;

            if ($columna === 'registro_sanitario') {
                $codigo_ficha = $this->obtenerFichaTecnicaProducto(
                    $id_empresa,
                    $codigo
                );

                if ($codigo_ficha === '') {
                    $codigo_ficha = $codigo;
                }

                $archivo_ficha = $this->buscarArchivoDrivePorCodigo(
                    $codigo_ficha,
                    $archivos_ficha
                );

                $registro_ficha = $this->obtenerRegistroSanitarioDesdeFichaTecnica(
                    $drive,
                    $archivo_ficha
                );

                if ($registro_ficha !== '') {
                    $archivo = $this->buscarArchivoDrivePorCodigo(
                        $registro_ficha,
                        $archivos,
                        true
                    );
                }
            } else {
                $archivo = $this->buscarArchivoDrivePorCodigo(
                    $codigo,
                    $archivos
                );
            }

            $ventas_ids = DB::table('ct_detalle_venta as detv')
                ->join('ct_ventas as cv', 'cv.id', '=', 'detv.id_ct_ventas')
                ->where('cv.id_empresa', $id_empresa)
                ->where('cv.estado', 1)
                ->where('cv.fecha', '>=', $fecha_inicio)
                ->where('cv.fecha', '<', $fecha_fin)
                ->whereRaw('TRIM(detv.id_ct_productos) = ?', [$codigo])
                ->select(DB::raw('DISTINCT cv.id as id'))
                ->groupBy('cv.id')
                ->pluck('id')
                ->toArray();

            if (empty($ventas_ids)) {
                continue;
            }

            $datos_actualizar = [
                $columna => empty($archivo) ? '' : $archivo['link'],
            ];

            if ($this->existeColumna('ct_ventas', 'id_usuariomod')) {
                $datos_actualizar['id_usuariomod'] = Auth::user()->id;
            }

            if ($this->existeColumna('ct_ventas', 'ip_modificacion')) {
                $datos_actualizar['ip_modificacion'] = $request->ip();
            }

            if (!empty($archivo)) {
                $actualizaciones_con_pdf[] = [
                    'ventas_ids' => $ventas_ids,
                    'datos_actualizar' => $datos_actualizar,
                    'codigo' => $codigo,
                    'codigo_archivo' => $archivo['codigo'],
                ];

                continue;
            }

            DB::table('ct_ventas')
                ->where('id_empresa', $id_empresa)
                ->whereIn('id', $ventas_ids)
                ->where('estado', 1)
                ->update($datos_actualizar);

            $resultado['ventas_actualizadas'] += count($ventas_ids);
            $resultado['ventas_sin_pdf'] += count($ventas_ids);
        }

        foreach ($actualizaciones_con_pdf as $actualizacion) {
            DB::table('ct_ventas')
                ->where('id_empresa', $id_empresa)
                ->whereIn('id', $actualizacion['ventas_ids'])
                ->where('estado', 1)
                ->update($actualizacion['datos_actualizar']);

            $resultado['ventas_actualizadas'] += count($actualizacion['ventas_ids']);
            $codigos_encontrados[$actualizacion['codigo']] = true;
            $archivos_encontrados[$actualizacion['codigo_archivo']] = true;
        }

        $resultado['codigos_encontrados'] = count($codigos_encontrados);
        $resultado['codigos_sin_ventas'] = array_values(
            array_diff(array_keys($archivos), array_keys($archivos_encontrados))
        );

        return $resultado;
    }

    private function obtenerLinksDriveProducto($id_empresa, $codigo_producto)
    {
        $links = [
            'registro_sanitario' => '',
            'ficha_tecnica' => '',
        ];

        try {
            $drive = $this->crearServicioGoogleDrive();
            $archivos_registro = $this->listarArchivosPdfDrive(
                $drive,
                self::DRIVE_REGISTRO_SANITARIO_FOLDER_ID
            );

            $archivos_ficha = $this->listarArchivosFichaTecnicaDrive($drive);

            $codigo_ficha = $this->obtenerFichaTecnicaProducto(
                $id_empresa,
                $codigo_producto
            );

            if ($codigo_ficha === '') {
                $codigo_ficha = $codigo_producto;
            }

            $archivo_ficha = $this->buscarArchivoDrivePorCodigo(
                $codigo_ficha,
                $archivos_ficha
            );

            $registro_ficha = $this->obtenerRegistroSanitarioDesdeFichaTecnica(
                $drive,
                $archivo_ficha
            );

            $archivo_registro = $registro_ficha === ''
                ? null
                : $this->buscarArchivoDrivePorCodigo(
                    $registro_ficha,
                    $archivos_registro,
                    true
                );

            $links['registro_sanitario'] = empty($archivo_registro)
                ? ''
                : $archivo_registro['link'];

            $links['ficha_tecnica'] = empty($archivo_ficha)
                ? ''
                : $archivo_ficha['link'];
        } catch (\Exception $e) {
            \Log::error('Error al obtener links Drive por producto', [
                'mensaje' => $e->getMessage(),
                'codigo_producto' => $codigo_producto,
            ]);
        }

        return $links;
    }

    private function obtenerRegistroSanitarioDesdeFichaTecnica($drive, $archivo_ficha)
    {
        if (empty($drive) || empty($archivo_ficha) || empty($archivo_ficha['id'])) {
            return '';
        }

        try {
            $contenido_pdf = $this->descargarPdfDrive($drive, $archivo_ficha['id']);
            $texto_pdf = $this->extraerTextoPdfBasico($contenido_pdf);

            return $this->extraerRegistroSanitarioTexto($texto_pdf);
        } catch (\Exception $e) {
            \Log::error('Error leyendo registro sanitario desde ficha tecnica', [
                'mensaje' => $e->getMessage(),
                'archivo_ficha' => $archivo_ficha,
            ]);

            return '';
        }
    }

    private function descargarPdfDrive($drive, $file_id)
    {
        $response = $drive->files->get($file_id, [
            'alt' => 'media',
            'supportsAllDrives' => true,
        ]);

        if (is_object($response) && method_exists($response, 'getBody')) {
            return (string) $response->getBody()->getContents();
        }

        return (string) $response;
    }

    private function extraerTextoPdfBasico($contenido_pdf)
    {
        $texto = '';

        if (!preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $contenido_pdf, $streams)) {
            return $texto;
        }

        foreach ($streams[1] as $stream) {
            $stream_texto = $this->decodificarStreamPdf($stream);
            $texto .= ' ' . $this->extraerCadenasTextoPdf($stream_texto);
        }

        $texto = preg_replace('/\s+/', ' ', $texto);

        return trim($texto);
    }

    private function decodificarStreamPdf($stream)
    {
        $stream = ltrim($stream, "\r\n");
        $decodificado = @zlib_decode($stream);

        if ($decodificado !== false) {
            return $decodificado;
        }

        $decodificado = @gzuncompress($stream);

        if ($decodificado !== false) {
            return $decodificado;
        }

        return $stream;
    }

    private function extraerCadenasTextoPdf($contenido)
    {
        $texto = '';

        if (preg_match_all('/\((?:\\\\.|[^\\\\()])*\)/s', $contenido, $matches)) {
            foreach ($matches[0] as $cadena) {
                $texto .= ' ' . $this->decodificarCadenaPdf($cadena);
            }
        }

        if (preg_match_all('/(?<!<)<([0-9A-Fa-f\s]+)>(?!>)/', $contenido, $hex_matches)) {
            foreach ($hex_matches[1] as $hex) {
                $texto .= ' ' . $this->decodificarHexPdf($hex);
            }
        }

        return $texto;
    }

    private function decodificarCadenaPdf($cadena)
    {
        $cadena = substr($cadena, 1, -1);
        $cadena = preg_replace('/\\\\([nrtbf()\\\\])/', '$1', $cadena);
        $cadena = preg_replace_callback('/\\\\([0-7]{1,3})/', function ($match) {
            return chr(octdec($match[1]));
        }, $cadena);

        return $cadena;
    }

    private function decodificarHexPdf($hex)
    {
        $hex = preg_replace('/\s+/', '', $hex);

        if ($hex === '') {
            return '';
        }

        if (strlen($hex) % 2 !== 0) {
            $hex .= '0';
        }

        $binario = @hex2bin($hex);

        if ($binario === false) {
            return '';
        }

        $utf16 = @mb_convert_encoding($binario, 'UTF-8', 'UTF-16BE');

        if ($utf16 !== false && preg_match('/[A-Za-z0-9]/', $utf16)) {
            return $utf16;
        }

        return $binario;
    }

    private function extraerRegistroSanitarioTexto($texto)
    {
        if (preg_match('/R\s*E\s*G\s*S\s*A\s*N\s*[:.\-]?\s*([0-9\s]+-\s*D\s*M\s*E\s*-\s*[0-9\s]+)/i', $texto, $match)) {
            return $this->normalizarRegistroSanitarioExtraido($match[1]);
        }

        if (preg_match('/REG\s*SAN\s*[:.\-]?\s*([0-9\s]+-\s*D\s*M\s*E\s*-\s*[0-9\s]+)/i', $texto, $match)) {
            return $this->normalizarRegistroSanitarioExtraido($match[1]);
        }

        return '';
    }

    private function normalizarRegistroSanitarioExtraido($codigo)
    {
        $codigo = preg_replace('/\s+/', '', strtoupper((string) $codigo));

        if (preg_match('/^[0-9]+-DME-[0-9]+$/', $codigo)) {
            return $codigo;
        }

        return '';
    }

    private function obtenerRegistroSanitarioProducto($id_empresa, $codigo_producto)
    {
        if (!$this->existeColumna('producto', 'registro_sanitario')) {
            return '';
        }

        $query_producto = DB::table('producto')
            ->whereRaw('TRIM(codigo) = ?', [$codigo_producto]);

        if ($this->existeColumna('producto', 'id_empresa')) {
            $query_producto->where('id_empresa', $id_empresa);
        }

        $producto = $query_producto
            ->select(DB::raw('TRIM(registro_sanitario) as registro_sanitario'))
            ->first();

        if (!$producto) {
            $producto = DB::table('producto')
                ->whereRaw('TRIM(codigo) = ?', [$codigo_producto])
                ->select(DB::raw('TRIM(registro_sanitario) as registro_sanitario'))
                ->first();
        }

        return $producto ? trim((string) $producto->registro_sanitario) : '';
    }

    private function obtenerFichaTecnicaProducto($id_empresa, $codigo_producto)
    {
        if (!$this->existeColumna('producto', 'ficha_tecnica')) {
            return '';
        }

        $query_producto = DB::table('producto')
            ->whereRaw('TRIM(codigo) = ?', [$codigo_producto]);

        if ($this->existeColumna('producto', 'id_empresa')) {
            $query_producto->where('id_empresa', $id_empresa);
        }

        $producto = $query_producto
            ->select(DB::raw('TRIM(ficha_tecnica) as ficha_tecnica'))
            ->first();

        if (!$producto) {
            $producto = DB::table('producto')
                ->whereRaw('TRIM(codigo) = ?', [$codigo_producto])
                ->select(DB::raw('TRIM(ficha_tecnica) as ficha_tecnica'))
                ->first();
        }

        return $producto ? trim((string) $producto->ficha_tecnica) : '';
    }

    private function buscarArchivoDrivePorCodigo(
        $codigo,
        array $archivos,
        $permitir_coincidencia_inversa = false
    )
    {
        $codigo_normalizado = mb_strtoupper(trim((string) $codigo));
        $codigo_comparacion = $this->normalizarCodigoComparacionDrive($codigo);

        if ($codigo_normalizado === '') {
            return null;
        }

        foreach ($archivos as $archivo) {
            $nombre_normalizado = mb_strtoupper((string) $archivo['codigo']);
            $nombre_comparacion = $this->normalizarCodigoComparacionDrive(
                $archivo['codigo']
            );

            if (
                $nombre_normalizado === $codigo_normalizado ||
                strpos($nombre_normalizado, $codigo_normalizado) !== false ||
                (
                    $codigo_comparacion !== '' &&
                    (
                        $nombre_comparacion === $codigo_comparacion ||
                        strpos($nombre_comparacion, $codigo_comparacion) !== false
                    )
                ) ||
                (
                    $permitir_coincidencia_inversa &&
                    (
                        strpos($codigo_normalizado, $nombre_normalizado) !== false ||
                        (
                            $nombre_comparacion !== '' &&
                            strpos($codigo_comparacion, $nombre_comparacion) !== false
                        )
                    )
                )
            ) {
                return $archivo;
            }
        }

        return null;
    }

    private function normalizarCodigoComparacionDrive($codigo)
    {
        $codigo = mb_strtoupper(trim((string) $codigo));
        $codigo = preg_replace('/\.PDF$/i', '', $codigo);
        $codigo = str_replace(['–', '—', '_'], '-', $codigo);
        $codigo = preg_replace('/\s+/', '', $codigo);
        $codigo = str_replace(['_', '/', '\\'], '-', $codigo);
        $codigo = preg_replace('/[\x{2010}-\x{2015}]/u', '-', $codigo);
        $codigo = preg_replace('/[^A-Z0-9]/', '', $codigo);

        return trim($codigo);
    }
}
