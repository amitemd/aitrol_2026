<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Rc_resultados extends Model
{
    protected $table = 'rc_resultados';
     
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
    public function examen_agrupador()
    {
        return $this->belongsTo('Sis_medico\Examen_Agrupador','id_agrupador_publico');
    }
    public function examen_parametro()
    {
        return $this->belongsTo('Sis_medico\Examen_Parametro','id_parametro');
    }




}