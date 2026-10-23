<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Log_agenda extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_agenda';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
}
