<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Medicina extends Model
{
    protected $table = 'medicina';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

     public function genericos()
    {
        return $this->hasMany('Sis_medico\Medicina_Principio','id_medicina');
    }
}
