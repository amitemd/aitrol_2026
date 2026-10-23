<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Examen_Orden extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'examen_orden';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }
    public function protocolo()
    {
        return $this->belongsTo('Sis_medico\Protocolo','id_protocolo');
    }
    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro','id_seguro');   
    }
    public function nivel()
    {
        return $this->belongsTo('Sis_medico\Nivel','id_nivel');   
    }
    public function forma_de_pago()
    {
        return $this->belongsTo('Sis_medico\Forma_de_pago','id_forma_de_pago');   
    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Examen_Detalle','id_examen_orden');   
    }
    public function resultados()
    {
        return $this->hasMany('Sis_medico\Examen_Resultado','id_orden');   
    }
    public function crea()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }
    public function modifica()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariomod');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa','id_empresa');
    }
    public function doctor()
    {
        return $this->belongsTo('Sis_medico\User','id_doctor_ieced');
    }

    public function detalle_forma_pago()
    {
        return $this->hasMany('Sis_medico\Examen_Detalle_Forma_Pago','id_examen_orden');   
    }

    public function toma_muestras()
    {
        return $this->hasMany('Sis_medico\Examen_Orden_Toma_Muestra','id_examen_orden');   
    }

    public function nombreasesor()
    {
        return $this->belongsTo('Sis_medico\User','asesor_venta');
    }
     public function doc_externo()
    {
        return $this->belongsTo('Sis_medico\Labs_doc_externos','codigo');   
    }
}
