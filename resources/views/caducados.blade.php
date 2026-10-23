@extends('layouts.app-template')

@section('content')

<?php
$rolUsuario = Auth::user()->id_tipo_usuario;
?>

<style>

    .vigencia-clean-wrapper {
        background: #f4f7ff;
        min-height: calc(100vh - 50px) !important;
        padding: 0;
    }

    .vigencia-clean-wrapper .content {
        margin: 0;
    }
    :root {
        --vig-bg: #f4f7ff;
        --vig-ink: #081b3d;
        --vig-muted: #65748d;
        --vig-line: #dfe7f3;
        --vig-blue: #3f6df6;
        --vig-purple: #6547f3;
        --vig-red: #f04455;
        --vig-red-dark: #9b1c1f;
        --vig-orange: #ff8a00;
        --vig-yellow: #f6b517;
        --vig-green: #13a36f;
        --vig-teal: #12b8b0;
        --vig-white: #ffffff;
    }

    .vigencia-page {
        min-height: calc(100vh - 70px);
        padding: 18px;
        background:
            radial-gradient(circle at 8% 4%, rgba(101, 71, 243, .10), transparent 28%),
            radial-gradient(circle at 94% 8%, rgba(18, 184, 176, .12), transparent 30%),
            linear-gradient(180deg, #f7f9ff 0%, #eef4ff 100%);
        color: var(--vig-ink);
    }

    .vigencia-shell {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
    }

    .vigencia-hero {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        padding: 28px 30px;
        margin-bottom: 18px;
        min-height: 145px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        color: #ffffff;
        background:
            linear-gradient(120deg, rgba(101, 71, 243, .98) 0%, rgba(52, 108, 246, .96) 52%, rgba(19, 163, 111, .88) 100%),
            url("data:image/svg+xml,%3Csvg width='900' height='220' viewBox='0 0 900 220' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.24)' stroke-width='2'%3E%3Cpath d='M20 170 C140 40,250 250,380 100 S640 10,880 120'/%3E%3Cpath d='M120 195 C210 90,330 190,435 60 S650 70,780 15'/%3E%3C/g%3E%3Cg fill='rgba(255,255,255,0.12)'%3E%3Ccircle cx='760' cy='60' r='52'/%3E%3Ccircle cx='835' cy='145' r='74'/%3E%3C/g%3E%3C/svg%3E");
        background-size: cover;
        box-shadow: 0 18px 36px rgba(63, 109, 246, .25);
    }

    .vigencia-hero:before {
        content: "";
        position: absolute;
        right: 260px;
        top: -60px;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .10);
        filter: blur(1px);
    }

    .vigencia-hero:after {
        content: "";
        position: absolute;
        right: 24px;
        bottom: -48px;
        width: 280px;
        height: 170px;
        background:
            linear-gradient(90deg, rgba(255,255,255,.18), rgba(255,255,255,.04)),
            url("data:image/svg+xml,%3Csvg width='260' height='150' viewBox='0 0 260 150' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.42)' stroke-width='4' stroke-linecap='round'%3E%3Cpath d='M30 120V70c0-18 24-18 24 0v50'/%3E%3Cpath d='M90 120V45c0-18 24-18 24 0v75'/%3E%3Cpath d='M150 120V62c0-18 24-18 24 0v58'/%3E%3Cpath d='M205 118h35'/%3E%3Cpath d='M222 100v35'/%3E%3C/g%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right center;
        opacity: .75;
    }

    .vigencia-hero-info,
    .vigencia-hero-actions {
        position: relative;
        z-index: 2;
    }

    .vigencia-hero-info {
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 0;
    }

    .vigencia-hero-icon {
        width: 62px;
        height: 62px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,.18);
        border: 1px solid rgba(255,255,255,.24);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.16);
        font-size: 28px;
        flex: 0 0 62px;
    }

    .vigencia-title {
        margin: 0;
        color: #ffffff;
        font-weight: 950;
        font-size: 34px;
        letter-spacing: -.7px;
        line-height: 1.08;
    }

    .vigencia-subtitle {
        color: rgba(255,255,255,.88);
        margin-top: 8px;
        font-size: 16px;
        font-weight: 500;
        max-width: 760px;
    }

    .btn-export-vigencia {
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(255,255,255,.7);
        color: #12335f;
        padding: 14px 20px;
        border-radius: 14px;
        font-weight: 900;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 14px 26px rgba(15, 23, 42, .18);
        white-space: nowrap;
        transition: all .18s ease-in-out;
    }

    .btn-export-vigencia:hover {
        color: #ffffff;
        background: #13a36f;
        border-color: #13a36f;
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(19, 163, 111, .28);
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(150px, 1fr)) minmax(270px, 1.25fr);
        gap: 14px;
        margin-bottom: 16px;
    }

    .kpi-card,
    .chart-card,
    .filters-wrap,
    .table-card,
    .vigencia-bottom-strip {
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(218, 228, 244, .9);
        box-shadow: 0 14px 32px rgba(18, 39, 75, .08);
        backdrop-filter: blur(10px);
    }

    .kpi-card {
        position: relative;
        min-height: 168px;
        padding: 18px;
        border-radius: 18px;
        color: inherit;
        overflow: hidden;
        text-decoration: none !important;
        display: block;
        transition: all .18s ease-in-out;
    }

    .kpi-card:after {
        content: "";
        position: absolute;
        left: -18px;
        right: -18px;
        bottom: -45px;
        height: 88px;
        opacity: .16;
        border-radius: 50% 50% 0 0;
        transform: rotate(-3deg);
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 38px rgba(18, 39, 75, .14);
    }

    .kpi-card.active {
        border-color: currentColor;
        box-shadow: 0 18px 42px rgba(63, 109, 246, .20);
    }

    .kpi-card.vigente:after { background: var(--vig-blue); }
    .kpi-card.caducado:after { background: var(--vig-red-dark); }
    .kpi-card.usar_primero:after { background: var(--vig-red); }
    .kpi-card.usar_segundo:after { background: var(--vig-orange); }
    .kpi-card.usar_tercero:after { background: var(--vig-green); }

    .kpi-top {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
        box-shadow: 0 12px 22px rgba(15, 23, 42, .16);
    }

    .kpi-number {
        font-size: 30px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.4px;
    }

    .kpi-label {
        font-weight: 900;
        color: #13213b;
        margin-top: 7px;
        font-size: 14px;
        line-height: 1.18;
    }

    .kpi-percent {
        position: relative;
        z-index: 2;
        color: var(--vig-muted);
        font-size: 13px;
        margin-top: 18px;
        font-weight: 700;
    }

    .sparkline {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 30px;
        margin-top: 16px;
    }

    .kpi-card.vigente .kpi-icon,
    .filter-pill.vigente.active,
    .badge-vigente,
    .table-row-icon.vigente {
        background: #145cff;
    }

    .kpi-card.caducado .kpi-icon,
    .filter-pill.caducado.active,
    .badge-caducado,
    .table-row-icon.caducado {
        background: #9b1c1f;
    }

    .kpi-card.usar_primero .kpi-icon,
    .filter-pill.usar_primero.active,
    .badge-usar_primero,
    .table-row-icon.usar_primero {
        background: #f04455;
    }

    .kpi-card.usar_segundo .kpi-icon,
    .filter-pill.usar_segundo.active,
    .badge-usar_segundo,
    .table-row-icon.usar_segundo {
        background: #ff8a00;
    }

    .kpi-card.usar_tercero .kpi-icon,
    .filter-pill.usar_tercero.active,
    .badge-usar_tercero,
    .table-row-icon.usar_tercero {
        background: #13a36f;
    }

    .kpi-card.vigente,
    .kpi-card.vigente .kpi-number,
    .kpi-card.vigente .sparkline { color: var(--vig-blue); }

    .kpi-card.caducado,
    .kpi-card.caducado .kpi-number,
    .kpi-card.caducado .sparkline { color: var(--vig-red-dark); }

    .kpi-card.usar_primero,
    .kpi-card.usar_primero .kpi-number,
    .kpi-card.usar_primero .sparkline { color: var(--vig-red); }

    .kpi-card.usar_segundo,
    .kpi-card.usar_segundo .kpi-number,
    .kpi-card.usar_segundo .sparkline { color: var(--vig-orange); }

    .kpi-card.usar_tercero,
    .kpi-card.usar_tercero .kpi-number,
    .kpi-card.usar_tercero .sparkline { color: var(--vig-green); }

    .chart-card {
        min-height: 168px;
        padding: 18px;
        border-radius: 18px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .chart-title {
        font-weight: 950;
        color: #13213b;
        margin-bottom: 10px;
        font-size: 15px;
    }

    .chart-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .donut-chart {
        width: 118px;
        height: 118px;
        border-radius: 50%;
        position: relative;
        flex-shrink: 0;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.3), 0 14px 28px rgba(15,23,42,.10);
    }

    .donut-chart::after {
        content: 'Total';
        width: 58px;
        height: 58px;
        background: white;
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        text-align: center;
        line-height: 58px;
        border-radius: 50%;
        position: absolute;
        left: 30px;
        top: 30px;
        box-shadow: inset 0 0 0 1px #eef2f7;
    }

    .legend-list {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 12px;
        width: 100%;
    }

    .legend-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
        color: #344054;
        font-weight: 800;
    }

    .legend-left {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .legend-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
        flex: 0 0 9px;
    }

    .filters-wrap {
        position: relative;
        z-index: 50;
        overflow: visible !important;
        border-radius: 18px;
        padding: 12px;
        margin-bottom: 16px;
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-pill {
        border: 1px solid #dce6f5;
        background: #ffffff;
        color: #26364d;
        border-radius: 13px;
        min-height: 44px;
        padding: 10px 15px;
        font-weight: 900;
        font-size: 13px;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        white-space: nowrap;
        transition: all .16s ease-in-out;
    }

    .filter-pill:hover {
        background: #f5f8ff;
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(18,39,75,.08);
    }

    .filter-pill.active {
        color: #ffffff !important;
        border-color: transparent;
        box-shadow: 0 12px 22px rgba(63, 109, 246, .20);
    }

    .filter-pill.todos.active {
        background: var(--vig-blue);
    }

    .filter-pill.por_vencer.active {
        background: #f6b517;
    }

    .search-vigencia-box {
        position: relative;
        z-index: 60;
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 420px;
        max-width: 680px;
        flex: 1;
    }

    .search-vigencia-input-wrap {
        position: relative;
        z-index: 70;
        width: 100%;
    }

    .search-vigencia-input-wrap i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #667085;
        font-size: 14px;
    }

    .search-vigencia-input {
        width: 100%;
        height: 46px;
        border: 1px solid #dce6f5;
        border-radius: 15px;
        padding: 10px 14px 10px 42px;
        color: #1f2937;
        font-weight: 700;
        outline: none;
        background: #fff;
    }

    .search-vigencia-input:focus {
        border-color: var(--vig-purple);
        box-shadow: 0 0 0 4px rgba(101, 71, 243, .10);
    }

    .btn-search-vigencia {
        height: 46px;
        border: 0;
        border-radius: 15px;
        background: var(--vig-blue);
        color: #fff;
        padding: 0 18px;
        font-weight: 950;
        white-space: nowrap;
        box-shadow: 0 10px 20px rgba(63, 109, 246, .22);
    }

    .btn-clear-search {
        height: 46px;
        width: 46px;
        border-radius: 15px;
        border: 1px solid #dce6f5;
        background: #fff;
        color: #667085;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
    }

    .autocomplete-list {
        position: absolute;
        top: 52px;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #dce6f5;
        border-radius: 15px;
        box-shadow: 0 14px 30px rgba(16, 24, 40, .18);
        z-index: 10000;
        max-height: 280px;
        overflow-x: hidden;
        overflow-y: auto;
        display: none;
    }

    .autocomplete-item {
        padding: 12px 15px;
        cursor: pointer;
        font-weight: 800;
        color: #26364d;
        border-bottom: 1px solid #eef2f7;
        font-size: 13px;
    }

    .autocomplete-item:hover {
        background: #f5f8ff;
    }

    .table-card {
        position: relative;
        z-index: 1;
        border-radius: 18px;
        padding: 12px;
        overflow: hidden;
    }

    .vigencia-table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .vigencia-table thead th {
        background: #ffffff;
        color: #344054;
        font-weight: 950;
        font-size: 11px;
        text-transform: uppercase;
        border-bottom: 1px solid #dce6f5 !important;
        padding: 15px 10px !important;
        white-space: nowrap;
        letter-spacing: .25px;
    }

    .vigencia-table tbody td {
        padding: 14px 10px !important;
        border-top: 1px solid #edf2fa !important;
        color: #14213f;
        font-size: 12.5px;
        vertical-align: middle !important;
        font-weight: 600;
    }

    .vigencia-table tfoot td {
        padding: 14px 10px !important;
        border-top: 2px solid #d7e0ea !important;
        background: #f8fafc;
        color: #12335f;
        font-size: 13px;
        vertical-align: middle !important;
    }

    .vigencia-table tbody tr:hover {
        background: #f7fbff !important;
    }

    .table-row-icon {
        width: 38px;
        height: 38px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: 0 10px 18px rgba(15, 23, 42, .12);
    }

    .codigo-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .codigo-text {
        font-weight: 950;
        color: #145cff;
        line-height: 1.15;
    }

    .td-truncate {
        max-width: 190px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .status-badge {
        color: #ffffff;
        padding: 8px 11px;
        border-radius: 10px;
        font-size: 10.5px;
        font-weight: 950;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 105px;
        text-align: center;
        letter-spacing: .25px;
        line-height: 1.15;
        box-shadow: 0 8px 14px rgba(15, 23, 42, .12);
    }

    .vencimiento-tiempo {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 105px;
        padding: 8px 11px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 950;
        text-align: center;
        white-space: nowrap;
    }

    .vencimiento-tiempo.vencido {
        background: #fee2e2;
        color: #b91c1c;
    }

    .vencimiento-tiempo.hoy {
        background: #ffe4e6;
        color: #e11d48;
    }

    .vencimiento-tiempo.proximo {
        background: #ffedd5;
        color: #c2410c;
    }

    .vencimiento-tiempo.vigente {
        background: #dff7eb;
        color: #047857;
    }

    .row-caducado {
        background: rgba(254, 226, 226, .60);
    }

    .row-usar_primero {
        background: rgba(255, 228, 230, .56);
    }

    .row-usar_segundo {
        background: rgba(255, 237, 213, .62);
    }

    .row-usar_tercero {
        background: rgba(220, 252, 231, .48);
    }

    .row-vigente {
        background: #ffffff;
    }

    .blink {
        animation: blink-animation 1s infinite;
    }

    @keyframes blink-animation {
        0% { opacity: 1; }
        50% { opacity: .52; }
        100% { opacity: 1; }
    }

    .total-cantidad-label {
        text-align: right;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .total-cantidad-value {
        text-align: center;
        font-weight: 950;
        font-size: 16px !important;
        color: var(--vig-blue) !important;
    }

    .total-cantidad-info {
        color: #667085 !important;
        font-weight: 700;
        font-size: 12px !important;
    }

    .pagination-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        padding: 15px 2px 2px;
        color: #667085;
        font-size: 13px;
        font-weight: 800;
    }

    .vigencia-bottom-strip {
        margin-top: 16px;
        border-radius: 18px;
        overflow: hidden;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        background: var(--vig-blue);
        color: #ffffff;
        box-shadow: 0 18px 36px rgba(63, 109, 246, .22);
    }

    .strip-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 20px;
        border-right: 1px solid rgba(255,255,255,.22);
    }

    .strip-item:last-child {
        border-right: 0;
    }

    .strip-icon {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        background: rgba(255,255,255,.16);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .strip-number {
        font-size: 22px;
        font-weight: 950;
        line-height: 1;
    }

    .strip-label {
        font-size: 12px;
        opacity: .92;
        margin-top: 4px;
        font-weight: 700;
    }

    .empty-box {
        text-align: center;
        padding: 54px 20px;
        color: #667085;
    }

    .empty-box i {
        font-size: 42px;
        color: #98a2b3;
        margin-bottom: 12px;
    }

    .modal-status-pill {
        color: #ffffff;
        border-radius: 999px;
        padding: 6px 9px;
        font-size: 10px;
        font-weight: 950;
        white-space: nowrap;
    }

    .modal-status-breakdown {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        min-width: 190px;
    }

    .modal-status-breakdown .modal-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        min-width: auto;
        box-shadow: 0 6px 12px rgba(15,23,42,.10);
    }

    .modal-status-pill.mixto {
        background: #64748b;
    }

    @media (max-width: 1450px) {
        .kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .chart-card {
            grid-column: span 3;
        }

        .vigencia-bottom-strip {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 992px) {
        .vigencia-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .vigencia-title {
            font-size: 28px;
        }

        .kpi-grid {
            grid-template-columns: 1fr;
        }

        .chart-card {
            grid-column: auto;
        }

        .filters-wrap {
            align-items: stretch;
        }

        .filter-pill {
            flex: 1 1 170px;
        }

        .search-vigencia-box {
            margin-left: 0;
            min-width: 100%;
            width: 100%;
            max-width: 100%;
        }

        .vigencia-bottom-strip {
            grid-template-columns: 1fr;
        }

        .strip-item {
            border-right: 0;
            border-bottom: 1px solid rgba(255,255,255,.20);
        }

        .strip-item:last-child {
            border-bottom: 0;
        }
    }

    @media (max-width: 640px) {
        .vigencia-page {
            padding: 12px;
        }

        .vigencia-hero {
            padding: 22px;
            border-radius: 18px;
        }

        .vigencia-hero-info {
            align-items: flex-start;
            flex-direction: column;
        }

        .search-vigencia-box {
            flex-direction: column;
        }

        .search-vigencia-input-wrap,
        .btn-search-vigencia,
        .btn-clear-search {
            width: 100%;
        }

        .btn-clear-search {
            height: 42px;
        }
    }

    /* ==============================
       MODAL ESTADÍSTICAS POR PRODUCTO
       ============================== */
    .codigo-estadistica-btn {
        border: 0;
        background: transparent;
        padding: 0;
        color: #145cff;
        font-weight: 950;
        line-height: 1.15;
        text-align: left;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .codigo-estadistica-btn:hover {
        color: var(--vig-purple);
        text-decoration: underline;
    }

    .codigo-estadistica-btn .codigo-mini-icon {
        width: 30px;
        height: 30px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: var(--vig-blue);
        box-shadow: 0 8px 16px rgba(63, 109, 246, .18);
        flex: 0 0 30px;
    }

    .modal-estadistica-producto .modal-dialog {
        width: 96%;
        max-width: 1180px;
        margin: 28px auto;
    }

    .modal-estadistica-producto .modal-content {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 28px 70px rgba(8, 27, 61, .28);
        background: #f8fbff;
    }

    .modal-estadistica-producto .modal-header {
        border-bottom: 1px solid #e4ebf7;
        background: radial-gradient(circle at 92% 0%, rgba(101, 71, 243, .13), transparent 32%), linear-gradient(135deg, #ffffff 0%, #f4f7ff 100%);
        padding: 22px 26px 16px;
    }

    .modal-estadistica-producto .modal-title {
        color: var(--vig-ink);
        font-size: 26px;
        font-weight: 950;
        letter-spacing: -.35px;
        margin: 0;
    }

    .modal-estadistica-producto .modal-subtitle {
        margin-top: 5px;
        color: var(--vig-muted);
        font-weight: 700;
        font-size: 13px;
    }

    .modal-estadistica-producto .close {
        opacity: .75;
        font-size: 30px;
        margin-top: -6px;
    }

    .modal-estadistica-producto .modal-body {
        padding: 18px 26px 22px;
    }

    .modal-estadistica-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 13px;
    }

    .modal-product-chip-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        min-height: 38px;
    }

    .modal-product-chip {
        border: 1px solid #dce6f5;
        background: #ffffff;
        color: #14213f;
        border-radius: 999px;
        padding: 8px 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 900;
        box-shadow: 0 8px 18px rgba(18,39,75,.06);
    }

    .modal-product-chip .chip-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--vig-blue);
    }

    .modal-product-chip button {
        border: 0;
        background: #edf2ff;
        color: #5b6b84;
        width: 19px;
        height: 19px;
        border-radius: 50%;
        line-height: 18px;
        padding: 0;
        font-weight: 900;
    }

    .btn-modal-soft {
        border: 1px solid #dce6f5;
        background: #ffffff;
        color: #26364d;
        border-radius: 12px;
        min-height: 38px;
        padding: 9px 13px;
        font-weight: 900;
        box-shadow: 0 8px 18px rgba(18,39,75,.06);
    }

    .btn-modal-soft:hover {
        color: #ffffff;
        background: var(--vig-red);
        border-color: var(--vig-red);
    }

    .modal-info-note {
        background: #eef5ff;
        border: 1px solid #d9e8ff;
        color: #315078;
        border-radius: 14px;
        padding: 10px 13px;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modal-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 13px;
        margin-bottom: 14px;
    }

    .modal-stat-card,
    .modal-chart-card,
    .modal-summary-card {
        background: #ffffff;
        border: 1px solid #dce6f5;
        border-radius: 16px;
        box-shadow: 0 12px 26px rgba(18, 39, 75, .07);
    }

    .modal-stat-card {
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .modal-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        flex: 0 0 52px;
    }

    .modal-stat-icon.blue { color: #145cff; background: #e6efff; }
    .modal-stat-icon.green { color: #059669; background: #dcfce7; }
    .modal-stat-icon.orange { color: #f97316; background: #ffedd5; }
    .modal-stat-icon.red { color: #e11d48; background: #ffe4e6; }

    .modal-stat-label {
        color: #344054;
        font-size: 12px;
        font-weight: 900;
    }

    .modal-stat-value {
        margin-top: 4px;
        color: var(--vig-ink);
        font-size: 25px;
        line-height: 1;
        font-weight: 950;
    }

    .modal-stat-help {
        margin-top: 4px;
        color: var(--vig-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .modal-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }

    .modal-chart-card,
    .modal-summary-card {
        padding: 15px;
    }

    .modal-card-title {
        color: #14213f;
        font-size: 15px;
        font-weight: 950;
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .modal-bar-chart {
        min-height: 185px;
        display: flex;
        align-items: flex-end;
        gap: 18px;
        padding: 18px 8px 8px;
        border-left: 1px solid #e5edf8;
        border-bottom: 1px solid #e5edf8;
        background-image: linear-gradient(to top, #edf2fa 1px, transparent 1px);
        background-size: 100% 38px;
    }

    .modal-bar-item {
        flex: 1;
        min-width: 42px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        height: 170px;
    }

    .modal-bar-value {
        color: #14213f;
        font-size: 12px;
        font-weight: 950;
        margin-bottom: 6px;
    }

    .modal-bar {
        width: 34px;
        min-height: 8px;
        border-radius: 10px 10px 3px 3px;
        background: linear-gradient(180deg, var(--vig-blue), var(--vig-purple));
        box-shadow: 0 10px 18px rgba(63, 109, 246, .18);
    }

    .modal-bar-label {
        margin-top: 8px;
        text-align: center;
        color: #344054;
        font-size: 10.5px;
        line-height: 1.15;
        font-weight: 900;
        max-width: 92px;
        word-break: break-word;
    }

    .modal-bar-label.truncate-label {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .modal-product-chip .chip-text {
        max-width: 260px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
        vertical-align: middle;
    }

    .modal-product-name-cell {
        display: inline-block;
        max-width: 280px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    .modal-bodega-chart-horizontal {
        min-height: auto;
        display: block;
        padding: 16px 12px;
        border-left: 0;
        border-bottom: 0;
        background-image: none;
    }

    .modal-bodega-row {
        display: grid;
        grid-template-columns: minmax(120px, 220px) 1fr 58px;
        gap: 12px;
        align-items: center;
        margin-bottom: 12px;
    }

    .modal-bodega-row:last-child {
        margin-bottom: 0;
    }

    .modal-bodega-name {
        color: #14213f;
        font-size: 12px;
        font-weight: 950;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .modal-bodega-track {
        height: 16px;
        border-radius: 999px;
        background: #edf2fa;
        overflow: hidden;
        box-shadow: inset 0 0 0 1px #dce6f5;
    }

    .modal-bodega-fill {
        height: 100%;
        min-width: 6px;
        border-radius: 999px;
        box-shadow: 0 8px 16px rgba(63, 109, 246, .18);
    }

    .modal-bodega-value {
        color: #14213f;
        font-size: 12px;
        font-weight: 950;
        text-align: right;
    }

    .modal-donut-area {
        display: flex;
        align-items: center;
        gap: 20px;
        min-height: 185px;
    }

    .modal-donut {
        width: 145px;
        height: 145px;
        border-radius: 50%;
        position: relative;
        flex: 0 0 145px;
        box-shadow: 0 14px 28px rgba(15,23,42,.10);
    }

    .modal-donut:after {
        content: attr(data-total);
        white-space: pre;
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #ffffff;
        position: absolute;
        left: 36px;
        top: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #14213f;
        font-size: 18px;
        font-weight: 950;
        box-shadow: inset 0 0 0 1px #e7edf7;
    }

    .modal-donut-legend {
        flex: 1;
        min-width: 0;
    }

    .modal-legend-row {
        display: grid;
        grid-template-columns: 10px 1fr auto;
        align-items: center;
        gap: 9px;
        margin-bottom: 9px;
        color: #344054;
        font-size: 12px;
        font-weight: 850;
    }

    .modal-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }


    .modal-summary-card-full {
        margin-top: 14px;
    }

    .modal-summary-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modal-summary-table th {
        background: #f4f7ff;
        color: #344054;
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        padding: 10px 8px;
        border-bottom: 1px solid #dce6f5;
    }

    .modal-summary-table td {
        color: #14213f;
        font-size: 12px;
        font-weight: 750;
        padding: 10px 8px;
        border-bottom: 1px solid #edf2fa;
        vertical-align: middle;
    }

    .modal-status-pill {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-width: 82px;
        padding: 6px 8px;
        border-radius: 9px;
        font-size: 10px;
        font-weight: 950;
        color: #fff;
        line-height: 1.1;
        text-align: center;
    }

    .modal-footer-actions {
        margin-top: 14px;
        padding-top: 13px;
        border-top: 1px solid #dce6f5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
    }

    .btn-modal-primary {
        border: 0;
        border-radius: 13px;
        min-height: 40px;
        padding: 10px 15px;
        background: var(--vig-blue);
        color: #ffffff;
        font-weight: 950;
        box-shadow: 0 12px 24px rgba(63, 109, 246, .20);
    }

    .modal-loading-box {
        padding: 24px;
        text-align: center;
        color: #64748b;
        font-weight: 900;
    }

    .modal-empty-box {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 28px;
        text-align: center;
        color: #64748b;
        font-weight: 850;
        background: #ffffff;
    }

    @media (max-width: 992px) {
        .modal-estadistica-producto .modal-dialog {
            width: calc(100% - 18px);
            margin: 9px auto;
        }

        .modal-stat-grid,
        .modal-grid-2 {
            grid-template-columns: 1fr;
        }

        .modal-donut-area {
            flex-direction: column;
            align-items: flex-start;
        }
    }


    /* Corrección solicitada: gráficos sin degradado, color sólido */
    .modal-bar,
    .modal-hbar-fill,
    .modal-bodega-bar-fill {
        background-image: none !important;
    }

</style>

<div class="content-wrapper cnt_wrapper vigencia-clean-wrapper">
<section class="content vigencia-page">
    <div class="vigencia-shell">

        @if(in_array($rolUsuario, [1, 7]) == true)

            @php
                $paramsBase = request()->except('page');

                $urlTodosParams = $paramsBase;
                unset($urlTodosParams['estado']);

                $urlTodos = url()->current() . (count($urlTodosParams) ? '?' . http_build_query($urlTodosParams) : '');

                $urlFiltro = function ($estado) use ($paramsBase) {
                    $params = array_merge($paramsBase, ['estado' => $estado]);
                    return url()->current() . '?' . http_build_query($params);
                };

                $urlExcelParams = request()->query();
                $urlExcelParams['estado'] = $filtro_estado;

                $total = $total_registros > 0 ? $total_registros : 1;

                $pVigente = round(($resumen['VIGENTE'] ?? 0) * 100 / $total, 1);
                $pCaducado = round(($resumen['CADUCADO'] ?? 0) * 100 / $total, 1);
                $pPrimero = round(($resumen['CADUCA EN 4 MESES'] ?? 0) * 100 / $total, 1);
                $pSegundo = round(($resumen['CADUCA EN 8 MESES'] ?? 0) * 100 / $total, 1);
                $pTercero = round(($resumen['CADUCA EN 12 MESES'] ?? 0) * 100 / $total, 1);

                $c1 = $pVigente;
                $c2 = $c1 + $pCaducado;
                $c3 = $c2 + $pPrimero;
                $c4 = $c3 + $pSegundo;
                $c5 = 100;

                $donutStyle = "
                    conic-gradient(
                        #3f6df6 0 {$c1}%,
                        #9b1c1f {$c1}% {$c2}%,
                        #f04455 {$c2}% {$c3}%,
                        #ff8a00 {$c3}% {$c4}%,
                        #13a36f {$c4}% {$c5}%
                    )
                ";

                $iconoFila = [
                    'vigente' => 'fa-check',
                    'caducado' => 'fa-times',
                    'usar_primero' => 'fa-arrow-up',
                    'usar_segundo' => 'fa-angle-double-right',
                    'usar_tercero' => 'fa-angle-double-right',
                ];

                $totalBodegas = isset($caducado) && method_exists($caducado, 'pluck') ? $caducado->pluck('bodega')->filter()->unique()->count() : 0;
                $totalMarcas = isset($caducado) && method_exists($caducado, 'pluck') ? $caducado->pluck('marca')->filter()->unique()->count() : 0;
                $totalProveedores = isset($caducado) && method_exists($caducado, 'pluck') ? $caducado->pluck('proveedor')->filter()->unique()->count() : 0;
            @endphp

            <div class="vigencia-hero">
                <div class="vigencia-hero-info">
                    <div class="vigencia-hero-icon">
                        <i class="fa fa-shield"></i>
                    </div>
                    <div>
                        <h2 class="vigencia-title">Control de Vigencia de Insumos Médicos</h2>
                        <div class="vigencia-subtitle">
                            Monitorea el estado de vencimiento del inventario en tiempo real y asegura la disponibilidad de los insumos.
                        </div>
                    </div>
                </div>

                <div class="vigencia-hero-actions">
                    <a href="{{ route('menu.exportar_caducados_excel', $urlExcelParams) }}" class="btn-export-vigencia">
                        <i class="fa fa-file-excel-o"></i>
                        Exportar Excel
                    </a>
                </div>
            </div>

            <div class="kpi-grid">

                @foreach($cards as $card)
                    <a href="{{ $urlFiltro($card['key']) }}"
                       class="kpi-card {{ $card['class'] }} {{ $filtro_estado == $card['key'] ? 'active' : '' }}">
                        <div class="kpi-top">
                            <div class="kpi-icon">
                                <i class="fa {{ $card['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="kpi-number">{{ $card['valor'] }}</div>
                                <div class="kpi-label">{{ $card['label'] }}</div>
                            </div>
                        </div>

                        <div class="kpi-percent">
                            {{ $card['porcentaje'] }}% del inventario
                        </div>

                        <svg class="sparkline" viewBox="0 0 160 30" preserveAspectRatio="none">
                            <polyline
                                fill="none"
                                stroke="currentColor"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                points="0,22 14,19 28,23 42,17 56,15 70,19 84,22 98,18 112,14 126,22 140,17 160,16"
                            />
                            <circle cx="160" cy="16" r="3.5" fill="currentColor"></circle>
                        </svg>
                    </a>
                @endforeach

                <div class="chart-card">
                    <div class="chart-title">Distribución por estado</div>

                    <div class="chart-content">
                        <div class="donut-chart" style="background: {{ $donutStyle }};"></div>

                        <ul class="legend-list">
                            <li>
                                <span class="legend-left">
                                    <span class="legend-dot" style="background:#3f6df6;"></span>
                                    Vigentes
                                </span>
                                <strong>{{ $resumen['VIGENTE'] ?? 0 }}</strong>
                            </li>
                            <li>
                                <span class="legend-left">
                                    <span class="legend-dot" style="background:#9b1c1f;"></span>
                                    Caducados
                                </span>
                                <strong>{{ $resumen['CADUCADO'] ?? 0 }}</strong>
                            </li>
                            <li>
                                <span class="legend-left">
                                    <span class="legend-dot" style="background:#f04455;"></span>
                                    Caduca en 4 meses
                                </span>
                                <strong>{{ $resumen['CADUCA EN 4 MESES'] ?? 0 }}</strong>
                            </li>
                            <li>
                                <span class="legend-left">
                                    <span class="legend-dot" style="background:#ff8a00;"></span>
                                    Caduca en 8 meses
                                </span>
                                <strong>{{ $resumen['CADUCA EN 8 MESES'] ?? 0 }}</strong>
                            </li>
                            <li>
                                <span class="legend-left">
                                    <span class="legend-dot" style="background:#13a36f;"></span>
                                    Caduca en 12 meses
                                </span>
                                <strong>{{ $resumen['CADUCA EN 12 MESES'] ?? 0 }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="filters-wrap">
                <a href="{{ $urlTodos }}"
                   class="filter-pill todos {{ $filtro_estado == 'todos' ? 'active' : '' }}">
                    <i class="fa fa-th-large"></i> Todos
                </a>

                <a href="{{ $urlFiltro('vigentes') }}"
                   class="filter-pill vigente {{ $filtro_estado == 'vigentes' ? 'active' : '' }}">
                    <i class="fa fa-check-circle"></i> Vigentes
                </a>

                <a href="{{ $urlFiltro('caducados') }}"
                   class="filter-pill caducado {{ $filtro_estado == 'caducados' ? 'active' : '' }}">
                    <i class="fa fa-calendar-times-o"></i> Caducados
                </a>

                <a href="{{ $urlFiltro('usar_primero') }}"
                   class="filter-pill usar_primero {{ $filtro_estado == 'usar_primero' ? 'active' : '' }}">
                    <i class="fa fa-arrow-up"></i> Caduca en 4 meses
                </a>

                <a href="{{ $urlFiltro('usar_segundo') }}"
                   class="filter-pill usar_segundo {{ $filtro_estado == 'usar_segundo' ? 'active' : '' }}">
                    <i class="fa fa-angle-double-right"></i> Caduca en 8 meses
                </a>

                <a href="{{ $urlFiltro('usar_tercero') }}"
                   class="filter-pill usar_tercero {{ $filtro_estado == 'usar_tercero' ? 'active' : '' }}">
                    <i class="fa fa-angle-double-right"></i> Caduca en 12 meses
                </a>

                <a href="{{ $urlFiltro('por_vencer') }}"
                    class="filter-pill por_vencer {{ $filtro_estado == 'por_vencer' ? 'active' : '' }}">
                    <i class="fa fa-clock-o"></i> Por vencer 30 días
                </a>

                <form method="GET"
                      action="{{ url()->current() }}"
                      class="search-vigencia-box"
                      id="form-buscar-vigencia">

                    @foreach(request()->except(['page', 'buscar']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="search-vigencia-input-wrap">
                        <i class="fa fa-search"></i>

                        <input type="text"
                               name="buscar"
                               id="buscar_vigencia"
                               class="search-vigencia-input"
                               value="{{ $busqueda ?? '' }}"
                               placeholder="Buscar código, nombre, serie, bodega, proveedor..."
                               autocomplete="off">

                        <div id="autocomplete_vigencia" class="autocomplete-list"></div>
                    </div>

                    <button type="submit" class="btn-search-vigencia">
                        Buscar
                    </button>

                    @if(!empty($busqueda))
                        @php
                            $paramsClear = request()->except(['buscar', 'page']);
                            $urlClear = url()->current() . (count($paramsClear) ? '?' . http_build_query($paramsClear) : '');
                        @endphp

                        <a href="{{ $urlClear }}" class="btn-clear-search" title="Limpiar búsqueda">
                            <i class="fa fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-card">
                <div class="table-responsive">

                    @if($caducado->count() > 0)

                        <table width="100%" id="example2" class="table vigencia-table" role="grid">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Serie</th>
                                    <th>Pedido</th>
                                    <th>Bodega</th>
                                    <th>Proveedor</th>
                                    <th>Marca</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Fecha Vencimiento</th>
                                    <th class="text-center">Tiempo</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($caducado as $value)
                                    @php
                                        $textoTiempoVencimiento = '-';
                                        $claseTiempoVencimiento = 'vigente';

                                        try {
                                            $hoy = \Carbon\Carbon::now()->startOfDay();
                                            $fechaVence = \Carbon\Carbon::parse($value->fecha_vence)->startOfDay();

                                            if ($fechaVence->equalTo($hoy)) {
                                                $textoTiempoVencimiento = 'Vence hoy';
                                                $claseTiempoVencimiento = 'hoy';
                                            } elseif ($fechaVence->lessThan($hoy)) {
                                                if ($fechaVence->diffInYears($hoy) >= 1) {
                                                    $cantidad = $fechaVence->diffInYears($hoy);
                                                    $unidad = $cantidad == 1 ? 'año' : 'años';
                                                } elseif ($fechaVence->diffInMonths($hoy) >= 1) {
                                                    $cantidad = $fechaVence->diffInMonths($hoy);
                                                    $unidad = $cantidad == 1 ? 'mes' : 'meses';
                                                } else {
                                                    $cantidad = $fechaVence->diffInDays($hoy);
                                                    $unidad = $cantidad == 1 ? 'día' : 'días';
                                                }

                                                $textoTiempoVencimiento = 'Venció hace ' . $cantidad . ' ' . $unidad;
                                                $claseTiempoVencimiento = 'vencido';
                                            } else {
                                                if ($hoy->diffInYears($fechaVence) >= 1) {
                                                    $cantidad = $hoy->diffInYears($fechaVence);
                                                    $unidad = $cantidad == 1 ? 'año' : 'años';
                                                } elseif ($hoy->diffInMonths($fechaVence) >= 1) {
                                                    $cantidad = $hoy->diffInMonths($fechaVence);
                                                    $unidad = $cantidad == 1 ? 'mes' : 'meses';
                                                } else {
                                                    $cantidad = $hoy->diffInDays($fechaVence);
                                                    $unidad = $cantidad == 1 ? 'día' : 'días';
                                                }

                                                $textoTiempoVencimiento = 'Faltan ' . $cantidad . ' ' . $unidad;
                                                $claseTiempoVencimiento = $hoy->diffInDays($fechaVence) <= 30 ? 'proximo' : 'vigente';
                                            }
                                        } catch (\Exception $e) {
                                            $textoTiempoVencimiento = '-';
                                            $claseTiempoVencimiento = 'vigente';
                                        }

                                        $iconoEstadoFila = isset($iconoFila[$value->color_class]) ? $iconoFila[$value->color_class] : 'fa-cube';
                                    @endphp

                                    <tr class="row-{{ $value->color_class }}">
                                        <td>
                                            <button type="button"
                                                    class="codigo-estadistica-btn btnAbrirEstadisticaProducto"
                                                    data-codigo="{{ $value->codigo }}"
                                                    data-nombre="{{ $value->nombre }}"
                                                    title="Ver estadísticas de {{ $value->nombre }}">
                                                <span class="codigo-mini-icon">
                                                    <i class="fa {{ $iconoEstadoFila }}"></i>
                                                </span>
                                                <span>{{ $value->codigo }}</span>
                                            </button>
                                        </td>

                                        <td class="td-truncate" title="{{ $value->nombre }}">
                                            {{ $value->nombre }}
                                        </td>

                                        <td>{{ $value->serie }}</td>

                                        <td class="td-truncate" title="{{ $value->pedido_detalle }}">
                                            {{ $value->pedido_detalle }}
                                        </td>

                                        <td class="td-truncate" title="{{ $value->bodega ?? '-' }}">
                                            {{ $value->bodega ?? '-' }}
                                        </td>

                                        <td class="td-truncate" title="{{ $value->proveedor ?? '-' }}">
                                            {{ $value->proveedor ?? '-' }}
                                        </td>

                                        <td>{{ $value->marca }}</td>

                                        <td class="td-truncate" title="{{ $value->tipo }}">
                                            {{ $value->tipo }}
                                        </td>

                                        <td class="text-center">
                                            <strong>{{ $value->existencia }}</strong>
                                        </td>

                                        <td>
                                            {{ $value->fecha_vence }}
                                        </td>

                                        <td class="text-center">
                                            <span class="vencimiento-tiempo {{ $claseTiempoVencimiento }}">
                                                {{ $textoTiempoVencimiento }}
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <span class="status-badge badge-{{ $value->color_class }} {{ in_array($value->color_class, ['caducado', 'usar_primero']) ? 'blink' : '' }}">
                                                {{ $value->prioridad_uso }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="8" class="total-cantidad-label">
                                        Total cantidad según filtro actual
                                    </td>
                                    <td class="total-cantidad-value">
                                        {{ number_format($total_cantidad_filtrada ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td colspan="3" class="total-cantidad-info">
                                        Incluye todos los registros encontrados, aunque estén en otras páginas.
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="pagination-footer">
                            <div>
                                Mostrando
                                {{ 1 + (($caducado->currentPage() - 1) * $caducado->perPage()) }}
                                a
                                {{ count($caducado) + (($caducado->currentPage() - 1) * $caducado->perPage()) }}
                                de
                                {{ $caducado->total() }}
                                registros
                            </div>

                            <div>
                                {{ $caducado->appends(request()->query())->links() }}
                            </div>
                        </div>

                    @else

                        <div class="empty-box">
                            <i class="fa fa-inbox"></i>
                            <h4>No hay registros para este filtro</h4>
                            <p>Selecciona otro estado para visualizar más productos.</p>
                        </div>

                    @endif

                </div>
            </div>

        @endif

    </div>
</section>

<!-- MODAL ESTADÍSTICAS POR PRODUCTO -->
<div class="modal fade modal-estadistica-producto" id="modalEstadisticaProducto" tabindex="-1" role="dialog" aria-labelledby="modalEstadisticaProductoLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="modalEstadisticaProductoLabel">
                    Estadísticas del producto
                </h4>
                <div class="modal-subtitle">
                    Historial consolidado de los productos seleccionados al hacer clic en el código.
                </div>
            </div>

            <div class="modal-body">

                <div class="modal-estadistica-toolbar">
                    <div class="modal-product-chip-wrap" id="modalProductosSeleccionados">
                        <span class="modal-empty-box" style="padding:10px 14px;">Selecciona un código para iniciar.</span>
                    </div>

                    <button type="button" class="btn-modal-soft" id="btnLimpiarSeleccionProductos">
                        <i class="fa fa-trash-o"></i>
                        Limpiar selección
                    </button>
                </div>

                <div class="modal-info-note">
                    <i class="fa fa-info-circle"></i>
                    Se acumulan los productos que seleccionas desde la tabla. Las estadísticas se actualizan automáticamente.
                </div>

                <div id="modalProductoLoading" class="modal-loading-box" style="display:none;">
                    <i class="fa fa-spinner fa-spin"></i>
                    Cargando estadísticas...
                </div>

                <div id="modalProductoEmpty" class="modal-empty-box" style="display:none;">
                    <i class="fa fa-bar-chart"></i>
                    No hay datos para los productos seleccionados.
                </div>

                <div id="modalProductoContenido">

                    <div class="modal-stat-grid">
                        <div class="modal-stat-card">
                            <div class="modal-stat-icon blue">
                                <i class="fa fa-cubes"></i>
                            </div>
                            <div>
                                <div class="modal-stat-label">Total seleccionados</div>
                                <div class="modal-stat-value" id="statTotalSeleccionados">0</div>
                                <div class="modal-stat-help">productos</div>
                            </div>
                        </div>

                        <div class="modal-stat-card">
                            <div class="modal-stat-icon green">
                                <i class="fa fa-archive"></i>
                            </div>
                            <div>
                                <div class="modal-stat-label">Stock total</div>
                                <div class="modal-stat-value" id="statStockTotal">0</div>
                                <div class="modal-stat-help">unidades</div>
                            </div>
                        </div>

                        <div class="modal-stat-card">
                            <div class="modal-stat-icon orange">
                                <i class="fa fa-hourglass-half"></i>
                            </div>
                            <div>
                                <div class="modal-stat-label">Próximo a vencer</div>
                                <div class="modal-stat-value" id="statProximoVencer">0</div>
                                <div class="modal-stat-help" id="statProximoHelp">unidades</div>
                            </div>
                        </div>

                        <div class="modal-stat-card">
                            <div class="modal-stat-icon red">
                                <i class="fa fa-exclamation-circle"></i>
                            </div>
                            <div>
                                <div class="modal-stat-label">Caducados</div>
                                <div class="modal-stat-value" id="statCaducados">0</div>
                                <div class="modal-stat-help" id="statCaducadosHelp">unidades</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-grid-2">
                        <div class="modal-chart-card">
                            <div class="modal-card-title">
                                <span>Stock por producto</span>
                                <button type="button" class="btn-modal-soft" style="min-height:31px;padding:6px 10px;" onclick="exportarEstadisticaProductoCsv();">
                                    <i class="fa fa-download"></i>
                                    Exportar detalle
                                </button>
                            </div>
                            <div class="modal-bar-chart" id="modalBarChartProductos"></div>
                        </div>

                        <div class="modal-chart-card">
                            <div class="modal-card-title">Distribución por estado</div>
                            <div class="modal-donut-area">
                                <div class="modal-donut" id="modalDonutEstados" data-total="0&#10;Total"></div>
                                <div class="modal-donut-legend" id="modalDonutLegendEstados"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-chart-card modal-chart-card-full">
                        <div class="modal-card-title">Stock por bodega</div>
                        <div class="modal-bar-chart modal-bodega-chart-horizontal" id="modalBarChartBodegas"></div>
                    </div>

                    <div class="modal-summary-card modal-summary-card-full">
                        <div class="modal-card-title">Resumen por producto</div>
                        <div class="table-responsive">
                            <table class="modal-summary-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Bodega(s)</th>
                                        <th>Stock</th>
                                        <th>Estado por vencimiento</th>
                                    </tr>
                                </thead>
                                <tbody id="modalTablaResumenProductos"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer-actions">
                        <span>
                            <i class="fa fa-clock-o"></i>
                            Datos actualizados automáticamente al seleccionar códigos.
                        </span>
                        <button type="button" class="btn-modal-primary" onclick="exportarEstadisticaProductoCsv();">
                            <i class="fa fa-file-excel-o"></i>
                            Exportar reporte
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>

    var productosSeleccionadosEstadistica = [];
    var ultimoResultadoEstadistica = null;

    function obtenerCodigosSeleccionadosEstadistica() {
        return productosSeleccionadosEstadistica.map(function(item) {
            return item.codigo;
        });
    }

    function obtenerProductoSeleccionadoPorCodigo(codigo) {
        return productosSeleccionadosEstadistica.find(function(item) {
            return item.codigo === codigo;
        }) || null;
    }

    var coloresEstadoEstadistica = {
        vigente: '#3f6df6',
        caducado: '#9b1c1f',
        usar_primero: '#f04455',
        usar_segundo: '#ff8a00',
        usar_tercero: '#13a36f',
        mixto: '#64748b'
    };

    var labelsEstadoEstadistica = {
        vigente: 'Vigente',
        caducado: 'Caducado',
        usar_primero: 'Caduca en 4 meses',
        usar_segundo: 'Caduca en 8 meses',
        usar_tercero: 'Caduca en 12 meses',
        mixto: 'Mixto'
    };

    function htmlSeguro(valor) {
        return String(valor === null || valor === undefined ? '' : valor)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatoNumero(valor) {
        valor = parseFloat(valor || 0);
        return valor.toLocaleString('es-EC', { maximumFractionDigits: 0 });
    }

    function truncarTexto(texto, limite) {
        texto = String(texto === null || texto === undefined ? '' : texto);
        limite = limite || 28;

        if (texto.length <= limite) {
            return texto;
        }

        return texto.substring(0, limite - 3) + '...';
    }

    function agregarProductoEstadistica(codigo, nombre) {
        codigo = String(codigo || '').trim();
        nombre = String(nombre || '').trim();

        if (codigo === '') {
            return;
        }

        if (!obtenerProductoSeleccionadoPorCodigo(codigo)) {
            productosSeleccionadosEstadistica.push({
                codigo: codigo,
                nombre: nombre || codigo
            });
        }

        $('#modalEstadisticaProducto').modal('show');
        renderizarChipsProductos();
        cargarEstadisticasProductos();
    }

    function quitarProductoEstadistica(codigo) {
        productosSeleccionadosEstadistica = productosSeleccionadosEstadistica.filter(function(item) {
            return item.codigo !== codigo;
        });

        renderizarChipsProductos();

        if (productosSeleccionadosEstadistica.length === 0) {
            limpiarContenidoModalEstadistica();
            return;
        }

        cargarEstadisticasProductos();
    }

    function renderizarChipsProductos() {
        var contenedor = $('#modalProductosSeleccionados');
        contenedor.empty();

        if (productosSeleccionadosEstadistica.length === 0) {
            contenedor.html('<span class="modal-empty-box" style="padding:10px 14px;">Selecciona un producto para iniciar.</span>');
            return;
        }

        productosSeleccionadosEstadistica.forEach(function(producto, index) {
            var colorKeys = Object.keys(coloresEstadoEstadistica);
            var color = coloresEstadoEstadistica[colorKeys[index % colorKeys.length]];
            var codigoChip = producto.codigo || '';
            var nombreChip = producto.nombre || producto.codigo || '';
            var nombreCorto = truncarTexto(nombreChip, 42);

            contenedor.append(
                '<span class="modal-product-chip" title="' + htmlSeguro(nombreChip) + '">' +
                    '<span class="chip-dot" style="background:' + color + ';"></span>' +
                    '<span class="chip-text">' + htmlSeguro(nombreCorto) + '</span>' +
                    '<button type="button" class="btnQuitarProductoEstadistica" data-codigo="' + htmlSeguro(codigoChip) + '">&times;</button>' +
                '</span>'
            );
        });
    }

    function limpiarContenidoModalEstadistica() {
        ultimoResultadoEstadistica = null;
        $('#statTotalSeleccionados').text('0');
        $('#statStockTotal').text('0');
        $('#statProximoVencer').text('0');
        $('#statProximoHelp').text('unidades');
        $('#statCaducados').text('0');
        $('#statCaducadosHelp').text('unidades');
        $('#modalBarChartProductos').empty();
        $('#modalBarChartBodegas').empty();
        $('#modalDonutEstados').css('background', '#e5edf8').attr('data-total', '0\nTotal');
        $('#modalDonutLegendEstados').empty();
        $('#modalTablaResumenProductos').empty();
        $('#modalProductoEmpty').show();
        $('#modalProductoContenido').hide();
    }

    function cargarEstadisticasProductos() {
        if (productosSeleccionadosEstadistica.length === 0) {
            limpiarContenidoModalEstadistica();
            return;
        }

        $('#modalProductoLoading').show();
        $('#modalProductoEmpty').hide();
        $('#modalProductoContenido').hide();

        $.ajax({
            url: "{{ url('menu/estadisticas-caducados-producto') }}",
            method: 'GET',
            dataType: 'json',
            data: {
                codigos: obtenerCodigosSeleccionadosEstadistica()
            },
            success: function(resp) {
                ultimoResultadoEstadistica = resp;

                if (!resp || resp.ok === false || !resp.productos || resp.productos.length === 0) {
                    limpiarContenidoModalEstadistica();
                    return;
                }

                productosSeleccionadosEstadistica = productosSeleccionadosEstadistica.map(function(item) {
                    var encontrado = (resp.productos || []).find(function(p) {
                        return p.codigo === item.codigo;
                    });

                    return {
                        codigo: item.codigo,
                        nombre: encontrado && encontrado.nombre ? encontrado.nombre : item.nombre
                    };
                });

                renderizarChipsProductos();
                renderizarEstadisticaProducto(resp);
            },
            error: function() {
                $('#modalProductoEmpty').html(
                    '<i class="fa fa-warning"></i><br>' +
                    'No se pudo cargar la estadística. Revisa que exista la ruta AJAX del controlador.'
                ).show();
                $('#modalProductoContenido').hide();
            },
            complete: function() {
                $('#modalProductoLoading').hide();
            }
        });
    }

    function renderizarEstadisticaProducto(resp) {
        var resumen = resp.resumen || {};

        $('#modalProductoEmpty').hide();
        $('#modalProductoContenido').show();

        $('#statTotalSeleccionados').text(formatoNumero(resumen.total_productos || 0));
        $('#statStockTotal').text(formatoNumero(resumen.stock_total || 0));
        $('#statProximoVencer').text(formatoNumero(resumen.proximo_vencer || 0));
        $('#statProximoHelp').text('unidades (' + (resumen.porcentaje_proximo || 0) + '%)');
        $('#statCaducados').text(formatoNumero(resumen.caducados || 0));
        $('#statCaducadosHelp').text('unidades (' + (resumen.porcentaje_caducados || 0) + '%)');

        renderizarBarrasProductos(resp.productos || []);
        renderizarDonutEstados(resp.estados || {}, resumen.stock_total || 0);
        renderizarBarrasBodegas(resp.bodegas || []);
        renderizarTablaResumenProductos(resp.productos || []);
    }

    function renderizarBarrasProductos(productos) {
        var contenedor = $('#modalBarChartProductos');
        contenedor.empty();

        if (!productos.length) {
            contenedor.html('<div class="modal-empty-box">Sin productos para graficar.</div>');
            return;
        }

        var max = Math.max.apply(null, productos.map(function(p) {
            return parseFloat(p.stock || 0);
        }));

        if (max <= 0) {
            max = 1;
        }

        productos.forEach(function(p, index) {
            var altura = Math.max(8, Math.round((parseFloat(p.stock || 0) / max) * 135));
            var colorKeys = Object.keys(coloresEstadoEstadistica);
            var color = coloresEstadoEstadistica[p.estado_class] || coloresEstadoEstadistica[colorKeys[index % colorKeys.length]];
            var nombre = p.nombre || p.codigo || '';

            contenedor.append(
                '<div class="modal-bar-item">' +
                    '<div class="modal-bar-value">' + formatoNumero(p.stock || 0) + '</div>' +
                    '<div class="modal-bar" style="height:' + altura + 'px;background:' + color + ';"></div>' +
                    '<div class="modal-bar-label truncate-label" title="' + htmlSeguro(nombre) + '">' + htmlSeguro(truncarTexto(nombre, 34)) + '</div>' +
                '</div>'
            );
        });
    }

    function renderizarBarrasBodegas(bodegas) {
        var contenedor = $('#modalBarChartBodegas');
        contenedor.empty();

        if (!bodegas || !bodegas.length) {
            contenedor.html('<div class="modal-empty-box">Sin bodegas para graficar.</div>');
            return;
        }

        var max = Math.max.apply(null, bodegas.map(function(b) {
            return parseFloat(b.stock || 0);
        }));

        if (max <= 0) {
            max = 1;
        }

        bodegas.forEach(function(b, index) {
            var porcentaje = Math.max(4, Math.round((parseFloat(b.stock || 0) / max) * 100));
            var colorKeys = Object.keys(coloresEstadoEstadistica);
            var color = coloresEstadoEstadistica[colorKeys[index % colorKeys.length]];
            var nombreBodega = b.bodega || 'Sin bodega';

            contenedor.append(
                '<div class="modal-bodega-row">' +
                    '<div class="modal-bodega-name" title="' + htmlSeguro(nombreBodega) + '">' + htmlSeguro(truncarTexto(nombreBodega, 32)) + '</div>' +
                    '<div class="modal-bodega-track">' +
                        '<div class="modal-bodega-fill" style="width:' + porcentaje + '%;background:' + color + ';"></div>' +
                    '</div>' +
                    '<div class="modal-bodega-value">' + formatoNumero(b.stock || 0) + '</div>' +
                '</div>'
            );
        });
    }

    function renderizarDonutEstados(estados, total) {
        var partes = [];
        var acumulado = 0;
        var legend = $('#modalDonutLegendEstados');

        legend.empty();

        Object.keys(labelsEstadoEstadistica).forEach(function(key) {
            var valor = parseFloat(estados[key] || 0);
            var porcentaje = total > 0 ? ((valor * 100) / total) : 0;
            var inicio = acumulado;
            acumulado += porcentaje;

            if (valor > 0) {
                partes.push((coloresEstadoEstadistica[key] || '#94a3b8') + ' ' + inicio + '% ' + acumulado + '%');
            }

            legend.append(
                '<div class="modal-legend-row">' +
                    '<span class="modal-legend-dot" style="background:' + (coloresEstadoEstadistica[key] || '#94a3b8') + ';"></span>' +
                    '<span>' + labelsEstadoEstadistica[key] + '</span>' +
                    '<strong>' + formatoNumero(valor) + ' (' + porcentaje.toFixed(1) + '%)</strong>' +
                '</div>'
            );
        });

        var bg = partes.length ? 'conic-gradient(' + partes.join(',') + ')' : '#e5edf8';
        $('#modalDonutEstados')
            .css('background', bg)
            .attr('data-total', formatoNumero(total) + '\nTotal');
    }

    function renderizarTablaResumenProductos(productos) {
        var tbody = $('#modalTablaResumenProductos');
        tbody.empty();

        productos.forEach(function(p) {
            var detalleEstados = p.estados_detalle || [];
            var htmlEstados = '';

            if (detalleEstados.length > 0) {
                htmlEstados += '<div class="modal-status-breakdown">';

                detalleEstados.forEach(function(estado) {
                    var colorDetalle = coloresEstadoEstadistica[estado.key] || '#64748b';
                    htmlEstados +=
                        '<span class="modal-status-pill" style="background:' + colorDetalle + ';" title="' + htmlSeguro(estado.label || '') + '">' +
                            '<strong>' + formatoNumero(estado.cantidad || 0) + '</strong> ' + htmlSeguro(estado.label || '-') +
                        '</span>';
                });

                htmlEstados += '</div>';
            } else {
                var color = coloresEstadoEstadistica[p.estado_class] || '#64748b';
                htmlEstados = '<span class="modal-status-pill" style="background:' + color + ';">' + htmlSeguro(p.estado || '-') + '</span>';
            }

            tbody.append(
                '<tr>' +
                    '<td class="td-truncate" title="' + htmlSeguro(p.nombre || '') + '">' +
                        '<strong style="color:#145cff;">' + htmlSeguro(p.nombre || '') + '</strong>' +
                    '</td>' +
                    '<td class="td-truncate" title="' + htmlSeguro(p.bodegas_texto || '-') + '">' + htmlSeguro(p.bodegas_texto || '-') + '</td>' +
                    '<td>' + formatoNumero(p.stock || 0) + '</td>' +
                    '<td>' + htmlEstados + '</td>' +
                '</tr>'
            );
        });
    }

    function exportarEstadisticaProductoCsv() {
        if (!ultimoResultadoEstadistica || !ultimoResultadoEstadistica.productos || !ultimoResultadoEstadistica.productos.length) {
            alert('No hay datos para exportar.');
            return;
        }

        var filas = [
            ['Nombre', 'Codigo', 'Bodegas', 'Stock total', 'Resumen estados']
        ];

        ultimoResultadoEstadistica.productos.forEach(function(p) {
            var detalle = (p.estados_detalle || []).map(function(estado) {
                return (estado.cantidad || 0) + ' ' + (estado.label || '');
            }).join(' | ');

            filas.push([
                p.nombre || '',
                p.codigo || '',
                p.bodegas_texto || '',
                p.stock || 0,
                detalle || p.estado || ''
            ]);
        });

        var csv = filas.map(function(fila) {
            return fila.map(function(campo) {
                return '"' + String(campo).replace(/"/g, '""') + '"';
            }).join(';');
        }).join('\n');

        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var enlace = document.createElement('a');

        enlace.href = url;
        enlace.download = 'estadistica_productos_seleccionados.csv';
        enlace.click();

        URL.revokeObjectURL(url);
    }

    $(document).on('click', '.btnAbrirEstadisticaProducto', function() {
        agregarProductoEstadistica($(this).data('codigo'), $(this).data('nombre'));
    });

    $(document).on('click', '.btnQuitarProductoEstadistica', function() {
        quitarProductoEstadistica($(this).data('codigo'));
    });

    $(document).on('click', '#btnLimpiarSeleccionProductos', function() {
        productosSeleccionadosEstadistica = [];
        renderizarChipsProductos();
        limpiarContenidoModalEstadistica();
    });

    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('buscar_vigencia');
        var lista = document.getElementById('autocomplete_vigencia');
        var form = document.getElementById('form-buscar-vigencia');

        var timerAutocomplete = null;

        if (!input || !lista || !form) {
            return;
        }

        function ocultarLista() {
            lista.style.display = 'none';
            lista.innerHTML = '';
        }

        function mostrarAutocomplete(valor) {
            if (valor.length < 2) {
                ocultarLista();
                return;
            }

            fetch("{{ route('menu.autocomplete_caducados') }}?term=" + encodeURIComponent(valor))
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    lista.innerHTML = '';

                    if (!data || data.length === 0) {
                        ocultarLista();
                        return;
                    }

                    data.forEach(function (item) {
                        var div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.textContent = item.label;

                        div.addEventListener('click', function () {
                            input.value = item.value;
                            ocultarLista();

                            /*
                            IMPORTANTE:
                            Aquí NO hacemos form.submit()
                            para que solo busque cuando des clic en Buscar.
                            */
                        });

                        lista.appendChild(div);
                    });

                    lista.style.display = 'block';
                })
                .catch(function () {
                    ocultarLista();
                });
        }

        input.addEventListener('keyup', function (e) {
            var valor = input.value.trim();

            if (e.key === 'Enter') {
                ocultarLista();
                return;
            }

            clearTimeout(timerAutocomplete);

            timerAutocomplete = setTimeout(function () {
                mostrarAutocomplete(valor);
            }, 250);
        });

        document.addEventListener('click', function (e) {
            if (!form.contains(e.target)) {
                ocultarLista();
            }
        });
    });
</script>

@endsection
