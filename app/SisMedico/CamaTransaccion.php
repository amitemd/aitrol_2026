<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class CamaTransaccion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cama_transaccion';

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
    public function imgh(){
        return $this->belongsTo('Sis_medico\Imagen', 'id_imagen');
    }

    public function cama(){
        return $this->belongsTo('Sis_medico\Cama', 'id_cama');
    }

   
}
 