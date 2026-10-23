<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Auditorias extends Model
{
    protected $table = 'new_db_auditorias';
    protected $primaryKey  = 'id_auditoria';
    const CREATED_AT = 'created_at_auditoria';
    const UPDATED_AT = 'updated_at_auditoria';

    public static function getAuditorias($id = '')
    {
        if ($id == '') {
            return Auditorias::join('users as u', 'new_db_auditorias.id_usuario_auditoria', 'u.id')
                ->where('id_empresa_auditoria', session('id_empresa'))
                ->orderBy('id_auditoria', 'desc')
                ->limit(1000)
                ->get([
                    'new_db_auditorias.id_auditoria',
                    'new_db_auditorias.controlador_auditoria',
                    'new_db_auditorias.submodulo_auditoria',
                    'new_db_auditorias.modelo_auditoria',
                    'new_db_auditorias.vista_auditoria',
                    'u.nombre1',
                    'u.apellido1',
                    'new_db_auditorias.created_at_auditoria',
                    'new_db_auditorias.sistema_auditoria',
                    'new_db_auditorias.navegador_auditoria',
                ]);
        } else {
            return Auditorias::join('users as u', 'new_db_auditorias.id_usuario_auditoria', 'u.id')
                ->where('id_auditoria', $id)
                ->first();
        }
    }
    public static function saveAuditoria($req, $query = null)
    {
        $consulta = '';
        $parametros = '';
        $tiempo = '';
        if ($query != null) {
            $consulta = $query[0]['query'];
            $parametros = $query[0]['bindings'];
            $tiempo = $query[0]['time'];
        }
        $res = Utilidades::detect();
        $navegador = $res['browser'];
        $sistema = $res['os'];
        if (isset($res['version']))
            $version = $res['version'];
        $ip = Utilidades::getRealIP();
        if (isset($req->d) && $req->d != '')
            $ubic = explode('|', $req->d);
        else
            $ubic = [0 => '', 1 => ''];
        $arrayAuditoria = [
            'controlador_auditoria' => $req->c,
            'submodulo_auditoria' => $req->s,
            'modelo_auditoria' => $req->m,
            'vista_auditoria' => $req->v,
            'query_auditoria' => $consulta,
            'parametros_auditoria' => json_encode($parametros),
            'tiempo_consulta_auditoria' => $tiempo,
            'id_usuario_auditoria' => Auth::user()->id != '' ? Auth::user()->id : 0,
            'id_empresa_auditoria' => session('id_empresa') != '' ? session('id_empresa') : 0,
            'ip_auditoria' => $ip,
            'navegador_auditoria' => $navegador,
            'sistema_auditoria' => $sistema,
            'version_navegador_auditoria' => $version,
            'latitud_auditoria' => isset($ubic[0]) ? $ubic[0] : '',
            'longitud_auditoria' => isset($ubic[1]) ? $ubic[1] : '',
            'agente_auditoria' => $_SERVER['HTTP_USER_AGENT'],
            'observacion_auditoria' => $req->o,
            'obj_auditoria' => json_encode($req->input())
        ];
        Auditorias::insert($arrayAuditoria);
    }
}
