<style type="text/css">
    .odont-lab-orders {
        overflow: hidden;
        margin: 0;
        border: 1px solid #c9dee8;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(0, 74, 117, .08);
    }
    .odont-lab-orders__header {
        padding: 14px 18px 16px;
        background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%);
        color: #fff;
    }
    .odont-lab-orders__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }
    .odont-lab-orders__title {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.25;
    }
    .odont-lab-orders__title-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        border: 1px solid rgba(255, 255, 255, .35);
        border-radius: 10px;
        background: rgba(255, 255, 255, .12);
    }
    .odont-lab-orders__actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }
    #area_trabajo .odont-lab-orders__action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 9px 14px;
        border: 1px solid rgba(255, 255, 255, .42);
        border-radius: 9px;
        background: rgba(255, 255, 255, .13);
        color: #fff;
        box-shadow: none;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }
    #area_trabajo .odont-lab-orders__action:hover,
    #area_trabajo .odont-lab-orders__action:focus {
        background: rgba(255, 255, 255, .24);
        color: #fff;
        outline: none;
    }
    .odont-lab-orders__patient {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 18px;
        margin-top: 14px;
        padding: 10px 14px;
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 9px;
        background: transparent;
        font-size: 14px;
    }
    .odont-lab-orders__patient-name {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .odont-lab-orders__age { white-space: nowrap; }
    .odont-lab-orders__body { padding: 16px; background: #f5fafc; }
    .odont-lab-orders .parent {
        height: 620px;
        margin: 0;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-color: #139ea8 #e3f0f4;
        scrollbar-width: thin;
    }
    .odont-lab-orders .parent::-webkit-scrollbar { width: 8px; }
    .odont-lab-orders .parent::-webkit-scrollbar-thumb { border-radius: 10px; background: #139ea8; }
    .odont-lab-orders .parent::-webkit-scrollbar-track { background: #e3f0f4; }
    .odont-lab-orders #listado { padding: 0 4px 0 0; }
    .odont-lab-orders .thead-dark > tr > th { padding: 5px; background-color: #07547e !important; }
    .odont-lab-orders .table-hover tbody tr:hover { background-color: #e8f8f8 !important; }

    @media (max-width: 900px) {
        .odont-lab-orders__top { align-items: stretch; flex-direction: column; }
        .odont-lab-orders__actions { justify-content: flex-start; flex-wrap: wrap; }
    }
    @media (max-width: 600px) {
        .odont-lab-orders__header { padding: 12px; }
        .odont-lab-orders__title { font-size: 16px; }
        .odont-lab-orders__actions { display: grid; grid-template-columns: 1fr; }
        #area_trabajo .odont-lab-orders__action { width: 100%; }
        .odont-lab-orders__patient { display: flex; align-items: flex-start; flex-direction: column; gap: 5px; }
        .odont-lab-orders__patient-name { white-space: normal; }
        .odont-lab-orders__body { padding: 10px; }
    }
</style>
@php
  $tipo_usuario  = Auth::user()->id_tipo_usuario;
@endphp
@php
    $xedad = '0';
    if ($paciente->fecha_nacimiento != null) {
        $xedad = Carbon\Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
    }
@endphp
<section class="odont-lab-orders">
    <input type="hidden" name="paciente_id" id="paciente_id" value="{{$paciente->id}}">
    <header class="odont-lab-orders__header">
        <div class="odont-lab-orders__top">
            <div class="odont-lab-orders__title">
                <span class="odont-lab-orders__title-icon"><i class="fa fa-flask" aria-hidden="true"></i></span>
                <span>@lang('hc4.historialOrdenesLaboratorio')</span>
            </div>
            <div class="odont-lab-orders__actions">
                @if($tipo_usuario == 1)
                    <button type="button" class="btn odont-lab-orders__action" onclick="agregar_lab_publico();">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        <span>@lang('hc4.agregarOrdenPublica')</span>
                    </button>
                @endif
                <button type="button" class="btn odont-lab-orders__action" onclick="agregar_orden_laboratorio();">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                    <span>@lang('hc4.agregarOrdenParticular')</span>
                </button>
            </div>
        </div>
        <div class="odont-lab-orders__patient">
            <div class="odont-lab-orders__patient-name">
                <b>@lang('hc4.paciente'):</b>
                {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
                {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
            </div>
            <div class="odont-lab-orders__age"><b>@lang('hc4.edad'):</b> {{$xedad}} @lang('hc4.anios-')</div>
        </div>
    </header>
    <div class="odont-lab-orders__body">
        <div class="parent">
            <div id="listado"></div>
        </div>
    </div>
</section>

<script type="text/javascript">
    agregar_orden_listado();
    //Funcion para crear una Orden de Procedimiento Endoscopico
    function agregar_orden_listado(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('odontologia.ordenes.laboratorio.lista',['id_paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }

    function agregar_orden_laboratorio(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('odontologia.ordenes.laboratorio.crear',['id_paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }

    function agregar_orden_as400(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('as400.index_hc4')}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }

    {{--function agregar_orden_publica(){
    	$.ajax({
			async: true,
			type: "GET",
			url: "{{route('laboratorio.orden.publica',['paciente' => $paciente->id ])}}",
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);

			},
			error:  function(){
				alert('error al cargar');
			}
		});
    }--}}




    //Descarga Orden de Procedimiento Endoscopico
	function descargar(id_or){//
       window.open('{{url('cotizador_p/orden/imprimir')}}/'+id_or,'_blank');
    }

	function agregar_lab_publico(){
		var id_paciente = $('#paciente_id').val();
		$.ajax({
			async: true,
			type: "GET",
			url: "{{route('laboratorio.ordenIess.crear',['id' => $paciente ])}}",
			
			data: "",
			datatype: "html",
			success: function(datahtml){
			    $("#listado").html(datahtml);
			},
			error:  function(){
				alert('error al cargar');
			}
		});
	}

	function editar_labs_publico(){
		var id_paciente = $('#paciente_id').val();
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/paciente/laboratorio/detalle/solic/editar/pub/')}}/"+id_paciente,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado").html(datahtml);
				
			},
			error:  function(){
				alert('error al cargar');
			}
		});
	}
	function listar_labs_publico(id){
		//alert("editar");
		$.ajax({
			async: true,
			type: "GET",
			url: "{{url('hospital/emergencia/paciente/laboratorio/listar/examenes/publicos/')}}/"+id,
			data: "",
			datatype: "html",
			success: function(datahtml){

			    $("#listado"+id).html(datahtml);
				
			},
			error:  function(){
				alert('error al cargar');
			}
		});
	}
</script>
