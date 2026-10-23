<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Empresa extends model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empresa';
    protected $keyType = 'string';

    public $incrementing = false;

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function sucursales()
    {
        return $this->hasMany('Sis_medico\Ct_Sucursales', 'id_empresa', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Ct_Nomina','id_empresa');
    }

    public function notadebito()
    {
        return $this->belongsTo('Sis_medico\Nota_Debito','empresa');
    }

    public function usuario_contador()
    {
        return $this->belongsTo('Sis_medico\User','id_contador');
    }

    public function usuario_representante()
    {
        return $this->belongsTo('Sis_medico\User','id_representante');
    }

    public function pref_repre()
    {
        return $this->belongsTo('Sis_medico\Titulo_Profesional','pref_representante');
    }

    public function pref_cont()
    {
        return $this->belongsTo('Sis_medico\Titulo_Profesional','pref_contador');
    }
    public static function getEmpresa($id)
    {
        return Empresa::join('de_empresa as e', 'empresa.id', '=', 'e.id_empresa')
            ->where('empresa.id', $id)->first([
                'empresa.id',
                'razonsocial',
                'nombrecomercial',
                'direccion',
                'tipo_rimpe',
                'agente_retencion'
            ]);
    }
}
