<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use DB;

class MaestroDeCodigo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'maestro_de_codigo';
    protected $keyType = 'string';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    

   
}
