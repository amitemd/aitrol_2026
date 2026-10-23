<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class DoctorDocumentos extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctor_documentos';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function estadoDoc()
    {
        return $this->belongsTo('Sis_medico\DoctorEstadosDocs','estado');
    }

}
