<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Nota_entrega extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nota_entrega';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function historia()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica','id_hc');
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Nota_entrega_detalle', 'id_nota_entrega');
    }

    public function logs_nota()
    {
        return $this->hasMany('Sis_medico\Log_nota_entrega', 'id_nota_entrega');
    }
}
 