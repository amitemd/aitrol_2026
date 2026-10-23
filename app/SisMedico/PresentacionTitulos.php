<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class PresentacionTitulos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'presentacion_titulos';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalle()
    {
        return $this->hasMany('Sis_medico\PresentacionDetalle', 'id_titulo');
    }

    
}