<?php

namespace App\Http\Controllers;

use App\Services\ContextoOperativo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final readonly class ContextoOperativoController
{
    public function __construct(private ContextoOperativo $contexto) {}

    public function inicio(Request $request): View|RedirectResponse
    {
        if ($this->contexto->resolverSucursalWeb() !== null) {
            return to_route('dashboard');
        }

        return view('configuracion.contexto-operativo', [
            'empresa' => $request->user()->empresa,
            'sucursales' => $this->contexto->opciones(),
            'periodo' => $this->contexto->periodo(),
            'ultimoPeriodo' => $this->contexto->anioActual() + 1,
        ]);
    }

    public function seleccionar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'id_sucursal' => ['required', 'integer', 'min:1'],
            'periodo' => $this->contexto->reglasPeriodo(),
        ]);

        $this->contexto->guardarSeleccion((int) $datos['id_sucursal'], (int) $datos['periodo']);

        return to_route('dashboard');
    }

    public function index(): View
    {
        return view('configuracion.sucursales', ['sucursales' => $this->contexto->opciones()]);
    }

    public function formulario(?int $sucursal = null): View
    {
        $registro = $sucursal === null ? null : ($this->contexto->opciones()->get($sucursal) ?? abort(404));

        return view('configuracion.sucursal-formulario', ['sucursal' => $registro]);
    }

    public function detalle(int $sucursal): View
    {
        $registro = $this->contexto->opciones()->get($sucursal) ?? abort(404);

        return view('partials.detalle-ficha-simple', [
            'titulo' => 'Detalle de sucursal', 'volver' => route('configuracion.sucursales.index'),
            'campos' => ['Código' => $registro->codigo, 'Nombre' => $registro->nombre, 'Dirección' => $registro->direccion,
                'Establecimiento SRI' => $registro->establecimiento_sri, 'Punto de emisión SRI' => $registro->punto_emision_sri],
        ]);
    }

    public function guardar(Request $request, ?int $sucursal = null): RedirectResponse|JsonResponse
    {
        $formatoSerie = 'regex:/\A(?!000)[0-9]{3}\z/';
        $datos = $request->validate([
            'codigo' => ['required', 'string', 'max:30', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'nombre' => ['required', 'string', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'establecimiento_sri' => ['required_with:punto_emision_sri', 'nullable', 'string', $formatoSerie],
            'punto_emision_sri' => ['required_with:establecimiento_sri', 'nullable', 'string', $formatoSerie],
        ], [
            'establecimiento_sri.required_with' => 'Ingresa el establecimiento SRI para completar la serie.',
            'punto_emision_sri.required_with' => 'Ingresa el punto de emisión SRI para completar la serie.',
            'establecimiento_sri.regex' => 'Ingresa tres dígitos entre 001 y 999.',
            'punto_emision_sri.regex' => 'Ingresa tres dígitos entre 001 y 999.',
        ]);
        $haySerie = array_key_exists('establecimiento_sri', $datos) || array_key_exists('punto_emision_sri', $datos);
        if ($haySerie) {
            $datos += ['establecimiento_sri' => null, 'punto_emision_sri' => null];
        }
        $primeraSucursal = $this->contexto->opciones()->isEmpty();
        $this->contexto->guardarSucursal($sucursal, $datos);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sucursal guardada.']);
        }

        if ($primeraSucursal) {
            return to_route('dashboard')->with('estado', 'Sucursal creada.');
        }

        return to_route('configuracion.sucursales.index')->with('estado', 'Sucursal guardada.');
    }
}
