<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Planilla_Detalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planilla_detalle';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\Planilla', 'id_planilla_cabecera');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function producto()
    {
        return $this->belongsTo('Sis_medico\Producto', 'codigo','codigo');
    }
    public function tmovimiento()
    {
        return $this->belongsTo('Sis_medico\Movimiento', 'movimiento');
    }
    public function bodega()
    {
        return $this->belongsTo('Sis_medico\Bodega', 'id_bodega'); 
    }
}
 