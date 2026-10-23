<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospital';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];


    public function salas() {
        return $this->hasMany('Sis_medico\Sala');
        }

}
