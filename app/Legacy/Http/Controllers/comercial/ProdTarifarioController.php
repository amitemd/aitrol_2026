<?php

namespace Sis_medico\Http\Controllers\comercial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Validate_Decimals;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Seguro;
use Sis_medico\PrecioProducto;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Comisiones_Gastro;
use Sis_medico\Producto_Comision;
use Sis_medico\Nivel;

class ProdTarifarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);

        $productos = [];

        return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos]);
    }

    public function buscarproductos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $producto = [];
        if (!is_null($request["search"])) {
            /*$producto = Ct_productos::where('id_empresa', $id_empresa)->where('codigo', "LIKE", "%{$request['search']}%")
                ->orWhere('nombre', 'LIKE', "%{$request['search']}%")
                ->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as text'))->take(20)->get();*/
            $producto = Ct_productos::where('estado_tabla', 1)
                //->where('codigo', "LIKE", "%{$request['term']}%")
                ->whereRaw('CONCAT(codigo," | ",nombre) like ?', "%{$request['search']}%")
                ->where('id_empresa', $id_empresa)->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as value'))
                ->take(50)->get();
        }
        //dd($producto);
        return response()->json($producto);
    }

    public function buscar(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $productos = Ct_productos::where('estado_tabla', '1')->where('id', $request->producto)->get();
        // dd($productos);
        return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos, 'nombre_buscar' => null]);
    }

    public function index_tarifario(Request $request, $id_producto)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $producto = Ct_productos::where('estado_tabla', '1')->where('id', $id_producto)->where('id_empresa', $id_empresa)->first();
        $prod_tarifario = Ct_Productos_Tarifario::where('id_producto', $producto->id)->where('estado', '1')->get();

        return view('comercial/producto_tarifario/index_tarifario', ['empresa' => $empresa, 'producto' => $producto, 'prod_tarifario' => $prod_tarifario, 'id_producto' => $id_producto]);
    }

    public function crear_tarifario($id_producto)
    {

        $producto = Ct_productos::where('id', $id_producto)->where('estado_tabla', '1')->first();

        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('seguros.promo_seguro', '<>', '1')
            ->orderBy('seguros.nombre', 'asc')
            ->get();


        return view('comercial/producto_tarifario/modal_crea_tarifario', ['producto' => $producto, 'seguros' => $seguros, 'id_producto' => $id_producto]);
    }

    public function guardar_tarifario(Request $request)
    {

        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        DB::beginTransaction();
        try {

            $prod_tarifario = Ct_Productos_Tarifario::where('nivel', $request->id_nivel)->where('id_producto', $request->id_producto)->where('estado', '1')->first();


            if (is_null($prod_tarifario)) {

                if ($request->id_seguro == 1) {
                    $ct_producto = Ct_productos::where('id', $request->id_producto)->where('estado_tabla', '1')->first();
                    $arr_prod = [
                        'valor_total_paq'   => $request->precio,
                        'id_usuariomod'     => $id_usuario,
                        'ip_modificacion'   => $ip_cliente,
                    ];
                    $ct_producto->update($arr_prod);
                } else {
                    $arr = [
                        'id_seguro'         => $request->id_seguro,
                        'nivel'             => $request->id_nivel,
                        'id_producto'       => $request->id_producto,
                        'precio_producto'   => $request->precio,
                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ];

                    Ct_Productos_Tarifario::create($arr);
                }
            } else {
                DB::rollBack();
                return ['respuesta' => 'error', 'msj' => 'Ya existe el nivel', 'titulos' => 'Error'];
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function edit_tarifario($id)
    {
        $prod_tarifario = Ct_Productos_Tarifario::find($id);

        return view('comercial/producto_tarifario/modal_edit_tarifario', ['prod_tarifario' => $prod_tarifario,  'id' => $id]);
    }

    public function edit_particular($id)
    {
        $producto = Ct_productos::find($id);

        return view('comercial/producto_tarifario/modal_edit_particular', ['producto' => $producto,  'id' => $id]);
    }

    public function update_tarifario(Request $request)
    {
        //dd($request->all());
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_prod_tar = $request->id_prod_tar;
        DB::beginTransaction();
        try {
            $prod_tarifario = Ct_Productos_Tarifario::find($id_prod_tar);
            $arr_up = [
                'precio_producto'   => $request->precio,
                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ];

            $prod_tarifario->update($arr_up);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function update_particular(Request $request)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $id_producto = $request->id_producto;
            $ct_producto = Ct_productos::where('id', $id_producto)->where('estado_tabla', '1')->first();
            $arr_prod = [
                'valor_total_paq'   => $request->precio,
                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ];
            $ct_producto->update($arr_prod);
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function eliminar_tarifario($id)
    {
        $id_usuario = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $prod_tarifario = Ct_Productos_Tarifario::find($id);

        $arr_p = [
            'estado'            => 0,
            'id_usuariomod'     => $id_usuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $prod_tarifario->update($arr_p);
    }

    public function excel(Request $request){
        $id_empresa = $request->session()->get('id_empresa');

        $productos = Ct_Productos::where('estado_tabla', '1')->where("id_empresa", $id_empresa)->get();

        //$insumos_baja = $insumos_baja->get();
        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Tarifario_Pro' . $fecha_d, function ($excel) use ($productos) {

            $excel->sheet('Reporte Tarifario Productos', function ($sheet) use ($productos) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

                $fecha_d = date('Y/m/d');
                $sheet->mergeCells('A1:F2');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte Productos Tarifario');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $i = 3;

                $arrTitulos = ["Id", 'Codigo', "Nombre", "Iva", "Valor PVP", 'Grupo'];

                $arrTitulos2 = ["", "Seguro", 'Nivel', "valor"];

                $comienzo = 3;

                for ($i = 0; $i < count($arrTitulos); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                foreach ($productos as $value) {

                    $arrValue = [];

                    $precio   = $value->valor_total_paq;
                    if ($precio == null || $precio == 0) {
                        $precio_producto = PrecioProducto::where('codigo_producto', $value->codigo)->orderBy('nivel', 'asc')->get()->first();
                        if (!is_null($precio_producto)) {
                            $precio = $precio_producto->precio;
                        } else {
                            $precio = 0;
                        }
                    }
                    $iva = 'NO';
                    if ($value->iva == 1) {
                        $iva = 'Si';
                    }
                    $tipo_grupo = "";
                    if ($value->grupo == 1) {
                        $tipo_grupo = 'Insumo';
                    } elseif ($value->grupo == 2) {
                        $tipo_grupo = 'Medicamentos';
                    } elseif ($value->grupo == 3) {
                        $tipo_grupo = 'Servicios';
                    } elseif ($value->grupo == 4) {
                        $tipo_grupo = 'Procedimientos';
                    } elseif ($value->grupo == 5) {
                        $tipo_grupo = 'Otros';
                    } elseif ($value->grupo == 6) {
                        $tipo_grupo = 'Honorario Medico';
                    } elseif ($value->grupo == 7) {
                        $tipo_grupo = 'Equipo';
                    } elseif ($value->grupo == 8) {
                        $tipo_grupo = 'Honorario Anestesiologo';
                    } elseif ($value->grupo == 9) {
                        $tipo_grupo = 'Membresias';
                    }

                    $arrValue = [$value->id, $value->codigo, $value->nombre, $iva, $precio, $tipo_grupo];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i, $abec) {
                            // manipulate the cel
                            $cell->setValue($abec[$i] == "E" ? "$ " .  $arrValue[$i] : $arrValue[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    $prod_tarifario = $value->tarifarios->where("estado", '1');
                    if ($prod_tarifario->count() > 0) {
                        for ($i = 1; $i < count($arrTitulos2); $i++) {
                            $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos2, $i) {
                                // manipulate the cel
                                $cell->setValue($arrTitulos2[$i]);
                                //$cell->setBackground('#D1E9F2');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $comienzo++;
                    }
                    foreach ($prod_tarifario as $prd_tarif) {
                        // $precio_tari = $prd_tarif->precio_producto;
                        // $precio_tari1 = $precio_tari

                        $arrValue = ["", $prd_tarif->seguro->nombre, $prd_tarif->fxnivel->nombre, $prd_tarif->precio_producto];

                        for ($i = 1; $i < count($arrValue); $i++) {
                            $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i, $abec) {
                                // manipulate the cel
                                $cell->setValue($abec[$i] == "D" ? "$ " .  $arrValue[$i] : $arrValue[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                //$cell->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');   

                            });
                        }
                        $comienzo++;
                    }
                    $comienzo++;
                }
            });
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(57)->setAutosize(false);
        })->export('xlsx');
    }

    public function comisiones_index(Request $request)
    {
        $producto = Ct_productos::find($request->id_producto);
        $doctores  = Comisiones_Gastro::where("estado", 1)->get();
        $all_producto = Producto_Comision::where('id_ct_producto',$producto->id)->get();
        return view("mantenimientos_comisiones.comisiones.index", ['all_producto' => $all_producto, 'producto' => $producto, 'doctores' => $doctores]);
    }
    public function comisiones_store(Request $request)
    {
        try {
            $id_usuario = Auth::user()->id;
            Producto_Comision::create([
                'estado'    => $request['estado'],
                'id_ct_producto' => $request['producto'],
                'id_comision_gatro' => $request['doctor'],
                'valor' => $request['precio'],
                'porcentaje' => $request['porcentaje'],
                'valor_privado' => $request['precio_privado'],
                'porcentaje_privado' => $request['porcentaje_privado'],
                'id_usuario_crea' => $id_usuario,
                'id_usuario_mod' => $id_usuario,
            ]);
            return redirect()->back()->with(['message' => 'Guardo Correctamente!!']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['message' => 'Ocurrio un eror en el guardado!!']);
        }
    }

    public function modal_store(Request $request)
    {

        $user = Producto_Comision::find($request['id']);
        $doctores  = Comisiones_Gastro::where("estado", 1)->get();
        $htmlModal = '
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar valor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formulario_editar">
                        <input type="hidden" name="id_editar" id="id_editar" value="' . $user->id . '" />
                        <div class="form-group">
                            <label for="doctor" class="col-form-label">Doctor:</label>
                            <select name="doctor" id="doctor" class="form-control" required>
                                <option value="">Seleccione</option>';
                            $select = '';
                            foreach ($doctores as $doctor) {
                                $select = '';
                                if ($doctor->id == $user->id_comision_gatro)
                                $select = 'selected';
                                $htmlModal .= '<option value="' . $doctor->id . '" ' . $select . '> ' . $doctor->codigo . ' | ' . $doctor->nombre . '</option>';
                            }
                            $htmlModal .= '</select>
                        </div>
                        <div class="form-group">
                            <label for="producto_nombre" class="col-form-label">Producto:</label>
                            <input type="hidden" name="producto_nombre_id" value="'.$user->id_ct_producto.'">
                            <input type="text" class="form-control" id="producto_nombre" value="' . $user->producto->nombre . '"
                                readonly>
                        </div>

                        <div class="form-group">
                            <label for="porcentaje_editar" class="col-form-label">Porcentaje:</label>
                            <input type="number" class="form-control" value="' . $user->porcentaje . '" id="porcentaje_editar" name="porcentaje_editar" min="0"
                                step="any" required />
                        </div>

                        <div class="form-group">
                            <label for="precio_editar" class="col-form-label">Precio:</label>
                            <input type="number" class="form-control" id="precio_editar" name="precio_editar" min="0"
                                step="any" value="' . $user->valor . '" required />
                        </div>


                        <div class="form-group">
                            <label for="porcentaje_privados_editar" class="col-form-label">Porcentaje:</label>
                            <input type="number" class="form-control" value="' . $user->porcentaje_privado . '" id="porcentaje_privados_editar" name="porcentaje_privados_editar" min="0"
                                step="any" required />
                        </div>

                        <div class="form-group">
                            <label for="precio_privados_editar" class="col-form-label">Precio:</label>
                            <input type="number" class="form-control" id="precio_privados_editar" name="precio_privados_editar" min="0"
                                step="any" value="' . $user->valor_privado . '" required />
                        </div>


                        <div class="form-group">
                            <label for="estado" class="col-form-label">Estado:</label>
                            <select id="estado" name="estado" class="form-control">
                                <option value="">Seleccione</option>';
                                $selected1 = '';
                                $selected2 = '';
                                if($user->estado == 0){
                                    $selected2 = 'selected';
                                }
                                if($user->estado == 1){
                                    $selected1 = 'selected';
                                }
                               
                            $htmlModal .= '<option value="1"  '. $selected1.'>Activo</option>';
                            $htmlModal .='<option value="0"  '. $selected2.'>Inactivo</option>';
                        $htmlModal .= 
                            '</select>
                        </div>
                        <div class="form-group">
                            <button type="button" onclick="formulario_editar()" class="btn btn-xs btn-primary">
                                editar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
        return ['html' => $htmlModal];
    }

    public function modal_update(Request $request){
        $array = [];
        try{
            parse_str($request['formulario'], $array);
            $editar = Producto_Comision::find($array['id_editar']);
            $editar->id_comision_gatro = $array['doctor'];
            $editar->id_ct_producto = $array['producto_nombre_id'];
            $editar->porcentaje = $array['porcentaje_editar'];
            $editar->valor = $array['precio_editar'];
            $editar->valor_privado = $array['precio_privados_editar'];
            $editar->porcentaje_privado = $array['porcentaje_privados_editar'];
            $editar->estado = $array["estado"];
            $editar->save();

            return ['status'=>true,"msj"=>'Guardado Correctamente'];
        }catch(\Exception $e){
            return ['status'=>false,"msj"=>$e->getMessage()];
        }
    }

    //GEORGE    
    public function buscar_productos_contenido(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $nombre_producto = explode('|',$request->producto);
        
        $buscar = trim($nombre_producto[0], " ");
        //dd($buscar);
        //$productos = Ct_productos::where('estado_tabla', '1')->where('nombre','like', '%'.$request->producto.'%')->get();
        $producto1 = Ct_productos::where('codigo',"LIKE","%{$buscar}%")->where('id_empresa', $id_empresa)->where('estado_tabla', '1');
        $producto2 = Ct_productos::where('nombre', 'LIKE', "%{$buscar}%")->where('id_empresa', $id_empresa)->where('estado_tabla', '1');
        $productos = $producto1->union($producto2)->take(100)->get();
        return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos, 'nombre_buscar' => $request->producto]);

    }

    public function buscar_todos_productos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        // $productos = Ct_productos::where('nombre','like','%'.$request->term.'%')
        // ->where('estado_tabla','1')
        // ->where('id_empresa',$id_empresa)->get();
        $producto1 = Ct_productos::where('codigo',"LIKE","%{$request['term']}%")->where('estado_tabla', '1')->where('id_empresa', $id_empresa)->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as text'));;
        $producto2 = Ct_productos::where('nombre', 'LIKE', "%{$request['term']}%")->where('estado_tabla', '1')->where('id_empresa', $id_empresa)->select('id as id', DB::raw('CONCAT(codigo," | ",nombre) as text'));;
        $productos = $producto1->union($producto2)->take(100)->get();
        $arr=null;

        foreach ($productos as $prod) {
            $arr[] = array('value' => $prod->text , 'id' => $prod->id );
        }
        
        
        //return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos]);

        return $arr;
        //return view('comercial/producto_tarifario/index', ['empresa' => $empresa, 'productos' => $productos]);
    }

    public function excel_producto_tarifario(Request $request){

        //dd("aqui");
        //ini_set("max_execution_time",600);
        //ini_set('memory_limit', '10024M');

        $id_empresa = $request->session()->get('id_empresa');

        $productos = Ct_Productos::where('estado_tabla', '1')->where("id_empresa", $id_empresa)->get();

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Tarifario_Pro' . $fecha_d, function ($excel) use ($productos) {

            $excel->sheet('Reporte Tarifario Productos', function ($sheet) use ($productos) {
                //$abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T","U","V","W", "X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ","BA","BB","BC");
               
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T","U","V","W", "X","Y","Z");
                //dd(count($abec));

                $fecha_d = date('Y/m/d');
                $sheet->mergeCells('A1:O2');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte Productos Tarifario');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $i = 3;


                $arrTitulos = ["Id", 'Codigo', "Nombre", 'Grupo', "Iva", "Valor PVP"];
                //dd($arrTitulos);
                $cont_titulo = 6;
                $niveles = Nivel::where('estado',1)->get();
                foreach($niveles as $nivel){
                    $arrTitulos[$cont_titulo] = $nivel->nombre;
                    $cont_titulo++;
                }
                //dd($arrTitulos);

                $comienzo = 3;
                $j = 0; $k=0;
                for ($i = 0; $i < count($arrTitulos); $i++) {
                    if($i == 26 or $i ==52 or $i ==78){
                        $k = $i;
                        $j++;
                        
                    }
                    if($j == 0){
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                            // manipulate the cel
                            $cell->setValue($arrTitulos[$i]);
                            $cell->setBackground('#D1E9F2');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }else{
                        $sheet->cell("{$abec[$j-1]}{$abec[$i-$k]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                            // manipulate the cel
                            $cell->setValue($arrTitulos[$i]);
                            $cell->setBackground('#D1E9F2');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                }
                //dd($abec);
                $comienzo++;
                foreach ($productos as $value) {

                    $arrValue = [];

                    $precio   = $value->valor_total_paq;
                    if ($precio == null || $precio == 0) {
                        $precio_producto = PrecioProducto::where('codigo_producto', $value->codigo)->orderBy('nivel', 'asc')->get()->first();
                        if (!is_null($precio_producto)) {
                            $precio = $precio_producto->precio;
                        } else {
                            $precio = 0;
                        }
                    }
                    $iva = 'NO';
                    if ($value->iva == 1) {
                        $iva = 'Si';
                    }
                    $tipo_grupo = "";
                    if ($value->grupo == 1) {
                        $tipo_grupo = 'Insumo';
                    } elseif ($value->grupo == 2) {
                        $tipo_grupo = 'Medicamentos';
                    } elseif ($value->grupo == 3) {
                        $tipo_grupo = 'Servicios';
                    } elseif ($value->grupo == 4) {
                        $tipo_grupo = 'Procedimientos';
                    } elseif ($value->grupo == 5) {
                        $tipo_grupo = 'Otros';
                    } elseif ($value->grupo == 6) {
                        $tipo_grupo = 'Honorario Medico';
                    } elseif ($value->grupo == 7) {
                        $tipo_grupo = 'Equipo';
                    } elseif ($value->grupo == 8) {
                        $tipo_grupo = 'Honorario Anestesiologo';
                    } elseif ($value->grupo == 9) {
                        $tipo_grupo = 'Membresias';
                    }
                    //$prod_tarifario = $value->tarifarios->where("estado", '1');

                    $arrValue = [$value->id, $value->codigo, $value->nombre, $tipo_grupo ,$iva, $precio];
                    $e = 0;
                    foreach ($niveles as $nivel) {
                        
                        $prod_tarifario = $value->tarifarios->where("estado", '1')->where('nivel', $nivel->id)->first();
                        //dd($prod_tarifario,$nivel);
                        if(!is_null($prod_tarifario)){
                            $arrValue[$e+6] = $prod_tarifario->precio_producto;
                        }else{
                            $arrValue[$e+6] = 0;
                        }
                        $e++;

                    }
                    //dd($arrValue);
                    /*for ($i = 0; $i < count($arrValue); $i++) {                        
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }*/

                    $j = 0; $k=0;
                    for ($i = 0; $i < count($arrValue); $i++) {
                        if($i == 26 or $i ==52 or $i ==78){
                            $k = $i;
                            $j++;
                            
                        }
                        if($j == 0){
                            $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                                // manipulate the cel
                                $cell->setValue($arrValue[$i]);
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }else{
                            $sheet->cell("{$abec[$j-1]}{$abec[$i-$k]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                                // manipulate the cel
                                $cell->setValue($arrValue[$i]);
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                    }



                    $comienzo++;
                }
            });
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(57)->setAutosize(false);
        })->export('xlsx');
 

    }

    public function doctores_convenios_index(){
        $registros = Comisiones_Gastro::where("estado",1)->get();
        return view("mantenimientos_comisiones.doctores.index",['registros'=>$registros]);
    }
    public function doctores_convenios_crear(){
        return view("mantenimientos_comisiones.doctores.create");
    }
    public function doctores_convenios_guardar(Request $request){
        try{
            $guardar = new Comisiones_Gastro;
            $dato_validar = Comisiones_Gastro::where('codigo',$request['codigo'])->first();
            if(!is_null($dato_validar)){
                return ['status'=>false,"msj"=>'Codigo repetido'];
            }
            $guardar->codigo = $request['codigo'];
            $guardar->nombre = $request['nombre'];
            $guardar->estado = 1;
            $guardar->save();
            return ['status'=>true,"msj"=>'Guardado Correctamente'];
        }catch(\Exception $e){
            return ['status'=>false,"msj"=>$e->getMessage()];
        }
    }
    public function doctores_convenios_editar (Request $request){
        $registro = Comisiones_Gastro::find($request['id']);
        return view("mantenimientos_comisiones.doctores.edit",['registro'=>$registro]);
    }
    public function doctores_save_editar(Request $request){
        try{
            $guardar = Comisiones_Gastro::find($request->id);
            $dato_validar = Comisiones_Gastro::where('codigo',$request['codigo'])->first();
            $guardar->codigo = $request['codigo'];
            $guardar->nombre = $request['nombre'];
            $guardar->estado = $request['estado'];
            $guardar->save();
            return ['status'=>true,"msj"=>'Editado Correctamente'];
        }catch(\Exception $e){
            return ['status'=>false,"msj"=>$e->getMessage()];
        }
    }
    
}
