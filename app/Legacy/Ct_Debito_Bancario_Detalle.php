<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Debito_Bancario_Detalle extends Model
{
    //
    protected $table = 'ct_debito_bancario_detalle';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Debito_Bancario','id_debito');
    }
    public function compras()
    {
        return $this->belongsTo('Sis_medico\Ct_compras','id_compra');
    }
}
