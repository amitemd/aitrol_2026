<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class Hospital_Salas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hospitalsalas';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public function scopeArea_salas($query,$area_salas){
     if($area_salas)

        return $query->where('area_salas','LIKE',"%$area_salas%");
    }
    public function scopeMedicina_salas($query,$medicina_salas){
     if($medicina_salas)

    return $query->where('medicina_salas','LIKE',"%$medicina_salas%");
    }

}
 