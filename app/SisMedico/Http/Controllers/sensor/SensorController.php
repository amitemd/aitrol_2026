<?php

namespace Sis_medico\Http\Controllers\sensor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Paciente;
use Sis_medico\Empresa;
use Carbon\Carbon;
use DateTime;
use Sis_medico\Examen;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\PruebaTemperatura;
use Sis_medico\Sensor_establecimiento;
use Sis_medico\Sensor_dispositivo;

class SensorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function sensor_establecimiento_create()
    {
        return view('sensor/sensor_establecimiento_create');
    }

    public function sensor_establecimiento_store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_sensor_establecimiento = [

            'nombre'               => $request["nombre"],
            'estado'               => 1,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,

        ];
        sensor_establecimiento::create($arr_sensor_establecimiento);
        return redirect(route('sensor.establecimiento_index'));
    }

    public function sensor_establecimiento_index()
    {
        $establecimiento = sensor_establecimiento::where('estado', 1)->get();
        return view('sensor/sensor_establecimiento_index', ['establecimiento' => $establecimiento]);
    }

    public function sensor_establecimiento_edit($id)
    {
        $establecimiento = sensor_establecimiento::where('id', $id)->first();
        return view('sensor/sensor_establecimiento_edit', ['establecimiento' => $establecimiento, 'id' => $id]);
    }

    public function sensor_establecimiento_update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $sensor = sensor_establecimiento::where('id', $request->id)->first();
        $arr_sensor = [

            'nombre'               => $request["nombre"],
            'estado'               => 1,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,

        ];
        $sensor->update($arr_sensor);
        return redirect(route('sensor.establecimiento_index'));
    }

    public function sensor_establecimiento_delete($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        try {

            $rc = sensor_establecimiento::find($id);
            $arr_eliminar = [
                'estado'               => 0,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,

            ];
            $rc->update($arr_eliminar);
            return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
        } catch (\Exception $e) {
            return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function sensor_dispositivo_create()
    {
        $establecimiento = sensor_establecimiento::where('estado', 1)->get();
        return view('sensor/sensor_dispositivo_create', compact('establecimiento'));
    }

    public function sensor_dispositivo_store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_sensor_dispositivo = [

            'serie'               => $request["serie"],
            'id_establecimiento'  => $request["establecimiento"],
            'estado'              => $request["estado"],
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,

        ];
        sensor_dispositivo::create($arr_sensor_dispositivo);
        return redirect(route('sensor.dispositivo_index'));
    }

    public function sensor_dispositivo_index()
    {
        $dispositivo = Sensor_dispositivo::where('estado', '!=', 2)->get();
        return view('sensor/sensor_dispositivo_index', ['dispositivo' => $dispositivo]);
    }

    public function sensor_dispositivo_edit($id)
    {
        $sensor = sensor_dispositivo::where('id', $id)->first();
        $dispositivo = Sensor_establecimiento::where('estado', 1)->get();
        return view('sensor/sensor_dispositivo_edit', ['dispositivo' => $dispositivo, 'id' => $id, 'sensor' => $sensor]);
    }

    public function sensor_dispositivo_update(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $sensor = sensor_dispositivo::where('id', $request->id)->first();
        $arr_sensor = [

            'serie'               => $request["serie"],
            'id_establecimiento'  => $request["dispositivo"],
            'estado'               => 1,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
        ];
        $sensor->update($arr_sensor);
        return redirect(route('sensor.dispositivo_index'));
    }

    public function sensor_dispositivo_delete($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        try {
            $rc = sensor_dispositivo::find($id);
            $arr_eliminar = [
                'estado'               => 2,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,

            ];
            $rc->update($arr_eliminar);
            return json_encode(['status' => true, 'msj' => "Eliminado con exito"]);
        } catch (\Exception $e) {
            return json_encode(['status' => false, 'msj' => $e->getMessage(), 'line' => $e->getLine()]);
        }
    }

    public function estadistico_sensor(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $serie = $request->serie;
        $data = PruebaTemperatura::where('estado', 1)
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                } else {
                    $q->where('created_at', '<=', $fecha_hasta . ' 23:59:59');
                }
            });
        if (!is_null($serie)) {
            $data->where('serie', $serie);
        }
        $data = $data->get();
        return view('sensor/estadistico_sensor');
    }

    public function cargar_data(Request $request)
    {
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $serie = $request->serie;
        $data = PruebaTemperatura::where('estado', 1)
            ->where(function ($q) use ($fecha_desde, $fecha_hasta) {
                if (!is_null($fecha_desde)) {
                    $q->whereBetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
                } else {
                    $q->where('created_at', '<=', $fecha_hasta . ' 23:59:59');
                }
            });

        if (!is_null($serie)) {
            $data->where('serie', $serie);
        }
        $data = $data->get();
        return  ['data' => $data];
    }

    public function buscador_sensor(Request $request)
    {
        $fecha_desde = $request->input('fecha_desde');
        $hora_desde = $request->input('hora_desde');
        $fecha_hasta = $request->input('fecha_hasta');
        $hora_hasta = $request->input('hora_hasta');

        $fecha_hora_desde = null;
        $fecha_hora_hasta = null;

        if (!is_null($hora_desde)) {
            $fecha_hora_desde = Carbon::createFromFormat('Y-m-d H:i', "$fecha_desde $hora_desde");
        }

        if (!is_null($hora_hasta)) {
            $fecha_hora_hasta = Carbon::createFromFormat('Y-m-d H:i', "$fecha_hasta $hora_hasta");
        }

        if (is_null($fecha_hora_desde)) {
            $fecha_hora_desde = Carbon::createFromFormat('Y-m-d', $fecha_desde)->startOfDay();
        }

        if (is_null($fecha_hora_hasta)) {
            $fecha_hora_hasta = Carbon::createFromFormat('Y-m-d', $fecha_hasta)->endOfDay();
        }

        $registros = PruebaTemperatura::where('estado', 1)
            ->whereBetween('created_at', [$fecha_hora_desde, $fecha_hora_hasta])
            ->get();

        return $registros;
    }
}
