<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class AgendaDocumentoRequisito extends Model
{
    protected $table = 'agenda_documento_requisitos';

    protected $guarded = [];

    public function documento()
    {
        return $this->belongsTo('Sis_medico\\Documento', 'id_documento');
    }
}
