<style type="text/css">
    .ui-autocomplete {
      z-index:2147483647;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />



<!-- Ventana modal editar -->
<div class="modal fade" id="bucar_nombre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal-header">
    <div class="col-12">
        <center>
            <h4 class="modal-title" id="myModalLabel">@lang('hc4.agregar') {{$i}} @lang('hc4.paraDoctor') {{ $doctor->apellido1}}</h4>    
        </center>
    </div>
</div>

<div class="modal-body"> 
    
    <div class="panel-body">
        
        <form method="POST" action="#" id="form" >  
                
            <input type="hidden" name="clase" value="{{$i}}">
            <input id="id_doctor1" type="hidden" name="id_doctor1" value="{{$doctor->id}}" >
            {{ csrf_field() }}



            <div class="col-12">          
                <div class="row">          
                    <div class="col-6">          
                        <div class="row">
                            <div class="form-group col-12 cl_inicio {{ $errors->has('inicio') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                                <label class="col-md-4 control-label">@lang('hc4.inicio')</label>
                                <div class="col-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{ old('inicio') }}" name="inicio" class="form-control" id="inicio" required>
                                    </div>
                                    <span class="help-block">
                                        <strong id="str_inicio"></strong>
                                        <strong id="str_id_doctor1"></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>       
                    <div class="col-6">          
                        <div class="row">
                            <div class="form-group col-12 cl_fin {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">@lang('hc4.fin')</label>
                                <div class="col-12">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{ old('fin') }}" name="fin" class="form-control" id="fin" required>
                                    </div>
                                    <span class="help-block">
                                        <strong id="str_fin"></strong>
                                        <strong id="str_id_doctor1"></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
            <div class="row">
                <div class="form-group col-6 cl_observaciones {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                    <label for="observaciones" class="col-md-4 control-label">@lang('hc4.titulo')</label>
                    <div class="col-12">
                        <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{old('observaciones')}}" required="required">
                        <span class="help-block">
                            <strong id="str_observaciones"></strong>
                        </span>
                    </div>
                </div>

                <div class="form-group col-6 cl_id_sala {{ $errors->has('id_sala') ? ' has-error' : '' }}">
                    <label for="id_sala" class="col-md-4 control-label">@lang('hc4.ubicacion')</label>
                    <div class="col-12">
                    <select id="id_sala" name="id_sala" class="form-control" required>
                            <option value="">@lang('hc4.seleccione')..</option>
                            @foreach ($salas as $sala)
                                <option @if(old('id_sala')==$sala->id){{"selected"}}@endif value="{{$sala->id}}">@if($sala->nombre_sala == "..") {{$sala->nombre_hospital}}{{$sala->nombre_sala}}@else {{$sala->nombre_sala}} / {{$sala->nombre_hospital}} @endif</option>
                            @endforeach
                        </select>      
                        <span class="help-block">
                            <strong id="str_id_sala"></strong>
                        </span>
                    </div>
                </div>
            </div>
            </div>

            <div class="col-12">
                <center>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <a href="#" id="confirme" class="btn btn-primary">@lang('hc4.guardar')</a>
                            
                        </div>
                    </div>  
                </center>
            </div>    
        </form>

    </div>
</div>        
<div class="modal-footer">
  <button type="button" id="bcerrar" class="btn btn-default" data-dismiss="modal" >@lang('hc4.cerrar')</button>
</div>

<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>
<script type="text/javascript">

 $(document).ready(function(){ 


        
        $('#inicio').datetimepicker({
            format: 'YYYY/MM/DD HH:mm',
            minDate: '{{date('Y/m/d H:i')}}',



            });
        $('#fin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD HH:mm',
            minDate: '{{date('Y/m/d H:i')}}',
             //Important! See issue #1075
            
        });

        $('#inicio').val('{{date('Y/m/d H:i')}}');
        incremento();

        $("#inicio").on("dp.change", function (e) {
            $('#fin').data("DateTimePicker").minDate(e.date);
            incremento();
        });


        $('.select2').select2();
        
        


    
});  

function incremento (){

        var fjs_inicio = document.getElementById("inicio").value;
        //alert(fjs_inicio);
        var fjs_fin = moment(fjs_inicio).add(60, 'm').format('YYYY/MM/DD HH:mm');
        $("#fin").val(fjs_fin   );
    } 


function Bloquear (){

        $.ajax({
        type: 'get',
        url:'{{ route('agenda.reuniondoctor') }}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#form").serialize(),
        success: function(data){
            
            alert('Agenda Bloqueada correctamente');
            $('#bcerrar').click();
            location.reload();
            //console.log(data);
        },
        error: function(data){
            //console.log(data);
            $(".cl_inicio").removeClass("has-error");
            $(".cl_fin").removeClass("has-error");
            $(".cl_inicio").removeClass("has-error"); 
            $(".cl_observaciones").removeClass("has-error");
            $(".cl_id_sala").removeClass("has-error");

            $('#str_inicio').empty().html("");
            $('#str_fin').empty().html("");
            $('#str_id_doctor1').empty().html("");
            $('#str_observaciones').empty().html("");
            $('#str_id_sala').empty().html("");
            
            if(data.responseJSON.inicio!=null){
                $(".cl_inicio").addClass("has-error");
                $('#str_inicio').empty().html(data.responseJSON.inicio);
            }
            if(data.responseJSON.fin!=null){
                $(".cl_fin").addClass("has-error");
                $('#str_fin').empty().html(data.responseJSON.fin);
            }
            if(data.responseJSON.id_doctor1!=null){
                $(".cl_inicio").addClass("has-error");    
                $(".cl_fin").addClass("has-error");
                $('#str_id_doctor1').empty().html(data.responseJSON.id_doctor1);
            }
            if(data.responseJSON.observaciones!=null){
                $(".cl_observaciones").addClass("has-error");
                $('#str_observaciones').empty().html(data.responseJSON.observaciones);
            }
            if(data.responseJSON.id_sala!=null){
                $(".cl_id_sala").addClass("has-error");
                $('#str_id_sala').empty().html(data.responseJSON.id_sala);
            }
            
        }
    })
}     
     

$('#confirme').click(function(event){

    $.ajax({
        type: 'get', 
        url:'{{ route('agenda.validaconfir') }}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#form").serialize(),
        success: function(data){
            if(data!=0){
                var confirmar = confirm(data);
                if(confirmar){
                    Bloquear();
                }    
            }else{
                Bloquear();    
            }
            
            
        },
        error: function(data){
            //console.log(data);
            $(".cl_inicio").removeClass("has-error");
            $(".cl_fin").removeClass("has-error");
            $(".cl_inicio").removeClass("has-error"); 
            $(".cl_observaciones").removeClass("has-error");
            $(".cl_id_sala").removeClass("has-error");

            $('#str_inicio').empty().html("");
            $('#str_fin').empty().html("");
            $('#str_id_doctor1').empty().html("");
            $('#str_observaciones').empty().html("");
            $('#str_id_sala').empty().html("");
            
            if(data.responseJSON.inicio!=null){
                $(".cl_inicio").addClass("has-error");
                $('#str_inicio').empty().html(data.responseJSON.inicio);
            }
            if(data.responseJSON.fin!=null){
                $(".cl_fin").addClass("has-error");
                $('#str_fin').empty().html(data.responseJSON.fin);
            }
            if(data.responseJSON.id_doctor1!=null){
                $(".cl_inicio").addClass("has-error");    
                $(".cl_fin").addClass("has-error");
                $('#str_id_doctor1').empty().html(data.responseJSON.id_doctor1);
            }
            if(data.responseJSON.observaciones!=null){
                $(".cl_observaciones").addClass("has-error");
                $('#str_observaciones').empty().html(data.responseJSON.observaciones);
            }
            if(data.responseJSON.id_sala!=null){
                $(".cl_id_sala").addClass("has-error");
                $('#str_id_sala').empty().html(data.responseJSON.id_sala);
            }
            
        }
    })


});

function js_paciente (){
    var js_ced = document.getElementById('id_paciente').value;
    //alert(js_ced);
    if(js_ced!='0'){
        $(".cl_telefono1").addClass("oculto");
        $('#mensaje').empty().html("Paciente se encuentra registrado en el sistema!!");   
    }else{
        $(".cl_telefono1").removeClass("oculto");
        $('#mensaje').empty().html("");   
    }
}


</script>

 


