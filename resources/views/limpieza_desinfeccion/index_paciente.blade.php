<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<div class="modal fade" id="nuevo2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>

<section class="content">
  <div class="box" style="border: none;">
   
      <div class="box-body">
        <div class="row">
          <div class="form-group col-md-6 ">
              <div class="row" >
                <div class="form-group col-md-10 ">
                        <h4>@if(!is_null($sala)){{$sala->nombre_sala}}@endif</h4>    
                </div>
              </div>
          </div>
          <form id="form_fecha" method="POST" >
            {{ csrf_field() }}
            <div class="form-group col-md-2 col-xs-2">
                <div class="col-md-9">
                  <div class="input-group date">
                    
                    <input type="hidden" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off" placeholder="AAAA/MM/DD" value="{{$fecha}}">
                    <input type="hidden" class="form-control input-sm" name="hasta" id="hasta" autocomplete="off" placeholder="AAAA/MM/DD" value="{{$fecha_2}}">
                   
                  </div>
                </div> 
            </div>
            <div class="form-group col-md-4 ">

                <a id="crear_gastro1" class="btn btn-success btn-xs oculto" href="{{route('limpieza.imprimir_excel',['id' => $id, '1'])}}"><span class="glyphicon glyphicon-download-alt"></span> Exportar Endoscopynet.</a>
                <a id="crear_gastro" class="btn btn-primary form-control" onclick="exportar_excel_gastro('{{$id}}','1')"><span class="glyphicon glyphicon-download-alt"></span> Exportar Endoscopynet</a>

                <!-- <a id="crear_crm1" class="btn btn-success btn-xs oculto" href="{{route('limpieza.imprimir_excel',['id' => $id, '2'])}}"><span class="glyphicon glyphicon-download-alt"></span> </a>
                <a id="crear_crm" class="btn btn-primary form-control" onclick="exportar_excel_crm('{{$id}}','2')"><span class="glyphicon glyphicon-download-alt"></span> </a> -->
           
                               
            </div>
          </form>
            <!--div class="col-md-2">
                  <a class="btn btn-primary" data-toggle="modal" data-target="#nuevo2" href="{{route('limpieza.crear2',['id_sala' =>$id])}}" >Registro</a>
            </-->
        </div>



          <div id="example3_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" id="listado">
              <div class="table-responsive col-md-12">
                <table id="example3" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr>
                      <th>Cedula</th>
                      <th>Apellidos</th>
                      <th>Nombres</th>
                      <th>Fecha</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($p_limpieza as $p_limp)
                      @if($p_limp->nom_limp == 1)
                        <tr>
                          <td colspan="3" style="text-align: center;">Registro Inicial</td>
                          <td>{{$p_limp->created_at}}</td>
                          <td><a id="boton_editar" class="btn btn-info btn-xs" href="{{route('limpieza.editar',['id' => $p_limp->id ])}}" data-toggle="modal" data-target="#edit"><span>Revisar Registro</span></a> </td>
                        </tr>
                      @endif
                    @endforeach
                    @foreach($pacientes as $value)
                      @php
                        $limpieza = Sis_medico\Limpieza::where('id_paciente', $value->id_paciente)->where('id_pentax', $value->id_pentax)->where('estado','1')->first();
                      @endphp

                      
                      <tr>
                        <td>{{$value->paciente->id}}</td>
                        <td>{{$value->paciente->apellido1}} {{$value->paciente->apellido2}}</td>
                        <td>{{$value->paciente->nombre1}} {{$value->paciente->nombre2}} </td>
                        <td>{{$value->fechaini}}</td>
                        <td> 
                          <!-- <a id="boton_index" target="_blank" class="btn btn-primary btn-xs" href="{{route('limpieza.index',['id_sala' => $value->pentax_sala, 'id_paciente' => $value->id_paciente, 'id_pentax' => $value->id_pentax])}}"><span>Revisar Registro</span></a>  -->
                          @if(isset($limpieza)) 
                          <a id="boton_editar" class="btn btn-info btn-xs" href="{{route('limpieza.editar',['id' => $limpieza->id ])}}" data-toggle="modal" data-target="#edit"><span>Revisar Registro</span></a> 
                          @else <a id="boton_nuevo" class="btn btn-primary btn-xs" href="{{route('limpieza.crear',['id_paciente' => $value->id_paciente, 'id_pentax' =>$value->id_pentax, 'id_sala' => $value->pentax_sala])}}" data-toggle="modal" data-target="#nuevo"> <span> Nuevo Registro </span></a> @endif
                          
                          
                          
                        </td>
                      </tr>
                    @endforeach
                    @foreach($p_limpieza as $p_limp)
                      @if($p_limp->nom_limp == 2)
                        <tr>
                          <td colspan="3" style="text-align: center;">Registro Final</td>
                          <td>{{$p_limp->created_at}}</td>
                          <td><a id="boton_editar" class="btn btn-info btn-xs" href="{{route('limpieza.editar',['id' => $p_limp->id ])}}" data-toggle="modal" data-target="#edit"><span>Revisar Registro</span></a> </td>
                        </tr>
                      @endif
                    @endforeach

                  </tbody>
                  
                </table>
                
                
              </div>
            </div>
            <div class="row">
             
            </div>
        </div>

        </div>
    </div>
</section>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
  $('#fecha').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD',        
  });

  $('#fechafin').datetimepicker({
        useCurrent: false,
        format: 'YYYY/MM/DD', 
  });

  $('#example3').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

  $('#example5').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

  $('#nuevo').on('hidden.bs.modal', function(){
    location.reload();
      $(this).removeData('bs.modal');
    });

  $('#edit').on('hidden.bs.modal', function(){
    location.reload();
      $(this).removeData('bs.modal');
    });


function exportar_excel_gastro(id, tipo){
    //alert(id);
    var fecha = $('#fecha').val();
    var hasta = $('#hasta').val();

    //alert(fecha);
    $("#crear_gastro1").attr("href", "{{url('limpieza/imprimir_excel')}}/"+id+'/'+tipo+'?fecha='+fecha+'&hasta='+hasta);
    window.location = $('#crear_gastro1').attr('href');

  
  }

  function exportar_excel_crm(id, tipo){

    var fecha = $('#fecha').val();
    var hasta = $('#hasta').val();

    $("#crear_crm1").attr("href", "{{url('limpieza/imprimir_excel')}}/"+id+'/'+tipo+'?fecha='+fecha+'&hasta='+hasta);
    window.location = $('#crear_crm1').attr('href');

  }



</script>
