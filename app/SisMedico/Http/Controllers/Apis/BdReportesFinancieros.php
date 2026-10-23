<?php

namespace Sis_medico\Http\Controllers\Apis;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use DateTime;
use DatePeriod;
use DateInterval;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Proveedor;
use Sis_medico\Seguro;
use Sis_medico\TipoProveedor;
use Sis_medico\Empresa;
use Sis_medico\v3\contable\Clientes as V3Clientes;
use Sis_medico\v3\contable\MasterTipos;
use Sis_medico\v3\contable\Proveedor as V3Proveedor;
use Sis_medico\v3\contable\FlujoCaja;
use Sis_medico\v3\contable\Inversiones;
use Sis_medico\v3\contable\Amortizaciones;
use Sis_medico\v3\contable\Prestamos;
use Illuminate\Support\Facades\Validator;

class BdReportesFinancieros extends Controller
{
    public static function detalle_cxp(Request $request)
    {
        if (filter_var($request->input('excluir_grupo_robles', false), FILTER_VALIDATE_BOOLEAN)) {
            $rucsGrupo = $request->input('rucs_grupo_robles');
            if (!is_array($rucsGrupo) || empty(array_filter($rucsGrupo, function ($ruc) {
                return is_scalar($ruc) && trim((string) $ruc) !== '';
            }))) {
                return ['status' => 'error', 'msj' => 'No se recibió la lista de RUC del Grupo Robles.'];
            }
        }

        $fecha_hasta = $request->h;
        $fecha_desde = $request->d;
        $data = V3Proveedor::getreporteDeudasPendientes($request, 'bd');
        //return $data;
        if (is_null($data)) {
            return ['status' => 'error', 'msj' =>  'No se encontraron datos'];
        }
        $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $arr = [];
        foreach ($data as $value) {
            $dia = Date('N', strtotime($value->fecha_compra));
            $mes = Date('n', strtotime($value->fecha_compra));
            $ds = intval($dia) - 1;
            $ms = intval($mes) - 1;

            // return $meses[$ms];

            $days = "0";
            $days2 = "0";
            $daysf = 0;

            if (!is_null($value->fecha_termino)) {
                $fec = new DateTime($value->fecha_compra);
                $fec2 = new DateTime($value->fecha_termino);
                $diff = $fec->diff($fec2);
                $days = $diff->days;
                $fech = new DateTime($value->fecha_termino);
                $fech2 = new DateTime($fecha_hasta);
                $diff2 = $fech2->diff($fech);
                $days2 = $diff2->days;
                $daysf = $diff2->format("%r%a");
            }
            $tipo_comprobante = "";
            $masterTipo = Ct_master_tipos::where("codigo", $value->tipo_comprobante)->first();
            if (isset($masterTipo)) {
                $tipo_comprobante = $masterTipo->nombre;
            }

            $caducidad = "";
            $tipo_caducidad = "";
            if ($daysf > '-30') {
                $caducidad = "1. Corriente / 30 Días";
                $tipo_caducidad = "1";
            } elseif ($daysf < '-30' && $daysf > '-60') {
                $caducidad = "2. 31/60 Días";
                $tipo_caducidad = "2";
            } elseif ($daysf < '-60' && $daysf > '-90') {
                $caducidad = "3. 61/90 Días";
                $tipo_caducidad = "3";
            } else {
                $caducidad = "4. Mayor a 90 Días";
                $tipo_caducidad = "4";
            }

            $aux['fecha_letras'] = $meses[$ms] . ', ' . $dias[$ds] . ' ' . $value->fecha_compra;
            $aux['fecha_compra'] = $value->fecha_compra;
            $aux['id_tipo_provedor'] = $value->id_tipo_provedor;
            $aux['tipo_provedor'] = $value->tipo_provedor;
            $aux['id_provedor'] = $value->identificacion;
            $aux['nombre_provedor'] = $value->identificacion . ' | ' . $value->razonsocial;
            $aux['dias'] = $days;
            $aux['fecha_vencimiento'] = $value->f_caducidad;
            $aux['dias_vencidos'] = $daysf;
            $aux['tipo_caducidad'] = $tipo_caducidad;
            $aux['caducidad'] = $caducidad;
            $aux['tipo_factura'] =  $value->tipo_factura;
            $aux['numero'] = $value->numero;
            $aux['detalle'] = 'Fact: # ' . $value->secuencia_f . ' Ref: ' . $value->numero . ' ' . $value->observacion . ' Id. Asiento: ' . $value->id_asiento . ' ID: ' . $value->id;
            $aux['tipo_comprobante'] = $tipo_comprobante;
            $aux['valor'] = $value->total_final;
            $aux['egreso'] = $value->egreso;
            $aux['retencion'] = $value->retencion;
            $aux['debito_acreedores'] = $value->debito_acreedores;
            $aux['cruce_valores'] = $value->cruce_valores;
            $aux['credito_acreedores'] = $value->credito_acreedores;
            $aux['saldo'] = $value->valor_contable;

            array_push($arr, $aux);
        }
        return ['status' => 'success', 'data' => $arr];
    }

    public function search_provedor(Request $request)
    {
        $data = Proveedor::buscarProveedor($request->term);
        if (is_null($data)) {
            return ['status' => 'error', 'msj' =>  'No se encontraron datos'];
        }
        return ['status' => 'success', 'data' => $data];
    }

    public static function search_edad_cartera(Request $request)
    {
        $datos = BdReportesFinancieros::detalle_cxp($request);
        $tipos = TipoProveedor::where('estado', 1)->get();
        $tipos_caducidad = ["1", "2", "3", "4"];
        $resultado_final = [
            "detalle" => [],
            "totales" => []
        ];
        // Inicializamos el total global por tipo de caducidad
        $total_por_caducidad = [
            "1" => 0,
            "2" => 0,
            "3" => 0,
            "4" => 0
        ];

        $total_saldo_global = 0; // Total global de todos los saldos
        // foreach ($tipos_caducidad as $tipo) {
        //     $total_por_caducidad[$tipo] = 0; // Inicializamos cada tipo con saldo 0
        // }
        foreach ($tipos as $tp) {
            $data['tipo1'] = 0;
            $data['tipo2'] = 0;
            $data['tipo3'] = 0;
            $data['tipo4'] = 0;
            $data['saldo'] = 0;

            $detalle_tipo = [
                "nombre_tipo" =>  $tp->nombre, // El nombre del tipo de proveedor
                'tipo1' => 0,
                'tipo2' => 0,
                'tipo3' => 0,
                'tipo4' => 0,
                'saldo' => 0,
                "detalle" => [], // La lista de proveedores y su detalle
            ];
            $proveedores_por_tipo = [];
            foreach ($datos['data'] as $value) {
                if ($value['id_tipo_provedor'] == $tp->id) {
                    $data['saldo'] += $value['saldo'];
                    if ($value['tipo_caducidad'] == 1) {
                        $data['tipo1'] += $value['saldo'];
                    }
                    if ($value['tipo_caducidad'] == 2) {
                        $data['tipo2'] += $value['saldo'];
                    }
                    if ($value['tipo_caducidad'] == 3) {
                        $data['tipo3'] += $value['saldo'];
                    }
                    if ($value['tipo_caducidad'] == 4) {
                        $data['tipo4'] += $value['saldo'];
                    }

                    $detalle_tipo['tipo1'] = round($data['tipo1'], 2);
                    $detalle_tipo['tipo2'] = round($data['tipo2'], 2);
                    $detalle_tipo['tipo3'] = round($data['tipo3'], 2);
                    $detalle_tipo['tipo4'] = round($data['tipo4'], 2);
                    $detalle_tipo['saldo'] = round($data['saldo'], 2);

                    $proveedor_id = $value['id_provedor'];
                    $tipo_caducidad = $value['tipo_caducidad'];
                    $saldo = (float) $value['saldo']; // Convertimos el saldo a float
                    if (!isset($proveedores_por_tipo[$proveedor_id])) {
                        $proveedores_por_tipo[$proveedor_id] = [
                            'nombre' => $value['nombre_provedor'], // Guardamos el nombre del proveedor
                            'total_saldo' => 0, // Inicializamos el saldo total
                            'caducidades' => [] // Inicializamos un sub-array para las caducidades
                        ];
                        // Inicializamos las caducidades con 0 para todos los tipos
                        foreach ($tipos_caducidad as $tipo) {
                            $proveedores_por_tipo[$proveedor_id]['caducidades'][$tipo] = 0;
                        }
                    }
                    // Sumamos el saldo al tipo de caducidad correspondiente
                    $proveedores_por_tipo[$proveedor_id]['caducidades'][$tipo_caducidad] += $saldo;
                    // Sumamos el saldo al total del proveedor
                    $proveedores_por_tipo[$proveedor_id]['total_saldo'] += $saldo;
                    // Sumamos el saldo al total global por tipo de caducidad
                    $total_por_caducidad[$tipo_caducidad] += $saldo;
                    // Sumamos al total global general
                    $total_saldo_global += $saldo;
                }
            }

            // Generamos el detalle del proveedor para este tipo
            foreach ($proveedores_por_tipo as $proveedor_id => $info) {
                $proveedor = [
                    'nombre_proveedor' => $info['nombre'],
                    'total_saldo' => round($info['total_saldo'], 2),
                ];
                // Agregamos los saldos por tipo de caducidad (si no hay saldos, será 0)
                foreach ($tipos_caducidad as $tipo_caducidad) {
                    $proveedor["tipo_caducidad{$tipo_caducidad}"] = round($info['caducidades'][$tipo_caducidad], 2);
                }
                // Agregamos el proveedor al detalle
                $detalle_tipo['detalle'][] = $proveedor;
            }
            // Agregamos el detalle del tipo de proveedor al resultado final
            //$resultado_final[] = $detalle_tipo;
            // Agregamos el detalle del tipo de proveedor al array final
            $resultado_final['detalle'][] = $detalle_tipo;
        }
        // Agregamos el total global de todos los tipos de caducidad
        $resultado_final['totales'] = [
            'total_caducidad1' => round($total_por_caducidad['1'], 2),
            'total_caducidad2' => round($total_por_caducidad['2'], 2),
            'total_caducidad3' => round($total_por_caducidad['3'], 2),
            'total_caducidad4' => round($total_por_caducidad['4'], 2),
            'total_saldo_global' => round($total_saldo_global, 2)
        ];
        return $resultado_final;
    }

    public function search_max_dias_prov(Request $request)
    {
        $datos = BdReportesFinancieros::detalle_cxp($request);
        if ($datos['data'] === []) {
            return ['status' => 'error', 'msj' =>  'No se encontraron datos'];
        }
        $resultado_final = [
            "dias_global" => 0,
            "detalle" => [],
        ];
        $tipos = TipoProveedor::where('estado', 1)->get();
        //return $datos;
        $arr_dt = array();
        foreach ($datos['data'] as $dt) {
            array_push($arr_dt, $dt['dias']);
        }
        //return $arr_dt;
        $days_global = max($arr_dt);

        $arr_days = array();
        $max_days_tipo = 0;
        foreach ($tipos as $tp) {
            $detalle_tipo = [
                "nombre_tipo" =>  $tp->nombre, // El nombre del tipo de proveedor
                "dias_tipo" => 0,
                "detalle" => [], // La lista de proveedores y su detalle
            ];
            $proveedores_por_tipo = [];
            foreach ($datos['data'] as $value) {
                array_push($arr_days, $value['dias']);
                if ($value['id_tipo_provedor'] == $tp->id) {
                    $proveedor_id = $value['id_provedor'];
                    if (!isset($proveedores_por_tipo[$proveedor_id])) {
                        $proveedores_por_tipo[$proveedor_id] = [
                            'nombre' => $value['nombre_provedor'], // Guardamos el nombre del proveedor
                            'dias' => []
                        ];
                    }
                    array_push($proveedores_por_tipo[$proveedor_id]['dias'], $value['dias']);
                }
            }
            $max_days_tipo = max($arr_days);
            // Generamos el detalle del proveedor para este tipo
            foreach ($proveedores_por_tipo as $proveedor_id => $info) {
                $max_days = max($info['dias']);
                $proveedor = [
                    'nombre_proveedor' => $info['nombre'],
                    'dias' => $max_days
                ];
                // Agregamos el proveedor al detalle
                $detalle_tipo['detalle'][] = $proveedor;
                $detalle_tipo['dias_tipo'] = $max_days_tipo;
            }
            $resultado_final['detalle'][] = $detalle_tipo;
        }

        $resultado_final['dias_global'] = $days_global;
        return $resultado_final;
    }

    public function searchClient(Request $request)
    {
        $data = V3Clientes::searchClient($request->input('term', ''));

        return ['status' => 'success', 'data' => $data];
    }

    public function cuentasxcobrar(Request $request)
    {
        try {
            if (filter_var($request->input('excluir_grupo_robles', false), FILTER_VALIDATE_BOOLEAN)) {
                $rucsGrupo = $request->input('rucs_grupo_robles');
                if (!is_array($rucsGrupo) || empty(array_filter($rucsGrupo, function ($ruc) {
                    return is_scalar($ruc) && trim((string) $ruc) !== '';
                }))) {
                    return ['status' => 'error', 'msj' => 'No se recibió la lista de RUC del Grupo Robles.'];
                }
            }

            $data = V3Clientes::getCuentasxCobrar($request);

            return $data;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function listaSegurosCuentasxcobrar(Request $request)
    {
        try {
            $data = Seguro::where('inactivo', 1)
                ->whereIn('tipo', [0, 1])
                ->orderBy('tipo')
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'tipo']);

            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function cuentascobradas(Request $request)
    {
        try {
            if (filter_var($request->input('excluir_grupo_robles', false), FILTER_VALIDATE_BOOLEAN)) {
                $rucsGrupo = $request->input('rucs_grupo_robles');
                if (!is_array($rucsGrupo) || empty(array_filter($rucsGrupo, function ($ruc) {
                    return is_scalar($ruc) && trim((string) $ruc) !== '';
                }))) {
                    return ['status' => 'error', 'msj' => 'No se recibió la lista de RUC del Grupo Robles.'];
                }
            }

            $data = V3Clientes::getCuentascobradas($request);
            return $data;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function cuentaspagadas(Request $request)
    {
        try {
            if (filter_var($request->input('excluir_grupo_robles', false), FILTER_VALIDATE_BOOLEAN)) {
                $rucsGrupo = $request->input('rucs_grupo_robles');
                if (!is_array($rucsGrupo) || empty(array_filter($rucsGrupo, function ($ruc) {
                    return is_scalar($ruc) && trim((string) $ruc) !== '';
                }))) {
                    return ['status' => 'error', 'msj' => 'No se recibió la lista de RUC del Grupo Robles.'];
                }
            }

            $data = Proveedor::getCuentasPagadas($request);
            return $data;
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
        }
    }

    public function flujoCaja(Request $request)
    {
        $appId = 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=';
        if (!hash_equals($appId, (string) $request->header('appid', ''))) {
            return response()->json(['status' => 'error', 'msj' => 'CREDENCIALES INVALIDAS'], 401);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required',
            'fecha_desde' => 'required|date_format:Y-m-d',
            'fecha_hasta' => 'required|date_format:Y-m-d|after_or_equal:fecha_desde',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'msj' => $validator->errors()->first()], 422);
        }

        try {
            return response()->json([
                'status' => 'success',
                'data' => FlujoCaja::resumen(
                    $request->input('id_empresa'),
                    $request->input('fecha_desde'),
                    $request->input('fecha_hasta')
                ),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al consultar flujo de caja', ['exception' => $e]);
            return response()->json(['status' => 'error', 'msj' => 'No se pudo consultar el flujo de caja'], 500);
        }
    }

    public function inversiones(Request $request)
    {
        $appId = 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=';
        if (!hash_equals($appId, (string) $request->header('appid', ''))) {
            return response()->json(['status' => 'error', 'msj' => 'CREDENCIALES INVALIDAS'], 401);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required',
            'fecha_desde' => 'required|date_format:Y-m-d',
            'fecha_hasta' => 'required|date_format:Y-m-d|after_or_equal:fecha_desde',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'msj' => $validator->errors()->first()], 422);
        }

        if (preg_replace('/\D+/', '', (string) $request->input('id_empresa')) !== '0992704152001') {
            return response()->json(['status' => 'error', 'msj' => 'El reporte está habilitado únicamente para GASTROCLINICA.'], 422);
        }

        try {
            return response()->json([
                'status' => 'success',
                'data' => Inversiones::resumen(
                    '0992704152001',
                    $request->input('fecha_desde'),
                    $request->input('fecha_hasta')
                ),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al consultar movimientos de inversiones', ['exception' => $e]);
            return response()->json(['status' => 'error', 'msj' => 'No se pudieron consultar los movimientos de inversiones'], 500);
        }
    }

    public function prestamos(Request $request)
    {
        $appId = 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=';
        if (!hash_equals($appId, (string) $request->header('appid', ''))) {
            return response()->json(['status' => 'error', 'msj' => 'CREDENCIALES INVALIDAS'], 401);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'msj' => $validator->errors()->first()], 422);
        }

        $idEmpresa = preg_replace('/\D+/', '', (string) $request->input('id_empresa'));
        if (!$idEmpresa || !Empresa::where('id', $idEmpresa)->exists()) {
            return response()->json(['status' => 'error', 'msj' => 'La empresa solicitada no existe en esta sucursal.'], 404);
        }

        try {
            return response()->json([
                'status' => 'success',
                'data' => Prestamos::resumen($idEmpresa),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al consultar prestamos contables', [
                'id_empresa' => $idEmpresa,
                'exception' => $e,
            ]);
            return response()->json(['status' => 'error', 'msj' => 'No se pudieron consultar los préstamos contables'], 500);
        }
    }

    public function amortizaciones(Request $request)
    {
        $appId = 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=';
        if (!hash_equals($appId, (string) $request->header('appid', ''))) {
            return response()->json(['status' => 'error', 'msj' => 'CREDENCIALES INVALIDAS'], 401);
        }

        $validator = Validator::make($request->all(), [
            'id_empresa' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'msj' => $validator->errors()->first()], 422);
        }

        $idEmpresa = preg_replace('/\D+/', '', (string) $request->input('id_empresa'));
        if (!$idEmpresa || !Empresa::where('id', $idEmpresa)->exists()) {
            return response()->json(['status' => 'error', 'msj' => 'La empresa solicitada no existe en esta sucursal.'], 404);
        }

        try {
            return response()->json([
                'status' => 'success',
                'data' => Amortizaciones::resumen($idEmpresa),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al consultar amortizaciones', ['exception' => $e]);
            return response()->json(['status' => 'error', 'msj' => 'No se pudieron consultar las amortizaciones'], 500);
        }
    }
}
