<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Rh_Valores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rh_valores';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /*public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa')->with('empresa');
        
    }*/


}
