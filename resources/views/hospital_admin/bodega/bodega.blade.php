@extends('hospital_admin.base')
@section('action-content')

<a type="button" href="{{ route('hospital_admin.agregar')}}" class="btn btn-sm btn-info my-2"><i class="fas fa-plus"></i> Crear bodega</a>
<a type="button" href="{{route('hospital_admin.farmacia') }}" class="btn btn-sm btn-primary my-2"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>

<div class="row">
  <div class="col-md-12">

    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4">
      <!-- Card Header - Accordion -->
      <a class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Bodega</h6>
      </a>
      <!-- Card Content - Collapse -->
      <div class="collapse show" id="collapseCardExample">
        <div class="card-body">
          <table class="table table-bordered">
            <tbody>
              <tr role="row">
                <th>Nombre</th>
                <th>Ubicacion</th>
                <th>Piso</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Color</th>
                <th>Accion</th>
              </tr>
              @foreach ($bodega as $value)
                <tr role="row" class="odd" style="text-align:center;">
                  <td> {{$value->nombre}}</td>
                  <td> {{$value->ubicacion }}</td>
                  <td> @if(($value->id_piso)==1)piso 1 @elseif (($value->id_piso)==2) piso 2 @elseif (($value->id_piso)==3) piso 3  @endif</td>
                  <td bgcolor={{$value->color}}></td>
                  <td><a href="{{ route('hospital_admin.editarb', ['id' => $value->id]) }}" data-target="#editarb" class="btn btn-sm btn-warning"><i class="far fa-edit"></i> Editar</a></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        
          <!--aqui va el paginate-->
            {{ $bodega->links()}}
          <!--Fin de la paginacion-->
        </div>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
$('#example2').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : true,
    'info'        : false,
    'autoWidth'   : false
  })
</script> 
@endsection