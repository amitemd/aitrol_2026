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
use Excel;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ct_Configuraciones;

class NominaAnticiposFuncionarioController extends Controller
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
        
        return view('contable.rol_anticipo_funcionario.index_anticipo_funcionario',['empresas' => $empresas]);
    }

    /*public function obtener_anticipo_quincena(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id; 

        $id_empresa = $request['id_empresa'];
        $id_anio = $request['year'];
        $id_mes = $request['mes'];
        $valor_porcentaje = $request['valor_porcent'];

        $empl_rol = Ct_Nomina::where('estado','1')->where('id_empresa', $id_empresa)->orderby('id', 'asc')->get();

        if (!is_null($empl_rol)) {
        
            foreach($empl_rol as $value){
   
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
    
    }*/

    /*public function buscar_anticipos_quincena(Request $request)
    {
        
        $id_empresa = $request['id_empresa'];
        $id_anio = $request['year'];
        $id_mes = $request['mes'];

        $anticip_quince = Ct_Rh_Valor_Anticipos::where('estado','1')
                                                ->where('id_empresa', $id_empresa)
                                                ->where('anio', $id_anio)
                                                ->where('quincena', $id_mes)
                                                ->orderby('id', 'asc')->get();

        
        
        
        return view('contable.rol_anticipo_quincena.buscador_anticipos',['anticip_quince' => $anticip_quince]);
     
    }*/

    /*public function edit_anticipo($id,$idempleado,$idempresa)
    {
        
        $obtener_sueldo = Ct_Nomina::where('estado','1')
                                    ->where('id_user', $idempleado)
                                    ->where('id_empresa', $idempresa)
                                    ->first();
        
        $anticip_quincena = Ct_Rh_Valor_Anticipos::find($id);

        return view('contable.rol_anticipo_quincena.edit_anticipos', ['anticip_quincena' => $anticip_quincena,'obtener_sueldo' => $obtener_sueldo]);

    }*/

    /*private function validateInput2($request){

        $rules = [

            'porcentaje_quincena' => 'required',
        
        ];
         
        $messages= [
            
            'porcentaje_quincena.required' => 'Ingrese el Porcentaje del Anticipo.',
        ]; 

        $this->validate($request, $rules, $messages);   

    }*/
    
    
    /*public function update(Request $request)
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

    }*/

    /*public function obtener_reporte_quincena(Request $request)
    {
        $valor_porcentaje = $request['valor_porcent'];
        $id_empresa = $request['id_empresa'];
        $id_anio = $request['year'];
        $id_mes = $request['mes'];

        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $anticip_quince = Ct_Rh_Valor_Anticipos::where('estado','1')
                                           ->where('id_empresa', $id_empresa)
                                           ->where('anio', $id_anio)
                                           ->where('quincena', $id_mes)
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


    }*/


    /***********************************************/
    /**Nueva Funcionalidad Anticipos Empleados******/
    /***********************************************/
    /*public function crear_anticipo_empleado($id_nomina)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empl_nomina = Ct_Nomina::findorfail($id_nomina);

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $lista_banco = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->get();

        return view('contable.rol_anticipo_quincena.modal_anticipos', ['tipo_pago_rol' => $tipo_pago_rol,'lista_banco' => $lista_banco,'bancos' => $bancos,'empl_nomina' => $empl_nomina]); 

    }*/


    /*************************************************
    ***************GUARDA ANTICIPO EMPLEADO***********    
    /*************************************************/  
    
    /*public function store_anticipo_empleado(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        Ct_Rh_Anticipos::create([
           
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
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente
        
        ]);

        return "ok";

    }*/

    /************************************************
	*********ANTICIPOS A EMPLEADOS INDEX*************
    /************************************************/
    /*public function index_anticipos(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        
        $principales = Ct_Rh_Anticipos::where('estado', '1')->where('id_empresa', $id_empresa)->orderby('id', 'desc')->paginate(5);
  
        return view('contable.rol_anticipo_quincena.index_anticipos', ['registros' => $principales,'empresa' => $empresa]); 


    }*/

    
    /*************************************************
    ************BUSCAR ANTICIPO EMPLEADO**************
    /*************************************************/
    /*public function search_anticipo(Request $request)
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
        ];

        $registros = $this->doSearchingQuery($constraints);
        return view('contable.rol_anticipo_quincena.index_anticipos', ['request' => $request, 'empresa' => $empresa, 'registros' => $registros, 'searchingVals' => $constraints]);

   }*/


   /*private function doSearchingQuery($constraints)
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
    }*/
    
    
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
