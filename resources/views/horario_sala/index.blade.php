@extends('horario_sala.base')
@section('action-content')

<section class="content">
    <div class="box">
      <div class="box-header">
      </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-1 text-right" style="margin-bottom: 15px">
                <button type="button" onclick="location.href='{{route('salas.crear')}}'" class="btn btn-success">
                     <i aria-hidden="true"></i>Agregar Sala 
                </button>
              </div>
          <div class="col-sm-6"></div>
        </div>
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row">
          <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Sala</th>
                  <th>Acción</th>                    
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table> 
  
          </div>
        </div>
      </div>
    </div>
    <!-- /.box-body -->
  </div>
      </section>
      <!-- /.content -->
    </div>
  
  
  
  
  
  <script type="text/javascript">
  
  
    $(document).ready(function() 
      {
          $(".breadcrumb").append('<li class="active">Agenda</li>');
      });
  
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