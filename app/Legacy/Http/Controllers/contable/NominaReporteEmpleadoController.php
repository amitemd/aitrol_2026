<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Nomina;
use Sis_medico\Empresa;
use Sis_medico\User;
use Excel;
use Sis_medico\Ct_Sucursales;

class NominaReporteEmpleadoController extends Controller
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


        return view('contable.rh_reporte_empleado.index',['empresas' => $empresas]);
    }

    public function buscarSucursales(Request $request) {
        $sucursales = Ct_Sucursales::where('estado','1')->where('id_empresa',$request->id_empresa)->where('uso_nomina','1')->get();
        $option = "";
        foreach($sucursales as $value){
            $option .= "<option value='{$value->id}'>{$value->nombre_sucursal}</option>";
        }
        if($option != ""){
            $option = "<option>Seleccione...</option> {$option}";
            return ["status"=>"success", "option"=>$option];
        }else{
            $option = "<option>No se encontraron sucursales</option>";
            return ["status"=>"error", "option"=>$option];
        }
        
    }

    /*public function reporte_datos_empleados(Request $request)
    {
        //dd($request['id_empresa']);
        $id_empresa = $request['id_empresa'];
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

       $datos_empl = Ct_Nomina::where('estado','1')->where('id_empresa', $id_empresa)->get();
        //$datos_empl = Ct_Nomina::where('id_empresa', $id_empresa)->get();
        
        //$datos_empl = Ct_Nomina::where('id_empresa', $id_empresa)->get();
        $fecha_d = date('Y/m/d');
        Excel::create('Reporte Datos Empleados-'.$fecha_d, function($excel) use($datos_empl,$empresa) { 
            $excel->sheet('Datos Empleados', function($sheet) use($datos_empl,$empresa){
                $fecha_d = date('Y/m/d');
                $i = 5;
                
                $sheet->mergeCells('A1:AJ1');
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
                    // manipulate the cel
                    $cell->setValue('REPORTE DATOS EMPLEADOS'.' - '.$fecha2);
                    $cell->setFontSize('15');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A1:AJ1', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->mergeCells('A2:AJ2');
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
                $sheet->cells('A2:AJ2', function($cells) {
                    $cells->setBackground('#3383FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('A3:AJ3');
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
                $sheet->cells('A3:AJ3', function($cells) {
                    $cells->setBackground('#3383FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->cells('A1:K3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('IDENTIFICACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PRIMER NOMBRE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGUNDO NOMBRE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PRIMER APELLLIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGUNDO APELLIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PAIS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CIUDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DIRECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONO DOMICILIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONO CELULAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('OCUPACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA NACIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('GENERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ETNIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DISCAPACIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PORCENTAJE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO DE CARGAS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });
                $sheet->cell('R4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NIVEL ACADEMICO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO CIVIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EMAIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('AREA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CARGO OCUPA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FONDO RESERVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DECIMO TERCERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DECIMO CUARTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO PRIVADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AB4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('HORARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AC4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BONO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BONO IMPUTABLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AE4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPUESTO A LA RENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AF4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ALIMENTACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AH4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('N# CUENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AI4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AJ4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SUELDO NETO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A4:AJ4', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                // FORMATEO DE COLUMNAS F Y H A DOS DECIMALES
                $sheet->setColumnFormat(array(
                    'AA' => '0.00',
                    'AC' => '0.00', 
                    'AD' => '0.00', 
                    'AE' => '0.00', 
                    'AH' => '0.00', 
                ));
                

                foreach($datos_empl as $value){

                    $txtcolor='#000000';

                    if($value->id_user!=null){
                       //$user_empl    = User::where('id', $value->id_user)->where('estado', '1')->first();
                       $user_empl    = User::where('id', $value->id_user)->first();
                       //$nombre1= $user_empl->nombre1;
                       //$nombre2= $user_empl->nombre2;
                       //$apellido1= $user_empl->apellido1;
                       //$apellido2= $user_empl->apellido2;
                    }

                    if($value->id_empresa!=null){
                        $empresa = Empresa::find($value->id_empresa);
                    }
                    $sheet->cell('A'.$i, function($cell) use($user_empl,$txtcolor){
                        // manipulate the cel
                        $cell->setValue($user_empl->id);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('B'.$i, function($cell) use($user_empl, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($user_empl->nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('C'.$i, function($cell) use($user_empl, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($user_empl->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('D'.$i, function($cell) use($user_empl, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($user_empl->apellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E'.$i, function($cell) use($user_empl, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($user_empl->apellido2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F'.$i, function($cell) use($user_empl, $txtcolor){
                        // manipulate the cel
                        $pais_txt='';
                        if($user_empl->id_pais=='1'){
                            $pais_txt = 'ECUADOR';    
                        }elseif($user_empl->id_pais=='2'){
                            $pais_txt = 'ALBANIA';
                        }elseif($user_empl->id_pais=='3'){
                            $pais_txt = 'ALEMANIA';
                        }elseif($user_empl->id_pais=='4'){
                            $pais_txt = 'ANDORRA';
                        }elseif($user_empl->id_pais=='5'){
                            $pais_txt = 'ANGOLA';
                        }elseif($user_empl->id_pais=='6'){
                            $pais_txt = 'ANTIGUA Y BARBUDA';
                        }elseif($user_empl->id_pais=='7'){
                            $pais_txt = 'ARABIA SAUDITA';
                        }elseif($user_empl->id_pais=='8'){
                            $pais_txt = 'ARGELIA';
                        }elseif($user_empl->id_pais=='9'){
                            $pais_txt = 'ARGENTINA';
                        }elseif($user_empl->id_pais=='10'){
                            $pais_txt = 'ARMENIA';
                        }elseif($user_empl->id_pais=='11'){
                            $pais_txt = 'AUSTRALIA';
                        }elseif($user_empl->id_pais=='12'){
                            $pais_txt = 'AUSTRIA';
                        }elseif($user_empl->id_pais=='13'){
                            $pais_txt = 'AZERBAIYAN';
                        }elseif($user_empl->id_pais=='14'){
                            $pais_txt = 'BAHAMAS';
                        }elseif($user_empl->id_pais=='15'){
                            $pais_txt = 'BAHREIN';
                        }elseif($user_empl->id_pais=='16'){
                            $pais_txt = 'BANGLADESH';
                        }elseif($user_empl->id_pais=='17'){
                            $pais_txt = 'BARBADOS';
                        }elseif($user_empl->id_pais=='18'){
                            $pais_txt = 'BÉLGICA';
                        }elseif($user_empl->id_pais=='19'){
                            $pais_txt = 'BÉLICE';
                        }elseif($user_empl->id_pais=='20'){
                            $pais_txt = 'BENIN';
                        }elseif($user_empl->id_pais=='21'){
                            $pais_txt = 'BIELORUSIA';
                        }elseif($user_empl->id_pais=='22'){
                            $pais_txt = 'BOLIVIA';
                        }elseif($user_empl->id_pais=='23'){
                            $pais_txt = 'BOSNIA';
                        }elseif($user_empl->id_pais=='24'){
                            $pais_txt = 'BOTSUANA';
                        }elseif($user_empl->id_pais=='25'){
                            $pais_txt = 'BRASIL';
                        }elseif($user_empl->id_pais=='26'){
                            $pais_txt = 'BRUNEI';
                        }elseif($user_empl->id_pais=='27'){
                            $pais_txt = 'BULGARIA';
                        }elseif($user_empl->id_pais=='28'){
                            $pais_txt = 'BURKINA FASO';
                        }elseif($user_empl->id_pais=='29'){
                            $pais_txt = 'BURUNDI';
                        }elseif($user_empl->id_pais=='30'){
                            $pais_txt = 'BUTAN';
                        }elseif($user_empl->id_pais=='31'){
                            $pais_txt = 'CABO VERDE';
                        }elseif($user_empl->id_pais=='32'){
                            $pais_txt = 'CAMBOYA';
                        }elseif($user_empl->id_pais=='33'){
                            $pais_txt = 'CAMERÚN';
                        }elseif($user_empl->id_pais=='34'){
                            $pais_txt = 'CANADÁ';
                        }elseif($user_empl->id_pais=='35'){
                            $pais_txt = 'CHAD';
                        }elseif($user_empl->id_pais=='36'){
                            $pais_txt = 'CHILE';
                        }elseif($user_empl->id_pais=='37'){
                            $pais_txt = 'CHINA';
                        }elseif($user_empl->id_pais=='38'){
                            $pais_txt = 'CHIPRE';
                        }elseif($user_empl->id_pais=='39'){
                            $pais_txt = 'COLOMBIA';
                        }elseif($user_empl->id_pais=='40'){
                            $pais_txt = 'COMORAS';
                        }elseif($user_empl->id_pais=='41'){
                            $pais_txt = 'COREA DEL NORTE';
                        }elseif($user_empl->id_pais=='42'){
                            $pais_txt = 'COREA DEL SUR';
                        }elseif($user_empl->id_pais=='43'){
                            $pais_txt = 'COSTA DE MARFIL';
                        }elseif($user_empl->id_pais=='44'){
                            $pais_txt = 'COSTA RICA';
                        }elseif($user_empl->id_pais=='45'){
                            $pais_txt = 'CROACIA';
                        }elseif($user_empl->id_pais=='46'){
                            $pais_txt = 'CUBA';
                        }elseif($user_empl->id_pais=='47'){
                            $pais_txt = 'DINAMARCA';
                        }elseif($user_empl->id_pais=='48'){
                            $pais_txt = 'DOMINICA';
                        }elseif($user_empl->id_pais=='49'){
                            $pais_txt = 'EEUU';
                        }elseif($user_empl->id_pais=='50'){
                            $pais_txt = 'EGIPTO';
                        }elseif($user_empl->id_pais=='51'){
                            $pais_txt = 'PERU';
                        }
                        $cell->setValue($pais_txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->ciudad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->direccion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('I'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->telefono1);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('J'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->telefono2);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('K'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->ocupacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('L'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->fecha_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('M'.$i, function($cell) use($value, $txtcolor){
                        $sexo_empl='';
                        if($value->sexo =='M'){
                            $sexo_empl = 'MASCULINO';    
                        }elseif($value->sexo =='F'){
                            $sexo_empl = 'FEMENINO';
                        }
                        $cell->setValue($sexo_empl);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('N'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->etnia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('O'.$i, function($cell) use($value, $txtcolor){
                        $cell->setAlignment('right');
                        $disca='';
                        if($value->check_discapacidad=='1'){
                            $disca = 'SI';    
                        }else{
                            $disca = 'NO';
                        }
                        
                        $cell->setValue($disca);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('P'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->porcentaje_discapacidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Q'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->numero_cargas);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('left');
                    });
                    $sheet->cell('R'.$i, function($cell) use($value, $txtcolor){
                        $niv_acad ='';
                        
                        if($value->nivel_academico =='1'){
                            $niv_acad = 'BACHILLER';    
                        }elseif($value->nivel_academico =='2'){
                            $niv_acad = 'UNIVERSITARIO';
                        }elseif($value->nivel_academico =='3'){
                            $niv_acad = 'TERCER NIVEL';
                        }elseif($value->nivel_academico =='4'){
                            $niv_acad = 'CUARTO NiVEL';
                        }
                        
                        $cell->setValue($niv_acad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('S'.$i, function($cell) use($value, $txtcolor){
                        $est_civil ='';
                        
                        if($value->estado_civil =='1'){
                            $est_civil = 'UNIDO';    
                        }elseif($value->estado_civil =='2'){
                            $est_civil = 'SOLTERO';
                        }elseif($value->estado_civil =='3'){
                            $est_civil = 'CASADO';
                        }elseif($value->estado_civil =='4'){
                            $est_civil = 'DIVORCIADO';
                        }elseif($value->estado_civil =='5'){
                            $est_civil = 'VIUDO';
                        }
                        
                        $cell->setValue($est_civil);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('T'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->email);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('U'.$i, function($cell) use($value, $txtcolor){
                        $area_empl ='';
                        
                        if($value->area =='1'){
                            $area_empl = 'ADMINISTRATIVA';    
                        }elseif($value->area =='2'){
                            $area_empl = 'MEDICA';
                        }
                        
                        $cell->setValue($area_empl);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('V'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->cargo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('W'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->fecha_ingreso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('X'.$i, function($cell) use($value, $txtcolor){
                        $fond_reser ='';
                        
                        if($value->pago_fondo_reserva =='1'){
                            $fond_reser = 'ACUMULA';    
                        }elseif($value->pago_fondo_reserva =='2'){
                            $fond_reser = 'MENSUALIZA';
                        }
                        
                        $cell->setValue($fond_reser);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Y'.$i, function($cell) use($value, $txtcolor){
                        $decimo_tercero ='';
                        
                        if($value->decimo_tercero =='1'){
                            $decimo_tercero = 'ACUMULA';    
                        }elseif($value->decimo_tercero =='2'){
                            $decimo_tercero = 'MENSUALIZA';
                        }
                        
                        $cell->setValue($decimo_tercero);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Z'.$i, function($cell) use($value, $txtcolor){
                        $decimo_cuarto ='';
                        
                        if($value->decimo_cuarto =='1'){
                            $decimo_cuarto = 'ACUMULA';    
                        }elseif($value->decimo_cuarto =='2'){
                            $decimo_cuarto = 'MENSUALIZA';
                        }
                        
                        $cell->setValue($decimo_cuarto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AA'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->seguro_privado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AB'.$i, function($cell) use($value, $txtcolor){
                        $cell->setAlignment('right');
                        $id_hor ='';
                        if($value->horario =='1'){
                            $id_hor = '8:00 - 16:40';    
                        }elseif($value->horario =='2'){
                            $id_hor = '7:30 - 16:10';
                        }elseif($value->horario =='3'){
                            $id_hor = '7:00 - 15:40';
                        }elseif($value->horario =='4'){
                            $id_hor = '8:30 - 17:10';
                        }elseif($value->horario =='5'){
                            $id_hor = '9:00 - 17:40';
                        }elseif($value->horario =='6'){
                            $id_hor = '9:30 - 18:10';
                        }
                        
                        $cell->setValue($id_hor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AC'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->bono);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AD'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->bono_imputable);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AE'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->impuesto_renta);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AF'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->alimentacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AG'.$i, function($cell) use($value, $txtcolor){
                        $cell->setAlignment('right');
                        $id_banc ='';
                        if($value->banco =='1'){
                            $id_banc = 'Banco Pichincha';    
                        }elseif($value->banco =='2'){
                            $id_banc = 'Banco del Pacífico';
                        }elseif($value->banco =='3'){
                            $id_banc = 'Banco Guayaquil';
                        }elseif($value->banco =='4'){
                            $id_banc = 'Banco Internacional ';
                        }elseif($value->banco =='5'){
                            $id_banc = 'Banco Bolivariano';
                        }elseif($value->banco =='6'){
                            $id_banc = 'Produbanco';
                        }elseif($value->banco =='7'){
                            $id_banc = 'Banco del Austro';
                        }elseif($value->banco =='8'){
                            $id_banc = 'Banco Solidario';
                        }elseif($value->banco =='9'){
                            $id_banc = 'Banco General Rumiñahui';
                        }elseif($value->banco =='10'){
                            $id_banc = 'Banco de Loja';
                        }
                        
                        $cell->setValue($id_banc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AH'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->numero_cuenta);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('right');
                      
                    });

                    $sheet->cell('AI'.$i, function($cell) use($value, $txtcolor){
                        
                        if ($value->estado == '1') {
                            $cell->setValue('Activo');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                            $cell->setAlignment('right');
                        } else {
                            $cell->setValue('Inactivo');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        }                 
                       
                    });

                    $sheet->cell('AJ'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->sueldo_neto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $i= $i+1;
                }
            });
         $excel->getActiveSheet()->getColumnDimension("B")->setWidth(21)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("C")->setWidth(21)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("F")->setWidth(14)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("G")->setWidth(14)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("K")->setWidth(18)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("M")->setWidth(18)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(10)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("U")->setWidth(21)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("V")->setWidth(25)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(12)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("AH")->setWidth(20)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("AI")->setWidth(15)->setAutosize(false);
         $excel->getActiveSheet()->getStyle('Q4')->getAlignment()->setWrapText(true);
         

        })->export('xlsx');
        

    }*/

     public function reporte_datos_empleados(Request $request)
    {
        //dd($request['id_empresa']);
        $id_empresa = $request['id_empresa'];
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

       $datos_empl = Ct_Nomina::where('estado','1')->where('id_empresa', $id_empresa)->get();
        //$datos_empl = Ct_Nomina::where('id_empresa', $id_empresa)->get();
        
        //$datos_empl = Ct_Nomina::where('id_empresa', $id_empresa)->get();
        $fecha_d = date('Y/m/d');
        Excel::create('Reporte Datos Empleados-'.$fecha_d, function($excel) use($datos_empl,$empresa) { 
            $excel->sheet('Datos Empleados', function($sheet) use($datos_empl,$empresa){
                $fecha_d = date('Y/m/d');
                $i = 5;
                
                $sheet->mergeCells('A1:AG1');
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
                    // manipulate the cel
                    $cell->setValue('REPORTE DATOS EMPLEADOS'.' - '.$fecha2);
                    $cell->setFontSize('15');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A1:AH1', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->mergeCells('A2:AH2');
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
                $sheet->cells('A2:AH2', function($cells) {
                    $cells->setBackground('#3383FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('A3:AH3');
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
                $sheet->cells('A3:AH3', function($cells) {
                    $cells->setBackground('#3383FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                });
                $sheet->cells('A1:H3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('IDENTIFICACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EMPLEADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PAIS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CIUDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DIRECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONO DOMICILIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONO CELULAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ACTIVIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA NACIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('GENERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ETNIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DISCAPACIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PORCENTAJE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NUMERO DE CARGAS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                });
                $sheet->cell('O4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NIVEL ACADEMICO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO CIVIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EMAIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('AREA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CARGO OCUPA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FONDO RESERVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DECIMO TERCERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DECIMO CUARTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO PRIVADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('HORARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BONO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BONO IMPUTABLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AB4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPUESTO A LA RENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AC4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ALIMENTACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BANCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AE4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('N# CUENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AF4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SUCURSAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AH4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SUELDO NETO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A4:AH4', function($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                // FORMATEO DE COLUMNAS F Y H A DOS DECIMALES
                $sheet->setColumnFormat(array(
                    'AA' => '0.00',
                    'AC' => '0.00', 
                    'AD' => '0.00', 
                    'AE' => '0.00', 
                    'AH' => '0.00', 
                ));
                

                foreach($datos_empl as $value){

                    $txtcolor='#000000';

                    if($value->id_user!=null){
                       //$user_empl    = User::where('id', $value->id_user)->where('estado', '1')->first();
                       $user_empl    = User::where('id', $value->id_user)->first();
                       //$nombre1= $user_empl->nombre1;
                       //$nombre2= $user_empl->nombre2;
                       //$apellido1= $user_empl->apellido1;
                       //$apellido2= $user_empl->apellido2;
                    }
                    $sucursal=Ct_Sucursales::where('id',$value->id_sucursal)->first();

                    if($value->id_empresa!=null){
                        $empresa = Empresa::find($value->id_empresa);
                    }
                    $sheet->cell('A'.$i, function($cell) use($user_empl,$txtcolor){
                        // manipulate the cel
                        $cell->setValue($user_empl->id);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('B'.$i, function($cell) use($user_empl, $txtcolor){
                        // manipulate the cel
                        $cell->setValue($user_empl->apellido1.' '.$user_empl->apellido2.' '.$user_empl->nombre1.' '.$user_empl->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                   
                    $sheet->cell('C'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $pais_txt='';
                        if($value->pais=='1'){
                            $pais_txt = 'ECUADOR';    
                        }elseif($value->pais=='2'){
                            $pais_txt = 'ALBANIA';
                        }elseif($value->pais=='3'){
                            $pais_txt = 'ALEMANIA';
                        }elseif($value->pais=='4'){
                            $pais_txt = 'ANDORRA';
                        }elseif($value->pais=='5'){
                            $pais_txt = 'ANGOLA';
                        }elseif($value->pais=='6'){
                            $pais_txt = 'ANTIGUA Y BARBUDA';
                        }elseif($value->pais=='7'){
                            $pais_txt = 'ARABIA SAUDITA';
                        }elseif($value->pais=='8'){
                            $pais_txt = 'ARGELIA';
                        }elseif($value->pais=='9'){
                            $pais_txt = 'ARGENTINA';
                        }elseif($value->pais=='10'){
                            $pais_txt = 'ARMENIA';
                        }elseif($value->pais=='11'){
                            $pais_txt = 'AUSTRALIA';
                        }elseif($value->pais=='12'){
                            $pais_txt = 'AUSTRIA';
                        }elseif($value->pais=='13'){
                            $pais_txt = 'AZERBAIYAN';
                        }elseif($value->pais=='14'){
                            $pais_txt = 'BAHAMAS';
                        }elseif($value->pais=='15'){
                            $pais_txt = 'BAHREIN';
                        }elseif($value->pais=='16'){
                            $pais_txt = 'BANGLADESH';
                        }elseif($value->pais=='17'){
                            $pais_txt = 'BARBADOS';
                        }elseif($value->pais=='18'){
                            $pais_txt = 'BÉLGICA';
                        }elseif($value->pais=='19'){
                            $pais_txt = 'BÉLICE';
                        }elseif($value->pais=='20'){
                            $pais_txt = 'BENIN';
                        }elseif($value->pais=='21'){
                            $pais_txt = 'BIELORUSIA';
                        }elseif($value->pais=='22'){
                            $pais_txt = 'BOLIVIA';
                        }elseif($value->pais=='23'){
                            $pais_txt = 'BOSNIA';
                        }elseif($value->pais=='24'){
                            $pais_txt = 'BOTSUANA';
                        }elseif($value->pais=='25'){
                            $pais_txt = 'BRASIL';
                        }elseif($value->pais=='26'){
                            $pais_txt = 'BRUNEI';
                        }elseif($value->pais=='27'){
                            $pais_txt = 'BULGARIA';
                        }elseif($value->pais=='28'){
                            $pais_txt = 'BURKINA FASO';
                        }elseif($value->pais=='29'){
                            $pais_txt = 'BURUNDI';
                        }elseif($value->pais=='30'){
                            $pais_txt = 'BUTAN';
                        }elseif($value->pais=='31'){
                            $pais_txt = 'CABO VERDE';
                        }elseif($value->pais=='32'){
                            $pais_txt = 'CAMBOYA';
                        }elseif($value->pais=='33'){
                            $pais_txt = 'CAMERÚN';
                        }elseif($value->pais=='34'){
                            $pais_txt = 'CANADÁ';
                        }elseif($value->pais=='35'){
                            $pais_txt = 'CHAD';
                        }elseif($value->pais=='36'){
                            $pais_txt = 'CHILE';
                        }elseif($value->pais=='37'){
                            $pais_txt = 'CHINA';
                        }elseif($value->pais=='38'){
                            $pais_txt = 'CHIPRE';
                        }elseif($value->pais=='39'){
                            $pais_txt = 'COLOMBIA';
                        }elseif($value->pais=='40'){
                            $pais_txt = 'COMORAS';
                        }elseif($value->pais=='41'){
                            $pais_txt = 'COREA DEL NORTE';
                        }elseif($value->pais=='42'){
                            $pais_txt = 'COREA DEL SUR';
                        }elseif($value->pais=='43'){
                            $pais_txt = 'COSTA DE MARFIL';
                        }elseif($value->pais=='44'){
                            $pais_txt = 'COSTA RICA';
                        }elseif($value->pais=='45'){
                            $pais_txt = 'CROACIA';
                        }elseif($value->pais=='46'){
                            $pais_txt = 'CUBA';
                        }elseif($value->pais=='47'){
                            $pais_txt = 'DINAMARCA';
                        }elseif($value->pais=='48'){
                            $pais_txt = 'DOMINICA';
                        }elseif($value->pais=='49'){
                            $pais_txt = 'EEUU';
                        }elseif($value->pais=='50'){
                            $pais_txt = 'EGIPTO';
                        }elseif($value->pais=='51'){
                            $pais_txt = 'PERU';
                        }
                        $cell->setValue($pais_txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('D'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->ciudad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->direccion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->telefono1);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->telefono2);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->ocupacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('I'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->fecha_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('J'.$i, function($cell) use($value, $txtcolor){
                        $sexo_empl='';
                        if($value->sexo =='M'){
                            $sexo_empl = 'MASCULINO';    
                        }elseif($value->sexo =='F'){
                            $sexo_empl = 'FEMENINO';
                        }
                        $cell->setValue($sexo_empl);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('K'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->etnia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('L'.$i, function($cell) use($value, $txtcolor){
                        $cell->setAlignment('right');
                        $disca='';
                        if($value->check_discapacidad=='1'){
                            $disca = 'SI';    
                        
                        }else{
                            $disca = 'NO';
                        }
                        
                        $cell->setValue($disca);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('M'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->porcentaje_discapacidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('N'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->numero_cargas);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('left');
                    });
                    $sheet->cell('O'.$i, function($cell) use($value, $txtcolor){
                        $niv_acad ='';
                        
                        if($value->nivel_academico =='1'){
                            $niv_acad = 'BACHILLER';    
                        }elseif($value->nivel_academico =='2'){
                            $niv_acad = 'UNIVERSITARIO';
                        }elseif($value->nivel_academico =='3'){
                            $niv_acad = 'TERCER NIVEL';
                        }elseif($value->nivel_academico =='4'){
                            $niv_acad = 'CUARTO NiVEL';
                        }
                        
                        $cell->setValue($niv_acad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('P'.$i, function($cell) use($value, $txtcolor){
                        $est_civil ='';
                        
                        if($value->estado_civil =='1'){
                            $est_civil = 'UNIDO';    
                        }elseif($value->estado_civil =='2'){
                            $est_civil = 'SOLTERO';
                        }elseif($value->estado_civil =='3'){
                            $est_civil = 'CASADO';
                        }elseif($value->estado_civil =='4'){
                            $est_civil = 'DIVORCIADO';
                        }elseif($value->estado_civil =='5'){
                            $est_civil = 'VIUDO';
                        }
                        
                        $cell->setValue($est_civil);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('Q'.$i, function($cell) use($user_empl, $txtcolor){
                        $cell->setValue($user_empl->email);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('R'.$i, function($cell) use($value, $txtcolor){
                        $area_empl ='';
                        
                        if($value->area =='1'){
                            $area_empl = 'ADMINISTRATIVA';    
                        }elseif($value->area =='2'){
                            $area_empl = 'MEDICA';
                        }
                        
                        $cell->setValue($area_empl);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('S'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->cargo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('T'.$i, function($cell) use($value, $txtcolor){
                        if (!is_null($value->fecha_ant)){
                            $cell->setValue($value->fecha_ant);
                        }
                        else{
                            $cell->setValue($value->fecha_ingreso);
                        }
                        
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('U'.$i, function($cell) use($value, $txtcolor){
                        $fond_reser ='';
                        
                        if($value->pago_fondo_reserva =='1'){
                            $fond_reser = 'ACUMULA';    
                        }elseif($value->pago_fondo_reserva =='2'){
                            $fond_reser = 'MENSUALIZA';
                        }
                        
                        $cell->setValue($fond_reser);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('V'.$i, function($cell) use($value, $txtcolor){
                        $decimo_tercero ='';
                        
                        if($value->decimo_tercero =='1'){
                            $decimo_tercero = 'ACUMULA';    
                        }elseif($value->decimo_tercero =='2'){
                            $decimo_tercero = 'MENSUALIZA';
                        }
                        
                        $cell->setValue($decimo_tercero);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('W'.$i, function($cell) use($value, $txtcolor){
                        $decimo_cuarto ='';
                        
                        if($value->decimo_cuarto =='1'){
                            $decimo_cuarto = 'ACUMULA';    
                        }elseif($value->decimo_cuarto =='2'){
                            $decimo_cuarto = 'MENSUALIZA';
                        }
                        
                        $cell->setValue($decimo_cuarto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('X'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->seguro_privado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Y'.$i, function($cell) use($value, $txtcolor){
                        $cell->setAlignment('right');
                        $id_hor ='';
                        if($value->horario =='1'){
                            $id_hor = '8:00 - 16:40';    
                        }elseif($value->horario =='2'){
                            $id_hor = '7:30 - 16:10';
                        }elseif($value->horario =='3'){
                            $id_hor = '7:00 - 15:40';
                        }elseif($value->horario =='4'){
                            $id_hor = '8:30 - 17:10';
                        }elseif($value->horario =='5'){
                            $id_hor = '9:00 - 17:40';
                        }elseif($value->horario =='6'){
                            $id_hor = '9:30 - 18:10';
                        }
                        
                        $cell->setValue($id_hor);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Z'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->bono);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AA'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->bono_imputable);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AB'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->impuesto_renta);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AC'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->alimentacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AD'.$i, function($cell) use($value, $txtcolor){
                        $cell->setAlignment('right');
                        $id_banc ='';
                        if($value->banco =='1'){
                            $id_banc = 'Banco Pichincha';    
                        }elseif($value->banco =='2'){
                            $id_banc = 'Banco del Pacífico';
                        }elseif($value->banco =='3'){
                            $id_banc = 'Banco Guayaquil';
                        }elseif($value->banco =='4'){
                            $id_banc = 'Banco Internacional ';
                        }elseif($value->banco =='5'){
                            $id_banc = 'Banco Bolivariano';
                        }elseif($value->banco =='6'){
                            $id_banc = 'Produbanco';
                        }elseif($value->banco =='7'){
                            $id_banc = 'Banco del Austro';
                        }elseif($value->banco =='8'){
                            $id_banc = 'Banco Solidario';
                        }elseif($value->banco =='9'){
                            $id_banc = 'Banco General Rumiñahui';
                        }elseif($value->banco =='10'){
                            $id_banc = 'Banco de Loja';
                        }
                        
                        $cell->setValue($id_banc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AE'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->numero_cuenta);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('right');
                      
                    });
                    $sheet->cell('AF'.$i, function($cell) use($sucursal, $txtcolor){
                        //dd($sucursal->nombre_sucursal);
                        if(isset($sucursal->nombre_sucursal)){
                            if(is_null($sucursal->nombre_sucursal)){
                                $cell->setValue("");
                            }else{
                                $cell->setValue($sucursal->nombre_sucursal);
                                
                            }
                        }else{
                            $cell->setValue("");
                        }
                       
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        $cell->setAlignment('right');
                      
                    });

                    $sheet->cell('AG'.$i, function($cell) use($value, $txtcolor){
                        
                        if ($value->estado == '1') {
                            $cell->setValue('Activo');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                            $cell->setAlignment('right');
                        } else {
                            $cell->setValue('Inactivo');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        }                 
                       
                    });

                    $sheet->cell('AH'.$i, function($cell) use($value, $txtcolor){
                        $cell->setValue($value->sueldo_neto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $i= $i+1;
                }
            });


         $excel->getActiveSheet()->getColumnDimension("B")->setWidth(60)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("C")->setWidth(21)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("F")->setWidth(14)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("G")->setWidth(14)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("K")->setWidth(18)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("M")->setWidth(18)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(10)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("U")->setWidth(21)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("V")->setWidth(25)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(12)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("AH")->setWidth(20)->setAutosize(false);
         $excel->getActiveSheet()->getColumnDimension("AI")->setWidth(15)->setAutosize(false);
         $excel->getActiveSheet()->getStyle('Q4')->getAlignment()->setWrapText(true);
         

        })->export('xlsx');
        

    }

    
   
}
