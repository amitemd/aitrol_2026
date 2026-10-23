<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class complaint extends Model
{
    protected $table = 'complaint';

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
 