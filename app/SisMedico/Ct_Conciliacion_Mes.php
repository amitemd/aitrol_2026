<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Conciliacion_Mes extends Model
{
    //
    protected $table = 'ct_conciliacion_mes';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function caja_banco()
    {
        return $this->belongsTo('Sis_medico\Ct_Caja_Banco', 'id_caja_banco');
    }

    public function nombre()
    {
        return $this->belongsTo('Sis_medico\user', 'id_usuariocrea');
    }

    
}