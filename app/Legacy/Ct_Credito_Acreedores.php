<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Credito_Acreedores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_credito_acreedores';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function detalle(){
        
        return $this->hasMany('Sis_medico\Ct_Detalle_Credito_Acreedores','id_debito', 'id');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    public function compra(){
        
        return $this->belongsTo('Sis_medico\Ct_compras','id_compra');
    }
    
}
