<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Cama extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cama';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function habitacion()
    {
        return $this->belongsTo('Sis_medico\Habitacion','id_habitacion');
    }
    public function transaccion()
    {
        return $this->hasMany('Sis_medico\CamaTransaccion','id_cama');
    }
    public function camapaciente()
    {
        return $this->hasMany('Sis_medico\CamaPaciente','id_cama');
    }

}
 