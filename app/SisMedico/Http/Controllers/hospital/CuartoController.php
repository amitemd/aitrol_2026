<?php

namespace Sis_medico\Http\Controllers\hospital;

use  Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Illuminate\Database\Eloquent;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Cama;
use Sis_medico\Paciente;
use Sis_medico\CamaPaciente;
use Sis_medico\CamaPacienteLog;
use Sis_medico\CamaTransaccion;
use Sis_medico\ClinicalOrder;
use Sis_medico\Habitacion;
use Sis_medico\Hospital_Producto;
use Sis_medico\Imagen;
use Sis_medico\Evolucion_Habitacion;
use Sis_medico\Formato_consentimiento;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Piso;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\User;
use Sis_medico\Tipo_Habitacion;
use Sis_medico\Hospital_Refrigerio;
use Sis_medico\Log_Agenda;
use Sis_medico\Pentax;
use Sis_medico\Pentax_log;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;
use Sis_medico\Services\ClinicalBedAssignmentService;
use Sis_medico\Services\ClinicalPatientBedTransferService;
use Sis_medico\Services\ClinicalPatientJourneyService;
use Sis_medico\Services\ClinicalTransferReconciliationService;
use Sis_medico\Services\CurrentHospitalResolver;
use Sis_medico\Services\HospitalBedBoardService;
use Sis_medico\Exceptions\ClinicalProcedureBlockedException;
use Sis_medico\Exceptions\ClinicalAccessDeniedException;

class CuartoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_if($this->rol_new('1'), 403);
            return $next($request);
        });
    }
    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Devuelve una ubicacion entendible para informar donde ya esta el paciente.
     */
    private function descripcionAsignacionActiva(Ho_Hospitalizacion $hospitalizacion)
    {
        $cama = Cama::with(['habitacion.piso'])->find($hospitalizacion->id_cama);

        if (is_null($cama)) {
            return 'otra cama activa';
        }

        $partes = ['cama ' . $cama->codigo];
        if (!is_null($cama->habitacion)) {
            $partes[] = 'habitacion ' . $cama->habitacion->codigo;
            if (!is_null($cama->habitacion->piso)) {
                $partes[] = $cama->habitacion->piso->nombre_piso;
            }
        }

        return implode(', ', $partes);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cuartos(HospitalBedBoardService $bedBoard)
    {
        return view('hospital/habitacion/cuartos', $bedBoard->data());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cuartos_habitacion()
    {

        $muestra = Habitacion::all();
        $muestra2 = Cama::all();
        $muestra3 = CamaTransaccion::all();
        $piso = Piso::all();
        $simples = Habitacion::where('id_tipo', '=', 1);

        $enhabitacion = Ho_Traspaso_Sala008::where('estado', 2)->get();
        //dd($enespera);
        return view('hospital/habitacion/cuartos_habitacion', ['muestra' => $muestra, 'muestra2' => $muestra2, 'muestra3' => $muestra3, 'piso' => $piso, 'simples' => $simples, 'enhabitacion' => $enhabitacion]);
    }

    public function autocompletarmodal(Request $request)
    {

        $codigo = $request['term'];
        $data      = array();

        //print_r($request)
        $medicinas = DB::table('hospital_producto')->where('estado', 1)
            ->where('nombre', 'like', trim($codigo) . '%')->orderBy('nombre')
            ->limit(20)->get(array('nombre'));
        foreach ($medicinas as $value) {
            $data[] = array('value' => $value->nombre);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }
    public function agregar_descripcion(Request $request)
    {
        //return $request['id_pedido'];
        $medicamento    = $request['medicamento'];
        $data      = null;
        $productos = DB::table('hospital_producto')->where('nombre', $medicamento)->first();

        if ($productos != '[]') {

            $data = $productos->descripcion;
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function autocompletarmodal2(Request $request)
    { {
            $codigo    = $request['codigo'];
            $data      = null;
            $productos = DB::table('hospital_producto')->where('id', $codigo)->first();
            if (!is_null($productos)) {
                return ['value' => $productos->nombre];
            } else {
                return ['value' => 'no'];
            }
        }
    }
    public function admcuarto($id, $id_paciente)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $pacienteid = Paciente::find($id);
        $log = CamaPaciente::where('id_cama', '=', $id)->where('id_paciente', $id_paciente)->get();
        $fecha = CamaPaciente::where('id_cama', '=', $id)->where('id_paciente', $id_paciente)->first();
        $cama = Cama::where('id', $id)->first();
        $enfermero = nota_enfermeria::where('id_paciente', '=', $id_paciente)->get();

        // ->rightJoin('nota_enfermeria', 'evolucion_habitacion.id_evolucion', '=', 'nota_enfermeria.id_evolucion')

        //->join('evolucion_habitacion','evolucion_habitacion.id_paciente','=','nota_enfermeria.id_evolucion')
        //->join('evolucion_habitacion', 'nota_enfermeria.id_evolucion', '=', 'evolucion_habitacion.id_paciente')
        //->select('nota_enfermeria.*', 'evolucion_habitacion.id_paciente')

        //hacer esto en la vista con la variable pacientito
        $habitacion = Habitacion::where('id', $cama->id_habitacion)->first();
        //dd($log);
        $id_habitacion = $habitacion->id_tipo;
        $id_cama = $id;
        $pacientito = Evolucion_Habitacion::where('id_paciente', '=', $id_paciente)->get();

        return view('hospital/habitacion/admcuarto', ['pacientito' => $pacientito, 'log' => $log, 'fecha' => $fecha, 'id_cama' => $id_cama, 'id_habitacion' => $id_habitacion, 'pacienteid' => $pacienteid, 'enfermero' => $enfermero]);
    }

    public function admasigncuarto($id, $idc, $ids)
    {

        $habitacion = Habitacion::find($id);
        $cama = Cama::where('id_habitacion', '=', $id)->get();
        $tipo = $habitacion->tipo->nombre;
        $id_tipo = $habitacion->tipo->id;
        $camatransaccion = CamaTransaccion::where('id_cama', '=', $ids)->get();
        $nombre = Imagen::where('id', '=', $idc)->get();
        foreach ($nombre as $value) {
            $url = $value->url_img;
        }
        foreach ($cama as $value) {
            $codigo = $value->codigo;
        }
        $id_cama = $ids;
        $otracama = Cama::where('id', '=', $ids)->get();
        foreach ($otracama as $value) {
            $estado = $value->estado;
        }
        return view('hospital/habitacion/admasigncuarto', ['tipo' => $tipo, 'url' => $url, 'id_tipo' => $id_tipo, 'codigo' => $codigo, 'id_cama' => $id_cama, 'estado' => $estado]);
    }
    public function provincias()
    {
        $evolucion = evolucion_producto::all();
        $evolucion_producto = evolucion::with('evolucion_producto')->get();
        return view('hospital/habitacion/admasigncuarto', ['evolucion' => $evolucion, 'evolucion_producto' => $evolucion_producto]);
    }

    //AUTOCOMPLETA LOS DATOS PRINCIPALES_DEL_PACIENTE
    public function auto(Request $request)
    {
        $nombre_encargado = $request['term'];
        $data             = null;
        $nuevo_nombre     = explode(' ', $nombre_encargado);
        $seteo            = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $nombres = DB::table('paciente')
            ->select('id', DB::raw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo"))
            ->whereRaw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like ?", array($seteo))
            ->orderBy('apellido1')->limit(20);
        app(\Sis_medico\Services\PatientTenantService::class)->constrain($nombres, 'paciente.id');
        $nombres = $nombres->get();
        foreach ($nombres as $product) {
            $data[] = array('value' => $product->completo, 'id' => $product->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $productos;
    }

    //AUTOCOMPLETA LOS DATOS DE FILIACION
    public function auto2(Request $request)
    {
        $nombre_encargado = $request['nombre'];
        $data  = null;
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = DB::table('paciente')
            ->select('nombre1', 'nombre2', 'apellido1', 'apellido2', 'telefono1', 'telefono2', 'id_seguro',
                'sexo', 'id', 'estadocivil', 'cedulafamiliar', 'religion', 'fecha_nacimiento', 'trabajo',
                'lugar_nacimiento', 'alergias', 'ciudad', 'gruposanguineo', 'direccion', 'antecedentes_pat',
                'antecedentes_fam', DB::raw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo"))
            ->whereRaw("CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like ?", array($seteo));
        app(\Sis_medico\Services\PatientTenantService::class)->constrain($query, 'paciente.id');
        $nombres = $query->get();
        foreach ($nombres as $product) {
            $data[] = array(
                'value' => $product->completo,
                'fecha' => $product->fecha_nacimiento, 'telefono1' => $product->telefono1, 'telefono2' => $product->telefono2,
                'seguro' => $product->id_seguro, 'sexo' => $product->sexo, 'id' => $product->id, 'estadoc' => $product->estadocivil, 'cedula' => $product->cedulafamiliar,
                'religion' => $product->religion, 'alergia' => $product->alergias, 'lugar_nacimiento' => $product->lugar_nacimiento, 'ciudad' => $product->ciudad,
                'grupos' => $product->gruposanguineo, 'direccion' => $product->direccion, 'antp' => $product->antecedentes_pat, 'antf' => $product->antecedentes_fam
            );
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
        return $data;
    }

    //ASIGNACION_PACIENTE_A_UNA_CAMA
    public function cuartog(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        return response()->json(['state' => 'error',
            'message' => 'Asigne la cama desde una solicitud de traslado pendiente en Recepcion.'], 422);
    }
    //LIBERAR CAMA DE LA HABITACION
    public function liberar(Request $request)
    {
        $active = Ho_Hospitalizacion::where('id_cama', $request->id_cama)->where('estado', 1)->first();
        if ($active) {
            throw new \InvalidArgumentException('Confirme el alta y la salida fisica, o reciba el traslado. No se puede liberar una cama con un paciente activo.');
        }
        $request->request->set('estado_cama', 'preparacion');
        return $this->actualizar_estado_cama($request);
    }
    public function freehabitation(Request $request)
    {
        $idusuario = Auth::user()->id;
        $ip = $request->ip();

        // Esta operación solo normaliza ocupaciones huérfanas. Nunca libera
        // ingresos clínicos ni modifica camas en preparación o fuera de servicio.
        $resultado = DB::transaction(function () use ($idusuario, $ip) {
            $roomIds = app(\Sis_medico\Services\ClinicalHospitalDestinationService::class)->roomIds();
            $camas = Cama::whereIn('id_habitacion', $roomIds)->where('estado', 3)->lockForUpdate()->get();
            $actualizadas = 0;
            $omitidas = 0;

            foreach ($camas as $cama) {
                $ocupacion = Ho_Hospitalizacion::where('id_cama', $cama->id)
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->first();
                if (!is_null($ocupacion)) {
                    $omitidas++;
                    continue;
                }
                $bedTransaction = CamaTransaccion::where('id_cama', $cama->id)->orderByDesc('id')->lockForUpdate()->first();
                if (($bedTransaction && $bedTransaction->id_paciente)
                    || CamaPaciente::where('id_cama', $cama->id)->where('estado', 1)->exists()) {
                    $omitidas++;
                    continue;
                }

                if ($this->guardar_estado_cama($cama, 'disponible', $idusuario, $ip)) {
                    $actualizadas++;
                }
            }

            return array('actualizadas' => $actualizadas, 'omitidas' => $omitidas);
        }, 3);

        $mensaje = $resultado['actualizadas'].' cama(s) disponible(s) normalizada(s).';
        if ($resultado['omitidas'] > 0) {
            $mensaje .= ' '.$resultado['omitidas'].' cama(s) ocupada(s) se conservaron sin cambios.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'state' => 'success',
                'message' => $mensaje,
            ]);
        }

        return redirect()->route('hospital.gcuartos');
    }

    public function mover_cama(Request $request)
    {
        abort_unless(Auth::user() && (int) Auth::user()->estado === 1
            && Auth::user()->isSystemUser(), 403);
        try {
            $idusuario = Auth::user()->id;
            $ip = $request->ip();
            $resultado = DB::transaction(function () use ($request, $idusuario, $ip) {
                $habitacion = Habitacion::where('id', $request->id_habitacion)->lockForUpdate()->first();
                if (is_null($habitacion) || (int) $habitacion->estado === 4) {
                    throw new \RuntimeException('La habitación destino no está disponible.');
                }

                $cama = Cama::where('id', $request->id_cama)->lockForUpdate()->first();
                if (is_null($cama)) {
                    throw new \RuntimeException('No se encontro la cama.');
                }

                $destinations = app(\Sis_medico\Services\ClinicalHospitalDestinationService::class);
                $destinations->assertBed($cama);
                if (!$destinations->roomIds()->contains($habitacion->id)) {
                    throw new \RuntimeException('La habitacion destino no esta configurada para esta empresa.');
                }
                if ((int) $cama->estado === 3 || Ho_Hospitalizacion::where('id_cama', $cama->id)->where('estado', 1)->exists()
                    || CamaPaciente::where('id_cama', $cama->id)->where('estado', 1)->exists()) {
                    throw new \RuntimeException('No se puede mover una cama ocupada. Registre el traslado del paciente.');
                }

                if ((int) $cama->id_habitacion === (int) $habitacion->id) {
                    return array(
                        'changed' => false,
                        'message' => 'La cama ya pertenece a la habitación '.$habitacion->codigo.'.',
                    );
                }

                $cama->id_habitacion = $habitacion->id;
                $cama->id_usuariomod = $idusuario;
                $cama->ip_modificacion = $ip;
                $cama->save();

                return array(
                    'changed' => true,
                    'message' => 'La cama fue movida a la habitación '.$habitacion->codigo.'.',
                );
            }, 3);

            return response()->json([
                'state' => 'success',
                'changed' => $resultado['changed'],
                'message' => $resultado['message'],
            ]);
        } catch (\RuntimeException $exception) {
            return response()->json(['state' => 'error', 'message' => $exception->getMessage()], 422);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json([
                'state' => 'error',
                'message' => 'No se pudo mover la cama. Ningun cambio fue guardado.',
            ], 500);
        }
    }

    public function trasladar_paciente(Request $request)
    {
        $this->validate($request, array(
            'id_hospitalizacion' => 'required|integer',
            'id_cama_destino' => 'required|integer',
            'motivo' => 'required|string|min:5|max:255',
        ));

        try {
            $result = app(ClinicalPatientBedTransferService::class)->transfer(
                $request->id_hospitalizacion,
                $request->id_cama_destino,
                trim((string) $request->motivo),
                array(
                    'actor_id' => Auth::user()->id,
                    'ip' => $request->ip(),
                )
            );

            return response()->json(array('state' => 'success') + $result);
        } catch (ClinicalAccessDeniedException $exception) {
            return response()->json(array('state' => 'error', 'message' => $exception->getMessage()), 403);
        } catch (\RuntimeException $exception) {
            return response()->json(array('state' => 'error', 'message' => $exception->getMessage()), 422);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(array(
                'state' => 'error',
                'message' => 'No se pudo completar el traslado. Ningún cambio fue guardado.',
            ), 500);
        }
    }

    public function liberar_cama_individual(Request $request)
    {
        $idusuario = Auth::user()->id;
        $ip_cliente = $request->ip();
        try {
            DB::transaction(function () use ($request, $idusuario, $ip_cliente) {
                $cama = Cama::where('id', $request->id_cama)->lockForUpdate()->first();
                if (is_null($cama)) {
                    throw new \RuntimeException('No se encontro la cama.');
                }
                $hospitalizacion = Ho_Hospitalizacion::where('id_cama', $cama->id)
                    ->where('estado', 1)->lockForUpdate()->first();

                if (!is_null($hospitalizacion)) {
                    throw new \RuntimeException('La cama tiene un paciente activo. Complete el alta y confirme su salida fisica.');
                }
                $estadoFinal = in_array($request->estado_final, ['disponible', 'preparacion', 'no_disponible']) ? $request->estado_final : 'disponible';
                $this->guardar_estado_cama($cama, $estadoFinal, $idusuario, $ip_cliente);
            }, 3);
        } catch (ClinicalAccessDeniedException $exception) {
            return response()->json(array('state' => 'error', 'message' => $exception->getMessage()), 403);
        } catch (\RuntimeException $exception) {
            return response()->json(array('state' => 'error', 'message' => $exception->getMessage()), 422);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json(array('state' => 'error', 'message' => 'No se pudo liberar la cama. Ningun cambio fue guardado.'), 500);
        }

        return response()->json(['state' => 'success', 'message' => 'La cama fue liberada.']);
    }

    private function authorizeBedOperation(Ho_Hospitalizacion $hospitalization, $action, Request $request)
    {
        return app(ClinicalPatientBedTransferService::class)->authorizeHospitalization(
            $hospitalization,
            $action,
            array(
                'user' => Auth::user(),
                'actor_id' => Auth::user()->id,
                'ip' => $request->ip(),
                'service_code' => 'HOSPITALIZATION_BEDS',
            )
        );
    }

    public function actualizar_estado_cama(Request $request)
    {
        $estado = $request->estado_cama;

        if (!in_array($estado, ['disponible', 'preparacion', 'no_disponible'], true)) {
            return response()->json(['state' => 'error', 'message' => 'Estado no valido.'], 422);
        }

        try {
            $idusuario = Auth::user()->id;
            $ip = $request->ip();
            $cambio = DB::transaction(function () use ($request, $estado, $idusuario, $ip) {
                $cama = Cama::where('id', $request->id_cama)->lockForUpdate()->first();
                if (is_null($cama)) {
                    throw new \RuntimeException('No se encontró la cama.');
                }

                $hospitalizacion = Ho_Hospitalizacion::where('id_cama', $cama->id)
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->first();
                if (!is_null($hospitalizacion)) {
                    throw new \RuntimeException('La cama esta ocupada. Confirme el alta y la salida fisica o reciba el traslado.');
                }

                return $this->guardar_estado_cama($cama, $estado, $idusuario, $ip);
            }, 3);
        } catch (\RuntimeException $exception) {
            return response()->json(['state' => 'error', 'message' => $exception->getMessage()], 422);
        } catch (\Exception $exception) {
            report($exception);
            return response()->json([
                'state' => 'error',
                'message' => 'No se pudo cambiar el estado. Ningún cambio fue guardado.',
            ], 500);
        }

        return response()->json([
            'state' => 'success',
            'changed' => $cambio,
            'message' => $cambio ? 'Estado actualizado.' : 'La cama ya tenía el estado solicitado.',
        ]);
    }

    private function guardar_estado_cama(Cama $cama, $estado, $idusuario, $ip_cliente)
    {
        abort_unless(Auth::user() && (int) Auth::user()->estado === 1
            && Auth::user()->hasAnyUserTypeName(['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA']), 403);
        app(\Sis_medico\Services\ClinicalHospitalDestinationService::class)->assertBed($cama);
        $estadoCama = 1;
        $imagen = 1;

        if ($estado == 'preparacion') {
            $estadoCama = 2;
            $imagen = 2;
        } elseif ($estado == 'no_disponible') {
            $estadoCama = 4;
            $imagen = 4;
        }

        if (!is_null($cama->habitacion) && !is_null($cama->habitacion->tipo)) {
            if ($cama->habitacion->tipo->id == 3) {
                $imagen = $estadoCama + 1 + $cama->habitacion->tipo->id;
            } elseif ($cama->habitacion->tipo->id == 5) {
                $imagen = $estadoCama + 3 + $cama->habitacion->tipo->id;
            }
        }

        $transaccion = CamaTransaccion::where('id_cama', $cama->id)->orderByDesc('id')->lockForUpdate()->first();
        if (($transaccion && $transaccion->id_paciente)
            || CamaPaciente::where('id_cama', $cama->id)->where('estado', 1)->exists()) {
            throw new \RuntimeException('Existe una ocupacion que debe conciliarse antes de cambiar el estado de la cama.');
        }
        $sinCambios = (int) $cama->estado === $estadoCama
            && !is_null($transaccion)
            && $transaccion->estado_cama === $estado
            && empty($transaccion->id_paciente);
        if ($sinCambios) {
            return false;
        }

        $cama->estado = $estadoCama;
        $cama->id_usuariomod = $idusuario;
        $cama->ip_modificacion = $ip_cliente;
        $cama->save();

        if (is_null($transaccion)) {
            CamaTransaccion::create([
                'id_cama'          => $cama->id,
                'estado_cama'      => $estado,
                'id_imagen'        => $imagen,
                'id_usuario'       => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'id_usuariomod'    => $idusuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
            ]);
        } else {
            $transaccion->estado_cama = $estado;
            $transaccion->id_paciente = null;
            $transaccion->id_imagen = $imagen;
            $transaccion->id_usuariomod = $idusuario;
            $transaccion->ip_modificacion = $ip_cliente;
            $transaccion->save();
        }

        return true;
    }
    //MODAL PRESCRIPCION DOCTOR
    public function modalprescripcion($id, Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $pacienteid = Paciente::find($id);
        $solicitud = Ho_Solicitud::where('id_paciente', $id)
            ->whereHas('encounter', function ($query) {
                return $query->where('status', 'ACTIVE');
            })->orderBy('id', 'desc')->first();
        $history = $solicitud && $solicitud->agenda ? $solicitud->agenda->historia_clinica : null;
        $prescriptionUrl = $history ? route('receta.receta', array('hcid' => $history->hcid)) : null;
        return view('hospital/habitacion/modalprescripcion', compact('pacienteid', 'prescriptionUrl'));
    }
    //Vista Costo generados por paciente
    public function costo($id, $id_cama)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        //dd($request->all());
        //$users  = CamaPaciente::where('id_paciente','=',$id)->get();
        $id_paciente = $id;
        //dd($id_paciente);
        $tipo = DB::table('tipo_habitacion')->get();
        $categoria_habitacion = DB::table('cama as c')
            ->join('cama_paciente as cp', 'cp.id_cama',  'c.id')
            ->join('paciente as p', 'p.id', 'cp.id_paciente')
            ->where('cp.id_cama', $id_cama)
            ->where('cp.id_paciente', $id)
            ->select('c.codigo', 'cp.id_paciente', 'p.nombre1', 'p.apellido1', 'cp.updated_at')->get();
        //dd($categoria_habitacion);
        $nombres = Hospital_Refrigerio::where('id_paciente', '=', $id)->get();
        //dd($nombres);
        return view('hospital/habitacion/costo', ['categoria_habitacion' => $categoria_habitacion, 'tipo' => $tipo, 'id_paciente' => $id_paciente, 'nombres' => $nombres]);
    }
    //Formulario de costo generado por paciente
    public function costos_generados($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        return view('hospital/habitacion/costo', ['users ' => $users]);
    }
    //AGSINAR MEDICAMENTO POR PARTE DEL DOCTOR Y GUARDAR EN LA BD
    public function guardarm(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $message = 'La prescripción libre antigua fue deshabilitada. Registre una receta estructurada vinculada al episodio clínico.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array('estado' => 'E', 'msn' => $message), 409);
        }
        return back()->withErrors(array($message));
    }
    public function paciente()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
        }
        return view('hospital/habitacion/paciente');
    }
    //MODAL ENFERMERIA VISTA
    public function modalenfermeria($id, Request $request, $id_evolucion)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $pacienteid = Paciente::find($id);
        $solicitud = Ho_Solicitud::where('id_paciente', $id)
            ->whereHas('encounter', function ($query) {
                return $query->where('status', 'ACTIVE');
            })->orderBy('id', 'desc')->first();
        $receta = null;
        if ($solicitud && $solicitud->agenda && $solicitud->agenda->historia_clinica) {
            $receta = hc_receta::where('id_hc', $solicitud->agenda->historia_clinica->hcid)->orderBy('id', 'desc')->first();
        }
        $emarUrl = $solicitud && $receta
            ? route('hospitalizacion.descargo_enfermeria_detalle', array('id' => $receta->id, 'id_solicitud' => $solicitud->id))
            : null;
        return view('hospital/habitacion/modalenfermeria', compact('pacienteid', 'emarUrl'));
    }
    //MEDICAMENTO SUMINISTRADO POR ENFERMERIA
    public function suministar(Request $request)
    {

        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $message = 'La administración clínica antigua fue deshabilitada. Registre la dosis desde el eMAR con una orden activa.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array('estado' => 'E', 'msn' => $message), 409);
        }
        return back()->withErrors(array($message));
    }
    public function modalservicio(Request $request, $id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $servicio = Paciente::find($id);
        return view('hospital/habitacion/modalservicios', ['id' => $id, 'servicio' => $servicio]);
    }
    public function salvar(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }
        $ip_clientes = $_SERVER["REMOTE_ADDR"];
        $idusuario1  = Auth::user()->id;

        Hospital_Refrigerio::create([
            'id_paciente'       => $request['pacienterefri'],
            'id_usuariocrea' => $idusuario1,
            'ip_creacion' => $ip_clientes,
            'id_usuariomod' => $idusuario1,
            'desayuno' => $request['desayunos'],
            'cant_desayuno' => $request['cantidad_desayuno'],
            'precio_desayuno' => $request['precio_desayuno'],
            'almuerzo' => $request['almuerzo'],
            'cant_almuerzo' => $request['cant_almuerzo'],
            'precio_almuerzo' => $request['precio_almuerzo'],
            'cena' => $request['cena'],
            'cant_cena' => $request['cantidad_cena'],
            'precio_cena' => $request['precio_cena'],
            'bebidas' => $request['bebidas'],
            'cant_bebidas' => $request['cantidad_bebi'],
            'precio_bebidas' => $request['precio_bebi'],


        ]);
        return back();
    }
    public function getSources(Request $request)
    {
        //dd($request->all());
        $id = $request['id'];
        $status = $request['status'];
        if ($status == '1') {
            $o = Ho_Traspaso_Sala008::find($id);
            if (is_null($o)) {
                return response()->json('error');
            }
            $arraySource['paciente'] = $o->paciente->apellido1 . ' ' . $o->paciente->apellido2 . ' ' . $o->paciente->nombre1;
            $arraySource['fecha'] = date('d/m/Y H:s', strtotime($o->fecha));
            $arraySource['id'] = $o->paciente->id;
            return view('hospital.habitacion.seleccion_paciente', ['paciente' => $arraySource['paciente'], 'fecha' => $arraySource['fecha'], 'id' => $arraySource['id'], 'traspaso' => $o]);
            //return response()->json($arraySource);
        } else { //ASIGNA CMA A HOSPITALIZACION 
            //dd("aqui");
            $s = Ho_Hospitalizacion::where('id_cama', $id)
                ->where('estado', 1)
                ->first();
            if (is_null($s)) {
                return response()->json(['state' => 'success']);
            }
            //dd($s);
            $o = Ho_Traspaso_Sala008::find($s->id_traspaso);
            if (!is_null($o)) {
                if ($o->estado == 1) {
                    $o->estado = 2;
                    $o->save();
                    return response()->json(['state' => 'success']);
                } else {

                    return response()->json(['state' => 'error']);
                }
            } else {
                return response()->json(['state' => 'error']);
            }
        }
    }
    public function modal_paciente(Request $request)
    {
        $cama = Cama::findOrFail($request->id);
        $destinations = app(\Sis_medico\Services\ClinicalHospitalDestinationService::class);
        $destinations->assertBed($cama);
        $salaId = DB::table('clinical_hospital_bed_locations')->where('habitacion_id', $cama->id_habitacion)
            ->where('company_id', $destinations->companyId())->value('sala_id');
        $enespera = Ho_Traspaso_Sala008::with('paciente')->where('estado', 1)
            ->whereIn('paso', [3, 6, 7])->where('id_sala', $salaId)
            ->whereHas('solicitud', function ($query) use ($destinations) {
                $query->where('estado', 1)->whereIn('encounter_id', \Sis_medico\ClinicalEncounter::where('id_empresa', $destinations->companyId())
                    ->whereNull('ended_at')->pluck('id'));
            })->orderBy('fecha')->get();
        return view('hospital.habitacion.modal_paciente', compact('enespera', 'cama'));
    }
    public function asignar_paciente($id, $y)
    {
        $cama = Cama::find($id);
        $traspaso = Ho_Traspaso_Sala008::find($y);

        if (is_null($cama) || is_null($traspaso)) {
            return redirect()->route('hospital.gcuartos')
                ->with('error', 'No fue posible encontrar la cama o la solicitud del paciente.');
        }

        $paciente = Paciente::find($traspaso->id_paciente);
        if (is_null($paciente)) {
            return redirect()->route('hospital.gcuartos')
                ->with('error', 'No fue posible encontrar al paciente seleccionado.');
        }

        app(\Sis_medico\Services\ClinicalHospitalTransferService::class)
            ->authorize($traspaso->solicitud, 'hospital.transfer.receive', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA']);
        app(\Sis_medico\Services\ClinicalHospitalDestinationService::class)->assertBed($cama, $traspaso->id_sala);

        return view('hospital.habitacion.asignar_paciente', ['cama' => $cama, 'paciente' => $paciente, 'traspaso' => $traspaso]);
    }
    public function save_paciente(Request $request)
    {
        $this->validate($request, ['id_paciente' => 'required|string|max:32', 'id_cama' => 'required|integer',
            'traspaso' => 'required|integer', 'observacion' => 'nullable|string|max:2000']);
        try {
            $result = app(\Sis_medico\Services\ClinicalHospitalTransferService::class)->receive(
                $request->traspaso, $request->id_paciente, $request->id_cama, $request->input('observacion', '')
            );
            return response()->json(['state' => 'Guardado Correctamente'] + $result);
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['state' => $exception->getMessage(), 'message' => $exception->getMessage(),
                'code' => 'invalid_hospital_transfer'], 422);
        } catch (ClinicalAccessDeniedException $exception) {
            return response()->json(['state' => $exception->getMessage(), 'message' => $exception->getMessage()], 403);
        }
    }
    public function show_cama($id, $id_traspaso, Request $request)
    {
        $hospitalizacion = Ho_Hospitalizacion::find($id_traspaso);
        abort_unless($hospitalizacion, 404);
        $workflowSolicitudId = $this->solicitudIdForHospitalization($hospitalizacion->id);
        $workflowSolicitud = Ho_Solicitud::findOrFail($workflowSolicitudId);
        app(\Sis_medico\Services\ClinicalHospitalTransferService::class)->authorize(
            $workflowSolicitud, 'hospital.view', ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ENFERMERIA', 'ENFERMERÍA', 'ANESTESIÓLOGO', 'ANESTESIOLOGO'], false);
        // La hospitalizacion es la fuente de verdad. Si el usuario conserva una
        // URL anterior a un traslado, la ficha debe mostrar la cama vigente.
        $cama = !is_null($hospitalizacion)
            ? Cama::with(['habitacion.tipo', 'habitacion.piso'])->find($hospitalizacion->id_cama)
            : null;

        if (is_null($cama) || is_null($hospitalizacion)) {
            return redirect()->route('hospital.gcuartos')
                ->with('error', 'No fue posible encontrar la cama o el ingreso seleccionado.');
        }

        $paciente = Paciente::with(['usuario', 'seguro', 'a_alergias.principio_activo'])
            ->find($hospitalizacion->id_paciente);

        if (is_null($paciente)) {
            return redirect()->route('hospital.gcuartos')
                ->with('error', 'No fue posible encontrar la información del paciente.');
        }

        $edad = null;
        $fechaNacimiento = null;
        $fechaNacimientoValor = trim((string) $paciente->fecha_nacimiento);

        if ($fechaNacimientoValor !== '' && $fechaNacimientoValor !== '0000-00-00') {
            try {
                $fechaNacimiento = new \DateTime($fechaNacimientoValor);
                $hoy = new \DateTime('today');
                if ($fechaNacimiento <= $hoy) {
                    $edad = $fechaNacimiento->diff($hoy)->y;
                }
            } catch (\Exception $exception) {
                $fechaNacimiento = null;
            }
        }

        $diasIngreso = 0;
        try {
            $ingreso = new \DateTime(date('Y-m-d', strtotime($hospitalizacion->fecha)));
            $hoy = new \DateTime('today');
            $diferenciaIngreso = $ingreso->diff($hoy);
            $diasIngreso = $diferenciaIngreso->invert ? 0 : (int) $diferenciaIngreso->days;
        } catch (\Exception $exception) {
            $diasIngreso = 0;
        }

        $journey = app(ClinicalPatientJourneyService::class)->forHospitalization($hospitalizacion);
        $ordersAvailable = Schema::hasTable('clinical_orders');
        $episodeOrders = $workflowSolicitud->encounter_id && $ordersAvailable
            ? ClinicalOrder::where('encounter_id', $workflowSolicitud->encounter_id)
                ->whereIn('category', ['LABORATORY', 'IMAGING'])
                ->orderBy('ordered_at', 'desc')->orderBy('id', 'desc')->limit(8)->get()
            : collect();

        return view('hospital.habitacion.detalle', [
            'cama' => $cama,
            'hospitalizacion' => $hospitalizacion,
            'paciente' => $paciente,
            'edad' => $edad,
            'fechaNacimiento' => $fechaNacimiento,
            'diasIngreso' => $diasIngreso,
            'journey' => $journey,
            'episodeOrders' => $episodeOrders,
            'ordersAvailable' => $ordersAvailable,
            'workflowSolicitudId' => $workflowSolicitudId,
        ]);
    }
    public function modal_quirofano(Request $request)
    {
        $solicitudId = $this->solicitudIdForHospitalization($request->id_hospitalizacion);
        app(\Sis_medico\Services\ClinicalHospitalTransferService::class)->authorize(
            Ho_Solicitud::findOrFail($solicitudId), 'hospital.transfer.request', ['SISTEMAS', 'DOCTORES', 'DOCTOR']);
        $sala = app(\Sis_medico\Services\ClinicalHospitalDestinationService::class)->salas([5]);
        $id_tipo = $request->id_tipo;
        $id_cama = $request->id_cama;
        $id_hospitalizacion = $request->id_hospitalizacion;
        $id_paciente = $request->id_paciente;
        $users = User::where('id_tipo_usuario', User::roleIdForName(array('DOCTORES', 'DOCTOR')))->get();
        $solicitudId = $this->solicitudIdForHospitalization($id_hospitalizacion);
        $activeOrders = $solicitudId ? app(ClinicalTransferReconciliationService::class)->previewActiveOrders($solicitudId) : array();
        $consentFormats = $this->consentFormatsForSolicitud($solicitudId);
        return view('hospital.habitacion.modal_cirugia', ['sala' => $sala, 'id_tipo' => $id_tipo, 'id_cama' => $id_cama, 'id_paciente' => $id_paciente, 'id_hospitalizacion' => $id_hospitalizacion, 'users' => $users, 'activeOrders' => $activeOrders, 'consentFormats' => $consentFormats]);
    }

    /**
     * Catálogo remoto para los selectores de cirugía e imágenes.
     * Evita cargar miles de procedimientos al abrir el modal.
     */
    public function buscar_procedimientos_modal(Request $request)
    {
        $termino = trim((string) $request->get('q', ''));
        $tipo = $request->get('tipo', 'procedimientos');

        $longitud = function_exists('mb_strlen') ? mb_strlen($termino, 'UTF-8') : strlen($termino);
        if ($longitud < 2) {
            return response()->json(['results' => []]);
        }

        $terminoLike = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $termino);
        $consulta = Procedimiento::query()
            ->select('id', 'nombre')
            ->where('estado', 1)
            ->where('nombre', 'like', '%' . $terminoLike . '%');

        if ($tipo === 'imagenes') {
            $consulta->where('id_grupo_procedimiento', 20);
        }

        $resultados = $consulta
            ->orderBy('nombre')
            ->limit(20)
            ->get()
            ->map(function ($procedimiento) {
                return [
                    'id' => (string) $procedimiento->id,
                    'text' => $procedimiento->nombre,
                ];
            })
            ->values();

        return response()->json(['results' => $resultados]);
    }
    public function cirugia(Request $request)
    {
        $this->validate($request, ['id_hospitalizacion' => 'required|integer', 'sala' => 'required|integer',
            'procedure_indication' => 'required|string|min:10|max:2000', 'procedimiento' => 'required|array|min:1',
            'procedimiento.*' => 'required|integer|exists:procedimiento,id']);
        try {
            $solicitudId = $this->solicitudIdForHospitalization($request->id_hospitalizacion);
            $names = Procedimiento::whereIn('id', $request->procedimiento)->pluck('nombre')->all();
            $reason = $request->procedure_indication.' Procedimientos propuestos: '.implode('; ', $names)
                .' '.mb_substr((string) $request->input('observaciones'), 0, 2000);
            app(\Sis_medico\Services\ClinicalHospitalTransferService::class)
                ->requestTransfer($solicitudId, 5, $request->sala, $reason);
            return redirect()->route('hospital.index_modulos', ['id_paso' => 5])
                ->with('success', 'Cirugia solicitada. La cama actual se conserva hasta la recepcion.');
        } catch (\InvalidArgumentException $exception) {
            return back()->withInput()->withErrors(['transfer' => $exception->getMessage()]);
        }
    }
    public function agenda(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalWardProcedureService::class)->agenda($request);
    }

    public function crear_procedimiento(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalWardProcedureService::class)->crear_procedimiento($request);
    }
    public function modal_imagenes(Request $request)
    {
        $solicitudId = $this->solicitudIdForHospitalization($request['id_hospitalizacion']);
        $activeOrders = $solicitudId ? app(ClinicalTransferReconciliationService::class)->previewActiveOrders($solicitudId) : array();
        $consentFormats = $this->consentFormatsForSolicitud($solicitudId);
        return view('hospital.habitacion.modal_imagenes', ['users' => User::where('id_tipo_usuario', User::roleIdForName(array('DOCTORES', 'DOCTOR')))->get(), 'sala' => $this->activeRoomsForSolicitud($solicitudId), 'id_tipo' => $request['id_tipo'], 'id_cama' => $request['id_cama'], 'id_hospitalizacion' => $request['id_hospitalizacion'], 'indetificacion' => $request['indetificacion'], 'activeOrders' => $activeOrders, 'consentFormats' => $consentFormats]);
    }

    public function guardar_imagenes(Request $request)
    {
        $this->validate($request, ['id_hospitalizacion' => 'required|integer', 'id_paciente' => 'required',
            'procedimiento' => 'required|array|min:1', 'sala' => 'required', 'doctor' => 'required',
            'procedure_indication' => 'required|min:5', 'procedure_identity_verified' => 'required|in:1']);
        try {
            return DB::transaction(function () use ($request) {
                $solicitud = Ho_Solicitud::where('id', $this->solicitudIdForHospitalization($request->id_hospitalizacion))
                    ->lockForUpdate()->firstOrFail();
                app(\Sis_medico\Services\ClinicalHospitalTransferService::class)->authorize($solicitud, 'procedure.order', ['SISTEMAS', 'DOCTORES', 'DOCTOR']);
                if ((string) $solicitud->id_paciente !== (string) $request->id_paciente) {
                    throw new \InvalidArgumentException('El paciente no corresponde al episodio.');
                }
                $agendaId = $this->crear_imagen($request);
                if (!is_numeric($agendaId)) throw new \InvalidArgumentException((string) $agendaId);
                return redirect()->route('hospital.gcuartos')->with('success', 'Estudio solicitado; la cama y el area clinica se conservan.');
            }, 3);
        } catch (ClinicalProcedureBlockedException $exception) {
            return back()->withInput()->withErrors([$exception->getMessage()]);
        } catch (\InvalidArgumentException $exception) {
            return back()->withInput()->withErrors([$exception->getMessage()]);
        }
    }

    private function solicitudIdForHospitalization($hospitalizationId)
    {
        $hospitalization = Ho_Hospitalizacion::find($hospitalizationId);
        if (!$hospitalization) return null;
        return Ho_Traspaso_Sala008::where('id', $hospitalization->id_traspaso)->value('id_solicitud');
    }

    private function consentFormatsForSolicitud($solicitudId)
    {
        $solicitud = $solicitudId ? Ho_Solicitud::with('agenda')->find($solicitudId) : null;
        $companyId = $solicitud && $solicitud->agenda ? $solicitud->agenda->id_empresa : null;
        return Formato_consentimiento::where('estado', 1)
            ->where(function ($query) use ($companyId) {
                $query->whereNull('id_empresa');
                if ($companyId) $query->orWhere('id_empresa', $companyId);
            })->whereNotNull('nombre')->where('nombre', '<>', '')
            ->selectRaw('MIN(id) as id, MIN(nombre) as nombre')
            ->groupBy(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(nombre))'))
            ->orderBy('nombre')->get();
    }

    private function activeRoomsForSolicitud($solicitudId)
    {
        $hospitalId = app(CurrentHospitalResolver::class)->id(array('solicitud_id' => $solicitudId));
        return Sala::where('estado', 1)->when($hospitalId, function ($query) use ($hospitalId) {
            return $query->where('id_hospital', $hospitalId);
        })->orderBy('nombre_sala')->get();
    }

    public function crear_imagen(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalWardProcedureService::class)->crear_imagen($request);
    }
    public function agenda_hospital(Request $request, $id_sala)
    {

        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        //dd($fecha_hasta);
        if ($fecha_desde == null) {
            $fecha_desde = date('Y-m-d');
        }

        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y-m-d');
        }

        $agendas = Agenda::where('estado', '>', 0)->where('id_sala', $id_sala)
            ->whereBetween('fechaini', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->get();


        $sala = Sala::find($id_sala);

        return view('hospital.master.index', ['agendas' => $agendas, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_sala' => $id_sala, 'sala' => $sala]);
    }
}
