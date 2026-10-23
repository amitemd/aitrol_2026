<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Orden_Tipo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orden_tipo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function orden_procedimiento()
    {
        return $this->hasMany('Sis_medico\Orden_Procedimiento','id_orden_tipo');   
    }

   
}
