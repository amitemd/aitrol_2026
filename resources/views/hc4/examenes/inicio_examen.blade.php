@extends('hc4.base')
@section('action-content')

<style type="text/css">

  .boton-1{
    font-size: 10px ;
    width: 20%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
   }

   .boton-2{
    font-size: 10px ;
    width: 60%;
    background-color: #004AC1;
    color: white;
    border-radius: 5px;
   }

   .color{
    font-size: 12px; 
    color: #004AC1; 
   }
   .titulo{
    font-family: 'Helvetica general' !important;
    border-bottom:  solid 1px #004AC1 !important;
   }
</style>
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="container-fluid">
  <div class="col-md-12">
    <div id="navbar" class="row"  style="z-index: 99999">
      <div class="col-lg-12 col-md-12" style="background: #004AC1;">
        <div class="row row_datos">
          <div class="col-md-5">
            <div class="row" onclick="cargar_pacientes_doctor()">
              <div class="col-12" style="padding: 5px 2px; padding-left: 5px; height: 240px;">
                <a href="#" class="boton calendario"  style="color: #004AC1; height: 230px">
                  <div class="row">
                    <div class="col-12" style="height: 42px; text-align: left;">
                      <img src="{{asset('/')}}hc4/img/bt_ca2.png" style="background-color: none;height: 46px;">
                      <span>AGENDA DEL D&Iacute;A</span>
                    </div>
                    <hr style="background-color: #004AC1;">
                    <div class="col-12" >
                      <div class="row">
                        <div class="col-md-6 col-12" style="padding: 5px;">
                          <div class="row">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1">&nbsp;</div>
                                <div class="col-10" style="text-align: left;">
                                  <span style="color: #004AC1; font-size: 14px;"><b>CALENDARIO DE AGENDA</b></span>
                                </div>
                              </div>
                              
                            </div>
                            <div class="col-12" style="height: 4px;">
                            </div>
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                  <div class="col-4">
                                    <p class="align-middle" style='text-align: center;margin:0;line-height: 1;color: #004AC1;'><span><b style="font-size: 30px;">{{date('d')}}</b></span><br><span style="line-height: 1; font-size: 16px; text-align: center"> @if(date('N') == 1) Lunes @elseif(date('N') == 2) Martes @elseif(date('N') == 3) Miercoles @elseif(date('N') == 4) Jueves @elseif(date('N') == 5) Viernes @elseif(date('N') == 6) Sabado @elseif(date('N') == 7) Domingo @endif</span></p>
                                  </div>
                                  <div class="col-6" style="text-align: left;">
                                    <div style="height: 8px;"></div>
                                    <span style="font-size:14px; text-align: left; color: #004AC1;">@if(date('m') == 1) Enero @elseif(date('m') == 2) Febrero @elseif(date('m') == 3) Marzo @elseif(date('m') == 4) Abril @elseif(date('m') == 5) Mayo @elseif(date('m') == 6) Junio @elseif(date('m') == 7) Julio @elseif(date('m') == 8) Agosto @elseif(date('m') == 9) Septiembre @elseif(date('m') == 10) Octubre @elseif(date('m') == 11) Noviembre @elseif(date('m') == 12) Diciembre @endif {{date('Y')}}</span>
                                  </div>
                              </div>
                            </div>
                            <hr style="height: 0px;">
                            <div class="col-12">
                              <div class="row">
                                <div class="col-1"></div>
                                <div class="col-10">
                                  <p style="font-size: 12px; text-align: left;">{{count($agenda_consultas)}} consultas agendadas <br> {{count($procedimiento_consultas)}} procedimientos agendados</p>
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
          <div class="col-md-7">
            <div class="row">
              <div class="col-3" style="padding: 5px 2px;">
                <a href="#" class="boton"  style="  height: 230px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;" onclick="cargar_horario_doctor()">
                    <div style="width: 100%;">
                      <img src="{{asset('/')}}hc4/img/reloj.png" style="width: 100px; ">
                    </div>
                    <br>
                    <br>
                    <br>
                    <div style="width: 100%;">
                      <p><b>HORARIO <br> LABORABLE</b></p>
                    </div>
                    <div style="width: 100%; color: #e88c07; font-size:12px;">
                      <p><b>Dr. {{ Auth::user()->nombre1}} {{ Auth::user()->apellido1}}</b></p>
                    </div> 
                  </div>
                </a>
              </div>
              <div class="col-3" style="padding: 5px 2px;">
                <a href="#" class="boton"  style="  height: 230px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;" onclick="cargar_historia_fecha()">
                    <div style="width: 100%;">
                      <img src="{{asset('/')}}hc4/img/listado.png" style="width: 100px; ">
                    </div>
                    <br>
                    <br>
                    <br>
                    <div style="width: 100%;">
                      <p><b>LISTADO DE <br>PACIENTES DEL D&Iacute;A</b></p>
                    </div>
                    <div style="width: 100%;  font-size:12px;">
                      <p style="text-align: center;"><b>{{count($consultas_todas)}}</b> Consultas <br> <b>{{count($procedimiento_todas)}}</b> Procedimientos</p>
                    </div> 
                  </div>
                </a>
              </div>
              <div class="col-3" style="padding: 5px 2px;">
                <a href="#" class="boton"  style="  height: 230px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF,#FFFFFF,#d1d1d1);border-radius: 10px;">
                  <div class="row" style="text-align: center;" onclick="cargar_ordenes_laboratorio()">
                    <div style="width: 100%;">
                      <img src="{{asset('/')}}hc4/img/lab2.png" style="width: 100px; ">
                    </div>
                    <br>
                    <br>
                    <br>
                    <div style="width: 100%;">
                      <p><b>LABORATORIO</b></p>
                    </div>
                    <div style="width: 100%;  font-size:12px;">
                      <p style="text-align: center;"><b>{{$ordenes_laboratorio}}</b> ORDENES</p>
                    </div> 
                  </div>
                </a>
              </div>
              <div class="col-3" style="padding: 5px 2px;  padding-right: 5px;">
                <div class="row">
                  <div class="col-12">
                    <a href="#" class="boton"  style="height: 105px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);border-radius: 10px;">
                      <div class="row" onclick="crear_editar_medicina()">
                        <div class="col-lg-4 col-12" style="padding:0; text-align: right;">
                          <img src="{{asset('/')}}hc4/img/med.png" style="width: 100%; max-width: 58px;margin-top: 12px;">
                        </div>
                        <div class="col-lg-8 col-12 cambiar" style="line-height: 0.2; text-align: left">

                          <hr style="background:rgba(0,0,0,0); margin-bottom: 7px;">
                          <br style="line-height: 0.7;"><br>
                          <span class="align-middle" style="font-size: 14px;line-height: 1; margin:0"><b>CREAR / EDITAR MEDICINAS</b> </span>
                        </div>
                      </div> 
                    </a>
                  </div>
                  <div class="col-12" style="height: 15px;"></div>
                  <div class="col-12">
                    <a href="#" class="boton"  style="height: 105px; color: #004AC1; background-image: linear-gradient(to right, #FFFFFF, #FFFFFF,#d1d1d1);border-radius: 10px;">
                      <div class="row" onclick="LanzaEvento()">
                        <div class="col-lg-4 col-12" style="padding:0; text-align: right;">
                          <img src="{{asset('/')}}hc4/img/exa1.png" style="width: 100%; max-width: 58px;margin-top: 12px;">
                        </div>
                        <div class="col-lg-8 col-12 cambiar" style="line-height: 0.2; text-align: left">

                          <hr style="background:rgba(0,0,0,0); margin-bottom: 7px;">
                          <br style="line-height: 0.7;"><br>
                          <span class="align-middle" style="font-size: 14px;line-height: 1; margin:0"><b>CREAR / EDITAR EXAMENES</b> </span>
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
  </div>
</div>

<section class="content" >	  
  <div class="container-fluid" id="info" style="padding-left: 0px; padding-right: 0px;">      
    <div class="col-12" style="font-family: Helvetica;color: white; margin-top: 5px; padding: 10px; border-radius: 8px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 10px">   
      
     <form method="POST" action="{{route('hc4.busqueda')}} ">
        {{ csrf_field() }}
        <div class="row">  
        <div class="col-5"> 
          <h1 style="font-size: 15px; margin:0;">
            <img style="width: 49px;" src="{{asset('/')}}hc4/img/hc_ima.png"> 
            <b>HISTORIA CL&Iacute;NICA POR PACIENTE</b>
          </h1>
        </div>
        <div class="col-2">
          <a class="btn btn-danger" onclick="cargar_nuevopaciente();" style="color:white; background-color:#004AC1 ; border-radius: 5px; border: 2px solid white;">  Agregar Nuevo Paciente</a>
        </div>
        <div class="col-5" style="padding-right: 0px;right: 0px; top: 5px"> 
          <div class="row">
            <div class="col-4">
              <div class="input-group">
                <input value="" type="text" class="form-control input-sm" name="apellidos" id="apellidos"   placeholder="Apellidos" style="text-transform:uppercase;"  >
              </div> 
            </div>
            <div class="col-4">
              <div class="input-group">
                <input value="" type="text" class="form-control input-sm" name="nombres" id="nombres"   placeholder="Nombres " style="text-transform:uppercase;" >
              </div>
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-info" style="color:white; background-color: #004AC1; border-radius: 5px; border: 2px solid white;"> <i class="fa fa-search" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;BUSCAR&nbsp;&nbsp;&nbsp;</button>
            </div> 
          </div>
        </div>
      </div>
     </form>
    </div>

    <div class="box box" style="border-radius: 8px;" id="area_trabajo">
      <div class="box-header with-border" style="background-color: #004AC1;color: white; font-size: 12px; padding: 8px;">RESULTADO DE LA B&Uacute;SQUEDA</div>
      <div class="box-body" style="border: 2px solid #004AC1">
        <div class="content">
          <div class="table-responsive">
            <table  id="contenido" class="table table-striped table-hover dataTable no-footer" cellspacing="0" width="100%" style="font-size: 12px;">
             
              <thead style="">
                <tr style=" ">
                    <th scope="col" class="color titulo" >Fecha</th>
                    <th scope="col" class="color titulo" >Hora</th>
                    <th scope="col" class="color titulo" >C&eacute;dula</th>
                    <th scope="col" class="color titulo" >Apellidos</th>
                    <th scope="col" class="color titulo" >Nombres</th>
                    <th scope="col" class="color titulo" >Procedimientos</th>
                    <th scope="col" class="color titulo" >Doctor</th>
                    <th scope="col" class="color titulo" >Seguro</th>
                    <th scope="col" class="color titulo" >Estado</th>
                    <th scope="col" class="color titulo" >Acci&oacute;n</th> 
                </tr>

              </thead>

              <tbody>

                @foreach ($pacientes as $pac)
                  @php
                  $agprocedimientos = collect([]);
                  if(!is_null($pac->agenda->last())){
                    $agprocedimientos= DB::table('agenda_procedimiento')
                    ->join('procedimiento','procedimiento.id','agenda_procedimiento.id_procedimiento')
                    ->select('agenda_procedimiento.*','procedimiento.nombre')
                    ->where('id_agenda',$pac->agenda->last()->id)->get();
                  }
                  @endphp
                  <tr>
                  <td class="color" >@if(!is_null($pac->agenda->last())) @if(!is_null($pac->agenda->last()->historia_clinica)){{substr($pac->agenda->last()->historia_clinica->created_at,0,10)}}@endif @endif</td>
                  <td class="color" >@if(!is_null($pac->agenda->last())) @if(!is_null($pac->agenda->last()->historia_clinica)){{substr($pac->agenda->last()->historia_clinica->created_at,11,5)}}@endif @endif</td>
                  <td class="color" >{{$pac->id}}</td>
                  <td class="color" >{{$pac->apellido1}} 
                   @if($pac->apellido2=='N/A')
                   @else{{$pac->apellido2}} 
                   @endif
                  </td>
                  <td class="color" >{{$pac->nombre1}} 
                   @if($pac->nombre2=='N/A')
                   @else{{$pac->nombre2}} 
                   @endif
                  </td>
                  <td class="color" >
                    @if(!$agprocedimientos->isEmpty())
                      {{$pac->agenda->last()->procedimiento->nombre}}
                      @if(!is_null($agprocedimientos))
                        @foreach($agprocedimientos as $agendaproc)
                           + {{$agendaproc->nombre}}
                        @endforeach
                      @endif
                    @endif
                  </td>
                  <td class="color" >@if(!is_null($pac->agenda->last())) @if(!is_null($pac->agenda->last()->doctor1)) {{$pac->agenda->last()->doctor1->nombre1}} {{$pac->agenda->last()->doctor1->apellido1}}
                  @endif @endif</td>
                  <td class="color">{{$pac->seguro->nombre}}</td>
                  <td class="color">
                    @if(!is_null($pac->agenda->last()))
                      @if(!is_null($pac->agenda->last()->historia_clinica)) 
                       @if(!is_null($pac->agenda->last()->historia_clinica->pentax)) 
                         @if($pac->agenda->last()->historia_clinica->pentax->estado_pentax == '0')
                          {{'EN ESPERA'}}
                         @elseif($pac->agenda->last()->historia_clinica->pentax->estado_pentax == '1')
                          {{'PREPARACION'}}
                         @elseif($pac->agenda->last()->historia_clinica->pentax->estado_pentax == '2')
                          {{'EN PROCEDIMIENTO'}}
                         @elseif($pac->agenda->last()->historia_clinica->pentax->estado_pentax == '3')
                          {{'RECUPERACION'}}
                         @elseif($pac->agenda->last()->historia_clinica->pentax->estado_pentax == '4')
                          {{'ALTA'}}
                         @elseif($pac->agenda->last()->historia_clinica->pentax->estado_pentax == '5')
                          {{'SUSPENDIDO'}}
                         @endif
                       @endif
                      @endif
                    @endif
                  </td>
                  <td><a class="btn btn-info boton-2" style="color: white;" href="{{route('nd.buscador', ['id_paciente' => $pac->id])}}">
                  Ver Detalle Completo</a></td>
                </tr> 
                @endforeach
              </tbody>
             </table>
          </div> 
        </div>
      </div>   
    </div>
  </div>


<script>
   function LanzaEvento()
   {
    alert("COMPRAR LICENCIA COMPLETA");
   } 

   function Text(string){
    var out = '';
    //Se añaden las letras validas
    var filtro = 'ABCDEFGHIJKLMNÑOPQRSTUVWXYZ" "';//Caracteres validos
  
    for (var i=0; i<string.length; i++)
       if (filtro.indexOf(string.charAt(i)) != -1) 
       out += string.charAt(i);
    return out;
   }

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

  function cargar_horario_doctor(){
    $.ajax({
      type: "GET",
      url: "{{route('obtener.horario_doctor')}}", 
      data: "",
      datatype: "html",
      success: function(datahtml){
        $("#info").html(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function cargar_ordenes_laboratorio(){
    $.ajax({
      type: "GET",
      url: "{{route('obtener.ordenes_lab')}}", 
      data: "",
      datatype: "html",
      success: function(datahtml){
        $("#info").html(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function crear_editar_medicina(){
    $.ajax({
      type: "GET",
      url: "{{route('agregar_edit.medicina')}}", 
      data: "",
      datatype: "html",
      success: function(datahtml){
        $("#info").html(datahtml);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function cargar_historia_fecha(){
    window.location.href = "{{route('busqueda_fecha')}}";
  }

  function cargar_pacientes_doctor(){
     window.location.href = "{{route('busqueda_pacientes_doctor')}}";
  }

  

</script>  
</section>
@endsection



