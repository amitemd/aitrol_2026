@extends('hc4.base')
@section('action-content')


<style type="text/css">

  .boton-1{
    font-size: 10px ;
    width: 20%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
   }

   .boton-2{
    font-size: 10px ;
    width: 60%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
   }

   .color{
    font-size: 12px;
    color: #004AC1;
   }
   .color2{
    color: white !important;
    background-color:  #004AC1 !important;
   }
   .titulo{
    font-family: 'Helvetica general' !important;
    border-bottom:  solid 1px black !important;
   }
   .oculto{
    display: none;
   }

   .plantilla{
      padding-top: 14px;
   }

   .hc4-dashboard-actions .boton {
      display: block;
      width: 100%;
   }

   .hc4-dashboard-shell {
      padding: 0 18px;
   }

   .hc4-dashboard-surface {
      padding: 0 !important;
      border: 1px solid #d5e6ee;
      border-radius: 18px;
      background: linear-gradient(135deg, #fafdff 0%, #edf7fb 48%, #e1f1f4 100%) !important;
      box-shadow: 0 12px 30px rgba(0, 83, 126, .08);
   }

   .hc4-dashboard-surface .boton {
      position: relative;
      overflow: hidden;
      border: 1px solid #dce8ed;
      background-color: #fff !important;
      background-image: linear-gradient(145deg, #fff 0%, #f8fcfd 100%) !important;
      box-shadow: 0 4px 12px rgba(0, 74, 117, .06);
      cursor: pointer;
      transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
   }

   .hc4-dashboard-surface .boton::after {
      content: '';
      position: absolute;
      inset: auto 0 0;
      height: 3px;
      background: var(--hc4-module-color, #087eae);
      opacity: 0;
      transition: opacity .18s ease;
   }

   .hc4-dashboard-surface .boton:hover,
   .hc4-dashboard-surface .boton:focus {
      border-color: var(--hc4-module-color, #b8aee0);
      box-shadow: 0 12px 24px rgba(0, 91, 137, .14);
      outline: none;
      transform: translateY(-3px);
   }

   .hc4-dashboard-surface .boton:hover::after,
   .hc4-dashboard-surface .boton:focus::after {
      opacity: 1;
   }

   .hc4-dashboard-surface .calendario {
      border-color: #087eae;
      background-image: linear-gradient(90deg, rgba(5,61,104,.98) 0%, rgba(0,111,185,.92) 48%, rgba(18,164,180,.34) 100%), url("{{asset('/')}}hc4/img/btn-ca.png") !important;
      background-position: center;
   }

   .hc4-dashboard-surface .calendario,
   .hc4-dashboard-surface .calendario span,
   .hc4-dashboard-surface .calendario p,
   .hc4-dashboard-surface .calendario b {
      color: #fff !important;
   }

   .hc4-dashboard-surface .calendario hr {
      background-color: rgba(255,255,255,.34) !important;
   }

   .hc4-dashboard-surface .calendario > .row > .col-12:first-child {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: .02em;
   }

   .hc4-dashboard-surface .calendario > .row > .col-12:first-child img {
      filter: brightness(0) invert(1) drop-shadow(0 3px 4px rgba(0,0,0,.14));
   }

   .hc4-dashboard-actions .boton {
      display: flex;
      align-items: center;
      justify-content: center;
   }

   .hc4-dashboard-actions .boton > .row {
      width: min(100%, 340px);
      margin: auto !important;
      justify-content: center;
   }

   .hc4-dashboard-actions .boton img {
      max-width: 82px !important;
      max-height: 82px;
      object-fit: contain;
   }

   .hc4-dashboard-actions .boton p {
      margin-bottom: 6px;
   }

   .hc4-dashboard-actions .hc4-module-description {
      margin: 3px 0 0 !important;
      color: #6b8290 !important;
      font-size: 12px !important;
      font-weight: 400;
      line-height: 1.35 !important;
   }

   .hc4-dashboard-actions .boton b {
      font-weight: 700;
   }

   .hc4-dashboard-actions .boton,
   .hc4-dashboard-actions .boton div,
   .hc4-dashboard-actions .boton p,
   .hc4-dashboard-actions .boton span {
      color: #244b61 !important;
   }

   [data-hc4-accent="agenda"],
   [data-hc4-accent="horario"],
   [data-hc4-accent="pacientes"],
   [data-hc4-accent="laboratorio"],
   [data-hc4-accent="examenes"],
   [data-hc4-accent="medicinas"],
   [data-hc4-accent="plantillas"] {
      --hc4-module-color: #3f63c9;
      --hc4-module-soft: #eaf0ff;
      --hc4-icon-filter: hue-rotate(5deg) saturate(1.18);
   }

   .hc4-dashboard-actions .boton[data-hc4-accent] {
      border-top: 3px solid var(--hc4-module-color);
      border-right: 1px solid color-mix(in srgb, var(--hc4-module-color) 22%, #ffffff) !important;
      border-bottom: 1px solid color-mix(in srgb, var(--hc4-module-color) 22%, #ffffff) !important;
      border-left: 1px solid color-mix(in srgb, var(--hc4-module-color) 22%, #ffffff) !important;
      background: linear-gradient(135deg, #ffffff 18%, var(--hc4-module-soft) 100%) !important;
      box-shadow: 0 5px 16px color-mix(in srgb, var(--hc4-module-color) 12%, transparent) !important;
      transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
   }

   .hc4-dashboard-actions .boton[data-hc4-accent] .text_2,
   .hc4-dashboard-actions .boton[data-hc4-accent] .text_2 b {
      color: var(--hc4-module-color) !important;
   }

   .hc4-dashboard-actions .boton[data-hc4-accent] img {
      filter: var(--hc4-icon-filter) drop-shadow(0 4px 5px color-mix(in srgb, var(--hc4-module-color) 22%, transparent)) !important;
   }

   .hc4-dashboard-actions .boton[data-hc4-accent]:hover,
   .hc4-dashboard-actions .boton[data-hc4-accent]:focus {
      transform: translateY(-2px);
      background: linear-gradient(135deg, #ffffff 4%, var(--hc4-module-soft) 100%) !important;
      box-shadow: 0 10px 22px color-mix(in srgb, var(--hc4-module-color) 20%, transparent) !important;
   }

   .hc4-dashboard-actions .boton[data-hc4-accent] > .row > div:first-child {
      border: 0;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
   }

   .hc4-dashboard-actions .boton[data-hc4-accent]:hover::after,
   .hc4-dashboard-actions .boton[data-hc4-accent]:focus::after {
      opacity: 1;
   }

   .hc4-dashboard-actions .boton.hc4-dashboard-action--disabled,
   .hc4-dashboard-actions .boton.hc4-dashboard-action--disabled:hover,
   .hc4-dashboard-actions .boton.hc4-dashboard-action--disabled:focus {
      pointer-events: none;
      cursor: not-allowed;
      opacity: .58;
      transform: none;
      box-shadow: none !important;
      background: #f3f6f8 !important;
   }

   .hc4-dashboard-actions .boton.hc4-dashboard-action--disabled::after {
      display: none;
   }

   .hc4-dashboard-surface img {
      filter: hue-rotate(5deg) saturate(.9) drop-shadow(0 4px 5px rgba(0, 76, 120, .12));
   }

   .hc4-dashboard-surface .text_2 {
      line-height: 1.3;
   }

   .hc4-history-panel {
      position: relative;
      margin-top: 0 !important;
      padding: 14px 18px !important;
      border: 1px solid #bad9e5;
      border-radius: 14px !important;
      color: #173f57 !important;
      background: linear-gradient(135deg, #eef9fc 0%, #dff2f7 48%, #cce9ef 100%) !important;
      box-shadow: 0 10px 28px rgba(0, 91, 133, .15);
   }

   .content-wrapper > section.content {
      padding-top: 10px;
   }

   .hc4-history-panel::before {
      content: '';
      position: absolute;
      top: 0;
      left: 22px;
      width: 72px;
      height: 3px;
      border-radius: 0 0 4px 4px;
      background: linear-gradient(90deg, #087eae, #18aaa5);
   }

   .hc4-history-panel h1 {
      display: flex;
      align-items: center;
      gap: 10px;
      top: auto !important;
      min-height: 100%;
      line-height: 1.4;
      color: #16465f;
      font-size: 17px !important;
      letter-spacing: -.1px;
      margin: 0 !important;
   }

   .hc4-history-panel h1 img {
      width: 64px !important;
      height: 64px;
      padding: 9px;
      object-fit: contain;
      border: 1px solid rgba(255,255,255,.48);
      border-radius: 15px;
      background: linear-gradient(145deg, #07547e 0%, #0b91b8 100%);
      box-shadow: 0 8px 18px rgba(0, 83, 126, .25);
      filter: drop-shadow(0 2px 3px rgba(20, 12, 55, .22));
   }

   .hc4-history-panel form > .row {
      display: grid;
      grid-template-columns: 1fr;
      gap: 16px;
      margin: 0;
   }

   .hc4-history-panel form > .row > .col-lg-3,
   .hc4-history-panel form > .row > .col-lg-9 {
      width: 100%;
      max-width: 100%;
      padding: 0;
   }

   .hc4-history-panel form > .row > .col-lg-3 {
      padding-bottom: 13px;
      border-bottom: 1px solid #ece8f4;
   }

   .hc4-history-panel label {
      color: #435f6e;
      font-weight: 600;
   }

   .hc4-history-panel .hc4-filter-panel {
      padding: 10px 12px !important;
      border: 1px solid rgba(8, 126, 174, .28);
      border-radius: 12px;
      background: rgba(255,255,255,.7);
      box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
   }

   .hc4-filter-panel .form-control,
   .hc4-filter-panel .input-group-text {
      min-height: 36px;
      border-color: #ded9ea;
   }

   .hc4-filter-panel .form-control {
      border-radius: 6px;
      color: #27495c;
      background-color: #fbfdfe;
      box-shadow: none;
   }

   .hc4-filter-panel .form-control:focus {
      border-color: #168eae;
      background-color: #fff;
      box-shadow: 0 0 0 3px rgba(8, 126, 174, .14);
   }

   .hc4-filter-panel .select2-container {
      width: 100% !important;
   }

   .hc4-filter-panel .select2-container .select2-selection--single {
      height: 36px;
      border: 1px solid #ded9ea;
      border-radius: 6px;
      background: #fbfafd;
   }

   .hc4-filter-panel .select2-container .select2-selection--single .select2-selection__rendered {
      padding-left: 10px;
      color: #27495c;
      line-height: 34px;
   }

   .hc4-filter-panel .select2-container .select2-selection--single .select2-selection__arrow {
      height: 34px;
   }

   .hc4-filter-panel .select2-container--focus .select2-selection--single,
   .hc4-filter-panel .select2-container--open .select2-selection--single {
      border-color: #168eae;
      box-shadow: 0 0 0 3px rgba(8, 126, 174, .14);
   }

   .hc4-filter-search {
      border-radius: 7px !important;
      background: linear-gradient(90deg, #07547e, #0b91b8) !important;
      border-color: #07547e !important;
      transition: background-color .18s ease, transform .18s ease;
   }

   .hc4-filter-search:hover,
   .hc4-filter-search:focus {
      background: linear-gradient(90deg, #063f66, #087eae) !important;
      transform: translateY(-1px);
   }

   #area_trabajo {
      --hc4-result-color: #004AC1;
      --hc4-result-color-end: #0b91b8;
      overflow: hidden;
      min-height: 230px;
      margin-top: 2px;
      border: 1px solid #d7e7ed;
      border-radius: 16px !important;
      background:
         linear-gradient(135deg, rgba(8, 126, 174, .035) 25%, transparent 25%) 0 0 / 22px 22px,
         linear-gradient(315deg, rgba(8, 126, 174, .025) 25%, transparent 25%) 0 0 / 22px 22px,
         #f9f8fc;
      box-shadow: 0 8px 22px rgba(0, 75, 114, .08);
   }

   /* Los resultados mantienen el estilo institucional, sin variar por módulo. */
   #area_trabajo[data-hc4-accent] {
      --hc4-result-color: #004AC1;
      --hc4-result-color-end: #0b91b8;
   }

   #area_trabajo > .box,
   #area_trabajo #area_busqueda_doctor {
      margin-bottom: 0;
      border-radius: 14px !important;
      box-shadow: none;
   }

   #area_trabajo .box-header.with-border {
      padding: 14px 16px !important;
      color: #fff !important;
      background: linear-gradient(100deg, var(--hc4-result-color) 0%, var(--hc4-result-color-end) 100%) !important;
      border-bottom: 0 !important;
      font-weight: 700;
      letter-spacing: .02em;
   }

   #area_trabajo .box-header.with-border .btn {
      min-height: 37px;
      color: var(--hc4-result-color) !important;
      background: rgba(255,255,255,.96) !important;
      border: 1px solid rgba(255,255,255,.65) !important;
      border-radius: 8px !important;
      box-shadow: 0 2px 7px rgba(63, 46, 130, .08);
   }

   #area_trabajo .box-header.with-border .btn:hover,
   #area_trabajo .box-header.with-border .btn:focus {
      color: #fff !important;
      background: var(--hc4-result-color) !important;
      border-color: var(--hc4-result-color) !important;
   }

   #area_trabajo .box-body {
      min-height: 150px;
      border-color: #e1ddec !important;
      background: rgba(255,255,255,.58);
   }

   #area_trabajo #modificar:not(:has(.box, table, .content)) {
      display: flex;
      align-items: center;
      justify-content: center;
   }

   #area_trabajo #modificar:not(:has(.box, table, .content))::before {
      content: '\f002  Selecciona una opción o utiliza los filtros para consultar pacientes';
      max-width: 430px;
      padding: 18px 24px;
      color: #746b89;
      border: 1px dashed #cfc7e1;
      border-radius: 12px;
      background: rgba(255,255,255,.78);
      font-family: FontAwesome, Helvetica, Arial, sans-serif;
      font-size: 14px;
      line-height: 1.5;
      text-align: center;
   }

   #area_trabajo #modificar:not(:has(.box, table, .content)) > .col-md-12,
   #area_trabajo #modificar > .col-md-12:empty {
      display: none;
   }

   #area_trabajo .color {
      color: var(--hc4-result-color) !important;
   }

   #area_trabajo .titulo {
      border-bottom-color: #d8d1e6 !important;
   }

   @media (min-width: 992px) {
      .hc4-history-panel form > .row {
         display: flex;
         align-items: center;
         gap: 0;
         margin-right: -8px;
         margin-left: -8px;
      }

      .hc4-history-panel form > .row > .col-lg-3 {
         flex: 0 0 25%;
         width: 25%;
         max-width: 25%;
         padding: 0 18px 0 8px;
         border-bottom: 0;
      }

      .hc4-history-panel form > .row > .col-lg-9 {
         flex: 0 0 75%;
         width: 75%;
         max-width: 75%;
         padding: 10px 12px;
      }

      .hc4-history-panel h1 {
         min-height: 96px;
      }

      .hc4-dashboard-surface > .row.row_datos {
         display: grid;
         grid-template-columns: minmax(300px, .82fr) minmax(0, 2.18fr);
         gap: 10px;
         align-items: start;
         margin: 0;
      }

      .hc4-dashboard-surface > .row.row_datos > .col-lg-5,
      .hc4-dashboard-surface > .row.row_datos > .col-lg-7 {
         display: block !important;
         width: 100%;
         max-width: 100%;
         height: auto !important;
         padding: 0;
      }

      .hc4-dashboard-surface > .row.row_datos > .col-lg-5 > .row,
      .hc4-dashboard-surface > .row.row_datos > .col-lg-5 > .row > .col-12 {
         height: auto !important;
         padding: 0 !important;
      }

      .hc4-dashboard-surface > .row.row_datos > .col-lg-5 > .row {
         margin-right: 0 !important;
         margin-left: 0 !important;
      }

      .hc4-dashboard-surface .calendario {
         height: 294px !important;
         min-height: 294px;
      }

      .hc4-dashboard-actions {
         display: grid !important;
         flex: none !important;
         grid-template-columns: repeat(3, minmax(0, 1fr));
         grid-template-rows: repeat(2, 142px);
         gap: 10px;
         margin: 0;
      }

      .hc4-dashboard-actions > [class*="col-"] {
         display: contents !important;
      }

      .hc4-dashboard-actions .boton {
         height: 142px !important;
         min-height: 142px;
         margin: 0 !important;
         padding: 12px 15px !important;
         border-radius: 14px !important;
      }

      .hc4-dashboard-actions .boton > .row {
         width: min(100%, 340px) !important;
         height: auto !important;
         margin: auto !important;
      }

      .hc4-dashboard-actions .boton > .row > br {
         display: none;
      }

      .hc4-dashboard-actions .boton .plantilla,
      .hc4-dashboard-actions .boton > .row {
         display: grid;
         grid-template-columns: 68px minmax(0, 1fr);
         column-gap: 12px;
         grid-auto-rows: auto;
         align-content: center;
         align-items: center;
         justify-content: center;
         text-align: left !important;
      }

      .hc4-dashboard-actions .boton > .row > div:first-child {
         grid-column: 1;
         grid-row: 1 / span 2;
         display: flex;
         align-items: center;
         justify-content: center;
         width: 62px !important;
         max-width: 62px !important;
         height: 62px;
         padding: 2px !important;
      }

      .hc4-dashboard-actions .boton > .row > div:nth-child(n+2) {
         grid-column: 2;
         width: auto !important;
         max-width: none !important;
         padding: 0 !important;
         text-align: left !important;
      }

      .hc4-dashboard-actions .boton p,
      .hc4-dashboard-actions .boton p[style] {
         margin: 0 0 4px !important;
         font-size: 12.5px !important;
         line-height: 1.28 !important;
         text-align: left !important;
      }

      .hc4-dashboard-actions .boton .text_2,
      .hc4-dashboard-actions .boton .text_2[style] {
         font-size: 15px !important;
         font-weight: 700;
      }

      .hc4-dashboard-actions .boton > .row > div:last-child p:last-child {
         margin-bottom: 0 !important;
      }

      .hc4-dashboard-actions .boton img {
         max-width: 56px !important;
         max-height: 56px;
         display: block;
         margin: 0 auto;
      }

      .hc4-dashboard-actions .boton .image_baner {
         width: 58px !important;
      }
   }

   @media (max-width: 991.98px) {
      .hc4-dashboard-actions {
         display: grid !important;
         grid-template-columns: repeat(2, minmax(0, 1fr));
         gap: 8px;
         margin: 0;
      }

      .hc4-dashboard-actions > [class*="col-"] {
         display: contents !important;
      }

      .hc4-dashboard-actions .boton {
         height: 128px !important;
         min-height: 128px;
         margin: 0 !important;
         padding: 10px !important;
      }
   }

   @media (max-width: 479.98px) {
      .hc4-dashboard-actions {
         grid-template-columns: 1fr;
      }

   }

   @media (min-width: 992px) {
      .hc4-dashboard-surface > .row {
         align-items: stretch;
      }

      .hc4-dashboard-surface > .row > [class*="col-"] {
         display: flex;
         flex-direction: column;
      }

      .hc4-dashboard-surface > .row > [class*="col-"] > .row,
      .hc4-dashboard-actions {
         flex: 1;
      }

      .hc4-history-panel .hc4-filter-panel > .row:first-child {
         padding-top: 0 !important;
      }
   }

   .hc4-filter-panel > .row {
      margin-left: -8px;
      margin-right: -8px;
   }

   .hc4-filter-panel > .row > [class*="col-"] {
      padding-left: 8px;
      padding-right: 8px;
   }

   .hc4-filter-panel .form-group {
      margin-bottom: 10px;
   }

   .hc4-filter-panel .control-label {
      display: flex;
      align-items: center;
      min-height: 30px;
      margin-bottom: 0;
   }

   .hc4-filter-search {
      width: 100%;
      min-height: 30px;
   }

   .hc4-results-toolbar {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 10px 12px;
      padding: 2px 4px 6px;
   }

   .hc4-results-toolbar > .hc4-results-title {
      flex: 0 0 100%;
      width: 100%;
      max-width: 100%;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 7px 6px 4px !important;
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: .25px;
      text-transform: uppercase;
   }

   .hc4-results-toolbar > .hc4-results-title::before {
      content: '\e003';
      font-family: 'Glyphicons Halflings';
      font-size: 12px;
      opacity: .9;
   }

   .hc4-results-toolbar > .hc4-toolbar-action {
      flex: 1 1 160px;
      width: auto;
      max-width: none;
      padding: 0 3px;
      text-align: center;
   }

   .hc4-results-toolbar .boton_doctor {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      width: 100%;
      min-height: 42px;
      padding: 7px 14px;
      border: 1px solid rgba(8, 126, 174, .22) !important;
      border-radius: 10px !important;
      color: #000000 !important;
      background: linear-gradient(180deg, #fff 0%, #edf8fb 100%) !important;
      box-shadow: 0 4px 11px rgba(0, 74, 117, .12);
      font-size: 13px;
      font-weight: 600;
      line-height: 1.2;
      white-space: normal;
      transition: transform .18s ease, color .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease;
   }

   .hc4-results-toolbar .boton_doctor .glyphicon,
   .hc4-results-toolbar .boton_doctor .fa {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 27px;
      width: 27px;
      height: 27px;
      border-radius: 8px;
      color: #fff;
      background: linear-gradient(135deg, #07547e, #0b91b8);
      box-shadow: 0 3px 7px rgba(0, 83, 126, .22);
      font-size: 12px;
   }

   .hc4-results-toolbar .boton_doctor:hover,
   .hc4-results-toolbar .boton_doctor:focus {
      border-color: rgba(8, 126, 174, .48) !important;
      color: #064b79 !important;
      background: #fff !important;
      box-shadow: 0 7px 17px rgba(0, 74, 117, .2);
      transform: translateY(-2px);
      outline: none;
   }

   .hc4-results-toolbar .boton_doctor:active {
      box-shadow: 0 2px 7px rgba(0, 74, 117, .14);
      transform: translateY(0);
   }

   @media (max-width: 767px) {
      .hc4-results-toolbar > .hc4-toolbar-action { flex-basis: calc(50% - 8px); }
      .hc4-results-toolbar .boton_doctor { min-height: 44px; padding: 7px 9px; font-size: 12px; }
   }

  @media (max-width: 480px) {
      .hc4-results-toolbar > .hc4-toolbar-action { flex-basis: 100%; }
   }

   /* Ajustes de contraste y profundidad del panel ambulatorio oscuro. */
   html.dark-layout .hc4-dashboard-surface {
      border-color: #344c5d !important;
      background: linear-gradient(145deg,#132632 0%,#172d39 100%) !important;
   }

   html.dark-layout .hc4-dashboard-actions .boton[data-hc4-accent] {
      border-right-color: #385363 !important;
      border-bottom-color: #385363 !important;
      border-left-color: #385363 !important;
      background: linear-gradient(135deg,#1b303d 12%,color-mix(in srgb,var(--hc4-module-color) 18%,#172a36) 100%) !important;
   }

   html.dark-layout .hc4-dashboard-actions .boton[data-hc4-accent] .text_2,
   html.dark-layout .hc4-dashboard-actions .boton[data-hc4-accent] .text_2 b {
      color: color-mix(in srgb,var(--hc4-module-color) 62%,#ffffff) !important;
   }

   html.dark-layout .hc4-dashboard-actions .hc4-module-description,
   html.dark-layout .hc4-dashboard-actions .boton[data-hc4-accent] p:not(.text_2) {
      color: #c3d2db !important;
   }

   html.dark-layout .hc4-history-panel {
      border-color: #405667 !important;
      color: #dce8ef !important;
      background: linear-gradient(135deg,#182d3a 0%,#1d3442 55%,#263b4c 100%) !important;
   }

   html.dark-layout .hc4-history-panel h1,
   html.dark-layout .hc4-history-panel label {
      color: #e4edf3 !important;
   }

   html.dark-layout .hc4-history-panel form > .row > .col-lg-3 { border-color: #3a5262; }

   html.dark-layout .hc4-history-panel .hc4-filter-panel {
      border-color: #496273;
      background: linear-gradient(135deg,#203946 0%,#1c3340 100%) !important;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.04),0 7px 18px rgba(0,0,0,.13);
   }

   html.dark-layout .hc4-filter-panel .form-control,
   html.dark-layout .hc4-filter-panel .input-group-text,
   html.dark-layout .hc4-filter-panel .select2-selection--single {
      border-color: #4a6675 !important;
      color: #e4edf2 !important;
      background: #152a36 !important;
   }

   html.dark-layout .hc4-filter-panel .select2-selection__rendered { color: #dce8ef !important; }
   html.dark-layout .hc4-filter-panel .select2-selection__placeholder { color: #91a8b5 !important; }
   html.dark-layout .hc4-filter-panel .select2-selection__arrow b { border-top-color: #b9ccd6 !important; }

   html.dark-layout #area_trabajo {
      border-color: #3a5363 !important;
      background:
         linear-gradient(135deg,rgba(105,83,189,.05) 25%,transparent 25%) 0 0/22px 22px,
         #152935 !important;
   }

   html.dark-layout #area_trabajo .box-body { border-color:#344e5e !important; background:#182f3b !important; }

   html.dark-layout #area_trabajo .box-header.with-border .btn,
   html.dark-layout .hc4-results-toolbar .boton_doctor {
      border-color: #50697a !important;
      color: #e2dcff !important;
      background: linear-gradient(180deg,#263f4d,#203744) !important;
      box-shadow: 0 4px 11px rgba(0,0,0,.17);
   }

   html.dark-layout #area_trabajo .box-header.with-border .btn:hover,
   html.dark-layout .hc4-results-toolbar .boton_doctor:hover {
      border-color: #806bd2 !important;
      color: #fff !important;
      background: #5945ad !important;
   }

   html.dark-layout #area_trabajo #modificar:not(:has(.box, table, .content))::before {
      border-color: #52697a;
      color: #b9cad4;
      background: #203744;
   }

   #modificar,
   #modificar > .content,
   #div_grafico,
   #div_grafico .dataTables_wrapper,
   #div_grafico .dataTables_wrapper > .row,
   #div_grafico .dataTables_wrapper > .row > [class*="col-"] {
      width: 100% !important;
      max-width: none !important;
   }

   #div_grafico .dataTables_wrapper > .row {
      margin-left: 0;
      margin-right: 0;
   }

   #div_grafico .dataTables_wrapper > .row > [class*="col-"] {
      padding-left: 0;
      padding-right: 0;
   }

   #div_grafico table.hc4-results-table {
      width: 100% !important;
      min-width: 100% !important;
   }

   @media (max-width: 500px) {
    .hc4-dashboard-shell {
      padding: 0 8px;
    }

    .hc4-dashboard-surface {
      padding: 6px !important;
    }

    .hc4-history-panel {
      padding: 14px 10px !important;
    }

    .hc4-history-panel h1 {
      justify-content: flex-start;
      margin-bottom: 14px !important;
    }

    #area_trabajo {
      min-height: 190px;
    }

    .text_2{
      font-size: 12px;
    }
    .text_2 b{
      font-size: 12px;
    }

    .image_baner{
      width: 50px !important;
    }
    .plantilla{
      padding-top: 5px;
    }
    .boton_doctor{
      font-size: 11px;
    }

    .estadisticos{
      padding-top: 5px;
    }
    .color{
      font-size: 11px;
    }
  }

</style>
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

<div class="container-fluid hc4-dashboard-shell">
  <div class="col-md-12">
    <div id="navbar" class="row"  style="z-index: 99999">
      <div class="col-lg-12 col-md-12 hc4-dashboard-surface" style="background: #004AC1;">
        <div class="row row_datos">
          <div class="col-lg-5 col-sm-12 col-12">
            <div class="row">
              <div class="col-12" style="padding: 5px 2px; padding-left: 5px; height: 240px;">
                <a onclick="cargar_pacientes_doctor();" class="boton calendario" data-tour="ambulatory-agenda" data-hc4-accent="agenda" style="color: #004AC1; height: 230px">
                  <div class="row">
                    <div class="col-12" style="height: 42px; text-align: left;color: #004AC1;">
                      <img src="{{asset('/')}}hc4/img/bt_ca2.png" style="background-color: none;height: 46px;">
                      <span>@lang('hc4.todays_schedule')</span>
                    </div>
                    <hr style="background-color: #004AC1;">
                    <div class="col-12" >
                      <div class="row">
                        <div class="col-md-6 col-12" style="padding: 5px;color:#004AC1;">
                          <div class="row">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1">&nbsp;</div>
                                <div class="col-10" style="text-align: left;">
                                  <span style="color: #004AC1; font-size: 14px;"><b>@lang('hc4.schedule_calendar')</b></span>
                                </div>
                              </div>
                            </div>
                            <div class="col-12" style="height: 4px; ">
                            </div>
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                  <div class="col-4">
                                    <p class="align-middle" style='text-align: center;margin:0;line-height: 1;color: #004AC1;'><span><b style="font-size: 30px;">{{date('d')}}</b></span><br><span style="line-height: 1; font-size: 16px; text-align: center"> @if(date('N') == 1) @lang('hc4.monday') @elseif(date('N') == 2) @lang('hc4.tuesday') @elseif(date('N') == 3) @lang('hc4.wednesday') @elseif(date('N') == 4) @lang('hc4.thursday') @elseif(date('N') == 5) @lang('hc4.friday') @elseif(date('N') == 6) @lang('hc4.saturday') @elseif(date('N') == 7) @lang('hc4.sunday') @endif</span></p>
                                  </div>
                                  <div class="col-6" style="text-align: left;">
                                    <div style="height: 8px;"></div>
                                    <span style="font-size:14px; text-align: left; color:#004AC1;">@if(date('m') == 1) @lang('hc4.january') @elseif(date('m') == 2) @lang('hc4.february') @elseif(date('m') == 3) @lang('hc4.march') @elseif(date('m') == 4) @lang('hc4.april') @elseif(date('m') == 5) @lang('hc4.may') @elseif(date('m') == 6) @lang('hc4.june') @elseif(date('m') == 7) @lang('hc4.july') @elseif(date('m') == 8) @lang('hc4.august') @elseif(date('m') == 9) @lang('hc4.september') @elseif(date('m') == 10) @lang('hc4.october') @elseif(date('m') == 11) @lang('hc4.november') @elseif(date('m') == 12) @lang('hc4.december') @endif {{date('Y')}}</span>
                                  </div>
                              </div>
                            </div>
                            <hr style="height: 0px;">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                <div class="col-10">
                                  <p style="font-size: 12px; text-align: left;">{{$agenda_consultas}} @lang('hc4.scheduled_consultations') <br> {{$procedimiento_consultas}} @lang('hc4.scheduled_procedures')</p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-8 cambiar" style="line-height: 1;">
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <div class="col-lg-7 col-sm-12 col-12">
            <div class="row hc4-dashboard-actions">
              <div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a onclick="cargar_horario_doctor();" class="boton" data-tour="ambulatory-schedule" data-hc4-accent="horario" style="height: 230px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div style="width: 100%;">
                      <img src="{{asset('/')}}hc4/img/reloj.png" style="width: 100px; ">
                    </div>
                    <br>
                    <br>
                    <br>
                    <div style="width: 100%; color: #004AC1">
                      <p class="text_2"><b>@lang('hc4.working_hours')</b></p>
                    </div>
                    <div style="width: 100%; color: #e88c07; font-size:12px;">
                      <p><b>Dr. {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</b></p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a onclick="cargar_listado_paciente();" class="boton" data-tour="ambulatory-patients" data-hc4-accent="pacientes" style="height: 230px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div style="width: 100%;">
                      <img src="{{asset('/')}}hc4/img/listado.png" style="width: 100px; ">
                    </div>
                    <br>
                    <br>
                    <br>
                    <div style="width: 100%;color: #004AC1;">
                      <p class="text_2" style="margin-bottom: 5px"><b>@lang('hc4.todays_patient_list')</b></p>
                    </div>
                    <div style="width: 100%;  font-size:12px;color:#004AC1;">
                      <p style="text-align: center;"><b>{{$consultas_todas}}</b> @lang('hc4.consultations') <br> <b>{{$procedimiento_todas}}</b> @lang('hc4.procedures')</p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a class="boton hc4-dashboard-action--disabled" data-tour="ambulatory-lab" data-hc4-accent="laboratorio" aria-disabled="true" tabindex="-1" title="Resultados de laboratorio temporalmente deshabilitados" style="height: 110px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div class="col-md-5 col-xs-6" style="width: 100%;padding-left: 0;padding-right: 5px;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/lab2.png" style="width: 95px;">
                    </div>
                    <div  class="col-md-7 col-xs-6" style="width: 100%;color: #004AC1;padding-left: 0;">
                      <p class="text_2" style="font-size: 15px"><b>@lang('hc4.laboratory_results')</b></p>
                      <p class="text_2" style="font-size: 15px"><b>{{$ordenes_laboratorio}}</b> @lang('hc4.orders')</p>
                    </div>
                  </div>
                </a>
                <a onclick="examenes_favoritos();" class="boton" data-tour="ambulatory-favorites" data-hc4-accent="examenes" style="margin-top: 5px; height: 112px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row plantilla"  style="text-align: center;">
                    <div class="col-md-4" style="width: 100%;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/exams_favoritos.png" style="width: 75px;">
                    </div>
                    <div class="col-md-8" style="width: 100%; color: #004AC1;">
                      <p style="font-size: 13px" class="text_2"><b>@lang('hc4.favorite_tests')</b></p>
                      <p class="hc4-module-description">Accesos rápidos de laboratorio</p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a onclick="cargar_crear_editar();" class="boton" data-tour="ambulatory-medicines" data-hc4-accent="medicinas" style="height: 110px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div class="col-md-6 col-xs-6" style="width: 100%;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/med.png" style="width: 95px;">
                    </div>
                    <div  class="col-md-6 col-xs-6" style="width: 100%;color: #004AC1;">
                      <p class="text_2" style="font-size: 15px"><b>@lang('hc4.create_edit_medicines')</b></p>
                      <p class="hc4-module-description">Catálogo terapéutico</p>
                    </div>
                  </div>
                </a>
                <a onclick="plantilla_proc();" class="boton" data-tour="ambulatory-templates" data-hc4-accent="plantillas" style="margin-top: 5px; height: 112px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row plantilla"  style="text-align: center;">
                    <div class="col-md-4" style="width: 100%;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/exa1.png" style="width: 55px;">
                    </div>
                    <div class="col-md-8" style="width: 100%; color: #004AC1;">
                      <p style="font-size: 13px" class="text_2"><b>@lang('hc4.procedure_templates')</b></p>
                      <p class="hc4-module-description">Documentos reutilizables</p>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $("#fecha").change(function(){
    alert($("#fecha").val());
  });
  function enviar_enter(e){
    //alert('entra1');
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla==13){
        buscador_paciente_fecha();
      };
  }
  function cambio_fecha(){
    alert('cambio');
  }


</script>


<section class="content" >
  <div class="container-fluid" id="info" style="padding-left: 0px; padding-right: 0px;">
    <div class="col-md-12 hc4-history-panel" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 8px; background-color: #004AC1; margin-bottom: 10px">
      <form method="POST" id="form_buscador" data-tour="ambulatory-search" action="{{route('historia_clinica.reporte_hc')}}">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-lg-3 col-12">
            <h1 style="font-size: 15px; margin:0; position: relative;top: 25%;">
              <img style="width: 49px;" src="{{asset('/')}}hc4/img/hc_ima.png">
              <b>@lang('hc4.patient_medical_history')</b>
            </h1>
          </div>
          <div class="col-lg-9 col-12 hc4-filter-panel">
            <div class="row" style="padding-top: 5px">
              <div class="col-lg-6 col-12" >
                  <div class="row">
                    <div class=" col-md-6 " >
                      <div class="row">
                        <div class="col-3 "><label for="fecha" class="col-md-3 control-label" style="padding:0px;">@lang('hc4.from')</label></div>
                        <div class="col-9">
                          <div class="form-group">
                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                              <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="desde_inicio" id="desde_inicio" class="form-control form-control-sm datetimepicker-input" data-target="#datetimepicker1"  />
                                <input type="hidden" id="fecha" name="fecha" onchange="cambio_fecha()">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class=" col-md-6 " >
                      <div class="row">
                        <div class="col-3"><label for="fecha" class="col-md-3 control-label" style="padding:0px;">@lang('hc4.to')</label></div>
                        <div class="col-9">
                          <div class="form-group">
                            <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                              <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="hasta_fin" id="hasta_fin" class="form-control form-control-sm datetimepicker-input" data-target="#datetimepicker2" />
                                <input type="hidden" id="fecha_hasta" name="fecha_hasta">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <!--<div class="col-md-2 col-sm-6 col-12" style="text-align: center;">
                <a class="btn btn-danger" onclick="cargar_nuevopaciente();" style=" background-color:#9b9b9b ; border-radius: 5px; border: 2px solid white;">  Agregar Nuevo Paciente
                </a>
              </div>
              <div class="col-2"></div>-->
              <div class="col-lg-6 col-12" >
                <div class="row">
                  <div class="form-group col-lg-4 col-md-6 col-xs-6" >
                    <div class="row">
                      <label for="proc_consul" class="col-md-3 control-label">@lang('hc4.type')</label>
                      <div class="col-lg-9 col-md-8">
                        <select class="form-control form-control-sm input-sm hc4-select2-local" name="proc_consul" id="proc_consul" onchange="buscador_paciente_fecha()">
                          <option @if($proc_consul=='2') selected @endif value="2" >@lang('hc4.all')</option>
                          <option @if($proc_consul=='0') selected @endif value="0" >@lang('hc4.consultations')</option>
                          <option @if($proc_consul=='1') selected @endif value="1" >@lang('hc4.procedures')</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-lg-8 col-md-6 col-xs-6" >
                    <div class="row">
                      <label for="espid" class="col-lg-4 col-md-3 control-label">@lang('hc4.specialty')</label>
                      <div class="col-md-8">
                        <select class="form-control form-control-sm input-sm" name="espid" id="espid" onchange="buscador_paciente_fecha()">
                          <option value="">@lang('hc4.all_ellipsis')</option>
                        @foreach($especialidades as $especialidad)
                          <option @if($especialidad->id==$id_especialidad) selected @endif value="{{$especialidad->id}}">{{$especialidad->nombre}}</option>
                        @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-lg-3 col-md-6 col-xs-6" >
                <div class="row">
                  <label for="id_doctor1" class="col-md-3 control-label">@lang('hc4.doctor')</label>
                  <div class="col-lg-9 col-md-8">
                    <select class="form-control form-control-sm input-sm" name="id_doctor1" id="id_doctor1" onchange="buscador_paciente_fecha()">
                      <option value="">@lang('hc4.select')</option>
                    @foreach($doctores as $doctor)
                      <option @if($doctor->id==$id_doctor1) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
                    @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group col-lg-3 col-md-6 col-xs-6" >
                <div class="row">
                  <label for="id_seguro" class="col-md-3 control-label">@lang('hc4.insurance')</label>
                  <div class="col-lg-9 col-md-8">
                    <select class="form-control form-control-sm input-sm" name="id_seguro" id="id_seguro" onchange="buscador_paciente_fecha()">
                      <option value="">@lang('hc4.select')</option>
                    @foreach($seguros as $seguro)
                      <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                    @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 form-group" >
                <div class="input-group">
                  <input value="{{$request->apellidos}}" type="text" class="form-control form-control-sm " name="apellidos" id="apellidos" placeholder="@lang('hc4.last_names')" style="text-transform:uppercase;" onkeypress="enviar_enter(event);" >
                </div>
              </div>
              <div class="col-lg-3 col-md-6 form-group" >
                <div class="input-group">
                  <input value="{{$request->nombres}}" type="text" class="form-control form-control-sm " name="nombres" id="nombres" placeholder="@lang('hc4.first_names')" style="text-transform:uppercase;" onkeypress="enviar_enter(event);"  >
                </div>
              </div>

              
            </div>
            <div class="row">
              <div class="form-group col-lg-3 col-md-6 col-xs-6" >
                <div class="row">
                  <label for="cedula" class="col-md-3 control-label">@lang('hc4.identification')</label>
                  <div class="col-lg-9 col-md-8">
                    <div class="input-group">
                      <input value="{{$request->cedula}}" type="text" class="form-control form-control-sm " name="cedula" id="cedula" placeholder="@lang('hc4.identification')" style="text-transform:uppercase;" onkeypress="enviar_enter(event);"  >
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-3 col-md-6 form-group col-xs-6" >
                <button type="button" onclick="buscador_paciente_fecha();" class="btn btn-danger hc4-filter-search" style="color:white; background-color: #004AC1; border-radius: 5px; border: 2px solid;"> <i class="fa fa-search" aria-hidden="true">
                </i> &nbsp;@lang('hc4.search')&nbsp;</button>

                <button type="button" onclick="enviar_formulario_revision();" id="exp_rev" class="oculto"></button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="box box" style="border-radius: 8px;" id="area_trabajo" data-tour="ambulatory-results" data-hc4-accent="agenda">

    </div>
  </div>
</section>


<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>


<script type="text/javascript">

  function inicializarFiltrosSelect2Ambulatorios() {
    $('#proc_consul').select2({
      width: '100%',
      minimumResultsForSearch: 0,
      language: {
        noResults: function() {
          return 'No se encontraron opciones';
        }
      }
    });

    var configuraciones = {
      '#espid': {
        catalogo: 'especialidades',
        placeholder: '@lang("hc4.all_ellipsis")'
      },
      '#id_doctor1': {
        catalogo: 'doctores',
        placeholder: '@lang("hc4.select")'
      },
      '#id_seguro': {
        catalogo: 'seguros',
        placeholder: '@lang("hc4.select")'
      }
    };

    $.each(configuraciones, function(selector, configuracion) {
      $(selector).select2({
        width: '100%',
        allowClear: true,
        placeholder: configuracion.placeholder,
        minimumInputLength: 1,
        ajax: {
          url: '{{ route("hc4.catalogos_select2") }}',
          dataType: 'json',
          delay: 250,
          cache: true,
          data: function(params) {
            return {
              catalogo: configuracion.catalogo,
              q: params.term || '',
              page: params.page || 1
            };
          },
          processResults: function(data) {
            return {
              results: data.results || [],
              pagination: data.pagination || { more: false }
            };
          }
        },
        language: {
          inputTooShort: function() {
            return 'Escribe al menos un carácter para buscar';
          },
          noResults: function() {
            return 'No se encontraron resultados';
          },
          searching: function() {
            return 'Buscando...';
          },
          loadingMore: function() {
            return 'Cargando más resultados...';
          },
          errorLoading: function() {
            return 'No se pudieron cargar los resultados';
          }
        }
      });
    });
  }

  function enviar_formulario_revision(){
    //form_buscador
    //formaction="{{route('hc4controller.exportar_revision')}}"

    //formulario.submit();
    $('#form_buscador').attr('action', '{{route("hc4controller.exportar_revision")}}');//Setting form action to "success.php" page
    $( "#form_buscador" ).submit();// Submitting form
    $('#form_buscador').attr('action', '{{route("historia_clinica.reporte_hc")}}');
  }

  function descargar_hc_reporte() {
    $( "#form_buscador" ).submit(); // Click on the checkbox
  }
            $(function () {
                inicializarFiltrosSelect2Ambulatorios();

                $('#datetimepicker1').datetimepicker({
                    format: 'YYYY/MM/DD',
                    defaultDate: '{{$fecha1}}'
                });

                $('#datetimepicker2').datetimepicker({
                    format: 'YYYY/MM/DD',
                    defaultDate: '{{$fecha2}}',
                    //minDate: '{{$fecha2}}',
                  });

                $("#datetimepicker1").on("change.datetimepicker", function (e) {
                    $('#datetimepicker2').datetimepicker('minDate', e.date);
                    buscador_paciente_fecha();
                });
                $("#datetimepicker2").on("change.datetimepicker", function (e) {
                    buscador_paciente_fecha();
                });

                $(".clickable-row").click(function() {
                    window.location = $(this).data("href");
                  });
            });

            $(function(){

            });

            function fechacalendario() {
                var dato = document.getElementById('datetimepicker1').value;
                $('#enviar_fecha').click();
            }


</script>

<script>
    function activarTemaResultadoAmbulatorio(acento) {
      // El panel de resultados usa siempre el estilo institucional estándar.
      $('#area_trabajo').attr('data-hc4-accent', 'agenda');
    }

    $(document).on('click', '.hc4-dashboard-surface .boton[data-hc4-accent]', function() {
      activarTemaResultadoAmbulatorio($(this).attr('data-hc4-accent'));
    });

    function mostrarErrorAmbulatorio(xhr, accion) {
      var referencia = '';
      var respuesta = xhr && xhr.responseText ? xhr.responseText : '';
      var coincidencia = respuesta.match(/AIT-[0-9]{8}-[0-9]{6}-[A-Z0-9]+/i);

      if (coincidencia) {
        referencia = ' Código de referencia: ' + coincidencia[0] + '.';
      }

      var mensaje = 'No se pudo ' + accion + '. Inténtalo nuevamente.' + referencia;
      if (typeof swal === 'function') {
        swal('No se pudo completar la acción', mensaje, 'error');
      } else {
        alert(mensaje);
      }

      if (window.console && console.error) {
        console.error('Error ambulatorio al ' + accion, xhr);
      }
    }

    //Funcion que permite cargar las consultas y procedimeinto del doctor separados
    function cargar_pacientes_doctor(){
      activarTemaResultadoAmbulatorio('agenda');
      $.ajax({
        type: "GET",
        url: "{{route('busqueda_pacientes_doctor')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'cargar los pacientes del médico');
        }
      });
    }


    //Funcion que permite cargar el horario laboral del Doctor
    function cargar_horario_doctor(){
      activarTemaResultadoAmbulatorio('horario');
      $.ajax({
          type: 'get',
          url:"{{route('obtener.horario_doctor')}}",
          success: function(data){
           $("#area_trabajo").html(data);
          },
          error: function(xhr){
            mostrarErrorAmbulatorio(xhr, 'cargar el horario del médico');
          }
      });
    }

    //Funcion para cargar todo el listado de pacientes del dia
    //Tanto para consulta como para procedimiento
    function cargar_listado_paciente(){
      activarTemaResultadoAmbulatorio('pacientes');
      $.ajax({
        type: "GET",
        url: "{{route('busqueda_fecha')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'cargar el listado de pacientes');
        }
      });
    }

    //Funcion para cargar todas las ordenes de laboratorio del dia
    function cargar_ordenes_laboratorio(){
      activarTemaResultadoAmbulatorio('laboratorio');
      $.ajax({
        type: "GET",
        url: "{{route('obtener.ordenes_lab')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'cargar las órdenes de laboratorio');
        }
      });
    }

    //Funcion para buscar las Ordenes de Procedimientos
    function buscar_ordenes_procedimientos(){
      $.ajax({
        type: "GET",
        url: "{{route('buscar_hc4.ordenes_procedimiento')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'cargar las órdenes de procedimientos');
        }
      });
    }

    //Muestra el listado de medicinas
    function cargar_crear_editar(){
      activarTemaResultadoAmbulatorio('medicinas');
      $.ajax({
        type: "GET",
        url: "{{route('editar.medicina_hc4')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'cargar los medicamentos');
        }
      });
    }


    function plantilla_proc(){
      activarTemaResultadoAmbulatorio('plantillas');
      $.ajax({
        type: "GET",
        url: "{{route('hc4/plantilla_proc.index')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'cargar las plantillas de procedimientos');
        }
      });
    }

    function examenes_favoritos(){
      activarTemaResultadoAmbulatorio('examenes');
      $.ajax({
        type: "GET",
        url: "{{route('hc4_examenes.favoritos')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'cargar los exámenes favoritos');
        }
      });
    }

    //Busqueda por fechas desde-fecha_hasta y por apellido y nombre del Paciente
    function buscador_paciente_fecha(){
        activarTemaResultadoAmbulatorio('agenda');
        $.ajax({
          type: 'post',
          url:"{{route('hc4.busqueda')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_buscador").serialize(),
          success: function(data){
            $("#area_trabajo").html(data);
            //console.log(data);
          },
          error:  function(xhr){
            mostrarErrorAmbulatorio(xhr, 'buscar pacientes');
          }
        });
    }

    //Function para agregar el nuevo paciente
    function cargar_nuevopaciente(){
      $.ajax({
        type: "GET",
        url: "{{route('agregar.paciente_hc4')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'abrir el registro de pacientes');
        }
      });
    }

    function cargar_log_hc(){
      $.ajax({
        type: "GET",
        url: "{{route('agregar.cargar_log_hc')}}",
        data: {
            from: $("#from").val(),
            to: $("#to").val()
        },
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'cargar la trazabilidad clínica');
        }
      });
    }

    function editar_medicina(){
      $.ajax({
        type: "GET",
        url: "{{route('editar.medicina_hc4')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(xhr){
          mostrarErrorAmbulatorio(xhr, 'editar los medicamentos');
        }
       });
    }
  </script>

  <script type="text/javascript">
    $('#agenda_semana').click(function(){
      $.ajax({
          type: 'get',
          url:"{{route('hc4.calendario_fullcalendar')}}",
          success: function(data){
            //console.log(data);
            //alert("ok");
            $("#modificar").html(data);
            //$("#agenda_semana").addClass('oculto');
          },
          error: function(xhr){
            mostrarErrorAmbulatorio(xhr, 'cargar la agenda semanal');
          }
      });
    });
  </script>

  <script type="text/javascript">
    @if(!is_null($request['variable']))
      @if($request['variable'] == 1)
        cargar_ordenes_laboratorio();
      @endif
    @endif

    @if(!is_null($request['variable2']))
      @if($request['variable2'] == 2)
        cargar_listado_paciente();
      @endif
    @endif

    @if(!is_null($request['variable3']))
      @if($request['variable3'] == 3)
        cargar_crear_editar();
      @endif
    @endif

    @if(!is_null($request['variable4']))
      @if($request['variable4'] == 4)
        cargar_pacientes_doctor();
      @endif
    @endif

    @if(!is_null($request['variable5']))
      @if($request['variable5'] == 5)
        editar_medicina();
      @endif
    @endif
  </script>

  <script type="text/javascript">
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  </script>

  <script type="text/javascript">
    window.onload = function(){
      @if($request['variable'] == 1)
      @else
        @if($request['variable2'] == 2)
        @else
          @if($request['variable3'] == 3)
          @else
            cargar_pacientes_doctor();
          @endif
        @endif
      @endif
    };

  </script>
</section>
@endsection
