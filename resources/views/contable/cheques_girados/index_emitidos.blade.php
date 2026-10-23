@extends('contable.balance_comprobacion.base')
@section('action-content')
<style>
  p.s1 {
    margin-left: 10px;
    font-size: 14px;
    font-weight: bold;
  }

  p.s2 {
    margin-left: 20px;
    font-size: 12px;
    font-weight: bold;
  }

  p.s3 {
    margin-left: 30px;
    font-size: 10px;
    font-weight: bold;
  }

  p.s4 {
    margin-left: 40px;
    font-size: 10px;
  }

  p.t1 {
    font-size: 14px;
    font-weight: bold;
  }

  p.t2 {
    font-size: 12px;
    font-weight: bold;
  }

  p.t3 {
    font-size: 10px;
  }

  .table-striped>thead>tr>th>td,
  .table-striped>tbody>tr>th>td,
  .table-striped>tfoot>tr>th>td,
  .table-striped>thead>tr>td,
  .table-striped>tbody>tr>td,
  .table-striped>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }

  .right_text {
    text-align: right;
  }

  .span {
    color: black;
    font-weight: bold;
  }

  th {
    text-align: center;
  }
</style>
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<!-- Main content -->
<section class="content">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
      <li class="breadcrumb-item"><a href="#">{{trans('contableM.acreedor')}}</a></li>
    </ol>
  </nav>

  <div class="box" style=" background-color: white;">
    <!-- <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Criterios de búsqueda</h3>
            </div>
        </div> -->
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">{{trans('contableM.Buscador')}} CHEQUES EMITIDOS</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('compra.informe.index_cheques_emitidos') }}">
        {{ csrf_field() }}

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
          <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}</label>
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
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="nombre_proveedor">{{trans('contableM.proveedor')}}: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select class="form-control select2_cuentas" name="id_proveedor" id="id_proveedor" style="width: 100%;">
            <option value="">{{trans('agenda.seleccione')}}...</option>
            @foreach($proveedores as $value)
            <option @if(!is_null($id_proveedor)) @if(($id_proveedor)==$value->id) selected='selected' @endif @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
            @endforeach
          </select>
        </div>

        
        <div class="form-group col-md-1 col-xs-1">
          <label class="texto" for="tipo">Estado: </label>
        </div>
        <div class="form-group col-md-4 col-xs-4 container-4">
          <select class="form-control select2_cuentas" name="tipo" id="tipo" style="width: 100%;">
            <option @if($tipo==-1) selected @endif value="-1">{{trans('agenda.seleccione')}}...</option>
            <option @if($tipo==1) selected @endif value="1">Activo</option>
            <option @if($tipo==2) selected @endif value="2">Entregados</option>
            <option @if($tipo==3) selected @endif value="3">Pendientes por entregar</option>
            <option @if($tipo==0) selected @endif value="0">Anulados</option>
          </select>
        </div>

        <div class="col-md-12">
          <div class="col-md-6">
            <div class="col-md-3">
              <label class="texto" for="tipo">Nro. Cheque: </label>
            </div>
            <div class="col-md-9">
              <input @if(!is_null($n_cheque) || $n_cheque != "") value="{{$n_cheque}}" @endif class="form-control" type="text" id="n_cheque" name="n_cheque">
            </div>
          </div>
        </div>

        <div class="form-group col-md-2 col-xs-5 pull-right" style="text-align: right;">
          <button type="submit" class="btn btn-success" formaction="{{route('compra.informe.excel_cheques_emitidos')}}" target="_blank">
            <span class="fa fa-download"></span> Excel
          </button>

          <button type="submit" class="btn btn-primary" id="boton_buscar">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
          </button>
        </div>

        <div class="form-group col-md-6 col-xs-9" style="text-align: right;">

        </div>
      </form>
    </div>
    <!-- /.box-body -->
    <form method="POST" id="print_reporte_master" action="{{ route('chequesa.excel') }}" target="_blank">
      {{ csrf_field() }}
      <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
      <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
      <input type="hidden" name="id_proveedor2" id="id_proveedor2" value="{{$id_proveedor}}">
    </form>

    @if(count($cheques)>0)
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
            </div>
            <!-- /.box-header -->
            {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
            <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">{{trans('contableM.InformedeCheque')}}</h4>
            <h4 style="text-align: center;">{{trans('contableM.periodo')}} {{$fecha_desde}} - {{$fecha_hasta}}</h4>
          </div>
          <div class="col-md-4">
            <dl>
              <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
              <dd style="text-align:right">{{trans('contableM.telefono')}}: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
              <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
            </dl>
          </div>
        </div> --}}

        <div class="box-body">
          <div class="col-md-1">
            <dl>
              <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
              {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
            </dl>
          </div>
          <div class="col-md-3">
            <dl>
              <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
              <dd>&nbsp; {{$empresa->id}}</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <h4 style="text-align: center;">{{trans('contableM.InformedeChequesGirados')}}</h4>
            <h5 style="text-align: center;"> @if(($fecha_desde)!=null) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}} @else Al {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
          </div>
          <div class="col-md-4">
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
  </div>
  <div class="row">
    <div class="table-responsive col-md-12">
      <div class="content">
        <table id="example2" class="table" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
          <thead>
            <tr>
              <th>{{trans('contableM.FechaComprobante')}}</th>
              <th>{{trans('contableM.fechacheque')}}</th>
              <th>Secuencia</th>
              <th>{{trans('contableM.NroAsiento')}}</th>

              <th>{{trans('contableM.NroCheque')}}</th>
              <th>{{trans('contableM.NombreProveedor')}}</th>
              <th>{{trans('contableM.banco')}}</th>
              <th>Entregado</th>
              <th>Fecha Entregado</th>
              <th>{{trans('contableM.valor')}}</th>
              <th>{{trans('contableM.accion')}}</th>
            </tr>
          </thead>
          <tbody>
            @php
            $acumulador=0;
            @endphp
            @foreach($cheques as $value)
            @php
            $pro= Sis_medico\Proveedor::find($value->id_proveedor);
            $banco= Sis_medico\Ct_Caja_Banco::find($value->banco);
            @endphp
            @if(!is_null($banco))
            @if($banco->clase == '1')
            @if($value->estado_egreso == 1)
            @php $acumulador+=$value->valor; @endphp
            @endif
            <tr style="text-align: center;">
              <td>{{date('d/m/Y',strtotime($value->fecha_comprobante))}}</td>
              <td>{{date('d/m/Y',strtotime($value->fecha_cheque))}}</td>
              <td>{{$value->secuencia_egreso}}</td>
              <td>
                <a class="label label-info" style="font-size: 10px;" target="_blank" href="{{route('librodiario.edit', ['id'=>$value->id_asiento_cabecera])}}">{{$value->id_asiento_cabecera}}</a>
              </td>

              <td>{{$value->cheque}}</td>
              <!-- <td>{{$value->id_proveedor}}</td> -->
              <td>@if(!is_null($pro)) {{$pro->razonsocial}} @else {{$value->id_proveedor}} @endif</td>
              <td>@if(!is_null($banco)) {{$banco->nombre}} @endif</td>
              <td><input id="asiento_{{$value->id_asiento_cabecera }}" type="checkbox" @if($value->estado_egreso == 1) value="{{$value->id_asiento_cabecera }}" onchange="colocarCheck({{$value->id_asiento_cabecera }})" @else disabled @endif @if($value->check == 1) checked disabled @endif></td>
              <td id="fecha_{{$value->id_asiento_cabecera }}"> @if(!is_null($value->fecha_entregado)) {{date('d/m/Y',strtotime($value->fecha_entregado))}} @endif</td>
              <td style="text-align: right;">{{$value->valor}}</td>
              <td>@if($value->estado_egreso == 1) <label style='font-size:12px' class='label label-success'>Activo</label> @else <label style='font-size:12px' class='label label-danger'>Anulado</label> @endif </td>
            </tr>
            @endif
            @endif
            @endforeach
            &nbsp;
            &nbsp;
            <tr>
              <td colspan="6"></td>
              <td><span class="span">Total Cheques</span></td>
              <td>&nbsp;</td>
              <td style="text-align:right; font-weight: bold;">{{number_format($acumulador,2,'.',',')}}</td>
              <td>&nbsp;</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
  </div>
  @endif

  </div>
</section>
</div>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
  const colocarCheck = (id_asiento) => {
    // console.log(id_asiento);
    let asiento = document.getElementById("asiento_" + id_asiento);
    let check = 0;
    if (asiento.checked) {
      check = 1;
    }
    console.log(check);
    $.ajax({
      type: "get",
      url: `{{route('compra.informe.updateCheck')}}`,
      data: {
        id_asiento: id_asiento,
        check: check
      },
      success: function(data) {
        if (data.status == 'error') {
          return;
        }
        asiento.disabled = true;
        let fecha = document.getElementById("fecha_" + id_asiento);

        fecha.innerHTML = data.fecha;



      },
      error: function(data) {
        console.log(data);
      }
    })
  }

  $(document).ready(function() {
    $('.select2_cuentas').select2({
      tags: false
    });
  });

  $(document).ready(function() {
    $('.select2_cheques').select2({
      tags: false
    });
  });

  $(function() {
    $('#fecha_desde').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_desde}}',
    });
    $('#fecha_hasta').datetimepicker({
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fecha_hasta}}',

    });
    $("#fecha_desde").on("dp.change", function(e) {
      verifica_fechas();
    });

    $("#fecha_hasta").on("dp.change", function(e) {
      verifica_fechas();
    });

  });

  function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: `{{trans('winsumos.verifiquerangofechayveuvlaconsultar')}}`
      });
    }
  }
</script>
@endsection