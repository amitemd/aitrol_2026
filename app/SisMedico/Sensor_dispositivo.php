<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Sensor_dispositivo extends Model
{
    protected $table = 'sensor_dispositivo';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];



    public function establecimiento()
    {
        return $this->belongsTo('Sis_medico\sensor_establecimiento', 'id_establecimiento');
    }

    
}