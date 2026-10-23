@extends('contable.estado_cuenta_bancos.base')
@section('action-content')
<!-- Ventana modal editar -->

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="#">Banco</a></li>
      <li class="breadcrumb-item active">Estado de Cuenta Bancos</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">Estado de Cuenta Bancos</h3>
      </div>
      <div class="col-md-1 text-right">

      </div>
    </div>

    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR DE ESTADO DE CUENTAS BANCARIAS</label>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('estadocuentabancos.index') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Asiento: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <div class="col-xs-12">
            <input type="text" name="asiento_id" class="form-control" id="asiento_id" value="@if(isset($asiento_id)) {{@$asiento_id}} @endif">
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">Caja/Banco: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <select class="form-control" name="banco" id="banco" autofocus>
            <option value="">Todos</option>
            @foreach($bancos as $value)
            <option @if($banco==$value->id) selected="selected" @endif value="{{$value->id}}">{{$value->nombre}}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Desde: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <div class="col-xs-12">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" name="fecha_desde" class="form-control" id="fecha_desde" value="@if(isset($fecha_desde)) {{@$fecha_desde}} @else {{ date('d/m/Y') }} @endif" required>
            </div>
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Hasta: </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <div class="col-xs-12">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" name="fecha_hasta" class="form-control" id="fecha_hasta" value="@if(isset($fecha_hasta)) {{@$fecha_hasta}} @else {{ date('d/m/Y') }} @endif" required>
            </div>
          </div>
        </div>
        <div class="form-group col-md-2 col-xs-2">
          <label class="texto" for="fecha">Tipo: </label>
        </div>
        <div class="form-group col-md-2 col-xs-2 container-4">
          <select class="form-control" name="tipo" id="tipo" autofocus>
            <option value="">SELECCIONE</option>
            <option @if($tipos==1) selected="selected" @endif value="1">ACR-EG</option>
            <option @if($tipos==2) selected="selected" @endif value="2">ACR-EGV</option>
            <option @if($tipos==3) selected="selected" @endif value="3">BAN-EG</option>
            <option @if($tipos==4) selected="selected" @endif value="4">BAN-ND</option>
            <option @if($tipos==5) selected="selected" @endif value="5">BAN-DEP</option>
            <option @if($tipos==6) selected="selected" @endif value="6">BAN-NC</option>
            <option @if($tipos==7) selected="selected" @endif value="7">BAN-TR</option>
            <option @if($tipos==8) selected="selected" @endif value="7">CAM</option>
          </select>

        </div>
        <div class="col-xs-2">
          <button type="submit" id="buscarAsiento" name="buscarAsiento" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      </form>

      <div class="col-xs-2">
        <form method="GET" id="4" action="{{ route('estadocuentabancos.exportar_excel') }}">
          {{ csrf_field() }}
          <input type="hidden" name="fecha_desde2" id="fecha_desde2" value="@if(isset($fecha_desde)){{$fecha_desde}}@endif">
          <input type="hidden" name="fecha_hasta2" id="fecha_hasta2" value="@if(isset($fecha_hasta)){{$fecha_hasta}}@endif">
          <input type="hidden" name="banco2" id="banco2" value="{{$banco}}">
          <input type="hidden" name="tipo2" value="{{$tipos}}">
          <button type="submit" id="buscarAsiento0" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Excel
          </button>
        </form>

      </div>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">REPORTE ESTADO DE CUENTA BANCOS</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
          <div id="resultados">
          </div>
          <div id="contenedor">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr class="well-dark">
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Asiento</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Banco</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Ref</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Cheque</th>
                        <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Beneficiario</th>
                        <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Debe</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Haber</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo</th>
                      </tr>
                    </thead>
                    <tbody id="tbl_detalles" name="tbl_detalles">
                      <tr class="well">
                        <td colspan="6"></td>
                        <td style="text-align: right;"><b>Saldo Anterior</b></td>
                        @php
                        //aqui cambiar att achilan
                        $dateHasta = str_replace('/', '-', $fecha_desde);
                        $id_auth = Auth::user()->id;
                        $fe= date("d/m/Y",strtotime($dateHasta."- 1 day"));

                        @endphp

                        <td><b> - {{$fe}} <b></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;"><b>{{number_format($saldoanterior, 2)}}</b></td>
                      </tr>
                      @php
                      $saldo = $saldoanterior; $saldodebe = 0; $saldohaber = 0; $contador_ctv=0; $numero_factura=0;
                      //dd($registros); me falta aun

                      @endphp

                      @foreach ($registros as $value)
                      <tr class="well">
                        @if($tipos=="")

                        @if(isset($value->cabecera->egresos))
                        @if($value->cabecera->egresos->estado==1)
                        @php
                        $saldo = $saldo + ($value['debe'] - $value['haber']);
                        $saldodebe += $value['debe'];
                        $saldohaber += $value['haber'];

                        $tipo = 0;
                        @endphp
                        <td>{{ $value->cabecera->id }}</td>
                        <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                        <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                        <td>ACR-EG</td>
                        <td>{{ $value->cabecera->egresos->secuencia }}</td>
                        <td>{{ $value->cabecera->egresos->no_cheque }}</td>
                        <td>@if(isset($value->cabecera->egresos->proveedor)) {{ $value->cabecera->egresos->proveedor->nombrecomercial }} @endif</td>
                        <td>{{ $value->cabecera->egresos->descripcion }}</td>
                        <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                        <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                        <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                        @endif
                        @elseif(isset($value->cabecera->depositos))
                        @php
                        $max_id = intval($value->cabecera->depositos->id);
                        $numero_factura=0;
                        if (strlen($max_id) < 10) { $numero_factura=str_pad($max_id, 10, "0" , STR_PAD_LEFT); } @endphp @if($value->cabecera->depositos->estado==1)
                          @php

                          $saldo = $saldo + ($value['debe'] - $value['haber']);
                          $saldodebe += $value['debe'];
                          $saldohaber += $value['haber'];

                          $tipo = 0;

                          @endphp
                          <td>{{ $value->cabecera->id }}</td>
                          <td>{{ date('d-m-Y', strtotime($value->cabecera->depositos->fecha_asiento))}}</td>
                          <td>{{ date('d-m-Y', strtotime($value->cabecera->depositos->fecha_asiento))}}</td>
                          <td>BAN-DP</td>
                          <td>{{ $numero_factura }}</td>
                          <td></td>
                          <td></td>
                          <td>{{ $value->cabecera->depositos->concepto }}</td>
                          <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                          <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                          <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                          @endif
                          @elseif(isset($value->cabecera->egresos_varios))
                          @if($value->cabecera->egresos_varios->estado==1)
                          @php

                          $saldo = $saldo + ($value['debe'] - $value['haber']);
                          $saldodebe += $value['debe'];
                          $saldohaber += $value['haber'];

                          $tipo = 0;

                          @endphp
                          <td>{{ $value->cabecera->id }}</td>
                          <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                          <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                          <td>ACR-EGV</td>
                          <td>{{ $value->cabecera->egresos_varios->secuencia }}</td>
                          <td>{{ $value->cabecera->egresos_varios->nro_cheque }}</td>
                          <td>@if(isset($value->cabecera->egresos_varios->beneficiario)) {{ $value->cabecera->egresos_varios->beneficiario }} @endif</td>
                          <td>{{ $value->cabecera->egresos_varios->descripcion }}</rtd>
                          <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                          <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                          <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>

                          @endif
                          @elseif(isset($value->cabecera->debito))
                          @php
                          $max_id = intval($value->cabecera->debito->id);
                          $numero_factura=0;
                          if (strlen($max_id) < 10) { $numero_factura=str_pad($max_id, 10, "0" , STR_PAD_LEFT); } @endphp @if($value->cabecera->debito->estado==1)
                            @php

                            $saldo = $saldo + ($value['debe'] - $value['haber']);
                            $saldodebe += $value['debe'];
                            $saldohaber += $value['haber'];

                            $tipo = 0;

                            @endphp
                            <td>{{ $value->cabecera->id }}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                            <td>BAN-ND</td>
                            <td>{{ $numero_factura }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $value->cabecera->debito->concepto}}</td>
                            <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                            <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                            <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                            @endif
                            @elseif(isset($value->cabecera->baneg))

                            @if($value->cabecera->baneg->estado==1)
                            @php

                            $saldo = $saldo + ($value['debe'] - $value['haber']);
                            $saldodebe += $value['debe'];
                            $saldohaber += $value['haber'];

                            $tipo = 0;

                            @endphp
                            <td>{{ $value->cabecera->id }}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                            <td>BAN-EG</td>
                            <td>{{ $value->cabecera->baneg->secuencia }}</td>
                            <td></td>
                            <td>@if(isset($value->cabecera->baneg->acreedor)) {{ $value->cabecera->baneg->acreedor->nombrecomercial }} @endif</td>
                            <td>{{ $value->cabecera->baneg->concepto }}</td>
                            <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                            <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                            <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                            @endif
                            @elseif(isset($value->cabecera->masivo))
                            @php
                            $numero_factura= $value->cabecera->masivo->secuencia;
                            @endphp
                            @if($value->cabecera->masivo->estado==1)
                            @php

                            $saldo = $saldo + ($value['debe'] - $value['haber']);
                            $saldodebe += $value['debe'];
                            $saldohaber += $value['haber'];

                            $tipo = 0;

                            @endphp
                            <td>{{ $value->cabecera->id }}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                            <td>CAM</td>
                            <td>{{ $numero_factura }}</td>
                            <td>{{$value->cabecera->masivo->no_cheque}}</td>
                            <td>{{$value->cabecera->masivo->girado_a}}</td>
                            <td>{{ $value->cabecera->masivo->descripcion}}</td>
                            <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                            <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                            <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                            @endif
                            @elseif(isset($value->cabecera->baneg))

                            @if($value->cabecera->baneg->estado==1)
                            @php

                            $saldo = $saldo + ($value['debe'] - $value['haber']);
                            $saldodebe += $value['debe'];
                            $saldohaber += $value['haber'];

                            $tipo = 0;

                            @endphp
                            <td>{{ $value->cabecera->id }}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                            <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                            <td>BAN-EG</td>
                            <td>{{ $value->cabecera->baneg->secuencia }}</td>
                            <td></td>
                            <td>@if(isset($value->cabecera->baneg->acreedor)) {{ $value->cabecera->baneg->acreedor->nombrecomercial }} @endif</td>
                            <td>{{ $value->cabecera->baneg->concepto }}</td>
                            <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                            <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                            <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                            @endif
                            @elseif(isset($value->cabecera->nota_credito))
                            @php
                            $max_id = intval($value->cabecera->nota_credito->id);
                            $numero_factura=0;
                            if (strlen($max_id) < 10) { $numero_factura=str_pad($max_id, 10, "0" , STR_PAD_LEFT); } @endphp @if($value->cabecera->nota_credito->estado==1)
                              @php

                              $saldo = $saldo + ($value['debe'] - $value['haber']);
                              $saldodebe += $value['debe'];
                              $saldohaber += $value['haber'];

                              $tipo = 0;

                              @endphp
                              <td>{{ $value->cabecera->id }}</td>
                              <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                              <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                              <td>BAN-NC</td>
                              <td>{{ $numero_factura }}</td>
                              <td></td>
                              <td></td>
                              <td>{{ $value->cabecera->nota_credito->descripcion }}</td>
                              <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                              <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                              <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                              @endif
                              @elseif(isset($value->cabecera->deposito))
                              @if($value->cabecera->deposito->estado==1)
                              @php

                              $saldo = $saldo + ($value['debe'] - $value['haber']);
                              $saldodebe += $value['debe'];
                              $saldohaber += $value['haber'];

                              $tipo = 0;

                              @endphp
                              <td>{{ $value->cabecera->id }}</td>
                              <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                              <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                              <td>BAN-DEP</td>
                              <td>{{ $value->cabecera->deposito->id }}</td>
                              <td></td>
                              <td>@if(isset($value->cabecera->deposito->id_cuenta_destino)) {{ $value->cabecera->deposito->id_cuenta_destino }} @endif</td>
                              <td>{{ $value->cabecera->deposito->concepto }}</td>
                              <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                              <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                              <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                              @endif
                              @elseif(isset($value->cabecera->transferencia))


                              @if($value->cabecera->transferencia->estado==1)
                              @php
                              $max_id = intval($value->cabecera->transferencia->id);
                              $numero_factura=0;
                              if (strlen($max_id) < 10) { $numero_factura=str_pad($max_id, 10, "0" , STR_PAD_LEFT); } @endphp @php $saldo=$saldo + ($value['debe'] - $value['haber']); $saldodebe +=$value['debe']; $saldohaber +=$value['haber']; $tipo=0; @endphp <td>{{ $value->cabecera->id }}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>BAN-TR</td>
                                <td>{{ $numero_factura }}</td>
                                <td></td>
                                <td></td>
                                <td>{{ $value->cabecera->transferencia->concepto }}</td>
                                <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                @endif

                                @endif






                                @elseif($tipos=="1")

                                @if(isset($value->cabecera->egresos))

                                @if($value->cabecera->egresos->estado==1)
                                @php

                                $saldo = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe += $value['debe'];
                                $saldohaber += $value['haber'];

                                $tipo = 0;

                                @endphp
                                <td>{{ $value->cabecera->id }}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>ACR-EG</td>
                                <td>{{ $value->cabecera->egresos->secuencia }}</td>
                                <td>{{ $value->cabecera->egresos->no_cheque }}</td>
                                <td>@if(isset($value->cabecera->egresos->proveedor)) {{ $value->cabecera->egresos->proveedor->nombrecomercial }} @endif</td>
                                <td>{{ $value->cabecera->egresos->descripcion }}</td>
                                <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                @endif
                                @endif
                                @elseif($tipos=="8")
                                @elseif(isset($value->cabecera->masivo))
                                @php
                                $numero_factura= $value->cabecera->masivo->secuencia;
                                @endphp
                                @if($value->cabecera->masivo->estado==1)
                                @php

                                $saldo = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe += $value['debe'];
                                $saldohaber += $value['haber'];

                                $tipo = 0;

                                @endphp
                                <td>{{ $value->cabecera->id }}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>CAM</td>
                                <td>{{ $numero_factura }}</td>
                                <td></td>
                                <td></td>
                                <td>{{ $value->cabecera->masivo->descripcion}}</td>
                                <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                @endif
                                @elseif($tipos=="2")


                                @if(isset($value->cabecera->egresos_varios))

                                @if($value->cabecera->egresos_varios->estado==1)
                                @php

                                $saldo = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe += $value['debe'];
                                $saldohaber += $value['haber'];

                                $tipo = 0;

                                @endphp
                                <td>{{ $value->cabecera->id }}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>ACR-EGV</td>
                                <td>{{ $value->cabecera->egresos_varios->secuencia }}</td>
                                <td>{{ $value->cabecera->egresos_varios->nro_cheque }}</td>
                                <td>@if(isset($value->cabecera->egresos_varios->beneficiario)) {{ $value->cabecera->egresos_varios->beneficiario }} @endif</td>
                                <td>{{ $value->cabecera->egresos_varios->descripcion }}</rtd>
                                <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                @endif
                                @else

                                @endif
                                @elseif($tipos=="3")

                                @if(isset($value->cabecera->baneg))

                                @if($value->cabecera->baneg->estado==1)
                                @php

                                $saldo = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe += $value['debe'];
                                $saldohaber += $value['haber'];

                                $tipo = 0;

                                @endphp
                                <td>{{ $value->cabecera->id }}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                <td>BAN-EG</td>
                                <td>{{ $value->cabecera->baneg->secuencia }}</td>
                                <td></td>
                                <td>@if(isset($value->cabecera->baneg->acreedor)) {{ $value->cabecera->baneg->acreedor->nombrecomercial }} @endif</td>
                                <td>{{ $value->cabecera->baneg->concepto }}</td>
                                <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                @endif
                                @endif

                                @elseif($tipos=="4")
                                @if(isset($value->cabecera->debito))

                                @if($value->cabecera->debito->estado==1)
                                @php
                                $saldo = $saldo + ($value['debe'] - $value['haber']);
                                $saldodebe += $value['debe'];
                                $saldohaber += $value['haber'];

                                $tipo = 0;


                                $max_id = intval($value->cabecera->debito->id);
                                $numero_factura=0;
                                if (strlen($max_id) < 10) { $numero_factura=str_pad($max_id, 10, "0" , STR_PAD_LEFT); } @endphp <td>{{ $value->cabecera->id }}</td>
                                  <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                  <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                  <td>BAN-ND</td>
                                  <td>{{ $numero_factura }}</td>
                                  <td></td>
                                  <td></td>
                                  <td>{{ $value->cabecera->debito->concepto}}</td>
                                  <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                  @endif
                                  @endif
                                  @elseif($tipos=="5")

                                  @if(isset($value->cabecera->depositos))
                                  @if($value->cabecera->depositos->estado==1)
                                  @php
                                  $saldo = $saldo + ($value['debe'] - $value['haber']);
                                  $saldodebe += $value['debe'];
                                  $saldohaber += $value['haber'];

                                  $tipo = 0;
                                  @endphp
                                  @php
                                  $max_id = intval($value->cabecera->depositos->id);
                                  $numero_factura=0;
                                  if (strlen($max_id) < 10) { $numero_factura=str_pad($max_id, 10, "0" , STR_PAD_LEFT); } @endphp <td>{{ $value->cabecera->id }}</td>
                                    <td>{{ date('d-m-Y', strtotime($value->cabecera->depositos->fecha_asiento))}}</td>
                                    <td>{{ date('d-m-Y', strtotime($value->cabecera->depositos->fecha_asiento))}}</td>
                                    <td>BAN-DP</td>
                                    <td>{{ $numero_factura }}</td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $value->cabecera->depositos->concepto }}</td>
                                    <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                    <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                    <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                    @endif
                                    @endif
                                    @elseif($tipos=="6")

                                    @if(isset($value->cabecera->nota_credito))

                                    @if($value->cabecera->nota_credito->estado==1)
                                    @php

                                    $saldo = $saldo + ($value['debe'] - $value['haber']);
                                    $saldodebe += $value['debe'];
                                    $saldohaber += $value['haber'];

                                    $tipo = 0;

                                    @endphp
                                    @php
                                    $max_id = intval($value->cabecera->nota_credito->id);
                                    $numero_factura=0;
                                    if (strlen($max_id) < 10) {
                                      $numero_factura=str_pad($max_id, 10, "0" , STR_PAD_LEFT);
                                      }
                                      @endphp
                                      <td>{{ $value->cabecera->id }}</td>
                                      <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                      <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                      <td>BAN-NC</td>
                                      <td>{{ $numero_factura }}</td>
                                      <td></td>
                                      <td></td>
                                      <td>{{ $value->cabecera->nota_credito->descripcion }}</td>
                                      <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                      <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                      <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                      @endif

                                      @endif

                                      @elseif($tipos=="7")

                                      @if(isset($value->cabecera->transferencia))

                                      @if($value->cabecera->transferencia->estado==1)
                                      @php

                                      $saldo = $saldo + ($value['debe'] - $value['haber']);
                                      $saldodebe += $value['debe'];
                                      $saldohaber += $value['haber'];

                                      $tipo = 0;

                                      @endphp
                                      @php
                                      $max_id = intval($value->cabecera->transferencia->id);
                                      $numero_factura=0;
                                      if (strlen($max_id) < 10) { $numero_factura=str_pad($max_id, 10, "0" , STR_PAD_LEFT); } @endphp <td>{{ $value->cabecera->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                        <td>{{ date('d-m-Y', strtotime($value['fecha']))}}</td>
                                        <td>BAN-TR</td>
                                        <td>{{ $numero_factura }}</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $value->cabecera->transferencia->concepto }}</td>
                                        <td style="text-align: right;">{{ number_format($value['debe'], 2) }}</td>
                                        <td style="text-align: right;">{{ number_format($value['haber'], 2) }}</td>
                                        <td style="text-align: right;">{{ number_format($saldo, 2) }} </td>
                                        @endif

                                        @endif

                                        @endif

                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-2">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros
                    {{count($registros)}}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="box-body dobra">
        <div class="row col-md-11">
          <div class="col-sm-2">
            &nbsp;
          </div>
          <div class="form-group col-xs-2 px-1">
            <div class="col-md-12 px-0">
              <label for="total_saldoant" style="text-align: right" class="label_header">Saldo Anterior &nbsp;</label>
            </div>
            <div class="col-md-12 px-0">
              <input id="total_saldoant" style="text-align: right;font-weight: bold;" type="text" size="16" class="form-control text_der number" value="{{number_format($saldoanterior, 2)}}" name="total_saldoant" readonly autofocus>
            </div>
          </div>
          <div class="form-group col-xs-2 px-1">
            <div class="col-md-12 px-0">
              <label for="total_debito" style="text-align: right" class="label_header">Total Debitos &nbsp;</label>
            </div>
            <div class="col-md-12 px-0">
              <input id="total_debito" style="text-align: right;font-weight: bold;" type="text" size="16" class="form-control text_der number" value="{{ number_format($saldodebe, 2) }}" name="total_debito" readonly autofocus>
            </div>
          </div>
          <div class="form-group col-xs-2 px-1">
            <div class="col-md-12 px-0">
              <label for="total_credito" style="text-align: right" class="label_header">Total Creditos &nbsp;</label>
            </div>
            <div class="col-md-12 px-0">
              <input id="total_credito" style="text-align: right;font-weight: bold;" type="text" size="16" class="form-control text_der number" value="{{ number_format($saldohaber, 2) }}" name="total_credito" readonly autofocus>
            </div>
          </div>
          <div class="form-group col-xs-2 px-1">
            <div class="col-md-12 px-0">
              <label for="saldo_final" style="text-align: right" class="label_header">Saldo Final &nbsp;</label>
            </div>
            <div class="col-md-12 px-0">
              <input id="saldo_final" style="text-align: right;font-weight: bold;" type="text" size="15.5" class="form-control text_der number" value="{{ number_format($saldo, 2) }}" name="saldo_final" readonly autofocus>
            </div>
          </div>

        </div>
      </div>



      <!-- /.box-body -->
    </div>
</section>
<!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function() {

    $('#example2').DataTable({
      'paging': false,
      'lengthChange': false,
      'searching': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'scrollY': '50vh',
      'scrollCollapse': true,
    });
  });
  $('#fecha_desde').datetimepicker({
    format: 'DD/MM/YYYY',
  });
  $('#fecha_hasta').datetimepicker({
    format: 'DD/MM/YYYY',
  });
</script>
@endsection