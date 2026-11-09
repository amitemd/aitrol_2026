<?php

namespace Sis_medico\v3\clinical;

use Illuminate\Database\Eloquent\Model;

class Order012Diagnosis extends Model
{
    protected $table = 'orden_012_cie10';
    protected $guarded = array();

    public function order()
    {
        return $this->belongsTo('Sis_medico\v3\clinical\Order012', 'id_orden_012');
    }
}
