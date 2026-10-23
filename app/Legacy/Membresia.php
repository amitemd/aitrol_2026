<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class Membresia extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'membresia';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\MembresiaDetalle','membresia_id');
    }
}