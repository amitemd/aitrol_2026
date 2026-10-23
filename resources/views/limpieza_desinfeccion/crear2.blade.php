<style>
	.autocomplete {
    z-index:999999 !important;
    z-index:999999999 !important;
    z-index:99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
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
  }
  .ui-autocomplete {
    z-index: 5000;
  }
  .ui-autocomplete {
    z-index: 999999;
    list-style:none;
    background-color:#FFFFFF;
    width:300px;
    border:solid 1px #EEE;
    border-radius:5px;
    padding-left:10px;
    line-height:2em;
</style>
<div class="modal-content" style="width: 100%;">
  <div class="modal-body">
  	<div class="box-body">  		

        <form class="form-horizontal" id="form">
            {{ csrf_field() }}
            <div class="form-group col-md-6 col-xs-6">
	            <label for="nom_limp" class="col-md-3 control-label" style="font-size:12px;">Limpieza</label>
	            <input type="hidden" name="id_sala" value="{{$id_sala}}">
	                <div class="col-md-9">
	                	<select id="nom_limp" name="nom_limp" class="form-control input-sm" >
		            		<option>Seleccione...</option>
		            		<option value="1">Inicial</option>
		            		<option value="2">Final</option>
	            		</select>
	                </div>

          	</div>
          	
          	<div class="form-group col-md-6 col-xs-6">
	            <label for="tipo_desinfeccion" class="col-md-3 control-label" style="font-size:12px;">Tipo de Desinfección</label>
	            <div class="col-md-9">
	            	<select id="tipo_desinfeccion" name="tipo_desinfeccion" class="form-control input-sm" >
	            		<option>Seleccione...</option>
	            		<option value="1">Concurrente</option>
	            		<option value="2">Terminal</option>
	            	</select>
	              
	            </div>  
          	</div>
          	<div class="form-group col-md-6 col-xs-6">
	            <label for="nom_detergente" class="col-md-3 control-label" style="font-size:12px;">Nombre del Detergente / Desinfectante</label>
	            <div class="col-md-9">
	              <input type="text" name="nom_detergente" class="form-control input-sm">
	            </div>  
          	</div>
          	<div class="form-group col-md-6 col-xs-6">
	            <label for="nom_toallas" class="col-md-3 control-label" style="font-size:12px;">Nombre de Toallitas desinfectantes</label>
	            <div class="col-md-9">
	              <input type="text" name="nom_toallas" class="form-control input-sm">
	            </div>  
          	</div>
          	<div class="form-group col-md-6 col-xs-6">
	            <label for="anestesiologia" class="col-md-3 control-label" style="font-size:12px;">Anestesiologia - Maquina de Anestesia</label>
	            <div class="col-md-9">
	              <select id="anestesiologia" name="anestesiologia" class="form-control input-sm" >
	              	<option value="">Seleccione...</option>
	            		<option value="1">Limpieza</option>
	            		<option value="2">Desinfeccion</option>
	            		<option value="3">Limpieza y Desinfeccion</option>
	            	</select>
	            </div>  
          	</div>
          	<div class="form-group col-md-6 col-xs-6">
	            <label for="responsable" class="col-md-3 control-label" style="font-size:12px;">Responsable</label>
	            <div class="col-md-9">
	                <select id="responsable_anest" name="responsable_anest" class="form-control input-sm" >
	                	<option value="">Seleccione...</option>
	              		@foreach($anestesiologos as $value)
	            		<option value="{{$value->id}}">{{$value->nombre1}} {{$value->apellido1}}</option>
	            		@endforeach
		            </select>
	            </div>  
          	</div>
          	<div class="form-group col-md-12 col-xs-6">
          		<div class="form-group col-md-12 col-xs-6">
	            	<label for="enfermeria" class="control-label" style="font-size:12px;">Enfermeria</label>
	        	</div>
	            <div class="col-md-4">
	            	<label for="camilla" class="col-md-3 control-label" style="font-size:12px;">Camilla</label>
	            	<div class="col-md-9">
		              	<select id="camilla" name="camilla" class="form-control input-sm" >
		              		<option value="">Seleccione...</option>
		            		<option value="1">Limpieza</option>
		            		<option value="2">Desinfeccion</option>
		            		<option value="3">Limpieza y Desinfeccion</option>
		            	</select>
	            	</div>
	            </div>  
	            <div class="col-md-4">
	            	<label for="velador" class="col-md-3 control-label" style="font-size:12px;">Veladores</label>
	            	<div class="col-md-9">
		              	<select id="velador" name="velador" class="form-control input-sm" >
		              		<option value="">Seleccione...</option>
		            		<option value="1">Limpieza</option>
		            		<option value="2">Desinfeccion</option>
		            		<option value="3">Limpieza y Desinfeccion</option>
		            	</select>
	            	</div>
	            </div> 
	            <div class="col-md-4">
	            	<label for="monitor" class="col-md-3 control-label" style="font-size:12px;">Monitores</label>
	            	<div class="col-md-9">
		              	<select id="monitor" name="monitor" class="form-control input-sm" >
		              		<option value="">Seleccione...</option>
		            		<option value="1">Limpieza</option>
		            		<option value="2">Desinfeccion</option>
		            		<option value="3">Limpieza y Desinfeccion</option>
		            	</select>
	            	</div>
	            </div> 
	            <div class="col-md-4">
	            	<label for="sop_monitor" class="col-md-3 control-label" style="font-size:12px;">Soporte Monitores</label>
	            	<div class="col-md-9">
		              	<select id="sop_monitor" name="sop_monitor" class="form-control input-sm" >
		              		<option value="">Seleccione...</option>
		            		<option value="1">Limpieza</option>
		            		<option value="2">Desinfeccion</option>
		            		<option value="3">Limpieza y Desinfeccion</option>
		            	</select>
	            	</div>
	            </div> 
	            <div class="col-md-4">
	            	<label for="otros" class="col-md-3 control-label" style="font-size:12px;">Otros Equipos</label>
	            	<div class="col-md-9">
		              	<select id="otros" name="otros" class="form-control input-sm" >
		              		<option value="">Seleccione...</option>
		            		<option value="1">Limpieza</option>
		            		<option value="2">Desinfeccion</option>
		            		<option value="3">Limpieza y Desinfeccion</option>
		            	</select>
	            	</div>
	            </div>
	            <div  class="col-md-4">
		            <label for="responsable" class="col-md-4 control-label" style="font-size:12px;">Responsable</label>
		            <div class="col-md-8">
		              <input type="text" name="responsable" class="form-control input-sm">
		          	</div>
	            </div>  
          	
          	</div>
          	<div class="form-group col-md-8 col-xs-6">
          		<div class="col-md-2">
   	          		<label for="observacion" class="col-md-3 control-label" style="font-size:12px;">Obsevación</label>
          		</div>
          		<div class="col-md-10">
	              <input type="text" name="observacion" class="form-control input-sm">
	            </div>  
          	</div>
          	<div class="form-group col-md-2 ">
                <div class="col-md-7">
                    <button type="button" class="btn btn-primary btn-xs" onclick="guardar();"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
                </div>
            </div>
        </form>
  	</div>
  </div>
</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
	

	function guardar(){
        //alert("ingreso");
        $.ajax({
          type: 'post',
          url:"{{ route('limpieza.guardar2') }}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            console.log(data);
            $('#nuevo2').modal('hide');
            $('#boton_salas'+data.id_sala).click();
            if(data == data.estado){
                swal({
                    title: "Datos Guardados",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                })
                
            };
          },
          error: function(data){
             console.log(data);
             //swal("Complete todos los campos");
          }
        });

    } 
    
</script>

