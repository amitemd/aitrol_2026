<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class hc_imagenes_protocolo extends Model
{
    protected $table = 'hc_imagenes_protocolo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function protocolo()
    {
        return $this->belongsTo('Sis_medico\hc_protocolo', 'id_hc_protocolo');
    }

    


 }   
 