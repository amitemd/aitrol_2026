<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\ApPlantilla;
use Sis_medico\ApPlantillaItem;
use Sis_medico\ApProcedimiento;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Procedimiento;
use Validator;

class ApPlantillaController extends Controller
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


    private function rol()
    {
	    $rolUsuario = Auth::user()->id_tipo_usuario;
		$id_auth = Auth::user()->id;
		if (in_array($rolUsuario, array(1,11, 22)) == false) {
			return true;
		}
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index2()
    {
        $codigo = date("ymdHis") . rand(100, 999);
        $id_procedimiento = Procedimiento::where('estado', 1)
            ->whereNotNull('id_grupo_procedimiento')
            ->get();
        return view('archivo_plano/plantillas/frmcrear2', ['codigo' => $codigo, 'procedimiento' => $id_procedimiento]);
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        //$plantilla = $request->input('descripcion');

        /*$plantillas = ApPlantilla::select('id','codigo','descripcion','desc_comp','estado')
        ->where('descripcion', 'LIKE', '%' . $plantilla . '%')
        ->groupBy('codigo')
        ->paginate(5);*/

        $plantillas = ApPlantilla::where('estado', '1')->paginate(10);

        //$plantillas->appends(['search' => $plantilla]);
        return view('archivo_plano/plantillas/index', ['plantillas' => $plantillas, 'plantilla' => '']);
    }

    public function buscar_plantillas(Request $request)
    {
        $plantilla = $request['descripcion'];

        $plantillas = ApPlantilla::where('estado', '1');

        if ($plantilla != null) {
            $plantillas = $plantillas->where(function ($jq1) use ($plantilla) {
                $jq1->orwhereraw('descripcion LIKE ?', ['%' . $plantilla . '%']);
            });
        }

        $plantillas = $plantillas->paginate(10);

        //$plantillas->appends(['search' => $plantilla]);
        return view('archivo_plano/plantillas/index', ['plantillas' => $plantillas, 'plantilla' => $plantilla]);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $pro                  = new ApProcedimiento();
        $pro->tipo            = $request->txttipo;
        $pro->codigo          = $request->txtcodigo;
        $pro->descripcion     = $request->txtdescripcion;
        $pro->estado          = $request->txtestado;
        $pro->id_usuariocrea  = $idusuario;
        $pro->id_usuariomod   = $idusuario;
        $pro->ip_creacion     = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente;
        $pro->save();
        return redirect('administracion/procedimientos');
    }

//create nivel
    public function store2(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $pro                   = new ApProcedimientoNivel();
        $pro->id_procedimiento = $request->id_procedimiento;
        $pro->cod_conv         = $request->txtnivel;
        $pro->desc_conv        = $request->txtdes;
        $pro->anexo            = $request->txtanexo;
        $pro->uvr1             = $request->uvr1;
        $pro->uvr2             = $request->uvr2;
        $pro->uvr3             = $request->uvr3;
        $pro->prc1             = $request->prc1;
        $pro->prc2             = $request->prc2;
        $pro->prc3             = $request->prc3;
        $pro->uvr1a            = $request->uvr1a;
        $pro->uvr2a            = $request->uvr2a;
        $pro->uvr3a            = $request->uvr3a;
        $pro->prc1a            = $request->prc1a;
        $pro->prc2a            = $request->prc2a;
        $pro->prc3a            = $request->prc3a;
        $pro->separado         = $request->txtseparado;
        $pro->estado           = $request->txtestado;

        $pro->id_usuariocrea  = $idusuario;
        $pro->id_usuariomod   = $idusuario;
        $pro->ip_creacion     = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente;
        $pro->save();
        return redirect('administracion/procedimientos');
    }

    public function index4()
    {
        return view('archivo_plano/procedimientos/frmasignonivel');
    }

    public function fetch(Request $request)
    {
        if ($request->get('query')) {
            $query  = $request->get('query');
            $data   = ApProcedimiento::where('descripcion', 'LIKE', "%{$query}%")->get();
            $output = '<ul id="cierrate" class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= '
       <li><a href="#"> Codigo: ' . $row->codigo . '    |    Descripción: ' . $row->descripcion . '</a></li>
       <input type="hidden" name="id_procedimiento" value="' . $row->id . '">
       ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }

    public function insert(Request $request)
    {

        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;
        if ($request->ajax()) {
            $rules = array(
                'id_procedimiento.*' => 'required',
                'cantidad.*'         => 'required',
                'honorario.*'        => 'required',
                //'orden.*'            => 'required',
                'separado.*'         => 'required',
            );
            $error = Validator::make($request->all(), $rules);
            //dd($error->errors()->all());
            if ($error->fails()) {
                return ['status' => 'error', 'msj' => $error->errors()->all()];
            }
            $cod_pantilla = $request->txtcodigo;
            $plantilla_cabecera = [
                'codigo'            => $cod_pantilla,
                'descripcion'       => $request->txtdescripcion,
                'desc_comp'         => $request->txtdescripcioncompleta,
                //se comento el dia 2342026 en produccion no se encuentra
                // 'id_procedimiento'  => $request->id_procedimiento,
                'estado'            => $request->txtestado,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];
            ApPlantilla::insert($plantilla_cabecera);

            $count = 0;
            $cantidad  = $request->cantidad;
            $honorario = $request->honorario;
            $orden     = $request->orden;
            $separado  = $request->separado;
            foreach ($request->codigo as $value) {
                if (!is_null($value)) {
                    $partes_procedimiento   = explode("|", $value);
                    $codigo_str = str_replace("Codigo:", "", $partes_procedimiento[0]);
                    $codigo    = trim($codigo_str);
                    $procedimiento = ApProcedimiento::where('codigo', $codigo)->first();

                    if (!is_null($procedimiento)) {
                        $data = [
                            'cod_plantilla'    => $request->txtcodigo,
                            'id_procedimiento' => $codigo,
                            'procedimiento'    => $procedimiento->id,
                            'cantidad'         => $cantidad[$count],
                            'honorario'        => $honorario[$count],
                            'orden'            => 0, //victor dijo que se guarde en 0 siemrpe 
                            'separado'         => "", //victor dijo que se guarde vacio siemrpe 
                            'tipo_item'        => $procedimiento->tipo,
                            'porcentaje_modificador' => $request->porcentaje_modificador[$count],
                            'id_usuariocrea'   => $idusuario,
                            'id_usuariomod'    => $idusuario,
                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                        ];
                        ApPlantillaItem::create($data);
                    }
                }
                $count++;
            }

            return ['status' => 'success', 'msj' => 'Plantilla e Items Grabados.'];
        }
    }

    public function search(Request $request)
    {
        $nombres = ApProcedimiento::where('estado', 1);

        $nombres = $nombres->where(function ($jq1) use ($request) {
            $jq1->orwhere('descripcion', 'LIKE', '%' . $request->term . '%')
                ->orwhere('codigo', 'LIKE', '%' . $request->term . '%');
        })->get();
        $data = null;

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->id . '|' . 'Codigo: ' . $nombre->codigo . '|  Descripción: ' . $nombre->descripcion);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    //edito plantilla
    public function edit($id)
    {
        $cabecera = ApPlantilla::find($id);


        $id_procedimiento = Procedimiento::where('estado', 1)->whereNotNull('id_grupo_procedimiento')->get();

        $items = ApPlantillaItem::join('ap_procedimiento', 'ap_plantilla_items.id_procedimiento', '=', 'ap_procedimiento.id')
            ->select(
                'ap_procedimiento.id',
                'ap_procedimiento.codigo',
                'ap_procedimiento.descripcion',
                'ap_plantilla_items.cantidad',
                'ap_plantilla_items.honorario',
                'ap_plantilla_items.orden',
                'ap_plantilla_items.separado',
                'ap_plantilla_items.porcentaje_modificador'

            )
            ->where('ap_plantilla_items.estado',1)
            ->where('ap_plantilla_items.cod_plantilla', $cabecera->codigo)->orderBy('ap_plantilla_items.id')->GroupBy('ap_procedimiento.codigo')->get();
        /*$item = "SELECT b.tipo, b.codigo, b.descripcion, a.cantidad, b.valor, b.iva from ap_plantilla_items a, ap_procedimiento b
        where cod_plantilla='$fila->codigo' and a.id_procedimiento=b.codigo GROUP by b.codigo order by b.descripcion ASC";
        $item = DB::select($item);*/
        //dd($item);

        //dd($items);
        $itemc = count($items);
        return view('archivo_plano/plantillas/editar', compact('items', 'cabecera', 'itemc', 'id_procedimiento'));
    }

    public function veritem($id)
    {
        $fila = ApPlantilla::where('codigo', $id)->first();
        //   $item = ApPlantillaItem::select('id_procedimiento','cantidad','honorario','orden','separado')
        //  ->where('cod_plantilla',$id)->get();
        $item = ApPlantillaItem::join('ap_procedimiento', 'ap_plantilla_items.id_procedimiento', '=', 'ap_procedimiento.codigo')
            ->select('ap_procedimiento.codigo', 'ap_procedimiento.descripcion', 'ap_plantilla_items.cantidad', 'ap_plantilla_items.honorario', 'ap_plantilla_items.orden',
                'ap_plantilla_items.separado')
            ->where('ap_plantilla_items.cod_plantilla', $id)->get();
        $itemc = ApPlantillaItem::select('id_procedimiento', 'cantidad', 'honorario', 'orden', 'separado')
            ->where('cod_plantilla', $id)->count();
        return view('archivo_plano/plantillas/veritem', compact('item', 'fila', 'itemc'));
    }

    public function update(Request $request)
    {

        //dd($request->all());
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;
        if ($request->ajax()) {
            $rules = array(
                'id_procedimiento.*' => 'required',
                'cantidad.*'         => 'required',
                'honorario.*'        => 'required',
                //'orden.*'            => 'required',
                'separado.*'         => 'required',
            );
            $error = Validator::make($request->all(), $rules);
            //dd($error->errors()->all());
            if ($error->fails()) {
                return ['status' => 'error', 'msj' => $error->errors()->all()];
            }

            $plantilla = ApPlantilla::find($request->chivo);
            $plantilla_cabecera = [
                'descripcion'       => $request->txtdescripcion,
                'desc_comp'         => $request->txtdescripcioncompleta,
                // 'id_procedimiento'  => $request->id_procedimiento,
                'estado'            => $request->txtestado,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            $plantilla->update($plantilla_cabecera);

            $id_procedimiento = $request->id_procedimiento;
            $cantidad         = $request->cantidad;
            $honorario        = $request->honorario;
            $orden            = $request->orden;
            $separado         = $request->separado;
            $codigo           = $plantilla->codigo;


            $insert_data = null;
            $count = 0;

            ApPlantillaItem::where('cod_plantilla', $codigo)->delete();
            //dd($request->codigo);
            foreach ($request->codigo as $value) {
                if (!is_null($value)) {
                    $porciones   = explode("|", $value);
                    $id_procedimiento = $porciones[0];
                    $tipo        = ApProcedimiento::find($id_procedimiento);
                    $porcentaje_modificador = $request->porcentaje_modificador[$count];
                    if ($porcentaje_modificador == null) {
                        $porcentaje_modificador = 0;
                    }
                    //dd($tipo);

                    if (!is_null($tipo)) {
                        $data = [
                            'cod_plantilla'    => $codigo,
                            'id_procedimiento' => $tipo->id,
                            'procedimiento'    => $tipo->id,
                            'cantidad'         => $cantidad[$count],
                            'honorario'        => $honorario[$count],
                            'orden'            => 0, //victor dijo que se guarde en 0 siemrpe 
                            'separado'         => "", //victor dijo que se guarde vacio siemrpe 
                            'tipo_item'        => $tipo->tipo,
                            'porcentaje_modificador' => $porcentaje_modificador,
                            'id_usuariocrea'   => $idusuario,
                            'id_usuariomod'    => $idusuario,
                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                        ];
                        ApPlantillaItem::create($data);
                    }
                }
                $count++;
            }
            return ['status' => 'success', 'msj' => 'Plantilla e Items Grabados.'];
        }
    }

}
