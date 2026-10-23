<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Arqueo_Caja extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_arqueo_caja';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Ct_Arqueo_Caja_Detalle','id_arqueo_caja');   
    }

    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User','id_usuario');
    }
    
 
    

}