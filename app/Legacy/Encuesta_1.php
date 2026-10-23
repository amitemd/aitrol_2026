<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Encuesta_1 extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encuesta_1';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function complementos()
        {
            return $this->hasMany('Sis_medico\Encuesta_Complemento','id_encuesta_1', 'id');
        }
}