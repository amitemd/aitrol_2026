<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bodega';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    public function cliente_nombre()
    {
        return $this->belongsTo('Sis_medico\Ct_Clientes', 'id_cliente');
    }
}
