<style>
    /* Identidad visual del expediente clinico AITROL Borja. */
    .hc4-patient-detail-page {
        padding: 8px 12px 18px !important;
        background: linear-gradient(180deg, #f5faff 0%, #edf5f9 100%) !important;
    }
    .hc4-patient-detail-page > .container-fluid { padding-right: 0; padding-left: 0; }
    .hc4-patient-detail-page > .container-fluid > .row { margin-right: 0; margin-left: 0; }
    .hc4-patient-detail-title {
        position: relative;
        overflow: hidden;
        margin: 0 0 9px !important;
        padding: 12px 16px !important;
        border: 1px solid rgba(255,255,255,.24) !important;
        border-radius: 13px !important;
        background: linear-gradient(118deg, #053d68 0%, #006fb9 55%, #12a4b4 100%) !important;
        box-shadow: 0 10px 27px rgba(0,74,130,.18) !important;
    }
    .hc4-patient-detail-title::after {
        position: absolute;
        top: -54px;
        right: 8%;
        width: 150px;
        height: 150px;
        border: 22px solid rgba(255,255,255,.08);
        border-radius: 50%;
        content: '';
    }
    .hc4-patient-detail-title h1 {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 38px;
        margin: 0;
        color: #fff;
        font-size: 16px !important;
    }
    .hc4-patient-detail-title h1 img {
        width: 40px !important;
        filter: brightness(0) invert(1);
    }
    .hc4-patient-detail-grid {
        display: grid !important;
        grid-template-columns: minmax(0, 7fr) minmax(0, 5fr) !important;
        align-items: start;
        gap: 10px;
        width: 100%;
        min-width: 0;
        margin-right: 0;
        margin-left: 0;
    }
    .hc4-patient-detail-grid > [class*="col-md-"] {
        float: none !important;
        width: 100%;
        min-width: 0;
        max-width: 100% !important;
        box-sizing: border-box;
        padding-right: 0 !important;
        padding-left: 0 !important;
    }
    .hc4-patient-detail-grid > .area_1_dato > .row {
        width: 100%;
        min-width: 0;
        margin-right: 0;
        margin-left: 0;
    }
    .hc4-patient-primary {
        overflow: hidden;
        margin: 0 !important;
        border: 1px solid #c9dee8 !important;
        border-radius: 13px !important;
        background: linear-gradient(135deg, #fff 0%, #f4fafc 100%) !important;
        box-shadow: 0 8px 23px rgba(0,74,117,.09) !important;
    }
    .hc4-patient-primary > .col-md-12 { padding: 10px 12px !important; }
    .hc4-patient-primary > .col-md-12 > .row {
        display: flex;
        align-items: flex-start;
        margin-right: -7px;
        margin-left: -7px;
    }
    .hc4-patient-primary > .col-md-12 > .row > [class*="col-"] { padding-right: 7px !important; padding-left: 7px !important; }
    .hc4-patient-primary .detalle_tabla { width: calc(100% - 24px); margin: 4px 12px 8px !important; }
    .hc4-patient-primary .detalle_tabla tr:first-child td { padding: 3px 4px; color: #000 !important; font-weight: 700; }
    .hc4-patient-primary .detalle_tabla tr:nth-child(2) td { padding: 4px; color: #213f51; }
    .hc4-patient-primary input,
    .hc4-patient-primary textarea,
    .hc4-patient-primary select {
        min-height: 34px;
        padding: 6px 9px;
        border: 1px solid #cbdde6 !important;
        border-radius: 7px;
        background: #fbfdfe !important;
    }
    .hc4-patient-primary textarea { display: block; width: 100%; min-height: 48px; resize: vertical; }
    .hc4-patient-primary label { color: #124574 !important; font-weight: 700; }
    .hc4-patient-primary .btn_ordenes {
        display: flex;
        align-items: center;
        min-height: 34px;
        height: auto !important;
        padding: 5px 8px !important;
        border: 1px solid #bcd9e5 !important;
        border-radius: 8px;
        color: #153f54 !important;
        background: linear-gradient(135deg, #fff, #eaf7fb) !important;
        box-shadow: 0 5px 13px rgba(0,83,126,.11);
    }
    .hc4-patient-primary .btn_ordenes:hover { border-color: #58a9c2 !important; box-shadow: 0 8px 18px rgba(0,83,126,.18); }
    .hc4-patient-primary .btn_ordenes > div { width: 100%; padding: 0 !important; }
    .hc4-patient-primary .btn_ordenes .row { display: flex; align-items: center; width: 100%; margin: 0 !important; padding: 0 !important; }
    .hc4-patient-primary .btn_ordenes .row > div:first-child {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 28px;
        width: 28px;
        height: 28px;
        padding: 5px !important;
        border-radius: 7px;
        background: linear-gradient(135deg, #07547e, #0b91b8);
        box-shadow: 0 4px 10px rgba(0,83,126,.22);
    }
    .hc4-patient-primary .btn_ordenes img { display: block; max-width: 17px; max-height: 17px; object-fit: contain; filter: brightness(0) invert(1); }
    .hc4-patient-primary .btn_ordenes i { color: #fff !important; }
    .hc4-patient-primary .btn_ordenes label { margin: 0; color: #153f54 !important; cursor: pointer; }

    .hc4-patient-modules > .row { margin-right: 0 !important; margin-left: 0 !important; }
    .hc4-patient-modules > .row > .col-md-12 > .row { margin-right: -4px !important; margin-left: -4px !important; }
    .hc4-patient-modules > .row > .col-md-12 > .row > [class*="col-"] { padding: 4px !important; }
    .hc4-patient-modules .recuadro {
        overflow: hidden;
        height: 210px;
        margin: 0;
        border: 1px solid #c9dee8;
        border-radius: 11px;
        background: #fff;
        box-shadow: 0 6px 17px rgba(0,74,117,.09);
    }
    .hc4-patient-modules .fila1,
    .hc4-patient-modules .fila2,
    .hc4-patient-modules .fila3,
    .hc4-patient-modules .fila4,
    .hc4-patient-modules .box-header {
        min-height: 42px;
        color: #fff !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
    }
    .hc4-patient-modules .box-header { display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; }
    .hc4-patient-modules .box-header > .row { display: flex; align-items: center; flex: 1 1 auto; min-width: 0; margin: 0 !important; }
    .hc4-patient-modules .box-header > .row > div { display: flex; align-items: center; min-width: 0; margin-right: 7px !important; }
    .hc4-patient-modules .box-header > .row > div:first-child {
        justify-content: center;
        flex: 0 0 27px;
        width: 27px;
        height: 27px;
        padding: 4px;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 7px;
        background: rgba(255,255,255,.14);
    }
    .hc4-patient-modules .box-header img { display: block !important; max-width: 17px; max-height: 17px; object-fit: contain; filter: brightness(0) invert(1); }
    .hc4-patient-modules .box-title { margin: 0; color: #fff !important; font-size: 10px !important; font-weight: 700; text-transform: uppercase; }
    .hc4-patient-modules .box-tools { margin-left: auto; }
    .hc4-patient-modules .btn-box-tool {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0 !important;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 8px;
        color: #fff !important;
        background: rgba(255,255,255,.1);
    }
    .hc4-patient-modules .box-body { height: calc(100% - 42px); padding: 8px 10px !important; color: #213f51; background: #fff; }
    .hc4-patient-modules .btn_ordenes {
        min-height: 26px;
        margin-bottom: 5px;
        border: 1px solid #087eae !important;
        border-radius: 7px;
        color: #fff !important;
        background: linear-gradient(100deg, #07547e, #0b91b8) !important;
        box-shadow: 0 3px 8px rgba(0,83,126,.16);
    }

    .hc4-patient-detail-page #area_trabajo {
        width: calc(100% - 30px) !important;
        min-width: 0;
        max-width: calc(100% - 30px) !important;
        box-sizing: border-box;
        margin: 8px 15px 0 !important;
        padding: 0 !important;
        border: 1px solid #c9dee8;
        border-radius: 12px;
        background: linear-gradient(145deg, #fbfeff, #edf7fa);
        box-shadow: 0 8px 24px rgba(0,74,117,.08);
    }
    .hc4-patient-detail-page #area_trabajo > * {
        width: 100%;
        min-width: 0;
        max-width: 100% !important;
        box-sizing: border-box;
    }
    .hc4-patient-detail-page #area_trabajo .parent {
        height: auto !important;
        min-height: 0 !important;
        max-height: 620px;
        overflow-y: auto;
        overflow-x: hidden;
        background: transparent !important;
    }
    .hc4-patient-detail-page #area_trabajo [style*="background-color: #56ABE3"],
    .hc4-patient-detail-page #area_trabajo [style*="background-color:#56ABE3"] {
        background-color: transparent !important;
        background-image: none !important;
    }
    .hc4-patient-detail-page #area_trabajo .hc4-recipes-list-shell,
    .hc4-patient-detail-page #area_trabajo .hc4-standard-module-container > div:not(.hc4-standard-module-header),
    .hc4-patient-detail-page #area_trabajo .hc4-standard-module-container > .box-body {
        background: linear-gradient(145deg, #fbfeff, #edf7fa) !important;
    }
    .hc4-patient-detail-page #area_trabajo:empty { display: none; }
    .hc4-patient-detail-page #area_trabajo .odont-consultas {
        min-height: 0;
        border: 0 !important;
        border-radius: 12px;
        background: transparent !important;
    }
    .hc4-patient-detail-page #area_trabajo .odont-consultas__header {
        padding: 12px 16px;
        border-radius: 11px 11px 0 0;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
    }
    .hc4-patient-detail-page #area_trabajo .odont-consultas__body { padding: 12px; }
    .hc4-patient-detail-page #area_trabajo .odont-consulta {
        overflow: hidden;
        border: 1px solid #c9dee8 !important;
        border-radius: 10px;
        box-shadow: 0 5px 14px rgba(0,74,117,.08);
    }
    .hc4-patient-detail-page #area_trabajo .odont-consulta__toggle { background: #087eae !important; }

    /* Modulos odontologicos cargados dentro del expediente. */
    .hc4-patient-detail-page #area_trabajo > .odont-proc-shell,
    .hc4-patient-detail-page #area_trabajo > .odont-proc-selector,
    .hc4-patient-detail-page #area_trabajo > .odont-cotizaciones-panel,
    .hc4-patient-detail-page #area_trabajo > .odont-captura-inline,
    .hc4-patient-detail-page #area_trabajo > .box {
        overflow: hidden;
        margin: 0 !important;
        border: 0 !important;
        border-radius: 12px !important;
        background: transparent !important;
        box-shadow: none !important;
    }
    .hc4-patient-detail-page #area_trabajo .odont-proc-header,
    .hc4-patient-detail-page #area_trabajo .odont-proc-selector-header,
    .hc4-patient-detail-page #area_trabajo .odont-cotizaciones-panel__header,
    .hc4-patient-detail-page #area_trabajo .odont-captura-titulo,
    .hc4-patient-detail-page #area_trabajo > .box > .box-header {
        color: #fff !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        border-color: transparent !important;
    }
    .hc4-patient-detail-page #area_trabajo .odont-proc-list,
    .hc4-patient-detail-page #area_trabajo .odont-proc-selector-body,
    .hc4-patient-detail-page #area_trabajo .odont-cotizaciones-panel__body,
    .hc4-patient-detail-page #area_trabajo > .box > .box-body {
        background: linear-gradient(145deg, #fbfeff, #edf7fa) !important;
    }
    .hc4-patient-detail-page #area_trabajo .odont-proc-item,
    .hc4-patient-detail-page #area_trabajo .odont-cotizacion-panel-item,
    .hc4-patient-detail-page #area_trabajo .odont-captura-box,
    .hc4-patient-detail-page #area_trabajo .box .box {
        overflow: hidden;
        border: 1px solid #c9dee8 !important;
        border-radius: 10px !important;
        background: #fff !important;
        box-shadow: 0 5px 14px rgba(0,74,117,.08) !important;
    }
    .hc4-patient-detail-page #area_trabajo .odont-proc-resumen { background: #fff; }
    .hc4-patient-detail-page #area_trabajo .odont-proc-toggle,
    .hc4-patient-detail-page #area_trabajo .odont-cotizacion-panel-item__toggle {
        border: 1px solid rgba(255,255,255,.34) !important;
        border-radius: 9px !important;
        color: #fff !important;
        background: linear-gradient(135deg, #087eae, #16aaa5) !important;
        box-shadow: 0 4px 11px rgba(0,94,129,.2);
    }
    .hc4-patient-detail-page #area_trabajo .odont-proc-edicion {
        border-top: 1px solid #d5e5ec !important;
        background: #f5fafc;
    }
    .hc4-patient-detail-page #area_trabajo .odont-proc-editor {
        overflow: hidden;
        border: 1px solid #c9dee8 !important;
        border-radius: 10px;
        background: #fff !important;
        box-shadow: 0 5px 14px rgba(0,74,117,.07);
    }
    .hc4-patient-detail-page #area_trabajo .odont-proc-editor-header,
    .hc4-patient-detail-page #area_trabajo .odont-proc-imagenes-original .box-header,
    .hc4-patient-detail-page #area_trabajo .odont-captura-box-header {
        color: #fff !important;
        background: linear-gradient(105deg, #07547e, #087eae 70%, #16aaa5) !important;
        border-color: transparent !important;
    }
    .hc4-patient-detail-page #area_trabajo .odont-proc-editor-body { background: #fff; }
    .hc4-patient-detail-page #area_trabajo .odont-proc-imagenes-original {
        overflow: hidden;
        border: 1px solid #c9dee8 !important;
        border-radius: 10px !important;
        box-shadow: 0 5px 14px rgba(0,74,117,.07);
    }
    .hc4-patient-detail-page #area_trabajo input.form-control,
    .hc4-patient-detail-page #area_trabajo select.form-control,
    .hc4-patient-detail-page #area_trabajo textarea.form-control,
    .hc4-patient-detail-page #area_trabajo .select2-selection {
        border-color: #c5dbe5 !important;
        border-radius: 8px !important;
        background: #fbfdfe !important;
        box-shadow: none !important;
    }
    .hc4-patient-detail-page #area_trabajo .select2-selection__choice {
        border: 1px solid #93c7d7 !important;
        border-radius: 6px !important;
        color: #124b62 !important;
        background: #e8f7fa !important;
    }
    .hc4-patient-detail-page #area_trabajo .btn-primary,
    .hc4-patient-detail-page #area_trabajo .btn-info,
    .hc4-patient-detail-page #area_trabajo .odo-primary {
        border-color: #087eae !important;
        color: #fff !important;
        background: linear-gradient(105deg, #07547e, #087eae 70%, #16aaa5) !important;
        box-shadow: 0 4px 10px rgba(0,83,126,.16);
    }
    .hc4-patient-detail-page #area_trabajo .odontologia-033 {
        --paper: #f4fafc;
        --line: #c9dee8;
        --soft: #dceaf0;
        --blue: #07547e;
        --blue2: #e8f6f9;
    }
    .hc4-patient-detail-page #area_trabajo .odontologia-033 .odo-head {
        background: linear-gradient(105deg, #07547e, #087eae 70%, #16aaa5) !important;
    }
    .hc4-patient-detail-page #area_trabajo .odontologia-033 .odo-page,
    .hc4-patient-detail-page #area_trabajo .odontologia-033 .odo-context {
        border-color: #c9dee8;
        border-radius: 11px;
        box-shadow: 0 5px 16px rgba(0,74,117,.08);
    }
    .hc4-patient-detail-page #area_trabajo > .box .box-header[style*="background-color: #004AC1"],
    .hc4-patient-detail-page #area_trabajo > .box .box-header[style*="background-color:#004AC1"] {
        color: #fff !important;
        background: linear-gradient(105deg, #07547e, #087eae 70%, #16aaa5) !important;
        border-color: transparent !important;
    }
    .hc4-patient-detail-page #area_trabajo > .box .box-body[style*="background-color: #56ABE3"] {
        background: linear-gradient(145deg, #fbfeff, #edf7fa) !important;
    }

    @media (max-width: 991px) {
        .hc4-patient-detail-grid {
            display: block !important;
            grid-template-columns: none !important;
        }
        .hc4-patient-modules { margin-top: 10px; }
    }
    @media (max-width: 767px) {
        .hc4-patient-detail-page { padding-right: 8px !important; padding-left: 8px !important; }
        .hc4-patient-primary > .col-md-12 > .row { display: block; }
        .hc4-patient-detail-title h1 { font-size: 14px !important; }
    }
</style>
