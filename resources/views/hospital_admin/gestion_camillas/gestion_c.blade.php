@extends('hospital_admin.base')
@section('action-content')
<!-- crear una habitacion -->
<div class="modal fade" id="modalagcuarto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

      </div>
    </div>
</div>

<button type="button" data-remote="{{ route('hospital_admin.modalagcuarto')}}" class="btn btn-sm btn-info my-2 btn-icon-split" data-nombre="crear" data-toggle="modal" data-target="#modalagcuarto">
    <span class="icon text-white-50">
      <i class="fas fa-check"></i>
    </span>
    <span class="text">Camillas</span>
</button>
<!-- FINAL / crear una habitacion -->

<!-- Editar -->
<div class="modal fade" id="modaleditarh" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

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
        <h6 class="m-0 font-weight-bold text-primary">Gestión de camillas</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="collapseCardExample">
        <div class="card-body">
          
        <table class="table table-bordered">
          <tbody>
            <tr class="text-dark">
              <th style="width: 10px">#</th>
              <th>Camillas</th>
              <th>Categorias</th>
              <th>Piso</th>
              <th>Número <br> Habitación</th>
              <th>Estado-Cama</th>
              <th>Acción</th>
            </tr>
            @foreach($habitacion as $value)
              <tr>
                <td>{{$value->id}}</td>
                <!--FILA DE TIPO DE HABITACION IMAGEN DE REFERENCIA-->
                <td>@if(($value->id_tipo)==1) <img src="{{asset('/')}}hc4/img/simple_block.png" style="width: 25px;"> @elseif(($value->id_tipo)==2) <img src="{{asset('/')}}hc4/img/Doble_Bloqueda.png" style="width: 50px; "> @elseif(($value->id_tipo)==3)<img src="{{asset('/')}}hc4/img/Suite.png" style="width: 50px; "> 
                @elseif(($value->id_tipo)==4)<img src="{{asset('/')}}hc4/img/Triple_Bloqueada.png" style="width: 75px; ">  @elseif(($value->id_tipo)==5)<img src="{{asset('/')}}hc4/img/Ejecutiva_Bloqueada.png" style="width: 25px; "> @endif</td>
                <!--FILA DE TIPO DE HABITACION-->
                <td>@if(($value->id_tipo)==1) SIMPLE @elseif(($value->id_tipo)==2)DOBLES @elseif(($value->id_tipo)==3)SUITE @elseif(($value->id_tipo)==4)TRIPLE @elseif(($value->id_tipo)==5)EJECUTIVA @endif</td>
                <!--FILA DE TIPO DE PISO-->
                <td>@if(($value->id_piso)==1)PRIMER PISO @elseif(($value->id_piso)==2)SEGUNDO PISO @elseif(($value->id_piso)==3)TERCER PISO @endif</td>
                <!--FILA DE ESTADO DE HABITACION no va ir porque solo en editar modificas el estado de habitacion-->
                <!--FILA DE NUMERO DE HABITACION--> 
                <td>{{$value->codigo}}</td>
                <!--FILA DE ESTADO DE CAMA-->
                <td> <!--ESTA CEDA TIENE UNA TABLA ADENTRO-->
                  <table class="table table-sm">
                    <tr>
                      <!--PRESENTA EL NUMERO DE LA CAMA-->
                      @foreach(($value->cama) as $val)
                      <td > <span>CAMA: {{$val->codigo}}</span></td>
                      <!--ESTADO DE LA CAMAS--->
                      <td class="text-white"
                          @if(($val->estado)==4) bgcolor='#D5DBDB' 
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
                  <button data-remote="{{ route('hospital_admin.editarc', ['id' => $value->id]) }}" data-toggle="modal" data-nombre="ver" data-target="#modaleditarh" class="btn btn-sm btn-warning"><i class="far fa-edit"></i> Editar</button>
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
              {{ $habitacion->links() }}
          </div>
        </div>
        <!--Fin de la paginacion-->
        </div>
      </div>
    </div>
  </div>
</div>



<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>


<script type="text/javascript">
  jQuery('body').on('click', '[data-toggle="modal"]', function() {
    var remoto_href="crear";
    console.log(jQuery(this).data('remote'));
    console.log(remoto_href);
    if(remoto_href != jQuery(this).data('nombre')) {
      remoto_href = jQuery(this).data('remote');
      jQuery(jQuery(this).data('target')).removeData('bs.modal');

      jQuery(jQuery(this).data('target')).find('.modal-body').empty();
      jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
    }
	});
</script>

@endsection