@extends('hospital_admin.base')
@section('action-content')

<div class="modal fade" id="modalmarcas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<button type="button" data-remote="{{ route('hospital_admin.modalmarcas')}}" class="btn btn-sm btn-info my-2" data-toggle="modal" data-nombre="crear" data-target="#modalmarcas"><i class="fas fa-plus"></i> Agregar marca</button>

<div class="modal fade" id="modaleditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<a type="button" href="{{route('hospital_admin.farmacia') }}" class="btn btn-sm btn-primary my-2"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>

<div class="row">
  <div class="col-md-12">
    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Sesión de Marcas</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="collapseCardExample">
        <div class="card-body">
          <table class="table table-bordered">
            <tbody>
              <tr class="text-dark">
                
                <th>Nombre</th>
                <th>Descripcion</th>
                <th>Estado</th>
                <th>Acción</th>
              </tr>
              @foreach ($marcas as $value)
                <tr role="row" class="odd">
                  <td> {{ $value->nombre}}</td>
                  <td>{{ $value->descripcion }}</td>
                  <td>@if($value->estado==1) ACTIVO @elseif($value->estado==2) INACTIVO @endif</td>
                  <td><button data-remote="{{ route('hospital_admin.editarm', ['id' => $value->id]) }}" data-toggle="modal" data-nombre="ver" data-target="#modaleditar" class="btn btn-sm btn-warning"><i class="far fa-edit"></i> Editar</button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        
        <!--aqui va el paginate-->
          {{ $marcas->links() }}
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