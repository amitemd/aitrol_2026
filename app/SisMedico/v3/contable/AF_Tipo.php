<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class AF_Tipo extends Model
{
    private static $modelo = 'AF_Tipo';
    protected $table = 'af_tipo';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getAF_Tipo($id = '')
    {
        if ($id == '') {
            return AF_Tipo::join('af_grupo as ag', 'af_tipo.grupo_id', 'ag.id')
                ->get([
                    'af_tipo.id',
                    'ag.codigo',
                    'ag.nombre as grupo',
                    'af_tipo.codigo as cod_tipo',
                    'af_tipo.nombre',
                    'af_tipo.cuentamayor',
                    'af_tipo.cuentagastos',
                    'af_tipo.cuantadepreciacion',
                    'ag.id as grupo_id',
                    'af_tipo.tipo_tasa',
                    'af_tipo.estado'

                ]);
        } else {
            return AF_Tipo::join('af_grupo as ag', 'af_tipo.grupo_id', 'ag.id')
                ->where('af_tipo.id', $id)
                ->first([
                    'af_tipo.id',
                    'ag.codigo',
                    'ag.nombre as grupo',
                    'af_tipo.codigo as cod_tipo',
                    'af_tipo.nombre',
                    'af_tipo.cuentamayor',
                    'af_tipo.cuentagastos',
                    'af_tipo.cuantadepreciacion',
                    'af_tipo.tasa',
                    'af_tipo.tipo_tasa',
                    'ag.id as grupo_id',
                    'af_tipo.vidautil',
                    'af_tipo.estado'
                ]);
        }
    }
    public static function saveTipoAf($r)
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
                $id = AF_Tipo::insertGetId($data);
                $r->c = 'ActivosFijos';
                $r->s = 'saveTipoAf';
                $r->d = $origin['d'];
                $r->m = AF_Tipo::$modelo;
                $r->o = 'Se agrego el nuevo tipo con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                AF_Tipo::where('id', $id)->update($data);
                $r->c = 'ActivosFijos';
                $r->s = 'saveTipoAf';
                $r->d = $origin['d'];
                $r->m = AF_Tipo::$modelo;
                $r->o = 'Se actualizo el tipo con id.: ' . $id;
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
            $r->c = 'ActivosFijos';
            $r->s = 'saveTipoAf';
            $r->d =  $origin['d'];
            $r->m = AF_Tipo::$modelo;
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
            if (AF_Tipo::where('id', $r->id)->update(['estado' => $estado])) {
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
    public static function getTipo($id = '')
    {
        if ($id == '') {
            return AF_Tipo::get([
                'id',
                'nombre',
            ]);
        } else {
            return AF_Tipo::where('af_tipo.id', $id)
                ->first([
                    'id',
                    'nombre',
                ]);
        }
    }

    public static function buscarTipos($term)
    {
        return AF_Tipo::selectRaw('id, nombre as text')
        ->where('nombre', 'like', '%' . $term . '%')
        ->where('estado', '1')
        ->get();
    }
}
