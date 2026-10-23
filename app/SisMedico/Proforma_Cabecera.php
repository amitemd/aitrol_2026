<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Proforma_Cabecera extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proforma_cabecera';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Proforma_Detalle','id_proforma');   
    }

    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente','id_paciente');
    }
    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro','id_seguro');
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
    public function agenda()
    {
        return $this->belongsTo('Sis_medico\Agenda', 'id_agenda');
    }
    public function datos_paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }
     public function usercrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usermod()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
    public function recibo()
    {
        return $this->belongsTo('Sis_medico\Ct_Orden_Venta', 'id_orden');
    }
   
}
