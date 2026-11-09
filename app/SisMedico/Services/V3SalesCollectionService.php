<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\v3\contable\AsientosDetalles;
use Sis_medico\v3\contable\Bancos;
use Sis_medico\v3\contable\CajaBanco;
use Sis_medico\v3\contable\Globales;
use Sis_medico\v3\contable\Plan_Cuentas;

class V3SalesCollectionService
{
    const PAGO_PENDIENTE = 7;

    public function registrar($idAsiento, array $datos, $request)
    {
        $pagos = $this->normalizarPagos($request);
        if (empty($pagos)) {
            throw new \InvalidArgumentException('El asiento de cobro no contiene formas de pago aplicadas.');
        }

        $idEmpresa = $datos['id_empresa'];
        $cuentaCobrar = Globales::cuentaVenta(Globales::VENTA_CUENTAS_COBRAR, $idEmpresa);
        if (!$cuentaCobrar) {
            throw new \RuntimeException('CONFIG_CONTABLE: Configure la cuenta por cobrar a clientes para Ventas.');
        }

        $metodos = array_values(array_unique(array_column($pagos, 'metodo')));
        $cuentas = CajaBanco::where('id_empresa', $idEmpresa)
            ->where('estado', 1)
            ->get();
        $formasPorCaja = CajaBanco::getFormasPagoPorCajas($cuentas->pluck('id')->all());
        $cuentasCobro = collect();
        foreach ($cuentas as $cuenta) {
            $formasCuenta = isset($formasPorCaja[(int) $cuenta->id])
                ? $formasPorCaja[(int) $cuenta->id]
                : array((int) $cuenta->formas_pago);
            foreach (array_unique($formasCuenta) as $formaCuenta) {
                if (!in_array((int) $formaCuenta, $metodos, true)) continue;
                $clave = (string) $formaCuenta;
                if (!$cuentasCobro->has($clave)) $cuentasCobro->put($clave, collect());
                $cuentasCobro->get($clave)->push($cuenta);
            }
        }

        $bancosSeleccionados = array_values(array_unique(array_filter(array_column($pagos, 'banco'))));
        $bancos = empty($bancosSeleccionados)
            ? collect()
            : Bancos::where('estado', 1)->whereIn('id', $bancosSeleccionados)->get()->keyBy('id');

        $cuentasResueltas = array();
        foreach ($pagos as $pago) {
            $cuenta = $this->resolverCuenta($pago, $cuentasCobro, $bancos, $idEmpresa);
            $idCuenta = (string) $cuenta->cuenta_mayor;
            if (!isset($cuentasResueltas[$idCuenta])) {
                $cuentasResueltas[$idCuenta] = array('valor' => 0, 'cuenta' => $cuenta);
            }
            $cuentasResueltas[$idCuenta]['valor'] += $pago['valor'];
        }

        $planes = Plan_Cuentas::whereIn('id', array_keys($cuentasResueltas))->get()->keyBy('id');
        $totalCobrado = 0;
        foreach ($cuentasResueltas as $idCuenta => $resumen) {
            $plan = $planes->get($idCuenta);
            if (!$plan) {
                throw new \RuntimeException('CONFIG_CONTABLE: La cuenta de cobro configurada no existe en el plan de cuentas.');
            }
            $valor = round((float) $resumen['valor'], 2);
            $totalCobrado += $valor;
            AsientosDetalles::insert($this->detalle($idAsiento, $plan->id, $plan->nombre, $datos, $valor, 0));
        }

        $totalCobrado = round($totalCobrado, 2);
        if ($totalCobrado <= 0 || abs($totalCobrado - round((float) $datos['valor'], 2)) > 0.009) {
            throw new \RuntimeException('El valor del asiento de cobro no coincide con las formas de pago aplicadas.');
        }

        AsientosDetalles::insert($this->detalle(
            $idAsiento,
            $cuentaCobrar->id_plan,
            $cuentaCobrar->nombre,
            $datos,
            0,
            $totalCobrado
        ));
    }

    private function normalizarPagos($request)
    {
        $metodos = (array) $request->input('id_pago', array());
        $valores = (array) $request->input('valor', array());
        $bancos = (array) $request->input('id_bancos', array());
        $cuentas = (array) $request->input('cuenta', array());
        $pagos = array();

        foreach ($metodos as $indice => $metodo) {
            if ((int) $metodo === self::PAGO_PENDIENTE) {
                continue;
            }
            $valor = isset($valores[$indice]) ? round((float) $valores[$indice], 2) : 0;
            if ($valor <= 0) {
                throw new \InvalidArgumentException('Cada forma de pago aplicada debe tener un valor mayor a cero.');
            }
            $pagos[] = array(
                'metodo' => (int) $metodo,
                'valor' => $valor,
                'banco' => isset($bancos[$indice]) ? trim((string) $bancos[$indice]) : '',
                'numero_cuenta' => isset($cuentas[$indice]) ? trim((string) $cuentas[$indice]) : '',
            );
        }

        return $pagos;
    }

    private function resolverCuenta(array $pago, $cuentasCobro, $bancos, $idEmpresa)
    {
        $candidatas = $cuentasCobro->get((string) $pago['metodo'], collect())->values();
        if ($candidatas->count() === 1) {
            return $candidatas->first();
        }

        if ($candidatas->count() > 1 && $pago['numero_cuenta'] !== '') {
            $porNumero = $candidatas->filter(function ($cuenta) use ($pago) {
                return trim((string) $cuenta->numero_cuenta) === $pago['numero_cuenta'];
            })->values();
            if ($porNumero->count() === 1) {
                return $porNumero->first();
            }
        }

        if ($candidatas->count() > 1 && $pago['banco'] !== '') {
            $banco = $bancos->get($pago['banco']);
            if ($banco) {
                $nombreBanco = $this->normalizarTexto($banco->nombre);
                $porBanco = $candidatas->filter(function ($cuenta) use ($nombreBanco) {
                    return $nombreBanco !== '' && strpos($this->normalizarTexto($cuenta->nombre), $nombreBanco) !== false;
                })->values();
                if ($porBanco->count() === 1) {
                    return $porBanco->first();
                }
            }
        }

        $metodo = Ct_Tipo_Pago::query()->find($pago['metodo']);
        $nombreMetodo = $metodo && trim((string) $metodo->nombre) !== ''
            ? $metodo->nombre
            : ('ID ' . $pago['metodo']);
        if ($candidatas->isEmpty()) {
            // La caja o cuenta bancaria individual tiene prioridad. Una cuenta
            // base solo puede recibir el movimiento si tambien es de detalle;
            // nunca se contabiliza directamente contra una cuenta de grupo.
            $tipoCuentaBase = $this->tipoCuentaBase($metodo);
            if ($tipoCuentaBase !== null) {
                $cuentaBase = $this->cuentaBaseEmpresa($idEmpresa, $tipoCuentaBase);
                if ($cuentaBase && (int) $cuentaBase->estado_cuenta === 2) {
                    return $cuentaBase;
                }
                if ($cuentaBase) {
                    throw new \RuntimeException(
                        'CONFIG_CONTABLE: La cuenta base para ' . $nombreMetodo
                        . ' ya esta configurada, pero es una cuenta de grupo. Cree una caja operativa activa, '
                        . 'seleccione una cuenta de detalle y vinculela con esta forma de pago.'
                    );
                }

                $nombreBase = $tipoCuentaBase === 'CAJA_BAN_CAJA' ? 'Caja' : 'Bancos';
                throw new \RuntimeException(
                    'CONFIG_CONTABLE: Configure la cuenta base de ' . $nombreBase
                    . ' para la empresa activa antes de registrar la forma de pago ' . $nombreMetodo . '.'
                );
            }

            throw new \RuntimeException(
                'CONFIG_CONTABLE: Configure una caja o cuenta bancaria activa para la forma de pago '
                . $nombreMetodo . ', o asigne la cuenta base correspondiente en Configuracion contable.'
            );
        }
        throw new \RuntimeException('CONFIG_CONTABLE: Existe mas de una cuenta para ' . $nombreMetodo . '. Indique el numero de cuenta o deje una sola cuenta predeterminada.');
    }

    private function tipoCuentaBase($metodo)
    {
        if (!$metodo) return null;

        $texto = $this->normalizarTexto(
            trim((string) $metodo->codigo) . ' ' . trim((string) $metodo->nombre)
        );

        if (strpos($texto, 'efectivo') !== false || preg_match('/(^|\s)(efe|ef)(\s|$)/', $texto)) {
            return 'CAJA_BAN_CAJA';
        }

        $esBancario = (int) $metodo->requiere_banco === 1;
        foreach (array('banco', 'tarjeta', 'transfer', 'deposit', 'cheque', 'paypal', 'datalink') as $termino) {
            if (strpos($texto, $termino) !== false) {
                $esBancario = true;
                break;
            }
        }

        return $esBancario ? 'CAJA_BAN_BANCOS' : null;
    }

    private function cuentaBaseEmpresa($idEmpresa, $tipo)
    {
        $configuracion = DB::table('ct_configuraciones as configuracion')
            ->join('plan_cuentas_empresa as empresa_cuenta', function ($join) {
                $join->on('empresa_cuenta.id_plan', '=', 'configuracion.id_plan')
                    ->on('empresa_cuenta.id_empresa', '=', 'configuracion.id_empresa');
            })
            ->join('plan_cuentas as cuenta', 'cuenta.id', '=', 'configuracion.id_plan')
            ->where('configuracion.id_empresa', $idEmpresa)
            ->where('configuracion.tipo', $tipo)
            ->where('configuracion.estado', 1)
            ->whereNotNull('configuracion.id_plan')
            ->where('configuracion.id_plan', '<>', '')
            ->orderBy('configuracion.id', 'desc')
            ->select('configuracion.id_plan', 'cuenta.nombre', 'empresa_cuenta.estado as estado_cuenta')
            ->first();

        if (!$configuracion) return null;

        return (object) array(
            'cuenta_mayor' => $configuracion->id_plan,
            'nombre' => $configuracion->nombre,
            'estado_cuenta' => $configuracion->estado_cuenta,
        );
    }

    private function normalizarTexto($texto)
    {
        $texto = function_exists('mb_strtolower') ? mb_strtolower((string) $texto, 'UTF-8') : strtolower((string) $texto);
        return trim(str_replace(array('á', 'é', 'í', 'ó', 'ú', 'ñ'), array('a', 'e', 'i', 'o', 'u', 'n'), $texto));
    }

    private function detalle($idAsiento, $idPlan, $nombre, array $datos, $debe, $haber)
    {
        $ahora = date('Y-m-d H:i:s');
        return array(
            'id_asiento_cabecera' => $idAsiento,
            'id_plan_cuenta' => $idPlan,
            'descripcion' => $nombre,
            'fecha' => $datos['fecha_asiento'],
            'debe' => $debe,
            'haber' => $haber,
            'estado' => 1,
            'id_usuariocrea' => $datos['id_usuariocrea'],
            'id_usuariomod' => $datos['id_usuariomod'],
            'ip_creacion' => $datos['ip_creacion'],
            'ip_modificacion' => $datos['ip_modificacion'],
            'created_at' => $ahora,
            'updated_at' => $ahora,
        );
    }
}
