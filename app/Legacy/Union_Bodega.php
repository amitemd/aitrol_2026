<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Union_Bodega extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'union_bodega';

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
