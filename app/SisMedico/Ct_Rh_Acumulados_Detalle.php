<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Rh_Acumulados_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_rh_acumulados_detalle';

    /**
     * The attributes that aren't mass assignable.
     * 
     *
     * @var array
     */
    protected $guarded = [];
    public function usuario_crea(){
        
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }




}
