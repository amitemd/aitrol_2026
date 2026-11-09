
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

	/* Vista que abre el cuadro IMÁGENES del historial clínico. */
	#area_trabajo .hc4-ecography-view .hc4-ecography-record-header {
		display:grid !important;
		grid-template-columns:minmax(0,1fr) 48px;
		align-items:center;
		gap:14px;
		min-height:92px;
		padding:14px 16px 14px 22px !important;
		color:#fff !important;
		background:linear-gradient(100deg,#075779 0%,#057ca7 54%,#12aaa7 100%) !important;
		border-bottom:0 !important;
	}

	#area_trabajo .hc4-ecography-record-header::before,
	#area_trabajo .hc4-ecography-record-header::after,
	#area_trabajo .hc4-ecography-summary::before,
	#area_trabajo .hc4-ecography-summary::after,
	#area_trabajo .hc4-ecography-content-grid::before,
	#area_trabajo .hc4-ecography-evolution-header::before,
	#area_trabajo .hc4-ecography-evolution-header::after,
	#area_trabajo .hc4-ecography-evolution-item-header::before,
	#area_trabajo .hc4-ecography-evolution-item-header::after {
		display:none !important;
		content:none !important;
	}

	#area_trabajo .hc4-ecography-summary {
		display:grid !important;
		grid-template-columns:minmax(260px,.9fr) minmax(360px,1.55fr) minmax(220px,.75fr);
		align-items:center;
		gap:28px;
		width:100%;
		margin:0 !important;
	}

	#area_trabajo .hc4-ecography-summary > [class*="col-"] {
		float:none !important;
		width:auto !important;
		max-width:none !important;
		min-width:0;
		margin:0 !important;
		padding:0 !important;
		color:#fff !important;
		line-height:1.45;
	}

	#area_trabajo .hc4-ecography-summary__date,
	#area_trabajo .hc4-ecography-summary__doctor {
		white-space:nowrap;
	}

	#area_trabajo .hc4-ecography-summary__procedure {
		white-space:normal;
		overflow-wrap:anywhere;
	}

	#area_trabajo .hc4-ecography-record-tools {
		position:static !important;
		float:none !important;
		justify-self:end;
		margin:0 !important;
	}

	#area_trabajo .hc4-ecography-record-tools .btn,
	#area_trabajo .hc4-ecography-evolution-tools .btn {
		display:inline-flex;
		align-items:center;
		justify-content:center;
		width:42px;
		height:42px;
		margin:0 !important;
		padding:0 !important;
		border:1px solid rgba(255,255,255,.38) !important;
		border-radius:9px !important;
		color:#fff !important;
		background:rgba(255,255,255,.13) !important;
	}

	#area_trabajo .hc4-ecography-content-header,
	#area_trabajo .hc4-ecography-evolution-item-header {
		min-height:62px;
		padding:9px 12px !important;
		color:#fff !important;
		background:linear-gradient(100deg,#075779 0%,#057ca7 54%,#12aaa7 100%) !important;
		border-bottom:0 !important;
	}

	#area_trabajo .hc4-ecography-content-grid,
	#area_trabajo .hc4-ecography-evolution-item-header {
		display:grid !important;
		align-items:center;
		gap:12px;
		width:100%;
		margin:0 !important;
	}

	#area_trabajo .hc4-ecography-content-grid {
		grid-template-columns:minmax(96px,112px) minmax(0,1fr) minmax(96px,112px);
	}

	#area_trabajo .hc4-ecography-evolution-item-header {
		grid-template-columns:minmax(154px,170px) minmax(0,1fr) minmax(154px,170px);
	}

	#area_trabajo .hc4-ecography-content-action,
	#area_trabajo .hc4-ecography-evolution-edit {
		grid-column:1;
		justify-self:start;
	}

	#area_trabajo .hc4-ecography-content-title,
	#area_trabajo .hc4-ecography-evolution-item-title {
		grid-column:2;
		min-width:0;
		color:#fff !important;
		font-size:14px;
		font-weight:600;
		line-height:1.25;
		text-align:center !important;
		white-space:normal;
	}

	#area_trabajo .hc4-ecography-content-header--images .hc4-ecography-content-grid {
		grid-template-columns:minmax(96px,112px) minmax(0,1fr);
	}

	#area_trabajo .hc4-ecography-content-header--images .hc4-ecography-content-title {
		grid-column:2;
		justify-self:end;
		max-width:100%;
		text-align:right !important;
		white-space:nowrap;
	}

	#area_trabajo .hc4-ecography-edit-button,
	#area_trabajo .hc4-ecography-evolution-add {
		display:inline-flex !important;
		align-items:center;
		justify-content:center;
		gap:6px;
		min-height:40px;
		margin:0 !important;
		padding:8px 11px !important;
		border:1px solid rgba(255,255,255,.38) !important;
		border-radius:9px !important;
		color:#fff !important;
		background:rgba(255,255,255,.13) !important;
		line-height:1;
		white-space:nowrap;
	}

	#area_trabajo button.hc4-ecography-edit-button {
		position:static !important;
		inset:auto !important;
		float:none !important;
		box-sizing:border-box !important;
		width:96px !important;
		min-width:96px !important;
		height:40px !important;
		min-height:40px !important;
		font-family:'Helvetica general3',Arial,sans-serif !important;
		font-size:14px !important;
		font-weight:500 !important;
		text-transform:none !important;
		box-shadow:none !important;
		appearance:none;
	}

	#area_trabajo .hc4-ecography-edit-button .hc4-ecography-edit-icon {
		display:block !important;
		position:static !important;
		flex:0 0 17px;
		width:17px !important;
		height:17px !important;
		margin:0 !important;
		padding:0 !important;
		color:inherit !important;
		fill:none;
		stroke:currentColor;
		stroke-width:2;
		stroke-linecap:round;
		stroke-linejoin:round;
		transform:none !important;
	}

	#area_trabajo .hc4-ecography-edit-button span {
		display:block !important;
		position:static !important;
		margin:0 !important;
		padding:0 !important;
		color:inherit !important;
		font:inherit !important;
		line-height:1 !important;
		transform:none !important;
	}

	#area_trabajo .hc4-ecography-evolution-header {
		display:grid !important;
		grid-template-columns:minmax(170px,210px) minmax(0,1fr) 48px;
		align-items:center;
		gap:12px;
		min-height:70px;
		padding:10px 14px !important;
		color:#fff !important;
		background:linear-gradient(100deg,#075779 0%,#057ca7 54%,#12aaa7 100%) !important;
		border-bottom:0 !important;
	}

	#area_trabajo .hc4-ecography-evolution-add {
		justify-self:start;
		font-size:12px;
		font-weight:700;
	}

	#area_trabajo .hc4-ecography-evolution-add img {
		width:20px;
		height:20px;
		object-fit:contain;
	}

	#area_trabajo .hc4-ecography-evolution-title {
		min-width:0;
		color:#fff !important;
		font-size:15px;
		font-weight:600;
		line-height:1.25;
		text-align:center;
	}

	#area_trabajo .hc4-ecography-evolution-tools {
		position:static !important;
		float:none !important;
		justify-self:end;
		margin:0 !important;
	}

	#area_trabajo .hc4-ecography-evolution-item-tools {
		display:flex !important;
		align-items:center;
		justify-content:flex-end;
		gap:8px;
		grid-column:3;
		justify-self:end;
		white-space:nowrap;
	}

	#area_trabajo .hc4-ecography-evolution-delete {
		display:inline-flex !important;
		position:static !important;
		align-items:center;
		justify-content:center;
		gap:6px;
		width:96px !important;
		min-width:96px !important;
		height:42px !important;
		margin:0 !important;
		padding:0 10px !important;
		border:1px solid rgba(255,255,255,.42) !important;
		border-radius:9px !important;
		color:#fff !important;
		background:#c74655 !important;
		font-family:'Helvetica general3',Arial,sans-serif !important;
		font-size:13px !important;
		font-weight:500 !important;
		line-height:1 !important;
		box-shadow:none !important;
	}

	#area_trabajo .hc4-ecography-evolution-delete svg {
		display:block !important;
		position:static !important;
		flex:0 0 17px;
		width:17px !important;
		height:17px !important;
		margin:0 !important;
		fill:none;
		stroke:currentColor;
		stroke-width:2;
		stroke-linecap:round;
		stroke-linejoin:round;
	}

	#area_trabajo .hc4-ecography-evolution-delete span {
		display:block !important;
		position:static !important;
		margin:0 !important;
		padding:0 !important;
		color:inherit !important;
		font:inherit !important;
		line-height:1 !important;
	}

	#area_trabajo .hc4-ecography-evolution-item-toggle {
		display:inline-flex !important;
		position:static !important;
		align-items:center;
		justify-content:center;
		width:42px !important;
		height:42px !important;
		margin:0 !important;
		padding:0 !important;
		border:1px solid rgba(255,255,255,.38) !important;
		border-radius:9px !important;
		color:#fff !important;
		background:rgba(255,255,255,.13) !important;
		font-family:Arial,sans-serif !important;
		font-size:24px !important;
		font-weight:700 !important;
		line-height:1 !important;
		box-shadow:none !important;
	}

	#area_trabajo .hc4-ecography-evolution-item-toggle span {
		display:block !important;
		position:static !important;
		margin:0 !important;
		padding:0 !important;
		line-height:1 !important;
		transform:none !important;
	}

	#area_trabajo .hc4-ecography-evolution-body[hidden] {
		display:none !important;
	}

	#area_trabajo .hc4-ecography-detail-panel .hc4-ecography-content-grid {
		grid-template-columns:minmax(96px,112px) minmax(0,1fr) minmax(245px,285px) !important;
	}

	#area_trabajo .hc4-ecography-content-mode {
		grid-column:3;
		justify-self:end;
	}

	#area_trabajo .hc4-ecography-mode-switch {
		display:inline-flex;
		align-items:center;
		justify-content:flex-end;
		gap:7px;
		margin:0;
		color:#fff;
		cursor:pointer;
		font-size:10px;
		font-weight:700;
		white-space:nowrap;
	}

	#area_trabajo .hc4-ecography-mode-switch input {
		position:absolute;
		width:1px;
		height:1px;
		opacity:0;
		pointer-events:none;
	}

	#area_trabajo .hc4-ecography-mode-switch__track {
		position:relative;
		display:inline-block;
		flex:0 0 42px;
		width:42px;
		height:23px;
		border:1px solid rgba(255,255,255,.58);
		border-radius:20px;
		background:rgba(255,255,255,.2);
		transition:background .2s ease;
	}

	#area_trabajo .hc4-ecography-mode-switch__track::after {
		position:absolute;
		top:3px;
		left:3px;
		width:15px;
		height:15px;
		border-radius:50%;
		background:#fff;
		box-shadow:0 2px 5px rgba(0,55,78,.3);
		content:'';
		transition:transform .2s ease;
	}

	#area_trabajo .hc4-ecography-mode-switch input:checked + .hc4-ecography-mode-switch__track {
		background:#21c7ae;
	}

	#area_trabajo .hc4-ecography-mode-switch input:checked + .hc4-ecography-mode-switch__track::after {
		transform:translateX(19px);
	}

	#area_trabajo .hc4-ecography-media-row {
		display:flex !important;
		flex-wrap:wrap;
	}

	#area_trabajo .hc4-ecography-detail-panel {
		flex:0 0 100% !important;
		max-width:100% !important;
		width:100% !important;
		padding:0 10px !important;
	}

	#area_trabajo .hc4-ecography-images-panel { display:none !important; }

	#area_trabajo .hc4-ecography-media-row.hc4-ecography-editor-mode > .hc4-ecography-detail-panel,
	#area_trabajo .hc4-ecography-media-row.hc4-ecography-editor-mode > .hc4-ecography-link-panel,
	#area_trabajo .hc4-ecography-media-row.hc4-ecography-editor-mode > .hc4-ecography-images-panel {
		display:none !important;
	}

	#area_trabajo .hc4-ecography-media-row.hc4-ecography-image-mode > .hc4-ecography-detail-panel {
		flex:0 0 66.666667% !important;
		max-width:66.666667% !important;
		width:66.666667% !important;
		padding-right:5px !important;
	}

	#area_trabajo .hc4-ecography-media-row.hc4-ecography-image-mode > .hc4-ecography-images-panel {
		display:block !important;
		flex:0 0 33.333333% !important;
		max-width:33.333333% !important;
		width:33.333333% !important;
		padding:0 10px 0 5px !important;
	}

	#area_trabajo .hc4-ecography-media-row.hc4-ecography-image-mode > .hc4-ecography-link-panel,
	#area_trabajo .hc4-ecography-media-row.hc4-ecography-image-mode .hc4-procedure-link-edit-section {
		display:none !important;
	}

	#area_trabajo .hc4-ecography-link-panel {
		flex:0 0 100% !important;
		max-width:100% !important;
		width:100%;
		padding:12px 10px 0 !important;
	}

	#area_trabajo .hc4-ecography-link-card {
		padding:13px 14px;
		border:1px solid #c9e0e8;
		border-radius:9px;
		background:#f6fbfd;
	}

	#area_trabajo .hc4-ecography-link-label {
		display:block;
		margin:0 0 7px;
		color:#315d73;
		font-size:11px;
		font-weight:700;
		text-transform:uppercase;
	}

	#area_trabajo .hc4-ecography-link-controls {
		display:flex;
		align-items:center;
		gap:7px;
	}

	#area_trabajo .hc4-ecography-link-view {
		flex:1 1 auto;
		min-width:0;
		color:#087fa7;
		font-size:12px;
		overflow-wrap:anywhere;
		text-decoration:underline;
	}

	#area_trabajo .hc4-ecography-link-empty {
		flex:1 1 auto;
		color:#78909e;
		font-size:12px;
	}

	#area_trabajo .hc4-ecography-link-copy {
		display:inline-flex;
		flex:0 0 32px;
		align-items:center;
		justify-content:center;
		width:32px;
		height:32px;
		padding:0;
		border:1px solid #0b8eaf;
		border-radius:5px;
		color:#16778e;
		background:#fff;
	}

	@media (max-width: 991px) {
		#area_trabajo .hc4-ecography-summary {
			grid-template-columns:minmax(0,1fr);
			gap:8px;
		}
		#area_trabajo .hc4-ecography-summary__date,
		#area_trabajo .hc4-ecography-summary__doctor { white-space:normal; }

		#area_trabajo .hc4-ecography-media-row.hc4-ecography-image-mode > .hc4-ecography-detail-panel,
		#area_trabajo .hc4-ecography-media-row.hc4-ecography-image-mode > .hc4-ecography-images-panel {
			flex:0 0 100% !important;
			max-width:100% !important;
			width:100% !important;
			padding:0 10px 10px !important;
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
<div class="box hc4-ecography-view" style="border: 2px solid #004AC1; background-color: white; ">
  @include('hc4.partials.module_header', [
      'moduleTitle' => __('hc4.imagenes-'),
      'moduleIcon' => asset('/').'hc4/img/iconos/pendo.png',
      'paciente' => $paciente,
      'actionLabel' => 'AGREGAR PROCEDIMIENTO DE IMAGEN',
      'actionOnClick' => 'agregar_procedimiento_ecografia();'
  ])
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
					<div class="box @if($fecha_r != date('Y-m-d') ) collapsed-box @endif" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 0px; ">
						<div class="box-header with-border hc4-ecography-record-header" style="background-color: white; color: black; font-family: 'Helvetica general3';border-bottom: #004AC1;">

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
							<div class="row hc4-ecography-summary">
								<div class="col-4 hc4-ecography-summary__date">
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
								<div class="col-4 hc4-ecography-summary__procedure">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
										<span style="font-size: 12px">@if(!is_null($texto)) {{$texto}} @endif </span>
									</div>
								</div>
								<div class="col-3 hc4-ecography-summary__doctor">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
										<span style="font-size: 12px">@if(!is_null($value->nombre1)) {{$value->nombre1}} {{$value->apellido1}} @endif</span>
									</div>
								</div>
			               	</div>
	<div class="pull-right box-tools hc4-ecography-record-tools">
		                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
		                            <i class="fa fa-plus"></i>
		                        </button>
		               		</div>
						    <!-- /.box-tools -->
						</div>
						<div class="box-body" style="background: white;">
                            <div class="row hc4-ecography-media-row" id="hc4_ecography_media_{{$value->id_protocolo}}">
                                <div class="col-md-12 col-sm-12 col-12 hc4-ecography-detail-panel" style="margin-bottom: 5px">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">

	<div class="box-header with-border hc4-ecography-content-header" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
	<div class="row hc4-ecography-content-grid">
	<div class="hc4-ecography-content-action">
												<button type="button" class="hc4-ecography-edit-button" onclick="editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');">
													<svg class="hc4-ecography-edit-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg><span>Editar</span>
												</button>
	</div>
	<div class="hc4-ecography-content-title">Detalles del procedimiento</div>
	<div class="hc4-ecography-content-mode">
		<label class="hc4-ecography-mode-switch" title="Alternar entre enlace e imágenes capturadas">
			<span>Enlazar imágenes</span>
			<input type="checkbox" onchange="alternar_modo_imagenes_ecografia(this, {{$value->id_protocolo}})">
			<span class="hc4-ecography-mode-switch__track" aria-hidden="true"></span>
			<span>Capturar imágenes</span>
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
								<div class="col-12 hc4-ecography-link-panel">
									@include('hc4.partials.procedure_image_links_view', ['variant' => 'ecography', 'copyHandler' => 'copiar_link_imagenes_ecografia'])
								</div>
                                <div class="col-md-4 col-sm-12 col-12 hc4-ecography-images-panel">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
	<div class="box-header with-border hc4-ecography-content-header hc4-ecography-content-header--images" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
	<div class="row hc4-ecography-content-grid">
	<div class="hc4-ecography-content-action">
											<button type="button" class="hc4-ecography-edit-button" onclick="editar_imagenes({{$value->id_protocolo}})">
												<svg class="hc4-ecography-edit-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg><span>Editar</span>
											</button>
	</div>
	<div class="hc4-ecography-content-title">Imágenes del procedimiento</div>
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
								<div class="col-12 hc4-images-inline-editor hc4-ecography-inline-editor" id="editor_imagenes_procedimiento{{$value->id_protocolo}}" style="display: none;"></div>
								<div class="col-12" style="height: 5px;"></div>
						  		<div class="col-12" style="padding-top: 15px">
	<div class="box box-primary " style=" border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
	<div class="box-header with-border hc4-ecography-evolution-header">
										<button type="button" class="hc4-ecography-evolution-add" onclick="agregar_evolucion({{$value->id_procedimiento}});">
											<img src="{{asset('/')}}hc4/img/iconos/agregar.png" alt=""><span>AGREGAR EVOLUCIÓN</span>
										</button>
										<div class="hc4-ecography-evolution-title">HISTORIAL DE EVOLUCIONES DEL PROCEDIMIENTO</div>
									    <div class="box-tools hc4-ecography-evolution-tools">
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
	<div class="box-header with-border hc4-ecography-evolution-item-header">
												<button type="button" class="hc4-ecography-edit-button hc4-ecography-evolution-edit" onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');">
													<svg class="hc4-ecography-edit-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg><span>Editar</span>
												</button>
												<div class="hc4-ecography-evolution-item-title">DETALLES DE LA EVOLUCIÓN</div>
												<div class="hc4-ecography-evolution-item-tools">
													<button type="button" class="hc4-ecography-evolution-delete" title="Eliminar evolución" onclick="eliminar_evolucion_imagenes(this, {{$evolucion->id}});">
														<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path><path d="M10 11v5M14 11v5"></path></svg><span>Eliminar</span>
													</button>
													<button type="button" class="hc4-ecography-evolution-item-toggle" aria-expanded="true" title="Colapsar detalle" onclick="alternar_detalle_evolucion(this);">
														<span aria-hidden="true">&minus;</span>
													</button>
												</div>
	</div>
	<div class="box-body hc4-ecography-evolution-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
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
					editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');
				</script>
				@endif
	    	@endforeach

	    	@foreach($procedimientos1 as $value)
		    	<div class="col-12">
	<div class="box collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
						<div class="box-header with-border hc4-ecography-record-header" style="background-color: white; color: black; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;">
	<div class="row hc4-ecography-summary">
	<div class="col-4 hc4-ecography-summary__date">
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
			                    <div class="col-4 hc4-ecography-summary__procedure">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Procedimiento:</span>
										<span style="font-size: 12px"> {{$value->nombre}} </span>
									</div>
								</div>
								<div class="col-3 hc4-ecography-summary__doctor">
									<div>
										<span style="font-family: 'Helvetica general'; font-size: 12px">Dr (a) </span>
										<span style="font-size: 12px"> {{$value->nombre1}} {{$value->apellido1}} </span>
									</div>
								</div>
						    </div>
	<div class="pull-right box-tools hc4-ecography-record-tools">
	                        	<button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
	                            	<i class="fa fa-plus"></i>
	                            </button>
	                    	</div>
						    <!-- /.box-tools -->
						</div>
						<div class="box-body" style="background: white;">
                            <div class="row hc4-ecography-media-row" id="hc4_ecography_media_{{$value->id_protocolo}}">
                                <div class="col-md-12 col-sm-12 col-12 hc4-ecography-detail-panel" style="margin-bottom: 5px">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
	<div class="box-header with-border hc4-ecography-content-header" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
	<div class="row hc4-ecography-content-grid">
	<div class="hc4-ecography-content-action">
												<button type="button" class="hc4-ecography-edit-button" onclick="editarprocedimiento({{$value->id_procedimiento}}, '{{$paciente->id}}');">
													<svg class="hc4-ecography-edit-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg><span>Editar</span>
												</button>
	</div>
	<div class="hc4-ecography-content-title">Detalles del procedimiento</div>
	<div class="hc4-ecography-content-mode">
		<label class="hc4-ecography-mode-switch" title="Alternar entre enlace e imágenes capturadas">
			<span>Enlazar imágenes</span>
			<input type="checkbox" onchange="alternar_modo_imagenes_ecografia(this, {{$value->id_protocolo}})">
			<span class="hc4-ecography-mode-switch__track" aria-hidden="true"></span>
			<span>Capturar imágenes</span>
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
								<div class="col-12 hc4-ecography-link-panel">
									@include('hc4.partials.procedure_image_links_view', ['variant' => 'ecography', 'copyHandler' => 'copiar_link_imagenes_ecografia'])
								</div>
                                <div class="col-md-4 col-sm-12 col-12 hc4-ecography-images-panel">
						  			<div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
	<div class="box-header with-border hc4-ecography-content-header hc4-ecography-content-header--images" style="background-color: #004AC1; color: white; text-align: center; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
	<div class="row hc4-ecography-content-grid">
	<div class="hc4-ecography-content-action">
											<button type="button" class="hc4-ecography-edit-button" onclick="editar_imagenes({{$value->id_protocolo}})">
												<svg class="hc4-ecography-edit-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg><span>Editar</span>
											</button>
	</div>
	<div class="hc4-ecography-content-title">Imágenes del procedimiento</div>
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
								<div class="col-12 hc4-images-inline-editor hc4-ecography-inline-editor" id="editor_imagenes_procedimiento{{$value->id_protocolo}}" style="display: none;"></div>
								<div class="col-12" style="padding-top: 15px">
	<div class="box box-primary collapsed-box" style="border: 2px solid #004AC1; background-color: #004AC1; border-radius: 3px; ">
	<div class="box-header with-border hc4-ecography-evolution-header">
										<button type="button" class="hc4-ecography-evolution-add" onclick="agregar_evolucion({{$value->id_procedimiento}});">
											<img src="{{asset('/')}}hc4/img/iconos/agregar.png" alt=""><span>AGREGAR EVOLUCIÓN</span>
										</button>
										<div class="hc4-ecography-evolution-title">HISTORIAL DE EVOLUCIONES DEL PROCEDIMIENTO</div>
									    <div class="box-tools hc4-ecography-evolution-tools">
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
	<div class="box-header with-border hc4-ecography-evolution-item-header">
												<button type="button" class="hc4-ecography-edit-button hc4-ecography-evolution-edit" onclick="editar_evolucion({{$evolucion->id}}, '{{$paciente->id}}');">
													<svg class="hc4-ecography-edit-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg><span>Editar</span>
												</button>
												<div class="hc4-ecography-evolution-item-title">DETALLES DE LA EVOLUCIÓN</div>
												<div class="hc4-ecography-evolution-item-tools">
													<button type="button" class="hc4-ecography-evolution-delete" title="Eliminar evolución" onclick="eliminar_evolucion_imagenes(this, {{$evolucion->id}});">
														<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path><path d="M10 11v5M14 11v5"></path></svg><span>Eliminar</span>
													</button>
													<button type="button" class="hc4-ecography-evolution-item-toggle" aria-expanded="true" title="Colapsar detalle" onclick="alternar_detalle_evolucion(this);">
														<span aria-hidden="true">&minus;</span>
													</button>
												</div>
	</div>
	<div class="box-body hc4-ecography-evolution-body" style="font-size: 11px;font-family: 'Helvetica general3';" id="evolucion{{$evolucion->id}}">
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
	function alternar_modo_imagenes_ecografia(control, id_protocolo) {
		$('#hc4_ecography_media_'+id_protocolo).toggleClass('hc4-ecography-image-mode', control.checked === true);
	}

	function copiar_link_imagenes_ecografia(control) {
		var url = $.trim($(control).siblings('.hc4-ecography-link-view').attr('href') || '');
		if (!url) { return; }
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(url).catch(function(){ copiar_link_imagenes_ecografia_compatibilidad(url); });
		} else {
			copiar_link_imagenes_ecografia_compatibilidad(url);
		}
	}

	function copiar_link_imagenes_ecografia_compatibilidad(url) {
		var temporal = $('<textarea readonly></textarea>').val(url).appendTo('body');
		temporal[0].select();
		document.execCommand('copy');
		temporal.remove();
	}
	var hc4EditorImagenesEcografiaActivo = null;

	function cerrarEditorImagenes(id_protocolo) {
		if (typeof window.detenerEditorImagenesProcedimiento === 'function') {
			window.detenerEditorImagenesProcedimiento();
			window.detenerEditorImagenesProcedimiento = null;
		}

		var fila = $('#hc4_ecography_media_'+id_protocolo);
		var contenedor = $('#editor_imagenes_procedimiento'+id_protocolo);
		contenedor.empty().hide();
		fila.removeClass('hc4-ecography-editor-mode');
		fila.children('.hc4-ecography-detail-panel').show();

		if (fila.hasClass('hc4-ecography-image-mode')) {
			fila.children('.hc4-ecography-images-panel').show();
			fila.children('.hc4-ecography-link-panel').hide();
		} else {
			fila.children('.hc4-ecography-images-panel').hide();
			fila.children('.hc4-ecography-link-panel').show();
		}

		hc4EditorImagenesEcografiaActivo = null;
	}

	function editar_imagenes(id_protocolo) {
		var fila = $('#hc4_ecography_media_'+id_protocolo);
		var contenedor = $('#editor_imagenes_procedimiento'+id_protocolo);
		if (!fila.length || !contenedor.length) {
			alert('No se encontró el contenedor de imágenes del procedimiento.');
			return;
		}

		if (hc4EditorImagenesEcografiaActivo !== null && hc4EditorImagenesEcografiaActivo !== id_protocolo) {
			cerrarEditorImagenes(hc4EditorImagenesEcografiaActivo);
		}

		if (typeof window.detenerEditorImagenesProcedimiento === 'function') {
			window.detenerEditorImagenesProcedimiento();
			window.detenerEditorImagenesProcedimiento = null;
		}

		hc4EditorImagenesEcografiaActivo = id_protocolo;
		fila.addClass('hc4-ecography-editor-mode');
		fila.children('.hc4-ecography-detail-panel, .hc4-ecography-link-panel, .hc4-ecography-images-panel').hide();
		contenedor.show().html('<div class="text-center" style="padding:30px"><i class="fa fa-spinner fa-spin"></i> Cargando editor de imágenes...</div>');

		$.ajax({
			type: 'GET',
			url: "{{ url('inicio/procedimientos/endoscopicos/imagenes/editor') }}/"+encodeURIComponent(id_protocolo),
			dataType: 'html',
			success: function(datahtml) {
				contenedor.html(datahtml);
			},
			error: function(xhr) {
				cerrarEditorImagenes(id_protocolo);
				var mensaje = 'No se pudo cargar el editor de imágenes.';
				if (xhr.responseJSON && xhr.responseJSON.message) {
					mensaje = xhr.responseJSON.message;
				}
				alert(mensaje);
			}
		});
	}
    function agregar_procedimiento_ecografia(){
    	contador = parseInt($('#contador_endoscopicos').val());
    	hc_endoscopico = parseInt($('#id_endoscopico').val());
    	hc_funcional = parseInt($('#id_funcional').val());
    	var url_datos = "";
    	if (hc_endoscopico >0) {
        	url_datos = "{{asset('/procedimiento2/selecciona2/')}}/2/{{$paciente->id}}/"+hc_endoscopico;
        	agregar_procedimiento_tipo(url_datos);
        	console.log(url_datos);
        }else if (hc_funcional >0) {
        	//alert(hc_funcional);
        	url_datos = "{{asset('/procedimiento2/selecciona2/')}}/2/{{$paciente->id}}/"+hc_funcional;
        	agregar_procedimiento_tipo(url_datos);
        }else{
          $.ajax({
				async: true,
				type: "GET",
				url: "{{route('hc4_procedimiento.selecciona_procedimiento',['tipo' => '2', 'paciente' => $paciente->id ])}}",
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
			url: "{{route('editar.procedimiento_ecografia2')}}/"+id,
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
		id_paciente = id_paciente || '{{$paciente->id}}';
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
	function eliminar_evolucion_imagenes(boton, id_evolucion){
		if(!confirm('¿Está seguro que desea eliminar la evolución?')){
			return;
		}

		$.ajax({
			type: 'DELETE',
			url: "{{ url('inicio/procedimientos/evolucion/eliminar') }}/"+encodeURIComponent(id_evolucion),
			data: {'_token': $('meta[name="csrf-token"]').attr('content')},
			dataType: 'json',
			success: function(){
				var tarjeta = boton.closest('.box');
				if(tarjeta){
					tarjeta.remove();
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
	function alternar_detalle_evolucion(boton){
		var tarjeta = boton.closest('.box');
		var detalle = tarjeta ? tarjeta.querySelector('.hc4-ecography-evolution-body') : null;
		var simbolo = boton.querySelector('span');

		if (!detalle || !simbolo) {
			return;
		}

		var estaExpandido = boton.getAttribute('aria-expanded') === 'true';
		detalle.hidden = estaExpandido;
		boton.setAttribute('aria-expanded', estaExpandido ? 'false' : 'true');
		boton.setAttribute('title', estaExpandido ? 'Expandir detalle' : 'Colapsar detalle');
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
</script>
