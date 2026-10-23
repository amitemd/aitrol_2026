@extends('laboratorio.orden.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">

<style type="text/css">
  .orden-control-page {
    padding: 14px 15px 28px !important;
    color: #243746;
    background: #f4f7f9;
  }

  .orden-control-page .control-shell {
    margin: 0;
    background: transparent;
    border: 0;
    box-shadow: none;
  }

  .orden-control-page .control-shell>.box-header {
    padding: 0 0 14px;
    border: 0;
  }

  .orden-control-page .control-shell>.box-header .row,
  .orden-control-page .control-shell>.box-body>.row {
    margin: 0;
  }

  .orden-control-page .control-shell>.box-header h3 {
    margin: 0 0 4px;
    color: #17384f;
    font-size: 21px;
  }

  .orden-control-subtitle {
    margin: 0;
    color: #6c7e8a;
    font-size: 12px;
  }

  .orden-control-page .control-shell>.box-body {
    padding: 0;
  }

  .control-panel {
    padding: 14px 16px 12px;
    background: #fff;
    border: 1px solid #dce5ea;
    border-top: 3px solid #2688c2;
    border-radius: 4px;
  }

  .control-filters {
    display: grid;
    grid-template-columns: 120px 150px 150px 180px minmax(250px, 1fr) 105px;
    gap: 10px;
    align-items: end;
    margin: 0 !important;
  }

  .control-filters::before,
  .control-filters::after,
  .report-actions::before,
  .report-actions::after {
    display: none;
    content: none;
  }

  .control-filters>.form-group {
    width: auto !important;
    min-width: 0;
    float: none;
    margin: 0;
    padding: 0 !important;
  }

  .control-filters>.form-group>label,
  .control-filters>.form-group>div {
    width: 100% !important;
    padding: 0 !important;
  }

  .control-filters label {
    margin-bottom: 5px;
    color: #405563;
    font-size: 11px;
  }

  .control-filters .form-control,
  .control-filters .input-group,
  .control-filters .input-group-addon {
    height: 36px;
  }

  .control-filters .form-control {
    border-color: #cbd7de;
    border-radius: 4px;
    box-shadow: none;
  }

  .control-filters .input-group .form-control {
    height: 36px;
  }

  .control-filters .input-group-addon {
    padding: 7px 9px;
    color: #557180;
    background: #f6f9fb;
    border-color: #cbd7de;
  }

  .control-search-button {
    width: 100%;
    height: 36px;
    margin: 0;
    border: 0;
    border-radius: 4px;
    font-weight: 700;
    background: #1976bd;
  }

  .report-actions {
    display: grid;
    grid-template-columns: repeat(6, minmax(140px, 1fr));
    gap: 8px;
    margin: 12px 0 0 !important;
    padding-top: 12px;
    border-top: 1px solid #e5ebef;
  }

  .report-actions>.form-group {
    width: auto !important;
    min-width: 0;
    float: none;
    margin: 0;
    padding: 0 !important;
  }

  .report-actions .btn {
    width: 100%;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin: 0 !important;
    padding: 0 10px !important;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    white-space: normal;
  }

  .report-actions .btn-default {
    color: #2f5065;
    background: #fff;
    border-color: #bfd0da;
  }

  .control-metrics {
    display: grid;
    grid-template-columns: repeat(4, minmax(150px, 1fr));
    gap: 10px;
    margin: 12px 0;
  }

  .control-metric {
    min-height: 58px;
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 9px 13px;
    background: #fff;
    border: 1px solid #dce5ea;
    border-left: 4px solid #2688c2;
    border-radius: 3px;
  }

  .control-metric:nth-child(2) {
    border-left-color: #e0a321;
  }

  .control-metric:nth-child(3) {
    border-left-color: #159b62;
  }

  .control-metric:nth-child(4) {
    border-left-color: #d94747;
  }

  .control-metric strong {
    display: block;
    margin-bottom: 2px;
    color: #17384f;
    font-size: 18px;
  }

  .control-metric span {
    color: #6f818d;
    font-size: 11px;
  }

  #example2_wrapper {
    overflow: hidden;
    background: #fff;
    border: 1px solid #d8e2e8;
    border-radius: 4px;
  }

  .orders-table-title {
    min-height: 43px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 13px;
    color: #17384f;
    border-bottom: 1px solid #d8e2e8;
  }

  .orders-table-title strong {
    font-size: 14px;
  }

  .orders-table-title span {
    color: #70838f;
    font-size: 11px;
  }

  #example2_wrapper>.row {
    margin: 0;
  }

  #example2_wrapper .table-responsive {
    padding: 0;
  }

  #example2 {
    min-width: 1400px;
    margin: 0;
    table-layout: auto;
    font-size: 11px !important;
    border: 0;
  }

  #example2>thead>tr>th {
    height: 42px;
    padding: 8px 7px;
    color: #405662;
    font-size: 10px;
    line-height: 14px;
    vertical-align: middle;
    text-transform: uppercase;
    background: #edf3f6;
    border: 0;
    border-bottom: 1px solid #cbd8df;
    white-space: normal;
    word-break: normal;
    overflow-wrap: normal;
  }

  #example2>tbody>tr>td {
    height: 76px;
    padding: 9px 7px;
    line-height: 15px;
    vertical-align: middle;
    border-color: #e2e9ed;
    white-space: normal;
    word-break: normal;
    overflow-wrap: normal;
  }

  #example2>tbody>tr:hover {
    background: #f8fbfd;
  }

  #example2>tbody>tr>td:first-child {
    color: #126ca6;
    font-weight: 700;
  }

  #example2 .badge {
    padding: 2px 5px;
    color: #b82e2e;
    font-size: 9px;
    background: #fff0f0;
    border: 1px solid #e8a3a3;
    border-radius: 3px;
  }

  #example2 .progress {
    position: relative;
    height: 24px;
    margin: 0;
    overflow: hidden;
    background: #edf2f6;
    border: 1px solid #d7e3eb;
    border-radius: 6px;
    box-shadow: inset 0 1px 2px rgba(31, 64, 84, .05);
  }

  #example2 .progress-bar {
    min-width: 0;
    box-shadow: none;
  }

  #example2 .progress span {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 2;
    width: 100%;
    color: #17384f !important;
    font-size: 10px;
    font-weight: 700;
    line-height: 22px;
    text-align: center;
  }

  #example2 tbody td:nth-child(9) .btn,
  #example2 tbody td:nth-child(10) .btn,
  #example2 tbody td:nth-child(11) .btn {
    width: 96px;
    min-height: 29px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 3px auto;
    padding: 4px 5px;
    border-radius: 4px;
    font-size: 9px;
    white-space: normal;
  }

  #example2 tbody td:nth-child(12) .btn {
    width: 68px;
    min-height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 6px;
    border-radius: 4px;
    font-size: 10px;
  }

  .acciones-cell {
    padding: 6px !important;
    overflow: visible;
  }

  .order-actions {
    display: grid;
    grid-template-columns: repeat(2, 84px);
    gap: 6px;
    justify-content: center;
  }

  .order-action-btn {
    width: 84px !important;
    height: 30px !important;
    min-height: 30px !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    gap: 4px;
    margin: 0 !important;
    padding: 0 4px !important;
    color: #335163;
    font-size: 9px !important;
    font-weight: 700;
    line-height: 11px;
    text-decoration: none !important;
    background: #fff;
    border: 1px solid #bfd0da;
    border-radius: 4px !important;
    box-shadow: none;
    white-space: normal;
  }

  .order-action-btn.btn-info,
  .order-action-btn.btn-primary {
    color: #176fa7;
    background: #f2f9fd;
    border-color: #82b9da;
  }

  .order-action-btn.btn-success {
    color: #087749;
    background: #f0faf5;
    border-color: #83c8a8;
  }

  .order-action-btn.btn-warning {
    color: #a96008;
    background: #fff8ef;
    border-color: #e2b477;
  }

  .order-action-btn.btn-danger {
    color: #bd3535;
    background: #fff5f5;
    border-color: #e7a3a3;
  }

  .order-action-btn:hover,
  .order-action-btn:focus {
    filter: brightness(.97);
  }

  .control-pagination {
    min-height: 48px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 13px;
  }

  .control-pagination .dataTables_info {
    padding: 0;
    color: #697d89;
    font-size: 11px;
  }

  .control-pagination .dataTables_paginate {
    margin: 0;
    text-align: right;
  }

  .control-pagination>div {
    width: auto !important;
    float: none;
    padding: 0;
  }

  .control-pagination .pagination {
    margin: 0;
  }

  .control-pagination .pagination>li>a,
  .control-pagination .pagination>li>span {
    min-width: 30px;
    min-height: 30px;
    padding: 6px 9px;
    color: #456171;
  }

  #documentos_riesgo_caida .modal-dialog {
    width: 680px;
    max-width: calc(100% - 24px);
    margin: 70px auto;
  }

  #documentos_riesgo_caida .modal-content {
    overflow: hidden;
    border: 0;
    border-radius: 6px;
    box-shadow: 0 24px 70px rgba(8, 28, 41, .35);
  }

  /* Identidad visual inspirada en el panel principal de ordenes */
  .orden-control-page {
    background: #f2f5f8;
  }

  .orden-control-page .control-shell {
    padding: 0 10px 14px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 18px rgba(28, 62, 84, .09);
  }

  .orden-control-page .control-shell>.box-header {
    padding: 16px 8px 13px;
  }

  .orden-control-heading {
    display: flex;
    align-items: center;
    gap: 11px;
  }

  .orden-control-heading-icon {
    width: 36px;
    height: 36px;
    flex: 0 0 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #1675c1;
    font-size: 17px;
    background: #eaf5fd;
    border-radius: 8px;
  }

  .orden-control-page .control-shell>.box-header h3 {
    font-size: 19px;
  }

  .control-panel {
    border: 1px solid #dce7ef;
    border-top: 1px solid #dce7ef;
    border-radius: 9px;
    box-shadow: 0 3px 12px rgba(32, 68, 91, .06);
  }

  .control-filters .form-control {
    border-color: #cfdeea;
    border-radius: 6px;
    transition: border-color .18s ease, box-shadow .18s ease;
  }

  .control-filters .form-control:focus {
    border-color: #2c7be5;
    box-shadow: 0 0 0 3px rgba(44, 123, 229, .12);
  }

  .control-search-button {
    border-radius: 6px;
    background: #2876e6;
    box-shadow: 0 4px 10px rgba(40, 118, 230, .24);
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .control-search-button:hover,
  .control-search-button:focus {
    color: #fff;
    background: #1768d4;
    transform: translateY(-1px);
    box-shadow: 0 6px 13px rgba(40, 118, 230, .3);
  }

  .report-actions {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 9px 20px;
  }

  .report-actions .btn {
    position: relative;
    height: 36px;
    padding: 0 28px 0 12px !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 6px;
    box-shadow: 0 4px 9px rgba(30, 61, 82, .15);
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
  }

  .report-actions .btn::after {
    content: '\203A';
    position: absolute;
    right: 11px;
    top: 50%;
    font-size: 18px;
    line-height: 1;
    transform: translateY(-52%);
  }

  .report-actions>.form-group:nth-child(1) .btn {
    background: #ff8617;
  }

  .report-actions>.form-group:nth-child(2) .btn {
    background: #09a664;
  }

  .report-actions>.form-group:nth-child(3) .btn,
  .report-actions>.form-group:nth-child(4) .btn {
    background: #2876e6;
  }

  .report-actions>.form-group:nth-child(5) .btn {
    background: #f44747;
  }

  .report-actions>.form-group:nth-child(6) .btn {
    background: #e63b45;
  }

  .report-actions .btn:hover,
  .report-actions .btn:focus {
    color: #fff;
    filter: brightness(.96);
    transform: translateY(-1px);
    box-shadow: 0 6px 13px rgba(30, 61, 82, .22);
  }

  .control-metric {
    border: 0;
    border-left: 4px solid #2876e6;
    border-radius: 8px;
    background: #edf5ff;
    box-shadow: 0 2px 8px rgba(32, 68, 91, .07);
  }

  .control-metric:nth-child(2) {
    background: #fff7e8;
  }

  .control-metric:nth-child(3) {
    background: #eafaf3;
  }

  .control-metric:nth-child(4) {
    background: #fff0f0;
  }

  #example2_wrapper {
    border-color: #dce7ef;
    border-radius: 9px;
    box-shadow: 0 4px 14px rgba(32, 68, 91, .07);
  }

  #example2>thead>tr>th {
    color: #193449;
    background: #f8fbfd;
    border-bottom-color: #d6e4ed;
    padding-right: 18px;
  }

  #example2>thead>tr>th:nth-child(1),
  #example2>tbody>tr>td:nth-child(1) {
    min-width: 78px;
  }

  #example2>thead>tr>th:nth-child(2),
  #example2>tbody>tr>td:nth-child(2) {
    min-width: 88px;
    text-align: center;
  }

  #example2>tbody>tr>td:nth-child(1),
  #example2>tbody>tr>td:nth-child(2) {
    white-space: nowrap;
  }

  #example2>thead>tr>th:nth-child(3),
  #example2>tbody>tr>td:nth-child(3) {
    min-width: 185px;
  }

  #example2>thead>tr>th:nth-child(4),
  #example2>tbody>tr>td:nth-child(4) {
    min-width: 105px;
  }

  #example2>thead>tr>th:nth-child(5),
  #example2>tbody>tr>td:nth-child(5) {
    min-width: 76px;
  }

  #example2>thead>tr>th:nth-child(6),
  #example2>tbody>tr>td:nth-child(6) {
    min-width: 95px;
  }

  #example2>thead>tr>th:nth-child(7),
  #example2>tbody>tr>td:nth-child(7) {
    min-width: 105px;
    text-align: center;
  }

  #example2>thead>tr>th:nth-child(8),
  #example2>tbody>tr>td:nth-child(8) {
    min-width: 155px;
  }

  #example2>thead>tr>th:nth-child(9),
  #example2>tbody>tr>td:nth-child(9),
  #example2>thead>tr>th:nth-child(10),
  #example2>tbody>tr>td:nth-child(10),
  #example2>thead>tr>th:nth-child(11),
  #example2>tbody>tr>td:nth-child(11) {
    min-width: 132px;
    text-align: center;
  }

  #example2>thead>tr>th:nth-child(12),
  #example2>tbody>tr>td:nth-child(12) {
    min-width: 105px;
    text-align: center;
  }

  #example2>thead>tr>th:nth-child(13),
  #example2>tbody>tr>td:nth-child(13) {
    min-width: 132px;
    width: 132px;
    text-align: center;
  }

  #example2>tbody>tr.orden-row-ready>td {
    background: #eafaf4;
  }

  #example2>tbody>tr.orden-row-review>td {
    background: #fff8e7;
  }

  #example2>tbody>tr.orden-row-pending>td {
    background: #fff1f1;
  }

  #example2>tbody>tr:hover>td {
    filter: brightness(.985);
  }

  .order-action-btn {
    color: #fff;
    border: 0;
    border-radius: 5px !important;
    box-shadow: 0 2px 5px rgba(24, 54, 74, .15);
    transition: transform .16s ease, box-shadow .16s ease, filter .16s ease;
  }

  .order-action-btn.btn-info,
  .order-action-btn.btn-primary {
    color: #fff;
    background: #2876e6;
    border-color: transparent;
  }

  .order-action-btn.btn-success {
    color: #fff;
    background: #09a664;
    border-color: transparent;
  }

  .order-action-btn.btn-warning {
    color: #fff;
    background: #ff8617;
    border-color: transparent;
  }

  .order-action-btn.btn-danger {
    color: #fff;
    background: #f44747;
    border-color: transparent;
  }

  .order-action-btn:hover,
  .order-action-btn:focus {
    color: #fff;
    filter: brightness(.96);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(24, 54, 74, .22);
  }

  .order-actions-dropdown {
    position: relative;
    z-index: 1;
  }

  .order-actions-toggle {
    width: 96px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0 10px;
    color: #fff !important;
    font-size: 10px;
    font-weight: 700;
    background: #2876e6;
    border: 0;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(40, 118, 230, .25);
    transition: transform .16s ease, box-shadow .16s ease;
  }

  .order-actions-toggle:hover,
  .order-actions-toggle:focus,
  .order-actions-dropdown.open .order-actions-toggle {
    color: #fff;
    background: #1768d4;
    transform: translateY(-1px);
    box-shadow: 0 5px 11px rgba(40, 118, 230, .32);
  }

  .order-actions-dropdown .dropdown-menu {
    right: 0;
    left: auto;
    z-index: 9999;
    min-width: 330px;
    padding: 8px;
    background: #fff !important;
    border: 1px solid #d8e4ec;
    border-radius: 8px;
    box-shadow: 0 14px 34px rgba(25, 54, 73, .22);
  }

  #example2>tbody>tr.actions-menu-open {
    position: relative;
    z-index: 3000;
  }

  #example2>tbody>tr.actions-menu-open>td {
    position: relative;
    z-index: 3000;
  }

  #example2>tbody>tr.actions-menu-open .order-actions-dropdown {
    z-index: 9999;
  }

  .order-actions-dropdown.open .dropdown-menu {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 5px;
  }

  .order-actions-dropdown .dropdown-menu>li {
    min-width: 0;
    margin: 0;
  }

  .order-actions-dropdown .order-menu-item {
    width: 100%;
    min-height: 34px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 6px 9px;
    color: #314d5f;
    font-size: 10px;
    font-weight: 600;
    line-height: 14px;
    text-align: left;
    text-decoration: none;
    white-space: normal;
    background: #f7fafc;
    border: 1px solid #e2ebf0;
    border-radius: 5px;
  }

  .order-actions-dropdown button.order-menu-item {
    font-family: inherit;
  }

  .order-actions-dropdown .order-menu-item i,
  .order-actions-dropdown .order-menu-item>span {
    width: 20px;
    height: 20px;
    flex: 0 0 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #2876e6;
    background: #eaf3ff;
    border-radius: 4px;
  }

  .order-actions-dropdown .order-menu-item:hover,
  .order-actions-dropdown .order-menu-item:focus {
    color: #17384f;
    background: #eef6fc;
    border-color: #bdd8ea;
    outline: 0;
  }

  .order-actions-dropdown .order-menu-item.menu-warning i,
  .order-actions-dropdown .order-menu-item.menu-warning>span {
    color: #bf6700;
    background: #fff1df;
  }

  .order-actions-dropdown .order-menu-item.menu-success i,
  .order-actions-dropdown .order-menu-item.menu-success>span {
    color: #078653;
    background: #e7f8f0;
  }

  .order-actions-dropdown .order-menu-item.menu-danger i,
  .order-actions-dropdown .order-menu-item.menu-danger>span {
    color: #d63b3b;
    background: #ffebeb;
  }

  /* Escala de lectura para pantallas de escritorio */
  .orden-control-page {
    font-size: 13px;
  }

  .control-filters {
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 12px;
  }

  .orden-control-page .control-shell>.box-header h3 {
    font-size: 22px;
    line-height: 27px;
  }

  .orden-control-subtitle {
    font-size: 13px;
    line-height: 18px;
  }

  .orden-control-heading-icon {
    width: 42px;
    height: 42px;
    flex-basis: 42px;
    font-size: 20px;
  }

  .control-panel {
    padding: 17px 18px 15px;
  }

  .control-filters label {
    margin-bottom: 6px;
    font-size: 13px;
  }

  .control-filters .form-control,
  .control-filters .input-group,
  .control-filters .input-group-addon {
    height: 40px;
    font-size: 13px;
  }

  .control-filters .input-group .form-control {
    height: 40px;
  }

  .control-search-button {
    height: 42px;
    font-size: 14px;
  }

  .report-actions .btn {
    height: 44px;
    font-size: 14px;
    line-height: 17px;
  }

  .control-metric {
    min-height: 70px;
    padding: 11px 15px;
  }

  .control-metric strong {
    font-size: 21px;
  }

  .control-metric span {
    font-size: 12px;
    line-height: 16px;
  }

  .orders-table-title {
    min-height: 48px;
  }

  .orders-table-title strong {
    font-size: 16px;
  }

  .orders-table-title span {
    font-size: 12px;
  }

  #example2 {
    min-width: 1580px;
    font-size: 13px !important;
  }

  #example2>thead>tr>th {
    height: 48px;
    padding: 9px 18px 9px 8px;
    font-size: 11px;
    line-height: 15px;
  }

  #example2>tbody>tr>td {
    min-height: 82px;
    padding: 10px 8px;
    font-size: 12px;
    line-height: 17px;
  }

  #example2 .progress span {
    font-size: 11px;
  }

  #example2 tbody td:nth-child(9) .btn,
  #example2 tbody td:nth-child(10) .btn,
  #example2 tbody td:nth-child(11) .btn {
    width: 112px;
    min-height: 36px;
    font-size: 12px;
    line-height: 15px;
  }

  #example2 tbody td:nth-child(12) .btn {
    width: 86px;
    min-height: 36px;
    font-size: 12px;
    line-height: 15px;
  }

  #example2 tbody td:nth-child(9) .btn span,
  #example2 tbody td:nth-child(10) .btn span,
  #example2 tbody td:nth-child(11) .btn span {
    font-size: 12px !important;
    line-height: 15px;
  }

  .order-actions-toggle {
    width: 112px;
    height: 38px;
    font-size: 13px;
  }

  .order-actions-dropdown .dropdown-menu {
    min-width: 370px;
    padding: 10px;
  }

  .order-actions-dropdown .order-menu-item {
    min-height: 42px;
    padding: 9px 11px;
    font-size: 13px;
    line-height: 17px;
  }

  .control-pagination .dataTables_info,
  .control-pagination .pagination>li>a,
  .control-pagination .pagination>li>span {
    font-size: 12px;
  }

  /* Tabla completa dentro del ancho disponible en escritorio */
  #example2_wrapper,
  #example2_wrapper .table-responsive {
    overflow: visible;
  }

  #example2 {
    width: 100% !important;
    min-width: 0;
    table-layout: fixed;
  }

  #example2>thead>tr>th,
  #example2>tbody>tr>td {
    min-width: 0 !important;
    box-sizing: border-box;
    overflow-wrap: break-word;
  }

  #example2>thead>tr>th:nth-child(1),
  #example2>tbody>tr>td:nth-child(1) { width: 5.5%; }

  #example2>thead>tr>th:nth-child(2),
  #example2>tbody>tr>td:nth-child(2) { width: 6.5%; }

  #example2>thead>tr>th:nth-child(3),
  #example2>tbody>tr>td:nth-child(3) { width: 13%; }

  #example2>thead>tr>th:nth-child(4),
  #example2>tbody>tr>td:nth-child(4) { width: 7.5%; }

  #example2>thead>tr>th:nth-child(5),
  #example2>tbody>tr>td:nth-child(5) { width: 5.5%; }

  #example2>thead>tr>th:nth-child(6),
  #example2>tbody>tr>td:nth-child(6) { width: 7%; }

  #example2>thead>tr>th:nth-child(7),
  #example2>tbody>tr>td:nth-child(7) { width: 7.5%; }

  #example2>thead>tr>th:nth-child(8),
  #example2>tbody>tr>td:nth-child(8) { width: 10%; }

  #example2>thead>tr>th:nth-child(9),
  #example2>tbody>tr>td:nth-child(9),
  #example2>thead>tr>th:nth-child(10),
  #example2>tbody>tr>td:nth-child(10) { width: 7.5%; }

  #example2>thead>tr>th:nth-child(11),
  #example2>tbody>tr>td:nth-child(11) { width: 8.5%; }

  #example2>thead>tr>th:nth-child(12),
  #example2>tbody>tr>td:nth-child(12) { width: 6%; }

  #example2>thead>tr>th:nth-child(13),
  #example2>tbody>tr>td:nth-child(13) { width: 8%; }

  #example2 tbody td:nth-child(9) .btn,
  #example2 tbody td:nth-child(10) .btn,
  #example2 tbody td:nth-child(11) .btn,
  #example2 tbody td:nth-child(12) .btn,
  .order-actions-toggle {
    width: 100% !important;
    max-width: 112px;
    padding-right: 4px;
    padding-left: 4px;
  }

  .order-actions-dropdown {
    width: 100%;
    max-width: 112px;
  }

  @media (max-width: 1100px) {
    .control-filters {
      grid-template-columns: repeat(3, 1fr);
    }

    .report-actions {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 767px) {
    .control-filters,
    .report-actions,
    .control-metrics {
      grid-template-columns: 1fr;
    }

    .control-pagination {
      display: block;
    }

    .control-pagination .dataTables_paginate {
      margin-top: 8px;
      text-align: left;
    }

    #example2_wrapper .table-responsive {
      overflow-x: auto;
      overflow-y: visible;
    }

    #example2 {
      min-width: 1380px;
      table-layout: auto;
    }
  }
</style>

<div class="modal fade" id="documentos_riesgo_caida" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<!-- Ventana modal editar -->
<div class="modal fade" id="log_muestra" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="log_comentario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal_query" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="modal_query">
    </div>
  </div>
</div>
<!-- Main content -->
<section class="content orden-control-page">
  <div class="box control-shell">
    <div class="box-header">
      <div class="row">
        <div class="col-md-12" id="pentax">

        </div>

        <div class="col-md-12">
          <div class="orden-control-heading">
            <div class="orden-control-heading-icon"><i class="fa fa-flask" aria-hidden="true"></i></div>
            <div>
          <h3 class="box-title"><b>Control de Exámenes de Laboratorio</b></h3>
          <p class="orden-control-subtitle">Consulta, procesa y da seguimiento a las &oacute;rdenes del laboratorio.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <form method="POST" action="{{route('orden.search_control')}}" id="lab_control" class="control-panel">
        {{ csrf_field() }}
        <div class="row control-filters">
          <div class="form-group col-md-1 col-xs-6">
            <label for="orden" class="col-md-12 control-label">Orden</label>
            <div class="col-md-12">
              <input type="text" class="form-control input-sm" name="orden" id="orden" autocomplete="off" value="{{$norden}}">
            </div>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <label for="fecha" class="col-md-12 control-label">Desde</label>
            <div class="col-md-12">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <label for="fecha_hasta" class="col-md-12 control-label">Hasta</label>
            <div class="col-md-12">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <label for="seguro" class="col-md-12 control-label">Seguro</label>
            <div class="col-md-12">
              <select id="seguro" name="seguro" class="form-control input-sm" onchange="buscar();">
                <option value="">TODOS</option>
                @foreach ($seguros as $value)
                <option @if(!is_null($seguro))@if($seguro==$value->id) selected @endif @endif value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-md-4 col-xs-6">
            <label for="nombres" class="col-md-12 control-label">Paciente</label>
            <div class="col-md-12">
              <div class="input-group">
                <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group col-md-1 col-xs-6">
            <button type="submit" class="btn btn-primary control-search-button" id="boton_buscar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
          </div>
        </div>
        <div class="row report-actions">
          <div class="form-group col-md-1 col-xs-6">
            <button type="submit" class="btn btn-default" formaction="{{route('orden.reporte_index')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Reporte</button>
          </div>
          <div class="form-group col-md-2 col-xs-3">
            <button type="submit" class="btn btn-danger" formaction="{{route('orden_labs.resultados_pendientes2')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Detalle Examenes Pendientes</button>
          </div>

          <div class="form-group col-md-2 col-xs-3">
            <button type="button" class="btn btn-danger" onclick="proceso_revision_resultados();"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Listado Examenes Pendientes</button>
          </div>
        </div>
      </form>

      @php
        $muestrasPagina = 0;
        $pendientesPagina = 0;
        $prioridadesPagina = 0;
        foreach ($ordenes as $ordenResumen) {
            if (!is_null($ordenResumen->toma_muestra)) {
                $muestrasPagina++;
            }
            if ($ordenResumen->prioridad_orden == '1') {
                $prioridadesPagina++;
            }
            $tienePendiente = false;
            if (isset($arr_or[$ordenResumen->id]['hema']) && $arr_or[$ordenResumen->id]['hema'] > 0 && $ordenResumen->er_biometria != '2') {
                $tienePendiente = true;
            }
            if (isset($arr_or[$ordenResumen->id]['bio']) && $arr_or[$ordenResumen->id]['bio'] > 0 && $ordenResumen->er_bioquimica != '2') {
                $tienePendiente = true;
            }
            if (isset($arr_or[$ordenResumen->id]['man']) && $arr_or[$ordenResumen->id]['man'] > 0 && $ordenResumen->er_manual != '2') {
                $tienePendiente = true;
            }
            if ($tienePendiente) {
                $pendientesPagina++;
            }
        }
      @endphp

      <div class="control-metrics">
        <div class="control-metric">
          <div><strong>{{$ordenes->total()}}</strong><span>&Oacute;rdenes encontradas</span></div>
        </div>
        <div class="control-metric">
          <div><strong>{{$pendientesPagina}}</strong><span>Con resultados pendientes en esta p&aacute;gina</span></div>
        </div>
        <div class="control-metric">
          <div><strong>{{$muestrasPagina}}</strong><span>Con toma de muestra en esta p&aacute;gina</span></div>
        </div>
        <div class="control-metric">
          <div><strong>{{$prioridadesPagina}}</strong><span>Prioridad alta en esta p&aacute;gina</span></div>
        </div>
      </div>
      
        

        

        

        

        

        

        {{-- <button type="submit" class="btn btn-success" formaction="{{route('orden.reporte_detalle_covid')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Covid</button> --}}

        {{--   <button type="submit" class="btn btn-danger" formaction="{{route('orden_labs.resultados_pendientes')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> {{trans('laboratorio.examenes_pendientes')}}</button> --}}
        

        
        
        

        <!--div class="form-group col-md-3">
          <a class="btn btn-primary" href="{{ route('orden_particular.crear_particular')}}"><span class="ionicons ion-ios-flask"></span> Orden Particular</a>
        </div-->


      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="orders-table-title">
          <strong>&Oacute;rdenes del laboratorio</strong>
          <span>{{$ordenes->count()}} registros en esta p&aacute;gina</span>
        </div>
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
              <thead>
                <tr role="row">
                  <th>Orden</th>
                  <th>Fecha</th>
                  <th>Nombres</th>
                  <th>Seguro</th>
                  <th>Tipo</th>
                  <th>Creada</th>
                  <th>Toma Muestra</th>
                  <th>Exámenes Ingresados(%)</th>
                  <th>Hematología</th>
                  <th>Bioquímica</th>
                  <th>Especiales</th>
                  <th>Cantidad de tubos</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody> 
                @foreach ($ordenes as $value)
                @php
                  $tieneHematologia = isset($arr_or[$value->id]['hema']) && $arr_or[$value->id]['hema'] > 0;
                  $tieneBioquimica = isset($arr_or[$value->id]['bio']) && $arr_or[$value->id]['bio'] > 0;
                  $tieneEspeciales = isset($arr_or[$value->id]['man']) && $arr_or[$value->id]['man'] > 0;
                  $areasActivas = ($tieneHematologia ? 1 : 0) + ($tieneBioquimica ? 1 : 0) + ($tieneEspeciales ? 1 : 0);
                  $areasListas = ($tieneHematologia && $value->er_biometria == '2' ? 1 : 0)
                    + ($tieneBioquimica && $value->er_bioquimica == '2' ? 1 : 0)
                    + ($tieneEspeciales && $value->er_manual == '2' ? 1 : 0);
                  $requiereRevision = ($tieneHematologia && $value->er_biometria == '1')
                    || ($tieneBioquimica && $value->er_bioquimica == '1')
                    || ($tieneEspeciales && $value->er_manual == '1');
                  $claseEstadoFila = $areasActivas > 0 && $areasListas == $areasActivas
                    ? 'orden-row-ready'
                    : ($requiereRevision ? 'orden-row-review' : ($areasActivas > 0 ? 'orden-row-pending' : ''));
                @endphp
                <tr role="row" id="{{$value->id}}" class="{{$claseEstadoFila}}">
                  <td>{{$value->id}} &nbsp;&nbsp; 
                      @if($value->prioridad_orden == '1')
                      <abbr title="!Prioridad Alta!">
                        <span class="badge">
                          <i class="fa fa-bell" aria-hidden="true"></i>
                        </span>
                      </abbr>
                      @endif
		  </td>
                  <td>{{substr($value->fecha_orden,0,10)}}</td>
                  <td>{{$value->paciente->apellido1}} @if($value->paciente->apellido2=='N/A'||$value->paciente->apellido2=='(N/A)') @else{{ $value->paciente->apellido2 }} @endif {{$value->paciente->nombre1}} @if($value->paciente->nombre2=='N/A'||$value->paciente->nombre2=='(N/A)') @else{{ $value->paciente->nombre2 }} @endif </td>
                  <td>{{$value->seguro->nombre}}</td>
                  <td>{{$value->pre_post}}</td>
                  <td>{{substr($value->crea->nombre1,0,1)}}{{$value->crea->apellido1}}</td>
                  <td>{{$value->toma_muestra}}@if($value->toma_muestras->count() > 0)<a data-toggle="modal" data-target="#log_muestra" data-remote="{{route('orden.log_toma_muestras',['id' => $value->id ])}}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i></a>@endif</td>
                  <!--td>{{substr($value->modifica->nombre1,0,1)}}{{$value->modifica->apellido1}}</td-->
                  <td>
                    <div class="progress progress">
                      <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" id="td{{$value->id}}">
                        <span id="sp{{$value->id}}" style="color: black;"></span>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($value->er_biometria=='2')
                    @php $texto = 'LISTO'; $color = 'btn btn-success btn-xs'; @endphp
                    @elseif($value->er_biometria=='1')
                    @php $texto = 'CERTIFICAR'; $color = 'btn btn-warning btn-xs'; @endphp
                    @else
                    @php $texto = 'PENDIENTE'; $color = 'btn btn-danger btn-xs'; @endphp
                    @endif

                    @if($arr_or[$value->id]['hema']>0)
                    <button class="{{$color}}" onclick="resultado({{$value->id}},'1');">
                      <span class="glyphicon glyphicon-check" style="font-size: 10px;"> {{$texto}}</span>
                    </button>
                    @endif
                  </td>
                  <td>
                    @if($value->er_bioquimica=='2')
                    @php $texto = 'LISTO'; $color = 'btn btn-success btn-xs'; @endphp
                    @elseif($value->er_bioquimica=='1')
                    @php $texto = 'CERTIFICAR'; $color = 'btn btn-warning btn-xs'; @endphp
                    @else
                    @php $texto = 'PENDIENTE'; $color = 'btn btn-danger btn-xs'; @endphp
                    @endif
                    @if($arr_or[$value->id]['bio']>0)
                    <button class="{{$color}}" onclick="resultado({{$value->id}},'2');">
                      <span class="glyphicon glyphicon-check" style="font-size: 10px;"> {{$texto}}</span>
                    </button>
                    @endif
                  </td>
                  <td>
                    @if($value->er_manual=='2')
                    @php $texto = 'LISTO'; $color = 'btn btn-success btn-xs'; @endphp
                    @elseif($value->er_manual=='1')
                    @php $texto = 'CERTIFICAR'; $color = 'btn btn-warning btn-xs'; @endphp
                    @else
                    @php $texto = 'PENDIENTE'; $color = 'btn btn-danger btn-xs'; @endphp
                    @endif
                    @if($arr_or[$value->id]['man']>0)
                    <button class="{{$color}}" onclick="resultado({{$value->id}},'0');">
                      <span class="glyphicon glyphicon-check" style="font-size: 10px;"> {{$texto}}</span>
                    </button>
                    <a class="btn btn-success btn-xs" href="{{route('orden.resultado_especial',['id' => $value->id])}}" >Panel Alimentos</a> 
                    @endif
                  </td>
                  <td>
                    <a data-toggle="modal" data-target="#modal_query" data-remote="{{route('orden.query.tubo',['id'=> $value->id])}}" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Tubos</a>
                  </td>
                  <td class="acciones-cell">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="btn-group order-actions-dropdown">
                      <button type="button"
                        class="btn dropdown-toggle order-actions-toggle"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">
                        <i class="fa fa-cog" aria-hidden="true"></i>
                        Acciones
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                          <button type="button"
                            class="order-menu-item @if($value->toma_muestra!=null) menu-success @else menu-danger @endif"
                            onclick="confirmar_impresion('{{$value->id}}')">
                            <span class="fa fa-barcode"></span>
                            C&oacute;digo de barras
                          </button>
                        </li>

                        @if($value->seguro->tipo!='0')
                          @if(Auth::user()->id_tipo_usuario=='1')
                          <li>
                            <a href="{{ route('cotizador.editar', ['id' => $value->id]) }}"
                              class="order-menu-item menu-warning">
                              <span class="glyphicon glyphicon-edit"></span>
                              Editar orden
                            </a>
                          </li>
                          @endif
                          <li>
                            <a target="_blank"
                              href="{{ route('cotizador.imprimir_orden', ['id' => $value->id]) }}"
                              class="order-menu-item menu-success">
                              <span class="fa fa-download"></span>
                              Descargar orden
                            </a>
                          </li>
                        @else
                          @if(Auth::user()->id_tipo_usuario=='1')
                          <li>
                            <a href="{{ route('orden.edit1_c', ['id' => $value->id]) }}"
                              class="order-menu-item menu-warning">
                              <span class="glyphicon glyphicon-edit"></span>
                              Editar orden
                            </a>
                          </li>
                          @endif
                          <li>
                            <a href="{{ route('orden.detalle', ['id' => $value->id, 'dir' => 'CON']) }}"
                              class="order-menu-item menu-success">
                              <span class="fa fa-download"></span>
                              Descargar orden
                            </a>
                          </li>
                        @endif

                        
                        <li>
                          <a class="order-menu-item"
                            data-remote="{{ route('modal.labs',['id_orden'=>$value->id])}}"
                            data-toggle="modal"
                            data-target="#documentos_riesgo_caida">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                            Formularios
                          </a>
                        </li>
                        <li>
                          <a class="order-menu-item menu-success"
                            href="{{route('plantilla.orden_plantilla', ['id' => $value->id])}}">
                            <i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>
                            Plantilla
                          </a>
                        </li>
                        <li>
                          <a class="order-menu-item menu-success"
                            href="javascript:;"
                            onclick="abrirComparativo('<?= $value->id; ?>');">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            Comparar resultados
                          </a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="row control-pagination">
          <div class="col-sm-5">
            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($ordenes->currentPage()-1)*$ordenes->perPage()}} / @if(($ordenes->currentPage()*$ordenes->perPage())<$ordenes->total()){{($ordenes->currentPage()*$ordenes->perPage())}} @else {{$ordenes->total()}} @endif de {{$ordenes->total()}} registros</div>
          </div>
          <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
              {{ $ordenes->appends(Request::only(['fecha', 'fecha_hasta', 'nombres', 'seguro']))->links()}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
</section>
<!-- /.content -->

<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>

<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  $(document).ready(function($) {
    $(document)
      .on('show.bs.dropdown', '.order-actions-dropdown', function() {
        $('#example2 tbody tr').removeClass('actions-menu-open');
        $(this).closest('tr').addClass('actions-menu-open');
      })
      .on('hidden.bs.dropdown', '.order-actions-dropdown', function() {
        $(this).closest('tr').removeClass('actions-menu-open');
      });

    <?php /* @foreach($ordenes as $value)

    $.ajax({
      type: 'get',
      url: "{{ route('resultados.puede_imprimir',['id' => $value->id]) }}",

      success: function(data) {

        if (data.cant_par == 0) {
          var pct = 0;
        } else {
          var pct = data.certificados / data.cant_par * 100;
        }

        //console.log(data);
        $('#td{{$value->id}}').css("width", Math.round(pct) + "%");
        $('#sp{{$value->id}}').text(Math.round(pct) + "%");
        if (pct < 10) {
          $('#td{{$value->id}}').addClass("progress-bar-danger");
        } else if (pct >= 10 && pct < 90) {
          $('#td{{$value->id}}').addClass("progress-bar-warning");
        } else {
          $('#td{{$value->id}}').addClass("progress-bar-success");
        }


      },


      error: function(data) {


      }
    });

    @endforeach */ ?>

    $("tr").each(function(){
      
      var id_orden = $(this)[0].id;
      console.log(id_orden);
      if(id_orden != ''){
        cargar_barra(id_orden);
      }
      
      
    });

    function cargar_barra(id){
 
      $.ajax({
        type: 'get',
        url: "{{ url('valida/imprimir') }}/"+id,

        success: function(data) {

          if (data.cant_par == 0) {
            var pct = 0;
          } else {
            var pct = data.certificados / data.cant_par * 100;
          }

          console.log(data, id);
          $('#td'+id).css("width", Math.round(pct) + "%");
          $('#sp'+id).text(Math.round(pct) + "%");
          if (pct < 10) {
            $('#td'+id).addClass("progress-bar-danger");
          } else if (pct >= 10 && pct < 90) {
            $('#td'+id).addClass("progress-bar-warning");
          } else {
            $('#td'+id).addClass("progress-bar-success");
          }
          


        },


        error: function(data) {


        }
      }); 

    }

    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',


      @if($fecha != null) defaultDate: '{{$fecha}}',
      @endif

    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',


      defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha").on("dp.change", function(e) {
      buscar();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      buscar();
    });

    //pentax_semaforo();

    $(".breadcrumb").append('<li class="active">Órdenes</li>');

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': false,
      'autoWidth': false,
      'order': [
        [1, "desc"]
      ]
    })

  });

  $('#modal_query').on('hidden.bs.modal', function() {
    // location.reload();
    $(this).removeData('bs.modal');
  });
  $('#doctor').on('hidden.bs.modal', function() {
    location.reload();
    $(this).removeData('bs.modal');
  });
  $('#log_muestra').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });
  $('#log_comentario').on('hidden.bs.modal', function() {
    $(this).removeData('bs.modal');
  });

  function abrirComparativo(id) {
    $.ajax({
      url: '<?= url('/') ?>/labs/plantilla/labs/comparativo/' + id,
      type: 'post',
      data: {
        _token: $('input[name=_token]').val(),
        id: id
      },
      success: function(json) {
        {
          if (json.status != false) {
            window.open('<?= url('/') ?>/labs/plantilla/labs/comparativo/' + id, '_self');
          } else {
            alertas('error', json.msj, '');
          }
        }
      }
    });
  }

  function buscar() {
    var obj = document.getElementById("boton_buscar");
    obj.click();
  }


  function pentax_semaforo() {

    $.ajax({
      type: 'get',
      url: "{{route('orden.pentax_semaforo')}}",
      //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      //datatype: 'json',
      //data: $("#cie10"),
      success: function(data) {
        //alert(data);
        $("#pentax").empty().html(data);

      },
      error: function(data) {

      }
    })


  }

  function resultado(id, maq) {
    //alert(maq);

    $('#lab_control').attr("action", "{{url('orden/realizar')}}/" + id + "/" + maq);
    //console.log($('#lab_control'));
    $("#lab_control").submit();

  }

  function enviar_resultado_dr(id_orden) {
    var confirmar_correo = confirm("¿Desea enviar los resultados al Dr. Externo?");
    if (confirmar_correo) {
      $.ajax({
        type: 'get',
        url: "{{url('envio/email_doctor_externo')}}/" + id_orden,
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        success: function(data) {
          console.log(data);
          if (data == "okay") {
            alert('Correo Exitoso.');
            location.reload();
          }
        },
      });
    }
  }

  function ___codigo___barras___(id) {

    var confirmar = confirm("Al generar el código se tomará como tomada la muestra al paciente");
    if (confirmar) {
      $.ajax({
        type: 'get',
        url: "{{url('examen_orden/toma_muestra')}}/" + id,

        success: function(data) {

          window.open("{{url('imprimir/codigo_barras/laboratorio')}}/" + id, "_blank");
          location.reload();

        },
        error: function(data) {}
      })
    }
  }
  // vartiempo = setInterval(function() {
  //   pentax_semaforo();
  // }, 50000);

  // vartiempo2 = setInterval(function() {
  //   buscar();
  // }, 500000);

  const alertas = (icon, title, text) => {
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      html: `${text}`,
    })
  }

  function verificar_etiqueta(id) {
    $.ajax({
      type: 'get',
      url: "{{url('laboratorio/verificar/impresion')}}/" + id,
      success: function(data) {
        if (data.status == 'success') {
          codigo_barras(id);
        } else {
          alertas(data.status, ``, data.message)
        }
      },
      error: function(data) {

      }
    })
  }

  function confirmar_impresion(id) {
    Swal.fire({
      title: `Al generar el código se tomará como tomada la muestra al paciente`,
      text: "",
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: `{{trans('contableM.sideseoconti')}}`
    }).then((result) => {
      if (result.isConfirmed) {
        test(id)
      }
    })
  }

  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        input: 'textarea',
        inputPlaceholder: 'Ingrese una observación...',
        inputAttributes: {
          'aria-label': 'Ingrese una observación'
        },
        showCancelButton: true
        
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{url('examen_orden/toma_muestra')}}/" + id,
          datatype: 'json',
          data: {
            'observacion': text
          },
          success: function(data) {
            window.open("{{url('imprimir/codigo_barras/laboratorio')}}/" + id, "_blank");
            location.reload();
          },
          error: function(data) {
            console.log(data);
          }
        });
      }
    } catch (err) {
      console.log(err);
    }
  }

  function __codigo_barras__(id) {
    //var confirmar = confirm("Al generar el código se tomará como tomada la muestra al paciente");
    Swal.fire({
      title: `Al generar el código se tomará como tomada la muestra al paciente`,
      text: "",
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: `{{trans('contableM.sideseoconti')}}`
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'get',
          url: "{{url('examen_orden/toma_muestra')}}/" + id,

          success: function(data) {

            window.open("{{url('imprimir/codigo_barras/laboratorio')}}/" + id, "_blank");
            location.reload();

          },
          error: function(data) {

          }
        })
      }
    })
  }

  const mensajeAgenda = id => {
    console.log(id)
    Swal.fire({
      title: 'Do you want to save the changes?',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Save',
      denyButtonText: `Don't save`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        Swal.fire('Saved!', '', 'success')
      } else if (result.isDenied) {
        Swal.fire('Changes are not saved', '', 'info')
      }
    })
  }

  
</script>

<script>
    document.getElementById('cerrar').addEventListener('click', () => {
        $('#log_comentario').click()
    });
</script>

<script>
    function eliminar_comentario(id) {
    $.ajax({
      type: 'get',
      url: "{{url('comentario/orden/labs/eliminar_comentario/')}}/" + id,

      success: function(data) {
        if (data.status == 'success') {
          //console.log();
          Swal.fire({ position: 'center', icon: 'success', title: data.msj, showConfirmButton: false, timer: 2000 })
          setTimeout(() => {
                    $('#log_comentario').click();
          }, 2000)
        } else {
          alertas(data.status, ``, data.message)
        }
      },
      error: function(data) {
        alertas("error", "Error", "Ocurrio un error");
        return;

      }
    })
  }
</script>


@endsection
