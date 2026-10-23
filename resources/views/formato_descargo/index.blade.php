@extends('formato_descargo.base')
@section('action-content')
<!-- Ventana modal editar -->

  <!-- Main content -->
  <section class="content">
    <div class="box" style=" background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-6">
              <h3 class="box-title">Fichero de Planillas </h3>
            </div>

            <div class="col-md-2">
              <button type="button" onclick="location.href='{{route('formatosProductos.create')}}'" class="btn btn-danger btn-gray" >
                   <i aria-hidden="true"></i>Agregar Plantilla 
              </button>
            </div>
        </div>
      <!-- /.box-header -->

      <div class="box-body">

        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th >Id</th>
                      <th >Descripcion</th>
                      <th >Nota</th>
                      <th >Estado</th>
                      <th >Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($formato as $x)
                        <tr>
                          <td>{{$x->id}}</td>
                          <td>{{$x->descripcion}}</td>
                          <td>{{$x->nota}}</td>
                          <td>@if($x->estado==1) ACTIVO @else INACTIVO @endif</td>
                          <td> <a href="{{route('formatosProductos.edit',['id'=>$x->id])}}" class="btn btn-info btn-gray" > <i class="fa fa-pencil"></i> </a> </td>
                        </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                 
                </div>
              </div>
            </div>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
  </section>
  <!-- /.content -->

<script type="text/javascript">
    
    $(document).ready(function(){

      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : true,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false
      });

    });
</script>
@endsection
