<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Comision extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_comision';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function examen()
    {
        return $this->belongsTo('Sis_medico\Examen','id_examen');
    }
    public function doc_externo()
    {
        return $this->belongsTo('Sis_medico\Labs_doc_externos','codigo');   
    }
}
