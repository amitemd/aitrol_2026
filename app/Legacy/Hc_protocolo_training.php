<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc_protocolo_training extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hc_protocolo_training';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

     public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_training');
    }
}
 