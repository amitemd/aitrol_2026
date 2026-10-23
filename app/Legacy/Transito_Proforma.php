<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Transito_Proforma extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transito_proforma';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function proforma()
    
    {
        return $this->belongsTo('Sis_medico\Proforma_Venta_Cab', 'id_proforma', 'id');
    }
}
