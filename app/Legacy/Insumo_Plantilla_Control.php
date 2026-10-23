<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Insumo_Plantilla_Control extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'insumo_plantilla_control';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Insumo_Plantilla_Item_Control', 'id_plantilla');
    }

    public function planilla_procedimientos()
    {
        return $this->hasMany('Sis_medico\Planilla_Procedimiento', 'id_planilla');
    }

}

