<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Labs_Factura_Agrupada_Orden extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_factura_agrupada_orden';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function examen_orden()
    {
        return $this->belongsTo('Sis_medico\Examen_Orden','id_examen_orden');
    }

}