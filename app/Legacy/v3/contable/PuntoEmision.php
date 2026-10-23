<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PuntoEmision extends Model
{
    private static $modelo = 'PuntoEmision';
    protected $table = 'ct_sucursales';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function saveEstablecimiento($r)
    {

        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $origin = $r->input();
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($r->sucursal == '') {
                $arrayEstablecimiento = [
                    'id_empresa' => session('id_empresa'),
                    'id_ciudad' => $r->id_ciudad,
                    'nombre_sucursal' => $r->nombre,
                    'codigo_sucursal' => $r->establecimiento,
                    'direccion_sucursal' => $r->direccion,
                    'email_sucursal' => $r->email,
                    'telefono_sucursal' => $r->telefono,
                    'estado' => 1,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip
                ];
                $idEstablecimiento = DB::table('ct_sucursales')->insertGetId($arrayEstablecimiento);
                $arrayEmision = [
                    'id_sucursal' => $idEstablecimiento,
                    'id_empresa' => session('id_empresa'),
                    'nombre_caja' => $r->nombre,
                    'codigo_caja' => $r->emision,
                    'estado' => $r->estado,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip
                ];
                DB::table('ct_caja')->insert($arrayEmision);
                $r->c = 'Configuraciones';
                $r->s = 'saveEstablecimiento';
                $r->d = $origin['d'];
                $r->m = PuntoEmision::$modelo;
                $r->o = 'Se creo el menu con id.: ' . $idEstablecimiento;
                $cont++;
            } else {
            }
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
            $r->c = 'Configuraciones';
            $r->s = 'saveEstablecimiento';
            $r->d =  $origin['d'];
            $r->m = PuntoEmision::$modelo;
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
    public static function validarPunto($es)
    {
        $emision = 0;
        $esta = PuntoEmision::join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_sucursales.id')
            ->where('codigo_sucursal', $es)
            ->where('ct_sucursales.id_empresa', session('id_empresa'))
            ->first();
        if ($esta != '') {
            $emision = (int)$esta->codigo_caja + 1;
        } else {
            $emision = 1;
        }
        $emision = str_pad($emision, 3, '0', STR_PAD_LEFT);
        return $emision;
    }
    public static function getPuntosEmision()
    {
        return PuntoEmision::join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_sucursales.id')
            ->where('ct_c.id_empresa', session('id_empresa'))
            ->get([
                'ct_sucursales.id',
                'ct_sucursales.codigo_sucursal',
                'ct_c.codigo_caja',
                'ct_sucursales.nombre_sucursal',
                'ct_sucursales.direccion_sucursal',
                'ct_sucursales.email_sucursal',
                'ct_sucursales.telefono_sucursal',
                'ct_sucursales.estado',
            ]);
    }
    public static function obtenerPuntoEmision($id)
    {
        return PuntoEmision::join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_sucursales.id')
            ->where('ct_c.estado', 1)
            ->where('ct_sucursales.id', $id)
            ->get();
    }
    public static function getPuntoEmision($id)
    {
        return PuntoEmision::join('ct_caja as ct_c', 'ct_c.id_sucursal', 'ct_sucursales.id')
            ->where('ct_c.estado', 1)
            ->where('ct_sucursales.id', $id)
            ->first();
    }
}
