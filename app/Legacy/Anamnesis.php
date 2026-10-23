<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Anamnesis extends Model
{
    protected $table = 'anamnesis';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function historiaclinica()
    {
        return $this->belongsTo('Sis_medico\Historiaclinica', 'hcid');
    }
}
