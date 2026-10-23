<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Empresa;
use Sis_medico\Ct_Nomina;
use Sis_medico\User;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Rh_Otros_Anticipos;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Rh_Anticipos;
use Sis_medico\Numeros_Letras;
use Sis_medico\Plan_Cuentas;
use Excel;
use Google\Service\CloudSearch\Principal;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios;
use Sis_medico\LogAsiento;

class OtrosAnticiposEmpleadosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }

    /***********************************************/
    /*********CREAR OTROS ANTICIPOS EMPLEADOS*******/
    /***********************************************/
    public function crear_otros_anticipo_empleado($id_nomina,$cedula, Request $request)
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
        $id_empresa = $request->session()->get('id_empresa');
        $empl_nomina = Ct_Nomina::findorfail($id_nomina);

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $lista_banco = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();

        return view('contable.rol_otros_anticipo_empleado.modal_otros_anticipos', ['cargos'=>$empl_nomina->cargo,'id_i'=>$id_i,'nombre_completo'=>$nombre_completo,'tipo_pago_rol' => $tipo_pago_rol,'lista_banco' => $lista_banco,'bancos' => $bancos,'empl_nomina' => $empl_nomina,'sueldo_neto' => $empl_nomina->sueldo_neto]); 

    }


    /*************************************************
    *********GUARDA OTROS ANTICIPO EMPLEADO***********    
    /*************************************************/  
    
    public function store_otros_anticipo_empleado(Request $request)
    {
        
        $contador_ct_otr_ant = DB::table('ct_rh_otros_anticipos')->get()->count();
        //dd($contador_ct_otr_ant);
        $numero_otro_anticipo = 0; 

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $mont_anticipo = $request['monto_anticipo'];
        $fech_creacion = $request['fecha_creacion'];

        $verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_creacion);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }

        DB::beginTransaction();
        try {

            $anio_cobro = $request['anio_pmes_cobro'];
            $mes_cobro = $request['pmes_cobro'];

        $cuent_sal = $request['cuenta_saliente'];
        $caja_banco = Ct_Caja_Banco::find($request['cuenta_saliente']);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        //Obtenemos el Mes de Inicio
        $txt_mes_cobro ='';
        if($mes_cobro == '12'){
            $txt_mes_cobro = 'DICIEMBRE';    
        }elseif($mes_cobro == '11'){
            $txt_mes_cobro = 'NOVIEMBRE';
        }elseif($mes_cobro == '10'){
            $txt_mes_cobro = 'OCTUBRE';
        }elseif($mes_cobro == '9'){
            $txt_mes_cobro = 'SEPTIEMBRE';
        }elseif($mes_cobro == '8'){
            $txt_mes_cobro = 'AGOSTO';
        }elseif($mes_cobro == '7'){
            $txt_mes_cobro = 'JULIO';
        }elseif($mes_cobro == '6'){
            $txt_mes_cobro = 'JUNIO';
        }elseif($mes_cobro == '5'){
            $txt_mes_cobro = 'MAYO';
        }elseif($mes_cobro == '4'){
            $txt_mes_cobro = 'ABRIL';
        }elseif($mes_cobro == '3'){
            $txt_mes_cobro = 'MARZO';
        }elseif($mes_cobro == '2'){
            $txt_mes_cobro = 'FEBRERO';
        }elseif($mes_cobro == '1'){
            $txt_mes_cobro = 'ENERO';
        }

        if($contador_ct_otr_ant == 0){
            $num = '1';
            $numero_otro_anticipo = str_pad($num, 9, "0", STR_PAD_LEFT);
        }else{
            
            $max_id = DB::table('ct_rh_otros_anticipos')->where('id_empresa',$id_empresa)->latest()->first();
            if (!is_null($max_id)) {
                $max_id = intval($max_id->secuencia);
                if(strlen($max_id)<10){
                    $nu = $max_id+1;
                    $numero_otro_anticipo = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }else{
                $n = '1';
                $numero_otro_anticipo = str_pad($n, 9, "0", STR_PAD_LEFT);
            }
            
        
        } 

        $numero_otro_anticipo = LogAsiento::getSecuencia(2);

        $text  = 'Otros Anticipos'.':'.' '.'Año Cobro Anticipo'.':'.$anio_cobro.' '.'Mes Cobro Anticipo'.':'.$txt_mes_cobro.' '.'Valor'.':'.$mont_anticipo.':'.$request['concepto'];

        /************************************
         *****Inserta Ct_Asientos_Cabecera***
        /************************************/
        $input_cabecera = [
            
            'fecha_asiento'   => $fech_creacion,
            'fact_numero'     => $numero_otro_anticipo,
            'id_empresa'      => $id_empresa,
            'observacion'     => $text,
            'valor'           => $mont_anticipo,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        
        ];
        
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        
        $input_otro_anticipo = [
           
            'id_empl' => $request['id_empl'],
            'nombres' =>$request['nombre'],
            'id_empresa' => $request['id_empresa'],
            'monto_anticipo' => $request['monto_anticipo'],
            'fecha_creacion' => $request['fecha_creacion'],
            'tipo_rol' => $request['tipo_rol'],
            'mes_cobro_anticipo' => $request['pmes_cobro'],
            'anio_cobro_anticipo' => $request['anio_pmes_cobro'],
            'id_tipo_pago' => $request['tipo_pago'],
            'concepto' => $request['concepto'],
            'num_cuenta_benef' => $request['numero_cuenta'],
            'banco_beneficiario' => $request['banco'],
            'cuenta_saliente' => $caja_banco->cuenta_mayor,
            'num_cheque' => $request['numero_cheque'],
            'fecha_cheque' => $request['fecha_cheque'],
            'id_asiento_cabecera' => $id_asiento_cabecera,
            //'id_asiento_cabecera' => 1,
            'secuencia'       => $numero_otro_anticipo,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente
        
        ];

        $id_otro_anticipo = Ct_Rh_Otros_Anticipos::insertGetId($input_otro_anticipo);
        
        /************************************
        *****Inserta Ct_Asientos_Detalle*****
        /************************************/ 

        if ($mont_anticipo > 0){

            $plan_cuentas= Plan_Cuentas::where('id',$caja_banco->cuenta_mayor)->first();
            
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $caja_banco->cuenta_mayor,
                'descripcion'                   => $plan_cuentas->nombre,
                'fecha'                         => $fech_creacion,
                'debe'                          => '0',
                'haber'                         => $mont_anticipo,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            
            ]); 

        }
        
        $id_empresa = $request->session()->get('id_empresa');
        //1.01.02.03.01
            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');

            //$plan_cuentas = Plan_Cuentas::where('id','1.01.02.06.04')->first();
        if ($mont_anticipo > 0){


            
            
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
                //'id_plan_cuenta'                => '1.01.02.06.04',
                'id_plan_cuenta'                => $plan_cuentas->cuenta_guardar,
                'descripcion'                   => $plan_cuentas->nombre_mostrar,
                'fecha'                         => $fech_creacion,
                'debe'                          => $mont_anticipo,
                'haber'                         => '0',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            
            ]); 

        }

            $arr_egresos_varios =[
                'id_caja_banco'         =>  $cuent_sal,
                'id_asiento_cabecera'   =>  $id_asiento_cabecera,
                'id_empresa'            =>  $id_empresa,
                'fecha_comprobante'     =>  $fech_creacion,
                'estado'                =>  '1',
                'id_secuencia'          =>  'null',
                'descripcion'           =>  $request['concepto'],
                'beneficiario'          =>  $request['nombre'],
                'valor'                 =>  $mont_anticipo,
                'check'                 =>  '0',
                'nota'                  =>  $request['concepto'],
                'secuencia'             =>  $numero_otro_anticipo,
                'nro_cheque'            =>  $request['numero_cheque'],
                'fecha_cheque'          =>  $request['fecha_cheque'],
                'girado'                =>  $request['nombre'],
                'id_usuariocrea'        =>  $idusuario,
                'id_usuariomod'         =>  $idusuario,
                'ip_creacion'           =>  $ip_cliente,
                'ip_modificacion'       =>  $ip_cliente,
            ];

            $id_egresos_varios = Ct_Comprobante_Egreso_Varios::insertGetId($arr_egresos_varios);

            //$plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS ANTICIPOS EMPLEADOS ');
            //$plan_cuentas = Plan_Cuentas::where('id','1.01.02.06.04')->first();

            Ct_Detalle_Comprobante_Egreso_Varios::create([
                'id_comprobante_varios'          => $id_egresos_varios,
                //'codigo'                         => '1.01.02.06.04',
                'codigo'                         => $plan_cuentas->cuenta_guardar,
                'cuenta'                         => $plan_cuentas->nombre_mostrar,
                'descripcion'                    => $request['concepto'],
                'debe'                           => $mont_anticipo,
                'id_secuencia'                   => $numero_otro_anticipo,
                'estado'                         => '1',
                'ip_creacion'                    => $ip_cliente,
                'ip_modificacion'                => $ip_cliente,
                'id_usuariocrea'                 => $idusuario,
                'id_usuariomod'                  => $idusuario,
            ]);

           

            //return $id_otro_anticipo;

            DB::commit();
            //$inventario = Inventario2::build_process('C', $id_compras, $id_empresa);
            //dd($inventario);
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito', 'id_otro_anticipo' => $id_otro_anticipo];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
        //return "ok";

    }

    /************************************************
	*********ANTICIPOS A EMPLEADOS INDEX*************
    /************************************************/
    public function index_anticipos(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        
        $principales = Ct_Rh_Anticipos::where('estado', '1')->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(5);
  
        //$empresas = Empresa::all();

        return view('contable.rol_anticipo_quincena.index_anticipos', ['registros' => $principales,'empresa' => $empresa]); 


    }

    
    /*************************************************
    ************BUSCAR ANTICIPO EMPLEADO**************
    /*************************************************/
    public function search_anticipo(Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'id_empl'      => $request['identificacion'],
            'estado'       => 1,
            'id_empresa'   => $id_empresa,
            'nombres'    => $request['buscar_nombre'],
           
        ];

        $registros = $this->doSearchingQuery($constraints);
        //$empresas = Empresa::all();

        return view('contable.rol_anticipo_quincena.index_anticipos', ['request' => $request, 'empresa' => $empresa, 'registros' => $registros, 'searchingVals' => $constraints]);

   }


    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Rh_Anticipos::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                
                $query = $query->where('estado', '1')->where($fields[$index], 'like', '%' . $constraint . '%');

            }

            $index++;
        }

        return $query->paginate(5);
    }

    private function doSearchingQuery2($constraints)
    {
        //dd($constraints);
        $query  = Ct_Rh_Otros_Anticipos::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $key => $constraint) {
            if($key == 'mes_cobro_anticipo'){
                if($constraint > 0){
                    $query = $query->where('estado', '1')->where($fields[$index],  $constraint );
                }
            }else{
                if ($constraint != null) {  
                    $query = $query->where('estado', '1')->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }
                
            $index++;
        }

        return $query->get();
    }

    public function pdf_otros_anticipo($id_anticipo,Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $anticip_empleado = Ct_Rh_Otros_Anticipos::where('estado', '1')
                                                   ->where('id_empresa',$id_empresa)
                                                   ->where('id', $id_anticipo)->first();

        $empresa = Empresa::where('estado', '1')
                            ->where('id',$anticip_empleado->id_empresa)->first();

        $letras= new Numeros_Letras(); 

        //CONVIERTE EL VALOR EN LETRAS
        $total_str = $letras->convertir(number_format($anticip_empleado->monto_anticipo,2,'.',''),"DOLARES","CTVS");

        $asiento_cabecera = Ct_Asientos_Cabecera::where('id',$anticip_empleado->id_asiento_cabecera)->first();

        $asiento_detalle= Ct_Asientos_Detalle::where('estado','1')
                            ->where('id_asiento_cabecera',$asiento_cabecera->id)->get();

        $caj_banc = Ct_Caja_Banco::where('estado', '1')
                                   ->where('cuenta_mayor',$anticip_empleado->cuenta_saliente)
                                   ->first();

        $view = \View::make('contable.rol_otros_anticipo_empleado.pdf_otros_anticipo', compact('anticip_empleado','empresa','total_str','asiento_cabecera','asiento_detalle','caj_banc'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Anticipo Comprobante' . $id_anticipo . '.pdf');
    }

    public function index(Request $request)  {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $meses = date('m');
        $anios = date('Y');
        $principales = Ct_Rh_Otros_Anticipos::where('estado', '1')->where('id_empresa', $id_empresa)->orderby('id', 'desc')
        ->where('anio_cobro_anticipo',$anios)->where('mes_cobro_anticipo',$meses)->get();

        
        $constraints = [
            'id_empl'    => null,
            'estado'     => 1,
            'id_empresa' => null,
            'nombres'    => $request['nombre'],
            'mes_cobro_anticipo'      => $meses, 
            'anio_cobro_anticipo'     => $anios    
        ];

        return view('contable.rol_otros_anticipo_empleado.index_busqueda', ['principales' => $principales, 'empresa' => $empresa, 'searchingVals' => $constraints]);

   }

   public function search_otros_anticipos(Request $request)  {
 
    if ($this->rol()) {
        return response()->view('errors.404');
    }

    $id_empresa = $request->session()->get('id_empresa');
    $empresa    = Empresa::where('id', $id_empresa)->first();
    $principales = Ct_Rh_Otros_Anticipos::where('estado', '1')->where('id_empresa', $id_empresa)->orderby('id', 'desc')->get();

    $meses = $request['mes'];
    if($meses == null){
        $meses = date('m');
    }   

    $anios = $request['anio'];
    if($anios == null){
        $anios = date('Y');
    }    

    $constraints = [
        'id_empl'    => $request['identificacion'],
        'estado'     => 1,
        'id_empresa' => $id_empresa,
        'nombres'    => $request['nombre'],
        'mes_cobro_anticipo'      => $meses, 
        'anio_cobro_anticipo'     => $anios    
    ];

    $principales = $this->doSearchingQuery2($constraints);

    if(Auth::user()->id == '0951561075'){
        //dd($principales);
    }
    

    return view('contable.rol_otros_anticipo_empleado.index_busqueda', ['request' => $request, 'empresa' => $empresa, 'principales' => $principales, 'searchingVals' => $constraints]);

  }
  
  public function modal_ver_anticipos(Request $request, $id_nomina,$cedula)
  {
      $id_empresa = $request->session()->get('id_empresa');
       $empresa    = Empresa::where('id', $id_empresa)->first();
      
      $nombre_completo = '';
      $inf_usuario = User::where('id',$cedula)->first();
      
      if(!is_null($inf_usuario)){
        $nombre_completo = $inf_usuario->nombre1." ".$inf_usuario->nombre2." ".$inf_usuario->apellido1." ".$inf_usuario->apellido2;
      }

      $id_i = $cedula;
           
      $empl_nomina = Ct_Nomina::where('id_user',$cedula)->where('id_empresa',$id_empresa)->first();

      $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

      $lista_banco = Ct_Bancos::all();

      $bancos = Ct_Caja_Banco::where('estado', '1')->get();
      $otros_anticipos = Ct_Rh_Otros_Anticipos::find($id_nomina);
      
      //dd($empl_nomina);


      return view('contable/rol_otros_anticipo_empleado/modal_ver_anticipos', ['cargos'=>$empl_nomina->cargo,'id_i'=>$id_i,'nombre_completo'=>$nombre_completo,'tipo_pago_rol' => $tipo_pago_rol,'lista_banco' => $lista_banco,'bancos' => $bancos,'empl_nomina' => $empl_nomina,'sueldo_neto' => $empl_nomina->sueldo_neto, 'otros_anticipos'=> $otros_anticipos ]); 

  }
  public function modal_editar_anticipos(Request $request, $id_nomina, $cedula)
  {
      $id_empresa = $request->session()->get('id_empresa');
      $empresa    = Empresa::where('id', $id_empresa)->first();
      
      $nombre_completo = '';
      $inf_usuario = User::where('id',$cedula)->first();
      
      if(!is_null($inf_usuario)){
        $nombre_completo = $inf_usuario->nombre1." ".$inf_usuario->nombre2." ".$inf_usuario->apellido1." ".$inf_usuario->apellido2;
      }

      $id_i = $cedula;
           
      $empl_nomina = Ct_Nomina::where('id_user',$cedula)->where('id_empresa',$id_empresa)->first();

      $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

      $lista_banco = Ct_Bancos::all();

      $bancos = Ct_Caja_Banco::where('estado', '1')->get();
      $otros_anticipos = Ct_Rh_Otros_Anticipos::find($id_nomina);
      
      //dd($empl_nomina);


      return view('contable/rol_otros_anticipo_empleado/modal_editar_anticipos', ['cargos'=>$empl_nomina->cargo,'id_i'=>$id_i,'nombre_completo'=>$nombre_completo,'tipo_pago_rol' => $tipo_pago_rol,'lista_banco' => $lista_banco,'bancos' => $bancos,'empl_nomina' => $empl_nomina,'sueldo_neto' => $empl_nomina->sueldo_neto, 'otros_anticipos'=> $otros_anticipos]);

  }


}
