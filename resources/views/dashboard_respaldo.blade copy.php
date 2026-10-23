<?php
$rolUsuario = Auth::user()->id_tipo_usuario;
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SM | Sistema Médico</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  @include('layouts.header')
  <!-- Sidebar -->
  @include('layouts.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Sistema medico
      </h1>
      <ol class="breadcrumb">
        <!-- li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li-->
        <li class="active"><i class="fa fa-home"></i> Inicio</li>
      </ol>
    </section>
    <div class="modal fade" id="modal_revisar" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

    <!-- Main content -->
    <section class="content">

      <div class="container-fluid" >
          <div class="row">
            <div class="box box-primary">
              <div class="box-body">


                <!-- {{ rand(1, 4)}} -->
                @if(in_array($rolUsuario, array(1, 7)) == true)
                  <div class="form-group col-md-12">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                        @if($caducado != '[]' )
                        <div class="table-responsive col-md-12">
                          <h2 >Insumos Medicos Caducados / Caducar</h2>

                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Serie</th>
                                <th>Pedido</th>
                                <th>Bodega</th>
                                <th>Proveedor</th>
                                <th>Marca</th>
                                <th>Cantidad</th>
                                <th>Fecha Vencimiento</th>
                                <th>Estado</th>
                                <th>Acción</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($caducado as $value)
                                @if (isset($value->inventario) and $value->inventario->existencia >= 1)
                                <tr>
                                  <td >@if (isset($value->producto)) {{$value->producto->codigo}} @endif</td>
                                  <td >@if (isset($value->producto)){{$value->producto->nombre}} @endif</td>
                                  <td >@if (isset($value->producto)){{$value->producto->descripcion}} @endif</td>
                                  <td >@if (isset($value->producto)){{$value->serie}} @endif</td>
                                  <td >@if (isset($value->producto)){{$value->cabecera->numero_documento}} @endif</td>
                                  <td >
                                    @php /*
                                    @if (isset($value->producto)){{$value->inventario->bodega->nombre}} @endif

                                    */@endphp
                                  </td>
                                  <td >@if (isset($value->cabecera->pedido)){{$value->cabecera->pedido->proveedor->nombrecomercial}} @endif</td>
                                  <td >@if (isset($value->producto)){{$value->producto->marca->nombre}} @endif</td>
                                  <td >@if (isset($value->producto)){{$value->inventario->existencia}} @endif</td>
                                  <td >{{$value->fecha_vence}}</td>
                                  @if($value->fecha_vence < $fecha_hoy)
                                    @php
                                      /*$fecha_actual = date("Y-m-d");
                                      $n_fecha_v =  date("Y-m-d",strtotime($fecha_actual."+6 month"));
                                      $value->fecha_vence = $n_fecha_v;
                                      $value->save();*/
                                    @endphp
                                        <td style="background-color: red; color: white"> <b>Producto Caducado</b> </td>
                                  @else <td style="background-color: yellow"> <b>Producto Proximo a Caducar</b> </td>
                                  @endif
                                  <td ><a href="{{route('dashboard.modal_revisar',['id'=>$value->id])}}" class="btn btn-primary" data-toggle="modal" data-target="#modal_revisar"> <i class="fa fa-book" aria-hidden="true"></i> Revisar</a>
                                      </td>

                                </tr>
                                @endif
                              @endforeach
                            </tbody>
                          </table>
                          <div class="row">
                            <div class="col-md-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($caducado->currentPage() - 1) * $caducado->perPage())}} / {{count($caducado) + (($caducado->currentPage() - 1) * $caducado->perPage())}} de {{$caducado->total()}} registros
                                </div>
                            </div>
                            <div class="col-md-7 text-right">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{$caducado->links() }}
                                </div>
                                
                            </div>
                        </div>
                        @endif
                        @if($stock != '[]' )
                          <div class="table-responsive col-md-12">
                            <h2 >Insumos Medicos con Stock Minimo</h2>
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                              <thead>
                                <tr>
                                  <th >Codigo</th>
                                  <th >Nombre</th>
                                  <th >Descripción</th>
                                  <th >Stock Minimo</th>
                                  <th >Cantidad Total</th>

                                </tr>
                              </thead>
                              <tbody>
                              {{-- @foreach ($stock as $value)
                                <tr role="row" class="odd">
                                    <td class="sorting_1">{{$value->codigo}}</td>
                                    <td class="sorting_1">{{$value->nombre}}</td>
                                    <td class="sorting_1">{{$value->descripcion}}</td>
                                    <td class="sorting_1">{{$value->minimo}}</td>
                                    <td class="sorting_1" style="background-color: yellow">{{$value->cantidad}}</td>

                                </tr>
                              @endforeach --}}


                              @foreach ($inventario as $value)
                                <tr role="row" class="odd">
                                    <td class="sorting_1">{{$value->codigo}}</td>
                                    <td class="sorting_1">{{$value->nombre}}</td>
                                    <td class="sorting_1">{{$value->descripcion}}</td>
                                    <td class="sorting_1">{{$value->existencia_min}}</td>
                                    <td class="sorting_1" style="background-color: yellow">{{$value->existencia}}</td>

                                </tr>
                              @endforeach
                              </tbody>
                            </table>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                @endif
                @if(in_array($rolUsuario, array(1, 5)) == true)
                  <div class="form-group col-md-12">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                          <h2>Reagendamientos pendientes</h2>
                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Dia Asignado</th>
                                <th>Motivo</th>
                                <th>Doctor</th>
                                <th>Reagendar</th>
                              </tr>
                            </thead>
                            <tbody>
                            @foreach ($faltantes as $value)
                              <tr role="row" class="odd">
                                  <td class="sorting_1">{{$value->id_paciente}}</td>
                                  <td class="sorting_1">{{$value->pnombre1}} {{$value->papellido1}} {{$value->papellido2}}</td>
                                  <td class="sorting_1">{{$value->fechaini}}</td>
                                  <td class="sorting_1">{{$value->observaciones}}</td>
                                  <td class="sorting_1">DR. {{$value->dnombre1}} {{$value->dapellido1}}</td>
                                  <td class="sorting_1"><a href="javascript:gestion({{$value->id}},{{$value->id_doctor1}});" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">Reagendar</a></td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="row">
                        <div class="table-responsive col-md-12">
                          <h2>Agendamientos Creados por el Doctor</h2>
                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Dia Asignado</th>
                                <th>Motivo</th>
                                <th>Doctor</th>
                                <th>Reagendar</th>
                              </tr>
                            </thead>
                            <tbody>
                            @foreach ($agregados as $value)
                              <tr role="row" class="odd">
                                  <td class="sorting_1">{{$value->id_paciente}}</td>
                                  <td class="sorting_1">{{$value->pnombre1}} {{$value->papellido1}} {{$value->papellido2}}</td>
                                  <td class="sorting_1">{{$value->fechaini}}</td>
                                  <td class="sorting_1">{{$value->observaciones}}</td>
                                  <td class="sorting_1">DR. {{$value->dnombre1}} {{$value->dapellido1}}</td>
                                  <td class="sorting_1"><a href="javascript:gestion2({{$value->id}},{{$value->id_doctor1}});" class="btn btn-warning col-md-9 col-sm-9 col-xs-9 btn-margin">Reagendar</a></td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif
                @if(in_array($rolUsuario, array(2)) == false)
                <div class="form-group col-md-12">
                  @if(($ch == Array()) && ($ca == Array()) && ($pm == Array()))
                    <h2>No hay Cumpleañeros en los proximos Meses</h2>
                  @endif
                  @if($ch != Array())
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                          <h2>Cumpleañeros del Dia de Hoy</h2>
                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <tbody>
                            @foreach ($ch as $value)
                                <tr role="row" class="odd">
                                  <td class="sorting_1"> <h3>{{ $value->nombre1}} {{ $value->nombre2}} {{ $value->apellido1}} {{ $value->apellido2}}</h3></td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  @endif
                  @if($ca != Array())
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                          <h2>Cumpleañeros de este Mes</h2>
                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr role="row">
                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Foto</th>
                                <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Nombre</th>
                                <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Dia</th>
                              </tr>
                            </thead>
                            <tbody>
                            @foreach ($ca as $value)
                                <tr role="row" class="odd">
                                  <td><input type="hidden" name="carga" value="@if($value->imagen_url==' ') {{$value->imagen_url='avatar.jpg'}} @endif">
                                    <img src="{{asset('/avatars').'/'.$value->imagen_url}}"  alt="User Image"  style="width:70%; margin-left: 15%;" id="fotografia_usuario" >
                                  </td>
                                  <td class="sorting_1"> <h4>{{ $value->nombre1}} {{ $value->nombre2}} {{ $value->apellido1}} {{ $value->apellido2}}</h4></td>
                                  <td class="sorting_1"> <h4>{{ substr($value->fecha_nacimiento, 8)}}</h4></td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  @endif
                  @if($pm != Array())
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                      <div class="row">
                        <div class="table-responsive col-md-12">
                          <h2>Cumpleañeros del proximo Mes</h2>
                          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                              <tr role="row">
                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Foto</th>
                                <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Nombre</th>
                                <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Dia</th>
                              </tr>
                            </thead>
                            <tbody>
                            @foreach ($pm as $value)
                                <tr role="row" class="odd">
                                  <td><input type="hidden" name="carga" value="@if($value->imagen_url==' ') {{$value->imagen_url='avatar.jpg'}} @endif">
                                    <img src="{{asset('/avatars').'/'.$value->imagen_url}}"  alt="User Image"  style="width:70%; margin-left: 15%;" id="fotografia_usuario" >
                                  </td>
                                  <td class="sorting_1"> <h4>{{ $value->nombre1}} {{ $value->nombre2}} {{ $value->apellido1}} {{ $value->apellido2}}</h4></td>
                                  <td class="sorting_1"> <h4>{{ substr($value->fecha_nacimiento, 8)}}</h4></td>
                              </tr>
                            @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
                @endif
              </div>
            </div>
          </div>
      </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  @include('layouts.footer')

<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script type="text/javascript">


jQuery(document).ready(function() {
        jQuery('#modal_revisar').on('hidden.bs.modal', function(e) {
            jQuery(this).removeData('bs.modal');
            jQuery(this).find('.modal-content').empty();
        })
    })
    jQuery(document).ready(function() {
        jQuery('#modal_revisar').on('hidden.bs.modal', function(e) {
            jQuery(this).removeData('bs.modal');
            jQuery(this).find('.modal-content').empty();
        })
    })

function gestion(id, doctor){


@foreach ($faltantes as $value)

  VariableJS = <?php echo $value->id; ?>;
  if(id==VariableJS){
    //alert(VariableJS);
    $.ajax({
        type: 'get',
        url: '{{ route('agenda.edit2_pre', ['id' => $value->id, 'doctor' => $value->id_doctor1])}}',
        success: function(data){
          //alert(data);
          if(data=='ok'){
            location.href ="{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => $value->id_doctor1])}}";
          }else{
            alert("Ya se gestionó esta cita");
            document.location.reload();
          }

        }
    });

  }


@endforeach



}

function gestion2(id, doctor){


@foreach ($agregados as $value)

  VariableJS = <?php echo $value->id; ?>;
  if(id==VariableJS){
    //alert(VariableJS);
    $.ajax({
        type: 'get',
        url: '{{ route('agenda.edit2_pre', ['id' => $value->id, 'doctor' => $value->id_doctor1])}}',
        success: function(data){
          //alert(data);
          if(data=='ok'){
            location.href ="{{ route('agenda.edit2', ['id' => $value->id, 'doctor' => $value->id_doctor1])}}";
          }else{
            alert("Ya se gestionó esta cita");
            document.location.reload();
          }

        }
    });

  }


@endforeach



}

</script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
