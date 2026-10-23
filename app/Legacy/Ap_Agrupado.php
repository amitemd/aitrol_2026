<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ap_Agrupado extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ap_agrupado';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function fx_seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro','seguro'); 
    }

    public function fx_empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','empresa'); 
    }

}