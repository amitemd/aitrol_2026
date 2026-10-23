<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Credito_Clientes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_credito_clientes';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Nota_Credito_Clientes','id_not_cred','id');
    }
    
}
