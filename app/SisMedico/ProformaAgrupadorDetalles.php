<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ProformaAgrupadordetalles extends Model
{
    /**
     * The table aaaaassociated with the model.
     *
     * @var string
     */
    protected $table = 'proforma_agrupador_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
 
    public function producto()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_producto');
    }

    

}
 