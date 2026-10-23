@extends('titulo_profesional.base')
@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title"> @lang('turnerof.mantituprof') </h3>
        </div>
        <div class="col-sm-4">
           <a  href="{{route('tituloprofesional.create')}}"  class="btn btn-primary btn-gray"><i aria-hidden="true"></i> @lang('turnerof.aggnuevtitu')</a>
        </div>
    </div>
  </div>
  
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <form method="POST" action="">
         {{ csrf_field() }}

      </form>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
            <tr class="well-dark" >
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">@lang('turnerof.tituprof')</th>
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">@lang('turnerof.preftitulo')</th>
                <th width="25%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">@lang('agenda.estado')</th>
                <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="2" aria-label="Action: activate to sort column ascending">@lang('agenda.accion')</th>
                
              </tr>
            </thead>
            <tbody>
                @foreach ($titulos as $titulo)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">{{ $titulo->titulo_universitario }}</td>
                  <td class="sorting_2">{{ $titulo->titulo_prefijo }}</td>
                  <td class="sorting_3">
                  @if($titulo->estado == 1)
                  @lang('agenda.activo')
                                @elseif($titulo->estado == 0)
                                @lang('agenda.inactivo')
                                @endif
                  <td align="center">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <a href="{{ route('tituloprofesional.edit', ['id' => $titulo->id])}}" class="btn btn-success btn-gray">
                        <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                        </a>
                        <button onclick="eliminar()" type="button" class="btn btn-danger btn-red">
                        <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>          
                        </button> 
                  </td>
              </tr>
              
            @endforeach            
            </tbody>
            <tfoot>
              
            </tfoot>
          </table>
        </div>
      </div>
    <div class="row">
    <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">@lang('laboratorio.mostrando') {{1 + (($titulos->currentPage() - 1) * $titulos->perPage())}} / {{count($titulos) + (($titulos->currentPage() - 1) * $titulos->perPage())}} @lang('laboratorio.de') {{$titulos->total()}} @lang('laboratorio.registros')
                     </div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{$titulos->appends(Request::only(['descripcion','estado']))->links() }}
                    </div>
                  </div>
                </div>
  </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  function eliminar() {
    var confirmar = confirm(`{{trans('turnerof.seguroquiereliminar')}}`);
    if(confirmar) {
            $.ajax({
                url: "{{route('tituloprofesional.delete', ['id' => $titulo->id])}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('tituloprofesional.index')}}"
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