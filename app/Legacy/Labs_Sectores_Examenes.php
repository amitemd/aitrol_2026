<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Labs_Sectores_Examenes extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_sectores_examenes';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function examen()
    {
        return $this->belongsTo('Sis_medico\Examen', 'id_examen');
    }
    

}