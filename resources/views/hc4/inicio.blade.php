@extends('hc4.base')
@section('action-content')

<style type="text/css">

  .boton-1{
    font-size: 10px ;
    width: 20%;
    background-color: #124574;
    color: white;
    border-radius: 5px;
   }

   .boton-2{
    font-size: 10px ;
    width: 60%;
    background-color: #124574;
    color: white;
    border-radius: 5px;
   }

   .color{
    font-size: 12px;
    color: #124574;
   }
   .color2{
    color: white !important;
    background-color:  #124574 !important;
   }
   .titulo{
    font-family: 'Helvetica general' !important;
    border-bottom:  solid 1px black !important;
   }
   .oculto{
    display: none;
   }

   .plantilla{
      padding-top: 14px;
   }

   @media (max-width: 500px) {
    .text_2{
      font-size: 12px;
    }
    .text_2 b{
      font-size: 12px;
    }

    .image_baner{
      width: 50px !important;
    }
    .plantilla{
      padding-top: 5px;
    }
    .boton_doctor{
      font-size: 11px;
    }

    .estadisticos{
      padding-top: 5px;
    }
    .color{
      font-size: 11px;
    }
  }
</style>
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

<div class="container-fluid">
  <div class="col-md-12">
    <div id="navbar" class="row"  style="z-index: 99999">
      <div class="col-lg-12 col-md-12" style="background: #124574;">
        <div class="row row_datos">
          <div class="col-lg-5 col-sm-12 col-12">
            <div class="row">
              <div class="col-12" style="padding: 5px 2px; padding-left: 5px; height: 240px;">
                <a onclick="cargar_pacientes_doctor();" class="boton calendario"  style="color: #124574; height: 230px">
                  <div class="row">
                    <div class="col-12" style="height: 42px; text-align: left;color: #124574;">
                      <img src="{{asset('/')}}hc4/img/bt_ca2.png" style="background-color: none;height: 46px;">
                      <span>@lang('hc4.agendaDia')</span>
                    </div>
                    <hr style="background-color: #124574;">
                    <div class="col-12" >
                      <div class="row">
                        <div class="col-md-6 col-12" style="padding: 5px;color:#124574;">
                          <div class="row">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1">&nbsp;</div>
                                <div class="col-10" style="text-align: left;">
                                  <span style="color: #124574; font-size: 14px;"><b>@lang('hc4.calendarioAgenda')</b></span>
                                </div>
                              </div>
                            </div>
                            <div class="col-12" style="height: 4px; ">
                            </div>
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                  <div class="col-4">
                                    <p class="align-middle" style='text-align: center;margin:0;line-height: 1;color: #124574;'><span><b style="font-size: 30px;">{{date('d')}}</b></span><br><span style="line-height: 1; font-size: 16px; text-align: center"> @if(date('N') == 1) @lang('hc4.lunes') @elseif(date('N') == 2) @lang('hc4.martes') @elseif(date('N') == 3) @lang('hc4.miercoles') @elseif(date('N') == 4) @lang('hc4.jueves') @elseif(date('N') == 5) @lang('hc4.viernes') @elseif(date('N') == 6) @lang('hc4.sabado') @elseif(date('N') == 7) @lang('hc4.domingo') @endif</span></p>
                                  </div>
                                  <div class="col-6" style="text-align: left;">
                                    <div style="height: 8px;"></div>
                                    <span style="font-size:14px; text-align: left; color:#124574;">@if(date('m') == 1) @lang('hc4.enero') @elseif(date('m') == 2) @lang('hc4.febrero') @elseif(date('m') == 3) @lang('hc4.marzo') @elseif(date('m') == 4) @lang('hc4.abril') @elseif(date('m') == 5) @lang('hc4.mayo') @elseif(date('m') == 6) @lang('hc4.junio') @elseif(date('m') == 7) @lang('hc4.julio') @elseif(date('m') == 8) @lang('hc4.agosto') @elseif(date('m') == 9) @lang('hc4.septiembre') @elseif(date('m') == 10) @lang('hc4.octubre') @elseif(date('m') == 11) @lang('hc4.noviembre') @elseif(date('m') == 12) @lang('hc4.diciembre') @endif {{date('Y')}}</span>
                                  </div>
                              </div>
                            </div>
                            <hr style="height: 0px;">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                <div class="col-10">
                                  <p style="font-size: 12px; text-align: left;">{{count($agenda_consultas)}} @lang('hc4.consultasAgendadas') <br> {{count($procedimiento_consultas)}} @lang('hc4.procedimientosAgendados')</p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-8 cambiar" style="line-height: 1;">
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <div class="col-lg-7 col-sm-12 col-12">
            <div class="row">
              <div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a  onclick="cargar_horario_doctor();" class="boton"  style="  height: 230px; color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div style="width: 100%;">
                      <img src="{{asset('/')}}hc4/img/reloj.png" style="width: 100px; ">
                    </div>
                    <br>
                    <br>
                    <br>
                    <div style="width: 100%; color: #124574">
                      <p class="text_2"><b >@lang('hc4.horario') <br> @lang('hc4.laborable')</b></p>
                    </div>
                    <div style="width: 100%; color: #e88c07; font-size:12px;">
                      <p><b>Dr. {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</b></p>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a onclick="cargar_listado_paciente();" class="boton"  style="  height: 230px; color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div style="width: 100%;">
                      <img src="{{asset('/')}}hc4/img/listado.png" style="width: 100px; ">
                    </div>
                    <br>
                    <br>
                    <br>
                    <div style="width: 100%;color: #124574;">
                      <p class="text_2" style="margin-bottom: 5px"><b>@lang('hc4.listadoPacientesDia')</b></p>
                    </div>
                    <div style="width: 100%;  font-size:12px;color:#124574;">
                      <p style="text-align: center;"><b>{{count($consultas_todas)}}</b> @lang('hc4.consultas') <br> <b>{{count($procedimiento_todas)}}</b> @lang('hc4.procedimientos')</p>
                    </div>
                  </div>
                </a>
              </div>
              <!--div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a onclick="cargar_ordenes_laboratorio();" class="boton"  style="  height: 230px; color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div style="width: 100%;">
                      <img src="{{asset('/')}}hc4/img/lab2.png" style="width: 100px; ">
                    </div>
                    <br>
                    <br>
                    <br>
                    <div style="width: 100%; color: #124574;">
                      <p><b>LABORATORIO</b></p>
                    </div>
                    <div style="width: 100%;  font-size:12px;color:#124574;">
                      <p style="text-align: center;"><b>{{$ordenes_laboratorio}}</b> ORDENES</p>
                    </div>
                  </div>
                </a>
              </div-->
              <div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a  onclick="cargar_ordenes_laboratorio();" class="boton"  style="  height: 110px; color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div class="col-md-5 col-xs-6" style="width: 100%;padding-left: 0;padding-right: 5px;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/lab2.png" style="width: 95px;">
                    </div>
                    <div  class="col-md-7 col-xs-6" style="width: 100%;color: #124574;padding-left: 0;">
                      <p class="text_2" style="font-size: 15px"><b>@lang('hc4.laboratorio-')</b></p>
                      <p class="text_2" style="font-size: 15px"><b>{{$ordenes_laboratorio}}</b> @lang('hc4.ordenes')</p>
                    </div>
                  </div>
                </a>
                <a  onclick="examenes_favoritos();" class="boton"  style="margin-top: 5px;  height: 112px; color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row plantilla"  style="text-align: center;">
                    <div class="col-md-4" style="width: 100%;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/exams_favoritos.png" style="width: 75px;">
                    </div>
                    <div class="col-md-8" style="width: 100%; color: #124574;">
                      <p style="font-size: 13px" class="text_2"><b>@lang('hc4.examenesFavoritos')</b></p>
                    </div>
                  </div>
                </a>
              </div>

              <div class="col-sm-3 col-6" style="padding: 5px 2px;">
                <a  onclick="cargar_crear_editar();" class="boton"  style="  height: 110px; color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;">
                    <div class="col-md-6 col-xs-6" style="width: 100%;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/med.png" style="width: 95px;">
                    </div>
                    <div  class="col-md-6 col-xs-6" style="width: 100%;color: #124574;">
                      <p class="text_2" style="font-size: 15px"><b>@lang('hc4.crear-') / @lang('hc4.editarmedicinas-')</b></p>
                    </div>
                  </div>
                </a>
                <a  onclick="plantilla_proc();" class="boton"  style="margin-top: 5px;  height: 112px; color: #124574; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row plantilla"  style="text-align: center;">
                    <div class="col-md-4" style="width: 100%;">
                      <img class="image_baner" src="{{asset('/')}}hc4/img/exa1.png" style="width: 55px;">
                    </div>
                    <div class="col-md-8" style="width: 100%; color: #124574;">
                      <p style="font-size: 13px" class="text_2"><b>@lang('hc4.plantillasdeprocedimientos-')</b></p>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $("#fecha").change(function(){
    alert($("#fecha").val());
  });
  function enviar_enter(e){
    //alert('entra1');
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla==13){
        buscador_paciente_fecha();
      };
  }
  function cambio_fecha(){
    alert('cambio');
  }


</script>


<section class="content" >
  <div class="container-fluid" id="info" style="padding-left: 0px; padding-right: 0px;">
    <div class="col-md-12" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 8px; background-color: #124574; margin-bottom: 10px">
      <form method="POST" id="form_buscador" action="{{route('historia_clinica.reporte_hc')}}">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-lg-3 col-12">
            <h1 style="font-size: 15px; margin:0; position: relative;top: 25%;">
              <img style="width: 49px;" src="{{asset('/')}}hc4/img/hc_ima.png">
              <b>@lang('hc4.historiaClinicaPaciente')</b>
            </h1>
          </div>
          <div class="col-lg-8 col-12">
            <div class="row" style="padding-top: 5px">
              <div class="col-lg-6 col-12" >
                  <div class="row">
                    <div class=" col-md-6 " >
                      <div class="row">
                        <div class="col-3 "><label for="fecha" class="col-md-3 control-label" style="padding:0px;">@lang('hc4.desde')</label></div>
                        <div class="col-9">
                          <div class="form-group">
                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                              <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="desde_inicio" id="desde_inicio" class="form-control form-control-sm datetimepicker-input" data-target="#datetimepicker1"  />
                                <input type="hidden" id="fecha" name="fecha" onchange="cambio_fecha()">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class=" col-md-6 " >
                      <div class="row">
                        <div class="col-3"><label for="fecha" class="col-md-3 control-label" style="padding:0px;">@lang('hc4.hasta')</label></div>
                        <div class="col-9">
                          <div class="form-group">
                            <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                              <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="hasta_fin" id="hasta_fin" class="form-control form-control-sm datetimepicker-input" data-target="#datetimepicker2" />
                                <input type="hidden" id="fecha_hasta" name="fecha_hasta">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <!--<div class="col-md-2 col-sm-6 col-12" style="text-align: center;">
                <a class="btn btn-danger" onclick="cargar_nuevopaciente();" style=" background-color:#9b9b9b ; border-radius: 5px; border: 2px solid white;">  Agregar Nuevo Paciente
                </a>
              </div>
              <div class="col-2"></div>-->
              <div class="col-lg-6 col-12" >
                <div class="row">
                  <div class="form-group col-lg-4 col-md-6 col-xs-6" >
                    <div class="row">
                      <label for="proc_consul" class="col-md-3 control-label">@lang('hc4.tipo')</label>
                      <div class="col-lg-9 col-md-8">
                        <select class="form-control form-control-sm input-sm" name="proc_consul" id="proc_consul" onchange="buscador_paciente_fecha()">
                          <option @if($proc_consul=='2') selected @endif value="2" >@lang('hc4.todos')</option>
                          <option @if($proc_consul=='0') selected @endif value="0" >@lang('hc4.consultas')</option>
                          <option @if($proc_consul=='1') selected @endif value="1" >@lang('hc4.procedimientos')</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-lg-8 col-md-6 col-xs-6" >
                    <div class="row">
                      <label for="espid" class="col-lg-4 col-md-3 control-label">@lang('hc4.especialidad')</label>
                      <div class="col-md-8">
                        <select class="form-control form-control-sm input-sm" name="espid" id="espid" onchange="buscador_paciente_fecha()">
                          <option value="">@lang('hc4.todos') ...</option>
                        @foreach($especialidades as $especialidad)
                          <option @if($especialidad->id==$id_especialidad) selected @endif value="{{$especialidad->id}}">{{$especialidad->nombre}}</option>
                        @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-lg-3 col-md-6 col-xs-6" >
                <div class="row">
                  <label for="id_doctor1" class="col-md-3 control-label">@lang('hc4.doctor')</label>
                  <div class="col-lg-9 col-md-8">
                    <select class="form-control form-control-sm input-sm" name="id_doctor1" id="id_doctor1" onchange="buscador_paciente_fecha()">
                      <option value="">@lang('hc4.seleccione') ...</option>
                    @foreach($doctores as $doctor)
                      <option @if($doctor->id==$id_doctor1) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
                    @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group col-lg-3 col-md-6 col-xs-6" >
                <div class="row">
                  <label for="id_seguro" class="col-md-3 control-label">@lang('hc4.seguro')</label>
                  <div class="col-lg-9 col-md-8">
                    <select class="form-control form-control-sm input-sm" name="id_seguro" id="id_seguro" onchange="buscador_paciente_fecha()">
                      <option value="">@lang('hc4.seleccione') ...</option>
                    @foreach($seguros as $seguro)
                      <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                    @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 col-md-6 form-group" >
                <div class="input-group">
                  <input value="{{$request->apellidos}}" type="text" class="form-control form-control-sm " name="apellidos" id="apellidos"   placeholder="@lang('hc4.apellidos')" style="text-transform:uppercase;" onkeypress="enviar_enter(event);" >
                </div>
              </div>
              <div class="col-lg-2 col-md-6 form-group" >
                <div class="input-group">
                  <input value="{{$request->nombres}}" type="text" class="form-control form-control-sm " name="nombres" id="nombres"   placeholder="@lang('hc4.nombres') " style="text-transform:uppercase;" onkeypress="enviar_enter(event);"  >
                </div>
              </div>

              
            </div>
            <div class="row">
              <div class="form-group col-lg-3 col-md-6 col-xs-6" >
                <div class="row">
                  <label for="cedula" class="col-md-3 control-label">Cedula</label>
                  <div class="col-lg-9 col-md-8">
                    <div class="input-group">
                      <input value="{{$request->cedula}}" type="text" class="form-control form-control-sm " name="cedula" id="cedula"   placeholder="Cedula" style="text-transform:uppercase;" onkeypress="enviar_enter(event);"  >
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-2 col-md-4 form-group col-xs-4" >
                <button type="button" onclick="buscador_paciente_fecha();" class="btn btn-danger" style="color:white; background-color: #124574; border-radius: 5px; border: 2px solid;"> <i class="fa fa-search" aria-hidden="true">
                </i> &nbsp;@lang('hc4.buscar-')&nbsp;</button>

                <button type="button" onclick="enviar_formulario_revision();" id="exp_rev" class="oculto" >
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="box box" style="border-radius: 8px;" id="area_trabajo">

    </div>
  </div>
</section>

<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>


<script type="text/javascript">

  function enviar_formulario_revision(){
    //form_buscador
    //formaction="{{route('hc4controller.exportar_revision')}}"

    //formulario.submit();
    $('#form_buscador').attr('action', '{{route("hc4controller.exportar_revision")}}');//Setting form action to "success.php" page
    $( "#form_buscador" ).submit();// Submitting form
    $('#form_buscador').attr('action', '{{route("historia_clinica.reporte_hc")}}');
  }

  function descargar_hc_reporte() {
    $( "#form_buscador" ).submit(); // Click on the checkbox
  }
            $(function () {
                $('#datetimepicker1').datetimepicker({
                    format: 'YYYY/MM/DD',
                    defaultDate: '{{$fecha1}}'
                });

                $('#datetimepicker2').datetimepicker({
                    format: 'YYYY/MM/DD',
                    defaultDate: '{{$fecha2}}',
                    //minDate: '{{$fecha2}}',
                  });

                $("#datetimepicker1").on("change.datetimepicker", function (e) {
                    $('#datetimepicker2').datetimepicker('minDate', e.date);
                    buscador_paciente_fecha();
                });
                $("#datetimepicker2").on("change.datetimepicker", function (e) {
                    buscador_paciente_fecha();
                });

                $(".clickable-row").click(function() {
                    window.location = $(this).data("href");
                  });
            });

            $(function(){

            });

            function fechacalendario() {
                var dato = document.getElementById('datetimepicker1').value;
                $('#enviar_fecha').click();
            }


</script>

<script>
    //Funcion que permite cargar las consultas y procedimeinto del doctor separados
    function cargar_pacientes_doctor(){
      $.ajax({
        type: "GET",
        url: "{{route('busqueda_pacientes_doctor')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }


    //Funcion que permite cargar el horario laboral del Doctor
    function cargar_horario_doctor(){
      $.ajax({
          type: 'get',
          url:"{{route('obtener.horario_doctor')}}",
          success: function(data){
           $("#area_trabajo").html(data);
          },
          error: function(data){
            console.log(data);
          }
      });
    }

    //Funcion para cargar todo el listado de pacientes del dia
    //Tanto para consulta como para procedimiento
    function cargar_listado_paciente(){
      $.ajax({
        type: "GET",
        url: "{{route('busqueda_fecha')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }

    //Funcion para cargar todas las ordenes de laboratorio del dia
    function cargar_ordenes_laboratorio(){
      $.ajax({
        type: "GET",
        url: "{{route('obtener.ordenes_lab_nuevo')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }

    //Funcion para buscar las Ordenes de Procedimientos
    function buscar_ordenes_procedimientos(){
      $.ajax({
        type: "GET",
        url: "{{route('buscar_hc4.ordenes_procedimiento')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }

    //Muestra el listado de medicinas
    function cargar_crear_editar(){
      $.ajax({
        type: "GET",
        url: "{{route('editar.medicina_hc4')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }


    function plantilla_proc(){
      $.ajax({
        type: "GET",
        url: "{{route('hc4/plantilla_proc.index')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }


    function examenes_favoritos(){
      $.ajax({
        type: "GET",
        url: "{{route('hc4_examenes.favoritos')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }

    //Busqueda por fechas desde-fecha_hasta y por apellido y nombre del Paciente
    function buscador_paciente_fecha(){
        $.ajax({
          type: 'post',
          url:"{{route('hc4.busqueda')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_buscador").serialize(),
          success: function(data){
            $("#area_trabajo").html(data);
            //console.log(data);
          },
          error:  function(){
            alert('error al cargar');
          }
        });
    }




    //Function para agregar el nuevo paciente
    function cargar_nuevopaciente(){
      $.ajax({
        type: "GET",
        url: "{{route('agregar.paciente_hc4')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }





    function editar_medicina(){
      $.ajax({
        type: "GET",
        url: "{{route('editar.medicina_hc4')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
       });
    }


  </script>

  <script type="text/javascript">
    $('#agenda_semana').click(function(){

      $.ajax({
          type: 'get',
          url:"{{route('hc4.calendario_fullcalendar')}}",
          success: function(data){
            //console.log(data);
            //alert("ok");
            $("#modificar").html(data);
            //$("#agenda_semana").addClass('oculto');

          },
          error: function(data){
            console.log(data);
          }
      });

    });
  </script>

  <script type="text/javascript">

    @if(!is_null($request['variable']))
      @if($request['variable'] == 1)
        cargar_ordenes_laboratorio();
      @endif
    @endif

    @if(!is_null($request['variable2']))
      @if($request['variable2'] == 2)
        cargar_listado_paciente();
      @endif
    @endif

    @if(!is_null($request['variable3']))
      @if($request['variable3'] == 3)
        cargar_crear_editar();
      @endif
    @endif

    @if(!is_null($request['variable4']))
      @if($request['variable4'] == 4)
        cargar_pacientes_doctor();
      @endif
    @endif

    @if(!is_null($request['variable5']))
      @if($request['variable5'] == 5)
        editar_medicina();
      @endif
    @endif

  </script>

  <script type="text/javascript">
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  </script>


  <script type="text/javascript">

    window.onload = function(){
      @if($request['variable'] == 1)
      @else
        @if($request['variable2'] == 2)
        @else
          @if($request['variable3'] == 3)
          @else
            cargar_pacientes_doctor();
          @endif
        @endif
      @endif
    };

  </script>

</section>
@endsection
