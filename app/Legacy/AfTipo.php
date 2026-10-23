<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AfTipo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'af_tipo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function cuenta_mayor()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas', 'cuentamayor');
    }

    public function cuenta_gastos()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas', 'cuentagastos');
    }

    public function cuenta_depreciacion()
    {
        return $this->belongsTo('Sis_medico\Plan_Cuentas', 'cuantadepreciacion');
    }

    
}