<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_detalle_retenciones extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_detalle_retenciones';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function porcentajer(){
        
        return $this->belongsTo('Sis_medico\Ct_Porcentaje_Retenciones', 'id_porcentaje');
    }
    public function cabecera(){
        return $this->belongsTo('Sis_medico\Ct_Retenciones', 'id_retenciones');
    }

    public static function getDetalles($id)
    {
        return Ct_detalle_retenciones::where('id_retenciones', $id)->get();
    }
}

