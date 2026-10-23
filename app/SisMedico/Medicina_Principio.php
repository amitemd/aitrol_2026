<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Medicina_Principio extends Model
{
    protected $table = 'medicina_principio';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function medicina()
    {
        return $this->belongsTo('Sis_medico\Medicina','id_medicina');
    }
    public function generico()
    {
        return $this->belongsTo('Sis_medico\Principio_Activo','id_principio_activo');
    }
}
