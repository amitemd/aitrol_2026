<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Convenio;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Detalle_Venta_Conglomerada;
use Sis_medico\Ct_Detalle_Venta_Omni;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Insumo_Plantilla;
use Sis_medico\Insumo_Plantilla_Item;
use Sis_medico\Planilla;
use Sis_medico\ProcedimientoHonorario;
use Sis_medico\Procedimiento_Detalle_Honorario;
use Sis_medico\Producto;
use Sis_medico\Tipo;

class PlantillaController extends Controller
{
    //
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $plantillas = Insumo_Plantilla::where('estado', '1')->paginate(10);

        return view('insumos/plantillas/index', ['plantillas' => $plantillas, 'nombre' => '']);
    }

    public function crear_plantilla()
    {

        return view('insumos/plantillas/crear_plantilla');
    }

    public function _buscar_item(Request $request)
    {

        $nombre    = $request['term'];
        $data      = array();
        $productos = Producto::where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value1' => $product->id, 'value' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function buscar_item(Request $request)
    {
        $productos = [];
        if ($request['search'] != null) {
            $productos = Producto::where('nombre', 'LIKE', '%' . $request['search'] . '%')->select('producto.id as id', 'producto.nombre as text')->get();
        }

        return response()->json($productos);
    }

    public function buscar_item_producto(Request $request)
    {
        $nombre    = $request['term'];
        $data      = array();
        $query = "SELECT CONCAT(codigo,' | ',nombre)
                  as completo, id, iva
                  FROM producto
                  WHERE CONCAT(codigo,' | ',nombre) like '%" . $nombre . "%'";
        $productos = DB::select($query);
        //dd($productos);
        foreach ($productos as $product) {
            $data[] = array('value1' => $product->id, 'value' => $product->completo, 'iva' => $product->iva);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function agregar_id_item(Request $request)
    {
        $nombre       = $request['nombre'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ',nombre)
                  as completo, id, iva
                  FROM producto
                  WHERE CONCAT_WS(' ',nombre) like '" . $seteo . "'";
        $nombres = DB::select($query);
        foreach ($nombres as $nombre) {
            $data[] = array(
                'value' => $nombre->completo,
                'id'    => $nombre->id,
                'iva'   => $nombre->iva,
            );
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $data;
    }

    public function guardar(Request $request)
    {

        // dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //dd($request->all());
        $planilla_id = Insumo_Plantilla::insertGetId([
            'codigo'          => $request['codigo'],
            'nombre'          => $request['nombre'],
            'estado'          => $request['estado'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);

        if (count($request->item_id) > 0) {
            foreach ($request->item_id as $item => $v) {
                // dd($request);
                $data2 = array(
                    'id_usuariocrea'  => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_producto'     => $request->id_item[$item],
                    'id_plantilla'    => $planilla_id,
                    'cantidad'        => $request->item_cant[$item],
                    'tipo'            => $request->tipo[$item],
                    'orden'           => '1',

                );
                //dd($data2);

                Insumo_Plantilla_Item::insert($data2);
            }
        }

        //return redirect()->intended('/insumos/plantillas');
        return "ok";
    }

    public function edit($id)
    {

        $plantilla = Insumo_Plantilla::where('id', $id)->where('estado', '1')->first();

        $plantillas_items = Insumo_Plantilla_Item::where('id_plantilla', $id)
            ->join('producto as prod', 'prod.id', 'insumo_plantilla_item.id_producto')
            ->select('prod.codigo', 'insumo_plantilla_item.id_plantilla', 'insumo_plantilla_item.id_producto', 'insumo_plantilla_item.orden', 'insumo_plantilla_item.cantidad', 'prod.nombre as nom_prod', 'insumo_plantilla_item.tipo as tipo')->get();

        //dd($id);

        return view('insumos/plantillas/editar', ['plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id]);
    }

    public function update(Request $request, $id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        $plant       = Insumo_Plantilla::find($id);
        $planilla_id = [
            'codigo'          => $request['codigo'],
            'nombre'          => $request['nombre'],
            'estado'          => $request['estado'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $plant->update($planilla_id);

        if ($request->has("item_id")) {
            $variable = insumo_plantilla_item::where('id_plantilla', $id);
            //dd($eliminar);
            if (!is_null($variable)) {
                $variable->delete();
            }
            if (count($request->item_id) > 0) {
                foreach ($request->item_id as $item => $v) {
                   
                    $data2 = array(
                        'id_usuariocrea'  => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                        'id_producto'     => $request->id_item[$item],
                        'id_plantilla'    => $id,
                        'cantidad'        => $request->item_cant[$item],
                        'orden'           => $request->orden[$item],
                        'tipo'            => $request->tipo[$item],
                    );

                    Insumo_Plantilla_Item::insert($data2);
                }
            }
        }
       
        return "ok";
    }

    public function item_lista($id)
    {

        $plantilla = Insumo_Plantilla::where('id', $id)->where('estado', '1')->first();

        $plantillas_items = Insumo_Plantilla_Item::where('id_plantilla', $id)
            ->join('producto as prod', 'prod.id', 'insumo_plantilla_item.id_producto')
            ->select('insumo_plantilla_item.id_plantilla', 'insumo_plantilla_item.id_producto', 'insumo_plantilla_item.orden', 'insumo_plantilla_item.cantidad', 'prod.nombre as nom_prod', 'insumo_plantilla_item.tipo as tipo')->get();

        //dd($id);

        return view('insumos/plantillas/item_lista', ['plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id]);
    }

    public function buscar(Request $request)
    {
        $nombre = $request['nombre'];
        //dd($nombre);

        $plantillas = Insumo_Plantilla::where('nombre', 'like', '%' . $nombre . '%')->where('estado', '1')->paginate(10);

        return view('insumos/plantillas/index', ['nombre' => $nombre, 'plantillas' => $plantillas]);
    }

    public function item_lista2($id, $hcid, $tipo)
    {

        $plantilla = Insumo_Plantilla::where('id', $id)->where('estado', '1')->first();

        $plantillas_items = Insumo_Plantilla_Item::where('id_plantilla', $id)
            ->join('producto as prod', 'prod.id', 'insumo_plantilla_item.id_producto')
            ->select('insumo_plantilla_item.id_plantilla', 'insumo_plantilla_item.id_producto', 'insumo_plantilla_item.orden', 'insumo_plantilla_item.cantidad', 'prod.nombre as nom_prod', 'prod.codigo')
            ->where('insumo_plantilla_item.tipo', $tipo)
            ->get();

        return view('insumos/plantillas/item_lista2', ['plantilla' => $plantilla, 'plantillas_items' => $plantillas_items, 'id' => $id, 'hcid' => $hcid]);
    }

    public function eliminar_plantilla($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $plantilla  = Insumo_Plantilla::find($id);

        $input = [
            'estado'          => 0,
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];

        $plantilla->update($input);

        return redirect()->intended('/insumos/plantillas');
    }

    ###############################################################
    #                   P D F   C O S T O
    ###############################################################

    public function genera_honorarios($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $hcp      = hc_procedimientos::find($id);
        $hc       = $hcp->historia;
        $agenda   = $hc->agenda;
        $paciente = $agenda->paciente;
        $empresa  = Empresa::find($agenda->id_empresa);
        //HONORARIOS MEDICOS Y ANASTESIOLOGICOS 18/FEB/2022 VICTOR
        //dd($hcp);
        $pxs         = $hcp->hc_procedimiento_f;
        $principal   = '';
        $secundarios = [];
        foreach ($pxs as $px) {

            if ($px->procedimiento->id_grupo_procedimiento != null) {
                $principal = $px->id_procedimiento;
            }

            if ($px->procedimiento->id_grupo_procedimiento == null) {
                $secundarios[] = $px->id_procedimiento;
            }
        }

        $seguro = $hc->seguro;
        if ($hcp->id_seguro != null) {
            $seguro = $hcp->seguro;
        }

        $id_empresa = $agenda->id_empresa;
        if ($hcp->id_empresa != null) {
            $id_empresa = $hcp->id_empresa;
        }

        $incluir_convenio = false;
        $convenio         = null;

        if ($seguro->tipo == 0) {
            //PUBLICO
            $convenio = Convenio::where('id_empresa', $id_empresa)->where('id_seguro', $seguro->id)->first();
        }

        if ($seguro->tipo == 1) {
            //PRIVADO

            $convenios = Convenio::where('id_seguro', $seguro->id)->get();
            if ($convenios->count() > 1) {
                $incluir_convenio = true;
                $convenio         = $convenios->first(); //DEBERIA PEDIR  NIVEL CUANDO TIENE VARIOS
            }

            if ($convenios->count() == 1) {
                $incluir_convenio = false;
                $convenio         = $convenios->first();
            }
        }

        $id_nivel = null;
        if (!is_null($convenio)) {
            $id_nivel = $convenio->id_nivel;
        }
        //dd($principal , $secundarios);

        $valor_principal          = 0;
        $valor_principal_anes     = 0;
        $honorario_principal      = ProcedimientoHonorario::where('id_procedimiento', $principal)->whereNull('id_proc_secu')->where('tipo', '1')->first();
        $anestesiologia_principal = ProcedimientoHonorario::where('id_procedimiento', $principal)->whereNull('id_proc_secu')->where('tipo', '2')->first();

        if (!is_null($honorario_principal)) {
            $valor_principal = $honorario_principal->valor_particular;
            if (!is_null($convenio)) {
                $valor_principal_convenio = $honorario_principal->valor_nivel->where('nivel', $id_nivel)->first();
                if (!is_null($valor_principal_convenio)) {
                    $valor_principal = $valor_principal_convenio->valor;
                }
            }
            $detalle_proc_hono = Procedimiento_Detalle_Honorario::where('id_hc_procedimientos', $hcp->id)->where('id_proc_conve', $honorario_principal->id)->where('estado', '1')->first();
            if (is_null($detalle_proc_hono)) {
                Procedimiento_Detalle_Honorario::create([
                    'id_proc_conve'        => $honorario_principal->id,
                    'id_hc_procedimientos' => $hcp->id,
                    'nivel'                => $id_nivel,
                    'estado'               => '1',
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'descripcion'          => $honorario_principal->descripcion,
                    'valor'                => $valor_principal,
                ]);
            }
        } //dd($valor_principal->valor,$convenio);
        //dd($honorario_principal->valor_nivel);
        if (!is_null($anestesiologia_principal)) {
            $valor_principal_anes = $anestesiologia_principal->valor_particular;
            if (!is_null($convenio)) {
                $valor_principal_anes_convenio = $anestesiologia_principal->valor_nivel->where('nivel', $id_nivel)->first();
                if (!is_null($valor_principal_anes_convenio)) {
                    $valor_principal_anes = $valor_principal_anes_convenio->valor;
                }
            }
            $detalle_proc_hono = Procedimiento_Detalle_Honorario::where('id_hc_procedimientos', $hcp->id)->where('id_proc_conve', $anestesiologia_principal->id)->where('estado', '1')->first();
            if (is_null($detalle_proc_hono)) {
                Procedimiento_Detalle_Honorario::create([
                    'id_proc_conve'        => $anestesiologia_principal->id,
                    'id_hc_procedimientos' => $hcp->id,
                    'nivel'                => $id_nivel,
                    'estado'               => '1',
                    'id_usuariocrea'       => $idusuario,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                    'ip_modificacion'      => $ip_cliente,
                    'descripcion'          => $anestesiologia_principal->descripcion,
                    'valor'                => $valor_principal_anes,
                ]);
            }
        }

        $secundario = [];
        foreach ($secundarios as $secundario) {
            //dd($secundario);
            $valor_secundario          = 0;
            $valor_secundario_anes     = 0;
            $honorario_secundario      = ProcedimientoHonorario::where('id_procedimiento', $principal)->where('id_proc_secu', $secundario)->where('tipo', '1')->first();
            $anestesiologia_secundario = ProcedimientoHonorario::where('id_procedimiento', $principal)->where('id_proc_secu', $secundario)->where('tipo', '2')->first();
            if (!is_null($honorario_secundario)) {
                $valor_secundario  = $honorario_secundario->valor_particular;
                $detalle_proc_hono = Procedimiento_Detalle_Honorario::where('id_hc_procedimientos', $hcp->id)->where('id_proc_conve', $honorario_secundario->id)->where('estado', '1')->first();
                if (is_null($detalle_proc_hono)) {
                    Procedimiento_Detalle_Honorario::create([
                        'id_proc_conve'        => $honorario_secundario->id,
                        'id_hc_procedimientos' => $hcp->id,
                        'nivel'                => $id_nivel,
                        'estado'               => '1',
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'descripcion'          => $honorario_secundario->descripcion,
                        'valor'                => $valor_secundario,
                    ]);
                }
            } //dd($valor_principal->valor,$convenio);
            //dd($honorario_principal->valor_nivel);
            if (!is_null($anestesiologia_secundario)) {
                $valor_secundario_anes = $anestesiologia_secundario->valor_particular;
                $detalle_proc_hono     = Procedimiento_Detalle_Honorario::where('id_hc_procedimientos', $hcp->id)->where('id_proc_conve', $anestesiologia_secundario->id)->where('estado', '1')->first();
                if (is_null($detalle_proc_hono)) {
                    Procedimiento_Detalle_Honorario::create([
                        'id_proc_conve'        => $anestesiologia_secundario->id,
                        'id_hc_procedimientos' => $hcp->id,
                        'nivel'                => $id_nivel,
                        'estado'               => '1',
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'descripcion'          => $anestesiologia_secundario->descripcion,
                        'valor'                => $valor_secundario_anes,
                    ]);
                }
            }
        }
        //
    }
    public function ExportarPlanillaCostoDetalle($id, $id_hc_procedimiento)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $planilla   = array();
        $fact_venta = array();
        $orden      = array();
        $detalles   = array();
        $hcp        = hc_procedimientos::find($id_hc_procedimiento);
        $hc         = $hcp->historia;
        $agenda     = $hc->agenda;
        $paciente   = $agenda->paciente;
        $empresa    = Empresa::find($agenda->id_empresa);
        $planilla   = $this->planillaProcedimiento($id_hc_procedimiento);
        $this->genera_honorarios($id);
        $equipos = DB::table('equipo_historia as eh')->join('equipo as e', 'e.id', 'eh.id_equipo')->where('hcid', $hcp->id_hc)->select('e.*', 'eh.*')->get();
        $detalle_pdf = Procedimiento_Detalle_Honorario::where('id_hc_procedimientos', $hcp->id)->where('estado', '1')->get();
        if (!isset($planilla->id)) {
            $detalles = '[]';
        } else {
            $detalles = $planilla->detalles_validos;
        }
        $html = '';
        $acumt = 0;
        $cabeceras = array(1 => 'HONORARIOS MEDICOS', 2 => 'MEDICINAS VALOR AL ORIGEN', 3 => 'INSUMOS VALOR AL ORIGEN', 4 => 'IMAGEN (*)', 5 => 'SERVICIOS INSTITUCIONALES',  0 => 'OTROS');
        $acum_honorarios = 0;
        $id_ = '';
        $cedula_ = '';
        $paciente_ = '';
        $procedimiento_ = '';
        $fecha_ = '';
        if (isset($planilla->id)) {
            $id_ = $planilla->id;
        }
        if (isset($paciente->id)) {
            $cedula_ = $paciente->id;
        }
        if (isset($paciente->id)) {
            $paciente_ = $paciente->nombre1 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;
        }
        if (isset($hcp->hc_procedimiento_final->procedimiento)) {
            $procedimiento_ = $hcp->hc_procedimiento_final->procedimiento->nombre;
        }
        if (isset($hc)) {
            $fecha_ = date('d/m/Y', strtotime($hc->created_at));
        }
        if (count($detalles) > 0) {
            $html .= '<table>
            <thead>
            <tr>
              <th>fecha</th>
              <th>codigo</th>
              <th>descripcion</th>
              <th>cantidad</th>
              <th>costo</th>
              <th>subtotal</th>
              <th>subtotal_25</th>
              <th>tarifa_0</th>
              <th>tarifa_12</th>
              <th>iva</th>
              <th>total</th>
            </tr>
            </thead>
            <tbody>';
            foreach ($cabeceras as $key => $row) {
                $valor_25 = 0;
                $acumtotal = 0;
                if ($key == '1') {
                    foreach ($detalle_pdf as $dpdf) {
                        $valor_25 = ($dpdf->valor * 0.25) + $dpdf->valor;
                        $acum_honorarios = $acum_honorarios + $dpdf->valor;
                        $html .= '<tr>
                            <td>' . substr($dpdf->created_at, 0, 10) . '</td>
                            <td></td>
                            <td>' . $dpdf->descripcion . '</td>
                            <td>1</td>
                            <td> ' . $dpdf->valor . ' </td>
                            <td> ' . $dpdf->valor . ' </td>
                            <td> ' . str_replace('.', ',', str_replace(',', '', number_format($valor_25, 4))) . ' </td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>' . str_replace('.', ',', str_replace(',', '', number_format($valor_25, 4))) . '</td>

                          </tr>';
                    }
                }
                if (count($detalles) > 0) {
                    foreach ($detalles as $item) {
                        $valor_25 = (((float)$item->cantidad * (float)$item->precio) * 0.25) + ((float)$item->cantidad * (float)$item->precio);
                        if ($item->tipo_plantilla == null) {
                            $item->tipo_plantilla = 0;
                        }
                        if (isset($item->producto)  and $item->producto->tipo == $key) {
                            if ($item->check == 1) {
                                $html .= '<tr>
                                    <td>' . date('d/m/Y', strtotime($item->created_at)) . '</td>
                                    <td></td>'; //se quito el codigo
                            }
                            if (isset($item->producto)) {
                                $producto_contable = $item->producto->producto_contable();
                            } else {
                                $producto_contable = '[]';
                            }
                            if ($producto_contable != '[]' or $producto_contable != null or count($producto_contable) > 0) {
                                if (isset($producto_contable->nombre)) {
                                    $html .= '<td>' . utf8_decode($producto_contable->nombre) . '</td>';
                                } else {
                                    if (isset($item->producto)) {
                                        $html .= '<td>' . utf8_decode($item->producto->nombre) . '</td>';
                                    }
                                }
                            }
                            $html .= '<td>' . $item->cantidad . '</td>
                            <td>' .  str_replace('.', ',', str_replace(',', '', number_format($item->precio, 2))) . '</td>';

                            $html .= '<td>' . str_replace('.', ',', str_replace(',', '', number_format($item->cantidad * $item->precio, 4))) . '</td>';


                            $subt = $item->cantidad * $item->precio;
                            $imp = 0;
                            $iva = 0;
                            $porcent = 0;
                            $total = ($subt + $porcent + $iva);
                            $html .= '<td>' . str_replace('.', ',', str_replace(',', '', number_format($valor_25, 4))) . '</td>';
                            $html .=  '<td>0.00</td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>' . str_replace('.', ',', str_replace(',', '', number_format($valor_25, 4))) . '</td>';
                            $acumtotal += $total;
                            $acumt += $total;
                            $html .= '</tr>';
                        }
                    }
                }
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<h3>PLANILLA NO APROBADA</h3>';
        }
        header("Pragma: public");
        header("Expires: 0");
        $filename = $cedula_ . '-' . $paciente_ . '-' . $procedimiento_ . '-' . $fecha_ . ".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        echo $html;
    }
    public function imprimirPlanillaCostoDetalle($id, $id_hc_procedimiento) //id_procedimiento//
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $planilla   = array();
        $fact_venta = array();
        $orden      = array();
        $detalles   = array();
        $hcp        = hc_procedimientos::find($id_hc_procedimiento);
        $hc         = $hcp->historia;
        $agenda     = $hc->agenda;
        $paciente   = $agenda->paciente;
        $empresa    = Empresa::find($agenda->id_empresa);
        $planilla   = $this->planillaProcedimiento($id_hc_procedimiento);
        $this->genera_honorarios($id);
        $equipos = DB::table('equipo_historia as eh')->join('equipo as e', 'e.id', 'eh.id_equipo')->where('hcid', $hcp->id_hc)->select('e.*', 'eh.*')->get();
        $detalle_pdf = Procedimiento_Detalle_Honorario::where('id_hc_procedimientos', $hcp->id)->where('estado', '1')->get();
        if (!isset($planilla->id)) {
            $detalles = '[]';
        } else {
            $detalles = $planilla->detalles_validos;
        }
        //dd($detalles);
        $vistaurl = "contable.ventas.pdf_detalle_planilla";
        $view = \View::make($vistaurl, compact('fact_venta', 'hc', 'hcp', 'detalles', 'orden', 'empresa', 'paciente', 'equipos', 'detalle_pdf', 'planilla'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public static function planillaProcedimiento($id_hc_procedimiento) //id_procedimiento//

    {
        $planilla = Planilla::where('id_hc_procedimiento', $id_hc_procedimiento)
            ->where('estado', '!=', 0)
            ->where('aprobado', '!=', 0)
            ->orderBy('id', 'desc')
            ->first();
        if (!isset($planilla->id)) {
            $planilla = '[]';
        }
        return $planilla;
    }

    ###############################################################
    #                   P D F   V E N T A
    ###############################################################
    public function imprimirPlanillaVentaDetalle($id, $id_hc_procedimiento) //id_procedimiento//

    {
        $planilla   = array();
        $fact_venta = array();
        $orden      = array();
        $detalles   = array();
        $hcp        = hc_procedimientos::find($id);
        $hcpf       = Hc_Procedimiento_Final::where('id_hc_procedimientos', $id_hc_procedimiento)->get();
        // dd($hcpf);
        $hc       = $hcp->historia;
        $agenda   = $hc->agenda;
        $paciente = $agenda->paciente;
        $empresa  = Empresa::find($agenda->id_empresa);
        $detalles = $this->detallePlanillaVenta($id, $id_hc_procedimiento);
        $planilla = Planilla::where('id_hc_procedimiento', $id_hc_procedimiento)->where('estado', 1)->first();

        $vistaurl = "contable.ventas.pdf_detalle_planilla_ventas";

        $view     = \View::make($vistaurl, compact('fact_venta', 'hc', 'hcp', 'detalles', 'orden', 'empresa', 'paciente', 'hcpf', 'planilla'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    public function detallePlanillaVenta($id, $id_hc_procedimiento) //id_procedimiento//

    {
        $planilla   = array();
        $fact_venta = array();
        $orden      = array();
        $detalles   = array();
        $hcp        = hc_procedimientos::find($id);
        $hcpf       = Hc_Procedimiento_Final::where('id_hc_procedimientos', $id_hc_procedimiento)->get();
        // dd($hcpf);
        $hc       = $hcp->historia;
        $agenda   = $hc->agenda;
        $paciente = $agenda->paciente;
        $empresa  = Empresa::find($agenda->id_empresa);
        $detalles = '[]';

        #   1. los detalles de ct_detalle_venta_omni VT: ESTE CODIGO VALE 3 ATADOS DE CANGREJO EN VEDA PENDIENTE DEFINIR COMO IR A OMNI
        /*$omni = Ct_Detalle_Venta_Omni::where('id_hc_procedimiento', $id_hc_procedimiento)->first();
        if (isset($omni->id)) {
            $sql = "select d.id, d.id_ct_ventas, p.id as id_producto, p.codigo, p.nombre, d.cantidad, d.precio, d.check_iva, c.iva, (d.precio*c.iva) as viva, p.ident_paquete
                    from ct_detalle_venta_omni d
                    join ct_productos p on p.codigo = d.id_ct_productos
                    left join ct_configuraciones c on d.check_iva = c.estado and c.id = 3
                    where d.id_ct_ventas = " . $omni->id_ct_ventas;
            $detalles = DB::select(DB::raw($sql));
        }*/
        #   2. los detalles de ct_detalle_venta
        if (isset($agenda->id)) {
            $ordenes = Ct_Orden_Venta::where('id_agenda', $agenda->id)->where('estado', '1')->get();
            $vt_detalles = [];
            $cont = 0;
            foreach ($ordenes as $orden) {
                # code...
                // VOY A LA VENTA
                $venta = Ct_ventas::where('orden_venta', $orden->id)->first();

                if (isset($venta->id)) {

                    $sql = "select d.id, d.id_ct_ventas, p.id as id_producto, p.codigo, p.nombre, d.cantidad, d.precio, d.porcentaje , d.check_iva, c.iva, (d.precio*c.iva) as viva, p.ident_paquete
                                from ct_detalle_venta d
                                join ct_productos p on p.codigo = d.id_ct_productos
                                left join ct_configuraciones c on d.check_iva = c.estado and c.id = 3
                            where d.id_ct_ventas = " . $venta->id . " AND p.id_empresa = " . $empresa->id;
                    $detalles = DB::select(DB::raw($sql));
                    $vt_detalles[$cont] = $detalles;
                    $cont++;
                    /*else {
                        #   3. los detalles de ct_detalle_venta_c
                        $det_venta = Ct_Detalle_Venta_Conglomerada::where('id_ct_ventas', $venta->id)->first();
                        if (isset($det_venta->id)) {
                            $sql = "select d.id, d.id_ct_ventas, p.id as id_producto, p.codigo, p.nombre, d.cantidad, d.precio, d.check_iva, c.iva, (d.precio*c.iva) as viva, p.ident_paquete
                                    from ct_detalle_venta_c d
                                    join ct_productos p on p.codigo = d.id_ct_productos
                                    left join ct_configuraciones c on d.check_iva = c.estado and c.id = 3
                                    where d.id_ct_ventas = " . $det_venta->id_ct_ventas;
                            $detalles = DB::select(DB::raw($sql));
                        }*/
                }
            }
        }
        //dd($vt_detalles);	

        return $vt_detalles;
    }
    ###############################################################
    #             P D F   C O S T O   VS   V E N T A
    ###############################################################

    public function imprimirPlanillaVentaVsCostoDetalle($id, $id_hc_procedimiento) //id_procedimiento//

    {
        $planilla   = array();
        $fact_venta = array();
        $orden      = array();
        $detalles   = array();
        $hcp        = hc_procedimientos::find($id);
        $hc         = $hcp->historia;
        $agenda     = $hc->agenda;
        $paciente   = $agenda->paciente;
        $empresa    = Empresa::find($agenda->id_empresa);
        $planilla   = $this->planillaProcedimiento($id_hc_procedimiento);



        $equipos2 = DB::table('equipo_historia as eh')->join('equipo as e', 'e.id', 'eh.id_equipo')->where('hcid', $hcp->id_hc)->select('e.*', 'eh.*')->get();
        // dd($equipos2[1]);
        if (!isset($planilla->id)) {
            $detallesc = '[]';
        } else {
            $detallesc = $planilla->detalles_validos;
        }

        $detallesv = $this->detallePlanillaVenta($id, $id_hc_procedimiento);



        $vistaurl = "contable.ventas.pdf_detalle_planilla_vs";
        $view     = \View::make($vistaurl, compact('fact_venta', 'hc', 'hcp', 'detallesc', 'detallesv', 'orden', 'empresa', 'paciente', 'equipos2', 'planilla'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }

    ###############################################################
    #              P D F   V E N T A   P U B L I C O
    ###############################################################
    public function imprimirPlanillaVentapDetalle($id, $id_hc_procedimiento) //id_procedimiento//

    {
        $planilla   = array();
        $fact_venta = array();
        $orden      = array();
        $detalles   = array();
        $hcp        = hc_procedimientos::find($id);
        $hcpf       = Hc_Procedimiento_Final::where('id_hc_procedimientos', $id_hc_procedimiento)->get();
        // dd($hcpf);
        $hc       = $hcp->historia;
        $agenda   = $hc->agenda;
        $paciente = $agenda->paciente;
        $empresa  = Empresa::find($agenda->id_empresa);
        $detalles = $this->detallePlanillaVenta($id, $id_hc_procedimiento);

        $vistaurl = "contable.ventas.pdf_detalle_planilla_ventas";
        $view     = \View::make($vistaurl, compact('fact_venta', 'hc', 'hcp', 'detalles', 'orden', 'empresa', 'paciente', 'hcpf'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('resultado-' . $id . '.pdf');
    }
    public function planilla_detalle_contab_pdf_vs($id, $id_procedimiento)
    {

        $hc_procedimiento = hc_procedimientos::find($id_procedimiento);
        $mensajep         = "";

        if (!is_null($hc_procedimiento)) {
            $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos', $hc_procedimiento->id)->where('estado', '1')->first();

            if (!is_null($archivo_plano)) {
                /////////////////////////INICIO DE VENTAS //////////////////////////////////////
                $txt_cie10 = null;

                $cie10 = Cie_10_3::find($archivo_plano->cie10);
                if (is_null($cie10)) {
                    $cie10 = Cie_10_4::find($archivo_plano->cie10);
                    if (!is_null($cie10)) {
                        $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                    }
                } else {
                    $txt_cie10 = '(' . $archivo_plano->cie10 . ') ' . $cie10->descripcion;
                }

                $honor_medicos = Db::table('archivo_plano_detalle as apd')
                    ->where('id_ap_cabecera', $archivo_plano->id)
                    ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                    ->orderby('apd.porcentaje_honorario', 'desc')
                    ->orderby('apt.secuencia', 'asc')
                    ->where('apt.tipo_ex', 'HME')
                    //->orWhere('apt.tipo_ex','P')
                    ->where('apd.estado', '1')
                    ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
                    ->get();

                $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

                $insumos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

                $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

                $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

                $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

                $equipos = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $archivo_plano->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();
                /******************************************FIN DE VENTAS *********************************** */
            } else {
                $mensajep = "PLANILLA PUBLICA NO APROBADA";
            }
        }

        /***********************************COSTOS************************************* */
        $planillac     = array();
        $fact_ventac   = array();
        $ordenc        = array();
        $detallesc     = array();
        $hcpc          = hc_procedimientos::find($id);
        $archivo_plano = Archivo_Plano_Cabecera::where('id_hc_procedimimentos', $hcpc->id)->where('estado', '1')->first();
        //dd($archivo_plano);
        $hcc      = $hcpc->historia;
        $agenda   = $hcc->agenda;
        $paciente = $agenda->paciente;
        //$empresa = Empresa::find($agenda->id_empresa);
        $planillac = $this->planillaProcedimiento($id_procedimiento);
        if (!isset($planillac->id)) {
            $detallesc = '[]';
        } else {
            $detallesc = $planillac->detalles_validos;
        }

        //compact('fact_ventac', 'hcc', 'hcpc', 'detallesc', 'ordenc',  'pacientec'))->render();
        /*************************************FIN DE COSTO********************************************/

        $view = \View::make('archivo_plano.planilla.planilla_pdf_contab_vs', compact('mensajep', 'fact_ventac', 'hcc', 'hcpc', 'detallesc', 'ordenc', 'paciente', 'archivo_plano', 'txt_cie10', 'medicinas', 'insumos', 'laboratorio', 'servicios_ins', 'imagen', 'equipos', 'honor_medicos'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->loadHTML($view);

        return $pdf->stream('planilla_pdf_' . $hc_procedimiento->historia->paciente->apellido1 . '_' . $hc_procedimiento->historia->paciente->nombre1 . '_.pdf');

        /*$pdf->setOptions(['dpi' => 96]);
        $paper_size = array(0, 0, 1100, 1650);
        $pdf->setpaper($paper_size);
        $pdf->loadHTML($view);*/
        /*$age        = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;*/
        /*->setPaper($paper_size, 'portrait')*/
    }
}
