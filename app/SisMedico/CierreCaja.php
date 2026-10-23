<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class CierreCaja extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cierre_caja';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

     public function crea()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
}
