<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Plan_Cuentas;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\PlanCuentas;

class Rubros extends Model
{
    private static $modelo = 'Rubros';
    protected $table = 'ct_rubros';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getAllRubros()
    {
        $arrayRespuesta = [                 
            'rubros' => DB::table('ct_rubros')->where('ct_rubros.id_empresa', session('id_empresa'))->get(),
        ];
        return json_encode($arrayRespuesta);

    }
    public static function getRubros($codigo = ''){
        if ($codigo == '') {
            return Rubros::join('plan_cuentas as pc', 'pc.id', 'ct_rubros.debe')
            ->leftjoin('plan_cuentas as pc1', 'pc1.id', 'ct_rubros.haber')
            ->where('ct_rubros.id_empresa', session('id_empresa'))
            //->where('pc.estado', '2')
            ->get([
                'ct_rubros.codigo',
                'ct_rubros.nombre',
                'pc.nombre as debe',
                'pc1.nombre as haber',
                'ct_rubros.estado',
                'ct_rubros.tipo'
            ]);
        } else {
            return Rubros::where('ct_rubros.id_empresa', session('id_empresa'))
            ->leftjoin('plan_cuentas as pc', 'pc.id', 'ct_rubros.debe')
            ->leftjoin('plan_cuentas as pc1', 'pc1.id', 'ct_rubros.haber')
            //->where('pc.estado', '2')
            ->where('ct_rubros.codigo', $codigo)
            ->first([
                'ct_rubros.codigo',
                'ct_rubros.nombre',
                'ct_rubros.debe',
                'ct_rubros.haber',
                'ct_rubros.estado',
                'ct_rubros.tipo'
            ]);
        }
    }

    public static function cambiarEstado($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Rubros::where('codigo', $r->id)->update(['estado' => $estado])) {
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
            $ip = Utilidades::getRealIP();
            $rubros = Rubros::where('codigo', $data['codigo'])->first();
            if ($rubros == '') {
                $data['id_empresa'] = session('id_empresa');
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = Rubros::insertGetId($data);
                $r->c = 'Proveedores';
                $r->s = 'saveRubros';
                $r->d = $origin['d'];
                $r->m = Rubros::$modelo;
                $r->o = 'Se creo el rubro con id.: ' . $id;
                $cont++;
            } else {
                $codigo = $data['codigo'];
                $data['id_empresa'] = session('id_empresa');
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Rubros::where('codigo', $codigo)->update($data);
                $r->c = 'Proveedores';
                $r->s = 'saveRubros';
                $r->d = $origin['d'];
                $r->m = Rubros::$modelo;
                $r->o = 'Se actualizo el rubro con id.: ' . $codigo;
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
            $r->m = Rubros::$modelo;
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
                'reload'=>''
            ];
            return json_encode($arrayRespuesta);
        }
    }

    public static function cuentaContable($id)
    {
        $combo = '';
        $cuenta = PlanCuentas::selectRaw('plan as id, nombre as text')
            ->where('plan', $id)
            ->where('id_empresa', session('id_empresa'))
            ->first();

        if ($cuenta == '') {
            $cuenta = PlanCuentas::selectRaw('id_plan as id, nombre as text')
                ->where('id_plan', $id)
                ->where('id_empresa', session('id_empresa'))
                ->first();
        }
        if ($cuenta != '') {
            $combo = '<option value="' . $cuenta->id . '" selected>' . $cuenta->id . ' | ' . $cuenta->text . '</option>';
        }
        return $combo;
    } 

    public static function verificarRubro($r)
    {
        $arrayRespuesta = [];
        $codRubro = Rubros::where('codigo', $r->codigo)->first([
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
