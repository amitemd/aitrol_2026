<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Control_Equipo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'control_equipo';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

}
