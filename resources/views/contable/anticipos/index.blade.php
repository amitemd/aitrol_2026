@extends('contable.anticipos.base')
@section('action-content')
<!-- Ventana modal editar -->
<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
      window.history.back();
    }

</script>
<div class="modal fade" id="modal_anticipos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
  <!-- Main content -->
  <section class="content">
    <div class="box size_text" style=" background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
            <div class="col-md-3">
              <h3 class="box-title">Crear Anticipo</h3>
            </div>

            <div class="col-md-6">
                     <a href="{{route('anticipo_create')}}" data-toggle="modal" data-target="#modal_anticipos" class="btn btn-warning col-md-3 col-xs-3 btn-margin">Agregar Anticipo</a>
            </div>
            <div class="col-md-3">
               <button type="button" onclick="goBack()" class="btn btn-primary" style="color:white; border-radius: 5px; border: 2px solid white;">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
               </button>
              
            </div>
        </div>
      <!-- /.box-header -->
      <div class="box-body">
        <form method="POST" action="{{route('compra_search')}}">
          {{ csrf_field() }}
          @component('layouts.search', ['title' => 'Buscar'])
            @component('layouts.two-cols-search-row', ['items' => ['Autorizacion', 'Proveedor'],
            'oldVals' => [isset($searchingVals) ? $searchingVals['autorizacion'] : '', isset($searchingVals) ? $searchingVals['p.nombrecomercial'] : '']])
            @endcomponent
            </br>
          @endcomponent
        </form>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap size_text">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th >#</th>
                      <th >Proveedor</th>
                      <th >Nro Anticipo</th>
                      <th >Monto</th>
                      <th >Monto Contable</th>
                      <th >Estado</th>
                      <th >Fecha</th>
                      <th >Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($anticipo as $value)
                        <tr>
                            <td>@if(!is_null($value->id)){{$value->id}}@endif</td>
                            <td>@if(!is_null($value->nro_comprobante)){{$value->nro_comprobante}}@endif</td>
                            <td>@if(!is_null($value->total)){{$value->total}}@endif</td>
                            <td>@if(!is_null($value->total_disponible)){{$value->total_disponible}}@endif</td>
                            <td>@if(!is_null($value->estado)) @if($value->estado!=0) ACTIVO @else INACTIVO  @endif @endif</td>
                            <td>@if(!is_null($value->observacion)) {{$value->observacion}} @endif</td>
                            <td>@if(!is_null($value->fecha_pago)) {{$value->fecha_pago}} @endif</td>
                            <td>@if(!is_null($value)) <a class="btn btn-primary" href="{{route('pdf_comprobante_anticipo',['id'=>$value->id])}}">PDF comp</a> @endif</td>
                        </tr>

                    @endforeach
                  
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
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
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true,
      'sInfoEmpty':  true,
      'sInfoFiltered': true,
      'language': {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

    });
    $('#modal_anticipos').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
     });

</script>
@endsection
