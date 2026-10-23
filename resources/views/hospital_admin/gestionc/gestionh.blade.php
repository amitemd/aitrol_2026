@extends('layouts.app-template-h')
@section('content')

<!-- crear una habitacion -->
<div class="modal fade" id="modalnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="date">

    </div>
  </div>
</div>
<div class="modal fade" id="modalnew2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="date2">

    </div>
  </div>
</div>
<div class="col-md-12" style="text-align:right;">
  <button onclick="showmodal()" class="btn btn-sm btn-primary my-2 btn-icon-split">
    <span class="icon text-white-50">
      <i class="fa fa-plus"></i>
    </span>
    <span class="text">Crear habitación</span>
  </button>
  <a href="{{route('hospital.gcuartos')}}" style="text-align: right;" class="btn btn-primary btn-sm">
    <i class="fa fa-arrow-left"></i>
    Regresar
  </a>
  <div class="modal fade" id="modaleditarh" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
  </div>
</div>


<!-- Final / Editar -->

<div class="row">
  <div class="col-md-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Gestión de habitación</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="collapseCardExample">
        <div class="card-body">

          <table class="table table-bordered">
            <thead>
              <tr class="text-dark">
                <th style="width: 10px">#</th>
                <th>Habitación</th>
                <th>Categorias</th>
                <th>Piso</th>
                <th>Número <br> Habitación</th>
                <th>Estado-Cama</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>

              @foreach($habitacion as $value)
              <tr>
                <td>{{$value->id}}</td>
                <!--FILA DE TIPO DE HABITACION IMAGEN DE REFERENCIA-->
                <td>@if(($value->id_tipo)==1) <img src="{{asset('/')}}hc4/img/simple_block.png" style="width: 25px;"> @elseif(($value->id_tipo)==2) <img src="{{asset('/')}}hc4/img/Doble_Bloqueda.png" style="width: 50px; "> @elseif(($value->id_tipo)==3)<img src="{{asset('/')}}hc4/img/suite.png" style="width: 50px; ">
                  @elseif(($value->id_tipo)==4)<img src="{{asset('/')}}hc4/img/triple_bloqueada.png" style="width: 75px; "> @elseif(($value->id_tipo)==5)<img src="{{asset('/')}}hc4/img/ejecutiva_bloqueada.png" style="width: 25px; "> @endif</td>
                <!--FILA DE TIPO DE HABITACION-->
                <td>@if(($value->id_tipo)==1) SIMPLE @elseif(($value->id_tipo)==2)DOBLES @elseif(($value->id_tipo)==3)SUITE @elseif(($value->id_tipo)==4)TRIPLE @elseif(($value->id_tipo)==5)EJECUTIVA @endif</td>
                <!--FILA DE TIPO DE PISO-->
                <td>@if(($value->id_piso)==1)PRIMER PISO @elseif(($value->id_piso)==2)SEGUNDO PISO @elseif(($value->id_piso)==3)TERCER PISO @endif</td>
                <!--FILA DE ESTADO DE HABITACION no va ir porque solo en editar modificas el estado de habitacion-->
                <!--FILA DE NUMERO DE HABITACION-->
                <td>{{$value->codigo}}</td>
                <!--FILA DE ESTADO DE CAMA-->
                <td>
                  <!--ESTA CEDA TIENE UNA TABLA ADENTRO-->
                  <table class="table table-sm">
                    <tr>
                      <!--PRESENTA EL NUMERO DE LA CAMA-->
                      @foreach(($value->cama) as $val)
                      <td> <span>CAMA: {{$val->codigo}}</span></td>
                      <!--ESTADO DE LA CAMAS--->
                      <td class="text-white" @if(($val->estado)==4) bgcolor='#D5DBDB'
                        @elseif(($val->estado==1)) bgcolor='#63D73E'
                        @elseif(($val->estado)==2) bgcolor='#E8FF00'
                        @elseif(($val->estado)==3) bgcolor='#FE1912' @endif>
                        @if(($val->estado)==1) LIBRE
                        @elseif(($val->estado)==2) PREPARACI&Oacute;N
                        @elseif(($val->estado)==3) OCUPADA
                        @elseif(($val->estado)==4) NO DISPONIBLE @endif</td>
                    </tr>
                    @endforeach
                  </table>
                </td>
                <!--FILA DE BOTON DE EDITAR-->
                <td><input type="hidden">
                  <button type="button" onclick="modal_newdata({{$value->id}})" class="btn btn-sm btn-warning"><i class="far fa-edit"></i> Editar</button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <!--aqui va el paginate-->
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($habitacion)}} de {{$habitacion->total()}} registros</div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $habitacion->links() }}
              </div>
            </div>
          </div>
          <!--Fin de la paginacion-->
        </div>
      </div>
    </div>
  </div>
</div>



<!-- <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>-->


<script type="text/javascript">
  function showmodal() {
    $.ajax({
      type: 'get',
      url: "{{ route('hospital_admin.modalagcuarto')}}",
      datatype: 'json',
      success: function(data) {
        $('#date').empty().html(data);
        $('#modalnew').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });
  }

  function modal_newdata(da) {
    $.ajax({
      type: 'get',
      url: "{{ url('hospital/admin/gestionh/editarh/')}}/" + da,
      datatype: 'json',
      success: function(data) {
        $('#date2').empty().html(data);
        $('#modalnew2').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });
  }
</script>

@endsection