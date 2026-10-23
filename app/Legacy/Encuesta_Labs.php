<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Encuesta_Labs extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encuesta_labs';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function complementos()
        {
            return $this->hasMany('Sis_medico\Encuesta_Complementolabs','id_encuesta_labs', 'id');
        }
}