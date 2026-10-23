<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Proforma_Venta_Cab extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proforma_ventas_cab';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles(){
        return $this->hasMany('Sis_medico\Proforma_Venta_Det','id_proforma');   
    }

    public function cliente(){
        return $this->belongsTo('Sis_medico\Ct_Clientes','id_cliente');   
    }

    public function empresa(){
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');   
    }


    public function vendedor(){
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');   
    }

    public function vendedorMod(){
        return $this->belongsTo('Sis_medico\User','id_usuariomod');   
    }
    
    public function responsable(){
        return $this->belongsTo('Sis_medico\User','id_responsable');   
    }

    public function producto(){
        return $this->belongsTo('Sis_medico\Producto','id_producto');   
    }

    
}