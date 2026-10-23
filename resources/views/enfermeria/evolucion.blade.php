@extends('enfermeria.evolucion_anestesiologo')
@section('action-content')
<style type="text/css">
  .alerta_correcto {
    position: absolute;
    z-index: 9999;
    top: 100px;
    right: 10px;
  }

  .btn {
    font-size: 15px;
    font-weight: bold;
    border-radius: 5px;
  }

  .radius {
    width: 70%;
    height: 80px;
    border-radius: 10px;
    /* Ajusta este valor según tu preferencia */
    padding: 10px;
    /* Opcional: Añade relleno para mejorar la apariencia */
    border: 1px solid #ccc;
    /* Opcional: Añade un borde para mayor contraste */
  }

  .butoon {
    width: 20%;
    height: 35px;
    border-radius: 10px;
    /* Ajusta este valor según tu preferencia */
    padding: 10px;
    /* Opcional: Añade relleno para mejorar la apariencia */
    border: 1px solid #ccc;
    /* Opcional: Añade un borde para mayor contraste */
  }




  .div-motivo {
    width: 500px;
  }


  .flex.justify-center {
    text-align: center;
  }


  .modal-footer {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
  }

  .modal-footer button {
    width: auto;
  }
</style>
<!-- Main content -->
@php
// Obtener la hora actual
$hora_actual = date('H:i:s'); // Formato: Horas, minutos, segundoResponsable: s
$user_ses = Auth::user()->nombre1." ".Auth::user()->nombre2." ".Auth::user()->apellido1." ".Auth::user()->apellido2;
$final = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $adicionales->id)->get();
@endphp
@php
$observacionesConcatenadas = "";
@endphp

@foreach ($final as $procedimiento)
@php
$observacion = $procedimiento->procedimiento->observacion;
$observacionesConcatenadas .= ($observacion . '+');
@endphp
@endforeach




<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<section class="content">

  <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{trans('eenfermeria.ObservaciónGuardadaCorrectamente')}}
  </div>

  <div class="box">
    <div class="box-header">

      <div class="row">
        <div class="col-md-12" style="text-align: right;">
          <a type="button" href="" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-arrow-left">{{trans('edisponibilidad.Regresar')}}</span>
          </a>
        </div>
        <div class="col-md-12">
          <div class="col-md-10">
            <h4 class="box-title"><b>{{trans('eenfermeria.Paciente')}}: </b><span style="color: red;">{{$adicionales->historia->id_paciente}}-{{$adicionales->historia->paciente->apellido1}} @if($adicionales->historia->paciente->apellido2!='(N/A)'){{$adicionales->historia->paciente->apellido2}}@endif {{$adicionales->historia->paciente->nombre1}} @if($adicionales->historia->paciente->nombre2!='(N/A)'){{$adicionales->historia->paciente->nombre2}}@endif </span></h4>
            <br>
            <h4 class="box-title"><b>Procedimiento: </b><span style="color: red;"> {{ rtrim($observacionesConcatenadas, '+') }} </span></h4>
          </div>

        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <form id="frm">
        <input type="hidden" value="{{$adicionales->id}}" name="id_proc_final" id="id_proc_final" />
        <div class="row">
          <div class="col-sm-6"></div>
          <div class="col-sm-6"></div>
        </div>

       

        <label style="font-size: 18;">Motivo - Doctor</label>
        <div>
          <textarea class="radius" style="width: 70%; height: 80px" type="text"  readonly>@if(isset($adicionales->evolucion_consulta))  {{$adicionales->evolucion_consulta->motivo}} @endif</textarea>
        </div>
        <label style="font-size: 18;">Evoluccion - Doctor</label>
        <div>
          <textarea class="radius" style="width: 70%; height: 80px" type="text"  readonly>@if(isset($adicionales->evolucion_consulta))   <?php echo strip_tags($adicionales->evolucion_consulta->cuadro_clinico); ?> @endif</textarea>
        </div>

       

        <div class="box box-warning">
          <h3><b>EVOLUCIONAR:</b></h3>
          <label style="font-size: 18;">Motivo</label>
          <div>
            <textarea class="radius" style="width: 70%; height: 80px" type="text" id="motivo" name="motivo"></textarea>
          </div>
          <label style="font-size: 18;">Evoluccion</label>
          <div>
            <textarea class="radius" style="width: 70%; height: 80px" type="text" id="evolucion" name="evolucion"></textarea>
            <button class="btn btn-primary bg-green" id="boton_enviar" onclick="guardar();" type="button">
              <i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Agregar
            </button>

            <input name='motivo1' id='motivo1' type='hidden' value="1">
          </div>
        </div>
        <div id="agregar_motivo" class="grid grid-cols-2 gap-4  mb-4"></div>

       
        @foreach($detalles as $val)
        @php

        $procedimiento_final = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $val->hc_id_procedimiento)->first();
        $nombre_individual = $procedimiento_final->procedimiento->nombre;
        @endphp
        <div id="filas2{{$val->id}}" class="box box-warning">
          <div class="box box-warning">
            <br>
            <div><b class="text-red">FECHA: </b> &nbsp;&nbsp;&nbsp;<b>{{date('Y-m-d', strtotime($val->created_at))}}</b></div>
            <div><b class="text-red">HORA: </b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>{{date('h:i:s', strtotime($val->created_at))}}</b></div>
            <div><b class="text-red">Responsable: &nbsp; </b><b> {{$user_ses}}</b></div>
            <div><b class="text-red">Procedimiento: </b> &nbsp;&nbsp; <b>{{$nombre_individual}}</b></div>
            <div><b>MOTIVO - EVOLUCION</b></div>

            <br>
            <textarea class="radius" style="width: 70%;" type="text" readonly>{{$val->motivo}}</textarea>
            <br>
            <br>
            <textarea class="radius" style="width: 70%;" type="text" readonly>{{$val->evolucion}}</textarea>

            <button style="width: 3%;" class="bg-red butoon" onclick="eliminar({{$val->id}})" type="button">
              <i class="fa fa-trash"></i>
            </button>

          </div>
        </div>
        @endforeach
        <!-- <div class="mb-4 flex justify-center">
          <button class="bg-green text-white py-2 px-8 font-semibold rounded" type="button" id="boton_enviar" onclick="guardar();">Guardar</button>
        </div> -->
        <div class="modal fade" id="modal_exito" tabindex="-1" aria-labelledby="modal_exito_titulo" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-body text-center">
                <p>
                <h2>Guardado con éxito</h2>
                </p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
              </div>
            </div>
          </div>
        </div>
    </div>
    </form>
  </div>
  </div>
  <br>
  </div>
  </div>
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>


<script type="text/javascript">
  function guardar() {
    var id = document.getElementById('motivo1').value;
    var d = new Date();
    var dateString = d.getUTCFullYear() + "/" + (d.getUTCMonth() + 1) + "/" + d.getUTCDate();
    let h = d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();
    let contenido = document.getElementById('motivo').value;
    let contenido2 = document.getElementById('evolucion').value;
    let motivo = document.getElementById('motivo').value;
    let evolucion = document.getElementById('evolucion').value;

    if (motivo.trim() === '') {
      alert('Agregar un motivo por favor');
      return;
    }

    if (evolucion.trim() === '') {
      alert('Agregar evolucion  por favor');
      return;
    }


    $('#boton_enviar').attr('disabled', true);

    //alert("ok");
    $.ajax({
      type: 'post',
      url: "{{route('enfermeria.guardar_evolucionar')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },

      datatype: 'json',
      data: $("#frm").serialize(),
      success: function(data) {

        $('#modal_exito').modal('show');

        // Anima la ventana modal
        $('#modal_exito').animate({
          'opacity': 1,
          'top': '50%',
        }, 500);
        //console.log(data);
        //alert(data);
        location.reload();

      },
      error: function(data) {

      }
    });
  }


  $(document).ready(function() {

    var newElement = document.createElement('div');
    newElement.innerHTML = fila;


    var agregarMotivo = document.getElementById('agregar_motivo');
    agregarMotivo.appendChild(newElement);

    document.getElementById('motivo').value = '';
    document.getElementById('evolucion').value = '';

    let aux = id;
    id++;
    document.getElementById('motivo1').value = id;
    return aux;
  });



  function eliminar(id) {

    var confirmacion = confirm("¿Estás seguro de eliminar este elemento?");


    if (confirmacion) {

      $.ajax({
        type: 'post',
        url: "{{route('enfermeria.evolucion_eliminar')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },

        datatype: 'json',
        data: {
          id: id,

        },
        success: function(data) {

          // Eliminar el elemento del DOM
          $('#filas2' + id).remove();
        },
        error: function(data) {


        }
      });
    }
  }
</script>

@endsection