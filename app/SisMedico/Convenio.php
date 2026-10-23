<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'convenio';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    public function nivel()
    {
        return $this->belongsTo('Sis_medico\Nivel', 'id_nivel');
    }


    

}
