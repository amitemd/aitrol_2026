@extends('layouts.app-template-h')

@section('content')
    @php
        $modulo_traducciones = [
            2 => 'hospital.Emergencia',
            3 => 'hospital.Hospitalizacion',
            4 => 'hospital.Imagenes',
            5 => 'hospital.Cirugia',
            6 => 'hospital.UCI',
            7 => 'hospital.UCIN',
        ];
        $modulo_iconos = [
            2 => 'fa-ambulance',
            3 => 'fa-hospital-o',
            4 => 'fa-file-image-o',
            5 => 'fa-bed',
            6 => 'fa-heartbeat',
            7 => 'fa-heartbeat',
        ];
        $modulo_descripciones = [
            2 => 'Consulta y seguimiento de pacientes atendidos en emergencia.',
            3 => 'Control de pacientes ingresados y atención hospitalaria.',
            4 => 'Gestión de pacientes y procedimientos de diagnóstico por imagen.',
            5 => 'Organización y seguimiento de procedimientos quirúrgicos.',
            6 => 'Seguimiento de pacientes en cuidados intensivos.',
            7 => 'Seguimiento de pacientes en cuidados intensivos neonatales.',
        ];
        $id_paso = $ho_estado_paso->id;
        $paso = $ho_estado_paso;
        $modulo_nombre = isset($modulo_traducciones[$id_paso])
            ? trans($modulo_traducciones[$id_paso])
            : $ho_estado_paso->nombre_paso;
        $modulo_icono = isset($modulo_iconos[$id_paso]) ? $modulo_iconos[$id_paso] : 'fa-user-md';
        $modulo_descripcion = isset($modulo_descripciones[$id_paso])
            ? $modulo_descripciones[$id_paso]
            : 'Consulta y seguimiento de pacientes del área médica.';
        $scheduledSurgeryCount = $id_paso == 5 && isset($scheduledSurgeries)
            ? $scheduledSurgeries->count() : 0;
        $listedSurgeryAdmissions = $id_paso == 5
            ? $solicitudes->getCollection()->whereNotNull('encounter_id')->keyBy('encounter_id') : collect();
        $moduleRecordCount = $solicitudes->count() + $scheduledSurgeryCount;
    @endphp

    <style>
        .hospital-module-page {
            --module-navy: #073b66;
            --module-blue: #0877be;
            --module-teal: #0d9488;
            --module-ink: #17324d;
            --module-muted: #65788b;
            --module-line: #dbe7ef;
            --module-soft: #f4f8fb;
            padding: .5rem 0 2rem;
            color: var(--module-ink);
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        }

        .hospital-module-page,
        .hospital-module-page * {
            box-sizing: border-box;
        }

        .module-breadcrumb {
            display: flex;
            align-items: center;
            gap: .55rem;
            margin-bottom: 1rem;
            color: var(--module-muted);
            font-size: .82rem;
        }

        .module-breadcrumb a {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: var(--module-blue);
            font-weight: 600;
            text-decoration: none;
        }

        .module-breadcrumb a:hover {
            color: var(--module-navy);
        }

        .module-heading {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 1.5rem 1.7rem;
            overflow: hidden;
            border-radius: 18px;
            background: linear-gradient(115deg, #063b67 0%, #0877ad 68%, #0d9488 100%);
            box-shadow: 0 14px 34px rgba(7, 59, 102, .14);
            color: #fff;
        }

        .module-heading::after {
            content: '';
            position: absolute;
            width: 210px;
            height: 210px;
            right: -70px;
            top: -115px;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 50%;
        }

        .module-heading-main {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .module-heading-icon {
            display: grid;
            width: 58px;
            height: 58px;
            flex: 0 0 58px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 16px;
            background: rgba(255, 255, 255, .14);
            color: #fff;
            font-size: 1.55rem;
        }

        .module-heading-label {
            display: block;
            margin-bottom: .28rem;
            color: #bde9ee;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .module-heading h1 {
            margin: 0;
            color: #fff;
            font-size: 1.65rem;
            font-weight: 700;
            line-height: 1.25;
        }

        .module-heading p {
            max-width: 720px;
            margin: .35rem 0 0;
            color: rgba(255, 255, 255, .82);
            font-size: .86rem;
        }

        .module-heading-count {
            position: relative;
            z-index: 1;
            min-width: 132px;
            padding: .72rem 1rem;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 13px;
            background: rgba(255, 255, 255, .12);
            text-align: center;
        }

        .module-heading-count strong {
            display: block;
            color: #fff;
            font-size: 1.45rem;
            line-height: 1;
        }

        .module-heading-count span {
            display: block;
            margin-top: .35rem;
            color: #ccecef;
            font-size: .68rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .module-workspace {
            margin-top: 1.25rem;
            overflow: hidden;
            border: 1px solid var(--module-line);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(18, 69, 116, .07);
        }

        .module-workspace-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--module-line);
        }

        .module-workspace-title {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .module-workspace-title-icon {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 10px;
            background: #eaf5fb;
            color: var(--module-blue);
        }

        .module-workspace-header h2 {
            margin: 0;
            color: var(--module-ink);
            font-size: 1.05rem;
            font-weight: 700;
        }

        .module-workspace-header p {
            margin: .15rem 0 0;
            color: var(--module-muted);
            font-size: .75rem;
        }

        .module-card-tool {
            display: grid;
            width: 34px;
            height: 34px;
            padding: 0 !important;
            place-items: center;
            border: 1px solid var(--module-line) !important;
            border-radius: 9px !important;
            background: #fff !important;
            color: var(--module-muted) !important;
        }

        .module-filter {
            padding: 1.35rem 1.5rem;
            border-bottom: 1px solid var(--module-line);
            background: var(--module-soft);
        }

        .module-filter-grid {
            display: grid;
            grid-template-columns: .9fr 1.3fr .9fr .9fr;
            gap: 1rem;
        }

        .module-filter-group {
            min-width: 0;
            margin: 0;
        }

        .hospital-module-page .module-filter-group label {
            display: block;
            margin: 0 0 .45rem;
            color: var(--module-ink) !important;
            font-size: .75rem;
            font-weight: 700;
        }

        .module-input-wrap {
            position: relative;
        }

        .module-input-wrap > i {
            position: absolute;
            z-index: 2;
            left: .85rem;
            top: 50%;
            color: #8ca1b4;
            font-size: .88rem;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .hospital-module-page .module-input-wrap .form-control {
            width: 100%;
            height: 42px;
            padding: .58rem .8rem .58rem 2.45rem;
            border: 1px solid #ccdae5;
            border-radius: 10px;
            background: #fff;
            color: var(--module-ink) !important;
            font-size: .84rem !important;
            box-shadow: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .hospital-module-page .module-input-wrap .form-control:focus {
            border-color: var(--module-blue);
            box-shadow: 0 0 0 3px rgba(8, 119, 190, .12);
        }

        .module-filter-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-top: 1rem;
        }

        .module-filter-actions-left {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
        }

        .hospital-module-page .module-primary-btn,
        .hospital-module-page .module-secondary-btn {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .62rem 1rem;
            border-radius: 10px;
            font-size: .8rem !important;
            font-weight: 700;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .hospital-module-page .module-primary-btn {
            border: 1px solid var(--module-blue);
            background: var(--module-blue);
            color: #fff;
        }

        .hospital-module-page .module-secondary-btn {
            border: 1px solid #b8d8e7;
            background: #fff;
            color: var(--module-navy);
        }

        .hospital-module-page .module-primary-btn:hover,
        .hospital-module-page .module-primary-btn:focus {
            color: #fff;
            background: #0667a5;
            box-shadow: 0 7px 16px rgba(8, 119, 190, .2);
            transform: translateY(-1px);
        }

        .hospital-module-page .module-secondary-btn:hover,
        .hospital-module-page .module-secondary-btn:focus {
            color: var(--module-navy);
            border-color: var(--module-teal);
            background: #edf9f7;
        }

        .module-filter-date {
            color: var(--module-muted);
            font-size: .73rem;
        }

        .module-table-section {
            padding: 1.35rem 1.5rem 1.5rem;
        }

        .module-table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .9rem;
        }

        .module-table-heading h3 {
            margin: 0;
            color: var(--module-ink);
            font-size: .95rem;
            font-weight: 700;
        }

        .module-table-heading span {
            padding: .35rem .62rem;
            border-radius: 999px;
            background: #edf7fb;
            color: var(--module-blue);
            font-size: .68rem;
            font-weight: 700;
        }

        .hospital-module-page .module-patient-table {
            width: 100%;
            min-width: 1180px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--module-ink) !important;
            font-size: .8rem !important;
            table-layout: fixed;
        }

        .hospital-module-page .module-patient-table thead th {
            padding: .78rem .85rem;
            border-top: 1px solid var(--module-line);
            border-bottom: 1px solid var(--module-line);
            background: #f5f8fb;
            color: #40576d !important;
            font-size: .68rem !important;
            font-weight: 700 !important;
            letter-spacing: .035em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .hospital-module-page .module-patient-table thead th:first-child {
            border-left: 1px solid var(--module-line);
            border-radius: 10px 0 0 10px;
        }

        .hospital-module-page .module-patient-table thead th:last-child {
            border-right: 1px solid var(--module-line);
            border-radius: 0 10px 10px 0;
        }

        .hospital-module-page .module-patient-table tbody td {
            padding: .95rem .85rem;
            border-top: 0;
            border-bottom: 1px solid #edf2f6;
            color: var(--module-ink) !important;
            font-size: .78rem !important;
            font-weight: 500;
            vertical-align: middle;
        }

        .hospital-module-page .module-patient-table tbody tr:hover td {
            background: #f8fbfd;
        }

        .hospital-module-page .module-patient-table tbody .module-scheduled-surgery td {
            background: #f5fbfd;
        }

        .hospital-module-page .module-patient-table tbody .module-scheduled-surgery:hover td {
            background: #eaf7fb;
        }

        .module-schedule-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .32rem .58rem;
            border: 1px solid #9fd8e2;
            border-radius: 999px;
            background: #e5f7f9;
            color: #08778e;
            font-size: .66rem;
            font-weight: 800;
            letter-spacing: .035em;
            text-transform: uppercase;
        }

        .module-priority {
            display: inline-flex;
            min-width: 88px;
            justify-content: center;
            padding: .34rem .55rem;
            border-radius: 999px;
            color: #fff;
            font-size: .68rem;
            font-weight: 700;
        }

        .priority-1 { background: #d93a3a; }
        .priority-2 { background: #e47718; }
        .priority-3 { background: #c99a00; }
        .priority-4 { background: #208b54; }
        .priority-5 { background: #2864ad; }

        .module-action-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(105px, 1fr));
            gap: .4rem;
            width: 100%;
        }

        .module-action-list > form {
            margin: 0;
        }

        .surgical-scalpel-icon {
            width: 14px;
            height: 14px;
            flex: 0 0 14px;
            fill: currentColor;
            vertical-align: -2px;
        }

        #anesthesiaPlanningModal .modal-dialog { width: calc(100% - 32px); max-width: 1080px; margin: 18px auto; }
        #anesthesiaPlanningModal .modal-content { border: 0; border-radius: 16px; overflow: hidden; background: #f3f8fb; box-shadow: 0 24px 70px rgba(7, 42, 70, .32); }
        #anesthesiaPlanningModal .modal-header { border-radius: 14px 14px 0 0; background: linear-gradient(120deg, #075486, #17a6b7); color: #fff; }
        #anesthesiaPlanningModal .modal-title {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin: 0;
            color: #fff !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 28, 54, .65);
        }
        #anesthesiaPlanningModal .modal-title .surgical-scalpel-icon { color: #fff !important; fill: #fff !important; }
        #anesthesiaPlanningModal .anesthesia-modal-close { display: inline-flex; align-items: center; justify-content: center; flex: 0 0 38px; width: 38px; height: 38px; margin: -4px -2px -4px 12px; padding: 0; border: 1px solid rgba(255,255,255,.65); border-radius: 8px; background: rgba(255,255,255,.16); color: #fff !important; opacity: 1; font-size: 20px; line-height: 1; text-shadow: none; }
        #anesthesiaPlanningModal .anesthesia-modal-close:hover,
        #anesthesiaPlanningModal .anesthesia-modal-close:focus { background: #fff; color: #075486 !important; outline: 0; }
        #anesthesiaPlanningModal .modal-footer { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .65rem; padding: 14px 20px; border-top: 1px solid #dbe8ef; background: #fff; box-shadow: 0 -8px 24px rgba(18, 67, 96, .08); }
        #anesthesiaPlanningModal .modal-footer form { margin: 0; }
        #anesthesiaPlanningModal .modal-footer .btn { min-height: 40px; padding: 9px 18px; border-radius: 8px; font-weight: 700; }
        #anesthesiaPlanningModal .modal-body { max-height: calc(100vh - 175px); padding: 18px 20px 22px; overflow-y: auto; background: #f3f8fb; }
        #anesthesiaPlanningModal .select2-container { width: 100% !important; }
        #anesthesiaPlanningModal .select2-selection { min-height: 42px; border-color: #c9dce7; border-radius: 8px; }
        #anesthesiaPlanningModal .form-control { min-height: 42px; border: 1px solid #c9dce7; border-radius: 8px; background: #fff; box-shadow: none; transition: border-color .18s ease, box-shadow .18s ease; }
        #anesthesiaPlanningModal input[type="text"],
        #anesthesiaPlanningModal textarea,
        #anesthesiaPlanningModal .select2-selection__rendered { text-transform: uppercase; }
        #anesthesiaPlanningModal .form-control:focus,
        #anesthesiaPlanningModal .select2-container--focus .select2-selection { border-color: #0b8eb3; box-shadow: 0 0 0 3px rgba(11, 142, 179, .12); }
        #anesthesiaPlanningModal .form-group { margin-bottom: 16px; }
        #anesthesiaPlanningModal .form-group > label { margin-bottom: 7px; color: #173f5b; font-size: 12px; font-weight: 700; }
        #anesthesiaPlanningModal .help-block { margin: 5px 0 0; color: #6b8292; font-size: 12px; }
        #anesthesiaPlanningModal .anesthesia-modal-section { margin: 8px 0 18px; padding: 11px 14px; border-left: 4px solid #0b8eb3; border-radius: 0 7px 7px 0; background: linear-gradient(90deg, #e8f6fa, #f6fbfd); color: #0b4e6d; font-size: 14px; font-weight: 700; }
        #anesthesiaPlanningModal .anesthesia-modal-note { color: #587487; font-size: 12px; margin: -10px 0 16px; }
        #anesthesiaPlanningModal .anesthesia-core-card { margin-bottom: 16px; padding: 16px 16px 2px; border: 1px solid #d7e6ee; border-radius: 12px; background: #fff; box-shadow: 0 5px 16px rgba(18, 67, 96, .06); }
        #anesthesiaPlanningModal .anesthesia-patient-summary { margin-bottom: 14px; padding: 12px 15px; border: 0; border-radius: 10px; background: linear-gradient(110deg, #dff6fb, #edf8ff); color: #0b5f7d; box-shadow: inset 4px 0 0 #16a4b6; }
        #anesthesiaPlanningModal .anesthesia-patient-summary strong { font-size: 14px; }
        #anesthesiaPlanningModal .anesthesia-patient-summary .small { margin-top: 3px; color: #537487; }
        #anesthesiaPlanningModal .anesthesia-tabs { position: sticky; top: -18px; z-index: 8; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin: 0 0 12px; padding: 10px 0; background: #f3f8fb; border: 0; }
        #anesthesiaPlanningModal .anesthesia-tabs > li { float: none; margin: 0; }
        #anesthesiaPlanningModal .anesthesia-tabs > li > a { display: flex; align-items: center; justify-content: center; min-height: 44px; margin: 0; padding: 10px 16px; border: 1px solid #cbdfe9; border-radius: 10px; background: #fff; color: #315d75; font-size: 13px; font-weight: 700; box-shadow: 0 3px 9px rgba(18, 67, 96, .05); }
        #anesthesiaPlanningModal .anesthesia-tabs > li.active > a,
        #anesthesiaPlanningModal .anesthesia-tabs > li.active > a:hover,
        #anesthesiaPlanningModal .anesthesia-tabs > li.active > a:focus { border-color: #087aa4; background: linear-gradient(120deg, #075486, #119eb0); color: #fff; box-shadow: 0 6px 16px rgba(7, 84, 134, .22); }
        #anesthesiaPlanningModal .tab-content > .tab-pane { padding: 16px; border: 1px solid #d7e6ee; border-radius: 12px; background: #fff; box-shadow: 0 5px 16px rgba(18, 67, 96, .06); }
        #anesthesiaPlanningModal .anesthesia-tab-actions { display: flex; justify-content: flex-end; margin-top: 8px; padding-top: 12px; border-top: 1px solid #edf2f5; }
        #anesthesiaPlanningModal textarea.form-control { min-height: 78px; resize: vertical; }
        #anesthesiaPlanningModal .anesthesia-signature-note { padding: 12px 14px; border: 1px solid #cfe5ef; border-radius: 8px; background: #f5fbfd; color: #315d75; }
        #anesthesiaPlanningModal .anesthesia-diagram-upload { padding: 14px; border: 1px dashed #8bbfd1; border-radius: 10px; background: #f6fbfd; }
        #anesthesiaPlanningModal .anesthesia-diagram-gallery { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-top: 12px; }
        #anesthesiaPlanningModal .anesthesia-diagram-card { position: relative; min-height: 120px; padding: 8px; border: 1px solid #d4e4ec; border-radius: 9px; background: #fff; text-align: center; }
        #anesthesiaPlanningModal .anesthesia-diagram-card img { width: 100%; height: 105px; object-fit: contain; border-radius: 5px; background: #eef5f8; }
        #anesthesiaPlanningModal .anesthesia-diagram-card span { display: block; margin-top: 5px; overflow: hidden; color: #47677a; font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
        #anesthesiaPlanningModal .anesthesia-diagram-remove { display: block; margin: 6px 0 0; color: #a42a2a; font-size: 11px; }
        @media (max-width: 760px) {
            #anesthesiaPlanningModal .modal-dialog { width: calc(100% - 14px); margin: 7px auto; }
            #anesthesiaPlanningModal .modal-body { padding: 12px; }
            #anesthesiaPlanningModal .anesthesia-tabs { gap: 5px; }
            #anesthesiaPlanningModal .anesthesia-tabs > li > a { white-space: nowrap; }
        }

        .pdf-preview-modal .modal-dialog {
            --module-navy: #073b66;
            --module-blue: #0877be;
            --module-teal: #0d9488;
            --module-ink: #17324d;
            --module-line: #dbe7ef;
            width: calc(100% - 32px);
            max-width: 1500px;
            margin: 16px auto;
        }

        .pdf-preview-modal .modal-content {
            overflow: hidden;
            border: 0;
            border-radius: 16px;
            box-shadow: 0 22px 60px rgba(7, 59, 102, .28);
        }

        .pdf-preview-modal .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 58px;
            padding: .9rem 1.2rem;
            border-bottom: 1px solid var(--module-line);
            background: #fff;
        }

        .pdf-preview-modal .modal-title {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .65rem;
            margin: 0;
            color: var(--module-ink);
            font-size: 1rem;
            font-weight: 700;
        }

        .pdf-preview-modal .modal-title i {
            color: var(--module-blue);
        }

        .pdf-preview-close {
            display: inline-flex;
            flex: 0 0 38px;
            width: 38px;
            height: 38px;
            margin-left: 1rem;
            padding: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid #d8e2ea;
            border-radius: 50%;
            background: #f4f7fa;
            box-shadow: 0 3px 10px rgba(23, 50, 77, .08);
            color: #496174;
            font-family: Arial, sans-serif;
            font-size: 25px;
            font-weight: 400;
            line-height: 1;
            cursor: pointer;
            transition: background-color .18s ease, border-color .18s ease, color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .pdf-preview-close span {
            display: block;
            transform: translateY(-1px);
        }

        .pdf-preview-close:hover {
            border-color: #f2b8b5;
            background: #fff1f0;
            box-shadow: 0 5px 14px rgba(180, 35, 24, .14);
            color: #b42318;
            transform: translateY(-1px);
        }

        .pdf-preview-close:focus {
            outline: 0;
            border-color: var(--module-blue);
            box-shadow: 0 0 0 3px rgba(8, 119, 190, .2);
            color: var(--module-navy);
        }

        .pdf-preview-close:active {
            transform: translateY(0);
        }

        .pdf-preview-modal .modal-body {
            position: relative;
            height: calc(100vh - 150px);
            min-height: 460px;
            padding: 0;
            background: #eef3f7;
            overflow: hidden;
        }

        .pdf-preview-loading {
            position: absolute;
            z-index: 2;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fbfd 0%, #edf4f8 100%);
            opacity: 1;
            visibility: visible;
            transition: opacity .2s ease, visibility .2s ease;
        }

        .pdf-preview-loading.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .pdf-preview-loading-content {
            max-width: 320px;
            padding: 1.5rem;
            color: #52697d;
            text-align: center;
        }

        .pdf-preview-spinner {
            width: 46px;
            height: 46px;
            margin: 0 auto 1rem;
            border: 4px solid #d5e5ee;
            border-top-color: var(--module-blue);
            border-radius: 50%;
            animation: pdfPreviewSpin .8s linear infinite;
        }

        .pdf-preview-loading-content strong {
            display: block;
            margin-bottom: .35rem;
            color: var(--module-ink);
            font-size: .95rem;
        }

        .pdf-preview-loading-content span {
            display: block;
            font-size: .75rem;
            line-height: 1.5;
        }

        @keyframes pdfPreviewSpin {
            to { transform: rotate(360deg); }
        }

        .pdf-preview-frame {
            display: block;
            width: 100%;
            height: 100%;
            border: 0;
            background: #fff;
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease;
        }

        .pdf-preview-frame.is-ready {
            opacity: 1;
            visibility: visible;
        }

        .pdf-preview-modal .modal-footer {
            padding: .75rem 1.2rem;
            border-top: 1px solid var(--module-line);
        }

        .pdf-preview-modal .module-primary-btn,
        .pdf-preview-modal .module-secondary-btn {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            padding: .55rem .9rem;
            border-radius: 9px;
            font-size: .78rem;
            font-weight: 700;
        }

        .pdf-preview-modal .module-primary-btn {
            border: 1px solid var(--module-blue);
            background: var(--module-blue);
            color: #fff;
        }

        .pdf-preview-modal .module-secondary-btn {
            border: 1px solid #b8d8e7;
            background: #fff;
            color: var(--module-navy);
        }

        @media (max-width: 767px) {
            .pdf-preview-modal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            .pdf-preview-modal .modal-body {
                height: calc(100vh - 132px);
                min-height: 360px;
            }
        }

        .hospital-module-page .module-action-list .btn {
            display: inline-flex;
            width: 100%;
            min-height: 32px;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            margin: 0;
            padding: .4rem .58rem;
            border-radius: 8px;
            font-size: .67rem !important;
            font-weight: 700;
        }

        .module-empty-state {
            padding: 3.5rem 1.5rem !important;
            border-bottom: 0 !important;
            text-align: center;
        }

        .module-empty-icon {
            display: inline-flex;
            width: 58px;
            height: 58px;
            margin: 0 auto .9rem;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: #eaf5fb;
            color: var(--module-blue);
            line-height: 1;
        }

        .module-empty-icon > i {
            display: block;
            margin: 0;
            font-size: 1.65rem;
            line-height: 1;
        }

        .module-empty-state strong {
            display: block;
            color: var(--module-ink);
            font-size: .9rem;
        }

        .module-empty-copy {
            display: block;
            margin-top: .3rem;
            color: var(--module-muted);
            font-size: .76rem;
        }

        .dark-layout .module-workspace,
        .dark-layout .module-card-tool,
        .dark-layout .hospital-module-page .module-input-wrap .form-control {
            border-color: #3d4c5e;
            background: #283447;
        }

        .dark-layout .module-filter,
        .dark-layout .hospital-module-page .module-patient-table thead th {
            border-color: #3d4c5e;
            background: #222d3f;
        }

        .dark-layout .module-workspace-header h2,
        .dark-layout .module-table-heading h3,
        .dark-layout .module-empty-state strong,
        .dark-layout .hospital-module-page .module-filter-group label,
        .dark-layout .hospital-module-page .module-input-wrap .form-control,
        .dark-layout .hospital-module-page .module-patient-table tbody td {
            color: #f1f5f8 !important;
        }

        .dark-layout .module-workspace-header p,
        .dark-layout .module-filter-date,
        .dark-layout .module-empty-copy {
            color: #b6c2cd;
        }

        @media (max-width: 1199.98px) {
            .module-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .hospital-module-page {
                padding-top: 0;
            }

            .module-heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 1.3rem;
                border-radius: 15px;
            }

            .module-heading-count {
                width: 100%;
                text-align: left;
            }

            .module-heading-count strong,
            .module-heading-count span {
                display: inline;
            }

            .module-heading-count span {
                margin-left: .4rem;
            }

            .module-workspace {
                border-radius: 15px;
            }

            .module-filter-grid {
                grid-template-columns: 1fr;
            }

            .module-filter-actions,
            .module-workspace-header {
                align-items: flex-start;
            }

            .module-filter-actions {
                flex-direction: column;
            }

            .module-filter-date {
                order: -1;
            }
        }

        @media (max-width: 479.98px) {
            .module-heading-main {
                align-items: flex-start;
            }

            .module-heading-icon {
                width: 48px;
                height: 48px;
                flex-basis: 48px;
                border-radius: 13px;
            }

            .module-heading h1 {
                font-size: 1.3rem;
            }

            .module-workspace-header,
            .module-filter,
            .module-table-section {
                padding-right: 1rem;
                padding-left: 1rem;
            }

            .module-filter-actions-left,
            .hospital-module-page .module-primary-btn,
            .hospital-module-page .module-secondary-btn {
                width: 100%;
            }
        }
    </style>

    <main class="hospital-module-page">
        @if(session('agenda_success'))
            <div class="alert alert-success" role="status"><i class="fa fa-check-circle" aria-hidden="true"></i> {{ session('agenda_success') }}</div>
        @endif
        <nav class="module-breadcrumb" aria-label="Navegación">
            <a href="{{ route('hospital.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Área médica
            </a>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
            <span>{{ $modulo_nombre }}</span>
        </nav>

        <section class="module-heading" aria-labelledby="module-page-title">
            <div class="module-heading-main">
                <span class="module-heading-icon"><i class="fa {{ $modulo_icono }}" aria-hidden="true"></i></span>
                <div>
                    <span class="module-heading-label">Gestión clínica</span>
                    <h1 id="module-page-title">Pacientes en {{ $modulo_nombre }}</h1>
                    <p>{{ $modulo_descripcion }}</p>
                </div>
            </div>
            <div class="module-heading-count" aria-label="Pacientes encontrados">
                <strong>{{ $moduleRecordCount }}</strong>
                <span>pacientes encontrados</span>
            </div>
        </section>

        <section class="module-workspace card card-primary">
            <header class="module-workspace-header card-header with-border">
                <div class="module-workspace-title">
                    <span class="module-workspace-title-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <div>
                        <h2>Consulta de pacientes</h2>
                        <p>Utiliza uno o varios filtros para localizar registros.</p>
                    </div>
                </div>
                <div class="card-tools">
                    <button type="button" class="btn btn-card-tool module-card-tool" data-widget="collapse" aria-label="Contraer panel">
                        <i class="fa fa-minus" aria-hidden="true"></i>
                    </button>
                </div>
            </header>

            <div class="card-body p-0">
                <form id="form_buscar" method="POST" action="{{ route('hospital.index_modulos', ['id_paso' => $ho_estado_paso->id]) }}" class="module-filter">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_estado_paso" id="id_estado_paso" value="{{ $ho_estado_paso->id }}">

                    <div class="module-filter-grid">
                        <div class="form-group module-filter-group">
                            <label for="cedula">{{ trans('hospitalizacion.Cedula') }}</label>
                            <div class="module-input-wrap">
                                <i class="fa fa-id-card-o" aria-hidden="true"></i>
                                <input type="text" class="form-control input-sm" name="cedula" id="cedula" autocomplete="off" value="{{ $cedula }}" placeholder="Número de identificación">
                            </div>
                        </div>

                        <div class="form-group module-filter-group">
                            <label for="paciente">{{ trans('hospitalizacion.Pacientes') }}</label>
                            <div class="module-input-wrap">
                                <i class="fa fa-user-o" aria-hidden="true"></i>
                                <input type="text" class="form-control input-sm" name="paciente" id="paciente" autocomplete="off" value="{{ $paciente }}" placeholder="Nombres o apellidos">
                            </div>
                        </div>

                        <div class="form-group module-filter-group">
                            <label for="fecha_desde">{{ trans('hospitalizacion.FechaDesde') }}</label>
                            <div class="module-input-wrap">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                <input type="text" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{ $desde }}">
                            </div>
                        </div>

                        <div class="form-group module-filter-group">
                            <label for="fecha_hasta">{{ trans('hospitalizacion.FechaHasta') }}</label>
                            <div class="module-input-wrap">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                <input type="text" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{ $hasta }}">
                            </div>
                        </div>

                        <!-- El filtro de sala se conserva para una futura reactivacion. -->
                        <!--
                        <div class="form-group module-filter-group">
                            <label for="sala">{{ trans('hospitalizacion.Salas') }}</label>
                            <select class="form-control" id="sala" name="sala">
                                <option value=" ">{{ trans('hospitalizacion.SeleccioneOpcion') }}</option>
                                @foreach ($salas as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre_sala }}</option>
                                @endforeach
                            </select>
                        </div>
                        -->
                    </div>

                    <div class="module-filter-actions">
                        <div class="module-filter-actions-left">
                            <button class="btn module-primary-btn" type="submit">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                Buscar pacientes
                            </button>
                            <a class="btn module-secondary-btn"
                                @if((int) $id_paso === 5)
                                    id="openSurgeryBooking" aria-haspopup="dialog"
                                    href="{{ route('hospital.index_modulos', ['id_paso' => 5, 'agendar' => 1] + request()->only('cedula', 'paciente', 'fecha_desde', 'fecha_hasta')) }}"
                                @else
                                    href="{{ route('hospital.admision', ['id_paso' => $ho_estado_paso->id]) }}"
                                @endif>
                                <i class="fa @if((int) $ho_estado_paso->id === 5) fa-calendar-plus-o @else fa-plus @endif" aria-hidden="true"></i>
                                @if((int) $ho_estado_paso->id === 5)
                                    Agendar cirugía
                                @else
                                    {{ trans('hospitalizacion.Ingreso') }}
                                @endif
                            </a>
                        </div>
                        <span class="module-filter-date">
                            @if($desde === '' && $hasta === '')
                                <i class="fa @if((int) $ho_estado_paso->id === 5) fa-calendar-check-o @else fa-bed @endif" aria-hidden="true"></i>
                                @if((int) $ho_estado_paso->id === 5) Mostrando cirugías activas y programadas @else Mostrando ingresos activos @endif
                            @else
                                <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                Periodo: {{ $desde !== '' ? $desde : 'inicio' }} al {{ $hasta !== '' ? $hasta : 'hoy' }}
                            @endif
                        </span>
                    </div>
                </form>

            @include('hospital.workflow.pending-transfers')
            @if((int) $id_paso === 5 && $errors->has('documentos_cirugia'))
                <div class="alert alert-warning" role="alert">{{ $errors->first('documentos_cirugia') }}</div>
            @endif
            <div class="module-table-section table-responsive" id="tablaajax">
                    <div class="module-table-heading">
                        <h3>Listado de pacientes</h3>
                        <span>{{ $moduleRecordCount }} registros</span>
                    </div>

                    <table class="table table-hover module-patient-table">
                        <colgroup>
                            <col style="width: 8%">
                            <col style="width: 8%">
                            <col style="width: 10%">
                            <col style="width: 20%">
                            <col style="width: 14%">
                            @if($id_paso == 2)
                                <col style="width: 10%">
                                <col style="width: 30%">
                            @elseif($id_paso == 5)
                                <col style="width: 14%">
                                <col style="width: 26%">
                            @else
                                <col style="width: 40%">
                            @endif
                        </colgroup>
                        <thead>
                            <tr>
                                <th>{{ trans('hospitalizacion.Fecha') }}</th>
                                <th>{{ trans('hospitalizacion.Ingreso') }}</th>
                                <th>{{ trans('hospitalizacion.Cedula') }}</th>
                                <th>{{ trans('hospitalizacion.Paciente') }}</th>
                                <th>
                                    @if($id_paso == 4 or $id_paso == 5)
                                        {{ trans('hospitalizacion.Doctor') }}
                                    @else
                                        {{ trans('hospitalizacion.Usuario') }}
                                    @endif
                                </th>
                                @if($id_paso == 2)
                                    <th>{{ trans('emergencia.PrioridadTabla') }}</th>
                                @endif
                                @if($id_paso == 5)
                                    <th>QUIROFANO / SALA</th>
                                @endif
                                <th>{{ trans('hospitalizacion.Accion') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($id_paso == 5 && isset($scheduledSurgeries))
                                @foreach($scheduledSurgeries as $scheduledSurgery)
                                    @php
                                        $scheduledPatient = $scheduledSurgery->paciente;
                                        $scheduledDoctor = $scheduledSurgery->doctor1;
                                        $scheduledRoom = $scheduledSurgery->sala;
                                        $linkedAdmission = $listedSurgeryAdmissions->get($scheduledSurgery->encounter_id);
                                        $scheduledState = $scheduledSurgery->estado_quirurgico
                                            ? str_replace('_', ' ', $scheduledSurgery->estado_quirurgico)
                                            : 'PROGRAMADA';
                                    @endphp
                                    <tr id="cirugia-agenda-{{ $scheduledSurgery->id }}" class="module-scheduled-surgery">
                                        <td>{{ substr($scheduledSurgery->fechaini, 0, 10) }}</td>
                                        <td>
                                            @if($linkedAdmission)
                                                {{ substr($linkedAdmission->fecha_ingreso, 0, 10) }}
                                            @else
                                                <span class="module-schedule-badge">Agenda</span>
                                            @endif
                                        </td>
                                        <td>{{ $scheduledSurgery->id_paciente ?: 'No disponible' }}</td>
                                        <td>
                                            @if($scheduledPatient)
                                                {{ $scheduledPatient->apellido1 }} {{ $scheduledPatient->apellido2 }} {{ $scheduledPatient->nombre1 }} {{ $scheduledPatient->nombre2 }}
                                            @else
                                                Información del paciente no disponible
                                            @endif
                                            @if($scheduledSurgery->procedimiento)
                                                <div class="text-muted small">{{ $scheduledSurgery->procedimiento->nombre }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($scheduledDoctor)
                                                Dr. {{ mb_strtoupper($scheduledDoctor->apellido1.' '.$scheduledDoctor->nombre1, 'UTF-8') }}
                                            @else
                                                No asignado
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $scheduledRoom ? mb_strtoupper($scheduledRoom->nombre_sala, 'UTF-8') : 'SALA NO REGISTRADA' }}</strong>
                                            <div class="text-muted small">{{ $scheduledState }}@unless($linkedAdmission) / PENDIENTE DE INGRESO @endunless</div>
                                        </td>
                                        <td>
                                            <div class="module-action-list">
                                                <a class="btn btn-info btn-sm" href="{{ route('v3.doctor-agenda.edit', ['doctor' => $scheduledSurgery->id_doctor1, 'appointment' => $scheduledSurgery->id]) }}">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i> Ver agenda
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            @forelse ($solicitudes as $solicitud)
                                @php
                                    $triaje = $solicitud->manchester->first();
                                    $pacienteSolicitud = $solicitud->paciente
                                        ?: ($solicitud->agenda ? $solicitud->agenda->paciente : null);
                                    $formulariosSolicitud = isset($formulariosDisponibles[$solicitud->id])
                                        ? $formulariosDisponibles[$solicitud->id]
                                        : array('001' => false, '005' => false, '008' => false);
                                    $surgeryAgenda = $id_paso == 5 && isset($surgeryAgendas)
                                        ? $surgeryAgendas->get($solicitud->encounter_id) : null;
                                @endphp
                                <tr @if($id_paso == 5 && ($surgeryAgenda || $solicitud->id_agenda)) id="cirugia-agenda-{{ $surgeryAgenda ? $surgeryAgenda->id : $solicitud->id_agenda }}" @endif>
                                    <td>{{ substr($surgeryAgenda ? $surgeryAgenda->fechaini : $solicitud->fechaini, 0, 10) }}</td>
                                    <td>{{ substr($solicitud->fecha_ingreso, 0, 10) }}</td>
                                    <td>{{ $solicitud->id_paciente ?: 'No disponible' }}</td>
                                    <td>
                                        @if($pacienteSolicitud)
                                            {{ $pacienteSolicitud->apellido1 }} {{ $pacienteSolicitud->apellido2 }} {{ $pacienteSolicitud->nombre1 }} {{ $pacienteSolicitud->nombre2 }}
                                        @else
                                            Informaci&oacute;n del paciente no disponible
                                        @endif
                                    </td>
                                    <td>
                                        @if($id_paso == 4 or $id_paso == 5) Dr. @endif
                                        @php
                                            $displayDoctor = $surgeryAgenda && $surgeryAgenda->doctor1
                                                ? $surgeryAgenda->doctor1 : ($solicitud->agenda ? $solicitud->agenda->doctor1 : null);
                                        @endphp
                                        @if($displayDoctor)
                                            {{ mb_strtoupper($displayDoctor->apellido1.' '.$displayDoctor->nombre1, 'UTF-8') }}
                                        @else
                                            No asignado
                                        @endif
                                    </td>

                                    @if($id_paso == 2)
                                        <td>
                                            @if(!is_null($triaje) && isset($triaje->ho_prioridad))
                                                <span class="module-priority priority-{{ $triaje->prioridad }}">
                                                    {{ $triaje->ho_prioridad->nombre }}
                                                </span>
                                            @else
                                                <span class="text-muted">Sin prioridad</span>
                                            @endif
                                        </td>
                                    @endif

                                    @if($id_paso == 5)
                                        @php
                                            $surgeryTransfer = $surgeryTransfers->get($solicitud->id);
                                            $surgeryState = isset($surgeryStates[$solicitud->id]) ? $surgeryStates[$solicitud->id] : 'RECEPCION QUIRURGICA';
                                        @endphp
                                        <td>
                                            <strong>{{ $surgeryTransfer && $surgeryTransfer->sala
                                                ? mb_strtoupper($surgeryTransfer->sala->nombre_sala, 'UTF-8')
                                                : ($surgeryAgenda && $surgeryAgenda->sala
                                                    ? mb_strtoupper($surgeryAgenda->sala->nombre_sala, 'UTF-8')
                                                    : 'SALA NO REGISTRADA') }}</strong>
                                            <div class="text-muted small">{{ $surgeryState }}</div>
                                        </td>
                                    @endif

                                    <td>
                                        <div class="module-action-list">
                                            @if((int) $id_paso === 5 || in_array((int) $solicitud->estado_paso, [2, 3, 5, 6, 7], true))
                                            <a href="{{route('hospital.documents.index', ['id_solicitud' => $solicitud->id])}}" class="btn btn-info btn-sm"><i class="fa fa-folder-open"></i> Documentos</a>
                                            @if((int) $id_paso === 5)
                                                @php
                                                    $form017Draft = isset($surgeryForm017Drafts)
                                                        ? $surgeryForm017Drafts->get($solicitud->id) : null;
                                                    $form017Payload = $form017Draft ? (array) $form017Draft->payload : [];
                                                    $form017PayloadEncoded = base64_encode(json_encode($form017Payload, JSON_UNESCAPED_UNICODE));
                                                @endphp
                                                <button type="button" id="anesthesiaPlanner{{ $solicitud->id }}" class="btn btn-primary btn-sm js-anesthesia-planner" data-toggle="modal" data-target="#anesthesiaPlanningModal"
                                                    data-store-url="{{ route('hospital.documents.form.store', ['code' => '017', 'id_solicitud' => $solicitud->id]) }}"
                                                    data-entry-id="{{ $form017Draft ? $form017Draft->id : '' }}"
                                                    data-draft="{{ $form017PayloadEncoded }}"
                                                    data-diagram-url-template="{{ $form017Draft ? route('hospital.documents.form017.diagram', ['id_solicitud' => $solicitud->id, 'entry' => $form017Draft->id, 'position' => '__POSITION__']) : '' }}"
                                                    data-cie-url="{{ route('hospital.surgery.catalog', ['type' => 'cie10', 'id_solicitud' => $solicitud->id]) }}"
                                                    data-procedure-url="{{ route('hospital.surgery.catalog', ['type' => 'procedures', 'id_solicitud' => $solicitud->id]) }}"
                                                    data-doctor-url="{{ route('hospital.surgery.catalog', ['type' => 'doctors', 'id_solicitud' => $solicitud->id]) }}"
                                                    data-patient="{{ $pacienteSolicitud ? trim($pacienteSolicitud->apellido1.' '.$pacienteSolicitud->apellido2.' '.$pacienteSolicitud->nombre1.' '.$pacienteSolicitud->nombre2) : $solicitud->id_paciente }}"
                                                    data-room-id="{{ $surgeryTransfer ? $surgeryTransfer->id_sala : '' }}"
                                                    data-room-name="{{ $surgeryTransfer && $surgeryTransfer->sala ? $surgeryTransfer->sala->nombre_sala : 'Quirófano' }}"
                                                    data-observations="{{ $surgeryTransfer ? $surgeryTransfer->observaciones : '' }}"
                                                    data-doctor-id="{{ $displayDoctor ? $displayDoctor->id : '' }}"
                                                    data-doctor-name="{{ $displayDoctor ? trim($displayDoctor->apellido1.' '.$displayDoctor->apellido2.' '.$displayDoctor->nombre1.' '.$displayDoctor->nombre2) : '' }}"
                                                    data-procedure-id="{{ $surgeryAgenda && $surgeryAgenda->id_procedimiento ? $surgeryAgenda->id_procedimiento : '' }}"
                                                    data-procedure-name="{{ $surgeryAgenda && $surgeryAgenda->procedimiento ? (trim((string) $surgeryAgenda->procedimiento->codigo) !== '' ? $surgeryAgenda->procedimiento->codigo : $surgeryAgenda->procedimiento->id).' - '.$surgeryAgenda->procedimiento->nombre : '' }}"
                                                    data-start="{{ $surgeryAgenda && $surgeryAgenda->fechaini ? date('Y-m-d\\TH:i', strtotime($surgeryAgenda->fechaini)) : '' }}"
                                                    data-end="{{ $surgeryAgenda && $surgeryAgenda->fechafin ? date('Y-m-d\\TH:i', strtotime($surgeryAgenda->fechafin)) : '' }}"
                                                    title="Planificar la cirugía y abrir el Formulario 017">
                                                    <svg class="surgical-scalpel-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                                        <path d="M20.9 2.1a3 3 0 0 0-4.2 0l-7.4 7.4 4.2 4.2 7.4-7.4a3 3 0 0 0 0-4.2ZM8.2 10.6 2 16.8V22h5.2l6.2-6.2-5.2-5.2Zm-3.9 7.1 3.9-3.9 2 2-3.9 3.9h-2v-2Z"/>
                                                    </svg>
                                                    Protocolo quirúrgico
                                                </button>
                                                <a href="{{ route('hospital.documents.index', ['id_solicitud' => $solicitud->id]) }}"
                                                    class="btn btn-success btn-sm" title="Abrir los documentos del episodio">
                                                    <i class="fa fa-heartbeat" aria-hidden="true"></i> Preanestésico
                                                </a>
                                            @endif
                                            @if((int) $solicitud->estado_paso === 5 && isset($surgeryState) && $surgeryState === 'RECUPERACION COMPLETADA')
                                                <a href="{{ route('hospital.traspaso_modulo', ['id_solicitud' => $solicitud->id]) }}" class="btn btn-warning btn-sm"><i class="fa fa-bed"></i> Trasladar a destino</a>
                                            @endif
                                            @if(in_array((int) $solicitud->estado_paso, [3, 6, 7], true))
                                                <a href="{{route('epicrisis.mostrar_final', ['id_solicitud' => $solicitud->id])}}" class="btn btn-success btn-sm"><i class="fa fa-sign-out"></i> Alta y salida</a>
                                            @endif
                                            @endif
                                            @if (!is_null($paso->ruta))
                                                @if($id_paso == 2)
                                                    @if(!is_null($triaje))
                                                        <a href="{{ route('hospital.resultadomanchester', $triaje->id) }}" class="btn btn-sm btn-success">
                                                            <i class="far fa-file-alt" aria-hidden="true"></i>{{ trans('emergencia.DatosTriaje') }}
                                                        </a>
                                                    @endif
                                                    @if($formulariosSolicitud['008'])
                                                        <a href="{{ route('hospital.nuevo.formulario008_pdf', ['id' => $solicitud->id]) }}" class="btn btn-info btn-xs js-pdf-preview" data-pdf-title="Formulario 008">
                                                            <i class="fa fa-eye" aria-hidden="true"></i>{{ trans('emergencia.Formulario008') }}
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('hospital.emergencia', ['solicitud' => $solicitud->id, 'accion' => 'destino']) }}" class="btn btn-warning btn-xs" title="Definir ubicación, destino asistencial o solicitar una cama">
                                                        <i class="fa fa-map-marker" aria-hidden="true"></i>Ubicar / destino
                                                    </a>
                                                @endif

                                                @if ($id_paso == 4)
                                                    <a href="{{ route($paso->ruta, ['tipo' => 0, 'id_solicitud' => $solicitud->id]) }}" class="btn btn-info">
                                                        <i class="fa fa-file" aria-hidden="true"></i>{{ trans('hospitalizacion.VerDetalle') }}
                                                    </a>
                                                @elseif($id_paso == 5)
                                                    <a href="{{ route($paso->ruta, ['tipo' => 1, 'id_solicitud' => $solicitud->id]) }}" class="btn btn-info">
                                                        <i class="fa fa-file" aria-hidden="true"></i>{{ trans('hospitalizacion.VerDetalle') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route($paso->ruta, ['id_solicitud' => $solicitud->id]) }}" class="btn btn-info">
                                                        <i class="fa fa-file" aria-hidden="true"></i>{{ trans('hospitalizacion.VerDetalle') }}
                                                    </a>
                                                    @if(in_array((int) $id_paso, [3, 6, 7], true))
                                                        @if($formulariosSolicitud['005'])
                                                            <a href="{{ route('habitacion.formulario005_pdf', ['id' => $solicitud->id]) }}" class="btn btn-info btn-xs js-pdf-preview" data-pdf-title="Formulario 005">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>{{ trans('emergencia.Formualrio005') }}
                                                            </a>
                                                        @endif
                                                        @if($formulariosSolicitud['008'])
                                                            <a href="{{ route('hospital.nuevo.formulario008_pdf', ['id' => $solicitud->id]) }}" class="btn btn-info btn-xs js-pdf-preview" data-pdf-title="Formulario 008">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>{{ trans('emergencia.Formulario008') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif

                                            @if((int) $id_paso !== 5)
                                                @if($formulariosSolicitud['001'])
                                                    <a href="{{ route('formulario.001', ['id' => $solicitud->id]) }}" class="btn btn-info btn-xs js-pdf-preview" data-pdf-title="Formulario 001">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>{{ trans('emergencia.formulario') }} 001
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-info btn-xs" onclick="cargar_planilla('{{ $solicitud->id }}')">
                                                    <i class="fa fa-calculator" aria-hidden="true"></i>{{ trans('hospitalizacion.planillar') }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                @if($scheduledSurgeryCount === 0)
                                    <tr>
                                        <td colspan="{{ $id_paso == 2 || $id_paso == 5 ? 7 : 6 }}" class="module-empty-state">
                                            <span class="module-empty-icon"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
                                            <strong>No se encontraron pacientes</strong>
                                            <span class="module-empty-copy">Prueba con otro periodo o modifica los criterios de búsqueda.</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                    <div class="module-pagination">
                        {{ $solicitudes->appends(Request::except((int) $id_paso === 5 ? ['page', 'agendar', 'doctor'] : ['page']))->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>

    @if(!empty($surgeryBooking))
        <style>
            #surgeryBookingModal .modal-dialog { width: calc(100% - 24px); max-width: 1120px; margin: 24px auto; }
            #surgeryBookingModal .modal-content { overflow: hidden; border: 0; border-radius: 14px; }
            #surgeryBookingModal .modal-header { background: linear-gradient(120deg, #104968, #087e92); color: #fff; }
            #surgeryBookingModal .modal-title, #surgeryBookingModal .close { color: inherit; }
            #surgeryBookingModal .modal-body { max-height: calc(100vh - 108px); overflow: auto; padding: 14px; }
            #surgeryBookingModal .surgery-booking { margin: 0; padding: 0; }
            #surgeryBookingModal .surgery-booking__selector { border-top: 1px solid var(--booking-border); border-radius: 10px; }
            @media (max-width: 575px) {
                #surgeryBookingModal .modal-dialog { margin: 8px auto; }
                #surgeryBookingModal .modal-body { max-height: calc(100vh - 76px); padding: 8px; }
            }
        </style>
        <div class="modal fade" id="surgeryBookingModal" tabindex="-1" role="dialog" aria-labelledby="surgeryBookingTitle" aria-hidden="true" data-select2-dropdown-root>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title h5" id="surgeryBookingTitle"><i class="fa fa-calendar-plus-o mr-1" aria-hidden="true"></i> Agendar cirugía</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @include('hospital._cirugia-agenda', $surgeryBooking + [
                            'bookingModal' => true,
                            'bookingFilters' => request()->only('cedula', 'paciente', 'fecha_desde', 'fecha_hasta'),
                        ])
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery(function ($) {
                $('#surgeryBookingModal').on('hidden.bs.modal', function () {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('agendar');
                    url.searchParams.delete('doctor');
                    window.history.replaceState(window.history.state, '', url);
                    document.getElementById('openSurgeryBooking').focus();
                }).modal('show');
            });
        </script>
    @endif

    @if((int) $id_paso === 5)
    <div class="modal fade" id="anesthesiaPlanningModal" tabindex="-1" role="dialog" aria-labelledby="anesthesiaPlanningTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="anesthesiaPlanningTitle">
                        <svg class="surgical-scalpel-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.9 2.1a3 3 0 0 0-4.2 0l-7.4 7.4 4.2 4.2 7.4-7.4a3 3 0 0 0 0-4.2ZM8.2 10.6 2 16.8V22h5.2l6.2-6.2-5.2-5.2Zm-3.9 7.1 3.9-3.9 2 2-3.9 3.9h-2v-2Z"/></svg>
                        Protocolo quirúrgico · Formulario 017
                    </h4>
                    <button type="button" class="close anesthesia-modal-close" data-dismiss="modal" aria-label="Cerrar" title="Cerrar"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <div class="modal-body">
                    <div id="anesthesiaPlanningError" class="alert alert-danger" role="alert" hidden></div>
                    <div id="anesthesiaPlanningContent">
                        <div class="alert alert-info anesthesia-patient-summary">
                            <strong id="anesthesiaPlanningPatient"></strong>
                            <span id="anesthesiaPlanningRoom" class="pull-right"></span>
                            <div id="anesthesiaPlanningObservations" class="small"></div>
                        </div>
                        <form method="POST" id="anesthesiaPlanningForm" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="entry_id" id="anesthesiaEntryId">
                            <input type="hidden" name="preserve_missing" value="1">
                            <input type="hidden" name="continue_to" id="anesthesiaContinueTo" value="surgery">
                            <input type="hidden" name="prepare_surgery" value="1">
                            <input type="hidden" name="surgery[room_id]" id="anesthesiaRoomId">
                            <input type="hidden" name="data[diagnosticos_preoperatorios]" id="anesthesiaDiagnosisText">
                            <input type="hidden" name="data[cie_preoperatorio]" id="anesthesiaDiagnosisCodes">
                            <input type="hidden" name="data[procedimiento]" id="anesthesiaProcedureText">
                            <input type="hidden" name="data[_procedimiento_proyectado_codigo]" id="anesthesiaProcedureCode">
                            <input type="hidden" name="data[diagnosticos_postoperatorios]" id="anesthesiaPostDiagnosisText">
                            <input type="hidden" name="data[cie_postoperatorio]" id="anesthesiaPostDiagnosisCodes">
                            <input type="hidden" name="data[indicacion]" id="anesthesiaPerformedProcedureText">
                            <input type="hidden" name="data[_procedimiento_realizado_codigos]" id="anesthesiaPerformedProcedureCodes">
                            <input type="hidden" name="data[cirujano_1]" id="anesthesiaDoctorName">
                            <input type="hidden" name="data[cirujano_2]" id="anesthesiaSurgeon2Name">
                            <input type="hidden" name="data[primer_ayudante]" id="anesthesiaAssistant1Name">
                            <input type="hidden" name="data[segundo_ayudante]" id="anesthesiaAssistant2Name">
                            <input type="hidden" name="data[tercer_ayudante]" id="anesthesiaAssistant3Name">
                            <input type="hidden" name="data[anestesiologo]" id="anesthesiaAnesthesiologistName">
                            <input type="hidden" name="data[instrumentista]" id="anesthesiaInstrumentistName">
                            <input type="hidden" name="data[circulante]" id="anesthesiaCirculatingName">
                            <input type="hidden" name="data[ayudante_anestesia]" id="anesthesiaAnesthesiaAssistantName">

                            <div class="anesthesia-core-card">
                                    <div class="anesthesia-modal-section">Diagnóstico y planificación quirúrgica</div>
                                    <div class="anesthesia-modal-note">Cabecera principal del protocolo: paciente, sala, diagnóstico, procedimiento, horario, cirujano y anestesia.</div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="anesthesiaDiagnosis">Diagnóstico preoperatorio (CIE-10) *</label>
                                            <select id="anesthesiaDiagnosis" class="form-control" name="diagnoses[pre][]" multiple required></select>
                                            <span class="help-block">Busque por código o descripción CIE-10.</span>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="anesthesiaCatalogProcedure">Procedimiento quirúrgico proyectado *</label>
                                            <select id="anesthesiaCatalogProcedure" class="form-control" name="surgery[procedure_codes][]" multiple required></select>
                                            <input type="hidden" id="anesthesiaClinicalProcedureId" name="surgery[procedure_id]">
                                            <span class="help-block">Busque por código o descripción en el catálogo de procedimientos.</span>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="anesthesiaProcedureType">Tipo de procedimiento *</label>
                                            <select id="anesthesiaProcedureType" class="form-control" name="data[tipo_procedimiento]" data-draft-key="tipo_procedimiento" data-select2-off required>
                                                <option value="">Seleccione...</option><option value="ELECTIVA">Electiva</option><option value="EMERGENCIA">Emergencia</option><option value="URGENCIA">Urgencia</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4"><label for="anesthesiaStart">Inicio previsto *</label><input id="anesthesiaStart" class="form-control" type="datetime-local" name="surgery[start]" required></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaEnd">Fin previsto *</label><input id="anesthesiaEnd" class="form-control" type="datetime-local" name="surgery[end]" required></div>
                                        <div class="form-group col-md-6">
                                            <label for="anesthesiaDoctor">Cirujano principal *</label>
                                            <select id="anesthesiaDoctor" class="form-control" name="surgery[doctor_id]" required></select>
                                            <span class="help-block">Busque por apellidos o nombres.</span>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="anesthesiaType">Tipo de anestesia *</label>
                                            <select id="anesthesiaType" class="form-control" name="data[tipo_anestesia]" data-draft-key="tipo_anestesia" data-select2-off required>
                                                <option value="">Seleccione...</option><option value="GENERAL">General</option><option value="REGIONAL">Regional</option><option value="SEDACIÓN">Sedación</option><option value="OTROS">Otros</option>
                                            </select>
                                        </div>
                                    </div>
                            </div>

                            <ul class="nav nav-tabs anesthesia-tabs" role="tablist" aria-label="Secciones del Formulario 017">
                                <li role="presentation" class="active"><a href="#anesthesiaTabTeam" aria-controls="anesthesiaTabTeam" role="tab" data-toggle="tab">1. Información General</a></li>
                                <li role="presentation"><a href="#anesthesiaTabAfter" aria-controls="anesthesiaTabAfter" role="tab" data-toggle="tab">2. Desarrollo quirúrgicos</a></li>
                            </ul>

                            <div class="tab-content">

                                <div role="tabpanel" class="tab-pane active" id="anesthesiaTabTeam">
                                    <div class="anesthesia-modal-section">Integrantes del equipo quirúrgico</div>
                                    <div class="anesthesia-modal-note">Todos los profesionales se consultan según lo que escriba; el catálogo completo no se carga de golpe.</div>
                                    <div class="row">
                                        <div class="form-group col-md-6"><label for="anesthesiaSurgeon2">Cirujano 2</label><select id="anesthesiaSurgeon2" class="form-control anesthesia-professional" data-name-target="#anesthesiaSurgeon2Name"></select></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaAnesthesiologist">Anestesiólogo/a</label><select id="anesthesiaAnesthesiologist" class="form-control anesthesia-professional" data-name-target="#anesthesiaAnesthesiologistName"></select></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaAssistant1">Primer ayudante</label><select id="anesthesiaAssistant1" class="form-control anesthesia-professional" data-name-target="#anesthesiaAssistant1Name"></select></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaAssistant2">Segundo ayudante</label><select id="anesthesiaAssistant2" class="form-control anesthesia-professional" data-name-target="#anesthesiaAssistant2Name"></select></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaAssistant3">Tercer ayudante</label><select id="anesthesiaAssistant3" class="form-control anesthesia-professional" data-name-target="#anesthesiaAssistant3Name"></select></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaInstrumentist">Instrumentista</label><select id="anesthesiaInstrumentist" class="form-control anesthesia-professional" data-name-target="#anesthesiaInstrumentistName"></select></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaCirculating">Circulante</label><select id="anesthesiaCirculating" class="form-control anesthesia-professional" data-name-target="#anesthesiaCirculatingName"></select></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaAnesthesiaAssistant">Ayudante de anestesia</label><select id="anesthesiaAnesthesiaAssistant" class="form-control anesthesia-professional" data-name-target="#anesthesiaAnesthesiaAssistantName"></select></div>
                                        <div class="form-group col-md-12"><label for="anesthesiaOtherTeam">Otros integrantes</label><input id="anesthesiaOtherTeam" class="form-control" name="data[otros_equipo]" data-draft-key="otros_equipo" maxlength="500"></div>
                                    </div>
                                    <div class="anesthesia-tab-actions"><button type="button" class="btn btn-info" data-next-anesthesia-tab="#anesthesiaTabAfter">Continuar a datos posteriores <i class="fa fa-arrow-right"></i></button></div>
                                </div>

                                <div role="tabpanel" class="tab-pane" id="anesthesiaTabAfter">
                                    <div class="anesthesia-modal-section">Datos posteriores y descripción del procedimiento</div>
                                    <div class="row">
                                        <div class="form-group col-md-6"><label for="anesthesiaPostDiagnosis">Diagnósticos postoperatorios (CIE-10)</label><select id="anesthesiaPostDiagnosis" class="form-control" name="diagnoses[post][]" multiple></select><span class="help-block">Puede seleccionar uno o varios diagnósticos.</span></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaPerformedProcedure">Procedimientos realizados</label><select id="anesthesiaPerformedProcedure" class="form-control" name="surgery[performed_procedure_codes][]" multiple></select><span class="help-block">Puede seleccionar uno o varios procedimientos.</span></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaOperationDate">Fecha de operación</label><input id="anesthesiaOperationDate" class="form-control" type="date" name="data[fecha_operacion]" data-draft-key="fecha_operacion"></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaActualStart">Hora de inicio</label><input id="anesthesiaActualStart" class="form-control" type="time" name="data[hora_inicio]" data-draft-key="hora_inicio"></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaActualEnd">Hora de terminación</label><input id="anesthesiaActualEnd" class="form-control" type="time" name="data[hora_terminacion]" data-draft-key="hora_terminacion"></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaDieresis">Diéresis</label><textarea id="anesthesiaDieresis" class="form-control" name="data[dieresis]" data-draft-key="dieresis" maxlength="10000"></textarea></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaExposure">Exposición y exploración</label><textarea id="anesthesiaExposure" class="form-control" name="data[exposicion_exploracion]" data-draft-key="exposicion_exploracion" maxlength="10000"></textarea></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaFindings">Hallazgos quirúrgicos</label><textarea id="anesthesiaFindings" class="form-control" name="data[hallazgos]" data-draft-key="hallazgos" maxlength="10000"></textarea></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaTechnique">Técnica / descripción del procedimiento</label><textarea id="anesthesiaTechnique" class="form-control" name="data[tecnica]" data-draft-key="tecnica" maxlength="10000"></textarea></div>
                                    </div>
                                    <div class="anesthesia-modal-section">Complicaciones, sangrado y material protésico</div>
                                    <div class="row">
                                        <div class="form-group col-md-12"><label for="anesthesiaComplications">Complicaciones del procedimiento quirúrgico</label><textarea id="anesthesiaComplications" class="form-control" name="data[complicaciones]" data-draft-key="complicaciones" maxlength="10000"></textarea></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaBloodLoss">Pérdida sanguínea total (ml)</label><input id="anesthesiaBloodLoss" class="form-control" type="number" min="0" step="1" name="data[perdida_sanguinea_total]" data-draft-key="perdida_sanguinea_total"></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaApproxBlood">Sangrado aproximado (ml)</label><input id="anesthesiaApproxBlood" class="form-control" type="number" min="0" step="1" name="data[sangrado_aproximado]" data-draft-key="sangrado_aproximado"></div>
                                        <div class="form-group col-md-4"><label for="anesthesiaProsthetic">Uso de material protésico</label><select id="anesthesiaProsthetic" class="form-control" name="data[material_protesico]" data-draft-key="material_protesico" data-select2-off><option value="">Seleccione...</option><option value="SÍ">Sí</option><option value="NO">No</option></select></div>
                                        <div class="form-group col-md-12"><label for="anesthesiaConclusion">Descripción de complicaciones o material protésico</label><textarea id="anesthesiaConclusion" class="form-control" name="data[conclusion]" data-draft-key="conclusion" maxlength="10000"></textarea></div>
                                    </div>
                                    <div class="anesthesia-modal-section">Exámenes, diagrama y responsable</div>
                                    <div class="row">
                                        <div class="form-group col-md-6"><label for="anesthesiaTranssurgical">Examen transquirúrgico</label><textarea id="anesthesiaTranssurgical" class="form-control" name="data[transquirurgico]" data-draft-key="transquirurgico" maxlength="10000"></textarea></div>
                                        <div class="form-group col-md-3"><label for="anesthesiaFrozenBiopsy">Biopsia por congelación</label><select id="anesthesiaFrozenBiopsy" class="form-control" name="data[biopsia_congelacion]" data-draft-key="biopsia_congelacion" data-select2-off><option value="">Seleccione...</option><option value="SÍ">Sí</option><option value="NO">No</option></select></div>
                                        <div class="form-group col-md-3"><label for="anesthesiaHistopathology">Examen histopatológico</label><select id="anesthesiaHistopathology" class="form-control" name="data[histopatologico]" data-draft-key="histopatologico" data-select2-off><option value="">Seleccione...</option><option value="SÍ">Sí</option><option value="NO">No</option></select></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaBiopsyResult">Resultado de biopsia por congelación</label><textarea id="anesthesiaBiopsyResult" class="form-control" name="data[resultado_biopsia]" data-draft-key="resultado_biopsia" maxlength="10000"></textarea></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaPathologist">Patólogo que reporta</label><input id="anesthesiaPathologist" class="form-control" name="data[patologo_reporta]" data-draft-key="patologo_reporta" maxlength="255"></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaSample">Muestra</label><textarea id="anesthesiaSample" class="form-control" name="data[muestra]" data-draft-key="muestra" maxlength="10000"></textarea></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaDiagram">Descripción del diagrama del procedimiento</label><textarea id="anesthesiaDiagram" class="form-control" name="data[diagrama]" data-draft-key="diagrama" maxlength="10000"></textarea></div>
                                        <div class="form-group col-md-12">
                                            <label for="anesthesiaDiagramImages">Imágenes del diagrama del procedimiento</label>
                                            <div class="anesthesia-diagram-upload">
                                                <input id="anesthesiaDiagramImages" class="form-control" type="file" name="diagram_images[]" accept="image/jpeg,image/png" multiple>
                                                <span class="help-block">Puede conservar o subir hasta 3 imágenes JPG o PNG, con un máximo de 4 MB por imagen.</span>
                                                <div id="anesthesiaDiagramGallery" class="anesthesia-diagram-gallery" aria-live="polite"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6"><label for="anesthesiaResponsible">Profesional responsable</label><input id="anesthesiaResponsible" class="form-control" name="data[profesional_responsable]" data-draft-key="profesional_responsable" maxlength="255"></div>
                                        <div class="form-group col-md-6"><label for="anesthesiaResponsibleSpecialty">Especialidad del profesional</label><input id="anesthesiaResponsibleSpecialty" class="form-control" name="data[especialidad_profesional]" data-draft-key="especialidad_profesional" maxlength="255"></div>
                                    </div>
                                    <div class="anesthesia-signature-note"><i class="fa fa-shield" aria-hidden="true"></i> La firma electrónica o digitalizada y el sello se incorporan desde el perfil del profesional al firmar el Formulario 017. Si no existen, esos espacios quedan en blanco.</div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="anesthesiaPlanningForm" id="anesthesiaPlanningSave" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Guardar Formulario 017</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="modal fade pdf-preview-modal" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pdfPreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="pdfPreviewTitle">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        <span>Vista previa del formulario</span>
                    </h4>
                    <button type="button" class="pdf-preview-close" data-dismiss="modal" aria-label="Cerrar visor PDF" title="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="pdfPreviewBody" aria-busy="true">
                    <div class="pdf-preview-loading" id="pdfPreviewLoading" role="status" aria-live="polite">
                        <div class="pdf-preview-loading-content">
                            <div class="pdf-preview-spinner" aria-hidden="true"></div>
                            <strong>Cargando documento…</strong>
                            <span>Espere un momento mientras preparamos el visor.</span>
                        </div>
                    </div>
                    <iframe id="pdfPreviewFrame" class="pdf-preview-frame" src="about:blank" title="Vista previa del PDF" aria-hidden="true"></iframe>
                </div>
                <div class="modal-footer">
                    <a id="pdfPreviewDownload" class="btn module-primary-btn" href="#" download>
                        <i class="fa fa-download" aria-hidden="true"></i>
                        Descargar PDF
                    </a>
                    <button type="button" class="btn module-secondary-btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        @if((int) $id_paso === 5)
        window.addEventListener('load', function () {
            var $ = window.jQuery;
            if (!$) return;
            var modal = $('#anesthesiaPlanningModal');
            var diagnosis = $('#anesthesiaDiagnosis');
            var postDiagnosis = $('#anesthesiaPostDiagnosis');
            var catalogProcedure = $('#anesthesiaCatalogProcedure');
            var performedProcedure = $('#anesthesiaPerformedProcedure');
            var doctor = $('#anesthesiaDoctor');
            var professionals = modal.find('.anesthesia-professional');

            $(document).on('click', '.js-anesthesia-planner', function (event) {
                if (!modal.data('continue-to')) modal.data('continue-to', 'surgery');
            });

            function selectedText(element) {
                var selected = element.find('option:selected');
                return selected.length ? $.trim(selected.text()) : '';
            }

            function selectedValues(element) {
                var value = element.val();
                if (value === null || value === '') return [];
                return Array.isArray(value) ? value.map(String) : [String(value)];
            }

            function selectedTexts(element) {
                return element.find('option:selected').map(function () {
                    return $.trim($(this).text());
                }).get().filter(Boolean);
            }

            function savedList(value, separator) {
                if (Array.isArray(value)) return value.map(String).filter(Boolean);
                value = $.trim(String(value || ''));
                if (!value) return [];
                if (value.charAt(0) === '[') {
                    try { return JSON.parse(value).map(String).filter(Boolean); } catch (error) {}
                }
                return value.split(separator || /\s*\/\s*/).map(function (item) {
                    return $.trim(item);
                }).filter(Boolean);
            }

            function appendSavedOptions(element, values, labels) {
                values.forEach(function (value, index) {
                    appendSelectedOption(element, value, labels[index] || value);
                });
            }

            function dateTimeParts(value) {
                value = String(value || '');
                return value.indexOf('T') > -1
                    ? {date: value.substring(0, 10), time: value.substring(11, 16)}
                    : {date: '', time: ''};
            }

            function syncPlannedTimeToForm(source, includeDate) {
                var previous = dateTimeParts(source.data('previous-value'));
                var current = dateTimeParts(source.val());
                var operationDate = $('#anesthesiaOperationDate');
                var actualTime = includeDate ? $('#anesthesiaActualStart') : $('#anesthesiaActualEnd');
                var actualWasLinked = !actualTime.val() || actualTime.val() === previous.time;
                if (includeDate && (!operationDate.val() || operationDate.val() === previous.date)) {
                    operationDate.val(current.date);
                }
                if (actualWasLinked) actualTime.val(current.time);
                source.data('previous-value', source.val());
            }

            function renderDiagramGallery(draft, trigger) {
                var gallery = $('#anesthesiaDiagramGallery').empty();
                var images = Array.isArray(draft._diagram_images) ? draft._diagram_images : [];
                var template = trigger.attr('data-diagram-url-template') || '';
                images.slice(0, 3).forEach(function (image, position) {
                    var card = $('<div class="anesthesia-diagram-card"></div>');
                    if (template) $('<img alt="Imagen guardada del procedimiento">')
                        .attr('src', template.replace('__POSITION__', String(position))).appendTo(card);
                    $('<span></span>').text(image.name || ('IMAGEN ' + (position + 1))).appendTo(card);
                    var remove = $('<label class="anesthesia-diagram-remove"></label>');
                    $('<input type="checkbox" name="diagram_remove[]">').val(position).appendTo(remove);
                    remove.append(document.createTextNode(' Quitar al guardar')).appendTo(card);
                    gallery.append(card);
                });
            }

            function previewNewDiagramImages(input) {
                var files = Array.prototype.slice.call(input.files || []);
                var existing = $('#anesthesiaDiagramGallery .anesthesia-diagram-card').filter(function () {
                    var remove = $(this).find('input[name="diagram_remove[]"]');
                    return !remove.length || !remove.prop('checked');
                }).length;
                if (files.length + existing > 3) {
                    input.value = '';
                    $('#anesthesiaPlanningError').text('El Formulario 017 admite un máximo de 3 imágenes. Quite una imagen guardada o seleccione menos archivos.').prop('hidden', false);
                    return;
                }
                files.forEach(function (file) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        var card = $('<div class="anesthesia-diagram-card anesthesia-diagram-new"></div>');
                        $('<img alt="Nueva imagen del procedimiento">').attr('src', event.target.result).appendTo(card);
                        $('<span></span>').text(file.name).appendTo(card);
                        $('#anesthesiaDiagramGallery').append(card);
                    };
                    reader.readAsDataURL(file);
                });
            }

            function decodeDraft(encoded) {
                if (!encoded) return {};
                try {
                    var binary = window.atob(encoded);
                    var escaped = '';
                    for (var index = 0; index < binary.length; index++) {
                        escaped += '%' + ('00' + binary.charCodeAt(index).toString(16)).slice(-2);
                    }
                    return JSON.parse(decodeURIComponent(escaped)) || {};
                } catch (error) {
                    return {};
                }
            }

            function appendSelectedOption(element, value, text) {
                value = $.trim(String(value || ''));
                text = $.trim(String(text || value));
                if (!value && !text) return;
                element.append(new Option(text, value || text, true, true));
            }

            function syncSelectedValues() {
                var preDiagnosisCodes = selectedValues(diagnosis);
                var projectedCodes = selectedValues(catalogProcedure);
                var postDiagnosisCodes = selectedValues(postDiagnosis);
                var performedCodes = selectedValues(performedProcedure);
                $('#anesthesiaDiagnosisCodes').val(preDiagnosisCodes.join(' / '));
                $('#anesthesiaDiagnosisText').val(selectedTexts(diagnosis).join('\n'));
                $('#anesthesiaProcedureText').val(selectedTexts(catalogProcedure).join('\n'));
                $('#anesthesiaProcedureCode').val(JSON.stringify(projectedCodes));
                $('#anesthesiaPostDiagnosisCodes').val(postDiagnosisCodes.join(' / '));
                $('#anesthesiaPostDiagnosisText').val(selectedTexts(postDiagnosis).join('\n'));
                $('#anesthesiaPerformedProcedureText').val(selectedTexts(performedProcedure).join('\n'));
                $('#anesthesiaPerformedProcedureCodes').val(JSON.stringify(performedCodes));
                $('#anesthesiaDoctorName').val(selectedText(doctor));
                professionals.each(function () {
                    var professional = $(this);
                    $(professional.attr('data-name-target')).val(selectedText(professional));
                });
            }

            function remoteSelect(element, url, multiple) {
                if (!$.fn.select2) return;
                if (element.hasClass('select2-hidden-accessible')) element.select2('destroy');
                element.select2({
                    width: '100%',
                    multiple: multiple,
                    dropdownParent: modal,
                    minimumInputLength: 2,
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 300,
                        data: function (params) { return {q: params.term || ''}; },
                        processResults: function (data) { return {results: data && data.results ? data.results : []}; },
                        cache: true
                    },
                    language: {
                        inputTooShort: function () { return 'Escriba al menos 2 caracteres'; },
                        errorLoading: function () { return 'No se pudo consultar el catálogo'; },
                        noResults: function () { return 'No se encontraron coincidencias'; },
                        searching: function () { return 'Buscando…'; }
                    }
                });
            }

            modal.on('show.bs.modal', function (event) {
                var trigger = $(event.relatedTarget);
                modal.data('trigger', trigger);
                $('#anesthesiaPlanningError').prop('hidden', true).text('');
                $('#anesthesiaPlanningForm').attr('action', trigger.attr('data-store-url'));
                $('#anesthesiaPlanningPatient').text(trigger.attr('data-patient') || 'Paciente');
                $('#anesthesiaPlanningRoom').text(trigger.attr('data-room-name') || 'Quirófano');
                $('#anesthesiaPlanningObservations').text(trigger.attr('data-observations') || '');
                $('#anesthesiaEntryId').val(trigger.attr('data-entry-id') || '');
                var continueTo = modal.data('continue-to') || 'surgery';
                $('#anesthesiaContinueTo').val(continueTo);
                $('#anesthesiaPlanningSave').html(continueTo === 'preoperative'
                    ? '<i class="fa fa-heartbeat" aria-hidden="true"></i> Guardar y abrir Preanestésico'
                    : '<i class="fa fa-check" aria-hidden="true"></i> Guardar Formulario 017');
                $('#anesthesiaRoomId').val(trigger.attr('data-room-id') || '');
                $('#anesthesiaClinicalProcedureId').val(trigger.attr('data-procedure-id') || '');
                var draft = decodeDraft(trigger.attr('data-draft'));
                $('#anesthesiaDiagramImages').val('');
                renderDiagramGallery(draft, trigger);
                modal.find('.anesthesia-tabs a[href="#anesthesiaTabTeam"]').trigger('click');
                modal.find('[data-draft-key]').each(function () {
                    var field = $(this);
                    field.val(draft[field.attr('data-draft-key')] || '');
                });
                var operationDate = draft.fecha_operacion || '';
                var savedStart = draft._inicio_previsto || (operationDate && draft.hora_inicio
                    ? operationDate + 'T' + String(draft.hora_inicio).substring(0, 5) : '');
                var savedEnd = draft._fin_previsto || (operationDate && draft.hora_terminacion
                    ? operationDate + 'T' + String(draft.hora_terminacion).substring(0, 5) : '');
                var plannedStart = savedStart || trigger.attr('data-start') || '';
                var plannedEnd = savedEnd || trigger.attr('data-end') || '';
                $('#anesthesiaStart').val(plannedStart).data('previous-value', plannedStart);
                $('#anesthesiaEnd').val(plannedEnd).data('previous-value', plannedEnd);
                if (!operationDate && plannedStart) $('#anesthesiaOperationDate').val(dateTimeParts(plannedStart).date);
                if (!draft.hora_inicio && plannedStart) $('#anesthesiaActualStart').val(dateTimeParts(plannedStart).time);
                if (!draft.hora_terminacion && plannedEnd) $('#anesthesiaActualEnd').val(dateTimeParts(plannedEnd).time);
                $('#anesthesiaDiagnosisText, #anesthesiaProcedureText, #anesthesiaPostDiagnosisText, #anesthesiaPerformedProcedureText, #anesthesiaDoctorName, #anesthesiaSurgeon2Name, #anesthesiaAssistant1Name, #anesthesiaAssistant2Name, #anesthesiaAssistant3Name, #anesthesiaAnesthesiologistName, #anesthesiaInstrumentistName, #anesthesiaCirculatingName, #anesthesiaAnesthesiaAssistantName').val('');
                diagnosis.empty();
                postDiagnosis.empty();
                catalogProcedure.empty();
                performedProcedure.empty();
                doctor.empty();
                professionals.empty();
                appendSavedOptions(diagnosis,
                    savedList(draft.cie_preoperatorio),
                    savedList(draft.diagnosticos_preoperatorios, /\r?\n/));
                appendSavedOptions(postDiagnosis,
                    savedList(draft.cie_postoperatorio),
                    savedList(draft.diagnosticos_postoperatorios, /\r?\n/));
                appendSavedOptions(catalogProcedure,
                    savedList(draft._procedimiento_proyectado_codigo),
                    savedList(draft.procedimiento, /\r?\n/));
                appendSavedOptions(performedProcedure,
                    savedList(draft._procedimiento_realizado_codigos),
                    savedList(draft.indicacion, /\r?\n/));
                if (trigger.attr('data-doctor-id')) {
                    doctor.append(new Option(trigger.attr('data-doctor-name'), trigger.attr('data-doctor-id'), true, true));
                }
                var savedProfessionals = {
                    '#anesthesiaSurgeon2': draft.cirujano_2,
                    '#anesthesiaAssistant1': draft.primer_ayudante,
                    '#anesthesiaAssistant2': draft.segundo_ayudante,
                    '#anesthesiaAssistant3': draft.tercer_ayudante,
                    '#anesthesiaAnesthesiologist': draft.anestesiologo,
                    '#anesthesiaInstrumentist': draft.instrumentista,
                    '#anesthesiaCirculating': draft.circulante,
                    '#anesthesiaAnesthesiaAssistant': draft.ayudante_anestesia
                };
                $.each(savedProfessionals, function (selector, name) {
                    if (name) appendSelectedOption($(selector), 'saved:' + name, name);
                });
                $('#anesthesiaPlanningSave').prop('disabled', false);
                syncSelectedValues();
                try {
                    var cieUrl = trigger.attr('data-cie-url');
                    var procedureUrl = trigger.attr('data-procedure-url');
                    var doctorUrl = trigger.attr('data-doctor-url');
                    if (!cieUrl || !procedureUrl || !doctorUrl) throw new Error('Faltan las rutas de los catálogos.');
                    remoteSelect(diagnosis, cieUrl, true);
                    remoteSelect(postDiagnosis, cieUrl, true);
                    remoteSelect(catalogProcedure, procedureUrl, true);
                    remoteSelect(performedProcedure, procedureUrl, true);
                    remoteSelect(doctor, doctorUrl, false);
                    professionals.each(function () { remoteSelect($(this), doctorUrl, false); });
                } catch (error) {
                    $('#anesthesiaPlanningError').text('No fue posible preparar los buscadores. Cierre el modal e inténtelo nuevamente.').prop('hidden', false);
                }
            });

            diagnosis.add(postDiagnosis).on('change select2:select', syncSelectedValues);
            catalogProcedure.add(performedProcedure).on('change select2:select', syncSelectedValues);
            doctor.on('change select2:select', syncSelectedValues);
            professionals.on('change select2:select', syncSelectedValues);
            $('#anesthesiaDiagramImages').on('change', function () {
                $('#anesthesiaDiagramGallery .anesthesia-diagram-new').remove();
                previewNewDiagramImages(this);
            });
            $('#anesthesiaStart').on('change input', function () { syncPlannedTimeToForm($(this), true); });
            $('#anesthesiaEnd').on('change input', function () { syncPlannedTimeToForm($(this), false); });

            modal.on('input', 'input[type="text"], textarea', function () {
                var start = typeof this.selectionStart === 'number' ? this.selectionStart : null;
                var end = typeof this.selectionEnd === 'number' ? this.selectionEnd : null;
                this.value = String(this.value || '').toLocaleUpperCase('es-EC');
                if (start !== null && typeof this.setSelectionRange === 'function') {
                    this.setSelectionRange(start, end);
                }
            });

            modal.on('click', '.anesthesia-tabs a[data-toggle="tab"]', function (event) {
                event.preventDefault();
                var tab = $(this);
                var target = tab.attr('href');
                modal.find('.anesthesia-tabs > li').removeClass('active');
                tab.parent().addClass('active');
                modal.find('.tab-content > .tab-pane').removeClass('active in');
                modal.find(target).addClass('active in');
            });

            modal.on('click', '[data-next-anesthesia-tab]', function () {
                var target = $(this).attr('data-next-anesthesia-tab');
                modal.find('.anesthesia-tabs a[href="' + target + '"]').trigger('click');
                modal.find('.modal-body').scrollTop(0);
            });

            $('#anesthesiaPlanningForm').on('submit', function (event) {
                syncSelectedValues();
                if (!$('#anesthesiaDiagnosisText').val() || !catalogProcedure.val() || !$('#anesthesiaDoctorName').val()) {
                    event.preventDefault();
                    $('#anesthesiaPlanningError').text('Seleccione el CIE-10, el procedimiento quirúrgico y el cirujano principal.').prop('hidden', false);
                    modal.find('.anesthesia-tabs a[href="#anesthesiaTabTeam"]').trigger('click');
                    modal.find('.modal-body').scrollTop(0);
                    return;
                }
                $('#anesthesiaPlanningSave').prop('disabled', true);
            });

            modal.on('hidden.bs.modal', function () {
                if (diagnosis.hasClass('select2-hidden-accessible')) diagnosis.select2('destroy');
                if (postDiagnosis.hasClass('select2-hidden-accessible')) postDiagnosis.select2('destroy');
                if (catalogProcedure.hasClass('select2-hidden-accessible')) catalogProcedure.select2('destroy');
                if (performedProcedure.hasClass('select2-hidden-accessible')) performedProcedure.select2('destroy');
                if (doctor.hasClass('select2-hidden-accessible')) doctor.select2('destroy');
                professionals.each(function () {
                    var professional = $(this);
                    if (professional.hasClass('select2-hidden-accessible')) professional.select2('destroy');
                });
                diagnosis.empty();
                postDiagnosis.empty();
                catalogProcedure.empty();
                performedProcedure.empty();
                doctor.empty();
                professionals.empty();
                modal.removeData('continue-to');
            });
        });
        @endif

        var pdfPreviewReadyTimer = null;
        var pdfPreviewSafetyTimer = null;

        function prepararCargaPdf() {
            clearTimeout(pdfPreviewReadyTimer);
            clearTimeout(pdfPreviewSafetyTimer);
            $('#pdfPreviewBody').attr('aria-busy', 'true');
            $('#pdfPreviewLoading').removeClass('is-hidden').attr('aria-hidden', 'false');
            $('#pdfPreviewFrame').removeClass('is-ready').attr('aria-hidden', 'true');
        }

        function mostrarPdfCargado() {
            var frame = $('#pdfPreviewFrame');
            var source = frame.attr('src');
            var modal = $('#pdfPreviewModal');
            var modalAbierto = modal.hasClass('show') || modal.hasClass('in') || modal.is(':visible');
            if (!source || source === 'about:blank' || !modalAbierto) {
                return;
            }

            clearTimeout(pdfPreviewReadyTimer);
            clearTimeout(pdfPreviewSafetyTimer);
            $('#pdfPreviewBody').attr('aria-busy', 'false');
            $('#pdfPreviewLoading').addClass('is-hidden').attr('aria-hidden', 'true');
            frame.addClass('is-ready').attr('aria-hidden', 'false');
        }

        $('#pdfPreviewFrame').on('load', function() {
            var source = $(this).attr('src');
            if (!source || source === 'about:blank') {
                return;
            }

            clearTimeout(pdfPreviewReadyTimer);
            pdfPreviewReadyTimer = setTimeout(mostrarPdfCargado, 180);
        });

        $(document).on('click', '.js-pdf-preview', function(event) {
            event.preventDefault();

            var pdfUrl = $(this).attr('href');
            var pdfTitle = $(this).data('pdf-title') || 'Vista previa del formulario';

            prepararCargaPdf();
            $('#pdfPreviewTitle span').text(pdfTitle);
            $('#pdfPreviewDownload').attr('href', pdfUrl).attr('download', pdfTitle + '.pdf');
            $('#pdfPreviewModal').modal('show');
            $('#pdfPreviewFrame').attr('src', pdfUrl);
            pdfPreviewSafetyTimer = setTimeout(mostrarPdfCargado, 2500);
        });

        $('#pdfPreviewModal').on('hidden.bs.modal', function() {
            clearTimeout(pdfPreviewReadyTimer);
            clearTimeout(pdfPreviewSafetyTimer);
            $('#pdfPreviewFrame').removeClass('is-ready').attr('aria-hidden', 'true').attr('src', 'about:blank');
            $('#pdfPreviewLoading').removeClass('is-hidden').attr('aria-hidden', 'false');
            $('#pdfPreviewBody').attr('aria-busy', 'true');
            $('#pdfPreviewDownload').attr('href', '#').removeAttr('download');
        });

        function buscador() {
            $.ajax({
                url: "{{ route('buscador.ajax_emergencia') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#form_buscar').serialize(),
                type: 'POST',
                dataType: 'html',
                beforeSend: function() {
                    //$('#loading').show();
                },
                success: function(datahtml, data) {
                    //$('#loading').hide();
                    $("#tablaajax").empty().html(datahtml).fadeIn();
                },
                error: function(xhr, status) {
                    alert("{{ trans('hospitalizacion.ErrorTabla') }}");
                },
            });
        }

        function cargar_planilla(id) {
            $.ajax({
                url: "{{ url('planillar_hospital') }}/" + id,
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#form_buscar').serialize(),
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    //$('#loading').show();
                },
                success: function(datahtml, data) {
                    location.href = "{{ url('planillar_hospital/detalle') }}/" + id;
                },
                error: function(xhr, status) {
                    alert("{{ trans('hospitalizacion.ErrorTabla') }}");
                },
            });
        }
    </script>
@endsection
