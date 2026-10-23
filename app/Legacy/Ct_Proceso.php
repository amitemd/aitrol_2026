<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Proceso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_proceso';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Proceso_Detalle','id_referencia');
    }
   

}