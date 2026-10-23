<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Procesado_Excel_Proforma_Cab extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'procesado_excel_proforma_cab';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles(){
        return $this->hasMany('Sis_medico\Procesado_Excel_Proforma_Det','id_procesado_excel','id');
    }

  
}
