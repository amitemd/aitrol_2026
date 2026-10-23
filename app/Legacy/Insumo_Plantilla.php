<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Insumo_Plantilla extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'insumo_plantilla';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function detalles()
    {
        return $this->belongsTo('Sis_medico\Insumo_Plantilla_Item', 'id_plantilla');
    }

}