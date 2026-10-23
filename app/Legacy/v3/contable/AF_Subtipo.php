<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class AF_Subtipo extends Model
{
    private static $modelo = 'AF_Subtipo';
    protected $table = 'af_subtipo';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';



    public static function buscarSubTipos($term)
    {
        return AF_Subtipo::selectRaw('id, nombre as text')
        ->where('nombre', 'like', '%' . $term . '%')
        ->where('estado', '1')
        ->get();
    }



    public static function verificarCodigoSubtipo($r)
    {
        $arrayRespuesta = [];
        $subtipo = AF_Subtipo::where('codigo', $r->codigo)->first([
            'codigo'
        ]);
        if ($subtipo != '') {
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
    public static function getAF_Subtipos($id = '')
    {
        if ($id == '') {
            return AF_Subtipo::join('af_tipo as at', 'af_subtipo.tipo_id', 'at.id')
                ->get([
                    'af_subtipo.id',
                    'af_subtipo.codigo',
                    'at.nombre as tipo',
                    'af_subtipo.nombre',
                    'af_subtipo.estado'

                ]);
        } else {
            return AF_Subtipo::join('af_tipo as at', 'af_subtipo.tipo_id', 'at.id')
                ->where('at.id', $id)
                ->get([
                    'af_subtipo.id',
                    'af_subtipo.codigo',
                    'at.nombre as tipo',
                    'af_subtipo.nombre',
                    'af_subtipo.estado'
                ]);
        }
    }
    public static function getSubtipos($id = '')
    {
        if ($id == '') {
            return AF_Subtipo::join('af_tipo as at', 'af_subtipo.tipo_id', 'at.id')
                ->get([
                    'af_subtipo.id',
                    'af_subtipo.codigo',
                    'at.nombre as tipo',
                    'af_subtipo.nombre',
                    'af_subtipo.estado'

                ]);
        } else {
            return AF_Subtipo::join('af_tipo as at', 'af_subtipo.tipo_id', 'at.id')
                ->where('af_subtipo.id', $id)
                ->first([
                    'af_subtipo.id',
                    'af_subtipo.codigo',
                    'at.nombre as tipo',
                    'af_subtipo.nombre',
                    'af_subtipo.estado',
                    'af_subtipo.tipo_id'
                ]);
        }
    }
    public static function saveSubtipoAf($r)
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
                $id = AF_Subtipo::insertGetId($data);
                $r->c = 'ActivosFijos';
                $r->s = 'saveSubtipoAf';
                $r->d = $origin['d'];
                $r->m = AF_Subtipo::$modelo;
                $r->o = 'Se agrego el nuevo subtipo con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                AF_Subtipo::where('id', $id)->update($data);
                $r->c = 'ActivosFijos';
                $r->s = 'saveSubtipoAf';
                $r->d = $origin['d'];
                $r->m = AF_Subtipo::$modelo;
                $r->o = 'Se actualizo el subtipo con id.: ' . $id;
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
            $r->s = 'saveSubtipoAf';
            $r->d =  $origin['d'];
            $r->m = AF_Subtipo::$modelo;
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
    public static function estadoSubtipoAf($r)
    {
        
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (AF_Subtipo::where('id', $r->id)->update(['estado' => $estado])) {
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
}
