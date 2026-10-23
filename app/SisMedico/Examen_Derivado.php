<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Derivado extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_derivado';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function examen(){
        return $this->belongsTo('Sis_medico\Examen', 'id_examen');
    }

    public function tipo_derivado(){
        return $this->belongsTo('Sis_medico\Examen_Tipo_Derivado', 'id_tipo');
    }

    
}