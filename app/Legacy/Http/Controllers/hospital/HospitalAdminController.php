<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital;
use Sis_medico\AgendaQ;
use Sis_medico\Hospital_Producto;
use Sis_medico\Pais;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Illuminate\Support\Facades\Storage;

class HospitalAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin/index');
    }
    public function modalfarmacia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin/modalfarmacia');
    }
    public function gestionh()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        
        $habitacion = Habitacion::paginate(5);
        $cama       = DB::table('cama')->join('habitacion', 'cama.id_habitacion', '=', 'habitacion.id')->get();
        //dd($cama);

        return view('hospital_admin/gestionc/gestionh', ['habitacion' => $habitacion, 'cama' => $cama]);
    
    }
    public function gestion_c()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        
        $habitacion = Habitacion::paginate(5);
        $cama       = DB::table('cama')->join('habitacion', 'cama.id_habitacion', '=', 'habitacion.id')->get();
        //dd($cama);

        return view('hospital_admin/gestion_camillas/gestion_c', ['habitacion' => $habitacion, 'cama' => $cama]);
    
    }
    

    public function gestionqui()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        $paises = Pais::all();
        $agenda= AgendaQ::paginate(5);
        return view('hospital_admin.gestionqui',['paises'=>$paises,'agenda'=>$agenda]);
    }

    public function resultadoquirofano($id){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
         $agenda = AgendaQ::find($id);
         //dd($agenda);
        return view('hospital_admin.resultadoquirofano',['agenda'=>$agenda]);
    } 
    public function editar($id,Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
        //return redirect('/');
        }
      $agenda = AgendaQ::find($id);
      $ip_cliente    = $_SERVER["REMOTE_ADDR"];
      $idusuario     = Auth::user()->id;
      $editar= [
        'estado' =>$request['estado'],
        'costo' =>$request['costo'],
        ];
        $marcate= $agenda->update($editar);
    return redirect()->route('hospital_admin.gestionqui', ['agenda' =>$agenda]);
     
    }

     public function buscadorfa(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        $farmacia=Hospital_Producto::all();
        if(($request->codigo)!=""){
            $farmacia= Hospital_Producto::where("codigo","like",$request->codigo."%")->get();
        }
        elseif(($request->nombre)!=""){
            $farmacia= Hospital_Producto::where("nombre","like",$request->nombre."%")->get();    
        }
          
        return view('hospital_admin/buscadorfa',['farmacia'=>$farmacia]);
    }

    public function dashboard()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin.dashboard');
    }
    public function farmacia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        $farmacia= Hospital_Producto::paginate(10);

        return view('hospital_admin/farmacia',['farmacia'=>$farmacia]);
    }
   
     
    
    public function insumo()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin.insumo');

    }
    public function emergenciadmin()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin.emergenciadmin');

    } 
    

    public function modalagcuarto()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }

        $nombre_piso = DB::table('piso')->get();
        $tipo        = DB::table('tipo_habitacion')->get();

        return view('hospital_admin/gestionc/modalagcuarto', ['nombre_piso' => $nombre_piso, 'tipo' => $tipo]);
    }

    public function aghabitaciones(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $input      = [
            'id_tipo'         => $request['id_tipo'],
            'id_piso'         => $request['id_piso'],
            'estado'          => $request['estado'],
            'codigo'          => $request['numhabitacion'],
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,

        ];
        $id_habitacion = Habitacion::insertGetId($input);
        $estado        = $request['estado_uno'];
        $estadodos     = $request['estado_dos'];
        $estadotres    = $request['estado_tres'];
        $codigo        = $request['codigo'];
        $costo         =$request['preciohabitacion'];
        $codigodos     = $request['codigodos'];
        $codigotres    = $request['codigotres'];
        $tipo          = $request['id_tipo'];
        if ($tipo != 0) {
            if ($tipo == 1) {
                $input_cama = [
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama = Cama::insertGetId($input_cama);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama,
                    'id_imagen'       => $estado,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);

            } elseif ($tipo == 2) {
                $input_cama = [
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama = Cama::insertGetId($input_cama);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama,
                    'id_imagen'       => $estado,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
                $input_cama2 = [
                    'estado'          => $estadodos,
                    'codigo'          => $codigodos,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama2 = Cama::insertGetId($input_cama2);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama2,
                    'id_imagen'       => $estadodos,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            } elseif ($tipo == 4) {
                $input_cama = [
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama = Cama::insertGetId($input_cama);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama,
                    'id_imagen'       => $estado,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
                $input_cama2 = [
                    'estado'          => $estadodos,
                    'codigo'          => $codigodos,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama2 = Cama::insertGetId($input_cama2);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama2,
                    'id_imagen'       => $estadodos,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);

                $input_cama3 = [
                    'estado'          => $estadotres,
                    'codigo'          => $codigotres,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama3 = Cama::insertGetId($input_cama3);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama3,
                    'id_imagen'       => $estadotres,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            } elseif ($tipo == 3) {
                $input_cama = [
                   
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama = Cama::insertGetId($input_cama);
                $suite= $estado+1+$tipo;
                CamaTransaccion::create([
                    'id_cama'         => $id_cama,
                    'id_imagen'       => $suite,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            } elseif ($tipo == 5) {
                $input_cama = [
                    
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                
                $id_camaej = Cama::insertGetId($input_cama);
                $ejecutiva= $estado+3+$tipo;
                CamaTransaccion::create([
                    'id_cama'         => $id_camaej,
                    'id_imagen'       => $ejecutiva,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            }

        }

        return back();
    }
    public function editarh($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        $habitacionedit = Habitacion::where('id', $id)->get();
        $habitacionid   = Habitacion::find($id);
        $id_dada        = $habitacionid->id;
        $nombre_piso    = DB::table('piso')->get();
        $tipo           = DB::table('tipo_habitacion')->get();
        $cama           = Cama::where('id_habitacion', '=', $id_dada)->get();
        
        return view('hospital_admin/gestionc/editarh', ['habitacionedit' => $habitacionedit, 'habitacionid' => $habitacionid, 'nombre_piso' => $nombre_piso, 'tipo' => $tipo, 'cama' => $cama]);
    }
  //Camillas
    public function editarc($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        $habitacionedit = Habitacion::where('id', $id)->get();
        $habitacionid   = Habitacion::find($id);
        $id_dada        = $habitacionid->id;
        $nombre_piso    = DB::table('piso')->get();
        $tipo           = DB::table('tipo_habitacion')->get();
        $cama           = Cama::where('id_habitacion', '=', $id_dada)->get();
        return view('hospital_admin/gestion_camillas/editarc', ['habitacionedit' => $habitacionedit, 'habitacionid' => $habitacionid, 'nombre_piso' => $nombre_piso, 'tipo' => $tipo, 'cama' => $cama]);
    }

    public function updateh($id, Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        $id_habitacion = Habitacion::find($id);
        $ip_cliente    = $_SERVER["REMOTE_ADDR"];
        $idusuario     = Auth::user()->id;
        $id_tipo       = $id_habitacion->id_tipo;
        $habitacionid= $id_habitacion->id;
        $cama = Cama::where('id_habitacion', '=', $habitacionid)->get();
        $contador= 1; 
        $inputs= "estadoC";
        $estados= "estado";
        
        foreach($cama as $value){
            $input      = [
                'estado'          => $request['estado'],
                'codigo'          => $request['codigo'],
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
    
            ];
            $id_habitacions = $id_habitacion->update($input);
            $contadore= (String)($contador++);
            $editarcama= [
                'codigo'=>$request['estadoC'.$contadore],
                'estado'=>$request['estado'.$contadore],               

            ];
            $editarr= $value->update($editarcama);
            foreach($value->transaccion as $val){
                $camatransaccion= [
                    'id_imagen' =>$request['estado'.$contadore],
                    'updated_at'=>date('Y-m-d H:i:s'),
                ];
                $comofue= $val->update($camatransaccion);
                if($id_tipo==3){
                    $camatransaccion= [
                        'id_imagen' =>$request['estado'.$contadore]+1+$id_tipo,
                        'updated_at'=>date('Y-m-d H:i:s'),
                    ];
                    $comofue= $val->update($camatransaccion);
                }
                elseif($id_tipo==5){
                    $camatransaccion= [
                        'id_imagen' =>$request['estado'.$contadore]+3+$id_tipo,
                        'updated_at'=>date('Y-m-d H:i:s'),
                    ];
                    $comofue= $val->update($camatransaccion);
                }
            }

        }
        
        
        
        return back();

    }

    //
    ///}

    /*
public function buscar(Request $request) {
$opcion = '1';
if($this->rol_new($opcion)){
//return redirect('/');
$id_habitacion =\DB::table('id_habitacion')->select('id','id_tipo')->get();
return $id_habitacion;

}

} */

}
