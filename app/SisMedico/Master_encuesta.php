<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
class Master_encuesta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_encuesta';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function usuario_evento()
    {
        return $this->belongsTo('App\Usuario_Evento', 'id_usuario_evento');
    }
}