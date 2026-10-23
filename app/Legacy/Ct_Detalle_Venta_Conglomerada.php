<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Detalle_Venta_Conglomerada extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_venta_c';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function paciente(){
        return $this->belongsTo('\Sis_medico\Paciente', "id_paciente");
    }

    public function cliente(){
        return $this->belongsTo('\Sis_medico\Ct_Clientes', "id_paciente");
    }
    
}
