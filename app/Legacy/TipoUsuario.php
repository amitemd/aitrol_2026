<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class tipousuario extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipousuario';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
}
