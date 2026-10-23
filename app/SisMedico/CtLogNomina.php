<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class CtLogNomina extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_log_nomina';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function usuario_crea()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
}
