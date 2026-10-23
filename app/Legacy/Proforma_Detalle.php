<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Proforma_Detalle extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proforma_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Proforma_Cabecera', 'id_proforma');
    }
    public function producto()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_producto');
    }

    
}
