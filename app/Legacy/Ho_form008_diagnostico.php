<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_form008_diagnostico extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_form008_diagnostico';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function cie_10_3()
    {
        return $this->belongsTo('Sis_medico\cie_10_3','id_cie_10_3');
    }
    public function cie_10_4()
    {
        return $this->belongsTo('Sis_medico\cie_10_4','id_cie_10_4');
    }

    

   

}