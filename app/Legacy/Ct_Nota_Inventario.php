<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Nota_Inventario extends Model
{
    //
    protected $table = 'ct_nota_inventario';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function detalles(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Inventario','id_inventario');
    }
    public function rubros(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Rubro_Inventario','id_inventario');
    }

}
