<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Rh_Prestamos_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rh_prestamos_detalle';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function prestamos(){
        
        return $this->belongsTo('Sis_medico\Ct_Rh_Prestamos','id_ct_rh_prestamos');
    
    }


}
