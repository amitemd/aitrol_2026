<?php

namespace Sis_medico\v3\contable;

use DB;
use Illuminate\Database\Eloquent\Model;

class DepositoBancario extends Model
{
    protected $table = 'ct_deposito_bancario';
    protected $primaryKey  = 'id';
    protected $keyType = 'string';

    public static function getNumeroSecuencial()
    {
        $numero = 1;
        $num = self::where('empresa', session('id_empresa'))->orderBy(DB::raw('CAST(numero AS UNSIGNED)'), 'desc')->first();
        if ($num != '') {
            $numero = $num->numero + 1;
        }
        return $numero;
    }
    public function detalles()
    {

        return $this->hasMany('Sis_medico\v3\contable\DetalleDepositoBancario', 'deposito_bancario_id', 'id');
    }
}
