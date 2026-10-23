@extends('contable.cierre.base')
@section('action-content')
<!-- Main content -->
<section class="content">
<div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title"> Administracion de Cierres de Registros Contable </h3>
        </div>
        <div class="col-sm-4">
           <a  href="{{route('cierre.mensual.create')}}"  class="btn btn-primary btn-gray"><i aria-hidden="true"></i> Agregar Nuevo Cierre</a>
        </div>
    </div>
  </div>
  <div class="modal fade" id="modal_eliminar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 95%;">
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <div class="row">
      <div class="col-sm-6"></div>
      <div class="col-sm-6"></div>
    </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr class="well-dark" >
                <th width="40%" tabindex="0"  >Motivo</th>
                <th width="15%" tabindex="0"  >Fecha Cierre</th>
                <th width="15%" tabindex="0"  >Fecha Creacion</th>
                <th width="20%" tabindex="0"  >Usuario</th>
                <th width="10%" tabindex="0"  >@lang('agenda.accion')</th>
              </tr>
            </thead>
            <tbody>
                @php
                  $ultimo_id = "";
                @endphp
                @foreach ($cierres as $key => $cierre)
                <tr role="row" class="odd">                  
                  <td class="sorting_1">{{ $cierre->observacion }}</td>
                  <td class="sorting_2">{{ substr($cierre->fecha_cierre, 0, 10) }}</td>
                  <td class="sorting_3">{{$cierre->created_at}}</td>
                  <td class="sorting_3">{{ $cierre->usuario_crea->nombre1}} {{ $cierre->usuario_crea->apellido1}}</td>
                  <td align="center">
                    @if($key == 0)
                      @php
                         $ultimo_id = $cierre->id; 
                      @endphp
                      <a type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal_eliminar" data-remote="{{route('cierre.mensual.edit', ['id'=>$cierre->id])}}"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></a>
                    @endif 
                  </td>
                </tr>
                @endforeach            
            </tbody>
          </table>
        </div>
      </div>
    <div class="row">
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">@lang('laboratorio.mostrando') {{1 + (($cierres->currentPage() - 1) * $cierres->perPage())}} / {{count($cierres) + (($cierres->currentPage() - 1) * $cierres->perPage())}} @lang('laboratorio.de') {{$cierres->total()}} @lang('laboratorio.registros')
            </div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{$cierres->appends(Request::only(['descripcion','estado']))->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<script type="text/javascript">
  $('#modal_eliminar').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
      //tinymce.remove('.text_input');
  });
</script>
<script>
  function eliminar() {
    var confirmar = confirm('Esta seguro que desea eliminar el registro?, al eliminarlo podran ingresar cualquier registro menor a la fecha de cierre.');
    if(confirmar) {
            $.ajax({
                @if($ultimo_id != "")
                  url: "{{route('cierre.mensual.edit', ['id' => $ultimo_id])}}",
                @else
                  url: "",
                @endif
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('cierre.mensual.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Eliminado!", "trans('turnerof.correcto')", "success");
                            window.location = url;
                        }, 2000);
                    }     
                },
                error: function(xhr, status) {
                    alert(`{{trans('laboratorio.existioproblema')}}`);
                    //console.log(xhr);
                },
            });
        }
    }  
</script>
@endsection