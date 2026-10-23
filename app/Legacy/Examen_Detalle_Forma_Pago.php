<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Detalle_Forma_Pago extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_detalle_forma_pago';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function tipo_pago()
    {
        return $this->belongsTo('Sis_medico\Ct_Tipo_Pago', 'id_tipo_pago');
    }

    public function bancos()
    {
        return $this->belongsTo('Sis_medico\Ct_Bancos', 'banco');
    }

    public function tarjetas()
    {
        return $this->belongsTo('Sis_medico\Ct_Tipo_Tarjeta', 'tipo_tarjeta');
    }

}