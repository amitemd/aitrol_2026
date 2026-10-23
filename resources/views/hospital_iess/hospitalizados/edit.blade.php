<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class="modal-header">
  <!--button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button-->
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">EDITAR</h4>
  <h4 class="modal-title" style="text-align: center;"><b>PACIENTE:</b> {{$paciente->id}} {{$paciente->nombre1}} {{$paciente->apellido1}} </h4>
    
</div>
 
<div class="modal-body"> 
    
    <form method="POST" action="#" id="form" >

        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$hospitalizado->id}}">
        
        
        <!--procedencia-->
                        <div class="form-group col-md-6 cl_proc">
                            <label for="procedencia" class="col-md-4 control-label">Ubicación</label>

                            <div class="col-md-7">
                                <input id="procedencia" type="text" class="form-control input-sm" name="procedencia" value="@if(old('procedencia')!=''){{ old('procedencia') }}@else {{$hospitalizado->procedencia}}@endif"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                <span class="help-block">
                                    <strong id="str_proc"></strong>
                                </span>
                            </div>
                        </div>

                        <!--Direccion-->
                        <div class="form-group col-md-6 cl_sala">
                            <label for="sala_hospital" class="col-md-4 control-label">Sala</label>

                            <div class="col-md-7">
                                <input id="sala_hospital" type="text" class="form-control input-sm" name="sala_hospital" value="@if(old('sala_hospital')!=''){{ old('sala_hospital') }}@else{{$hospitalizado->sala_hospital}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                <span class="help-block">
                                    <strong id="str_sala"></strong>
                                </span>
                            </div>
                        </div>

                        <!--id_tipo_seguro-->
                        <div class="form-group col-md-6 cl_segu">
                            <label for="id_seguro" class="col-md-4 control-label">Tipo Seguro</label>
                            <div class="col-md-7">
                                <select id="id_tipo_seguro" name="id_seguro" class="form-control input-sm" required>
                                        <option value="">Seleccione ..</option>
                                    @foreach($seguros as $seguro) 
                                        <option @if(old('id_seguro')== $seguro->id) selected @else @if($hospitalizado->id_seguro==$seguro->id) selected @endif @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>   
                                    @endforeach
                                </select>  
                                
                                <span class="help-block">
                                    <strong id="str_segu"></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group col-md-6 cl_doct">
                                <label for="id_doctor1" class="col-md-4 control-label">Doctor</label>
                                <div class="col-md-7"> 
                                    <select id="id_doctor1" name="id_doctor1" class="form-control input-sm" >
                                        <option value="">Seleccione ...</option>
                                    @foreach ($usuarios as $usuario)
                                        <option @if(old('id_doctor1') == $usuario->id) selected @else @if($hospitalizado->id_doctor1 == $usuario->id) selected @endif @endif value="{{$usuario->id}}">{{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
                                    @endforeach
                                    </select>
                                    <span class="help-block">
                                        <strong id="str_doct"></strong>
                                    </span>
                                </div>
                            </div>

                        <div id="cambio17" class="form-group col-md-6 cl_fech" >
                            <label class="col-md-4 control-label">Ingreso</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="@if(old('fechaini')!=''){{ old('fechaini') }}@else{{$hospitalizado->fechaini}}@endif" name="fechaini" class="form-control input-sm" id="fechaini"  placeholder="AAAA/MM/DD" required >
                                </div>
                                   <span class="help-block">
                                        <strong id="str_fech"></strong>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group col-md-6 cl_esta">
                            <label for="estado" class="col-md-4 control-label">Estado</label>
                            <div class="col-md-7"> 
                                <select id="estado" name="estado" class="form-control input-sm" onchange="alta();">
                                    <option @if(old('estado')!='') @if(old('estado')=='1') selected @endif @else @if($hospitalizado->estado=='1') selected @endif @endif value="1">HOSPITALIZADO</option>
                                    <option @if(old('estado')!='') @if(old('estado')=='2') selected @endif @else @if($hospitalizado->estado=='2') selected @endif @endif value="2">ALTA</option>
                                </select>
                                <span class="help-block">
                                        <strong id="str_esta"></strong>
                                    </span> 
                            </div>
                        </div>

                        <div id="cambio17" class="form-group col-md-6 cl_alta oculto" >
                            <label class="col-md-4 control-label">Alta</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="@if(old('fechafin')!=''){{ old('fechafin') }}@else{{$hospitalizado->fechafin}}@endif" name="fechafin" class="form-control input-sm" id="fechafin"  placeholder="AAAA/MM/DD" required >
                                </div>
                                   <span class="help-block">
                                        <strong id="str_alta"></strong>
                                    </span>
                            </div>
                        </div>    

                        <div class="form-group col-md-6 cl_obse">
                            <label for="observaciones" class="col-md-4 control-label">Observaciones</label>

                            <div class="col-md-7">
                                <input id="observaciones" type="text" class="form-control input-sm" name="observaciones" value="@if(old('observaciones')!=''){{ old('observaciones') }}@else{{$hospitalizado->observaciones}}@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >

                                <span class="help-block">
                                        <strong id="str_obse"></strong>
                                    </span>
                            </div>
                        </div>    
        
         
            
        <!--Botón para abrir la ventana modal de editar -->
        <a href="#" id="confirme" class="btn btn-primary col-md-offset-5"><span class="glyphicon glyphicon-floppy-disk"></span> Confirme</a>
    </form>

</div>  

<div class="modal-footer">
  <button id="btn_cerrar" type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
$(document).ready(function() 
{

    alta();

    $('#fechaini').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
    });

    $('#fechafin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
    });    

});



$('#confirme').click(function(event){

    $(".cl_proc").removeClass("has-error");
    $(".cl_sala").removeClass("has-error");
    $(".cl_segu").removeClass("has-error");
    $(".cl_doct").removeClass("has-error");
    $(".cl_fech").removeClass("has-error");
    $(".cl_esta").removeClass("has-error");
    $(".cl_obse").removeClass("has-error");
    $(".cl_alta").removeClass("has-error");
    

    $('#str_proc').empty().html('');
    $('#str_sala').empty().html('');
    $('#str_segu').empty().html('');
    $('#str_doct').empty().html('');
    $('#str_fech').empty().html('');
    $('#str_esta').empty().html('');
    $('#str_obse').empty().html('');
    $('#str_alta').empty().html('');
    

    $.ajax({
        type: 'get',
        url:'{{route('hospitalizados.update2',['id' => $hospitalizado->id])}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#form").serialize(),
        success: function(data){
            
            //location.href='{{route('hospitalizados.index')}}';
            $("#btn_cerrar").click();
        },
        error: function(data){
            
            if(data.responseJSON.procedencia!=null){
                $(".cl_proc").addClass("has-error");
                $('#str_proc').empty().html(data.responseJSON.procedencia);
            }
            if(data.responseJSON.sala_hospital!=null){
                $(".cl_sala").addClass("has-error");
                $('#str_sala').empty().html(data.responseJSON.sala_hospital);
            }
            if(data.responseJSON.id_seguro!=null){
                $(".cl_segu").addClass("has-error");
                $('#str_segu').empty().html(data.responseJSON.id_seguro);
            }
            if(data.responseJSON.id_doctor1!=null){
                $(".cl_doct").addClass("has-error");
                $('#str_doct').empty().html(data.responseJSON.id_doctor1);
            }
            if(data.responseJSON.fechaini!=null){
                $(".cl_fech").addClass("has-error");
                $('#str_fech').empty().html(data.responseJSON.fechaini);
            }
            if(data.responseJSON.fechafin!=null){
                $(".cl_alta").addClass("has-error");
                $('#str_alta').empty().html(data.responseJSON.fechafin);
            }
            if(data.responseJSON.estado!=null){
                $(".cl_esta").addClass("has-error");
                $('#str_esta').empty().html(data.responseJSON.estado);
            }
            if(data.responseJSON.observaciones!=null){
                $(".cl_obse").addClass("has-error");
                $('#str_obse').empty().html(data.responseJSON.observaciones);
            }
        }
    })


});

function alta(){

    jsestado=document.getElementById('estado').value;

    if(jsestado==2){
        $(".cl_alta").removeClass("oculto");    
    }else{
        $(".cl_alta").addClass("oculto");
        document.getElementById('fechafin').value=document.getElementById('fechaini').value 
    }


}

</script>        