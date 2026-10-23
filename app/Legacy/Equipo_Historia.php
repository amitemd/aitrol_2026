<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Equipo_Historia extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipo_historia';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function historia()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'hcid');
    }
    public function equipo()
    {
        return $this->belongsTo('Sis_medico\Equipo', 'id_equipo');
    }
    public function usuario_crea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

}
