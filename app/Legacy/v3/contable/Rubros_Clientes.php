<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Plan_Cuentas;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class Rubros_Clientes extends Model
{
    private static $modelo = 'Rubros_Clientes';
    protected $table = 'ct_rubros_cliente';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getRubros($codigo = '')
    {
        if ($codigo == '') {
            return Rubros_Clientes::where('ct_rubros_cliente.id_empresa', session('id_empresa'))
                ->leftjoin('plan_cuentas_empresa as pe', function ($q) {
                    $q->on('pe.id_plan', 'ct_rubros_cliente.debe')
                        ->where('pe.id_empresa', session('id_empresa'));
                })
                ->join('plan_cuentas as pc', 'pc.id', 'pe.id_plan')
                ->leftjoin('plan_cuentas_empresa as pe1', function ($q) {
                    $q->on('pe1.id_plan', 'ct_rubros_cliente.haber')
                        ->where('pe1.id_empresa', session('id_empresa'));
                })
                ->leftjoin('plan_cuentas as pc1', 'pc1.id', 'pe1.id_plan')
                //->where('pc.estado', '2')
                ->get([
                    'ct_rubros_cliente.codigo',
                    'ct_rubros_cliente.nombre',
                    'pe.nombre as debe',
                    'pe1.nombre as haber',
                    'ct_rubros_cliente.estado',
                    'ct_rubros_cliente.tipo'
                ]);
        } else {
            return Rubros_Clientes::join('plan_cuentas_empresa as pe', 'pe.plan', 'ct_rubros_cliente.debe')
                ->leftjoin('plan_cuentas as pc', 'pc.id', 'pe.id_plan')
                ->join('plan_cuentas_empresa as pe1', 'pe1.plan', 'ct_rubros_cliente.haber')
                ->leftjoin('plan_cuentas as pc1', 'pc1.id', 'pe1.id_plan')
                ->where('ct_rubros_cliente.id_empresa', session('id_empresa'))
                //->where('pc.estado', '2')
                ->where('ct_rubros_cliente.codigo', $codigo)
                ->first([
                    'ct_rubros_cliente.codigo',
                    'ct_rubros_cliente.nombre',
                    'ct_rubros_cliente.debe',
                    'ct_rubros_cliente.haber',
                    'ct_rubros_cliente.estado',
                    'ct_rubros_cliente.tipo'
                ]);
        }
    }
    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Rubros_Clientes::where('codigo', $r->id)->update(['estado' => $estado])) {
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
    public static function saveRubros($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            unset($data['tipo_r']);
            unset($data['emily']);
            $ip = Utilidades::getRealIP();
            $rubros = Rubros_Clientes::where('codigo', $data['codigo'])->first();
            $data['tipo'] =  $origin['tipo_r'];
            if ($rubros == '') {
                $data['id_empresa'] = session('id_empresa');
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = Rubros_Clientes::insertGetId($data);
                $r->c = 'Proveedores';
                $r->s = 'saveRubros';
                $r->d = $origin['d'];
                $r->m = Rubros_Clientes::$modelo;
                $r->o = 'Se creo el rubro con id.: ' . $id;
                $cont++;
            } else {
                if ($origin['emily'] == 'editar') {
                    $codigo = $data['codigo'];
                    $data['id_empresa'] = session('id_empresa');
                    $data['id_usuariomod'] = Auth::user()->id;
                    $data['ip_modificacion'] = $ip;
                    Rubros_Clientes::where('codigo', $codigo)->update($data);
                    $r->c = 'Proveedores';
                    $r->s = 'saveRubros';
                    $r->d = $origin['d'];
                    $r->m = Rubros_Clientes::$modelo;
                    $r->o = 'Se actualizo el rubro con id.: ' . $codigo;
                } else {
                    $arrayRespuesta = [
                        'status' => 'error',
                        'code' => 500,
                        'message' => 'El código que intenta guardar ya existe en la base de datos',
                        'linea' => 131,
                        'file' => 'Rubros_Clientes',
                        'data' => $r->input(),
                    ];
                    return json_encode($arrayRespuesta);
                }
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
            $r->c = 'Proveedores';
            $r->s = 'saveProveedor';
            $r->d = $origin['d'];
            $r->m = Rubros_Clientes::$modelo;
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
                //'reload' => '',
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function cuentaContable($id)
    {
        $combo = '';
        $cuenta = PlanCuentas::where('id_plan', $id)->where('id_empresa', session('id_empresa')->where('estado', '2'))->first();
        if ($cuenta != '') {
            $combo = '<option value="' . $cuenta->id_plan . '" selected>' . $cuenta->id_plan . ' | ' . $cuenta->nombre . '</option>';
        }
        return $combo;
    }
    public static function verificarRubro($r)
    {
        $arrayRespuesta = [];
        $codRubro = Rubros_Clientes::where('codigo', $r->codigo)->first([
            'codigo'
        ]);
        if ($codRubro != '') {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => 'El código ya se encuentra registrado...',
                'data' => $r->input(),
            ];
        } else {
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => '',
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }
}
