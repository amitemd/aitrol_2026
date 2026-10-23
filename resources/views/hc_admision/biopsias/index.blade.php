@extends('hc_admision/biopsias/base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style>
/* unvisited link */
a:link {
    color: black;
}

/* visited link */
a:visited {
    color: lightgreen;
}

/* mouse over link */
a:hover {
    color: blue;
}
button{
  width: 100%;
}


</style>

<section class="content">
<div class="box">
<div class="box-header">
        <form id="formulario_biopsias" action="{{route('muestrabiopsias.index')}}" method="POST">
           {{ csrf_field() }}
          
            <div class="form-group col-md-5 col-xs-6" >
              <label for="cedula" class="col-md-3 control-label">Cedula Paciente</label>
              <div class="col-md-9">
                <div class="input-group">
                  <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Numero de Cedula" onchange="fecha_buscador()">
                  <div class="input-group-addon">
                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = '';"></i>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-10" >
            <div class="form-group col-md-5">
              <label class="col-md-3 control-label">Fecha Inicio</label>
              <div class="col-md-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" value="" name="fecha" class="form-control" id="fecha"  required>
                </div>
              </div>
            </div>

            <div class="form-group col-md-5" >
              <label class="col-md-3 control-label">Fecha Fin </label>
              <div class="col-md-9">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" value="" name="fechafin" class="form-control" id="fechafin" required>
                </div>
              </div>
            </div>
            
            <div class="form-group col-md-2" >
              <button type="submit" class="col-md-12 btn btn-primary">Buscar</button>
            </div>

            </div>
                  
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
                <div class="table-responsive col-md-12 col-xs-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>Nombre del paciente</th>
                                <th>Cedula</th>
                                <th>Descripcion </th>
                                <th># De Frascos</th>
                                <th> Se realizo examenes aqui </th>
                      
                                </tr>
                        </thead>
                        <tbody>
                        @if(count($biopsias) > 0)
                        @foreach ($biopsias as $biopsia)
                        <tr>
                            <td>@if(isset($biopsia->pacientes)){{$biopsia->pacientes->nombre1}} {{$biopsia->pacientes->nombre2}} {{$biopsia->pacientes->apellido1}} {{$biopsia->pacientes->apellido2}} @endif</td>
                            <td>{{$biopsia->id_paciente}}</td>
                            <td>{{$biopsia->descripcion_frasco}}</td>
                            <td>{{$biopsia->numero_frasco}} </td>
                            <td> 
                            <select class="form-control " id="biopsias_biopsia" onchange="actualizar_estadosm(this, {{$biopsia->id}})">
                              <option @if ($biopsia->muestra_biopsia == 0) selected  @endif  value="0"> No </option>
                              <option @if ($biopsia->muestra_biopsia == 1) selected  @endif  value="1"> SI </option>
                            </select>
                                       
                         
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">
                                No hay datos
                            </td>
                        </tr>
                        @endif
                        
                        </tbody>
                    
                    </table>   
                    {{$biopsias->appends(Request::only(['fechafin','fecha']))->links()}} 
                   </div>
              
            </div>
         </div>
        </div>
        </div>
        </form>
        </section>

<script src="{{ asset("/plugins/datetimepicker/bootstrap-material-datetimepicker.js")}}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha}}'

        });
        $("#fecha").on("dp.change", function (e) {
          $('#fechafin').data("DateTimePicker").minDate(e.date);
          fecha_buscador();
          //alert('entra:inicio');
        });

        $('#fechafin').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fechafin}}'
        });
        $("#fechafin").on("dp.change", function (e) {
          //alert('entra:fin');
          fecha_buscador();
        });
  
    });
</script>
<script type="text/javascript" >
    function fecha_buscador(){
      //alert('entra');
      $('#formulario_biopsias').submit();
    }
</script>
<script type="text/javascript">
  
  function actualizar_estadosm (e, id){
    console.log(e.value)
    console.log(id)
   $.ajax({
    type: 'post',
    url:"{{route('muestrabiopsias.update')}}",
    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {
       "estado":e.value,
       "id":id,
        }
        ,success: function(data){
            location.reload();  
        console.log(data);
             alert(data.msj);
            $( "#boton_actualizar" ).click();
            if(data == "ok"){
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
            }
    });

  }
</script>


@endsection