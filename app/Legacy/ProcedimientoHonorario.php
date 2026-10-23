<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ProcedimientoHonorario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'procedimiento_honorario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function valor_nivel()
    {
        return $this->hasMany('Sis_medico\ProcedimientoHonorarioConvenio', 'id_proc_conv');
    }

    
}
 