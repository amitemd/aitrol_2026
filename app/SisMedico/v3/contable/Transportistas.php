<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class Transportistas extends Model
{
    private static $modelo = 'Transportista';
    protected $table = 'transportistas';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getTransportistas($id = '')
    {
        if ($id == '') {
            return Transportistas::leftjoin('new_db_catalogo as c', 'c.id_catalogo', 'transportistas.tipo_documento')
                ->leftjoin('new_db_catalogo as p', 'c.id_catalogo_pertenece', 'p.id_catalogo')
                //->where('p.catalogo', 'Tipo de identificacion')
                ->get([
                    'transportistas.id',
                    'transportistas.ci_ruc',
                    'transportistas.razon_social',
                    'transportistas.nombrecomercial',
                    'transportistas.ciudad',
                    'c.catalogo as tipo',
                    'transportistas.rise',
                    'transportistas.contabilidad',
                    'transportistas.contribuyente_especial',
                    'transportistas.estado',
                    'transportistas.email',
                ]);
        } else {
            return Transportistas::leftjoin('new_db_catalogo as c', 'c.id_catalogo', 'transportistas.tipo_documento')
                ->leftjoin('new_db_catalogo as p', 'c.id_catalogo_pertenece', 'p.id_catalogo')
                ->leftjoin('new_db_ciudades as ci', 'ci.id_ciudad', 'transportistas.id_ciudad')
                //->where('p.catalogo', 'Tipo de identificacion')
                ->where('transportistas.id', $id)
                ->first([
                    'transportistas.web',
                    'transportistas.id',
                    'transportistas.placa',
                    'transportistas.ci_ruc',
                    'transportistas.razon_social',
                    'transportistas.nombrecomercial',
                    'transportistas.ciudad',
                    'transportistas.email',
                    'c.valor as tipo',
                    'transportistas.rise',
                    'transportistas.contabilidad',
                    'transportistas.contribuyente_especial',
                    'transportistas.estado',
                    'ci.id_pais_ciudad',
                    'ci.id_provincia_ciudad',
                    'ci.id_ciudad',
                    'transportistas.telefono1',
                    'transportistas.telefono2',
                    'transportistas.urbanizacion',
                    'transportistas.etapa',
                    'transportistas.manzana',
                    'transportistas.villa_solar',
                    'transportistas.direccion',
                    'transportistas.referencia'
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
                $id = Transportistas::insertGetId($data);
                $r->c = 'GuiadeRemision';
                $r->s = 'saveTransportista';
                $r->d = $origin['d'];
                $r->m = Transportistas::$modelo;
                $r->o = 'Se agrego el nuevo transportista con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Transportistas::where('id', $id)->update($data);
                $r->c = 'GuiadeRemision';
                $r->s = 'saveTransportista';
                $r->d = $origin['d'];
                $r->m = Transportistas::$modelo;
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
            $r->m = Transportistas::$modelo;
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
            if (Transportistas::where('id', $r->id)->update(['estado' => $estado])) {
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
