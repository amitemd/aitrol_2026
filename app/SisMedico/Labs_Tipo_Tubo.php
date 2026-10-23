<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Labs_Tipo_Tubo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_tipo_tubo';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
