<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Importaciones_Compras extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_importaciones_compras';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Importaciones_Detalle_Compra', 'id_ct_compras');
    }

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function proveedor_da(){
        
        return $this->belongsTo('Sis_medico\Proveedor', 'proveedor');
    }

}
 
 