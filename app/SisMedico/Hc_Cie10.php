<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hc_Cie10 extends Model
{
    //
    protected $table = 'hc_cie10';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];
    /*public function sala()
    {
        return $this->belongsTo('Sis_medico\Sala', 'id_sala');
    }*/
    public function cie_10_3()
    {
        return $this->belongsTo('Sis_medico\Cie_10_3', 'cie10');
    }
    public function cie_10_4()
    {
        return $this->belongsTo('Sis_medico\Cie_10_4', 'cie10');
    }
    

}
  