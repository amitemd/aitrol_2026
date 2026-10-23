<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class EquipoUsado extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipo_procedimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function nombre()
    {
        return $this->belongsTo('Sis_medico\Procedimiento','id_procedimiento');
    }
}
