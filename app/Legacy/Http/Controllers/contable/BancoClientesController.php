<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Divisas;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Empresa;
use Sis_medico\Ct_Sucursal_Banco;
use Sis_medico\Http\Controllers\Controller;

class BancoClientesController extends Controller
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

        $caj_ban = Ct_Bancos::paginate(10);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa   = Empresa::find($id_empresa);

           
        return view('contable.banco.index', ['caj_ban' => $caj_ban, 'empresa' => $empresa]); //*Aqui va la ruta de la carpeta*
    }
    public function create(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //$cuenta_efectivo = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CAJABAN_EFECT_EQUIVALENTE'); 

        $divisas = Ct_Divisas::where('estado', 1)->get();
        //$plan= Plan_Cuentas::where('id_padre', $cuenta_efectivo->id_padre)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa   = Empresa::find($id_empresa);
        
        return view('contable.banco.create', ['divisas' => $divisas, 'empresa' => $empresa]); //*Aqui va la ruta de la carpeta*
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        Ct_Bancos::create([
            'nombre'       => $request['nombre'],
            'estado'       => $request['estado_caj_banco'],
            'id_usuariomod'         => $idusuario,
            'id_usuariocrea'        => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'ip_creacion'           => $ip_cliente,
            
        ]); 

        return redirect()->route('banco_clientes.index');
          //return Redirect::to('formulario.registro')->withInput();

    }
     
    public function query_cuentas(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $codigo= $request['opcion'];

        if(!is_null($codigo)){
            $id_padre= Plan_Cuentas::where('id_padre',$codigo)->get();

            return $id_padre;
        }                
        return 'no';
    }
         public function edit($id)
            {
                $cuenta_efectivo = \Sis_medico\Ct_Configuraciones::obtener_cuenta('BANCO_EFECT_EQUIVALENTE'); 

   
                $caja=Ct_Bancos::find($id);
                $divisas = Ct_Divisas::where('estado', 1)->get();
                $plan= Plan_Cuentas::where('id_padre', $cuenta_efectivo->id_padre)->get();
                $sucursal= Ct_Bancos::where('id',$id)->first();
                return view('contable.banco.edit', ['caja' => $caja,'divisas' => $divisas,'plan'=>$plan, 'sucursal'=> $sucursal]);
            }
         public function update(Request $request, $id)
    {
        $caja = Ct_Bancos::findOrFail($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $input = [
            'nombre'       => $request['nombre'],
            'estado'       => $request['estado'],
            'id_usuariomod'         => $idusuario,
            'id_usuariocrea'        => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'ip_creacion'           => $ip_cliente,           
        ]; 


        $caja->update($input); 
        
             
        return redirect()->route('banco_clientes.index');
    }
    public function search(Request $request){
        $id_empresa = $request->session()->get('id_empresa');
        $empresa   = Empresa::find($id_empresa);
        
       $buscador =  Ct_Bancos::where("nombre",'like','%'.$request->buscar_nombre."%")->paginate(10);
       return view('contable.banco.index',['caj_ban'=>$buscador,'empresa'=>$empresa]);
        
    }


    public function ver_asientos_descuadrados(){
       
        $id_asientos = array();
        $asiento_cabecera = Ct_Asientos_Cabecera::where("id_empresa", "0993075000001")->where("estado", 1)->get();
        foreach($asiento_cabecera as $value){
            $debe =0;
            $haber=0;

            $detalle = Ct_Asientos_Detalle::where("id_asiento_cabecera", $value->id)->get();
            foreach($detalle as $det){
                $debe   +=  $det->debe;
                $haber  +=  $det->haber;
            }
            if($debe != $haber){
               array_push($id_asientos, $value->id);
            }
        }

        dd($id_asientos);

    }


}
