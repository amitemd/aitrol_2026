<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class ControlAmbiente extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'registro_ambiente';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */

    public function usuario(){
        
        return $this->belongsTo('Sis_medico\User','responsable');
    }
    protected $guarded = [];
}