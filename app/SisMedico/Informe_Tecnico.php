<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use DB;

class Informe_Tecnico extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'informe_tecnico';
    protected $keyType = 'string';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function maestro_codigo()
    {
        return $this->belongsTo('Sis_medico\MaestroDeCodigo','id_maestro_codigo');
    }
    

   
}
