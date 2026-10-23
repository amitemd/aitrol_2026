<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ProformaAgrupador extends Model
{
    /**
     * The table aaaaassociated with the model.
     *
     * @var string
     */
    protected $table = 'proforma_agrupador';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function detalles()
    {
        return $this->hasMany('Sis_medico\ProformaAgrupadorDetalles','id_proforma_agrupador');
    }

}
 