@extends('limpieza_desinfeccion.base')
@section('action-content')

<style>
  .btn {
    font-size: 15px;
    font-weight: bold;
  }

  .salas:hover {
    background-color: #4192C2;
  }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- Main content -->
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<section class="content">
  <div class="box">
    <div class="box-header">
      <div class="box-header">
        <form id="form_fecha" method="POST">
          {{ csrf_field() }}
          <div class="form-group col-md-4 col-xs-6">
            <label for="fecha" class="col-md-3 control-label">Desde</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off" placeholder="AAAA/MM/DD" value="">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group col-md-4 col-xs-6">
            <label for="hasta" class="col-md-3 control-label">Hasta</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="hasta" id="hasta" autocomplete="off" placeholder="AAAA/MM/DD" value="">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle"></i>
                </div>
              </div>
            </div>
          </div>
        </form>

      </div>


      <div class="box-body">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

          <table id="example2" class="table table-bordered table-hover dataTable">
            <tbody>
              @foreach($sala as $sala)
             
              <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                  <a href="javascript:void($('#demo').click());" id="boton_salas{{$sala->id}}" class="btn btn-primary" style="width: 100%; height: 60px; line-height: 40px; font-size: 20px; text-align: center" onClick="index_limpieza({{$sala->id}});">{{$sala->nombre_sala}}
                  </a>

                </div>
              </div>
             
              @endforeach
            </tbody>
          </table>
          
      <div class="box">
        <div class="box-header">
        <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="demo">
              <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body" style="display: block;"> <div class="col-md-12" id="index_form"></div></div>
      </div>
         
        </div>
      </div>
    </div>
  </div>
</section>


<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>


<script type="text/javascript">
  $(document).ready(function() {
    index_limpieza(10);
  });

  $(function() {
    $('#fecha').datetimepicker({
      useCurrent: false,
      format: 'YYYY/MM/DD',
      @if($fecha == '0')
      defaultDate: '{{date("Y/m/d")}}'
      @else
      <?php
      $fecha  = substr($fecha, 0, 10);
      $fecha2 = date('Y/m/d', $fecha);
      ?>
      defaultDate: '{{$fecha2}}'
      @endif
    });
    $('#hasta').datetimepicker({
      useCurrent: false,
      format: 'YYYY/MM/DD',

      @if($fecha == '0')
      defaultDate: '{{date("Y/m/d")}}'
      @else
      <?php

      $fecha  = substr($fecha, 0, 10);
      $fecha2 = date('Y/m/d', $fecha);
      ?>
      defaultDate: '{{$fecha2}}'
      @endif
    });
  });

  function index_limpieza(id) {

    $.ajax({
      type: 'post',
      url: "{{ url('limpieza/index_paciente')}}/" + id,
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: $("#form_fecha").serialize(),
      success: function(datahtml) {

        $("#index_form").html(datahtml);

      },
      error: function() {
        alert('error al cargar');
      }
    });
  }
</script>

@endsection