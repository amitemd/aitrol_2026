<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\v3\contable\Auditorias;
use Sis_medico\v3\contable\Utilidades;

class NotaDebitoBancaria extends Model
{
    private static $modelo = 'NotaDebitoBancaria';
    protected $table = 'ct_debito_bancario';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    public function empresas()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'empresa');
    }
    public function banco()
    {
        return $this->belongsTo('Sis_medico\Ct_Caja_Banco', 'id_banco');
    }
    public function creador()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Nota_Debito_Detalle', 'id_nota_debito');
    }
    public static function saveDebito($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $dataDetalle = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $compra = NotaDebitoBancaria::find($data['id']);
            if ($compra == '') {
                $numero_factura = NotaDebitoBancaria::getMaxId();
                $cabecera['fecha'] = $r->fecha;
                $cabecera['tipo'] = $r->tipo_c;
                $cabecera['id_acreedor'] = $r->id_acreedor;
                $cabecera['id_banco'] = $r->id_banco;
                $cabecera['id_divisa'] = $r->id_divisa;
                $cabecera['id_empresa'] = session('id_empresa');
                $cabecera['id_asiento'] = 73;
                $cabecera['secuencia'] = $numero_factura;
                $cabecera['concepto'] = $r->concepto_c;
                $cabecera['cambio'] = $r->cambio;
                $cabecera['valor'] = $r->valor;
                $cabecera['total_debito'] = $r->total_debito;
                $cabecera['debito_aplicado'] = $r->debito_aplicado;
                $cabecera['total_deudas'] = $r->total_deudas;
                $cabecera['total_abonos'] = $r->total_abonos;
                $cabecera['nuevo_saldo'] = $r->nuevo_saldo;
                $cabecera['deficit'] = $r->deficit;
                $cabecera['debito_favor'] = $r->debito_favor;
                $cabecera['nota'] = $r->nota;
                $cabecera['estado'] = 1; //0:inactivo, 1:activo
                $cabecera['id_usuariocrea'] = Auth::user()->id;
                $cabecera['id_usuariomod'] = Auth::user()->id;
                $cabecera['ip_creacion'] = $ip;
                $cabecera['ip_modificacion'] = $ip;
                $id = NotaDebitoBancaria::insertGetId($cabecera);
                for ($i = 0; $i < count($dataDetalle["numero"]); $i++) {
                    $detalle['id_debito'] = $id;
                    $detalle['numero'] = $r->numero[$i];
                    $detalle['id_compra'] = $r->id_compra[$i];
                    $detalle['tipo'] = $r->tipo[$i];
                    $detalle['fecha_vencimiento'] = $r->fecha_vencimiento[$i];
                    $detalle['concepto'] = $r->concepto[$i];
                    $detalle['saldo'] = $r->saldo[$i];
                    $detalle['abono'] = $r->abono[$i];
                    $detalle['estado'] = 1;
                    $detalle['id_usuariocrea'] = Auth::user()->id;
                    $detalle['id_usuariomod'] = Auth::user()->id;
                    $detalle['ip_creacion'] = $ip;
                    $detalle['ip_modificacion'] = $ip;
                    DebitoBancarioDetalle::insertGetId($detalle);
                }
                $r->c = 'NotaDebitoBancaria';
                $r->s = 'saveDebito';
                $r->d = $origin['d'];
                $r->m = NotaDebitoBancaria::$modelo;
                $r->o = 'Se creo la nota debito bancario con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                NotaDebitoBancaria::where('id', $id)->update($data);
                $r->c = 'NotaDebitoBancario';
                $r->s = 'saveDebito';
                $r->d = $origin['d'];
                $r->m = NotaDebitoBancaria::$modelo;
                $r->o = 'Se actualizo la nota de debito con id.: ' . $id;
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
            $r->c = 'NotaDebitoBancaria';
            $r->s = 'saveDebito';
            $r->d = $origin['d'];
            $r->m = NotaDebitoBancaria::$modelo;
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

    public static function getMaxId(){
        $contador = NotaDebitoBancaria::where('id_empresa', session('id_empresa'))->get()->count();
        if ($contador == 0) {
            $num = '1';
            $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);                    
        } else {
            $notaDebito = NotaDebitoBancaria::where('id_empresa', session('id_empresa'))->orderBy('id','DESC')->first();
            $max_id = intval($notaDebito->secuencia);
            if (strlen($max_id) < 10) {
                $num = $max_id + 1;
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            }
        }
        return $numero_factura;
    }
}
