<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Archivo_Plano_Detalle extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'archivo_plano_detalle';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function cabecera()
    {
        
        return $this->belongsTo('Sis_medico\Archivo_Plano_Cabecera','id_ap_cabecera');
    }

}
