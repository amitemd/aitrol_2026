<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use DateTime;

class Ct_Cierre_Cabecera extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_cierre_cabecera';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    public function usuario_crea() {
        return $this->belongsTo('Sis_medico\User','id_usuariocrea');
    }

    public static function verificar_fecha($fecha)
    {
        $formato = 'Y-m-d';
        $d = DateTime::createFromFormat($formato, $fecha);
        if ($d && $d->format($formato) !== $fecha) {
            return abort(403, 'Fecha inválida');
        }

        $id_empresa = Session::get('id_empresa');
        $bandera = true;
        $fecha = Ct_Cierre_Cabecera::where('id_empresa', $id_empresa)
            ->where('fecha_cierre', '>=', $fecha . ' 00:00:00')
            ->where('estado', 1)
            ->first();
        if (!is_null($fecha)) {
            $bandera = false;
        }
        if ($bandera) {
            return json_encode(array('status' => "success", "message" => "fecha correcta", 'code' => "200"));
        } else {
            return json_encode(array('status' => "error", "message" => "No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.", 'code' => "500"));
        }
    }

    public static function ultima_fecha()
    {
        $fec = null;
        $id_empresa = Session::get('id_empresa');
        $fecha = Ct_Cierre_Cabecera::where('id_empresa', $id_empresa)
            ->where('estado', 1)
            ->orderBy('fecha_cierre', 'Desc')
            ->first();
        if ($fecha != '') {
            $fec = date('Y-m-d', strtotime($fecha->fecha_cierre));
        } else {
            $fec = null;
        }
        return $fec;
    }
}