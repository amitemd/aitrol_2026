<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ProductosTarifario extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_producto_tarifario';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro');
    }

    public function producto()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_producto');
    }

    public function fxnivel()
    {
        return $this->belongsTo('Sis_medico\Nivel', 'nivel');
    }
}
