<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Arqueo_Caja_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_arqueo_caja_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function arqueo()
    {
        return $this->belongsTo('Sis_medico\Ct_Arqueo_Caja','id_arqueo_caja');
    }

}