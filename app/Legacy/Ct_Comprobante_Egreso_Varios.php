<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Comprobante_Egreso_Varios extends Model
{
    /** 
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_comprobante_egreso_varios';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function banco()
    {
        return $this->belongsTo('Sis_medico\Ct_Caja_Banco', 'id_caja_banco');
    }
    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    public function detalles(){
        return $this->hasMany('Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios', 'id_comprobante_varios');
    }
    public function proveedor(){
        return $this->belongsTo('Sis_medico\Proveedor','id_proveedor');
    }
    

}
