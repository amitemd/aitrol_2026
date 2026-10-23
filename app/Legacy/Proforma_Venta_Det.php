<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Proforma_Venta_Det extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proforma_venta_det';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function inv_serie () {
       return $this->belongsTo('Sis_medico\InvInventarioSerie', 'id_inv_serie'); 
    }
    public function nombre () {
        return $this->belongsTo('Sis_medico\InvInventarioSerie', 'id_inv_serie'); 
     }

   public function proforma_venta_cab () {
      return $this->belongsTo('Sis_medico\Proforma_Venta_Cab', 'id_proforma');
   }
   
   public function producto(){
      return $this->belongsTo('Sis_medico\Producto', 'id_producto');
   }

}