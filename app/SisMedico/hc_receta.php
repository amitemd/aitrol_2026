<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class hc_receta extends Model
{
    protected $table = 'hc_receta';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function historia()
    {
        return $this->belongsTo('Sis_medico\historiaclinica','id_hc');
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\hc_receta_detalle','id_hc_receta', 'id');
    }
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor_examinador');
    }
}