<?php

namespace Aitrol\Contabilidad\Domain;

final class PartidaDoble
{
    /**
     * Valida la partida doble con importes enteros en centavos.
     *
     * @return array<string, string>
     */
    public static function errores(array $lineas, array $cuentas, ?string $flujo): array
    {
        $errores = [];
        if (count(array_unique(array_column($lineas, 'id_cuenta'))) < 2) {
            $errores['lineas'] = 'Un asiento debe afectar al menos dos cuentas distintas.';
        }
        $efectivo = 0;
        $cantidadEfectivo = 0;
        foreach ($lineas as $indice => $linea) {
            $cuenta = $cuentas[$linea['id_cuenta']] ?? null;
            if ($cuenta === null) {
                $errores['lineas.'.$indice.'.id_cuenta'] = 'Selecciona una cuenta auxiliar activa de tu empresa.';
                continue;
            }
            if (($linea['debe'] > 0) === ($linea['haber'] > 0)) {
                $errores['lineas.'.$indice.'.debe'] = 'Cada línea debe tener un importe positivo en Debe o en Haber.';
            }
            if ($flujo === 'apertura' && in_array($cuenta->clase, ['ingreso', 'gasto'], true)) {
                $errores['flujo'] = 'Una apertura solo puede usar cuentas de activo, pasivo y patrimonio.';
            }
            if ($cuenta->efectivo) {
                $efectivo += $linea['debe'] - $linea['haber'];
                $cantidadEfectivo++;
            }
            if ($cuenta->clase !== 'patrimonio' && $linea['movimiento_patrimonio'] !== null) {
                $errores['lineas.'.$indice.'.movimiento_patrimonio'] = 'La clasificación solo corresponde a cuentas de patrimonio.';
            }
        }
        if (array_sum(array_column($lineas, 'debe')) !== array_sum(array_column($lineas, 'haber'))) {
            $errores['lineas'] ??= 'El total del Debe debe ser igual al total del Haber.';
        }
        if ($cantidadEfectivo === 0 && $flujo !== null && $flujo !== 'apertura') {
            $errores['flujo'] ??= 'Solo clasifica el flujo cuando el asiento afecta efectivo.';
        } elseif ($cantidadEfectivo > 0 && $efectivo === 0 && ($flujo !== 'traslado' || $cantidadEfectivo !== count($lineas))) {
            $errores['flujo'] ??= 'Separa los movimientos compensados. Un traslado interno solo usa cuentas de efectivo y su flujo neto es cero.';
        } elseif ($efectivo !== 0 && ($flujo === null || $flujo === 'traslado')) {
            $errores['flujo'] ??= 'Clasifica el movimiento de efectivo como operación, inversión, financiación o apertura.';
        }

        return $errores;
    }
}
