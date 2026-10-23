<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Sugerencia extends Model
{
    protected $table = 'sugerencia';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function area()
    {
        return $this->belongsTo('Sis_medico\Area', 'id_area');
    }
    public function tiposugerencia()
    {
        return $this->belongsTo('Sis_medico\TipoSugerencia', 'id_tiposugerencia');
    }
}
 