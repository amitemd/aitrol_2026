<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Ven_Orden extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_ven_orden';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function paciente()
    {

        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }
    public function detalles()
    {

        return $this->hasMany('Sis_medico\Ct_Ven_Orden_Detalle','id_ct_ven_orden');
    }
    public function orden_venta()
    {

        return $this->belongsTo('Sis_medico\Ct_Orden_Venta', 'orden_venta');
    }

}
