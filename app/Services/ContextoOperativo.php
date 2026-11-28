<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;

final class ContextoOperativo
{
    public const PRIMER_ANIO = 1900;

    public function __construct(private EmpresaActiva $empresa) {}

    public function sucursales(): Builder
    {
        return DB::table('erp_sucursales')->where('id_empresa', $this->empresa->id());
    }

    public function opciones(): Collection
    {
        return request()->attributes->get('sucursales') ?? $this->cargarSucursales();
    }

    private function cargarSucursales(): Collection
    {
        try {
            $sucursales = $this->sucursales()->orderBy('nombre')->get()->keyBy('id');
        } catch (QueryException $error) {
            if (! Schema::hasTable('erp_sucursales')) {
                abort(503, 'La actualización de sucursales está pendiente.');
            }

            throw $error;
        }

        request()->attributes->set('sucursales', $sucursales);

        return $sucursales;
    }

    public function sucursal(): stdClass
    {
        $sucursales = $this->opciones();
        if (request()->routeIs('api.*')) {
            if (! request()->has('id_sucursal') && $sucursales->count() === 1) {
                return $sucursales->first();
            }
            $datos = request()->validate(['id_sucursal' => ['required', 'integer', 'min:1']], [
                'id_sucursal.required' => 'Indica una sucursal de tu empresa.',
            ]);

            return $sucursales->get((int) $datos['id_sucursal'])
                ?? throw ValidationException::withMessages(['id_sucursal' => 'Selecciona una sucursal de tu empresa.']);
        }

        return $this->resolverSucursalWeb()
            ?? throw ValidationException::withMessages(['id_sucursal' => 'Selecciona una sucursal para continuar.']);
    }

    public function resolverSucursalWeb(): ?stdClass
    {
        $sucursales = $this->opciones();
        $seleccion = $this->seleccion();
        $sucursal = $sucursales->get($seleccion['id_sucursal'] ?? 0);
        if ($sucursal === null && $sucursales->count() === 1) {
            $sucursal = $sucursales->first();
            $this->guardarSeleccion($sucursal->id, $seleccion['periodo'] ?? $this->anioActual());
        }

        return $sucursal;
    }

    public function periodo(): ?int
    {
        if (request()->routeIs('api.*')) {
            if (! request()->has('periodo')) {
                return null;
            }
            $datos = request()->validate(['periodo' => $this->reglasPeriodo()]);

            return (int) $datos['periodo'];
        }

        return request()->hasSession()
            ? ($this->seleccion()['periodo'] ?? $this->anioActual())
            : null;
    }

    public function anioActual(): int
    {
        return now('America/Guayaquil')->year;
    }

    public function reglasPeriodo(): array
    {
        return ['required', 'integer', 'between:'.self::PRIMER_ANIO.','.($this->anioActual() + 1)];
    }

    public function guardarSeleccion(int $sucursal, int $periodo): void
    {
        if (! $this->opciones()->has($sucursal)) {
            throw ValidationException::withMessages(['id_sucursal' => 'Selecciona una sucursal de tu empresa.']);
        }

        request()->session()->put($this->clave(), ['id_sucursal' => $sucursal, 'periodo' => $periodo]);
    }

    public function aplicarPeriodo(Builder $consulta, string $columna): Builder
    {
        $periodo = $this->periodo();
        if ($periodo !== null) {
            $consulta->where($columna, '>=', $periodo.'-01-01')->where($columna, '<', ($periodo + 1).'-01-01');
        }

        return $consulta;
    }

    public function acotarFechas(array $filtros): array
    {
        if (($periodo = $this->periodo()) !== null) {
            $filtros['desde'] = max($filtros['desde'] ?? '', $periodo.'-01-01');
            $filtros['hasta'] = min($filtros['hasta'] ?? '9999-12-31', $periodo.'-12-31');
        }

        return $filtros;
    }

    public function guardarSucursal(?int $id, array $datos): void
    {
        $this->empresa->transaccion(function () use ($id, $datos): void {
            $codigo = Str::upper($datos['codigo']);
            $sucursal = $id === null ? null : ($this->sucursales()->where('id', $id)->first() ?? abort(404));
            if ($sucursal?->codigo === 'PRINCIPAL') {
                $codigo = 'PRINCIPAL';
            }

            if ($this->sucursales()->where('codigo', $codigo)->when($id !== null, fn (Builder $q) => $q->where('id', '!=', $id))->exists()) {
                throw ValidationException::withMessages(['codigo' => 'El código ya existe en esta empresa.']);
            }
            if (isset($datos['establecimiento_sri'], $datos['punto_emision_sri']) && $this->sucursales()
                ->where('establecimiento_sri', $datos['establecimiento_sri'])->where('punto_emision_sri', $datos['punto_emision_sri'])
                ->when($id !== null, fn (Builder $q) => $q->where('id', '!=', $id))->exists()) {
                throw ValidationException::withMessages(['punto_emision_sri' => 'Esta combinación de establecimiento y punto de emisión ya está asignada a otra sucursal de la empresa.']);
            }

            $datos['codigo'] = $codigo;
            $datos['updated_at'] = now();
            if ($id === null) {
                $this->sucursales()->insert($datos + ['id_empresa' => $this->empresa->id(), 'created_at' => now()]);

                return;
            }

            $this->sucursales()->where('id', $id)->update($datos);
        });
    }

    private function seleccion(): array
    {
        return request()->hasSession() ? request()->session()->get($this->clave(), []) : [];
    }

    private function clave(): string
    {
        return 'contexto_operativo.'.auth()->id().'.'.$this->empresa->id();
    }
}
