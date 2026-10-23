<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Aud_Hc_Child_Pugh extends Model
{
    protected $table = 'aud_hc_child_pugh';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function evolucion()
    {
        return $this->belongsTo('Sis_medico\Hc_Evolucion', 'id_hc_evolucion');
    }
}
 