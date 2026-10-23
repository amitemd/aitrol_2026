<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ct_Nota_Credito extends Model
{
    //
    protected $table = 'ct_nota_credito_cabecera';

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

}
