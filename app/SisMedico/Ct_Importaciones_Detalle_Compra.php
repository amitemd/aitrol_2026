<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Importaciones_Detalle_Compra extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_importaciones_detalle_compra';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function gasto()
    {
        return $this->belongsTo('Sis_medico\Ct_Imp_Gastos','id_gasto');
    }

    public function producto(){
    return $this->belongsTo('Sis_medico\Ct_productos','codigo','codigo');
    }


    public function union_bodega(){
        return $this->belongsTo('Sis_medico\Union_Bodega', 'bodega', 'id_bodega_cont');
    }

    
  


}
 
 