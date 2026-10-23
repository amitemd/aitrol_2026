<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InfoTributaria extends Model
{
    protected $table = 'de_info_tributaria';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';

    public static function getDatos($id, $esta, $emis, $idDoc)
    {

       
        return InfoTributaria::where('id_empresa', $id)
            ->where('cod_sucursal', $esta)
            ->where('cod_caja', $emis)
            ->where('id_maestro_documentos', $idDoc)
            ->where('estado', 1)
            ->first([
                'secuencial_nro',
                'id_sucursal',
                'id_caja',
                'id_maestro_documentos'
            ]);
    }
    public static function crearInfoTributaria($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        try {
            $idusuario = isset(Auth::user()->id) ? Auth::user()->id : '';
            $id_empresa = session('id_empresa');
            if ($idusuario == '') {
                $idusuario = $r->idUsuario;
            }
            if ($id_empresa == '') {
                $id_empresa = $r->id_empresa;
            }
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $serie = explode('-', $r->s);
            $documentos = MaestroDocumentos::where('estado', 1)->get(['id']);
            $infoTributaria = InfoTributaria::whereIn('id_maestro_documentos', $documentos)
                ->where('id_empresa', $id_empresa)
                ->where('cod_sucursal', $serie[0])
                ->where('cod_caja', $serie[1])
                ->get();
            if ($infoTributaria->count() == 0) {
                foreach ($documentos as $row) {
                    $arrayDatos = [
                        'id_empresa' => $id_empresa,
                        'cod_sucursal' => $serie[0],
                        'cod_caja' => $serie[1],
                        'id_maestro_documentos' => $row->id,
                        'estado' => 1,
                        'id_sucursal' => $r->e,
                        'id_caja' => $r->p,
                        'numero_factura' => $serie[0] . '-' . $serie[1] . '-000000000',
                        'secuencial_nro' => 0,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    InfoTributaria::insert($arrayDatos);
                }
            }
            $infoTributaria = InfoTributaria::join('empresa as e', 'de_info_tributaria.id_empresa', 'e.id')
                ->join('de_maestro_documentos as d', 'de_info_tributaria.id_maestro_documentos', 'd.id')
                ->whereIn('id_maestro_documentos', $documentos)
                ->where('id_empresa', session('id_empresa'))
                ->where('cod_sucursal', $serie[0])
                ->where('cod_caja', $serie[1])
                ->get([
                    'de_info_tributaria.created_at as fecha',
                    'e.razonsocial as empresa',
                    'd.nombre as tipo',
                    'de_info_tributaria.cod_sucursal as establecimiento',
                    'de_info_tributaria.cod_caja as emision',
                    'de_info_tributaria.secuencial_nro as secuencial',
                    'de_info_tributaria.numero_factura as num',
                    'de_info_tributaria.estado'
                ]);
                echo '<pre>';
                print_r($infoTributaria);
                exit;
        } catch (Exception $e) {
            DB::rollBack();
            $arrayError = [
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            echo '<pre>';
            print_r($arrayError);
            exit;
        }
        DB::commit();
        return $infoTributaria;
    }
}
