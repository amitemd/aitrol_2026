<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Nomina;
use Sis_medico\Empresa;
use Sis_medico\Ct_Rh_Saldos_Iniciales;
use Sis_medico\User;
use Sis_medico\Ct_Cierre_Cabecera;

class SaldoInicialEmpleadosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }

    /*************************************************
     *************CREAR SALDO INICIAL*****************
    /*************************************************/
    public function crear_saldo_inicial($id_nomina,$cedula)
    {
        $nombre_completo = '';
        $inf_usuario = User::where('id',$cedula)->first();
        
        if(!is_null($inf_usuario)){
          $nombre_completo = $inf_usuario->nombre1." ".$inf_usuario->nombre2." ".$inf_usuario->apellido1." ".$inf_usuario->apellido2;
        }
        
        $id_i = $cedula;
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empl_nomina = Ct_Nomina::findorfail($id_nomina);

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('contable.rh_saldos_iniciales.modal_saldoinicial', ['min_date'=>$min_date,'empl_nomina' => $empl_nomina,'nombre_completo'=>$nombre_completo,'tipo_pago_rol' => $tipo_pago_rol,'sueldo_neto' => $empl_nomina->sueldo_neto]);
    }


    /*************************************************
    ***************GUARDA SALDO INICIAL***************    
    /*************************************************/ 
   public function store_saldo_inicial(Request $request)
   {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;

            $fech_creacion = $request['fecha_creacion'];
            
            $mes_inicio_cobro = $request['pmes_cobro'];
            $anio_1mes_cobro = $request['anio_pmes_cobro'];
            $mes_fin_cobro = $request['mes_fcobro'];
            $anio_fin_cobro = $request['anio_fcobro'];

            $val_saldo = $request['valor_saldo'];
            $cuotas = $request['cuotas'];
            $val_cuotas = $request['valor_cuotas'];

            $id_empresa = $request->session()->get('id_empresa');
            $empresa    = Empresa::where('id', $id_empresa)->first();

            // Verificar el cierre
            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fech_creacion));
            //dd($verifica_cierre);
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }

            try {
            $input_saldo_inicial = [

                'id_empl' => $request['id_empl'],
                'nombres' =>$request['nombres'],
                'id_empresa' => $id_empresa,
                'fecha_creacion' => $request['fecha_creacion'],
                'saldo_inicial' => $request['valor_saldo'],
                'tipo_rol' => $request['tipo_rol'],
                'num_cuotas' => $request['cuotas'],
                'valor_cuota' => $request['valor_cuotas'],
                'mes_inicio_cobro' => $request['pmes_cobro'],
                'anio_inicio_cobro' => $request['anio_pmes_cobro'],
                'mes_fin_cobro' => $request['mes_fcobro'],
                'anio_fin_cobro' => $request['anio_fcobro'],
                'observacion' => $request['observ_saldo'],
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'saldo_res' => $request['valor_saldo'],

            ];

            $id_saldo_inicial = Ct_Rh_Saldos_Iniciales::insertGetId($input_saldo_inicial);

            return ['status' => 'success', 'msj' => 'Guardado con exito','id_saldo_inicial' => $id_saldo_inicial];
        } catch (\Exception $ex) {
            return ['status' =>'error', 'msj' => $ex->getMessage(), 'line' => $ex->getLine()];
        }
   }

}
