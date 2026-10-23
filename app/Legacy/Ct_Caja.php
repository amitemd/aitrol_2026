<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Caja extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_caja';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function getCajas($num, $ruc)
    {
        return Ct_Caja::where('codigo_caja', $num)
            ->where('id_empresa', $ruc)
            ->first();
    }
    public function sucursal()
    {
        return $this->belongsTo('Sis_medico\Ct_Sucursales', 'id_sucursal', 'id');
    }
}
