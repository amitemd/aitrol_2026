<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Hospital_Bodega;
use Sis_medico\AgendaQ;
use Sis_medico\Log_AgendaQ;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;

class AgendaQController extends Controller
{
public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

public function agenda(Request $request){
    $opcion = '1';
    if ($this->rol_new($opcion)) {
        return redirect('/');
    }
    $descripcion= $request['descripcion'];
    $descripcionad= 'ADMITIDO OPERACION';
    $ip_cliente = $_SERVER["REMOTE_ADDR"];
    $idusuario  = Auth::user()->id;
    $fechaini= $request['fechaini'];
    $fechatotal= substr($fechaini,0,10);
    $fechafin= $request['fechafin'];
    
     $input= [
        'id_paciente'=>$request['id_paciente'],
        'estado'=>'1',
        'fechaini'=>$fechaini,
        'fechafin'=>$fechafin,
        'id_doctor'=>$idusuario,
        'fecha_total'=>$fechatotal,
        'observaciones'=>$descripcion,
        'ipcreacion'=>$idusuario,
        'costo'=>$request['costo'],
        'ip_modificacion'=>$ip_cliente,
        
     ];
     $id_agenda= AgendaQ::insertGetId($input);

     $input2= [
        'id_agenda'=>$id_agenda,
        'descripcion'=>$request['descripcion'],
        'descripcion2'=>$descripcionad,
        'descripcion3'=>$descripcion,
        'fechaini'=>$request['fechaini'],
        'fechafin'=>$request['fechafin'],
        'costo'=>$request['costo'],
        'id_doctor_back'=>$idusuario,
        'estado'=>'1',
        'observaciones'=>$descripcion,
        'observaciones_ant'=>$descripcion,
        'ip_modificacion'=>$ip_cliente,
        'ip_creacion'=>$ip_cliente,
        'id_usuariomod'=>$ip_cliente,
     ];
     $id_log= Log_AgendaQ::insertGetId($input2);
    return back();
   }
    public function reserva(){

    $opcion = '1';
    if ($this->rol_new($opcion)) {
        return redirect('/');
    }
    return view('hospital/quirofano/agregarp');
    }
    public function datospacientq(){
    $opcion = '1';
    if ($this->rol_new($opcion)) {
        return redirect('/');
    }
return view('hospital/datospacientq');
}
public function autocomplete(Request $request){

    $nombre_encargado = $request['term'];
    $data             = null;
    $nuevo_nombre     = explode(' ', $nombre_encargado);
    $seteo            = "%";

    
    foreach ($nuevo_nombre as $value) {
        $seteo = $seteo . $value . '%';
    }
    $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                FROM `paciente`
                WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' 
                ";

    $nombres = DB::select($query);
    foreach ($nombres as $product) {
        $data[] = array('value' => $product->completo, 'id' => $product->id);
    }
    if (count($data)) {
        return $data;
    } else {
        return ['value' => 'No se encontraron resultados', 'id' => ''];
    }
    return $data;

}
public function autocomplete2(Request $request){

    $nombre_encargado = $request['apellido'];

    $data  = null;
    $nuevo_nombre = explode(' ', $nombre_encargado);
    $seteo        = "%";
    foreach ($nuevo_nombre as $value) {
        $seteo = $seteo . $value . '%';
    }
    $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, fecha_nacimiento, telefono1, telefono2, id_seguro, sexo, id
              FROM paciente
              WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "'";
    $nombres = DB::select($query);
    foreach ($nombres as $product) {
        $data[] = array('value' => $product->completo, 'fecha' => $product->fecha_nacimiento, 'telefono1'=> $product->telefono1, 'telefono2'=> $product->telefono2, 'seguro'=>$product->id_seguro, 'sexo'=>$product->sexo, 'id'=>$product->id);
    }
    if (count($data)) {
        return $data;
    } else {
        return ['value' => 'No se encontraron resultados', 'id' => ''];
    }
    return $data;

}

}