<?php

namespace Aitrol\Inventarios\Application;

use Aitrol\Inventarios\Domain\CantidadInventario as Cantidad;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class Inventario
{
    public function __construct(private RepositorioInventario $repositorio) {}

    /** @return array{operacion: stdClass, repetida: bool} */
    public function registrar(array $datos): array
    {
        $operacion = [
            'tipo' => $datos['tipo'],
            'id_producto' => (int) $datos['id_producto'],
            'id_bodega' => (int) $datos['id_bodega'],
            'id_bodega_destino' => isset($datos['id_bodega_destino']) ? (int) $datos['id_bodega_destino'] : null,
            'cantidad' => Cantidad::unidades($datos['cantidad'], 3),
            'costo_unitario' => isset($datos['costo_unitario']) ? Cantidad::unidades($datos['costo_unitario'], 4) : null,
            'motivo' => $datos['motivo'],
            'referencia' => $datos['referencia'] ?? null,
        ];
        $clave = isset($datos['clave_operacion']) ? strtolower($datos['clave_operacion']) : null;
        $huella = hash('sha256', json_encode($operacion, JSON_THROW_ON_ERROR));

        try {
            return $this->repositorio->transaccion(function () use ($operacion, $clave, $huella): array {
                $producto = $this->repositorio->catalogo('productos', $operacion['id_producto'], bloquear: true);
                if ($clave !== null && ($existente = $this->repositorio->porClave($clave)) !== null) {
                    return $this->repetida($existente, $huella);
                }

                $bodegas = array_filter([$operacion['id_bodega'], $operacion['id_bodega_destino']]);
                sort($bodegas, SORT_NUMERIC);
                foreach ($bodegas as $id) {
                    $bodega = $this->repositorio->catalogo('bodegas', $id, bloquear: true);
                    if (! $bodega->activo) throw ValidationException::withMessages(['id_bodega' => 'La bodega debe estar activa.']);
                }
                if (! $producto->activo) throw ValidationException::withMessages(['id_producto' => 'El producto debe estar activo.']);

                $origen = $this->repositorio->saldo($producto->id, $operacion['id_bodega']);
                $suma = in_array($operacion['tipo'], ['entrada', 'ajuste_entrada'], true);
                $costo = $operacion['costo_unitario'] ?? (int) $origen->costo_promedio;
                $cantidad = $operacion['cantidad'];
                $lineas = [$this->linea($origen, $suma ? $cantidad : -$cantidad, $costo)];
                if ($operacion['tipo'] === 'transferencia') {
                    $destino = $this->repositorio->saldo($producto->id, $operacion['id_bodega_destino']);
                    $lineas[] = $this->linea($destino, $cantidad, $costo);
                }

                return ['operacion' => $this->repositorio->registrar(array_replace($operacion, [
                    'costo_unitario' => $costo, 'clave_operacion' => $clave, 'huella' => $huella,
                ]), $lineas), 'repetida' => false];
            });
        } catch (UniqueConstraintViolationException $error) {
            $existente = $clave === null ? null : $this->repositorio->porClave($clave);
            if ($existente === null) throw $error;

            return $this->repetida($existente, $huella);
        }
    }

    private function repetida(stdClass $existente, string $huella): array
    {
        abort_unless($existente->huella === $huella, 409, 'La clave ya se usó para otra operación.');

        return ['operacion' => $existente, 'repetida' => true];
    }

    private function linea(stdClass $existencia, int $variacion, int $costo): array
    {
        $anterior = (int) $existencia->cantidad;
        $saldo = $anterior + $variacion;
        if ($saldo < 0 || $saldo > Cantidad::MAXIMO) {
            throw ValidationException::withMessages([
                'cantidad' => $saldo < 0 ? 'La existencia disponible es insuficiente.' : 'El movimiento supera el máximo de existencia admitido.',
            ]);
        }

        $promedio = (int) $existencia->costo_promedio;
        if ($saldo === 0) {
            $promedio = 0;
        } elseif ($variacion > 0) {
            $promedio = intdiv($anterior * $promedio + $variacion * $costo + intdiv($saldo, 2), $saldo);
        }

        return [
            'id_bodega' => $existencia->id_bodega,
            'variacion' => $variacion, 'saldo_anterior' => $anterior, 'saldo' => $saldo,
            'costo_unitario' => $costo, 'costo_promedio' => $promedio,
        ];
    }
}
