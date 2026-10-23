<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class hc_receta_detalle extends Model
{
    protected $table = 'hc_receta_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function hc_receta()
    {
        return $this->belongsTo('Sis_medico\hc_receta','id_hc_receta');
    }
    public function medicina()
    {
        return $this->belongsTo('Sis_medico\Medicina','id_medicina');
    }
}