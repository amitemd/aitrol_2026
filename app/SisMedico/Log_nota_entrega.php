<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Log_nota_entrega extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_nota_entrega';

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
 