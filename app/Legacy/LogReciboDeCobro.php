<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class LogReciboDeCobro extends Model
{
    /**
     * The table aaaaassociated with the model.
     *
     * @var string
     */
    protected $table = 'log_recibo_cobro';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function usercrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }


    

}
 