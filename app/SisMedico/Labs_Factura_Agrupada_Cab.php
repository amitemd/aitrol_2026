<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Labs_Factura_Agrupada_Cab extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_factura_agrupada_cab';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Labs_Factura_Agrupada_Detalle','id_agrup_cab');   
    }

}