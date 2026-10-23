<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class FormatoDescargaInsumos extends Model
{
    protected $table = 'formato_descarga_insumos';
     
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Detalle_Formato_Descargo','id_formato');
    }
}
