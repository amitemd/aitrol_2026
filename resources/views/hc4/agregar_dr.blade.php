<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<style type="text/css">
    .ui-autocomplete {
      z-index:2147483647;
    }
</style>
<link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">


<!-- Ventana modal editar -->
<div class="modal fade" id="bucar_nombre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal-header" >
    <div class="col-12">
        <center>
            <h4 class="modal-title" id="myModalLabel">AGREGAR @if($i=='1') NUEVO PROCEDIMIENTO @else NUEVA CONSULTA @endif PARA EL DR(A). {{ $doctor->apellido1}}</h4> 
        </center>
    </div>
</div>

<div class="modal-body"> 
    
    <div class="panel-body">
        
        
        <form method="POST" action="#" id="form" >
                    
            {{ csrf_field() }}
            <!--nombre1-->
            <span style="color: blue;" class="col-md-offset-4" id="mensaje"></span>
            <div class="form-group col-md-12 cl_nombre1 {{ $errors->has('nombre1') ? ' has-error' : '' }}" id="cambio5">
                <div class="row">
                <label for="nombre1" class="col-md-2 control-label">Nombre Paciente</label>
                <div class="col-md-8">
                    <input id="nombre1" type="text" class="form-control"  name="nombre1" value="{{old('nombre1')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required placeholder="Nombre y Apellido">
                    <span class="help-block">
                        <strong id="str_nombre1"></strong>
                    </span>
                </div>
                </div>
            </div>
            
                           
            <input type="hidden" class="form-control" name="id_doctor1" value="{{ $doctor->id}}" >
            <input type="hidden" class="form-control" id="id_paciente" name="id_paciente" value="" >
            <input type="hidden" class="form-control" id="proc_consul" name="proc_consul" value="{{$i}}" >
                  
            <div class="col-12">          
                <div class="row">          
                    <div class="col-6">          
                        <div class="row">
                            <div class="form-group col-12 cl_inicio {{ $errors->has('inicio_dr') ? ' has-error' : '' }} {{ $errors->has('id_doctor1') ? ' has-error' : '' }}" >
                                <div class="row">
                                    <label class="col-md-4 control-label">Inicio</label>
                                    <div class="col-md-7">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" value="" autocomplete="off" name="inicio" class="form-control" id="inicio_dr" required onchange="incremento(event)">
                                        </div>
                                       <span class="help-block">
                                            <strong id="str_inicio"></strong>
                                        </span>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">          
                        <div class="row">
                            <div class="form-group col-12 cl_fin {{ $errors->has('fin') ? ' has-error' : '' }}  {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                                <div class="row">
                                    <label class="col-md-4 control-label">Fin</label>
                                    <div class="col-md-7">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                        <input autocomplete="off" type="text" value="{{ old('fin') }}" name="fin" class="form-control" id="fin_dr" required>
                                        </div>
                                       <span class="help-block">
                                            <strong id="str_fin"></strong>
                                        </span>
                                    </div>
                                </div>   
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12" style="padding-left: 0px">
                <div id="cambio15a" class="form-group col-md-6 cl_cortesia {{ $errors->has('cortesia') ? ' has-error' : '' }} " style="padding-left: 0px">
                    <label for="cortesia" class="col-md-4 control-label">Cortesia</label>
                    <div class="col-md-7">
                        <select id="cortesia" class="form-control" name="cortesia">
                            <option value="NO" @if(old('cortesia')=="NO"){{"selected"}}@endif>NO</option>
                            <option value="SI" @if(old('cortesia')=="SI"){{"selected"}}@endif>SI</option>        
                        </select>
                    </div>
                    <span class="help-block">
                        <strong id="str_cortesia"></strong>
                    </span>
                </div>
            </div>

            <!--telefono1-->
            <div class="form-group col-md-6 cl_telefono1 {{ $errors->has('telefono1') ? ' has-error' : '' }} oculto">
                <label for="telefono1" class="col-md-4 control-label">Teléfono Contacto</label>
                <div class="col-md-7">
                    <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ old('telefono1') }}" required>
                    <span class="help-block">
                        <strong id="str_telefono1"></strong>
                    </span>
                </div>    
            </div>  

            @if($i=='1')    
            @php $cont=count(old('procedimiento')) @endphp 
            <div id="cambio3" class="form-group col-12 cl_procedimiento {{ $errors->has('id_procedimiento') ? ' has-error' : '' }}">
                <label for="id_procedimiento" class="col-md-2 col-12 control-label">Procedimientos </label>
                <div class="col-12" style="padding-left: 10px;">
                    <select id="id_procedimiento" class="form-control select2" name="procedimiento[]" multiple="multiple" data-placeholder="Seleccione" style="width: 100%;" >
                        @foreach($procedimiento as $procedimiento)
                            <option  @for($x=0; $x<$cont; $x++) @if(old('procedimiento.'.$x)==$procedimiento->id) selected @endif @endfor value="{{$procedimiento->id}}">{{$procedimiento->nombre}}</option>
                        @endforeach
                    </select>
                    <span class="help-block">
                        <strong id="str_procedimiento"></strong>
                    </span>
                </div>
            </div>
            @endif
            <div class="col-12">
                <center>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <a href="#" id="confirme" class="btn btn-primary">Guardar</a>
                        </div>
                    </div>
                </center>
            </div>


        </form>   
         
            

    </div>
</div>

<div class="modal-footer">
  <button type="button" id="bcerrar" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>






<script type="text/javascript">

 $(document).ready(function(){ 


    
          $("#nombre1").autocomplete({
              source: function( request, response ) {
                
                $.ajax({
                    url:"{{route('paciente.nombre')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                    data: {
                        term: request.term
                      },
                    dataType: "json",
                    type: 'post',
                    success: function(data){
                        response(data);
                        //console.log(data)
                    }
                })
              },
              minLength: 3,
            } );

          $("#nombre1").change( function(){
              $.ajax({
                type: 'post',
                url:"{{route('paciente.nombre2')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#nombre1"),
                success: function(data){
                    $('#id_paciente').val(data);
                    //console.log(data);
                    js_paciente();

                },
                error: function(data){
                    //console.log(data);
                }
            })
          });

        
        $('#inicio_dr').datetimepicker({
            format: 'YYYY/MM/DD hh:mm',
            minDate: '{{date("Y/m/d H:i")}}',



            });
        $('#fin_dr').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD hh:mm',
            minDate: '{{date("Y/m/d H:i")}}',
             //Important! See issue #1075
            
        });

        $('#inicio_dr').val('{{date("Y/m/d H:i")}}');
        var inicio = document.getElementById("inicio_dr").value;

        @if($doctor->id=='1314490929')
            var fin = moment(inicio).add(20, 'm').format('YYYY/MM/DD HH:mm');
        @else
            var fin = moment(inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
            
        @endif

        $("#fin_dr").val(fin);

        $("#inicio_dr").on("dp.change", function (e) {
            $('#fin_dr').data("DateTimePicker").minDate(e.date);
            incremento(e);
        });


        $('.select2').select2();
        
        


    
});  

    function incremento (e){
        var fjs_inicio = document.getElementById("inicio_dr").value;
        var fjs_valor = document.getElementById("proc_consul").value;
         

         if(fjs_valor == 0 || fjs_valor == ''){

            
            @if($doctor->id=='1314490929')
            var fjs_fin = moment(fjs_inicio).add(20, 'm').format('YYYY/MM/DD HH:mm');
            @else
             var fjs_fin = moment(fjs_inicio).add(15, 'm').format('YYYY/MM/DD HH:mm');
            @endif 
            

            $("#fin_dr").val(fjs_fin);
         }
         if(fjs_valor == 1){

            var fjs_fin = moment(fjs_inicio).add(30, 'm').format('YYYY/MM/DD HH:mm');
            
            $("#fin_dr").val(fjs_fin);
         }
    } 
     

$('#confirme').click(function(event){

    $.ajax({
        type: 'get',
        url:'{{route('agendar_dr.actualizar')}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#form").serialize(),
        success: function(data){
            
            alert('Paciente Agendado correctamente');
            $('#bcerrar').click();
            location.reload();
            //console.log(data);
        },
        error: function(data){
            //console.log(data);
            $(".cl_nombre1").removeClass("has-error");
            $(".cl_inicio").removeClass("has-error");
            $(".cl_fin").removeClass("has-error");
            $(".cl_cortesia").removeClass("has-error");
            $(".cl_telefono1").removeClass("has-error");
            $(".cl_procedimiento").removeClass("has-error");

            $('#str_nombre1').empty().html("");
            $('#str_inicio').empty().html("");
            $('#str_inicio').empty().html("");
            $('#str_cortesia').empty().html("");
            $('#str_telefono1').empty().html("");
            $('#str_procedimiento').empty().html("");
            
            if(data.responseJSON.nombre1!=null){
                $(".cl_nombre1").addClass("has-error");
                $('#str_nombre1').empty().html(data.responseJSON.nombre1);
            }
            if(data.responseJSON.inicio!=null){
                $(".cl_inicio").addClass("has-error");
                $('#str_inicio').empty().html(data.responseJSON.inicio);
            }
            if(data.responseJSON.fin!=null){
                $(".cl_fin").addClass("has-error");
                $('#str_fin').empty().html(data.responseJSON.fin);
            }
            if(data.responseJSON.cortesia!=null){
                $(".cl_cortesia").addClass("has-error");
                $('#str_cortesia').empty().html(data.responseJSON.cortesia);
            }
            if(data.responseJSON.telefono1!=null){
                $(".cl_telefono1").addClass("has-error");
                $('#str_telefono1').empty().html(data.responseJSON.telefono1);
            }
            if(data.responseJSON.procedimiento!=null){
                $(".cl_procedimiento").addClass("has-error");
                $('#str_procedimiento').empty().html(data.responseJSON.procedimiento);
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

 


