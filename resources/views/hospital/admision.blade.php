@extends('layouts.app-template-h')
@section('content')
<style>
    .autocomplete {
        z-index: 999999 !important;
        z-index: 999999999 !important;
        z-index: 99999999999999 !important;
        position: absolute;
        top: 0px;
        left: 0px;
        float: left;
        display: block;
        min-width: 160px;
        padding: 4px 0;
        margin: 0 0 10px 25px;
        list-style: none;
        background-color: #ffffff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
    }

    .ui-autocomplete {
        z-index: 5000;
    }

    .ui-autocomplete {
        z-index: 999999;
        list-style: none;
        background-color: #FFFFFF;
        width: 300px;
        border: solid 1px #EEE;
        border-radius: 5px;
        padding-left: 10px;
        line-height: 2em;
    }

    .hospital-admission-page {
        --admission-navy: #073d66;
        --admission-blue: #087cba;
        --admission-cyan: #16a9bd;
        --admission-soft: #edf8fb;
        --admission-line: #d4e6ef;
        --admission-muted: #647d90;
        padding: 0 !important;
    }

    .hospital-admission-page .admission-hero {
        margin-bottom: 1rem;
        padding: 1.25rem 1.4rem;
        border: 0;
        border-radius: 15px;
        color: #fff;
        background: linear-gradient(120deg, #06456f 0%, #0879a7 58%, #0b9c9a 100%);
        box-shadow: 0 14px 32px rgba(7, 61, 102, .15);
    }

    .hospital-admission-page .admission-hero-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .hospital-admission-page .admission-hero-icon {
        display: grid;
        width: 54px;
        height: 54px;
        flex: 0 0 54px;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, .3);
        border-radius: 14px;
        background: rgba(255, 255, 255, .13);
        font-size: 1.45rem;
    }

    .hospital-admission-page .admission-hero-label {
        display: block;
        margin-bottom: .15rem;
        color: #9ce9eb;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .hospital-admission-page .admission-hero h3 {
        margin: 0;
        color: #fff;
        font-size: 1.55rem;
        font-weight: 800;
    }

    .hospital-admission-page .admission-hero p {
        margin: .2rem 0 0;
        color: rgba(255, 255, 255, .82);
        font-size: .82rem;
    }

    .hospital-admission-page .admission-card {
        overflow: hidden;
        border: 1px solid var(--admission-line);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 12px 32px rgba(21, 73, 107, .08);
    }

    .hospital-admission-page .admission-card > .card-header {
        display: flex;
        min-height: 70px;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admission-line);
        background: linear-gradient(120deg, #f7fcfe, #eaf7fa);
    }

    .hospital-admission-page .admission-card-heading {
        display: flex;
        align-items: center;
        gap: .8rem;
    }

    .hospital-admission-page .admission-card-heading > i {
        display: grid;
        width: 40px;
        height: 40px;
        place-items: center;
        border-radius: 11px;
        color: var(--admission-blue);
        background: #dff2f8;
        font-size: 1rem;
    }

    .hospital-admission-page .admission-card-heading .card-title {
        float: none;
        margin: 0 0 .12rem;
        color: var(--admission-navy);
        font-size: 1.08rem;
        font-weight: 800;
    }

    .hospital-admission-page .admission-card-heading p {
        margin: 0;
        color: var(--admission-muted);
        font-size: .72rem;
    }

    .hospital-admission-page .card-tools {
        margin-left: auto;
    }

    .hospital-admission-page .admission-draft-status {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        margin-left: auto;
        margin-right: .75rem;
        padding: .38rem .65rem;
        border-radius: 999px;
        color: #407087;
        background: #e5f4f8;
        font-size: .68rem;
        font-weight: 700;
        transition: color .18s ease, background .18s ease;
    }

    .hospital-admission-page .admission-draft-status.is-saved {
        color: #16755c;
        background: #e5f7f0;
    }

    .hospital-admission-page .btn-card-tool {
        display: grid;
        width: 36px;
        height: 36px;
        place-items: center;
        border: 1px solid var(--admission-line);
        border-radius: 9px;
        color: var(--admission-navy);
        background: #fff;
    }

    .hospital-admission-page .admission-card .card-body {
        padding: 1.35rem 1.4rem .6rem;
    }

    .hospital-admission-page .admission-section-title {
        display: flex;
        align-items: center;
        gap: .7rem;
        margin: .35rem 0 1rem;
        padding: .7rem .85rem;
        border-left: 3px solid var(--admission-cyan);
        border-radius: 0 9px 9px 0;
        color: var(--admission-navy);
        background: var(--admission-soft);
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .hospital-admission-page .admission-section-title:not(:first-child) {
        margin-top: 1rem;
    }

    .hospital-admission-page .admission-section-title i {
        color: var(--admission-blue);
        font-size: .95rem;
    }

    .hospital-admission-page .form-row {
        margin-right: -.45rem;
        margin-left: -.45rem;
    }

    .hospital-admission-page .form-group {
        margin-bottom: 1rem;
        padding-right: .45rem;
        padding-left: .45rem;
    }

    .hospital-admission-page label.col-form-label-sm {
        min-height: 23px;
        margin-bottom: .3rem;
        padding: 0;
        color: #244862;
        font-size: .72rem;
        font-weight: 700;
    }

    .hospital-admission-page label.col-form-label-sm b {
        font-weight: inherit;
    }

    .hospital-birthdate-calendar .flatpickr-monthDropdown-months {
        -webkit-appearance: menulist !important;
        appearance: auto !important;
        pointer-events: auto;
        cursor: pointer;
    }

    .hospital-birthdate-calendar .flatpickr-monthDropdown-month {
        color: #21465f;
        background: #fff;
    }

    .hospital-admission-page .form-control,
    .hospital-admission-page .select2-container--default .select2-selection--single {
        height: 43px !important;
        border: 1px solid #c9dce7;
        border-radius: 9px;
        color: #21465f;
        background-color: #fff;
        font-size: .78rem;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .hospital-admission-page .select2-container {
        width: 100% !important;
    }

    .hospital-admission-page .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-right: 34px;
        padding-left: 13px;
        color: #21465f;
        line-height: 41px;
    }

    .hospital-admission-page .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #91a5b3;
    }

    .hospital-admission-page .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 1px;
        right: 8px;
        height: 40px;
    }

    .hospital-admission-page .select2-dropdown {
        overflow: hidden;
        border: 1px solid #b9d4e2;
        border-radius: 9px;
        box-shadow: 0 12px 24px rgba(15, 61, 88, .14);
    }

    .hospital-admission-page .select2-search--dropdown {
        padding: .65rem;
    }

    .hospital-admission-page .select2-search--dropdown .select2-search__field {
        height: 39px;
        padding: 0 .75rem;
        border: 1px solid #c9dce7;
        border-radius: 8px;
        outline: 0;
    }

    .hospital-admission-page .select2-results__option {
        padding: .65rem .8rem;
        color: #274c64;
        font-size: .76rem;
    }

    .hospital-admission-page .form-control:focus,
    .hospital-admission-page .select2-container--focus .select2-selection--single {
        border-color: var(--admission-cyan);
        background: #fbfeff;
        box-shadow: 0 0 0 3px rgba(22, 169, 189, .11);
    }

    .hospital-admission-page .input-group .form-control {
        border-radius: 9px !important;
    }

    .hospital-admission-page .input-group-addon {
        display: none;
    }

    .hospital-admission-page .admission-wizard-nav {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: .65rem;
        margin: 0 0 1.25rem;
        padding: .8rem;
        border: 1px solid var(--admission-line);
        border-radius: 13px;
        background: #f7fbfd;
    }

    .hospital-admission-page .admission-wizard-step {
        display: flex;
        align-items: center;
        gap: .65rem;
        min-width: 0;
        padding: .7rem .8rem;
        border: 0;
        border-radius: 10px;
        color: #698296;
        background: transparent;
        text-align: left;
        transition: all .18s ease;
    }

    .hospital-admission-page .admission-wizard-step-number {
        display: grid;
        flex: 0 0 31px;
        width: 31px;
        height: 31px;
        place-items: center;
        border: 1px solid #c9dce7;
        border-radius: 50%;
        background: #fff;
        font-size: .74rem;
        font-weight: 800;
    }

    .hospital-admission-page .admission-wizard-step-copy {
        overflow: hidden;
        font-size: .72rem;
        font-weight: 800;
        line-height: 1.2;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .hospital-admission-page .admission-wizard-step.is-active {
        color: #fff;
        background: linear-gradient(120deg, #0878b5, #0a9a9b);
        box-shadow: 0 7px 16px rgba(8, 120, 181, .16);
    }

    .hospital-admission-page .admission-wizard-step.is-active .admission-wizard-step-number {
        border-color: rgba(255, 255, 255, .82);
        color: #0878b5;
        background: #fff;
        box-shadow: 0 3px 8px rgba(0, 50, 80, .15);
    }

    .hospital-admission-page .admission-wizard-step.is-complete {
        color: #0878b5;
        background: #e8f7f5;
        cursor: pointer;
    }

    .hospital-admission-page .admission-wizard-step.is-complete .admission-wizard-step-number {
        border-color: #8fd4d0;
        color: #0878b5;
        background: #fff;
    }

    .hospital-admission-page .admission-wizard-panel { display: none; }
    .hospital-admission-page .admission-wizard-panel.is-active { display: block; }

    .hospital-admission-page .admission-room-warning,
    .hospital-admission-page .admission-field-help {
        display: block;
        margin-top: .4rem;
        font-size: .7rem;
        line-height: 1.45;
    }

    .hospital-admission-page .admission-room-warning {
        padding: .55rem .65rem;
        border-radius: 8px;
        color: #945008;
        background: #fff3df;
    }

    .hospital-admission-page .admission-room-warning a {
        display: inline-block;
        margin-left: .35rem;
        color: #0878b5;
        font-weight: 800;
        text-decoration: underline;
    }

    .hospital-admission-page .admission-field-help { color: #6b8293; }

    .hospital-admission-page .admission-review {
        margin-top: 1.1rem;
        padding: 1rem;
        border: 1px solid #b9dce7;
        border-radius: 12px;
        background: linear-gradient(135deg, #f4fbfd, #f7fcfa);
    }

    .hospital-admission-page .admission-review h4 {
        margin: 0 0 .35rem;
        color: var(--admission-navy);
        font-size: .92rem;
        font-weight: 800;
    }

    .hospital-admission-page .admission-review p {
        margin: 0 0 .8rem;
        color: #6b8293;
        font-size: .72rem;
    }

    .hospital-admission-page .admission-review-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .65rem;
    }

    .hospital-admission-page .admission-review-item {
        padding: .7rem;
        border-radius: 9px;
        background: #fff;
    }

    .hospital-admission-page .admission-review-item span,
    .hospital-admission-page .admission-review-item strong { display: block; }
    .hospital-admission-page .admission-review-item span { color: #7890a1; font-size: .65rem; }
    .hospital-admission-page .admission-review-item strong { margin-top: .2rem; color: #123f5d; font-size: .76rem; }

    .hospital-admission-page .admission-footer-actions {
        display: flex;
        gap: .65rem;
    }

    .hospital-admission-page .admission-nav-button {
        min-width: 125px;
        min-height: 44px;
        border-radius: 10px;
        font-size: .8rem;
        font-weight: 800;
    }

    .hospital-admission-page .card-footer {
        position: sticky;
        bottom: 0;
        z-index: 20;
        display: flex;
        justify-content: flex-end;
        padding: 1rem 1.4rem;
        border-top: 1px solid var(--admission-line);
        background: rgba(248, 252, 254, .96);
        backdrop-filter: blur(8px);
    }

    .hospital-admission-page .card-footer .row {
        margin: 0;
    }

    .hospital-admission-page .admission-save-button {
        min-width: 150px;
        min-height: 44px;
        margin: 0 !important;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(120deg, #0878b5, #0a9a9b);
        box-shadow: 0 8px 18px rgba(8, 120, 181, .18);
        font-size: .82rem;
        font-weight: 800;
    }

    .hospital-admission-page .admission-save-button i {
        margin-right: .45rem;
    }

    html.dark-layout .hospital-admission-page {
        --admission-navy: #e9f5f8;
        --admission-blue: #62cce0;
        --admission-cyan: #35c2c4;
        --admission-soft: #203f4c;
        --admission-line: #3c5c6b;
        --admission-muted: #abc1cc;
        color: #dce9ef;
    }

    html.dark-layout .hospital-admission-page .admission-hero {
        border: 1px solid #294858;
        background:
            radial-gradient(circle at 92% 0%, rgba(46,187,199,.15), transparent 30rem),
            linear-gradient(125deg,#152c39 0%,#193846 62%,#1c4651 100%) !important;
        box-shadow: 0 12px 30px rgba(0,0,0,.2);
    }

    html.dark-layout .hospital-admission-page .admission-hero-label { color:#78dbe1 !important; }
    html.dark-layout .hospital-admission-page .admission-hero h3 { color:#f1f8fb !important; }
    html.dark-layout .hospital-admission-page .admission-hero p { color:#b2c8d2 !important; }
    html.dark-layout .hospital-admission-page .admission-hero-icon {
        border-color:#4a6c7b;
        color:#9be3e7;
        background:#213f4d;
    }

    html.dark-layout .hospital-admission-page .admission-card {
        border-color:#3a5969 !important;
        color:#dce9ef !important;
        background:#172d39 !important;
        box-shadow:0 14px 34px rgba(0,0,0,.24);
    }

    html.dark-layout .hospital-admission-page .admission-card > .card-header {
        border-color:#426170 !important;
        background:linear-gradient(110deg,#1d3846,#213f4d) !important;
    }

    html.dark-layout .hospital-admission-page .admission-card-heading > i {
        border-color:#426779;
        color:#73d6e3;
        background:#244654;
    }

    html.dark-layout .hospital-admission-page .admission-card-heading .card-title { color:#edf7fa !important; }
    html.dark-layout .hospital-admission-page .admission-card-heading p { color:#aec4ce !important; }
    html.dark-layout .hospital-admission-page .admission-draft-status {
        border-color:#426678;
        color:#bfe1e8;
        background:#203e4b;
    }

    html.dark-layout .hospital-admission-page .btn-card-tool {
        border-color:#486a79;
        color:#dce9ef;
        background:#203d49;
    }

    html.dark-layout .hospital-admission-page .admission-card .card-body { background:#172d39 !important; }

    html.dark-layout .hospital-admission-page .admission-wizard-nav {
        border-color:#3d5d6c;
        background:linear-gradient(180deg,#203b48,#1c3441) !important;
        box-shadow:inset 0 1px 0 rgba(255,255,255,.035);
    }

    html.dark-layout .hospital-admission-page .admission-wizard-step {
        border:1px solid transparent;
        color:#a9c0cb;
        background:transparent;
    }

    html.dark-layout .hospital-admission-page .admission-wizard-step-number {
        border-color:#4b6a79;
        color:#bcd0d9;
        background:#18303c;
    }

    html.dark-layout .hospital-admission-page .admission-wizard-step.is-active {
        border-color:#23b7c2;
        color:#fff;
        background:linear-gradient(110deg,#087ca4,#16a7a9) !important;
        box-shadow:0 6px 15px rgba(6,112,139,.24);
    }

    html.dark-layout .hospital-admission-page .admission-wizard-step.is-complete {
        color:#8ce0c0;
        background:rgba(48,168,117,.1);
    }

    html.dark-layout .hospital-admission-page .admission-section-title {
        border-top:1px solid #3b5968;
        border-right:1px solid #3b5968;
        border-bottom:1px solid #3b5968;
        border-left-color:#34c2c5;
        color:#dff2f5 !important;
        background:linear-gradient(90deg,#203f4c,#1c3542) !important;
    }

    html.dark-layout .hospital-admission-page .admission-section-title i { color:#66d2df; }
    html.dark-layout .hospital-admission-page label.col-form-label-sm,
    html.dark-layout .hospital-admission-page label.col-form-label-sm b { color:#d8e6ed !important; }

    html.dark-layout .hospital-admission-page .form-control,
    html.dark-layout .hospital-admission-page .select2-container--default .select2-selection--single {
        border-color:#426171 !important;
        color:#e3edf2 !important;
        background:#1d3744 !important;
        box-shadow:inset 0 1px 2px rgba(0,0,0,.12);
    }

    html.dark-layout .hospital-admission-page .form-control::placeholder { color:#839da9 !important; }
    html.dark-layout .hospital-admission-page .select2-selection__rendered { color:#dce9ef !important; }
    html.dark-layout .hospital-admission-page .select2-selection__placeholder { color:#8da5b0 !important; }

    html.dark-layout .hospital-admission-page .form-control:focus,
    html.dark-layout .hospital-admission-page .select2-container--focus .select2-selection--single {
        border-color:#35b8c6 !important;
        background:#213e4a !important;
        box-shadow:0 0 0 3px rgba(53,184,198,.15) !important;
    }

    html.dark-layout .hospital-admission-page .admission-field-help { color:#9fb7c2 !important; }
    html.dark-layout .hospital-admission-page .admission-room-warning {
        border-color:#8a6c31;
        color:#f0cf89;
        background:#40351f;
    }

    html.dark-layout .hospital-admission-page .admission-review {
        border-color:#3f5e6d;
        background:linear-gradient(135deg,#1d3845,#203e4b) !important;
    }

    html.dark-layout .hospital-admission-page .admission-review h4 { color:#edf7fa !important; }
    html.dark-layout .hospital-admission-page .admission-review p,
    html.dark-layout .hospital-admission-page .admission-review-item span { color:#9fb7c2 !important; }
    html.dark-layout .hospital-admission-page .admission-review-item {
        border-color:#3f6070;
        background:#182f3c;
    }
    html.dark-layout .hospital-admission-page .admission-review-item strong { color:#e4f0f4 !important; }

    html.dark-layout .hospital-admission-page .card-footer {
        border-color:#3b5968 !important;
        background:#1b3441 !important;
    }

    html.dark-layout .hospital-admission-page .admission-nav-button.btn-default {
        border-color:#486877;
        color:#dce9ef;
        background:#223e4b;
    }

    html.dark-layout body > .select2-container .select2-dropdown {
        border-color:#426171 !important;
        color:#dce9ef;
        background:#1a323f !important;
    }
    html.dark-layout body > .select2-container .select2-search__field {
        border-color:#486a79 !important;
        color:#e3edf2 !important;
        background:#203d49 !important;
    }
    html.dark-layout body > .select2-container .select2-results__option { color:#dce9ef; background:#1a323f; }
    html.dark-layout body > .select2-container .select2-results__option--highlighted { color:#fff; background:#0d8da8 !important; }

    html.dark-layout .hospital-admission-page .is-invalid,
    html.dark-layout .hospital-admission-page .has-error .form-control { border-color:#f07880 !important; }
    html.dark-layout .hospital-admission-page .invalid-feedback,
    html.dark-layout .hospital-admission-page .help-block { color:#ff9ba1 !important; }

    @media (max-width: 767.98px) {
        .hospital-admission-page .admission-hero { padding: 1rem; }
        .hospital-admission-page .admission-hero h3 { font-size: 1.2rem; }
        .hospital-admission-page .admission-card .card-body { padding: 1rem .9rem .4rem; }
        .hospital-admission-page .admission-card > .card-header { padding: .9rem; }
        .hospital-admission-page .admission-draft-status { display: none; }
        .hospital-admission-page .card-footer { padding: .85rem; }
        .hospital-admission-page .admission-wizard-nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .hospital-admission-page .admission-review-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .hospital-admission-page .admission-footer-actions { width: 100%; }
        .hospital-admission-page .admission-nav-button,
        .hospital-admission-page .admission-save-button { width: 100%; min-width: 0; }
    }
</style>

<?php
date_default_timezone_set('America/Guayaquil');
$fecha_actual = date("Y-m-d H:i:s");
?>


<div class="content hospital-admission-page">

    <section class="content-header admission-hero">
        <div class="admission-hero-content">
            <div class="admission-hero-icon"><i class="fa fa-hospital-o" aria-hidden="true"></i></div>
            <div>
                <span class="admission-hero-label">Gestión de pacientes</span>
                <h3>@if((int) $id_paso === 2) Ingreso por Emergencia @elseif((int) $id_paso === 3) Ingreso a Hospitalización @else {{trans('emergencia.NUEVOINGRESO')}} @endif</h3>
                <p>@if((int) $id_paso === 2) Registra la atención inmediata; al guardar se habilitarán el triaje y el Formulario 008. @elseif((int) $id_paso === 3) Registra la internación del paciente; al guardar se habilitarán la cama y el Formulario 005. @else Registra los datos personales, clínicos y de contacto necesarios para el ingreso hospitalario. @endif</p>
            </div>
        </div>
    </section>

    <div class="row">

        <div class="col-md-12">
            <div class="card card-primary admission-card">
                <div class="card-header with-border">
                    <div class="admission-card-heading">
                        <i class="fa fa-address-card-o" aria-hidden="true"></i>
                        <div>
                            <h3 class="card-title">{{trans('emergencia.Admision')}}</h3>
                            <p>Completa los campos requeridos para registrar correctamente al paciente.</p>
                        </div>
                    </div>

                    <div class="admission-draft-status" aria-live="polite">
                        <i class="fa fa-cloud" aria-hidden="true"></i>
                        <span>Borrador automático</span>
                    </div>

                    <div class="card-tools pull-right">
                        <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form method="post" id="form_emergencia">
                    <input type="hidden" name="clinical_override" id="clinical_override" value="0">
                    <input type="hidden" name="clinical_override_reason" id="clinical_override_reason" value="">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                @if(session('message'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{session('message')}}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="{{trans('emergencia.Cerrar')}}">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                <nav class="admission-wizard-nav" aria-label="Pasos del registro de ingreso">
                                    <button type="button" class="admission-wizard-step is-active" data-step="1">
                                        <span class="admission-wizard-step-number">1</span>
                                        <span class="admission-wizard-step-copy">Identificación</span>
                                    </button>
                                    <button type="button" class="admission-wizard-step" data-step="2">
                                        <span class="admission-wizard-step-number">2</span>
                                        <span class="admission-wizard-step-copy">Atención y datos</span>
                                    </button>
                                    <button type="button" class="admission-wizard-step" data-step="3">
                                        <span class="admission-wizard-step-number">3</span>
                                        <span class="admission-wizard-step-copy">Datos demográficos</span>
                                    </button>
                                    <button type="button" class="admission-wizard-step" data-step="4">
                                        <span class="admission-wizard-step-number">4</span>
                                        <span class="admission-wizard-step-copy">Cobertura y domicilio</span>
                                    </button>
                                    <button type="button" class="admission-wizard-step" data-step="5">
                                        <span class="admission-wizard-step-number">5</span>
                                        <span class="admission-wizard-step-copy">Confirmación</span>
                                    </button>
                                </nav>

                                <section class="admission-wizard-panel is-active" data-step="1">
                                <div class="admission-section-title"><i class="fa fa-search" aria-hidden="true"></i> Identificación y búsqueda del paciente</div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.CeduladeCiudadania')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="cedula" name="cedula" required onchange="buscapaciente();">
                                        <input type="hidden" name="id_paso" id="id_paso" value="{{$id_paso}}" class="form-control">
                                        <input type="hidden" class="form-control input-sm" name="procedimiento_nombre" id="pronombre" value="{{old('procedimiento_nombre')}}">
                                    </div>
                                    <div class="form-group col-md-10">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.ApellidosyNombres')}}</b></label>
                                        <input type="text" class="form-control form-control-sm nombre" name="apellidos_nombres" id="apellidos_nombres" placeholder="{{trans('emergencia.BuscarApellidosNombres')}}" style="z-index:999999 !important">
                                    </div>
                                </div>    
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Apellido1')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" name="apellido1" id="apellido1" autocomplete="off" style="z-index:999999 !important">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Apellido2')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" name="apellido2" id="apellido2" autocomplete="off" style="z-index:999999 !important">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Nombre1')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" name="nombre1" id="nombre1" autocomplete="off" style="z-index:999999 !important">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Nombre2')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" name="nombre2" id="nombre2" autocomplete="off" style="z-index:999999 !important">
                                    </div>
                                </div>
                                </section>

                                <section class="admission-wizard-panel" data-step="2">
                                    
                                    <!--  -->
                                <div class="admission-section-title"><i class="fa fa-user-md" aria-hidden="true"></i> Asignación clínica</div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Doctor')}}</b></label>
                                        <select class="form-control form-control-sm validar" name="id_doctor" id="id_doctor" required>
                                            <option value="">{{trans('emergencia.SeleccioneOpcion')}}</option>
                                            @foreach($user as $us)
                                            <option value="{{$us->id}}" @if((string) $us->id === (string) Auth::id()) selected @endif>Dr. {{$us->apellido1}} {{$us->nombre1}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="col-form-label-sm"><b>@if((int) $id_paso === 2) Área de atención de Emergencia @elseif((int) $id_paso === 3) Servicio de Hospitalización @else Sala clínica de ingreso @endif</b></label>
                                        <select class="form-control form-control-sm validar" name="id_sala" id="id_sala">
                                            <option value="">{{trans('emergencia.SeleccioneOpcion')}}</option>
                                            @foreach($salas as $s)
                                            <option value="{{$s->id}}" @if(count($salas) === 1) selected @endif>{{$s->nombre_sala}} — {{$s->nombre_hospital}}</option>
                                            @endforeach
                                        </select>
                                        @if(count($salas) === 0)
                                        <small class="admission-room-warning">
                                            @if((int) $id_paso === 2)
                                            <i class="fa fa-exclamation-triangle"></i> No hay salas configuradas para Emergencia en esta empresa.
                                            @elseif((int) $id_paso === 3)
                                            <i class="fa fa-exclamation-triangle"></i> No existe una sala clínica de <b>Hospitalización</b> activa. Sistemas debe crear una sala llamada “Hospitalización” dentro de la unidad médica; luego el piso, la habitación y la cama se asignarán en el mapa hospitalario.
                                            @else
                                            <i class="fa fa-exclamation-triangle"></i> No existen salas de atención activas. Sistemas debe crear una antes de registrar el ingreso.
                                            @endif
                                            @if(Auth::check() && in_array((int) Auth::user()->id_tipo_usuario, array(1, 4)))
                                            <a href="{{route('hospital.workflow.locations')}}">Destinos hospitalarios</a>
                                            @endif
                                        </small>
                                        @else
                                        <small class="admission-field-help">
                                            @if((int) $id_paso === 3)
                                                Ingreso directo a Hospitalización. La cama se asignará desde Gestión de camas.
                                            @else
                                                Ingreso por Emergencia. Pendiente de triaje y valoración médica.
                                            @endif
                                        </small>
                                        @endif
                                    </div>
                                    @if($id_paso==4 or $id_paso==5)
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Procedimientos')}}</b></label>
                                        <select id="id_procedimiento" class="form-control form-control-sm select2" name="id_procedimiento"  data-placeholder="{{trans('emergencia.SeleccioneOpcion')}}" autocomplete="off">
                                            <option value="">{{trans('emergencia.SeleccioneOpcion')}}</option>
                                            @foreach($procedimiento as $procedimiento)
                                            <option value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>    
                                    <!--  -->
                                <div class="admission-section-title"><i class="fa fa-id-card-o" aria-hidden="true"></i> Información personal y de contacto</div>
                                <div class="form-row">    
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.FechaAdmision')}}</b></label>
                                        <div class="input-group date">
                                            <input type="text" data-input="true" class="form-control input-xs flatpickr-basic active validar" name="f_admision" id="f_admision" value="{{$fecha}}" autocomplete="off">
                                            <div class="input-group-addon">
                                                <i class="glyphicon glyphicon-remove-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.tipoDocumento')}}</b></label>
                                        <select name="tipo_doc" id="tipo_doc" class="form-control form-control-sm">
                                            <option value="1">CI</option>
                                            <option value="2">{{trans('emergencia.Pasaporte')}}</option>
                                            <option value="3">{{trans('emergencia.Carnet')}}</option>
                                            <option value="4">SD</option>
                                            <!--option value="CAR">{{trans('emergencia.OtroTransporte')}}</option-->
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Sexo')}}</b></label>

                                        <select class="form-control form-control-sm validar" id="sexo" name="sexo">
                                            <option value="1">{{trans('emergencia.Hombre')}}</option>
                                            <option value="2">{{trans('emergencia.Mujer')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.EstadoCivil')}}</b></label>
                                        <select class="form-control form-control-sm validar" id="estado" name="estado">
                                            <option value="1"> {{trans('emergencia.Soltero')}}</option>
                                            <option value="2"> {{trans('emergencia.Casado')}}</option>
                                            <option value="3"> {{trans('emergencia.Viudo')}}</option>
                                            <option value="4"> {{trans('emergencia.Divorciado')}}</option>
                                            <option value="5"> {{trans('emergencia.UnionLibre')}}</option>
                                            <option value="6"> {{trans('emergencia.UniondeHecho')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Telefono')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="telefono1" name="telefono1" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Celular')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="telefono2" name="telefono2" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Fechadenacimiento')}}</b></label>
                                        <div class="input-group date">
                                            <input type="text" data-input="true" class="form-control input-xs flatpickr-basic active validar" name="f_nacimiento" id="f_nacimiento" value="{{$fecha}}" placeholder="AAAA-MM-DD" aria-label="Fecha de nacimiento en formato AAAA-MM-DD" autocomplete="off">
                                            <div class="input-group-addon">
                                                <i class="glyphicon glyphicon-remove-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>    
                                </section>

                                <section class="admission-wizard-panel" data-step="3">
                                <div class="admission-section-title"><i class="fa fa-users" aria-hidden="true"></i> Información demográfica y social</div>
                                <div class="form-row"> 
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.lugarNacimiento')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="lugarNacimiento" name="lugarNacimiento" required>
                                    </div> 
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Nacionalidad')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="nacionalidad" name="nacionalidad">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Ciudad')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="ciudad" name="ciudad" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.grupoPrioritario')}}</b></label>

                                        <select class="form-control form-control-sm" id="grupoPrioritario" name="grupoPrioritario">
                                            <option value="">{{trans('emergencia.SeleccioneOpcion')}}</option>
                                            <option value="1">{{trans('emergencia.SI')}}</option>
                                            <option value="0">{{trans('emergencia.NO')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.especifiqueGrupo')}}</b></label>
                                        <input type="text" class="form-control form-control-sm" id="especifiqueGrupo" name="especifiqueGrupo" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.autoIdentificacionEtnica')}}</b></label>
                                        <input type="text" class="form-control form-control-sm" id="autoIdentificacionEtnica" name="autoIdentificacionEtnica" autocomplete="off">
                                    </div>
                                    <!--div class="form-group col-md-6">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Direccionderesidencia')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" name="direccion" id="direccion" autocomplete="off">
                                    </div-->
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.nacionalidadEtnica')}}</b></label>
                                        <input type="text" class="form-control form-control-sm" id="nacionalidadEtnica" name="nacionalidadEtnica" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.pueblos')}}</b></label>
                                        <input type="text" class="form-control form-control-sm" id="pueblos" name="pueblos" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.nivelEducacion')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="nivelEducacion" name="nivelEducacion" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.estadoNivelEducacion')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="estadoNivelEducacion" name="estadoNivelEducacion" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Empresadondetrabaja')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="empresa" name="empresa">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Ocupacion')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="ocupacion" name="ocupacion">
                                    </div>
                                </div>
                                </section>

                                <section class="admission-wizard-panel" data-step="4">
                                <div class="admission-section-title"><i class="fa fa-medkit" aria-hidden="true"></i> Cobertura y seguro</div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.TipodeSeguro')}}</b></label>
                                        <select class="form-control form-control-sm validar" id="id_seguro" name="id_seguro">
                                            @foreach($seguros as $seg)
                                            <option value="{{$seg->id}}">{{$seg->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Sub Seguro')}}</b></label>
                                        <select class="form-control form-control-sm" id="id_subseguro" name="id_subseguro">
                                            <option value="">{{trans('emergencia.Ninguna')}}</option>
                                            @foreach($subseguros as $seg)
                                            <option value="{{$seg->id}}">{{$seg->nombre}} - {{$seg->seguro}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.LimiteCredito')}}</b></label>
                                        <input type="text" class="form-control form-control-sm " id="limite_credito" name="limite_credito" value=""> 
                                    </div>
                                </div>   
                                <div class="admission-section-title"><i class="fa fa-map-marker" aria-hidden="true"></i> {{trans('emergencia.Direccionderesidencia')}}</div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Provincia')}}</b></label>
                                        <input type="hidden" id="provincia" name="provincia" value="">
                                        <select class="form-control form-control-sm validar admission-geography-select" id="provincia_selector" data-placeholder="Escriba o seleccione una provincia" required>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Canton')}}</b></label>
                                        <input type="hidden" id="canton" name="canton" value="">
                                        <select class="form-control form-control-sm validar admission-geography-select" id="canton_selector" data-placeholder="Primero seleccione la provincia" disabled required>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Parroquia')}}</b></label>
                                        <input type="hidden" id="parroquia" name="parroquia" value="">
                                        <select class="form-control form-control-sm validar admission-geography-select" id="parroquia_selector" data-placeholder="Primero seleccione el cantón" disabled required>
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Barrio')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="barrio" name="barrio" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.callePrincipal')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="callePrincipal" name="callePrincipal" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.calleSecundaria')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="calleSecundaria" name="calleSecundaria" required>
                                    </div>
                                </div>    
                                </section>

                                <section class="admission-wizard-panel" data-step="5">
                                <div class="admission-section-title"><i class="fa fa-phone" aria-hidden="true"></i> Contacto de emergencia y forma de llegada</div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.referencia')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="referencia" name="referencia" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Encasonecesariollamara')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="llamar_a" name="llamar_a">
                                    </div>
                                    <!--div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.ZonaUR')}}</b></label>
                                        <select class="form-control form-control-sm validar" id="zona" name="zona">
                                            <option value="Urbana">{{trans('emergencia.Urbana')}}</option>
                                            <option value="Rural">{{trans('emergencia.Rural')}}</option>
                                        </select>
                                    </div-->
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.ParentescoAfinidad')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="parentesco" name="parentesco">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.DireccionFamiliar')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="direccion_familiar" name="direccion_familiar">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.NTelefono')}}</b></label>
                                        <input type="number" class="form-control form-control-sm validar" id="telefono_llamar" name="telefono_llamar">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.FormadeLlegada')}}</b></label>
                                        <select name="forma_llegada" id="forma_llegada" class="form-control form-control-sm">
                                            <option value="Ambulatorio">{{trans('emergencia.Ambulatorio')}}</option>
                                            <option value="Ambulancia">{{trans('emergencia.Ambulancia')}}</option>
                                            <option value="Otro">{{trans('emergencia.OtroTransporte')}}</option>
                                        </select>
                                    </div>
                                </div>
                               
                                    
                                    <!--div class="form-group col-md-1">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.GrCultural')}}</b></label>
                                        <select class="form-control form-control-sm validar" id="grupo_cultural" name="grupo_cultural">
                                            <option value="Mestizo">{{trans('emergencia.Mestizo')}}</option>
                                            <option value="Morisco">{{trans('emergencia.Morisco')}}</option>
                                            <option value="Cholo">{{trans('emergencia.Cholocoyote')}}</option>
                                            <option value="Mulatos">{{trans('emergencia.Mulatos')}}</option>
                                            <option value="Zambo">{{trans('emergencia.Zambo')}}</option>
                                            <option value="Castizo">{{trans('emergencia.Castizo')}}</option>
                                            <option value="Criollo">{{trans('emergencia.Criollo')}}</option>
                                            <option value="Chino">{{trans('emergencia.Chino')}}</option>
                                        </select>
                                    </div-->
                                    
                                    <!--div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.Instruccion')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="instruccion" name="instruccion">
                                    </div-->
                                    
                        
                                <div class="admission-section-title"><i class="fa fa-info-circle" aria-hidden="true"></i> Información de referencia</div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.FuentedeInformacion')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="fuente_informacion" name="fuente_informacion">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.nombreEntregapaciente')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="referido" name="referido">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="col-form-label-sm"><b>{{trans('emergencia.NTelefono')}}</b></label>
                                        <input type="text" class="form-control form-control-sm validar" id="telefono_inst_per_paci" name="telefono_inst_per_paci">
                                    </div>
                                    

                                </div>

                                <div class="admission-review" aria-live="polite">
                                    <h4><i class="fa fa-check-circle" aria-hidden="true"></i> Revisa el ingreso antes de guardar</h4>
                                    <p>Confirma que el paciente, la sala y la cobertura sean correctos.</p>
                                    <div class="admission-review-grid">
                                        <div class="admission-review-item"><span>Paciente</span><strong id="review_patient">Sin completar</strong></div>
                                        <div class="admission-review-item"><span>Identificación</span><strong id="review_identification">Sin completar</strong></div>
                                        <div class="admission-review-item"><span>Sala</span><strong id="review_room">Sin seleccionar</strong></div>
                                        <div class="admission-review-item"><span>Seguro</span><strong id="review_insurance">Sin seleccionar</strong></div>
                                    </div>
                                </div>
                                </section>

                            </div>

                        </div>

                    </div>



                    <div class="card-footer">
                        <div class="admission-footer-actions">
                            <button type="button" id="admission_previous" class="btn btn-default admission-nav-button" style="display:none"><i class="fa fa-arrow-left"></i> Anterior</button>
                            <button type="button" id="admission_next" class="btn btn-primary admission-nav-button">Siguiente <i class="fa fa-arrow-right"></i></button>
                            <button type="button" id="admission_save" onclick="guardar();" class="btn btn-primary admission-save-button" style="display:none"><i class="far fa-save"></i>{{trans('emergencia.Guardar')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ('/js/jquery-ui.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>


<script type="text/javascript">
    var admissionDraftCompleted = false;
    var admissionDraftTimer = null;
    var currentAdmissionStep = 1;
    var totalAdmissionSteps = 5;
    var admissionAddressHydrating = false;

    function geographySelectLanguage() {
        return {
            noResults: function() { return 'No se encontraron resultados'; },
            searching: function() { return 'Buscando...'; },
            inputTooShort: function() { return 'Escriba para buscar'; }
        };
    }

    function resetAdmissionGeographySelect(selector, disabled, placeholder) {
        var select = $(selector);
        select.empty().append(new Option('', '', false, false));
        select.prop('disabled', !!disabled);
        if (placeholder) {
            select.attr('data-placeholder', placeholder);
            select.next('.select2-container').find('.select2-selection__placeholder').text(placeholder);
        }
        select.val(null).trigger('change.select2');
    }

    function selectedGeographyText(selector) {
        var text = $(selector).find('option:selected').text();
        return $.trim(text || '');
    }

    function initializeAdmissionGeography() {
        $('#provincia_selector').select2({
            width: '100%',
            allowClear: true,
            placeholder: $('#provincia_selector').data('placeholder'),
            language: geographySelectLanguage(),
            ajax: {
                url: "{{ route('catalogo-geografico.ecuador.buscar-provincias') }}",
                dataType: 'json',
                delay: 180,
                data: function(params) { return { q: params.term || '' }; },
                processResults: function(data) { return data; },
                cache: true
            }
        });

        $('#canton_selector').select2({
            width: '100%',
            allowClear: true,
            placeholder: $('#canton_selector').data('placeholder'),
            language: geographySelectLanguage(),
            ajax: {
                url: "{{ route('catalogo-geografico.ecuador.buscar-cantones') }}",
                dataType: 'json',
                delay: 180,
                data: function(params) {
                    return { q: params.term || '', id_provincia: $('#provincia_selector').val() || 0 };
                },
                processResults: function(data) { return data; },
                cache: true
            }
        });

        $('#parroquia_selector').select2({
            width: '100%',
            allowClear: true,
            placeholder: $('#parroquia_selector').data('placeholder'),
            language: geographySelectLanguage(),
            ajax: {
                url: "{{ route('catalogo-geografico.ecuador.buscar-parroquias') }}",
                dataType: 'json',
                delay: 180,
                data: function(params) {
                    return { q: params.term || '', id_canton: $('#canton_selector').val() || 0 };
                },
                processResults: function(data) { return data; },
                cache: true
            }
        });

        $('#provincia_selector').on('change', function() {
            var provinceId = $(this).val();
            $('#provincia').val(provinceId ? selectedGeographyText(this) : '').trigger('input');
            resetAdmissionGeographySelect('#canton_selector', !provinceId, provinceId ? 'Escriba o seleccione un cantón' : 'Primero seleccione la provincia');
            resetAdmissionGeographySelect('#parroquia_selector', true, 'Primero seleccione el cantón');
            if (!admissionAddressHydrating) {
                $('#canton, #parroquia').val('').trigger('input');
            }
        });

        $('#canton_selector').on('change', function() {
            var cantonId = $(this).val();
            $('#canton').val(cantonId ? selectedGeographyText(this) : '').trigger('input');
            resetAdmissionGeographySelect('#parroquia_selector', !cantonId, cantonId ? 'Escriba o seleccione una parroquia' : 'Primero seleccione el cantón');
            if (!admissionAddressHydrating) {
                $('#parroquia').val('').trigger('input');
            }
        });

        $('#parroquia_selector').on('change', function() {
            var parishId = $(this).val();
            $('#parroquia').val(parishId ? selectedGeographyText(this) : '').trigger('input');
        });
    }

    function findAdmissionGeography(url, params, expectedText) {
        if (!$.trim(expectedText || '')) {
            return $.Deferred().resolve(null).promise();
        }

        params.q = expectedText;
        return $.getJSON(url, params).then(function(response) {
            var expected = $.trim(expectedText).toLocaleUpperCase();
            var results = response.results || [];
            var exact = results.filter(function(item) {
                return $.trim(item.text || '').toLocaleUpperCase() === expected;
            });
            return exact.length ? exact[0] : (results.length ? results[0] : null);
        });
    }

    function selectAdmissionGeography(selector, item) {
        if (!item) {
            return;
        }
        var select = $(selector);
        select.append(new Option(item.text, item.id, true, true)).prop('disabled', false).trigger('change');
    }

    function setAdmissionAddress(provinceName, cantonName, parishName) {
        provinceName = $.trim(provinceName || '');
        cantonName = $.trim(cantonName || '');
        parishName = $.trim(parishName || '');
        admissionAddressHydrating = true;

        $('#provincia').val(provinceName);
        $('#canton').val(cantonName);
        $('#parroquia').val(parishName);
        resetAdmissionGeographySelect('#provincia_selector', false, 'Escriba o seleccione una provincia');
        resetAdmissionGeographySelect('#canton_selector', true, 'Primero seleccione la provincia');
        resetAdmissionGeographySelect('#parroquia_selector', true, 'Primero seleccione el cantón');

        if (!provinceName) {
            admissionAddressHydrating = false;
            return $.Deferred().resolve().promise();
        }

        return findAdmissionGeography(
            "{{ route('catalogo-geografico.ecuador.buscar-provincias') }}",
            {},
            provinceName
        ).then(function(province) {
            if (!province) {
                return $.Deferred().reject().promise();
            }
            selectAdmissionGeography('#provincia_selector', province);
            if (!cantonName) {
                return null;
            }
            return findAdmissionGeography(
                "{{ route('catalogo-geografico.ecuador.buscar-cantones') }}",
                { id_provincia: province.id },
                cantonName
            );
        }).then(function(canton) {
            if (!canton) {
                return null;
            }
            selectAdmissionGeography('#canton_selector', canton);
            if (!parishName) {
                return null;
            }
            return findAdmissionGeography(
                "{{ route('catalogo-geografico.ecuador.buscar-parroquias') }}",
                { id_canton: canton.id },
                parishName
            );
        }).then(function(parish) {
            if (parish) {
                selectAdmissionGeography('#parroquia_selector', parish);
            }
        }).always(function() {
            admissionAddressHydrating = false;
            $('#provincia').val(provinceName);
            $('#canton').val(cantonName);
            $('#parroquia').val(parishName);
        });
    }

    function admissionFieldValue(selector, fallback) {
        var value = $.trim($(selector).val() || '');
        return value || fallback;
    }

    function updateAdmissionReview() {
        var patientName = [$('#nombre1').val(), $('#nombre2').val(), $('#apellido1').val(), $('#apellido2').val()]
            .filter(function(value) { return $.trim(value || '') !== ''; })
            .join(' ');

        $('#review_patient').text(patientName || 'Sin completar');
        $('#review_identification').text(admissionFieldValue('#cedula', 'Sin completar'));
        $('#review_room').text($('#id_sala').val() ? $('#id_sala option:selected').text() : 'Sin seleccionar');
        $('#review_insurance').text($('#id_seguro').val() ? $('#id_seguro option:selected').text() : 'Sin seleccionar');
    }

    function showAdmissionStep(step) {
        step = Math.max(1, Math.min(totalAdmissionSteps, parseInt(step, 10) || 1));
        currentAdmissionStep = step;

        $('.admission-wizard-panel').removeClass('is-active')
            .filter('[data-step="' + step + '"]').addClass('is-active');

        $('.admission-wizard-step').each(function() {
            var itemStep = parseInt($(this).data('step'), 10);
            $(this).toggleClass('is-active', itemStep === step);
            $(this).toggleClass('is-complete', itemStep < step);
            $(this).attr('aria-current', itemStep === step ? 'step' : null);
        });

        $('#admission_previous').toggle(step > 1);
        $('#admission_next').toggle(step < totalAdmissionSteps);
        $('#admission_save').toggle(step === totalAdmissionSteps);

        if (step === totalAdmissionSteps) {
            updateAdmissionReview();
        }
    }

    function validateAdmissionPanel(step, focusFirst) {
        var invalid = false;
        var firstInvalid = null;
        var panel = $('.admission-wizard-panel[data-step="' + step + '"]');

        panel.find('.validar').each(function() {
            var value = $.trim($(this).val() || '');
            var isInvalid = value.length === 0;
            var visibleControl = $(this).hasClass('select2-hidden-accessible')
                ? $(this).next('.select2-container').find('.select2-selection')
                : $(this);

            visibleControl.css({
                border: isInvalid ? '2px solid #CD6155' : '1px solid #c9dce7',
                borderRadius: '9px'
            });

            if (isInvalid) {
                invalid = true;
                firstInvalid = firstInvalid || this;
            }
        });

        if (firstInvalid && focusFirst !== false) {
            firstInvalid.focus();
        }
        return invalid;
    }

    function admissionDraftKey() {
        return 'aitrol.hospital.admision.' + ($('#id_paso').val() || 'general');
    }

    function admissionDraftFields() {
        var values = {};

        $('#form_emergencia').find('input, select, textarea').each(function() {
            if (!this.name || this.name === '_token' || this.type === 'password' || this.type === 'file') {
                return;
            }

            if ((this.type === 'checkbox' || this.type === 'radio') && !this.checked) {
                return;
            }

            values[this.name] = $(this).val();
        });

        return values;
    }

    function updateAdmissionDraftStatus(message, saved) {
        var status = $('.admission-draft-status');
        status.toggleClass('is-saved', !!saved);
        status.find('i').attr('class', saved ? 'fa fa-check-circle' : 'fa fa-cloud');
        status.find('span').text(message);
    }

    function saveAdmissionDraft() {
        if (admissionDraftCompleted || !window.sessionStorage) {
            return;
        }

        try {
            sessionStorage.setItem(admissionDraftKey(), JSON.stringify({
                values: admissionDraftFields(),
                savedAt: new Date().toISOString()
            }));
            updateAdmissionDraftStatus('Borrador guardado', true);
        } catch (error) {
            updateAdmissionDraftStatus('Borrador no disponible', false);
        }
    }

    function restoreAdmissionDraft() {
        if (!window.sessionStorage) {
            return;
        }

        try {
            var draft = JSON.parse(sessionStorage.getItem(admissionDraftKey()) || 'null');
            if (!draft || !draft.values) {
                return;
            }

            $.each(draft.values, function(name, value) {
                var field = $('#form_emergencia').find('[name="' + name.replace(/"/g, '\\"') + '"]');
                if (!field.length) {
                    return;
                }

                if (field.first().is(':checkbox,:radio')) {
                    field.filter('[value="' + String(value).replace(/"/g, '\\"') + '"]').prop('checked', true);
                } else {
                    field.val(value);
                    if (field.hasClass('select2')) {
                        field.trigger('change.select2');
                    }
                }
            });

            updateAdmissionDraftStatus('Borrador recuperado', true);
        } catch (error) {
            sessionStorage.removeItem(admissionDraftKey());
        }
    }

    function clearAdmissionDraft() {
        admissionDraftCompleted = true;
        if (admissionDraftTimer) {
            clearInterval(admissionDraftTimer);
        }
        try {
            sessionStorage.removeItem(admissionDraftKey());
        } catch (error) {
            // El almacenamiento puede estar deshabilitado por el navegador.
        }
    }

    function normalizeAdmissionDate(value) {
        return $.trim(value || '').replace(/\//g, '-');
    }

    function setAdmissionBirthDate(value) {
        var input = document.getElementById('f_nacimiento');
        var normalizedValue = normalizeAdmissionDate(value);

        if (input && input._flatpickr) {
            input._flatpickr.setDate(normalizedValue, false, 'Y-m-d');
            return;
        }

        $('#f_nacimiento').val(normalizedValue);
    }

    function initializeAdmissionBirthDatePicker() {
        var input = document.getElementById('f_nacimiento');
        if (!input || typeof window.flatpickr !== 'function') {
            return;
        }

        var initialValue = normalizeAdmissionDate(input.value);
        if (input._flatpickr) {
            input._flatpickr.destroy();
        }

        window.flatpickr(input, {
            allowInput: true,
            clickOpens: true,
            dateFormat: 'Y-m-d',
            maxDate: 'today',
            monthSelectorType: 'dropdown',
            showMonths: 1,
            disableMobile: true,
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                    longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                },
                months: {
                    shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
                }
            },
            onReady: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('hospital-birthdate-calendar');
                instance.input.removeAttribute('readonly');
                instance.input.setAttribute('placeholder', 'AAAA-MM-DD');

                var monthSelector = instance.calendarContainer.querySelector('.flatpickr-monthDropdown-months');
                if (monthSelector) {
                    monthSelector.setAttribute('aria-label', 'Seleccionar mes');
                    monthSelector.setAttribute('title', 'Seleccionar mes');
                }
            }
        });

        if (initialValue) {
            setAdmissionBirthDate(initialValue);
        }
    }

    $(function() {
        initializeAdmissionGeography();

        $('#form_emergencia select').each(function() {
            var select = $(this);
            if (select.hasClass('select2-hidden-accessible') || select.hasClass('admission-geography-select')) {
                return;
            }

            select.addClass('select2').select2({
                width: '100%',
                placeholder: select.data('placeholder') || 'Seleccione una opción',
                language: {
                    noResults: function() { return 'No se encontraron resultados'; },
                    searching: function() { return 'Buscando...'; }
                }
            });
        });

        initializeAdmissionBirthDatePicker();
        restoreAdmissionDraft();
        setAdmissionBirthDate($('#f_nacimiento').val());
        setAdmissionAddress($('#provincia').val(), $('#canton').val(), $('#parroquia').val());
        showAdmissionStep(1);

        $('#admission_next').on('click', function() {
            if (validateAdmissionPanel(currentAdmissionStep)) {
                alertas('warning', 'Completa este paso', 'Revisa los campos marcados antes de continuar.');
                return;
            }
            showAdmissionStep(currentAdmissionStep + 1);
        });

        $('#admission_previous').on('click', function() {
            showAdmissionStep(currentAdmissionStep - 1);
        });

        $('#form_emergencia').on('submit', function(event) {
            event.preventDefault();
            if (currentAdmissionStep < totalAdmissionSteps) {
                $('#admission_next').trigger('click');
            } else {
                guardar();
            }
        });

        $('.admission-wizard-step').on('click', function() {
            var requestedStep = parseInt($(this).data('step'), 10);
            if (requestedStep < currentAdmissionStep) {
                showAdmissionStep(requestedStep);
            }
        });

        $('#form_emergencia').on('input change', 'input, select, textarea', function() {
            updateAdmissionDraftStatus('Guardando cambios...', false);
            clearTimeout($(this).data('admission-draft-timeout'));
            var timeout = setTimeout(saveAdmissionDraft, 450);
            $(this).data('admission-draft-timeout', timeout);
        });

        admissionDraftTimer = setInterval(saveAdmissionDraft, 2500);
    });

    function validar_campos() {
        var firstInvalidStep = null;

        for (var step = 1; step <= totalAdmissionSteps; step++) {
            if (validateAdmissionPanel(step, false) && firstInvalidStep === null) {
                firstInvalidStep = step;
            }
        }

        if (firstInvalidStep !== null) {
            showAdmissionStep(firstInvalidStep);
            $('.admission-wizard-panel.is-active .validar').filter(function() {
                return $.trim($(this).val() || '').length === 0;
            }).first().focus();
            return true;
        }
        return false;
    }

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }


    function guardar() {
        if (!validar_campos()) {
            var saveButton = $('#admission_save');
            saveButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando ingreso...');
            $.ajax({
                type: 'post',
                url: "{{ route('hospital.ingreso_modulos') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                dataType: 'json',
                data: $("#form_emergencia").serialize(),
                success: function(data) {
                    if (data.respuesta == 'success') {
                        alertas('success', 'Ingreso registrado', data.msj);
                        clearAdmissionDraft();
                        setTimeout(function() {
                            if (data.url) {
                                window.location.href = data.url;
                                return;
                            }
                            if (data.requiere_asignacion_cama && data.asignacion_cama_url) {
                                window.location.href = data.asignacion_cama_url;
                                return;
                            }
                            window.location.href = "{{url('hospital/modulos/index')}}/" + data.paso;
                        }, 1500)
                    } else if (data.respuesta === 'conflicto_clinico') {
                        if (!data.puede_autorizar) {
                            alertas('warning', 'Episodio clínico activo', data.msj + '<br><br>Solicita autorización a un médico responsable o a Sistemas.');
                            return;
                        }
                        Swal.fire({
                            icon: 'warning',
                            title: 'Confirmar excepción clínica',
                            html: data.msj + '<br><br><b>La autorización quedará auditada.</b>',
                            input: 'textarea',
                            inputPlaceholder: 'Describe el motivo clínico de la excepción (mínimo 10 caracteres)',
                            showCancelButton: true,
                            confirmButtonText: 'Autorizar y continuar',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#b26a00',
                            inputValidator: function(value) {
                                if (!value || $.trim(value).length < 10) return 'Debes registrar un motivo clínico suficiente.';
                            }
                        }).then(function(result) {
                            if (result.value) {
                                $('#clinical_override').val('1');
                                $('#clinical_override_reason').val($.trim(result.value));
                                guardar();
                            }
                        });
                    } else {
                        if (data.campo) {
                            var field = $('#' + data.campo);
                            var panelStep = field.closest('.admission-wizard-panel').data('step');
                            if (panelStep) {
                                showAdmissionStep(panelStep);
                            }
                            var visibleField = field.hasClass('select2-hidden-accessible')
                                ? field.next('.select2-container').find('.select2-selection')
                                : field;
                            visibleField.css({ border: '2px solid #CD6155', borderRadius: '9px' });
                            if (field.hasClass('select2-hidden-accessible')) {
                                field.select2('open');
                            } else {
                                field.focus();
                            }
                        }
                        if (data.codigo === 'empresa_principal_no_configurada' && data.configuracion_url) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Falta configurar la empresa',
                                html: 'Crea la empresa del centro médico y quedará marcada automáticamente como principal. <b>Los datos de este ingreso permanecerán guardados como borrador.</b>',
                                showCancelButton: true,
                                confirmButtonText: '<i class="fa fa-building"></i> Configurar empresa',
                                cancelButtonText: 'Continuar después',
                                confirmButtonColor: '#0b7bb8'
                            }).then(function(result) {
                                if (result.value) {
                                    window.location.href = data.configuracion_url;
                                }
                            });
                        } else {
                            alertas('error', 'No se pudo registrar el ingreso', data.msj);
                        }
                    }
                },
                error: function() {
                    alertas('error', 'No se pudo registrar el ingreso', 'Ocurrió un inconveniente al guardar. Tus datos permanecen en el borrador para que puedas intentarlo nuevamente.');
                },
                complete: function() {
                    saveButton.prop('disabled', false).html('<i class="far fa-save"></i>{{trans('emergencia.Guardar')}}');
                }
            });
        } else {
            alertas('warning', 'Faltan datos por completar', 'Revisa los campos marcados en el paso indicado. El borrador se conservará automáticamente.')
        }

    }

    $("#apellidos_nombres").autocomplete({

        source: function(request, response) {
            $.ajax({
                type: 'get',
                url: "{{route('buscar_paciente')}}",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response(data);

                }
            });
        },
        change: function(event, data) {


            console.log(data.item);
            $('#nombre1').val(data.item.nombre1);
            $('#nombre2').val(data.item.nombre2);
            $('#apellido1').val(data.item.apellido1);
            $('#apellido2').val(data.item.apellido2);
            $('#sexo').val(data.item.sexo);
            setAdmissionBirthDate(data.item.fecha_nacimiento);
            $('#ciudad').val(data.item.ciudad);
            $('#telefono1').val(data.item.telefono1);
            $('#telefono2').val(data.item.telefono2);
            $('#direccion').val(data.item.direccion);
            $('#estado').val(data.item.estadocivil);
            $('#ocupacion').val(data.item.ocupacion);
            $('#barrio').val(data.item.barrio);
            $('#parroquia').val(data.item.parroquia);
            $('#canton').val(data.item.canton);
            $('#provincia').val(data.item.provincia);
            setAdmissionAddress(data.item.provincia, data.item.canton, data.item.parroquia);
            $('#nacionalidad').val(data.item.nacionalidad);
            $('#edad').val(data.item.edad);
            $('#direccion_familiar').val(data.item.direccion_familiar);
            $('#forma_llegada').val(data.item.forma_llegada);
            $('#fuente_informacion').val(data.item.fuente_informacion);
            $('#empresa').val(data.item.empresa_trabajo);
            $('#instruccion').val(data.item.instruccion);
            $('#referido').val(data.item.referido);
            $('#telefono_inst_per_paci').val(data.item.telefono_inst_per_paci);
            $('#telefono_llamar').val(data.item.telefono3);
            $('#parentesco').val(data.item.parentesco);
            $('#llamar_a').val(data.item.llamar_a);
            $('#cedula').val(data.item.id);
        },
        selectFirst: true,
        minLength: 3,
    });

    function buscapaciente() {
        var id_paciente = document.getElementById('cedula').value;
        $.ajax({
            type: 'get',
            url: "{{ url('hospital/emergencia/buscar')}}/" + id_paciente, //hospitalizados.buscapaciente

            success: function(data) {
                if (data == 'no') {
                    $('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                    $('#sexo').val('');
                    setAdmissionBirthDate('1980-01-01');
                    $('#ciudad').val('');
                    $('#telefono1').val('');
                    $('#telefono2').val('');
                    $('#direccion').val('');
                    $('#estado').val('');
                    $('#ocupacion').val('');
                    $('#barrio').val('');
                    $('#parroquia').val('');
                    $('#canton').val('');
                    $('#provincia').val('');
                    setAdmissionAddress('', '', '');
                    $('#nacionalidad').val('');
                    $('#edad').val(data.edad);
                    $('#direccion_familiar').val('');
                    $('#forma_llegada').val('');
                    $('#fuente_informacion').val('');
                    $('#empresa').val('');
                    $('#instruccion').val('');
                    $('#referido').val('');
                    $('#telefono_inst_per_paci').val('');
                    $('#telefono_llamar').val('');
                    $('#parentesco').val('');
                    $('#apellidos_nombres').val('');
                    $('#llamar_a').val('');
                    $('#lugarNacimiento').val('');
                    $('#grupoPrioritario').val('');
                    $('#especifiqueGrupo').val('');
                    $('#autoIdentificacionEtnica').val('');
                    $('#nacionalidadEtnica').val('');
                    $('#pueblos').val('');
                    $('#nivelEducacion').val('');
                    $('#estadoNivelEducacion').val('');
                    $('#id_subseguro').val('');
                    $('#limite_credito').val('');
                    $('#callePrincipal').val('');
                    $('#calleSecundaria').val('');
                    $('#referencia').val('');


                } else {
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#sexo').val(data.sexo);
                    setAdmissionBirthDate(data.fecha_nacimiento);
                    $('#ciudad').val(data.ciudad);
                    $('#telefono1').val(data.telefono1);
                    $('#telefono2').val(data.telefono2);
                    $('#direccion').val(data.direccion);
                    $('#estado').val(data.estadocivil);
                    $('#ocupacion').val(data.ocupacion);
                    $('#barrio').val(data.barrio);
                    $('#parroquia').val(data.parroquia);
                    $('#canton').val(data.canton);
                    $('#provincia').val(data.provincia);
                    setAdmissionAddress(data.provincia, data.canton, data.parroquia);
                    $('#nacionalidad').val(data.nacionalidad);
                    $('#edad').val(data.edad);
                    $('#direccion_familiar').val(data.direccion_familiar);
                    $('#forma_llegada').val(data.forma_llegada);
                    $('#fuente_informacion').val(data.fuente_informacion);
                    $('#empresa').val(data.empresa_trabajo);
                    $('#instruccion').val(data.instruccion);
                    $('#referido').val(data.referido);
                    $('#telefono_inst_per_paci').val(data.telefono_inst_per_paci);
                    $('#telefono_llamar').val(data.telefono3);
                    $('#parentesco').val(data.parentesco_afinidad);
                    $('#apellidos_nombres').val(data.apellido1 + ' ' + data.apellido2 + ' ' + data.nombre1 + ' ' + data.nombre2);
                    if (data.apellido1familiar != null) {
                        $('#llamar_a').val(data.apellido1familiar + ' ' + data.apellido2familiar + ' ' + data.nombre1familiar + ' ' + data.nombre2familiar);
                    }
                    $('#lugarNacimiento').val(data.lugar_nacimiento);
                    $('#grupoPrioritario').val(data.grupo_prioritario);
                    $('#especifiqueGrupo').val(data.especifique_grupo);
                    $('#autoIdentificacionEtnica').val(data.autoidentificacion_etnica);
                    $('#nacionalidadEtnica').val(data.nacionalidad_etnica);
                    $('#pueblos').val(data.pueblos);
                    $('#nivelEducacion').val(data.nivel_educacion);
                    $('#estadoNivelEducacion').val(data.estado_nivel_educacion);
                    $('#id_subseguro').val(data.id_subseguro);
                    $('#limite_credito').val(data.limite_credito);
                    $('#callePrincipal').val(data.calle_principal);
                    $('#calleSecundaria').val(data.calle_secundaria);
                    $('#referencia').val(data.referencia);
                }
            }
        });
    }
</script>

@endsection
