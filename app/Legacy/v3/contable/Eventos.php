<?php

namespace Sis_medico\v3\contable;

use Carbon\Carbon;
use Exception;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Notificacion;

class Eventos extends Model
{
    private static $modelo = 'Eventos';
    protected $table = 'new_db_eventos';
    protected $primaryKey  = 'id_evento';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function verNotificacionesGenerales()
    {
        $html = '';


        $eventos = Eventos::where('estado_evento', 1)
            ->where('id_usuariocrea', Auth::user()->id)
            ->get();



        if (count($eventos) > 0) {
            foreach ($eventos as $evento) {
                $diaNotificar = date("d-m-Y", strtotime($evento->desde_evento . "- $evento->selectalert days"));

                if ($diaNotificar >= date('Y-m-d')) {

                    $html .= ' <button type="button" class="dropdown-item abrir-modal" onclick="modalNotificaciones(\'' . $evento->id_evento . '\')">
            <i class="fas fa-envelope"></i> &nbsp;' . $evento->nombre_evento;


                    $fechainIEventoObj = new DateTime($evento->hasta_evento);
                    $fechaActualObj = new DateTime();
                    $diferencia = $fechaActualObj->diff($fechainIEventoObj);

                    $html .= '<span style="font-size: 8px;" class="float-right text-muted text-sm">';
                    if ($diferencia->d >= 1) {

                        $html .= $diferencia->d . ' dias';
                    } else {
                        $html .= $diferencia->h . ' : ' . $diferencia->i . ' horass';
                    }
                    $html .= '</span>
        </button>';
                }
            }
        }

        return $html;
    }

    public static function revisarNotificacion($r)
    {
        $count = 0;
        $html = '';
        $revisar = Eventos::where('estado_evento', 1)->where('id_usuariocrea', Auth::user()->id)->get();
        foreach ($revisar as $row) {
            $diaNotificar = date("d-m-Y", strtotime($row->desde_evento . "- $row->selectalert days"));



            if ($diaNotificar >= date('Y-m-d')) {

                $count++;
            }
        }

        if ($revisar->count() > 0) {
            $html = '<i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge">' . $count . '</span>';
        }
        return $html;
    }



    public static function verificarProximoCaducar($evento)
    {

        $fechaActualObj = new DateTime();
        $fechainIEventoObj = new DateTime($evento->desde_evento);
        $diferencia = $fechaActualObj->diff($fechainIEventoObj);

        if ($diferencia->d == 0) {

            $horas = $diferencia->h;
            $minutos = $diferencia->i;


            return 'El evento ' . $evento->nombre_evento . ' está por Iniciar en ' . $horas . ' horas y ' . $minutos . ' minutos.';
        }


        if ($diferencia->d >= 1 && $diferencia->d <= 2) {


            $horas = $diferencia->h;

            return 'El evento ' . $evento->nombre_evento . ' está próximo a Iniciar en ' . $diferencia->d . ' días y ' . $horas . ' horas';
        }


        return null;
    }



    public static function verNotificacion($r)
    {
        $html = '';
        $notificacion = Eventos::find($r->id);
        //dd($notificacion);
        $alertaProximoCaducar = self::verificarProximoCaducar($notificacion);
        if ($notificacion != '') {
            $html .= '<div class="row">
        <div class="col-12 col-sm-12">
            <div class="form-group">
                <label>Nombre del evento</label>
                <div class="input-group mb-3">
                    <input type="text" id="nombre_evento2" name="nombre_evento2" class="form-control" placeholder="Nombre del evento a ingresar" value="' . $notificacion->nombre_evento . '">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>Desde</label>
            <div class="input-group mb-3">
                <input type="date" id="desde_evento2" name="desde_evento2" class="form-control text-right" placeholder="dd/mm/aaaa" value="' . $notificacion->desde_evento . '">
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <label>Hasta</label>
            <div class="input-group mb-3">
                <input type="date" id="hasta_evento2" name="hasta_evento2" class="form-control text-right" placeholder="dd/mm/aaaa" value="' . $notificacion->hasta_evento . '">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-12">
        <div class="form-group">
            <label>Descripción</label>
            <div class="input-group mb-3">
                <input type="text" id="descripcion_evento2" name="descripcion_evento2" class="form-control"  value="' . $notificacion->descripcion_evento . '">
            </div>
        </div>
    </div>
</div>
    ';


            if ($alertaProximoCaducar) {
                $html .= '<div class="alert alert-warning">' . $alertaProximoCaducar . '</div>';
            }
        }

        return $html;
    }



    public static function getCounNotificaciones()
    {

        $count = 0;
        $r = self::getdatosNotificacion();



        foreach ($r as $row) {
            $diaNotificar = date("d-m-Y", strtotime($row->desde_evento . "- $row->selectalert days"));



            if ($diaNotificar >= date('Y-m-d')) {

                $count++;
            }
        }
        //dd($count);



        return $count;
    }

    public static function getdatosNotificacion()
    {

        $count = 0;

        $eventos = Eventos::where('estado_evento', 1)
            ->where('id_usuariocrea', Auth::user()->id)
            ->get();


        foreach ($eventos as $row) {
            $diaNotificar = date("d-m-Y", strtotime($row->desde_evento . "- $row->selectalert days"));



            if ($diaNotificar >= date('Y-m-d')) {

                $count++;
            }
        }
        //dd($count);

        return $eventos;
    }



    public static function modificarEvento($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            $id = $data['id_evento'];
            unset($data['id_evento']);
            $data['id_usuariomod'] = Auth::user()->id;
            $data['ip_modificacion'] = $ip;
            Eventos::where('id_evento', $id)->update($data);
            $r->c = 'Utilidades';
            $r->s = 'saveEvento';
            $r->d = $origin['d'];
            $r->m = Eventos::$modelo;
            $r->o = 'Se actualizo el evento con id.: ' . $id;
            $cont++;
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Utilidades';
            $r->s = 'saveEvento';
            $r->d = $origin['d'];
            $r->m = Eventos::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function listarEventos()
    {
        return Eventos::where('estado_evento', '!=', 0)
            ->get([
                'id_evento as id',
                'nombre_evento as title',
                'descripcion_evento as descripcion',
                DB::Raw('CONCAT(desde_evento,IFNULL(CONCAT(" ",hora_desde_evento),"")) as start'),
                DB::Raw('CONCAT(hasta_evento,IFNULL(CONCAT(" ",hora_hasta_evento),"")) as end'),
                'color_fondo_evento as backgroundColor',
                'color_texto_evento as textColor',
                DB::Raw('IFNULL(url_evento,"") as url'),
                'allDay'
            ]);
    }
    public static function saveEvento($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);
            $ip = Utilidades::getRealIP();
            if ($data['id_evento'] == '') {
                $data['id_usuariocrea'] = Auth::user()->id;
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_creacion'] = $ip;
                $data['ip_modificacion'] = $ip;
                $id = Eventos::insertGetId($data);
                $r->c = 'Utilidades';
                $r->s = 'saveEvento';
                $r->d = $origin['d'];
                $r->m = Eventos::$modelo;
                $r->o = 'Se creo el evento con id.: ' . $id;
                $cont++;
            } else {
                $id = $data['id_evento'];
                unset($data['id_evento']);
                $data['id_usuariomod'] = Auth::user()->id;
                $data['ip_modificacion'] = $ip;
                Eventos::where('id_evento', $id)->update($data);
                $r->c = 'Utilidades';
                $r->s = 'saveEvento';
                $r->d = $origin['d'];
                $r->m = Eventos::$modelo;
                $r->o = 'Se actualizo el evento con id.: ' . $id;
                $cont++;
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Utilidades';
            $r->s = 'saveEvento';
            $r->d = $origin['d'];
            $r->m = Eventos::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
                //'reload' => '',
            ];
            return json_encode($arrayRespuesta);
        }
    }
}
