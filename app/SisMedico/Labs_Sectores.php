<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Labs_Sectores extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'labs_sectores';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function sector_examenes()
    {
        return $this->hasMany('Sis_medico\Labs_Sectores_Examenes', 'id_sector');
    }
    

}