<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ciudad;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Especialidad;
use Sis_medico\Examen_Orden;
use Sis_medico\Pais;
use Sis_medico\Provincia;
use Sis_medico\TipoUsuario;
use Sis_medico\User;

class ClinicalHc4SupportQueryService
{
    public function carga_barra_progress($id)
    {

        $orden      = Examen_Orden::find($id);
        $detalle    = $orden->detalles;
        $resultados = $orden->resultados;

        $cant_par = 0;
        foreach ($detalle as $d) {
            //$parametros = $parametros->where('id_examen', $d->id_examen);
            if ($d->examen->no_resultado == '0') {

                if (count($d->parametros) == '0') {
                    $cant_par++;
                }
                if ($d->examen->sexo_n_s == '0') {
                    $parametro_nuevo = $d->parametros->where('sexo', '3');

                } else {
                    $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);

                }
                foreach ($parametro_nuevo as $p) {
                    $cant_par++;
                }
            }

        }

        $certificados = 0;
        $cantidad     = 0;
        foreach ($resultados as $r) {
            $cantidad++;
            if ($r->certificado == '1') {
                $certificados++;

            }
        }
        if ($certificados > $cant_par) {
            $certificados = $cant_par;
        }

        return ['cantidad' => $cantidad, 'certificados' => $certificados, 'cant_par' => $cant_par];

    }

    //AGREGAR NUEVO PACIENTE
    //SOLO PARA DOCTORES

    public function calendario(Request $request)
    {

        //dd($request->all());
        if ($request['fecha'] == null) {
            $fecha_hoy = Date('Y-m-d');
        } else {
            $fecha_hoy = Date('Y-m-d', strtotime($request['fecha']));
        }

        $fecha_desde = date('Y-m-d', strtotime($fecha_hoy . "- 90 days"));
        $fecha_hasta = date('Y-m-d', strtotime($fecha_hoy . "+ 90 days"));
        //dd($fecha_hoy);
        $tipo = Auth::user()->id_tipo_usuario;

        $id   = Auth::user()->id;
        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3], ['id', '=', $id]])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento')->where('agenda.estado_cita', '<', '4')
            ->where('agenda.proc_consul', '=', 1)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
            })

            ->whereBetween('fechaini', [$fecha_desde . ' 0:00:00', $fecha_hasta . ' 23:59:00'])
            ->get();

        $agenda_px = DB::table('agenda as a')
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->leftjoin('pentax as p', 'p.id_agenda', 'a.id')
            ->join('paciente', 'a.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'h.id_seguro', '=', 'seguros.id')
            ->join('procedimiento', 'a.id_procedimiento', '=', 'procedimiento.id')
            ->select('a.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'p.id as pentax')
            ->where('a.proc_consul', '=', 1)
            ->where(function ($query) use ($id) {
                $query->where([['h.id_doctor1', '=', $id], ['a.estado', '=', '1']])
                    ->orWhere([['h.id_doctor2', '=', $id], ['a.estado', '=', '1']])
                    ->orWhere([['h.id_doctor3', '=', $id], ['a.estado', '=', '1']]);
            })
            ->where('a.created_at', '>', $fecha_desde)->where('a.estado_cita', '4')
            ->whereBetween('fechaini', [$fecha_desde . ' 0:00:00', $fecha_hasta . ' 23:59:00'])
            ->get();

        //dd($agenda,$agenda_px);
        $agenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro')
            ->where('proc_consul', '=', 0)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
            })

            ->whereBetween('fechaini', [$fecha_desde . ' 0:00:00', $fecha_hasta . ' 23:59:00'])
            ->get();

        //dd($agenda3);

        $agenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id], ['estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id], ['estado', '=', '1']]);
            })

            ->whereBetween('fechaini', [$fecha_desde . ' 0:00:00', $fecha_hasta . ' 23:59:00'])
            ->get();

        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();
        //dd($fecha_desde);
        $doctores    = DB::table('users')->where([['id_tipo_usuario', '=', 3]])->get(); //3=DOCTORES;
        $enfermero   = DB::table('users')->where([['id_tipo_usuario', '=', 6]])->get();
        $doctor      = User::find($id);
        $nombres     = $request['nombres'];
        $nombres_sql = '';
        $agendas_pac = [];
        if ($nombres != null) {
            $agendas_pac = DB::table('paciente as p')->join('historiaclinica as h', 'h.id_paciente', 'p.id')->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento')->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento');

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);
            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }
            $nombres_sql = $nombres_sql . '%';

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas_pac = $agendas_pac->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });

            } else {

                //$agendas_pac = $agendas_pac->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
                $agendas_pac = $agendas_pac->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }

            $agendas_pac = $agendas_pac->get();
        }

        //dd($agendas_pac);
        //return "hola";

        return view('hc4/calendario', ['user' => $user, 'doctor' => $doctor, 'users' => $doctores, 'enfermero' => $enfermero, 'id' => $id, 'agenda' => $agenda, 'agenda2' => $agenda2, 'salas' => $salas, 'agenda3' => $agenda3, 'agenda_px' => $agenda_px, 'fecha_hoy' => $fecha_hoy, 'nombres' => $nombres, 'agendas_pac' => $agendas_pac]);
    }


    public function perfil()
    {
        $id         = Auth::user()->id;
        $rolusuario = Auth::user()->id_tipo_usuario;
        $user       = User::find($id);
        // Redirect to user list if updating user wasn't existed

        $especialidades = especialidad::where('estado', 1)->orderBy('nombre')->get();
        $especialidad   = DB::table('user_espe')->where('usuid', '=', $id)->get();
        $paises         = pais::where(function ($query) use ($user) {
            $query->where('estado', 1)->orWhere('id', $user->id_pais);
        })->orderBy('nombre')->get();
        $ciudadUsuario  = !is_null($user->id_ciudad_usuario) ? Ciudad::find($user->id_ciudad_usuario) : null;
        if (is_null($ciudadUsuario) && !empty($user->ciudad)) {
            $ciudadUsuario = Ciudad::whereRaw('UPPER(nombre_ciudad) = ?', array(strtoupper($user->ciudad)))
                ->where('id_pais_ciudad', $user->id_pais)->first();
        }
        $idProvincia    = $ciudadUsuario ? $ciudadUsuario->id_provincia_ciudad : null;
        $provincias     = Provincia::where('id_pais_provincia', $user->id_pais)->where(function ($query) use ($idProvincia) {
            $query->where('estado', 1);
            if ($idProvincia) {
                $query->orWhere('id_provincia', $idProvincia);
            }
        })->orderBy('nombre_provincia')->get();
        $ciudades       = $idProvincia ? Ciudad::where('id_provincia_ciudad', $idProvincia)->where(function ($query) use ($ciudadUsuario) {
            $query->where('estado', 1);
            if ($ciudadUsuario) {
                $query->orWhere('id_ciudad', $ciudadUsuario->id_ciudad);
            }
        })->orderBy('nombre_ciudad')->get() : collect();
        $tipousuarios   = tipousuario::all();
        $anio           = date('Y');
        $rol_pag        = Ct_Rol_Pagos::where('estado', '1')->where('id_user', $user->id)->orderby('id', 'desc')->get();

        return view('hc4/perfil', ['user' => $user])->with('paises', $paises)->with('tipousuarios', $tipousuarios)->with('rolusuario', $rolusuario)->with('especialidad', $especialidad)->with('especialidades', $especialidades)->with('id', $id)->with('rol_pag', $rol_pag)->with('provincias', $provincias)->with('ciudades', $ciudades)->with('idProvincia', $idProvincia)->with('ciudadUsuario', $ciudadUsuario);
    }


}
