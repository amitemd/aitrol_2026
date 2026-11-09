@extends('hc4.base2')
@section('action-content')

<style type="text/css">
	.boton-2{
		font-size: 9px ;
		width: 100%;
		background-color: #004AC1;
		color: white;
		text-align: center;
		height: 35px;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 0px;
		padding-top: 7px;
		margin-bottom: 5px;
	}

	.boton-buscar{
		font-size: 14px ;
		width: 70%;
		height: 35px;
		background-color: #004AC1;
		color: white;
		text-align: center;

	}

	.btn_ordenes{
		font-size: 10px ;
		width: 100%;
		background-color: #004AC1;
		color: white;
		text-align: center;
		height: 22px;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 0px;
		padding-top: 2px;
		margin-bottom: 5px;


	}


	.btn_accion{
		font-size: 9px ;
		width: 95px;
		background-color: #004AC1;
		color: white;
		text-align: center;
		height: 20px;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 0px;
		padding-top: 2px;
		margin-bottom: 0px;

	}

	.recuadro{
			height: 200px;
			margin-bottom: 0px;


	}

	.cuerpo{
		font-size: 10px;
		font-weight: bold;

	}

	.fila1{
		background-color: #004AC1;
		height: 40px;
		color: white;
	}

	.fila2{
		background-color: #0081D5;
		height: 40px;
	}

	.fila3{
		background-color: #56ABE3;
		height: 40px;
	}

	.fila4{
		background-color: #004AC1;
		height: 40px;
	}

	.contenido_btn_ordenes{
		color: white;
		height: 20px;
		width: 20px;
		font-size: 12px;

	}
	.select2-selection__choice{
		background-color: red !important;
		border-color: red !important;
	}

	.btn-block{
      background-color: #004AC1;
    }
    .boton_burbuja{
    	color: white;
    	border-radius: 15px;
    	padding: 5px;
    	margin: 2px;
    	-moz-animation: 2s bote 1;
    	animation: 2s bote 1;
    	-webkit-transform: 2s bote 1;
    }
    .boton_burbuja span{
    	color: white;
    	margin: 20px;
    }
    @keyframes bote {
	  20%, 50%, 80% {
	  	transform: translateY(0);
	    -moz-transform: translateY(0);
	    -webkit-transform: translateY(0);
	  }

	  40% {
	  	transform: translateY(-30px);
	    -moz-transform: translateY(-30px);
	    -webkit-transform: translateY(-30px);
	  }

	  65% {
	  	transform: translateY(-15px);
	    -moz-transform: translateY(-15px);
	    -webkit-transform:  translateY(-15px);
	  }
	}

	.hc4-patient-detail-page {
		padding: 12px 18px 28px !important;
		background: linear-gradient(180deg,#f5f3fa 0%,#ece9f4 100%);
	}
	.hc4-patient-detail-page > .container-fluid { padding-right:0; padding-left:0; }
	.hc4-patient-detail-page > .container-fluid > .row { margin-right:0; margin-left:0; }
	.hc4-patient-detail-title {
		position:relative;
		overflow:hidden;
		margin:0 0 14px !important;
		padding:17px 22px !important;
		border:1px solid rgba(255,255,255,.22);
		border-radius:15px !important;
		background:linear-gradient(118deg,#403183,#624dbc 60%,#8b60ca 100%) !important;
		box-shadow:0 10px 27px rgba(63,46,130,.18);
	}
	.hc4-patient-detail-title h1 { display:flex; align-items:center; gap:10px; min-height:38px; font-size:17px !important; }
	.hc4-patient-detail-title h1 img { width:40px !important; }
	.hc4-patient-detail-grid {
		display:grid;
		grid-template-columns:minmax(0,7fr) minmax(0,5fr);
		align-items:start;
		gap:12px;
		margin-right:0;
		margin-left:0;
	}
	.hc4-patient-detail-grid > [class*="col-md-"] {
		width:100%;
		min-width:0;
		max-width:100%;
		padding-right:0 !important;
		padding-left:0 !important;
	}
	.hc4-patient-detail-grid > .area_1_dato {
		min-width:0;
		max-width:100%;
		overflow-x:hidden;
	}
	.hc4-patient-primary {
		overflow:hidden;
		margin:0 !important;
		border:1px solid #cfc5e5 !important;
		border-radius:14px !important;
		background:linear-gradient(135deg,#fff 0%,#f5f1fb 100%);
		box-shadow:0 8px 23px rgba(63,46,130,.1);
	}
	.hc4-patient-primary > .col-md-12 { padding:15px 17px !important; }
	.hc4-patient-primary > .col-md-12 > .row { display:flex; align-items:flex-start; margin-right:-7px; margin-left:-7px; }
	.hc4-patient-primary > .col-md-12 > .row > [class*="col-"] { padding-right:7px !important; padding-left:7px !important; }
	.hc4-patient-primary .detalle_tabla { width:calc(100% - 40px); margin:8px 20px 12px !important; }
	.hc4-patient-primary input,
	.hc4-patient-primary textarea,
	.hc4-patient-primary select { min-height:36px; padding:7px 10px; border:1px solid #d4cce6; border-radius:8px; background:#fcfbfe; }
	.hc4-patient-primary textarea { display:block; width:100%; min-height:62px; resize:vertical; }
	.hc4-patient-primary form .row { margin-right:-6px; margin-left:-6px; }
	.hc4-patient-primary form .row > [class*="col-"] { padding-right:6px !important; padding-left:6px !important; }
	.hc4-patient-primary label { color:#4b416d !important; font-weight:700; }
	.hc4-patient-primary .detalle_tabla tr:first-child td {
		padding:4px 10px 5px 0;
		color:#786f91;
		font-size:10px !important;
		font-weight:700;
		letter-spacing:.25px;
		text-transform:uppercase;
	}
	.hc4-patient-primary .detalle_tabla tr:nth-child(2) td {
		padding:5px 10px 10px 0;
		color:#312a49;
		font-size:12px !important;
		font-weight:600;
	}
	.hc4-patient-primary .detalle_tabla tr:nth-child(2) td:first-child { color:#ff0014 !important; font-size:17px !important; }
	.hc4-patient-primary .btn_ordenes {
		display:flex;
		align-items:center;
		justify-content:flex-start;
		gap:10px;
		min-height:44px;
		padding:8px 12px !important;
		border:1px solid #d5caed !important;
		border-radius:10px !important;
		color:#4e3aa1 !important;
		background:linear-gradient(135deg,#fff,#f0ebfb) !important;
		box-shadow:0 5px 13px rgba(70,49,145,.11);
		font-size:11px !important;
		font-weight:700;
		transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease;
	}
	.hc4-patient-primary .btn_ordenes:hover {
		border-color:#9b86d2 !important;
		box-shadow:0 8px 18px rgba(70,49,145,.2);
		transform:translateY(-2px);
		text-decoration:none;
	}
	.hc4-patient-primary .btn_ordenes > div { width:100%; padding:0 !important; }
	.hc4-patient-primary .btn_ordenes .row { display:flex; align-items:center; width:100%; margin:0 !important; padding:0 !important; }
	.hc4-patient-primary .btn_ordenes .row > div:first-child {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		flex:0 0 30px;
		width:30px;
		height:30px;
		padding:0 !important;
		border-radius:8px;
		border:1px solid rgba(255,255,255,.32);
		background:linear-gradient(135deg,#493591,#765bc7);
		box-shadow:0 4px 10px rgba(73,53,145,.24);
	}
	.hc4-patient-primary .btn_ordenes .row > div:first-child img {
		display:block;
		width:17px !important;
		height:17px !important;
		object-fit:contain;
		opacity:1 !important;
		filter:brightness(0) invert(1) drop-shadow(0 1px 1px rgba(0,0,0,.18));
	}
	.hc4-patient-primary .btn_ordenes .row > div:first-child i {
		color:#fff !important;
		font-size:15px;
		text-shadow:0 1px 2px rgba(0,0,0,.2);
	}
	.hc4-patient-primary .btn_ordenes .row > div:nth-child(2) { flex:1 1 auto; width:auto; padding:0 8px !important; text-align:left; }
	.hc4-patient-primary .btn_ordenes label { margin:0; color:#4e3aa1 !important; font-size:10px !important; cursor:pointer; }
	.hc4-patient-modules > .row { margin-right:0 !important; margin-left:0 !important; }
	.hc4-patient-modules > .row > .col-md-12 > .row { margin-right:-5px !important; margin-left:-5px !important; }
	.hc4-patient-modules > .row > .col-md-12 > .row > [class*="col-"] { padding:5px !important; }
	.hc4-patient-modules .recuadro {
		overflow:hidden;
		height:210px;
		margin:0;
		border:1px solid #d8dfea;
		border-radius:12px;
		background:#fff;
		box-shadow:0 6px 17px rgba(40,53,94,.09);
	}
	.hc4-patient-modules .fila1,
	.hc4-patient-modules .fila2,
	.hc4-patient-modules .fila3,
	.hc4-patient-modules .fila4 { height:42px; background:linear-gradient(100deg,#493591,#6752bb) !important; }
	.hc4-patient-modules .fila2 { background:linear-gradient(100deg,#5b46a7,#8065ca) !important; }
	.hc4-patient-modules .fila3 { background:linear-gradient(100deg,#6c56b7,#9a7cd5) !important; }
	.hc4-patient-modules .box-header { display:flex; align-items:center; justify-content:space-between; min-height:42px; padding:7px 10px; }
	.hc4-patient-modules .box-header > .row {
		display:flex;
		align-items:center;
		flex:1 1 auto;
		min-width:0;
		margin:0 !important;
	}
	.hc4-patient-modules .box-header > .row > div { display:flex; align-items:center; min-width:0; margin-right:7px !important; }
	.hc4-patient-modules .box-header > .row > div:first-child {
		justify-content:center;
		flex:0 0 27px;
		width:27px;
		height:27px;
		margin-right:8px !important;
		border:1px solid rgba(255,255,255,.28);
		border-radius:7px;
		background:rgba(255,255,255,.14);
	}
	.hc4-patient-modules .box-header > .row > div:first-child img {
		display:none !important;
	}
	.hc4-patient-modules .box-header > .row > div:first-child::before {
		content:'\f0f6';
		display:block;
		color:#fff;
		font-family:'Font Awesome 5 Free','FontAwesome';
		font-size:15px;
		font-style:normal;
		font-weight:900;
		line-height:1;
		text-shadow:0 1px 2px rgba(0,0,0,.22);
	}
	.hc4-patient-modules .box-header .box-title {
		margin:0;
		overflow:hidden;
		color:#fff !important;
		font-size:10px !important;
		font-weight:700;
		line-height:1.15;
		text-transform:uppercase;
		text-overflow:ellipsis;
	}
	.hc4-patient-modules .box-body {
		display:block;
		height:calc(100% - 42px);
		margin:0 !important;
		padding:8px 10px 0 !important;
		color:#3c3554;
		background:#fff;
		line-height:1.15;
	}
	.hc4-patient-modules .box-body > .row {
		margin:0 !important;
	}
	.hc4-patient-modules .box-body > .row > [class*="col-"] {
		padding:0 !important;
	}
	.hc4-patient-modules .box-body p,
	.hc4-patient-modules .box-body label {
		margin-top:0 !important;
		margin-bottom:0 !important;
	}
	.hc4-patient-modules .box-tools { margin-left:auto; }
	.hc4-patient-modules .btn-box-tool {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		width:28px;
		height:28px;
		padding:0 !important;
		border:1px solid rgba(255,255,255,.28);
		border-radius:8px;
		color:#fff !important;
		background:rgba(255,255,255,.1);
	}
	.hc4-patient-modules .btn_ordenes {
		display:flex;
		align-items:center;
		justify-content:center;
		min-height:26px;
		margin-bottom:6px;
		border:1px solid #6953bd !important;
		border-radius:7px;
		color:#fff !important;
		background:linear-gradient(100deg,#493591,#6c54bd) !important;
		box-shadow:0 3px 8px rgba(73,53,145,.15);
		font-size:9px !important;
		font-weight:700;
	}
	.hc4-patient-modules .btn_ordenes > div {
		display:flex;
		align-items:center;
		justify-content:center;
		flex:1 1 auto;
		gap:7px;
		width:auto !important;
		min-width:0;
		margin:0 !important;
	}
	.hc4-patient-modules .btn_ordenes img {
		display:none !important;
	}
	.hc4-patient-modules .btn_ordenes::before {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		flex:0 0 22px;
		width:22px;
		height:22px;
		margin-right:7px;
		border:1px solid rgba(255,255,255,.28);
		border-radius:6px;
		color:#fff;
		background:rgba(255,255,255,.13);
		font-family:'Font Awesome 5 Free','FontAwesome';
		font-size:13px;
		font-weight:900;
		line-height:1;
		text-shadow:0 1px 2px rgba(0,0,0,.22);
	}
	.hc4-patient-modules .btn_ordenes[onclick*="laboratorio"]::before { content:'\f0c3'; }
	.hc4-patient-modules .btn_ordenes[onclick*="imagenes"]::before { content:'\f03e'; }
	.hc4-patient-modules .btn_ordenes[onclick*="proendospico"]::before { content:'\f0f1'; }
	.hc4-patient-modules .btn_ordenes[onclick*="profuncional"]::before,
	.hc4-patient-modules .btn_ordenes[onclick*="funcionales"]::before { content:'\f022'; }
	.hc4-patient-modules .btn_ordenes label { margin:0; color:#fff !important; cursor:pointer; }

	#area_trabajo { width:100%; padding:12px !important; }
	#area_trabajo > *, #area_trabajo .container, #area_trabajo .container-fluid,
	#area_trabajo .box, #area_trabajo .panel, #area_trabajo .card { width:100%; max-width:none; margin-right:0; margin-left:0; }
	#area_trabajo .box, #area_trabajo .panel, #area_trabajo .card {
		overflow:hidden; border:1px solid #d5cdea !important; border-radius:13px !important; background:#fff; box-shadow:0 7px 20px rgba(54,39,110,.1);
	}
	#area_trabajo .box-header, #area_trabajo .panel-heading, #area_trabajo .card-header,
	.modal-content .modal-header {
		display:flex; align-items:center; justify-content:space-between; min-height:48px; gap:10px; padding:11px 15px !important;
		border-bottom:0 !important; color:#fff !important; background:linear-gradient(105deg,#493591,#6953bd 68%,#8c69ce) !important;
	}
	#area_trabajo .box-title, #area_trabajo .panel-title, #area_trabajo .card-title,
	.modal-content .modal-title { margin:0 !important; color:#fff !important; font-size:14px !important; font-weight:700; }
	#area_trabajo .box-body, #area_trabajo .panel-body, #area_trabajo .card-body,
	.modal-content .modal-body { padding:16px !important; }
	#area_trabajo .row, .modal-content .modal-body > .row { margin-right:-7px; margin-left:-7px; }
	#area_trabajo .row > [class*="col-"], .modal-content .modal-body > .row > [class*="col-"] { padding-right:7px; padding-left:7px; }
	#area_trabajo .form-group, .modal-content .form-group { margin-bottom:13px; }
	#area_trabajo input, #area_trabajo select, #area_trabajo textarea, #area_trabajo .form-control,
	.modal-content .form-control { width:100%; min-height:36px; border:1px solid #d7d0e7; border-radius:8px; }
	#area_trabajo .table-responsive { width:100%; margin:0; overflow-x:auto; }
	#area_trabajo table { width:100% !important; margin-bottom:0; }
	#area_trabajo table th, #area_trabajo table td { padding:9px 10px !important; vertical-align:middle !important; }

	/* Vistas clínicas cargadas por AJAX desde los botones + */
	#area_trabajo {
		position:relative;
		margin-top:12px;
		border:1px solid #d7cfea;
		border-radius:15px;
		background:linear-gradient(145deg,#fbfaff,#f2eef9);
		box-shadow:0 8px 24px rgba(62,44,126,.09);
	}
	#area_trabajo:empty { display:none; }
	.hc4-clinical-workspace-toolbar {
		position:absolute;
		top:13px;
		right:14px;
		z-index:25;
		display:flex;
		align-items:center;
		justify-content:flex-end;
		margin:0;
		padding:0;
		color:#fff;
		background:transparent;
		box-shadow:none;
	}
	.hc4-clinical-workspace-back {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		flex:0 0 auto;
		min-height:36px;
		padding:8px 13px;
		border:1px solid rgba(255,255,255,.42);
		border-radius:9px;
		color:#fff !important;
		background:rgba(255,255,255,.12);
		font-size:11px;
		font-weight:700;
		cursor:pointer;
	}
	.hc4-clinical-workspace-back:hover { background:rgba(255,255,255,.22); }
	#area_trabajo.hc4-hide-workspace-back > .hc4-clinical-workspace-toolbar { display:none; }
	#area_trabajo .hc4-consultations-view > .box-header {
		display:block !important;
		position:relative;
		min-height:62px;
		padding:10px 16px !important;
	}
	#area_trabajo .hc4-consultations-header-row {
		display:flex !important;
		align-items:center;
		justify-content:space-between;
		width:100%;
		margin:0 !important;
	}
	#area_trabajo .hc4-consultations-title {
		flex:1 1 auto;
		width:auto;
		max-width:none;
		padding:0 !important;
	}
	#area_trabajo .hc4-consultations-add-wrap {
		flex:0 0 auto;
		width:auto;
		max-width:none;
		padding:0 !important;
		margin-left:18px;
	}
	#area_trabajo .hc4-consultations-view > .box-header .hc4-add-consultation-button {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		gap:8px;
		width:auto !important;
		height:auto !important;
		min-height:38px;
		padding:8px 14px !important;
		border:1px solid rgba(255,255,255,.55) !important;
		border-radius:9px !important;
		color:#fff !important;
		background:rgba(255,255,255,.14) !important;
		box-shadow:0 4px 12px rgba(41,27,104,.16);
		font-size:11px;
		font-weight:700;
		line-height:1;
		white-space:nowrap;
		cursor:pointer;
	}
	#area_trabajo .hc4-consultations-view > .box-header .hc4-add-consultation-button:hover { background:rgba(255,255,255,.24) !important; }
	#area_trabajo .hc4-consultations-view > .box-header .hc4-add-consultation-button img { width:18px !important; height:18px; object-fit:contain; }
	#area_trabajo .hc4-consultations-view > .box-header > center {
		position:absolute;
		top:50%;
		left:50%;
		z-index:1;
		display:block;
		width:auto;
		max-width:48%;
		margin:0;
		transform:translate(-50%,-50%);
		white-space:nowrap;
	}
	#area_trabajo .hc4-consultations-view > .box-header > center > div {
		width:auto;
		padding:0 !important;
	}
	#area_trabajo .hc4-consultation-form {
		width:100%;
		padding:2px 4px 10px;
	}
	#area_trabajo .hc4-consultation-form > .col-12,
	#area_trabajo .hc4-consultation-form > [class*="col-"] {
		width:100%;
		padding:0 !important;
	}
	#area_trabajo .hc4-consultation-form .row {
		align-items:flex-end;
		margin-right:-6px !important;
		margin-left:-6px !important;
	}
	#area_trabajo .hc4-consultation-form .row > [class*="col-"] {
		padding:5px 6px !important;
	}
	#area_trabajo .hc4-consultation-form label.control-label {
		display:block;
		min-height:17px;
		margin:0 0 5px !important;
		color:#124574 !important;
		font-size:11px !important;
		font-weight:700;
		line-height:1.25;
	}
	#area_trabajo .hc4-consultation-form h6 {
		margin:12px 0 3px;
		color:#124574;
		font-size:15px;
	}
	#area_trabajo .hc4-consultation-form .input-group {
		display:flex;
		align-items:stretch;
		flex-wrap:nowrap;
		width:100%;
		padding:0 !important;
		border:0 !important;
	}
	#area_trabajo .hc4-consultation-form .input-group > .form-control {
		flex:1 1 auto;
		width:1% !important;
		min-width:0;
		border-radius:8px 0 0 8px !important;
	}
	#area_trabajo .hc4-consultation-form .input-group-append {
		display:flex;
		flex:0 0 40px;
	}
	#area_trabajo .hc4-consultation-form .input-group-text {
		display:flex;
		align-items:center;
		justify-content:center;
		width:40px;
		border:1px solid #c9dee8;
		border-left:0;
		border-radius:0 8px 8px 0;
		background:#edf7fa;
	}
	#area_trabajo .hc4-consultation-form textarea.form-control {
		min-height:38px;
		resize:vertical;
	}
	#area_trabajo .hc4-consultation-form .select2-container {
		display:block;
		width:100% !important;
	}
	#area_trabajo .hc4-consultation-form .select2-container .select2-selection--single {
		height:38px !important;
		border:1px solid #c9dee8 !important;
		border-radius:8px !important;
		background:#fbfeff !important;
		box-shadow:inset 0 1px 2px rgba(0,74,117,.03);
	}
	#area_trabajo .hc4-consultation-form .select2-container .select2-selection--single .select2-selection__rendered {
		padding-right:32px;
		padding-left:12px;
		color:#213f51;
		font-size:12px;
		line-height:36px !important;
	}
	#area_trabajo .hc4-consultation-form .select2-container .select2-selection--single .select2-selection__arrow {
		top:1px;
		right:7px;
		height:36px;
	}
	#area_trabajo .hc4-consultation-form .hc4-clinical-text-section {
		width:100%;
		margin-top:10px;
		padding:12px !important;
		border:1px solid #c9dee8;
		border-radius:11px;
		background:#f8fcfd;
		box-shadow:0 3px 10px rgba(0,74,117,.05);
	}
	#area_trabajo .hc4-consultation-form .hc4-clinical-text-section > label {
		display:block;
		margin:0 0 7px !important;
		color:#124574 !important;
		font-size:12px !important;
		font-weight:700;
	}
	#area_trabajo .hc4-consultation-form .hc4-rich-editor,
	#area_trabajo .hc4-consultation-form .hc4-clinical-textarea {
		display:block;
		width:100%;
		min-height:76px;
		padding:11px 13px !important;
		border:1px solid #b9dbe5 !important;
		border-radius:8px !important;
		color:#213f51;
		background:#fff !important;
		box-shadow:inset 0 1px 3px rgba(0,74,117,.04);
		font-size:13px;
		line-height:1.55;
		outline:none;
	}
	#area_trabajo .hc4-consultation-form .hc4-rich-editor:focus,
	#area_trabajo .hc4-consultation-form .hc4-clinical-textarea:focus {
		border-color:#159eaa !important;
		box-shadow:0 0 0 3px rgba(21,158,170,.14);
	}
	#area_trabajo .hc4-consultation-form .hc4-rich-editor--evolution { min-height:88px; }
	#area_trabajo .hc4-consultation-form .hc4-rich-editor--results { min-height:70px; }
	#area_trabajo .hc4-consultation-form .hc4-clinical-textarea--physical { min-height:150px; }
	#area_trabajo .hc4-consultation-form .mce-tinymce-inline,
	#area_trabajo .hc4-consultation-form .tox-tinymce-inline {
		overflow:hidden;
		margin-bottom:5px !important;
		border:1px solid #b9dbe5 !important;
		border-radius:8px !important;
		box-shadow:0 7px 18px rgba(0,74,117,.10) !important;
	}
	#area_trabajo .hc4-consultation-form .mce-toolbar-grp,
	#area_trabajo .hc4-consultation-form .tox-editor-header,
	#area_trabajo .hc4-consultation-form .tox-toolbar,
	#area_trabajo .hc4-consultation-form .tox-toolbar__primary {
		border:0 !important;
		background:#edf7fa !important;
		box-shadow:none !important;
	}
	/* La filiación del paciente debe permanecer visible también cuando se
	   consulta un módulo clínico o se abre el detalle de un registro. */
	.hc4-clinical-list .hc4-patient-primary,
	.hc4-clinical-detail .hc4-patient-primary { display:block !important; }
	.hc4-clinical-list #area_trabajo {
		box-sizing:border-box;
		flex:0 0 calc(100% - 24px);
		width:calc(100% - 24px);
		min-width:0;
		max-width:calc(100% - 24px) !important;
		margin-top:14px;
		margin-left:12px;
		padding-right:0 !important;
		padding-left:0 !important;
		overflow-x:hidden;
	}
	.hc4-clinical-list #area_trabajo > .hc4-recipes-view,
	.hc4-clinical-list #area_trabajo > .hc4-recipes-view > .row,
	.hc4-clinical-list #area_trabajo .hc4-recipes-view .hc4-recipes-list-shell,
	.hc4-clinical-list #area_trabajo .hc4-recipes-view .parent {
		box-sizing:border-box;
		width:100% !important;
		min-width:0 !important;
		max-width:100% !important;
		overflow-x:hidden !important;
	}
	.hc4-clinical-detail #area_trabajo {
		min-height:calc(100vh - 220px);
		margin-top:0;
		padding:12px !important;
	}
	#area_trabajo > .container-fluid,
	#area_trabajo > .container { padding:0 !important; }
	#area_trabajo > .container-fluid > .row,
	#area_trabajo > .container > .row { margin:0 !important; }
	#area_trabajo > .container-fluid > .row > [class*="col-"],
	#area_trabajo > .container > .row > [class*="col-"] {
		width:100%;
		padding:0 !important;
		margin:0 !important;
	}
	#area_trabajo .parent {
		height:auto !important;
		max-height:650px;
		padding:12px !important;
		overflow-y:auto;
		background:transparent !important;
		scrollbar-color:#0b91b8 #dceef3;
		scrollbar-width:thin;
	}
	#area_trabajo .parent::-webkit-scrollbar { width:7px; }
	#area_trabajo .parent::-webkit-scrollbar-track { border-radius:10px; background:#dceef3 !important; box-shadow:none !important; }
	#area_trabajo .parent::-webkit-scrollbar-thumb {
		border-radius:10px;
		background:linear-gradient(#087eae,#16aaa5) !important;
	}
	#area_trabajo #area_index > .row > .col-md-12 > .col-md-12 {
		overflow:hidden;
		border:0 !important;
		border-radius:13px !important;
		background:transparent !important;
	}
	#area_trabajo #area_index .parent > div { margin-right:0 !important; }
	#area_trabajo .hc4-recipes-view .parent {
		padding-right:10px !important;
		padding-left:0 !important;
	}
	#area_trabajo .hc4-recipes-view .hc4-recipes-list-shell {
		padding-right:10px !important;
		padding-left:0 !important;
	}
	#area_trabajo .hc4-recipes-view .parent > div {
		width:100%;
		margin-right:0 !important;
		margin-left:0 !important;
	}
	#area_trabajo .hc4-recipes-view .parent > div > .box {
		width:100%;
		margin-right:0 !important;
		margin-left:0 !important;
	}
	#area_trabajo #area_index [style*="padding-left: 110px"] { padding-right:0 !important; padding-left:0 !important; }
	#area_trabajo #area_index .col-md-11 {
		width:100%;
		margin-right:0 !important;
		margin-left:0 !important;
	}
	#area_trabajo [style*="background-color: #56ABE3"],
	#area_trabajo [style*="background-color:#56ABE3"] { background:transparent !important; }
	#area_trabajo [style*="border: 2px solid #004AC1"],
	#area_trabajo [style*="border:2px solid #004AC1"] { border-color:#d4c9e9 !important; }
	#area_trabajo [style*="background-color: #004AC1"],
	#area_trabajo [style*="background-color:#004AC1"] {
		color:#fff !important;
		background:linear-gradient(105deg,#493591,#6952bd 70%,#8b69ce) !important;
	}
	#area_trabajo h1 {
		padding:12px 16px !important;
		font-size:15px !important;
		line-height:1.35 !important;
	}
	#area_trabajo h1 img { width:28px !important; max-height:28px; margin:0 9px 0 0 !important; object-fit:contain; }
	#area_trabajo h1 center,
	#area_trabajo h1 center > div { display:inline; padding:0 !important; }
	#area_trabajo h1 center h1 { display:inline; padding:0 !important; font-size:12px !important; }
	#area_trabajo .box {
		margin-bottom:12px !important;
		border-color:#d8d0e8 !important;
		background:#fff !important;
	}
	#area_trabajo .box-header {
		border-radius:0 !important;
		box-shadow:none;
	}
	#area_trabajo .box-header .box-tools { display:flex; align-items:center; margin-left:auto; }
	#area_trabajo .box-header .btn,
	#area_trabajo .box-header .btn-box-tool {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		width:30px;
		height:30px;
		padding:0 !important;
		border:1px solid rgba(255,255,255,.3) !important;
		border-radius:8px !important;
		color:#fff !important;
		background:rgba(255,255,255,.12) !important;
	}
	#area_trabajo .box-body { color:#3c3553; background:#fff !important; }
	#area_trabajo .contenedor2 {
		margin:0 !important;
		padding:15px !important;
		border:1px solid #e0d9ed;
		border-radius:10px;
		background:#faf9fd !important;
	}
	#area_trabajo .btn-info,
	#area_trabajo .btn-primary,
	#area_trabajo .btn-block,
	#area_trabajo .btn_accion {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		gap:7px;
		min-height:36px;
		padding:8px 14px !important;
		border:1px solid #5b46aa !important;
		border-radius:9px !important;
		color:#fff !important;
		background:linear-gradient(105deg,#493591,#6d55be) !important;
		box-shadow:0 4px 10px rgba(73,53,145,.16);
		font-size:11px !important;
		font-weight:700;
	}
	#area_trabajo .btn-info:hover,
	#area_trabajo .btn-primary:hover,
	#area_trabajo .btn_accion:hover { background:linear-gradient(105deg,#3e2c82,#5d46aa) !important; transform:translateY(-1px); }
	#area_trabajo .btn img,
	#area_trabajo .btn_accion img { width:16px !important; height:16px !important; object-fit:contain; filter:brightness(0) invert(1); }
	#area_trabajo .btn label,
	#area_trabajo .btn_accion label { margin:0 !important; color:#fff !important; cursor:pointer; }
	#area_trabajo input,
	#area_trabajo select,
	#area_trabajo textarea,
	#area_trabajo .select2-selection {
		border-color:#d7cfe7 !important;
		background:#fdfcff !important;
		box-shadow:inset 0 1px 2px rgba(47,34,88,.03);
	}
	#area_trabajo textarea,
	#area_trabajo [id="trp"],
	#area_trabajo [id="tprescripcion"] {
		padding:10px !important;
		border:1px solid #d8d0e8 !important;
		border-radius:9px !important;
		background:#fff !important;
	}
	#area_trabajo .table-responsive { border:1px solid #ddd6ea; border-radius:10px; }
	#area_trabajo table thead th { color:#fff !important; background:#5843a2 !important; }
	#area_trabajo .panel,
	#area_trabajo .well,
	#area_trabajo .card,
	#area_trabajo fieldset {
		margin-bottom:14px !important;
		border:1px solid #dcd4e9 !important;
		border-radius:12px !important;
		background:#fff !important;
		box-shadow:0 5px 14px rgba(61,44,119,.06);
	}
	#area_trabajo legend {
		width:auto;
		margin:0 12px;
		padding:0 8px;
		border:0;
		color:#513d9d;
		font-size:12px;
		font-weight:700;
	}
	#area_trabajo .panel-heading,
	#area_trabajo .card-header {
		border-radius:11px 11px 0 0 !important;
	}
	#area_trabajo .nav-tabs {
		display:flex;
		flex-wrap:wrap;
		gap:6px;
		padding:7px;
		border:1px solid #ddd5eb;
		border-radius:10px;
		background:#f3eff9;
	}
	#area_trabajo .nav-tabs > li { float:none; margin:0; }
	#area_trabajo .nav-tabs > li > a {
		margin:0;
		padding:8px 12px;
		border:0 !important;
		border-radius:7px;
		color:#5a4897;
		font-size:11px;
		font-weight:700;
	}
	#area_trabajo .nav-tabs > li.active > a,
	#area_trabajo .nav-tabs > li > a:hover { color:#fff !important; background:#6550b5 !important; }
	#area_trabajo .tab-content { padding:14px 2px; }
	#area_trabajo .alert {
		padding:11px 14px;
		border-radius:9px;
		font-size:11px;
	}
	#area_trabajo .form-group > label,
	#area_trabajo .control-label {
		display:block;
		margin-bottom:5px;
		color:#4b4168 !important;
		font-size:11px !important;
		font-weight:700;
	}
	#area_trabajo .input-group { display:flex; width:100%; }
	#area_trabajo .input-group .form-control { flex:1 1 auto; min-width:0; }
	#area_trabajo .input-group-addon,
	#area_trabajo .input-group-btn > .btn {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		min-width:38px;
		border-color:#d5cce7 !important;
		color:#5943a4;
		background:#eee8f8 !important;
	}
	#area_trabajo .select2-container { width:100% !important; }
	#area_trabajo .select2-selection { min-height:38px !important; border-radius:8px !important; }
	#area_trabajo .select2-selection__rendered { padding-top:4px; line-height:28px !important; }
	#area_trabajo .dataTables_wrapper .dt-buttons,
	#area_trabajo .dataTables_wrapper .dataTables_filter,
	#area_trabajo .dataTables_wrapper .dataTables_length { margin:0 0 10px; }
	#area_trabajo .dataTables_wrapper .dt-button {
		padding:6px 10px !important;
		border:1px solid #d3c8e8 !important;
		border-radius:7px !important;
		color:#563fa4 !important;
		background:#f3effa !important;
	}
	#area_trabajo .pagination { display:flex; gap:4px; margin:12px 0 0; }
	#area_trabajo .pagination > li > a,
	#area_trabajo .pagination > li > span { border-radius:7px !important; color:#5943a5; }
	#area_trabajo .pagination > .active > a { border-color:#6049ad; color:#fff; background:#6049ad; }
	#area_trabajo .box-footer,
	#area_trabajo .panel-footer,
	#area_trabajo .card-footer {
		display:flex;
		align-items:center;
		justify-content:flex-end;
		flex-wrap:wrap;
		gap:8px;
		padding:12px 15px !important;
		border-top:1px solid #e2dced !important;
		background:#faf8fd !important;
	}
		#area_trabajo .hc4-endoscopy-view > .box-header {
			display:block !important;
			padding:14px 16px !important;
			background:linear-gradient(105deg,#07547e 0%,#087eae 68%,#16aaa5 100%) !important;
		}
	#area_trabajo .hc4-endoscopy-view > .box-header > .row {
		display:flex;
		align-items:center;
		margin:0 !important;
	}
	#area_trabajo .hc4-endoscopy-view > .box-header > .row > [class*="col-"] { padding:0 6px !important; }
		#area_trabajo .hc4-endoscopy-view > .box-header > center { display:block; width:100%; margin:8px 0 0; }
		#area_trabajo .hc4-endoscopy-view > .box-header > center > .col-12 { width:100%; padding:0 !important; }
	#area_trabajo .hc4-endoscopy-view .hc4-endoscopy-title {
		display:flex;
		align-items:center;
		gap:10px;
		padding:0 !important;
		background:transparent !important;
	}
	#area_trabajo .hc4-endoscopy-view .hc4-endoscopy-title img {
		width:34px !important;
		height:34px !important;
		margin:0 !important;
		padding:7px;
		border:1px solid rgba(255,255,255,.28);
		border-radius:9px;
		background:rgba(255,255,255,.13);
		filter:brightness(0) invert(1);
	}
		#area_trabajo .hc4-endoscopy-view .hc4-endoscopy-patient {
		display:flex;
		align-items:center;
		justify-content:center;
		margin:0;
		padding:9px 13px !important;
		border:1px solid rgba(255,255,255,.24);
		border-radius:9px;
			background:rgba(0,65,101,.2) !important;
			font-size:12px !important;
		}
	#area_trabajo .hc4-endoscopy-view > .box-body { padding:14px !important; background:#faf9fd !important; }
	#area_trabajo .hc4-endoscopy-add {
		display:inline-flex !important;
		width:auto !important;
		min-width:168px;
		margin:0 0 0 auto !important;
		padding:9px 14px !important;
		border:1px solid rgba(255,255,255,.42) !important;
		border-radius:9px !important;
		background:rgba(255,255,255,.12) !important;
		box-shadow:none;
	}
	#area_trabajo .hc4-endoscopy-add > .row,
	#area_trabajo .hc4-endoscopy-add > .row > div { width:auto; margin:0 !important; padding:0 !important; }
	#area_trabajo .hc4-endoscopy-add img { margin-right:7px; }
	#area_trabajo .hc4-procedure-summary {
		display:grid !important;
		grid-template-columns:minmax(190px,1.15fr) minmax(220px,1.35fr) minmax(180px,1.2fr) 190px;
		align-items:center;
		gap:14px;
		width:100%;
		margin:0 !important;
	}
	#area_trabajo .hc4-procedure-summary > [class*="col-"] {
		width:auto;
		max-width:none;
		min-width:0;
		margin:0 !important;
		padding:0 !important;
	}
	#area_trabajo .hc4-procedure-summary > div:not(.hc4-procedure-delete-wrap) {
		padding:4px 9px !important;
		border-left:2px solid rgba(255,255,255,.2);
		line-height:1.35;
	}
	#area_trabajo .hc4-procedure-summary .titulo_pro { display:block; margin-top:3px; color:#fff; font-size:11px; }
		#area_trabajo .hc4-procedure-actions .hc4-procedure-delete {
			display:inline-flex;
			align-items:center;
			justify-content:center;
			width:100% !important;
			min-width:92px;
			height:34px !important;
			padding:8px 12px !important;
			border:1px solid rgba(255,255,255,.28);
			border-radius:8px;
			background:#c74655 !important;
			box-shadow:0 4px 10px rgba(105,23,35,.2);
			white-space:nowrap;
	}
	#area_trabajo .hc4-procedure-actions {
		display:grid;
		grid-template-columns:minmax(92px,1fr) 36px;
		align-items:center;
		gap:7px;
		justify-self:stretch;
	}
		#area_trabajo .hc4-procedure-actions .hc4-procedure-delete span { margin-left:6px; }
		#area_trabajo .hc4-procedure-actions .hc4-procedure-toggle {
			display:inline-flex;
		align-items:center;
		justify-content:center;
			width:36px !important;
			height:34px !important;
		padding:0 !important;
		border:1px solid rgba(255,255,255,.35) !important;
		border-radius:8px !important;
		color:#fff !important;
		background:rgba(255,255,255,.13) !important;
	}
		#area_trabajo .hc4-procedure-id {
			display:none !important;
		}
		#area_trabajo .hc4-endoscopy-action-wrap {
			display:flex;
			align-items:center;
		}
		#area_trabajo .box-header .hc4-endoscopy-inline-action {
			display:inline-flex;
			align-items:center;
			justify-content:center;
			gap:6px;
			width:auto !important;
			min-width:78px;
			height:34px !important;
			padding:7px 11px !important;
			border:1px solid rgba(255,255,255,.35) !important;
			border-radius:8px !important;
			color:#fff !important;
			background:rgba(255,255,255,.14) !important;
			white-space:nowrap;
		}
		#area_trabajo .box-header .hc4-endoscopy-inline-action span {
			font-family:'Helvetica general3';
			font-size:11px;
		}
		#area_trabajo .box-header .hc4-endoscopy-inline-action--danger {
			background:#c74655 !important;
			box-shadow:0 4px 10px rgba(105,23,35,.2);
		}
	#area_trabajo .hc4-endoscopy-view > .box-body .box > .box-header { position:relative; }
	#area_trabajo .hc4-endoscopy-view > .box-body > .col-12 { padding:0 !important; }
	#area_trabajo .hc4-endoscopy-view > .box-body > .col-12 > .parent { margin:0 !important; }
	#area_trabajo .hc4-endoscopy-view .hc4-endoscopy-header-row > .hc4-clinical-workspace-toolbar {
		position:static;
		flex:0 0 auto;
		order:2;
	}
	#area_trabajo .hc4-endoscopy-view .hc4-endoscopy-title-wrap {
		flex:1 1 auto;
		order:1;
		width:auto;
		max-width:none;
	}
	#area_trabajo .hc4-endoscopy-view .hc4-endoscopy-add-wrap {
		flex:0 0 auto;
		order:3;
		width:auto;
		max-width:none;
		margin-left:8px;
	}
		@media(max-width:900px){
			#area_trabajo .hc4-procedure-summary{grid-template-columns:repeat(2,minmax(0,1fr))}
		#area_trabajo .hc4-procedure-actions{justify-self:start;width:190px}
	}
	@media(max-width:560px){
		#area_trabajo .hc4-endoscopy-view > .box-header > .row{display:block}
		#area_trabajo .hc4-endoscopy-add{margin-top:10px!important;margin-left:0!important}
		#area_trabajo .hc4-procedure-summary{grid-template-columns:1fr}
	}
	.modal-dialog { display:flex; align-items:center; min-height:calc(100vh - 30px); margin:15px auto !important; }
	.modal-content { width:100%; overflow:hidden; border:0; border-radius:15px; box-shadow:0 20px 55px rgba(20,12,55,.3); }
	.modal-content .modal-footer { display:flex; align-items:center; justify-content:flex-end; gap:8px; padding:12px 16px; }

	/* Paleta clínica ambulatoria: misma identidad azul del panel de inicio. */
	.hc4-patient-detail-page {
		background:linear-gradient(180deg,#f5faff 0%,#edf5f9 100%) !important;
	}
	.hc4-patient-detail-title {
		border-color:rgba(255,255,255,.24) !important;
		background:linear-gradient(118deg,#053d68 0%,#006fb9 55%,#12a4b4 100%) !important;
		box-shadow:0 10px 27px rgba(0,74,130,.18) !important;
	}
	.hc4-patient-primary {
		border-color:#c9dee8 !important;
		background:linear-gradient(135deg,#fff 0%,#f4fafc 100%) !important;
		box-shadow:0 8px 23px rgba(0,74,117,.09) !important;
	}
	.hc4-patient-primary input,
	.hc4-patient-primary textarea,
	.hc4-patient-primary select { border-color:#cbdde6; background:#fbfdfe; }
	.hc4-patient-primary label { color:#124574 !important; }
	.hc4-patient-primary .detalle_tabla tr:first-child td { color:#000000; }
	.hc4-patient-primary .detalle_tabla tr:nth-child(2) td { color:#213f51; }
	.hc4-patient-primary .btn_ordenes {
		border-color:#bcd9e5 !important;
		color:#000000 !important;
		background:linear-gradient(135deg,#fff,#eaf7fb) !important;
		box-shadow:0 5px 13px rgba(0,83,126,.11);
	}
	.hc4-patient-primary .btn_ordenes:hover {
		border-color:#58a9c2 !important;
		box-shadow:0 8px 18px rgba(0,83,126,.18);
	}
	.hc4-patient-primary .btn_ordenes .row > div:first-child {
		background:linear-gradient(135deg,#07547e,#0b91b8);
		box-shadow:0 4px 10px rgba(0,83,126,.22);
	}
	.hc4-patient-primary .btn_ordenes label { color:#000000 !important; }
	.hc4-patient-modules .fila1,
	.hc4-patient-modules .fila2,
	.hc4-patient-modules .fila3,
	.hc4-patient-modules .fila4 {
		background:linear-gradient(105deg,#07547e 0%,#087eae 68%,#16aaa5 100%) !important;
	}
	.hc4-patient-modules .recuadro {
		border-color:#c9dee8;
		box-shadow:0 6px 17px rgba(0,74,117,.09);
	}
	.hc4-patient-modules .box-body { color:#000000; }
	.hc4-patient-modules .btn_ordenes {
		border-color:#087eae !important;
		background:linear-gradient(100deg,#07547e,#0b91b8) !important;
		box-shadow:0 3px 8px rgba(0,83,126,.16);
	}
	#area_trabajo {
		border-color:#c9dee8;
		background:linear-gradient(145deg,#fbfeff,#edf7fa);
		box-shadow:0 8px 24px rgba(0,74,117,.08);
	}
	#area_trabajo .box,
	#area_trabajo .panel,
	#area_trabajo .card {
		border-color:#c9dee8 !important;
		box-shadow:0 7px 20px rgba(0,74,117,.09);
	}
	#area_trabajo .box-header,
	#area_trabajo .panel-heading,
	#area_trabajo .card-header,
	.modal-content .modal-header {
		background:linear-gradient(105deg,#07547e 0%,#087eae 68%,#16aaa5 100%) !important;
	}
	/* Las vistas heredadas cargadas por AJAX todavía traen colores inline.
	   Se normalizan aquí para que todas conserven la paleta del expediente. */
	#area_trabajo [style*="background-color: #004AC1"],
	#area_trabajo [style*="background-color:#004AC1"] {
		background:linear-gradient(105deg,#07547e 0%,#087eae 68%,#16aaa5 100%) !important;
	}
	#area_trabajo [style*="border: 2px solid #004AC1"],
	#area_trabajo [style*="border:2px solid #004AC1"] { border-color:#159eaa !important; }
	#area_trabajo .btn-info,
	#area_trabajo .btn-primary,
	#area_trabajo .btn-block,
	#area_trabajo .btn_accion {
		border-color:#087eae !important;
		background:linear-gradient(100deg,#07547e,#0b91b8) !important;
		box-shadow:0 4px 10px rgba(0,83,126,.16);
	}
	#area_trabajo .btn-info:hover,
	#area_trabajo .btn-primary:hover,
	#area_trabajo .btn_accion:hover { background:linear-gradient(100deg,#06486c,#087f9f) !important; }
	#area_trabajo .contenedor2 { border-color:#c9dee8; background:#f8fcfd !important; }
	#area_trabajo .hc4-recipes-heading { position:relative; padding-right:165px !important; }
	#area_trabajo .hc4-recipes-heading > .hc4-clinical-workspace-toolbar {
		top:50%;
		right:14px;
		transform:translateY(-50%);
	}
	/* Consultas usa la misma paleta azul-turquesa de sus tarjetas inferiores. */
	#area_trabajo .hc4-consultations-view {
		border-color:#c9dee8 !important;
		background:#fff !important;
	}
	#area_trabajo .hc4-consultations-view > .box-header {
		background:linear-gradient(105deg,#07547e 0%,#087eae 68%,#16aaa5 100%) !important;
	}
	#area_trabajo .hc4-consultations-view > .box-body {
		padding-right:16px !important;
		padding-left:16px !important;
		background:#fff !important;
	}
	#area_trabajo .hc4-consultations-view > .box-body > .col-12 {
		width:100%;
		max-width:none;
		padding:0 !important;
	}
	#area_trabajo .hc4-consultations-view .parent {
		width:100%;
		margin-right:0 !important;
		margin-left:0 !important;
		padding:12px 8px 12px 0 !important;
	}
	#area_trabajo .hc4-consultations-view .parent > [id^="consulta"] {
		width:100%;
		max-width:none;
		padding:0 !important;
	}
	#area_trabajo .hc4-consultations-view .hc4-consultations-header-row > .hc4-clinical-workspace-toolbar {
		position:static;
		flex:0 0 auto;
		order:2;
	}
	#area_trabajo .hc4-consultations-view .hc4-consultations-add-wrap { order:3; margin-left:8px; }
	#area_trabajo .hc4-consultations-view .hc4-consultations-title { order:1; }
	#area_trabajo .hc4-consultations-view.collapsed-box .hc4-clinical-workspace-toolbar,
	#area_trabajo .hc4-consultations-view.collapsed-box .hc4-consultations-add-wrap,
	#area_trabajo .hc4-consultations-view.hc4-is-collapsed .hc4-clinical-workspace-toolbar,
	#area_trabajo .hc4-consultations-view.hc4-is-collapsed .hc4-consultations-add-wrap {
		display:none !important;
	}

	/* Cabeceras de consultas: datos completos y control en el extremo derecho. */
	#area_trabajo .hc4-consultation-record-header {
		display:block;
		padding:12px !important;
		text-align:left !important;
	}
	#area_trabajo .hc4-consultation-record-header > .row {
		display:flex;
		align-items:center;
		flex-wrap:wrap;
		gap:12px 28px;
		width:100%;
		margin:0 !important;
	}
	#area_trabajo .hc4-consultation-record-header > .row > [class*="col-"] {
		flex:0 1 auto;
		width:auto;
		max-width:100%;
		min-width:0;
		padding:0 !important;
		text-align:left;
		line-height:1.5;
	}
	#area_trabajo .hc4-consultation-record-header > .row > .box-tools {
		position:static;
		flex:0 0 auto;
		float:none;
		margin:0 0 0 auto !important;
		padding:0 !important;
	}
	#area_trabajo [id^="consulta"] { margin-bottom:8px; }
	@media (max-width:767px) {
		#area_trabajo .hc4-consultation-record-header > .row {
			position:relative;
			padding-right:42px;
			gap:8px 20px;
		}
		#area_trabajo .hc4-consultation-record-header > .row > .box-tools {
			position:absolute;
			top:0;
			right:0;
		}
	}
	#area_trabajo .hc4-functional-view .hc4-functional-shell {
		width:100%;
		overflow:hidden;
		border:1px solid #c9dee8 !important;
		border-radius:13px;
		background:#fff !important;
		box-shadow:0 7px 20px rgba(0,74,117,.09);
	}
	#area_trabajo .hc4-functional-shell > .box-header {
		display:block !important;
		padding:14px 16px !important;
		background:linear-gradient(105deg,#07547e 0%,#087eae 68%,#16aaa5 100%) !important;
	}
	#area_trabajo .hc4-functional-header-row {
		display:flex !important;
		align-items:center;
		width:100%;
		margin:0 !important;
	}
	#area_trabajo .hc4-functional-title-wrap {
		flex:1 1 auto;
		order:1;
		width:auto;
		max-width:none;
		padding:0 6px !important;
	}
	#area_trabajo .hc4-functional-title {
		display:flex;
		align-items:center;
		gap:10px;
		padding:0 !important;
		background:transparent !important;
		white-space:nowrap;
	}
	#area_trabajo .hc4-functional-title img {
		width:34px !important;
		height:34px !important;
		margin:0 !important;
		padding:7px;
		border:1px solid rgba(255,255,255,.28);
		border-radius:9px;
		background:rgba(255,255,255,.13);
		filter:brightness(0) invert(1);
	}
	#area_trabajo .hc4-functional-header-row > .hc4-clinical-workspace-toolbar {
		position:static;
		flex:0 0 auto;
		order:2;
	}
	#area_trabajo .hc4-functional-add-wrap {
		flex:0 0 auto;
		order:3;
		width:auto;
		max-width:none;
		margin-left:8px;
		padding:0 !important;
	}
	#area_trabajo .hc4-functional-add-wrap > div { margin:0 !important; }
	#area_trabajo .hc4-functional-add {
		display:inline-flex !important;
		align-items:center;
		width:auto !important;
		min-width:168px;
		min-height:38px;
		margin:0 !important;
		padding:8px 14px !important;
		border:1px solid rgba(255,255,255,.42) !important;
		border-radius:9px !important;
		white-space:nowrap;
	}
	#area_trabajo .hc4-functional-add > .row,
	#area_trabajo .hc4-functional-add > .row > div {
		display:flex;
		align-items:center;
		width:auto;
		margin:0 !important;
		padding:0 !important;
	}
	#area_trabajo .hc4-functional-add img { margin-right:7px; }
	#area_trabajo .hc4-functional-shell > .box-header > center {
		display:block;
		width:100%;
		margin:8px 0 0;
	}
	#area_trabajo .hc4-functional-shell > .box-header > center > .col-12 { width:100%; padding:0 !important; }
	#area_trabajo .hc4-functional-patient {
		display:flex;
		align-items:center;
		justify-content:center;
		margin:0;
		padding:9px 13px !important;
		border:1px solid rgba(255,255,255,.24);
		border-radius:9px;
		background:rgba(0,65,101,.2) !important;
		font-size:12px !important;
	}
	#area_trabajo .hc4-functional-list-shell {
		padding:14px !important;
		background:#fafcfd !important;
	}
	#area_trabajo .hc4-functional-list-shell > .parent {
		margin:0 !important;
		padding:0 !important;
	}
	#area_trabajo .hc4-functional-list-shell > .parent > div { margin:0 !important; }
	#area_trabajo .hc4-functional-record > .box-header { cursor:pointer; }
	#area_trabajo .hc4-functional-summary {
		display:grid !important;
		grid-template-columns:minmax(190px,1.2fr) minmax(220px,1.35fr) minmax(180px,1fr);
		align-items:center;
		gap:14px;
		flex:1 1 auto;
		width:auto;
		margin:0 !important;
	}
	#area_trabajo .hc4-functional-summary > [class*="col-"] {
		width:auto;
		max-width:none;
		min-width:0;
		margin:0 !important;
		padding:4px 9px !important;
		border-left:2px solid rgba(255,255,255,.2);
		line-height:1.35;
	}
	#area_trabajo .hc4-functional-action-wrap { display:flex; align-items:center; }
	#area_trabajo .box-header .hc4-functional-inline-action {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		gap:6px;
		width:auto !important;
		min-width:78px;
		height:34px !important;
		padding:7px 11px !important;
		border:1px solid rgba(255,255,255,.35) !important;
		border-radius:8px !important;
		color:#fff !important;
		background:rgba(255,255,255,.14) !important;
		white-space:nowrap;
	}
	#area_trabajo .box-header .hc4-functional-inline-action span {
		font-family:'Helvetica general3';
		font-size:11px;
	}
	@media(max-width:900px){
		#area_trabajo .hc4-functional-summary{grid-template-columns:repeat(2,minmax(0,1fr))}
	}
	@media(max-width:560px){
		#area_trabajo .hc4-functional-header-row{display:block!important}
		#area_trabajo .hc4-functional-add-wrap{margin-top:10px;margin-left:0}
		#area_trabajo .hc4-functional-title{white-space:normal}
		#area_trabajo .hc4-functional-summary{grid-template-columns:1fr}
	}
	#area_trabajo input,
	#area_trabajo select,
	#area_trabajo textarea,
	#area_trabajo .form-control,
	.modal-content .form-control { border-color:#cbdde6; }
	.modal-content { box-shadow:0 20px 55px rgba(0,52,91,.26); }

	html.dark-layout .hc4-patient-detail-page { color:#dce8ef; background:linear-gradient(180deg,#101e2a,#0f1b26) !important; }
	html.dark-layout .hc4-patient-primary,
	html.dark-layout .hc4-patient-modules .recuadro,
	html.dark-layout .hc4-patient-modules .box-body,
	html.dark-layout #area_trabajo .box,
	html.dark-layout #area_trabajo .panel,
	html.dark-layout #area_trabajo .card,
	html.dark-layout #area_trabajo .box-body,
	html.dark-layout #area_trabajo .panel-body,
	html.dark-layout #area_trabajo .card-body,
	html.dark-layout .modal-content,
	html.dark-layout .modal-content .modal-body,
	html.dark-layout .modal-content .modal-footer { border-color:#3b5363 !important; color:#dce8ef !important; background:#182d3a !important; }
	html.dark-layout .hc4-patient-primary input,
	html.dark-layout .hc4-patient-primary textarea,
	html.dark-layout .hc4-patient-primary select,
	html.dark-layout #area_trabajo input,
	html.dark-layout #area_trabajo select,
	html.dark-layout #area_trabajo textarea,
	html.dark-layout #area_trabajo .form-control,
	html.dark-layout .modal-content .form-control { border-color:#426071 !important; color:#e1edf2 !important; background:#203845 !important; }
	html.dark-layout .hc4-patient-primary label { color:#dce8ef !important; }
	html.dark-layout .hc4-patient-primary .detalle_tabla tr:first-child td { color:#a6bac5 !important; }
	html.dark-layout .hc4-patient-primary .detalle_tabla tr:nth-child(2) td { color:#e2edf2 !important; }
	html.dark-layout .hc4-patient-primary .btn_ordenes {
		border-color:#496273 !important;
		color:#e3dcff !important;
		background:linear-gradient(135deg,#213b48,#273f50) !important;
	}
	html.dark-layout .hc4-patient-primary .btn_ordenes .row > div:first-child { border-color:#8069ca; background:linear-gradient(135deg,#5842a4,#765bc7); }
	html.dark-layout .hc4-patient-primary .btn_ordenes label { color:#e3dcff !important; }
	html.dark-layout .hc4-patient-modules .btn_ordenes { border-color:#8069ca !important; color:#fff !important; background:linear-gradient(100deg,#493591,#6750b7) !important; }
	html.dark-layout #area_trabajo table, html.dark-layout #area_trabajo table td { border-color:#35505f !important; color:#d7e5ed !important; background:#182d3a !important; }
	html.dark-layout #area_trabajo table th { border-color:#405e6d !important; color:#eef7fa !important; background:#243e4b !important; }
	html.dark-layout #area_trabajo { border-color:#3b5363; background:linear-gradient(145deg,#132631,#172b37); }
	html.dark-layout #area_trabajo .contenedor2,
	html.dark-layout #area_trabajo [id="trp"],
	html.dark-layout #area_trabajo [id="tprescripcion"] { border-color:#405b6a !important; color:#dbe8ee !important; background:#1d3441 !important; }
	html.dark-layout #area_trabajo .parent::-webkit-scrollbar-track { background:#233b48 !important; }
	html.dark-layout .hc4-clinical-workspace-toolbar { background:transparent; }
	html.dark-layout #area_trabajo .panel,
	html.dark-layout #area_trabajo .well,
	html.dark-layout #area_trabajo .card,
	html.dark-layout #area_trabajo fieldset,
	html.dark-layout #area_trabajo .box-footer,
	html.dark-layout #area_trabajo .panel-footer,
	html.dark-layout #area_trabajo .card-footer { border-color:#3d5665 !important; color:#dce8ef !important; background:#1a303d !important; }
	html.dark-layout #area_trabajo .nav-tabs { border-color:#3d5766; background:#203744; }
	html.dark-layout #area_trabajo .nav-tabs > li > a,
	html.dark-layout #area_trabajo .form-group > label,
	html.dark-layout #area_trabajo .control-label,
	html.dark-layout #area_trabajo legend { color:#d9d1f8 !important; }
	html.dark-layout #area_trabajo .dataTables_wrapper .dt-button { border-color:#466171 !important; color:#ddd5ff !important; background:#203947 !important; }
	html.dark-layout #area_trabajo .hc4-consultation-form .hc4-clinical-text-section {
		border-color:#3b5363;
		background:#192e3a;
		box-shadow:none;
	}
	html.dark-layout #area_trabajo .hc4-consultation-form .hc4-clinical-text-section > label { color:#ddd5f8 !important; }
	html.dark-layout #area_trabajo .hc4-consultation-form .hc4-rich-editor,
	html.dark-layout #area_trabajo .hc4-consultation-form .hc4-clinical-textarea {
		border-color:#405b6a !important;
		color:#deebf0;
		background:#203744 !important;
		box-shadow:none;
	}
	html.dark-layout #area_trabajo .hc4-consultation-form .mce-toolbar-grp,
	html.dark-layout #area_trabajo .hc4-consultation-form .tox-editor-header,
	html.dark-layout #area_trabajo .hc4-consultation-form .tox-toolbar,
	html.dark-layout #area_trabajo .hc4-consultation-form .tox-toolbar__primary { background:#243c49 !important; }

	/* Vista compacta: elimina espacios de separación que no aportan información. */
	.hc4-patient-detail-page { padding:8px 12px 16px !important; }
	.hc4-patient-detail-title { margin-bottom:8px !important; padding:12px 16px !important; }
	.hc4-patient-primary > .col-md-12 { padding:10px 12px !important; }
	.hc4-patient-primary .detalle_tabla { width:calc(100% - 24px); margin:4px 12px 6px !important; }
	.hc4-patient-primary .detalle_tabla tr:first-child td { padding-top:2px; padding-bottom:3px; }
	.hc4-patient-primary .detalle_tabla tr:nth-child(2) td { padding-top:3px; padding-bottom:5px; }
	.hc4-patient-primary .col-lg-3 { margin-top:4px !important; margin-bottom:0 !important; }
	.hc4-patient-primary .col-lg-3 .col-md-12 { margin-bottom:6px !important; }
	.hc4-patient-primary .col-lg-12 { margin-top:3px !important; margin-bottom:0 !important; padding-left:6px !important; }
	.hc4-patient-primary .antecedentes_responsive > [class*="col-"] { margin-bottom:4px; }
	.hc4-patient-primary textarea { min-height:48px; }
	.hc4-patient-alerts.is-empty { display:none; }
	.hc4-patient-alerts, .hc4-patient-today-consultation { padding-top:2px !important; padding-bottom:2px !important; }
	.hc4-patient-primary .boton_burbuja { display:inline-flex; align-items:center; margin:3px 0 !important; padding:5px 10px; }
	.hc4-patient-primary .boton_burbuja span { margin:0; }
	.hc4-patient-primary > .col-md-12 > br { display:none; }

	@media(max-width:1199px){
		.hc4-patient-detail-grid{display:block}
		.hc4-patient-detail-grid > .area_1_dato,
		.hc4-patient-detail-grid > .hc4-patient-modules{
			float:none!important;
			width:100%!important;
			max-width:100%!important;
		}
		.hc4-patient-detail-grid > .area_1_dato{padding-right:5px!important}
		.hc4-patient-modules{margin-top:12px}
	}
	@media(max-width:767px){ .hc4-patient-detail-page{padding-right:10px!important;padding-left:10px!important}.hc4-patient-primary>.col-md-12>.row{display:block}.hc4-clinical-list #area_trabajo{flex-basis:100%;width:100%;max-width:100%!important;margin-top:10px;margin-left:0}#area_trabajo{padding:8px!important}.modal-dialog{min-height:calc(100vh - 16px);margin:8px auto!important} }
	@media(max-width:767px){
		#area_trabajo .hc4-consultations-view > .box-header > center {
			position:static;
			width:100%;
			max-width:none;
			margin-top:7px;
			transform:none;
			white-space:normal;
		}
	}

	/* Alineación definitiva de las cabeceras cargadas por AJAX en Imágenes. */
	#area_trabajo .hc4-procedure-id,
	#area_trabajo .procedure-summary-header__id {
		display:none !important;
	}

	#area_trabajo .hc4-content-header-row,
	#area_trabajo .procedure-section-header__grid {
		display:grid !important;
		grid-template-columns:minmax(96px,112px) minmax(0,1fr) minmax(96px,112px);
		align-items:center;
		gap:12px;
		width:100%;
		min-height:44px;
		margin:0 !important;
		padding:0 !important;
	}

	#area_trabajo .hc4-content-header-row::before,
	#area_trabajo .procedure-section-header__grid::before,
	#area_trabajo .hc4-evolution-section-header::before,
	#area_trabajo .hc4-evolution-section-header::after,
	#area_trabajo .hc4-evolution-item-header::before,
	#area_trabajo .hc4-evolution-item-header::after {
		display:none !important;
		content:none !important;
	}

	#area_trabajo .hc4-content-header-row::after,
	#area_trabajo .procedure-section-header__grid::after {
		display:block !important;
		grid-column:3;
		width:1px;
		content:"" !important;
	}

	#area_trabajo .hc4-content-header-row > [class*="col-"],
	#area_trabajo .procedure-section-header__grid > [class*="col-"] {
		float:none !important;
		width:100% !important;
		max-width:none !important;
		margin:0 !important;
		padding:0 !important;
	}

	#area_trabajo .hc4-content-edit,
	#area_trabajo .procedure-section-header__action {
		position:static !important;
		grid-column:1;
		justify-self:start;
		width:auto !important;
		margin:0 !important;
		transform:none !important;
	}

	#area_trabajo .hc4-content-title,
	#area_trabajo .procedure-section-header__title {
		grid-column:2;
		width:100% !important;
		min-width:0;
		max-width:none !important;
		color:#fff !important;
		font-size:14px;
		font-weight:600;
		line-height:1.25;
		text-align:center !important;
		white-space:normal;
		word-break:normal;
		overflow-wrap:normal;
	}

	#area_trabajo .hc4-evolution-section-header {
		display:grid !important;
		grid-template-columns:minmax(170px,210px) minmax(0,1fr) 48px !important;
		align-items:center;
		gap:12px;
		min-height:70px;
		padding:10px 14px !important;
		color:#fff !important;
		background:linear-gradient(100deg,#075779 0%,#057ca7 54%,#12aaa7 100%) !important;
	}

	#area_trabajo .hc4-evolution-add {
		position:static !important;
		justify-self:start;
		margin:0 !important;
		transform:none !important;
	}

	#area_trabajo .hc4-evolution-section-title {
		min-width:0;
		color:#fff !important;
		font-size:15px;
		font-weight:600;
		line-height:1.25;
		text-align:center;
	}

	#area_trabajo .hc4-evolution-collapse {
		position:static !important;
		justify-self:end;
		margin:0 !important;
	}

	#area_trabajo .hc4-evolution-item-header {
		display:grid !important;
		grid-template-columns:minmax(96px,112px) minmax(0,1fr) minmax(96px,112px) !important;
		align-items:center;
		gap:12px;
		min-height:62px;
		padding:10px 14px !important;
		color:#fff !important;
		background:linear-gradient(100deg,#075779 0%,#057ca7 54%,#12aaa7 100%) !important;
	}

	#area_trabajo .hc4-evolution-item-header .hc4-endoscopy-action-wrap,
	#area_trabajo .hc4-evolution-item-header .hc4-functional-action-wrap {
		position:static !important;
		grid-column:1;
		justify-self:start;
		width:auto !important;
		margin:0 !important;
		transform:none !important;
	}

	#area_trabajo .hc4-evolution-item-title {
		grid-column:2;
		min-width:0;
		color:#fff !important;
		font-size:14px;
		font-weight:600;
		line-height:1.25;
		text-align:center;
		white-space:normal;
		overflow-wrap:normal;
	}

	#area_trabajo .hc4-evolution-item-spacer {
		grid-column:3;
		width:100%;
	}
</style>
<script type="text/javascript">
    function desplegar_consulta_hoy(idProcedimiento){
        var entrada = idProcedimiento
            ? document.getElementById('consulta' + idProcedimiento)
            : null;
        if (!entrada) {
            entrada = $('#area_trabajo [data-consulta-admitida-hoy="1"]').first().get(0);
        }
        var $entrada = entrada ? $(entrada) : $();

        if (!$entrada.length) return false;

        var $registro = $entrada.find('.hc4-consultation-record').first();
        var $boton = $registro.find('> .box-header [data-widget="collapse"]').first();
        if ($registro.hasClass('collapsed-box')) {
            $registro.removeClass('collapsed-box').children('.box-body').show();
            $boton.attr('aria-expanded', 'true')
                .find('i').removeClass('fa-plus').addClass('fa-minus');
            // Ejecuta únicamente la carga de datos propia de la consulta,
            // sin volver a alternar el estado visual de la tarjeta.
            $boton.triggerHandler('click');
        }

        if (typeof window.inicializarCamposConsulta === 'function') {
            window.inicializarCamposConsulta($entrada);
        }
        if (window.hc4NavegacionClinica) {
            window.hc4NavegacionClinica.cargadorListado = 'cargar_consulta';
            window.hc4NavegacionClinica.proximoNivel = 'detalle';
            window.hc4NavegacionClinica.nivelActual = 'detalle';
        }
        if (typeof window.activarVistaClinica === 'function') {
            window.activarVistaClinica();
        }

        $('html, body').stop(true).animate({
            scrollTop: Math.max($entrada.offset().top - 90, 0)
        }, 350);

        return true;
    }

    function cargar_consulta(origen, idProcedimiento){
        if (window.Hc4Consultations && window.Hc4Consultations.isBusy()) return;
        if ((origen || idProcedimiento) && desplegar_consulta_hoy(idProcedimiento)) return;
        if (window.hc4ConsultaListadoRequest) window.hc4ConsultaListadoRequest.abort();
        var $origen = origen ? $(origen) : $();

        if ($origen.data('cargando')) {
            return;
        }

        var textoOriginal = $origen.length ? $origen.html() : '';
        if ($origen.length) {
            $origen.data('cargando', true)
                .prop('disabled', true)
                .attr('aria-busy', 'true')
                .html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Cargando consulta...');
        }

        window.hc4ConsultaListadoRequest = $.ajax({
            type: "GET",
            url: "{{route('paciente.consulta', ['id_paciente' => $paciente->id])}}",
            dataType: "html"
        }).done(function(datahtml){
            $("#area_trabajo").html(datahtml);

            // Abre directamente la consulta admitida de hoy, tanto desde una
            // acción manual como desde la inicialización automática de la ficha.
            if (idProcedimiento && !desplegar_consulta_hoy(idProcedimiento) && window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Consulta no disponible',
                    text: 'La agenda está admitida, pero no fue posible localizar su formulario clínico.'
                });
            }
        }).fail(function(xhr, status){
            if (status === 'abort') return;
            var mensaje = {!! json_encode(trans('hc4.errorCargar')) !!};
            if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'No se pudo cargar la consulta', text: mensaje });
            } else {
                console.error(mensaje);
            }
        }).always(function(){
            if ($origen.length) {
                $origen.data('cargando', false)
                    .prop('disabled', false)
                    .removeAttr('aria-busy')
                    .html(textoOriginal);
            }
        });
    }

    function atender_consulta_hoy(origen, idAgenda, idProcedimiento){
        if (desplegar_consulta_hoy(idProcedimiento)) return;

        var $origen = $(origen);
        if ($origen.data('preparando')) return;
        var textoOriginal = $origen.html();
        var restaurarBoton = function(){
            $origen.data('preparando', false)
                .prop('disabled', false)
                .removeAttr('aria-busy')
                .html(textoOriginal);
        };

        $origen.data('preparando', true)
            .prop('disabled', true)
            .attr('aria-busy', 'true')
            .html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Abriendo consulta...');

        $.ajax({
            type: 'POST',
            url: "{{url('consulta/admitida/abrir')}}/" + encodeURIComponent('{{$paciente->id}}') + '/' + encodeURIComponent(idAgenda),
            dataType: 'json',
            data: {_token: '{{csrf_token()}}'}
        }).done(function(respuesta){
            restaurarBoton();
            if (!respuesta.ok || !respuesta.procedure_id) {
                Swal.fire({icon: 'warning', title: 'Consulta no disponible', text: 'No se pudo preparar el formulario clínico.'});
                return;
            }
            cargar_consulta(origen, respuesta.procedure_id);
        }).fail(function(xhr){
            restaurarBoton();
            var respuesta = xhr.responseJSON || {};
            Swal.fire({
                icon: 'warning',
                title: 'Consulta no disponible',
                text: respuesta.message || 'No fue posible preparar el formulario clínico de esta consulta.'
            });
        });
    }
</script>
@include('hc4.consulta.creation_script')

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
      <div class="modal-content" >

      </div>
    </div>
</div>

<div class="modal fade" id="foto2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>

<form id="formulario1" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
	{{ csrf_field() }}
	<input type="hidden" name="variable" id="variable">
</form>
<form id="formulario2" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
	{{ csrf_field() }}
	<input type="hidden" name="variable2" id="variable2">
</form>
<form id="formulario3" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
	{{ csrf_field() }}
	<input type="hidden" name="variable3" id="variable3">
</form>
<form id="formulario4" class="form-vertical" role="form" method="POST" action="{{ route('nuevo.diseño') }}">
	{{ csrf_field() }}
	<input type="hidden" name="variable4" id="variable4">
</form>
<section class="content hc4-patient-detail-page" >
	<div class="container-fluid" >
		<div class="row">
		    <div class="col-md-12 hc4-patient-detail-title" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right, #004AC1,#0C8BEC,#004AC1); margin-bottom: 10px">
		     	<div class="row">
			        <div class="col-md-7" style="">
			            <h1 style="font-size: 15px; margin:0;">
			            	<img style="width: 49px;" src="{{asset('/')}}hc4/img/hc_ima.png">
				<b>@lang('hc4.historiaClinicaPaciente')</b>
			            </h1>
			        </div>
			        <div class="col-md-5" style="padding-right: 0px;right: 0px; top: 5px">
				        <!--div class="row">
				            <div class="col-8">
				                <div class="input-group">
				                  	<input value="" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
				                </div>
				            </div>

				            <div class="col-4">
				              	<a class="btn btn-info boton-buscar" style="color: white ; background-color: #da291c;  border-radius: 5px; border: 2px solid white; "  > <i class="fa fa-search" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;BUSCAR&nbsp;&nbsp;&nbsp;</a>
				            </div>
				        </div -->
			        </div>
		        </div>
            </div>
		</div>
	</div>
	<div class="container-fluid" >
		<div class="row hc4-patient-detail-grid">
			<div class="col-md-7 area_1_dato"  style="padding-left: 5px; padding-right: 10px; " >
				<div class="row">
					<div class="col-md-12">
						<div class="hc4-patient-primary" style="border: 2px solid white; border-radius: 8px; margin-left: 8px; margin-bottom: 5px" >
							<div class="col-md-12 responsive_filiacion" style="" >
								<div class="row">
					 				<div class="col-lg-9 col-12" style="padding-left: 0px; padding-right: 15px;" >
										<label style="margin-top: 10px; color: #004AC1; font-family: arial ; margin-left: 20px" >
											<span style="font-family: 'Helvetica general';"><i class="fa fa-exclamation-circle"></i> @lang('hc4.datosPrincipalesPaciente_') </span>
										</label>
										<table class="detalle_tabla" style="margin-left: 20px; margin-bottom: 5px; text-align: left; margin-top: 5px"   >
											<tr>
												<td style="font-size: 11px; width: 300px"> <span style="font-family: 'Helvetica general';">@lang('hc4.paciente')</span></td>
												<td style="font-size: 11px; width: 100px"> <span style="font-family: 'Helvetica general';">@lang('hc4.identificacion')</span></td>
												<td style="font-size: 11px; width: 60px"> <span style="font-family: 'Helvetica general';">@lang('hc4.edad')</span></td>
												<td style="font-size: 11px; width: 80px"> <span style="font-family: 'Helvetica general';">@lang('hc4.seguro')</span></td>
												<td style="font-size: 11px; width: 80px"> <span style="font-family: 'Helvetica general';">@lang('hc4.cortesia')</span></td>
											</tr>
											<tr>
												<td style="font-size: 16px; color: #da291c"> <span style="font-family: 'Helvetica general';">{{ $paciente->apellido1}} @if($paciente->apellido2 != "(N/A)"){{ $paciente->apellido2}}@endif {{ $paciente->nombre1}} @if($paciente->nombre2 != "(N/A)"){{ $paciente->nombre2}}@endif </span></td>
												<td style="font-size: 11px"> {{$paciente->id}} </td>
												<td style="font-size: 11px"> {{$edad}} </td>
												<td style="font-size: 11px"> {{$paciente->seguro->nombre}} </td>
												<td style="font-size: 11px">
												<select id="cortesia_paciente" name="cortesia_paciente" class="form-control input-sm" required onchange="actualiza_cortesia();" style="background-color: #ccffcc; font-size: 11px">
												@php
													$paciente_cort = Sis_medico\cortesia_paciente::where('id', $paciente->id)->get()->first();
												@endphp
												@if(!is_null($paciente_cort))
									<option @if($paciente_cort->cortesia=='NO'){{'selected '}}@endif value="NO">@lang('hc4.no')</option>
									<option @if($paciente_cort->cortesia=='SI'){{'selected '}}@endif value="SI">@lang('hc4.si')</option>
							                    @else
									<option value="NO" selected >@lang('hc4.no')</option>
									<option value="SI" >@lang('hc4.si')</option>
							                    @endif
							                  </select>
												</td>
											</tr>
										</table>
										<div class="col-md-11 has-error" style="padding: 1px; margin-left: 20px; margin-bottom: 10px">
											<form  id="frm">
												<input type="hidden" name="id_paciente" value="{{$paciente->id}}">
				                                <div class="col-md-12" style="padding: 0px;">
															<label for="ale_list" class="control-label" style="color: orange; font-size: 11px"><span style="font-family: 'Helvetica general';">@lang('hc4.alergias')</span></label>
				                                </div>
				                                <div class="col-md-12 has-error" style="padding: 15px; padding-left: 0px; font-size: 11px">
				                                    <select id="ale_list" name="ale_list[]" class="form-control" multiple style="width: 100%;" >
				                                        @foreach($alergiasxpac as $ale_pac)
				                                        <option selected value="{{$ale_pac->id_principio_activo}}" >{{$ale_pac->principio_activo->nombre}}</option>
				                                        @endforeach
				                                    </select>
				                                </div>
			                                </form>
                            			</div>								
									</div>
									<div class="col-lg-3 col-12" style="margin-top: 10px;margin-bottom: 5px; padding-left: 15px" >
										<div class="row">
											<div class="col-md-12 col-xs-6 col-6" style="margin-bottom: 15px">
												<a class="btn btn-info btn_ordenes" style="color: white; height: 100%" onClick="cargar_detalle_filiacion()">
				                               		<div class="col-12" >
					                                  	<div class="row" style="padding-left: 10px; padding-right: 10px;">
			                								<div class="col-2" style="padding-left: 0px; padding-right: 5px" >
			                									<img style="color: black" width="16px" src="{{asset('/')}}hc4/img/iconos/escudo.png">
			                								</div>
								                        	<div class="col-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
										<label style="font-size: 10px">@lang('hc4.verDetallesFiliacion')</label>
								                        	</div>
					                        			</div>
					                        		</div>
			                               		</a>
											</div>
											<div class="col-md-12 col-sm-6 col-6" style="margin-bottom: 15px;">
												<a class="btn btn-info btn_ordenes" style="color: white; height: 100%" onClick="cargar_nueva_receta()">
				                               		<div class="col-12" >
					                                  	<div class="row" style="padding-left: 15px; padding-right: 15px;">
			                								<div class="col-2" style="padding-left: 0px; padding-right: 5px" >
			                									<img style="color: black" width="16px" src="{{asset('/')}}hc4/img/iconos/receta.png">
			                								</div>
								                        	<div class="col-8" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
										<label style="font-size: 10px">@lang('hc4.nuevaReceta')</label>
								                        	</div>
					                        			</div>
					                        		</div>
			                               		</a>
											</div>
											<div class="col-md-12 col-sm-6 col-6" style="margin-bottom: 15px;">
												<a class="btn btn-info btn_ordenes" style="color: white; height: 100%" target="_blank" href="{{ route('paciente.historia', ['id_paciente' => $paciente->id]) }}" >
				                               		<div class="col-12" >
					                                  	<div class="row" style="padding-left: 15px; padding-right: 15px;">
			                								<div class="col-2" style="padding-left: 0px; padding-right: 5px" >
			                									<i class="glyphicon glyphicon-download-alt"></i>
			                								</div>
								                        	<div class="col-9" style="padding-left: 5px; padding-right: 0px; margin-right: 10px">
										<label style="font-size: 10px">@lang('hc4.historiaClinica')</label>
								                        	</div>
					                        			</div>
					                        		</div>
			                               		</a>
											</div>
										</div>
									</div>
									<!--------------->
									@php 
										$datos_pac = $paciente->ho_datos_paciente;
										$texto_def = '';
										if($paciente->sexo == '1'){
											$texto_def = 'NO APLICA';
										}
									@endphp
									<div class="col-lg-12 col-12" style="margin-top: 10px;margin-bottom: 5px; padding-left: 15px" >
										<form id="obs_id" >
											@php
												$observacionAdministrativa = !is_null($paciente_observaciones)
													? trim((string) $paciente_observaciones->observacion)
													: '';
											@endphp
											<label style="color: black; font-size: 11px; margin-left: 20px">
												<span style="font-family: 'Helvetica general';">@lang('hc4.observacionesadministrativas')</span>
											</label>
											<input style="font-size: 12px; width: 95%; margin-left: 20px" onchange="guardar_ob();" value="{{$observacionAdministrativa}}" autocomplete="off" type="text" name="observacion_admin" maxlength="2000" placeholder="@lang('hc4.observacionesadministrativas')">
											<label style=" color: black; font-size: 11px; margin-left: 20px">
											<span style="font-family: 'Helvetica general';"> @lang('hc4.observaciones') generales </span>
											</label>
											<input type="hidden" name="id_paciente"  value="{{$paciente->id}}">
											<input style="font-size: 12px; width: 95%; margin-left: 20px" onchange="guardar_ob();" value="{{$paciente->observacion}}" autocomplete="off" type="text" name="observacion" placeholder="@lang('hc4.observacionesGeneralesPaciente')">
											<label style=" color: black; font-size: 11px; margin-left: 20px">
											<span style="font-family: 'Helvetica general';"> @lang('hc4.habitos') </span>
										    </label>
										    <input style="font-size: 12px; width: 95%; margin-left: 20px" onchange="guardar_ob();" value="{{$paciente->alcohol}}" autocomplete="off" type="text" name="habitos" placeholder="@lang('hc4.habitosPaciente')">
											<div class="col-md-12" style="padding: 10px">
												<div class="col-md-12" style="padding-left: 0px; padding-right: 0px; text-align: center; ">
													<div class="row antecedentes_responsive">
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"> <span style="font-family: 'Helvetica general';">@lang('hc4.antecedentesPatologicos')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="an_patologicos" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_pat}}</textarea>
																	<!--<input style="width: 100%" type="text" name="an_patologicos" value="{{$paciente->antecedentes_pat}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">@lang('hc4.antecedentesFamiliares')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12" >
																	<textarea rows="2" name="an_familiares" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_fam}}</textarea>
																	<!--<input style="width: 100%" type="text" name="an_familiares" value="{{$paciente->antecedentes_fam}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">@lang('hc4.antecedentesQuirurgicos')</span></div>
																<div class="col-md-12 col-sm-6 col-12">
																<textarea rows="2" name="an_quirurgicos" onchange="guardar_ob();" style="width: 95%">{{$paciente->antecedentes_quir}}</textarea>
																<!--<input style="width: 100%" type="text" name="an_quirurgicos" value="{{$paciente->antecedentes_quir}}" onchange="guardar_ob();">-->
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"> <span style="font-family: 'Helvetica general';">@lang('hc4.gynecological_history')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="ginecologico" onchange="guardar_ob();" style="width: 95%">@if($texto_def != ''){{$texto_def}}@else @if(!is_null($datos_pac)){{ $datos_pac->ginecologico }}@endif @endif</textarea>
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">@lang('hc4.pediatric_history')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12" >
																	<textarea rows="2" name="antecedentes_pediatricos" onchange="guardar_ob();" style="width: 95%">@if(!is_null($datos_pac)){{ $datos_pac->antecedentes_pediatricos }}@endif</textarea>
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"><span style="font-family: 'Helvetica general';">@lang('hc4.pharmacological_history')</span></div>
																<div class="col-md-12 col-sm-6 col-12">
																<textarea rows="2" name="farmacologico" onchange="guardar_ob();" style="width: 95%">@if(!is_null($datos_pac)){{ $datos_pac->farmacologico }}@endif</textarea>
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"> <span style="font-family: 'Helvetica general';">@lang('hc4.clinical_history')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="clinico" onchange="guardar_ob();" style="width: 95%">@if(!is_null($datos_pac)){{ $datos_pac->clinico }}@endif</textarea>
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"> <span style="font-family: 'Helvetica general';">@lang('hc4.trauma_history')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="traumatologico" onchange="guardar_ob();" style="width: 95%">@if(!is_null($datos_pac)){{ $datos_pac->traumatologico }}@endif</textarea>
																</div>
															</div>
														</div>
														<div class="col-lg-4 col-sm-12 ">
															<div class="row">
																<div class="col-md-12 col-sm-6 col-12" style="font-size: 12px"> <span style="font-family: 'Helvetica general';">@lang('hc4.otros')</span>
																</div>
																<div class="col-md-12 col-sm-6 col-12">
																	<textarea rows="2" name="otros_antecedente" onchange="guardar_ob();" style="width: 95%">@if(!is_null($datos_pac)){{ $datos_pac->otros_antecedente }}@endif</textarea>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
									    </form>
									</div>	
									<!--------------->
								</div>
							</div>
							@php
						    	$pendoscopicos = Sis_medico\Agenda::join('pentax', 'agenda.id', '=', 'pentax.id_agenda')
						    	->join('pentax_procedimiento', 'pentax.id', '=', 'pentax_procedimiento.id_pentax')
						    	->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
						    	->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
						    	->where('agenda.proc_consul', 1)
						    	->where('agenda.estado_cita', 4)
						    	->where('grupo_procedimiento.tipo_procedimiento', 0)
						    	->where('agenda.id_paciente', $paciente->id)
						    	->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						    	->count();

						    	$hc = Sis_medico\Agenda::join('pentax', 'agenda.id', '=', 'pentax.id_agenda')
						    	->join('pentax_procedimiento', 'pentax.id', '=', 'pentax_procedimiento.id_pentax')
						    	->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
						    	->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
						    	->join('historiaclinica', 'agenda.id', '=', 'historiaclinica.id_agenda')
						    	->where('agenda.proc_consul', 1)
						    	->where('agenda.estado_cita', 4)
						    	->where('grupo_procedimiento.tipo_procedimiento', 0)
						    	->where('agenda.id_paciente', $paciente->id)
						    	->select('historiaclinica.hcid')
						    	->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						    	->first();

						    	$rendoscopicos = DB::table('historiaclinica as h')
						            ->where('h.id_paciente', $paciente->id)
						            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
						            ->join('agenda', 'agenda.id', 'h.id_agenda')
						            ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
						            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
						            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
						            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
						            ->where('gp.tipo_procedimiento', '0')
						            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc')
						            ->count();

								$rendoscopicos2 = DB::table('historiaclinica as h')
						            ->where('h.id_paciente', $paciente->id)
						            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
						            ->join('agenda', 'agenda.id', 'h.id_agenda')
						            ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
						            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
						            ->where('hc_proto.tipo_procedimiento', '0')
						            ->select( 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion' , 'h.id_agenda as id_agenda')
						            ->count();
						    	$fendoscopicos = $pendoscopicos - $rendoscopicos - $rendoscopicos2;
						    @endphp
						    @php
						    	$pfuncional = Sis_medico\Agenda::join('pentax', 'agenda.id', '=', 'pentax.id_agenda')
						    	->join('pentax_procedimiento', 'pentax.id', '=', 'pentax_procedimiento.id_pentax')
						    	->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
						    	->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
						    	->where('agenda.proc_consul', 1)
						    	->where('agenda.estado_cita', 4)
						    	->where('grupo_procedimiento.tipo_procedimiento', 1)
						    	->where('agenda.id_paciente', $paciente->id)
						    	->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						    	->count();

						    	$hc_funcional = Sis_medico\Agenda::join('pentax', 'agenda.id', '=', 'pentax.id_agenda')
						    	->join('pentax_procedimiento', 'pentax.id', '=', 'pentax_procedimiento.id_pentax')
						    	->join('procedimiento', 'procedimiento.id', '=', 'pentax_procedimiento.id_procedimiento')
						    	->join('grupo_procedimiento', 'grupo_procedimiento.id', '=', 'procedimiento.id_grupo_procedimiento')
						    	->join('historiaclinica', 'agenda.id', '=', 'historiaclinica.id_agenda')
						    	->where('agenda.proc_consul', 1)
						    	->where('agenda.estado_cita', 4)
						    	->where('grupo_procedimiento.tipo_procedimiento', 1)
						    	->where('agenda.id_paciente', $paciente->id)
						    	->select('historiaclinica.hcid')
						    	->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						    	->first();

						    	$rfuncional = DB::table('historiaclinica as h')
						            ->where('h.id_paciente', $paciente->id)
						            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
						            ->join('agenda', 'agenda.id', 'h.id_agenda')
						            ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
						            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
						            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
						            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
						            ->where('gp.tipo_procedimiento', '1')
						            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc')
						            ->count();

								$rfuncional2 = DB::table('historiaclinica as h')
						            ->where('h.id_paciente', $paciente->id)
						            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
						            ->join('agenda', 'agenda.id', 'h.id_agenda')
						            ->whereBetween('agenda.fechaini',array(date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59'))
						            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
						            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
						            ->where('hc_proto.tipo_procedimiento', '1')
						            ->select( 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion' , 'h.id_agenda as id_agenda')
						            ->count();
						    	$ffuncional  = $pfuncional  - $rfuncional - $rfuncional2;
						    @endphp
                            @if($consultasAdmitidasHoy > 0)
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    atender_consulta_hoy(null, '{{$consultaAdmitidaHoyId}}', '{{$consultaAdmitidaHoyProcedimientoId}}');
                                });
                            </script>
                            @endif
						</div>
					</div>
					<div id="area_trabajo" data-patient-id="{{$paciente->id}}" class="container-fluid hc4-clinical-workspace"></div>
				</div>
			</div>
			<div id="area_trabajo_2" class="col-md-5 hc4-patient-modules" style="padding-left: 0px; padding-right: 0px; ">
				<div class="row" style="margin-right: 25px;">
					<!--parte 1-->
					<div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
						<div class="row " style="margin-left: 10px;">
							<div class="col-md-6 col-lg-4" style="padding: 3px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila1">
		        						<div class="box-header">
					                        <div class="row" style="margin-left: 0px">
		        								<div style="margin-right: 5px">
		        									<img width="20px" src="{{asset('/')}}hc4/img/iconos/estudios.png">
		        								</div>
					                        	<div>
										<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;@lang('hc4.consulta')</h3>
					                        	</div>
			                        		</div>
					                        <div class="box-tools">
						                          <button type="button" class="btn btn-box-tool"
						                          onclick="cargar_consulta();"
						                            style="color: white"><i class="fa fa-plus"></i>
						                          </button>
					                        </div>
		       						    </div>
		       						</div>
		   						 	<div class="box-body cuerpo" >
		   						 		<div class="row">
										<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
		   						 		<div class="col-md-6" style="color: white"> @if(!is_null($consulta_nueva)) {{$consulta_nueva->hcid}} @endif</div>
		   						 		</div>
		   						 		@if(!is_null($consulta_nueva))
		   						 		@php
		   						 			$agenda_fecha_consulta = DB::table('agenda as a')->where('a.id', $consulta_nueva->id_agenda)->first();
		   						 			//dd($agenda_fecha_consulta);
		   						 		@endphp
		   						 		@if(!is_null($consulta_nueva->fecha_atencion))
					                        @php
					                        $dia =  Date('N',strtotime($consulta_nueva->fecha_atencion));
					                        $mes =  Date('n',strtotime($consulta_nueva->fecha_atencion));
					                        @endphp
				                       		<b>
					                            @if($dia == '1') @lang('hc4.monday')
					                                 @elseif($dia == '2') @lang('hc4.tuesday')
					                                 @elseif($dia == '3') @lang('hc4.wednesday')
					                                 @elseif($dia == '4') @lang('hc4.thursday')
					                                 @elseif($dia == '5') @lang('hc4.friday')
					                                 @elseif($dia == '6') @lang('hc4.saturday')
					                                 @elseif($dia == '7') @lang('hc4.sunday')
					                            @endif
						{{substr($consulta_nueva->fecha_atencion,8,2)}} @lang('hc4.of')
					                            @if($mes == '1') @lang('hc4.january')
					                                 @elseif($mes == '2') @lang('hc4.february')
					                                 @elseif($mes == '3') @lang('hc4.march')
					                                 @elseif($mes == '4') @lang('hc4.april')
					                                 @elseif($mes == '5') @lang('hc4.may')
					                                 @elseif($mes == '6') @lang('hc4.june')
					                                 @elseif($mes == '7') @lang('hc4.july')
					                                 @elseif($mes == '8') @lang('hc4.august')
					                                 @elseif($mes == '9') @lang('hc4.september')
					                                 @elseif($mes == '10') @lang('hc4.october')
					                                 @elseif($mes == '11') @lang('hc4.november')
					                                 @elseif($mes == '12') @lang('hc4.december')
					                            @endif
						@lang('hc4.of') {{substr($consulta_nueva->fecha_atencion,0,4)}}</b>
				                        @else
				                         	@php
					                        $dia =  Date('N',strtotime($agenda_fecha_consulta->fechaini));
					                        $mes =  Date('n',strtotime($agenda_fecha_consulta->fechaini));
					                        @endphp
				                       		<b>
					                            @if($dia == '1') @lang('hc4.monday')
					                                 @elseif($dia == '2') @lang('hc4.tuesday')
					                                 @elseif($dia == '3') @lang('hc4.wednesday')
					                                 @elseif($dia == '4') @lang('hc4.thursday')
					                                 @elseif($dia == '5') @lang('hc4.friday')
					                                 @elseif($dia == '6') @lang('hc4.saturday')
					                                 @elseif($dia == '7') @lang('hc4.sunday')
					                            @endif
						{{substr($agenda_fecha_consulta->fechaini,8,2)}} @lang('hc4.of')
					                            @if($mes == '1') @lang('hc4.january')
					                                 @elseif($mes == '2') @lang('hc4.february')
					                                 @elseif($mes == '3') @lang('hc4.march')
					                                 @elseif($mes == '4') @lang('hc4.april')
					                                 @elseif($mes == '5') @lang('hc4.may')
					                                 @elseif($mes == '6') @lang('hc4.june')
					                                 @elseif($mes == '7') @lang('hc4.july')
					                                 @elseif($mes == '8') @lang('hc4.august')
					                                 @elseif($mes == '9') @lang('hc4.september')
					                                 @elseif($mes == '10') @lang('hc4.october')
					                                 @elseif($mes == '11') @lang('hc4.november')
					                                 @elseif($mes == '12') @lang('hc4.december')
					                            @endif
						@lang('hc4.of') {{substr($agenda_fecha_consulta->fechaini,0,4)}}</b>
				                        @endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @php $doctor_nombre = null; @endphp
				                        @if(!is_null($consulta_nueva))
				                        	@if(!is_null($consulta_nueva->id_doctor_examinador))
						                        @php
						                        	//dd($consulta_nueva);
						                        	$doctor_nombre = DB::table('users as u')->where('u.id', $consulta_nueva->id_doctor_examinador)->first();
						                        	 //dd($doctor_nombre);
						                        @endphp
					                        @endif
				                            <b>@if(!is_null($consulta_nueva))

				                        			@if(!is_null($doctor_nombre))
				                        				Dr.	{{$doctor_nombre->nombre1}} {{$doctor_nombre->apellido1}}
				                        			@else
				                        				@if(!is_null($paciente->agenda->last()->historia_clinica))
				                        				Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
				                        				@endif
				                        			@endif
				                        		@else
				                        			Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} 	{{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
				                        		@endif</b>
				                       	 @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.evolucion') </span>
				                        <br>
				                      	@if(!is_null($consulta_nueva))
					                        @if(!is_null($consulta_nueva->cuadro_clinico))
						                        @if(strlen($consulta_nueva->cuadro_clinico)>= '30')
						                        	<?php echo substr(strip_tags($consulta_nueva->cuadro_clinico), 0, 30) . '...'; ?>
						                        @else
						                        	 <?php echo substr(strip_tags($consulta_nueva->cuadro_clinico), 0, 30); ?>
						                        @endif
						                    @endif
					                    @endif

			                   	    </div>
								</div>
							</div>
							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="">
									<div class="box-header fila1" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									 <img width="20px" src="{{asset('/')}}hc4/img/iconos/receta.png">
                								</div>

					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.recetas')</h3>
					                        	</div>
						                    </div>
					                        <div class="box-tools">
						                          <button type="button" class="btn btn-box-tool"
						                           onclick="cargar_recetas()"
						                           style="color: white"><i class="fa fa-plus"></i>
						                          </button>
					                        </div>
	           						    </div>
	           						</div>
           						 	<div class="box-body cuerpo " >

				                      <div class="row">
									<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($hc_rec)) {{$hc_rec->id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($hc_rec))


						                        @php
						                        	$dia =  Date('N',strtotime($hc_rec->fechaini));
						                        	$mes =  Date('n',strtotime($hc_rec->fechaini));
						                        @endphp

		                                        @if($dia == '1') @lang('hc4.monday')
			                                        @elseif($dia == '2') @lang('hc4.tuesday')
			                                        @elseif($dia == '3') @lang('hc4.wednesday')
			                                        @elseif($dia == '4') @lang('hc4.thursday')
			                                        @elseif($dia == '5') @lang('hc4.friday')
			                                        @elseif($dia == '6') @lang('hc4.saturday')
			                                        @elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif

		{{substr($hc_rec->fechaini,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
		@elseif($mes == '2') @lang('hc4.february')
		@elseif($mes == '3') @lang('hc4.march')
		@elseif($mes == '4') @lang('hc4.april')
		@elseif($mes == '5') @lang('hc4.may')
		@elseif($mes == '6') @lang('hc4.june')
		@elseif($mes == '7') @lang('hc4.july')
		@elseif($mes == '8') @lang('hc4.august')
		@elseif($mes == '9') @lang('hc4.september')
		@elseif($mes == '10') @lang('hc4.october')
		@elseif($mes == '11') @lang('hc4.november')
		@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
		@lang('hc4.of') {{substr($hc_rec->fechaini,0,4)}}


				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">Rp</span>
				                         <br>

				                        @if(!is_null($hc_rec))
					                        @php
												if (strlen($hc_rec->rp) >= 150) {
												    $texto = substr(strip_tags($hc_rec->rp), 0, 150);
												    echo $texto . '...';
												} else {
												    echo strip_tags($hc_rec->rp);
												}
											@endphp
				                        @endif

				                       <br>
			                   	    </div>
       							</div>
							</div>

							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;" >
								<div class="box box-solid box-primary recuadro"	>
									<div class="fila1">
                						<div class="box-header" >
            								<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
                								</div>
					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">PROCEDIMIENTOS</h3>
					                        	</div>
				                        	</div>

					                        <div class="box-tools">
							                        <button type="button" class="btn btn-box-tool"  onclick="cargar_procedimientos();"
						                               style="color: white"><i class="fa fa-plus"></i>
						                        </button>
					                        </div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
									<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> 
											@if(!is_null($doctor_procedimiento_endoscopico)) 
											{{$doctor_procedimiento_endoscopico->hc_proto_id}} 
											@endif</div>
           						 		</div>
           						 		@if(!is_null($doctor_procedimiento_endoscopico))
					                        @if(!is_null($doctor_procedimiento_endoscopico->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($doctor_procedimiento_endoscopico->f_operacion)); 
						                        $mes =  Date('n',strtotime($doctor_procedimiento_endoscopico->f_operacion));
						                        @endphp

		@if($dia == '1') @lang('hc4.monday')
			@elseif($dia == '2') @lang('hc4.tuesday')
			@elseif($dia == '3') @lang('hc4.wednesday')
			@elseif($dia == '4') @lang('hc4.thursday')
			@elseif($dia == '5') @lang('hc4.friday')
			@elseif($dia == '6') @lang('hc4.saturday')
			@elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
			{{substr($doctor_procedimiento_endoscopico->f_operacion,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
			@elseif($mes == '2') @lang('hc4.february')
			@elseif($mes == '3') @lang('hc4.march')
			@elseif($mes == '4') @lang('hc4.april')
			@elseif($mes == '5') @lang('hc4.may')
			@elseif($mes == '6') @lang('hc4.june')
			@elseif($mes == '7') @lang('hc4.july')
			@elseif($mes == '8') @lang('hc4.august')
			@elseif($mes == '9') @lang('hc4.september')
			@elseif($mes == '10') @lang('hc4.october')
			@elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($doctor_procedimiento_endoscopico->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$fecha_agenda = $fechas_agenda->get($doctor_procedimiento_endoscopico->id_agenda);
	                                        		$dia = Date('N', strtotime($fecha_agenda));
	                                        		$mes = Date('n', strtotime($fecha_agenda));
						                        @endphp

		@if($dia == '1') @lang('hc4.monday')
			@elseif($dia == '2') @lang('hc4.tuesday')
			@elseif($dia == '3') @lang('hc4.wednesday')
			@elseif($dia == '4') @lang('hc4.thursday')
			@elseif($dia == '5') @lang('hc4.friday')
			@elseif($dia == '6') @lang('hc4.saturday')
			@elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
			{{substr($fecha_agenda,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
			@elseif($mes == '2') @lang('hc4.february')
			@elseif($mes == '3') @lang('hc4.march')
			@elseif($mes == '4') @lang('hc4.april')
			@elseif($mes == '5') @lang('hc4.may')
			@elseif($mes == '6') @lang('hc4.june')
			@elseif($mes == '7') @lang('hc4.july')
			@elseif($mes == '8') @lang('hc4.august')
			@elseif($mes == '9') @lang('hc4.september')
			@elseif($mes == '10') @lang('hc4.october')
			@elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($fecha_agenda,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_endoscopico))
				                        <b>@if(!is_null($doctor_procedimiento_endoscopico))
					                        	@if(!is_null($doctor_procedimiento_endoscopico->id))
					                        		Dr. {{$doctor_procedimiento_endoscopico->nombre1}} {{$doctor_procedimiento_endoscopico->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $doctor_procedimiento_endoscopico->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_endoscopico))
					                        @if(isset($doctor_procedimiento_endoscopico->nombre))
				                        		@if(strlen($doctor_procedimiento_endoscopico->nombre) >= '25')
						  							{{substr($doctor_procedimiento_endoscopico->nombre,0,25)}}...
						  						@else
						  							{{substr($doctor_procedimiento_endoscopico->nombre,0,25)}}
						  						@endif
					                        @else
						                        @php
							  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $doctor_procedimiento_endoscopico->id_procedimiento)->get();
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

							  						if(strlen($texto) >= '50')
							  							$texto = substr($texto,0,50).'...';
							  					@endphp
						  						{{$texto}}
				                        	@endif
				                        @endif

				                        <br>
			                   	    </div>
       							</div>
							</div>

							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
									<div class="box box-solid box-primary recuadro" >
										<div class="fila2">
	                    						<div class="box-header" >
							                        	<div class="row" style="margin-left: 0px">
			                								<div style="margin-right: 5px">
			                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/ordenes.png">
			                								</div>

								                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.ordenes')</h3>
								                        	</div>
						                        		</div>
								                        <!--<div class="box-tools">
									                        <button type="button" class="btn btn-box-tool"
									                           style="color: white" onclick="alert('¡COMPRAR LICENCIA COMPLETA!')"><i class="fa fa-plus"></i>
									                        </button>
								                        </div>-->
	                   						    </div>
	                   					</div>
	               						<div class="box-body" style="margin-top:5px;padding-left:10px;padding-top:0px; padding-bottom:0px;">
			                                <a class="btn btn-info btn_ordenes" style="color: white" onClick="carga_ordenes_laboratorio()">
					                        	<div class="col-12" style="padding: 0px;  margin-right: 10px">
					                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/lab.png">
															<label style="font-size: 10px">@lang('hc4.laboratory')</label>
					                        	</div>
			                                </a>

		                               		<a class="btn btn-info btn_ordenes" style="color: white" onClick="carga_ordenes_imagenes();">
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
															<label style="font-size: 10px">@lang('hc4.imagenes')</label>
					                        	</div>
		                               		</a>

										<a class="btn btn-info btn_ordenes" style="color: white; height: 33px" onClick="carga_ordenes_proendospico();" >
											<div class="col-12" style="padding: 0px; margin-right: 10px">
												<img style="padding-bottom: 20px" width="16px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
																				<label style="font-size: 10px">PROCEDIMIENTOS</label>
											</div>
										</a>

										@if(false)
										<a class="btn btn-info btn_ordenes" style="color: white; height: 33px" onClick="carga_ordenes_profuncional();">
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img style="padding-bottom: 20px" width="16px" src="{{asset('/')}}hc4/img/iconos/procedimientos_funcionales.png">
															<label style="font-size: 10px">@lang('hc4.procedimientos-')<br>@lang('hc4.funcionales-')</label>
											</div>
										</a>
										@endif

		                               		<!-- <a class="btn btn-info btn_ordenes" style="color: white; " onClick="alert('¡COMPRAR LICENCIA COMPLETA!')" >
					                        	<div class="col-12" style="padding: 0px; margin-right: 10px">
					                        		<img width="16px" src="{{asset('/')}}hc4/img/iconos/biopsias.png">
					                        		<label style="font-size: 10px">BIOPSIAS</label>
					                        	</div>
		                               		</a> -->
					                   	</div>
	       							</div>
							</div>

								@if(false)
								<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila2">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/procedimientos_funcionales.png">
	            								</div>
					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.procedimientos-')<br>@lang('hc4.funcionales-')</h3>
					                        	</div>
			                        		</div>
					                        <div class="box-tools">
					                           	<button type="button" class="btn btn-box-tool"
						                           	onclick="cargar_procedimientos();"
					                           style="color: white"><i class="fa fa-plus"></i>
					                            </button>
					                        </div>
	           						    </div>
	           						</div>
	       						 	<div class="box-body cuerpo" style="font-size: 10px">
					                    <div class="row">
									<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
	           						 		<div class="col-md-6" style="color: white"> @if(!is_null($doctor_procedimiento_funcional)) {{$doctor_procedimiento_funcional->id_procedimiento}} @endif</div>
	       						 		</div>
	       						 			@if(!is_null($doctor_procedimiento_funcional))
						                        @if(!is_null($doctor_procedimiento_funcional->f_operacion))
							                        @php
							                        $dia =  Date('N',strtotime($doctor_procedimiento_funcional->f_operacion));
							                        $mes =  Date('n',strtotime($doctor_procedimiento_funcional->f_operacion)); @endphp
			                                   		<b>
			                                        @if($dia == '1') @lang('hc4.monday')
				                                         @elseif($dia == '2') @lang('hc4.tuesday')
				                                         @elseif($dia == '3') @lang('hc4.wednesday')
				                                         @elseif($dia == '4') @lang('hc4.thursday')
				                                         @elseif($dia == '5') @lang('hc4.friday')
				                                         @elseif($dia == '6') @lang('hc4.saturday')
				                                         @elseif($dia == '7') @lang('hc4.sunday')
			                                        @endif
				{{substr($doctor_procedimiento_funcional->f_operacion,8,2)}} @lang('hc4.of')
			                                        @if($mes == '1') @lang('hc4.january')
				                                         @elseif($mes == '2') @lang('hc4.february')
				                                         @elseif($mes == '3') @lang('hc4.march')
				                                         @elseif($mes == '4') @lang('hc4.april')
				                                         @elseif($mes == '5') @lang('hc4.may')
				                                         @elseif($mes == '6') @lang('hc4.june')
				                                         @elseif($mes == '7') @lang('hc4.july')
				                                         @elseif($mes == '8') @lang('hc4.august')
				                                         @elseif($mes == '9') @lang('hc4.september')
				                                         @elseif($mes == '10') @lang('hc4.october')
				                                         @elseif($mes == '11') @lang('hc4.november')
				                                         @elseif($mes == '12') @lang('hc4.december')
			                                        @endif
				@lang('hc4.of') {{substr($doctor_procedimiento_funcional->f_operacion,0,4)}}</b>
			                                    @else
			                                     	@php
							                        $fecha_agenda = $fechas_agenda->get($doctor_procedimiento_funcional->id_agenda);
							                        $dia = Date('N', strtotime($fecha_agenda));
							                        $mes = Date('n', strtotime($fecha_agenda)); @endphp
			                                   		<b>
			                                        @if($dia == '1') @lang('hc4.monday')
				                                         @elseif($dia == '2') @lang('hc4.tuesday')
				                                         @elseif($dia == '3') @lang('hc4.wednesday')
				                                         @elseif($dia == '4') @lang('hc4.thursday')
				                                         @elseif($dia == '5') @lang('hc4.friday')
				                                         @elseif($dia == '6') @lang('hc4.saturday')
				                                         @elseif($dia == '7') @lang('hc4.sunday')
			                                        @endif
				{{substr($fecha_agenda,8,2)}} @lang('hc4.of')
			                                        @if($mes == '1') @lang('hc4.january')
				                                         @elseif($mes == '2') @lang('hc4.february')
				                                         @elseif($mes == '3') @lang('hc4.march')
				                                         @elseif($mes == '4') @lang('hc4.april')
				                                         @elseif($mes == '5') @lang('hc4.may')
				                                         @elseif($mes == '6') @lang('hc4.june')
				                                         @elseif($mes == '7') @lang('hc4.july')
				                                         @elseif($mes == '8') @lang('hc4.august')
				                                         @elseif($mes == '9') @lang('hc4.september')
				                                         @elseif($mes == '10') @lang('hc4.october')
				                                         @elseif($mes == '11') @lang('hc4.november')
				                                         @elseif($mes == '12') @lang('hc4.december')
			                                        @endif
				@lang('hc4.of') {{substr($fecha_agenda,0,4)}}</b>
						                        @endif
					                        @endif
					                        <br>
				                          <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                          <br>
					                       	@if(!is_null($doctor_procedimiento_funcional))
						                        @if(!is_null($doctor_procedimiento_funcional)) 						@if(!is_null($doctor_procedimiento_funcional->id))Dr. 				{{$doctor_procedimiento_funcional->nombre1}} 						{{$doctor_procedimiento_funcional->apellido1}}
						                        	@else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
						                        	@endif
					                        	@else Dr. {{$paciente->agenda->last()->historia_clinica->doctor_1->nombre1}} {{$paciente->agenda->last()->historia_clinica->doctor_1->apellido1}}
					                        	@endif
					                        @endif

					                       <br>
				                          <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento')</span>
				                          <br>
					                        @if(!is_null($doctor_procedimiento_funcional))
				                        @if(isset($doctor_procedimiento_funcional->nombre))
				                        {{substr($doctor_procedimiento_funcional->nombre,0,50)}}
				                        ...
				                        @else

				                        @php
					  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $doctor_procedimiento_funcional->id_procedimiento)->get();
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

					  						if(strlen($texto) >= '50')
					  							$texto = substr($texto,0,50).'...';
					  					@endphp
					  						{{$texto}}
				                        @endif
				                        @endif
					                        <br>
			                  	    </div>
	   							</div>
								</div>
								@endif
								<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px; z-index: 998;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila2">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/biopsias.png">
	            								</div>
					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.resultadosBiopsias')</h3>
					                        	</div>
			                        		</div>
					                        <div class="box-tools">
					                            <button type="button" class="btn btn-box-tool"
					                            onclick="cargar_biopsias()"
					                            style="color: white"><i class="fa fa-plus"></i>
					                            </button>
					                        </div>
	           						    </div>
	           						</div>

	       						 	<div class="box-body cuerpo" style="font-size: 10px">
	       						 		<center>
	           						 		<table style="text-align: center; margin-top: 25px"   >
	           						 			<tr>
											<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.fecha') </td>
											<td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.accion')</td>
	           						 			</tr>

	       						 				<tr>
	       						 					@if(!is_null($biopsias_1))
	       						 					<td>
	       						 					@if(!is_null($biopsias_1)) {{substr($biopsias_1->created_at,0,10)}} @endif
	       						 					</td>
	       						 					<td>
	           						 					<a class="btn btn-info btn_accion" href="{{asset('laboratorio_externo_descarga')}}/{{$biopsias_1->id}}" >
	           						 						<div class="row" style="margin-left: 0px; margin-right: 0px">
				                								<div style="margin-right: 0px">
				                									<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
				                								</div>

									                        	<div>
											<label style="color: white" >@lang('hc4.verResultados')</label>
									                        	</div>
						                        			</div>
	           						 					</a>
	       						 					</td>
	       						 					@endif
	       						 				</tr>
	       						 				<tr>
	       						 					<td><br></td>
	       						 				</tr>
	       						 				<tr>
	       						 					@if(!is_null($biopsias_2))
	       						 					<td>
	       						 						@if(!is_null($biopsias_2)) {{substr($biopsias_2->created_at,0,10)}} @endif
	       						 					</td>
	       						 					<td>
	           						 					<a class="btn btn-info btn_accion" href="{{asset('hc_ima_nombre')}}/{{$biopsias_2->id}}" >
	           						 						<div class="row" style="margin-left: 0px; margin-right: 0px">
				                								<div style="margin-right: 0px">
				                									<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
				                								</div>

									                        	<div>
											<label style="color: white">@lang('hc4.verResultados')</label>
									                        	</div>
						                        			</div>
	           						 					</a>
	           						 				</td>
	           						 				@endif
	       						 				</tr>
	           						 		</table>
	       						 		</center>
	   								</div>
								</div>
							</div>


							@if(Auth::user()->id == '1307189140')
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.bancoImagenes')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_estudios_paciente();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo"  >
           						 		@if(!is_null($nuevo_armado))
				                        	@if(isset($nuevo_armado->nombre))
				                        	<div style="color: black, font-size: 10px; ">
 <div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">
						  						@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
						  						</div>
					  						</div>

				                        	@else

					                        @php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
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

						  						if(strlen($texto) >= '25')
						  							$texto = substr($texto,0,25).'...';
						  					@endphp
					  						<div style="color: black, font-size: 10px; ">
 <div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
					  						</div>
				                       		@endif
				                        @endif
           						 		<center>
           						 			@if(!is_null($nuevo_armado))
	           						 			@php
								  						$id_protocolo = $nuevo_armado->hc_proto_id;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->OrderBy('created_at', 'desc')->limit(4)->get();
							  					@endphp


							  					@if($imagenes != '[]')
	           						 				<div class="col-md-12">
	           						 					<div class="row">
	           						 					@foreach($imagenes as $imagen)

					                                            @php
					                                                $explotar = explode( '.', $imagen->nombre);
					                                                $extension = end($explotar);
					                                            @endphp
					                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
					                                            <div class="col-6 hc4-procedure-image-item" data-hc4-image-id="{{$imagen->id}}">
					                                            	<a style="" data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                            	<img style="margin-bottom: 4px; max-width: 50px; max-height: 50px"   src="{{asset('hc_ima')}}/{{$imagen->nombre}}"   >
					                                            	</a>
					                                            </div>
					                                            @endif

						           						@endforeach
	           						 					</div>
	           						 				</div>
	           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
	           						 					NO POSEE IMAGENES DE PROCEDIMIENTOS
	           						 				</div>
           						 				@endif
           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
	           						 					NO POSEE IMAGENES DE PROCEDIMIENTOS
	           						 				</div>
       						 				@endif
           						 		</center>
       								</div>
								</div>
							</div>
							@else
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
	            								</div>

					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.armarEstudios')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_imagenes_paciente();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
	           						    </div>
	           						</div>
	       						 	<div class="box-body cuerpo"  >
	       						 		@if(!is_null($nuevo_armado))
				                        	@if(isset($nuevo_armado->nombre))
				                        	<div style="color: black, font-size: 10px; ">
 <div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">
						  						@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
						  						</div>
					  						</div>

				                        	@else

					                        @php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
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

						  						if(strlen($texto) >= '25')
						  							$texto = substr($texto,0,25).'...';
						  					@endphp
					  						<div style="color: black, font-size: 10px; ">
 <div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
					  						</div>
				                       		@endif
				                        @endif
	       						 		<center>
	       						 		<table style="text-align: center; margin-top: 25px" >
	       						 			<tr>
 <td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.fecha')</td>
 <td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.accion')</td>
	       						 			</tr>
	   						 				<tr>
	   						 					<td>
	   						 						@if(!is_null($nuevo_armado))
	   						 							@php
	       						 					$fecha_proc = $fechas_agenda->get($nuevo_armado->id_agenda);

	       						 							$fecha = substr($fecha_proc,0,10);
	       						 							$invert = explode( '-',$fecha);
								                            $fecha_invert = $invert[0]."-".$invert[1]."-".$invert[2];
							                            @endphp
							                            {{$fecha_invert}}
						                        	@endif
	   						 					</td>
	   						 					<td>
	   						 						@php
	   						 							$imagenes = null;
	   						 						@endphp
	   						 						@if(!is_null($nuevo_armado))
	   						 							@php
									  						$id_protocolo = $nuevo_armado->hc_proto_id;
									  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->get()->last();
									  					@endphp
								  					@endif

								  					@if(!is_null($imagenes))
	       						 					<a type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $id_protocolo])}}" data-toggle="modal" data-target="#foto">
	       						 						<div class="row" style="margin-left: 0px; margin-right: 0px">
			                								<div style="margin-right: 0px">
			                									<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
			                								</div>
								                        	<div>
 <label style="color: white" >@lang('hc4.verResultados')</label>
								                        	</div>
					                        			</div>
	       						 					</a>
	       						 					@else
 <div>@lang('hc4.noPoseeImagenesProcedimientos')</div>
	       						 					@endif
	   						 					</td>
	   						 				</tr>
	       						 		</table>
	       						 		</center>
	   								</div>
								</div>
							</div>
							@endif
							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
	            						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
	            								</div>

					                        	<div>
										<h3 class="box-title" style="font-size: 10px; color: white">@lang('hc4.preVisualizarEstudios')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_visualizador_paciente();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
	           						    </div>
	           						</div>
	       						 	<div class="box-body cuerpo"  >
	       						 		@if(!is_null($nuevo_armado))
				                        	@if(isset($nuevo_armado->nombre))
				                        	<div style="color: black, font-size: 10px; ">
 <div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">
						  						@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
						  						</div>
					  						</div>

				                        	@else

					                        @php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
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

						  						if(strlen($texto) >= '25')
						  							$texto = substr($texto,0,25).'...';
						  					@endphp
					  						<div style="color: black, font-size: 10px; ">
 <div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
					  						</div>
				                       		@endif
				                        @endif
	       						 		<center>
	       						 		<table style="text-align: center; margin-top: 25px" >
	       						 			<tr>
 <td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.fecha')</td>
 <td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.accion')</td>
	       						 			</tr>
	   						 				<tr>
	   						 					<td>
	   						 						@if(!is_null($nuevo_armado))
	   						 							@php
	       						 					$fecha_proc = $fechas_agenda->get($nuevo_armado->id_agenda);

	       						 							$fecha = substr($fecha_proc,0,10);
	       						 							$invert = explode( '-',$fecha);
								                            $fecha_invert = $invert[0]."-".$invert[1]."-".$invert[2];
							                            @endphp
							                            {{$fecha_invert}}
						                        	@endif
	   						 					</td>
	   						 					<td>
	   						 						@php
	   						 							$imagenes = null;
	   						 						@endphp
	   						 						@if(!is_null($nuevo_armado))
	   						 							@php
									  						$id_protocolo = $nuevo_armado->hc_proto_id;
									  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->get()->last();
									  					@endphp
								  					@endif

								  					@if(!is_null($imagenes))
	       						 					<a type="button" class="btn btn-info btn_accion" data-remote="{{route('hc_reporte.seleccion_descargar.imagenes', ['id_protocolo' => $id_protocolo])}}" data-toggle="modal" data-target="#foto">
	       						 						<div class="row" style="margin-left: 0px; margin-right: 0px">
			                								<div style="margin-right: 0px">
			                									<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
			                								</div>
								                        	<div>
 <label style="color: white" >@lang('hc4.verResultados')</label>
								                        	</div>
					                        			</div>
	       						 					</a>
	       						 					@else
 <div>@lang('hc4.noPoseeImagenesProcedimientos')</div>
	       						 					@endif
	   						 					</td>
	   						 				</tr>
	       						 		</table>
	       						 		</center>
	   								</div>
								</div>
							</div>
							<div class="col-md-6 col-lg-4" style="padding: 3px; z-index: 998; ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
		                				<div class="box-header" >


	                                        <div class="row" style="margin-left: 0px">
	            								<div style="margin-right: 5px">
	            									<img width="20px" src="{{asset('/')}}hc4/img/iconos/lab.png">
	            								</div>
					                        	<div>
										<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;&nbsp;@lang('hc4.laboratory')</h3>
					                        	</div>
			                        		</div>


								            <div class="box-tools">
									            <button type="button" class="btn btn-box-tool"
									                onclick="cargar_laboratorio();"
									                style="color: white"><i class="fa fa-plus"></i>
									            </button>
								            </div>
		               				    </div>
		               			    </div>
	                                <div class="box-body cuerpo" >
	               						<center>
	                   						<table style="text-align: center; margin-top: 25px" >
	                   						    <tr>
 <td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.fecha') </td>
 <td style="width: 80px; font-family: 'Helvetica general';"> @lang('hc4.accion')</td>
	           						 		    </tr>
	           						 		    @if(!is_null($orden_lab))
	           						 		    <tr>
	           						 		        <td rowspan="2">
	                                                    @if(!is_null($orden_lab)) {{substr($orden_lab->fecha_orden,0,10)}}
	           						 				    @endif
	           						 				</td>
	           						 			</tr>
	           						 			<tr>
	           						 			    <td>
	           						 				    <a target="_blank" class="btn btn-info btn_accion"
	           						 					     onclick="carga_resultado_laboratorio({{$orden_lab->id}});">
	               						 					<div class="row" style="margin-left: 0px; margin-right: 0px">
					                							<div style="margin-right: 0px">
					                								<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
					                						    </div>
	                                                            <div>
 <label style="color: white">@lang('hc4.verResultados')</label>
										                        </div>
							                        		</div>
	           						 			        </a>
	           						 			    </td>
	           						 			</tr>
	           						 		    @endif
	                                        </table>
	               						</center>
	           						</div>
							    </div>
							</div>
							@if(Auth::user()->id == '1307189140')
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.epicrisis')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_epicrisis();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
 <div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($nuevo_armado)) {{$nuevo_armado->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($nuevo_armado))
					                        @if(!is_null($nuevo_armado->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($nuevo_armado->f_operacion)); $mes =  Date('n',strtotime($nuevo_armado->f_operacion));
						                        @endphp

 @if($dia == '1') @lang('hc4.monday')
 @elseif($dia == '2') @lang('hc4.tuesday')
 @elseif($dia == '3') @lang('hc4.wednesday')
 @elseif($dia == '4') @lang('hc4.thursday')
 @elseif($dia == '5') @lang('hc4.friday')
 @elseif($dia == '6') @lang('hc4.saturday')
 @elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
 {{substr($nuevo_armado->f_operacion,8,2)}} @lang('hc4.of')
 @if($mes == '1') @lang('hc4.january')
 @elseif($mes == '2') @lang('hc4.february')
 @elseif($mes == '3') @lang('hc4.march')
 @elseif($mes == '4') @lang('hc4.april')
 @elseif($mes == '5') @lang('hc4.may')
 @elseif($mes == '6') @lang('hc4.june')
 @elseif($mes == '7') @lang('hc4.july')
 @elseif($mes == '8') @lang('hc4.august')
 @elseif($mes == '9') @lang('hc4.september')
 @elseif($mes == '10') @lang('hc4.october')
 @elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($nuevo_armado->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$fecha_agenda = $fechas_agenda->get($nuevo_armado->id_agenda);
	                                        		$dia = Date('N', strtotime($fecha_agenda));
	                                        		$mes = Date('n', strtotime($fecha_agenda));
						                        @endphp

		@if($dia == '1') @lang('hc4.monday')
			@elseif($dia == '2') @lang('hc4.tuesday')
			@elseif($dia == '3') @lang('hc4.wednesday')
			@elseif($dia == '4') @lang('hc4.thursday')
			@elseif($dia == '5') @lang('hc4.friday')
			@elseif($dia == '6') @lang('hc4.saturday')
			@elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
			{{substr($fecha_agenda,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
			@elseif($mes == '2') @lang('hc4.february')
			@elseif($mes == '3') @lang('hc4.march')
			@elseif($mes == '4') @lang('hc4.april')
			@elseif($mes == '5') @lang('hc4.may')
			@elseif($mes == '6') @lang('hc4.june')
			@elseif($mes == '7') @lang('hc4.july')
			@elseif($mes == '8') @lang('hc4.august')
			@elseif($mes == '9') @lang('hc4.september')
			@elseif($mes == '10') @lang('hc4.october')
			@elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($fecha_agenda,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($nuevo_armado))
				                        <b>@if(!is_null($nuevo_armado))
					                        	@if(!is_null($nuevo_armado->id))
					                        		Dr. {{$nuevo_armado->nombre1}} {{$nuevo_armado->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $nuevo_armado->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($nuevo_armado))
					                        @if(isset($nuevo_armado->nombre))
				                        		@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
					                        @else
						                        @php
							  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
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

							  						if(strlen($texto) >= '50')
							  							$texto = substr($texto,0,50).'...';
							  					@endphp
						  						{{$texto}}
				                        	@endif
				                        @endif

				                        <br>
			                   	    </div>
								</div>
							</div>
							@else
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.bancoImagenes')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_estudios_paciente();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo"  >
           						 		@if(!is_null($nuevo_armado))
				                        	@if(isset($nuevo_armado->nombre))
				                        	<div style="color: black, font-size: 10px; ">
											<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">
						  						@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
						  						</div>
					  						</div>

				                        	@else

					                        @php
						  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
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

						  						if(strlen($texto) >= '25')
						  							$texto = substr($texto,0,25).'...';
						  					@endphp
					  						<div style="color: black, font-size: 10px; ">
											<div style="font-family: 'Helvetica general'; text-align: center">@lang('hc4.procedimiento')</div>
						  						<div style="font-family: 'Helvetica general'; text-align: center;">{{$texto}}</div>
					  						</div>
				                       		@endif
				                        @endif
           						 		<center>
           						 			@if(!is_null($nuevo_armado))
	           						 			@php
								  						$id_protocolo = $nuevo_armado->hc_proto_id;
								  						$imagenes = \Sis_medico\hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado','1')->OrderBy('created_at', 'desc')->limit(4)->get();
							  					@endphp


							  					@if($imagenes != '[]')
	           						 				<div class="col-md-12">
	           						 					<div class="row">
	           						 					@foreach($imagenes as $imagen)

					                                            @php
					                                                $explotar = explode( '.', $imagen->nombre);
					                                                $extension = end($explotar);
					                                            @endphp
					                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
					                                            <div class="col-6 hc4-procedure-image-item" data-hc4-image-id="{{$imagen->id}}">
					                                            	<a style="" data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
					                                            	<img style="margin-bottom: 4px; max-width: 50px; max-height: 50px"   src="{{asset('hc_ima')}}/{{$imagen->nombre}}"   >
					                                            	</a>
					                                            </div>
					                                            @endif

						           						@endforeach
	           						 					</div>
	           						 				</div>
	           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
	           						 					NO POSEE IMAGENES DE PROCEDIMIENTOS
	           						 				</div>
           						 				@endif
           						 			@else
	           						 				<div class="col-12" style="padding-top: 20px">
	           						 					NO POSEE IMAGENES DE PROCEDIMIENTOS
	           						 				</div>
       						 				@endif
           						 		</center>
       								</div>
								</div>
							</div>
							@endif

							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px;">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/exa_externos.png">
                								</div>
					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.resultados')<br>@lang('hc4.externos')</h3>
					                        	</div>
			                        		</div>
					                        <div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                          onclick="cargar_resultados_externos();"
					                            style="color: white"><i class="fa fa-plus"></i>
					                          </button>
					                        </div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<center>
           						 		<table style="text-align: center; margin-top: 25px" >
           						 			<tr>
											<td style="width: 80px; font-family: 'Helvetica general';">@lang('hc4.fecha') </td>
											<td style="width: 80px; font-family: 'Helvetica general';" >@lang('hc4.accion')</td>
           						 			</tr>
       						 				<tr>
       						 					@if(!is_null($laboratorio_externo))
       						 					<td>@if(!is_null($laboratorio_externo)) {{substr($laboratorio_externo->created_at,0,10)}} @endif
       						 					</td>
       						 					<td>
												<button type="button" class="btn btn-info btn_accion" data-url-validacion="{{route('laboratorio_externo.disponible', ['id' => $laboratorio_externo->id])}}" onclick="abrir_resultado_externo(this)">
													<div class="row" style="margin-left: 0px; margin-right: 0px">
														<div style="margin-right: 0px">
															<img width="16px" src="{{asset('/')}}hc4/img/iconos/descargar.png">
														</div>
														<div>
															<label style="color: white">@lang('hc4.verResultados')</label>
														</div>
													</div>
													</button>
												</td>
       						 					@endif
       						 				</tr>
           						 		</table>
           						 		</center>
       							    </div>
					            </div>
							</div>

							<div class="col-md-6 col-lg-4" style="padding: 3px; z-index: 998; ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
		                				<div class="box-header" >
	                                        <div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>
					                        	<div>
										<h3 class="box-title" style="font-size: 13px; color: white">&nbsp;&nbsp;&nbsp;@lang('hc4.imagenes')</h3>
					                        	</div>
			                        		</div>
								            <div class="box-tools">
									            <button type="button" class="btn btn-box-tool"
									                onclick="cargar_procedimiento_ecografia();"
									                style="color: white"><i class="fa fa-plus"></i>
									            </button>
								            </div>
		               				    </div>
		               			    </div>
           						 	<div class="box-body cuerpo" >

           						 		<div class="row">
									<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($doctor_procedimiento_ecografia)) {{$doctor_procedimiento_ecografia->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($doctor_procedimiento_ecografia))
					                        @if(!is_null($doctor_procedimiento_ecografia->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($doctor_procedimiento_ecografia->f_operacion)); $mes =  Date('n',strtotime($doctor_procedimiento_ecografia->f_operacion));
						                        @endphp

		@if($dia == '1') @lang('hc4.monday')
			@elseif($dia == '2') @lang('hc4.tuesday')
			@elseif($dia == '3') @lang('hc4.wednesday')
			@elseif($dia == '4') @lang('hc4.thursday')
			@elseif($dia == '5') @lang('hc4.friday')
			@elseif($dia == '6') @lang('hc4.saturday')
			@elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
			{{substr($doctor_procedimiento_ecografia->f_operacion,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
			@elseif($mes == '2') @lang('hc4.february')
			@elseif($mes == '3') @lang('hc4.march')
			@elseif($mes == '4') @lang('hc4.april')
			@elseif($mes == '5') @lang('hc4.may')
			@elseif($mes == '6') @lang('hc4.june')
			@elseif($mes == '7') @lang('hc4.july')
			@elseif($mes == '8') @lang('hc4.august')
			@elseif($mes == '9') @lang('hc4.september')
			@elseif($mes == '10') @lang('hc4.october')
			@elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($doctor_procedimiento_ecografia->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$fecha_agenda = $fechas_agenda->get($doctor_procedimiento_ecografia->id_agenda);
	                                        		$dia = Date('N', strtotime($fecha_agenda));
	                                        		$mes = Date('n', strtotime($fecha_agenda));
						                        @endphp

		@if($dia == '1') @lang('hc4.monday')
			@elseif($dia == '2') @lang('hc4.tuesday')
			@elseif($dia == '3') @lang('hc4.wednesday')
			@elseif($dia == '4') @lang('hc4.thursday')
			@elseif($dia == '5') @lang('hc4.friday')
			@elseif($dia == '6') @lang('hc4.saturday')
			@elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
			{{substr($fecha_agenda,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
			@elseif($mes == '2') @lang('hc4.february')
			@elseif($mes == '3') @lang('hc4.march')
			@elseif($mes == '4') @lang('hc4.april')
			@elseif($mes == '5') @lang('hc4.may')
			@elseif($mes == '6') @lang('hc4.june')
			@elseif($mes == '7') @lang('hc4.july')
			@elseif($mes == '8') @lang('hc4.august')
			@elseif($mes == '9') @lang('hc4.september')
			@elseif($mes == '10') @lang('hc4.october')
			@elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($fecha_agenda,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_ecografia))
				                        <b>@if(!is_null($doctor_procedimiento_ecografia))
					                        	@if(!is_null($doctor_procedimiento_ecografia->id))
					                        		Dr. {{$doctor_procedimiento_ecografia->nombre1}} {{$doctor_procedimiento_ecografia->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $doctor_procedimiento_ecografia->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_ecografia))
				                        @if(isset($doctor_procedimiento_ecografia->nombre))
			                        		@if(strlen($doctor_procedimiento_ecografia->nombre) >= '25')
					  							{{substr($doctor_procedimiento_ecografia->nombre,0,25)}}...
					  						@else
					  							{{substr($doctor_procedimiento_ecografia->nombre,0,25)}}
					  						@endif
				                        @else

				                        @php
					  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $doctor_procedimiento_ecografia->id_procedimiento)->get();
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

					  						if(strlen($texto) >= '50')
					  							$texto = substr($texto,0,50).'...';
					  					@endphp
					  						{{$texto}}
				                        @endif
				                        @endif

				                        <br>
			                   	    </div>
							    </div>
							</div>
							@if(Auth::user()->id == '1307189140')
							@else
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/imagenes.png">
                								</div>

					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.epicrisis')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_epicrisis();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
									<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($nuevo_armado)) {{$nuevo_armado->hc_proto_id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($nuevo_armado))
					                        @if(!is_null($nuevo_armado->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($nuevo_armado->f_operacion)); $mes =  Date('n',strtotime($nuevo_armado->f_operacion));
						                        @endphp

		@if($dia == '1') @lang('hc4.monday')
			@elseif($dia == '2') @lang('hc4.tuesday')
			@elseif($dia == '3') @lang('hc4.wednesday')
			@elseif($dia == '4') @lang('hc4.thursday')
			@elseif($dia == '5') @lang('hc4.friday')
			@elseif($dia == '6') @lang('hc4.saturday')
			@elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
			{{substr($nuevo_armado->f_operacion,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
			@elseif($mes == '2') @lang('hc4.february')
			@elseif($mes == '3') @lang('hc4.march')
			@elseif($mes == '4') @lang('hc4.april')
			@elseif($mes == '5') @lang('hc4.may')
			@elseif($mes == '6') @lang('hc4.june')
			@elseif($mes == '7') @lang('hc4.july')
			@elseif($mes == '8') @lang('hc4.august')
			@elseif($mes == '9') @lang('hc4.september')
			@elseif($mes == '10') @lang('hc4.october')
			@elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($nuevo_armado->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$fecha_agenda = $fechas_agenda->get($nuevo_armado->id_agenda);
	                                        		$dia = Date('N', strtotime($fecha_agenda));
	                                        		$mes = Date('n', strtotime($fecha_agenda));
						                        @endphp

		@if($dia == '1') @lang('hc4.monday')
			@elseif($dia == '2') @lang('hc4.tuesday')
			@elseif($dia == '3') @lang('hc4.wednesday')
			@elseif($dia == '4') @lang('hc4.thursday')
			@elseif($dia == '5') @lang('hc4.friday')
			@elseif($dia == '6') @lang('hc4.saturday')
			@elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
			{{substr($fecha_agenda,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
			@elseif($mes == '2') @lang('hc4.february')
			@elseif($mes == '3') @lang('hc4.march')
			@elseif($mes == '4') @lang('hc4.april')
			@elseif($mes == '5') @lang('hc4.may')
			@elseif($mes == '6') @lang('hc4.june')
			@elseif($mes == '7') @lang('hc4.july')
			@elseif($mes == '8') @lang('hc4.august')
			@elseif($mes == '9') @lang('hc4.september')
			@elseif($mes == '10') @lang('hc4.october')
			@elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($fecha_agenda,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($nuevo_armado))
				                        <b>@if(!is_null($nuevo_armado))
					                        	@if(!is_null($nuevo_armado->id))
					                        		Dr. {{$nuevo_armado->nombre1}} {{$nuevo_armado->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $nuevo_armado->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($nuevo_armado))
					                        @if(isset($nuevo_armado->nombre))
				                        		@if(strlen($nuevo_armado->nombre) >= '25')
						  							{{substr($nuevo_armado->nombre,0,25)}}...
						  						@else
						  							{{substr($nuevo_armado->nombre,0,25)}}
						  						@endif
					                        @else
						                        @php
							  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $nuevo_armado->id_procedimiento)->get();
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

							  						if(strlen($texto) >= '50')
							  							$texto = substr($texto,0,50).'...';
							  					@endphp
						  						{{$texto}}
				                        	@endif
				                        @endif

				                        <br>
			                   	    </div>
								</div>
							</div>
							<div class="col-md-6 col-lg-4" style="padding: 3px;  ">
								<div class="box box-solid box-primary recuadro" >
									<div class="fila3">
                						<div class="box-header" >
			                        		<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
                								</div>
					                        	<div>
										<h3 class="box-title" style="font-size: 12px; color: white">@lang('hc4.proximosprocedimientos-')</h3>
					                        	</div>
			                        		</div>
				                        	<div class="box-tools">
					                          <button type="button" class="btn btn-box-tool"
					                           style="color: white" onclick="cargar_prox_proc();"><i class="fa fa-plus"></i>
					                          </button>
				                        	</div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
									<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($prox_procedimientos)) {{$prox_procedimientos->id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($prox_procedimientos))
					                        @if(!is_null($prox_procedimientos->fechaini))
						                        @php
						                        $dia =  Date('N',strtotime($prox_procedimientos->fechaini)); $mes =  Date('n',strtotime($prox_procedimientos->fechaini));
						                        @endphp
		@if($dia == '1') @lang('hc4.monday')
			@elseif($dia == '2') @lang('hc4.tuesday')
			@elseif($dia == '3') @lang('hc4.wednesday')
			@elseif($dia == '4') @lang('hc4.thursday')
			@elseif($dia == '5') @lang('hc4.friday')
			@elseif($dia == '6') @lang('hc4.saturday')
			@elseif($dia == '7') @lang('hc4.sunday')
	                                        	@endif
			{{substr($prox_procedimientos->fechaini,8,2)}} @lang('hc4.of')
		@if($mes == '1') @lang('hc4.january')
			@elseif($mes == '2') @lang('hc4.february')
			@elseif($mes == '3') @lang('hc4.march')
			@elseif($mes == '4') @lang('hc4.april')
			@elseif($mes == '5') @lang('hc4.may')
			@elseif($mes == '6') @lang('hc4.june')
			@elseif($mes == '7') @lang('hc4.july')
			@elseif($mes == '8') @lang('hc4.august')
			@elseif($mes == '9') @lang('hc4.september')
			@elseif($mes == '10') @lang('hc4.october')
			@elseif($mes == '11') @lang('hc4.november')
			@elseif($mes == '12') @lang('hc4.december')
	                                        	@endif
			@lang('hc4.of') {{substr($prox_procedimientos->fechaini,0,4)}}
					                        @endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($prox_procedimientos))
				                        <b>@if(!is_null($prox_procedimientos))
					                        	@if(!is_null($prox_procedimientos->id_doctor1))
					                        		Dr. {{$prox_procedimientos->doctor1->nombre1}} {{$prox_procedimientos->doctor1->apellido1}}
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        	@if(!is_null($prox_procedimientos))
	                                            @php 
	                                            	$texto = ""; 
		                                            if(!is_null($prox_procedimientos->id_procedimiento)){
		                                                $texto = $procedimientos->find($prox_procedimientos->id_procedimiento)->nombre;
		                                            }
		                                            if(!is_null($prox_procedimientos->id)){ 
		                                                $proc_agenda = \Sis_medico\AgendaProcedimiento::where('id_agenda', $prox_procedimientos->id)->get();
		                                                foreach($proc_agenda as $value2){
		                                                    $texto = $texto.' + '. $value2->procedimiento->nombre  ;
		                                                }
		                                            }
		                                            if(strlen($texto) >= '40'){
									  					$texto = substr($texto,0,40).'...';
		                                            }
	                                            @endphp
	                                            {{$texto}}
	                                        @endif
				                        <br>
			                   	    </div>
								</div>
							</div>
													
							<div class="col-md-6 col-lg-4" style="padding: 3px;left: 0px; z-index: 998;" >
								<div class="box box-solid box-primary recuadro"	>
									<div class="fila3">
                						<div class="box-header" >
            								<div class="row" style="margin-left: 0px">
                								<div style="margin-right: 5px">
                									<img width="20px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
                								</div>

					                        	<div>
					                        		<h3 class="box-title" style="font-size: 12px; color: white"> PROTOCOLO <br> QUIRURGICO </h3>
					                        	</div>
				                        	</div>

					                        <div class="box-tools">
						                        <button type="button" class="btn btn-box-tool"  onclick="cargar_procedimiento_quirurgicos();"
						                               style="color: white"><i class="fa fa-plus"></i>
						                        </button>
					                        </div>
               						    </div>
               						</div>
           						 	<div class="box-body cuerpo" >
           						 		<div class="row">
								<div class="col-md-6"> <span style="font-family: 'Helvetica general';">@lang('hc4.fecha')</span> </div>
           						 		<div class="col-md-6" style="color: white"> @if(!is_null($doctor_procedimiento_quirurgico)) {{$doctor_procedimiento_quirurgico->id}} @endif</div>
           						 		</div>
           						 		@if(!is_null($doctor_procedimiento_quirurgico))
					                        @if(!is_null($doctor_procedimiento_quirurgico->f_operacion))
						                        @php
						                        $dia =  Date('N',strtotime($doctor_procedimiento_quirurgico->f_operacion));
						                        $mes =  Date('n',strtotime($doctor_procedimiento_quirurgico->f_operacion));
						                        @endphp

		@if($dia == '1') @lang('hc4.lunes')
												@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
			{{substr($doctor_procedimiento_quirurgico->f_operacion,8,2)}} @lang('hc4.de')
		@if($mes == '1') @lang('hc4.enero')
												@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr($doctor_procedimiento_quirurgico->f_operacion,0,4)}}
	                                        @else

	                                        	@php
	                                        		$fecha_agenda = $fechas_agenda->get($doctor_procedimiento_quirurgico->id_agenda);
	                                        		$dia = Date('N', strtotime($fecha_agenda));
	                                        		$mes = Date('n', strtotime($fecha_agenda));
						                        @endphp

		@if($dia == '1') @lang('hc4.lunes')
												@elseif($dia == '2') @lang('hc4.martes')
				                                 @elseif($dia == '3') @lang('hc4.miercoles')
				                                 @elseif($dia == '4') @lang('hc4.jueves')
				                                 @elseif($dia == '5') @lang('hc4.viernes')
				                                 @elseif($dia == '6') @lang('hc4.sabado')
				                                 @elseif($dia == '7') @lang('hc4.domingo')
	                                        	@endif
			{{substr($fecha_agenda,8,2)}} @lang('hc4.de')
		@if($mes == '1') @lang('hc4.enero')
												@elseif($mes == '2') @lang('hc4.febrero')
				                                 @elseif($mes == '3') @lang('hc4.marzo')
				                                 @elseif($mes == '4') @lang('hc4.abril')
				                                 @elseif($mes == '5') @lang('hc4.mayo')
				                                 @elseif($mes == '6') @lang('hc4.junio')
				                                 @elseif($mes == '7') @lang('hc4.julio')
				                                 @elseif($mes == '8') @lang('hc4.agosto')
				                                 @elseif($mes == '9') @lang('hc4.septiembre')
				                                 @elseif($mes == '10') @lang('hc4.octubre')
				                                 @elseif($mes == '11') @lang('hc4.noviembre')
				                                 @elseif($mes == '12') @lang('hc4.diciembre')
	                                        	@endif
												@lang('hc4.del') {{substr($fecha_agenda,0,4)}}
					                        @endif
				                        @endif

				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.medicoExaminador')</span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_quirurgico))
				                        <b>@if(!is_null($doctor_procedimiento_quirurgico))
					                        	@if(!is_null($doctor_procedimiento_quirurgico->id))
					                        		Dr. {{$doctor_procedimiento_quirurgico->nombre1}} {{$doctor_procedimiento_quirurgico->apellido1}}
					                        	@else
						                        	@php
						                        		$doc_id_hc = \Sis_medico\Historiaclinica::where('hcid', $doctor_procedimiento_quirurgico->hcid)->first();
						                        	@endphp

						                        	@if(!is_null($doc_id_hc))
							                        	@if (!is_null($doc_id_hc->id_doctor1))
							                        		@php
							                        			$nombre_doc_hc = \Sis_medico\User::where('id', $doc_id_hc->id_doctor1)->first();
							                        		@endphp

							                        		Dr. {{$nombre_doc_hc->nombre1}} {{$nombre_doc_hc->apellido1}}

							                        	@else
							                        		@php
							                        			$doc_id_agenda = \Sis_medico\agenda::where('id', $doc_id_hc->id_agenda)->first();

							                        			$nombre_doc_agenda = \Sis_medico\User::where('id', $doc_id_agenda->id_doctor1)->first();
							                        		@endphp
							                        		Dr. {{$nombre_doc_agenda->nombre1}} {{$nombre_doc_agenda->apellido1}}
							                        	@endif
						                        	@endif
					                        	@endif</b>
				                        	@endif
				                        @endif
				                        <br>
				                        <span style="font-family: 'Helvetica general';">@lang('hc4.procedimiento') </span>
				                        <br>
				                        @if(!is_null($doctor_procedimiento_quirurgico))
					                        @if(isset($doctor_procedimiento_quirurgico->nombre))
				                        		@if(strlen($doctor_procedimiento_quirurgico->nombre) >= '25')
						  							{{substr($doctor_procedimiento_quirurgico->nombre,0,25)}}...
						  						@else
						  							{{substr($doctor_procedimiento_quirurgico->nombre,0,25)}}
						  						@endif
					                        @else
						                        @php
							  						$adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $doctor_procedimiento_quirurgico->id_procedimiento)->get();
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

							  						if(strlen($texto) >= '50')
							  							$texto = substr($texto,0,50).'...';
							  					@endphp
						  						{{$texto}}
				                        	@endif
				                        @endif

				                        <br>
			                   	    </div>
								</div>
							</div>
							@endif
						</div>
					</div>

					<div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
						<div class="row" style="margin-left: 10px;">


						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">

		var hc4NavegacionClinica = {
			nivelActual: 'inicio',
			proximoNivel: 'listado',
			cargadorListado: null
		};

		function obtenerFuncionClinica(elemento){
			var codigo = $(elemento).attr('onclick') || '';
			var coincidencia = codigo.match(/^\s*([A-Za-z_$][\w$]*)\s*\(/);
			return coincidencia ? coincidencia[1] : null;
		}

		function activarVistaClinica(){
			var $area = $('#area_trabajo');
			if(!$area.length){
				return;
			}
			$area.find('.hc4-clinical-workspace-toolbar').remove();
			if(!$area.children().length){
				return;
			}
			var nivel = hc4NavegacionClinica.proximoNivel === 'detalle' ? 'detalle' : 'listado';
			$area.addClass('hc4-hide-workspace-back');

			$('.hc4-patient-detail-page')
				.removeClass('hc4-clinical-list hc4-clinical-detail')
				.addClass(nivel === 'detalle' ? 'hc4-clinical-detail' : 'hc4-clinical-list');
			hc4NavegacionClinica.nivelActual = nivel;
			hc4NavegacionClinica.proximoNivel = nivel;
			window.requestAnimationFrame(function(){
				/* Reserva el alto de las opciones superiores para que no cubran
				   el encabezado de la vista clínica recién cargada. */
				var margenSuperior = window.innerWidth <= 767 ? 78 : 118;
				$('html, body').stop(true).animate({
					scrollTop:Math.max(0, $area.offset().top - margenSuperior)
				}, 280);
			});
		}

		function volverVistaClinica(){
			if(hc4NavegacionClinica.nivelActual === 'detalle' &&
				hc4NavegacionClinica.cargadorListado &&
				typeof window[hc4NavegacionClinica.cargadorListado] === 'function'){
				hc4NavegacionClinica.proximoNivel = 'listado';
				window[hc4NavegacionClinica.cargadorListado]();
				return;
			}
			$('#area_trabajo').removeClass('hc4-hide-workspace-back').empty();
			$('.hc4-patient-detail-page').removeClass('hc4-clinical-list hc4-clinical-detail');
			hc4NavegacionClinica.nivelActual = 'inicio';
			hc4NavegacionClinica.proximoNivel = 'listado';
			window.requestAnimationFrame(function(){
				var $destino = $('.hc4-patient-primary');
				if($destino.length){
					$('html, body').stop(true).animate({scrollTop:$destino.offset().top - 10}, 250);
				}
			});
		}

		$(function(){
			var areaTrabajo = document.getElementById('area_trabajo');
			if(!areaTrabajo){ return; }

			$(document).on('click', '.hc4-patient-modules [onclick], .hc4-patient-primary [onclick]', function(){
				var funcion = obtenerFuncionClinica(this);
				if(funcion){
					hc4NavegacionClinica.cargadorListado = funcion;
				}
				hc4NavegacionClinica.proximoNivel = 'listado';
			});

			$(document).on('click', '#area_trabajo [onclick]', function(){
				if($(this).hasClass('hc4-clinical-workspace-back')){ return; }
				/* Una acción dentro del historial abre un registro o formulario;
				   el botón de regreso debe devolver al listado de origen. */
				hc4NavegacionClinica.proximoNivel = 'detalle';
				hc4NavegacionClinica.nivelActual = 'detalle';
				$('.hc4-patient-detail-page')
					.removeClass('hc4-clinical-list')
					.addClass('hc4-clinical-detail');
			});

			/* Los historiales antiguos despliegan el detalle en el mismo DOM sin
			   ocultar las opciones clínicas de la columna derecha. */
			$(document).on('click', '#area_trabajo .hc4-consultation-record > .box-header, #area_trabajo .hc4-recipe-record > .box-header, #area_trabajo .hc4-endoscopy-record > .box-header, #area_trabajo .hc4-functional-record > .box-header', function(){
				hc4NavegacionClinica.proximoNivel = 'detalle';
				hc4NavegacionClinica.nivelActual = 'detalle';
				$('.hc4-patient-detail-page')
					.removeClass('hc4-clinical-list')
					.addClass('hc4-clinical-detail');
				activarVistaClinica();
			});

			function sincronizarAccionesConsultas(){
				var $component = $('#area_trabajo .hc4-consultations-view').first();
				if(!$component.length){ return; }
				var collapsed = $component.hasClass('collapsed-box') ||
					$component.children('.box-body').is(':hidden');
				$component.toggleClass('hc4-is-collapsed', collapsed);
			}

			$(document).on('click', '#area_trabajo .hc4-consultations-view [data-widget="collapse"], #area_trabajo .hc4-consultations-view [data-card-widget="collapse"]', function(){
				window.setTimeout(sincronizarAccionesConsultas, 0);
			});

			var observadorVistaClinica = new MutationObserver(function(){
				if(areaTrabajo.children.length){
					activarVistaClinica();
					sincronizarAccionesConsultas();
				}
			});
			observadorVistaClinica.observe(areaTrabajo, {childList:true, subtree:true, attributes:true, attributeFilter:['class','style']});
		});

		$('#foto').on('hidden.bs.modal', function(){
	        $(this).removeData('bs.modal');
	        //$(this).find('#imagen_solita').empty().html('');
	    });

		var remoto_href = '';
		jQuery('body').on('click', '[data-toggle="modal"]', function() {
		    if(remoto_href != jQuery(this).data('remote')) {
		        remoto_href = jQuery(this).data('remote');
		        jQuery(jQuery(this).data('target')).removeData('bs.modal');

		        jQuery(jQuery(this).data('target')).find('.modal-body').empty();
		      	//console.log(remoto_href);
		    	//console.log($(this).data('target'));

		    	//console.log(jQuery(this).data('target') + ' .modal-content');
		        jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
		    }
		});



		$('#ale_list').select2({
	        placeholder: "Seleccione Medicamento...",
	        minimumInputLength: 2,
	        ajax: {
	            url: '{{route('generico.find')}}',
	            dataType: 'json',
	            data: function (params) {
	                //console.log(params);
	                return {
	                    q: $.trim(params.term)
	                };
	            },
	            processResults: function (data) {
	                //console.log(data);
	                return {
	                    results: data
	                };
	            },
	            cache: true
	        },
	        tags: true,
	        createTag: function (params) {
	            var term = $.trim(params.term);
	            return {
	                id: term.toUpperCase()+'xnose',
	                text: term.toUpperCase(),
	                newTag: true, // add additional parameters
	            }
	        }
	    });

	    $('#ale_list').on('change', function (e) {
	      //alert("hola");
	      guardar_alergia();
	    });

	    function guardar_alergia(){

	        //alert("ok");
	        $.ajax({
	          type: 'post',
	          url:"{{route('n_filiacion')}}", // hc4/HistoriaPacienteController->ingreso
	          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

	          datatype: 'json',
	          data: $("#frm").serialize(),
	          success: function(data){
	            console.log(data);
	            //alert(data);
	            /*var edad;
	            fecha_nacimiento = $( "#fecha_nacimiento" ).val();
	            edad = calcularEdad(fecha_nacimiento);

	            $('#edad').val( edad );*/
	          },
	          error: function(data){

	            console.log(data.responseJSON);

	          }
	        });
	    }


	    function guardar_ob(){

			$.ajax({

				type: 'post',
	            url:"{{route('paciente_observacion_act')}}",
	            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	            datatype: 'json',
	            data: $("#obs_id").serialize(),
				success: function(data){

				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});

		}



		function scroll_datos(id){
			$('html, body').animate({
		      scrollTop: $(id).offset().top
		    }, 1000)
		}


		function cargar_procedimientos(mensaje){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.procedimiento_endoscopico', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
					$('#msn1').text(mensaje);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}


		function cargar_procedimiento_quirurgicos(mensaje){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.procedimiento_quirurgicos', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
					$('#msn1').text(mensaje);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}





		function cargar_procedimiento_ecografia(mensaje){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.procedimiento_ecografia', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
					$('#msn1').text(mensaje);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

		function cargar_evoluciones(){

			$.ajax({
				type: "GET",
				url: "{{route('paciente.evoluciones', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}


		function cargar_imagenes_paciente(){
			//alert('entra');
			$.ajax({
				type: "GET",
				url: "{{route('paciente.imagenes_paciente', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

		function cargar_visualizador_paciente(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.visualizador_estudio', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}


		function cargar_estudios_paciente(){
			$.ajax({
				type: "GET",
				url: "{{route('hc4.estudios_paciente', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

		function cargar_resultados_externos(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.hc4resultados_externos', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

		function mostrar_resultado_externo_no_disponible(mensaje){
			var texto = mensaje || 'El resultado externo solicitado no se encuentra disponible.';
			if (window.Swal) {
				Swal.fire({
					icon: 'warning',
					title: 'Resultado externo no disponible',
					text: texto,
					confirmButtonText: 'Aceptar'
				});
				return;
			}
			alert(texto);
		}

		function abrir_resultado_externo(origen){
			var $origen = $(origen);
			var urlValidacion = $origen.data('url-validacion');
			if ($origen.data('verificando')) {
				return;
			}

			$origen.data('verificando', true).prop('disabled', true).attr('aria-busy', 'true');

			$.ajax({
				type: 'GET',
				url: urlValidacion,
				dataType: 'json',
				cache: false
			}).done(function(respuesta){
				if (!respuesta.disponible || !respuesta.url_descarga) {
					mostrar_resultado_externo_no_disponible(respuesta.mensaje);
					return;
				}
				window.location.assign(respuesta.url_descarga);
			}).fail(function(xhr){
				var respuesta = xhr.responseJSON || {};
				mostrar_resultado_externo_no_disponible(respuesta.mensaje || 'No fue posible verificar el archivo del resultado externo. Intente nuevamente.');
			}).always(function(){
				$origen.data('verificando', false).prop('disabled', false).removeAttr('aria-busy');
			});
		}

	    function cargar_recetas(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.recetas', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

	    function cargar_nueva_receta(){
			$.ajax({
				type: "POST",
				url: "{{route('paciente_nueva.receta', ['id_paciente' => $paciente->id])}}",
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
				data: {_token: '{{ csrf_token() }}'},
				dataType: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
	    }


		function cargar_laboratorio(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.laboratorio', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

		function cargar_biopsias(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.biopsias', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

		function agregar_procedimiento_hc(div){
    		//alert("agregar");
	    	$.ajax({
				async: true,
				type: "GET",
				url: $('#'+div).val(),
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
	    }

		function cargar_detalle_filiacion(){
			$.ajax({
				type: "GET",
				url: "{{route('paciente.detalle_filiacion', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

		function carga_resultado_laboratorio(id_or){

	      window.open('{{url('inicio/laboratorio/hc4resultados/imprimir')}}/'+id_or,'_blank');

	    }

	    function agregar_procedimiento_tipo(url_datos){
    		//alert("agregar");
    	$.ajax({
			async: true,
			type: "GET",
			url: url_datos,
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert({!! json_encode(trans('hc4.errorCargar')) !!});
			}
			});
	    }

	      function actualiza_cortesia(e){
          cortesia = document.getElementById("cortesia_paciente").value;
          if (cortesia == "SI"){
              act_cortesia_si();
          }
          else if(cortesia == "NO"){
              act_cortesia_no();
          }

      }


      function act_cortesia_si(){

	        $.ajax({
	          type: 'get',
	          url:"{{ route('hc4_filiacion.cortesia', ['id' => $paciente->id, 'c' => 1])}}",
	          datatype: 'json',
	          success: function(data){
	          },
	          error: function(data){
	             //console.log(data);
	          }
	        });

    }

    function act_cortesia_no(){

	        $.ajax({
	          type: 'get',
	          url:"{{ route('hc4_filiacion.cortesia', ['id' => $paciente->id, 'c' => 0])}}",
	          datatype: 'json',
	          success: function(data){
	          },
	          error: function(data){
	             //console.log(data);
	          }
	        });
    }

    function cargar_epicrisis(){

			$.ajax({
				type: "GET",
				url: "{{route('hc4_paciente.epicrisis', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){

					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

    function cargar_prox_proc(){
			$.ajax({
				type: "GET",
				url: "{{route('hc4.index_proximos_proc', ['id_paciente' => $paciente->id])}}",
				data: "",
				datatype: "html",
				success: function(datahtml){
					scroll_datos("#area_trabajo");
					$("#area_trabajo").html(datahtml);
				},
				error:  function(){
					alert({!! json_encode(trans('hc4.errorCargar')) !!});
				}
			});
		}

    //Funcion Permite crear Orden Procedimiento Endoscopico
    //Solo para Doctores
    function carga_ordenes_proendospico(){
    	$.ajax({
		    type: "GET",
			url: "{{route('paciente.orden_proc_endoscopico',['id_paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert({!! json_encode(trans('hc4.errorCargar')) !!});
			}
		});
    }

    //Funcion Permite crear Orden Procedimiento Funcional
    //Solo para Doctores
    function carga_ordenes_profuncional(){
    	$.ajax({
		    type: "GET",
			url: "{{route('paciente.orden_proc_funcional',['id_paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert({!! json_encode(trans('hc4.errorCargar')) !!});
			}
		});
    }


    //Funcion Permite crear Orden de Imagenes
    //Solo para Doctores
    function carga_ordenes_imagenes(){
    	$.ajax({
		    type: "GET",
			url: "{{route('paciente.orden_proc_imagenes',['id_paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert({!! json_encode(trans('hc4.errorCargar')) !!});
			}
		});
    }


    function carga_ordenes_laboratorio(){
    	$.ajax({
		    type: "GET",
			url: "{{route('hc4_orden_lab.index',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

					scroll_datos("#area_trabajo");
				$("#area_trabajo").html(datahtml);
			},
			error:  function(){
				alert({!! json_encode(trans('hc4.errorCargar')) !!});
			}
		});
    }


   </script>
</section>


@endsection




