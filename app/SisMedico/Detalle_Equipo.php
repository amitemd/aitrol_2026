<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Detalle_Equipo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'detalle_limpieza';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function usado()
    {
        return $this->belongsTo('Sis_medico\Equipo', 'id_equipo');
    }
}
