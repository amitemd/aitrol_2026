@extends('layouts.app-template-h')
@section('content')

@php
  $estadoLabels = [
    'disponible' => 'Libre',
    'ocupada' => 'Ocupada',
    'preparacion' => 'Preparacion',
    'no_disponible' => 'No disponible',
  ];

  $estadoOrden = ['disponible', 'ocupada', 'preparacion', 'no_disponible'];

  $normalizarEstadoCama = function($cama, $hospitalizacion = null) use ($latestBedTransactions) {
    if (!is_null($hospitalizacion)) {
      return 'ocupada';
    }

    $estado = 'disponible';
    if (isset($cama->estado)) {
      if ($cama->estado == 2) {
        $estado = 'preparacion';
      } elseif ($cama->estado == 3) {
        $estado = 'ocupada';
      } elseif ($cama->estado == 4) {
        $estado = 'no_disponible';
      }
    }

    $transaccion = $latestBedTransactions->get($cama->id);
    if (!is_null($transaccion) && isset($transaccion->estado_cama)) {
      $texto = strtolower(trim($transaccion->estado_cama));
      $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', ' ' => '_', '-' => '_']);

      if (strpos($texto, 'ocup') !== false) {
        $estado = 'ocupada';
      } elseif (strpos($texto, 'prepar') !== false || strpos($texto, 'limpieza') !== false) {
        $estado = 'preparacion';
      } elseif (strpos($texto, 'bloq') !== false || strpos($texto, 'no') !== false || strpos($texto, 'manten') !== false) {
        $estado = 'no_disponible';
      } elseif (strpos($texto, 'disp') !== false || strpos($texto, 'libre') !== false) {
        $estado = 'disponible';
      }
    }

    return $estado;
  };

  $nombrePaciente = function($traspaso) {
    if (is_null($traspaso) || is_null($traspaso->paciente)) {
      return 'Paciente asignado';
    }

    $paciente = $traspaso->paciente;
    return trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2);
  };

  $stats = ['total' => 0, 'disponible' => 0, 'ocupada' => 0, 'preparacion' => 0, 'no_disponible' => 0];
  $spaceStats = ['quirofanos' => 0, 'consultorios' => 0];
  $floorStats = [];
  $roomsForMove = [];
  $bedsForTransfer = [];

  foreach ($pisos as $piso) {
    $etiquetaPiso = !empty($piso->nombre_piso) ? $piso->nombre_piso : 'Piso '.$piso->id;
    $floorStats[$piso->id] = ['total' => 0, 'disponible' => 0, 'ocupada' => 0, 'preparacion' => 0, 'no_disponible' => 0, 'quirofanos' => 0, 'consultorios' => 0];

    foreach ($piso->habitacion as $habitacion) {
      if ($habitacion->estado == 4) {
        continue;
      }

      $categoriaEspacio = !is_null($habitacion->tipo) && !empty($habitacion->tipo->categoria)
        ? $habitacion->tipo->categoria : 'habitacion';
      if ($categoriaEspacio === 'quirofano' || $categoriaEspacio === 'consultorio') {
        $claveEspacio = $categoriaEspacio === 'quirofano' ? 'quirofanos' : 'consultorios';
        $spaceStats[$claveEspacio]++;
        $floorStats[$piso->id][$claveEspacio]++;
      }

      $roomsForMove[] = [
        'id' => $habitacion->id,
        'name' => 'Habitacion '.$habitacion->codigo,
        'floor' => $etiquetaPiso,
      ];

      foreach ($habitacion->cama as $cama) {
        $hospitalizacion = $activeHospitalizationsByBed->get($cama->id);
        $estado = $normalizarEstadoCama($cama, $hospitalizacion);

        $bedsForTransfer[] = [
          'id' => $cama->id,
          'code' => $cama->codigo,
          'roomId' => $habitacion->id,
          'roomName' => 'Habitacion '.$habitacion->codigo,
          'floor' => $etiquetaPiso,
          'state' => $estado,
        ];

        if (in_array($categoriaEspacio, ['quirofano', 'consultorio'], true)) {
          continue;
        }

        $stats['total']++;
        $stats[$estado]++;
        $floorStats[$piso->id]['total']++;
        $floorStats[$piso->id][$estado]++;
      }
    }
  }

  $porcentajeOcupacion = $stats['total'] > 0 ? round(($stats['ocupada'] / $stats['total']) * 100) : 0;
@endphp

<style>
  .bed-board {
    --bed-bg: #f5f8fb;
    --bed-card: #ffffff;
    --bed-soft: #eef6fb;
    --bed-border: #d8e6ef;
    --bed-text: #17324d;
    --bed-muted: #63788f;
    --bed-blue: #0b7fab;
    --bed-blue-2: #11a4c5;
    --bed-green: #16a34a;
    --bed-red: #dc2626;
    --bed-yellow: #f59e0b;
    --bed-gray: #64748b;
    color: var(--bed-text);
  }

  .hospital-modern-ui .bed-board {
    --bed-bg: #eef7fb;
    --bed-card: #ffffff;
    --bed-soft: #e2f3f8;
    --bed-border: #b9dce8;
    --bed-text: #07375f;
    --bed-muted: #557287;
    --bed-blue: #0b6fac;
    --bed-blue-2: #22b8cf;
    --bed-green: #20a86b;
    --bed-red: #d94b4b;
    --bed-yellow: #e4a72f;
    --bed-gray: #7d8c96;
  }

  .bed-board .card,
  .bed-board .bed-panel,
  .bed-board .bed-room-card,
  .bed-board .bed-summary-card {
    background: var(--bed-card);
    border: 1px solid var(--bed-border);
    border-radius: 8px;
    box-shadow: 0 10px 28px rgba(15, 23, 42, .08);
  }

  .hospital-modern-ui .bed-board .card,
  .hospital-modern-ui .bed-board .bed-panel,
  .hospital-modern-ui .bed-board .bed-room-card,
  .hospital-modern-ui .bed-board .bed-summary-card {
    box-shadow: 0 10px 26px rgba(7, 55, 95, .08);
  }

  .bed-board-wrap {
    background: var(--bed-bg);
    border-radius: 8px;
    padding: 18px;
  }

  .bed-page-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
  }

  .bed-command-hero {
    position: relative;
    overflow: hidden;
    border: 1px solid var(--bed-border);
    border-radius: 8px;
    padding: 18px;
    margin-bottom: 14px;
    background:
      radial-gradient(circle at 12% 10%, rgba(79, 182, 216, .24), transparent 34%),
      linear-gradient(135deg, rgba(11, 127, 171, .14), rgba(22, 163, 74, .08)),
      var(--bed-card);
  }

  .hospital-modern-ui .bed-command-hero {
    background:
      radial-gradient(circle at 12% 10%, rgba(34, 184, 207, .18), transparent 34%),
      linear-gradient(135deg, rgba(255, 255, 255, .92), rgba(226, 243, 248, .86)),
      var(--bed-card);
  }

  .bed-command-hero h3 {
    margin: 0;
    color: var(--bed-text);
    font-size: 22px;
    font-weight: 900;
  }

  .bed-command-hero p {
    max-width: 760px;
    margin: 6px 0 0;
    color: var(--bed-muted);
    font-weight: 700;
  }

  .bed-page-title h4 {
    margin: 0;
    color: var(--bed-text);
    font-weight: 800;
    letter-spacing: 0;
  }

  .bed-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .bed-actions .btn {
    border-radius: 6px;
    font-weight: 700;
  }

  .bed-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 14px;
  }

  .bed-summary-grid.is-bed-availability {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }

  .bed-summary-heading {
    margin: 4px 0 8px;
    color: var(--bed-text);
    font-size: 14px;
    font-weight: 800;
  }

  .bed-summary-card {
    min-height: 92px;
    padding: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .bed-summary-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: var(--bed-soft);
    color: var(--bed-blue);
    font-size: 18px;
    flex: 0 0 auto;
  }

  .bed-summary-card strong {
    display: block;
    color: var(--bed-text);
    font-size: 24px;
    line-height: 1;
  }

  .bed-summary-card span {
    color: var(--bed-muted);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
  }

  .bed-summary-card.is-free .bed-summary-icon { color: var(--bed-green); }
  .bed-summary-card.is-busy .bed-summary-icon { color: var(--bed-red); }
  .bed-summary-card.is-ready .bed-summary-icon { color: var(--bed-yellow); }
  .bed-summary-card.is-off .bed-summary-icon { color: var(--bed-gray); }
  .bed-summary-card.is-quirofano { border-top: 3px solid #7c4dbe; }
  .bed-summary-card.is-quirofano .bed-summary-icon { color: #7c4dbe; background: #f4edfc; }
  .bed-summary-card.is-consultorio { border-top: 3px solid #0e9f9a; }
  .bed-summary-card.is-consultorio .bed-summary-icon { color: #0e9f9a; background: #eaf8f7; }

  .bed-resource-empty {
    padding: 14px;
    color: var(--bed-muted);
    font-weight: 700;
  }

  .bed-occupancy {
    background: var(--bed-card);
    border: 1px solid var(--bed-border);
    border-radius: 8px;
    padding: 12px 14px;
    margin-bottom: 16px;
  }

  .bed-occupancy-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--bed-muted);
    font-weight: 700;
    margin-bottom: 8px;
  }

  .bed-occupancy-bar {
    height: 12px;
    background: var(--bed-soft);
    border-radius: 999px;
    overflow: hidden;
  }

  .bed-occupancy-bar span {
    display: block;
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, #07375f, #22b8cf);
    border-radius: inherit;
  }

  .bed-panel {
    margin-bottom: 14px;
    overflow: hidden;
  }

  .bed-panel-header {
    background: linear-gradient(90deg, #07375f, #0b6fac 52%, #22b8cf);
    color: #fff;
    padding: 12px 16px;
    font-weight: 800;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .bed-panel-body {
    padding: 16px;
  }

  .bed-filter-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 12px;
  }

  .bed-filter-grid .form-control {
    border-radius: 6px;
    border-color: var(--bed-border);
    background: var(--bed-card);
    color: var(--bed-text);
  }

  .bed-status-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .bed-chip {
    border: 1px solid var(--bed-border);
    background: var(--bed-card);
    color: var(--bed-text);
    border-radius: 999px;
    padding: 7px 11px;
    font-weight: 800;
    font-size: 12px;
    cursor: pointer;
  }

  .bed-chip.active {
    background: #0b6fac;
    border-color: #0b6fac;
    color: #fff;
  }

  .bed-legend {
    display: grid;
    gap: 8px;
  }

  .patient-finder {
    display: grid;
    gap: 10px;
  }

  .patient-search-help {
    color: var(--bed-muted);
    font-size: 12px;
    font-weight: 800;
    line-height: 1.35;
  }

  .patient-finder .form-control {
    border-color: var(--bed-border);
    border-radius: 7px;
    color: var(--bed-text);
  }

  .patient-results {
    display: grid;
    gap: 8px;
    max-height: 280px;
    overflow: auto;
  }

  .patient-result-card {
    border: 1px solid var(--bed-border);
    border-left: 5px solid var(--bed-blue);
    background: var(--bed-soft);
    border-radius: 8px;
    padding: 10px;
    cursor: pointer;
    transition: border-color .15s ease, transform .15s ease;
  }

  .patient-result-card:hover {
    border-color: var(--bed-blue);
    transform: translateY(-1px);
  }

  .patient-result-card strong {
    color: var(--bed-text);
    display: block;
    font-size: 13px;
    line-height: 1.2;
  }

  .patient-result-card span {
    color: var(--bed-muted);
    display: block;
    font-size: 12px;
    font-weight: 700;
    margin-top: 3px;
  }

  .patient-result-location {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 6px;
    margin-top: 9px;
  }

  .patient-result-location em {
    background: #fff;
    border: 1px solid var(--bed-border);
    border-radius: 7px;
    color: var(--bed-text);
    font-size: 11px;
    font-style: normal;
    font-weight: 900;
    padding: 6px;
    text-align: center;
  }

  .patient-result-action {
    background: var(--bed-blue);
    border-radius: 7px;
    color: #fff;
    display: inline-flex;
    justify-content: center;
    margin-top: 9px;
    padding: 7px 10px;
    width: 100%;
    font-size: 12px;
    font-weight: 900;
  }

  .patient-result-empty {
    color: var(--bed-muted);
    background: var(--bed-soft);
    border: 1px dashed var(--bed-border);
    border-radius: 8px;
    padding: 10px;
    font-size: 12px;
    font-weight: 800;
  }

  .bed-legend-item {
    display: flex;
    align-items: center;
    gap: 9px;
    color: var(--bed-text);
    font-weight: 800;
  }

  .bed-dot {
    width: 14px;
    height: 14px;
    border-radius: 4px;
    display: inline-block;
  }

  .bed-dot.disponible { background: var(--bed-green); }
  .bed-dot.ocupada { background: var(--bed-red); }
  .bed-dot.preparacion { background: var(--bed-yellow); }
  .bed-dot.no_disponible { background: var(--bed-gray); }

  .selected-patient-box {
    border: 1px dashed var(--bed-border);
    background: var(--bed-soft);
    border-radius: 8px;
    padding: 12px;
    color: var(--bed-muted);
    font-weight: 700;
  }

  .floor-block {
    margin-bottom: 18px;
  }

  .floor-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 8px 0 12px;
  }

  .floor-header h5 {
    margin: 0;
    color: var(--bed-text);
    font-size: 17px;
    font-weight: 900;
  }

  .floor-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
  }

  .floor-pill {
    background: var(--bed-soft);
    color: var(--bed-muted);
    border: 1px solid var(--bed-border);
    border-radius: 999px;
    padding: 5px 9px;
    font-size: 12px;
    font-weight: 800;
  }

  .room-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 12px;
  }

  .bed-room-card {
    padding: 12px;
    transition: transform .15s ease, border-color .15s ease;
  }

  .bed-room-card:hover {
    transform: translateY(-2px);
    border-color: var(--bed-blue);
  }

  .bed-room-card.drop-ready {
    border-color: var(--bed-blue);
    background: color-mix(in srgb, var(--bed-blue) 10%, var(--bed-card));
  }

  .bed-room-card.drop-active {
    outline: 3px solid rgba(79, 182, 216, .3);
    transform: translateY(-3px) scale(1.01);
  }

  .bed-room-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 10px;
  }

  .bed-room-title {
    color: var(--bed-text);
    font-weight: 900;
    font-size: 15px;
  }

  .bed-room-type {
    color: var(--bed-muted);
    font-size: 12px;
    font-weight: 700;
  }

  .bed-room-badge {
    background: var(--bed-soft);
    color: var(--bed-text);
    border-radius: 999px;
    padding: 5px 8px;
    font-size: 12px;
    font-weight: 900;
    white-space: nowrap;
  }

  .bed-list {
    display: grid;
    gap: 8px;
  }

  .bed-unit {
    position: relative;
    min-height: 70px;
    display: grid;
    grid-template-columns: 48px minmax(0, 1fr);
    gap: 10px;
    align-items: center;
    border: 1px solid var(--bed-border);
    border-left-width: 6px;
    background: var(--bed-soft);
    border-radius: 8px;
    padding: 9px;
    color: var(--bed-text);
    text-decoration: none;
    cursor: grab;
    user-select: none;
  }

  .bed-unit:active {
    cursor: grabbing;
  }

  .bed-unit.selected {
    border-color: var(--bed-blue);
    box-shadow: 0 0 0 3px rgba(79, 182, 216, .18);
  }

  .bed-unit.search-focus {
    animation: bedSearchPulse 1.4s ease-in-out 2;
  }

  @keyframes bedSearchPulse {
    0% { box-shadow: 0 0 0 0 rgba(11, 111, 172, .35); }
    70% { box-shadow: 0 0 0 12px rgba(11, 111, 172, 0); }
    100% { box-shadow: 0 0 0 0 rgba(11, 111, 172, 0); }
  }

  .bed-unit:hover,
  .bed-unit:focus {
    color: var(--bed-text);
    text-decoration: none;
    border-color: var(--bed-blue);
  }

  .bed-unit.disponible { border-left-color: var(--bed-green); }
  .bed-unit.ocupada { border-left-color: var(--bed-red); }
  .bed-unit.preparacion { border-left-color: var(--bed-yellow); }
  .bed-unit.no_disponible { border-left-color: var(--bed-gray); }

  .bed-icon {
    width: 46px;
    height: 46px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--bed-card);
    font-size: 22px;
    overflow: hidden;
  }

  .bed-icon img {
    max-width: 40px;
    max-height: 40px;
    object-fit: contain;
    display: block;
  }

  .bed-unit.disponible .bed-icon { color: var(--bed-green); }
  .bed-unit.ocupada .bed-icon { color: var(--bed-red); }
  .bed-unit.preparacion .bed-icon { color: var(--bed-yellow); }
  .bed-unit.no_disponible .bed-icon { color: var(--bed-gray); }

  .bed-label {
    display: block;
    font-weight: 900;
    color: var(--bed-text);
  }

  .bed-meta {
    display: block;
    color: var(--bed-muted);
    font-size: 12px;
    font-weight: 700;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .bed-tooltip-card {
    position: absolute;
    left: 56px;
    bottom: calc(100% + 8px);
    min-width: 220px;
    max-width: 280px;
    background: #111827;
    color: #f9fafb;
    border-radius: 8px;
    padding: 10px;
    box-shadow: 0 18px 45px rgba(0, 0, 0, .25);
    opacity: 0;
    pointer-events: none;
    transform: translateY(4px);
    transition: opacity .15s ease, transform .15s ease;
    z-index: 20;
  }

  .bed-tooltip-card strong,
  .bed-tooltip-card span {
    display: block;
  }

  .bed-tooltip-card span {
    color: #d1d5db;
    font-size: 12px;
    margin-top: 3px;
  }

  .bed-unit:hover .bed-tooltip-card,
  .bed-unit:focus .bed-tooltip-card {
    opacity: 1;
    transform: translateY(0);
  }

  .waiting-table-wrap {
    max-height: 360px;
    overflow: auto;
  }

  .waiting-table-wrap table {
    margin-bottom: 0;
  }

  .waiting-table-wrap th {
    color: var(--bed-muted);
    font-size: 12px;
    text-transform: uppercase;
  }

  .bed-empty {
    color: var(--bed-muted);
    padding: 20px;
    text-align: center;
    border: 1px dashed var(--bed-border);
    border-radius: 8px;
    background: var(--bed-soft);
  }

  .bed-setup-empty {
    display: grid;
    justify-items: center;
    gap: 10px;
    min-height: 245px;
    padding: 36px 22px;
    align-content: center;
    color: var(--bed-muted);
    text-align: center;
  }

  .bed-setup-empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 66px;
    height: 66px;
    border-radius: 18px;
    background: var(--bed-soft);
    color: var(--bed-blue);
    font-size: 28px;
  }

  .bed-setup-empty h5 {
    margin: 4px 0 0;
    color: var(--bed-text);
    font-size: 18px;
    font-weight: 900;
  }

  .bed-setup-empty p {
    max-width: 560px;
    margin: 0;
    font-weight: 600;
  }

  .bed-control-card {
    background: var(--bed-soft);
    border: 1px solid var(--bed-border);
    border-radius: 8px;
    padding: 12px;
  }

  .bed-control-card h5 {
    color: var(--bed-text);
    font-size: 15px;
    font-weight: 900;
    margin: 0 0 8px;
  }

  .bed-control-line {
    color: var(--bed-muted);
    display: block;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .bed-control-actions {
    display: grid;
    gap: 8px;
    margin-top: 12px;
  }

  .bed-control-actions .btn {
    border-radius: 6px;
    font-weight: 800;
    text-align: left;
  }

  .bed-quick-modal {
    border: 0;
    border-radius: 10px;
    overflow: hidden;
    background: var(--bed-card);
    box-shadow: 0 18px 48px rgba(7, 55, 95, .2);
  }

  .bed-quick-modal .modal-header {
    padding: 12px 16px;
    background: linear-gradient(90deg, #eaf7fb, #cfeef7);
    color: #07375f;
    border-bottom: 0;
  }

  .bed-quick-modal .modal-title {
    font-size: 16px;
    font-weight: 900;
    color: #07375f;
  }

  #calendarModal .bed-quick-modal .modal-header button.close {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    background: #0b6fac !important;
    border: 0 !important;
    color: transparent !important;
    opacity: 1 !important;
    text-shadow: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 !important;
    margin: -4px -4px -4px auto !important;
    font-size: 22px;
    line-height: 1;
    position: relative;
  }

  #calendarModal .bed-quick-modal .modal-header button.close::before {
    content: "×";
    color: #fff;
    display: block;
    font-weight: 900;
    font-size: 22px;
    line-height: 28px;
    opacity: 1;
  }

  #calendarModal .bed-quick-modal .modal-header button.close span {
    display: none !important;
  }

  #calendarModal .bed-quick-modal .modal-header button.close:hover {
    background: #0b6fac;
    color: #fff;
  }

  .bed-quick-modal .modal-body {
    padding: 16px;
  }

  .bed-modal-summary {
    border: 1px solid var(--bed-border);
    background: var(--bed-soft);
    border-radius: 8px;
    padding: 12px;
    display: grid;
    grid-template-columns: 44px minmax(0, 1fr);
    gap: 10px;
    align-items: center;
    margin-bottom: 12px;
  }

  .bed-modal-summary .bed-modal-summary-icon {
    width: 42px;
    height: 42px;
    margin: 0;
    border-radius: 8px;
    background: var(--bed-card);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--bed-blue);
    font-size: 23px;
    line-height: 1;
    box-shadow: 0 4px 10px rgba(11, 111, 172, .1);
  }

  .bed-modal-summary .bed-modal-summary-icon i {
    display: block;
    width: auto;
    height: auto;
    margin: 0;
    color: inherit;
    font-size: inherit;
    line-height: 1;
  }

  .bed-modal-summary strong {
    color: var(--bed-text);
    display: block;
    font-size: 14px;
    line-height: 1.2;
  }

  .bed-modal-summary > div > span {
    color: var(--bed-muted);
    display: block;
    font-size: 12px;
    font-weight: 700;
    margin-top: 2px;
  }

  .bed-modal-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
  }

  .bed-modal-actions .btn {
    min-height: 42px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 900;
    text-align: center;
    white-space: normal;
  }

  .bed-modal-move {
    border-top: 1px solid var(--bed-border);
    margin-top: 12px;
    padding-top: 12px;
  }

  .bed-modal-move label {
    color: var(--bed-text);
    font-size: 12px;
    font-weight: 900;
    margin-bottom: 6px;
  }

  .bed-modal-move .form-control {
    border-color: var(--bed-border);
    border-radius: 7px;
    color: var(--bed-text);
  }

  .bed-patient-transfer {
    margin-top: 12px;
    padding: 13px;
    border: 1px solid #b9dce8;
    border-radius: 10px;
    background: linear-gradient(135deg, #edf9fc, #f7fcfd);
  }

  .bed-patient-transfer-title { display:flex; align-items:center; gap:9px; margin-bottom:4px; color:#07527d; font-size:13px; font-weight:900; }
  .bed-patient-transfer-title i { width:30px; height:30px; display:flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; background:linear-gradient(135deg,#0b7fab,#12aaa4); }
  .bed-patient-transfer-help { margin:0 0 10px; color:var(--bed-muted); font-size:11px; line-height:1.35; }
  .bed-patient-transfer .form-group { margin-bottom:8px; }
  .bed-patient-transfer label { margin-bottom:4px; color:var(--bed-text); font-size:11px; font-weight:800; }
  .bed-patient-transfer textarea.form-control { min-height:58px; resize:vertical; }
  .bed-physical-move-note { display:block; margin-bottom:7px; color:var(--bed-muted); font-size:10px; line-height:1.35; }

  /* Presentacion profesional de pisos, habitaciones y camas. */
  .floor-block {
    margin-bottom: 18px;
    padding: 14px;
    border: 1px solid #dceaf0;
    border-radius: 14px;
    background: linear-gradient(145deg, #f8fcfd 0%, #f3f9fb 100%);
  }

  .floor-header { margin: 0 0 14px; }

  .floor-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
  }

  .floor-heading-icon {
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    border: 1px solid #cce3eb;
    border-radius: 10px;
    background: #fff;
    color: var(--bed-blue);
    box-shadow: 0 5px 14px rgba(7, 55, 95, .07);
  }

  .floor-heading small {
    display: block;
    margin-top: 2px;
    color: var(--bed-muted);
    font-size: 11px;
    font-weight: 700;
  }

  .floor-pill {
    padding: 5px 10px;
    background: #fff;
    font-size: 11px;
  }

  .floor-pill.is-free { border-color: #bce8d3; background: #effbf5; color: #13754c; }
  .floor-pill.is-busy { border-color: #f0caca; background: #fff4f4; color: #aa3434; }
  .floor-pill.is-quirofano { border-color: #d9c7ed; background: #f7f0fc; color: #7143a9; }
  .floor-pill.is-consultorio { border-color: #bee9e5; background: #eefbf9; color: #087e7a; }

  .room-grid {
    grid-template-columns: repeat(auto-fill, minmax(285px, 340px));
    gap: 14px;
    align-items: start;
    justify-content: start;
  }

  .bed-room-card {
    padding: 0;
    overflow: visible;
    border-radius: 12px;
    width: 100%;
    box-shadow: 0 7px 20px rgba(7, 55, 95, .06);
    transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
  }

  .bed-room-card:hover {
    border-color: #9ecddd;
    box-shadow: 0 12px 26px rgba(7, 55, 95, .1);
  }

  .bed-room-header {
    margin-bottom: 0;
    padding: 12px 13px;
    border-bottom: 1px solid #e5eff3;
    border-radius: 12px 12px 0 0;
    background: linear-gradient(135deg, #fff, #f7fbfc);
  }

  .bed-room-identity {
    display: grid;
    grid-template-columns: 34px minmax(0, 1fr);
    gap: 9px;
    align-items: center;
    min-width: 0;
  }

  .bed-room-identity-icon {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #cde4ec;
    border-radius: 9px;
    background: linear-gradient(145deg, #fff, #eaf6fa);
    color: var(--bed-blue);
    box-shadow: 0 4px 10px rgba(7, 55, 95, .06);
  }

  .bed-room-title { font-size: 14px; }
  .bed-room-type { margin-top: 2px; font-size: 11px; }

  .bed-room-badge {
    padding: 5px 9px;
    border: 1px solid #d9e8ee;
    background: #f2f7f9;
    font-size: 10px;
  }

  .bed-room-badge.is-empty { border-color: #bce8d3; background: #effbf5; color: #13754c; }
  .bed-room-badge.is-partial { border-color: #f3dfad; background: #fffaeb; color: #946710; }
  .bed-room-badge.is-full { border-color: #f0caca; background: #fff4f4; color: #aa3434; }

  .bed-room-occupancy {
    padding: 0 13px 11px;
    background: linear-gradient(180deg, #f7fbfc, #fff);
    border-bottom: 1px solid #e8f0f4;
  }

  .bed-room-occupancy-track {
    display: block;
    height: 4px;
    overflow: hidden;
    border-radius: 999px;
    background: #e5eef2;
  }

  .bed-room-occupancy-value {
    display: block;
    height: 100%;
    min-width: 0;
    border-radius: inherit;
    background: linear-gradient(90deg, #1fb879, #18a6bd);
    transition: width .2s ease;
  }

  .bed-room-card.is-partial .bed-room-occupancy-value {
    background: linear-gradient(90deg, #e5a52d, #efbf58);
  }

  .bed-room-card.is-full .bed-room-occupancy-value {
    background: linear-gradient(90deg, #d94b4b, #ed7777);
  }

  .bed-room-card.resource-quirofano { border-top: 4px solid #7c4dbe; }
  .bed-room-card.resource-consultorio { border-top: 4px solid #0e9f9a; }
  .bed-room-card.resource-habitacion { border-top: 4px solid var(--bed-blue); }
  .resource-quirofano .bed-room-identity-icon,
  .resource-quirofano .bed-icon { color: #7c4dbe; }
  .resource-consultorio .bed-room-identity-icon,
  .resource-consultorio .bed-icon { color: #0e9f9a; }

  .bed-resource-label {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 5px;
    padding: 3px 7px;
    border-radius: 999px;
    color: #fff;
    background: var(--bed-blue);
    font-size: 9px;
    font-weight: 900;
    text-transform: uppercase;
    vertical-align: middle;
  }

  .resource-quirofano .bed-resource-label { background: #7c4dbe; }
  .resource-consultorio .bed-resource-label { background: #0e9f9a; }

  .bed-list { gap: 9px; padding: 11px; }

  .bed-unit {
    min-height: 72px;
    grid-template-columns: 50px minmax(0, 1fr) auto;
    gap: 10px;
    border-left-width: 4px;
    border-radius: 10px;
    padding: 9px 10px;
    background: #f8fbfc;
    box-shadow: 0 2px 7px rgba(7, 55, 95, .035);
    transition: transform .14s ease, box-shadow .14s ease, border-color .14s ease;
  }

  .bed-unit:hover,
  .bed-unit:focus {
    box-shadow: 0 7px 16px rgba(7, 55, 95, .1);
    transform: translateY(-1px);
    outline: none;
  }

  .bed-unit.disponible { background: #f6fcf9; }
  .bed-unit.ocupada { background: #fff9f9; }
  .bed-unit.preparacion { background: #fffaf1; }
  .bed-unit.no_disponible { background: #f7f9fa; }

  .bed-icon {
    position: relative;
    width: 48px;
    height: 48px;
    border: 1px solid #d9e8ee;
    border-radius: 12px;
    background: linear-gradient(145deg, #fff, #f1f7f9);
    box-shadow: 0 5px 13px rgba(7, 55, 95, .08);
    overflow: visible;
  }

  .bed-icon svg {
    width: 38px;
    height: 38px;
    display: block;
    stroke: currentColor;
    filter: drop-shadow(0 2px 2px rgba(7, 55, 95, .08));
  }

  .bed-icon::after {
    content: '';
    position: absolute;
    right: -3px;
    bottom: -3px;
    width: 11px;
    height: 11px;
    border: 3px solid #fff;
    border-radius: 50%;
    background: var(--bed-gray);
    box-shadow: 0 1px 4px rgba(7, 55, 95, .16);
  }

  .bed-unit.disponible .bed-icon { border-color: #bde6d1; background: linear-gradient(145deg, #fff, #edfaf4); }
  .bed-unit.ocupada .bed-icon { border-color: #efc4c4; background: linear-gradient(145deg, #fff, #fff0f0); }
  .bed-unit.preparacion .bed-icon { border-color: #efdca8; background: linear-gradient(145deg, #fff, #fff8e8); }
  .bed-unit.no_disponible .bed-icon { border-color: #d6dfe5; background: linear-gradient(145deg, #fff, #f1f4f6); }
  .bed-unit.disponible .bed-icon::after { background: var(--bed-green); }
  .bed-unit.ocupada .bed-icon::after { background: var(--bed-red); }
  .bed-unit.preparacion .bed-icon::after { background: var(--bed-yellow); }
  .bed-unit.no_disponible .bed-icon::after { background: var(--bed-gray); }

  .bed-label { font-size: 13px; line-height: 1.25; }
  .bed-meta { margin-top: 2px; font-size: 10px; }

  .bed-unit-copy {
    min-width: 0;
  }

  .bed-state-indicator {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    align-self: center;
    border: 1px solid #dbe8ed;
    border-radius: 999px;
    background: #fff;
    color: var(--bed-muted);
    padding: 4px 7px;
    font-size: 9px;
    font-weight: 900;
    line-height: 1;
    white-space: nowrap;
  }

  .bed-state-indicator::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--bed-gray);
  }

  .bed-unit.disponible .bed-state-indicator { color: #13754c; border-color: #ccebdc; }
  .bed-unit.disponible .bed-state-indicator::before { background: var(--bed-green); }
  .bed-unit.ocupada .bed-state-indicator { color: #aa3434; border-color: #f0d0d0; }
  .bed-unit.ocupada .bed-state-indicator::before { background: var(--bed-red); }
  .bed-unit.preparacion .bed-state-indicator { color: #946710; border-color: #f3dfad; }
  .bed-unit.preparacion .bed-state-indicator::before { background: var(--bed-yellow); }

  .bed-panel-status-note {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: 1px solid rgba(255, 255, 255, .28);
    border-radius: 999px;
    background: rgba(255, 255, 255, .12);
    padding: 4px 9px;
    font-size: 10px;
    font-weight: 800;
  }

  .bed-panel-status-note::before {
    content: '';
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #ff8c8c;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, .12);
  }

  @media (max-width: 575px) {
    .bed-modal-actions {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 991px) {
    .bed-summary-grid,
    .bed-summary-grid.is-bed-availability {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .bed-filter-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 575px) {
    .bed-board-wrap {
      padding: 12px;
    }

    .bed-page-title,
    .floor-header {
      align-items: flex-start;
      flex-direction: column;
    }

    .bed-summary-grid,
    .bed-summary-grid.is-bed-availability {
      grid-template-columns: 1fr;
    }

    .floor-block { padding: 11px; }
    .room-grid { grid-template-columns: 1fr; }
    .bed-state-indicator,
    .bed-panel-status-note { display: none; }
    .bed-unit { grid-template-columns: 50px minmax(0, 1fr); }
  }

  #areachange {
    transition: opacity .18s ease;
  }

  #areachange.is-refreshing {
    opacity: .55;
    pointer-events: none;
  }

  html.dark-layout .bed-board {
    --bed-bg: #102430;
    --bed-card: #19313e;
    --bed-soft: #244653;
    --bed-border: #3c6070;
    --bed-text: #e6f2f6;
    --bed-muted: #a9c2cd;
    --bed-blue: #65cee0;
    --bed-green: #55d79a;
    --bed-red: #ff7c83;
    --bed-yellow: #ffc65a;
    --bed-gray: #a3b2ba;
  }

  html.dark-layout .hospital-modern-ui .bed-board-wrap {
    border: 1px solid #294958;
    background: linear-gradient(145deg,#112733 0%,#162f3b 100%) !important;
    box-shadow: 0 14px 34px rgba(0,0,0,.22);
  }

  html.dark-layout .hospital-modern-ui .bed-command-hero {
    border-color: #426676;
    color: #e8f4f7;
    background:
      radial-gradient(circle at 10% 0%,rgba(50,190,205,.2),transparent 36%),
      linear-gradient(125deg,#193847 0%,#1b4451 58%,#205864 100%) !important;
  }

  html.dark-layout .hospital-modern-ui .bed-command-hero h3 { color:#f2fbfd !important; }
  html.dark-layout .hospital-modern-ui .bed-command-hero p { color:#b9d1da !important; }

  html.dark-layout .hospital-modern-ui .bed-summary-card {
    border-color: #3b5e6e !important;
    background: linear-gradient(145deg,#1b3441,#19303d) !important;
  }

  html.dark-layout .hospital-modern-ui .bed-summary-icon {
    border: 1px solid #416574;
    background: #244653 !important;
  }

  html.dark-layout .hospital-modern-ui .bed-summary-card strong { color:#f1f8fb !important; }
  html.dark-layout .hospital-modern-ui .bed-summary-card span,
  html.dark-layout .hospital-modern-ui .bed-occupancy-head,
  html.dark-layout .hospital-modern-ui .bed-legend-item { color:#b5cad4 !important; }

  html.dark-layout .hospital-modern-ui .bed-panel {
    border-color:#395b6b !important;
    background:#19313e !important;
  }

  html.dark-layout .hospital-modern-ui .bed-panel-body,
  html.dark-layout .hospital-modern-ui .bed-setup-empty,
  html.dark-layout .hospital-modern-ui .waiting-table-wrap {
    color:#dceaf0 !important;
    background:#19313e !important;
  }

  html.dark-layout .hospital-modern-ui .bed-setup-empty h5 { color:#edf7fa !important; }
  html.dark-layout .hospital-modern-ui .bed-setup-empty p { color:#a9c2cd !important; }
  html.dark-layout .hospital-modern-ui .bed-occupancy-bar { background:#294b59 !important; }

  html.dark-layout .hospital-modern-ui .bed-filter-grid .form-control,
  html.dark-layout .hospital-modern-ui .patient-finder .form-control {
    border-color:#456979 !important;
    color:#e5f0f4 !important;
    background:#203d4a !important;
  }

  html.dark-layout .hospital-modern-ui .bed-chip {
    border-color:#456979 !important;
    color:#c9dce4 !important;
    background:#1d3845 !important;
  }

  html.dark-layout .hospital-modern-ui .bed-chip.active {
    border-color:#20b3c5 !important;
    color:#fff !important;
    background:linear-gradient(135deg,#087ca4,#17a9ae) !important;
  }

  html.dark-layout .hospital-modern-ui .waiting-table-wrap table td { color:#c4d7df !important; }
</style>

<div class="bed-board" id="areachange"
  data-rooms-catalog="{{json_encode($roomsForMove)}}"
  data-beds-catalog="{{json_encode($bedsForTransfer)}}">
  <div class="bed-board-wrap">
    {{ csrf_field() }}
    @if(session('error'))
      <div class="alert alert-warning" role="alert" style="margin-bottom: 16px;">
        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
        {{ session('error') }}
      </div>
    @endif
    <div class="bed-command-hero">
      <div class="bed-page-title">
        <div>
          <h3><i class="fa fa-bed"></i> {{trans('hospitalizacion.GESTIONDECUARTOS')}}</h3>
          <p>Control central de ocupación, disponibilidad y asignación de camas por piso y habitación.</p>
        </div>
        <div class="bed-actions">
        @if(Auth::user() && (int) Auth::user()->id_tipo_usuario === 1)
        <a href="{{route('hospital.workflow.locations')}}" class="btn btn-outline-primary"><i class="fa fa-cog"></i> Destinos hospitalarios</a>
        @endif
        <a href="{{route('hospital.gcuartos_habitacion')}}" class="btn btn-outline-primary">
          <i class="fa fa-list"></i> {{trans('hospitalizacion.PacientesenHabitación')}}
        </a>
        <a href="{{route('hospital_admin.gestionh')}}" class="btn btn-primary">
          <i class="fa fa-procedures"></i> {{trans('hospitalizacion.GestionHabitaciones')}}
        </a>
        <button type="button" class="btn btn-info" onclick="freehabitation()">
          <i class="fa fa-door-open"></i> Revisar camas sin ingreso
        </button>
        </div>
      </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content" id="content"></div>
      </div>
    </div>

    <h5 class="bed-summary-heading">Cantidad por tipo</h5>
    <div class="bed-summary-grid">
      <div class="bed-summary-card">
        <span class="bed-summary-icon"><i class="fa fa-bed"></i></span>
        <div><strong>{{$stats['total']}}</strong><span>Total camas</span></div>
      </div>
      <div class="bed-summary-card is-quirofano">
        <span class="bed-summary-icon"><i class="fa fa-procedures"></i></span>
        <div><strong>{{$spaceStats['quirofanos']}}</strong><span>Quirófanos</span></div>
      </div>
      <div class="bed-summary-card is-consultorio">
        <span class="bed-summary-icon"><i class="fa fa-stethoscope"></i></span>
        <div><strong>{{$spaceStats['consultorios']}}</strong><span>Consultorios</span></div>
      </div>
    </div>

    <h5 class="bed-summary-heading">Disponibilidad de camas</h5>
    <div class="bed-summary-grid is-bed-availability">
      <div class="bed-summary-card is-free">
        <span class="bed-summary-icon"><i class="fa fa-check"></i></span>
        <div><strong>{{$stats['disponible']}}</strong><span>Camas disponibles</span></div>
      </div>
      <div class="bed-summary-card is-busy">
        <span class="bed-summary-icon"><i class="fa fa-user-injured"></i></span>
        <div><strong>{{$stats['ocupada']}}</strong><span>Camas ocupadas</span></div>
      </div>
      <div class="bed-summary-card is-ready">
        <span class="bed-summary-icon"><i class="fa fa-broom"></i></span>
        <div><strong>{{$stats['preparacion']}}</strong><span>Preparación</span></div>
      </div>
      <div class="bed-summary-card is-off">
        <span class="bed-summary-icon"><i class="fa fa-ban"></i></span>
        <div><strong>{{$stats['no_disponible']}}</strong><span>No disponibles</span></div>
      </div>
    </div>

    <div class="bed-occupancy">
      <div class="bed-occupancy-head">
        <span>Ocupación de camas</span>
        <span>{{$porcentajeOcupacion}}%</span>
      </div>
      <div class="bed-occupancy-bar"><span style="width: {{$porcentajeOcupacion}}%;"></span></div>
    </div>

    <div class="row">
      <div class="col-lg-4 col-xl-3">
        <div class="bed-panel">
          <div class="bed-panel-header">
            <span><i class="fa fa-user-plus"></i> {{trans('hospitalizacion.Pacientesenespera')}}</span>
            <span>{{$enespera->count()}}</span>
          </div>
          <div class="bed-panel-body">
            <div id="anadir" class="selected-patient-box">
              Seleccione un paciente en espera y luego haga clic en una cama libre.
            </div>
          </div>
        </div>

        <div class="bed-panel">
          <div class="bed-panel-header">
            <span><i class="fa fa-search"></i> Buscar paciente</span>
          </div>
          <div class="bed-panel-body">
            <div class="patient-finder">
              <input type="text" id="patientFinder" class="form-control" placeholder="Buscar por nombre, apellido, cedula o sala">
              <div class="patient-search-help">Escriba el nombre del paciente y toque el resultado para ver piso, habitacion, cama y abrir sus datos.</div>
              <div id="patientFinderResults" class="patient-results">
                <div class="patient-result-empty">Escriba al menos 2 caracteres para ubicar un paciente hospitalizado.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bed-panel">
          <div class="bed-panel-header">
            <span><i class="fa fa-filter"></i> Seleccionar sala</span>
          </div>
          <div class="bed-panel-body">
            <div class="bed-filter-grid">
              <select name="tipoh" id="tipoh" class="form-control" onchange="muestra(this.value);">
                <option value="">{{trans('hospitalizacion.TODOS')}} los tipos</option>
                <option value="1">{{trans('hospitalizacion.SIMPLE')}}</option>
                <option value="2">{{trans('hospitalizacion.DOBLE')}}</option>
                <option value="3">{{trans('hospitalizacion.TRIPLE')}}</option>
                <option value="4">{{trans('hospitalizacion.EJECUTIVA')}}</option>
                <option value="5">{{trans('hospitalizacion.SUITE')}}</option>
              </select>

              <select name="pisomaster" id="pisomaster" onchange="pisos(this.value);" class="form-control">
                <option value="">{{trans('hospitalizacion.TODOS')}} los pisos</option>
                @foreach($pisos as $piso)
                  <option value="{{$piso->id}}">{{!empty($piso->nombre_piso) ? $piso->nombre_piso : trans('hospitalizacion.Piso').' '.$piso->id}}</option>
                @endforeach
              </select>

              <input type="text" id="bedSearch" class="form-control" placeholder="Buscar habitacion, cama o paciente">
            </div>

            <div class="bed-status-chips" style="margin-bottom: 14px;">
              <button type="button" class="bed-chip active" data-status-filter="">Todos</button>
              <button type="button" class="bed-chip" data-status-filter="disponible">Libres</button>
              <button type="button" class="bed-chip" data-status-filter="ocupada">Ocupadas</button>
              <button type="button" class="bed-chip" data-status-filter="preparacion">Preparacion</button>
              <button type="button" class="bed-chip" data-status-filter="no_disponible">No disponibles</button>
            </div>

            <div class="bed-legend">
              <div class="bed-legend-item"><span class="bed-dot disponible"></span> Libre: se puede asignar</div>
              <div class="bed-legend-item"><span class="bed-dot ocupada"></span> Ocupada: ver paciente</div>
              <div class="bed-legend-item"><span class="bed-dot preparacion"></span> Preparacion: limpieza o espera</div>
              <div class="bed-legend-item"><span class="bed-dot no_disponible"></span> No disponible</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-8 col-xl-9">
        <div class="bed-panel">
          <div class="bed-panel-header">
            <span><i class="fa fa-map-marked-alt"></i> Distribución de espacios y camas</span>
            <span class="bed-panel-status-note">Rojo indica cama ocupada</span>
          </div>
          <div class="bed-panel-body">
            @if($pisos->isEmpty())
              <div class="bed-setup-empty">
                <span class="bed-setup-empty-icon"><i class="fa fa-bed"></i></span>
                <h5>No hay espacios configurados</h5>
                <p>Crea habitaciones, quirófanos o consultorios para comenzar a gestionar los espacios.</p>
                <a href="{{route('hospital_admin.gestionh')}}" class="btn btn-primary">
                  <i class="fa fa-plus"></i> Configurar habitaciones
                </a>
              </div>
            @else
            @foreach($pisos as $piso)
              @php $etiquetaPiso = !empty($piso->nombre_piso) ? $piso->nombre_piso : 'Piso '.$piso->id; @endphp
              <section class="floor-block" id="pisosa{{$piso->id}}" data-floor="{{$piso->id}}">
                <div class="floor-header">
                  <div class="floor-heading">
                    <span class="floor-heading-icon"><i class="fa fa-building" aria-hidden="true"></i></span>
                    <div>
                      <h5>{{!empty($piso->nombre_piso) ? $piso->nombre_piso : trans('hospitalizacion.Piso').' '.$piso->id}}</h5>
                      <small>Habitaciones, quirófanos y consultorios</small>
                    </div>
                  </div>
                  <div class="floor-pills">
                    <span class="floor-pill">{{$floorStats[$piso->id]['total']}} camas</span>
                    <span class="floor-pill is-free">{{$floorStats[$piso->id]['disponible']}} libres</span>
                    <span class="floor-pill is-busy">{{$floorStats[$piso->id]['ocupada']}} ocupadas</span>
                    <span class="floor-pill is-quirofano">{{$floorStats[$piso->id]['quirofanos']}} quirófanos</span>
                    <span class="floor-pill is-consultorio">{{$floorStats[$piso->id]['consultorios']}} consultorios</span>
                  </div>
                </div>

                <div class="room-grid">
                  @foreach($piso->habitacion as $habitacion)
                    @if($habitacion->estado != 4)
                      @php
                        $tipoNombre = isset($habitacion->tipo) ? $habitacion->tipo->descripcion : 'Habitacion';
                        $recursoCategoria = isset($habitacion->tipo) && !empty($habitacion->tipo->categoria) ? $habitacion->tipo->categoria : 'habitacion';
                        $recursoLabel = $recursoCategoria === 'quirofano' ? 'Quirófano' : ($recursoCategoria === 'consultorio' ? 'Consultorio' : 'Habitación');
                        $recursoIcon = $recursoCategoria === 'quirofano' ? 'fa-procedures' : ($recursoCategoria === 'consultorio' ? 'fa-stethoscope' : 'fa-door-open');
                        $camasHabitacion = $habitacion->cama;
                        $ocupadasHabitacion = 0;
                        $textoBusquedaHabitacion = strtolower($habitacion->codigo.' '.$tipoNombre);
                      @endphp

                      @foreach($camasHabitacion as $camaPrevia)
                        @php
                          $hospitalizacionPrevia = $activeHospitalizationsByBed->get($camaPrevia->id);
                          if (!is_null($hospitalizacionPrevia)) {
                            $ocupadasHabitacion++;
                            $traspasoPrevio = !empty($hospitalizacionPrevia->id_traspaso) ? $transfersById->get($hospitalizacionPrevia->id_traspaso) : null;
                            $textoBusquedaHabitacion .= ' '.$nombrePaciente($traspasoPrevio);
                          }
                          $textoBusquedaHabitacion .= ' '.$camaPrevia->codigo;
                        @endphp
                      @endforeach

                      @php
                        $totalCamasHabitacion = $camasHabitacion->count();
                        $claseOcupacionHabitacion = $ocupadasHabitacion === 0
                          ? 'is-empty'
                          : (($totalCamasHabitacion > 0 && $ocupadasHabitacion >= $totalCamasHabitacion) ? 'is-full' : 'is-partial');
                        $porcentajeHabitacion = $totalCamasHabitacion > 0
                          ? round(($ocupadasHabitacion / $totalCamasHabitacion) * 100)
                          : 0;
                      @endphp

                      <article class="bed-room-card resource-{{$recursoCategoria}} {{$claseOcupacionHabitacion}}"
                        data-room-type="{{$habitacion->id_tipo}}"
                        data-room-id="{{$habitacion->id}}"
                        data-room-name="{{$recursoLabel}} {{$habitacion->codigo}}"
                        data-floor="{{$piso->id}}"
                        data-search="{{strtolower($textoBusquedaHabitacion)}}">
                        <div class="bed-room-header">
                          <div class="bed-room-identity">
                            <span class="bed-room-identity-icon" aria-hidden="true"><i class="fa {{$recursoIcon}}"></i></span>
                            <div>
                              <div class="bed-room-title">{{$recursoLabel}} {{$habitacion->codigo}} <span class="bed-resource-label"><i class="fa {{$recursoIcon}}"></i> {{$recursoLabel}}</span></div>
                              <div class="bed-room-type">{{$tipoNombre}}</div>
                            </div>
                          </div>
                          @if($totalCamasHabitacion > 0)
                            <span class="bed-room-badge {{$claseOcupacionHabitacion}}">{{$ocupadasHabitacion}} / {{$totalCamasHabitacion}} ocupadas</span>
                          @endif
                        </div>
                        @if($totalCamasHabitacion > 0)
                          <div class="bed-room-occupancy" title="Ocupacion de la habitacion: {{$porcentajeHabitacion}}%">
                            <span class="bed-room-occupancy-track" aria-hidden="true">
                              <span class="bed-room-occupancy-value" style="width: {{$porcentajeHabitacion}}%;"></span>
                            </span>
                          </div>
                        @endif

                        <div class="bed-list">
                          @if($camasHabitacion->isEmpty())
                            <div class="bed-resource-empty">Espacio registrado sin camas.</div>
                          @endif
                          @foreach($camasHabitacion as $cama)
                            @php
                              $hospitalizacion = $activeHospitalizationsByBed->get($cama->id);
                              $estadoCama = $normalizarEstadoCama($cama, $hospitalizacion);
                              $traspaso = !is_null($hospitalizacion) && !empty($hospitalizacion->id_traspaso) ? $transfersById->get($hospitalizacion->id_traspaso) : null;
                              $pacienteTexto = !is_null($hospitalizacion) ? $nombrePaciente($traspaso) : 'Sin paciente asignado';
                              $cedulaPaciente = !is_null($traspaso) && !is_null($traspaso->paciente) ? $traspaso->paciente->id : '';
                              $salaTexto = !is_null($traspaso) && !is_null($traspaso->sala) ? $traspaso->sala->nombre_sala : 'Sin sala registrada';
                              $causaTexto = !is_null($traspaso) ? $traspaso->causa : '';
                              $fechaIngresoTexto = !is_null($traspaso) ? $traspaso->fecha : '';
                              $urlCama = !is_null($hospitalizacion) ? route('hospitalizacion.show', ['id' => $cama->id, 'id_d' => $hospitalizacion->id]) : 'javascript:loadUrl('.$cama->id.')';
                            @endphp

                            <div class="bed-unit resource-{{$recursoCategoria}} {{$estadoCama}}"
                              role="button"
                              tabindex="0"
                              draggable="true"
                              onclick="selectBedFromElement(this)"
                              onkeydown="selectBedFromKeyboard(event, this)"
                              data-bed-id="{{$cama->id}}"
                              data-bed-code="{{$cama->codigo}}"
                              data-room-id="{{$habitacion->id}}"
                              data-room-name="{{$recursoLabel}} {{$habitacion->codigo}}"
                              data-room-code="{{$habitacion->codigo}}"
                              data-floor="{{$piso->id}}"
                              data-floor-label="{{$etiquetaPiso}}"
                              data-patient="{{$pacienteTexto}}"
                              data-patient-id="{{$cedulaPaciente}}"
                              data-sala="{{$salaTexto}}"
                              data-causa="{{$causaTexto}}"
                              data-fecha="{{$fechaIngresoTexto}}"
                              data-details-url="{{$urlCama}}"
                              data-bed-state="{{$estadoCama}}"
                              data-hospitalization-id="{{!is_null($hospitalizacion) ? $hospitalizacion->id : ''}}"
                              data-search="{{strtolower($habitacion->codigo.' '.$cama->codigo.' '.$pacienteTexto.' '.$cedulaPaciente.' '.$salaTexto.' '.$tipoNombre)}}">
                              <span class="bed-icon">
                                @if($recursoCategoria !== 'habitacion')
                                  <i class="fa {{$recursoIcon}}" aria-hidden="true" style="font-size:26px;display:flex;align-items:center;justify-content:center;height:100%;"></i>
                                @else
                                <svg viewBox="0 0 64 64" fill="none" aria-hidden="true" focusable="false">
                                  <path d="M10 49V20.5a3.5 3.5 0 0 1 7 0V36h36a4 4 0 0 1 4 4v9H10Z" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M17 28h10a8 8 0 0 1 8 8H17v-8Z" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
                                  <path d="M35 36h18a4 4 0 0 1 4 4v3H35v-7Z" fill="currentColor" fill-opacity=".12" stroke-width="3.2" stroke-linejoin="round"/>
                                  <path d="M14 49v6M53 49v6" stroke-width="3.2" stroke-linecap="round"/>
                                  <circle cx="14" cy="56" r="2" fill="currentColor" stroke="none"/>
                                  <circle cx="53" cy="56" r="2" fill="currentColor" stroke="none"/>
                                </svg>
                                @endif
                              </span>
                              <span class="bed-unit-copy">
                                <span class="bed-label">{{$recursoCategoria === 'habitacion' ? 'Cama' : $recursoLabel}} {{$cama->codigo}}</span>
                                <span class="bed-meta">
                                  @if($recursoCategoria !== 'habitacion')
                                    {{$recursoLabel}} ·
                                  @endif
                                  @if($estadoCama == 'ocupada')
                                    {{$pacienteTexto}}
                                  @else
                                    {{$estadoLabels[$estadoCama]}}
                                  @endif
                                </span>
                              </span>
                              <span class="bed-state-indicator">{{$estadoLabels[$estadoCama]}}</span>
                              <span class="bed-tooltip-card">
                                @if($estadoCama == 'ocupada')
                                  <strong>{{$pacienteTexto}}</strong>
                                  <span>{{$recursoLabel}} {{$habitacion->codigo}} - {{$recursoCategoria === 'habitacion' ? 'Cama' : $recursoLabel}} {{$cama->codigo}}</span>
                                  <span>{{$salaTexto}}</span>
                                  @if(!empty($causaTexto))
                                    <span>{{substr($causaTexto, 0, 80)}}</span>
                                  @endif
                                  <span>Click para abrir datos del paciente</span>
                                @elseif($estadoCama == 'disponible')
                                  <strong>Cama libre</strong>
                                  <span>{{$recursoLabel}} {{$habitacion->codigo}} - {{$recursoCategoria === 'habitacion' ? 'Cama' : $recursoLabel}} {{$cama->codigo}}</span>
                                  <span>Seleccione un paciente en espera y haga click aqui.</span>
                                @else
                                  <strong>{{$estadoLabels[$estadoCama]}}</strong>
                                  <span>{{$recursoLabel}} {{$habitacion->codigo}} - {{$recursoCategoria === 'habitacion' ? 'Cama' : $recursoLabel}} {{$cama->codigo}}</span>
                                @endif
                              </span>
                            </div>
                          @endforeach
                        </div>
                      </article>
                    @endif
                  @endforeach
                </div>
              </section>
            @endforeach
            @endif
            <div id="bedEmptyState" class="bed-empty" style="display:none;">No hay camas que coincidan con los filtros seleccionados.</div>
          </div>
        </div>

        <div class="bed-panel">
          <div class="bed-panel-header">
            <span><i class="fa fa-clock"></i> Lista de pacientes en espera</span>
          </div>
          <div class="bed-panel-body waiting-table-wrap">
            <table id="example2" class="table table-striped table-hover-animation">
              <thead>
                <tr>
                  <th>{{trans('hospitalizacion.Fecha')}}</th>
                  <th>{{trans('hospitalizacion.Paciente')}}</th>
                  <th>{{trans('hospitalizacion.Causa')}}</th>
                  <th>{{trans('hospitalizacion.Doctor')}}</th>
                  <th>{{trans('hospitalizacion.SalaHabitacion')}}</th>
                  <th>{{trans('hospitalizacion.Estado')}}</th>
                  <th>{{trans('hospitalizacion.Accion')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($enespera as $y)
                  @php $hospitalizacionEspera = $waitingHospitalizationsByTransfer->get($y->id); @endphp
                  <tr>
                    <td>{{$y->fecha}}</td>
                    <td>@if(isset($y->paciente)) {{$y->paciente->apellido1}} {{$y->paciente->apellido2}} {{$y->paciente->nombre1}} {{$y->paciente->nombre2}} @endif</td>
                    <td>{{substr($y->causa, 0, 45)}}</td>
                    <td>@if(isset($y->doctor)) {{$y->doctor->apellido1}} {{$y->doctor->nombre1}} @endif</td>
                    <td>@if(isset($y->sala)) {{$y->sala->nombre_sala}} @endif</td>
                    <td>
                      @if($y->estado == 1)
                        <span class="badge badge-light-danger">{{trans('hospitalizacion.Pendiente')}}</span>
                      @elseif($y->estado == 2)
                        {{trans('hospitalizacion.ENHABITACION')}}
                      @endif
                    </td>
                    <td>
                      @if(is_null($hospitalizacionEspera) && $y->estado == 1)
                        <button id="pending-transfer-{{$y->id}}" class="btn btn-primary btn-sm bsz" type="button" onclick="promise('{{$y->id}}', this)">
                          <i class="fa fa-plus"></i>
                        </button>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  var table = null;

  function initializeWaitingTable() {
    if (!$.fn.DataTable || !$('#example2').length) {
      return;
    }

    if ($.fn.DataTable.isDataTable('#example2')) {
      $('#example2').DataTable().destroy();
    }

    table = $('#example2').DataTable({
    'paging': false,
    'dom': 'lBrtip',
    'lengthChange': true,
    'searching': false,
    'ordering': false,
    'info': false,
    'autoWidth': true,
    'sInfoEmpty': true,
    'sInfoFiltered': true,
    'language': {
      'emptyTable': 'No hay pacientes en espera en este momento.',
      'zeroRecords': 'No se encontraron pacientes con los filtros aplicados.'
    },
    "buttons": [{
        extend: 'copyHtml5',
        text: "{{trans('hospitalizacion.Copiar')}}",
        className: 'btn btn-icon btn-outline-primary btn-sm',
        footer: true
      },
      {
        extend: 'excelHtml5',
        footer: true,
        className: 'btn btn-icon btn-outline-primary btn-sm',
        title: 'HOSPITAL ',
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      },
      {
        extend: 'csvHtml5',
        className: 'btn btn-icon btn-outline-primary btn-sm',
        footer: true
      },
      {
        extend: 'pdfHtml5',
        orientation: 'landscape',
        className: 'btn btn-icon btn-outline-primary btn-sm',
        pageSize: 'LEGAL',
        footer: true,
        title: 'HOSPITAL ',
        customize: function(doc) {
          doc.styles.title = {
            color: 'black',
            fontSize: '17',
            alignment: 'center'
          }
        },
        exportOptions: { columns: [0, 1, 2, 3, 4, 5] }
      }
    ],
    });
  }

  initializeWaitingTable();

  var currentStatusFilter = '';
  var selectedBed = null;
  var roomsCatalog = [];
  var bedsCatalog = [];
  var bedRoutes = {
    move: "{{route('hospital.gcuartos_mover_cama')}}",
    transferPatient: "{{route('hospital.gcuartos_trasladar_paciente')}}",
    release: "{{route('hospital.gcuartos_liberar_cama')}}",
    state: "{{route('hospital.gcuartos_estado_cama')}}"
  };

  function syncBedCatalogs() {
    var $board = $('#areachange');
    try {
      roomsCatalog = JSON.parse($board.attr('data-rooms-catalog') || '[]');
      bedsCatalog = JSON.parse($board.attr('data-beds-catalog') || '[]');
    } catch (error) {
      roomsCatalog = [];
      bedsCatalog = [];
      if (window.console && console.error) {
        console.error('No se pudo actualizar el catálogo de camas.', error);
      }
    }
  }

  syncBedCatalogs();

  function escapeHtml(text) {
    return $('<div>').text(text || '').html();
  }

  function normalizeSearchText(text) {
    return (text || '').toString().toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .replace(/ñ/g, 'n')
      .trim();
  }

  function bedStateLabel(state) {
    var labels = {
      disponible: 'Libre',
      ocupada: 'Ocupada',
      preparacion: 'Preparacion / desinfeccion',
      no_disponible: 'No disponible'
    };
    return labels[state] || state;
  }

  function csrfToken() {
    return $('input[name=_token]').val();
  }

  function releaseBedBoardOverlay() {
    $('.modal-backdrop').remove();
    $('body')
      .removeClass('modal-open')
      .css({
        'padding-right': '',
        'overflow': ''
      });
    $('#areachange').removeClass('is-refreshing');
  }

  function refreshBedBoard() {
    var state = {
      type: $('#tipoh').val() || '',
      floor: $('#pisomaster').val() || '',
      search: $('#bedSearch').val() || '',
      status: currentStatusFilter,
      scrollTop: window.pageYOffset || document.documentElement.scrollTop || 0
    };
    var $currentBoard = $('#areachange');
    $currentBoard.addClass('is-refreshing');

    return $.ajax({
      url: "{{route('hospital.gcuartos')}}",
      method: 'GET',
      data: { fragment: 1 },
      cache: false,
      timeout: 20000
    }).then(function(html) {
      var documentResponse = new DOMParser().parseFromString(html, 'text/html');
      var nextBoard = documentResponse.querySelector('#areachange');
      if (!nextBoard) {
        throw new Error('La respuesta no contiene el tablero de camas.');
      }

      if ($.fn.DataTable && $.fn.DataTable.isDataTable('#example2')) {
        $('#example2').DataTable().destroy();
      }

      $('#calendarModal').modal('hide');
      releaseBedBoardOverlay();
      $currentBoard.replaceWith(document.importNode(nextBoard, true));
      selectedBed = null;
      pacientes_memory = [];
      syncBedCatalogs();

      $('#tipoh').val(state.type);
      $('#pisomaster').val(state.floor);
      $('#bedSearch').val(state.search);
      currentStatusFilter = state.status;
      $('.bed-chip').removeClass('active');
      $('.bed-chip[data-status-filter="' + currentStatusFilter + '"]').addClass('active');

      initializeWaitingTable();
      applyBedFilters();
      releaseBedBoardOverlay();
      window.scrollTo(0, state.scrollTop);

      return true;
    }).fail(function(error) {
      releaseBedBoardOverlay();
      var message = error && error.responseJSON && error.responseJSON.message
        ? error.responseJSON.message
        : 'No se pudo actualizar el tablero. Los datos guardados no se han perdido.';
      Swal.fire('No se pudo actualizar la vista', message, 'error');
    });
  }

  function selectBedFromKeyboard(event, element) {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      selectBedFromElement(element, true);
    }
  }

  function selectBedFromElement(element, openModal) {
    var $bed = $(element);
    $('.bed-unit').removeClass('selected');
    $bed.addClass('selected');

    selectedBed = {
      id: $bed.data('bed-id'),
      code: $bed.data('bed-code'),
      roomId: $bed.data('room-id'),
      roomName: $bed.data('room-name'),
      floor: $bed.data('floor-label'),
      patient: $bed.data('patient'),
      state: $bed.data('bed-state'),
      hospitalizationId: $bed.data('hospitalization-id'),
      detailsUrl: $bed.data('details-url')
    };

    renderSelectedBedPanel();
    if (openModal !== false) {
      showSelectedBedModal();
    }
  }

  function renderSelectedBedPanel() {
    if (!selectedBed) {
      return;
    }

    var html = '';
    html += '<h5>Cama ' + escapeHtml(selectedBed.code) + '</h5>';
    html += '<span class="bed-control-line"><b>Ubicacion:</b> ' + escapeHtml(selectedBed.roomName) + ', ' + escapeHtml(selectedBed.floor) + '</span>';
    html += '<span class="bed-control-line"><b>Estado:</b> ' + escapeHtml(bedStateLabel(selectedBed.state)) + '</span>';

    if (selectedBed.state === 'ocupada') {
      html += '<span class="bed-control-line"><b>Paciente:</b> ' + escapeHtml(selectedBed.patient) + '</span>';
    } else {
      html += '<span class="bed-control-line">Arrastre esta cama a otra habitacion si necesita reorganizar la sala.</span>';
    }

    html += '<div class="bed-control-actions">';

    if (selectedBed.state === 'ocupada') {
      html += '<button type="button" class="btn btn-primary" onclick="openSelectedBedDetails()"><i class="fa fa-id-card"></i> Ver paciente</button>';
      html += '<button type="button" class="btn btn-success" onclick="releaseSelectedBed(\'disponible\')"><i class="fa fa-check-circle"></i> Liberar esta cama</button>';
      html += '<button type="button" class="btn btn-warning" onclick="releaseSelectedBed(\'preparacion\')"><i class="fa fa-broom"></i> Liberar y desinfectar</button>';
    } else if (selectedBed.state === 'disponible') {
      html += '<button type="button" class="btn btn-success" onclick="assignSelectedBed()"><i class="fa fa-user-plus"></i> Asignar paciente seleccionado</button>';
      html += '<button type="button" class="btn btn-warning" onclick="setSelectedBedState(\'preparacion\')"><i class="fa fa-broom"></i> Enviar a desinfeccion</button>';
      html += '<button type="button" class="btn btn-secondary" onclick="setSelectedBedState(\'no_disponible\')"><i class="fa fa-ban"></i> Marcar no disponible</button>';
    } else {
      html += '<button type="button" class="btn btn-success" onclick="setSelectedBedState(\'disponible\')"><i class="fa fa-check-circle"></i> Marcar libre</button>';
      html += '<button type="button" class="btn btn-warning" onclick="setSelectedBedState(\'preparacion\')"><i class="fa fa-broom"></i> Mantener en desinfeccion</button>';
      html += '<button type="button" class="btn btn-secondary" onclick="setSelectedBedState(\'no_disponible\')"><i class="fa fa-ban"></i> Marcar no disponible</button>';
    }

    if (selectedBed.state === 'ocupada') {
      html += renderPatientTransferSelector('panelTransfer');
    }
    html += renderRoomMoveSelector('panelMoveRoom', 'moveSelectedBedFromSelect(\'panelMoveRoom\')');
    html += '</div>';
    if ($('#selectedBedPanel').length) {
      $('#selectedBedPanel').html(html);
    }
  }

  function renderRoomMoveSelector(selectId, action) {
    if (!selectedBed) {
      return '';
    }

    var html = '<div class="bed-modal-move">';
    html += '<label>Reubicar la cama física</label>';
    html += '<span class="bed-physical-move-note">Esta opción mueve el mueble completo. Para cambiar al paciente de cama utilice “Trasladar paciente”.</span>';
    html += '<select class="form-control" id="' + selectId + '">';

    roomsCatalog.forEach(function(room) {
      var selected = selectedBed.roomId == room.id ? ' selected' : '';
      html += '<option value="' + room.id + '"' + selected + '>' + escapeHtml(room.name) + ' - ' + escapeHtml(room.floor) + '</option>';
    });

    html += '</select>';
    html += '<button type="button" class="btn btn-info btn-block" style="margin-top:8px;" onclick="' + action + '"><i class="fa fa-exchange-alt"></i> Mover cama</button>';
    html += '</div>';
    return html;
  }

  function renderPatientTransferSelector(prefix) {
    if (!selectedBed || selectedBed.state !== 'ocupada') return '';
    var roomIds = {};
    var available = bedsCatalog.filter(function(bed) {
      return bed.state === 'disponible' && bed.id != selectedBed.id;
    });
    available.forEach(function(bed) { roomIds[bed.roomId] = bed; });

    var html = '<div class="bed-patient-transfer">';
    html += '<div class="bed-patient-transfer-title"><i class="fa fa-random"></i><span>Trasladar paciente</span></div>';
    html += '<p class="bed-patient-transfer-help">Seleccione una habitación y una cama libre. El cambio conservará el mismo episodio y quedará registrado en la trazabilidad.</p>';
    html += '<div class="form-group"><label>Nueva habitación</label><select class="form-control" id="' + prefix + 'Room" onchange="populateTransferBeds(\'' + prefix + '\')">';
    html += '<option value="">Seleccione una habitación</option>';
    Object.keys(roomIds).forEach(function(roomId) {
      var bed = roomIds[roomId];
      html += '<option value="' + roomId + '">' + escapeHtml(bed.roomName) + ' · ' + escapeHtml(bed.floor) + '</option>';
    });
    html += '</select></div>';
    html += '<div class="form-group"><label>Cama libre</label><select class="form-control" id="' + prefix + 'Bed" disabled><option value="">Seleccione primero la habitación</option></select></div>';
    html += '<div class="form-group"><label>Motivo del traslado</label><textarea class="form-control" id="' + prefix + 'Reason" maxlength="255" placeholder="Ej.: cambio de nivel de cuidado, aislamiento o necesidad clínica"></textarea></div>';
    html += '<button type="button" class="btn btn-primary btn-block" onclick="transferSelectedPatient(\'' + prefix + '\')"><i class="fa fa-exchange"></i> Trasladar a nueva cama</button>';
    html += '</div>';
    return html;
  }

  function populateTransferBeds(prefix) {
    var roomId = $('#' + prefix + 'Room').val();
    var $select = $('#' + prefix + 'Bed').empty();
    var beds = bedsCatalog.filter(function(bed) {
      return bed.state === 'disponible' && bed.roomId == roomId && (!selectedBed || bed.id != selectedBed.id);
    });
    $select.append('<option value="">Seleccione una cama libre</option>');
    beds.forEach(function(bed) {
      $select.append('<option value="' + bed.id + '">Cama ' + escapeHtml(bed.code) + '</option>');
    });
    $select.prop('disabled', !roomId || beds.length === 0);
  }

  function transferSelectedPatient(prefix) {
    var destinationBedId = $('#' + prefix + 'Bed').val();
    var destination = $('#' + prefix + 'Bed option:selected').text();
    var room = $('#' + prefix + 'Room option:selected').text();
    var reason = $.trim($('#' + prefix + 'Reason').val());
    if (!destinationBedId) return Swal.fire('Seleccione una cama', 'Debe elegir una habitación y una cama libre.', 'warning');
    if (reason.length < 5) return Swal.fire('Indique el motivo', 'Escriba un motivo breve para mantener la trazabilidad clínica.', 'warning');

    Swal.fire({
      title: 'Confirmar traslado del paciente',
      html: '<b>' + escapeHtml(selectedBed.patient) + '</b><br>' + escapeHtml(room) + ' · ' + escapeHtml(destination),
      icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, trasladar', cancelButtonText: 'Cancelar'
    }).then(function(result) {
      if (!result.isConfirmed) return;
      $.ajax({
        url: bedRoutes.transferPatient, method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken() },
        data: { id_hospitalizacion: selectedBed.hospitalizationId, id_cama_destino: destinationBedId, motivo: reason },
        success: function(response) {
          refreshBedBoard().done(function() {
            Swal.fire('Traslado registrado', response.message, 'success');
          });
        },
        error: function(xhr) {
          var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Revise la cama destino e intente nuevamente.';
          if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.motivo) message = xhr.responseJSON.errors.motivo[0];
          Swal.fire('No se pudo trasladar', message, 'error');
        }
      });
    });
  }

  function showSelectedBedModal() {
    if (!selectedBed) {
      return;
    }

    $('#calendarModal .modal-dialog').removeClass('modal-xl').addClass('modal-dialog-centered').css('max-width', '460px');

    var modal = '';
    modal += '<div class="modal-header">';
    modal += '<h5 class="modal-title"><i class="fa fa-bed"></i> Cama ' + escapeHtml(selectedBed.code) + '</h5>';
    modal += '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    modal += '</div>';
    modal += '<div class="modal-body">';
    modal += '<div class="bed-modal-summary">';
    modal += '<span class="bed-modal-summary-icon"><i class="fa fa-bed"></i></span>';
    modal += '<div>';
    modal += '<strong>' + escapeHtml(selectedBed.roomName) + ' - ' + escapeHtml(selectedBed.floor) + '</strong>';
    modal += '<span>Estado: ' + escapeHtml(bedStateLabel(selectedBed.state)) + '</span>';

    if (selectedBed.state === 'ocupada') {
      modal += '<span>Paciente: ' + escapeHtml(selectedBed.patient) + '</span>';
    } else {
      modal += '<span>Sin paciente activo</span>';
    }

    modal += '</div>';
    modal += '</div>';
    modal += '<div class="bed-modal-actions">';

    if (selectedBed.state === 'ocupada') {
      modal += '<button type="button" class="btn btn-primary" onclick="openSelectedBedDetails()"><i class="fa fa-id-card"></i><br>Ver paciente</button>';
      modal += '<button type="button" class="btn btn-success" onclick="releaseSelectedBed(\'disponible\')"><i class="fa fa-check-circle"></i><br>Liberar</button>';
      modal += '<button type="button" class="btn btn-warning" onclick="releaseSelectedBed(\'preparacion\')"><i class="fa fa-broom"></i><br>Desinfectar</button>';
    } else if (selectedBed.state === 'disponible') {
      modal += '<button type="button" class="btn btn-success" onclick="assignSelectedBed()"><i class="fa fa-user-plus"></i><br>Asignar</button>';
      modal += '<button type="button" class="btn btn-warning" onclick="setSelectedBedState(\'preparacion\')"><i class="fa fa-broom"></i><br>Desinfectar</button>';
      modal += '<button type="button" class="btn btn-secondary" onclick="setSelectedBedState(\'no_disponible\')"><i class="fa fa-ban"></i><br>No disponible</button>';
    } else {
      modal += '<button type="button" class="btn btn-success" onclick="setSelectedBedState(\'disponible\')"><i class="fa fa-check-circle"></i><br>Libre</button>';
      modal += '<button type="button" class="btn btn-warning" onclick="setSelectedBedState(\'preparacion\')"><i class="fa fa-broom"></i><br>Desinfeccion</button>';
      modal += '<button type="button" class="btn btn-secondary" onclick="setSelectedBedState(\'no_disponible\')"><i class="fa fa-ban"></i><br>No disponible</button>';
    }

    modal += '</div>';
    if (selectedBed.state === 'ocupada') {
      modal += renderPatientTransferSelector('modalTransfer');
    }
    modal += renderRoomMoveSelector('modalMoveRoom', 'moveSelectedBedFromSelect(\'modalMoveRoom\')');
    modal += '</div>';

    $('#content').addClass('bed-quick-modal').html(modal);
    $('#calendarModal').modal('show');
  }

  function openSelectedBedDetails() {
    if (selectedBed && selectedBed.detailsUrl) {
      location.href = selectedBed.detailsUrl;
    }
  }

  function assignSelectedBed() {
    if (selectedBed) {
      loadUrl(selectedBed.id);
    }
  }

  function releaseSelectedBed(finalState) {
    if (!selectedBed) {
      return;
    }

    Swal.fire({
      title: 'Confirmar liberacion',
      text: finalState === 'preparacion' ? 'La cama quedara en proceso de desinfeccion.' : 'La cama quedara libre.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Si, continuar',
      cancelButtonText: 'Cancelar'
    }).then(function(result) {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: bedRoutes.release,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken() },
        data: {
          id_cama: selectedBed.id,
          estado_final: finalState
        },
        success: function(response) {
          refreshBedBoard().done(function() {
            Swal.fire('Listo', response.message || 'Cama liberada.', 'success');
          });
        },
        error: function(xhr) {
          Swal.fire('No se pudo completar', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Revise la cama seleccionada.', 'error');
        }
      });
    });
  }

  function setSelectedBedState(state) {
    if (!selectedBed) {
      return;
    }

    $.ajax({
      url: bedRoutes.state,
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken() },
      data: {
        id_cama: selectedBed.id,
        estado_cama: state
      },
      success: function(response) {
        refreshBedBoard().done(function() {
          Swal.fire('Listo', response.message || 'Estado actualizado.', 'success');
        });
      },
      error: function(xhr) {
        Swal.fire('No se pudo completar', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Revise la cama seleccionada.', 'error');
      }
    });
  }

  function moveSelectedBedToRoom(roomId, roomName) {
    if (!selectedBed || selectedBed.roomId == roomId) {
      return;
    }

    Swal.fire({
      title: 'Mover cama',
      text: 'La cama ' + selectedBed.code + ' pasara a ' + roomName + '.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Mover cama',
      cancelButtonText: 'Cancelar'
    }).then(function(result) {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: bedRoutes.move,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken() },
        data: {
          id_cama: selectedBed.id,
          id_habitacion: roomId
        },
        success: function(response) {
          refreshBedBoard().done(function() {
            Swal.fire('Listo', response.message || 'Cama movida.', 'success');
          });
        },
        error: function(xhr) {
          Swal.fire('No se pudo mover', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Revise la habitacion destino.', 'error');
        }
      });
    });
  }

  function moveSelectedBedFromSelect(selectId) {
    var roomId = $('#' + selectId).val();
    var roomName = $('#' + selectId + ' option:selected').text();
    moveSelectedBedToRoom(roomId, roomName);
  }

  $(document).on('dragstart', '.bed-unit', function(event) {
    selectBedFromElement(this, false);
    event.originalEvent.dataTransfer.setData('text/plain', $(this).data('bed-id'));
    $('.bed-room-card').addClass('drop-ready');
  });

  $(document).on('dragend', '.bed-unit', function() {
    $('.bed-room-card').removeClass('drop-ready drop-active');
  });

  $(document).on('dragover', '.bed-room-card', function(event) {
    event.preventDefault();
  });

  $(document).on('dragenter', '.bed-room-card', function() {
    $(this).addClass('drop-active');
  });

  $(document).on('dragleave', '.bed-room-card', function() {
    $(this).removeClass('drop-active');
  });

  $(document).on('drop', '.bed-room-card', function(event) {
    event.preventDefault();
    $('.bed-room-card').removeClass('drop-ready drop-active');
    moveSelectedBedToRoom($(this).data('room-id'), $(this).data('room-name'));
  });

  function applyBedFilters() {
    var selectedType = $('#tipoh').val();
    var selectedFloor = $('#pisomaster').val();
    var search = normalizeSearchText($('#bedSearch').val());
    var visibleRooms = 0;

    $('.floor-block').each(function() {
      var floor = $(this).data('floor').toString();
      var showFloor = selectedFloor === '' || selectedFloor === floor;
      var floorHasRooms = false;

      $(this).find('.bed-room-card').each(function() {
        var roomType = $(this).data('room-type').toString();
        var roomSearch = normalizeSearchText($(this).data('search'));
        var typeOk = selectedType === '' || selectedType === roomType;
        var searchOk = search === '' || roomSearch.indexOf(search) !== -1;
        var roomHasVisibleBed = false;

        $(this).find('.bed-unit').each(function() {
          var bedState = ($(this).data('bed-state') || '').toString();
          var bedSearch = normalizeSearchText($(this).data('search'));
          var statusOk = currentStatusFilter === '' || currentStatusFilter === bedState;
          var bedSearchOk = search === '' || bedSearch.indexOf(search) !== -1 || roomSearch.indexOf(search) !== -1;
          var showBed = showFloor && typeOk && statusOk && bedSearchOk;

          $(this).toggle(showBed);
          if (showBed) {
            roomHasVisibleBed = true;
          }
        });

        var showRoom = showFloor && typeOk && searchOk && roomHasVisibleBed;
        $(this).toggle(showRoom);
        if (showRoom) {
          floorHasRooms = true;
          visibleRooms++;
        }
      });

      $(this).toggle(showFloor && floorHasRooms);
    });

    $('#bedEmptyState').toggle(visibleRooms === 0);
  }

  function muestra(valor) {
    applyBedFilters();
  }

  function pisos(valor) {
    applyBedFilters();
  }

  $(document).on('input', '#bedSearch', applyBedFilters);

  function getOccupiedPatientBeds() {
    var patients = [];
    $('.bed-unit.ocupada').each(function() {
      var $bed = $(this);
      var searchText = [
        $bed.data('search') || '',
        $bed.data('patient') || '',
        $bed.data('patient-id') || '',
        $bed.data('sala') || '',
        $bed.data('room-name') || '',
        $bed.data('floor-label') || '',
        $bed.data('bed-code') || ''
      ].join(' ');

      patients.push({
        element: this,
        bedId: $bed.data('bed-id'),
        bedCode: $bed.data('bed-code'),
        roomCode: $bed.data('room-code'),
        roomName: $bed.data('room-name'),
        floor: $bed.data('floor-label'),
        patient: $bed.data('patient') || '',
        patientId: $bed.data('patient-id') || '',
        sala: $bed.data('sala') || 'Sin sala registrada',
        causa: $bed.data('causa') || '',
        fecha: $bed.data('fecha') || '',
        detailsUrl: $bed.data('details-url') || '',
        search: normalizeSearchText(searchText)
      });
    });
    return patients;
  }

  function renderPatientFinderResults() {
    var query = normalizeSearchText($('#patientFinder').val());
    var $results = $('#patientFinderResults');

    if (query.length < 2) {
      $results.html('<div class="patient-result-empty">Escriba al menos 2 caracteres para ubicar un paciente hospitalizado.</div>');
      return;
    }

    var matches = getOccupiedPatientBeds().filter(function(item) {
      return item.search.indexOf(query) !== -1;
    });

    if (matches.length === 0) {
      $results.html('<div class="patient-result-empty">No se encontro un paciente hospitalizado con esa busqueda.</div>');
      return;
    }

    var html = '';
    matches.forEach(function(item, index) {
      html += '<div class="patient-result-card" data-result-index="' + index + '">';
      html += '<strong>' + escapeHtml(item.patient) + '</strong>';
      if (item.patientId) {
        html += '<span>Cedula: ' + escapeHtml(item.patientId) + '</span>';
      }
      html += '<span>Sala: ' + escapeHtml(item.sala) + '</span>';
      html += '<div class="patient-result-location">';
      html += '<em>' + escapeHtml(item.floor) + '</em>';
      html += '<em>Hab. ' + escapeHtml(item.roomCode) + '</em>';
      html += '<em>Cama ' + escapeHtml(item.bedCode) + '</em>';
      html += '</div>';
      if (item.fecha) {
        html += '<span>Ingreso: ' + escapeHtml(item.fecha) + '</span>';
      }
      html += '<span class="patient-result-action"><i class="fa fa-map-marker-alt"></i>&nbsp; Ver ubicacion y datos</span>';
      html += '</div>';
    });

    $results.html(html);
    $results.data('matches', matches);
  }

  $(document).on('input', '#patientFinder', renderPatientFinderResults);

  function focusPatientResult(item) {
    if (!item || !item.element) {
      return;
    }

    $('#tipoh').val('');
    $('#pisomaster').val('');
    $('#bedSearch').val('');
    currentStatusFilter = '';
    $('.bed-chip').removeClass('active');
    $('.bed-chip[data-status-filter=""]').addClass('active');
    applyBedFilters();

    var $bed = $(item.element);
    $('html, body').animate({ scrollTop: $bed.offset().top - 140 }, 250);
    $('.bed-unit').removeClass('search-focus');
    $bed.addClass('search-focus');
    setTimeout(function() {
      $bed.removeClass('search-focus');
    }, 3000);
    selectBedFromElement(item.element, true);
  }

  $(document).on('click', '.patient-result-card', function() {
    var matches = $('#patientFinderResults').data('matches') || [];
    var selected = matches[$(this).data('result-index')];
    focusPatientResult(selected);
  });

  $(document).on('click', '.bed-chip', function() {
    $('.bed-chip').removeClass('active');
    $(this).addClass('active');
    currentStatusFilter = ($(this).data('status-filter') || '').toString();
    applyBedFilters();
  });
</script>
<script type="text/javascript">
  var pacientes_memory = [];

  function bigImg(x) {}

  function normalImg(x) {}

  function promise(d, e) {
    $.ajax({
      url: "{{route('cuartos.get_paciente')}}",
      method: "POST",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      data: {
        'id': d,
        'status': '1'
      },
      success: function(data) {
        if ((pacientes_memory.length) > 0) {
          Swal.fire("{{trans('hospitalizacion.Mensaje')}}", "{{trans('hospitalizacion.PacienteYaSeleccionado')}}", 'error');
        } else {
          $('#anadir').html(data);
          pacientes_memory.push({ id: d });
          $(e).attr('disabled', 'disabled');
        }
      },
      error: function(xhr, status, error) {
        alert("{{trans('hospitalizacion.ErrorContactarSoporte')}}");
      }
    });
  }

  @if(request()->filled('traspaso') && $enespera->contains('id', (int) request('traspaso')))
  $(function() {
    var transferId = '{{ (int) request('traspaso') }}';
    var transferButton = document.getElementById('pending-transfer-' + transferId);
    if (transferButton) promise(transferId, transferButton);
  });
  @endif

  function freehabitation() {
    Swal.fire({
      title: "{{trans('hospitalizacion.ConfirmarAccion')}}",
      text: "{{trans('hospitalizacion.LiberarCamasTexto')}}",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: "{{trans('hospitalizacion.Si')}}"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{route('hospitalizacion.freehabitation')}}",
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json'
          },
          success: function(response) {
            refreshBedBoard().done(function() {
              Swal.fire('Listo', response.message || 'Las camas fueron actualizadas.', 'success');
            });
          },
          error: function(xhr) {
            var message = xhr.responseJSON && xhr.responseJSON.message
              ? xhr.responseJSON.message
              : 'No se pudieron actualizar las camas.';
            Swal.fire('No se pudo completar', message, 'error');
          }
        });
      }
    })
  }

  function loadUrl(id) {
    if ((pacientes_memory.length) > 0) {
      $.ajax({
        url: "{{route('cuartos.get_paciente')}}",
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
          'id': id,
          'status': '2'
        },
        success: function(data) {
          if (data.state == 'success') {
            var dataurl = pacientes_memory[0].id;
            pacientes_memory = [];
            location.href = "{{url('hospital/cuarto/paciente/')}}/" + id + "/" + dataurl;
          } else {
            Swal.fire("{{trans('hospitalizacion.Mensaje')}}", "{{trans('hospitalizacion.PacienteAsignado')}}", 'error');
          }
        },
        error: function(xhr, status, error) {
          alert("{{trans('hospitalizacion.ErrorContactarSoporte')}}");
        }
      });
    } else {
      Swal.fire("{{trans('hospitalizacion.Mensaje')}}", "{{trans('hospitalizacion.SeleccionePacientePrimero')}}", 'error');
    }
  }

  function getModal(id) {
    $('#calendarModal .modal-dialog').addClass('modal-xl').removeClass('modal-dialog-centered').css('max-width', '');
    $('#content').removeClass('bed-quick-modal');
    $.ajax({
      type: "get",
      url: "{{route('cuartos.modal_paciente')}}",
      data: {
        'id': id,
      },
      datatype: "html",
      success: function(datahtml, data) {
        $("#content").html(datahtml);
        $("#calendarModal").modal("show");
      },
      error: function() {
        alert("{{trans('hospitalizacion.ErrorCargar')}}");
      }
    });
  }

  function reset(e) {
    $(e).parent().parent().parent().remove();
    $('.bsz').attr('disabled', false);
    pacientes_memory = [];
    $('#anadir').html('Seleccione un paciente en espera y luego haga clic en una cama libre.');
  }

  $('#calendarModal').on('hidden.bs.modal', function() {
    $('#content').removeClass('bed-quick-modal').empty();
    $('#calendarModal .modal-dialog').addClass('modal-xl').removeClass('modal-dialog-centered').css('max-width', '');
  });
</script>

<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
@endsection
