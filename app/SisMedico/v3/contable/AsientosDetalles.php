<?php

namespace Sis_medico\v3\contable;

use Hash;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\ImpAsientosCabecera;
use Sis_medico\v3\contable\ImpAsientosDetalle;
use Sis_medico\v3\contable\AsientosCabecera;


class AsientosDetalles extends Model
{
    protected $table = 'ct_asientos_detalle';
    protected $primaryKey  = 'id_asiento_cabecera';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function getFacturasProveedor($id_proveedor)
    {

        $id_empresa = session('id_empresa');
        $deudas = AsientosCabecera::join('ct_compras as co', 'co.id_asiento_cabecera', 'ct_asientos_cabecera.id')
            ->where('co.proveedor', $id_proveedor)
            ->where('ct_asientos_cabecera.id_empresa', $id_empresa)
            ->where('co.estado', '>', '0')
            ->where('co.valor_contable', '>', '0')
            ->select([
                'co.valor_contable',
                'co.secuencia_f',
                'co.tipo',
                'ct_asientos_cabecera.observacion',
                'co.f_caducidad as fecha_asiento',
                'co.numero',
                'co.proveedor',
                'ct_asientos_cabecera.valor as valor_nuevo',
                'co.id as id_compra',
            ])
            ->orderby('ct_asientos_cabecera.id', 'desc')
            ->get();
        $data = [];
        if (count($deudas) < 0) {
            return ['value' => 'no resultados'];
        }
        for ($i = 0; $i < count($deudas); $i++) {
            $tipo = '';
            if ($deudas[$i]->tipo == 1)
                $tipo = 'COM-FA';
            elseif ($deudas[$i]->tipo == 2)
                $tipo = 'COM-FACT';
            else
                $tipo = 'SALDO INICIAL';

            $deuda = [
                'fecha_asiento' => $deudas[$i]->fecha_asiento, //0
                'tipo' => $tipo, //1
                'secuencia_f' => $deudas[$i]->secuencia_f, //2
                'observacion' => $deudas[$i]->observacion, //3
                'valor_contable' => $deudas[$i]->valor_contable, //4
                'proveedor' => $deudas[$i]->proveedor,
                'numero' => $deudas[$i]->numero,
                'valor_nuevo' => $deudas[$i]->valor_nuevo, //5
                'id_compra' => $deudas[$i]->id_compra,
            ];
            $data[$i] = $deuda;
        }
        return $data;
    }

    public function cuenta_empresa()
    {
        return $this->belongsTo('Sis_medico\v3\contable\PlanCuentas', 'id_plan_cuenta', 'id_plan');
    }

    public static function getdetalleAsiento($r)
    {
        $data = AsientosDetalles::join('plan_cuentas_empresa as pe', 'ct_asientos_detalle.id_plan_cuenta', '=', 'pe.id_plan')
            ->where('ct_asientos_detalle.id_asiento_cabecera', $r->id)
            ->where('pe.id_empresa', session('id_empresa'))
            ->where('pe.estado', 2)
            ->groupBy('pe.plan', 'ct_asientos_detalle.descripcion', 'pe.nombre', 'ct_asientos_detalle.id_plan_cuenta')
            ->select([
                'pe.id',
                'pe.plan as id_plan_cuenta',
                'pe.nombre',
                'ct_asientos_detalle.descripcion',
                DB::raw('SUM(ct_asientos_detalle.debe) as debe'),
                DB::raw('SUM(ct_asientos_detalle.haber) as haber'),
                'pe.nombre'
            ])->get();

        //dd($data);

        $html = '';
        foreach ($data as $value) {
            $html .= "<tr id='tr_{$value->id}'>
            <td style='max-width: 100%;'>
                <select class='form-control select2_cuenta' id='id_cuenta_{$value->id}' name='cuenta[]'>
                    <option value='{$value->id_plan_cuenta}'>{$value->id_plan_cuenta} | {$value->nombre}</option>
                </select>
            </td>
            <td><input type='text' class='form-control' value='{$value->descripcion}' id='detalle_{$value->id}' name='detalle[]'/></td>
            <td><input type='text' class='form-control text-right' id='debe_{$value->id}' name='debe[]' value='{$value->debe}' onchange='valor_debe({$value->id})'/></td>
            <td><input type='text' class='form-control text-right' id='haber_{$value->id}' name='haber[]' value='{$value->haber}' onchange='valor_haber({$value->id})'/></td>
            <td><a href='javascript:;' class='btn btn-danger' onclick='eliminarFilaAsiento({$value->id})'><i class='fa fa-trash-alt'></i></a>
            </td>
            <tr>";
        }
        return ['table' => $html];
    }

    public static function checkpass($r)
    {
        $hashedPassword = Auth::user()->password;
        $ingresada      = $r['userpass'];
        if (Hash::check($ingresada, $hashedPassword)) {
            return 'ok';
        } else {
            return 'error';
        }
    }

    public static function updateAsientoDescuadrado($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->except(['d', '_token']);
            $sri = $r->input('sri') === 'on' ? 1 : 0;
            $ip = Utilidades::getRealIP();
            $idusuario = Auth::id();
            $id_empresa = session('id_empresa');

            $cabecera = AsientosCabecera::find($r->id_asiento_cabecera);
            if (!is_null($cabecera)) {
                $nuev = AsientosDetalles::moverTabla($r->id_asiento_cabecera);
                if ($nuev["status"] == "error") {
                    DB::rollBack();
                    return ["status" => "error", "msj" => "Error al editar", "error" => $nuev["error"]];
                }
            }
            AsientosDetalles::where('id_asiento_cabecera', $r->id_asiento_cabecera)->delete();
            if (!empty($data['cuenta'])) {
                foreach ($data['cuenta'] as $index => $cuentaId) {
                    $cuenta = PlanCuentas::where('id_plan', $cuentaId)->orWhere('plan', $cuentaId)->first();
                    if ($cuenta) {
                        $plan = $cuenta->id_plan;
                        $detalles = AsientosDetalles::editDetalles($r, $plan, $cabecera, $index);
                        if ($detalles['status'] === 'error') {
                            $plan = $cuenta->plan;
                        }
                    }
                }
            }
            if ($r->totaldebe > 0 && $r->totalhaber > 0) {
                if (!is_null($cabecera)) {
                    $input_actualiza = [
                        'aparece_sri'     => $sri,
                        'valor'  => $r->totaldebe,
                        'observacion'  => $r->observacion,
                        'ip_modificacion' => $ip,
                        'id_usuariomod'   => $idusuario,
                    ];
                    AsientosCabecera::where('id', $r->id_asiento_cabecera)->where('estado', '<>', '0')->where('id_empresa', $id_empresa)->update($input_actualiza);
                }
            }
            $r->c = 'Contabilidad';
            $r->s = 'updateAsientoDescuadrado';
            $r->o = 'Se actualizo el asiento.: ' . $r->id_asiento_cabecera;
            $cont++;
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
            $r->c = 'Contabilidad';
            $r->s = 'updateAsientoDescuadrado';
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            //DB::rollBack();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                //'reload' => 'v3/contabilidad/descuadrados',
                //'reload' => '',
            ];
            return json_encode($arrayRespuesta);
        }
    }

    public static function moverTabla($id)
    {
        $cabecera = AsientosCabecera::find($id);
        DB::beginTransaction();
        try {
            if (!is_null($cabecera)) {
                $data2 = [
                    'observacion'     => $cabecera->observacion,
                    'fecha_asiento'   => $cabecera->fecha_asiento,
                    'valor'           => $cabecera->valor,
                    'id_empresa'      => $cabecera->id_empresa,
                    'aparece_sri'     => $cabecera->aparece_sri,
                    'especial'        => $cabecera->especial,
                    'ip_creacion'     => $cabecera->ip_creacion,
                    'ip_modificacion' => $cabecera->ip_modificacion,
                    'id_usuariocrea'  => $cabecera->id_usuariocrea,
                    'id_usuariomod'   => $cabecera->id_usuariomod,
                    'id_ant'          => $cabecera->id
                ];

                $id_asiento = ImpAsientosCabecera::insertGetId($data2);

                if (count($cabecera->detalles) > 0) {
                    foreach ($cabecera->detalles as $details) {
                        $data2 = [
                            'id_asiento_cabecera' => $id_asiento,
                            'id_plan_cuenta'      => $details->id_plan_cuenta,
                            'descripcion'         => $details->descripcion,
                            'fecha'               => $details->fecha,
                            'debe'                => $details->debe,
                            'haber'               => $details->haber,
                            'estado'              => $details->estado,
                            'ip_creacion'         => $details->ip_creacion,
                            'ip_modificacion'     => $details->ip_modificacion,
                            'id_usuariocrea'      => $details->id_usuariocrea,
                            'id_usuariomod'       => $details->id_usuariomod,
                        ];
                        ImpAsientosDetalle::insertGetId($data2);
                    }
                }
            }
            DB::commit();
            return ["status" => "success", "msj" => "Guardado con exito"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "Error..", "error" => $e->getMessage()];
        }
    }

    public static function editDetalles($r, $plan, $cabecera, $i)
    {
        $ip = Utilidades::getRealIP();
        $idusuario = Auth::user()->id;
        DB::beginTransaction();
        try {
            $data = [
                'id_asiento_cabecera' => $cabecera->id,
                'id_plan_cuenta'      => $plan,
                'descripcion'         => $r['detalle'][$i],
                'fecha'               => $cabecera->fecha_asiento,
                'debe'                => $r['debe'][$i],
                'haber'               => $r['haber'][$i],
                'estado'              => '1',
                'ip_creacion'         => $ip,
                'ip_modificacion'     => $ip,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];
            AsientosDetalles::insertGetId($data);
            DB::commit();
            return ['status' => 'success', 'ok' => true];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', 'ok' => false];
        }
    }
}
