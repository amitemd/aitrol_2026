<?php

namespace Aitrol\Contabilidad\Infrastructure\Queries;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class ReferenciaAsiento
{
    public static function agregar(Builder $consulta, string $origen, string $columna, string $prefijo = 'asiento'): Builder
    {
        $tabla = explode('.', $columna)[0];
        $asiento = DB::table('co_asientos as contable')->whereColumn('contable.id_empresa', $tabla.'.id_empresa')
            ->where('contable.origen', $origen)->whereColumn('contable.id_origen', $columna);

        return self::proyectar($consulta, $asiento, $prefijo);
    }

    public static function inventario(Builder $consulta, string $columna): Builder
    {
        $tabla = explode('.', $columna)[0];
        $asiento = DB::table('co_asientos as contable')->whereColumn('contable.id_empresa', $tabla.'.id_empresa')
            ->where(function (Builder $q) use ($columna): void {
                $q->where(fn (Builder $directo) => $directo->where('contable.origen', 'inventario.movimiento')->whereColumn('contable.id_origen', $columna))
                    ->orWhereExists(function (Builder $comercial) use ($columna): void {
                        $comercial->from('com_lineas as partida')->selectRaw('1')
                            ->whereColumn('partida.id_empresa', 'contable.id_empresa')->whereColumn('partida.id_documento', 'contable.id_origen')
                            ->where(fn (Builder $vinculo) => $vinculo
                                ->where(fn (Builder $normal) => $normal->whereIn('contable.origen', ['comercial.documento', 'comercial.recepcion'])
                                    ->whereColumn('partida.id_operacion', $columna))
                                ->orWhere(fn (Builder $reversa) => $reversa->whereIn('contable.origen', ['comercial.anulacion', 'comercial.anulacion-recepcion'])
                                    ->whereColumn('partida.id_operacion_reversa', $columna)));
                    });
            });

        return self::proyectar($consulta, $asiento, 'asiento');
    }

    private static function proyectar(Builder $consulta, Builder $asiento, string $prefijo): Builder
    {
        return $consulta
            ->selectSub((clone $asiento)->select('contable.id')->orderBy('contable.id')->limit(1), 'id_'.$prefijo)
            ->selectSub($asiento->select('contable.numero')->orderBy('contable.id')->limit(1), 'numero_'.$prefijo);
    }
}
