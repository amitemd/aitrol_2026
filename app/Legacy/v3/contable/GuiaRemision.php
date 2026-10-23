<?php

namespace Sis_medico\v3\contable;

use Sis_medico\Http\Controllers\ApiFacturacionController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\v3\contable\ClaveAcceso;
use Sis_medico\v3\contable\Transportistas;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\Proveedor;
use Sis_medico\v3\contable\Sucursales;
use Sis_medico\v3\contable\ActivosFijos;
use Sis_medico\v3\contable\GuiaRemisionDetalle;
use Sis_medico\v3\contable\Caja;


class GuiaRemision extends Model
{
    private static $modelo = 'GuiaRemision';
    protected $table = 'ct_cabecera_remision';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getGuiaRemision($id = '')
    {

        if ($id == '') {
            return GuiaRemision::join('transportistas as t', 'ct_cabecera_remision.ci_ruc_trasnportista', 't.ci_ruc')->get([
                'ct_cabecera_remision.id',
                'ct_cabecera_remision.num_secuencial',
                'ct_cabecera_remision.establecimiento',
                'ct_cabecera_remision.punto_emision',
                'ct_cabecera_remision.razon_social_destinatario',
                'ct_cabecera_remision.fecha_emision_documento',
                't.razon_social',
                't.placa',
                'ct_cabecera_remision.direccion_partida',
                'ct_cabecera_remision.estado'

            ]);
        } else {
            return GuiaRemision::join('transportistas as t', 'ct_cabecera_remision.ci_ruc_trasnportista', 't.ci_ruc')
                ->leftjoin('new_db_catalogo', 'new_db_catalogo.id_catalogo', 'ct_cabecera_remision.tipo_documento_destinatario')
                ->leftjoin('ct_ventas', 'ct_ventas.id', 'ct_cabecera_remision.num_doc_destino')
                ->where('ct_cabecera_remision.id', $id)
                ->first([
                    'ct_cabecera_remision.fecha_ini',
                    'ct_cabecera_remision.ci_ruc_trasnportista',
                    'ct_cabecera_remision.tipo_guia',
                    'ct_cabecera_remision.id',
                    'ct_cabecera_remision.num_secuencial',
                    'ct_cabecera_remision.establecimiento',
                    'ct_cabecera_remision.punto_emision',
                    'ct_cabecera_remision.razon_social_destinatario',
                    'ct_cabecera_remision.fecha_emision_documento',
                    't.razon_social',
                    't.placa',
                    't.id as id_transportista',
                    'ct_cabecera_remision.direccion_partida',
                    'ct_cabecera_remision.estado',
                    'ct_cabecera_remision.email_transportista',
                    'ct_cabecera_remision.fecha_fin',
                    'ct_cabecera_remision.ci_destinatario',
                    'ct_cabecera_remision.direccion_destinatario',
                    'new_db_catalogo.catalogo',
                    'ct_cabecera_remision.fecha_autorizacion_destinatario',
                    'ct_cabecera_remision.email_traslado_destinatario',
                    'ct_ventas.id as id_venta',
                    'ct_cabecera_remision.ruta',
                    'ct_cabecera_remision.razon_social_destinatario',
                    'ct_cabecera_remision.motivo_traslado_destinatario',
                    DB::raw('concat(ct_ventas.sucursal, ct_ventas.punto_emision, ct_ventas.numero) as numero_concat')
                ]);
        }
    }
    public static function saveTransportista($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            if ($data['id'] == '') {
                unset($data['id']);
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = GuiaRemision::insertGetId($data);
                $r->c = 'GuiadeRemision';
                $r->s = 'saveTransportista';
                $r->d = $origin['d'];
                $r->m = GuiaRemision::$modelo;
                $r->o = 'Se agrego el nuevo transportista con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                GuiaRemision::where('id', $id)->update($data);
                $r->c = 'GuiadeRemision';
                $r->s = 'saveTransportista';
                $r->d = $origin['d'];
                $r->m = GuiaRemision::$modelo;
                $r->o = 'Se actualizo el transportista con id.: ' . $id;
                $cont++;
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'GuiadeRemision';
            $r->s = 'saveTransportista';
            $r->d =  $origin['d'];
            $r->m = GuiaRemision::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (GuiaRemision::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado del ',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }

    public static function obtenerDatosGuia($r)
    {
        $transportistas = [];
        $nombreBuscar = '%' . $r['term'] . '%';
        $ct_transportista = Transportistas::where('ci_ruc', 'like', $nombreBuscar)->orWhere(function ($query) use ($r) {
            $query->where('razon_social', 'like', '%' . $r['term'] . '%');
        })->get();


        if (count($ct_transportista) == 0) {

            $buscar = Clientes::where('identificacion', 'like', $nombreBuscar)->orWhere(function ($query) use ($r) {
                $query->where('nombre', 'like', '%' . $r['term'] . '%');
            })->get();

            if (count($buscar) == 0) {
                $buscar = Proveedor::where('id', 'like', $nombreBuscar)->orWhere(function ($query) use ($r) {
                    $query->where('razonsocial', 'like', '%' . $r['term'] . '%');
                })->get();

                foreach ($buscar as $value) {
                    $transportistas[] = ['id' => $value->id, 'nombreappe' => $value->razonsocial, 'data' => $value, 'opcion' => "p"];
                }
            } else {

                foreach ($buscar as $value) {
                    $transportistas[] = ['id' => $value->identificacion, 'nombreappe' => $value->nombre, 'data' => $value, 'opcion' => "c"];
                }
            }
        } else {
            foreach ($ct_transportista as $value) {
                $transportistas[] = ['id' => $value->id, 'nombreappe' => $value->razon_social, 'data' => $value, 'opcion' => "t"];
            }
        }
        return response()->json($transportistas);
    }

    public static function obtenerDatosTransportista($r)
    {

        $datos = [];

        switch ($r['tipo']) {


            case 'p':
                $data = Proveedor::where("id", $r['id'])->first();
                $datos = [
                    "identificacion" => $data->id,
                    "email_representante" => $data->email,
                    "direccion_representante" => $data->direccion,
                    "razonsocial" => $data->razonsocial,
                    "telefono1" => $data->telefono1,
                    "tipo" =>  strpos($data->tipo, '0') === true  ? $data->tipo  : "0" . $data->tipo,
                    "placa" => "",
                ];
                break;

            case 'c':
                $data = Clientes::where("identificacion", $r['id'])->first();
                $datos = [
                    "identificacion" => $data->identificacion,
                    "email_representante" => $data->email_representante,
                    "direccion_representante" => $data->direccion_representante,
                    "razonsocial" => $data->nombre,
                    "telefono1" => $data->telefono1_representante,
                    "tipo" => strpos($data->tipo, '0') === true  ? $data->tipo  : "0" . $data->tipo,
                    "placa" => "",

                ];
                break;

            case 't':
                $data = Transportistas::where('id', $r['id'])->first();

                $datos = [
                    "identificacion" => $data->ci_ruc,
                    "email_representante" => $data->email,
                    "direccion_representante" => $data->direccion,
                    "razonsocial" => $data->razon_social,
                    "telefono1" => $data->telefono1,
                    "tipo" => strpos($data->tipo_documento, '0') === true  ? $data->tipo_documento  : "0" . $data->tipo_documento,
                    "placa" => $data->placa,
                ];

                break;
        }

        return ['data' => $datos];
    }

    public static function validarCampos($r)
    {

        $ct_transportista = Transportistas::where("ci_ruc", $r["numero"])->first();
        if (!is_null($ct_transportista)) {
            return json_encode([
                'status' => false,
                'code' => 500,
                'message' => 'El numero de cédula ya se encuentra registrado',
                'data' => $r->input(),
            ]);
        }
        return json_encode([
            'status' => true,
            'code' => 200,
            'message' => 'Ok',
            'data' => $r->input(),
        ]);
    }




    public static function validarPlaca($r)
    {

        $ct_transportista = Transportistas::where("placa", $r["placa"])->first();
        if (!is_null($ct_transportista)) {
            return json_encode([
                'status' => false,
                'code' => 500,
                'message' => 'El numero de placa ya se encuentra registrado',
                'data' => $r->input(),
            ]);
        }
        return json_encode([
            'status' => true,
            'code' => 200,
            'message' => 'Ok',
            'data' => $r->input(),
        ]);
    }

    public static function obtenerPuntoEmision($r)
    {
        return Sucursales::join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_sucursales.id')
            ->where('ct_c.estado', 1)
            ->where('ct_sucursales.id', $r->id_sucursal)
            ->get();
    }

    public static function getProductos($r)
    {
        $id_empresa  = $r->session()->get('id_empresa');
        return Productos::where('id_empresa', $id_empresa)->get();
    }

    public static function consultarProductos($r)
    {



        $campo      = strtoupper($r['term']);
        $id_empresa  = $r->session()->get('id_empresa');
        //DB::enableQueryLog();
        $productos = Productos::select('id', 'codigo', 'nombre', 'tipo_objeto')
            ->where('id_empresa', $id_empresa)
            ->where(function ($q) use ($campo) {
                $q->orWhere('nombre', 'like', '%' . $campo . '%');
                $q->orWhere('codigo', 'like', '%' . $campo . '%');
            });


        $activos = ActivosFijos::select('id', 'codigo', 'nombre', 'tipo_objeto')
            ->where('empresa', $id_empresa)
            ->where('estado', 1)
            ->where(function ($q) use ($campo) {
                $q->orWhere('nombre', 'like', '%' . $campo . '%');
                $q->orWhere('codigo', 'like', '%' . $campo . '%');
            })
            ->union($productos)
            ->get();
        $valid_tags = [];
        foreach ($activos as $product) {
            $valid_tags[] = ['id' => $product->id, 'nombre' => $product->nombre, 'codigo' => $product->codigo, 'tipo' => $product->tipo_objeto];
        }
        return response()->json($valid_tags);
    }

    public static function revisarCodigo($r)
    {
        $dato = explode('|', $r->id_producto);
        if ($dato[1] == 'P') {
            $producto = Productos::find($dato[0]);
            if (!is_null($producto)) {
                return ['producto' => $producto];
            } else {
                return ['producto' => []];
            }
        } else {
            $producto = ActivosFijos::find($dato[0]);
            if (!is_null($producto)) {
                return ['producto' => $producto];
            } else {
                return ['producto' => []];
            }
        }
    }

    public static function guardarGuiaRemision($r)
    {

        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $dataDetalle = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            if ($data['id'] == '') {
                unset($data['id']);
                $sucursales  = Sucursales::where('id', $data["sucursal_empresa"])->first();
                $transportistas = Transportistas::find($data["nombre_ruc"]);
                $ct_caja = Caja::where('id_sucursal', $data["sucursal_empresa"])->where('id', $data["punto_emision"])->first();
                $data['establecimiento'] = $sucursales->codigo_sucursal;
                $data['punto_emision'] = $ct_caja->codigo_caja;
                $data['ci_ruc_trasnportista'] =  $transportistas->ci_ruc;
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $data['estado'] = -1;
                // Quitar campos innecarios 
                unset($data["sucursal_empresa"]);
                unset($data["nombre_ruc"]);
                unset($data["tipo"]);
                unset($data["razonsocial"]);
                unset($data["producto"]);
                unset($data["descripcion"]);
                unset($data["observacion"]);
                unset($data["detalle3"]);
                unset($data["cantidad"]);
                unset($data["cod_principal"]);
                unset($data["cod_adicional"]);
                DB::enableQueryLog();
                $num_secuencial = GuiaRemision::where('establecimiento', $sucursales->codigo_sucursal)
                    ->where('punto_emision', $ct_caja->codigo_caja)
                    ->where('id_empresa', session('id_empresa'))
                    ->orderBy('num_secuencial', 'desc')
                    ->first(['num_secuencial']);
                if ($num_secuencial == '') {
                    $data['num_secuencial'] = 1;
                } else {
                    $data['num_secuencial'] = $num_secuencial->num_secuencial + 1;
                }
                $secuencial = $data['num_secuencial'];
                $data["id_empresa"] = session('id_empresa');
                $deEmpresa = DeEmpresas::where('id_empresa', session('id_empresa'))->first(["ambiente"]);
                $claveObj = new ClaveAcceso();
                $numComprobante = explode('-', $sucursales->codigo_sucursal . '-' . $ct_caja->codigo_caja . '-' . str_pad($secuencial, 9, 0, STR_PAD_LEFT));
                $campos = [
                    'fecha_emision' => str_replace('/', '-', date('d/m/Y', strtotime($r->fecha_emision))),
                    'tipo_comprobante' => '01',
                    'ruc' => session('id_empresa'),
                    'tipo_ambiente' => $deEmpresa->ambiente,
                    'punto_establecimiento' => $numComprobante[0],
                    'punto_emision' => $numComprobante[1],
                    'numero_comprobante' => $numComprobante[2],
                    'codigo_numerico' => $numComprobante[2],
                    'tipo_emision' => 1
                ];
                $claveObj->llenarDatos($campos);
                $data["clave_acceso"] = $claveObj->clave_acceso;
                $id = GuiaRemision::insertGetId($data);
                for ($i = 0; $i < count($dataDetalle["producto"]); $i++) {

                    $detalle = new GuiaRemisionDetalle;
                    $detalle->id_cabecera_remision = $id;
                    $detalle->id_producto = $dataDetalle['producto'][$i];
                    $detalle->cantidad = $dataDetalle['cantidad'][$i];
                    $detalle->cod_principal = $dataDetalle['cod_principal'][$i];
                    $detalle->cod_adicional = $dataDetalle['cod_adicional'][$i];
                    $detalle->observacion = $dataDetalle['observacion'][$i];
                    $detalle->descripcion = $dataDetalle['descripcion'][$i];
                    $detalle->detalle3 = $dataDetalle['detalle3'][$i];
                    $detalle->id_usuariocrea = Auth::user()->id;
                    $detalle->id_usuariomod =  Auth::user()->id;
                    $detalle->ip_creacion = $ip;
                    $detalle->ip_modificacion = $ip;
                    $detalle->save();
                }


                $r->c = 'GuiadeRemision';
                $r->s = 'saveTransportista';
                $r->d = $origin['d'];
                $r->m = GuiaRemision::$modelo;
                $r->o = 'Se agrego el nuevo transportista con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $sucursales  = Sucursales::where('id', $data["sucursal_empresa"])->first();
                $transportistas = Transportistas::find($data["nombre_ruc"]);
                $ct_caja = Caja::where('id_sucursal', $data["sucursal_empresa"])->where('id', $data["punto_emision"])->first();
                $data['establecimiento'] = $sucursales->codigo_sucursal;
                $data['punto_emision'] = $ct_caja->codigo_caja;
                $data['ci_ruc_trasnportista'] =  $transportistas->ci_ruc;
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $data['estado'] = -1;
                // Quitar campos innecarios 
                unset($data["sucursal_empresa"]);
                unset($data["nombre_ruc"]);
                unset($data["tipo"]);
                unset($data["razonsocial"]);
                unset($data["producto"]);
                unset($data["descripcion"]);
                unset($data["observacion"]);
                unset($data["detalle3"]);
                unset($data["cantidad"]);
                unset($data["cod_principal"]);
                unset($data["cod_adicional"]);
                GuiaRemision::where('id', $id)->update($data);
                $detalle = GuiaRemisionDetalle::where('id_cabecera_remision', $id)->delete();
                for ($i = 0; $i < count($dataDetalle["producto"]); $i++) {
                    $detalle = new GuiaRemisionDetalle;
                    $detalle->id_cabecera_remision = $id;
                    $detalle->id_producto = $dataDetalle['producto'][$i];
                    $detalle->cantidad = $dataDetalle['cantidad'][$i];
                    $detalle->cod_principal = $dataDetalle['cod_principal'][$i];
                    $detalle->cod_adicional = $dataDetalle['cod_adicional'][$i];
                    $detalle->observacion = $dataDetalle['observacion'][$i];
                    $detalle->descripcion = $dataDetalle['descripcion'][$i];
                    $detalle->detalle3 = $dataDetalle['detalle3'][$i];
                    $detalle->id_usuariocrea = Auth::user()->id;
                    $detalle->id_usuariomod =  Auth::user()->id;
                    $detalle->ip_creacion = $ip;
                    $detalle->ip_modificacion = $ip;
                    $detalle->save();
                }
                $r->c = 'GuiadeRemision';
                $r->s = 'saveTransportista';
                $r->d = $origin['d'];
                $r->m = GuiaRemision::$modelo;
                $r->o = 'Se actualizo el guia remisión con id.: ' . $id;
                $cont++;
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'GuiadeRemision';
            $r->s = 'saveGuiaRemision';
            $r->d =  $origin['d'];
            $r->m = GuiaRemision::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }

        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }

    public static function traerFacturas($r)
    {
        $nombres = trim($r['term']);
        $id_empresa  = $r->session()->get('id_empresa');

        $ventas = Ventas::join('ct_clientes as cl', 'ct_ventas.id_cliente', 'cl.identificacion')
            ->where('ct_ventas.nro_comprobante', 'like', '%' . $nombres . '%')
            ->where('ct_ventas.estado', '<>', 0)
            ->where('ct_ventas.nro_autorizacion', '<>', null)
            ->where('ct_ventas.id_empresa', $id_empresa)
            ->limit(20)
            ->get([
                'ct_ventas.nro_comprobante', 'ct_ventas.id', 'ct_ventas.nro_autorizacion', 'cl.*'
            ]);
        $data      = array();
        foreach ($ventas as $val) {

            $data[] = array(
                'nombreappe' => $val->nro_comprobante, 'id' => $val->id, 'autorizacion' => $val->nro_autorizacion, 'id_cliente' => $val->cedula_representante,
                'email' => $val->email_representante, 'direccion_representante' => $val->direccion_representante, 'nombre' => $val->nombre
            );
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public static function crearTabla($r)
    {
        $num = explode('-', $r['producto']);
        $query = DB::table('ct_ventas as v')
            ->join('ct_detalle_venta as d', 'v.id', 'd.id_ct_ventas')
            ->leftjoin('ct_productos as p', 'd.id_ct_productos', 'p.codigo')
            ->where('v.sucursal', $num[0])
            ->where('v.punto_emision', $num[1])
            //->where('v.nro_comprobante', 'like', '%' . (int)$num[2] . '%')
            ->where('v.nro_comprobante', $num[0] . '-' . $num[1] . '-' . str_pad($num[2], 9, 0, STR_PAD_LEFT))
            ->where('v.id_empresa', session('id_empresa'))
            ->get([
                'd.id_ct_productos',
                'p.id',
                'p.codigo',
                'd.nombre',
                'v.created_at',
                'p.tipo_objeto as tipo'
            ]);
        return ['query' => $query];
    }

    public function nombreTrasportista()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Transportistas', "ci_ruc_trasnportista", "ci_ruc");
    }

    public function nombreDestinatario()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Proveedor', "ci_destinatario", "id");
    }

    public function tipoDocumento()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Catalogos', "tipo_guia", "id_catalogo");
    }

    public function tipGuia()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Catalogos', "tipo_documento_destinatario", "id_catalogo");
    }

    public function detalle()
    {
        return $this->hasMany('Sis_medico\v3\contable\GuiaRemisionDetalle', "id_cabecera_remision", "id");
    }

    public static function getVentaPrint($r)
    {
        $row = GuiaRemision::find($r->id_guia);
        $detalle = GuiaRemisionDetalle::where('id_cabecera_remision', $row->id)->get();
        $contenidoHtml = '';
        if ($row != '') {
            $contenidoHtml .= '
        <div class="row">
            <div class="col-12">
                <h4>' . (config('data.empresa')->logo != null ? '<img src="' . url('/') . '/logo/' . config('data.empresa')->logo . '" style="width:120px;height: 60px; margin-left: 11px;" />' : '') . '</h4>
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <address>
                    <strong>' . config('data.empresa')->razonsocial . '</strong><br>
                    ' . config('data.empresa')->id . '<br>
                    ' . config('data.empresa')->direccion . '<br>
                    Teléfono: ' . config('data.empresa')->telefono1 . ' ' . config('data.empresa')->telefono2 . '<br>
                    E-mail: <a href="mailto:' . config('data.empresa')->email . '">' . config('data.empresa')->email . '</a>
                </address>
            </div>
        
            <div class="col-sm-4 invoice-col">
              <address>
                <strong>DATOS DEL TRANSPORTISTA</strong></br>
                <b>Cédula:</b>' . $row->nombreTrasportista->ci_ruc . '</br>
                <b>Razón Social:</b>' . $row->nombreTrasportista->razon_social . '<br>
                <b>Email:</b><a href="mailto:' . $row->nombreTrasportista->email . '">' . $row->nombreTrasportista->email . '</a>' . '<br>
                <b>Telefono:</b>' . $row->nombreTrasportista->telefono1 . '<br>
              </address>
            </div>


            <div class="col-sm-4 invoice-col">
              <address>
                <strong>DATOS DEL DESTINATARIO</strong></br>
                <b>Cédula:</b>' . ($row->nombreDestinatario != null ? $row->nombreDestinatario->id  : $row->ci_ruc) . '</br>
                <b>Razón Social:</b>' . ($row->nombreDestinatario != null ?  $row->nombreDestinatario->razonsocial : $row->razon_social_destinatario) . '<br>
                <b>Email:</b><a href="mailto:' . ($row->nombreDestinatario != null ?  $row->nombreDestinatario->email : $row->email_traslado_destinatario) . '">' . ($row->nombreDestinatario != null ?  $row->nombreDestinatario->email : $row->email_traslado_destinatario) . '</a>' . '<br>
                <b>Telefono:</b>' . ($row->nombreDestinatario != null ?  $row->nombreDestinatario->telefono1 : '') . '<br>
              </address>
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 150px;">Producto</th>
                            <th style="width: 300px;">Detalle 1</th>
                            <th style="width: 160px;">Detalle 2</th>
                            <th style="width: 160px;">Detalle 3</th>
                            <th style="width: 160px;">Cantidad</th>
                            <th style="width: 160px;">Cod PRINCIPAL</th>
                            <th style="width: 160px;">Cod ADICIONAL</th>
                        </tr>
                    </thead>
                    <tbody>';
            if (count($detalle) > 0) {
                foreach ($detalle as $value) {
                    $contenidoHtml .= '<tr>
                                <td>' . $value->detalleProducto->nombre . '</td>
                                <td>' .  $value->observacion . '</td>
                                <td>' . $value->descripcion . '</td>
                                <td>' . $value->detalle3 . '</td>
                                <td>' . $value->cantidad . '</td>
                                <td>' .  $value->detalleProducto->codigo . '</td>
                                <td>' . $value->detalleProducto->codigo . '</td>
                                </tr>';
                }
            }

            $contenidoHtml .= '
                </tbody>
            </table>';

            $contenidoHtml .= '
                <div class="row invoice-info">
                    <div class="col-sm-6 invoice-col">
                        <address>
                            <strong>DATOS GENERALES</strong></br>
                            <b>Dirección de partida:</b>' . $row->direccion_partida . '</br>
                            <b>Dirección de destino:</b>' . $row->direccion_destinatario . '<br>
                            <b>Ruta:</b>' . $row->ruta . '<br>
                            <b>Clave:</b>' . $row->clave_acceso . '<br>
                        </address>
                    </div>
                    <div class="col-sm-6 invoice-col">
                        <address>
                        <b>Tipo de guía:</b>' . ($row->tipoDocumento != null ? $row->tipoDocumento->catalogo : null) . '</br>
                        <b>Fecha inicio:</b>' . $row->fecha_ini . '<br>
                        <b>Fecha fin:</b>' . $row->fecha_fin . '<br>
                        <b>Tipo documento:</b>' . ($row->tipGuia != null ? $row->tipGuia->catalogo : '') . '<br>
                        </address>
                    </div>
                </div>
            ';
        } else {
            $contenidoHtml = 'Busqueda sin resultados';
        }
        return $contenidoHtml;
    }

  
}
