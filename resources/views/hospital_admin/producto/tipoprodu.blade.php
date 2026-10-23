@extends('hospital_admin.base')
@section('action-content')

<!-- MODAL TIPO DE PRODUCTO -->
<div class="modal fade" id="modaltipoproducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<button type="button" data-remote="{{ route('hospital_admin.modaltipop')}}" class="btn btn-sm btn-info my-2" data-toggle="modal" data-nombre="crear" data-target="#modaltipoproducto"><i class="fas fa-plus"></i> Agregar tipo de medicina</button>

<!-- final modal tipo de producto -->

<!-- MODAL EDITAR -->
<div class="modal fade" id="modaltipoproductot" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<!-- final / MODAL EDITAR -->


<a type="button" href="{{route('hospital_admin.farmacia') }}" class="btn btn-sm btn-primary my-2"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>

<!-- tabla de medicica content -->
<div class="row">
  <div class="col-md-12" id="info">

    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Tipo de producto</h6>
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
              @foreach ($tipoproduc as $value)
                <tr role="row" class="odd">
                  <td>{{ $value->nombre}}</td>
                  <td>{{ $value->descripcion }}</td>
                  <td>@if($value->estado==1) ACTIVO @elseif($value->estado==2) INACTIVO @endif</td> 
                  <td><button data-remote="{{ route('hospital_admin.editartip', ['id' => $value->id]) }}" data-toggle="modal" data-nombre="ver" data-target="#modaltipoproductot" class="btn btn-sm btn-warning"><i class="far fa-edit"></i> Editar</button></td> 
                </tr>
              @endforeach
            </tbody>
          </table>
        <!--aqui va el paginate-->
          {{ $tipoproduc->links() }}
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

<!-- fianl / tabla de medicina content -->
@endsection