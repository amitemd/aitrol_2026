<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Comp_Ing_Det_Valores_Recibidos;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Porcentajes_Retencion_Fuente;
use Sis_medico\Ct_Porcentajes_Retencion_Iva;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;
use Sis_medico\Ct_Configuraciones;


class CompIngresoClientesController extends Controller
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

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ct_comp_ing = DB::table('ct_comprobante_ingreso as ct_comp_ing')
            ->where('ct_comp_ing.estado', '1')
            ->select('ct_comp_ing.*')
            ->paginate(10);

        return view('contable/comp_ingreso_cliente/index', ['ct_comp_ing' => $ct_comp_ing]);

    }

    public function crear()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $cuentas   = Plan_Cuentas::where('estado', '2')->get();
        $banco     = Ct_Bancos::where('estado', '1')->get();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $divisas   = Ct_Divisas::where('estado', '1')->get();
        $clientes  = Ct_Clientes::where('estado', '1')->get();
        //$user_vendedor = User::where('id_tipo_usuario', 17)
        //->where('estado', 1)->get();

        $porce_rete_iva    = Ct_Porcentajes_Retencion_Iva::where('estado', '1')->get();
        $porce_rete_fuente = Ct_Porcentajes_Retencion_Fuente::where('estado', '1')->get();

        return view('contable/comp_ingreso_cliente/create', ['divisas' => $divisas, 'clientes' => $clientes, 'porce_rete_iva' => $porce_rete_iva, 'porce_rete_fuente' => $porce_rete_fuente, 'tipo_pago' => $tipo_pago, 'banco' => $banco, 'cuentas' => $cuentas]);

    }

    public function obtener_numero()
    {

        //Obtener el Total de Registros de la Tabla ct_ingreso_clientes
        $contador_ctv = DB::table('ct_comprobante_ingreso')->get()->count();

        if ($contador_ctv == 0) {

            //return 'No Retorno nada';
            $num                     = '1';
            $num_comprobante_ingreso = str_pad($num, 8, "0", STR_PAD_LEFT);
            return $num_comprobante_ingreso;

        } else {

            //Obtener Ultimo Registro de la Tabla ct_ingreso_clientes
            $max_id = DB::table('ct_comprobante_ingreso')->max('id');

            if (($max_id >= 1) && ($max_id < 10)) {
                $nu                      = $max_id + 1;
                $num_comprobante_ingreso = str_pad($nu, 8, "0", STR_PAD_LEFT);
                return $num_comprobante_ingreso;
            }

            if (($max_id >= 10) && ($max_id < 99)) {
                $nu                      = $max_id + 1;
                $num_comprobante_ingreso = str_pad($nu, 8, "0", STR_PAD_LEFT);
                return $num_comprobante_ingreso;
            }

            if (($max_id >= 100) && ($max_id < 1000)) {
                $nu                       = $max_id + 1;
                $num_comprobante_ingresoe = str_pad($nu, 8, "0", STR_PAD_LEFT);
                return $num_comprobante_ingreso;
            }

            if ($max_id == 1000) {
                $num_comprobante_ingreso = $max_id;
                return $num_comprobante_ingreso;
            }

        }

    }

    public function store(Request $request)
    {

        $total_deuda    = $request['total_deudas'];
        $cuent_re_iva   = $request['cuen_cl_iva'];
        $cuent_re_fuent = $request['cuen_cl_fuent'];

        //CAJA GENERAL

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha_actual = Date('Y-m-d H:i:s');
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $variable     = $request['contador'];

        $input = [

            'id_fact_vent'    => $request['identificador_fact'],
            'numero'          => $request['nu_com_ingreso'],
            'tipo'            => $request['tipo'],
            //'id_caja'                   => $request['caja'],
            'divisas'         => $request['divisas'],
            'id_cliente'      => $request['cliente'],
            //'id_vendedor'               => $request['vendedor'],
            'fecha'           => $request['fecha'],
            //'faltante'                  => $request['faltante'],
            //'sobrante'                  => $request['sobrante'],
            'rf_iva'          => $request['rfiva'],
            'tipo_r_iva'      => $request['tipo_riva'],
            'rf_renta'        => $request['rf_renta'],
            'tipo_r_fuente'   => $request['tipo_rfuente'],
            'total_ingreso'   => $request['total_ingresos'],
            'total_deudas'    => $request['total_deudas'],
            'total_credito'   => $request['total_credito'],
            'total_abonos'    => $request['total_abonos'],
            'nuevo_saldo'     => $request['nuevo_saldo'],
            //'deficit_ingreso'           => $request['deficit_ingresos'],
            //'superavit'                 => $request['superavit'],
            'autollenar'      => $request['autollenar'],
            //'observaciones'             => $request['observaciones'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $id_comp_ingreso = Ct_Comprobante_Ingreso::insertGetId($input);

        for ($i = 0; $i < $variable; $i++) {

            Ct_Comp_Ing_Det_Valores_Recibidos::create([
                'id_comp_ing'     => $id_comp_ingreso,
                'id_tipo_pago'    => $request['id_tip_pago' . $i],
                'fecha'           => $request['fecha' . $i],
                'numero'          => $request['numero' . $i],
                'id_banco'        => $request['id_banco' . $i],
                //'tiempo'                   => $request['tiempo' . $i],
                'id_cuenta'       => $request['id_cuenta' . $i],
                //'identificacion_cliente'   => $request['girador' . $i],
                //'id_divisas'               => $request['id_divisa' . $i],
                'valor'           => $request['valor' . $i],
                'valor_base'      => $request['valor_base' . $i],
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

        }

        //REGISTRO DE ASIENTO DIARIO.
        //SE GUARDA EN  LA TABLA CABECERA DEL ASIENTO.

        $input_cabecera = [
            'id_comp_ingreso'     => $id_comp_ingreso,
            'observacion'         => $request['autollenar'],
            'fecha_asiento'       => $fecha_actual,
            'num_ingreso_cliente' => $request['nu_com_ingreso'],
            'valor'               => $request['total_deudas'],
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
        ];

        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

        $total_deuda = $request['total_deudas'];
        $nuev_sal    = $request['nuevo_saldo'];

        //CAJA GENERAL
        if ($nuev_sal > 0) {

            $valor_rfiva = $request['rfiva'];
            if ($valor_rfiva > 0) {

                $plan_cuentas = Plan_Cuentas::where('id', $cuent_re_iva)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_actual,
                    'debe'                => $valor_rfiva,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

            }

            $valor_rf_renta = $request['rf_renta'];
            if ($valor_rf_renta > 0) {

                $plan_cuentas = Plan_Cuentas::where('id', $cuent_re_fuent)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->id,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_actual,
                    'debe'                => $valor_rf_renta,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

            }

            $cuenta_comp_ingreso = Ct_Configuraciones::obtener_cuenta('COMPINGRESOCLIENTE_CAJA_GENERAL');
            //$plan_cuentas = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_comp_ingreso->cuenta_guardar,
                'descripcion'         => $cuenta_comp_ingreso->nombre_mostrar,
                'fecha'               => $fecha_actual,
                'debe'                => $nuev_sal,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);

        } elseif ($total_deuda > 0) {
            $cuenta_comp_ingreso = Ct_Configuraciones::obtener_cuenta('COMPINGRESOCLIENTE_CAJA_GENERAL');
            //$plan_cuentas = Plan_Cuentas::where('id', '1.01.01.1.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_comp_ingreso->cuenta_guardar,
                'descripcion'         => $cuenta_comp_ingreso->nombre_mostrar,
                'fecha'               => $fecha_actual,
                'debe'                => $total_deuda,
                'haber'               => '0',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);

        }

        //CUENTAS POR COBRAR
        if ($total_deuda > 0) {

            $cuenta_c_cobrar_clientes = Ct_Configuraciones::obtener_cuenta('COMPINGRESOCLIENTES_CUENTAS_COBRAR');
            //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.05.01')->first();

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_c_cobrar_clientes->cuenta_guardar,
                'descripcion'         => $cuenta_c_cobrar_clientes->nombre_mostrar,
                'fecha'               => $fecha_actual,
                'debe'                => '0',
                'haber'               => $total_deuda,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,

            ]);

        }

    }

    //Obtenemos el Ruc o Cedula del Cliente Seleccionado
    public function buscar_identificacion(Request $request)
    {

        $id_cliente   = $request['cliente'];
        $data_cliente = null;
        $clientes     = DB::table('ct_clientes')->where('identificacion', $id_cliente)->first();
        if (!is_null($clientes)) {

            $client_identificacion = $clientes->identificacion;

            return ['client_identificacion' => $client_identificacion];

        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    //Obtenemos la Cedula del Vendedor Seleccionado
    public function buscar_identificacion_vendedor(Request $request)
    {

        $id_vendedor = $request['vendedor'];

        $user_vendedor = User::where('id', $id_vendedor)
            ->where('estado', 1)->first();

        if (!is_null($user_vendedor)) {

            $vendedor_cedula = $user_vendedor->id;

            return ['vendedor_cedula' => $vendedor_cedula];

        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    //Obtenemos el numero Factura completo
    public function completar_numero(Request $request)
    {
        $numero              = $request['term'];
        $data                = array();
        $numeros_fact_ventas = DB::table('ct_ventas')->where('numero', 'like', '%' . $numero . '%')->where('estado', '1')->get();

        foreach ($numeros_fact_ventas as $num_fact_vent) {
            $data[] = array('value' => $num_fact_vent->numero);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }

    }

    public function buscar_factura_numero(Request $request)
    {

        $num_fact_venta = $request['num_factura'];

        $obt_datos_factura = DB::table('ct_ventas as ct_vent')
            ->join('ct_asientos_cabecera as ct_ac', 'ct_ac.id_ct_ventas', 'ct_vent.id')
            ->join('ct_clientes as ct_cli', 'ct_cli.identificacion', 'ct_vent.id_cliente')
            ->where('ct_vent.numero', $num_fact_venta)
            ->select('ct_vent.id', 'ct_vent.numero', 'ct_ac.id as numero_asiento', 'ct_cli.identificacion as identificacion_cliente', 'ct_vent.nro_comprobante as numero_comprobante', 'ct_vent.fecha as fecha_factura', 'ct_vent.tipo as tipo_comprobante', 'ct_vent.procedimientos as detalle_procedimiento', 'ct_vent.total_final as valor_cobrar', 'ct_vent.subtotal_12 as sub_total12', 'ct_vent.subtotal_0 as sub_total0', 'ct_vent.impuesto as impuesto_cobrado')
            ->where('ct_vent.estado', '1')->first();

        if ($obt_datos_factura != '[]') {

            $data = [$obt_datos_factura->id,
                $obt_datos_factura->numero,
                $obt_datos_factura->numero_asiento,
                $obt_datos_factura->identificacion_cliente,
                $obt_datos_factura->numero_comprobante,
                $obt_datos_factura->fecha_factura,
                $obt_datos_factura->tipo_comprobante,
                $obt_datos_factura->detalle_procedimiento,
                $obt_datos_factura->valor_cobrar,
                $obt_datos_factura->sub_total12,
                $obt_datos_factura->sub_total0,
                $obt_datos_factura->impuesto_cobrado,
            ];

            return $data;
        } else {
            return ['value' => 'no resultados'];
        }

    }

    public function calculo_porcentaje_iva(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_identificador = $request['id_tip_ret_iva'];

        $valor_porcent = DB::table('ct_porcentajes_retencion_iva as ct_pr_iva')
            ->where('ct_pr_iva.id', $id_identificador)
            ->select('ct_pr_iva.valor as val_iva', 'ct_pr_iva.cuenta_clientes as cuent_client_iva')
            ->where('ct_pr_iva.estado', '1')->first();

        if ($valor_porcent != '[]') {

            $data = [$valor_porcent->val_iva,
                $valor_porcent->cuent_client_iva];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }

    }

    public function calculo_porcentaje_retencion_fuente(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_identificador = $request['id_tip_ret_fuente'];

        $valor_porcent = DB::table('ct_porcentajes_retencion_fuente as ct_pr_f')
            ->where('ct_pr_f.id', $id_identificador)
            ->select('ct_pr_f.valor as val_fuente', 'ct_pr_f.cuenta_clientes as cuent_client_fuent')
            ->where('ct_pr_f.estado', '1')->first();

        if ($valor_porcent != '[]') {

            $data = [$valor_porcent->val_fuente,
                $valor_porcent->cuent_client_fuent];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }

    }

    public function editar()
    {

    }

}
