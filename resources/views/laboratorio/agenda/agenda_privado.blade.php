<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .select2-container--default .select2-selection--multiple .select2-selection__choice{
        background-color: #009999;
    }
</style>
<div class="modal-header" style="padding: 1px;">
    <div class="col-md-10"><h4>Agendar Toma de Muestra en Laboratorio</h4></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body" style="padding: 2px;">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12" >
               	
	                <div class="form-group col-md-3 " style="padding: 0px;">
	                    <label for="fecha" class="col-md-12 control-label">Fecha </label>
	                    <div class="input-group date col-md-12" >
	                        <div class="input-group-addon">
	                            <i class="fa fa-calendar"></i>
	                        </div>
	                        <input type="text" name="fecha" class="form-control pull-right input-sm" id="fecha" required autocomplete="off">    
	                    </div>
	                </div> 

	                <div class="form-group col-md-3 " style="padding: 0px;">
	                    <label for="fecha" class="col-md-12 control-label">Tipo </label>
	                    <div class="input-group date col-md-12" >
	                    	<select name="proc_consul" id="proc_consul" class="form-control input-sm" onchange="si_es_test(this)">
	                    		<option value="0">Toma de Muestra</option>
	                    		<option value="1">Prueba de Alimentos</option>
	                    	</select>
	                    </div>
	                </div> 
	                <div class="form-group col-md-6" id="div_test" style="padding: 0px;display: none" >
	                	<label for="fecha" class="col-md-12 control-label">Test Alimentos</label>
                        <select class="form-control select2 input-sm" multiple="multiple" name="proc[]" data-placeholder="Seleccione los Procedimientos" required style="width: 100%;" id="procs">
                            @foreach($procedimientos as $procedimiento)
                            <option value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                            @endforeach
                        </select>
                    </div>   
	                <div class="form-group col-md-12 " style="padding: 0px;">
	                    <label for="observaciones" class="col-md-12 control-label">Observacion </label>
	                    <div class="col-md-12" style="padding: 0px;">
	                        <input type="text" name="observaciones" class="form-control input-sm" id="observaciones" autocomplete="off">    
	                    </div>
	                </div>

	                <input type="hidden" name="inicio" id="inicio">
	                <input type="hidden" name="fin" id="fin">

                    <div class="col-md-3">
                        <button type="button" id="bagregar" onclick="agendar();" class="btn btn-primary" disabled>
                            <span class="glyphicon glyphicon-floppy-disk"></span> Agendar
                        </button>
                    </div>  
                    
	        </div>
	               
            <div class="box-body" id="xdiv">
                   
            </div>   
            <div class="box-body" id="consulta_calendario">
                   
            </div>    
                
        </div>
            
    </div>     
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>
<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
            defaultDate: '{{Date('Y-m-d')}}',
        });
        $("#fecha").on("dp.change", function (e) {
            calendario();
        });
        calendario();
        //Initialize Select2 Elements
        $('.select2').select2({
            tags: false
        });

        $("select").on("select2:select", function (evt) {
            var element = evt.params.data.element;
            //console.log(element);
            var $element = $(element);

            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });
    });

    function calendario(){
        
        $.ajax({
          type: 'post',
          url:"{{route('orden_labs.ag_laboratorio_calendario')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_aglabs").serialize(),
          success: function(data){
            //console.log(data);
            $('#consulta_calendario').empty().html(data);
          },
          error: function(data){
             //console.log(data);
          }
        });
    }

    function agendar(){
        var procs       = $('#procs').val();
        var proc_consul = $('#proc_consul').val();
        var pasar = true;
        if(proc_consul=='1'){
            if(procs==''){
                pasar = false;
                alert("Seleccione pruebas");
            }
        } 
        if(pasar){
            
            $.ajax({// Guarda en Base
                type: "post",
                url: "{{route('orden_labs.privados_store')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: "json",
                data: $("#form_aglabs").serialize(),
                success: function(datahtml){
                    console.log(datahtml);
                    if(datahtml.estado=='OK'){
                        swal.fire({
                            title: datahtml.mensaje,
                            //text: "You won't be able to revert this!",
                            icon: "success",
                            type: 'success',
                            buttons: true,
                          
                        }).then((result) => {
                          if (result.value) {
                            location.reload();
                          }
                        })     
                    }else{
                        swal.fire({
                            title: datahtml.mensaje,
                            //text: "You won't be able to revert this!",
                            icon: "error",
                            type: 'error',
                            buttons: true,
                          
                        }).then((result) => {
                          if (result.value) {
                            alert("que hago");
                          }
                        })     
                    }
                             
                },
                error: function(datahtml){
                    
                    swal.fire({
                        title: 'Error, no se pudo realizar la operación',
                        //text: "You won't be able to revert this!",
                        icon: "error",
                        type: 'error',
                        buttons: true,
                      
                    }).then((result) => {
                      if (result.value) {
                        location.reload();
                      }
                    })     
                   
                }
            });
        }
        console.log(procs);
        //alert("guardo");
        
    }

    function si_es_test(sel){
        //alert(sel.value);
        if(sel.value=='1'){
            $('#div_test').show();
        }
    }
</script>