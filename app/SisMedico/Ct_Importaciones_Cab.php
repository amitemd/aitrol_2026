<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Importaciones_Cab extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_importaciones_cab';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Importaciones_Det', 'id_cab');
    }

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function proveedor_da(){
        
        return $this->belongsTo('Sis_medico\Proveedor', 'id_proveedor');
    }
    public function producto_imp(){
        
        return $this->belongsTo('Sis_medico\Ct_productos', 'id_producto');
    }

    public function cliente(){
        
        return $this->belongsTo('Sis_medico\Empresa', 'id_cliente');
    }

    public function paises(){
        return $this->belongsTo('Sis_medico\Pais', 'pais');
    }

    public function cruce(){
        return $this->hasMany('Sis_medico\Ct_Importaciones_Gasto_Cab', 'id_import_cabl')->where('tipo', 3);
    }
}
 
 