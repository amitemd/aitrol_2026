<form id="form_new_aglabs_publica" class="form-vertical" role="form">
    <div class="modal-header" style="padding: 1px;">
        <div class="col-md-10"><h4>@lang('laboratorio.agendartomamuestralab')</h4></div>
        <div class="col-md-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
        </button>
        </div>
    </div>
    <div class="modal-body" style="padding: 2px;">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12" >
                    <input type="hidden" name="id_orden" value="{{$orden->id}}">
                    
                    <div class="form-group col-md-3 " style="padding: 0px;">
                        <label for="fecha" class="col-md-12 control-label">@lang('laboratorio.fecha') </label>
                        <div class="input-group date col-md-12" >
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" name="fecha" class="form-control pull-right input-sm" id="fecha" required autocomplete="off">    
                        </div>
                    </div>    
                    
                    <div class="form-group col-md-9 " style="padding: 0px;">
                        <label for="observaciones" class="col-md-12 control-label">@lang('laboratorio.observacion') </label>
                        <div class="col-md-12" style="padding: 0px;">
                            <input type="text" name="observaciones" class="form-control input-sm" id="observaciones" autocomplete="off">    
                        </div>
                    </div>

                    <input type="hidden" name="inicio" id="inicio">
                    <input type="hidden" name="fin" id="fin"> 
                        <div class="col-md-offset-5">
                            <button type="button" id="bagregar" onclick="agendar();" class="btn btn-primary" disabled>
                                <span class="glyphicon glyphicon-floppy-disk"></span> @lang('laboratorio.agendar')
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
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" >@lang('laboratorio.cerrar')</button>
    </div>
</form>
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
    });
    function calendario(){
        
        $.ajax({
          type: 'post',
          url:"{{route('orden_labs.ag_laboratorio_calendario_publico')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_new_aglabs_publica").serialize(),
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
        //alert("guardo");
        $.ajax({// Guarda en Base
            type: "post",
            url: "{{route('orden.new_store_publico')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data: $("#form_new_aglabs_publica").serialize(),
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
                        location.reload();
                      //location.href = '{{ route('orden.index')}}';
                    })     
                }else{
                    swal.fire({
                        title: datahtml.mensaje,
                        //text: "You won't be able to revert this!",
                        icon: "error",
                        type: 'error',
                        buttons: true,
                      
                    }).then((result) => {
                      
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
</script>