<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Prestamos_Utilidades extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_prestamos_utilidades';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','id_usuario');
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
        
    }

}