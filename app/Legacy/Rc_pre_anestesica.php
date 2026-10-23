<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Rc_pre_anestesica extends Model
{
    protected $table = 'rc_pre_anestesica';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function anestesiologia()
    {
        return $this->belongsTo('Sis_medico\Hc_Anestesiologia', 'id_hc_anestesiologia');
    }
   
}