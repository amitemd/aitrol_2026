<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\PruebaTemperatura;
use Sis_medico\SensorDispositivo;

class BdSensoresController extends Controller
{
    public function sensores_store(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != '77f6d45d0070d89fe99787824988fa12') {
            return json_encode(["msj" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $data = $request->all();

        $serie = $data['serieDispositivo'];
        $dispositivo = SensorDispositivo::where('serie', $serie)->first();

        if (is_null($dispositivo)) {
            SensorDispositivo::create([
                'serie' => $data['serieDispositivo'],
                'estado' => 0
            ]);
            return ["msj" => 'SERIE NO EXISTE', 'status' => 'error'];
            
        }

        if ($dispositivo->estado == 0) {
            return ["msj" => 'SERIE NO AUTORIZADA', 'status' => 'error'];
        }


        PruebaTemperatura::create([
            'valor_temperatura' => $data['valorTemperatura'],
            'valor_humedad' => $data['valorHumedad'],
            'serie' => $data['serieDispositivo']
        ]);
        return ['status' => 'Success', 'msj' => 'Guardado'];
    }

    public function tabla(Request $request)
    {
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $data = PruebaTemperatura::where('estado', 1);
        if (!is_null($fecha_desde)) {
            $data = $data->whereBetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $data = $data->where('created_at', '<=', $fecha_hasta);
        }

        if (!is_null($request->serie)) {
            $data = $data->where('serie', $request->serie);
        }

        $data = $data->orderBy('created_at', 'desc')->get();

        $tr = "";

        foreach ($data as $value) {
            $tr .= "<tr>
                    <td>{$value->valor_temperatura}</td>
                    <td>{$value->valor_humedad}</td>
                    <td>{$value->serie}</td>
                    <td>{$value->created_at}</td>
                </tr>";
        }

        $table = "<table>
                <thead>
                    <tr>
                        <th>valorTemperatura</th>
                        <th>valorHumedad</th>
                        <th>serieDispositivo</th>
                        <th>created_at</th>
                    </tr>
                </thead>
                <tbody>
                    {$tr}
                </tbody>
            </table>";

        return $table;
    }
}
