<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hospital_Proovedor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_proovedor';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function proovedortipo()
    {
        return $this->belongsTo('Sis_medico\Hospital_Tipo_Proovedor','id_tipoproovedor');
    }

}
 