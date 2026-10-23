<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Debito_Acreedores extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_debito_acreedores';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Ct_Debito_Acreedores', 'id_debito_acreedores');
    }

  
}