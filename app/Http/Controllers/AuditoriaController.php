<?php

namespace App\Http\Controllers;

use App\Services\EmpresaActiva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class AuditoriaController
{
    public function index(Request $request, EmpresaActiva $empresa): View
    {
        $niveles = ['info' => 'Información', 'warning' => 'Advertencia', 'error' => 'Error'];
        $nivel = $request->query('nivel');
        $nivel = is_string($nivel) && isset($niveles[$nivel]) ? $nivel : null;
        $global = $request->user()->is_admin_global && $request->query('ambito') === 'sistema';

        $consulta = DB::table('auditoria_eventos')
            ->when($global, fn ($q) => $q->whereNull('id_empresa'), fn ($q) => $q->where('id_empresa', $empresa->id()))
            ->when($nivel, fn ($q) => $q->where('nivel', $nivel));

        return view('auditoria.index', [
            'eventos' => $consulta->orderByDesc('id')->paginate(30)
                ->appends(array_filter(['nivel' => $nivel, 'ambito' => $global ? 'sistema' : null])),
            'niveles' => $niveles,
            'nivel' => $nivel,
            'global' => $global,
        ]);
    }
}
