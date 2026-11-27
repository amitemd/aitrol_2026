<?php

namespace Aitrol\Tributario\Application;

use Aitrol\Tributario\Application\Puertos\RepositorioTributario;
use Aitrol\Tributario\Domain\PeriodoFiscal;
use stdClass;

final readonly class PreparacionTributaria
{
    public function __construct(private RepositorioTributario $repositorio) {}

    public function preparar(PeriodoFiscal $periodo, array $entradas): stdClass
    {
        return $this->repositorio->transaccion(function () use ($periodo, $entradas): stdClass {
            $anterior = $this->repositorio->buscarPeriodo($periodo);
            abort_if($anterior !== null && $anterior->estado !== 'borrador', 409, 'Reabre el borrador antes de regenerarlo.');
            $papel = $this->repositorio->preparar($periodo, $entradas);
            $id = $this->repositorio->guardarDeclaracion([
                'formato' => $periodo->formato, 'desde' => $periodo->desde, 'hasta' => $periodo->hasta, 'estado' => 'borrador',
                'resumen' => json_encode($papel['resumen'], JSON_THROW_ON_ERROR), 'detalle' => json_encode($papel['detalle'], JSON_THROW_ON_ERROR),
                'observaciones' => json_encode($papel['observaciones'], JSON_THROW_ON_ERROR),
            ], $anterior?->id);
            $this->repositorio->evento($id, 'preparado');

            return $this->repositorio->declaracion($id);
        });
    }

    public function transicion(int $id, string $accion, array $campos): stdClass
    {
        return $this->repositorio->transaccion(function () use ($id, $accion, $campos): stdClass {
            $anterior = $this->repositorio->declaracion($id);
            [$origen, $destino] = match ($accion) {
                'revisar' => ['borrador', 'revisado'],
                'reabrir' => ['revisado', 'borrador'],
                'registrar-entrega' => ['revisado', $anterior->formato === 'formulario-107' ? 'entregado' : 'presentado'],
            };
            abort_unless($anterior->estado === $origen, 409, 'La acción no está disponible para el estado actual.');
            if ($accion === 'registrar-entrega') {
                abort_unless($this->repositorio->tieneConstancia($id), 422, 'Adjunta una constancia de presentación o entrega en Gestión documental.');
                abort_if($this->repositorio->existeEntregaSolapada($anterior), 409, 'Ya hay una constancia registrada para parte de ese período.');
            }
            $this->repositorio->guardarDeclaracion(['estado' => $destino] + array_intersect_key($campos, array_flip(['referencia_externa', 'fecha_externa'])), $id);
            $this->repositorio->evento($id, $destino, $campos['motivo'] ?? $campos['referencia_externa'] ?? null);

            return $this->repositorio->declaracion($id);
        });
    }

    public function guardarNomina(array $entrada): stdClass
    {
        return $this->repositorio->transaccion(function () use ($entrada): stdClass {
            $periodo = new PeriodoFiscal('formulario-107', $entrada['ejercicio'], null, false);
            $previo = $this->repositorio->buscarPeriodo($periodo);
            abort_if($previo !== null && $previo->estado !== 'borrador', 409, 'El papel anual ya está revisado. Reábrelo antes de modificar los datos.');

            return $this->repositorio->guardarNomina($entrada);
        });
    }
}
