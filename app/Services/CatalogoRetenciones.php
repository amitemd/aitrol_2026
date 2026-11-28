<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CatalogoRetenciones
{
    public function vigentes(string $tipo, string $fecha): Builder
    {
        return DB::table('sri_codigos_retencion')->where('tipo', $tipo)->where('vigente_desde', '<=', $fecha)
            ->where(fn (Builder $consulta) => $consulta->whereNull('vigente_hasta')->orWhere('vigente_hasta', '>=', $fecha));
    }

    public function verificar(array $lineas, string $fecha): void
    {
        $vigentes = collect(['renta', 'iva'])->mapWithKeys(fn (string $tipo) => [
            $tipo => $this->vigentes($tipo, $fecha)->whereIn('codigo', array_column($lineas, 'codigo_retencion'))->pluck('porcentaje', 'codigo'),
        ]);
        foreach ($lineas as $indice => $linea) {
            $tasa = $vigentes->get($linea['tipo_retencion'])?->get($linea['codigo_retencion']);
            if ($tasa !== null && (float) $tasa !== (float) $linea['porcentaje_retencion']) {
                throw ValidationException::withMessages([
                    'lineas.'.$indice.'.porcentaje_retencion' => 'Para la fecha indicada, el código '.$linea['codigo_retencion'].' tiene una retención de '.$tasa.' %.',
                ]);
            }
        }
    }
}
