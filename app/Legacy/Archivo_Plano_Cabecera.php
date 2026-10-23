<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Archivo_Plano_Cabecera extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'archivo_plano_cabecera';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function paciente()
    {
        return $this->belongsTo('Sis_medico\Paciente', 'id_paciente');
    }
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuario');
    }

    public function usuario_crear()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuario_modifica()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod');
    }
    public function tiposeguro()
    {
        return $this->belongsTo('Sis_medico\Tipo_Seguro', 'id_tipo_seguro');
    }

    public function detalles()
    {
        return $this->hasMany('Sis_medico\Archivo_Plano_Detalle','id_ap_cabecera');   
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }

    public function seguro()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro');
    }

    public function seguro_privado()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_seguro_priv');
    }

    public function cob_compartida()
    {
        return $this->belongsTo('Sis_medico\Seguro', 'id_cobertura_comp');
    }

}
