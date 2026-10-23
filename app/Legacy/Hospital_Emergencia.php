<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hospital_Emergencia extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_emergencia';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
     public function emergencia ()
    {
        return $this->belongsTo('Sis_medico\users','id_user');

    }
     public function examen ()
    {
        return $this->hasmany('Sis_medico\Examen','id_examen');

    }
     public function usuario ()
    {
        return $this->hasmany('Sis_medico\Firma_Usuario','id_nombre');

    }
    
    


       
    

}
 