<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Seguro extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seguros';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function tipo_seguro()
    {
        return $this->belongsTo('Sis_medico\SeguroTipos', 'id_seguro_tipos');
    }
}
