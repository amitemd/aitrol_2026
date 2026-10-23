<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Ho_Form008 extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ho_form008';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function agenda()
    {
        return $this->belongsTo('Sis_medico\Agenda','id_agenda');
    }

    public function accidentes()
    {
        return $this->hasMany('Sis_medico\Ho_Form008_acc_vio_int','id_ho_form008');
    }
 
    public function examen_fisico()
    {
        return $this->hasMany('Sis_medico\Ho_form008_examen_fisico','id_ho_form008');
    }
    public function embarazo()
    {
        return $this->hasOne('Sis_medico\Ho_form008_embarazo','id_ho_form008');
    }
    public function diagnostico()
    {
        return $this->hasMany('Sis_medico\Ho_form008_diagnostico','id_ho_form008');
    }

   

}