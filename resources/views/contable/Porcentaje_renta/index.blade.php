@extends('contable.Porcentaje_renta.base')
@section('action-content')
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
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

  .table-condensed>thead>tr>th>td,
  .table-condensed>tbody>tr>th>td,
  .table-condensed>tfoot>tr>th>td,
  .table-condensed>thead>tr>td,
  .table-condensed>tbody>tr>td,
  .table-condensed>tfoot>tr>td {
    padding: 0.5px;
    line-height: 1;
  }
</style>

<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item active">Porcentaje RENTA</li>
    </ol>
  </nav>
  <div class="box">
    <div class="box-header">
      <div class="col-md-9">
        <h5><b>PORCENTAJE RENTA</b></h5>
      </div>
      <div class="col-md-1 text-right">
        <button type="button" onclick="location.href='{{route('Porcentaje.create')}}'" class="btn btn-success btn-gray">
          <i aria-hidden="true"></i>Agregar Porcentaje RENTA
        </button>
      </div>
    </div>


    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCADOR PORCENTAJE DE RENTA</label>
      </div>
    </div>


    <div class="box-body dobra">
      <form method="POST" id="buscar_porcentaje_impuesto_renta" action="{{ route('Porcentaje.buscar') }}"> {{ csrf_field() }}
    
      <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="codigo">Codigo:</label>
        </div>
        <div class="form-group col-md-2 col-xs-5 container-2">
          <input class="form-control" type="text" id="codigo" name="codigo" value="@if(isset($searchingVals)){{$searchingVals['id']}}@endif" autocomplete="off" placeholder="Ingrese el Codigo..." />
        </div>

        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_anio">Año: </label>
        </div>

        <div class="form-group col-md-2 col-xs-10 container-4">
          @php
          $cont = date('Y');
          @endphp
          <select class="form-control" type="text" id="buscar_anio" name="buscar_anio" value="@if(isset($searchingVals)){{$searchingVals['anio']}}@endif" autocomplete="off" />

          <option value="" disabled selected> Seleccione año...</option>
          @php while ($cont >= 1990) {
          @endphp

          <option value="
  
  @php echo($cont); 
  @endphp">
            @php echo($cont);
            @endphp
          </option>
          @php$cont = ($cont-1);


          }
          @endphp
          </select>
        </div>
        <div class="form-group col-md-1 col-xs-2">
          <label class="texto" for="buscar_porcentaje">Porcentaje Renta:</label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <input class="form-control" type="text" id="buscar_porcentaje" name="buscar_porcentaje" value="@if(isset($searchingVals)){{$searchingVals['porcentaje']}}@endif" autocomplete="off" placeholder="Ingrese el Porcentaje RENTA..." />
        </div>
        <div class="form-group col-md-1 col-xs-2">

          <label class="texto">Regimen Especial </label>
        </div>
        <div class="form-group col-md-2 col-xs-10 container-4">
          <select class="form-control" name="regimen" id="si_no">

            <option value=''>Seleccione</option>
            <option value="1">Si</option>
            <option value="0">No</option>

          </select>
        </div>
        

        <div class="col-xs-2">
          <button type="submit" id="buscarCodigoTipo" class="btn btn-primary btn-gray">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
          </button>
        </div>
      </form>
    </div>
    <div class="row head-title">
      <div class="col-md-12 cabecera">
        <label class="color_texto">LISTADO DE PORCENTAJE RENTA</label>
      </div>
    </div>
    <div class="box-body dobra">
      <div class="form-row">
        <div id="contenedor">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr class="well-dark">
                      <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Código</th>
                      <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">% Renta</th>
                      <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Año</th>
                      <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Regimen Especial</th>
                      <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Usuario crea</th>
                      <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Usuario Modifica</th>
                      <th class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                    </tr>
                  </thead>
                  <tbody>

                    @foreach ($porcentaje_r as $value)
                    @php
                    // dd($value->id_usuariocrea);
                    @endphp
                    <tr role="row" class="odd" style="background:#F5F5F5">
                      <td class="sorting_1">@if(!is_null($value->porcentaje)){{$value->id}}@endif</td>
                      <td>@if(!is_null($value->porcentaje)){{$value->porcentaje}}@endif</td>
                      <td>@if(!is_null($value->anio)){{$value->anio}}@endif</td>
                      <td>@if($value->regimen_especial=='1')Si @elseif($value->regimen_especial == '0') No @endif</td>
                      @php
                      $usercrea= Sis_medico\User::find($value->id_usuariocrea);
                      $user_mod= Sis_medico\User::find($value->id_usuariomod);
                      // dd($usercrea);
                      @endphp
                      <td>@if(! is_null($usercrea)) {{$usercrea->nombre1}} {{$usercrea->apellido1}} @endif</td>
                      <td>@if(! is_null($user_mod)) {{$user_mod->nombre1}} {{$user_mod->apellido1}} @endif</td>
                      <td style="text-align: center;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('Porcentaje.edit', ['id' => $value->id])}}" class="btn btn-success btn-gray">
                          <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                        </a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($porcentaje_r->currentPage() - 1) * $porcentaje_r->perPage())}} / {{count($porcentaje_r) + (($porcentaje_r->currentPage() - 1) * $porcentaje_r->perPage())}} de {{$porcentaje_r->total()}} registros
                </div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{$porcentaje_r->appends(Request::only(['anio','porcentaje']))->links() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- /.content -->
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>


<script type="text/javascript">
  $('#example2').DataTable({
    'paging': false,
    'lengthChange': false,
    'searching': false,
    'ordering': false,
    'info': false,
    'autoWidth': false
  })
</script>

@endsection