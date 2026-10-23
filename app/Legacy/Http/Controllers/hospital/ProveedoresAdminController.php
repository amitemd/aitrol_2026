<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Cama;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Hospital_Proovedor;
use Sis_medico\Hospital_Tipo_Proovedor;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Illuminate\Support\Facades\Storage;

class ProveedoresAdminController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function proveedores()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $proovedor= Hospital_Proovedor::paginate(10);
        return view('hospital_admin/proovedores/proveedores',['proovedor'=>$proovedor]);
    }
         public function modalprovedor(){
          $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $hospital_proovedor= Hospital_Tipo_Proovedor::all();

        return view('hospital_admin/proovedores/modalprovedor',['hospital_proovedor'=>$hospital_proovedor]);

    }
    public function modalprovedord(){
          $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        

        return view('hospital_admin/proovedores/modalprovedord');

    }
    public function registro (Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $hospitalentra= Hospital_Proovedor::all();
        $hospit=  [
            'ruc'=> $request['ruc'],
            'razonsocial'=> $request['razon'],
            'nombrecomercial'   =>$request['nombre'],
            'email'  => $request['emails'],
            'id_tipoproveedor'=>$request['tipop'],
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,    
        ];
        $id_logo = Hospital_Proovedor::insertGetId($hospit);
        $id= $request['imagen'];
        $nombre_original=$request['imagen']->getClientOriginalName();
        $nuevo_nombre="logo_farmacia".$nombre_original;
            
        $r12=Storage::disk('logo')->put($nuevo_nombre,  \File::get($request['imagen']) );

        $rutadelaimagen=$nuevo_nombre;
            
        
        if ($r12){
   
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');
            $logito=Hospital_Proovedor::find($id_logo);
            $logito->logo=$rutadelaimagen;
            $logito->ip_modificacion=$ip_cliente;
            $logito->id_usuariomod=$idusuario;
            $r22=$logito->save();
               
            return back();
          }
               
            

    
        
     }
      public function registropro (Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $hospital_proovedor = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
    
        Hospital_Tipo_Proovedor::create([
            'nombre'=> $request['name'],
            'descripcion'=> $request['descri'],

        ]);
        
    
        return back();
     }
     public function modaleditarpr($id){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }
          $proovedorid= Hospital_Proovedor::find($id);
          
       return view('hospital_admin/proovedores/modaleditarpr',['proovedorid'=>$proovedorid]);
      }

      
    public function updatep($id, Request $request){
        $opcion = '1';
          if ($this->rol_new($opcion)) {
              return redirect('/');
  
          }

        $id1= $request['imagen'];
        $nombre_original2=$request['imagen']->getClientOriginalName();
        $nuevo_nombre="logo_farmacia1".$nombre_original2;   
        $r12=Storage::disk('logo')->put($nuevo_nombre,  \File::get($request['imagen']) );
        $rutadelaimagen=$nuevo_nombre;
          $updatet= [
            'email'=> $request['emails'],
            'ruc'=> $request['ruc'],
            'razonsocial'=> $request['razon'],
            'nombrecomercial'   =>$request['nombre'],
            'id_tipoproveedor'=>$request['tipop'],
            //'logo' => $request['imagen'],
            'logo' => $rutadelaimagen,
            
          ];

          //$updater= Hospital_Proovedor::find($id);
          //dd($id->);

          if ($r12){
   
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');
            $logito=Hospital_Proovedor::find($id);
            //dd($logito->logo);
            $logito->logo=$rutadelaimagen;
            $logito->ip_modificacion=$ip_cliente;
            $logito->id_usuariomod=$idusuario;
            $r22=$logito->save();
          }

          $logito->update($updatet);
          //dd($logito);
          //$logito->logo=$rutadelaimagen;

          return back();
      }


      

     

}
