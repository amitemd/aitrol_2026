<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Labs_Grupo_Familiar extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_grupo_familiar';

    protected $keyType = 'string';

    public $incrementing = false;

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}