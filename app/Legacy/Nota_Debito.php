<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Nota_Debito extends Model
{
    //

    protected $table = 'ct_nota_debito_cabecera';

    public function empresas()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'empresa');

    }

    public function banco()
    {
        return $this->belongsTo('Sis_medico\Ct_Caja_Banco', 'id_banco');

    }

    public function creador()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');

    }
    public function detalles()
    {
        return $this->hasMany('Sis_medico\Nota_Debito_Detalle','id_nota_debito');
        
    }

}
