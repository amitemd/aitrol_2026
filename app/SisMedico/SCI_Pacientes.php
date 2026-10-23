<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class SCI_Pacientes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sci_pacientes';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
 
 