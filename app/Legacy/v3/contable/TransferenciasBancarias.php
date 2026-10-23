<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransferenciasBancarias extends Model
{
    private static $modelo = 'Transferencias Bancarias';
    protected $table = 'ct_transferencia_bancaria';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getTransferenciasBancarias($id = '', $r)
    {
        if ($id == '') {
            return TransferenciasBancarias::join('users as uc', 'ct_transferencia_bancaria.id_usuariocrea', '=', 'uc.id')
                ->leftJoin('users as um', 'ct_transferencia_bancaria.id_usuariomod', '=', 'um.id')
                ->whereYear('ct_transferencia_bancaria.fecha_asiento', session('periodo'))
                ->where('ct_transferencia_bancaria.empresa', session('id_empresa'))
                ->where(function ($q) use ($r) {
                    if ($r->desde != '')
                        $q->whereBetween("ct_transferencia_bancaria.fecha_asiento", [$r->desde . ' 00:00:00', $r->hasta . ' 23:59:59']);
                })
                ->get([
                    'ct_transferencia_bancaria.id',
                    'ct_transferencia_bancaria.id_asiento',
                    'ct_transferencia_bancaria.numcheque',
                    'ct_transferencia_bancaria.fecha_cheque',
                    'ct_transferencia_bancaria.concepto',
                    'ct_transferencia_bancaria.valor_destino',
                    DB::raw('CONCAT(SUBSTRING(uc.nombre1, 1, 1), ". ", uc.apellido1) AS usuariocrea'),
                    DB::raw('IF(ct_transferencia_bancaria.estado=0, CONCAT(SUBSTRING(um.nombre1, 1, 1), ". ", um.apellido1), "") AS usuariomod'),
                    'ct_transferencia_bancaria.estado'
                ]);
        } else {
            return TransferenciasBancarias::find($id);
        }
    }
}
