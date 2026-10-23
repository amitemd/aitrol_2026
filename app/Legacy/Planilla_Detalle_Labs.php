<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Planilla_Detalle_Labs extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planilla_detalle_labs';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function producto(){
       return $this->belongsTo('Sis_medico\Producto', 'id_producto');
    }

    public function usuario(){
       return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public function derivado(){
      return $this->belongsTo('Sis_medico\Examen_Derivado', 'id_examen_derivado');
    }

    public function bodega(){
      return $this->belongsTo('Sis_medico\Bodega', 'id_bodega');
    }

    public function cabecera(){
      return $this->belongsTo('Sis_medico\Planilla_Cabecera', 'id_planilla_cabecera');
    }
    
}
 