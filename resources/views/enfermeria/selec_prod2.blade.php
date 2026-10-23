@extends('enfermeria.baseinsumo2')
@section('action-content')
<style type="text/css">
  .alerta_correcto {
    position: absolute;
    z-index: 9999;
    top: 200px;
    right: 100px;
  }

  .ui-autocomplete {
    overflow-x: hidden;
    max-height: 200px;
    width: 1px;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    float: left;
    display: none;
    min-width: 160px;
    _width: 160px;
    padding: 4px 0;
    margin: 2px 0 0 0;
    list-style: none;
    background-color: #fff;
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
    *border-right-width: 2px;
    *border-bottom-width: 2px;
  }

  .btn-danger {
    display: none;
  }
</style>
<!-- Main content -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">

  <div id="alerta_datos" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{trans('eenfermeria.ObservaciónGuardadaCorrectamente')}}
  </div>

  <div id="alerta_datos_vh" class="alert alert-success alerta_correcto alert-dismissable" role="alert" style="display:none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span id="msn_guardado_vh"></span> {{trans('eenfermeria.GuardadaCorrectamente')}}
  </div>

  <div class="col-md-12 p-10" style="padding: 20px; z-index:  1;">
    <center>
      <label class="label label-danger " style="font-size: 18px;"> {{trans('eenfermeria.PLANILLAGUARDADA')}}</label>
      <br>
      <label class="label label-danger " style="font-size: 18px;">{{trans('eenfermeria.NOSEPUEDEMODIFICAR')}} </label>
    </center>

    </div>

  <div class="box">
    <div class="box-header">
      <div class="row">
          <div class="col-md-12" style="text-align: right;">
            <a type="button" href="{{route('enfermeria.insumos_uso',['id'=>$agenda->id])}}" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-arrow-left">{{trans('edisponibilidad.Regresar')}}</span>
            </a>
          </div>
        <div class="col-md-12">
            <div class="col-md-10"><h4 class="box-title"><b>{{trans('eenfermeria.Paciente')}}: </b><span style="color: red;">{{$agenda->id_paciente}}-{{$agenda->paciente->apellido1}} @if($agenda->paciente->apellido2!='(N/A)'){{$agenda->paciente->apellido2}}@endif {{$agenda->paciente->nombre1}} @if($agenda->paciente->nombre2!='(N/A)'){{$agenda->paciente->nombre2}}@endif </span></h4></div>
            <div class="col-md-5"><h4 class="box-title"><b>Seguro: </b> {{$agenda->seguro->nombre}}</h4></div><div class="col-md-5"><h4 class="box-title"><b>{{trans('etodos.Fecha')}}: </b> {{$agenda->fechaini}}</h4></div>
        </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      {{--<div class="box box-success">
        <div class="box-header">
          <label class="col-md-8 control-label" style="padding-left: 0px;"><b>{{trans('eenfermeria.Productos')}}</b></label>
        </div>
        <div class="box-body">
          <div class="form-group col-md-4" style="padding: 1px;">
            <input id="nombre" type="text" class="form-control" name="nombre" value="{{old('nombre')}}" style="text-transform:uppercase;">
            <input type="hidden" id="codigo" name="codigo">
          </div>
          <div class="form-group col-md-6">
            <button class="btn btn-success btn-sm col-md-2" onClick="enviar();"><span class="glyphicon glyphicon-plus"> {{trans('ecamilla.Agregar')}}</span></button>
          </div>
        </div>
      </div>--}}
      <div class="box box-info">
          <div class="box-header">
            <div class="row">
              <div class="col-md-6" style="text-align: left;">
              <b>{{trans('eenfermeria.Observaciones')}}</b>
              </div>
              <div class="col-md-6" style="text-align: right;">
               <a class="btn btn-info btn-xs" href="{{route('anestesiologia.mostrar',['id'=>$id_proc])}}"> <i class="fa fa-medkit"></i> {{trans('eenfermeria.RécordAnestésico')}}</a>
              </div>
            </div>


        </div>
       {{-- <div class="box-body">
          <form id="hc_observacion">
            <input type="hidden" name="id_historia2" value="{{$hcid}}">
            <textarea name="observaciones_enfermeria" id="observaciones_enfermeria" onchange="guardar_observacion();" style="width: 100%">{{$agenda->historia_clinica->observaciones_enfermeria}}</textarea>
          </form>
          <button class="btn btn-success btn-xs" onclick="guardar_msn();"><span class="glyphicon glyphicon-floppy-disk">&nbsp;</span>Guardar</button>
        </div>--}}

      </div>
      <!--div class="box box-success">
        <div class="box-header">
          <b>Tipos </b>
        </div>
        <div class="box-body">
          @foreach($tipos as $tipo)
          <button class="btn btn-primary" onClick="productos({{$tipo->id}});">{{$tipo->nombre}}</button>
          @endforeach
        </div>
        <div></div>
        <div class="col-md-12" id="tipo"></div>
      </div-->
      <div class="col-md-12" id="listado"></div>

    </div>
    {{--<div class="box box-info">
      <div class="row">
        <div class="col-md-10">
          <h3><b>{{trans('eenfermeria.PlantillaProcedimiento')}}: </b></h3>
        </div>
        <div class="col-md-4">
          <div class="col-md-10">
            <input type="hidden" class="form-control input-sm" id="id_plantilla" name="id_plantilla">
            <input type="text" class="form-control input-sm" id="nom_plantilla" name="nom_plantilla">
          </div>
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-success" onclick="mostrar_planilla();"> {{trans('erol.Buscar')}}</button>
          <input type="hidden" id="id_plantilla_2" name="id_plantilla_2">
        </div>
        <div class="col-md-2">
          <button type="button" onclick="guardar_plantilla_basica()" class="btn btn-success oculto">{{trans('econtrolsintomas.Guardar')}}</button>
        </div>
        <div class="col-md-12" id="detalle"></div>
      </div>
    </div>--}}
    <!-- /.box-body -->
</section>
    <!-- /.content -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ('/js/calendario/moment.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
  $(document).ready(function() {
    listado_prod();
    setTimeout(() => {
      removebutton()
    }, 1500);
    // removebutton();
  });

  const removebutton = () => {
    console.log('entra aquii');
    let btns = document.querySelectorAll(".btn-danger");
    for (let i = 0; i < btns.length; i++){
      console.log(btns[i])
      btns[i].remove()
    }
  }

  function productos(id_producto) {
    console.log();
    $.ajax({
      type: "GET",
      url: "{{url('enfermeria/productos')}}/"+id_producto+"/{{$id_proc}}",
      datatype: "html",
      success: function(datahtml){

          $("#tipo").html(datahtml);

      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }
  function enviar() {
    var nombre= $('#codigo').val();
    //console.log(nombre);
    $.ajax({
      type: 'GET',
      url: "{{url('enfermeria/serie_enfermeroget')}}/" + nombre + "/{{$id_proc}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#codigo_enviar" + nombre).serialize(),
      success: function(data) {
        //console.log(data);
        if (data == 'No se encontraron resultados') {
          $('#agregar_respuesta' + nombre).html('No se encontraron resultados');
          swal({
            title: "No se encontraron resultados",
            icon: "warning",
            type: 'error',
            buttons: true,
          });
          $("#recibir" + nombre).focus();
        } else if (data == 'caducado') {
          swal({
            title: "No se puede usar el medicamento, se encuentra caducado",
            icon: "warning",
            type: 'error',
            buttons: true,
          });
        } else if (data != 'ok') {
          swal({
            title: data,
            icon: "warning",
            type: 'error',
            buttons: true,
          });
        } else {

              $("#msn_guardado_vh").text($("#nombre").val());
              $("#alerta_datos_vh").fadeIn(1000);
              $("#alerta_datos_vh").fadeOut(4000);
              listado_prod();
            }
        },
        error: function(data){
            console.log(data);
        }
    });
  }

  function listado_prod(){

    $.ajax({
      type: "GET",
      url: "{{route('enfermeria.listado_prod',['id_proc'=>$id_proc])}}",
      datatype: "html",
      success: function(datahtml){
          //console.log(datahtml);
          $("#listado").html(datahtml);

      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  $("#nombre").autocomplete({
        source: function( request, response ) {

            $.ajax({
                url:"{{route('enfermeria.nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    //console.log(data);
                    response(data);
                }
            })
        },
        minLength: 2,
        select: function(data, ui){
            //console.log("++"+ui.item.codigo);
            $('#codigo').val(ui.item.codigo);
            //enfermeria_nombre_2(ui.item.codigo);
        }
    } );

    $("#nombre").change( function(){
        $.ajax({
          type: 'post',
          url:"{{route('enfermeria.nombre2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          datatype: 'json',
          data: $("#nombre"),
          success: function(data){
              $('#codigo').val(data);
              console.log(data);
          },
          error: function(data){
              console.log(data);
          }
        })
      });

    function enfermeria_nombre_2 (codigo){
        $.ajax({
          type: 'post',
          url:"{{route('enfermeria.nombre2')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          datatype: 'json',
          data: $("#nombre"),
          success: function(data){
              $('#codigo').val(data);
          },
          error: function(data){
              console.log(data);
          }
        })
    }

    function guardar_observacion(){

      $.ajax({
          type: 'post',
          url: "{{route('enfermeria.guardar_observacion')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#hc_observacion").serialize(),
          success: function(data){
              console.log(data);
              $("#alerta_datos").fadeIn(1000);
              $("#alerta_datos").fadeOut(3000);

          },
          error: function(data){
              console.log(data);
          }
      });
    }

    function mostrar_planilla() {
      var plantilla = document.getElementById("id_plantilla").value;
      //console.log(plantilla);
      $.ajax({
        type: 'get',
        url: "{{ url('insumos/plantillas/item_lista2')}}/" + plantilla+"/{{$hcid}}/2",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        success: function(data) {
          $('#detalle').empty().html(data);
          $(".oculto").show();
        },
        error: function(data) {
          console.log(data);
        }
      });
    }

    $("#nom_plantilla").autocomplete({
    source: function(request, response) {

      $.ajax({
        url: "{{route('enfermeria.nombre_plantilla')}}",
        headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        data: {
          term: request.term
        },
        dataType: "json",
        type: 'post',
        success: function(data) {
          response(data);
          console.log(data);

        }
      })
    },
    minLength: 2,
    select: function(data, ui){
        //console.log("select",ui.item.id);
        $('#id_plantilla').val(ui.item.id);
        $('#id_plantilla_2').val(ui.item.id);

    }
  });

  $("#nom_plantilla").change(function() {
    $.ajax({
      type: 'post',
      url: "{{route('enfermeria.nombre_plantilla2')}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#nom_plantilla"),
      success: function(data) {
        console.log(data);
        if (data != '0') {
          $('#id_plantilla').val(data.id);
          $('#id_plantilla_2').val(data.id);

        }
      },
      error: function(data) {

      }
    })
  });

  function guardar_plantilla_basica(){

    Swal.fire({
        title: 'Desea aplicar el Kit Básico: '+$('#nom_plantilla').val(),
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        denyButtonText: 'No Enviar',
        showLoaderOnConfirm: true,
      }).then((result) => {

        if (result.isConfirmed) {
          $.ajax({
              type: 'post',
              url: "{{route('enfermeria.vhguardar_plantilla_basica')}}",
              headers: {
                  'X-CSRF-TOKEN': $('input[name=_token]').val()
              },
              datatype: 'json',
              data: $('#plantilla_basica').serialize() + "&hc_procedimientos={{$id_proc}}",
              success: function(data) {
                  console.log(data);
                  if(data.estado == "ERROR"){
                    alertas('error','Error..',data.msn);

                  }else{
                    alertas('success', 'Exito....', data.msj)
                  }

                  setTimeout(function() {
                    location.reload();
                  }, 2500)
              },
              error: function(data) {
                  console.log(data);
              }
          });

        }
      });




  }

  const alertas = (icon, title, msj)=>{
    Swal.fire({
      icon: `${icon}`,
      title: `${title}`,
      text: `${msj}`,
    })
  }

  </script>

@endsection
