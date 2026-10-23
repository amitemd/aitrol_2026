<?php

namespace Sis_medico\Http\Controllers\contable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Bodega;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Empresa;
use Sis_medico\Hospital;
use Sis_medico\Union_Bodega;

class BodegasController extends Controller
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

    
    /************************************************
    ********LISTADO DE BODEGAS POR EMPRESA***********
    /************************************************/
    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $bodegas = Ct_Bodegas::where('estado','=', 1)->where('id_empresa', $id_empresa)->orderby('id', 'desc')->get();
        $hospital = Hospital::all();


        return view('contable.bodegas.index', ['empresa' => $empresa,'bodegas' => $bodegas,'hospital' => $hospital]); 
    }
    
    /*************************************************
    ********CREAR NUEVA BODEGAS POR EMPRESA***********
    /*************************************************/
    public function crear(Request $request)
    {
        
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $hospital = hospital::all();
        $bodegas_compras = Bodega::where('id_empresa',$id_empresa)->get();

        return view('contable.bodegas.create', ['empresa' => $empresa,'hospital' => $hospital, "bodegas_compras"=>$bodegas_compras]); 
    }

    /*************************************************
    *************GUARDA BODEGAS POR EMPRESA***********
    /*************************************************/    
    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');


        if ($request["importacion"] == "1") {
            $bodega_ant = Ct_Bodegas::where('id_empresa', $id_empresa)->where('estado', 1)->where('importacion', '1')->first();
            if (!is_null($bodega_ant)) {
                $bodega_ant->importacion = 0;
                $bodega_ant->ip_modificacion = $ip_cliente;
                $bodega_ant->id_usuariomod = $idusuario;
                $bodega_ant->save();
            }
        }

        $id_bodega = Ct_Bodegas::insertGetId([
            
            'id_hospital'        => $request['id_hospital'],
            'nombre'             => $request['nombre'],
            'color'              => $request['color'],
            'departamento'       => $request['departamento'],
            'id_empresa'         => $id_empresa,
            'estado'             => $request['estado'],
            'encargado'          => $idusuario,
            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
            'importacion'       => $request['importacion'],
        ]);

        if(!is_null($request->ligue_bodega)){
            $data= [
                    'id_bodega_cont'    => $id_bodega,
                    'id_bodega_compra'  => $request->ligue_bodega,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_empresa'        => $id_empresa
            ];
            Union_Bodega::create($data);
        }

        return redirect()->route('bodegas.index');
    }

    /*************************************************
    *************EDITAR BODEGAS POR EMPRESA***********
    /*************************************************/
    public function editar($id,$id_empresa)
    {
        
        $empresa  = Empresa::where('id', $id_empresa)->first();
        
        $hospital = hospital::all();

        $bodegas = Ct_Bodegas::findorfail($id);

        $union_bodega = Union_Bodega::where('id_bodega_cont', $id)->first();

        $bodegas_compras = Bodega::where('id_empresa',$id_empresa)->get();
        // dd($union_bodega);

        return view('contable.bodegas.edit',['empresa'=> $empresa,'hospital'=> $hospital,'bodegas'=>$bodegas, "union_bodega"=> $union_bodega, "bodegas_compras" => $bodegas_compras]);
    }

    /*************************************************
    ***********ACTUALIZA BODEGAS POR EMPRESA**********
    /*************************************************/    
    public function update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id = $request['id_bodeg'];
        $bodegas = Ct_Bodegas::findOrFail($id);
        $id_empresa = $request->session()->get('id_empresa');

        if ($request["importacion"] == "1") {
            $bodega_ant = Ct_Bodegas::where('id_empresa', $id_empresa)->where('estado', 1)->where('importacion', '1')->first();
            if (!is_null($bodega_ant)) {
                $bodega_ant->importacion = 0;
                $bodega_ant->ip_modificacion = $ip_cliente;
                $bodega_ant->id_usuariomod = $idusuario;
                $bodega_ant->save();
            }
        }

        $input = [

            'id_hospital'        => $request['id_hospital'],
            'nombre'             => $request['nombre'],
            'color'              => $request['color'],
            'departamento'       => $request['departamento'],
            'id_empresa'         => $id_empresa,
            'estado'             => $request['estado_bodega'],
            'encargado'          => $idusuario,
            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
            'importacion'        => $request['importacion']
        ];

        if(!is_null($request["ligue_bodega"])){
            $union_bodega = Union_Bodega::where("id_bodega_cont", $request->id_bodeg)->where('id_empresa', $id_empresa)->first();
            if(is_null($union_bodega)){
                $data= [
                        'id_bodega_cont'    => $request->id_bodeg,
                        'id_bodega_compra'  => $request->ligue_bodega,
                        'id_usuariocrea'    => $idusuario,
                        'id_usuariomod'     => $idusuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                        'id_empresa'        => $id_empresa
                ];
                Union_Bodega::create($data);
            }else{
                $union_bodega->id_bodega_compra     = $request->ligue_bodega;
                $union_bodega->ip_modificacion      = $ip_cliente;
                $union_bodega->id_empresa           = $id_empresa;
                $union_bodega->save();
            }
        }

        $bodegas->update($input); 
        
        return redirect()->route('bodegas.index');
    }

  
    /*************************************************
    ************BUSCAR BODEGAS POR EMPRESA************
    /*************************************************/  
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $constraints = [
            'nombre'       => $request['nombre'],
            'id_hospital'  => $request['id_hospital'],
            'departamento' => $request['departamento'],
            'estado'       => 1,
            'id_empresa'   => $id_empresa,
        ];
        $hospital = hospital::all();
        $bodegas = $this->doSearchingQuery($constraints,$request);
        return view('contable.bodegas.index', ['request' => $request, 'bodegas' => $bodegas, 'searchingVals' => $constraints,'empresa' => $empresa,'hospital' => $hospital]);
    
    }

    private function doSearchingQuery($constraints,Request  $request)
    {
        
        $id_empresa = $request->session()->get('id_empresa');

        $query  = Ct_Bodegas::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->get();
    }

}
