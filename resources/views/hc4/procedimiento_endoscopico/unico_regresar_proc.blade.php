
<style type="text/css">
 	.parent{
	  	overflow-y:scroll;
     	height: 600px;
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

    .procedure-summary-header {
        display: grid !important;
        grid-template-columns: minmax(245px, .95fr) minmax(360px, 1.65fr) minmax(220px, .8fr) 44px;
        align-items: center;
        gap: 18px;
        min-height: 94px;
        padding: 14px 16px 14px 24px !important;
        color: #fff !important;
        text-align: left !important;
        background: linear-gradient(100deg, #075779 0%, #057ca7 54%, #12aaa7 100%) !important;
        border-bottom: 0 !important;
    }

    .procedure-summary-header::before,
    .procedure-summary-header::after,
    .procedure-section-header::before,
    .procedure-section-header::after,
    .hc4-evolution-section-header::before,
    .hc4-evolution-section-header::after,
    .hc4-evolution-item-header::before,
    .hc4-evolution-item-header::after {
        display: none !important;
        content: none !important;
    }

    .procedure-summary-header__grid {
        display: contents !important;
    }

    .procedure-summary-header__grid > [class*="col-"] {
        width: auto !important;
        max-width: none !important;
        padding: 0 !important;
    }

    .procedure-summary-header__procedure,
    .procedure-summary-header__doctor {
        min-width: 0;
        align-self: center;
        overflow-wrap: anywhere;
    }

    .procedure-summary-header__date,
    .procedure-summary-header__doctor {
        white-space: nowrap;
    }

    .procedure-summary-header__procedure {
        white-space: normal;
        line-height: 1.45;
    }

    .procedure-summary-header > .box-tools {
        position: static !important;
        float: none !important;
        align-self: center;
        justify-self: end;
        margin: 0 !important;
    }

    .procedure-summary-header__id {
        display: none !important;
    }

    .procedure-section-header {
        position: relative;
        display: flex !important;
        align-items: center;
        justify-content: center;
        min-height: 62px;
        padding: 10px 14px !important;
        color: #fff !important;
        background: linear-gradient(100deg, #075779 0%, #057ca7 54%, #12aaa7 100%) !important;
        border-bottom: 0 !important;
    }

    .procedure-section-header__grid {
        position: relative;
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 38px;
        margin: 0 !important;
        padding: 0 126px !important;
    }

    .procedure-section-header__grid > [class*="col-"] {
        width: auto !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .procedure-section-header__action {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        white-space: nowrap;
    }

    .procedure-section-header__action .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 38px;
        padding: 7px 10px;
        color: #fff !important;
        background: rgba(255, 255, 255, .12) !important;
        border: 1px solid rgba(255, 255, 255, .35) !important;
        border-radius: 8px !important;
        font-family: 'Helvetica general3';
        font-size: 13px;
        line-height: 1;
        text-decoration: none !important;
        cursor: pointer;
    }

    .procedure-section-header__action .btn i {
        position: static !important;
        display: inline-block;
        margin: 0 !important;
        font-size: 16px;
        line-height: 1;
        transform: none !important;
    }

    .procedure-section-header__action .btn span {
        display: inline-block;
        margin: 0 !important;
        line-height: 1;
    }

    .procedure-section-header__title {
        flex: 1 1 auto !important;
        min-width: 0;
        max-width: none !important;
        width: 100%;
        text-align: center;
        line-height: 1.3;
        overflow-wrap: normal;
        word-break: normal;
    }

    .selected-procedure-title,
    .selected-procedure-patient {
        background: transparent !important;
        box-shadow: none !important;
    }

    .hc4-evolution-section-header,
    .hc4-evolution-item-header {
        display: grid !important;
        align-items: center;
        gap: 12px;
        width: 100%;
        margin: 0 !important;
        color: #fff !important;
        background: linear-gradient(100deg, #075779 0%, #057ca7 54%, #12aaa7 100%) !important;
        border-bottom: 0 !important;
    }

    .hc4-evolution-section-header {
        grid-template-columns: minmax(190px, 230px) minmax(0, 1fr) 48px;
        min-height: 64px;
        padding: 8px 14px !important;
    }

    .hc4-evolution-add,
    .hc4-evolution-item-header .hc4-endoscopy-inline-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 7px 12px;
        color: #fff !important;
        background: rgba(255, 255, 255, .12) !important;
        border: 1px solid rgba(255, 255, 255, .35) !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        line-height: 1.15;
    }

    .hc4-evolution-add img {
        width: 22px;
        height: 22px;
        object-fit: contain;
    }

    .hc4-evolution-section-title,
    .hc4-evolution-item-title {
        min-width: 0;
        text-align: center;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .hc4-evolution-collapse {
        position: static !important;
        justify-self: end;
        margin: 0 !important;
    }

    .hc4-evolution-collapse .btn {
        width: 42px;
        height: 42px;
        color: #fff !important;
        background: rgba(255, 255, 255, .12) !important;
        border: 1px solid rgba(255, 255, 255, .35) !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }

    .hc4-evolution-item-header {
        grid-template-columns: 110px minmax(0, 1fr) 110px;
        min-height: 56px;
        padding: 8px 14px !important;
    }

    .hc4-evolution-item-header .hc4-endoscopy-action-wrap {
        position: static !important;
        justify-self: start;
        margin: 0 !important;
    }

    .hc4-evolution-item-title {
        grid-column: 2;
    }

    .hc4-evolution-item-spacer {
        width: 110px;
    }

    @media (max-width: 767px) {
        .procedure-summary-header {
            grid-template-columns: minmax(0, 1fr) 38px 42px;
            gap: 8px 10px;
            padding: 14px !important;
        }

        .procedure-summary-header__date,
        .procedure-summary-header__procedure,
        .procedure-summary-header__doctor {
            grid-column: 1 / -1;
            white-space: normal;
        }

        .procedure-summary-header__id {
            grid-column: 2;
            grid-row: 1;
        }

        .procedure-summary-header > .box-tools {
            grid-column: 3;
            grid-row: 1;
        }

        .procedure-section-header__grid {
            min-height: 84px;
            padding: 48px 0 0 !important;
        }

        .procedure-section-header__action {
            top: 0;
            transform: none;
        }

        .hc4-evolution-section-header {
            grid-template-columns: minmax(0, 1fr) 44px;
        }

        .hc4-evolution-add {
            grid-column: 1;
            justify-self: start;
        }

        .hc4-evolution-section-title {
            grid-column: 1 / -1;
            grid-row: 2;
        }

        .hc4-evolution-collapse {
            grid-column: 2;
            grid-row: 1;
        }

        .hc4-evolution-item-header {
            grid-template-columns: 94px minmax(0, 1fr);
        }

        .hc4-evolution-item-spacer {
            display: none;
        }
    }

    /* Cabeceras del detalle cargado dentro del módulo Imágenes. */
    #area_trabajo .procedure-summary-header {
        grid-template-columns: minmax(245px, .95fr) minmax(360px, 1.65fr) minmax(220px, .8fr) 44px;
    }

    #area_trabajo .procedure-summary-header__id {
        display: none !important;
    }

    #area_trabajo .procedure-section-header {
        display: block !important;
        min-height: 64px;
        padding: 10px 14px !important;
    }

    #area_trabajo .procedure-section-header__grid {
        display: grid !important;
        grid-template-columns: minmax(96px, 112px) minmax(0, 1fr) minmax(96px, 112px);
        align-items: center;
        gap: 12px;
        min-height: 42px;
        padding: 0 !important;
    }

    #area_trabajo .procedure-section-header__grid::before {
        display: none !important;
        content: none !important;
    }

    #area_trabajo .procedure-section-header__grid::after {
        display: block !important;
        grid-column: 3;
        width: 100%;
        content: "" !important;
    }

    #area_trabajo .procedure-section-header__action,
    #area_trabajo .procedure-section-header__action.hc4-content-edit {
        position: static !important;
        grid-column: 1;
        justify-self: start;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
    }

    #area_trabajo .procedure-section-header__title {
        grid-column: 2;
        width: auto !important;
        min-width: 0;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.25;
        text-align: center !important;
        overflow-wrap: normal;
        word-break: normal;
    }

    #area_trabajo .procedure-section-header__action .hc4-endoscopy-inline-action,
    #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-inline-action {
        position: static !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: auto !important;
        min-width: 82px;
        min-height: 40px;
        margin: 0 !important;
        padding: 8px 11px !important;
        color: #fff !important;
        background: rgba(255, 255, 255, .13) !important;
        border: 1px solid rgba(255, 255, 255, .38) !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        line-height: 1;
        white-space: nowrap;
        transform: none !important;
    }

    #area_trabajo .procedure-section-header__action .hc4-endoscopy-inline-action i,
    #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-inline-action i {
        position: static !important;
        display: inline-block !important;
        margin: 0 !important;
        font-size: 16px;
        line-height: 1;
        transform: none !important;
    }

    #area_trabajo .procedure-section-header__action .hc4-endoscopy-inline-action span,
    #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-inline-action span {
        display: inline-block !important;
        margin: 0 !important;
        line-height: 1;
    }

    #area_trabajo .hc4-procedure-detail-panel .procedure-section-header__grid {
        grid-template-columns: minmax(96px, 112px) minmax(0, 1fr) minmax(245px, 285px) !important;
    }

    #area_trabajo .hc4-content-mode {
        grid-column: 3;
        justify-self: end;
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
        flex: 0 0 42px;
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
        display: none !important;
    }

    #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-detail-panel {
        flex: 0 0 66.666667% !important;
        max-width: 66.666667% !important;
        width: 66.666667% !important;
        padding-right: 5px !important;
    }

    #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-images-panel {
        display: block !important;
        flex: 0 0 33.333333% !important;
        max-width: 33.333333% !important;
        width: 33.333333% !important;
        padding: 0 10px 0 5px !important;
    }

    #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-link-panel,
    #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode .hc4-procedure-link-edit-section {
        display: none !important;
    }

    #area_trabajo .hc4-procedure-link-panel {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        width: 100%;
        padding: 12px 10px 0 !important;
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

    #area_trabajo .hc4-procedure-images-panel .procedure-section-header__grid {
        grid-template-columns: minmax(88px, 100px) minmax(150px, 1fr) !important;
    }

    #area_trabajo .hc4-procedure-images-panel .procedure-section-header__grid::after {
        display: none !important;
    }

    #area_trabajo .hc4-procedure-images-panel .procedure-section-header__title {
        min-width: 150px;
        white-space: normal !important;
        word-break: keep-all !important;
        overflow-wrap: normal !important;
    }

    #area_trabajo .hc4-evolution-section-header {
        grid-template-columns: minmax(170px, 210px) minmax(0, 1fr) 48px;
        min-height: 70px;
        padding: 10px 14px !important;
        color: #fff !important;
        background: linear-gradient(100deg, #075779 0%, #057ca7 54%, #12aaa7 100%) !important;
    }

    #area_trabajo .hc4-evolution-add {
        position: static !important;
        justify-self: start;
        margin: 0 !important;
        transform: none !important;
    }

    #area_trabajo .hc4-evolution-section-title {
        color: #fff !important;
        font-size: 15px;
        font-weight: 600;
    }

    #area_trabajo .hc4-evolution-collapse {
        position: static !important;
        float: none !important;
        justify-self: end;
        margin: 0 !important;
    }

    #area_trabajo .hc4-evolution-item-header {
        grid-template-columns: 110px minmax(0, 1fr) 110px;
        min-height: 62px;
        padding: 10px 14px !important;
    }

    #area_trabajo .hc4-evolution-item-header .hc4-endoscopy-action-wrap,
    #area_trabajo .hc4-evolution-item-header .hc4-content-edit {
        position: static !important;
        justify-self: start;
        width: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
    }

    #area_trabajo .hc4-evolution-item-title {
        grid-column: 2;
        color: #fff !important;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.25;
        text-align: center;
    }

    #area_trabajo .hc4-evolution-item-spacer {
        display: block;
        grid-column: 3;
        width: 110px;
    }

    @media (max-width: 767px) {
        #area_trabajo .procedure-summary-header {
            grid-template-columns: minmax(0, 1fr) 44px;
        }

        #area_trabajo .procedure-summary-header__date,
        #area_trabajo .procedure-summary-header__procedure,
        #area_trabajo .procedure-summary-header__doctor {
            grid-column: 1 / -1;
        }

        #area_trabajo .procedure-summary-header > .box-tools {
            grid-column: 2;
            grid-row: 1;
        }

        #area_trabajo .procedure-section-header__grid {
            grid-template-columns: 92px minmax(0, 1fr);
        }

        #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-detail-panel,
        #area_trabajo .hc4-procedure-media-row.hc4-procedure-image-mode > .hc4-procedure-images-panel {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 10px 10px !important;
        }

        #area_trabajo .procedure-section-header__grid::after {
            display: none !important;
        }

        #area_trabajo .hc4-evolution-section-header {
            grid-template-columns: minmax(0, 1fr) 44px;
        }

        #area_trabajo .hc4-evolution-section-title {
            grid-column: 1 / -1;
            grid-row: 2;
        }

        #area_trabajo .hc4-evolution-item-header {
            grid-template-columns: 92px minmax(0, 1fr);
        }

        #area_trabajo .hc4-evolution-item-spacer {
            display: none;
        }
    }
</style>
@php
	$ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
@endphp
<script type="text/javascript">
    function editarprocedimiento(id, id_paciente){
    	var entra = id;
			$.ajax({
			type: "GET",
			url: "{{route('editar.procedimiento_endoscopico')}}/"+id+'/'+id_paciente,
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
	function editarprocedimiento_ecografia(id, id_paciente){
    	var entra = id;
			$.ajax({
			type: "GET",
			url: "{{route('editar.procedimiento_ecografia')}}/"+id+'/'+id_paciente,
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
<div class="box " style="border: 2px solid #004AC1; background-color: white; ">
  <div class="box-header with-border" style="background: linear-gradient(100deg, #075779 0%, #057ca7 54%, #12aaa7 100%); color: white; font-family: 'Helvetica general3'; border-bottom: 0;">
  	<div class="row">
	  	<div class="col-md-12 col-sm-12 col-12">
		    <h1 class="selected-procedure-title" style="font-size: 15px; margin:0; color: white;" >
            	<img style="width: 35px; margin-left: 5px; margin-bottom: 5px" src="{{asset('/')}}hc4/img/iconos/pendo.png">
             	<b>PROCEDIMIENTO SELECCIONADO</b>
			</h1>
		</div>

	</div>
	@if(!is_null($paciente))
		<center>
			<div class="col-12" style="padding-top: 15px">
				<h1 class="selected-procedure-patient" style="font-size: 14px; margin:0; color: white; padding-left: 20px" >
			        <b>PACIENTE : {{$paciente->apellido1}} {{$paciente->apellido2}}
			        	{{$paciente->nombre1}} {{$paciente->nombre2}}
	                </b>
				</h1>
		    </div>
		</center>
	@endif
    <!-- /.box-tools -->
  </div>
  <!-- /.box-header -->
  <div class="box-body" style="background-color: #56ABE3;">
  	<div class="col-12">
	  	<div class="row parent" >
	  		<span id="msn1" style="color: white; "></span>
	  		@foreach($procedimientos2 as $value)

	  			@php
	  				if($paciente->sexo == 1){
	                    $sexo = "MASCULINO";
	                }else{
	                    $sexo = "FEMENINO";
	                }
	                $seguro =  \Sis_medico\Seguro::find($value->seguro_final);
	                $nombre_procedimiento = "";
	                $historia =\Sis_medico\Historiaclinica::find($value->id_hc);
	                $alergias = \Sis_medico\Paciente_Alergia::where('id_paciente', $historia->id_paciente)->get();
					if($alergias == "[]"){
	                    $alergia = "No";
	                }else{
	                    $alergia  = "";
	                    foreach ($alergias as $value_alergia) {
	                        if($alergia == ""){
	                            $alergia = $value_alergia->principio_activo->nombre;
	                        }else{
	                            $alergia = $alergia.", ".$value_alergia->principio_activo->nombre;
	                        }

	                    }
	                }

	                $procedimientos = \Sis_medico\hc_procedimientos::find($value->id_procedimiento);
	                //dd($value);
	                if($procedimientos->id_procedimiento != null){
	                    $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
	                }
	                $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_procedimiento)->get();

  					$mas = true;
  					$nombre_procedimiento = "";

	          		foreach($adicionales as $value2)
	          		{
						if($mas == true){
						 	$nombre_procedimiento = $nombre_procedimiento.$value2->procedimiento->nombre  ;
						 	$mas = false;
						 }
						else{
					  	 	$nombre_procedimiento = $nombre_procedimiento.' + '.  $value2->procedimiento->nombre  ;
					  	 }
					}
	                $edad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
	  				$contador = \Sis_medico\Hc_Evolucion::where('hc_id_procedimiento', $value->id_procedimiento)->where('secuencia', 0)->orderBy('secuencia', 'DESC')->count();
	  				if($contador == 0){
	  					$cuadro_clinico ="<p>PACIENTE ".$sexo." DE ".$edad." AÑOS DE EDAD ACUDE CON ORDEN DEL ".$seguro->nombre." PARA LA REALIZACION DE ".$nombre_procedimiento."<br> APP: ".$paciente->antecedentes_pat." <br> APF: ".$paciente->antecedentes_fam."<br> APQX: ".$paciente->antecedentes_quir."<br> ALERGIAS: ".$alergia."<br></p>";
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
		    		<div class="box collapse-box " style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 0px; ">
						<div class="box-header with-border procedure-summary-header" style="background-color: white; color: black; font-family: 'Helvetica general3';border-bottom: #004AC1;">

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
							<div class="row procedure-summary-header__grid">
								<div class="col-4 procedure-summary-header__date">
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
								<div class="col-4 procedure-summary-header__procedure">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
										<span style="font-size: 12px">@if(!is_null($texto)) {{$texto}} @endif </span>
									</div>
								</div>
								<div class="col-3 procedure-summary-header__doctor">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
										<span style="font-size: 12px">@if(!is_null($value->nombre1)) {{$value->nombre1}} {{$value->apellido1}} @endif</span>
									</div>
								</div>
			               	</div>
			               	<div class="pull-right box-tools ">
		                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
		                            <i class="fa fa-plus"></i>
		                        </button>
		               		</div>
						    <!-- /.box-tools -->
						</div>
						<div class="box-body" style="background: white;">
                            <div class="row hc4-procedure-media-row" id="hc4_images_procedure_media_{{$value->id_protocolo}}">
								<div class="col-md-12 col-sm-12 col-12 hc4-procedure-detail-panel hc4-procedure-summary-{{$value->id_protocolo}}" style="margin-bottom: 5px">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">

<div class="box-header with-border procedure-section-header" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
<div class="row procedure-section-header__grid">
<div class="col-3 procedure-section-header__action hc4-endoscopy-action-wrap hc4-content-edit">
										<button type="button" class="btn hc4-endoscopy-inline-action" @if($value->tipo == 0) onclick="editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');" @else onclick="editarprocedimiento_ecografia({{$value->id_procedimiento}}, '{{$paciente->id}}');" @endif>
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Editar</span>
										</button>
									</div>
<div class="col-8 procedure-section-header__title" style="text-align: center">
							  						<span>Detalles del procedimiento</span>
                                    </div>
									<div class="hc4-content-mode">
										<label class="hc4-procedure-mode-switch" title="Alternar entre enlace e imágenes capturadas">
											<span class="hc4-procedure-mode-switch__label">Enlazar imágenes</span>
											<input type="checkbox" onchange="alternar_modo_imagenes_cuadro(this, {{$value->id_protocolo}})">
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
							  						<span>Dr. {{$value->nombre1}} {{$value->apellido1}}</span>
							  					</div>
							  				</div>
							  			</div>
                                </div>
                            </div>
								<div class="col-12 hc4-procedure-link-panel hc4-procedure-summary-{{$value->id_protocolo}}">
									@include('hc4.partials.procedure_image_links_view', ['variant' => 'procedure', 'copyHandler' => 'copiar_link_imagenes_cuadro'])
								</div>
								<div class="col-md-4 col-sm-12 col-12 hc4-procedure-images-panel hc4-procedure-summary-{{$value->id_protocolo}}">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
<div class="box-header with-border procedure-section-header" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
<div class="row procedure-section-header__grid">
<div class="col-3 procedure-section-header__action hc4-endoscopy-action-wrap hc4-content-edit">
										<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_imagenes({{$value->id_protocolo}})">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Editar</span>
										</button>
									</div>
<div class="col-8 procedure-section-header__title" style="text-align: center">
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
							  					<div class="col-md-6 col-sm-4 ">
		                                            @php
		                                                $explotar = explode( '.', $imagen->nombre);
		                                                $extension = end($explotar);
		                                            @endphp
		                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
		                                                <a data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
		                                                    <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
		                                                </a><br>
		                                            @elseif(($extension == 'pdf') || ($extension == 'PDF'))
		                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
		                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
		                                                </a>
		                                            @elseif(($extension == 'mp4'))
		                                                <a data-toggle="modal" data-target="#video" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
		                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
		                                                </a>
		                                            @else
		                                                @php
		                                                    $variable = explode('/' , asset('/hc_ima/'));
		                                                    $d1 = $variable[3];
		                                                    $d2 = $variable[4];
		                                                    $d3 = $variable[5];
		                                                 @endphp
		                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
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
                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="Expandir o contraer"><i class="fa fa-plus"></i></button>
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
                                <button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');"><i class="fa fa-pencil-square-o"></i><span>Editar</span></button>
                                </div>
                                <div class="hc4-evolution-item-title">DETALLES DE LA EVOLUCIÓN</div>
                                <div class="hc4-evolution-item-spacer" aria-hidden="true"></div>
								  				</div>
								  				<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
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
							                                <button id="bagregar{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
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
						  	</div>
						</div>
					</div>
		    	</div>
		    	@if($fecha_r == date('Y-m-d') )
				<script type="text/javascript">
						@if($value->tipo == 0)
							editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');
						@else
							editarprocedimiento_ecografia({{$value->id_procedimiento}}, '{{$paciente->id}}');
						@endif

				</script>
				@endif
	    	@endforeach

	    	@foreach($procedimientos1 as $value)
		    	<div class="col-12">
		    		<div class="box collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
						<div class="box-header with-border procedure-summary-header" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
<div class="row procedure-summary-header__grid">
<div class="col-4 procedure-summary-header__date">
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
				                    <div class="col-4 procedure-summary-header__procedure">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
										<span style="font-size: 12px"> {{$value->nombre}} </span>
									</div>
								</div>
								<div class="col-3 procedure-summary-header__doctor">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
										<span style="font-size: 12px"> {{$value->nombre1}} {{$value->apellido1}} </span>
									</div>
								</div>
						    </div>
					    	<div class="pull-right box-tools">
	                        	<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
	                            	<i class="fa fa-plus"></i>
	                            </button>
	                    	</div>
						    <!-- /.box-tools -->
						</div>
						<div class="box-body" style="background: white;">
                            <div class="row hc4-procedure-media-row" id="hc4_images_procedure_media_{{$value->id_protocolo}}">
								<div class="col-md-12 col-sm-12 col-12 hc4-procedure-detail-panel hc4-procedure-summary-{{$value->id_protocolo}}" style="margin-bottom: 5px">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
<div class="box-header with-border procedure-section-header" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
<div class="row procedure-section-header__grid">
<div class="col-3 procedure-section-header__action hc4-endoscopy-action-wrap hc4-content-edit">
										<button type="button" class="btn hc4-endoscopy-inline-action" @if($value->tipo_procedimiento == 0) onclick="editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');" @else onclick="editarprocedimiento_ecografia({{$value->id_procedimiento}}, '{{$paciente->id}}');" @endif>
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Editar</span>
										</button>
									</div>
<div class="col-8 procedure-section-header__title" style="text-align: center">
							  						<span>Detalles del procedimiento</span>
                                    </div>
									<div class="hc4-content-mode">
										<label class="hc4-procedure-mode-switch" title="Alternar entre enlace e imágenes capturadas">
											<span class="hc4-procedure-mode-switch__label">Enlazar imágenes</span>
											<input type="checkbox" onchange="alternar_modo_imagenes_cuadro(this, {{$value->id_protocolo}})">
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
							  						<span>Dr. {{$value->nombre1}} {{$value->apellido1}}</span>
							  					</div>
							  				</div>
							  			</div>
                                </div>
                            </div>
								<div class="col-12 hc4-procedure-link-panel hc4-procedure-summary-{{$value->id_protocolo}}">
									@include('hc4.partials.procedure_image_links_view', ['variant' => 'procedure', 'copyHandler' => 'copiar_link_imagenes_cuadro'])
								</div>
								<div class="col-md-4 col-sm-12 col-12 hc4-procedure-images-panel hc4-procedure-summary-{{$value->id_protocolo}}">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
<div class="box-header with-border procedure-section-header" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
<div class="row procedure-section-header__grid">
<div class="col-3 procedure-section-header__action hc4-endoscopy-action-wrap hc4-content-edit">
										<button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_imagenes({{$value->id_protocolo}})">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i><span>Editar</span>
										</button>
									</div>
<div class="col-8 procedure-section-header__title" style="text-align: center">
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
							  					<div class="col-md-6 col-sm-4">
		                                            @php
		                                                $explotar = explode( '.', $imagen->nombre);
		                                                $extension = end($explotar);
		                                            @endphp
		                                            @if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'JPG') || ($extension == 'JPEG') || ($extension == 'PNG'))
		                                                <a data-toggle="modal" data-target="#foto2" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
		                                                    <img style="margin-bottom: 4px;"  src="{{asset('hc_ima')}}/{{$imagen->nombre}}" width="98%">
		                                                </a><br>
		                                            @elseif(($extension == 'pdf') || ($extension == 'PDF'))
		                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
		                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/pdf.png')}}" width="98%">
		                                                </a>
		                                            @elseif(($extension == 'mp4'))
		                                                <a data-toggle="modal" data-target="#video" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
		                                                    <img style="margin-bottom: 4px;"   src="{{asset('imagenes/video.png')}}" width="98%">
		                                                </a>
		                                            @else
		                                                @php
		                                                    $variable = explode('/' , asset('/hc_ima/'));
		                                                    $d1 = $variable[3];
		                                                    $d2 = $variable[4];
		                                                    $d3 = $variable[5];
		                                                 @endphp
		                                                <a data-toggle="modal" data-target="#foto" data-remote="{{ route('hc4_mostrar_foto_eliminar', ['id' => $imagen->id]) }}">
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
                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="Expandir o contraer"><i class="fa fa-plus"></i></button>
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
                                <button type="button" class="btn hc4-endoscopy-inline-action" onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');"><i class="fa fa-pencil-square-o"></i><span>Editar</span></button>
                                </div>
                                <div class="hc4-evolution-item-title">DETALLES DE LA EVOLUCIÓN</div>
                                <div class="hc4-evolution-item-spacer" aria-hidden="true"></div>
								  				</div>
								  				<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
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
						                                <button id="bagregar{{$value->id_procedimiento}}" class="btn btn_agregar_diag btn-sm"><span class="glyphicon glyphicon-plus"> Agregar</span></button>
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
						  	</div>
						</div>
					</div>
		    	</div>
	    	@endforeach
	    </div>
  	</div>
  </div>
  <!-- box-footer -->
</div>
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

	window.alternar_modo_imagenes_cuadro = function(control, id_protocolo) {
		var fila = $('#hc4_images_procedure_media_'+id_protocolo);
		fila.toggleClass('hc4-procedure-image-mode', control.checked === true);
	};

	window.copiar_link_imagenes_cuadro = function(control) {
		var enlace = $(control).closest('.hc4-procedure-link-row').find('.hc4-procedure-link-view').first();
		var url = $.trim(enlace.attr('href') || '');
		if (!url) { return; }

		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(url).catch(function() {
				copiar_link_imagenes_cuadro_compatibilidad(url);
			});
		} else {
			copiar_link_imagenes_cuadro_compatibilidad(url);
		}
	};

	function copiar_link_imagenes_cuadro_compatibilidad(url) {
		var temporal = $('<textarea readonly></textarea>').val(url).appendTo('body');
		temporal[0].select();
		document.execCommand('copy');
		temporal.remove();
	}

	function editar_imagenes22(id_protocolo){
		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('paciente.imagenes_protocolo')}}"+'/'+id_protocolo,
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
	window.hc4EditorImagenesActivo = null;

	window.cerrarEditorImagenes = function(id_protocolo) {
		if (typeof window.detenerEditorImagenesProcedimiento === 'function') {
			window.detenerEditorImagenesProcedimiento();
			window.detenerEditorImagenesProcedimiento = null;
		}

		$('#editor_imagenes_procedimiento'+id_protocolo).empty().hide();
		$('.hc4-procedure-summary-'+id_protocolo).show();
		var fila = $('#hc4_images_procedure_media_'+id_protocolo);
		if (fila.hasClass('hc4-procedure-image-mode')) {
			fila.find('.hc4-procedure-link-panel').hide();
		} else {
			fila.find('.hc4-procedure-images-panel').hide();
		}
		window.hc4EditorImagenesActivo = null;
	};

	window.editar_imagenes = function(id_protocolo) {
		var contenedor = $('#editor_imagenes_procedimiento'+id_protocolo);
		if (!contenedor.length) {
			alert('No se encontró el contenedor de imágenes del procedimiento.');
			return;
		}

		if (window.hc4EditorImagenesActivo !== null && Number(window.hc4EditorImagenesActivo) !== Number(id_protocolo)) {
			window.cerrarEditorImagenes(window.hc4EditorImagenesActivo);
		}

		window.hc4EditorImagenesActivo = id_protocolo;
		$('.hc4-procedure-summary-'+id_protocolo).hide();
		contenedor.show().html('<div class="text-center" style="padding:30px"><i class="fa fa-spinner fa-spin"></i> Cargando editor de imágenes...</div>');

		$.ajax({
			type: 'GET',
			url: "{{ url('inicio/procedimientos/endoscopicos/imagenes/editor') }}/"+encodeURIComponent(id_protocolo),
			dataType: 'html',
			success: function(datahtml) {
				contenedor.html(datahtml);
			},
			error: function(xhr) {
				window.cerrarEditorImagenes(id_protocolo);
				var mensaje = xhr.responseJSON && xhr.responseJSON.message
					? xhr.responseJSON.message
					: 'No se pudo cargar el editor de imágenes.';
				alert(mensaje);
			}
		});
	};
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
        	console.log(url_datos);
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

	window.editar_evolucion = function(id, id_paciente){
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
	};
	window.agregar_evolucion = function(id, id_evolucion){
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
	};

	function cargar_tabla(id){
        $.ajax({
                url:"{{route('epicrisis.cargar22')}}/"+id,
                dataType: "json",
                type: 'get',
                success: function(data){

                    var table = document.getElementById("tdiagnostico"+id);
					if (!table) {
						return;
					}

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


	@foreach($procedimientos2 as $value)

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
    @endforeach

    @foreach($procedimientos1 as $value)

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
    @endforeach

    function guardar_cie10_PRO(id_procedimiento, id_hc){
        //alert($("#pre_def").val());
        $.ajax({
            type: 'post',
            url:"{{route('procedimiento.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: { 'codigo': $("#codigo"+id_procedimiento).val(), 'pre_def': $("#pre_def"+id_procedimiento).val(),'hcid': id_hc, 'hc_id_procedimiento': id_procedimiento, 'in_eg': $("#ing_egr"+id_procedimiento).val(), 'id_paciente': @json($paciente->id) },
            success: function(data){


                var indexr = data.count-1
                var table = document.getElementById("tdiagnostico"+id_procedimiento);
				if (!table) {
					return;
				}
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
</script>
