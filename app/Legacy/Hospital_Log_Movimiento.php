<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hospital_Log_Movimiento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital_log_movimiento';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    /**
    public function agenda()   
    {
        return $this->belongsTo('Sis_medico\Agenda', 'id_agenda');
    }
    public function callcenter()   
    {
        return $this->belongsTo('Sis_medico\CallCenter', 'id_callcenter');
    }**/
    public function producto(){

        return $this->belongsTo('Sis_medico\Hospital_Producto', 'id_producto');
    }
    public function movimiento(){

        return $this->belongsTo('Sis_medico\Hospital_Movimiento','id_movimiento');
    }



}
 