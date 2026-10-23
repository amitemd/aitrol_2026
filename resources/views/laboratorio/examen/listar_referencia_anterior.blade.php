

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding-left: 0;
    padding-top: 0;
    padding-right: 3px;
    padding-bottom: 0;
} 
</style>
<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
    <!-- Main content -->
    <section class="content">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <thead>
                    <tr role="row">
                     
                      <th >@lang('laboratorio.fecha')</th>
                      <th >@lang('laboratorio.valor')</th>
                      <th >@lang('laboratorio.unidad')</th>
                      <th >@lang('laboratorio.referencia')</th>
                      <th >@lang('laboratorio.accion')</th>
                    </tr>
                  </thead>
                  <tbody>
                  
                  @foreach ($parametros_anteriores as $value)
                    <tr role="row">
                     
                       <td style="font-size: 10px;">
                        {{$value->fecha_valida}}
                      </td>
                      <td>{{$value->valor1}} - {{$value->valor1g}}</td>
                      <td>{{$value->unidad1}}</td>
                      <td><?php echo $value->texto_referencia; ?></td>
                      <td>
                        <!--a href="{{ route('examen.edit', ['id' => $value->id]) }}" class="btn btn-warning btn-xs" >
                        <span class="glyphicon glyphicon-edit"></span>
                        </a-->  
                        <!--a href="{{ route('examen.parametro',['id_examen' => $value->id]) }}" class="btn btn-success btn-xs" >
                          P
                        </a-->
                      </td>          
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            
          </div>
    </section>
    <!-- /.content -->
  

<script type="text/javascript">

  $(document).ready(function($){

    $(".breadcrumb").append('<li class="active">Exámenes</li>');

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      "order": [[ 0, 'asc' ]]
    })



  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 

</script>  
