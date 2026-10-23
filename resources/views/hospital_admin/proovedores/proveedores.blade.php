@extends('hospital_admin.base')
@section('action-content')

<div class="modal fade" id="modals"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<button type="button" data-remote="{{ route('hospital_admin.modalprovedor')}}" class="btn btn-sm btn-info my-2" data-toggle="modal" data-target="#modals"><i class="fas fa-plus"></i> Agregar proveedores</button>

<div class="modal fade" id="modalsd"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<button type="button" data-remote="{{ route('hospital_admin.modalprovedord')}}"  class="btn btn-sm btn-success my-2" data-toggle="modal" data-target="#modalsd"><i class="far fa-plus-square"></i> Crear proveedores</button>

<div class="modal fade" id="modalst" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<a type="button" href="{{route('hospital_admin.farmacia') }}" class="btn btn-sm btn-primary my-2"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>

<div class="row">
  <div class="col-md-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de proveedores</h6>
      </div>
      <div class="card-body">
        
        <table class="table table-bordered">
          <tbody>
            <tr class="text-dark">
              <th>Logo</th>
              <th>Nombre Comercial</th>
              <th>Raz&oacute;n social</th>
              <th>Ruc</th>
              <th>Email</th>
              <th>Tipo Proveedor</th>
              <th>Acci&oacute;n</th>
            </tr>
            @foreach ($proovedor as $value)
              <tr role="row" class="odd">
                <td><img src="{{asset('/logo').'/'.$value->logo}}" style="width:80px;height:80px;" alt="Logo Image"></td>
                <td> {{ $value->nombrecomercial}}</td>
                <td> {{ $value->razonsocial}}</td>
                <td> {{ $value->ruc}}</td>
                <td> {{ $value->email}}</td>
                <td> @if(($value->id_tipoproveedor)==1) Takeda Mexico @elseif (($value->id_tipoproveedor)==2) Roche @elseif (($value->id_tipoproveedor)==3) ICN Farmacéutica @elseif (($value->id_tipoproveedor)==4) farmacia  @endif </td>  
                <td><button data-remote="{{ route('hospital_admin.modaleditarpr', ['id' => $value->id]) }}" data-toggle="modal" data-target="#modalst" class="btn btn-sm btn-warning"><i class="far fa-edit"></i> Editar</button></td>         
              </tr>
            @endforeach
          </tbody>
        </table>
        <!--aqui va el paginate-->
        {{ $proovedor->links()}}
        <!--Fin de la paginacion-->
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