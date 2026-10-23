<?php

namespace Sis_medico;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class De_Estado_Sri extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_estado_sri';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

    public static function getEstado()
    {
        /*  $estado = '';
        try {
            $sri = De_Estado_Sri::get()->first();
            if(count($sri)==0){
                $arraySRI = [
                    'valor' => 1,
                    'estado' => 1,
                    'id_usuariomod' => 'FACELECTRO',
                    'id_usuariocrea' => 'FACELECTRO',
                    'ip_creacion' => '::1',
                    'ip_modificacion' => '::1',
                ];
                De_Estado_Sri::insertGetId($arraySRI);
                $estado = 1;
            }
            else{
            $estado = $sri->valor;
            }
        } catch (Exception $e) {
            $estado = '2';
        }
        return $estado;*/
        $estado = '';
        try {
            $sri = De_Estado_Sri::all();
            if(count($sri)==0){
                $arraySRI = [
                    'valor' => 1,
                    'estado' => 1,
                    'id_usuariomod' => 'FACELECTRO',
                    'id_usuariocrea' => 'FACELECTRO',
                    'ip_creacion' => '::1',
                    'ip_modificacion' => '::1',
                ];
                De_Estado_Sri::insert($arraySRI);
                $estado = 1;
            }
            else{
                $estado = $sri->valor;
            }
        } catch (Exception $e) {
            $estado = '2';
        }
        return $estado;
    }

    public static function updateEstado($estado)
    {
        $arrayDoc = [
            'valor' => $estado
        ];
        De_Estado_Sri::where('id',1)->update($arrayDoc);
    }
}
