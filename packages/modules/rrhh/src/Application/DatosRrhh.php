<?php

namespace Aitrol\Rrhh\Application;

use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

final readonly class DatosRrhh
{
    public function __construct(private EmpresaActiva $empresa, private ContextoOperativo $contexto) {}

    public function tabla(string $tabla): Builder
    {
        $alcance = $this->alcance();

        return DB::table($tabla)
            ->where($tabla.'.id_empresa', $alcance['id_empresa'])
            ->where($tabla.'.id_sucursal', $alcance['id_sucursal']);
    }

    public function procedimientos(string $seccion): Builder
    {
        return $this->tabla('rh_procesos')->where('rh_procesos.seccion', $seccion);
    }

    public function codigoEmpleadoOcupado(string $codigo, ?int $excluir): bool
    {
        return DB::table('rh_empleados')->where('id_empresa', $this->empresa->id())
            ->where('codigo', $codigo)->when($excluir !== null, fn (Builder $q) => $q->where('id', '<>', $excluir))->exists();
    }

    public function registro(Builder $consulta, int $id): stdClass
    {
        return $consulta->where($consulta->from.'.id', $id)->first() ?? abort(404);
    }

    public function insertar(string $tabla, array $datos): int
    {
        return (int) DB::table($tabla)->insertGetId($this->alcance() + $datos + [
            'creado_por' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function transaccion(Closure $trabajo): mixed
    {
        return $this->empresa->transaccion($trabajo);
    }

    private function alcance(): array
    {
        return [
            'id_empresa' => $this->empresa->id(),
            'id_sucursal' => $this->contexto->sucursal()->id,
        ];
    }

    public static function centavos(int|float|string $importe): int
    {
        [$entero, $fraccion] = array_pad(explode('.', (string) $importe, 2), 2, '');

        return (int) $entero * 100 + (int) str_pad($fraccion, 2, '0');
    }

    public static function importe(int $centavos): string
    {
        return intdiv($centavos, 100).'.'.str_pad((string) ($centavos % 100), 2, '0', STR_PAD_LEFT);
    }
}
