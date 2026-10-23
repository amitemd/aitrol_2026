<?php 

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Aud_Hc_Procedimiento_Final extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aud_hc_procedimiento_final';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function procedimiento()
    {
        return $this->belongsTo('Sis_medico\Procedimiento', 'id_procedimiento', 'id');
    }

}
