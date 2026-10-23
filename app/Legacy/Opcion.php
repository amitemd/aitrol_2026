<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'opcion';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
 