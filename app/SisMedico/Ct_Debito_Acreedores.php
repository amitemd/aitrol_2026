<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Debito_Acreedores extends Model
{
    //
    protected $table = 'ct_debito_acreedores';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function proveedor()
    {
        return $this->belongsTo('Sis_medico\Proveedor','id_proveedor');
        
    }
    public function detalle(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Debito_Acreedores','id_debito_acreedores', 'id');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

}
