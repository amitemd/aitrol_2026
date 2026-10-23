<label for="id_procedimiento" class="col-md-12 control-label">Procedimientos</label>
                                    <div class="col-md-12">
                                        <select class="form-control select2 input-sm" multiple="multiple" name="proc[]" data-placeholder="Seleccione los Procedimientos" required style="width: 100%;">
                                            @if($agenda->id_procedimiento!=null)
                                            <option selected value="{{$agenda->id_procedimiento}}">{{$procedimientos->find($agenda->id_procedimiento)->nombre}}</option>
                                            @endif
                                            @foreach($agendaprocedimientos as $agendaproc)
                                            <option selected value="{{$agendaproc->id_procedimiento}}">{{$procedimientos->find($agendaproc->id_procedimiento)->nombre}}</option>
                                            @endforeach
                                            @foreach($procedimientos as $procedimiento)
                                            @if($agenda->id_procedimiento!=$procedimiento->id)
                                            @if(is_null($agendaprocedimientos->where('id_procedimiento',$procedimiento->id)->first()))
                                            <option @if($agenda->id_procedimiento==$procedimiento->id) selected @endif @if(!$agendaprocedimientos->isEmpty()) @foreach($agendaprocedimientos as $agendaproc) @if($agendaproc->id_procedimiento==$procedimiento->id) selected @endif @endforeach @endif value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                                            @endif
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
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
                <!--input type="hidden" name="id_agenda" value=""-->
                <!--input type="hidden" name="url_doctor" value=""-->
                <!--div class="form-group col-md-6 " style="padding: 0px;">
                    <label for="id_doctor" class="col-md-12 control-label">doctor</label>
                    <div class="col-md-12">
                        <select id="id_doctor" name="id_doctor" class="form-control input-sm" required onchange="calendario();">
                            
                        </select>
                        
                    </div>
                </div-->
                <div class="form-group col-md-3 " style="padding: 0px;">
                    <label for="fecha" class="col-md-12 control-label">Fecha </label>
                    <div class="input-group date col-md-12" >
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="fecha" class="form-control pull-right input-sm" id="fecha" required autocomplete="off">    
                    </div>
                </div>    
                <!--salas-->
                <!--div class="form-group col-md-6 " style="padding: 0px;">
                    <label for="id_sala" class="col-md-12 control-label">Ubicación</label>
                    <div class="col-md-12">
                        <select id="id_sala" name="id_sala" class="form-control input-sm" required >
                       
                        </select> 
                           
                    </div>
                </div-->
                <div class="form-group col-md-9 " style="padding: 0px;">
                    <label for="observaciones" class="col-md-12 control-label">Observacion </label>
                    <div class="col-md-12" style="padding: 0px;">
                        <input type="text" name="observaciones" class="form-control input-sm" id="observaciones" autocomplete="off">    
                    </div>
                </div>

                <input type="hidden" name="inicio" id="inicio">
                <input type="hidden" name="fin" id="fin"> 
                    <div class="col-md-offset-5">
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
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>
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
        //alert("guardo");
        $.ajax({// Guarda en Base
            type: "post",
            url: "{{route('orden_labs.aglaboratorio_store')}}",
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
                        @if($agenda->id_doctor1!=null)
                            location.href = '{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $agenda->id_doctor1])}}';
                        @else
                            location.href = '{{ route('preagenda.edit', ['id' => $agenda->id])}}';
                        @endif
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
                        @if($agenda->id_doctor1!=null)
                            location.href = '{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $agenda->id_doctor1])}}';
                        @else
                            location.href = '{{ route('preagenda.edit', ['id' => $agenda->id])}}';
                        @endif
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

    function div_agendar(){
        //alert("guardo");
        $.ajax({// Guarda en Base
            type: "post",
            url: "{{route('orden_labs.aglaboratorio_store')}}",
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
                        @if($agenda->id_doctor1!=null)
                            location.href = '{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $agenda->id_doctor1])}}';
                        @else
                            location.href = '{{ route('preagenda.edit', ['id' => $agenda->id])}}';
                        @endif
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
                        @if($agenda->id_doctor1!=null)
                            location.href = '{{ route('agenda.edit2', ['id' => $agenda->id, 'doctor' => $agenda->id_doctor1])}}';
                        @else
                            location.href = '{{ route('preagenda.edit', ['id' => $agenda->id])}}';
                        @endif
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


</script>