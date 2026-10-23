<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class TipoHonorario extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_honorario';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

   
    
}