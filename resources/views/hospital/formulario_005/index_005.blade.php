@extends('layouts.app-template-h')

@section('content')
    <div class="col-12 hospitalization-workspace">
        @include('hospital.workflow.episode-links')
        <div class="row hospitalization-workspace__row">
            <main class="col-12 col-xl-7 hospitalization-workspace__main" id="content">
                <div class="hospitalization-workspace__surface" id="pasos" aria-live="polite"></div>
            </main>

            <aside class="col-12 col-xl-5 hospitalization-workspace__sidebar" aria-label="Accesos de hospitalización">
                @include('layouts.box_005')
            </aside>
        </div>
    </div>
@endsection

<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('ho/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<style>
    .hospitalization-workspace {
        padding-right: 0;
        padding-left: 0;
    }

    .hospitalization-workspace__row {
        margin-right: -10px;
        margin-left: -10px;
    }

    .hospitalization-workspace__main,
    .hospitalization-workspace__sidebar {
        padding-right: 10px;
        padding-left: 10px;
        transition: flex-basis .25s ease, max-width .25s ease;
    }

    .hospitalization-workspace__surface {
        min-height: 280px;
    }

    .hospitalization-workspace__surface > .card {
        width: 100%;
        margin-right: 0;
        margin-left: 0;
    }

    .epicrisis006-workspace {
        width: 100%;
        margin: 0;
        overflow: hidden;
        border: 0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 61, 91, .09);
    }
    .epicrisis006-workspace__body { position: relative; width: 100%; min-height: 720px; margin: 0; padding: 0; background: #fff; }
    .epicrisis006-workspace__loading { position: absolute; inset: 0; z-index: 1; display: flex; align-items: center; justify-content: center; gap: 9px; color: #39718d; background: #f4f9fc; font-size: 12px; font-weight: 700; }
    .epicrisis006-workspace__frame { position: relative; z-index: 2; display: block; width: 100%; min-height: 720px; margin: 0; padding: 0; border: 0; background: #fff; }

    @media (min-width: 1200px) {
        .hospitalization-workspace.is-editor-open .hospitalization-workspace__main {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .hospitalization-workspace.is-editor-open .hospitalization-workspace__sidebar {
            display: none;
        }
    }

    @media (max-width: 1199.98px) {
        .hospitalization-workspace__sidebar {
            margin-top: 18px;
        }

        .epicrisis006-workspace__frame { min-height: 720px; }
    }

    @media (max-width: 767.98px) {
        .epicrisis006-workspace__frame { min-height: 620px; }
    }
</style>
