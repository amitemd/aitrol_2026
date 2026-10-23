<?php

namespace Sis_medico\Http\Controllers\auditoria;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Response;
use Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Sis_medico\User;
use Sis_medico\Paciente;

class Caducidad_CodigoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        if (in_array($rolUsuario, array(5, 1, 2)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }



        $fecha = date("Y-m-d");
        $data_seis_meses  = date('Y-m-d', strtotime("+6 months"));
        $agenda_dias = date("Y-m-d", strtotime($fecha . "- 2 days"));

        $usuarios = DB::table("paciente as pa")
            ->join("agenda as ag", "pa.id", "ag.id_paciente")
            ->join("seguros", "ag.id_seguro", "seguros.id")
            ->where("ag.estado_cita", "!=", 4)
            ->where("seguros.tipo", 0)
            ->where("ag.estado", "!=", 0)
            ->whereNotNull('pa.fecha_val')
            ->whereNotNull('pa.cod_val')
            ->where("ag.fechaini", ">", $agenda_dias)
            ->whereBetween("pa.fecha_val", [$fecha, $data_seis_meses])
            ->select("ag.*", "pa.nombre1", "pa.apellido1", "seguros.nombre as nombreseguro", "pa.cod_val", "pa.fecha_val", "pa.id as id_importante")
            ->groupBy("ag.id_paciente")
            ->orderBy("ag.created_at")
            ->paginate(15, ['*'], 'codigo_valido');
        //how to create tr in table js

        $usuarios_sin_codigo = DB::table("paciente as pa")
            ->join("agenda as ag", "pa.id", "ag.id_paciente")
            ->join("seguros", "ag.id_seguro", "seguros.id")
            ->where("ag.estado_cita", "!=", 4)
            ->where("ag.estado", "!=", 0)
            ->where("seguros.tipo", 0)
            ->where('pa.fecha_val', null)
            ->where('pa.cod_val', null)
            ->where("ag.fechaini", ">", $agenda_dias)
            ->select("ag.*", "pa.nombre1", "pa.apellido1", "seguros.nombre as nombreseguro", "pa.cod_val", "pa.fecha_val", "pa.id as id_importante")
            ->groupBy("ag.id_paciente")
            ->orderBy("ag.created_at")
            ->paginate(15, ['*'], 'codigo_invalido');



        return view('auditoria.index', ['usuarios' => $usuarios, 'usuarios_sin_codigo' => $usuarios_sin_codigo]);
    }

    public function buscador(Request $request)
    {

        $paciente = Paciente::find($request->id);

        try {
            $paciente = Paciente::find($request['id']);
            return ['status' => true, 'array' => $paciente];
        } catch (\Throwable $th) {
            return ['status' => false, 'msj' => "Ocurrio un error"];
        }
    }

    public function actualizar(Request $request)
    {
        try {
            $paciente = Paciente::find($request['id'])->update(['cod_val' => $request['codigo'], 'fecha_val' => $request['fecha_vali']]);
            return ['status' => true, 'array' => $paciente];
        } catch (\Throwable $th) {
            return ['status' => false, 'msj' => "Ocurrio un error"];
        }
    }

    public function buscadorGeneral($fecha_val, $codigo_val, $agenda_dias, $fecha, $data_seis_meses, $cedula, $nombres)
    {


        $usuarios = DB::table("paciente as pa")
            ->join("agenda as ag", "pa.id", "ag.id_paciente")
            ->join("seguros", "ag.id_seguro", "seguros.id")
            ->where("ag.estado_cita", "!=", 4)
            ->where("seguros.tipo", 0)
            ->where("ag.estado", "!=", 0);
        if ($fecha_val === null and $codigo_val === null) {

            $usuarios = $usuarios->where('pa.fecha_val', null);
            $usuarios = $usuarios->where('pa.fecha_val', null);
            $usuarios = $usuarios->where("ag.fechaini", ">", $agenda_dias);
        } else {

            $usuarios = $usuarios->whereNotNull('pa.fecha_val');
            $usuarios = $usuarios->whereNotNull('pa.cod_val');
            $usuarios = $usuarios->where("ag.fechaini", ">", $agenda_dias);
            $usuarios = $usuarios->whereBetween("pa.fecha_val", [$fecha, $data_seis_meses]);
        }

        if (!is_null($nombres)) {

            $usuarios = $usuarios->where(function ($jq1) use ($nombres) {
                $jq1->orwhereraw('CONCAT(pa.apellido1," ",pa.apellido2," ",pa.nombre1," ",pa.nombre2) LIKE ?', ['%' . $nombres . '%'])
                    ->orwhereraw('CONCAT(pa.apellido1," ",pa.apellido2," ",pa.nombre1) LIKE ?', ['%' . $nombres . '%'])
                    ->orwhereraw('CONCAT(pa.nombre1," ",pa.apellido1," ",pa.apellido2) LIKE ?', ['%' . $nombres . '%'])
                    ->orwhereraw('CONCAT(pa.nombre1," ",pa.nombre2," ",pa.apellido1) LIKE ?', ['%' . $nombres . '%']);
            });
        }

        if (!is_null($cedula)) {

            $usuarios = $usuarios->where('pa.id', $cedula);
        }

        $usuarios = $usuarios->select("ag.*", "pa.nombre1", "pa.apellido1", "seguros.nombre as nombreseguro", "pa.cod_val", "pa.fecha_val", "pa.id as id_importante");
        $usuarios = $usuarios->groupBy("ag.id_paciente");
        $usuarios = $usuarios->orderBy("ag.created_at");
        $usuarios = $usuarios->paginate(15, ['*'], 'codigo_valido');
        return $usuarios;
    }


    public function buscar_codigo(Request $request)
    {

        $fecha = date("Y-m-d");
        $data_seis_meses  = date('Y-m-d', strtotime("+6 months"));
        $agenda_dias = date("Y-m-d", strtotime($fecha . "- 2 days"));
   
        $usuarios_busqueda = $this->buscadorGeneral(1, 1, $agenda_dias, $fecha, $data_seis_meses, $request['cedula'], $request['nombres']);
        $usuarios_sin_codigo = $this->buscadorGeneral(null, null, $agenda_dias, $fecha, $data_seis_meses, null, null);
        return view('auditoria.index', ['usuarios' => $usuarios_busqueda, 'usuarios_sin_codigo' => $usuarios_sin_codigo]);
    }

    public function buscar_codigo_no_valido(Request $request)
    {

        $fecha = date("Y-m-d");
        $data_seis_meses  = date('Y-m-d', strtotime("+6 months"));
        $agenda_dias = date("Y-m-d", strtotime($fecha . "- 2 days"));

        $usuarios_sin_codigo = $this->buscadorGeneral(null, null, $agenda_dias, $fecha, $data_seis_meses, $request['cedula_sin_codigo'], $request['nombres_sin_codigo']);

        $usuarios_busqueda = $this->buscadorGeneral(1, 1, $agenda_dias, $fecha, $data_seis_meses, null, null);

        return view('auditoria.index', ['usuarios' => $usuarios_busqueda, 'usuarios_sin_codigo' => $usuarios_sin_codigo]);
    }
}
