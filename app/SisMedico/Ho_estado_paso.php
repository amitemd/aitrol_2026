<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_estado_paso extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_estado_paso';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */

    public function paso_insumos()
    {
        return $this->hasMany('Sis_medico\Ho_estado_paso_insumo', 'id_estado_paso');
    }

    protected $guarded = [];

   

}