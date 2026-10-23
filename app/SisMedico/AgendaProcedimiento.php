<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AgendaProcedimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agenda_procedimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\Procedimiento', 'id_procedimiento');
    }
}
