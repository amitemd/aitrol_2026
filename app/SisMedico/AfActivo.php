<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AfActivo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'af_activo';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function detalle_depreciacion()
    {
        return $this->hasMany('Sis_medico\AfDepreciacionDetalle', 'activo_id');
    }

    public function ultima_depreciacion()
    {
        return $this->hasMany('Sis_medico\AfDepreciacionDetalle', 'activo_id')->orderBy('id', 'desc')->first();
    }

    public function sub_tipo()
    {
        return $this->belongsTo('Sis_medico\AfSubTipo', 'subtipo_id');
    }

    public function tipo()
    {
        return $this->belongsTo('Sis_medico\AfTipo', 'tipo_id');
    }

    public function marca_activo()
    {
        return $this->belongsTo('Sis_medico\Marca', 'marca', 'id');
    }

    public function user_responsable()
    {
        return $this->belongsTo('Sis_medico\Ct_Nomina','responsable','id_user');
    }

    public function user_crea()
    {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

    public function accesorios()
    {
        return $this->hasMany('Sis_medico\AfActivo_Accesorios', 'id_activo', 'id');
    }
   
    
}