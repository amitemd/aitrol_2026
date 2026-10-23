<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Debito_Bancario extends Model
{
    //
    protected $table = 'ct_debito_bancario';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function acreedor()
    {
        return $this->belongsTo('Sis_medico\Proveedor','id_acreedor')->with('acreedor');
        
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Debito_Bancario_Detalle','id_debito');
        
    }

    public function banco()
    {
        return $this->belongsTo('Sis_medico\Ct_caja_banco','id_caja_banco');
        
    }
    public function proveedor()
    {
        return $this->belongsTo('Sis_medico\Proveedor','id_acreedor');
        
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
        
    }
    public function usuariomod(){
        return $this->belongsTo('Sis_medico\User','id_usuariomod');
    }

}
