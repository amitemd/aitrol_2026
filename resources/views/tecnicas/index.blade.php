

@extends('tecnicas.base')
@section('action-content')
<style type="text/css">

tbody>tr:hover{
  background-color: #b3ffff;
}  

</style>


    
    <section class="content">
      <div class="box box-primary">
        <div class="box-header">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-8">
                <h3 class="box-title">Lista de Procedimientos Completos</h3>
              </div>
              <form method="POST" action="{{ route('tecnicas.search', ['agenda' => $agenda]) }}" >
                {{ csrf_field() }}
                <div class="col-md-6" style="padding: 1px;">
                  <label for="proc_com" class="control-label col-md-6">Buscar Procedimiento</label>
                  <div class="col-md-6">
                    <select class="form-control input-sm select2"  name="proc_com" onchange="" style="width: 100%;">
                      <option value="">Todos</option> 
                      @foreach($proc_completo as $value)    
                      <option @if($value->id == $procedimiento_completo) selected @endif value="{{$value->id}}">{{$value->nombre_general}}</option>
                      @endforeach    
                    </select> 
                  </div>     
                </div>
                <div class="form-group col-md-1 col-xs-2" >
                  <button type="submit" class="btn btn-primary" id="boton_buscar">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
                </div>
              </form>  
              <div class="col-md-2 ">
                <a type="button" class="btn btn-primary btn-sm" href="{{ route('tecnicas.create',['agenda' => $agenda]) }}" ><span class="glyphicon glyphicon-plus"> Crear</span></a>
              </div>
              <div class="col-md-2" style="padding-left: 0px;padding-right: 0px;">
                <a type="button" href="{{route('agenda.detalle', ['agenda' => $agenda ])}}" class="btn btn-success btn-sm">
                    <span class="glyphicon glyphicon-arrow-left"> Historia Clínica</span>
                </a>
              </div>
            </div>
          </div>
        </div>
  
        <div class="box-body">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th  width="15%">Nombre</th>
                      <th  width="50%">Hallazgo</th>                      
                      <th  width="5%">Grupo</th>
                      <th  width="5%">Record</th>
                      <th  width="5%">Estado</th>
                      <!--th  width="5%">Acción</th-->
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($tecnicas_quirurgicas as $value)
                      <tr class="clickable-row" data-href="{{ route('tecnicas.edit', ['id' => $value->id, 'agenda' => $agenda]) }}">
                        <td style="font-size: 12px;">{{ $value->nombre_completo }}</td>
                        <td style="font-size: 12px;"><?php echo $value->tecnica_quirurgica;?></td>                        
                        <td style="font-size: 12px;">{{ $value->grupo_procedimiento->nombre }}</td>
                        <!--<td>{{ $value->precio_compra }}</td>-->
                        <td style="text-align: center;vertical-align: middle;">@if($value->estado_anestesia == 0){{"No"}}@endif @if($value->estado_anestesia == 1){{"Si"}}@endif</td>
                        <td style="text-align: center;vertical-align: middle;">@if($value->estado == 0){{"Inactivo"}}@endif @if($value->estado == 1){{"Activo"}}@endif</td>
                        <!--td>
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <a href="{{ route('tecnicas.edit', ['id' => $value->id, 'agenda' => $agenda]) }}" class="btn btn-warning btn-xs">
                              Actualizar
                              </a>
                        </td-->
                    </tr>
                  @endforeach 
                  </tbody>
                  
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($tecnicas_quirurgicas->currentPage()-1)*$tecnicas_quirurgicas->perPage()}}  / @if(($tecnicas_quirurgicas->currentPage()*$tecnicas_quirurgicas->perPage())<$tecnicas_quirurgicas->total()){{($tecnicas_quirurgicas->currentPage()*$tecnicas_quirurgicas->perPage())}} @else {{$tecnicas_quirurgicas->total()}} @endif de {{$tecnicas_quirurgicas->total()}} registros
                </div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $tecnicas_quirurgicas->links() }}
                </div>
              </div>
            </div>
          </div>
        </div>
 
      </div>
    </section>

  

  <script type="text/javascript">
    

    $(document).ready(function(){

      $('.select2').select2({
        tags: false
      });


      $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      });

      $(".clickable-row").click(function() {
          window.location = $(this).data("href");
      });

      $(".breadcrumb").append('<li class="active">Procedimientos</li>');
 
    
    });


  </script>
@endsection