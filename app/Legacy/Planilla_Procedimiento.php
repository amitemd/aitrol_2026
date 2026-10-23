<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Planilla_Procedimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planilla_procedimiento';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function procedimientos()
    {
        return $this->belongsTo('Sis_medico\Procedimiento', 'id_procedimiento');
    }
  
}