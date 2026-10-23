@extends('contable.nuevo_rol_pago.base')
@section('action-content')
@php
  $tipo_usuario  = Auth::user()->id_tipo_usuario;
@endphp
<style type="text/css">

 .nomina>li>a{
   background-color: #00bfff;
  
 }
 .nomina>li>a:hover{
   background-color: #0086b3;
 }

</style>
<div class="modal fade" id="visualizar_estado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="col-md-7">
        <h5><b> Valores Correspondientes a Vacaciones </b></h5>
      </div>
      <!--div class="col-md-3 text-right">
            <a href="#" class="btn btn-success btn-gray">{{trans('nomina.crear_rol')}}</a>
          </div-->
      <div class="col-md-1 text-right">
        <button onclick="goBack()" class="btn btn-default btn-gray">
          <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('nomina.regresar')}}
        </button>
      </div>
    </div>
    <div class="row head-title">
      <!--div class="col-md-12 cabecera">
          <label class="color_texto" for="title">{{trans('nomina.buscador_rol_pago')}}</label>
        </div-->
    </div>
    <div class="box-body dobra">
      <form method="POST" id="buscad_rol_pago" action="{{route('nuevo_rol.acumula_valor_vacacion')}}">
        {{ csrf_field() }}

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="anio">{{trans('nomina.anio')}}</label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <select id="anio" name="anio" class="form-control">
            <option value="">{{trans('nomina.seleccione')}}...</option>
            @for($i = 2019; $i <= 2030; $i++ ) <option @if($anio==$i) selected @endif value="{{$i}}">{{$i}}</option>
              @endfor
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="mes">{{trans('nomina.empleado')}}</label>
        </div>


        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="empleado" name="empleado" placeholder="Buscar Empleado" value="{{$empleado}}">
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="anio">Sucursal</label>
        </div>
        @php $cantidadSucursales = count($sucursales); @endphp
        <div class="form-group col-md-4 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <select id="sucursal" name="sucursal" class="form-control">
            @if($cantidadSucursales > 1)<option value="">Seleccione...</option>@endif
            @foreach ($sucursales as $sucursal)
            <option value="{{$sucursal->id}}" @if($id_sucursal==$sucursal->id) selected @endif>{{$sucursal->codigo_sucursal}}-{{$sucursal->nombre_sucursal}}</option>.
            @endforeach
          </select>
        </div>
        <div class="col-xs-2">
          <button type="submit" class="btn btn-default btn-gray" onclick="changeExcel('b')">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>
        <input type="hidden" name="excel" id="excel" value="0">
        <div class="col-xs-2">
          <button type="submit" class="btn btn-default btn-gray" onclick="changeExcel('e')">
            <span class="fa fa-file" aria-hidden="true"></span> {{trans('contableM.reporte')}}
          </button>
        </div>
        @if($tipo_usuario == 1)
        @if($id_sucursal != null)
        <div class="col-xs-2">
          <!--a type="button" class="btn btn-default btn-gray" href="{{route('nuevo_rol.ct_rh_acumulados_vacaciones',['anio' => $anio, 'sucursal' => $id_sucursal])}}">
            <span class="fa fa-file" aria-hidden="true"></span> Cargar
          </a-->
          <button type="button" class="btn btn-default btn-gray" onclick="cargar_vacaciones();">Cargar</button>
        </div>
        @endif
        @endif


      </form>
    </div>
    <div class="row head-title">
      <!--div class="col-md-12 cabecera">
          <label class="color_texto" >{{trans('nomina.listado_rol')}}</label>
        </div-->
    </div>
    <div class="box-body dobra" style="padding-left: 0px;padding-right: 0px;">
      <div class="form-group col-md-12" style="padding-left: 0px;padding-right: 0px;">
        <div class="form-row">
          <div class="col-md-7">
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                  <form class="form-horizontal" role="form" id="form_pagos_valor_vacacion">
                    <div class="table-responsive col-md-12">
                      Todos <input type="checkbox" name="ctodos" id="ctodos" onclick="sel_todos()">
                      <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info" style="width: 100%">
                        <thead>
                          <tr class='well-dark'>
                            <th width="5%">Sel.</th>
                            <th width="10%">{{trans('nomina.cedula')}}</th>
                            <th width="20%">{{trans('nomina.empleado')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('nomina.periodo')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('nomina.dlaborados')}}</th>
                            <th width="10%" style="text-align: center;">{{trans('nomina.valor')}}</th>
                            <th width="10%" style="text-align: right;">{{trans('nomina.valor_pagado')}}</th>
                            <th width="10%" style="text-align: center;"> Valor a Aplicar</th>
                            <th width="25%">{{trans('nomina.acciones')}}</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php
                          $tvalor=0;
                          $tvalorpago=0;
                          @endphp

                          @foreach ($ValorVacaciones as $value)
                          @php
                          $valor_por_cruzar=0;
                          $valor_por_cruzar = $value->valor - $value->valor_pago;
                          $valor_por_cruzar = number_format($valor_por_cruzar,4);
                          @endphp
                          <tr>
                            <td> @if($valor_por_cruzar != 0) <input type="checkbox" name="ValorVacaciones[]" value="{{$value->id}}"> @else <input type="hidden" value="{{$value->id}}"> @endif</td>
                            <td style="text-align: left;">{{$value->id_user}}</td>
                            <td>{{$value->apellido1}} {{$value->apellido2}} <br> {{$value->nombre1}} {{$value->nombre2}}</td>
                            <td style="text-align: center;">{{substr($value->inicio,0,4)}}-{{substr($value->fin,0,4)}}</td>
                            <td style="text-align: center;">{{$value->dias_laborados}}</td>
                            <td> ${{$value->valor}} </td>
                            <td> ${{$value->valor_pago}} </td>
                            <td>
                              <input size="8" id="valor_pago" type="text" name="valor_pago[{{$value->id}}]" value="{{$valor_por_cruzar}}" autocomplete="off">
                            </td>
                            <td>

                              <div class="btn-group col-md-12">
                                <button type="button" class="btn btn-success" style="background-color: #00bfff;" onclick="cargar_detalle('{{ $value->id_nomina }}','{{ $value->inicio }}','{{ $value->fin }}');">{{trans('nomina.detalle')}}</button>
                                <button type="button" class="btn btn-primary dropdown-toggle" style="background-color: #00bfff;" data-toggle="dropdown">
                                  <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu nomina" role="menu">
                                  <li><a href="#" onclick="cargar_pago_detalle('{{ $value->id }}');"> Pagos </a></li>
                                  <li><a href="#" onclick="cargar_detalle_prestamos('{{ $value->id }}');"> Prestamos <br> Pendientes</a></li>
                                  <li><a href="#" onclick="pdf_comprobante('{{$value->id}}')"> Comprobante </a></li>
                                </ul>
                              </div>

                            </td>
                          </tr>
                          @php

                          $tvalor += $value->valor;
                          $tvalorpago += $value->valor_pago;
                          @endphp

                          @endforeach

                        </tbody>
                        <tfoot>

                          <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style=" text-align: center;"><b>TOTAL</b>
                            </td>
                            <td style="text-align: center;">${{$tvalor}}</td>
                            <td style="text-align: center;">${{$tvalorpago}}</td>
                            <td></td>
                          </tr>
                        </tfoot>

                      </table>
                    </div>
                </div>
                <div class="row">
                  <div class="row head-title">
                    <div class="col-md-12 cabecera">
                      <label class="color_texto">PAGO DE VALOR VACACION</label>
                    </div>
                  </div>


                  <div class="form-group col-md-6 col-xs-3">
                    <label for="fecha_creacion" class="col-md-6 texto">Fecha:</label>
                    <div class="col-md-6">
                      <input id="fecha_creacion" type="date" class="form-control validar" name="fecha_creacion" value="">
                    </div>
                  </div>

                  <div class="form-group col-md-6 col-xs-3">
                    <label for="tipo_pago" class="col-md-6 texto">Tipo de Pago</label>
                    <div class="col-md-6">
                      <select class="form-control validar" style="width:150px" id="tipo_pago" name="tipo_pago" onchange="obtener_seleccion_vacacion()">
                        <option value="">Seleccione...</option>
                        @foreach($tipo_pago_rol as $value)
                        <option value="{{$value->id}}">{{$value->tipo}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div id="num_cuenta" class="form-group col-md-6 col-xs-3">
                    <label for="numero_cuenta" class="col-md-6 texto ">N# Cuenta</label>
                    <div class="col-md-6">
                      <input id="numero_cuenta" type="text" class="form-control" name="numero_cuenta" value="" autocomplete="off">
                    </div>
                  </div>

                  <div id="id_banco" class="form-group col-md-6 col-xs-3">
                    <label for="banco" class="col-md-6 texto">Banco</label>
                    <div class="col-md-6">
                      <select class="form-control" style="width:150px" id="banco" name="banco">
                        <option value="">Seleccione...</option>
                        @foreach($lista_banco as $value)
                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div id="fech_cheq" class="form-group col-md-6 col-xs-3">
                    <label for="fecha_cheque" class="col-md-6 texto">Fecha Cheque:</label>
                    <div class="col-md-6">
                      <input id="fecha_cheque" type="date" class="form-control " name="fecha_cheque" value="{{ old('fecha_cheque') }}">
                    </div>
                  </div>

                  <div id="num_che" class="form-group col-md-6 col-xs-3">
                    <label for="numero_cheque" class="col-md-6 texto">N # Cheque:</label>
                    <div class="col-md-6">
                      <input id="numero_cheque" type="text" class="form-control " name="numero_cheque" value="{{ old('numero_cheque') }}" onkeypress="return isNumberKey(event)" autocomplete="off">
                    </div>
                  </div>

                  <div id="id_cuenta_saliente" class="form-group col-md-6 col-xs-3">
                    <label for="cuenta_saliente" class="col-md-6 texto">Cuenta Saliente</label>
                    <div class="col-md-6">
                      <select class="form-control " id="cuenta_saliente" name="cuenta_saliente" style="width:150px">
                        <option value="">Seleccione...</option>
                        @foreach($bancos as $value)
                        <option value="{{$value->cuenta_mayor}}">{{$value->nombre}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div id="conce1" class="form-group col-md-6 col-xs-8">
                    <label for="concepto1" class="col-md-6 texto">Concepto:</label>
                    <div class="col-md-6">
                      <input id="concepto1" type="text" class="form-control " name="concepto1" value="PAGO DE VALOR VACACION" autocomplete="off">
                    </div>
                  </div>


                  <div class="form-group col-md-12">
                    <a class="btn btn-info" name="boton_guardar" id="boton_guardar" onclick="guardar_pago_vacacion(event);">Generar Pago</a>
                  </div>

                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-5" id="detalle_contenido">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
  $('#visualizar_estado').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });
  function changeExcel(l) {
    if (l === 'e') {
      document.getElementById("excel").value = 1;
    } else {
      document.getElementById("excel").value = 0;
    }

  }

  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': true,
    'info': false,
    'autoWidth': false
  })

  $('#example2det').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': false,
    'info': false,
    'autoWidth': false
  })

  function cargar_detalle(id_nomina, inicio, fin) {

    $.ajax({
      url: "{{ asset('nomina_acum/valor_vacacion') }}/" + id_nomina + "/" + inicio + "/" + fin,
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      type: 'GET',
      success: function(data) {

        $("#detalle_contenido").empty().html(data);

      },
      error: function(data) {
        Swal.fire(`{{trans('ERROR!')}}`, '', 'error');
      }
    });
  }

  function cargar_vacaciones() {

    $.ajax({
      url: "{{route('nuevo_rol.ct_rh_acumulados_vacaciones',['anio' => $anio, 'sucursal' => $id_sucursal])}}",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      type: 'GET',
      success: function(data) {

        location.reload();

      },
      error: function(data) {
        Swal.fire(`{{trans('ERROR!!')}}`, '', 'error');
      }
    });

  }

  function sel_todos() {
    // Get the checkbox
    var checkBox = document.getElementById("ctodos");

    // If the checkbox is checked, display the output text
    if (checkBox.checked == true) {
      $(':checkbox').prop('checked', true);
    } else {
      $(':checkbox').prop('checked', false);
    }
  }

  function obtener_seleccion_vacacion() {

    var id_tipo = $("#tipo_pago").val();
    if (id_tipo == 1) { //ACREDITACION

      document.getElementById("id_cuenta_saliente").style.display = 'block';
      document.getElementById("id_banco").style.display = 'block';
      document.getElementById("num_cuenta").style.display = 'block';
      document.getElementById("num_che").style.display = 'none';
      document.getElementById("fech_cheq").style.display = 'none';

      $('#banco').val("");
      $('#numero_cuenta').val("");
      $('#numero_cheque').val("");
      $('#fecha_cheque').val("");

    } else if (id_tipo == 2) { //EFECTIVO

      document.getElementById("id_banco").style.display = 'none';
      document.getElementById("num_cuenta").style.display = 'none';
      document.getElementById("num_che").style.display = 'none';
      document.getElementById("fech_cheq").style.display = 'none';

      $('#numero_cheque').val("");
      $('#numero_cuenta').val("");
      $('#banco').val("");
      $('#cuenta_saliente').val("");

    } else if (id_tipo == 3) { //CHEQUE

      document.getElementById("id_banco").style.display = 'none';
      document.getElementById("num_cuenta").style.display = 'none';
      document.getElementById("num_che").style.display = 'block';
      document.getElementById("fech_cheq").style.display = 'block';

      $('#numero_cheque').val("");
      $('#numero_cuenta').val("");
      $('#banco').val("");
      $('#cuenta_saliente').val("");
      $('#fecha_cheque').val("");

    }

  }

  function guardar_pago_vacacion(e) {
    e.preventDefault();
    let fecha_asiento = document.getElementById('fecha_creacion').value;
    let tipo_pago = document.getElementById('tipo_pago').value;
    let cuenta_saliente = document.getElementById('cuenta_saliente').value;
    let fecha_cheque = document.getElementById('fecha_cheque').value;
    let numero_cheque = document.getElementById('numero_cheque').value;
    let numero_cuenta = document.getElementById('numero_cuenta').value;
    let banco = document.getElementById('banco').value;
    let concepto1 = document.getElementById('concepto1').value;

    let msj = "";
    if (fecha_asiento == "") {
      msj += "Seleccione la Fecha de Creacion <br>";
    }
    if (tipo_pago == "") {
      msj += "Seleccione el Tipo de Pago <br>";
    }
    if (cuenta_saliente == "") {
      msj += "Seleccione la Cuenta Saliente <br>";
    }
    if (tipo_pago == '3') { //CHEQUE
      if (fecha_cheque == "") {
        msj += "Seleccione la Fecha del Cheque <br>";
      }
      if (numero_cheque == "") {
        msj += "Escriba el Numero de Cheque <br>";
      }
    }

    if (tipo_pago == '1') { //ACREDITACION 
      if (numero_cuenta == "") {
        msj += "Escriba el Numero de Cuenta <br>";
      }
      if (banco == "") {
        msj += "Seleccione el banco <br>";
      }
    }
    if (concepto1 == "") {
      msj += "Ingrese el concepto <br>";
    }


    if (msj != "") {
      alertas('error', 'Error!..', msj)
    } else {
      var confirmar = confirm('Desea Realizar el Pago del Valor Vacacion');
      if (confirmar) {
        $.ajax({
          type: 'post',
          url: "{{ route('nuevo_rol.pago_valor_vacacion') }}",

          headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
          },
          datatype: 'json',
          data: $("#form_pagos_valor_vacacion").serialize(),
          success: function(data) {
            alertas(data.respuesta, data.titulos, data.msj);
            if (data.respuesta == 'success') {
              setTimeout(function() {
                location.reload();
              }, 1000);
            }
          },
          error: function(data) {
            Swal.fire(`{{trans('ERROR!')}}`, '', 'error');
          }
        });
      }
    }
  }

  function cargar_pago_detalle(id_ct_rh_acumulado) {

    $.ajax({
      url: "{{ asset('nomina_acum/valor_vacacion/detalle_pago_vacaciones_empleado/') }}/" + id_ct_rh_acumulado,
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      type: 'GET',
      success: function(data) {

        $("#detalle_contenido").empty().html(data);

      },
      error: function(data) {
        Swal.fire(`{{trans('ERROR!')}}`, '', 'error');
      }
    });
  }

  function cargar_detalle_prestamos(id_ct_rh_acumulado) {

    $.ajax({
      url: "{{ asset('nomina_acum/vacaciones/listado_prestamos/por_empleado') }}/" + id_ct_rh_acumulado,
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      type: 'GET',
      success: function(data) {

        $("#detalle_contenido").empty().html(data);

      },
      error: function(data) {
        //Swal.fire(`{{trans('El empleado no tiene prestamos pendientes')}}`, '', 'error');
        console.log(data);
        alertas('error', 'Error!..', msj)
      }
    });
  }

  function pdf_comprobante(id) {
    window.open("{{url('comprobante/pago/valor_vacacion')}}/" + id, "_blank");
  }
</script>

@endsection