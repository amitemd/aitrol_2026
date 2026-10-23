<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Archivo_historico extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'archivo_historico';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    
    public function documentos()
    {
        return $this->hasMany('Sis_medico\Historiaclinica');
    }


    

}
