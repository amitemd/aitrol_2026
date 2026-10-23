

@extends('rrhh.resultados.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Lista Resultados de Sugerencias</h3>
        </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
    <!--AQUI VA EL BUSCADOR-->
    <form method="POST" action="{{route('sugerencia.search')}}">
        {{ csrf_field() }}
         
          <div class="form-group col-md-4 col-xs-6">
            <label for="fecha" class="col-md-3 control-label">Fecha Desde</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>   
              </div>
            </div>  
          </div>

          <div class="form-group col-md-4 col-xs-6">
            <label for="fecha_hasta" class="col-md-3 control-label">Fecha Hasta</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>   
              </div>
            </div>  
          </div>
          <div class="form-group col-md-4 col-xs-6">
            <label for="nombres" class="col-md-3 control-label">Áreas</label>
            <div class="col-md-9">
              <div class="input-group">
                <select id="id_area" name="id_area" class="form-control input-sm" >
                  <option value="">Seleccione ...</option>
                  @foreach ($area as $value)
                  <option @if($id_area == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>  
            </div>
          </div> 
          <div class="form-group col-md-4 col-xs-6">
            <label for="nombres" class="col-md-4 control-label">Tipo de Ingreso</label>
            <div class="col-md-8">
              <div class="input-group">
                <select id="id_tiposugerencia" name="id_tiposugerencia" class="form-control input-sm" >
                  <option value="">Seleccione ...</option>
                  @foreach ($tiposugerencia as $value)
                  <option @if($id_tiposugerencia == $value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                  @endforeach
                </select>
              </div>  
            </div>
          </div>
          <div class="form-group col-md-2 col-xs-6">
            <button type="submit" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
          </div>      
    
      </form>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="col-sm-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
              <tr role="row">
            
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Tipo de informacion</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Area de atencion</th>
                <th width="50%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Observacion </th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Fecha </th>
              </tr>
            </thead>
            <tbody>
            @foreach ($sugerencia as $value)
                <tr role="row" class="odd">
                  
                  <td class="sorting_1">{{ $value->tiposugerencia->nombre }}</td>
                  <td> {{ $value->area->nombre }}</td>
                  <td> {{ $value->observacion}}</td>
                  <td>{{ $value->created_at }}</td>
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
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($sugerencia)}} de {{count($sugerencia)}} registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $sugerencia->links() }}
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

  <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function($){

    $('#fecha').datetimepicker({
            format: 'YYYY-MM-DD',
            
            
            defaultDate: '{{$fecha}}',
            
            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY-MM-DD',
            
            
            defaultDate: '{{$fecha_hasta}}',
            
            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

         $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });

    $(".breadcrumb").append('<li class="active">Órdenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 


   function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}

</script>  

@endsection