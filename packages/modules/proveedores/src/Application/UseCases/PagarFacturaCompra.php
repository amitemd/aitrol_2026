<?php

namespace Aitrol\Proveedores\Application\UseCases;

use Aitrol\CajaBancos\Application\LibroCaja;
use Aitrol\Inventarios\Domain\CantidadInventario;
use Aitrol\Proveedores\Infrastructure\Persistence\CarteraProveedores;
use App\Services\EmpresaActiva;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

final readonly class PagarFacturaCompra
{
    public function __construct(private EmpresaActiva $empresa, private CarteraProveedores $cartera, private LibroCaja $caja) {}

    /** @return array{0: stdClass, 1: bool} */
    public function registrar(int $id, array $datos): array
    {
        return $this->empresa->transaccion(function () use ($id, $datos): array {
            $hoy = CarbonImmutable::today('America/Guayaquil');
            $factura = $this->cartera->factura($id, $hoy);
            if ($datos['fecha'] < $factura->fecha) {
                throw ValidationException::withMessages(['fecha' => 'La fecha del pago debe ser igual o posterior a la factura.']);
            }

            $claveExistente = DB::table('cb_operaciones')->where('id_empresa', $this->empresa->id())
                ->where('clave_operacion', strtolower($datos['clave_operacion']))->exists();
            $importe = CantidadInventario::unidades($datos['importe'], 2);
            $saldoAlPago = $this->cartera->factura($id, CarbonImmutable::parse($datos['fecha'], 'America/Guayaquil'))->saldo;
            if (! $claveExistente && $importe > min($factura->saldo, $saldoAlPago)) {
                throw ValidationException::withMessages(['importe' => 'El pago supera el saldo pendiente de la factura.']);
            }

            return $this->caja->registrar('egreso', $datos + [
                'id_factura_compra' => $id,
                'concepto' => 'Pago a proveedor · '.$factura->numero,
            ]);
        });
    }
}
