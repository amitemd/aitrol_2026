<style type="text/css">
    .parent{
        overflow-y:auto;
        height: auto;
        max-height: 600px;
    }
    .parent::-webkit-scrollbar {
        width: 8px;
    } /* this targets the default scrollbar (compulsory) */
    .parent::-webkit-scrollbar-thumb {
        background: #004AC1;
        border-radius: 10px;
    }
    .parent::-webkit-scrollbar-track {
        width: 10px;
        background-color: #004AC1;
        box-shadow: inset 0px 0px 0px 3px #56ABE3;
    } /* the new scrollbar will have a flat appearance with the set background color */
    .parent::-webkit-scrollbar-track-piece{
        width: 2px;
        background-color: none;
    }

    .parent::-webkit-scrollbar-button {
          background-color: none;
    } /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */

    .parent::-webkit-scrollbar-corner {
          background-color: none;
    } /* if both the vertical and the horizontal bars appear, then perhaps the right bottom corner also needs to be styled */

    .btn-block{
      background-color: #004AC1;
    }
     .table>tbody>tr>td, .table>tbody>tr>th {
        padding: 0.4% ;
    }



    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 15px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }
    .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
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
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }

    .mce-edit-focus,
    .mce-content-body:hover {
        outline: 2px solid #2276d2 !important;
    }

    .select2-selection--multiple{
        background-color: white !important;
    }

    .btn_agregar_diag{
        color: white;
        background-color: green;
    }
    .alerta_correcto{
        position: absolute;
        z-index: 9999;
        top: 100px;
        right: 10px;
    }

    /* Presentación compacta compartida con el listado de consultas. */
    #area_trabajo .hc4-endoscopy-view {
        border-color: #c9dee8 !important;
        background: #fff !important;
    }

    #area_trabajo .hc4-endoscopy-view > .box-header:not(.hc4-standard-module-header) {
        position: relative;
        display: block !important;
        min-height: 62px;
        padding: 10px 16px !important;
        background: linear-gradient(105deg,#07547e 0%,#087eae 68%,#16aaa5 100%) !important;
    }

    #area_trabajo .hc4-endoscopy-view > .box-header.hc4-standard-module-header {
        display: grid !important;
        overflow: visible !important;
    }

    #area_trabajo .hc4-endoscopy-header-row {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin: 0 !important;
    }

    #area_trabajo .hc4-endoscopy-title-wrap {
        flex: 1 1 auto;
        width: auto;
        max-width: none;
        padding: 0 !important;
    }

    #area_trabajo .hc4-endoscopy-title {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 0 !important;
        background: transparent !important;
        font-size: 14px !important;
    }

    #area_trabajo .hc4-endoscopy-title img {
        width: 28px !important;
        height: 28px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }

    #area_trabajo .hc4-endoscopy-title-text {
        display: inline-block;
        text-transform: lowercase;
    }

    #area_trabajo .hc4-endoscopy-title-text::first-letter {
        text-transform: uppercase;
    }

    #area_trabajo .hc4-endoscopy-add-wrap {
        flex: 0 0 auto;
        width: auto;
        max-width: none;
        margin-left: 18px;
        padding: 0 !important;
    }

    #area_trabajo .hc4-endoscopy-add {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: auto !important;
        min-width: 0;
        min-height: 38px;
        margin: 0 !important;
        padding: 8px 14px !important;
        border: 1px solid rgba(255,255,255,.55) !important;
        border-radius: 9px !important;
        color: #fff !important;
        background: rgba(255,255,255,.14) !important;
        box-shadow: 0 4px 12px rgba(0,74,117,.16);
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    #area_trabajo .hc4-endoscopy-add img {
        width: 18px !important;
        height: 18px !important;
        margin: 0 !important;
        object-fit: contain;
    }

    #area_trabajo .hc4-endoscopy-view > .box-header > center {
        position: absolute;
        top: 50%;
        left: 50%;
        z-index: 1;
        display: block;
        width: auto;
        max-width: 48%;
        margin: 0;
        transform: translate(-50%,-50%);
        white-space: nowrap;
    }

    #area_trabajo .hc4-endoscopy-view > .box-header > center > .col-12 {
        width: auto;
        padding: 0 !important;
    }

    #area_trabajo .hc4-endoscopy-patient {
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        font-size: 14px !important;
    }

    #area_trabajo .hc4-endoscopy-view > .box-body {
        padding: 16px !important;
        background: #fff !important;
    }

    #area_trabajo .hc4-endoscopy-view > .box-body > .col-12 {
        width: 100%;
        max-width: none;
        padding: 0 !important;
    }

    #area_trabajo .hc4-endoscopy-list {
        width: 100%;
        height: auto !important;
        max-height: 650px;
        margin: 0 !important;
        padding: 12px 8px 12px 0 !important;
        overflow-y: auto;
        background: transparent !important;
        scrollbar-color: #0b91b8 #dceef3;
        scrollbar-width: thin;
    }

    #area_trabajo .hc4-endoscopy-list > .col-12 {
        width: 100%;
        max-width: none;
        margin: 0 !important;
        padding: 0 !important;
    }

    #area_trabajo .hc4-endoscopy-list::-webkit-scrollbar { width: 7px; }
    #area_trabajo .hc4-endoscopy-list::-webkit-scrollbar-track {
        border-radius: 10px;
        background: #dceef3 !important;
        box-shadow: none !important;
    }
    #area_trabajo .hc4-endoscopy-list::-webkit-scrollbar-thumb {
        border-radius: 10px;
        background: linear-gradient(#087eae,#16aaa5) !important;
    }

    #area_trabajo .hc4-endoscopy-record {
        width: 100%;
        margin: 0 0 8px !important;
        border-radius: 12px !important;
    }

    #area_trabajo .hc4-endoscopy-record-header {
        display: block !important;
        padding: 12px !important;
        text-align: left !important;
        border: 0 !important;
        color: #fff !important;
        background: linear-gradient(100deg, #075f87 0%, #0789b4 55%, #19b5ac 100%) !important;
    }

    #area_trabajo .hc4-endoscopy-record-header .hc4-procedure-summary,
    #area_trabajo .hc4-endoscopy-record-header .hc4-procedure-summary div,
    #area_trabajo .hc4-endoscopy-record-header .hc4-procedure-summary span,
    #area_trabajo .hc4-endoscopy-record-header .hc4-procedure-summary b {
        color: #fff !important;
    }

    #area_trabajo .hc4-procedure-summary {
        display: grid !important;
        grid-template-areas: "date procedure doctor actions";
        grid-template-columns:
            minmax(160px, .95fr)
            minmax(220px, 1.65fr)
            minmax(150px, .8fr)
            minmax(79px, auto);
        align-items: center;
        column-gap: 16px;
        row-gap: 12px;
        width: 100%;
        margin: 0 !important;
    }

    #area_trabajo .hc4-procedure-summary > [class*="col-"] {
        flex: 0 1 auto;
        width: auto;
        max-width: 100%;
        min-width: 0;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        text-align: left;
        line-height: 1.5;
    }

    #area_trabajo .hc4-procedure-summary > :nth-child(1) { grid-area: date; }
    #area_trabajo .hc4-procedure-summary > :nth-child(2) { grid-area: procedure; }
    #area_trabajo .hc4-procedure-summary > :nth-child(3) { grid-area: doctor; }
    #area_trabajo .hc4-procedure-summary > :nth-child(4) { grid-area: actions; }

    #area_trabajo .hc4-procedure-summary .titulo_pro {
        display: inline !important;
        margin: 0 !important;
        color: #fff;
        font-size: inherit;
    }

    #area_trabajo .hc4-procedure-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        justify-self: end;
        gap: 7px;
        flex: 0 0 auto !important;
        width: max-content !important;
        min-width: 79px;
        max-width: none !important;
        margin: 0 !important;
    }

    #area_trabajo .hc4-procedure-actions .hc4-procedure-delete,
    #area_trabajo .hc4-procedure-actions .hc4-procedure-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 36px;
        width: 36px !important;
        min-width: 36px;
        height: 36px !important;
        padding: 0 !important;
        border-radius: 9px !important;
    }

    #area_trabajo .hc4-procedure-actions .hc4-procedure-delete {
        border: 1px solid rgba(255,255,255,.35) !important;
        color: #fff !important;
        background: #c74655 !important;
    }

    #area_trabajo .hc4-procedure-actions .hc4-procedure-delete span,
    #area_trabajo .hc4-procedure-id {
        display: none !important;
    }

    #area_trabajo .hc4-procedure-actions .hc4-procedure-toggle {
        border: 1px solid rgba(255,255,255,.35) !important;
        color: #fff !important;
        background: rgba(255,255,255,.13) !important;
    }

    #area_trabajo .hc4-content-header-row {
        display: grid !important;
        grid-template-columns: minmax(96px, 112px) minmax(0, 1fr) minmax(96px, 112px);
        align-items: center;
        gap: 10px;
        width: 100%;
        min-height: 56px;
        margin: 0 !important;
        padding: 7px 12px !important;
    }

    #area_trabajo .hc4-content-header-row::before,
    #area_trabajo .hc4-evolution-section-header::before,
    #area_trabajo .hc4-evolution-section-header::after,
    #area_trabajo .hc4-evolution-item-header::before,
    #area_trabajo .hc4-evolution-item-header::after {
        content: none !important;
        display: none !important;
    }

    #area_trabajo .hc4-content-header-row > [class*="col-"] {
        float: none;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #area_trabajo .hc4-content-edit,
    #area_trabajo .hc4-content-left-action,
    #area_trabajo .hc4-content-delete {
        position: static !important;
        width: auto !important;
        transform: none !important;
    }

    #area_trabajo .hc4-content-edit,
    #area_trabajo .hc4-content-left-action {
        grid-column: 1;
        justify-self: start;
    }

    #area_trabajo .hc4-content-delete {
        grid-column: 3;
        justify-self: end;
    }

    #area_trabajo .hc4-content-mode {
        grid-column: 3;
        justify-self: end;
    }

    #area_trabajo .hc4-content-title {
        grid-column: 2;
        width: 100% !important;
        max-width: 100%;
        text-align: center !important;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
        white-space: normal;
        overflow-wrap: break-word;
    }

    #area_trabajo .hc4-content-header-row::after {
        display: block !important;
        grid-column: 3;
        width: 1px;
        content: '' !important;
    }

    #area_trabajo .hc4-content-header-row .hc4-endoscopy-inline-action,
    #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-inline-action {
        position: static !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: auto !important;
        min-width: 88px;
        min-height: 40px;
        margin: 0 !important;
        padding: 8px 11px !important;
        border: 1px solid rgba(255,255,255,.4) !important;
        border-radius: 9px !important;
        color: #fff !important;
        background: rgba(255,255,255,.14) !important;
        line-height: 1 !important;
        white-space: nowrap;
        transform: none !important;
    }

    #area_trabajo .hc4-procedure-mode-switch {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;
        margin: 0;
        color: #fff;
        cursor: pointer;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    #area_trabajo .hc4-procedure-detail-panel .hc4-content-header-row {
        grid-template-columns: minmax(96px, 112px) minmax(0, 1fr) minmax(245px, 285px) !important;
    }

    #area_trabajo .hc4-procedure-mode-switch__label {
        display: inline-block;
        line-height: 1.15;
        white-space: nowrap;
    }

    #area_trabajo .hc4-procedure-mode-switch input {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
        pointer-events: none;
    }

    #area_trabajo .hc4-procedure-mode-switch__track {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 23px;
        border: 1px solid rgba(255,255,255,.58);
        border-radius: 20px;
        background: rgba(255,255,255,.2);
        transition: background .2s ease;
    }

    #area_trabajo .hc4-procedure-mode-switch__track::after {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0,55,78,.3);
        content: '';
        transition: transform .2s ease;
    }

    #area_trabajo .hc4-procedure-mode-switch input:checked + .hc4-procedure-mode-switch__track {
        background: #21c7ae;
    }

    #area_trabajo .hc4-procedure-mode-switch input:checked + .hc4-procedure-mode-switch__track::after {
        transform: translateX(19px);
    }

    #area_trabajo .hc4-procedure-media-row {
        display: flex !important;
        flex-wrap: wrap;
    }

    #area_trabajo .hc4-procedure-media-row > .hc4-procedure-detail-panel {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 10px !important;
    }

    #area_trabajo .hc4-procedure-images-panel {
        display: none;
    }

    #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-detail-panel {
        flex: 0 0 66.666667% !important;
        max-width: 66.666667% !important;
        width: 66.666667% !important;
        padding-right: 5px !important;
    }

    #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-images-panel {
        display: block;
        flex: 0 0 33.333333% !important;
        max-width: 33.333333% !important;
        width: 33.333333% !important;
        padding: 0 10px 0 5px !important;
    }

    #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-link-panel {
        display: none;
    }

    #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode .hc4-procedure-link-edit-section {
        display: none !important;
    }

    #area_trabajo .hc4-procedure-link-panel {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        width: 100%;
        padding: 12px 10px 0 !important;
    }

    #area_trabajo .hc4-procedure-images-panel .hc4-content-header-row {
        grid-template-columns: minmax(88px, 100px) minmax(150px, 1fr) !important;
    }

    #area_trabajo .hc4-procedure-images-panel .hc4-content-header-row::after {
        display: none !important;
    }

    #area_trabajo .hc4-procedure-images-panel .hc4-content-title {
        min-width: 150px;
        white-space: normal !important;
        word-break: keep-all !important;
        overflow-wrap: normal !important;
    }

    #area_trabajo .hc4-procedure-link-card {
        padding: 13px 14px;
        border: 1px solid #c9e0e8;
        border-radius: 9px;
        background: #f6fbfd;
    }

    #area_trabajo .hc4-procedure-link-label {
        display: block;
        margin: 0 0 7px;
        color: #315d73;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    #area_trabajo .hc4-procedure-link-controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 7px;
    }

    #area_trabajo .hc4-procedure-link-empty {
        flex: 1 1 auto;
        min-width: 0;
        color: #78909e;
        font-size: 12px;
    }

    #area_trabajo .hc4-procedure-link-action {
        display: inline-flex;
        flex: 0 0 32px;
        align-items: center;
        justify-content: center;
        width: 32px;
        min-width: 32px;
        height: 32px;
        padding: 0;
        border: 1px solid #0b8eaf;
        border-radius: 5px;
        color: #16778e;
        background: #fff;
        font-size: 13px;
    }

    #area_trabajo .hc4-procedure-link-view {
        display: inline-block;
        flex: 1 1 auto;
        min-width: 0;
        margin: 0;
        color: #087fa7;
        font-size: 12px;
        overflow-wrap: anywhere;
        text-decoration: underline;
    }

    #area_trabajo .hc4-content-header-row .hc4-endoscopy-inline-action i,
    #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-inline-action i {
        position: static !important;
        margin: 0 !important;
        transform: none !important;
    }

    #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-inline-action--danger {
        width: 40px !important;
        min-width: 40px;
        height: 40px;
        padding: 0 !important;
        background: #c74655 !important;
    }

    #area_trabajo .hc4-evolution-section-header,
    #area_trabajo .hc4-evolution-item-header {
        display: grid !important;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
        width: 100%;
        margin: 0 !important;
    }

    #area_trabajo .hc4-evolution-section-header {
        grid-template-columns: minmax(170px, 210px) minmax(0, 1fr) 48px;
        min-height: 64px;
        padding: 8px 14px !important;
        color: #fff !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        border-bottom: 0 !important;
    }

    #area_trabajo .hc4-evolution-add {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 42px;
        padding: 8px 12px;
        border: 1px solid #0b8bab;
        border-radius: 9px;
        color: #fff;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%);
        font-family: 'Helvetica general3';
        font-size: 12px;
        font-weight: 700;
        line-height: 1.15;
        white-space: nowrap;
    }

    #area_trabajo .hc4-evolution-add img {
        width: 20px;
        height: 20px;
        object-fit: contain;
    }

    #area_trabajo .hc4-evolution-section-title,
    #area_trabajo .hc4-evolution-item-title {
        min-width: 0;
        text-align: center;
        font-family: 'Helvetica general3';
        font-weight: 700;
        line-height: 1.25;
    }

    #area_trabajo .hc4-evolution-section-title {
        font-size: 16px;
        color: #fff;
    }

    #area_trabajo .hc4-evolution-collapse {
        position: static !important;
        float: none !important;
        justify-self: end;
        margin: 0 !important;
    }

    #area_trabajo .hc4-evolution-collapse .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        margin: 0 !important;
        border-radius: 9px;
    }

    #area_trabajo .hc4-evolution-item-header {
        grid-template-columns: minmax(154px, 170px) minmax(0, 1fr) minmax(154px, 170px) !important;
        min-height: 56px;
        padding: 7px 12px !important;
        color: #fff !important;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
        border-bottom: 0 !important;
    }

    #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-action-wrap {
        position: static !important;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
    }

    #area_trabajo .hc4-evolution-item-header > .hc4-content-edit {
        grid-column: 1 !important;
        grid-row: 1 !important;
        justify-self: start !important;
    }

    #area_trabajo .hc4-evolution-item-header > .hc4-evolution-item-title {
        grid-column: 2 !important;
        grid-row: 1 !important;
        align-self: center;
    }

    #area_trabajo .hc4-evolution-item-header > .hc4-content-delete {
        grid-column: 3 !important;
        grid-row: 1 !important;
        justify-self: end !important;
    }

    #area_trabajo .hc4-evolution-item-actions {
        display: flex !important;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        width: auto !important;
        white-space: nowrap;
    }

    #area_trabajo .hc4-evolution-item-toggle {
        display: inline-flex !important;
        position: static !important;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        width: 40px !important;
        min-width: 40px !important;
        height: 40px !important;
        min-height: 40px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 1px solid rgba(255,255,255,.4) !important;
        border-radius: 9px !important;
        color: #fff !important;
        background: rgba(255,255,255,.14) !important;
        font-family: Arial,sans-serif !important;
        font-size: 23px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        box-shadow: none !important;
    }

    #area_trabajo .hc4-evolution-item-toggle span {
        display: block !important;
        position: static !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1 !important;
        transform: none !important;
    }

    #area_trabajo .hc4-evolution-item-body[hidden] {
        display: none !important;
    }

    #area_trabajo .hc4-evolution-item-title {
        grid-column: 2;
        font-size: 14px;
        color: #fff;
    }

    #area_trabajo .hc4-evolution-item-spacer {
        width: 82px;
    }

    #area_trabajo .hc4-biopsy-table {
        width: 100% !important;
        margin: 0 !important;
        table-layout: fixed;
    }

    #area_trabajo .hc4-biopsy-section {
        display: none !important;
    }

    #area_trabajo .hc4-biopsy-table thead {
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%) !important;
    }

    #area_trabajo .hc4-biopsy-table thead th {
        border-color: #087eae !important;
        color: #fff !important;
        background: transparent !important;
    }

    #area_trabajo .hc4-biopsy-table th,
    #area_trabajo .hc4-biopsy-table td {
        overflow-wrap: anywhere;
        vertical-align: middle !important;
    }

    #area_trabajo .hc4-biopsy-table th:first-child,
    #area_trabajo .hc4-biopsy-table th:last-child,
    #area_trabajo .hc4-biopsy-table td:first-child,
    #area_trabajo .hc4-biopsy-table td:last-child {
        text-align: center;
    }

    @media (min-width: 768px) and (max-width: 1199px) {
        #area_trabajo .hc4-procedure-summary {
            grid-template-areas:
                "date actions"
                "procedure actions"
                "doctor actions";
            grid-template-columns: minmax(0, 1fr) minmax(79px, auto);
            column-gap: 12px;
        }
    }

    @media (max-width: 767px) {
        #area_trabajo .hc4-endoscopy-view > .box-header:not(.hc4-standard-module-header) {
            min-height: 0;
        }
        #area_trabajo .hc4-endoscopy-header-row {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
        }
        #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-detail-panel,
        #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-images-panel {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 10px 10px !important;
        }
        #area_trabajo .hc4-procedure-link-controls {
            align-items: stretch;
            flex-wrap: wrap;
        }
        #area_trabajo .hc4-endoscopy-add-wrap {
            width: 100%;
            margin-left: 0;
        }
        #area_trabajo .hc4-endoscopy-add {
            width: 100% !important;
        }
        #area_trabajo .hc4-endoscopy-view > .box-header > center {
            position: static;
            max-width: 100%;
            margin-top: 10px;
            transform: none;
            white-space: normal;
        }
        #area_trabajo .hc4-procedure-summary {
            grid-template-areas:
                "date"
                "procedure"
                "doctor"
                "actions";
            grid-template-columns: minmax(0, 1fr);
            align-items: start;
            gap: 8px;
        }
        #area_trabajo .hc4-procedure-actions {
            justify-self: start;
        }
        #area_trabajo .hc4-content-header-row {
            display: grid !important;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 8px;
            min-height: 44px;
            padding: 4px 6px !important;
        }
        #area_trabajo .hc4-content-edit,
        #area_trabajo .hc4-content-left-action,
        #area_trabajo .hc4-content-delete {
            position: static;
            transform: none;
        }
        #area_trabajo .hc4-content-edit,
        #area_trabajo .hc4-content-left-action { grid-column: 1; }
        #area_trabajo .hc4-content-title { grid-column: 2; }
        #area_trabajo .hc4-content-delete { grid-column: 3; }
        #area_trabajo .hc4-evolution-section-header {
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 8px;
            padding: 7px 8px !important;
        }
        #area_trabajo .hc4-evolution-item-header {
            grid-template-columns: minmax(86px,auto) minmax(0,1fr) minmax(86px,auto) !important;
            gap: 8px;
            padding: 7px 8px !important;
        }
        #area_trabajo .hc4-evolution-add span,
        #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-inline-action span {
            display: none;
        }
        #area_trabajo .hc4-evolution-add {
            width: 42px;
            min-width: 42px;
            padding: 8px;
        }
        #area_trabajo .hc4-evolution-section-title {
            font-size: 13px;
        }
        #area_trabajo .hc4-evolution-item-actions { gap: 6px; }
    }
</style>
@php
    $ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
@endphp
<script type="text/javascript">
    function activar_detalle_endoscopico(){
        if(typeof hc4NavegacionClinica !== 'undefined'){
            hc4NavegacionClinica.proximoNivel = 'detalle';
            hc4NavegacionClinica.nivelActual = 'detalle';
        }

        $('.hc4-patient-detail-page')
            .removeClass('hc4-clinical-list')
            .addClass('hc4-clinical-detail');
    }

    function editarprocedimiento(id, id_paciente){
        activar_detalle_endoscopico();
        $.ajax({
            type: "GET",
            url: "{{route('editar.procedimiento_endoscopico')}}/"+id+'/'+id_paciente,
            data: "",
            datatype: "html",
            success: function(datahtml){
                $("#procedimiento"+id).html(datahtml);
                if(typeof activarVistaClinica === 'function'){
                    activarVistaClinica();
                }
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    function eliminar_evolucion(id_evolucion, id_procedimiento){
        var opcion = confirm("Estas Seguro que deseas borrar la evolucion?");
        if (opcion == true) {
            $.ajax({
                type: "DELETE",
                url: "{{ url('inicio/procedimientos/evolucion/eliminar') }}/"+encodeURIComponent(id_evolucion),
                data: {'_token': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                success: function(){
                    var evolucion = document.getElementById('evolucion_eliminar'+id_evolucion);
                    if(evolucion){
                        evolucion.style.display = 'none';
                    }
                },
                error: function(xhr){
                    var mensaje = 'No se pudo eliminar la evolución.';
                    if(xhr.responseJSON && xhr.responseJSON.message){
                        mensaje = xhr.responseJSON.message;
                    }
                    alert(mensaje);
                }
            });

        }
    }
    function eliminar_procedimiento(id_procedimiento){
        var opcion = confirm("Estas Seguro que deseas borrar el procedimiento?");
        if (opcion == true) {
            $.ajax({
                type: "DELETE",
                url: "{{asset('/')}}/inicio/procedimientos/eliminar/"+id_procedimiento,
                data: {'_token': '{{ csrf_token() }}'},
                datatype: "html",
                success: function(){
                    document.getElementById( 'procedimiento_eliminado'+id_procedimiento ).style.display = 'none';
                },
                error:  function(){
                    alert('error al cargar');
                }
            });

        }
    }
</script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
    var remoto_href = '';
    jQuery('body').on('click', '[data-toggle="modal"]', function() {
        if(remoto_href != jQuery(this).data('remote')) {
            remoto_href = jQuery(this).data('remote');
            jQuery(jQuery(this).data('target')).removeData('bs.modal');

            jQuery(jQuery(this).data('target')).find('.modal-body').empty();
            jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
        }
    });
    var hc4EditorImagenesActivo = null;

    function alternar_modo_imagenes(control, id_protocolo){
        var fila = $('#hc4_procedure_media_'+id_protocolo);
        var activarImagenes = control.checked === true;
        fila.toggleClass('hc4-procedure-image-mode', activarImagenes);
    }

    function copiar_link_imagenes(control){
        var enlace = $(control).closest('.hc4-procedure-link-row').find('.hc4-procedure-link-view').first();
        var url = $.trim(enlace.attr('href') || '');
        if(!url){ return; }

        if(navigator.clipboard && navigator.clipboard.writeText){
            navigator.clipboard.writeText(url).catch(function(){ copiar_link_imagenes_compatibilidad(url); });
        }else{
            copiar_link_imagenes_compatibilidad(url);
        }
    }

    function copiar_link_imagenes_compatibilidad(url){
        var temporal = $('<textarea readonly></textarea>').val(url).appendTo('body');
        temporal[0].select();
        document.execCommand('copy');
        temporal.remove();
    }

    window.actualizarImagenesProcedimientoTrasEliminar = function(archivo){
        var selector = '[data-hc4-image-id="' + archivo.id + '"]';

        $(selector).each(function(){
            var elemento = $(this);
            var item = elemento.hasClass('hc4-procedure-image-item')
                ? elemento
                : elemento.closest('.hc4-procedure-image-item');
            (item.length ? item : elemento).remove();
        });

        if(archivo.nombre){
            $('img').filter(function(){
                var origen = $(this).attr('src') || '';
                return origen.indexOf('/' + archivo.nombre) !== -1;
            }).each(function(){
                var elemento = $(this);
                var item = elemento.closest('.hc4-procedure-image-item');
                if(!item.length){
                    item = elemento.closest('.col-6');
                }
                (item.length ? item : elemento).remove();
            });
        }

        if(Number(hc4EditorImagenesActivo) === Number(archivo.id_protocolo)){
            editar_imagenes(archivo.id_protocolo);
        }
    };

    function cerrarEditorImagenes(id_protocolo){
        if(typeof window.detenerEditorImagenesProcedimiento === 'function'){
            window.detenerEditorImagenesProcedimiento();
            window.detenerEditorImagenesProcedimiento = null;
        }

        var contenedor = $('#editor_imagenes_procedimiento'+id_protocolo);
        contenedor.empty().hide();
        $('.hc4-procedure-summary-'+id_protocolo).show();
        var fila = $('#hc4_procedure_media_'+id_protocolo);
        if(fila.hasClass('hc4-procedure-image-mode')){
            fila.find('.hc4-procedure-link-panel').hide();
        }else{
            fila.find('.hc4-procedure-images-panel').hide();
        }

        hc4EditorImagenesActivo = null;
    }

    function editar_imagenes(id_protocolo) {
      var contenedor = $('#editor_imagenes_procedimiento'+id_protocolo);
      if(!contenedor.length){
        alert('No se encontró el contenedor de imágenes del procedimiento.');
        return;
      }

      if(hc4EditorImagenesActivo !== null && hc4EditorImagenesActivo !== id_protocolo){
        cerrarEditorImagenes(hc4EditorImagenesActivo);
      }

      if(hc4EditorImagenesActivo === id_protocolo && typeof window.detenerEditorImagenesProcedimiento === 'function'){
        window.detenerEditorImagenesProcedimiento();
        window.detenerEditorImagenesProcedimiento = null;
      }

      hc4EditorImagenesActivo = id_protocolo;
      $('.hc4-procedure-summary-'+id_protocolo).hide();
      contenedor.show();
      contenedor.html('<div class="text-center" style="padding:30px"><i class="fa fa-spinner fa-spin"></i> Cargando editor de imágenes...</div>');

      $.ajax({
        type: 'GET',
        url: "{{ url('inicio/procedimientos/endoscopicos/imagenes/editor') }}/"+encodeURIComponent(id_protocolo),
        dataType: 'html',
        success: function(datahtml){
          contenedor.html(datahtml);
        },
        error: function(xhr){
          cerrarEditorImagenes(id_protocolo);
          var mensaje = 'No se pudo cargar el editor de imágenes.';
          if(xhr.responseJSON && xhr.responseJSON.message){
            mensaje = xhr.responseJSON.message;
          }
          alert(mensaje);
        }
      });
    }
    function agregar_procedimiento(){
        contador = parseInt($('#contador_endoscopicos').val());
        hc_endoscopico = parseInt($('#id_endoscopico').val());
        hc_funcional = parseInt($('#id_funcional').val());
        var url_datos = "";
        if(contador>0){
            agregar_procedimiento_hc('endoscopico_id');
        }else if (hc_endoscopico >0) {

            url_datos = "{{asset('/procedimiento2/selecciona2/')}}/0/{{$paciente->id}}/"+hc_endoscopico;
            agregar_procedimiento_tipo(url_datos);
            //console.log(url_datos);
        }else if (hc_funcional >0) {
            //alert(hc_funcional);
            url_datos = "{{asset('/procedimiento2/selecciona2/')}}/0/{{$paciente->id}}/"+hc_funcional;
            agregar_procedimiento_tipo(url_datos);
        }else{
          $.ajax({
                async: true,
                type: "GET",
                url: "{{route('hc4_procedimiento.selecciona_procedimiento',['tipo' => '0', 'paciente' => $paciente->id ])}}",
                data: "",
                datatype: "html",
                success: function(datahtml){
                    $("#area_trabajo").html(datahtml);
                },
                error:  function(){
                    alert('error al cargar');
                }
            });
        }
        //alert("agregar");
    }


    function editarprocedimiento2(id){
        var entra = id;
            $.ajax({
            type: "GET",
            url: "{{route('editar.procedimiento_endoscopico2')}}/"+id,
            data: "",
            datatype: "html",
            success: function(datahtml, entra){
                $("#procedimiento"+id).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    function editar_evolucion(id, id_paciente){
        var entra = id;
            $.ajax({
            type: "GET",
            url: "{{route('editar.procedimiento_evolucion')}}/"+id+'/'+id_paciente,
            data: "",
            datatype: "html",
            success: function(datahtml, entra){
                $("#evolucion"+id).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
    function alternar_detalle_evolucion_endoscopica(boton){
        var tarjeta = boton.closest('.box');
        var detalle = tarjeta ? tarjeta.querySelector('.hc4-evolution-item-body') : null;
        var simbolo = boton.querySelector('span');

        if(!detalle || !simbolo){
            return;
        }

        var estaExpandido = boton.getAttribute('aria-expanded') === 'true';
        detalle.hidden = estaExpandido;
        boton.setAttribute('aria-expanded', estaExpandido ? 'false' : 'true');
        boton.setAttribute('title', estaExpandido ? 'Maximizar detalle' : 'Minimizar detalle');
        simbolo.textContent = estaExpandido ? '+' : '−';
    }
    function agregar_evolucion(id, id_evolucion){
        var entra = id;
        $.ajax({
            type: "POST",
            url: "{{route('agregar.procedimiento_evolucion')}}/"+id,
            data: {'_token': '{{ csrf_token() }}'},
            datatype: "html",
            success: function(datahtml, entra){
                anterior  = $("#evolucion_agregar"+id).html();
                $("#evolucion_agregar"+id).html(datahtml+anterior);

            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }

    function cargar_tabla(id){
        $.ajax({
                url:"{{route('epicrisis.cargar22')}}/"+id,
                dataType: "json",
                type: 'get',
                success: function(data){
                    //console.log(data);
                    var table = document.getElementById("tdiagnostico"+id);

                    $.each(data, function (index, value) {

                        var row = table.insertRow(index);
                        row.id = 'tdiag'+value.id;
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+value.cie10+'</b>';
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.pre_def;
                        var cell3 = row.insertCell(2);
                        cell3.innerHTML = value.descripcion;
                        var cell4 = row.insertCell(3);
                        cell4.innerHTML = value.ingreso_egreso;
                        var cell5 = row.insertCell(4);
                        cell5.innerHTML = '<a href="javascript:eliminar('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                    });

                }
            })
    }


    function cargar_tabla_biopsias(id){

        $.ajax({
                url:"{{route('biopsias.carga_prescripcion')}}/"+id,
                dataType: "json",
                type: 'get',
                success: function(data){
                    //console.log(data);
                    var table = document.getElementById("tbiopsia"+id);

                    $.each(data, function (index, value) {

                        var row = table.tBodies[0].insertRow(-1);
                        row.id = 'tbio'+value.id;
                        var cell1 = row.insertCell(0);
                        cell1.innerHTML = '<b>'+'Fco'+' '+value.numero_frasco+':'+'</b>';
                        var cell2 = row.insertCell(1);
                        cell2.innerHTML = value.descripcion;
                        if(value.observacion != null){
                            var cell3 = row.insertCell(2);
                            cell3.innerHTML = '<b>'+'Obs:'+' '+value.observacion+' '+'</b>';
                        }else{
                            var cell3 = row.insertCell(2);
                            cell3.innerHTML = value.observacion;
                        }

                        var cell4 = row.insertCell(3);
                        cell4.innerHTML = '<a href="javascript:eliminar_biopsia('+value.id+', '+id+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash" ></span></a>';

                    });
                }

        })

    }

    function eliminar(id_h, id){
        var i = document.getElementById('tdiag'+id_h).rowIndex;
        document.getElementById("tdiagnostico"+id).deleteRow(i);

        $.ajax({
          type: 'delete',
          url:"{{url('cie10/eliminar')}}/"+id_h,  //epicrisis.eliminar
          data: {'_token': '{{ csrf_token() }}'},
          datatype: 'json',

          success: function(data){

          },
          error: function(data){

          }
        });
    }


    //Elimina la Prescripcion Biopsias
    function eliminar_biopsia(id_h, id){

        var i = document.getElementById('tbio'+id_h).rowIndex;
        document.getElementById("tbiopsia"+id).deleteRow(i);

        $.ajax({
          type: 'delete',
          url:"{{url('hc4_prescripcion_biopsia/eliminar')}}/"+id_h,  //Biopsias.eliminar
          data: {'_token': '{{ csrf_token() }}'},
          datatype: 'json',

          success: function(data){

          },
          error: function(data){

          }
        });

    }


    function guardar_biopsia_frasco(id_procedimiento, id_hc, div1, div2){
        $.ajax({
            type: 'post',
            url:"{{route('procedimiento.agregar_biopsia')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'hcid': id_hc,'hc_id_procedimiento': id_procedimiento,'descripcion_frasco': $("#descripcion_frasco"+id_procedimiento).val(),'detalle_observacion': $("#detalle_observacion"+id_procedimiento).val(),'id_paciente': '{{$paciente->id}}' },
            success: function(data){

                editarprocedimiento(id_procedimiento, '{{$paciente->id}}');

                console.log(data);
                var table = document.getElementById("tbiopsia"+id_procedimiento);
                var row = table.tBodies[0].insertRow(-1);
                row.id = 'tbio'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+'Fco'+' '+data.secuencia+':'+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.biopsias;

                if(data.observacion != null){
                  var cell3 = row.insertCell(2);
                  cell3.innerHTML = '<b>'+'Obs:'+' '+data.observacion+' '+'</b>';
                }else{
                  var cell3 = row.insertCell(2);
                  cell3.innerHTML = data.observacion;
                }
                  var cell4 = row.insertCell(3);
                  cell4.innerHTML = '<a href="javascript:eliminar_biopsia('+data.id+', '+id_procedimiento+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';
                $('#'+div1).val('');
                $('#'+div2).val('');


             },
            error: function(data){

            }
        });
    }

    function cargar_cuad_diag(id_proc,variable_tiempo){

        $.ajax({
          type: 'post',
          url:"{{route('obtener.cuadclin_diagnostico')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          //data: $("#id_proced"+id_proc).serialize(),
          data: {'id_proced':id_proc},
          success: function(data){
            //console.log(data);
            tinyMCE.get('cuad_clinico'+id_proc+variable_tiempo).setContent(data.evol_cuadro_clinico);
            tinyMCE.get('diagnos'+id_proc+variable_tiempo).setContent(data.texto);

            //$('#cuad_clinico'+id_proc).val(data.evol_cuadro_clinico);
            //$('#diagnos'+id_proc).val(data.texto);

            //$("#evolucion_agregar"+id).html(datahtml+anterior);

          },
          error: function(data){
            console.log(data);
          }
        });

    }

    function actualiza_cuadclinico_biopsia(id_proc){

        $.ajax({
          type: 'post',
          url:"{{route('update.cuad_biopsia')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_biopsia"+id_proc).serialize(),
          success: function(data){
            //console.log(data);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);
          },
          error: function(data){
            console.log(data);
          }
        });

    }

    function actualiza_diagnost_biopsia(id_proc){

        $.ajax({
          type: 'post',
          url:"{{route('update.diag_biopsia')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#frm_biopsia"+id_proc).serialize(),
          success: function(data){
            //console.log(data);
            $("#alerta_datos").fadeIn(1000);
            $("#alerta_datos").fadeOut(3000);
          },
          error: function(data){
            console.log(data);
          }
        });

    }


    function guardar_cie10_PRO(id_procedimiento, id_hc){
        //alert($("#pre_def").val());

        $.ajax({
            type: 'post',
            url:"{{route('procedimiento.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo"+id_procedimiento).val(), 'pre_def': $("#pre_def"+id_procedimiento).val(),'hcid': id_hc, 'hc_id_procedimiento': id_procedimiento, 'in_eg': $("#ing_egr"+id_procedimiento).val(), 'id_paciente': '{{$paciente->id}}'  },
            success: function(data){


                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico"+id_procedimiento);
                var row = table.insertRow(indexr);
                row.id = 'tdiag'+data.id;
                var cell1 = row.insertCell(0);
                cell1.innerHTML = '<b>'+data.cie10+'</b>';
                var cell2 = row.insertCell(1);
                cell2.innerHTML = data.pre_def;
                var cell3 = row.insertCell(2);
                cell3.innerHTML = data.descripcion;
                var cell4 = row.insertCell(3);
                cell4.innerHTML = data.in_eg;
                var cell5 = row.insertCell(4);
                cell5.innerHTML = '<a href="javascript:eliminar('+data.id+', '+id_procedimiento+');" class="btn btn-xs btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span></a>';

            },
            error: function(data){

                }
        })
    }

    function cargar_formulario_013(id_procedimiento){
        $.ajax({
            type: "GET",
            url:"{{url('procedimientoendoscopicos_datos/formulario013/cargar')}}/" + id_procedimiento,
            data: "",
            datatype: "html",
            success: function(datahtml){
                var editorResumenClinico = tinymce.get('tresumen_clinico');
                if(editorResumenClinico){
                    editorResumenClinico.remove();
                }

                scroll_datos("#area_013"+id_procedimiento);
                $("#area_013"+id_procedimiento).html(datahtml);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
</script>
<div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  Guardado Correctamente
</div>
<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>
<div class="modal fade" id="foto2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>
<div class="modal fade" id="video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="width: 95%;">

      </div>
    </div>
</div>
<div class="modal fade hc4-procedure-file-modal" id="hc4_procedure_file_modal" tabindex="-1" role="dialog" aria-label="Vista previa del archivo">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%; max-width: 1100px;">
        <div class="modal-content"></div>
    </div>
</div>
<script>
    (function(){
        var $visorArchivos = $('.hc4-procedure-file-modal').last();
        $('.hc4-procedure-file-modal').not($visorArchivos).remove();
        $visorArchivos.appendTo(document.body);

        $(document).off('click.hc4ProcedureFile', '.hc4-procedure-file-link')
            .on('click.hc4ProcedureFile', '.hc4-procedure-file-link', function(event){
                event.preventDefault();
                var urlArchivo = $(this).data('remote');
                var $contenido = $visorArchivos.find('.modal-content');

                $contenido.html('<div class="text-center" style="padding:40px"><i class="fa fa-spinner fa-spin"></i> Cargando archivo...</div>');
                $visorArchivos.modal('show');
                $contenido.load(urlArchivo, function(response, estado){
                    if(estado === 'error'){
                        $contenido.html('<div class="alert alert-danger" style="margin:20px">No se pudo abrir el archivo.</div>');
                    }
                });
            });

        $visorArchivos.off('hidden.bs.modal.hc4ProcedureFile')
            .on('hidden.bs.modal.hc4ProcedureFile', function(){
                $(this).find('.modal-content').empty();
            });
    })();
</script>
<div class="box hc4-endoscopy-view" style="border: 2px solid #004AC1; background-color: white; ">
  @include('hc4.partials.module_header', [
      'moduleTitle' => __('hc4.procedimientos'),
      'moduleIcon' => asset('/').'hc4/img/iconos/pendo.png',
      'paciente' => $paciente,
      'actionLabel' => __('hc4.agregarProcedimiento'),
      'actionOnClick' => 'agregar_procedimiento();'
  ])
  <!-- /.box-header -->
  <div class="box-body" style="background-color: #56ABE3;">
    <div class="col-12">
        <div class="row parent hc4-endoscopy-list" >
            <span id="msn1" style="color: white; "></span>
            @foreach($procedimientos2 as $value)
                @php

                    $variable_tiempo  = rand(100000,999999);

                    if($paciente->sexo == 1){
                        $sexo = "MASCULINO";
                    }else{
                        $sexo = "FEMENINO";
                    }
                    $seguro =  \Sis_medico\Seguro::find($value->seguro_final);
                    $nombre_procedimiento = "";
                    $historia =\Sis_medico\Historiaclinica::find($value->id_hc);
                    $alergias = is_null($historia)
                        ? collect()
                        : \Sis_medico\Paciente_Alergia::where('id_paciente', $historia->id_paciente)->get();
                    if($alergias->isEmpty()){
                        $alergia = "No";
                    }else{
                        $alergia  = "";
                        foreach ($alergias as $value_alergia) {
                            $principio_activo = $value_alergia->principio_activo;
                            if(is_null($principio_activo)){
                                continue;
                            }

                            if($alergia == ""){
                                $alergia = $principio_activo->nombre;
                            }else{
                                $alergia = $alergia.", ".$principio_activo->nombre;
                            }

                        }
                        if($alergia == ""){
                            $alergia = "No";
                        }
                    }

                    $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();

                    $mas = true;
                    $nombre_procedimiento = "";

                    foreach($adicionales as $value2)
                    {
                        $procedimiento_adicional = $value2->procedimiento;
                        if(is_null($procedimiento_adicional)){
                            continue;
                        }

                        if($mas == true){
                            $nombre_procedimiento = $nombre_procedimiento.$procedimiento_adicional->nombre;
                            $mas = false;
                         }
                        else{
                            $nombre_procedimiento = $nombre_procedimiento.' + '.$procedimiento_adicional->nombre;
                         }
                    }
                    $edad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
                    $contador = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 0)->orderBy('secuencia', 'DESC')->count();
                    if($contador == 0){
                        $cuadro_clinico ="<p>PACIENTE ".$sexo." DE ".$edad." AÑOS DE EDAD <br> APP: ".$paciente->antecedentes_pat." <br> APF: ".$paciente->antecedentes_fam."<br> APQX: ".$paciente->antecedentes_quir."<br> ALERGIAS: ".$alergia."<br></p>";
                        $input = [
                            'hc_id_procedimiento' => $value->id_procedimiento,
                            'hcid' => $value->id_hc,
                            'secuencia' => 0,
                            'cuadro_clinico' => $cuadro_clinico,
                            'motivo' => 'Evolucion Pre-Operatoria',
                            'fecha_ingreso' => ' ',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ];
                        \Sis_medico\Hc_Evolucion::insert($input);
                    }
                    $contador2 = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 1)->orderBy('secuencia', 'DESC')->count();
                    if($contador2 == 0){
                        $input = [
                            'hc_id_procedimiento' => $value->id_procedimiento,
                            'hcid' => $value->id_hc,
                            'motivo' => 'Evolucion Post-Operatoria',
                            'secuencia' => 1,
                            'cuadro_clinico' => ' ',
                            'fecha_ingreso' => ' ',
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                        ];
                        \Sis_medico\Hc_Evolucion::insert($input);
                    }
                    //dd($value);
                @endphp
                <div class="col-12">
                    @php
                        if(!is_null($value->f_operacion)){
                            $fecha_r =  Date('Y-m-d',strtotime($value->f_operacion));
                        }else{
                            $fecha_r = Date('Y-m-d',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                        }
                    @endphp
                    <div id="procedimiento_eliminado{{$value->id_procedimiento}}" class="box collapsed-box hc4-endoscopy-record" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 0px; ">
                        <div class="box-header with-border hc4-endoscopy-record-header" style="background-color: white; color: black; font-family: 'Helvetica general3';border-bottom: #004AC1;">

                            @php
                                $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();
                                $mas = true;
                                $texto = "";

                                    foreach($adicionales as $value2)
                                    {
                                        if($mas == true){
                                         $texto = $texto.$value2->procedimiento->nombre  ;
                                         $mas = false;
                                         }
                                        else{
                                         $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                                         }
                                    }
                            @endphp
                            <div class="row hc4-procedure-summary">
                                <div class="col-4">
                                    @if(!is_null($value->f_operacion))
                                        @php
                                        $dia =  Date('N',strtotime($value->f_operacion));
                                        $mes =  Date('n',strtotime($value->f_operacion)); @endphp
                                        <b>
                                        @if($dia == '1') Lunes
                                             @elseif($dia == '2') Martes
                                             @elseif($dia == '3') Miércoles
                                             @elseif($dia == '4') Jueves
                                             @elseif($dia == '5') Viernes
                                             @elseif($dia == '6') Sábado
                                             @elseif($dia == '7') Domingo
                                        @endif
                                            {{substr($value->f_operacion,8,2)}} de
                                        @if($mes == '1') Enero
                                             @elseif($mes == '2') Febrero
                                             @elseif($mes == '3') Marzo
                                             @elseif($mes == '4') Abril
                                             @elseif($mes == '5') Mayo
                                             @elseif($mes == '6') Junio
                                             @elseif($mes == '7') Julio
                                             @elseif($mes == '8') Agosto
                                             @elseif($mes == '9') Septiembre
                                             @elseif($mes == '10') Octubre
                                             @elseif($mes == '11') Noviembre
                                             @elseif($mes == '12') Diciembre
                                        @endif
                                            del {{substr($value->f_operacion,0,4)}}</b>
                                    @else
                                        @php
                                        $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                        $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); @endphp
                                        <b>
                                        @if($dia == '1') Lunes
                                             @elseif($dia == '2') Martes
                                             @elseif($dia == '3') Miércoles
                                             @elseif($dia == '4') Jueves
                                             @elseif($dia == '5') Viernes
                                             @elseif($dia == '6') Sábado
                                             @elseif($dia == '7') Domingo
                                        @endif
                                            {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de
                                        @if($mes == '1') Enero
                                             @elseif($mes == '2') Febrero
                                             @elseif($mes == '3') Marzo
                                             @elseif($mes == '4') Abril
                                             @elseif($mes == '5') Mayo
                                             @elseif($mes == '6') Junio
                                             @elseif($mes == '7') Julio
                                             @elseif($mes == '8') Agosto
                                             @elseif($mes == '9') Septiembre
                                             @elseif($mes == '10') Octubre
                                             @elseif($mes == '11') Noviembre
                                             @elseif($mes == '12') Diciembre
                                        @endif
                                            del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
                                    @endif
                                </div>
                                <div class="col-4">
                                    <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
                                        <span class="titulo_pro" >@if(!is_null($texto)) {{$texto}} @endif </span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
                                        <span class="titulo_pro" >
                                        @if(!is_null($value->nombre1))
                                            {{$value->nombre1}} {{$value->apellido1}}
                                        @else
                                            @php
                                                $doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $value->hcid)->first();
                                            @endphp

                                            @if(!is_null($doc_id_hc))
                                                @if (!is_null($doc_id_hc->id_doctor1))
                                                    @php
                                                        $nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
                                                    @endphp

                                                    Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

                                                @else
                                                    @php
                                                        $doc_id_agenda = \Sis_medico\Agenda::where('id', $doc_id_hc->id_agenda)->first();

                                                        $nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
                                                    @endphp
                                                    Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
                                                @endif
                                            @endif
                                        @endif

                                        </span>
                                    </div>
                                </div>
                                <div class="col-2 hc4-procedure-actions">
                                    <button type="button" class="btn hc4-procedure-delete" onclick="eliminar_procedimiento({{$value->id_procedimiento}});" title="Eliminar procedimiento" aria-label="Eliminar procedimiento">
										<i class="fa fa-trash"></i><span class="sr-only">Eliminar</span>
                                    </button>
                                    <button type="button" class="btn hc4-procedure-toggle" data-widget="collapse" title="Mostrar u ocultar detalle">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <div class="box-body" style="background: white;">
                            <div class="row hc4-procedure-media-row" id="hc4_procedure_media_{{$value->id_protocolo}}">

                                <div class="col-lg-12 col-sm-12 col-12 endo_interno hc4-procedure-detail-panel hc4-procedure-summary-{{$value->id_protocolo}}">
                                    <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">

                                        <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
                                            <div class="row hc4-content-header-row procedure-section-header__grid">
												<div class="col-3 hc4-endoscopy-action-wrap hc4-content-edit procedure-section-header__action" style="margin-right: 10px">
													<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');">
														<i class="fa fa-pencil-square-o"></i><span>Editar</span>
													</button>
												</div>
                                                <div class="col-8 hc4-content-title procedure-section-header__title" style="text-align: center">
                                                    <span>Detalles del procedimiento</span>
                                                </div>
                                                <div class="hc4-content-mode">
                                                    <label class="hc4-procedure-mode-switch" title="Alternar entre link e imágenes cargadas">
                                                        <span class="hc4-procedure-mode-switch__label">Enlazar imágenes</span>
                                                        <input type="checkbox" onchange="alternar_modo_imagenes(this, {{$value->id_protocolo}})">
                                                        <span class="hc4-procedure-mode-switch__track" aria-hidden="true"></span>
                                                        <span class="hc4-procedure-mode-switch__label">Capturar imágenes</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="procedimiento{{$value->id_procedimiento}}" >
                                            <div class="row">
                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Procedimiento</span>
                                                </div>

                                                <div class="col-12">
                                                    <span>
                                                        {{$texto}}
                                                    </span>
                                                </div>

                                                <div class="col-12" style="padding-top: 10px">
                                                    <span style="font-family: 'Helvetica general';">Seguro</span>
                                                </div>
                                                @php
                                                    $hc_seguro = \Sis_medico\Seguro::where('id', $value->seguro_final)->first();
                                                    //dd($hc_seguro);
                                                @endphp
                                                <div class="col-12" style="padding-bottom: 10px"><span> @if(!is_null($hc_seguro)) {{$hc_seguro->nombre}} @endif</span>

                                                </div>
                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Motivo</span>
                                                </div>
                                                <div class="col-12">
                                                    <span><?php echo strip_tags($value->mprotocolo); ?></span>
                                                </div>
                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Hallazgos</span>
                                                </div>
                                                <div class="col-12">
                                                    <span><?php echo strip_tags($value->hallazgos); ?></span>
                                                </div>
                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Conclusiones</span>
                                                </div>
                                                <div class="col-12">
                                                    <span><?php if (!is_null($value->conclusion)) {echo strip_tags($value->conclusion);} else {echo ' &nbsp;&nbsp;';}?></span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">&nbsp;</span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">&nbsp;</span>
                                                </div>
                                                <div class="col-4">
                                                    <span>Dr.
                                                        @if(!is_null($value->nombre1))
                                                            {{$value->nombre1}} {{$value->apellido1}}
                                                        @else
                                                            @php
                                                                $doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $value->hcid)->first();
                                                            @endphp

                                                            @if(!is_null($doc_id_hc))
                                                                @if (!is_null($doc_id_hc->id_doctor1))
                                                                    @php
                                                                        $nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
                                                                    @endphp

                                                                    Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

                                                                @else
                                                                    @php
                                                                        $doc_id_agenda = \Sis_medico\Agenda::where('id', $doc_id_hc->id_agenda)->first();

                                                                        $nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
                                                                    @endphp
                                                                    Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 hc4-procedure-link-panel hc4-procedure-summary-{{$value->id_protocolo}}">
                                    @include('hc4.partials.procedure_image_links_view', ['variant' => 'procedure', 'copyHandler' => 'copiar_link_imagenes'])
                                </div>
                                <div class="col-lg-4 col-sm-12 col-12 endo_interno2 hc4-procedure-images-panel hc4-procedure-summary-{{$value->id_protocolo}}">
                                    <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
                                        <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
                                            <div class="row hc4-content-header-row procedure-section-header__grid">
												<div class="col-3 hc4-endoscopy-action-wrap hc4-content-edit procedure-section-header__action" style="margin-right: 10px">
													<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_imagenes({{$value->id_protocolo}})">
														<i class="fa fa-pencil-square-o"></i><span>Editar</span>
													</button>
												</div>
                                                <div class="col-8 hc4-content-title procedure-section-header__title" style="text-align: center">
                                                    <span>Imágenes del procedimiento</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                @php
                                                    $id_protocolo = $value->id_protocolo;
                                                    $imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->limit(8)->OrderBy('created_at', 'desc')->get()
                                                @endphp
                                                @foreach($imagenes as $imagen)
                                                <div class="col-md-6 col-sm-4 hc4-procedure-image-item" data-hc4-image-id="{{$imagen->id}}">
                                                    @php
                                                        $explotar = explode( '.', $imagen->nombre);
                                                        $extension = end($explotar);
                                                    @endphp
                                                    @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG') || ($extension == 'bmp') || ($extension == 'BMP'))
                                                        <a href="#" class="hc4-procedure-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
                                                        </a><br>
                                                    @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                                                        <a href="#" class="hc4-procedure-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
                                                        </a>
                                                    @elseif(($extension == 'mp4'))
                                                        <a href="#" class="hc4-procedure-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
                                                        </a>
                                                    @else
                                                        @php
                                                            $variable = explode('/' , asset('/hc_ima/'));
                                                            $d1 = $variable[3];
                                                            $d2 = $variable[4];
                                                            $d3 = $variable[5];
                                                         @endphp
                                                        <a href="#" class="hc4-procedure-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/office.png')}}" width="98%">
                                                        </a>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 hc4-images-inline-editor" id="editor_imagenes_procedimiento{{$value->id_protocolo}}" style="display: none;"></div>
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12" style="padding-top: 15px">
                                    <div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border hc4-evolution-section-header">
                                            <button type="button" class="hc4-evolution-add" onclick="agregar_evolucion({{$value->id_procedimiento}});">
                                                <img src="{{asset('/')}}hc4/img/iconos/agregar.png" alt="">
                                                <span>AGREGAR EVOLUCIÓN</span>
                                            </button>
                                            <div class="hc4-evolution-section-title">HISTORIAL DE EVOLUCIONES DEL PROCEDIMIENTO</div>
                                            <div class="box-tools hc4-evolution-collapse">
                                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="Expandir o contraer">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="box-body" style="background: white;" id="evolucion_agregar{{$value->id_procedimiento}}">
                                            @php
                                                $evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('estado', 1)->orderBy('secuencia', 'DESC')->get();

                                            @endphp
                                            @foreach($evoluciones as $evolucion)
                                            <div class="box" id="evolucion_eliminar{{$evolucion->id}}" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
                                                <div class="box-header with-border hc4-evolution-item-header">
													<div class="hc4-endoscopy-action-wrap hc4-content-edit">
															<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');">
																<i class="fa fa-pencil-square-o"></i><span>Editar</span>
															</button>
														</div>
                                                        <div class="hc4-evolution-item-title">DETALLES DE LA EVOLUCIÓN</div>
											<div class="hc4-endoscopy-action-wrap hc4-content-delete hc4-evolution-item-actions">
											<button type="button" class="btn hc4-endoscopy-inline-action hc4-endoscopy-inline-action--danger" onclick="eliminar_evolucion({{$evolucion->id}}, '{{$value->id_procedimiento}}');" title="Eliminar evolución" aria-label="Eliminar evolución">
												<i class="fa fa-trash"></i><span class="sr-only">Eliminar</span>
													</button>
													<button type="button" class="hc4-evolution-item-toggle" aria-expanded="true" title="Minimizar detalle" onclick="alternar_detalle_evolucion_endoscopica(this);">
														<span aria-hidden="true">&minus;</span>
													</button>
												</div>
                                                </div>
                                                <div class="box-body hc4-evolution-item-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <span style="font-family: 'Helvetica general';">Motivo</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span>{{$evolucion->motivo}}</span>
                                                        </div>
                                                        <div class="col-12">&nbsp;</div>
                                                        <div class="col-12">
                                                            <span style="font-family: 'Helvetica general';">Detalle</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span><?php echo $evolucion->cuadro_clinico; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="height: 5px;"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Diagnostico CIE10</b></span>
                                            <div class="pull-right box-tools">
                                                <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                                    <i class="fa fa-plus"></i></button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <div class="box-body" style="background: white;" >
                                            <form id="frm_cie{{$value->id_procedimiento}}">
                                                <div class="col-12">
                                                    <div class="row">

                                                        <input type="hidden" name="codigo" id="codigo{{$value->id_procedimiento}}">
                                                        <label for="cie10" class="col-lg-12 control-label" style="padding-left: 0px" ><b>Diagnóstico</b></label>

                                                        <div class="form-group col-lg-6 col-sm-12 " style="padding-left: 0px">
                                                            <input id="cie10{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" >
                                                        </div>
                                                        <div class="form-group col-lg-2 col-sm-4 " style="padding: 1px;">
                                                            <select id="pre_def{{$value->id_procedimiento}}" name="pre_def" class="form-control input-sm" required>
                                                                <option value="">Seleccione ...</option>
                                                                <option value="PRESUNTIVO">PRESUNTIVO</option>
                                                                <option value="DEFINITIVO">DEFINITIVO</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-lg-2 col-sm-4 " style="padding: 1px;">
                                                            <select id="ing_egr{{$value->id_procedimiento}}" name="pre_def" class="form-control input-sm" required>
                                                                <option value="">Seleccione ...</option>
                                                                <option value="INGRESO">INGRESO</option>
                                                                <option value="EGRESO">EGRESO</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 " style="padding: 1px; padding-top: 3px">
                                                            <button type="button" id="bagregar{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                                        </div>


                                                        <div class="form-group col-md-12" >
                                                            <table id="tdiagnostico{{$value->id_procedimiento}}" class="table table-striped" style="font-size: 12px;">

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Receta</b></span>
                                            <div class="pull-right box-tools">
                                                <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili" onclick="crear_receta('{{$value->id_procedimiento}}')">
                                                    <i class="fa fa-plus"></i></button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        @php $hc_receta = Sis_medico\hc_receta::where('id_hc',$historia->hcid)->first(); @endphp
                                        <div class="box-body" style="background: white;" >
                                            
                                           <div class="" id="receta{{$value->id_procedimiento}}"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--Orden Biopsias-->
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12 hc4-biopsy-section">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Biopsias Dra. Gilbert</b></span>
                                            <div class="pull-right box-tools">
                                                <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                                    <i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body" style="background: white;">
                                            <form id="frm_biopsia{{$value->id_procedimiento}}">
                                                <input type="hidden" name="id_hc_proc" value="{{$value->id_procedimiento}}">
                                                <div class="col-12">
                                                    <div class="row">
                                                        <input type="hidden" name="codigo_biop" id="codigo_biop{{$value->id_procedimiento}}">
                                                        <div class="col-md-3 col-sm-3 col-4" style="padding-left: 0px">
                                                          <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('imprimir.biopsias_hc4', ['id' => $value->id_procedimiento,'id_hcid' => $value->id_hc,'id_doct' => $value->id]) }}">
                                                          <div class="col-md-12" style="text-align: center;">
                                                            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                                              <div class="class="col-12">
                                                                <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                              </div>
                                                              <div class="col-md-6 col-sm-6 col-7">
                                                                <label style="font-size: 14px; ">Imprimir</label>
                                                              </div>
                                                            </div>
                                                          </div>
                                                          </a>
                                                        </div>
                                                        <div class="col-12" style="height: 8px;"></div>
                                                        <label for="pre" class="col-md-12 control-label" style="padding-left: 0px" ><b>Detalle</b></label>
                                                        <div class="form-group col-md-8 col-sm-12 " style="padding-left: 0px">
                                                           <input id="descripcion_frasco{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="descripcion_frasco" value="{{old('descripcion_frasco')}}" required placeholder="Detalle Frasco">
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 " style="padding: 1px;">
                                                        </div>
                                                        <div class="form-group col-md-2 col-sm-4 " style="padding: 1px; padding-top: 3px">
                                                            <button onclick="guardar_biopsia_frasco('{{$value->id_procedimiento}}', '{{$value->id_hc}}', 'descripcion_frasco{{$value->id_procedimiento}}', 'detalle_observacion{{$value->id_procedimiento}}');" id="bagregar_frasco{{$value->id_procedimiento}}" type="button" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                                        </div>
                                                        <label for="observacion" class="col-md-12 control-label" style="padding-left: 0px" ><b>Observación</b></label>
                                                        <div class="form-group col-md-8 col-sm-12 " style="padding-left: 0px">
                                                           <input id="detalle_observacion{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="detalle_observacion" value="{{old('detalle_observacion')}}" placeholder="Detalle Observación">
                                                        </div>
                                                        <div class="form-group col-md-12" >
                                                            <table id="tbiopsia{{$value->id_procedimiento}}" class="table table-striped hc4-biopsy-table" style="font-size: 12px;">
                                                                <colgroup>
                                                                    <col style="width: 10%;">
                                                                    <col style="width: 40%;">
                                                                    <col style="width: 40%;">
                                                                    <col style="width: 10%;">
                                                                </colgroup>
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Detalle</th>
                                                                        <th>Observación</th>
                                                                        <th>Acción</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-5">
                                                        </div>
                                                        <div class="col-md-7">
                                                            <a class="btn btn-info btn_ordenes" style="color: white; height: 100%;width: 80%" onClick="cargar_cuad_diag({{$value->id_procedimiento}},{{$variable_tiempo}});">
                                                                <div class="col-12">
                                                                    <label style="font-size: 16px;">Insertar Cuadro Cl&iacute;nico / Diagnostico</label>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-12">
                                                          <span style="font-family: 'Helvetica general';font-size: 12px">CUADRO CLINICO:</span>
                                                          <div id="cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}" style="width: 100%; border: 2px solid #004AC1;">@if(!is_null($value->cuadro_clinico))<?php echo $value->cuadro_clinico ?>@endif
                                                          </div>
                                                          <input type="hidden" name="cua_cl" id="cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}" >
                                                        </div>
                                                        <div class="col-md-12">
                                                            <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
                                                            <div id="diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}" style="width: 100%; border: 2px solid #004AC1;">@if(!is_null($value->diagnosticos))<?php echo $value->diagnosticos ?>@endif
                                                            </div>
                                                            <input type="hidden" name="diag" id="diag{{$value->id_procedimiento}}{{$variable_tiempo}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                 <!--Formulario 013-->
                                 <div class="col-12">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Formulario 013</b></span>
                                            <div class="pull-right box-tools">
                                                <button  type="button" class="btn btn-info btn-sm" onclick="cargar_formulario_013('{{$value->id_procedimiento}}')" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                                    <i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body" style="background: white;">
                                            <form id="frm_013{{$value->id_procedimiento}}">
                                                <input type="hidden" name="id_hc_proc" value="{{$value->id_procedimiento}}">
                                                <div id="area_013{{$value->id_procedimiento}}" class="col-12">
                                                    
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 hc4-biopsy-section">
                                    <div class="box box-primary collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Biopsias Dr. Torres</b></span>
                                            <div class="pull-right box-tools">
                                                <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="open_biopsias_ptv{{$value->id_procedimiento}}" onclick="ver_listado_ordenes_ptv('{{$value->id_procedimiento}}')">
                                                    <i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body" style="background: white;">
                                            <div class="col-12">
                                                <div class="row">

                                                    <div class="col-12" style="padding-left: 0px">
                                                        @php $tipos_biopsia = Sis_medico\Hc4_Tipo_Biopsias_Ptv::where('estado',1)->get(); @endphp
                                                        @foreach($tipos_biopsia as $tipo_biopsia)
                                                            <button class="btn btn-success btn-sm" onclick="crear_biopsia_ptv('{{$tipo_biopsia->id}}','{{$value->id_procedimiento}}')">{{$tipo_biopsia->descripcion}}</button>
                                                        @endforeach
                                                    </div>
                                                    <div class="col-12" id="div_biopsias_ptv{{$value->id_procedimiento}}" style="padding: 0;">

                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12">
                                    <div class="box box-primary @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Observación Administrativa</b></span>
                                            <div class="pull-right box-tools">
                                                <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                                    <i class="fa fa-plus"></i></button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <div class="box-body" style="background: white;" >
                                            @php
                                                $log_agenda = Sis_medico\Log_Agenda::where('id_agenda',$value->id_agenda)->where('descripcion','not like','%DESPLA%')->get();
                                                $cont = 0;
                                            @endphp
                                            @foreach($log_agenda as $log)
                                                    @if($cont == '0')
                                                        <p> {{$log->observaciones_ant}} {{$log->observaciones}}</p>
                                                    @else
                                                        <p> {{$log->observaciones}}</p>
                                                    @endif
                                                    @php $cont ++; @endphp


                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script type="text/javascript">

                    cargar_tabla_biopsias({{$value->id_procedimiento}});
                    cargar_tabla({{$value->id_procedimiento}});


                    $("#cie10{{$value->id_procedimiento}}").autocomplete({
                        source: function( request, response ) {

                            $.ajax({
                                url:"{{route('epicrisis.cie10_nombre')}}",
                                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                                data: {
                                    term: request.term
                                      },
                                dataType: "json",
                                type: 'post',
                                success: function(data){
                                    response(data);

                                }
                            })
                        },
                        minLength: 2,
                    } );

                    $("#cie10{{$value->id_procedimiento}}").change( function(){
                        $.ajax({
                            type: 'post',
                            url:"{{route('epicrisis.cie10_nombre2')}}",
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: 'json',
                            data: $("#cie10{{$value->id_procedimiento}}"),
                            success: function(data){
                                //console.log(data);
                                if(data!='0'){
                                    $('#codigo{{$value->id_procedimiento}}').val(data.id);
                                }

                            },
                            error: function(data){

                                }
                        })
                    });


                    $('#bagregar{{$value->id_procedimiento}}').click( function(){
                        if($('#pre_def{{$value->id_procedimiento}}').val()!='' ){
                            if($('#ing_egr{{$value->id_procedimiento}}').val()!='' ){
                                guardar_cie10_PRO({{$value->id_procedimiento}}, {{$value->id_hc}});
                                $('#ing_egr{{$value->id_procedimiento}}').val('');
                            }else{
                                alert("Seleccione Ingreso o Egreso");
                            }
                        }else{
                            alert("Seleccione Presuntivo o Definitivo");
                        }
                        $('#codigo{{$value->id_procedimiento}}').val('');
                        $('#cie10{{$value->id_procedimiento}}').val('');
                    });


                    tinymce.init({
                      selector: '#cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}',
                      inline: true,
                      menubar: false,
                      invalid_elements : 'table,tr,td',
                      content_style: ".mce-content-body {font-size:14px;}",

                      setup: function (editor) {
                          editor.on('init', function (e) {

                            var ed = tinyMCE.get('cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());


                          });
                      },

                      init_instance_callback: function (editor) {
                          editor.on('Change', function (e) {
                            var ed = tinyMCE.get('cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                            actualiza_cuadclinico_biopsia({{$value->id_procedimiento}})

                          });
                      }
                    });


                    tinymce.init({
                      selector: '#diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}',
                      inline: true,
                      menubar: false,
                      invalid_elements : 'table,tr,td',
                      content_style: ".mce-content-body {font-size:14px;}",

                      setup: function (editor) {
                          editor.on('init', function (e) {

                            var ed = tinyMCE.get('diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#diag{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());


                          });
                      },

                      init_instance_callback: function (editor) {
                          editor.on('Change', function (e) {
                            var ed = tinyMCE.get('diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#diag{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                            actualiza_diagnost_biopsia({{$value->id_procedimiento}})


                          });
                      }
                    });

                </script>

            @endforeach

            @foreach($procedimientos1 as $value)
                @php
                    $variable_tiempo  = rand(100000,999999);
                @endphp
                <div class="col-12">
                    <div class="box collapsed-box hc4-endoscopy-record" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                        <div class="box-header with-border hc4-endoscopy-record-header" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                            <div class="row hc4-procedure-summary">
                                <div class="col-4">
                                    @if(!is_null($value->f_operacion))
                                        @php
                                        $dia =  Date('N',strtotime($value->f_operacion));
                                        $mes =  Date('n',strtotime($value->f_operacion)); @endphp
                                        <b>
                                        @if($dia == '1') Lunes
                                             @elseif($dia == '2') Martes
                                             @elseif($dia == '3') Miércoles
                                             @elseif($dia == '4') Jueves
                                             @elseif($dia == '5') Viernes
                                             @elseif($dia == '6') Sábado
                                             @elseif($dia == '7') Domingo
                                        @endif
                                            {{substr($value->f_operacion,8,2)}} de
                                        @if($mes == '1') Enero
                                             @elseif($mes == '2') Febrero
                                             @elseif($mes == '3') Marzo
                                             @elseif($mes == '4') Abril
                                             @elseif($mes == '5') Mayo
                                             @elseif($mes == '6') Junio
                                             @elseif($mes == '7') Julio
                                             @elseif($mes == '8') Agosto
                                             @elseif($mes == '9') Septiembre
                                             @elseif($mes == '10') Octubre
                                             @elseif($mes == '11') Noviembre
                                             @elseif($mes == '12') Diciembre
                                        @endif
                                            del {{substr($value->f_operacion,0,4)}}</b>
                                    @else
                                        @php
                                        $dia =  Date('N',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini));
                                        $mes =  Date('n',strtotime(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini)); @endphp
                                        <b>
                                        @if($dia == '1') Lunes
                                             @elseif($dia == '2') Martes
                                             @elseif($dia == '3') Miércoles
                                             @elseif($dia == '4') Jueves
                                             @elseif($dia == '5') Viernes
                                             @elseif($dia == '6') Sábado
                                             @elseif($dia == '7') Domingo
                                        @endif
                                            {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,8,2)}} de
                                        @if($mes == '1') Enero
                                             @elseif($mes == '2') Febrero
                                             @elseif($mes == '3') Marzo
                                             @elseif($mes == '4') Abril
                                             @elseif($mes == '5') Mayo
                                             @elseif($mes == '6') Junio
                                             @elseif($mes == '7') Julio
                                             @elseif($mes == '8') Agosto
                                             @elseif($mes == '9') Septiembre
                                             @elseif($mes == '10') Octubre
                                             @elseif($mes == '11') Noviembre
                                             @elseif($mes == '12') Diciembre
                                        @endif
                                            del {{substr(\Sis_medico\Agenda::where('id', $value->id_agenda)->first()->fechaini,0,4)}}</b>
                                    @endif
                                </div>
                                <div class="col-4">
                                    <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
                                        <span style="font-size: 12px"> {{$value->nombre}} </span>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
                                        <span style="font-size: 12px">
                                        @if(!is_null($value->nombre1))
                                            {{$value->nombre1}} {{$value->apellido1}}
                                        @else
                                            @php
                                                $doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $value->hcid)->first();
                                            @endphp

                                            @if(!is_null($doc_id_hc))
                                                @if (!is_null($doc_id_hc->id_doctor1))
                                                    @php
                                                        $nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
                                                    @endphp

                                                    Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

                                                @else
                                                    @php
                                                        $doc_id_agenda = \Sis_medico\Agenda::where('id', $doc_id_hc->id_agenda)->first();

                                                        $nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
                                                    @endphp
                                                    Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
                                                @endif
                                            @endif
                                        @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-2 hc4-procedure-actions">
                                    <button type="button" class="btn hc4-procedure-toggle" data-widget="collapse" title="Mostrar u ocultar detalle" aria-label="Mostrar u ocultar detalle">
                                    <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <div class="box-body" style="background: white;">
                            <div class="row hc4-procedure-media-row" id="hc4_procedure_media_{{$value->id_protocolo}}">
                                <div class="col-md-12 col-sm-12 col-12 hc4-procedure-detail-panel hc4-procedure-summary-{{$value->id_protocolo}}" style="margin-bottom: 5px">
                                    <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
                                        <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
                                            <div class="row hc4-content-header-row procedure-section-header__grid">
												<div class="col-3 hc4-endoscopy-action-wrap hc4-content-edit procedure-section-header__action" style="margin-right: 10px">
													<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');">
														<i class="fa fa-pencil-square-o"></i><span>Editar</span>
													</button>
												</div>
                                                <div class="col-8 hc4-content-title procedure-section-header__title" style="text-align: center">
                                                    <span>Detalles del procedimiento</span>
                                                </div>
                                                <div class="hc4-content-mode">
                                                    <label class="hc4-procedure-mode-switch" title="Alternar entre link e imágenes cargadas">
                                                        <span class="hc4-procedure-mode-switch__label">Enlazar imágenes</span>
                                                        <input type="checkbox" onchange="alternar_modo_imagenes(this, {{$value->id_protocolo}})">
                                                        <span class="hc4-procedure-mode-switch__track" aria-hidden="true"></span>
                                                        <span class="hc4-procedure-mode-switch__label">Capturar imágenes</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="procedimiento{{$value->id_procedimiento}}">
                                            <div class="row">
                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Procedimiento</span>
                                                </div>
                                                <div class="col-12">
                                                    <span>
                                                        {{$value->nombre}}
                                                    </span>
                                                </div>
                                                <div class="col-12">&nbsp;</div>

                                                <div class="col-12" style="padding-top: 10px">
                                                    <span style="font-family: 'Helvetica general';">Seguro</span>
                                                </div>
                                                @php
                                                    $hc_seguro = \Sis_medico\Seguro::where('id', $value->seguro_final)->first();
                                                @endphp
                                                <div class="col-12" style="padding-bottom: 10px"><span> @if(!is_null($hc_seguro)) {{$hc_seguro->nombre}} @endif</span>

                                                </div>

                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Hallazgos</span>
                                                </div>
                                                <div class="col-12">
                                                    <span><?php echo strip_tags($value->hallazgos); ?></span>
                                                </div>
                                                <div class="col-12">&nbsp;</div>
                                                <div class="col-12">
                                                    <span style="font-family: 'Helvetica general';">Conclusiones</span>
                                                </div>
                                                <div class="col-12">
                                                    <span><?php if (!is_null($value->conclusion)) {echo strip_tags($value->conclusion);} else {echo ' &nbsp;&nbsp;';}?></span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">M&eacute;dico Examinador</span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">&nbsp;</span>
                                                </div>
                                                <div class="col-4">
                                                    <span style="font-family: 'Helvetica general';">&nbsp;</span>
                                                </div>
                                                <div class="col-4">
                                                    <span>Dr.
                                                        @if(!is_null($value->nombre1))
                                                            {{$value->nombre1}} {{$value->apellido1}}
                                                        @else
                                                            @php
                                                                $doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $value->hcid)->first();
                                                            @endphp

                                                            @if(!is_null($doc_id_hc))
                                                                @if (!is_null($doc_id_hc->id_doctor1))
                                                                    @php
                                                                        $nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
                                                                    @endphp

                                                                    Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

                                                                @else
                                                                    @php
                                                                        $doc_id_agenda = \Sis_medico\Agenda::where('id', $doc_id_hc->id_agenda)->first();

                                                                        $nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
                                                                    @endphp
                                                                    Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 hc4-procedure-link-panel hc4-procedure-summary-{{$value->id_protocolo}}">
                                    @include('hc4.partials.procedure_image_links_view', ['variant' => 'procedure', 'copyHandler' => 'copiar_link_imagenes'])
                                </div>
                                <div class="col-md-4 col-sm-12 col-12 hc4-procedure-images-panel hc4-procedure-summary-{{$value->id_protocolo}}">
                                    <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
                                        <div class="box-header with-border" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
                                            <div class="row hc4-content-header-row procedure-section-header__grid">
												<div class="col-3 hc4-endoscopy-action-wrap hc4-content-edit procedure-section-header__action" style="margin-right: 10px">
													<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_imagenes({{$value->id_protocolo}})">
														<i class="fa fa-pencil-square-o"></i><span>Editar</span>
													</button>
												</div>
                                                <div class="col-8 hc4-content-title procedure-section-header__title" style="text-align: center">
                                                    <span>Imágenes del procedimiento</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                @php
                                                    $id_protocolo = $value->id_protocolo;
                                                    $imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->limit(8)->OrderBy('created_at', 'desc')->get()
                                                @endphp
                                                @foreach($imagenes as $imagen)
                                                <div class="col-md-6 col-sm-4 hc4-procedure-image-item" data-hc4-image-id="{{$imagen->id}}">
                                                    @php
                                                        $explotar = explode( '.', $imagen->nombre);
                                                        $extension = end($explotar);
                                                    @endphp
                                                    @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG') || ($extension == 'bmp') || ($extension == 'BMP'))
                                                        <a href="#" class="hc4-procedure-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
                                                        </a><br>
                                                    @elseif(($extension == 'pdf') || ($extension == 'PDF'))
                                                        <a href="#" class="hc4-procedure-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
                                                        </a>
                                                    @elseif(($extension == 'mp4'))
                                                        <a href="#" class="hc4-procedure-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
                                                        </a>
                                                    @else
                                                        @php
                                                            $variable = explode('/' , asset('/hc_ima/'));
                                                            $d1 = $variable[3];
                                                            $d2 = $variable[4];
                                                            $d3 = $variable[5];
                                                         @endphp
                                                        <a href="#" class="hc4-procedure-file-link" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
                                                            <img style="margin-bottom: 4px;"   src="{{asset('imagenes/office.png')}}" width="98%">
                                                        </a>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 hc4-images-inline-editor" id="editor_imagenes_procedimiento{{$value->id_protocolo}}" style="display: none;"></div>
                                <div class="col-12" style="padding-top: 15px">
                                    <div class="box box-primary collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border hc4-evolution-section-header">
                                            <button type="button" class="hc4-evolution-add" onclick="agregar_evolucion({{$value->id_procedimiento}});">
                                                <img src="{{asset('/')}}hc4/img/iconos/agregar.png" alt="">
                                                <span>AGREGAR EVOLUCIÓN</span>
                                            </button>
                                            <div class="hc4-evolution-section-title">HISTORIAL DE EVOLUCIONES DEL PROCEDIMIENTO</div>
                                            <div class="box-tools hc4-evolution-collapse">
                                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="Expandir o contraer">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="box-body" style="background: white;" id="evolucion_agregar{{$value->id_procedimiento}}">
                                            @php
                                                $evoluciones = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->orderBy('secuencia', 'DESC')->get();
                                            @endphp
                                            @foreach($evoluciones as $evolucion)
                                            <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
                                                <div class="box-header with-border hc4-evolution-item-header">
													<div class="hc4-endoscopy-action-wrap hc4-content-edit">
															<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');">
																<i class="fa fa-pencil-square-o"></i><span>Editar</span>
															</button>
														</div>
                                                        <div class="hc4-evolution-item-title">DETALLES DE LA EVOLUCIÓN</div>
											<div class="hc4-endoscopy-action-wrap hc4-content-delete hc4-evolution-item-actions">
												<button type="button" class="hc4-evolution-item-toggle" aria-expanded="true" title="Minimizar detalle" onclick="alternar_detalle_evolucion_endoscopica(this);">
													<span aria-hidden="true">&minus;</span>
												</button>
											</div>
                                                </div>
                                                <div class="box-body hc4-evolution-item-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <span style="font-family: 'Helvetica general';">Motivo</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span>{{$evolucion->motivo}}</span>
                                                        </div>
                                                        <div class="col-12">&nbsp;</div>
                                                        <div class="col-12">
                                                            <span style="font-family: 'Helvetica general';">Detalle</span>
                                                        </div>
                                                        <div class="col-12">
                                                            <span><?php echo $evolucion->cuadro_clinico; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="height: 5px;"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12">
                                    <div class="box box-primary collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Diagnostico CIE10</b></span>
                                            <div class="pull-right box-tools">
                                                <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                                    <i class="fa fa-plus"></i></button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <div class="box-body" style="background: white;" >
                                            <form id="frm_cie{{$value->id_procedimiento}}">
                                            <div class="col-12">
                                                <div class="row">
                                                    <input type="hidden" name="codigo" id="codigo{{$value->id_procedimiento}}">
                                                    <label for="cie10" class="col-md-12 control-label" style="padding-left: 0px" ><b>Diagnóstico</b></label>
                                                    <div class="form-group col-md-6 col-sm-12 " style="padding-left: 0px">
                                                        <input id="cie10{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="cie10" value="{{old('cie10')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Diagnóstico" >
                                                    </div>
                                                    <div class="form-group col-md-2 col-sm-4 " style="padding: 1px;">
                                                        <select id="pre_def{{$value->id_procedimiento}}" name="pre_def" class="form-control input-sm" required>
                                                            <option value="">Seleccione ...</option>
                                                            <option value="PRESUNTIVO">PRESUNTIVO</option>
                                                            <option value="DEFINITIVO">DEFINITIVO</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-2 col-sm-4 " style="padding: 1px;">
                                                        <select id="ing_egr{{$value->id_procedimiento}}" name="pre_def" class="form-control input-sm" required>
                                                            <option value="">Seleccione ...</option>
                                                            <option value="INGRESO">INGRESO</option>
                                                            <option value="EGRESO">EGRESO</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-2 col-sm-4 " style="padding: 1px; padding-top: 3px">
                                                        <button type="button" id="bagregar{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
                                                    </div>

                                                    <div class="form-group col-md-12" >
                                                        <table id="tdiagnostico{{$value->id_procedimiento}}" class="table table-striped" style="font-size: 12px;">

                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!--Orden Biopsias-->
                                <div class="col-12" style="height: 5px;"></div>
                                <div class="col-12 hc4-biopsy-section">
                                    <div class="box box-primary collapsed-box " style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
                                        <div class="box-header with-border" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
                                            <span> <b>Biopsias</b></span>
                                            <div class="pull-right box-tools">
                                                <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                                                    <i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="box-body" style="background: white;">
                                            <form id="frm_biopsia{{$value->id_procedimiento}}">
                                                <input type="hidden" name="id_hc_proc" value="{{$value->id_procedimiento}}">
                                                <div class="col-12">
                                                    <div class="row">
                                                        <input type="hidden" name="codigo_biop" id="codigo_biop{{$value->id_procedimiento}}">
                                                        <div class="col-md-3 col-sm-3 col-4" style="padding-left: 0px">
                                                          <a target="_blank" class="btn btn-info btn_accion"  style=" width: 100%; height: 100%" href="{{ route('imprimir.biopsias_hc4', ['id' => $value->id_procedimiento,'id_hcid' => $value->id_hc,'id_doct' => $value->id]) }}">
                                                          <div class="col-md-12" style="text-align: center;">
                                                            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                                                              <div class="class="col-12">
                                                                <img style="" width="20px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
                                                              </div>
                                                              <div class="col-md-6 col-sm-6 col-7">
                                                                <label style="font-size: 14px; ">Imprimir</label>
                                                              </div>
                                                            </div>
                                                          </div>
                                                          </a>
                                                        </div>
                                                        <div class="col-12" style="height: 8px;"></div>
                                                        <label for="pre" class="col-md-12 control-label" style="padding-left: 0px" ><b>Detalle</b></label>

                                                        <div class="form-group col-md-8 col-sm-12" style="padding-left: 0px">
                                                           <input id="descripcion_frasco{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="descripcion_frasco" value="{{old('descripcion_frasco')}}" required placeholder="Detalle Frasco">
                                                        </div>

                                                        <div class="form-group col-md-2 col-sm-4" style="padding: 1px;">
                                                        </div>

                                                        <div class="form-group col-md-2 col-sm-4 " style="padding: 1px; padding-top: 3px">
                                                            <button onclick="guardar_biopsia_frasco('{{$value->id_procedimiento}}', '{{$value->id_hc}}', 'descripcion_frasco{{$value->id_procedimiento}}', 'detalle_observacion{{$value->id_procedimiento}}');" type="button" id="bagregar_frasco{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus">Agregar</span></button>
                                                        </div>

                                                        <label for="observacion" class="col-md-12 control-label" style="padding-left: 0px" ><b>Observación</b></label>

                                                        <div class="form-group col-md-8 col-sm-12 " style="padding-left: 0px">
                                                           <input id="detalle_observacion{{$value->id_procedimiento}}" type="text" class="form-control input-sm"  name="detalle_observacion" value="{{old('detalle_observacion')}}" placeholder="Detalle Observación">
                                                        </div>

                                                        <div class="form-group col-md-12" >
                                                            <table id="tbiopsia{{$value->id_procedimiento}}" class="table table-striped hc4-biopsy-table" style="font-size: 12px;">
                                                                <colgroup>
                                                                    <col style="width: 10%;">
                                                                    <col style="width: 40%;">
                                                                    <col style="width: 40%;">
                                                                    <col style="width: 10%;">
                                                                </colgroup>
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Detalle</th>
                                                                        <th>Observación</th>
                                                                        <th>Acción</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                            <p id="demo"></p>
                                                        </div>

                                                        <div class="col-md-5"></div>
                                                        <div class="col-md-7">
                                                            <a class="btn btn-info btn_ordenes" style="color: white; height: 100%;width: 80%" onClick="cargar_cuad_diag({{$value->id_procedimiento}},{{$variable_tiempo}});">
                                                                <div class="col-12">
                                                                    <label style="font-size: 16px;">Insertar Cuadro Cl&iacute;nico / Diagnostico</label>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-12">
                                                          <span style="font-family: 'Helvetica general';font-size: 12px">CUADRO CLINICO:</span>
                                                          <div id="cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}" style="width: 100%; border: 2px solid #004AC1;">@if(!is_null($value->cuadro_clinico))<?php echo $value->cuadro_clinico ?>@endif
                                                          </div>
                                                          <input type="hidden" name="cua_cl" id="cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}" >
                                                        </div>
                                                        <div class="col-md-12">
                                                            <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
                                                            <div id="diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}" style="width: 100%; border: 2px solid #004AC1;">@if(!is_null($value->diagnosticos))<?php echo $value->diagnosticos ?>@endif
                                                            </div>
                                                            <input type="hidden" name="diag" id="diag{{$value->id_procedimiento}}{{$variable_tiempo}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script type="text/javascript">

                    cargar_tabla_biopsias({{$value->id_procedimiento}});
                    cargar_tabla({{$value->id_procedimiento}});

                    $("#cie10{{$value->id_procedimiento}}").autocomplete({
                        source: function( request, response ) {

                            $.ajax({
                                url:"{{route('epicrisis.cie10_nombre')}}",
                                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                                data: {
                                    term: request.term
                                      },
                                dataType: "json",
                                type: 'post',
                                success: function(data){
                                    response(data);
                                    //console.log(data);

                                }
                            })
                        },
                        minLength: 2,
                    } );

                    $("#cie10{{$value->id_procedimiento}}").change( function(){
                        $.ajax({
                            type: 'post',
                            url:"{{route('epicrisis.cie10_nombre2')}}",
                            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                            datatype: 'json',
                            data: $("#cie10{{$value->id_procedimiento}}"),
                            success: function(data){
                                if(data!='0'){
                                    $('#codigo{{$value->id_procedimiento}}').val(data.id);
                                }

                            },
                            error: function(data){

                                }
                        })
                    });

                    $('#bagregar{{$value->id_procedimiento}}').click( function(){
                        if($('#pre_def{{$value->id_procedimiento}}').val()!='' ){
                            if($('#ing_egr{{$value->id_procedimiento}}').val()!='' ){
                                guardar_cie10_PRO({{$value->id_procedimiento}}, {{$value->id_hc}});
                                $('#ing_egr{{$value->id_procedimiento}}').val('');
                            }else{
                                alert("Seleccione Ingreso o Egreso");
                            }
                        }else{
                            alert("Seleccione Presuntivo o Definitivo");
                        }
                        $('#codigo{{$value->id_procedimiento}}').val('');
                        $('#cie10{{$value->id_procedimiento}}').val('');
                    });


                    tinymce.init({
                      selector: '#cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}',
                      inline: true,
                      menubar: false,
                      invalid_elements : 'table,tr,td',
                      content_style: ".mce-content-body {font-size:14px;}",

                      setup: function (editor) {
                          editor.on('init', function (e) {

                            var ed = tinyMCE.get('cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());


                          });
                      },

                      init_instance_callback: function (editor) {
                          editor.on('Change', function (e) {
                            var ed = tinyMCE.get('cuad_clinico{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#cua_cl{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                            actualiza_cuadclinico_biopsia({{$value->id_procedimiento}})

                          });
                      }
                    });


                    tinymce.init({
                      selector: '#diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}',
                      inline: true,
                      menubar: false,
                      invalid_elements : 'table,tr,td',
                      content_style: ".mce-content-body {font-size:14px;}",

                      setup: function (editor) {
                          editor.on('init', function (e) {

                            var ed = tinyMCE.get('diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#diag{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());


                          });
                      },

                      init_instance_callback: function (editor) {
                          editor.on('Change', function (e) {
                            var ed = tinyMCE.get('diagnos{{$value->id_procedimiento}}{{$variable_tiempo}}');
                            $('#diag{{$value->id_procedimiento}}{{$variable_tiempo}}').val(ed.getContent());
                            actualiza_diagnost_biopsia({{$value->id_procedimiento}})


                          });
                      }
                    });

                </script>
            @endforeach
        </div>
    </div>
  </div>
  <!-- box-footer -->
</div>

<script type="text/javascript">
    function ver_listado_ordenes_ptv( hc_id_proc){
        $.ajax({
            url:"{{url('ordenes_biopsia_ptv/index')}}/"+ hc_id_proc,
            dataType: "html",
            type: 'get',
            data: "",
            success: function(data){
                $('#div_biopsias_ptv'+hc_id_proc).empty().html(data);
                //console.log(data);

            },
            error:  function(){
                alert('error al cargar');
            }
        })
    }

    function crear_biopsia_ptv(tipo , hc_id_proc){
        $.ajax({
            url:"{{url('ordenes_biopsia_ptv/crear')}}/"+ tipo + "/" + hc_id_proc,
            dataType: "json",
            type: 'post',
            data: {'_token': '{{ csrf_token() }}'},
            success: function(data){
                ver_listado_ordenes_ptv(hc_id_proc);
                //console.log(data);

            },
            error:  function(){
                alert('error al cargar');
            }
        })
    }

    function editar_orden_ptv( id_orden_ptv ){
        $.ajax({
            url:"{{url('ordenes_biopsia_ptv/editar')}}/"+ id_orden_ptv,
            dataType: "html",
            type: 'get',
            success: function(data){
                $('#detalle_biopsia_ptv'+id_orden_ptv).empty().html(data);
                //console.log(data);
                $('#open_biopsias_ptv_2'+id_orden_ptv).click();

            },
            error:  function(){
                alert('error al cargar');
            }
        })
    }

    function guardar_orden_biopsia_ptv(id){

        $.ajax({
            type: 'post',
            url:"{{route('ordenbiopsiasptv.update')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_editar_biopsia_ptv"+id).serialize(),
            success: function(data){
              //console.log(data);
                $("#alerta_datos").fadeIn(1000);
                $("#alerta_datos").fadeOut(3000);

            },
            error: function(data){

                }
        })

    }

    function eliminar_orden_ptv( id_orden_ptv , hc_id_proc ){

        var confirmar = confirm("Desea eliminar la orden de biopsia");
        if(confirmar){
            $.ajax({
                url:"{{url('ordenes_biopsia_ptv/eliminar')}}/"+ id_orden_ptv,
                dataType: "html",
                type: 'delete',
                data: {'_token': '{{ csrf_token() }}'},
                success: function(data){

                    ver_listado_ordenes_ptv(hc_id_proc);

                },
                error:  function(){
                    alert('error al cargar');
                }
            })
        }

    }

    function crear_receta(id){

        $.ajax({
                url:"{{url('receta_008_inicio')}}/"+ id,
                dataType: "html",
                type: 'get',
                success: function(data){

                    $('#receta'+id).empty().html(data);
                    
                },
                error:  function(){
                    alert('error al cargar');
                }
            })    

    }
</script>
