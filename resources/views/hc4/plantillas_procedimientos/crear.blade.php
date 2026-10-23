                <section class="content">
            <div class="box box-primary">
                <div class="box-header">
                	<div class="col-md-12">
                		<div class="row">
                			<div class="col-md-6">
                				<span style="font-family: 'Helvetica general';" class="box-title">Agregar Nuevo Procedimiento</span>
                			</div>
                			<div class="col-md-6">
                				<button type="button" class="btn btn-success " onclick="plantilla_proc();" >
					            	<span > 
					            		Regresar
					            	</span>
					            </button>
                			</div>
                		</div>
                	</div>
                	
                </div>
                <div class="box-body"> 
                    <form id="plantilla_crear" class="form-vertical" role="form" method="POST" >
                        {{ csrf_field() }}
                        
                        <div class="col-md-12 cl_nombre_general" style="margin: 20px">
                        	<div class="row">
                            <label for="nombre_general" class="col-md-4 ">Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre_general" type="text" class="form-control" name="nombre_general" value="" required autofocus >
                            </div>
                            <span class="help-block">
				                <strong id="str_nombre_general"></strong>
				            </span>
                            </div>
                        </div>
                        <div class="col-md-12 cl_id_grupo_procedimiento" style="margin: 20px">
                            <div class="row">
                            <label for="id_grupo_procedimiento" class="col-md-4 ">Grupo al que pertenece</label>
                            <div class="col-md-7">
                                <select id="id_grupo_procedimiento" name="id_grupo_procedimiento" class="form-control" required>
                                    <option value="">Seleccione..</option>
                                    @foreach($grupo_procedimiento as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach            
                                </select>  
                            </div>
                           
                            </div>
                        </div>
                        <div class="col-md-12 cl_anestesia" style="margin: 20px">
                            <div class="row">
                            <label for="estado_anestesia" class="col-md-4 control-label">Posee Record Anestesico</label>
                            <div class="col-md-7">
                                <select id="estado_anestesia" name="estado_anestesia" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>            
                                </select>  
                            </div>
                           
                            </div>
                        </div>
                        <center>
                        <div class="form-group">
                            <div class="col-md-6 col-md-4">
                                <button id="btn_save" type="button" class="btn btn-primary" onclick="guardar_plantilla_proc()">
                                    Agregar
                                </button>
                            </div>
                        </div>
                        </center>
                    </form>
                </div>
            </div>
        </section>


    <script type="text/javascript">


    function guardar_plantilla_proc(){
     	//alert("entro");
     	$('.cl_nombre_general').removeClass('has-error');
        $('#str_nombre_general').text('');

        $.ajax({
          type: 'post',
          url:"{{route('hc4/plantilla_proc.store')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#plantilla_crear").serialize(),
          success: function(data){
            plantilla_proc();
          },
          error:  function(){

            if(data.responseJSON.nombre_general!=null){
              $('.cl_nombre_general').addClass('has-error');
              $('#str_nombre_general').text(data.responseJSON.nombre_general);
            }
          
          }
        });
    }

    </script>
    
