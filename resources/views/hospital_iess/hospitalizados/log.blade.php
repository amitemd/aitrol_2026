<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class="modal-header">
  <!--button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button-->
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">LOG</h4>
  <h4 class="modal-title" style="text-align: center;"><b>PACIENTE:</b> {{$paciente->id}} {{$paciente->nombre1}} {{$paciente->apellido1}} </h4>
    
</div>
 
<div class="modal-body"> 
    
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th >Fecha</th>
                <th >Descripción</th>
                <th >Campos Antes</th>
                <th >Campos Después</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($logs as $value)
                <tr role="row">
                  <td >{{$value->created_at}}</td>
                  <td >{{$value->descripcion2}}</td>
                  <td >{{$value->campos_ant}}</td>
                  <td >{{$value->campos}}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
    </div>    

</div>  

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
$(document).ready(function() 
{

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })    
        

});





</script>        