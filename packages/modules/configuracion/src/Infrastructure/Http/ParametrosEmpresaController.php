<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use App\Services\EmpresaActiva;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final readonly class ParametrosEmpresaController
{
    private const CONCEPTOS = [
        'renta' => 'Impuesto a la renta',
        'utilidades' => 'Participación de trabajadores en utilidades',
        'iva' => 'IVA',
        'ice' => 'ICE',
        'irbp' => 'IRBP · Botellas plásticas',
    ];

    public function __construct(private EmpresaActiva $empresa) {}

    public function index(): View
    {
        $parametros = DB::table('erp_parametros_fiscales')->where('id_empresa', $this->empresa->id())
            ->orderBy('concepto')->orderBy('codigo')->orderByDesc('vigente_desde')->orderByDesc('id')->paginate(20);

        return view('configuracion::parametros-empresa', ['parametros' => $parametros, 'conceptos' => self::CONCEPTOS]);
    }

    public function create(): View
    {
        return view('configuracion::parametro-empresa-formulario', ['conceptos' => self::CONCEPTOS]);
    }

    public function guardar(Request $request): RedirectResponse|JsonResponse
    {
        $datos = $request->validate([
            'concepto' => ['required', Rule::in(array_keys(self::CONCEPTOS))],
            'codigo' => ['required', 'regex:/^[a-zA-Z0-9_-]{1,30}$/'],
            'nombre' => ['required', 'string', 'max:100'],
            'unidad' => ['required', 'in:porcentaje,usd-unidad'],
            'valor' => ['required', 'regex:/^\d{1,6}(?:\.\d{1,6})?$/', 'numeric', 'min:0'],
            'vigente_desde' => ['required', 'date_format:Y-m-d'],
        ]);
        if (in_array($datos['concepto'], ['renta', 'utilidades', 'iva'], true) && $datos['unidad'] !== 'porcentaje') {
            throw ValidationException::withMessages(['unidad' => 'Este concepto se parametriza mediante un porcentaje.']);
        }
        if ($datos['unidad'] === 'porcentaje' && $datos['valor'] > 100) {
            throw ValidationException::withMessages(['valor' => 'Un porcentaje debe estar entre 0 y 100.']);
        }

        $this->empresa->transaccion(function () use ($datos): void {
            $clave = ['id_empresa' => $this->empresa->id(), 'concepto' => $datos['concepto'], 'codigo' => Str::upper($datos['codigo']),
                'vigente_desde' => $datos['vigente_desde']];
            if (DB::table('erp_parametros_fiscales')->where($clave)->exists()) {
                throw ValidationException::withMessages(['vigente_desde' => 'Ya existe una tarifa con ese código y fecha de vigencia.']);
            }
            DB::table('erp_parametros_fiscales')->insert($clave + [
                'nombre' => $datos['nombre'], 'unidad' => $datos['unidad'], 'valor' => $datos['valor'],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        return $request->expectsJson()
            ? response()->json(['message' => 'Parámetro fiscal guardado.'])
            : to_route('configuracion.empresas.parametros')->with('estado', 'Parámetro fiscal guardado.');
    }
}
