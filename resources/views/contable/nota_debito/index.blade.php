@extends('contable.nota_debito.base')
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
      <li class="breadcrumb-item active">Nota de Debito</li>

    </ol>
  </nav>
  <div class="box">
    <div class="box-header header_new">
      <div class="col-md-9">
        <h3 class="box-title">Listado de Notas de D&eacute;bito</h3>
      </div>
      <div class="col-md-1 text-right">
        <button onclick="location.href='{{route('notadebito.crear')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Agregar Nota de D&eacute;bito
        </button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR NOTA DE D&Eacute;BITO</label>
      </div>
    </div>
    <div class="box-body dobra">
      <form method="POST" id="reporte_master" action="{{ route('notadebito.buscar') }}">
        {{ csrf_field() }}
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_asiento">Asiento: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_asiento" name="buscar_asiento" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif" placeholder="Ingrese asiento..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="numero">N&uacute;mero: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <input class="form-control" type="text" id="numero" name="numero" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" placeholder="Ingrese número..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="fecha">Fecha: </label>
        </div>
        <div class="form-group col-md-3 col-xs-10 container-4">
          <div class="col-xs-12">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" name="fecha" class="form-control" id="fecha" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
            </div>
          </div>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="concepto">Concepto: </label>
        </div>
        <div class="form-group col-md-8 col-xs-10 container-4">
          <input class="form-control buscar_secuencia" type="text" id="concepto" name="concepto" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif" placeholder="Ingrese el concepto..." />
        </div>
        <div class="col-md-1">
          <button type="submit" id="buscarAsiento" class="btn btn-success btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
        <div class="col-md-2">
        </div>
      </form>
      <form method="GET" id="4" action="{{ route('notadebito.exportar_excel') }}">
        <input type="hidden" name="fecha2" id="fecha2" value="@if(isset($searchingVals)){{$searchingVals['fecha']}}@endif">
        <input type="hidden" name="numero2" id="numero2" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif">
        <input type="hidden" name="buscar_asiento2" id="buscar_asiento2" value="@if(isset($searchingVals)){{$searchingVals['id_asiento']}}@endif">
        <input type="hidden" name="concepto2" id="concepto2" value="@if(isset($searchingVals)){{$searchingVals['concepto']}}@endif">
        <button type="submit" id="buscarAsiento0" class="btn btn-success btn-gray">
          <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar Excel
        </button>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">NOTAS DE D&Eacute;BITOS</label>
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
                      <tr class='well-dark'>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># de Asiento</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"># de Nota</th>
                        <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                        <th width="35%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Creado Por</th>
                        <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $pago = 0;
                      @endphp
                      @foreach ($registros as $value)

                      @php

                      if(($value->valor)!=null){
                      $pago = $pago+$value->valor;
                      }

                      @endphp
                      <tr class="well">
                        <td>{{ $value->fecha}}</td>
                        <td>{{ $value->id_asiento }}</td>
                        <td>{{ $value->id }}</td>
                        <td>BAN-ND</td>
                        <td>{{ $value->concepto }}</td>
                        <td style="text-align: right;">{{$value->valor}}</td>
                        <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                        <td>{{ $value->creador->nombre1 }} {{ $value->creador->apellido1 }}</td>
                        <td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                          <a href="{{ route('notadebito.revisar', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                            <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                            <!--&nbsp;&nbsp; Revisar Nota-->
                          </a>
                          @if(($value->estado)==1)
                          <a class="btn btn-danger btn-gray" href="javascript:anular({{$value->id}});"><i class="fa fa-trash"></i></a>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="4">&nbsp;</td>
                        <td><b>TOTAL:</b></td>

                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="row">

                <div class="col-sm-5">
                  <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($registros->currentPage() - 1) * $registros->perPage())}} / {{count($registros) + (($registros->currentPage() - 1) * $registros->perPage())}} de {{$registros->total()}} registros</div>
                </div>
                <div class="col-sm-7">
                  <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $registros->appends(Request::only(['id_asiento', 'fecha', 'concepto']))->links() }}
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
      'autoWidth': false
    });
    $('#fecha').datetimepicker({
      format: 'YYYY-MM-DD',
    });
  });

  function anular(id) {

    Swal.fire({
      title: '¿Desea Anular esta nota de débito?',
      text: "No puedes revertir esta acccion!",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
        let ids = "";
        var acumulate = "";
        var mensaje = "";

        let msj_ingr = "";
        let msj_ret = "";
        let msj_cruce = "";
        let msj_che = "";
        let msj_cruce_cue = "";
        let msj_credito = "";
        let mensajeaux = "";

        test(id);

      }
    })
  }
  async function test(id) {
    try {
      const {
        value: text
      } = await Swal.fire({
        input: 'textarea',
        inputPlaceholder: 'Ingrese motivo de anulación...',
        inputAttributes: {
          'aria-label': 'Ingrese motivo de anulación'
        },
        showCancelButton: true
      })

      if (text) {
        $.ajax({
          type: 'get',
          url: "{{ url('contable/Banco/notadebito/anular/')}}/" + id,
          datatype: 'json',
          data: {
            'observacion': text
          },
          success: function(data) {
            Swal.fire("Correcto!", "Anulación Correcta", "success");
            location.href = "{{route('notadebito.index')}}";
          },
          error: function(data) {
            console.log(data);
          }
        });
      }

    } catch (err) {
      console.log(err);
    }
  }
</script>
@endsection