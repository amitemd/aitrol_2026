<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Conglomerado_Productos;
use Sis_medico\Ct_Detalle_Inventario;
use Sis_medico\Ct_Detalle_Rubro_Inventario;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_Nota_Inventario;
use Sis_medico\Ct_productos;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\TipoProveedor;

class NotaInventarioController extends Controller
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

    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $inventario = Ct_Nota_Inventario::where('id_empresa', $id_empresa)->orderBy('id', 'desc')->where('estado', '!=', '-1')->paginate(15);
        //dd($acreedores);

        return view('contable/nota_inventario/index', ['inventario' => $inventario, 'empresa' => $empresa]);
    }

    public function create(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //$pais = Pais::all();
        $tipos      = TipoProveedor::all();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        //2.01.01
        //$id_padre   = Plan_Cuentas::where('id_padre', '2.01.03')->get();

        $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('N/I_CUENTAS_DOCUMENTOSXPAGAR');
        $id_padre   = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->where('pe.id_padre', $cuenta->cuenta_guardar)->select('plan_cuentas.*')->get();

        $bodega     = Ct_Bodegas::where('estado', '1')->get();
        return view('contable/nota_inventario/create', ['tipos' => $tipos, 'id_padre' => $id_padre, 'empresa' => $empresa, 'bodega' => $bodega]);
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'id'       => $request['id'],
            'concepto' => $request['concepto'],
        ];
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor  = $this->doSearchingQuery($constraints, $id_empresa);
        //dd($constraints);

        $empresa = Empresa::where('id', $id_empresa)->first();
        return view('contable/nota_inventario/index', ['inventario' => $proveedor, 'searchingVals' => $constraints, 'empresa' => $empresa]);
    }

    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Ct_Nota_Inventario::where('id_empresa', $id_empresa);
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->orderBy('id', 'desc')->paginate(15);
    }
    private function validateInput($request)
    {
        $this->validate($request, []);
    }

    public function store(Request $request)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_empresa     = $request->session()->get('id_empresa');
        $idusuario      = Auth::user()->id;
        $log_errores    = "";
        $contador_ctv   = DB::table('ct_nota_inventario')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura = 0;
        if ($contador_ctv == 0) {
            $num            = '1';
            $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
        } else {

            //Obtener Ultimo Registro de la Tabla ct_nota_inventario
            $max_id = DB::table('ct_nota_inventario')->where('id_empresa', $id_empresa)->latest()->first();
            $max_id = intval($max_id->secuencia);

            if (($max_id >= 1) && ($max_id <= 10)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }

            if (($max_id >= 10) && ($max_id < 1000)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }

            if (($max_id >= 100) && ($max_id < 1000)) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }

            if ($max_id == 1000) {
                $numero_factura = $max_id;
            }
        }
        $cabecera = [
            'observacion'     => $request['concepto'],
            'fecha_asiento'   => $request['fecha_hoy'],
            'fact_numero'     => $request['secuencia_factura'],
            'valor'           => $request['total'],
            'id_empresa'      => $id_empresa,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];

        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabecera);
        $input               = [
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'concepto'            => $request['concepto'],
            'fecha'               => $request['fecha_hoy'],
            'id_empresa'          => $id_empresa,
            'id_bodega'           => $request['id_bodega'],
            'estado'              => '1',
            'secuencia'           => $numero_factura,
            'valor_contable'      => $request['total'],
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
        ];
        $id_inventario = Ct_Nota_Inventario::insertGetId($input);
        $contador1     = $request['contador'];
        $contador2     = $request['contadora'];
        $primerarray   = array();
        $valor_total   = 0;
        $totas         = 0;
        $arr_total     = [];
        $acumul        = 0;
        $cuentauno     = 0;
        $ts            = 0;
        if (!is_null($contador1)) {
            for ($i = 0; $i <= $contador1; $i++) {
                if ($request['precio' . $i] > 0) {
                    Ct_Detalle_Inventario::create([
                        'id_inventario'   => $id_inventario,
                        'codigo'          => $request['codigo' . $i],
                        'nombre'          => $request['nombre' . $i],
                        'bodega'          => $request['bodega' . $i],
                        'cantidad'        => $request['cantidad' . $i],
                        'total'           => $request['extendido' . $i],
                        'costo'           => $request['precio' . $i],
                        'estado'          => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                }
                $verificar   = 0;
                $cuentatotal = "";

                if (!is_null($request['codigo' . $i])) {
                    $productos = Ct_productos::where('codigo', $request['codigo' . $i])->first();
                    if (!is_null($productos)) {

                        $segundoarray = [$productos->cta_costos, $request['extendido_' . $i], "", 0, 0, $productos->codigo];
                        $valor_total  = $request['precio' . $i];
                        $cuentauno    = $productos->cta_gastos;
                        $key          = array_search($productos->codigo, array_column($primerarray, '0'));

                        if ($key !== false) {
                            $verificar++;
                            $valor_total += $request['precio' . $i];
                            $valor                = $primerarray[$key][1];
                            $valor                = $valor + $request['extendido_' . $i];
                            $primerarray[$key][0] = $productos->cta_costos;
                            $cuentauno            = $productos->cta_costos;
                            $cuentatotal          = $productos->cta_costos;
                            $primerarray[$key][1] = $valor;

                            $primerarray[$key][2] = $productos->impuesto_iva_compras;
                            $primerarray[$key][3] = 0;
                            $primerarray[$key][4] = $productos->codigo;
                        } else {
                            array_push($primerarray, $segundoarray);
                        }
                    }
                }
                if ($request["nombre" . $i] != "" || $request["nombre" . $i] != null) {
                    $arr = [
                        'nombre'    => $request["nombre" . $i],
                        'cantidad'  => $request["cantidad" . $i],
                        'bodega'    => $request["bodega" . $i],
                        'codigo'    => $request["codigo" . $i],
                        'precio'    => $request["precio" . $i],
                        'extendido' => $request["extendido" . $i],
                    ];

                    array_push($arr_total, $arr);
                }
            }

            $cuentapadre = "";

            foreach ($arr_total as $vas) {
                $detalle = [
                    'id_inventario'   => $id_inventario,
                    'codigo'          => $vas['codigo'],
                    'nombre'          => $vas['nombre'],
                    'bodega'          => $vas['bodega'],
                    'cantidad'        => $vas['cantidad'],
                    'costo'           => $vas['precio'],
                    'extendido'       => $vas['extendido'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];

                Ct_Conglomerado_Productos::create($detalle);
            }

            for ($file = 0; $file < count($primerarray); $file++) {

                $cuent_descrip = Plan_Cuentas::where('id', $primerarray[$file][0])->first();
                $cuentapadre   = $cuent_descrip;
                $cuenta        = $primerarray[$file][0];
                $debe          = number_format($primerarray[$file][1], 2, '.', '');
                $codigo        = $primerarray[$file][4];
                if (!is_null($codigo)) {
                    $productos = Ct_productos::where('codigo', $codigo)->first();
                    if (!is_null($productos)) {
                        /*
                    $inventarios_codigo= Ct_Inventario::where('producto_id',$productos->id)->where('bodega_id',$request['bodega'.$file])->first();
                    if(!is_null($inventarios_codigo) && $inventarios_codigo!='[]'){
                    $total_producto= $inventarios_codigo->existencia+$request['cantidad'.$i];
                    $ijn= [
                    'existencia'=>$total_producto,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    ];
                    $inventarios_codigo->update($ijn);
                    $log_errores.=" funciona ";
                    }else{
                    Ct_Conglomerado_Productos::create([
                    'id_inventario'=>$id_inventario,
                    'codigo'=>$request['codigo'.$file],
                    'nombre'=>$request['nombre'.$file],
                    'bodega'=>$request['bodega'.$file],
                    'cantidad'=>$request['cantidad'.$file],
                    'total'=>$request['extendido'.$file],
                    'costo'=>$request['precio'.$file],
                    'estado'=>'1',
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    ]);
                    Ct_Inventario::create([
                    'producto_id'=>$productos->id,
                    'bodega_id'=>$request['id_bodega'],
                    'id_empresa'=>$id_empresa,
                    'existencia'=>'1',
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    ]);
                    }*/
                    } else {
                        $log_errores .= " no se encontro el producto ";
                    }
                }
                $acumul += $debe;
            }

            if ($cuent_descrip != '[]' && !is_null($cuent_descrip)) {
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta,
                    'descripcion'         => $cuent_descrip->nombre,
                    'fecha'               => date('Y-m-d'),
                    'haber'               => $valor_total,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            } else {
                $log_errores .= "en el haber no encontro una cuenta ";
            }

            if (!is_null($cuentapadre)) {
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuentapadre->id,
                    'descripcion'         => $cuentapadre->nombre,
                    'fecha'               => date('Y-m-d'),
                    'debe'                => $acumul,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            } else {
                //1.01.03.01.01
                
                $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('N/I_INVENTARIOS_PROD_TERM_MERCAD_ALMACEN_COMPRADO_TERCEROS');
                $log_errores .= "entro a la cuenta por defecto";
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.03.01.02',
                    //'descripcion'         => 'Productos Terminado (Compras)',
                    'id_plan_cuenta'      => $cuenta->cuenta_guardar,
                    'descripcion'         => $cuenta->nombre_mostrar,
                    'fecha'               => date('Y-m-d'),
                    'debe'                => $acumul,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
        } else {
            return response()->json('error');
        }
        if (!is_null($contador2)) {
            $vr  = 0;
            $cue = 0;

            $primerarray2 = array();

            for ($i = 0; $i < $contador2; $i++) {

                if (!is_null($request['codigo_' . $i])) {
                    if ($request['visibilidad_' . $i] > 0) {

                        Ct_Detalle_Rubro_Inventario::create([
                            'id_inventario'   => $id_inventario,
                            'codigo'          => $request['codigo_' . $i],
                            'nombre'          => $request['nombre_' . $i],
                            'fecha'           => $request['fecha_hoy'],
                            'valor'           => $request['valor' . $i],
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    }
                    $productos = Plan_Cuentas::where('id', $request['codigo_' . $i])->first();

                    if (!is_null($productos)) {

                        $segundoarray = [$productos->id, $request['valor' . $i]];
                        $key          = array_search($productos->id, array_column($primerarray2, '0'));
                        if ($key !== false) {

                            $valor                 = $primerarray2[$key][1];
                            $valor                 = $valor + $request['valor' . $i];
                            $primerarray2[$key][0] = $productos->id;
                            $primerarray2[$key][1] = $valor;
                        } else {

                            array_push($primerarray2, $segundoarray);
                        }
                    }
                }
            }
            $cuenta        = "";
            $debe          = 0;
            $cuent_descrip = "";
            for ($file = 0; $file < count($primerarray2); $file++) {
                $cuent_descrip = Plan_Cuentas::where('id', $primerarray2[$file][0])->first();
                $cuenta        = $primerarray2[$file][0];
                $debe += number_format($primerarray2[$file][1], 2, '.', '');
                $tots = $debe - $valor_total;
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta,
                    'descripcion'         => $cuent_descrip->nombre,
                    'fecha'               => $request['fecha_hoy'],
                    'haber'               => $tots,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }

            if (!is_null($cuenta)) {
            } else {
                $log_errores .= "entro a la cuenta de emergencia";
            }
        } else {
            return response()->json('error');
        }
        $data['id']   = $id_inventario;
        $data['tipo'] = 'ING-INV';
        $msj          = Ct_Kardex::generar_kardex($data);
        $log_errores .= " el kardex respuesta:  " . $msj;
        return [$id_inventario, $id_asiento_cabecera, $numero_factura, $log_errores];
    }

    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $inventario = Ct_Nota_Inventario::where('id', $id)->first();
        $tipos      = TipoProveedor::all();
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        //$id_padre   = Plan_Cuentas::where('id_padre', '2.01.03')->get();
        $cuenta = \Sis_medico\Ct_Configuraciones::obtener_cuenta('N/I_CUENTAS_DOCUMENTOSXPAGAR');
        $id_padre   = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->where('id_padre', $cuenta->cuenta_guardar)->select('plan_cuentas.*')->get();

        $bodega     = Ct_Bodegas::where('estado', '1')->get();
        return view('contable/nota_inventario/edit', ['inventario' => $inventario, 'bodega' => $bodega, 'empresa' => $empresa]);
    }
    public function anular($id, Request $request)
    {
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        $estado_inventario = Ct_Nota_Inventario::where('id', $id)->where('estado', '<>', 0)->first();
        if (!empty($estado_inventario)) {
            $act_estado = [
                'estado'          => '0',
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $fechahoy = Date('Y-m-d H:i:s');
            Ct_Nota_Inventario::where('id', $id)->update($act_estado);
            //Necesito llenar los datos de la factura pero al revès para que cumplan los datos y quiten las cuentas en el haber
            $inventario = Ct_Nota_Inventario::where('id', $id)->first();
            //$contador_ctv = DB::table('Ct_Nota_Inventario')->get()->count();
            $id_empresa = $request->session()->get('id_empresa');

            $cabecera  = Ct_Asientos_Cabecera::where('id', $inventario->id_asiento_cabecera)->first();
            $actualiza = [
                'estado'          => '1',
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $cabecera->update($actualiza);
            $detalles   = $cabecera->detalles;
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion'     => 'ANULACIÓN ' . $cabecera->observacion,
                'fecha_asiento'   => $cabecera->fecha_asiento,
                'id_empresa'      => $id_empresa,
                'fact_numero'     => $cabecera->secuencia,
                'valor'           => $cabecera->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
            foreach ($detalles as $value) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $value->id_plan_cuenta,
                    'debe'                => $value->haber,
                    'haber'               => $value->debe,
                    'descripcion'         => $value->descripcion,
                    'fecha'               => $cabecera->fecha_asiento,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ]);
            }
            $data['id']   = $inventario;
            $data['tipo'] = 'ING-INV';
            $msj          = Ct_Kardex::anular_kardex($data);
            return redirect()->route('notainventario.index');
        }
    }
}
