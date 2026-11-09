<?php

namespace Sis_medico\Services;

use Sis_medico\CamaTransaccion;
use Sis_medico\ClinicalEncounter;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Piso;

class HospitalBedBoardService
{
    /**
     * Carga el tablero completo con un numero fijo de consultas.
     * Ninguna vista debe consultar la base mientras se renderiza.
     */
    public function data()
    {
        $destinations = app(ClinicalHospitalDestinationService::class);
        $visibleRooms = function ($query) {
            $query->where('estado', '<>', 4)
                ->where(function ($rooms) {
                    $rooms->whereHas('cama', function ($beds) {
                        $beds->where('estado', '<>', 0);
                    })->orWhereHas('tipo', function ($types) {
                        $types->whereIn('categoria', ['quirofano', 'consultorio']);
                    });
                });
        };
        $pisos = Piso::with(['habitacion' => function ($query) use ($visibleRooms) {
            $visibleRooms($query);
            $query->with(['tipo', 'cama' => function ($beds) {
                $beds->where('estado', '<>', 0);
            }]);
        }])->whereHas('habitacion', $visibleRooms)->orderBy('id')->get();

        $bedIds = array();
        foreach ($pisos as $piso) {
            foreach ($piso->habitacion as $habitacion) {
                foreach ($habitacion->cama as $cama) {
                    $bedIds[] = $cama->id;
                }
            }
        }

        $activeByBed = collect();
        $latestBedTransactions = collect();
        $transferIds = array();
        if (!empty($bedIds)) {
            $latestBedTransactions = CamaTransaccion::whereIn('id_cama', $bedIds)
                ->whereIn('id', function ($query) use ($bedIds) {
                    $query->selectRaw('MAX(id)')
                        ->from('cama_transaccion')
                        ->whereIn('id_cama', $bedIds)
                        ->groupBy('id_cama');
                })
                ->get()
                ->keyBy('id_cama');

            $hospitalizaciones = Ho_Hospitalizacion::where('estado', 1)
                ->whereIn('id_cama', $bedIds)
                ->whereIn('id', Ho_Hospitalizacion::where('estado', 1)->whereIn('id_cama', $bedIds)
                    ->selectRaw('MAX(id)')->groupBy('id_cama'))
                ->get();

            foreach ($hospitalizaciones as $hospitalizacion) {
                if (!$activeByBed->has($hospitalizacion->id_cama)) {
                    $activeByBed->put($hospitalizacion->id_cama, $hospitalizacion);
                }
                if (!empty($hospitalizacion->id_traspaso)) {
                    $transferIds[] = $hospitalizacion->id_traspaso;
                }
            }
        }

        $transfersById = collect();
        if (!empty($transferIds)) {
            $transfersById = Ho_Traspaso_Sala008::query()
                ->whereIn('id', array_unique($transferIds))
                ->get()
                ->keyBy('id');
        }

        $destinationRooms = $destinations->salasQuery([3, 6, 7]);
        $enespera = Ho_Traspaso_Sala008::query()
            ->where('estado', 1)
            ->whereIn('paso', [3, 6, 7])
            ->whereIn('id_sala', $destinationRooms ? $destinationRooms->select('sala.id') : [])
            ->whereHas('solicitud', function ($query) use ($destinations) {
                $query->where('estado', 1)->whereIn('encounter_id', ClinicalEncounter::where('id_empresa', $destinations->companyId())
                    ->whereNull('ended_at')->select('id'));
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Un solo lote de pacientes, salas y médicos para camas ocupadas y sala de espera.
        $allTransfers = $enespera->merge($transfersById->values())
            ->load(['paciente', 'sala', 'doctor'])->keyBy('id');
        $transfersById = $allTransfers->only($transferIds)->keyBy('id');
        $enespera = $enespera->map(function ($transfer) use ($allTransfers) {
            return $allTransfers->get($transfer->id);
        });

        $waitingByTransfer = collect();
        $waitingTransferIds = $enespera->pluck('id')->toArray();
        if (!empty($waitingTransferIds)) {
            $waitingHospitalizations = Ho_Hospitalizacion::whereIn('id_traspaso', $waitingTransferIds)
                ->whereIn('id', Ho_Hospitalizacion::whereIn('id_traspaso', $waitingTransferIds)
                    ->selectRaw('MAX(id)')->groupBy('id_traspaso'))
                ->get();
            foreach ($waitingHospitalizations as $hospitalizacion) {
                if (!$waitingByTransfer->has($hospitalizacion->id_traspaso)) {
                    $waitingByTransfer->put($hospitalizacion->id_traspaso, $hospitalizacion);
                }
            }
        }

        return array(
            'pisos' => $pisos,
            'enespera' => $enespera,
            'activeHospitalizationsByBed' => $activeByBed,
            'latestBedTransactions' => $latestBedTransactions,
            'transfersById' => $transfersById,
            'waitingHospitalizationsByTransfer' => $waitingByTransfer,
            'boardGeneratedAt' => date('Y-m-d H:i:s'),
        );
    }
}
