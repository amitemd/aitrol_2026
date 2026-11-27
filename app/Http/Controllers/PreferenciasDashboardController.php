<?php

namespace App\Http\Controllers;

use App\Services\EmpresaActiva;
use App\Services\PanelAdministrativo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class PreferenciasDashboardController extends Controller
{
    public function __invoke(Request $request, EmpresaActiva $empresa, PanelAdministrativo $panel): Response
    {
        abort_unless($panel->personalizable(), 503);

        $reglas = [
            'distribucion' => ['required', 'array:indicadores,paneles'],
            'grafico' => ['required', 'string', Rule::in(array_keys(PanelAdministrativo::GRAFICOS))],
        ];
        foreach (PanelAdministrativo::ORDEN as $grupo => $ids) {
            $reglas["distribucion.$grupo"] = ['required', 'array', 'list', 'size:'.count($ids)];
            $reglas["distribucion.$grupo.*"] = ['required', 'string', 'distinct:strict', Rule::in($ids)];
        }
        $datos = $request->validate($reglas);

        DB::table('dashboard_preferencias')->upsert([[
            'id_empresa' => $empresa->id(),
            'id_usuario' => $request->user()->id,
            'distribucion' => json_encode($datos['distribucion'], JSON_THROW_ON_ERROR),
            'grafico' => $datos['grafico'],
        ]], ['id_empresa', 'id_usuario'], ['distribucion', 'grafico']);

        return response()->noContent();
    }
}
