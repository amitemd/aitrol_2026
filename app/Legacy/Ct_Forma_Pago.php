<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Forma_Pago extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_forma_pago';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Asientos_Detalle', 'id_asiento_cabecera');
    }

}
