<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AfActivoEquipo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'af_activo_equipo';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function afActivo()
    {
        return $this->belongsTo('Sis_medico\AfActivo', 'id_activo');
    }
    public function afEquipo()
    {
        return $this->belongsTo('Sis_medico\Equipo', 'id_equipo');
    }
}
