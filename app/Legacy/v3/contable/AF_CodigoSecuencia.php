<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Empresas;
use Sis_medico\v3\contable\AF_Tipo;



class AF_CodigoSecuencia extends Model
{
    private static $modelo = 'AF_CodigoSecuencia';
    protected $table = 'af_codigo_secuencia';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function CodigoAf($r){
        DB::enableQueryLog();
        DB::beginTransaction();
        $id_empresa = $r->session()->get('id_empresa');
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $empresa = Empresas::where('id', $id_empresa)->where('estado', 1)->first();
        $tipo = AF_Tipo::where('id', $r->tipo)->where('estado', 1)->first();
        try {
            $num = AF_CodigoSecuencia::where('id_empresa', session('id_empresa'))
                ->where('id_tipo', $r->tipo)
                ->first();

            if (is_null($num)) {
                $arrayDetalle = [
                    'id_tipo'           => $r->tipo,
                    'secuencia'         => '000001',
                    'id_empresa'        => $id_empresa,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];
                AF_CodigoSecuencia::insert($arrayDetalle);
                $codigo_activo = '000001';
            }else{
                $codigo_activo = intval($num->secuencia);
                $codigo_activo++;
                $codigo_activo = str_pad($codigo_activo, 6, "0", STR_PAD_LEFT);
                $arrayInfo = [
                    'secuencia' => $codigo_activo,
                    'id_usuariomod' => $idusuario,
                ];
                AF_CodigoSecuencia::where('id_empresa', session('id_empresa'))->where('id_tipo', $r->tipo)->update($arrayInfo);
            }
            $arr_sec = [
                'codigo' => 'AF-' . $empresa->abreviatura_emp . '-' . $tipo->abreviatura . '-' . $codigo_activo,
                'secuencia' => $codigo_activo,
                'tasa' => $tipo->tasa,
                'vida_util' => $tipo->vidautil
            ];
            DB::commit();
            return $arr_sec;
            
        } catch (Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => $e->getmessage(), 'linea' => $e->getLine()];
        }   
    }

}
