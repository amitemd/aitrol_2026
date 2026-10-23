<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Ct_Valida_Anticipo;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Requests\Request as RequestsRequest;
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;

class NominaOtrosAnticiposController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha_actual = date('Y-m-d');
        $anio_actual  = date('Y');
        $mes_actual   = date('m');

        //dd($mes_actual);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();
        $lista_banco   = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        //Obtenemos los empleados ingresados por empresa para calculo de Anticipo
        // $empl_nomina = Ct_Nomina::where('estado', '1')
        //     ->where('id_empresa', $id_empresa)
        //     ->orderby('nombres', 'asc')->get();
        //->get();

        $empl_nomina = Ct_Nomina::where('ct_nomina.estado', '1')
            ->where('id_empresa', $id_empresa)
            ->join('users as u', 'u.id', 'ct_nomina.id_user')
            ->select('u.*', 'ct_nomina.*')
            ->orderby('u.apellido1', 'asc')->get();

        //Obtenemos el Numero de Empleados por empresa
        $cont_empl = Ct_Nomina::where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->orderby('id', 'asc')
            ->get()->count();

        //dd($empl_nomina);

        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->where('anio', $anio_actual)
            ->where('mes', $mes_actual)
            ->where('estado', '1')
            ->first();

        $anticipo_valida = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)
            ->where('anio', $anio_actual)
            ->where('mes', $mes_actual)
            ->where('estado', '1')
            ->first();

        $valida_anticipo = 0;
        if (!is_null($inf_val_anticip)) {
            $valida_anticipo = 1;
        }

        if (!is_null($anticipo_valida)) {
            $valida_anticipo = 1;
        }


        return view('contable.rol_otro_anticipo.index_otros_anticipo', ['empresa' => $empresa, 'empl_nomina' => $empl_nomina, 'cont_empl' => $cont_empl, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'fecha_actual' => $fecha_actual, 'mes_actual' => $mes_actual, 'anio_actual' => $anio_actual, 'valida_anticipo' => $valida_anticipo]);
    }

    public function obtener_anticipo_quincena(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $id_anio          = $request['year'];
        $id_mes           = $request['mes'];
        $valor_porcentaje = $request['valor_porcent'];

        $id_empresa = $request->session()->get('id_empresa');

        $empl_rol = Ct_Nomina::where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->orderby('id', 'asc')->get();

        if (!is_null($empl_rol)) {

            foreach ($empl_rol as $value) {

                $valor_anticip = (($value->sueldo_neto) * ($valor_porcentaje)) / 100;

                $input = [
                    'id_user'         => $value->id_user,
                    'id_empresa'      => $value->id_empresa,
                    'anio'            => $id_anio,
                    'mes'             => $id_mes,
                    'sueldo'          => $value->sueldo_neto,
                    'porcentaje'      => $valor_porcentaje,
                    'valor_anticipo'  => $valor_anticip,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ];

                Ct_Rh_Valor_Anticipos::create($input);
            }
        }

        //return view('contable.rol_anticipo_quincena.resultado_anticipo',['empl_rol' => $empl_rol,'valor_porcentaje' => $valor_porcentaje,'id_empresa' => $id_empresa,'id_anio' => $id_anio,'id_mes' => $id_mes]);

    }

    //Calculo de Anticipos Individual (Empleados)
    public function obtener_anticipo_individual($id_nom, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $id_anio          = $request['year'];
        $id_mes           = $request['mes'];
        $valor_porcentaje = $request['valor_porcent'];

        $id_empresa = $request->session()->get('id_empresa');

        $info_empleado = Ct_Nomina::where('id', $id_nom)
            ->where('id_empresa', $id_empresa)
            ->where('estado', '1')
            ->first();

        if (!is_null($info_empleado)) {

            $valor_anticip = (($info_empleado->sueldo_neto) * ($valor_porcentaje)) / 100;

            $input = [
                'id_user'         => $info_empleado->id_user,
                'id_empresa'      => $info_empleado->id_empresa,
                'anio'            => $id_anio,
                'mes'             => $id_mes,
                'sueldo'          => $info_empleado->sueldo_neto,
                'porcentaje'      => $valor_porcentaje,
                'valor_anticipo'  => $valor_anticip,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
            ];

            Ct_Rh_Valor_Anticipos::create($input);
        }
    }

    //Calculo de Anticipos Individual (Empleados)
    public function registrar_anticipo_quincena(Request $request)
    {

        $ip_cliente    = $_SERVER["REMOTE_ADDR"];
        $id_usuario    = Auth::user()->id;
        $id_nom        = $request['id_nom'];
        $anio          = $request['anio'];
        $id_mes        = $request['mes'];
        $valor_anticip = $request['val_anticip'];
        $id_empresa    = $request->session()->get('id_empresa');

        $info_empleado = Ct_Nomina::where('id', $id_nom)
            ->where('id_empresa', $id_empresa)
            ->where('estado', '1')
            ->first();

        if (!is_null($info_empleado)) {

            $input = [
                'id_user'         => $info_empleado->id_user,
                'id_empresa'      => $id_empresa,
                'anio'            => $anio,
                'mes'             => $id_mes,
                'fecha_creacion'  => $request['fech_crea'],
                'id_tipo_pago'    => $request['tip_pag'],
                'numero_cuenta'   => $request['num_cuent'],
                'banco'           => $request['banco'],
                'cuenta_saliente' => $request['cuent_salient'],
                'num_cheque'      => $request['num_cheq'],
                'fecha_cheque'    => $request['fecha_cheq'],
                'sueldo'          => $info_empleado->sueldo_neto,
                'valor_anticipo'  => $valor_anticip,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'estado'          => '1',
            ];
            Ct_Rh_Valor_Anticipos::create($input);
        }
    }

    public function registrar_asiento_anticipo_quincena(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');

        $anio_cobro   = $request['anio'];
        $mes_cobro    = $request['mes'];
        $sum_anticipo = $request['sum_anticip'];
        //$fech_creacion = Date('Y-m-d H:i:s');
        $fech_creacion = $request['fech_crea'];
        $cuent_sal     = $request['cuent_sal'];

        //Obtenemos el Mes de Inicio
        $txt_mes_cobro = '';
        if ($mes_cobro == '12') {
            $txt_mes_cobro = 'DICIEMBRE';
        } elseif ($mes_cobro == '11') {
            $txt_mes_cobro = 'NOVIEMBRE';
        } elseif ($mes_cobro == '10') {
            $txt_mes_cobro = 'OCTUBRE';
        } elseif ($mes_cobro == '9') {
            $txt_mes_cobro = 'SEPTIEMBRE';
        } elseif ($mes_cobro == '8') {
            $txt_mes_cobro = 'AGOSTO';
        } elseif ($mes_cobro == '7') {
            $txt_mes_cobro = 'JULIO';
        } elseif ($mes_cobro == '6') {
            $txt_mes_cobro = 'JUNIO';
        } elseif ($mes_cobro == '5') {
            $txt_mes_cobro = 'MAYO';
        } elseif ($mes_cobro == '4') {
            $txt_mes_cobro = 'ABRIL';
        } elseif ($mes_cobro == '3') {
            $txt_mes_cobro = 'MARZO';
        } elseif ($mes_cobro == '2') {
            $txt_mes_cobro = 'FEBRERO';
        } elseif ($mes_cobro == '1') {
            $txt_mes_cobro = 'ENERO';
        }

        $text = 'Anticipo 1ERA Quincena' . ':' . ' ' . 'Año Cobro Anticipo' . ':' . $anio_cobro . ' ' . 'Mes Cobro Anticipo' . ':' . $txt_mes_cobro . ' ' . 'Valor' . ':' . $sum_anticipo;

        /************************************
         *****Inserta Ct_Asientos_Cabecera***
        /************************************/
        $input_cabecera = [
            'observacion'     => 'ANTICIPOS EMPLEADOS 1ERA QUINCENA:' . ' ' . 'Año Cobro Anticipo' . ':' . $anio_cobro . ' ' . 'Mes Cobro Anticipo' . ':' . $txt_mes_cobro . ' ' . 'Por la Cantidad de' . ':' . $sum_anticipo,
            'fecha_asiento'   => $fech_creacion,
            //'fact_numero'     => $numero_anticipo,
            'id_empresa'      => $id_empresa,
            //'observacion'     => $text,
            'valor'           => $sum_anticipo,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        //dd($input_cabecera);

        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        //dd($id_asiento_cabecera);

        /************************************
         *****Inserta Ct_Asientos_Detalle*****
        /************************************/

        if ($sum_anticipo > 0) {

            $plan_cuentas = Plan_Cuentas::where('id', $cuent_sal)->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuent_sal,
                'descripcion'         => $plan_cuentas->nombre,
                'fecha'               => $fech_creacion,
                'debe'                => '0',
                'haber'               => $sum_anticipo,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        if ($sum_anticipo > 0) {

            $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('NOMINAANTICIPO_ANT_SUELDOS');

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta->cuenta_guardar,
                'descripcion'         => $cuenta->secuencial_mostrar,
                'fecha'               => $fech_creacion,
                'debe'                => $sum_anticipo,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);
        }

        Ct_Valida_Anticipo::create([

            'id_empresa'      => $id_empresa,
            'asiento'         => $id_asiento_cabecera,
            'anio'            => $anio_cobro,
            'mes'             => $mes_cobro,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ]);
    }

    //Actualiza Valor Anticipo Primera Quincena
    public function actualiza_valor_anticipos($id_nomina)
    {

        $inf_nomina = Ct_Nomina::where('id', $id_nomina)->first();
        //->where('estado','1')
        //->first();

        return view('contable/rol_otro_anticipo/mod_update_val_anticipo', ['id_nomina' => $id_nomina, 'inf_nomina' => $inf_nomina]);
    }

    //Guarda Valor Anticipo Actualizados
    public function store_valor_anticipo(Request $request)
    {

        $val_anticipo = $request['val_anticipo'];

        //dd($val_anticipo);

        Ct_Nomina::where('id', $request['id_nomina'])
            ->where('estado', '1')
            ->update(['val_anticip_quince' => $request['val_anticipo']]);

        $msj = "ok";

        return ['msj' => $msj];
    }

    //Valida Que Exista Anticipo
    public function imprime_anticipos_quincena(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $anio            = $request['year'];
        $mes             = $request['mes'];
        $inf_val_anticip = 0;
        $anticipo_valida = 0;

        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('estado', '1')
            ->get()->count();

        $anticipo_valida = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('estado', '1')
            ->get()->count();

        if ($inf_val_anticip > 0 or $anticipo_valida > 0) {

            $msj = "si";
            return ['msj' => $msj];
        } else {

            $msj = "no";
            return ['msj' => $msj];
        }
    }

    public function comprobar_empresa(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        return ['nombre_empresa' => $empresa->nombrecomercial, 'id_empresa' => $empresa->id];
    }

    //Descarga Pdf de Anticipos de Quincena
    public function obtener_pdf_anticipo_quincena2($id, Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $inf_cab_anticip = Ct_Valida_Anticipo::findorfail($id);

        //$id_mes  = $mes;
        //$id_anio = $anio;
        $mes = $inf_cab_anticip->mes;
        $anio = $inf_cab_anticip->id_anio;
        // $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
        //     ->where('anio', $anio)
        //     ->where('mes', $mes)
        //     ->where('estado', '1')
        //     ->groupBy('id_user')
        //     ->get();

        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->join('users', 'users.id', 'ct_rh_valor_anticipos.id_user')
            //->where('ct_rh_valor_anticipos.anio', $anio)
            //->where('ct_rh_valor_anticipos.mes', $mes)
            ->where('ct_rh_valor_anticipos.id_valida', $id)
            ->where('ct_rh_valor_anticipos.estado', '1')
            ->where('ct_rh_valor_anticipos.id_asiento_cabecera', '<>', null)
            ->orderBy('users.apellido1')
            ->groupBy('users.id')
            ->get();

        /*$inf_val_anticip->transform(function($item) {
            $item->sueldo_mensual =$this->calcular_sueldo_neto(
                $item->jornada,
                $item->cantidad_horas,
                $item->dias_laborables,
                $item->sueldo_neto
            );
            return $item;
        });
        dd($inf_val_anticip);*/
        $ult_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            //->where('anio', $anio)
            //->where('mes', $mes)
            ->where('ct_rh_valor_anticipos.id_valida', $id)
            ->where('estado', '1')
            ->where('id_asiento_cabecera', '<>', null)
            ->get()->first();

        //dd($inf_val_anticip);

        $vistaurl = "contable.rol_otro_anticipo.pdf_anticipo_quincena";
        $view     = \View::make($vistaurl, compact('inf_val_anticip', 'empresa', 'ult_val_anticip'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Anticipo 1era Quincena Mensual.pdf');
    }


    public function obtener_excel_anticipo_quincena2($id, Request $request)
    {
        //dd($mes, $anio);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $inf_cab_anticip = Ct_Valida_Anticipo::findorfail($id);

        //$id_mes  = $mes;
        //$id_anio = $anio;
        $mes = $inf_cab_anticip->mes;
        $anio = $inf_cab_anticip->id_anio;


        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->join('users', 'users.id', 'ct_rh_valor_anticipos.id_user')
            //->where('ct_rh_valor_anticipos.anio', $anio)
            //->where('ct_rh_valor_anticipos.mes', $mes)
            ->where('ct_rh_valor_anticipos.id_valida', $id)
            ->where('ct_rh_valor_anticipos.estado', '1')
            ->where('ct_rh_valor_anticipos.id_asiento_cabecera', '<>', null)
            ->orderBy('users.apellido1')
            ->groupBy('users.id')
            ->get();


        $ult_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            //->where('anio', $anio)
            //->where('mes', $mes)
            ->where('ct_rh_valor_anticipos.id_valida', $id)
            ->where('estado', '1')
            ->where('id_asiento_cabecera', '<>', null)
            ->get()->first();

        $tip_pago = Ct_Rh_Tipo_Pago::where('id', $ult_val_anticip->id_tipo_pago)->where('estado', '1')->first();
        $inf_banco = Ct_Bancos::where('id', $ult_val_anticip->banco)->where('estado', '1')->first();
        $inf_caja_ban = Ct_Caja_Banco::where('cuenta_mayor', $ult_val_anticip->cuenta_saliente)->where('estado', '1')->first();


        Excel::create('Anticipo 1era Quincena Mensual', function ($excel) use ($inf_val_anticip, $ult_val_anticip, $anio, $mes, $tip_pago, $inf_banco, $inf_caja_ban, $empresa) {

            $excel->sheet('ANTICIPO DE QUINCENA', function ($sheet) use ($inf_val_anticip, $ult_val_anticip, $anio, $mes, $tip_pago, $inf_banco, $inf_caja_ban, $empresa) {
                $i = 2;
                $sheet->mergeCells('A1:E1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('ANTICIPOS 1ERA QUINCENA EMPLEADOS');
                    $cell->setFontSize(16);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B' . $i, function ($cell) use ($anio) {
                    $cell->setValue('AÑO: ' . $anio);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C' . $i, function ($cell) use ($mes) {
                    $cell->setValue('MES: ' . $mes);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) use ($ult_val_anticip) {
                    $cell->setValue('FECHA: ' . $ult_val_anticip->fecha_creacion);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;

                $sheet->cell('B' . $i, function ($cell) use ($tip_pago) {
                    $cell->setValue('Tipo de Pago: ' . $tip_pago->tipo);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) use ($ult_val_anticip) {
                    $cell->setValue('N# Cuenta: ' . $ult_val_anticip->numero_cuenta);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;

                $sheet->cell('B' . $i, function ($cell) use ($inf_banco) {
                    $cell->setValue('Banco: ' . $inf_banco->nombre);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) use ($inf_caja_ban) {
                    $cell->setValue('Cuenta Saliente: ' . $inf_caja_ban->nombre);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;

                $sheet->cell('A' . $i, function ($cell) {
                    $cell->setValue('#');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B' . $i, function ($cell) {
                    $cell->setValue('NOMBRE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C' . $i, function ($cell) {
                    $cell->setValue('CARGO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) {
                    $cell->setValue('SUELDO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) {
                    $cell->setValue('VALOR ANTICIPO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;

                $sumaTotal = 0;
                $count = 1;
                foreach ($inf_val_anticip as $value) {
                    $sumaTotal += $value->valor_anticipo;
                    $inf_nomina = null;
                    if (!is_null($value->id_user)) {
                        $user = Ct_Nomina::join('users as u', 'u.id', 'ct_nomina.id_user')
                            ->where('ct_nomina.id_user', $value->id_user)
                            ->where('ct_nomina.id_empresa', $empresa->id)
                            //->where('ct_nomina.estado', '1')
                            ->where('u.estado', '1')
                            ->select('ct_nomina.cargo', 'u.apellido1', 'u.apellido2', 'u.nombre1', 'u.nombre2')
                            ->orderBy('u.apellido1', 'desc')->first();
                        if (is_null($user)) {
                            // dd($value);
                        }
                    }

                    $sheet->cell('A' . $i, function ($cell) use ($count) {
                        $cell->setValue($count);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($user) {
                        $cell->setValue($user->apellido1 . " " . $user->apellido2 . " " . $user->nombre1 . " " . $user->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($user) {
                        $cell->setValue($user->cargo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        $cell->setValue('$ ' . $value->sueldo);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        $cell->setValue('$ ' . $value->valor_anticipo);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    $count++;
                }

                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) use ($sumaTotal) {
                    $cell->setValue('$ ' . $sumaTotal);
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
            });
        })->export('xlsx');
    }
    public function new_excel_anticipo_quincena($id, Request $request)
    {
        //dd($mes, $anio);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $inf_cab_anticip = Ct_Valida_Anticipo::findorfail($id);

        //$id_mes  = $mes;
        //$id_anio = $anio;
        $mes = $inf_cab_anticip->mes;
        $anio = $inf_cab_anticip->anio;

        $txt_mes = '';
        if ($mes == '12') {
            $txt_mes = 'DICIEMBRE';
        } elseif ($mes == '11') {
            $txt_mes = 'NOVIEMBRE';
        } elseif ($mes == '10') {
            $txt_mes = 'OCTUBRE';
        } elseif ($mes == '9') {
            $txt_mes = 'SEPTIEMBRE';
        } elseif ($mes == '8') {
            $txt_mes = 'AGOSTO';
        } elseif ($mes == '7') {
            $txt_mes = 'JULIO';
        } elseif ($mes == '6') {
            $txt_mes = 'JUNIO';
        } elseif ($mes == '5') {
            $txt_mes = 'MAYO';
        } elseif ($mes == '4') {
            $txt_mes = 'ABRIL';
        } elseif ($mes == '3') {
            $txt_mes = 'MARZO';
        } elseif ($mes == '2') {
            $txt_mes = 'FEBRERO';
        } elseif ($mes == '1') {
            $txt_mes = 'ENERO';
        }


        $inf_val_anticip = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)
            ->join('users', 'users.id', 'ct_rh_valor_anticipos.id_user')
            //->where('ct_rh_valor_anticipos.anio', $anio)
            //->where('ct_rh_valor_anticipos.mes', $mes)
            ->where('ct_rh_valor_anticipos.id_valida', $id)
            ->where('ct_rh_valor_anticipos.estado', '1')
            //->where('ct_rh_valor_anticipos.id_asiento_cabecera', '<>', null)
            ->orderBy('users.apellido1')
            ->groupBy('users.id')
            ->get();



        Excel::create('Anticipo 1era Quincena Mensual', function ($excel) use ($inf_val_anticip, $anio, $mes, $empresa, $txt_mes) {

            $excel->sheet('ANTICIPO DE QUINCENA', function ($sheet) use ($inf_val_anticip, $anio, $mes, $empresa, $txt_mes) {
                $i = 2;
                $sheet->mergeCells('A1:E1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('ANTICIPOS 1ERA QUINCENA EMPLEADOS');
                    $cell->setFontSize(16);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A' . $i . ':B' . $i);
                $sheet->mergeCells('C' . $i . ':E' . $i);

                // AÑO
                $sheet->cell('A' . $i, function ($cell) use ($anio) {
                    $cell->setValue('AÑO: ' . $anio);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                // MES
                $sheet->cell('C' . $i, function ($cell) use ($txt_mes) {
                    $cell->setValue('MES: ' . $txt_mes);
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;

                $sheet->cell('A' . $i, function ($cell) {
                    $cell->setValue('#');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('B' . $i, function ($cell) {
                    $cell->setValue('NOMBRE');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C' . $i, function ($cell) {
                    $cell->setValue('CARGO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) {
                    $cell->setValue('SUELDO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) {
                    $cell->setValue('VALOR ANTICIPO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#D1F2EB');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;

                $sumaTotal = 0;
                $count = 1;
                foreach ($inf_val_anticip as $value) {
                    $sumaTotal += $value->valor_anticipo;
                    $inf_nomina = null;
                    if (!is_null($value->id_user)) {
                        $user = Ct_Nomina::join('users as u', 'u.id', 'ct_nomina.id_user')
                            ->where('ct_nomina.id_user', $value->id_user)
                            ->where('ct_nomina.id_empresa', $empresa->id)
                            //->where('ct_nomina.estado', '1')
                            ->where('u.estado', '1')
                            ->select('ct_nomina.cargo', 'u.apellido1', 'u.apellido2', 'u.nombre1', 'u.nombre2')
                            ->orderBy('u.apellido1', 'desc')->first();
                        if (is_null($user)) {
                            // dd($value);
                        }
                    }

                    $sheet->cell('A' . $i, function ($cell) use ($count) {
                        $cell->setValue($count);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($user) {
                        $cell->setValue($user->apellido1 . " " . $user->apellido2 . " " . $user->nombre1 . " " . $user->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($user) {
                        $cell->setValue($user->cargo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        $cell->setValue('$ ' . $value->sueldo);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        $cell->setValue('$ ' . $value->valor_anticipo);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    $count++;
                }

                $sheet->cell('D' . $i, function ($cell) use ($value) {
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E' . $i, function ($cell) use ($sumaTotal) {
                    $cell->setValue('$ ' . $sumaTotal);
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('E' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
            });
        })->export('xlsx');
    }


    public function busca_quincena(Request $request)
    {
        $id_empresa      = $request->session()->get('id_empresa');
        $empresa         = Empresa::where('id', $id_empresa)->first();
        $anticipo_qui    = 0;
        $anticipo_valida = 0;
        $anticipo_qui    = Ct_Rh_Valor_Anticipos::where('id_empresa', $id_empresa)->where('anio', $request['anio'])->where('mes', $request['mes'])->where('estado', '1')->get()->count();
        $anticipo_valida = Ct_Valida_Anticipo::where('id_empresa', $id_empresa)->where('anio', $request['anio'])->where('mes', $request['mes'])->where('estado', '1')->get()->count();
        //dd($anticipo_qui);
        if ($anticipo_qui > 0 or $anticipo_valida > 0) {
            return json_encode("existe");
        } else {
            return json_encode("no");
        }
    }

    public function calcular_sueldo_neto($jornada, $horas_dias, $dias_laborados, $sueldo_mensual)
    {
        if ($jornada != '0') { //parcial
            $horas_mes = $horas_dias * $dias_laborados;
            $sueldo_mensual = $sueldo_mensual * $horas_mes / 240;
        } else {
            $sueldo_mensual = $sueldo_mensual / 30;
            $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        }
        $sueldo_mensual = round($sueldo_mensual, 2);

        return $sueldo_mensual;
    }
}
