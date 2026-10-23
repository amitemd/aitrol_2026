<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Triaje_Telefonico extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'triaje_telefonico';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function bodega_compras(){
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega_compra');
    }

}
