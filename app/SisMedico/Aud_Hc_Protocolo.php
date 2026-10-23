<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Aud_Hc_Protocolo extends Model
{
    protected $table = 'aud_hc_protocolo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\hc_procedimientos','id_hc_procedimientos');
    }
    
    public function historiaclinica()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica','hcid');
    }

    public function hc_imagenes_protocolo()
    {
        return $this->belongsTo('Sis_medico\hc_imagenes_protocolo','id', 'id_hc_protocolo');
    }

    public function usuario_anestesiologo()
    {
        return $this->belongsTo('Sis_medico\User', 'id_anestesiologo');
    }
   
     public function auditoria_procedimiento()
    {
        return $this->belongsTo('Sis_medico\Aud_Hc_Procedimientos','id_hc_procedimientos','id_procedimientos_org');
    }
}
 