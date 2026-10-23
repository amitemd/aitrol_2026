<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Formulario_053 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'formulario_053';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor');
    }

}
