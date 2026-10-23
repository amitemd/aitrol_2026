<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Aud_Hc_Epicrisis extends Model
{
    //
    protected $table = 'aud_hc_epicrisis';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    /*public function sala()
    {
        return $this->belongsTo('Sis_medico\Sala', 'id_sala');
    }*/
    

}
  