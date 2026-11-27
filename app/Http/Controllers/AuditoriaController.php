<?php

namespace App\Http\Controllers;

use App\Services\Accesos;
use App\Services\EmpresaActiva;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

final class AuditoriaController
{
    public function index(Request $request, EmpresaActiva $empresa): View
    {
        $niveles = ['info' => 'Información', 'warning' => 'Advertencia', 'error' => 'Error'];
        $modulos = config('auditoria.modulos');
        $zona = config('auditoria.zona_horaria');
        $filtros = $request->validate([
            'nivel' => ['nullable', 'in:'.implode(',', array_keys($niveles))],
            'modulo' => ['nullable', 'in:'.implode(',', array_keys($modulos))],
            'ambito' => ['nullable', 'in:empresa,sistema,todas'],
            'usuario' => ['nullable', 'string', 'max:80'],
            'desde' => ['nullable', 'date_format:Y-m-d', 'after:1900-01-01', 'before:2100-01-01'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after:1900-01-01', 'before:2100-01-01'],
        ]);
        $ambito = $request->user()->is_admin_global ? ($filtros['ambito'] ?? 'empresa') : 'empresa';

        $consulta = DB::table('auditoria_eventos as e')
            ->leftJoin('erp_empresas as empresas', 'empresas.id', '=', 'e.id_empresa')
            ->select('e.*', 'empresas.razon_social as empresa');

        match ($ambito) {
            'sistema' => $consulta->whereNull('e.id_empresa'),
            'todas' => $consulta->whereNotNull('e.id_empresa'),
            default => $consulta->where('e.id_empresa', $empresa->id()),
        };

        foreach (['nivel', 'modulo'] as $campo) {
            if (! empty($filtros[$campo])) {
                $consulta->where('e.'.$campo, $filtros[$campo]);
            }
        }
        if (! empty($filtros['usuario'])) {
            $usuario = ltrim($filtros['usuario'], '#');
            if (ctype_digit($usuario) && strlen($usuario) <= 18) {
                $consulta->where('e.id_usuario', (int) $usuario);
            } else {
                $consulta->whereLike('e.usuario_nombre', '%'.$usuario.'%');
            }
        }
        if (! empty($filtros['desde'])) {
            $consulta->where('e.created_at', '>=', CarbonImmutable::parse($filtros['desde'], $zona)->setTimezone(config('app.timezone')));
        }
        if (! empty($filtros['hasta'])) {
            $consulta->where('e.created_at', '<', CarbonImmutable::parse($filtros['hasta'], $zona)->addDay()->setTimezone(config('app.timezone')));
        }

        return view('auditoria.index', [
            'eventos' => $consulta->orderByDesc('e.id')->paginate(30)->appends($filtros),
            'niveles' => $niveles,
            'modulos' => $modulos,
            'filtros' => $filtros,
            'ambito' => $ambito,
            'zona' => $zona,
        ]);
    }

    public function navegacion(Request $request, Accesos $accesos): Response
    {
        $datos = $request->validate(['ruta' => ['required', 'string', 'max:100']]);
        $ruta = Route::getRoutes()->getByName($datos['ruta']);
        abort_unless($ruta && in_array('GET', $ruta->methods(), true) && $ruta->parameterNames() === [], 404);

        $usuario = $request->user();
        $opcion = DB::table('menu')->where('ruta', $ruta->getName())->where('activo', true)
            ->where(fn ($consulta) => $consulta->where('solo_administrador', false)
                ->when($usuario->is_admin, fn ($consulta) => $consulta->orWhere('solo_administrador', true)));
        abort_unless($accesos->muestra($usuario, $ruta->getName()) && $opcion->exists(), 403);

        $request->attributes->set('auditoria.pestana', $ruta);

        return response()->noContent();
    }
}
