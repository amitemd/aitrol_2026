<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Procedimiento extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'procedimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function grupo_procedimiento()
    {
        return $this->belongsTo('Sis_medico\grupo_procedimiento', 'id_grupo_procedimiento');
    }
}
