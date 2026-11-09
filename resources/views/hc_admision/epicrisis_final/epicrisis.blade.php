@extends(request()->boolean('embedded') ? 'layouts.epicrisis-embedded' : 'v3.layouts.view_plantilla')
@if(!request()->boolean('embedded'))
    @section('title', view('v3.layouts.view_title'))
    @section('css', view('v3.layouts.view_css'))
    @section('header', view('v3.layouts.view_header'))
    @section('menu', view('v3.layouts.view_menu'))
    @section('footer', view('v3.layouts.view_footer'))
    @section('variables', view('v3.layouts.view_variables'))
    @section('js', view('v3.layouts.view_js'))
@endif

@section('contenido')


<style type="text/css">
    /* Keep the final epicrisis fields compact without affecting other forms. */
    .epicrisis-final-form {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
    }
    .epicrisis-final-form > .form-group {
        float: none;
        box-sizing: border-box;
        margin-bottom: 14px;
    }
    .epicrisis-final-form > .col-md-12 { width: 100%; }
    .epicrisis-final-form > .col-md-8 { width: 66.666667%; }
    .epicrisis-final-form > .col-md-6 { width: 50%; }
    .epicrisis-final-form > .col-md-4 { width: 33.333333%; }
    .epicrisis-final-form > .col-md-3 { width: 25%; }
    .epicrisis-final-form > .form-group > .col-md-12 {
        width: 100%;
        padding-left: 0;
        padding-right: 0;
    }
    .epicrisis-final-form input,
    .epicrisis-final-form select,
    .epicrisis-final-form textarea { max-width: 100%; }
    @media (max-width: 767px) {
        .epicrisis-final-form > .col-md-8,
        .epicrisis-final-form > .col-md-6,
        .epicrisis-final-form > .col-md-4,
        .epicrisis-final-form > .col-md-3 { width: 100%; }
    }
    .epicrisis-save-bar { display: flex; align-items: center; gap: 12px; margin: 12px 0 4px; }
    .epicrisis-save-bar .btn { min-width: 150px; }

    .mce-branding {
        display: none;
    }

    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 15px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }

    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
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
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }

    .ui-widget-content a {
        color: #222222;
    }
</style>

<style>
    .epicrisis-v3-page{max-width:1540px;margin:0 auto;padding:22px 24px 56px;color:#173b57}
    .epicrisis-v3-page *{box-sizing:border-box}
    .epicrisis-v3-page .box{margin-bottom:18px;border:1px solid #d8e7ef;border-radius:14px;background:#fff;box-shadow:0 8px 24px rgba(22,72,103,.07);overflow:hidden}
    .epicrisis-v3-page .box>.box-header{display:flex;min-height:58px;align-items:center;padding:14px 20px;border:0;background:linear-gradient(105deg,#0b4b75 0%,#117aa0 55%,#20b8c2 100%);color:#fff}
    .epicrisis-v3-page .box>.box-header .box-title{margin:0;color:#fff;font-size:18px;font-weight:800;letter-spacing:-.2px}
    .epicrisis-v3-page .box>.box-header .box-title i{margin-right:8px;color:#bff7fb}
    .epicrisis-v3-page .box>.box-body{padding:20px;background:#fff}
    .epicrisis-v3-page label{color:#284e68;font-weight:700}
    .epicrisis-v3-page .form-control,.epicrisis-v3-page input[type="text"],.epicrisis-v3-page input[type="number"],.epicrisis-v3-page input[type="datetime-local"],.epicrisis-v3-page textarea,.epicrisis-v3-page select{border:1px solid #c9dce9;border-radius:9px;background:#fff;box-shadow:none;transition:border-color .18s ease,box-shadow .18s ease}
    .epicrisis-v3-page .form-control:focus,.epicrisis-v3-page input:focus,.epicrisis-v3-page textarea:focus,.epicrisis-v3-page select:focus{outline:0;border-color:#18aebe;box-shadow:0 0 0 3px rgba(24,174,190,.14)}
    .epicrisis-v3-page textarea{padding:11px 12px;line-height:1.5;resize:vertical}
    .epicrisis-v3-page .btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border-radius:9px;font-weight:800;box-shadow:none}
    .epicrisis-v3-page .label{display:inline-flex;align-items:center;margin:2px;padding:6px 9px;border-radius:999px;font-size:11px}
    .epicrisis-hero{position:relative;display:flex;min-height:128px;align-items:center;justify-content:space-between;gap:24px;margin-bottom:18px;padding:22px 26px;border-radius:16px;overflow:hidden;color:#fff;background:linear-gradient(110deg,#083f69 0%,#087eaa 54%,#18b5b7 100%);box-shadow:0 12px 28px rgba(16,88,119,.18)}
    .epicrisis-hero:after{position:absolute;top:-95px;right:-55px;width:255px;height:255px;border:1px solid rgba(255,255,255,.18);border-radius:50%;content:""}
    .epicrisis-hero__main{position:relative;z-index:1;display:flex;align-items:center;gap:18px;min-width:0}
    .epicrisis-hero__icon{display:flex;width:58px;height:58px;flex:0 0 58px;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.35);border-radius:15px;background:rgba(255,255,255,.13);font-size:26px}
    .epicrisis-hero__eyebrow{margin-bottom:3px;color:#bdf7f2;font-size:11px;font-weight:800;letter-spacing:.9px;text-transform:uppercase}
    .epicrisis-hero h1{margin:0 0 7px;color:#fff;font-size:27px;font-weight:800;line-height:1.1}
    .epicrisis-hero__meta{display:flex;flex-wrap:wrap;gap:8px 18px;color:rgba(255,255,255,.94);font-size:13px}
    .epicrisis-hero__meta span{display:inline-flex;align-items:center;gap:6px}
    .epicrisis-hero__actions{position:relative;z-index:1;display:flex;flex-wrap:wrap;justify-content:flex-end;gap:9px}
    .epicrisis-hero__actions .btn{min-height:42px;padding:10px 15px;border:1px solid rgba(255,255,255,.32);color:#fff;background:rgba(255,255,255,.12)}
    .epicrisis-hero__actions .btn:hover{color:#06496f;background:#fff}
    .epicrisis-hero__actions .btn-pdf{border-color:#ef626b;background:#ef626b}
    .epicrisis-section-intro{margin:0 0 18px;color:#617c90}
    .epicrisis-completeness-summary{display:flex;align-items:center;justify-content:space-between;gap:18px;margin-bottom:18px;padding:14px 16px;border-radius:11px;background:#f0f8fb}
    .epicrisis-completeness-summary strong{display:block;color:#164a68;font-size:15px}
    .epicrisis-completeness-summary small{color:#698397}
    .epicrisis-completeness-summary__value{display:inline-flex;min-width:66px;height:34px;align-items:center;justify-content:center;border-radius:999px;color:#08758d;background:#d9f5f3;font-size:14px;font-weight:900}
    .epicrisis-completeness-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
    .epicrisis-completeness-card{padding:15px;border:1px solid #dceaf0;border-radius:12px;background:#fff}
    .epicrisis-completeness-card__top{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:12px}
    .epicrisis-completeness-card__title{display:flex;align-items:center;gap:9px;color:#174560;font-weight:800}
    .epicrisis-completeness-card__icon{display:inline-flex;width:31px;height:31px;flex:0 0 31px;align-items:center;justify-content:center;border-radius:8px;color:#087e99;background:#e8f7f8}
    .epicrisis-completeness-card__percent{color:#087b91;font-size:13px;font-weight:900}
    .epicrisis-progress{height:7px;margin-bottom:11px;border-radius:999px;background:#e7eff3;overflow:hidden}
    .epicrisis-progress span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#0c8bb3,#20c4a1)}
    .epicrisis-progress.is-pending span{background:linear-gradient(90deg,#f0a62b,#f5ca4b)}
    .epicrisis-completeness-card__detail{margin:0;color:#6a8192;font-size:12px;line-height:1.45}
    .epicrisis-readiness{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:18px;padding:13px 15px;border:1px solid #dceaf0;border-radius:11px;background:#f8fbfc}
    .epicrisis-readiness__checks{display:flex;flex-wrap:wrap;gap:8px}
    .epicrisis-readiness__check{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:8px;color:#a13b43;background:#fff0f1;font-size:12px;font-weight:800}
    .epicrisis-readiness__check.is-ready{color:#087553;background:#e8f8f1}
    .epicrisis-readiness__orders{color:#526e82;font-size:13px}
    .epicrisis-readiness__orders strong{color:#0b5275;font-size:16px}
    .epicrisis-closure-grid{display:grid;grid-template-columns:minmax(210px,1fr) minmax(300px,2fr);gap:14px 16px}
    .epicrisis-closure-grid .form-group{width:auto!important;margin:0!important;padding:0!important}
    .epicrisis-closure-grid .form-group:nth-child(n+3){grid-column:auto}
    .epicrisis-closure-grid .form-control{width:100%}
    .epicrisis-closure-grid textarea{min-height:78px}
    .epicrisis-closure-grid__three{grid-column:1/-1;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
    .epicrisis-clinical-header{display:flex!important;justify-content:space-between;gap:16px}
    .epicrisis-clinical-header__text{display:flex;align-items:center}
    .epicrisis-save-bar{margin:0!important}
    .epicrisis-save-bar .btn{min-height:39px}
    .epicrisis-save-status{display:inline-flex;min-height:30px;align-items:center;padding:5px 9px;border-radius:7px;background:rgba(255,255,255,.14);color:#fff!important;font-size:12px}
    .epicrisis-save-status:empty{display:none}
    .epicrisis-save-status.text-success{background:rgba(23,201,128,.24)}
    .epicrisis-save-status.text-danger{background:rgba(239,89,97,.28)}
    .epicrisis-section-heading{display:flex;align-items:center;gap:9px;margin:8px 0 2px!important;padding:11px 13px!important;border:0!important;border-left:4px solid #16a8ba!important;border-radius:8px;background:#eaf8fa!important;color:#0d5972!important;font-weight:800}
    .epicrisis-section-heading:before{display:inline-flex;width:24px;height:24px;align-items:center;justify-content:center;border-radius:6px;color:#087d99;background:#d4f1f4;content:"\f15c";font-family:FontAwesome;font-weight:normal}
    .epicrisis-section-heading label{margin:0;padding:0!important;color:#0d5972!important}
    .epicrisis-field-label{margin-bottom:-4px!important}
    .epicrisis-field-label label{padding:0!important;font-size:12px;text-transform:uppercase;letter-spacing:.25px}
    .epicrisis-final-form input[type="text"],.epicrisis-final-form input[type="number"],.epicrisis-final-form select{min-height:40px;padding:8px 11px}
    .epicrisis-diagnosis{border-top:1px solid #e2edf2}
    .epicrisis-diagnosis__title{margin-top:4px!important}
    #death-workflow-fields{border:1px solid #f0c4c7!important;border-radius:11px;background:#fff7f7!important}
    #closure-blockers{margin-top:15px}
    .epicrisis-v3-page .alert{border:0;border-radius:10px}
    @media(max-width:1050px){.epicrisis-completeness-grid{grid-template-columns:1fr}.epicrisis-closure-grid{grid-template-columns:1fr}.epicrisis-closure-grid__three{grid-template-columns:1fr 1fr}.epicrisis-hero{align-items:flex-start;flex-direction:column}.epicrisis-hero__actions{justify-content:flex-start}}
    @media(max-width:767px){.epicrisis-v3-page{padding:12px}.epicrisis-hero{padding:18px}.epicrisis-hero h1{font-size:22px}.epicrisis-hero__icon{display:none}.epicrisis-hero__actions{width:100%}.epicrisis-hero__actions .btn{flex:1}.epicrisis-completeness-summary,.epicrisis-readiness,.epicrisis-clinical-header{align-items:flex-start;flex-direction:column}.epicrisis-closure-grid__three{grid-template-columns:1fr}.epicrisis-clinical-header .epicrisis-save-bar{width:100%;flex-wrap:wrap}.epicrisis-clinical-header .btn{flex:1}}
    @if(request()->boolean('embedded'))
    html,body{margin:0!important;padding:0!important;background:#fff}
    .epicrisis-v3-page{width:100%;max-width:none;margin:0;padding:0;background:#fff;font-size:13px}
    .epicrisis-v3-page>.row{margin:0;padding:18px;background:#fff}
    .epicrisis-v3-page>.row>.col-md-12{padding:0}
    .epicrisis-v3-page .box{margin-bottom:16px;border:1px solid #dce8ef;border-radius:12px;box-shadow:none}
    .epicrisis-v3-page .box > .box-header{min-height:54px;padding:12px 16px;border-top:0;background:linear-gradient(105deg,#0c5e91,#159dbb);color:#fff}
    .epicrisis-v3-page .box > .box-header .box-title{color:#fff}
    .epicrisis-v3-page .box-body{padding:17px 18px;background:#fff}
    .epicrisis-v3-page .box-title{font-size:16px!important}
    .epicrisis-v3-page .epicrisis-actions{display:none}
    .epicrisis-module-header{display:flex;width:100%;min-height:64px;margin:0;align-items:center;justify-content:space-between;gap:16px;padding:12px 18px;color:#fff;background:linear-gradient(105deg,#064b7a 0%,#087db5 60%,#1db8c8 100%);box-sizing:border-box}
    .epicrisis-module-header__title{display:flex;align-items:center;gap:12px;min-width:0}
    .epicrisis-module-header__number{display:inline-flex;min-width:48px;height:30px;align-items:center;justify-content:center;padding:0 10px;border:1px solid rgba(255,255,255,.78);border-radius:999px;color:#fff;font-size:12px;font-weight:800}
    .epicrisis-module-header h2{margin:0;color:#fff;font-size:17px;font-weight:800}
    .epicrisis-module-header__actions{display:flex;align-items:center;gap:10px}
    .epicrisis-module-header__actions .btn{display:inline-flex;min-height:40px;align-items:center;justify-content:center;gap:7px;padding:8px 15px;border:0;border-radius:9px;color:#fff;font-weight:800;box-shadow:0 6px 15px rgba(15,61,91,.15)}
    .epicrisis-module-header__save{background:#20c982!important}
    .epicrisis-module-header__pdf{background:#ef5961!important}
    .epicrisis-module-header__status{max-width:160px;color:rgba(255,255,255,.9);font-size:10px}
    .epicrisis-module-header__status.text-success{color:#eafff5!important}
    .epicrisis-module-header__status.text-danger{color:#ffe3e5!important}
    .epicrisis-module-footer{margin-top:0}
    .epicrisis-final-form>.form-group{margin-bottom:12px}
    .epicrisis-final-form textarea{min-height:84px;padding:9px 10px;resize:vertical}
    .epicrisis-final-form>div[style*="background-color"]{padding:10px 12px!important;border-left:4px solid #159dbb;border-radius:6px;background:#e9f7fa!important;color:#0b5370}
    .epicrisis-save-bar{display:none}
    .epicrisis-save-bar .btn{min-width:132px}
    .epicrisis-completeness-row{margin:0 0 10px!important;padding:10px 4px;border:1px solid #e0ebf0;border-radius:9px;background:#f8fbfc}
    #clinical-closure-panel .label{margin-bottom:5px}
    #tdiagnostico{min-width:520px}
    .epicrisis-egress-title{margin-top:8px!important}
    .epicrisis-egress-field{padding:0 7px!important}
    .epicrisis-egress-field>label{display:block;width:100%;margin-bottom:6px;padding:0!important;color:#34495a;font-weight:800}
    .epicrisis-egress-field>div{width:100%;padding:0!important}
    .epicrisis-egress-field input,.epicrisis-egress-field select{width:100%;height:38px;padding:7px 10px;border:1px solid #c9dce9;border-radius:8px;background:#fff}
    .epicrisis-diagnosis{width:100%;padding:0 18px 18px}
    .epicrisis-diagnosis__title{margin:0 0 14px;padding:10px 12px;border-left:4px solid #159dbb;border-radius:6px;background:#e9f7fa;color:#0b5370;font-weight:800}
    .epicrisis-diagnosis__grid{display:grid;grid-template-columns:minmax(220px,2fr) minmax(150px,1fr) minmax(150px,1fr) auto;gap:10px;align-items:end}
    .epicrisis-diagnosis__field{margin:0}
    .epicrisis-diagnosis__field label{display:block;margin-bottom:5px;color:#34495a;font-size:11px;font-weight:800}
    .epicrisis-diagnosis__grid .form-control{height:38px}
    .epicrisis-diagnosis__add{height:38px;padding:7px 14px!important;white-space:nowrap}
    .epicrisis-diagnosis__error{display:none;margin:10px 0 0;color:#c83f49;font-weight:700}
    .epicrisis-diagnosis__table{margin-top:14px;overflow-x:auto;border-top:1px solid #e1edf2}
    .epicrisis-diagnosis__table table{margin:0!important}
    @media(max-width:1050px){.epicrisis-diagnosis__grid{grid-template-columns:1fr 1fr}.epicrisis-diagnosis__field--diagnosis{grid-column:1/-1}}
    @media(max-width:700px){.epicrisis-egress-field{width:100%!important}.epicrisis-diagnosis__grid{grid-template-columns:1fr}.epicrisis-diagnosis__field--diagnosis{grid-column:auto}.epicrisis-diagnosis__add{width:100%}}
    @media(max-width:700px){.epicrisis-module-header{align-items:flex-start;flex-direction:column}.epicrisis-module-header__actions{width:100%;flex-wrap:wrap}.epicrisis-module-header__actions .btn{flex:1}.epicrisis-v3-page>.row{padding:12px}}
    @endif
</style>

<div class="modal fade fullscreen-modal" id="Crea_Actualiza" tabindex="-1" role="dialog" aria-labelledby="Crea_ActualizaLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>

<div class="epicrisis-v3-page container-fluid">
    @if(request()->boolean('embedded'))
        <header class="epicrisis-module-header">
            <div class="epicrisis-module-header__title"><span class="epicrisis-module-header__number">006</span><h2>Epicrisis</h2></div>
            <div class="epicrisis-module-header__actions">
                <span class="epicrisis-module-header__status js-epicrisis-save-status" role="status" aria-live="polite"></span>
                <button type="button" class="btn epicrisis-module-header__save js-save-epicrisis" onclick="guardar(true);"><i class="fa fa-save"></i> Guardar</button>
                <a href="{{ route('epicrisis.pdf_final', ['id_solicitud' => $solicitud->id]) }}" target="_blank" class="btn epicrisis-module-header__pdf"><i class="fa fa-file-pdf-o"></i> PDF F-006</a>
            </div>
        </header>
    @endif
    <div class="row ">
        @if(!request()->boolean('embedded'))
        <div class="col-md-12">
            <section class="epicrisis-hero" aria-labelledby="epicrisis-page-title">
                <div class="epicrisis-hero__main">
                    <div class="epicrisis-hero__icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></div>
                    <div>
                        <div class="epicrisis-hero__eyebrow">Módulo clínico · Formulario 006</div>
                        <h1 id="epicrisis-page-title">Epicrisis y cierre del episodio</h1>
                        <div class="epicrisis-hero__meta">
                            <span><i class="fa fa-user" aria-hidden="true"></i> <strong>{{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif {{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</strong></span>
                            <span><i class="fa fa-id-card-o" aria-hidden="true"></i> {{$agenda->id_paciente}}</span>
                        </div>
                    </div>
                </div>
                <div class="epicrisis-hero__actions">
                    <a href="{{ URL::previous() }}" class="btn"><i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar</a>
                    <a href="{{ route('epicrisis.pdf_final', ['id_solicitud' => $solicitud->id]) }}" target="_blank" rel="noopener" class="btn btn-pdf"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Descargar PDF</a>
                </div>
            </section>
        </div>
        @endif
        <div class="col-md-12">
            @if(count($documentCompleteness))
            <div class="box box-default">
                <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-list-check"></i> Completitud del expediente</h3></div>
                <div class="box-body">
                    @php($overallCompleteness = (int) round(collect($documentCompleteness)->avg('percent')))
                    <div class="epicrisis-completeness-summary">
                        <div>
                            <strong>Estado general de la documentación</strong>
                            <small>Puede guardar borradores incompletos; para firmar, todos los documentos deben alcanzar el 100%.</small>
                        </div>
                        <span class="epicrisis-completeness-summary__value">{{$overallCompleteness}}%</span>
                    </div>
                    <div class="epicrisis-completeness-grid">
                        @foreach($documentCompleteness as $item)
                            <article class="epicrisis-completeness-card">
                                <div class="epicrisis-completeness-card__top">
                                    <div class="epicrisis-completeness-card__title">
                                        <span class="epicrisis-completeness-card__icon"><i class="fa {{$item['status']==='COMPLETE' ? 'fa-check' : 'fa-file-text-o'}}" aria-hidden="true"></i></span>
                                        <span>{{$item['document']->title}}</span>
                                    </div>
                                    <span class="epicrisis-completeness-card__percent">{{$item['percent']}}%</span>
                                </div>
                                <div class="epicrisis-progress {{$item['status']==='COMPLETE' ? '' : 'is-pending'}}" aria-label="{{$item['percent']}} por ciento completado">
                                    <span style="width:{{max(0, min(100, (int) $item['percent']))}}%"></span>
                                </div>
                                <p class="epicrisis-completeness-card__detail">
                                    @if(count($item['missing']))
                                        <strong>Pendiente:</strong> {{implode(', ', $item['missing'])}}
                                    @else
                                        Documento completo y listo para firma.
                                    @endif
                                </p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <div class="box box-info" id="clinical-closure-panel">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-check-circle"></i> {{$closurePreview['hospital'] ? 'Alta médica y salida física' : 'Cierre seguro del episodio'}}</h3>
                </div>
                <div class="box-body">
                    @if($closurePreview['already_closed'])
                        <div class="alert alert-success"><b>Episodio cerrado.</b> El checklist clínico y la firma ya fueron registrados.</div>
                        @if($closurePreview['physical_exit_at'])
                            <p>Salida física: {{$closurePreview['physical_exit_at']}}</p>
                        @endif
                    @elseif($closurePreview['medical_discharge_signed'])
                        <div class="alert alert-info"><b>Alta médica firmada. Pendiente de salida física.</b></div>
                        <p>Firma: {{$closurePreview['signed_at']}} | Profesional: {{$closurePreview['signed_by']}}</p>
                        @if(\Illuminate\Support\Facades\Route::has('epicrisis.confirmar_salida_final'))
                        <form id="frm_physical_exit">
                            {{csrf_field()}}
                            <input type="hidden" name="id_solicitud" value="{{$solicitud->id}}">
                            <input type="hidden" name="closure_id" value="{{$closurePreview['closure_id']}}">
                            <label><input type="checkbox" name="confirm_exit" value="1" required> Paciente fuera de la unidad; salida física verificada</label>
                            <button type="submit" class="btn btn-danger"><i class="fa fa-sign-out"></i> Confirmar salida física</button>
                        </form>
                        <div id="physical-exit-error" role="alert" hidden></div>
                        @endif
                    @else
                        <div class="epicrisis-readiness">
                            <div class="epicrisis-readiness__checks">
                                <span class="epicrisis-readiness__check {{$closurePreview['epicrisis_ready'] ? 'is-ready' : ''}}"><i class="fa {{$closurePreview['epicrisis_ready'] ? 'fa-check-circle' : 'fa-exclamation-circle'}}" aria-hidden="true"></i> Epicrisis</span>
                                <span class="epicrisis-readiness__check {{$closurePreview['diagnosis_ready'] ? 'is-ready' : ''}}"><i class="fa {{$closurePreview['diagnosis_ready'] ? 'fa-check-circle' : 'fa-exclamation-circle'}}" aria-hidden="true"></i> Diagnóstico de egreso</span>
                                <span class="epicrisis-readiness__check {{$closurePreview['signature_ready'] ? 'is-ready' : ''}}"><i class="fa {{$closurePreview['signature_ready'] ? 'fa-check-circle' : 'fa-exclamation-circle'}}" aria-hidden="true"></i> Firma</span>
                            </div>
                            <div class="epicrisis-readiness__orders">
                                <strong>{{$closurePreview['active_orders_count']}}</strong> orden(es) activa(s) requieren disposición antes del cierre
                            </div>
                        </div>
                        <form id="frm_closure">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="id_solicitud" value="{{$solicitud->id}}">
                            <div class="epicrisis-closure-grid">
                                <div class="form-group">
                                    <label>Tipo de cierre</label>
                                    <select class="form-control" name="closure_type" id="closure_type">
                                        <option value="">Seleccione...</option>
                                        <option value="DISCHARGE">Alta clínica</option>
                                        <option value="DECEASED">Defunción</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Conciliación de medicamentos</label>
                                    <textarea class="form-control" name="medication_reconciliation" rows="2" placeholder="Medicamentos suspendidos, continuados y cambios indicados al egreso."></textarea>
                                </div>
                                <div class="epicrisis-closure-grid__three">
                                    <div class="form-group">
                                        <label>Órdenes pendientes</label>
                                        <select class="form-control" name="pending_orders_decision" id="pending_orders_decision">
                                            <option value="NONE">No existen / no aplica</option>
                                            <option value="CANCEL">Cancelar con motivo</option>
                                            <option value="FOLLOW_UP">Transferir a seguimiento</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Motivo o plan de seguimiento</label>
                                        <input class="form-control" name="pending_orders_reason" placeholder="Motivo de cancelación o instrucciones">
                                    </div>
                                    <div class="form-group">
                                        <label>Responsable del seguimiento</label>
                                        <select class="form-control" name="follow_up_responsible_id">
                                            <option value="">Seleccione si corresponde...</option>
                                            @foreach($closureDoctors as $doctor)
                                                <option value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="death-workflow-fields" style="display:none;border:1px solid #e6b8b8;background:#fff7f7;padding:15px;margin-bottom:15px;">
                                <h4><i class="fa fa-file-text"></i> Circuito específico de defunción</h4>
                                <p class="text-muted">Este registro es independiente del alta clínica y exige constatación, certificado y notificaciones.</p>
                                <div class="row">
                                    <div class="form-group col-md-3"><label>Fecha y hora de defunción</label><input type="datetime-local" class="form-control" name="death_occurred_at"></div>
                                    <div class="form-group col-md-3"><label>Profesional que constata</label><select class="form-control" name="death_pronounced_by"><option value="">Seleccione...</option>@foreach($closureDoctors as $doctor)<option value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->nombre1}}</option>@endforeach</select></div>
                                    <div class="form-group col-md-3"><label>Fecha y hora de constatación</label><input type="datetime-local" class="form-control" name="death_pronounced_at"></div>
                                    <div class="form-group col-md-3"><label>Lugar</label><input class="form-control" name="death_location" placeholder="Sala, emergencia, UCI..."></div>
                                    <div class="form-group col-md-6"><label>Causa probable de muerte</label><textarea class="form-control" name="death_probable_cause" rows="2"></textarea></div>
                                    <div class="form-group col-md-3"><label>Manera de muerte</label><select class="form-control" name="death_manner"><option value="">Seleccione...</option><option value="NATURAL">Natural</option><option value="ACCIDENT">Accidental</option><option value="VIOLENT">Violenta</option><option value="UNDETERMINED">Por determinar</option></select></div>
                                    <div class="form-group col-md-3"><label>Número de certificado</label><input class="form-control" name="death_certificate_number"></div>
                                    <div class="form-group col-md-4"><label>Familiar/contacto notificado</label><input class="form-control" name="death_family_contact"></div>
                                    <div class="form-group col-md-4"><label>Hora de notificación familiar</label><input type="datetime-local" class="form-control" name="death_family_notified_at"></div>
                                    <div class="form-group col-md-4"><label>Hora de notificación a morgue</label><input type="datetime-local" class="form-control" name="death_morgue_notified_at"></div>
                                    <div class="form-group col-md-6"><label><input type="checkbox" name="death_identity_verified" value="1"> Identidad del paciente verificada</label></div>
                                    <div class="form-group col-md-6"><label><input type="checkbox" name="death_body_identified" value="1"> Identificación segura del cuerpo colocada</label></div>
                                    <div class="form-group col-md-12"><label>Observaciones</label><textarea class="form-control" name="death_notes" rows="2"></textarea></div>
                                </div>
                            </div>
                            <div id="closure-blockers" class="alert alert-danger" style="display:none;"></div>
                            <button type="button" class="btn btn-danger pull-right" onclick="cerrarEpisodio();" @if($closurePreview['hospital'] && (int) Auth::user()->id_tipo_usuario !== 3) disabled @endif>
                                <i class="fa fa-lock"></i> {{$closurePreview['hospital'] ? 'Validar y firmar alta / defunción' : 'Validar y cerrar episodio'}}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border epicrisis-clinical-header">
                    <div class="epicrisis-clinical-header__text">
                        <h3 class="box-title"><i class="fa fa-file-text-o"></i> Información clínica de egreso</h3>
                    </div>
                    @if(!request()->boolean('embedded'))
                    <div class="epicrisis-save-bar">
                        <span id="epicrisis-save-status" class="epicrisis-save-status js-epicrisis-save-status" role="status" aria-live="polite"></span>
                        <button type="button" class="btn btn-success js-save-epicrisis" id="save-epicrisis" onclick="guardar(true);">
                            <i class="fa fa-save"></i> Guardar cambios
                        </button>
                    </div>
                    @endif
                </div>
                <div class="box-body">
                    <form id="frm" class="epicrisis-final-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id_solicitud" value="{{$solicitud->id}}">
                        <input type="hidden" name="id_epicrisis_final" value="{{$epicrisis_final->id}}">
                        <input type="hidden" name="amendment_reason" id="amendment_reason" value="">
                        <input type="hidden" name="fecha_imprime" id="fecha_imprime" value="{{$epicrisis_final->fecha_imprime}}">

                        <div class="form-group col-md-12 epicrisis-section-heading">
                            <label for="tcuadro" class="control-label" >B. @lang('admision.rescuadroclinico-')</label>
                        </div> 
                        <div class="form-group col-md-12 epicrisis-field-label" >
                         <label for="cuadro" class="col-md-12 control-label">@lang('laboratorio.evolucion-') PRE</label>   
                        </div>
                        <div class="form-group col-md-12" >
                            <textarea rows="5" name="cuadro" id="cuadro" style="width: 100%;" onchange="guardar();">{{$epicrisis_final->cuadro_clinico}}</textarea>
                        </div>

                        <div class="form-group col-md-12 epicrisis-field-label" >
                         <label for="favorable_des" class="col-md-12 control-label">@lang('laboratorio.evolucion-') POST</label>   
                        </div>
                        <div class="form-group col-md-12" >
                            <input type="text" name="favorable_des" id="favorable_des" style="width: 100%;" onchange="guardar();" value="{{$epicrisis_final->favorable_des}}">
                        </div> 

                        <div class="form-group col-md-12 epicrisis-section-heading"><b>C. @lang('admision.resumenevocompli-')</b></div>

                        <div class="form-group col-md-6 {{ $errors->has('ep_resumen_evolucion') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="ep_resumen_evolucion" class="col-md-12 control-label">@lang('laboratorio.evolucion-')</label>
                            <div class="col-md-12">
                                <textarea rows="3" name="ep_resumen_evolucion" id="ep_resumen_evolucion" style="width: 100%;" onchange="guardar();">@if(!is_null($epicrisis_final))@if($epicrisis_final->ep_resumen_evolucion==null)FAVORABLE @else{{$epicrisis_final->ep_resumen_evolucion}}@endif @else FAVORABLE @endif</textarea>
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('complicacion') ? ' has-error' : '' }}" style="padding-left: 0px;">
                            <label for="complicacion" class="col-md-12 control-label">@lang('admision.complicacion-')</label>
                            <div class="col-md-12">
                                <textarea rows="3" name="complicacion" id="complicacion" style="width: 100%;" onchange="guardar();">@if(!is_null($epicrisis_final))@if($epicrisis_final->complicacion==null)NINGUNA @else{{$epicrisis_final->complicacion}}@endif @else NINGUNA @endif</textarea>
                            </div>
                        </div>

                        <div class="form-group col-md-12 epicrisis-field-label" >
                         <label for="conclusion" class="col-md-12 control-label">Conclusión clínica para el cierre</label>
                        </div>
                        <div class="form-group col-md-12" >
                            <textarea rows="5" name="conclusion" id="conclusion" style="width: 100%;" onchange="guardar();"><?php echo $epicrisis_final->conclusion; ?></textarea>
                        </div>

                        <div class="form-group col-md-12 epicrisis-section-heading">
                            <label for="hallazgo" class="control-label">D. HALLAZGOS RELEVANTES DE EXÁMENES Y PROCEDIMIENTOS DIAGNÓSTICOS</label>
                        </div>
                        <div class="form-group col-md-12">
                            <textarea rows="4" name="hallazgo" id="hallazgo" style="width: 100%;" onchange="guardar();">{{$epicrisis_final->hallazgo}}</textarea>
                        </div>

                        <div class="form-group col-md-12 epicrisis-section-heading">
                            <label for="resumen" class="control-label">E. RESUMEN DE TRATAMIENTO Y PROCEDIMIENTOS TERAPÉUTICOS</label>
                        </div>
                        <div class="form-group col-md-12">
                            <textarea rows="5" name="resumen" id="resumen" style="width: 100%;" onchange="guardar();">{{$epicrisis_final->resumen}}</textarea>
                        </div>

                        <div class="form-group col-md-12 epicrisis-section-heading" >
                         <label for="indicaciones_alta" class="col-md-12 control-label">F. INDICACIONES DE ALTA / EGRESO</label>
                        </div>
                        <div class="form-group col-md-12" >
                            <textarea rows="4" name="indicaciones_alta" id="indicaciones_alta" style="width: 100%;" onchange="guardar();">{{$epicrisis_final->indicaciones_alta}}</textarea>
                        </div>
                        <div class="form-group col-md-12 epicrisis-egress-title epicrisis-section-heading"><b>H. @lang('admision.condegresoypronos-')</b></div>

                        <div class="form-group col-md-6 epicrisis-egress-field {{ $errors->has('condicion') ? ' has-error' : '' }}">
                            <label for="condicion" class="col-md-12 control-label">@lang('admision.condicion-')</label>
                            <div class="col-md-12">
                                <input type="text" name="condicion" id="condicion" value="@if(!is_null($epicrisis_final)) @if($epicrisis_final->condicion==null) MEJORADA @else {{$epicrisis_final->condicion}} @endif @else MEJORADA @endif" onchange="guardar();">
                            </div>
                        </div>

                        <div class="form-group col-md-6 epicrisis-egress-field {{ $errors->has('pronostico') ? ' has-error' : '' }}">
                            <label for="pronostico" class="col-md-12 control-label">@lang('admision.pronostico-')</label>
                            <div class="col-md-12">
                                <input type="text" name="pronostico" id="pronostico" value="@if(!is_null($epicrisis_final)) @if($epicrisis_final->pronostico==null) BUENA @else {{$epicrisis_final->pronostico}} @endif @else BUENA @endif" onchange="guardar();">
                            </div>
                        </div>

                        <div class="form-group col-md-3 epicrisis-egress-field {{ $errors->has('alta') ? ' has-error' : '' }}">
                            <label for="alta" class="col-md-12 control-label">@lang('laboratorio.alta-')</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="alta" required onchange="guardar();">
                                    <option value="">@lang('laboratorio.seleccione') ...</option>
                                    <option value="DEFINITIVA" @if(old('alta')!='')@if(old('alta')=='DEFINITIVA') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->alta=='DEFINITIVA') selected @endif @endif>@lang('admision.definitiva-')</option>
                                    <option value="TRANSITORIA"  @if(old('alta')!='')@if(old('alta')=='TRANSITORIA') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->alta=='TRANSITORIA') selected @endif @endif>@lang('admision.transitoria-')</option>
                                    <option value="MEDICA" @if(old('alta')!='')@if(old('alta')=='MEDICA') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->alta=='MEDICA') selected @endif @endif>MEDICA</option>
                                    <option value="VOLUNTARIA" @if(old('alta')!='')@if(old('alta')=='VOLUNTARIA') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->alta=='VOLUNTARIA') selected @endif @endif>VOLUNTARIA</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 epicrisis-egress-field {{ $errors->has('discapacidad') ? ' has-error' : '' }}">
                            <label for="discapacidad" class="col-md-12 control-label">@lang('admision.discapacidad-')</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="discapacidad" required onchange="guardar();">
                                    <option value="">@lang('laboratorio.seleccione') ...</option>
                                    <option @if(old('discapacidad')!='')@if(old('discapacidad')=='ASINTOMÁTICA') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->discapacidad=='ASINTOMÁTICA') selected @endif @endif value="ASINTOMÁTICA">@lang('admision.asintomatica-')</option>
                                    <option @if(old('discapacidad')!='')@if(old('discapacidad')=='LEVE') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->discapacidad=='LEVE') selected @endif @endif value="LEVE">@lang('admision.leve-')</option>
                                    <option @if(old('discapacidad')!='')@if(old('discapacidad')=='MODERADA') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->discapacidad=='MODERADA') selected @endif @endif value="MODERADA">@lang('admision.moderada-')</option>
                                    <option @if(old('discapacidad')!='')@if(old('discapacidad')=='GRAVE') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->discapacidad=='GRAVE') selected @endif @endif value="GRAVE">@lang('admision.grave-')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 epicrisis-egress-field {{ $errors->has('retiro') ? ' has-error' : '' }}">
                            <label for="retiro" class="col-md-12 control-label">@lang('admision.retiro-')</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="retiro" required onchange="guardar();">
                                    <option value="">@lang('laboratorio.seleccione') ...</option>
                                    <option @if(old('retiro')!='')@if(old('retiro')=='AUTORIZADO') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->retiro=='AUTORIZADO') selected @endif @endif value="AUTORIZADO">@lang('admision.autorizado-')</option>
                                    <option @if(old('retiro')!='')@if(old('retiro')=='NO AUTORIZADO') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->retiro=='NO AUTORIZADO') selected @endif @endif value="NO AUTORIZADO">@lang('admision.noautorizado-')</option>                                    
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 epicrisis-egress-field {{ $errors->has('defuncion') ? ' has-error' : '' }}">
                            <label for="defuncion" class="col-md-12 control-label">@lang('admision.defuncion-')</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="defuncion" required onchange="guardar();">
                                    <option value="">@lang('laboratorio.seleccione') ...</option>
                                    <option @if(!is_null($epicrisis_final) && $epicrisis_final->defuncion=='NO') selected @endif value="NO">NO</option>
                                    <option @if(old('defuncion')!='')@if(old('defuncion')=='MENOS DE 48H') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->defuncion=='MENOS DE 48H') selected @endif @endif value="MENOS DE 48H">@lang('admision.menosde48-')</option>
                                    <option @if(old('defuncion')!='')@if(old('defuncion')=='MAS DE 48H') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->defuncion=='MAS DE 48H') selected @endif @endif value="MAS DE 48H">@lang('admision.masde48-')</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-3 epicrisis-egress-field">
                            <label for="dias_estadia" class="col-md-12 control-label" style="padding-left: 0px;">@lang('admision.diasestadia-')</label>    
                            <input id="dias_estadia" type="number" class="form-control input-sm" name="dias_estadia" value="@if(old('dias_estadia')!=''){{old('dias_estadia')}}@elseif(!is_null($epicrisis_final)){{$epicrisis_final->dias_estadia}}@endif" onchange="guardar();">
                        </div>

                        <div class="form-group col-md-3 epicrisis-egress-field">
                            <label for="dias_incapacidad" class="col-md-12 control-label">@lang('admision.diasincapa-')</label>    
                            <input id="dias_incapacidad" type="number" class="form-control input-sm" name="dias_incapacidad" value="@if(old('dias_incapacidad')!=''){{old('dias_incapacidad')}}@elseif(!is_null($epicrisis_final)){{$epicrisis_final->dias_incapacidad}}@endif" onchange="guardar();">
                        </div>

                        <div class="form-group col-md-3 epicrisis-egress-field {{ $errors->has('receta') ? ' has-error' : '' }}">
                            <label for="receta" class="col-md-12 control-label">@lang('admision.receta-')</label>
                            <div class="col-md-12">
                                <select class="form-control input-sm" name="receta" required onchange="guardar();">
                                    <option value="">@lang('laboratorio.seleccione') ...</option>
                                    <option  @if(old('receta')!='')@if(old('receta')=='SI') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->receta=='SI') selected @endif @endif value="SI">@lang('laboratorio.si')</option>
                                    <option @if(old('receta')!='')@if(old('receta')=='NO') selected @endif @elseif(!is_null($epicrisis_final))@if($epicrisis_final->receta=='NO') selected @endif @endif value="NO">@lang('laboratorio.no')</option>
                                </select>
                            </div>
                        </div>
                        @if($closurePreview['already_closed'] && !$closurePreview['hospital'])
                        <div class="form-group col-md-12">
                            <div class="alert alert-info">
                                <b>Documento firmado e inmutable.</b> Una corrección conservará el original y generará una adenda con motivo, autor y fecha.
                            </div>
                            <button type="button" id="start-amendment" class="btn btn-warning" onclick="iniciarAdenda();"><i class="fa fa-edit"></i> Registrar adenda</button>
                            <button type="button" id="save-amendment" class="btn btn-primary" style="display:none" onclick="guardarAdenda();"><i class="fa fa-save"></i> Guardar adenda</button>
                            <button type="button" id="cancel-amendment" class="btn btn-default" style="display:none" onclick="location.reload();">Cancelar</button>
                            <div id="amendment-error" class="alert alert-danger" style="display:none;margin-top:10px"></div>
                        </div>
                        @endif
                    </form> 

                    <div class="epicrisis-diagnosis">
                            <form id="frm_cie" novalidate>
                                <input type="hidden" name="codigo" id="codigo">
                                <input type="hidden" name="id_solicitud2" id="id_solicitud2" value="{{$solicitud->id}}">
                                <input type="hidden" name="id_epicrisis_final2" id="id_epicrisis_final2" value="{{$epicrisis_final->id}}">
                                <div class="epicrisis-diagnosis__title">G. DIAGNÓSTICO DE ALTA / EGRESO · @lang('admision.diagpresundefi-')</div>
                                <div class="epicrisis-diagnosis__grid">
                                <div class="form-group epicrisis-diagnosis__field epicrisis-diagnosis__field--diagnosis">
                                    <label for="cie10">Diagnóstico CIE-10</label>
                                    <input id="cie10" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" >
                                </div>
                                <div class="form-group epicrisis-diagnosis__field">
                                    <label for="pre_def">Tipo</label>
                                    <select id="pre_def" name="pre_def" class="form-control input-sm" required>
                                        <option value="">@lang('laboratorio.seleccione') ...</option>
                                        <option value="PRESUNTIVO">@lang('admision.presuntivo-')</option>
                                        <option value="DEFINITIVO">@lang('admision.definitivo-')</option>   
                                    </select> 
                                </div> 
                                <div class="form-group epicrisis-diagnosis__field">
                                    <label for="ing_egr">Momento</label>
                                    <select id="ing_egr" name="ing_egr" class="form-control input-sm" required>
                                        <option value="">@lang('laboratorio.seleccione') ...</option>
                                        <option value="INGRESO">@lang('laboratorio.ingreso-')</option>
                                        <option value="EGRESO">@lang('laboratorio.egreso-')</option>   
                                    </select> 
                                </div>
                                <div class="form-group epicrisis-diagnosis__field">
                                    <button type="button" id="bagregar" class="btn btn-success btn-sm epicrisis-diagnosis__add"><span class="glyphicon glyphicon-plus"></span> @lang('laboratorio.agregar')</button>
                                </div>
                                </div>
                                <p id="diagnosis-error" class="epicrisis-diagnosis__error" role="alert"></p>
                            <div class="epicrisis-diagnosis__table">
                                <table id="tdiagnostico" class="table table-striped" style="font-size: 12px;">

                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(request()->boolean('embedded'))
        <footer class="epicrisis-module-header epicrisis-module-footer">
            <div class="epicrisis-module-header__title"><span class="epicrisis-module-header__number">006</span><h2>Epicrisis</h2></div>
            <div class="epicrisis-module-header__actions">
                <span class="epicrisis-module-header__status js-epicrisis-save-status" role="status" aria-live="polite"></span>
                <button type="button" class="btn epicrisis-module-header__save js-save-epicrisis" onclick="guardar(true);"><i class="fa fa-save"></i> Guardar</button>
                <a href="{{ route('epicrisis.pdf_final', ['id_solicitud' => $solicitud->id]) }}" target="_blank" class="btn epicrisis-module-header__pdf"><i class="fa fa-file-pdf-o"></i> PDF F-006</a>
            </div>
        </footer>
    @endif
</div>



<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    var hospitalFinalReadOnly = @json($closurePreview['hospital'] && ($closurePreview['medical_discharge_signed'] || $closurePreview['already_closed'] || !Auth::user()->hasAnyUserTypeName(['SISTEMAS', 'DOCTORES', 'DOCTOR'])));
    $(document).ready(function() {
        $('.select2').select2();
        cargar_tabla();
        var edad;
        edad = calcularEdad('<?php echo $paciente->fecha_nacimiento; ?>') + "años";
        $('#edad').text(edad);
        @if($closurePreview['already_closed'] || $closurePreview['medical_discharge_signed'])
            $('#frm').find('input, textarea, select').prop('disabled', true);
        @endif
        if (hospitalFinalReadOnly) {
            $('#frm, #frm_cie').find('input, textarea, select, button').prop('disabled', true);
            $('.js-save-epicrisis').prop('disabled', true);
        }
        @if(request()->boolean('embedded'))
            notificarEpicrisisPadre();
        @endif
    });

    function guardar(manual) {
        manual = manual === true;
        if (hospitalFinalReadOnly) return;
        var status = $('.js-epicrisis-save-status');
        var button = $('.js-save-epicrisis');
        status.removeClass('text-success text-danger').text('Guardando...');
        button.prop('disabled', true);
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.actualiza_final')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            dataType: 'json',
            data: $("#frm").serialize(),
            success: function(data) {
                status.addClass('text-success').text('Guardado correctamente');
                notificarEpicrisisPadre();
                setTimeout(function() { status.text(''); }, manual ? 3500 : 1500);
            },
            error: function(xhr) {
                var message = (xhr.responseJSON && (xhr.responseJSON.msj || xhr.responseJSON.message)) || 'No se pudo guardar.';
                status.addClass('text-danger').text(message);
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    }

    function notificarEpicrisisPadre() {
        if (window.parent === window) return;
        var summary = $('<div>').html($('#cuadro').val() || '').text().replace(/\s+/g, ' ').trim();
        window.parent.postMessage({
            type: 'epicrisis006:saved',
            summary: summary.substring(0, 130),
            updatedAt: new Date().toLocaleString('es-EC')
        }, window.location.origin);
    }

    function iniciarAdenda() {
        var reason = window.prompt('Motivo clínico de la corrección (mínimo 5 caracteres):');
        if (!reason || $.trim(reason).length < 5) {
            $('#amendment-error').text('Debe documentar un motivo de al menos 5 caracteres.').show();
            return;
        }
        $('#amendment_reason').val($.trim(reason));
        $('#frm').find('input, textarea, select').prop('disabled', false).removeAttr('onchange');
        $('#start-amendment').hide();
        $('#save-amendment, #cancel-amendment').show();
        $('#amendment-error').hide();
    }

    function guardarAdenda() {
        $('#save-amendment').prop('disabled', true);
        $.ajax({
            type: 'post', url: "{{route('epicrisis.actualiza_final')}}", data: $('#frm').serialize(),
            success: function () { location.reload(); },
            error: function (xhr) {
                $('#save-amendment').prop('disabled', false);
                $('#amendment-error').text(xhr.responseText || 'No se pudo registrar la adenda.').show();
            }
        });
    }

    function cerrarEpisodio() {
        var deceased = $('#closure_type').val() === 'DECEASED';
        var message = deceased ? '¿Confirma el cierre por DEFUNCIÓN? Este proceso no se registrará como alta común.' : @json($closurePreview['hospital'] ? 'Firmar alta medica y mantener cama ocupada hasta confirmar la salida fisica?' : '¿Confirma que desea validar y cerrar definitivamente este episodio?');
        if (!confirm(message)) return;
        $('#closure-blockers').hide().empty();
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.actualiza_final')}}",
            data: $('#frm').serialize(),
            success: function() { ejecutarCierre(); },
            error: function() {
                $('#closure-blockers').html('<b>No se pudo guardar la epicrisis antes de validar el cierre.</b>').show();
            }
        });
    }

    $('#closure_type').on('change', function(){
        $('#death-workflow-fields').toggle($(this).val() === 'DECEASED');
    }).trigger('change');

    function ejecutarCierre() {
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.cerrar_final')}}",
            data: $('#frm_closure').serialize(),
            success: function(data) {
                alertas('success', 'Cierre clínico', data.msj);
                setTimeout(function(){ window.location.reload(); }, 1200);
            },
            error: function(xhr) {
                var data = xhr.responseJSON || {};
                var items = data.bloqueos || [data.msj || 'No fue posible cerrar el episodio.'];
                var html = '<b>El cierre está bloqueado:</b><ul>';
                $.each(items, function(index, item){ html += '<li>' + $('<div>').text(item).html() + '</li>'; });
                html += '</ul>';
                $('#closure-blockers').html(html).show();
            }
        });
    }

    @if(\Illuminate\Support\Facades\Route::has('epicrisis.confirmar_salida_final'))
    $('#frm_physical_exit').on('submit', function(event) {
        event.preventDefault();
        if (!this.checkValidity() || !confirm('Confirmar salida fisica y dejar cama en preparacion?')) return;
        var form = $(this);
        form.find('button').prop('disabled', true);
        $.ajax({
            type: 'post', url: @json(route('epicrisis.confirmar_salida_final')), data: form.serialize(),
            success: function() { location.reload(); },
            error: function(xhr) {
                form.find('button').prop('disabled', false);
                var data = xhr.responseJSON || {};
                $('#physical-exit-error').text((data.bloqueos || [data.msj || 'No se pudo confirmar la salida.']).join(' ')).prop('hidden', false);
            }
        });
    });
    @endif

    function formatRepoSelection(repo) {
        return repo.full_name || repo.text;
    }

    $("#cie10").autocomplete({
        source: function(request, response) {

            $.ajax({
                url: "{{route('epicrisis.cie10_nombre')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    term: request.term
                },
                dataType: "json",
                type: 'post',
                success: function(data) {
                    response(data);

                }
            })
        },
        minLength: 2,
    });

    $("#cie10").change(function() {
        $.ajax({
            type: 'post',
            url: "{{route('epicrisis.cie10_nombre2')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            dataType: 'json',
            data: $("#cie10"),
            success: function(data) {
                if (data != '0') {
                    $('#codigo').val(data.id);

                }

            },
            error: function(data) {

            }
        })
    });


    $('#bagregar').on('click', function(event) {
        event.preventDefault();
        var error = $('#diagnosis-error');
        var button = $(this);
        error.hide().text('');

        if (!$('#codigo').val() || !$.trim($('#cie10').val())) {
            error.text('Seleccione un diagnóstico válido de la lista CIE-10.').show();
            return;
        }
        if (!$('#pre_def').val()) {
            error.text('Seleccione si el diagnóstico es presuntivo o definitivo.').show();
            return;
        }
        if (!$('#ing_egr').val()) {
            error.text('Seleccione si corresponde al ingreso o al egreso.').show();
            return;
        }

        button.prop('disabled', true);
        guardar_cie10_PRO().done(function() {
            $('#codigo, #cie10').val('');
            $('#pre_def, #ing_egr').val('');
            cargar_tabla();
            notificarEpicrisisPadre();
        }).fail(function(xhr) {
            var data = xhr.responseJSON || {};
            error.text(data.msn || data.message || 'No se pudo agregar el diagnóstico.').show();
        }).always(function() {
            button.prop('disabled', false);
        });
    });

    function guardar_cie10_PRO() {
        return $.ajax({
            type: 'post',
            url: "{{route('epicrisis.agregar_cie10_final')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            dataType: 'json',
            data: {
                'codigo': $("#codigo").val(),
                'pre_def': $("#pre_def").val(),
                'id_epicrisis_final': $("#id_epicrisis_final2").val(),
                'id_ho_solicitud':  $("#id_solicitud2").val(),
                'in_eg': $("#ing_egr").val()
            }
        });
    }

    function cargar_tabla() {
        $.ajax({
            url: "{{route('epicrisis.cargar_final',['id' => $epicrisis_final->id])}}",
            dataType: "json",
            type: 'get',
            success: function(data) {
                var table = $('#tdiagnostico').empty();

                $.each(data, function(index, value) {
                    var row = $('<tr>', {id: 'tdiag' + value.id});
                    $('<td>').append($('<b>').text(value.cie10 || '')).appendTo(row);
                    $('<td>').text(value.pre_def || '').appendTo(row);
                    $('<td>').text(value.descripcion || '').appendTo(row);
                    $('<td>').text(value.ingreso_egreso || '').appendTo(row);
                    var actions = $('<td>');
                    if (!hospitalFinalReadOnly) {
                        $('<button>', {type: 'button', class: 'btn btn-xs btn-danger', title: 'Eliminar diagnóstico'})
                            .append($('<span>', {class: 'glyphicon glyphicon-trash'}))
                            .on('click', function() { eliminar(value.id); })
                            .appendTo(actions);
                    }
                    actions.appendTo(row);
                    table.append(row);
                });
            }
        });
    }

    function eliminar(id) {
        if (hospitalFinalReadOnly) return;
        var i = document.getElementById('tdiag' + id).rowIndex;
        console.log('id_cie10 ' + i);
        document.getElementById("tdiagnostico").deleteRow(i);
        $.ajax({
            type: 'delete',
            url: "{{url('cie10/final/eliminar')}}/" + id,
            data: {'_token': '{{ csrf_token() }}'},
            dataType: 'json',
            success: function(data) {
                
            },
            error: function(data) {

            }
        });
    }
</script>

@include('sweet::alert')
@endsection
