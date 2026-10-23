<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CruceValores extends Model
{
    public static $modelo = 'Cruce Valores';
    protected $table = 'ct_cruce_valores';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public function cabecera()
    {
        return $this->belongsTo('Sis_medico\v3\contable\CruceValores','id_comprobante');
    }

    public function detalleCruce()
    {
        return $this->hasMany('Sis_medico\v3\contable\DetalleCruce','id_comprobante',"id");
    }

    public function detallePagoCruceProv(){
        return $this->hasMany('Sis_medico\v3\contable\DetallePagoCruceProv','id_comprobante',"id")->where("estado",1);
    }
    
}
