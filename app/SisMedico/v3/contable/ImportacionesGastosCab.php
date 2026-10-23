<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class ImportacionesGastosCab extends Model
{
    protected $table = 'ct_importaciones_gasto_cab';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';

    public function compra()
    {
        return $this->belongsTo('Sis_medico\v3\contable\ImportacionesCompras', 'id_import_compra');
    }
}
