<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_estado_paso_insumo extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_estado_paso_insumo';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */

    public function insumo()
    {
        return $this->belongsTo('Sis_medico\Producto','id_insumo');
    }


    protected $guarded = [];

   

}