<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AfDepreciacionDetalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'af_depreciacion_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function activo()
    {
        return $this->belongsTo('Sis_medico\AfActivo', 'activo_id', 'id');
    }
    
}