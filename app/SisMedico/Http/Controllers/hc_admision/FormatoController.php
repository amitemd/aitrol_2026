<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\Subseguro;
use Sis_medico\Documento;
use Sis_medico\Archivo_historico;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\Sala;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Log_usuario;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax_log;
use Sis_medico\Pentax;
use Sis_medico\Especialidad;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Response;
use Cookie;
use Sis_medico\ApPlantillaItem;
use Sis_medico\ApProcedimiento;
use Sis_medico\Detalle_Formato_Descargo;
use Sis_medico\FormatoDescargaInsumos;

class FormatoController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 13, 5, 7, 20)) == false) {
            return true;
        }
    }

    //Visualiza el listado de Rubros
    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $formato= FormatoDescargaInsumos::orderBy('id','desc')->get();
        
        return view('formato_descargo/index', ['formato' => $formato, 'empresa' => $empresa]);
    }

    //Visualiza el ingreso de los datos del Rubro
    public function create(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //$formato= FormatoDescargaInsumos::where('estado','1')->get(); 
        $id_empresa = $request->session()->get('id_empresa');
        $items= ApPlantillaItem::where('estado','1')->get();
        $productos= ApProcedimiento::where('estado','1')->get();
        $empresa = Empresa::where('id', $id_empresa)->first();
        
        return view('formato_descargo/create', ['productos' => $productos, 'empresa' => $empresa]);
    }

    //Guarda informacion del Rubro 
    public function store(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $ip_cliente          = $_SERVER["REMOTE_ADDR"];
        $idusuario           = Auth::user()->id;
        //dd($request->all());
        if(!is_null($request['codigo'])){
            $contador=0;
            $id_formato=FormatoDescargaInsumos::insertGetId([
                'descripcion'=>$request['descripcion'],
                'nota'=>$request['nota'],
                'estado'=>$request['estado'],
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ]);
            foreach($request['codigo'] as $key=>$value){
                //consulta ap
                //dd($value);
                $ap= ApProcedimiento::where('id',$value)->first();
                if(!is_null($ap)){
                    Detalle_Formato_Descargo::create([
                        'id_formato'=>$id_formato,
                        'codigo'=>$value,
                        'nombre'=>$ap->descripcion,
                        'cantidad'=>$request['cantidad'][$contador],
                        'precio'=>$request['precio'][$contador],
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,    
                    ]);
                }
               
                $contador++;
            }
        }
        return redirect()->route('formatosProductos.index');
    }


    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'codigo' => $request['codigo'],
            'nombre' => $request['nombre'],
        ];
        $id_empresa = $request->session()->get('id_empresa');
        $rubros = $this->doSearchingQuery($constraints, $id_empresa);

        return view('formato_descargo/index', ['rubros' => $rubros, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = FormatoDescargaInsumos::query();

        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {

                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }


        return $query->where('id_empresa', $id_empresa)->paginate(20);
    }

    public function edit(Request $request,$id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $formato=FormatoDescargaInsumos::find($id);
        $productos= ApProcedimiento::where('estado','1')->get();
        $detalle_formato= Detalle_Formato_Descargo::where('id_formato',$formato->id)->get();

        return view('formato_descargo/edit', ['formato' => $formato, 'detalle_formato' => $detalle_formato,'productos'=>$productos,'id'=>$id]);
    }


    public function update(Request $request, $id)
    {


         $id_empresa = $request->session()->get('id_empresa');
        $ip_cliente          = $_SERVER["REMOTE_ADDR"];
        $idusuario           = Auth::user()->id;
        //dd($request->all());
        $formato= FormatoDescargaInsumos::find($id);
        if(!is_null($request['codigo'])){
            $contador=0;
            $id_formato=$formato->update([
                'descripcion'=>$request['descripcion'],
                'nota'=>$request['nota'],
                'estado'=>$request['estado'],
                'ip_modificacion'     => $ip_cliente,
                'id_usuariomod'       => $idusuario,
            ]);
            foreach($request['id_detalle'] as $key=>$value){
                //consulta ap
                //dd($value);
                if(!is_null($request['codigo'][$contador])){
                    if($value=='-1'){
                        $ap= ApProcedimiento::where('id',$request['codigo'][$contador])->first();
                        if(!is_null($ap)){
                            Detalle_Formato_Descargo::create([
                                'id_formato'=>$id,
                                'codigo'=>$request['codigo'][$contador],
                                'nombre'=>$ap->descripcion,
                                'cantidad'=>$request['cantidad'][$contador],
                                'precio'=>$request['precio'][$contador],
                                'ip_creacion'         => $ip_cliente,
                                'ip_modificacion'     => $ip_cliente,
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,    
                            ]);
                        }
                    }else{
                        $ap= ApProcedimiento::where('id',$request['codigo'][$contador])->first();
                        if(!is_null($ap)){
                            $det= Detalle_Formato_Descargo::where('id_formato',$id)->get();

                            $detalles= Detalle_Formato_Descargo::find($value);
                            $detalles->codigo=$request['codigo'][$contador];
                            $detalles->nombre=$ap->descripcion;
                            $detalles->cantidad=$request['cantidad'][$contador];
                            $detalles->cantidad=$request['precio'][$contador];
                            $detalles->id_usuariomod=$idusuario;
                            $detalles->ip_modificacion=$ip_cliente;
                            $detalles->save();
                            foreach($det as $z){
                                if($z->id!=$value){
                                    $zx= Detalle_Formato_Descargo::find($z->id);
                                    $zx->delete();
                                }
                            }
                        }
                        
                    }
                }

               
               
                $contador++;
            }
        }

        return redirect()->route('formatosProductos.index');
    }
}
