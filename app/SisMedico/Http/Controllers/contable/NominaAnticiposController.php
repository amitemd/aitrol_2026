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
use Sis_medico\Ct_Rh_Anticipos;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Numeros_Letras;
use Sis_medico\Ct_Rh_Otros_Anticipos;
use Sis_medico\Plan_Cuentas;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ct_Configuraciones;

class NominaAnticiposController extends Controller
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

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empresas = Empresa::all();
        
        return view('contable.rol_anticipo_quincena.index',['empresas' => $empresas]);
    }

    public function obtener_anticipo_quincena(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id; 

        //$id_mes = $request['mes'];
        $id_empresa = $request['id_empresa'];
        $id_anio = $request['year'];
        $id_mes = $request['mes'];
        $valor_porcentaje = $request['valor_porcent'];

        
        
        //dd($id_empresa);      

        /*$empl_rol = DB::table('ct_nomina')
        ->join('ct_rol_pagos','ct_rol_pagos.id_nomina','=','ct_nomina.id')
        ->where('ct_rol_pagos.id_empresa', $id_empresa)
        ->where('ct_rol_pagos.mes', $id_mes)
        ->select('ct_nom$rol_pag = Ct_Rol_Pagos::where('estado','1')->where('id_nomina', $id_nomina)->orderby('id', 'asc')->paginate(10);ina.id_user as nombre','ct_rol_pagos.id_empresa as empresa','ct_rol_pagos.mes as mes','ct_nomina.sueldo_neto as sueldo')->get();*/

        //$empl_rol = Ct_Nomina::where('estado','1')->where('id_empresa', $id_empresa)->orderby('id', 'asc')->paginate(5);
        $empl_rol = Ct_Nomina::where('estado','1')->where('id_empresa', $id_empresa)->orderby('id', 'asc')->get();
        dd($empl_rol);

        if (!is_null($empl_rol)) {
        
            foreach($empl_rol as $value){
                
                //$valor_anticip = round(((($value->sueldo_neto)*($valor_porcentaje))/100),2);
                $valor_anticip = (($value->sueldo_neto)*($valor_porcentaje))/100;
         
                $input = [
                    'id_user'                  => $value->id_user,
                    'id_empresa'               => $value->id_empresa,
                    'anio'                     => $id_anio,
                    'quincena'                 => $id_mes,
                    'quincena'                 => $id_mes,
                    'sueldo'                   => $value->sueldo_neto,
                    'porcentaje'               => $valor_porcentaje,
                    'valor_anticipo'           => $valor_anticip,
                    'ip_creacion'              => $ip_cliente,
                    'ip_modificacion'          => $ip_cliente,
                    'id_usuariocrea'           => $id_usuario,
                    'id_usuariomod'            => $id_usuario
                ];

                Ct_Rh_Valor_Anticipos::create($input);
            }

        }
                        
        return view('contable.rol_anticipo_quincena.resultado_anticipo',['empl_rol' => $empl_rol,'valor_porcentaje' => $valor_porcentaje,'id_empresa' => $id_empresa,'id_anio' => $id_anio,'id_mes' => $id_mes]);                 
    
    }

    public function buscar_anticipos_quincena(Request $request)
    {
        
        $id_empresa = $request['id_empresa'];
        $id_anio = $request['year'];
        $id_mes = $request['mes'];

        $anticip_quince = Ct_Rh_Valor_Anticipos::where('estado','1')
                                                ->where('id_empresa', $id_empresa)
                                                ->where('anio', $id_anio)
                                                ->where('mes', $id_mes)
                                                ->orderby('id', 'asc')->get();

        
        
        
        return view('contable.rol_anticipo_quincena.buscador_anticipos',['anticip_quince' => $anticip_quince]);
     
    }

    public function edit_anticipo($id,$idempleado,$idempresa)
    {
        
        $obtener_sueldo = Ct_Nomina::where('estado','1')
                                    ->where('id_user', $idempleado)
                                    ->where('id_empresa', $idempresa)
                                    ->first();
        
        $anticip_quincena = Ct_Rh_Valor_Anticipos::find($id);

        return view('contable.rol_anticipo_quincena.edit_anticipos', ['anticip_quincena' => $anticip_quincena,'obtener_sueldo' => $obtener_sueldo]);

    }

    private function validateInput2($request){

        $rules = [

            'porcentaje_quincena' => 'required',
        
        ];
         
        $messages= [
            
            'porcentaje_quincena.required' => 'Ingrese el Porcentaje del Anticipo.',
        ]; 

        $this->validate($request, $rules, $messages);   

    }
    
    
    public function update(Request $request)
    {

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id_anticipo =  $request['id_anticip_val'];
        $sueld_net =  $request['sueldo_mensual'];
        $porc_quincen =  $request['porcentaje_quincena'];

        $this->validateInput2($request);

        $valor_anticip = (($sueld_net)*($porc_quincen))/100;


        $input = [
            
            'sueldo' => $request['sueldo_mensual'],
            'porcentaje' => $request['porcentaje_quincena'],
            'valor_anticipo' => $valor_anticip,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente

        ];


        Ct_Rh_Valor_Anticipos::where('id', $id_anticipo)->update($input);

    }

    public function obtener_reporte_quincena(Request $request)
    {
        $valor_porcentaje = $request['valor_porcent'];
        $id_empresa = $request['id_empresa'];
        $id_anio = $request['year'];
        $id_mes = $request['mes'];

        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $anticip_quince = Ct_Rh_Valor_Anticipos::where('estado','1')
                                           ->where('id_empresa', $id_empresa)
                                           ->where('anio', $id_anio)
                                           ->where('mes', $id_mes)
                                           ->orderby('id', 'asc')->get();

        $fecha_d = date('Y/m/d'); 
        Excel::create('Calculo de Anticipo Quincena-'.$fecha_d, function($excel) use($anticip_quince,$empresa,$valor_porcentaje){
            
            $excel->sheet('Anticipo Quincena', function($sheet) use($anticip_quince,$empresa,$valor_porcentaje){
                $fecha_d = date('Y/m/d');
                $i = 5;

                $sheet->mergeCells('A1:H1');
               
                $mes = substr($fecha_d, 5, 2); 
                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' del '.substr($fecha_d, 0, 4);
                $sheet->cell('A1', function($cell) use($fecha2){
                    $cell->setValue('ANTICIPO QUINCENA EMPLEADOS'.' - '.$fecha2);
                    $cell->setFontSize('15');
                    $cell->setFontWeight('bold');
                    //$cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A1:H1', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->mergeCells('A2:H2');
                $sheet->cell('A2', function($cell) use ($empresa){
                    // manipulate the cel
                    if(!is_null($empresa)){
                    $cell->setValue($empresa->nombrecomercial);
                    }
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->cells('A2:H2', function($cells) {
                    $cells->setBackground('#3383FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('A3:H3');
                $sheet->cell('A3', function($cell) use ($empresa){
                    // manipulate the cel
                    if(!is_null($empresa)){
                     $cell->setValue($empresa->id);
                    }
                    $cell->setFontWeight('bold'); 
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->cells('A3:H3', function($cells) {
                    $cells->setBackground('#3383FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->cells('A1:K3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cell('A4', function($cell) {
                    $cell->setValue('IDENTIFICACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin'); 
                });
                
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES Y APELLIDOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });
                
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EMPRESA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });

                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('AÑO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });

                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('QUINCENA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });

                $sheet->cell('F4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SUELDO MENSUAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });

                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('% PORCENTAJE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });

                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR ANTICIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });
                
                $sheet->cells('A4:H4', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                // FORMATEO DE COLUMNAS F Y H A DOS DECIMALES
                $sheet->setColumnFormat(array(
                    'F' => '0.00', 
                    'H' => '0.00', 
                ));

                foreach($anticip_quince as $value){
                    $txtcolor='#000000';
                    
                    $empresa = null;
                    $usuario = null;
                    
                    if($value->id_user !=null){
                        $usuario = User::find($value->id_user);
                        $nombre_paciente = $usuario->nombre1 . " ";

                        if ($usuario->nombre2 != '(N/A)') {
                            $nombre_paciente = $nombre_paciente . $usuario->nombre2 . " ";
                        }

                        $nombre_paciente = $nombre_paciente . $usuario->apellido1 . " ";
                        if ($usuario->apellido2 != '(N/A)') {
                            $nombre_paciente = $nombre_paciente . $usuario->apellido2 . " ";
                        }
                    }
                    if($value->id_empresa!=null){
                        $empresa = Empresa::find($value->id_empresa);
                    }

                    if($value->sueldo > 0){
                        $valor = (($value->sueldo)*($valor_porcentaje))/100;
                    }

                    $sheet->cell('A'.$i, function($cell) use($value,$nombre_paciente, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->id_user);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('B'.$i, function($cell) use($nombre_paciente, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($nombre_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('C'.$i, function($cell) use($empresa, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($empresa->nombrecomercial);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('D'.$i, function($cell) use($value,$txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->anio);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E'.$i, function($cell) use($value,$txtcolor){
                        // manipulate the cel
                        $mes='';
                        
                        if($value->quincena=='1'){
                            $mes = 'ENERO';    
                        }elseif($value->quincena=='2'){
                            $mes = 'FEBRERO';
                        }elseif($value->quincena=='3'){
                            $mes = 'MARZO';
                        }elseif($value->quincena=='4'){
                            $mes = 'ABRIL';
                        }elseif($value->quincena=='5'){
                            $mes = 'MAYO';
                        }elseif($value->quincena=='6'){
                            $mes = 'JUNIO';
                        }elseif($value->quincena=='7'){
                            $mes = 'JULIO';
                        }elseif($value->quincena=='8'){
                            $mes = 'AGOSTO';
                        }elseif($value->quincena=='9'){
                            $mes = 'SEPTIEMBRE';
                        }elseif($value->quincena=='10'){
                            $mes = 'OCTUBRE';
                        }elseif($value->quincena=='11'){
                            $mes = 'NOVIEMBRE';
                        }elseif($value->quincena=='12'){
                            $mes = 'DICIEMBRE';
                        }

                        $cell->setValue($mes);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F'.$i, function($cell) use($value,$txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->sueldo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G'.$i, function($cell) use($value,$txtcolor){
                        // manipulate the cel
                        $cell->setValue($value->porcentaje);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    
                    $sheet->cell('H'.$i, function($cell) use($value,$txtcolor){
                        // manipulate the cel
                        $cell->setValue(number_format($value->valor_anticipo,2));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $i= $i+1;
                }

            });


        })->export('xlsx');


    }


    /***********************************************/
    /**Nueva Funcionalidad Anticipos Empleados******/
    /***********************************************/
    public function crear_anticipo_empleado($id_nomina,$nombre1,$nombre2,$apellido1,$apellido2,$cedula)
    {
        
        $nombre_completo = $nombre1." ".$nombre2." ".$apellido1." ".$apellido2;
        $id_i = $cedula;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empl_nomina = Ct_Nomina::findorfail($id_nomina);

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $lista_banco = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->get();

        return view('contable.rol_anticipo_quincena.modal_anticipos', ['cargos'=>$empl_nomina->cargo,'id_i'=>$id_i,'nombre_completo'=>$nombre_completo,'tipo_pago_rol' => $tipo_pago_rol,'lista_banco' => $lista_banco,'bancos' => $bancos,'empl_nomina' => $empl_nomina,'sueldo_neto' => $empl_nomina->sueldo_neto]); 

    }


    /*************************************************
    ***************GUARDA ANTICIPO EMPLEADO***********    
    /*************************************************/  
    
    public function store_anticipo_empleado(Request $request)
    {
        
        $contador_ctant = DB::table('ct_rh_anticipos')->get()->count();
        $numero_anticipo = 0; 

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $mont_anticipo = $request['monto_anticipo'];
        $fech_creacion = $request['fecha_creacion'];

        $anio_cobro = $request['anio_pmes_cobro'];
        $mes_cobro = $request['pmes_cobro'];

        $cuent_sal = $request['cuenta_saliente'];

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

        if($contador_ctant == 0){
            $num = '1';
            $numero_anticipo = str_pad($num, 9, "0", STR_PAD_LEFT);
        }else{
            
            $max_id = DB::table('ct_rh_anticipos')->where('id_empresa',$id_empresa)->latest()->first();
            $max_id = intval($max_id->secuencia);
            if(strlen($max_id)<10){
                $nu = $max_id+1;
                $numero_anticipo = str_pad($nu, 9, "0", STR_PAD_LEFT);
            }
        
        } 

        $text  = 'Anticipo 1ERA Quincena'.':'.' '.'Año Cobro Anticipo'.':'.$anio_cobro.' '.'Mes Cobro Anticipo'.':'.$txt_mes_cobro.' '.'Valor'.':'.$mont_anticipo;

        /************************************
         *****Inserta Ct_Asientos_Cabecera****
        /************************************/
        $input_cabecera = [
            'observacion'     => 'ANTICIPO EMPLEADO 1ERA QUINCENA:'.$numero_anticipo.' POR LA CANTIDAD DE '.$request['monto_anticipo'],
            'fecha_asiento'   => $fech_creacion,
            'fact_numero'     => $numero_anticipo,
            'id_empresa'      => $id_empresa,
            'observacion'     => $text,
            'valor'           => $mont_anticipo,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        
        
        $input_anticipo = [
           
            'id_empl' => $request['id_empl'],
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
            'cuenta_saliente' => $request['cuenta_saliente'],
            'num_cheque' => $request['numero_cheque'],
            'fecha_cheque' => $request['fecha_cheque'],
            'id_asiento_cabecera' => $id_asiento_cabecera,
            //'id_asiento_cabecera' => 1,
            'secuencia'       => $numero_anticipo,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente
        
        ];

        $id_anticipo = Ct_Rh_Anticipos::insertGetId($input_anticipo);
        
        /************************************
        *****Inserta Ct_Asientos_Detalle*****
        /************************************/ 

        if ($mont_anticipo > 0){

            $plan_cuentas= Plan_Cuentas::where('id',$cuent_sal)->first();
            
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $cuent_sal,
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
        
        
        if ($mont_anticipo > 0){
            $plan_cuentas = Ct_Configuraciones::obtener_cuenta('NOMINAANTICIPOS_ANTICIPOS_SUELDOS');
            //$plan_cuentas= Plan_Cuentas::where('id','1.01.02.06.04')->first();
            
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
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

        return $id_anticipo;

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
            'id_empl'       => $request['identificacion'],
            'estado'       => 1,
            'id_empresa'   => $id_empresa,
            'nombres'   =>$request['nombre']
        ];
        $registros = $this->doSearchingQuery($constraints);
        //$empresas = Empresa::all();

        return view('contable.rol_anticipo_quincena.index_anticipos', ['request' => $request, 'empresa' => $empresa, 'registros' => $registros, 'searchingVals' => $constraints]);

   }


    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Rh_Otros_Anticipos::query();
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

    public function pdf_anticipo_quincena($id_anticipo,Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $anticip_empleado = Ct_Rh_Anticipos::where('estado', '1')
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

        $view = \View::make('contable.rol_anticipo_quincena.pdf_anticipo', compact('anticip_empleado','empresa','total_str','asiento_cabecera','asiento_detalle','caj_banc'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Anticipo Comprobante' . $id_anticipo . '.pdf');

    }
    
    
    /*public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $constraints = [
            
            'mes'        => $request['mes'],
            'id_empresa' => $request['id_empresa'],

        ];
        
        $rol_pag = $this->doSearchingQuery($constraints);
     
        $id_nomina = $request['id_nomina'];


        return view('contable/rol_pago/index', ['request' => $request, 'rol_pag' => $rol_pag,'id_nomina' => $id_nomina,'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Rol_Pagos::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(5);
    }*/



}
