<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Observacion_General extends Model
{
    //
    protected $table = 'observacion_general';

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

    public function usuario_modifica()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariomod');
    }
    
    
}
