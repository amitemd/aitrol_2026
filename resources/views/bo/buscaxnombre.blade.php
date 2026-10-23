@extends('bo.base_agenda')
@section('action-content')
    <!-- Main content -->
    <section class="content">
      <div class="box box-primary">
        <div class="box-header">
          <div class="row">
              <div class="col-sm-8">
                <h3 class="box-title">BUSCA PACIENTE POR NOMBRE</h3>
              </div>
              
          </div>
        </div>
      <!-- /.box-header -->
        <div class="box-body">
        
          <form method="POST" action="{{route('solicitud.search_paciente')}}">
             {{ csrf_field() }}
             <input type="hidden" value="{{$id_doc}}" name="id_doc" id="id_doc">
             <input type="hidden" value="{{$fecha}}" name="fecha" id="fecha">

             @component('paciente.buscar', ['title' => 'Buscar'])
              @component('paciente.nombre', ['items' => ['Nombres'], 
              'oldVals' => [isset($searchingVals) ? $searchingVals['nombre1'] : '' ]])

              @endcomponent
            @endcomponent
          </form>  
      
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr role="row">
                       
                      <th>Nombres</th>
                      <th>Cédula</th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($paciente as $value)
                      <tr>
                         
                        <td><a  href="{{ route('solicitud.agendar', ['id' => $id_doc, 'fecha' => $fecha, 'pac' => $value->id ])}}">{{$value->nombre1}} @if($value->nombre2!='(N/A)'){{$value->nombre2}}@endif {{$value->apellido1}} @if($value->apellido2!='(N/A)'){{$value->apellido2}}@endif</a></td>
                        <td><a href="{{ route('solicitud.agendar', ['id' => $id_doc, 'fecha' => $fecha, 'pac' => $value->id ])}}">{{ $value->id}}</a></td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($paciente->currentPage()-1)*$paciente->perPage()}}  / @if(($paciente->currentPage()*$paciente->perPage())<$paciente->total()){{($paciente->currentPage()*$paciente->perPage())}} @else {{$paciente->total()}} @endif de {{$paciente->total()}} registros</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $paciente->appends(Request::only(['nombres']))->links()}}
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

  $('#editMaxPacientes').on('hidden.bs.modal', function(){
                $(this).removeData('bs.modal');
            });

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