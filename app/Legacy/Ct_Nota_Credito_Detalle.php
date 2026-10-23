<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Nota_Credito_Detalle extends Model
{
    //
    protected $table = 'ct_nota_credito_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Nota_Credito', 'id_nota_credito');
    }
}
