<?php

namespace Sis_medico\Http\Controllers\contable;

use App\Modules\AccountingFinance\Application\Queries\ListJournalEntryLinesForEditing;
use Excel;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Comprobante_Ingreso_Varios;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Configuraciones2;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Ct_Cruce_Valores_Cliente;
use Sis_medico\Ct_Debito_Acreedores;
use Sis_medico\Ct_Imp_Asientos_Cabecera;
use Sis_medico\Ct_Imp_Asientos_Detalle;
use Sis_medico\Ct_Nota_Debito_Cliente;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\servicios\ServiciosController;
use Sis_medico\Log_Contable;
use Sis_medico\LogAsiento;
use Sis_medico\LogCierreAnio;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\UsuarioEspecial;

class LibroDiarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, [1, 4, 5, 20, 22, 7, 26]) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        if (is_null($fecha)) {
            $fecha = date('Y-m-d');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        /* $registros  = DB::table('ct_asientos_cabecera as ct_c')
        ->leftjoin('empresa as p', 'p.id', 'ct_c.id_empresa')
        ->select('ct_c.id', 'ct_c.fecha_asiento', 'ct_c.observacion','ct_c.tipo', 'ct_c.valor', 'p.nombrecomercial', 'ct_c.estado', 'ct_c.id_usuariocrea')
        ->where('ct_c.id_empresa', $id_empresa)
        ->orderby('id', 'desc')
        ->paginate(15); */
        $registros = Ct_Asientos_Cabecera::where('id_empresa', $id_empresa);
        if ($request->id != null) {
            $registros = $registros->where('id', $request->id);
        } else {
            if ($request->fecha == null && $request->fecha_hasta == null) {
                $request->fecha = $fecha;
                $request->fecha_hasta = $fecha_hasta;
            }
            if (is_null($fecha) && ! is_null($fecha_hasta)) {
                $registros = $registros->where('fecha_asiento', '<=', $fecha_hasta);
            } else {
                $registros = $registros->whereBetween('fecha_asiento', [$fecha.' 00:00:00', $fecha_hasta.' 23:59:59']);
            }

            if ($request->detalle != null) {
                $registros = $registros->where('observacion', 'LIKE', '%'.$request->detalle.'%');
            }
            if ($request->id_usuariocrea != null) {
                $registros = $registros->where('id_usuariocrea', $request->id_usuariocrea);
            }
            if ($request->sri != null) {
                $registros = $registros->where('aparece_sri', $request->sri);
            }
            if ($request->secuencia_f != null) {
                $registros = $registros->where('fact_numero', 'LIKE', '%'.$request->secuencia_f.'%');
            }
        }
        $registros = $registros->orderBy('id', 'DESC')->get();

        return view('contable/diario/index', ['registros' => $registros, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'empresa' => $empresa, 'request' => $request]);
    }

    public function buscar(Request $request)
    {
        $id = $request['nombre'];
        $contador = $request['contador'];
        $cuenta = Plan_Cuentas::find($id);

        return view('contable/diario/unico', ['cuenta' => $cuenta, 'contador' => $contador]);
    }

    public function revisar(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $registro = Ct_Asientos_Cabecera::findorfail($id);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_empresa = $request->session()->get('id_empresa');
        if ($registro->id_empresa != $id_empresa) {
            return redirect()->route('librodiario.index');
        }
        $detalle = Ct_Asientos_Detalle::where('ct_asientos_detalle.id_asiento_cabecera', $id)
            ->join('plan_cuentas_empresa as p', 'p.id_plan', 'ct_asientos_detalle.id_plan_cuenta')
            ->where('p.id_empresa', $id_empresa)
            ->select('p.plan as id_plan_cuenta', 'p.nombre')
            ->selectRaw('MAX(ct_asientos_detalle.descripcion) as descripcion')
            ->selectRaw('SUM(ct_asientos_detalle.debe) as debe, SUM(ct_asientos_detalle.haber) as haber')
            ->groupBy('p.id_plan', 'p.plan', 'p.nombre')
            ->get();
        // $empresa = Empresa::where('id', '0992704152001')->first();
        // $empresa = Empresa::where('id', '0992704152001')->first();
        $empresa = Empresa::where('id', $id_empresa)
            ->where('estado', '1')
            ->first();
        $cuentas = Plan_Cuentas::where('p.estado', '>', 0)->join('plan_cuentas_empresa as p', 'p.id_plan', 'plan_cuentas.id')->where('p.id_empresa', $id_empresa)->select('p.id_plan as id_plan', 'p.nombre as nombre', 'p.descripcion as descripcion', 'p.estado as estado')->get();

        return view('contable/diario/asiento', ['registro' => $registro, 'empresa' => $empresa, 'detalle' => $detalle, 'cuentas' => $cuentas]);
    }

    public function edit(Request $request, $id, ListJournalEntryLinesForEditing $listJournalEntryLines)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $registro = Ct_Asientos_Cabecera::findorfail($id);

        if ($registro->id_empresa != $id_empresa) {
            return redirect()->route('librodiario.index');
        }
        $detalle = $listJournalEntryLines->handle((int) $id, (string) $id_empresa);
        $empresa = Empresa::where('id', $id_empresa)
            ->where('estado', '1')
            ->first();
        $cuentas = Plan_Cuentas_Empresa::where('estado', '2')->where('id_empresa', $id_empresa)->get();
        if (Auth::user()->id == '0957258056') {
            //  dd($cuentas[0]);
        }
        // dd($cuentas);
        $id_auth = Auth::user()->id;
        $especial = UsuarioEspecial::where('id_usuario', $id_auth)->where('id_empresa', $id_empresa)->first();

        return view('contable/diario/edit', ['registro' => $registro, 'empresa' => $empresa, 'detalle' => $detalle, 'cuentas' => $cuentas, 'especial' => $especial, 'id_asiento_cabecera' => $id]);
    }

    public function update(Request $request/* , $id */)
    {
        $id = $request['id_asiento_cabecera'];
        $asientos = Ct_Asientos_Detalle::where('id_asiento_cabecera', $id);
        // dd($asientos);

        DB::beginTransaction();

        try {
            $contador = 0;
            $sri = $request['aparece_sri'];
            // dd($sri);
            if ($sri == 'on') {
                $sri = 1;
            } elseif (is_null($sri)) {
                $sri = 0;
            }
            $especial = $request['especial'];
            if ($especial == 'on') {
                $especial = 1;
            } elseif (is_null($especial)) {
                $especial = 0;
            }

            $cabecera = Ct_Asientos_Cabecera::find($id);
            if (! is_null($cabecera)) {
                $nuev = LibroDiarioController::moverTabla($id);
                if ($nuev['status'] == 'error') {
                    return ['status' => 'error', 'msj' => 'Error al editar', 'error' => $nuev['error']];
                }
            }

            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            $idusuario = Auth::user()->id;
            $asientos->delete();

            foreach ($request['id_asiento'] as $f) {
                // hacer update

                if (is_null($request['id_plan_cuenta'][$contador]) or $request['id_plan_cuenta'][$contador] == '') {
                    DB::rollback();

                    return ['status' => 'error', 'msj' => 'No ha seleccionado una cuenta'];
                }

                $cuenta = Plan_Cuentas_Empresa::where('id_plan', $request['id_plan_cuenta'][$contador])->orWhere('plan', $request['id_plan_cuenta'][$contador])->first();
                $plan = $cuenta->id_plan;
                do {

                    $detalles = LibroDiarioController::editDetalles($request, $plan, $cabecera, $contador);
                    if ($detalles['status'] == 'error') {
                        $plan = $cuenta->plan;
                    }
                } while (! $detalles['ok']);

                // Ct_Asientos_Detalle::create([
                //     'id_asiento_cabecera' => $cabecera->id,
                //     'id_plan_cuenta'      => $cuenta->plan,
                //     'descripcion'         => $request['descripcion'][$contador],
                //     'fecha'               => $cabecera->fecha_asiento,
                //     'debe'                => $request['debe'][$contador],
                //     'haber'               => $request['haber'][$contador],
                //     'estado'              => '1',
                //     'ip_creacion'         => $ip_cliente,
                //     'ip_modificacion'     => $ip_cliente,
                //     'id_usuariocrea'      => $idusuario,
                //     'id_usuariomod'       => $idusuario,
                // ]);
                $contador++;
            }
            if ($request['totaldebe'] > 0 && $request['totalhaber'] > 0) {
                $total = $request['totaldebe'];
                // dd($cabecera->aparece_sri);
                if (! is_null($cabecera)) {
                    $cabecera->aparece_sri = $sri;
                    $cabecera->especial = $especial;
                    $cabecera->valor = $total;
                    $cabecera->observacion = $request['observacion'];
                    $cabecera->id_usuariomod = $idusuario;
                    $cabecera->fecha_asiento = $request['fecha_asiento'];
                    $cabecera->ip_modificacion = $ip_cliente;
                    $cabecera->save();
                }
            }
            DB::commit();

            return ['status' => 'success', 'msj' => 'Editado con Exito....'];
            // return redirect()->route('librodiario.edit', ['id' => $id]);
        } catch (\Exception $e) {
            DB::rollback();

            return ['status' => 'error', 'msj' => 'Ocurrio un error', 'error' => $e->getMessage()];
        }
    }

    public static function editDetalles($request, $plan, $cabecera, $contador)
    {
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;
        DB::beginTransaction();
        try {
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $cabecera->id,
                'id_plan_cuenta' => $plan,
                'descripcion' => $request['descripcion'][$contador],
                'fecha' => $cabecera->fecha_asiento,
                'debe' => $request['debe'][$contador],
                'haber' => $request['haber'][$contador],
                'estado' => '1',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ]);
            DB::commit();

            return ['status' => 'success', 'ok' => true];
        } catch (\Exception $e) {
            DB::rollback();

            return ['status' => 'error', 'ok' => false];
        }
    }

    public static function moverTabla($id)
    {
        $cabecera = Ct_Asientos_Cabecera::find($id);
        $id_usuario = Auth::user()->id;
        // dd($cabecera);
        DB::beginTransaction();

        try {
            if (! is_null($cabecera)) {
                // $data           = $cabecera["attributes"];
                // $data["id_ant"] = $data["id"];
                // unset($data["id"]);
                // dd(count($cabecera->detalles));

                $data2 = [
                    'observacion' => $cabecera->observacion,
                    'fecha_asiento' => $cabecera->fecha_asiento,
                    'valor' => $cabecera->valor,
                    'id_empresa' => $cabecera->id_empresa,
                    'aparece_sri' => $cabecera->aparece_sri,
                    'especial' => $cabecera->especial,
                    'ip_creacion' => $cabecera->ip_creacion,
                    'ip_modificacion' => $cabecera->ip_modificacion,
                    'id_usuariocrea' => $cabecera->id_usuariocrea,
                    'id_usuariomod' => $cabecera->id_usuariomod,
                    'id_ant' => $cabecera->id,
                ];

                $id_asiento = Ct_Imp_Asientos_Cabecera::insertGetId($data2);

                if (count($cabecera->detalles) > 0) {
                    foreach ($cabecera->detalles as $details) {
                        Ct_Imp_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento,
                            'id_plan_cuenta' => $details->id_plan_cuenta,
                            'descripcion' => $details->descripcion,
                            'fecha' => $details->fecha,
                            'debe' => $details->debe,
                            'haber' => $details->haber,
                            'estado' => $details->estado,
                            'ip_creacion' => $details->ip_creacion,
                            'ip_modificacion' => $details->ip_modificacion,
                            'id_usuariocrea' => $details->id_usuariocrea,
                            'id_usuariomod' => $details->id_usuariomod,
                        ]);
                    }
                }
            }
            DB::commit();

            return ['status' => 'success', 'msj' => 'Guardado con exito'];
        } catch (\Exception $e) {
            DB::rollback();

            return ['status' => 'error', 'msj' => 'Error..', 'error' => $e->getMessage()];
        }
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        // $libro_d    = plan_cuentas::where('estado', '2')->get();
        $libro_d = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->get();
        // dd($libro_d);
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();

        return view('contable/diario/create', ['cuentas' => $cuentas, 'empresa' => $empresa, 'libro_d' => $libro_d, 'sucursales' => $sucursales]);
    }

    public function search(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');

        $empresa = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'ct_c.id' => $request['id'],
            'nombrecomercial' => $request['empresa'],
            'observacion' => $request['detalle'],
            'ct_c.fact_numero' => $request['secuencia_f'],
            'ct_c.fecha_asiento' => $request['fecha'],
            'ct_c.id_usuariocrea' => $request['ct_c.id_usuariocrea'],
            'ct_c.aparece_sri' => $request['sri'],
        ];
        // dd($request);
        // dd($constraints);
        $registros = $this->doSearchingQuery($constraints, $request);

        return view('contable/diario/index', ['request' => $request, 'registros' => $registros, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }

    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/
    private function doSearchingQuery($constraints, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $query = DB::table('ct_asientos_cabecera as ct_c')
            ->join('empresa as p', 'p.id', 'ct_c.id_empresa')
            ->where('ct_c.id_empresa', $id_empresa)
            ->select('ct_c.id', 'p.nombrecomercial', 'ct_c.fecha_asiento', 'ct_c.observacion', 'ct_c.valor', 'ct_c.estado', 'ct_c.fact_numero', 'ct_c.id_usuariocrea');
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%'.$constraint.'%');
                if ($fields[$index] == 'ct_c.id') {
                    $query = $query->where($fields[$index], $constraint);
                }
            }

            $index++;
        }

        return $query->orderBy('ct_c.id', 'desc')->paginate(15);
    }

    public function buscar_empresa(Request $request)
    {

        $codigo = $request['term'];
        $data = null;
        $nuevo_nombre = explode(' ', $codigo);
        $seteo = '%';
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo.$value.'%';
        }
        $query = "SELECT CONCAT_WS(' ',nombrecomercial) as completo
                  FROM `empresa`
                  WHERE CONCAT_WS(' ',nombrecomercial) like '".$seteo."'
                  ";
        $nombres = DB::select($query);
        foreach ($nombres as $product) {
            $data[] = ['value' => $product->completo];
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    public function store(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $check = 0;
        if (isset($request['aparece_sri'])) {
            $check = 1;
        }
        $especial = 0;
        if (isset($request['especial'])) {
            $especial = 1;
        }

        // dd($request);
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        DB::beginTransaction();
        try {
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion' => $request['observacion'],
                'fecha_asiento' => $request['fecha_asiento'].' '.date('H:i:s'),
                'valor' => $request['total'],
                'id_empresa' => $id_empresa,
                'aparece_sri' => $check,
                'especial' => $especial,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'sucursal' => $request['sucursal'],
                // 'estado_manual'       => 1,

            ]);
            if (isset($request->final)) {
                // dd($request->all());
                // ingresos va en el debe
                // gastos va en el haber
                for ($i = 0; $i < count($request->input('debe')); $i++) {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento,
                        'id_plan_cuenta' => $request->input('id_plan_cuenta')[$i],
                        'debe' => $request->input('debe')[$i],
                        'haber' => $request->input('haber')[$i],
                        'fecha' => $request['fecha_asiento'],
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                    ]);
                }
            } else {
                for ($i = 1; $i <= $request['contador']; $i++) {
                    if (! is_null($request['debe'.$i]) && ! is_null($request['haber'.$i])) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento,
                            'id_plan_cuenta' => $request['id_plan'.$i],
                            'debe' => $request['debe'.$i],
                            'haber' => $request['haber'.$i],
                            'fecha' => $request['fecha_asiento'],
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ]);
                    }
                }
            }
            DB::commit();

            return redirect()->route('librodiario.index');
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function store_cierre(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $anio = [];
        $anio = explode('-', $request['fecha_asiento']);
        $anio = $anio[0];

        $logCierre = LogCierreAnio::where('anio', $anio)->where('id_empresa', $id_empresa)->first();
        if (! is_null($logCierre)) {
            return ['status' => 'existe', 'msj' => 'Ya esta creado el cierre', 'id_asiento' => $logCierre->id_asiento];
        }
        DB::beginTransaction();
        try {
            $check = 0;
            if (isset($request['aparece_sri'])) {
                $check = 1;
            }
            $especial = 0;
            if (isset($request['especial'])) {
                $especial = 1;
            }
            // dd($request);
            $fecha_asiento = "{$request['fecha_asiento']} 23:59:59";
            // dd($fecha_asiento);
            // if(Auth::user()->id == "0951561075"){
            //   dd($fecha_asiento);
            // }

            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion' => $request['observacion'],
                // 'fecha_asiento'   => $request['fecha_asiento'] . ' ' . date('H:i:s'),
                'fecha_asiento' => $fecha_asiento,
                'valor' => $request['total'],
                'id_empresa' => $id_empresa,
                'aparece_sri' => 1,
                'especial' => $especial,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                // 'estado_manual'       => 1,

            ]);

            // ingresos va en el debe
            // gastos va en el haber
            for ($i = 0; $i < count($request['debe']); $i++) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta' => $request['id_plan_cuenta'][$i],
                    'debe' => $request['debe'][$i],
                    'haber' => $request['haber'][$i],
                    'fecha' => $fecha_asiento,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ]);
            }
            LogCierreAnio::create([
                'id_asiento' => $id_asiento,
                'fecha_asiento' => $fecha_asiento,
                'anio' => $anio,
                'id_empresa' => $id_empresa,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ]);
            DB::commit();

            return ['status' => 'success', 'msj' => 'Guardado con Exito', 'id_asiento' => $id_asiento];
        } catch (\Exception $e) {
            DB::rollback();

            return ['status' => 'error', 'msj' => 'Error al guardar', 'obs' => $e->getMessage()];
        }

        // return redirect()->route('librodiario.index');
    }

    public function __libro_mayor(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        if (isset($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('Y-m-d');
        }

        if (isset($request['fecha_hasta'])) {
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_hasta = date('Y-m-d');
        }

        if (isset($request['cuenta'])) {
            $filcuenta = $request['cuenta'];
        } else {
            $filcuenta = '[]';
        }

        $conditions = [];
        if ($filcuenta != '[]') {
            foreach ($filcuenta as $field) {
                // $conditions[] = ['id', 'like', '%' . $field . '%'];
                $conditions[] = $field;
            }
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $scuentas = Plan_Cuentas::where('estado', '=', 2)->get();

        // $cuentas = Plan_Cuentas::where('id', 'like', '_.__.__')->orwhere('id', 'like', '_._.__')->orwhere('id', 'like', '_.__.___')->get();
        if (count($conditions) > 0) {
            // $cuentas = Plan_Cuentas::where('id', 'like', $filcuenta)->get();
            $cuentas = Plan_Cuentas::whereIn('id', $conditions)->get();
        } else {
            // $cuentas = Plan_Cuentas::where('id', 'like', '_.__.__')->orwhere('id', 'like', '_._.__')->orwhere('id', 'like', '_.__.___')->get();
            $cuentas = [];
        }

        if (! isset($request['imprimir'])) {
            // return view('contable/libro_mayor/index', ['cuentas' => $cuentas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta,
            // 'empresa' => $empresa, 'scuentas' => $scuentas, 'filcuenta' => $filcuenta, 'id_empresa'=>$id_empresa]);

            return view('contable/libro_mayor/index', compact('cuentas', 'fecha', 'fecha_hasta', 'empresa', 'scuentas', 'filcuenta', 'id_empresa'));
        } else {
            if (isset($request['filfecha'])) {
                $fecha = $request['filfecha'];
            } else {
                $fecha = date('Y-m-d');
            }

            if (isset($request['filfecha_hasta'])) {
                $fecha_hasta = $request['filfecha_hasta'];
            } else {
                $fecha_hasta = date('Y-m-d');
            }

            if (isset($request['filcuenta'])) {
                $filcuenta = $request['filcuenta'];
            } else {
                $filcuenta = '[]';
            }

            $conditions = [];
            if ($filcuenta != '[]' and $filcuenta != null) {
                $filcuenta = explode(',', $filcuenta);
                foreach ($filcuenta as $field) {
                    $conditions[] = ['id', 'like', '%'.$field.'%'];
                }
            }

            if (count($conditions) > 0) {
                // $cuentas = Plan_Cuentas::where('id', 'like', $filcuenta)->get();
                $cuentas = Plan_Cuentas::where($conditions)->get();
            } else {
                // $cuentas = Plan_Cuentas::where('id', 'like', '_.__.__')->orwhere('id', 'like', '_._.__')->orwhere('id', 'like', '_.__.___')->get();
                $cuentas = [];
            }

            if ($request['exportar'] == '') {
                $vistaurl = 'contable/libro_mayor/print';
                $view = \View::make($vistaurl, compact('cuentas', 'fecha', 'fecha_hasta', 'empresa', 'scuentas', 'filcuenta', 'id_empresa'))->render();

                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot' => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('Mayor-'.$filcuenta.'.pdf');
            } else {
                $this->libro_mayor_excel($fecha, $fecha_hasta, $empresa, $cuentas);
            }

            /*  return view('contable/libro_mayor/print', ['cuentas' => $cuentas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'empresa'=>$empresa, 'scuentas'=>$scuentas, 'filcuenta'=>$filcuenta]); */
        }
    }

    public function libro_mayor(Request $request)
    {
        $filcuenta = '';
        $filcuentahasta = '';
        if (isset($request->cuenta)) {
            $filcuenta = explode('.', $request->cuenta);
            if (count($filcuenta) == 1) {
                $buscuenta = Plan_Cuentas_Empresa::find($filcuenta[0]);
                if ($buscuenta == '') {
                    $buscuenta = Plan_Cuentas_Empresa::where('id_plan', $filcuenta[0])->first();
                }
                $filcuenta = $buscuenta->plan;
            } else {
                $filcuenta = $request->cuenta;
            }
        }
        if (isset($request->cuenta_hasta)) {
            $filcuentahasta = explode('.', $request->cuenta_hasta);
            if (count($filcuentahasta) == 1) {
                $buscuentahasta = Plan_Cuentas_Empresa::find($filcuentahasta[0]);
                if ($buscuentahasta == '') {
                    $buscuentahasta = Plan_Cuentas_Empresa::where('id_plan', $filcuentahasta[0])->first();
                }
                $filcuentahasta = $buscuentahasta->plan;
            } else {
                $filcuentahasta = $request->cuenta_hasta;
            }
        }
        $id_empresa = Session::get('id_empresa');
        if (isset($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('Y-m-d');
        }
        if (isset($request['fecha_hasta'])) {
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $scuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
            ->where('pe.id_empresa', $id_empresa)
            ->where('plan_cuentas.estado', '=', 2)
            ->select('plan_cuentas.*', 'pe.plan')
            ->get();
        if ($filcuenta != '' and $filcuentahasta != '') {
            $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
                ->whereBetween('pe.plan', [$filcuenta, $filcuentahasta])
                ->where('pe.id_empresa', $id_empresa)
                ->select('plan_cuentas.*', 'pe.nombre as nombre_plan', 'pe.plan as plan')
                ->orderBy('pe.plan')
                ->get();
        } elseif ($filcuenta != '') {
            $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
                ->where('pe.plan', $filcuenta)
                ->where('pe.id_empresa', $id_empresa)
                ->where('pe.plan', '=', $filcuenta)
                ->select('plan_cuentas.*', 'pe.nombre as nombre_plan', 'pe.plan as plan')
                ->orderBy('pe.plan')
                ->get();
        } else {
            $cuentas = [];
        }
        if (! isset($request['imprimir'])) {
            return view('contable/libro_mayor/index', compact('cuentas', 'fecha', 'fecha_hasta', 'empresa', 'scuentas', 'filcuenta', 'id_empresa', 'filcuentahasta'));
        } else {
            if (isset($request['filfecha'])) {
                $fecha = $request['filfecha'];
            } else {
                $fecha = date('Y-m-d');
            }
            if (isset($request['filfecha_hasta'])) {
                $fecha_hasta = $request['filfecha_hasta'];
            } else {
                $fecha_hasta = date('Y-m-d');
            }
            if (isset($request->filcuenta)) {
                $filcuenta = explode('.', $request->filcuenta);
                if (count($filcuenta) == 1) {
                    $buscuenta = Plan_Cuentas_Empresa::find($filcuenta[0]);
                    if (! is_null($buscuenta)) {
                        $filcuenta = $buscuenta->plan;
                    }
                } else {
                    $filcuenta = $request->filcuenta;
                }
            }
            if (isset($request->filcuenta_hasta)) {
                $filcuentahasta = explode('.', $request->filcuenta_hasta);
                if (count($filcuentahasta) == 1) {
                    $buscuentahasta = Plan_Cuentas_Empresa::find($filcuentahasta[0]);
                    if ($buscuentahasta == '') {
                        $buscuentahasta = Plan_Cuentas_Empresa::where('id_plan', $filcuentahasta[0])->first();
                    } else {
                        $filcuentahasta = $buscuentahasta->plan;
                    }
                } else {
                    $filcuentahasta = $request->filcuenta_hasta;
                }
            }
            if ($filcuenta != '' and $filcuentahasta != '') {
                $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
                    ->whereBetween('pe.plan', [$filcuenta, $filcuentahasta])
                    ->where('pe.id_empresa', $id_empresa)
                    ->select('plan_cuentas.*', 'pe.nombre as nombre_plan', 'pe.plan as plan')
                    ->orderBy('pe.plan')
                    ->get();
                if ($cuentas->count() == 0) {
                    $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
                        ->whereBetween('pe.id_plan', [$filcuenta, $filcuentahasta])
                        ->where('pe.id_empresa', $id_empresa)
                        ->select('plan_cuentas.*', 'pe.nombre as nombre_plan', 'pe.plan as plan')
                        ->orderBy('pe.plan')
                        ->get();
                }
            } elseif ($filcuenta != '') {
                $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
                    ->where('pe.plan', $filcuenta)
                    ->where('pe.id_empresa', $id_empresa)
                    ->select('plan_cuentas.*', 'pe.nombre as nombre_plan', 'pe.plan as plan')
                    ->orderBy('pe.plan')
                    ->get();
                if (count($cuentas) == 0) {
                    $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
                        ->where('pe.id_plan', $filcuenta[0])
                        ->where('pe.id_empresa', $id_empresa)
                        ->select('plan_cuentas.*', 'pe.nombre as nombre_plan', 'pe.plan as plan')
                        ->orderBy('pe.plan')
                        ->get();
                }
            } else {
                $cuentas = [];
            }
            if ($request['exportar'] == '') {
                $vistaurl = 'contable/libro_mayor/print';
                $view = \View::make($vistaurl, compact('cuentas', 'fecha', 'fecha_hasta', 'empresa', 'scuentas', 'filcuenta', 'id_empresa'))->render();
                $pdf = \App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot' => base_path('/')]);
                $pdf->setPaper('A4', 'portrait');

                return $pdf->stream('Mayor-'.$filcuenta.'.pdf');
            } else {
                $this->libro_mayor_excel($fecha, $fecha_hasta, $empresa, $cuentas);
            }
        }
    }

    public function libromayor(Request $r)
    {
        $sql = '';
        $sql_ = '';
        config(['data' => []]);
        $data['controlador'] = 'librodiario';
        $dato = $r->input();
        $id_empresa = session('id_empresa');
        $data['empresa'] = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $data['cuentas'] = Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->get();
        if (count($dato) > 0) {
            $desde = date('Y-m-d', strtotime(str_replace('/', '-', $r->fecha)));
            $hasta = date('Y-m-d', strtotime(str_replace('/', '-', $r->fecha_hasta)));
            $data['cuenta'] = $r->cuenta;
            $data['desde'] = $desde;
            $data['hasta'] = $hasta;
            if (substr($r->cuenta, 0, 1) == 1 || substr($r->cuenta, 0, 1) == 2 || substr($r->cuenta, 0, 1) == 3) {
                $sql_ = "(select IFNULL(ac.fecha_asiento,'') as fecha_asiento,
                    'Saldo anterior' as informacion,
                    IFNULL(pe.plan,'') as plan,'' as id_asiento_cabecera,IFNULL(pe.nombre,'Saldo anterior') as nombre,'Saldo anterior' as observacion,IFNULL(sum(ad.debe),0) as debe,IFNULL(sum(ad.haber),0) as haber,IFNULL(pe.naturaleza,'') as naturaleza,IFNULL(pe.naturaleza_2,'') as naturaleza_2,'' as identificacion,'' as socio
                    from ct_asientos_detalle ad 
                    join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id 
                    left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa 
                    left join plan_cuentas pc on ad.id_plan_cuenta=pc.id 
                    where ac.id_empresa=$id_empresa 
                     and ac.fecha_asiento>='2010-01-01' 
                    and ac.fecha_asiento<'$desde' 
                    and pe.plan like '$r->cuenta%' 
                    group by pe.plan) UNION ALL";
            }

            $sql = $sql_."(select ac.fecha_asiento,
                /*COMPRAS*/
                IF((select IFNULL(id,0) from ct_compras where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'COMPRAS',
                IF((select IFNULL(id,0) from ct_retenciones where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'RETENCION-A',
                IF((select IFNULL(id,0) from ct_comprobante_egreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'EGRESO-A',
                IF((select IFNULL(id,0) from ct_comprobante_egreso_varios where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'EGRESOV-A',
                IF((select IFNULL(id,0) from ct_comprobante_egreso_masivo where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'EGRESOM-A',
                IF((select IFNULL(id,0) from ct_debito_bancario where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'DEBITOB-A',
                IF((select IFNULL(id,0) from ct_debito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'DEBITOAC-A',
                IF((select IFNULL(id,0) from ct_cruce_valores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CRUCE-A',
                IF((select IFNULL(id,0) from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CREDITOA-A',
                /*COMPRAS*/ 
                /*VENTAS*/ 
                IF((select IFNULL(id,0) from ct_ventas where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'VENTA',
                IF((select IFNULL(id,0) from ct_comprobante_ingreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'INGRESO-C',
                IF((select IFNULL(id,0) from ct_cliente_retencion where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'RETENCION-C',
                IF((select IFNULL(id,0) from ct_cruce_valores_cliente where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CRUCE-C',
                IF((select IFNULL(id,0) from ct_cheques_post where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CHEQUEPOST-C',
                IF((select IFNULL(id,0) from ct_cruce_cuentas_clientes where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CRUCECUENTAS-C',
                IF((select IFNULL(id,0) from ct_nota_credito_clientes where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,'CREDITOCLIENTES-C',
                /*VENTAS*/
                /*BANCOS*/
                IF((select IFNULL(id,0) from ct_nota_debito_cabecera where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'NOTA DE DEBITO-B',
                IF((select IFNULL(id,0) from ct_nota_credito_cabecera where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'NOTA DE CREDITO-B',
                IF((select IFNULL(id,0) from ct_transferencia_bancaria where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'TRANSFERENCIA-B',
                IF((select IFNULL(id,0) from ct_deposito_bancario where id_asiento=ad.id_asiento_cabecera limit 1)!=0,'DEPOSITO-B','')
                )))
                /*BANCOS*/
                ))))))))))))))))
                as informacion,pe.plan,ad.id_asiento_cabecera,pe.nombre,ac.observacion,sum(ad.debe) as debe, sum(ad.haber) as haber,
                pe.naturaleza,pe.naturaleza_2,
                IF((select IFNULL(id,0) from ct_comprobante_egreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select id_proveedor from ct_comprobante_egreso where id_asiento_cabecera=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_compras where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select proveedor from ct_compras where id_asiento_cabecera=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_debito_bancario where id_asiento=ad.id_asiento_cabecera limit 1)!=0,(select id_acreedor from ct_debito_bancario where id_asiento=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select id_proveedor from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_ventas where id_asiento=ad.id_asiento_cabecera limit 1)!=0,(select id_cliente from ct_ventas where id_asiento=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_comprobante_ingreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select id_cliente from ct_comprobante_ingreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1),'')
                ))))) as identificacion,
                IF((select IFNULL(id,0) from ct_comprobante_egreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select IFNULL(nombrecomercial,razonsocial) from ct_comprobante_egreso join proveedor on proveedor.id=ct_comprobante_egreso.id_proveedor where id_asiento_cabecera=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_compras where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select IFNULL(nombrecomercial,razonsocial) from ct_compras join proveedor on proveedor.id=ct_compras.proveedor where id_asiento_cabecera=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_debito_bancario where id_asiento=ad.id_asiento_cabecera limit 1)!=0,(select IFNULL(nombrecomercial,razonsocial) from ct_debito_bancario join proveedor on proveedor.id=ct_debito_bancario.id_acreedor where ct_debito_bancario.id_asiento=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select id_proveedor from ct_credito_acreedores where id_asiento_cabecera=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_ventas where id_asiento=ad.id_asiento_cabecera limit 1)!=0,(select nombre from ct_ventas join ct_clientes on id_cliente=ct_clientes.identificacion where id_asiento=ad.id_asiento_cabecera),
                IF((select IFNULL(id,0) from ct_comprobante_ingreso where id_asiento_cabecera=ad.id_asiento_cabecera limit 1)!=0,(select nombre from ct_comprobante_ingreso join ct_clientes on id_cliente=ct_clientes.identificacion where id_asiento_cabecera=ad.id_asiento_cabecera limit 1),'')
                ))))) as socio
                from ct_asientos_detalle ad
                join ct_asientos_cabecera ac on ad.id_asiento_cabecera=ac.id
                left join plan_cuentas_empresa pe on ad.id_plan_cuenta=pe.id_plan and pe.id_empresa=ac.id_empresa
                left join plan_cuentas pc on ad.id_plan_cuenta=pc.id
                where ac.id_empresa=$id_empresa
                and ac.fecha_asiento>='$desde'
                and ac.fecha_asiento<='$hasta'
                and pe.plan like '$r->cuenta%'
                group by ad.id_asiento_cabecera
                order by ac.fecha_asiento)";
            $sql = 'select * from('.$sql.') as info order by info.fecha_asiento asc';
            $data['mayor'] = DB::select($sql);
        }
        config(['data' => $data]);

        return view('contable/libro_mayor/view_libro_mayor', $data);
    }

    public function libro_mayor_excel($fecha_desde, $fecha_hasta, $empresa, $cuentas)
    {
        Excel::create('LibroMayor-'.$fecha_desde.'-al-'.$fecha_hasta, function ($excel) use ($empresa, $fecha_desde, $fecha_hasta, $cuentas) {
            $excel->sheet('LibroMayor', function ($sheet) use ($empresa, $fecha_desde, $fecha_hasta, $cuentas) {
                $sheet->mergeCells('A1:I1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:I2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:I3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LIBRO MAYOR');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A4:I4');
                $sheet->cell('A4', function ($cell) use ($fecha_desde, $fecha_hasta) {
                    // manipulate the cel
                    $cell->setValue("$fecha_desde al $fecha_hasta");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->setColumnFormat([
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                    'I' => '0.00',
                ]);
                $i = $this->cab_detalle($sheet, 5);
                // dd('Detalle');
                $i = $this->setDetalles($sheet, $i, $cuentas, $fecha_desde, $fecha_hasta, $empresa->id);
                //  CONFIGURACION FINAL
                $sheet->cells('A3:I3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });
                $sheet->cells('A5:I5', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#cdcdcd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->setWidth([
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 12,
                    'I' => 12,
                ]);
            });
        })->export('xlsx');
    }

    public function cab_detalle($sheet, $i)
    {
        // $sheet->mergeCells('A4:A5');
        $sheet->cell("A$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('FECHA');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        // $sheet->mergeCells('B5:E5');
        $sheet->cell("B$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('ASIENTO');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell("C$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('INFORMACION');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell("D$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('CUENTA');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("E$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('NOMBRE DE LA CUENTA');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("F$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('DETALLE');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("G$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('DEBE');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("H$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('HABER');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("I$i", function ($cell) {
            // manipulate the cel
            $cell->setValue('SALDO');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $i++;

        return $i;
    }

    public function setDetalles($sheet, $n, $cuentas, $fecha_desde, $fecha_hasta, $id_empresa)
    {
        if ($fecha_desde == '') {
            $fecha_desde = '2012-01-01';
        }
        foreach ($cuentas as $cuenta) {
            $fi = str_replace('/', '-', $fecha_desde);
            $ff = str_replace('/', '-', $fecha_hasta);
            $fi = date('Y-m-d', strtotime($fi));
            $ff = date('Y-m-d', strtotime($ff));
            $registros = Ct_Asientos_Detalle::where('id_plan_cuenta', '=', $cuenta->id)
                ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                ->join('plan_cuentas as p', 'p.id', 'ct_asientos_detalle.id_plan_cuenta')
                ->join('plan_cuentas_empresa as pe', 'p.id', 'pe.id_plan')
                ->whereBetween('c.fecha_asiento', [$fi.' 00:00:00', $ff.' 23:59:59'])
                ->where('c.id_empresa', $id_empresa)
                ->orderBy('fecha_asiento', 'ASC')
                ->select('c.*', 'ct_asientos_detalle.*', 'pe.nombre')
                ->groupBy('ct_asientos_detalle.id', 'ct_asientos_detalle.debe')
                ->get();
            $saldoanterior = Ct_Asientos_Detalle::where('id_plan_cuenta', '=', $cuenta->id)
                ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                ->where('c.fecha_asiento', '<', $fi)
                ->where('c.id_empresa', $id_empresa)
                ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                ->first();
            $saldo = 0;
            $bandera = 0;
            // $n_ = 0;
            $impuesto = 0;
            if ($saldoanterior != '' and $bandera == 0) {
                $n = $this->cab_detalle_saldo($sheet, $n, $cuenta->plan, $cuenta->nombre_plan, "Saldo Anterior $fecha_desde ", $saldoanterior->saldo);
                $saldo = $saldoanterior->saldo;
            }
            foreach ($registros as $value) {
                $modulo = json_encode(Contable::recovery_by_asiento($value->cabecera->id), true);
                $modulo = json_decode($modulo, true);
                $compra = '';
                $venta = '';
                $banco = '';
                if (isset($modulo['original']['compra']['module'])) {
                    $compra = $modulo['original']['compra']['module'];
                }
                if (isset($modulo['original']['venta']['module'])) {
                    $venta = $modulo['original']['venta']['module'];
                }
                if (isset($modulo['original']['bancos']['module'])) {
                    $banco = $modulo['original']['bancos']['module'];
                }
                $sheet->cell("A$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->fecha_asiento);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // $sheet->mergeCells('B5:E5');
                $sheet->cell("B$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->id_asiento_cabecera);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell("C$n", function ($cell) use ($compra, $venta, $banco) {
                    // manipulate the cel
                    if ($compra != null) {
                        $cell->setValue($compra);
                    }
                    if ($venta != null) {
                        $cell->setValue($venta);
                    }
                    if ($banco != null) {
                        $cell->setValue($banco);
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $id_plan_cuenta = $value->id_plan_cuenta;
                $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', $value->id_plan_cuenta)->orWhere('plan', $value->id_plan_cuenta)->first();
                if (! is_null($plan_empresa)) {
                    $id_plan_cuenta = $plan_empresa->plan;
                }
                $sheet->cell("D$n", function ($cell) use ($id_plan_cuenta) {
                    // manipulate the cel
                    // $cell->setValue($value->id_plan_cuenta);
                    $cell->setValue($id_plan_cuenta);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell("E$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->nombre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $consulta_venta = null;
                if ($venta != '' or ! is_null($venta)) {
                    $consulta_venta = Ct_ventas::where('id_asiento', $value->id_asiento_cabecera)->first();
                }
                $sheet->cell("F$n", function ($cell) use ($value, $consulta_venta) {
                    // manipulate the cel
                    if (! is_null($consulta_venta)) {
                        $cell->setValue("FV: {$consulta_venta->nro_comprobante} / {$consulta_venta->concepto}");
                    } else {
                        $cell->setValue("$value->observacion");
                    }
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell("G$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->debe);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell("H$n", function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue($value->haber);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $saldo = ($saldo + $value->debe) - $value->haber;
                $sheet->cell("I$n", function ($cell) use ($saldo) {
                    // manipulate the cel
                    $cell->setValue($saldo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $n++;
                $bandera = 1;
            }
            if (count($registros) > 0) {
                $n = $this->cab_detalle_saldo($sheet, $n, $id_plan_cuenta, $value->nombre, "Saldo de $fecha_desde a $fecha_hasta", $saldo, $impuesto);
            }
        }
    }

    public function cab_detalle_saldo($sheet, $i, $cuenta, $nomcuenta, $detalle, $saldo, $impuesto = 0)
    {
        $sheet->mergeCells("A$i:B$i");
        $sheet->cell("A$i:B$i", function ($cell) {
            // manipulate the cel
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell("D$i", function ($cell) use ($cuenta) {
            // manipulate the cel
            $cell->setValue($cuenta);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("E$i", function ($cell) use ($nomcuenta) {
            // manipulate the cel
            $cell->setValue($nomcuenta);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->cell("F$i", function ($cell) use ($detalle) {
            // manipulate the cel
            $cell->setValue($detalle);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $sheet->mergeCells("G$i:H$i");
        $sheet->cell("G$i:H$i", function ($cell) {
            // manipulate the cel
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $sheet->cell("I$i", function ($cell) use ($saldo, $impuesto) {
            // manipulate the cel
            $cell->setValue($saldo - $impuesto);
            $cell->setFontWeight('bold');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });

        $i++;

        return $i;
    }

    public function buscador_secuencia(Request $request)
    {
        // dd($request['nombre']);
        if (($request['nombre']) != '') {

            $id_empresa = $request->session()->get('id_empresa');
            $registros = Ct_Asientos_Cabecera::where('id_empresa', $id_empresa)->where('fact_numero', $request['nombre'])->get();

            // dd($registros);
            return view('contable/diario/resultados_tabla', ['registros' => $registros]);
        }

        return 'no datos';
    }

    public function buscar_proveedor(Request $request)
    {
        if (! is_null($request['id_proveedor'])) {
            $proveedor = $request['id_proveedor'];
            $id_empresa = $request->session()->get('id_empresa');
            $registros = DB::table('ct_asientos_detalle as a')
                ->join('ct_asientos_cabecera as c', 'c.id', 'a.id_asiento_cabecera')
                ->join('ct_compras as co', 'co.id', 'c.id_ct_compras')
                ->join('proveedor as p', 'p.id', 'co.proveedor')
                ->where('a.id_empresa', $id_empresa)
                ->where('p.nombrecomercial', $proveedor)
                ->select('c.*')->orderby('id', 'desc')
                ->get();
            if ($registros != '[]') {
                return view('contable/diario/resultados_tabla', ['registros' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        } elseif (isset($request['concepto'])) {
            $concepto = '%'.$request['concepto'].'%';
            $id_empresa = $request->session()->get('id_empresa');
            $registros = Ct_Asientos_Cabecera::where('id_empresa', $id_empresa)
                ->where('observacion', 'like', $concepto)->orderby('id', 'desc')
                ->get();
            if ($registros != '[]') {
                return view('contable/diario/resultados_tabla', ['registros' => $registros]);
            } else {
                return ['value' => 'no resultados'];
            }
        }

        return ['value' => 'no resultados'];
    }

    public function buscador_fecha(Request $request)
    {
        $fecha = $request['fechaini'];
        $fecha_hasta = $request['fecha_hasta'];
        if ($fecha != '' && $fecha_hasta != '') {
            $registros = Ct_Asientos_Cabecera::whereBetween('fecha_asiento', [$fecha.' 00:00:00', $fecha_hasta.' 23:59:59'])->paginate(20);

            return view('contable/diario/resultados_tabla', ['registros' => $registros]);
        }

        return 'no data';
    }

    public function buscar_cuenta_diario(Request $request)
    {
        $cuenta = Plan_Cuentas::find($request['nombre']);

        return view('contable/libro_mayor/unico', ['cuenta' => $cuenta, 'contador' => $request['contador']]);
    }

    public function anular_asiento($id, Request $request)
    {
        // dd($request->all());
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;
        $asiento = Ct_Asientos_Cabecera::findorfail($id);
        // dd($asiento);
        $asiento->tipo = 0;
        $asiento->estado = 1;
        $asiento->id_usuariomod = $idusuario;
        $asiento->save();
        $detalles = $asiento->detalles;
        $concepto = $request['concepto'];
        $estado_compras = Ct_Asientos_Cabecera::where('id', $id)->first();
        if (! is_null($estado_compras)) {
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion' => 'ANULACION '.$asiento->observacion,
                'fecha_asiento' => $asiento->fecha_asiento,
                'valor' => $asiento->valor,
                'tipo' => '2',
                'id_empresa' => $asiento->id_empresa,
                'aparece_sri' => $asiento->aparece_sri,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'comentario_anulacion' => $concepto,
                'sucursal' => $asiento->sucursal,
            ]);
            foreach ($detalles as $value) {
                $value->estado = 1;
                $value->save();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta' => $value->id_plan_cuenta,
                    'debe' => $value->haber,
                    'haber' => $value->debe,
                    'descripcion' => $value->descripcion,
                    'fecha' => $asiento->fecha_asiento,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ]);
            }
            Log_Contable::create([
                'tipo' => 'L',
                'valor_ant' => $asiento->valor,
                'valor' => $asiento->valor,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'observacion' => $asiento->concepto,
                'id_ant' => $id,
                'id_referencia' => $id_asiento,
            ]);
        }

        return 'ok';
    }

    public function checkpass(Request $request)
    {
        $hashedPassword = Auth::user()->password;
        $ingresada = $request['userpass'];
        if (Hash::check($ingresada, $hashedPassword)) {
            return 'ok';
        } else {
            return 'error';
        }
    }

    public function buscar_asiento(Request $request)
    {
        if (! is_null($request['id_asiento']) && ! is_null($request['validacion'])) {
            $id_d = '';
            switch ($request['validacion']) {
                case '9':
                    $ingresos = Ct_Comprobante_Ingreso_Varios::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $ingresos->id_asiento_cabecera;
                    $id_d = [$id_asiento, $ingresos->secuencia];
                    break;
                case '8':
                    $ingresos = Ct_Comprobante_Ingreso::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $ingresos->id_asiento_cabecera;
                    $id_d = [$id_asiento, $ingresos->secuencia];
                    break;
                case '10':
                    $nota_debito = Ct_Nota_Debito_Cliente::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $nota_debito->id_asiento_cabecera;
                    $id_d = [$id_asiento, $nota_debito->secuencia];
                    break;
                case '7':
                    $comprobante_egreso = Ct_Comprobante_Egreso::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    if (is_null($comprobante_egreso)) {
                        return ['value' => 'No se encontraron resultados'];
                    }
                    $id_asiento = $comprobante_egreso->id_asiento_cabecera;
                    $id_d = [$id_asiento, $comprobante_egreso->secuencia];
                    break;
                case '6':
                    $comprobante_egreso_varios = Ct_Comprobante_Egreso_Varios::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $comprobante_egreso_varios->id_asiento_cabecera;
                    $id_d = [$id_asiento, $comprobante_egreso_varios->secuencia];
                    break;
                case '5':
                    $nota_debito = Ct_Debito_Acreedores::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $nota_debito->id_asiento_cabecera;
                    $id_d = [$id_asiento, $nota_debito->secuencia];
                    break;
                case '4':
                    $nota_debito = Ct_Credito_Acreedores::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $nota_debito->id_asiento_cabecera;
                    $id_d = [$id_asiento, $nota_debito->secuencia];
                    break;
                case '3':
                    $nota_debito = Ct_Cruce_Valores_Cliente::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $nota_debito->id_asiento_cabecera;
                    $id_d = [$id_asiento, $nota_debito->secuencia];
                    break;
                case '2':
                    $nota_debito = Ct_Cruce_Valores::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $nota_debito->id_asiento_cabecera;
                    $id_d = [$id_asiento, $nota_debito->secuencia];
                    break;
                case '1':
                    $nota_debito = Ct_Nota_Debito_Cliente::where('estado', '1')->where('id', $request['id_asiento'])->first();
                    $id_asiento = $nota_debito->id_asiento_cabecera;
                    $id_d = [$id_asiento, $nota_debito->secuencia];
                    break;
            }

            return $id_d;
        } else {
            return 'error faltan campos vacios';
        }
    }

    public function modal_estado(Request $request, $id)
    {
        if (! is_null($request['fecha']) && ! is_null($request['fecha_hasta'])) {
            $fecha = $request['fecha'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha = date('Y-m-d');
            $fecha_hasta = date('Y-m-d');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $compras = DB::table('ct_compras as ct_c')
            ->leftjoin('proveedor as p', 'p.id', 'ct_c.proveedor')
            ->join('users as u', 'u.id', 'ct_c.id_usuariocrea')
            ->where('ct_c.estado', '1')
            ->select('ct_c.id', 'ct_c.numero', 'ct_c.fecha', 'p.nombrecomercial', 'ct_c.autorizacion', 'u.nombre1', 'u.apellido1', 'ct_c.secuencia_factura', 'ct_c.tipo_comprobante', 'ct_c.estado', 'ct_c.observacion', 'ct_c.id_asiento_cabecera')
            ->where('ct_c.id_empresa', $id_empresa)
            ->where('ct_c.tipo', 1)
            ->orderby('ct_c.id', 'desc')
            ->paginate(10);
        $registro = Ct_Asientos_Cabecera::findorfail($id);
        $detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $id)
            ->select('id_plan_cuenta')
            ->selectRaw('MAX(descripcion) as descripcion')
            ->selectRaw('SUM(debe) as debe, SUM(haber) as haber')
            ->groupBy('id_plan_cuenta')
            ->get();

        // dd($compras);
        // dd($edit->estado_uso);
        return view('contable/compra/modal_estado', ['compras' => $compras, 'id_empresa' => $empresa, 'registro' => $registro, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'detalle' => $detalle]);
    }

    public function cierre(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $end = date('Y', strtotime('-1 years'));
        if (isset($request->anio_cierre)) {

            if (! is_null($request->anio_cierre)) {
                $end = $request->anio_cierre;
            }
        }

        $plan_cuenta = Plan_Cuentas::where('descripcion', 'Gasto')->whereNotNull('id_padre')->get();
        // $plan_cuenta2 = Plan_Cuentas::where('descripcion', 'Ingreso')->whereNotNull('id_padre')->get();
        $plan_cuenta = Plan_Cuentas::all();
        $plan_cuenta2 = Plan_Cuentas::all();
        $plan_cuenta = Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->get();
        foreach ($plan_cuenta as $plan) {
            $plan_separado = explode('.', $plan->plan);
            if ($plan_separado[0] == 4 or $plan_separado[0] == 5) {
                // $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', $plan->id)->get();
                // foreach ($plan_empresa as $emp) {
                $plan->estado_cierre_ano = 1;
                $plan->save();
                // }
            }
        }
        $detalle = DB::table('plan_cuentas as p')
            ->join('ct_asientos_detalle as detalle', 'detalle.id_plan_cuenta', 'p.id')
            ->join('ct_asientos_cabecera as cabecera', 'cabecera.id', 'detalle.id_asiento_cabecera')
            ->join('plan_cuentas_empresa as pe', 'p.id', 'pe.id_plan')
            ->where('pe.estado_cierre_ano', 1)
            ->where('pe.id_empresa', $id_empresa)
            ->whereYear('cabecera.fecha_asiento', $end)
            // ->whereRaw('((p.descripcion LIKE "%Gasto%") OR (p.descripcion LIKE "%Ingreso%"))')
            ->groupBy('detalle.id_plan_cuenta')
            ->select(DB::raw('SUM(detalle.debe) - SUM(detalle.haber) as d'), DB::raw('SUM(detalle.haber) as haber'), DB::raw('SUM(detalle.debe) as debe'), 'p.nombre', 'p.id', 'p.naturaleza as tipo', 'cabecera.id_empresa', 'p.descripcion', 'pe.plan as plan_cuenta_empresa')
            ->where('cabecera.id_empresa', $id_empresa)->get();
        // dd(json_encode($detalle)); */
        // dd($detalle);
        // $cuentas = Plan_Cuentas::all();
        $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->select('plan_cuentas.*', 'pe.plan as cuenta_plan', 'pe.nombre as nombre_plan_empresa')->get();
        // $detalle= Ct_Asientos_Detalle::where('detalle.id_')
        // return response()->json($detalle);

        return view('contable/diario/fin', ['empresa' => $empresa, 'detalle' => $detalle, 'cuentas' => $cuentas, 'end' => $end]);
        // return response()->json($queryFin);
    }

    public function modal($id, Request $request)
    {
        $id_asiento = $id;
        // dd($id_asiento);
        $module = Contable::recovery_by_asiento($id_asiento);
        // dd($module);
        $module = json_encode($module);
        $module = json_decode($module, true);

        // dd($module);
        // dd($module['original']['compra']);
        return view('contable.libro_mayor.modal', ['id_asiento' => $id_asiento, 'module' => $module]);
    }

    public function descuadrados(Request $request)
    {
        // dd($request->all());
        $anio_actual = date('Y');
        if (isset($request->anio_asiento) or count($request->all()) > 0 or ! is_null($request->anio_asiento)) {
            $anio_actual = $request->anio_asiento;
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $busqueda = ['id_empresa' => $id_empresa, 'anio' => $anio_actual];
        $descuadrados = Contable::recovery_by_model('O', 'ASIENTO', $busqueda);
        $descuadrados = json_encode($descuadrados);
        $descuadrados = json_decode($descuadrados, true);

        return view('contable.diario.descuadrados', ['empresa' => $empresa, 'descuadrados' => $descuadrados, 'anio_asiento' => $anio_actual]);
    }

    public function AsientoNoExiste()
    {
        $empresas = Empresa::all();

        foreach ($empresas as $emp) {
            $asientos_detalles = Ct_Asientos_Cabecera::where('ct_asientos_cabecera.id_empresa', $emp->id)
                ->join('ct_asientos_detalle as det', 'det.id_asiento_cabecera', 'ct_asientos_cabecera.id')->groupBy('det.id_plan_cuenta')->select('det.id_plan_cuenta as id_cuenta')->get();

            foreach ($asientos_detalles as $det_asie) {
                $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', $det_asie->id_cuenta)->where('id_empresa', $emp->id)->first();

                if (is_null($plan_empresa)) {
                    $datos = Plan_Cuentas_Empresa::where('id_plan', $det_asie->id_cuenta)->first();
                    if (! is_null($datos)) {
                        $data_maestra = $datos['attributes'];
                        // Elimino los elementos que no necesito
                        unset($data_maestra['id'], $data_maestra['created_at'], $data_maestra['updated_at']);
                        $data_maestra['id_empresa'] = $emp->id;
                        $data_maestra['ip_modificacion'] = 'CREAR_NO_EXISTE';

                        Plan_Cuentas_Empresa::create($data_maestra);
                    }
                }
            }
        }
        if (Auth::user()->id == '0957258056') {
            dd('ok gracias amigo');
        }
    }

    public static function problemasAsientos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $asientosPorblemas = LogAsiento::faltaAsiento($id_empresa);
        $empresa = Empresa::find($id_empresa);

        return view('contable/diario/problemaAsiento', ['data' => $asientosPorblemas, 'empresa' => $empresa]);
    }

    public function existenciaModulo(Request $request)
    {
        $otherController = new ServiciosController;
        try {
            $request->merge(['parameter' => 'S', 'type' => '']);
            $sendP = $otherController->loadData($request);
            $objectF = $sendP->getData()->content->original->compra;

            return ['status' => 'success', 'data' => $objectF];
        } catch (\Throwable $th) {
            return ['status' => false, 'msj' => 'Hubo en error ....'];
        }
    }

    public function planConfiguraciones(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $cuentasConfiguradas = Ct_Configuraciones::where('id_empresa', $id_empresa);
        if (count($request->all()) > 0 and isset($request->id)) {
            $cuentasConfiguradas = $cuentasConfiguradas->where('tipo', 'like', "%{$request->id}%");
        }

        $nombre_cuenta = Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->where('estado', '<>', '0')->get();
        //   dd($nombre_cuenta[53]);

        $cuentasConfiguradas = $cuentasConfiguradas->paginate(15);

        // ANTICIPOPROV_ANT_PROV -1.01.04.03.01-

        // dd(LibroDiarioController::planConfig2('4.1.08'));

        // LibroDiarioController::masivoCuentaNuevas('TRANS_BANCARIA -5.2.03.03.03-', "0916293723001");

        return view('contable/diario/cuentas_configurar', ['cuentas' => $cuentasConfiguradas, 'data' => $request->all(), 'empresa' => $empresa, 'plan_cuentas' => $nombre_cuenta]);
    }

    public static function masivoCuentaNuevas($tipo, $id_empresa)
    {
        $config2 = Ct_Configuraciones2::where('tipo', $tipo)->where('id_empresa', $id_empresa)->first();
        $datos = $config2['original'];
        unset($datos['id']);
        // dd($datos);
        $empresas = Empresa::where('id', '!=', $id_empresa)->get();
        foreach ($empresas as $value) {
            $datos['id_empresa'] = $value->id;
            // dd($datos);
            Ct_Configuraciones2::create($datos);
        }
    }

    /* public function anular_asiento_edit($id, Request $request)
{
$ip_cliente = $_SERVER["REMOTE_ADDR"];
$idusuario  = Auth::user()->id;
$asiento    = Ct_Asientos_Cabecera::findorfail($id);
//dd($asiento);
$asiento->tipo          = 0;
$asiento->estado        = 1;
$asiento->id_usuariomod = $idusuario;
$asiento->save();
$detalles       = $asiento->detalles;
$concepto       = $request['concepto'];
$estado_compras = Ct_Asientos_Cabecera::where('id', $id)->first();
if (!is_null($estado_compras)) {
$id_asiento = Ct_Asientos_Cabecera::insertGetId([
'observacion'     => 'ANULACION ' . $asiento->observacion,
'fecha_asiento'   => $asiento->fecha_asiento,
'valor'           => $asiento->valor,
'tipo'            => '2',
'id_empresa'      => $asiento->id_empresa,
'aparece_sri'     => $asiento->aparece_sri,
'ip_creacion'     => $ip_cliente,
'ip_modificacion' => $ip_cliente,
'id_usuariocrea'  => $idusuario,
'id_usuariomod'   => $idusuario,
]);
foreach ($detalles as $value) {
$value->estado = 1;
$value->save();
Ct_Asientos_Detalle::create([
'id_asiento_cabecera' => $id_asiento,
'id_plan_cuenta'      => $value->id_plan_cuenta,
'debe'                => $value->haber,
'haber'               => $value->debe,
'descripcion'         => $value->descripcion,
'fecha'               => $asiento->fecha_asiento,
'ip_creacion'         => $ip_cliente,
'ip_modificacion'     => $ip_cliente,
'id_usuariocrea'      => $idusuario,
'id_usuariomod'       => $idusuario,
]);
}
Log_Contable::create([
'tipo'           => 'L',
'valor_ant'      => $asiento->valor,
'valor'          => $asiento->valor,
'id_usuariocrea' => $idusuario,
'id_usuariomod'  => $idusuario,
'observacion'    => $asiento->concepto,
'id_ant'         => $id,
'id_referencia'  => $id_asiento,
]);
}
return "ok";
}*/

    public function buscarPlanCuentasEmpresa(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $cuenta = [];
        if (! is_null($request['search'])) {
            $id_cuenta = Plan_Cuentas_Empresa::where('id_plan', 'LIKE', "%{$request['search']}%")->where('id_empresa', $id_empresa)->select('id_plan as id', DB::raw('CONCAT(id_plan," | ",nombre) as text'));
            $nombre_cuenta = Plan_Cuentas_Empresa::where('nombre', 'LIKE', "%{$request['search']}%")->where('id_empresa', $id_empresa)->select('id_plan as id', DB::raw('CONCAT(id_plan," | ",nombre) as text'));
            $cuenta = $id_cuenta->union($nombre_cuenta)->take(20)->get();
        }

        return response()->json($cuenta);
    }

    public function Actualizacion_Cuenta(Request $request)
    {
        $idusuario = Auth::user()->id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];

        $actualizacion = Ct_Configuraciones::find($request->id);
        $actualizacion->id_plan = $request->cuenta;
        $actualizacion->id_usuariomod = $idusuario;
        $actualizacion->ip_modificacion = $ip_cliente;
        $actualizacion->save();

        return ['status' => 'success', 'msj' => 'Guardado con Exito'];
    }

    public function index_dobleAnulacion()
    {
        $id_empresa = Session::get('id_empresa');
        $log = Log_Contable::selectRaw('COUNT(id_ant) as cantidad, id_ant')
            ->groupBy('id_ant')->get();
        $ids = [];
        foreach ($log as $value) {
            if ($value->cantidad > 1) {
                array_push($ids, $value->id_ant);
            }
        }

        $logContable = Log_Contable::whereIn('id_ant', $ids)
            ->join('ct_asientos_cabecera as cab', 'cab.id', 'log_contable.id_ant')
            ->join('ct_asientos_cabecera as cab2', function ($q) use ($id_empresa) {
                $q->on('cab2.id', 'log_contable.id_referencia')
                    ->where('cab2.id_empresa', '=', $id_empresa);
            })
            // JOIN ct_asientos_cabecera cac ON id_referencia = cac.id AND cac.id_empresa =  '0993075000001'
            ->join('users as us', 'us.id', 'log_contable.id_usuariocrea')
            ->where('cab.id_empresa', $id_empresa)
            ->select('log_contable.*', DB::raw('CONCAT(us.nombre1, " ", us.apellido1) as nombre_usuario'), DB::Raw("(SELECT COUNT(lc.id_referencia) FROM log_contable lc 
            join `ct_asientos_cabecera` as `cab3` on `cab3`.`id` = lc.id_referencia AND cab3.id_empresa = $id_empresa  
            WHERE lc.id_ant = log_contable.id_ant
            GROUP BY lc.id_ant) as aux"))
            ->orderBy('log_contable.id_ant', 'ASC')
            ->get();

        // echo '<pre>';
        // print_r($logContable);
        // exit;
        return view('contable.diario.doble_anulacion', ['logContable' => $logContable]);
    }
}
