<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Nota_Debito_Cliente extends Model
{
    //
    protected $table = 'ct_debito_clientes';
    protected $guarded = [];
    public function cliente(){
        
        return $this->belongsTo('Sis_medico\Ct_Clientes','id_cliente', 'identificacion');
    }
    public function venta(){
        
        return $this->belongsTo('Sis_medico\Ct_ventas','id_factura', 'id');
    }
    public function detalle(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Debito_Clientes','id_debito', 'id');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    
}
