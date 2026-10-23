<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\ApProcedimiento;
use Sis_medico\ApProcedimientoNivel;
use Sis_medico\ApPlantilla;
use Sis_medico\Ap_Tipo_Examen;
use Sis_medico\Utilidades;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ApProcedimientosController extends Controller
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
        $tipo= Ap_Tipo_Examen::where('estado','1')->get();
        return view('archivo_plano/procedimientos/frmcrear',['tipo' =>$tipo]);
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $procedimiento = trim((string) $request->input('descripcion', ''));
        $filtros = [
            'descripcion' => $procedimiento,
            'tipo' => trim((string) $request->input('tipo', '')),
            'iva' => trim((string) $request->input('iva', '')),
            'seguro' => max(0, (int) $request->input('seguro', 0)) ?: '',
        ];
        $consulta = ApProcedimiento::where('estado', '1');
        if ($procedimiento !== '') {
            $consulta->where(function ($query) use ($procedimiento) {
                $query->where('descripcion', 'like', '%'.$procedimiento.'%')
                    ->orWhere('codigo', 'like', '%'.$procedimiento.'%');
            });
        }
        if ($filtros['tipo'] !== '') {
            $consulta->where('tipo', $filtros['tipo']);
        }
        if ($filtros['iva'] === 'si') {
            $consulta->where(function ($query) {
                $query->where('iva', '>', 0)->orWhere('iva_nuevo', '>', 0);
            });
        } elseif ($filtros['iva'] === 'no') {
            $consulta->where(function ($query) {
                $query->whereNull('iva')->orWhere('iva', '<=', 0);
            })->where(function ($query) {
                $query->whereNull('iva_nuevo')->orWhere('iva_nuevo', '<=', 0);
            });
        }
        if ($filtros['seguro'] !== '') {
            $consulta->where('id_seguro', $filtros['seguro']);
        }
        $procedimientos = $consulta->orderBy('id')->paginate(20);
        $procedimientos->appends($filtros);
        $tipos = Ap_Tipo_Examen::where('estado', '1')->orderBy('tipo')->pluck('tipo')->unique();

        $seguros = DB::table('seguros')->where('inactivo', 1)
            ->select('id', 'nombre')->orderBy('nombre')->get();


        // El seguro pertenece al ítem; los convenios de su nivel no lo asignan.
        $idsSeguros = $procedimientos->getCollection()->pluck('id_seguro')->filter(function ($id) {
            return $id !== null;
        })->unique()->values()->all();
        $nombresSeguros = DB::table('seguros')->whereIn('id', $idsSeguros)->where("tipo",0)->pluck('nombre', 'id');

        foreach ($procedimientos as $item) {
            $item->nombre_seguro = $item->id_seguro !== null && isset($nombresSeguros[$item->id_seguro])
                ? $nombresSeguros[$item->id_seguro] : '';
        }
        return view('archivo_plano/procedimientos/index', compact(
            'procedimientos', 'procedimiento', 'tipos', 'seguros', 'filtros'
        ));
    }

    public function buscar_procedimiento(Request $request)
    {
        return $this->index($request);
    }

    private function tasaIvaConfigurada($campo = 'aplica_iva')
    {
        // Misma fuente que VendedorController. Las planillas consumen una fracción.
        $configuracion = Utilidades::porcentajeIva();
        $iva = $configuracion ? $configuracion->iva : null;
        if (!is_numeric($iva) || !is_finite((float) $iva) || $iva < 0 || $iva > 100) {
            throw ValidationException::withMessages([
                $campo => 'Configure un porcentaje de IVA válido en la configuración del sistema antes de guardar.',
            ]);
        }
        return (float) $iva > 1 ? (float) $iva / 100 : (float) $iva;
    }

    public function guardarInline(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->json(['message' => 'No tiene permisos para editar ítems.'], 403);
        }

        return DB::transaction(function () use ($request, $id) {
            $item = ApProcedimiento::where('id', $id)->lockForUpdate()->firstOrFail();
            if ((int) $item->estado !== 1) {
                return response()->json(['message' => 'El ítem está inactivo. Recargue la lista.'], 409);
            }
            // Actualizar solo los campos enviados evita pisar otros cambios de la fila.
            $input = array_intersect_key($request->all(), array_flip([
                'tipo', 'codigo', 'descripcion', 'cantidad', 'valor', 'aplica_iva', 'id_seguro',
            ]));
            if (!$input) {
                return response()->json(['message' => 'No se enviaron campos para guardar.'], 422);
            }
            foreach (['tipo', 'codigo', 'descripcion', 'valor'] as $campo) {
                if (isset($input[$campo]) && is_string($input[$campo])) {
                    $input[$campo] = trim($input[$campo]);
                }
            }
            $tipos = Ap_Tipo_Examen::where('estado', '1')->pluck('tipo')->all();
            // Un tipo histórico puede conservarse, pero no asignarse a otra fila.
            $tipos[] = $item->tipo;
            $segurosPermitidos = DB::table('seguros')->where('inactivo', 1)->pluck('id')->all();
            if ($item->id_seguro !== null) { $segurosPermitidos[] = $item->id_seguro; }
            if (array_key_exists('id_seguro', $input) && $input['id_seguro'] === '') {
                $input['id_seguro'] = null;
            }
            $validator = Validator::make($input, [
                'tipo' => ['sometimes', 'required', 'string', Rule::in($tipos)],
                'codigo' => ['sometimes', 'required', 'regex:/^[0-9]{1,19}$/'],
                'descripcion' => ['sometimes', 'required', 'string', 'max:16000'],
                'cantidad' => ['sometimes', 'required', 'numeric', 'min:1', 'max:1000000'],
                'valor' => ['sometimes', 'required', 'regex:/^[0-9]{1,6}(\.[0-9]{1,4})?$/'],
                'aplica_iva' => ['sometimes', 'required', 'boolean'],
                'id_seguro' => ['sometimes', 'nullable', 'integer', Rule::in($segurosPermitidos)],
            ], [
                'required' => 'Este campo es obligatorio.',
                'tipo.in' => 'Seleccione un tipo disponible.',
                'codigo.regex' => 'El código debe contener hasta 19 dígitos.',
                'cantidad.numeric' => 'Ingrese una cantidad numérica.',
                'cantidad.min' => 'La cantidad mínima es 1.',
                'cantidad.max' => 'La cantidad máxima es 1000000.',
                'valor.regex' => 'Ingrese un valor entre 0 y 999999.9999, con hasta 4 decimales.',
                'aplica_iva.boolean' => 'Seleccione Sí o No.',
                'id_seguro.in' => 'Seleccione un seguro disponible.',
                'id_seguro.integer' => 'Seleccione un seguro válido.',
                'descripcion.max' => 'La descripción es demasiado larga.',
            ]);
            $validator->after(function ($validator) use ($input) {
                if (isset($input['codigo']) && is_scalar($input['codigo'])) {
                    $codigo = ltrim((string) $input['codigo'], '0');
                    if (strlen($codigo) === 19 && strcmp($codigo, '9223372036854775807') > 0) {
                        $validator->errors()->add('codigo', 'El código supera el máximo permitido.');
                    }
                }
            });
            if ($validator->fails()) {
                return response()->json(['message' => 'Revise los campos indicados.', 'errors' => $validator->errors()], 422);
            }

            if (array_key_exists('aplica_iva', $input)) {
                $aplica = (bool) $input['aplica_iva'];
                unset($input['aplica_iva']);
                $tasaIva = $aplica ? $this->tasaIvaConfigurada() : 0;
                $input['iva'] = $tasaIva;
                $input['iva_nuevo'] = $tasaIva;
            }
            if (!isset($input['cantidad']) && (float) $item->cantidad <= 0) {
                $input['cantidad'] = 1;
            }
            $item->fill($input);
            if ($item->isDirty()) {
                $item->id_usuariomod = Auth::id();
                $item->ip_modificacion = $request->ip();
                $item->save();
            }
            return response()->json(['message' => 'Guardado', 'item' => [
                'id' => $item->id,
                'tipo' => $item->tipo,
                // Los códigos BIGINT deben viajar como texto para no perder dígitos en JS.
                'codigo' => (string) $item->codigo,
                'descripcion' => $item->descripcion,
                'cantidad' => $item->cantidad,
                'id_seguro' => $item->id_seguro === null ? '' : (string) $item->id_seguro,
                'valor' => $item->valor,
                'aplica_iva' => (float) $item->iva > 0 || (float) $item->iva_nuevo > 0 ? '1' : '0',
            ]]);
        });
    }

    public function eliminarInline(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->json(['message' => 'No tiene permisos para eliminar ítems.'], 403);
        }
        return DB::transaction(function () use ($request, $id) {
            $item = ApProcedimiento::where('id', $id)->lockForUpdate()->firstOrFail();
            if ((int) $item->estado === 1) {
                $item->update([
                    'estado' => 0,
                    'id_usuariomod' => Auth::id(),
                    'ip_modificacion' => $request->ip(),
                ]);
            }
            return response()->json(['message' => 'Ítem eliminado de la lista.', 'id' => $item->id]);
        });
    }

    public function lista(Request $request)
    {
        $lista = ApPlantilla::orderBy('descripcion', 'ASC')->get();
        $procedimiento = $request->input('descripcion');

        $procedimientos = "SELECT b.tipo, b.codigo, b.descripcion, a.cantidad, b.valor, b.iva from ap_plantilla_items a, ap_procedimiento b
        where cod_plantilla='$procedimiento' and a.id_procedimiento=b.codigo order by b.descripcion ASC";
        $procedimientos = DB::select($procedimientos);
        //dd($procedimientos);

        return view('archivo_plano/procedimientos/lista', compact('procedimientos', 'lista'));
    }


    public function editar(Request $request, $id)
    {

        $editar_procedimiento = ApProcedimiento::findOrFail($id);
        $tipos = Ap_Tipo_Examen::where('estado', '1')->orderBy('tipo')->pluck('tipo')->unique();
        return view('archivo_plano/procedimientos/editar', compact('editar_procedimiento', 'tipos'));
    }

    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id = $request['id_procedimiento'];
        //dd($id);
        $tipo_procedimiento = ApProcedimiento::findOrFail($id);
        $tasaIva = (float) $request['txtiva'] > 0 ? $this->tasaIvaConfigurada('txtiva') : 0;

        $input = [

            'tipo'                  => $request['txttipo'],
            'codigo'                => $request['txtcodigo'],
            'descripcion'           => $request['txtdescripcion'],
            'cantidad'              => $request->input('txtcantidad') ?: 1,
            'valor'                 => $request['txtvalor'],
            'iva'                   => $tasaIva,
            'iva_nuevo'             => $tasaIva,
            'estado'                => $request['txtestado'],
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,

        ];

        //dd($input);
        $tipo_procedimiento->update($input);

        return redirect('administracion/procedimientos');
    }




    public function store(Request $request)
    {
        /*
        $pro = new ApProcedimiento();
        $pro->tipo = $request->txttipo;
        $pro->codigo = $request->txtcodigo;
        $pro->descripcion = $request->txtdescripcion;
        $pro->cantidad = $request->txtcantidad;
        $pro->valor = $request->txtvalor;
        $pro->iva = $request->txtiva;
        $pro->estado = $request->txtestado;
        $pro->id_usuariocrea = $idusuario;
        $pro->id_usuariomod = $idusuario;
        $pro->ip_creacion = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente;
        $pro->save();
        */
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $tasaIva = (float) $request['txtiva'] > 0 ? $this->tasaIvaConfigurada('txtiva') : 0;

        $arr=[
            'tipo' =>$request['txttipo'],
            'codigo' =>$request['txtcodigo'],
            'descripcion' =>$request['txtdescripcion'],
            'cantidad' => $request->input('txtcantidad') ?: 1,
            'valor' =>$request['txtvalor'],
            'iva' => $tasaIva,
            'iva_nuevo' => $tasaIva,
            'estado' =>$request['txtestado'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        ApProcedimiento::create($arr);
        return redirect('administracion/procedimientos');
    }




    //create nivel
    public function store2(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        /*$pro = new ApProcedimientoNivel();
        $pro->codigo = $request->id_procedimiento;
        $pro->cod_conv = $request->txtnivel;
        $pro->desc_conv = $request->txtdes;
        $pro->anexo = $request->txtanexo;
        $pro->uvr1 = $request->uvr1;
        $pro->uvr2 = $request->uvr2;
        $pro->uvr3 = $request->uvr3;
        $pro->prc1 = $request->prc1;
        $pro->prc2 = $request->prc2;
        $pro->prc3 = $request->prc3;
        $pro->uvr1a = $request->uvr1a;
        $pro->uvr2a = $request->uvr2a;
        $pro->uvr3a = $request->uvr3a;
        $pro->prc1a = $request->prc1a;
        $pro->prc2a = $request->prc2a;
        $pro->prc3a = $request->prc3a;
        $pro->separado = $request->txtseparado;
        $pro->estado = $request->txtestado;
        $pro->id_usuariocrea = $idusuario;
        $pro->id_usuariomod = $idusuario;
        $pro->ip_creacion = $ip_cliente;
        $pro->ip_modificacion = $ip_cliente;
        $pro->save();*/

        $arr=[
            'id_procedimiento'=>$request['id'],
            'codigo' =>$request['id_procedimiento'],
            'cod_conv' =>$request['txtnivel'],
            'desc_conv' =>$request['txtdes'],
            'anexo' =>$request['txtanexo'],
            'uvr1' =>$request['uvr1'],
            'uvr2' =>$request['uvr2'],
            'uvr3' =>$request['uvr3'],
            'prc1' =>$request['prc1'],
            'prc2' =>$request['prc2'],
            'prc3' =>$request['prc3'],
            'uvr1a' =>$request['uvr1a'],
            'uvr2a' =>$request['uvr2a'],
            'uvr3a' =>$request['uvr3a'],
            'prc1a' =>$request['prc1a'],
            'prc2a' =>$request['prc2a'],
            'prc3a' =>$request['prc3a'],
            'estado'=>$request['txtestado'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        //dd($request->all);
        ApProcedimientoNivel::create($arr);
        return redirect('administracion/procedimientos');
    }



    function index4()
    {
        return view('archivo_plano/procedimientos/frmasignonivel');
    }

    function fetch(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = ApProcedimiento::where('descripcion', 'LIKE', "%{$query}%")->get();
            $output = '<ul id="cierrate" class="dropdown-menu" style="display:block; position:relative">';
            foreach ($data as $row) {
                $output .= '
        <li><a href="#"> Codigo: ' . $row->codigo . '    |    Descripción: ' . $row->descripcion . '</a></li>
        <input type="hidden" name="id_procedimiento" value="' . $row->codigo . '"> <input type="hidden" name="id" value="' . $row->id . '">
       ';
            }
            $output .= '</ul>';
            echo $output;
        }
    }
    
    function cambio_estado()
    {
        $procedimientos = ApProcedimiento::where('estado','0')->get();
        foreach ($procedimientos as $value) {
            $arr=[
            'estado' => '1',
            'ip_modificacion' => 'cambio estado',
            ];

            $value->update($arr);
        }      

        return "ok";
    }

}
