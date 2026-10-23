<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class InsumosUtilizadosArea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'insumos_utilizados_area';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function insumos_limpieza()
    {
        return $this->belongsTo('Sis_medico\Mantenimientos_Insumos_Limpieza', 'id_insumos');
    }

}
