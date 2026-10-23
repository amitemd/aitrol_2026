<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class hc_child_pugh extends Model
{
    protected $table = 'hc_child_pugh';

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
 