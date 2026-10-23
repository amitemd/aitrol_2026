<?php

namespace Sis_medico\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Sis_medico\Bodega;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\EntregaMercaderia;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\Log_Contable;
use Sis_medico\Plan_Cuentas;

class EntregaMercaderiaService
{
    public function descargarInventario(EntregaMercaderia $entrega, array $lineas, $usuario, $ip)
    {
        if ($entrega->id_movimiento_inventario) {
            throw new Exception('La entrega ya fue descargada del inventario.');
        }

        $documento = InvDocumentosBodegas::where('abreviatura_documento', 'EGV')
            ->where('id_empresa', $entrega->id_empresa)->first();
        if (!$documento) {
            throw new Exception('Configure el documento de bodega EGV antes de registrar entregas.');
        }

        $costoTotal = 0;
        $movimientos = [];
        $grupos = collect($lineas)->groupBy('id_bodega_inventario');
        foreach ($grupos as $idBodegaInventario => $lineasBodega) {
            $bodega = Bodega::where('id', $idBodegaInventario)->where('estado', 1)->first();
            if (!$bodega) {
                throw new Exception('La bodega física seleccionada no existe o está inactiva.');
            }
            $idMovimiento = $this->crearMovimiento($entrega, $documento, $bodega->id, $usuario, $ip);
            $costoMovimiento = 0;

            foreach ($lineasBodega as $linea) {
                $producto = Ct_productos::where('id', $linea['id_producto'])
                    ->where('id_empresa', $entrega->id_empresa)->first();
                if (!$producto || (int) $producto->tipo_producto === 0) {
                    throw new Exception('Uno de los productos no pertenece a la empresa o no maneja inventario.');
                }
                $idsInsumos = Ct_productos_insumos::where('id_producto', $producto->id)
                    ->pluck('id_insumo')->filter()->unique()->values()->all();
                if (!count($idsInsumos)) {
                    throw new Exception('El producto ' . $producto->nombre . ' no está ligado al inventario de compras.');
                }

                $cantidad = (int) $linea['cantidad'];
                $serie = InvInventarioSerie::where('id', $linea['id_serie'])
                    ->whereIn('id_producto', $idsInsumos)
                    ->where('id_bodega', $bodega->id)
                    ->where(function ($query) use ($entrega) {
                        $query->where('id_empresa', $entrega->id_empresa)->orWhereNull('id_empresa');
                    })
                    ->where(function ($query) {
                        $query->where('estado', 1)->orWhereNull('estado');
                    })->where('existencia', '>', 0)
                    ->lockForUpdate()->first();
                if (!$serie) {
                    throw new Exception('La serie o lote seleccionado para ' . $producto->nombre . ' ya no está disponible en esa bodega.');
                }
                $existenciaAntes = (int) $serie->existencia;
                if ($existenciaAntes < $cantidad) {
                    throw new Exception('Stock insuficiente en la serie/lote de ' . $producto->nombre . '. Disponible: ' . $existenciaAntes . '.');
                }

                $inventario = InvInventario::where('id', $serie->id_inv_inventario)
                    ->where('id_producto', $serie->id_producto)
                    ->where('id_bodega', $bodega->id)
                    ->where(function ($query) use ($entrega) {
                        $query->where('id_empresa', $entrega->id_empresa)->orWhereNull('id_empresa');
                    })
                    ->lockForUpdate()->first();
                if (!$inventario) {
                    throw new Exception('La serie ' . $serie->serie . ' no tiene cabecera de inventario.');
                }
                if ((int) $inventario->existencia < $cantidad) {
                    throw new Exception('La cabecera de inventario de ' . $producto->nombre . ' no tiene existencia suficiente. Disponible: ' . (int) $inventario->existencia . '.');
                }
                $costo = (float) $inventario->costo_promedio;
                $subtotal = $cantidad * $costo;

                $idDetalleMovimiento = InvDetMovimientos::insertGetId([
                    'id_inv_cab_movimientos' => $idMovimiento,
                    'id_producto' => $serie->id_producto,
                    'serie' => $serie->serie,
                    'fecha_vence' => $serie->fecha_vence,
                    'lote' => $serie->lote,
                    'id_inv_inventario' => $inventario->id,
                    'cantidad' => $cantidad,
                    'cant_uso' => $cantidad,
                    'valor_unitario' => $costo,
                    'subtotal' => $subtotal,
                    'descuento' => 0,
                    'iva' => 0,
                    'total' => $subtotal,
                    'estado' => 1,
                    'kardex' => 1,
                    'motivo' => 'ENTREGA ' . $entrega->numero,
                    'id_usuariocrea' => $usuario,
                    'id_usuariomod' => $usuario,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ]);

                $inventario->existencia = max(0, (int) $inventario->existencia - $cantidad);
                $inventario->existencia_uso = max(0, (int) $inventario->existencia_uso - $cantidad);
                $inventario->id_usuariomod = $usuario;
                $inventario->ip_modificacion = $ip;
                $inventario->save();

                $serie->existencia = max(0, (int) $serie->existencia - $cantidad);
                $serie->existencia_uso = max(0, (int) $serie->existencia_uso - $cantidad);
                $serie->id_usuariomod = $usuario;
                $serie->ip_modificacion = $ip;
                $serie->save();

                InvKardex::create([
                    'id_inv_inventario' => $inventario->id,
                    'id_bodega' => $bodega->id,
                    'id_producto' => $serie->id_producto,
                    'tipo' => 'E',
                    'descripcion' => 'EGV-ENTREGA',
                    'referencia' => $entrega->numero,
                    'fecha' => $entrega->fecha_entrega,
                    'cantidad' => $cantidad,
                    'cant_uso' => $cantidad,
                    'valor_unitario' => $costo,
                    'total' => $subtotal,
                    'exist_cant' => $inventario->existencia,
                    'exist_uso' => $inventario->existencia_uso,
                    'estado' => 1,
                    'id_documento_bodega' => $documento->id,
                    'id_inv_det_movimientos' => $idDetalleMovimiento,
                    'id_empresa' => $entrega->id_empresa,
                    'id_usuariocrea' => $usuario,
                    'id_usuariomod' => $usuario,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ]);

                $costoMovimiento += $subtotal;
                $costoTotal += $subtotal;

                DB::table('ct_entregas_mercaderia_detalle')->where('id', $linea['id_detalle'])
                    ->where('id_entrega', $entrega->id)->update([
                        'id_bodega_contable' => isset($linea['id_bodega_contable']) ? $linea['id_bodega_contable'] : null,
                        'id_bodega_inventario' => $bodega->id,
                        'id_inv_inventario_serie' => $serie->id,
                        'serie' => $serie->serie,
                        'lote' => $serie->lote,
                        'fecha_vence' => $serie->fecha_vence,
                        'existencia_antes' => $existenciaAntes,
                        'costo_unitario' => $costo,
                        'costo_total' => $subtotal,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            InvCabMovimientos::where('id', $idMovimiento)->update([
                'subtotal' => $costoMovimiento,
                'subtotal_0' => $costoMovimiento,
                'total' => $costoMovimiento,
                'id_usuariomod' => $usuario,
                'ip_modificacion' => $ip,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            DB::table('ct_entregas_mercaderia_movimientos')->insert([
                'id_entrega' => $entrega->id,
                'id_bodega_contable' => isset($lineasBodega->first()['id_bodega_contable']) ? $lineasBodega->first()['id_bodega_contable'] : 0,
                'id_bodega_inventario' => $bodega->id,
                'id_movimiento_inventario' => $idMovimiento,
                'costo_total' => $costoMovimiento,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $movimientos[] = $idMovimiento;
        }

        $idAsiento = $this->crearAsientoCosto($entrega, $costoTotal, $usuario, $ip);
        if ($idAsiento && count($movimientos)) {
            InvCabMovimientos::whereIn('id', $movimientos)->update([
                'id_asiento' => $idAsiento,
                'id_usuariomod' => $usuario,
                'ip_modificacion' => $ip,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $entrega->id_movimiento_inventario = reset($movimientos);
        $entrega->id_asiento_costo = $idAsiento;
        $entrega->save();

        return ['movimiento' => implode(',', $movimientos), 'movimientos' => $movimientos, 'asiento' => $idAsiento, 'costo' => $costoTotal];
    }

    private function crearMovimiento(EntregaMercaderia $entrega, $documento, $idBodegaInventario, $usuario, $ip)
    {
        $ultima = InvCabMovimientos::where('id_empresa', $entrega->id_empresa)->lockForUpdate()->latest()->first();
        $numero = str_pad($ultima ? ((int) $ultima->numero_documento + 1) : 1, 9, '0', STR_PAD_LEFT);
        $transaccion = InvTransaccionesBodegas::insertGetId([
            'id_documento_bodega' => $documento->id,
            'id_bodega' => $idBodegaInventario,
            'secuencia' => 100,
            'id_empresa' => $entrega->id_empresa,
            'id_usuariocrea' => $usuario,
            'id_usuariomod' => $usuario,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
        ]);

        return InvCabMovimientos::insertGetId([
            'id_documento_bodega' => $documento->id,
            'id_transaccion_bodega' => $transaccion,
            'id_bodega_origen' => $idBodegaInventario,
            'numero_documento' => $numero,
            'observacion' => 'ENTREGA DE MERCADERIA ' . $entrega->numero,
            'fecha' => $entrega->fecha_entrega,
            'descuento' => 0,
            'subtotal' => 0,
            'subtotal_0' => 0,
            'iva' => 0,
            'total' => 0,
            'estado' => 1,
            'id_empresa' => $entrega->id_empresa,
            'id_usuariocrea' => $usuario,
            'id_usuariomod' => $usuario,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
        ]);
    }

    public function revertirInventario(EntregaMercaderia $entrega, $usuario, $ip, $motivo)
    {
        if (!$entrega->id_movimiento_inventario) {
            throw new Exception('La entrega no tiene un movimiento de inventario para revertir.');
        }

        $idsMovimientos = DB::table('ct_entregas_mercaderia_movimientos')
            ->where('id_entrega', $entrega->id)->pluck('id_movimiento_inventario')->all();
        if (!count($idsMovimientos)) {
            $idsMovimientos = [$entrega->id_movimiento_inventario];
        }
        $revertidos = [];
        foreach ($idsMovimientos as $idMovimiento) {
            $movimiento = InvCabMovimientos::where('id', $idMovimiento)
                ->where('id_empresa', $entrega->id_empresa)->lockForUpdate()->first();
            if (!$movimiento || (int) $movimiento->estado !== 1) {
                throw new Exception('Uno de los movimientos de inventario ya está anulado o no se encuentra activo.');
            }

            $detalles = InvDetMovimientos::where('id_inv_cab_movimientos', $movimiento->id)
                ->where('estado', 1)->lockForUpdate()->get();
            if ($detalles->isEmpty()) {
                throw new Exception('Uno de los movimientos no tiene detalles activos para revertir.');
            }

            foreach ($detalles as $detalle) {
            $inventario = InvInventario::where('id', $detalle->id_inv_inventario)
                ->where('id_empresa', $entrega->id_empresa)
                ->lockForUpdate()->first();
            if (!$inventario) {
                throw new Exception('No se encontró el inventario asociado al detalle #' . $detalle->id . '.');
            }

            $consultaSerie = InvInventarioSerie::where('id_inv_inventario', $detalle->id_inv_inventario)
                ->where('id_producto', $detalle->id_producto)
                ->where('id_empresa', $entrega->id_empresa)
                ->where('estado', 1);
            $consultaSerie->where('serie', $detalle->serie)
                ->where('lote', $detalle->lote)
                ->where('fecha_vence', $detalle->fecha_vence);
            $serie = $consultaSerie->lockForUpdate()->first();
            if (!$serie) {
                throw new Exception('No se encontró la serie o lote asociado al detalle #' . $detalle->id . '.');
            }
            $kardex = InvKardex::where('id_inv_det_movimientos', $detalle->id)
                ->where('estado', 1)->lockForUpdate()->first();
            if (!$kardex) {
                throw new Exception('No se encontró el kárdex activo asociado al detalle #' . $detalle->id . '.');
            }

            $inventario->existencia = (int) $inventario->existencia + (int) $detalle->cantidad;
            $inventario->existencia_uso = (int) $inventario->existencia_uso + (int) $detalle->cant_uso;
            $inventario->id_usuariomod = $usuario;
            $inventario->ip_modificacion = $ip;
            $inventario->save();

            $serie->existencia = (int) $serie->existencia + (int) $detalle->cantidad;
            $serie->existencia_uso = (int) $serie->existencia_uso + (int) $detalle->cant_uso;
            $serie->id_usuariomod = $usuario;
            $serie->ip_modificacion = $ip;
            $serie->save();

            $kardex->estado = 0;
            $kardex->id_usuariomod = $usuario;
            $kardex->ip_modificacion = $ip;
            $kardex->save();

            $detalle->estado = 0;
            $detalle->id_usuariomod = $usuario;
            $detalle->ip_modificacion = $ip;
            $detalle->save();
            }

            $movimiento->estado = 0;
            $movimiento->observacion = $movimiento->observacion . ' | ANULADO: ' . $motivo;
            $movimiento->id_usuariomod = $usuario;
            $movimiento->ip_modificacion = $ip;
            $movimiento->save();
            $revertidos[] = $movimiento->id;
        }

        return [
            'movimiento' => implode(',', $revertidos),
            'asiento_reversion' => $this->revertirAsientoCosto($entrega, $usuario, $ip, $motivo),
        ];
    }

    private function crearAsientoCosto(EntregaMercaderia $entrega, $total, $usuario, $ip)
    {
        if ($total <= 0) {
            return null;
        }
        $debe = Ct_Configuraciones::where('id_empresa', $entrega->id_empresa)
            ->where('tipo', 'COSTO_DEBE_PROCEDIMIENTO')->first();
        $haber = Ct_Configuraciones::where('id_empresa', $entrega->id_empresa)
            ->where('tipo', 'COSTO_HABER_PROCEDIMIENTO')->first();
        if (!$debe || !$haber) {
            throw new Exception('Configure las cuentas COSTO_DEBE_PROCEDIMIENTO y COSTO_HABER_PROCEDIMIENTO.');
        }

        $asiento = Ct_Asientos_Cabecera::create([
            'observacion' => 'COSTO ENTREGA DE MERCADERIA: ' . $entrega->numero,
            'fecha_asiento' => $entrega->fecha_entrega,
            'valor' => $total,
            'estado' => 1,
            'id_empresa' => $entrega->id_empresa,
            'modulo' => 'ENTREGA_MERCADERIA',
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $usuario,
            'id_usuariomod' => $usuario,
        ]);
        foreach ([[$debe->id_plan, $total, 0], [$haber->id_plan, 0, $total]] as $linea) {
            $plan = Plan_Cuentas::find($linea[0]);
            if (!$plan) {
                throw new Exception('Una cuenta configurada para costo no existe en el plan contable.');
            }
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $asiento->id,
                'fecha' => $entrega->fecha_entrega,
                'id_plan_cuenta' => $plan->id,
                'descripcion' => $plan->nombre,
                'debe' => $linea[1],
                'haber' => $linea[2],
                'estado' => 1,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $usuario,
                'id_usuariomod' => $usuario,
            ]);
        }
        return $asiento->id;
    }

    private function revertirAsientoCosto(EntregaMercaderia $entrega, $usuario, $ip, $motivo)
    {
        if (!$entrega->id_asiento_costo) {
            return null;
        }

        $original = Ct_Asientos_Cabecera::where('id', $entrega->id_asiento_costo)
            ->where('id_empresa', $entrega->id_empresa)->lockForUpdate()->first();
        if (!$original) {
            throw new Exception('No se encontró el asiento de costo de la entrega.');
        }

        $detalles = Ct_Asientos_Detalle::where('id_asiento_cabecera', $original->id)
            ->where('estado', 1)->get();
        if ($detalles->isEmpty()) {
            throw new Exception('El asiento de costo no tiene detalles activos para revertir.');
        }

        $reversion = Ct_Asientos_Cabecera::create([
            'observacion' => 'REVERSIÓN COSTO ENTREGA ' . $entrega->numero . ': ' . $motivo,
            'fecha_asiento' => date('Y-m-d'),
            'valor' => $original->valor,
            'estado' => 1,
            'id_empresa' => $entrega->id_empresa,
            'modulo' => 'ENTREGA_MERCADERIA',
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $usuario,
            'id_usuariomod' => $usuario,
        ]);

        foreach ($detalles as $detalle) {
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $reversion->id,
                'fecha' => date('Y-m-d'),
                'id_plan_cuenta' => $detalle->id_plan_cuenta,
                'descripcion' => 'REVERSIÓN - ' . $detalle->descripcion,
                'debe' => $detalle->haber,
                'haber' => $detalle->debe,
                'estado' => 1,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $usuario,
                'id_usuariomod' => $usuario,
            ]);
        }

        Log_Contable::create([
            'tipo' => 'EM',
            'valor_ant' => $original->valor,
            'valor' => $original->valor,
            'id_usuariocrea' => $usuario,
            'id_usuariomod' => $usuario,
            'observacion' => 'Anulación de entrega ' . $entrega->numero . ': ' . $motivo,
            'id_ant' => $original->id,
            'id_referencia' => $reversion->id,
        ]);

        return $reversion->id;
    }
}
