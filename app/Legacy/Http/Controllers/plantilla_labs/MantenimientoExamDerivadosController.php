<?php

namespace Sis_medico\Http\Controllers\plantilla_labs;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Examen_Derivado;
use Sis_medico\Examen;
use Sis_medico\Examen_Tipo_Derivado;

class MantenimientoExamDerivadosController extends Controller
{
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 10)) == false) {
            return true;
        }
    }


    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $examenes = [];

        return view('plantillas_labs/mantenimiento_derivados/index', ['examenes' => $examenes]);
    }
    public function buscarexamenes(Request $request)
    {
        $examenes = [];
        if (!is_null($request["search"])) {
            $examenes = Examen::where('estado', 1)
                ->whereRaw('CONCAT(id," | ",nombre) like ?', "%{$request['search']}%")
                ->select('id as id', DB::raw('CONCAT(id," | ",nombre) as value'))
                ->take(50)->get();
        }
        //dd($producto);
        return response()->json($examenes);
    }
    public function buscar_todos_examenes(Request $request)
    {
        $examen1 = Examen::where('estado', 1)->where('id', "LIKE", "%{$request['term']}%")->select('id as id', DB::raw('CONCAT(id," | ",nombre) as value'));
        $examen2 = Examen::where('estado', 1)->where('nombre', 'LIKE', "%{$request['term']}%")->select('id as id', DB::raw('CONCAT(id," | ",nombre) as value'));
        $examenes = $examen1->union($examen2)->get();
        $arr = null;
        $i = 0;
        foreach ($examenes as $exam) {
            $arr[] = array('value' => $exam->value, 'id' => $exam->id);
        }
        //dd($arr);
        return $arr;
    }
    public function buscar_examenes_contenido(Request $request)
    {
        //dd($request->all());
        //$productos = Ct_productos::where('estado_tabla', '1')->where('nombre','like', '%'.$request->producto.'%')->get();
        $examenes = Examen::where('estado', 1)
            //->where('codigo', "LIKE", "%{$request['term']}%")
            ->whereRaw('CONCAT(id," | ",nombre) like ?', "%{$request['examen']}%")
            ->take(50)->get();

        if ($request['excel'] == 1) {
            $this->excel1($request);
        }
        return view('plantillas_labs/mantenimiento_derivados/index', ['examenes' => $examenes]);
    }
    public function index_derivados(Request $request, $id_examen)
    {
        $examenes = Examen::where('estado', '1')->where('id', $id_examen)->first();
        $examen_derivado = Examen_Derivado::where('id_examen', $examenes->id)->where('estado', '1')->get();
        return view('plantillas_labs/mantenimiento_derivados/index_derivados', ['examenes' => $examenes, 'examen_derivado' => $examen_derivado, 'id_examen' => $id_examen]);
    }
    public function crear_derivado($id_examen)
    {
        $examenes = Examen::where('id', $id_examen)->first();
        $derivado = Examen_Tipo_Derivado::where('estado', '1')
            ->orderBy('nombre', 'asc')
            ->get();
        return view('plantillas_labs/mantenimiento_derivados/modal_crea_derivado', ['examenes' => $examenes, 'derivado' => $derivado, 'id_examen' => $id_examen]);
    }

    public function guardar_derivado(Request $request)
    {
        //dd($request->all());
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {

            $examen_derivado = Examen_Derivado::where('id_tipo', $request->id_derivado)->where('id_examen', $request->id_examen)->where('estado', '1')->first();


            if (is_null($examen_derivado)) {

                $arr = [
                    'id_examen'         => $request->id_examen,
                    'valor'             => $request->precio,
                    'id_tipo'           => $request->id_derivado,
                    'estado'            => 1,

                    'id_usuariocrea'    => $id_usuario,
                    'id_usuariomod'     => $id_usuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];
                Examen_Derivado::create($arr);
            } else {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => 'Ya existe el examen derivado', 'titulos' => 'Error'];
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
    public function edit_derivado($id)
    {
        $examen_derivado = Examen_Derivado::find($id);

        return view('plantillas_labs/mantenimiento_derivados/modal_edit_derivado', ['examen_derivado' => $examen_derivado,  'id' => $id]);
    }

    public function update_derivado(Request $request)
    {
        //dd($request->all());
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_exam_der = $request->id_exam_der;
        //dd($request->all());
        DB::beginTransaction();
        try {
            $examen_derivado = Examen_Derivado::find($id_exam_der);
            $arr_up = [
                'valor'             => $request->precio,
                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ];

            $examen_derivado->update($arr_up);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function eliminar_derivado($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $examen_derivado = Examen_Derivado::find($id);

        $arr_p = [
            'estado'            => 0,
            'id_usuariomod'     => $id_usuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $examen_derivado->update($arr_p);
    }
}
