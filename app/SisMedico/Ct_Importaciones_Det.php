<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Importaciones_Det extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_importaciones_det';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function productos()
    {
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_producto','id');
    }

    


}
 
 