@extends('contable.ventas.base')
@section('action-content')
<style type="text/css">
  table tr td {
    font-size: 10px;
  }

  th {
    font-size: 12px;
  }

  .sticky {
    position: fixed;
    top: 0;
    left: 100px;
    width: 100%;
    max-height: 125%;

  }

  .sticky+.content {
    padding-top: 55px;
  }

  @media screen and (max-width:700px) {
    /* reglas CSS */

    .cambiar {
      display: none !important;
    }

    .sticky {
      position: relative !important;
    }

  }
</style>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<div class="modal fade" id="buscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content">

    </div>
  </div>
</div>

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Ventas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Facturación OMNI</li>

    </ol>
  </nav>

  <div class="box">
    <div class="box-header">
    </div>
    <div class="box-body">
      <form method="POST" id="reporte_master">
        {{ csrf_field() }}
        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="col-md-2 control-label">Desde</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="col-md-2 control-label">Hasta</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group col-md-6 col-xs-6">
          <label for="nombres" class="col-md-3 control-label">Paciente</label>
          <div class="col-md-9">
            <div class="input-group">
              <input @if(isset($request['nombres'])) value="{{$request['nombres']}}" @endif type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="APELLIDOS - NOMBRES" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
              </div>
            </div>
          </div>
        </div>

        <!--div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label class="col-md-2 control-label">Caja</label>
          <div class="col-md-9">
              <select class="form-control input-sm" name="caja" id="caja">
               <option value="" >Todas</option>
               <option @if($request['caja'] == "Torre 1") selected="selected" @endif  value="Torre 1" >Torre 1</option>
               <option @if($request['caja'] == "Torre 2") selected="selected" @endif value="Torre 2">Torre 2</option>
              </select>
          </div>
        </div-->
        @if(isset($doctores))
        <div class="form-group col-md-3 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
          <label class="col-md-2 control-label">Doctor</label>
          <div class="col-md-9">
            <select class="form-control input-sm" name="doctor" id="doctor">
              <option value="">Seleccione ...</option>
              @foreach($doctores as $doctor)
              <option @if($doctor->id==$request->doctor) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
              @endforeach
            </select>
          </div>
        </div>
        @endif
        <!--
        <div class="form-group col-md-3 col-xs-5" style="padding-left: 0px;padding-right: 0px;">
          <label class="col-md-2 control-label">Tipo</label>
          <div class="col-md-9">
            <select class="form-control input-sm" name="tipo" id="tipo">
              <option value="">Seleccione ...</option>
              <option @if($request->tipo=='4') selected @endif value="4">VISITAS</option>
              <option @if($request->tipo=='1') selected @endif value="1">PROCEDIMIENTOS</option>
            </select>
          </div>
        </div>-->
        <div class="form-group col-md-3">
          <label for="tipo" class="control-label col-md-3">Tipo</label>

          <div class="col-md-9">
            <select name="tipo" id="tipo" class="form-control">
              <option @if($request->tipo==null) selected="selected" @endif value="">Seleccione...</option>
              <option value="0">Consulta</option>
              <option @if($request->tipo==1) selected="selected" @endif value="1">Procedimientos</option>
              <option @if($request->tipo==3) selected="selected" @endif value="3">Hospitalizados</option>
              <option @if($request->tipo==4) selected="selected" @endif value="4">Visitas</option>
            </select>
          </div>
        </div>
        <div class="form-group col-md-3">
          <label for="tipo" class="control-label col-md-3">Hospital</label>

          <div class="col-md-9">
            <select name="omni" id="omni" class="form-control">
              <option value="">Seleccione...</option>
              <option @if($request->omni=="SI") selected="selected" @endif value="SI">SI</option>
              <option @if($request->omni=="NO") selected="selected" @endif value="NO">NO</option>

            </select>
          </div>
        </div>
        <div class="form-group col-md-3 px-0">
          <label class="col-md-3 control-label">Seguro</label>
          <div class="col-md-9">
            <select class="form-control" name="id_seguro[]" id="id_seguro" multiple="multiple">
              <option value="">Seleccione ...</option>
              @foreach($seguros as $seguro)
              @if(is_array($request->id_seguro))
              <option @if(in_array($seguro->id, $request->id_seguro)) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
              @else
              <option value="{{$seguro->id}}">{{$seguro->nombre}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group col-md-3 px-0">
          <label class="col-md-5 control-label">Procedimientos</label>
          <div class="col-md-7">
            <select class="form-control" name="procedimientos[]" id="procedimientos" multiple="multiple">
              <option value="">Seleccione ...</option>
              @foreach($procedimiento as $procedimientos2)
              @if(is_array($request->procedimientos2))
              <option @if(in_array($procedimientos2->id, $request->procedimiento)) selected @endif value="{{$procedimientos2->id}}">{{$procedimientos2->nombre}}</option>
              @else
              <option value="{{$procedimientos2->id}}">{{$procedimientos2->nombre}}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group col-md-3 px-0">
          <label class="col-md-5" for=""> ID Agenda</label>
          <div class="col-md-7">
          <select class="form-control select" name="id_agenda[]" multiple="multiple">
              @if(isset($request->id_agenda))
                @foreach($request->id_agenda as $key=>$v)
                  
                  <option selected="selected" value="{{$v}}">{{$v}}</option>
                @endforeach
              @endif
          </select>
          </div>
        </div>
        <div class="col-md-12">
            &nbsp;
        </div>




        <div class="form-group col-md-3 col-xs-3" style="text-align: left;">
          <button type="submit" formaction="{{ route('ventas.omni')}}" class="btn btn-primary btn-sm" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Buscar&nbsp;</span></button>
        </div>
        <div class="form-group col-md-12" style="text-align: center;">
          <div class="col-md-12" style="text-align: center;">
            <label>INFORME DE USO</label>
          </div>

          <button formaction="{{route('ventas.getReportUses')}}" class="btn btn-info btn-gray" formtarget="_blank"> <i class="fa fa-file-pdf-o"></i> </button>
          <button formaction="{{route('ventas.getReportUsesExcel')}}" class="btn btn-info btn-gray" formtarget="_blank"> <i class="fa fa-file-excel-o"></i> </button>

        </div>

        <div class="form-group col-xs-12 text-center" id="ok" style="z-index: 999">

          <div class="col-md-12" style="text-align: center;">
            <label>ACCIONES</label>
          </div>


          <button type="button" class="btn btn-warning btn-gray btn_fact1 ">
            <i class="fa fa-medkit" aria-hidden="true"></i>&nbsp;&nbsp;Facturar Insumos
          </button>


          <button type="button" class="btn btn-success btn-gray btn_fact2">
            <i class="fa fa-stethoscope" aria-hidden="true"></i>&nbsp;&nbsp;Facturar Equipos
          </button>
          <!-- Only Dr. Robles -->
          @if($empresa->id=="1307189140001" || $empresa->id=="1314490929001" || $empresa->id=="32222222222")
           
          <button type="button" class="btn btn-default btn-gray btn_fact3">
            <i class="fa fa-user-md" aria-hidden="true"></i>&nbsp;&nbsp;Honorarios Medicos
          </button>
          @endif

        </div>

        <!--<div class="form-group col-md-2 col-xs-2" >
          
          <a type="button" href="{{route('cierrecaja.imprimir_excel')}}" class="btn btn-primary btn-sm" formtarget="_blank" >
            <span class="glyphicon glyphicon-download-alt" aria-hidden="true" style="font-size: 16px">&nbsp;Excel&nbsp;</span></a
          <button type="submit" formaction="{{route('cierrecaja.imprimir_excel')}}" class="btn btn-primary btn-sm" id="boton_buscar">
          <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px">&nbsp;Excel&nbsp;</span></button>
        </div>-->
      </form>

    @include('contable.ventas.detalles_omni')
    </div>
  </div>
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#example2').DataTable({
      'paging': false,
      'lengthChange': true,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'sInfoEmpty': true,
      'sInfoFiltered': true,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      }
    });
  });
  $(function() {
    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha}}',

    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',
    });

    $("#fecha").on("dp.change", function(e) {
      $('#fecha_hasta').data("DateTimePicker").minDate(e.date);
    });

    $("#fecha_hasta").on("dp.change", function(e) {});


    $(function() {

      $(document).ready(function() {
        $('#id_seguro').select2();
        $('#procedimientos').select2();
      });
    });

    $(".btn_fact2").click(function(e) {
      $("#tipo_fact").val(2);
      $("#form").submit();

    });
    $(".btn_fact1").click(function(e) {
      //$("#tipo_fact").val(1);
      console.log($("#tipo_fact").val());
      //alert($("#tipo_fact").val());
      $("#form").submit();

    });
    $('select').select2({
      tags: true
      /* insertTag: function (data, tag) {
        // Insert the tag at the end of the results
        data.push(tag);
      } */
    });

    $(".btn_fact3").click(function(e) {
      $("#tipo_fact").val(3);
      var noexisteProcedimiento = false;
      $('.facturar').each(function(i, obj) {
        //total_pagos = parseFloat(total_pagos) + parseFloat($(this).val());
        if ($(this).prop('checked')) {
          var copro = $(this).find('.t_fact').val();
          if (copro == 1) {
            noexisteProcedimiento = true;
          }
        }
      });
      // console.log(noexisteProcedimiento);
      if (noexisteProcedimiento) {
        $("#form").submit();
        console.log('submit');
      } else {
        console.log('no submit');

        Swal.fire({
          html: 'Esta seguro que desea facturar <br/>' +
            'Honorarios Médicos a ' +
            '{{$empresa->nombrecomercial}}',
          showCloseButton: true,
          showCancelButton: true,
          focusConfirm: false,
          confirmButtonText: 'Si',
          confirmButtonAriaLabel: 'Si',
          cancelButtonText: 'No',
          cancelButtonAriaLabel: 'Thumbs down'
        }).then((result) => {
          if (result.value) {
            /*Swal.fire(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )*/
            $("#form").submit();
          }
        });
      }


    });

    $('body').on('click', '#allItems', function() {
      if ($(this).prop('checked')) {

        //$('.fact_id').val(1);
        $('.facturar').each(function(i) {
          //aqui preguntar si esta disabled
          if ($(this).prop('disabled')) {

          } else {
            $(this).prop("checked", true);
            $(this).next().attr('name', 'id_agenda[]');
            $(this).next().next().attr('name', 'paciente[]');
          }

        });
        /*
        $('.facturars').attr('name', 'id_agenda[]');
        $('.pacientes').attr('name', 'paciente[]');*/
      } else {
        $('.facturar').prop("checked", false);
        //$('.fact_id').val(0);
      }
    });

    $('body').on('change', '.facturar', function() {
      //console.log($(this));
      if ($(this).prop('checked')) {
        //console.log("entra");
        $(this).parent().find('.facturars').attr('name', 'id_agenda[]');
        $(this).parent().find('.pacientes').attr('name', 'paciente[]');
      } else {
        $(this).parent().find('.facturars').attr('name', '');
        $(this).parent().find('.pacientes').attr('name', '');
      }
    });

  });
  window.onscroll = function() {
    myFunction()
  };

  var navbar = document.getElementById("ok");
  var sticky = navbar.offsetTop;

  function myFunction() {
    if (window.pageYOffset >= sticky) {
      navbar.classList.add("sticky")
    } else {
      navbar.classList.remove("sticky");
    }

  }
</script>
@endsection