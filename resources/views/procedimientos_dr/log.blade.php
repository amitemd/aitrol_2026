<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">LOG PROCEDIMIENTOS</h4>
  <h4 class="modal-title" style="text-align: center;"><b>PACIENTE:</b> {{$pentax->id_paciente}} {{$pentax->nombre1}} {{$pentax->nombre2}} {{$pentax->apellido1}} {{$pentax->apellido2}}</h4>
    <div style="text-align: right;"><span class="label label-primary" style="font-size: 100%;">@if($pentax->estado_pentax=='1') <b>PREPARACIÓN</b> @endif
    @if($pentax->estado_pentax=='0') <b>EN ESPERA</b> @endif
    @if($pentax->estado_pentax=='2') <b>EN PROCEDIMIENTO</b> @endif
    @if($pentax->estado_pentax=='3') <b>RECUPERACIÓN</b> @endif
    @if($pentax->estado_pentax=='4') <b>ALTA</b> @endif 
    @if($pentax->estado_pentax=='5') <b>SUSPENDER</b> @endif
    </span></div>
</div>

<div class="modal-body">
  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover" style="font-size: 12px;">
            <thead>
              <tr  >
                <th >Fecha</th>
                <th >Hora</th>
                <th >Cambio</th>
                <th >Descripción</th>
                <th >Doctor</th>
                <th >Asistente 1</th>
                <th >Asistente 2</th>
                <th >Procedimientos</th>
                <th >Seguro</th>
                <th >Sala</th>
                <th >Usuario Modifica</th>
                <th >Observación</th>
              </tr>
            </thead>
            <tbody >
            @foreach ($pentax_logs as $value)
              <tr >
                  <td >{{ substr($value->created_at, 0, 10)}}</td>
                  <td >{{ substr($value->created_at, 11, 15)}}</td>
                  <td >{{ $value->tipo_cambio}}</td>
                  <td >{{ $value->descripcion}}</td>
                  <td >{{ $value->d1nombre1}} {{ $value->d1apellido1}}</td>
                  <td >{{ $value->d2nombre1}} {{ $value->d2apellido1}}</td>
                  <td >{{ $value->d3nombre1}} {{ $value->d3apellido1}}</td>
                  <td >@php 
                        $id_procs = explode('+',$value->procedimientos);
                        $list_procs="";
                        $flag=0;
                        foreach($id_procs as $id_proc){
                          if($flag==0){
                            $list_procs=Sis_medico\Procedimiento::find($id_proc)->observacion;
                            $flag=1;
                          }
                          else{
                            $list_procs=$list_procs."+".Sis_medico\Procedimiento::find($id_proc)->observacion;
                          }
                        }
                      @endphp
                      {{$list_procs}}
                  </td>
                  <td >{{ $value->snombre}}</td>
                  <td >{{ $value->nbrsala}}</td>
                  <td >{{substr($value->umnombre1,0,1)}}{{ $value->umapellido1}}</td>
                  <td >{{ $value->observacion}}</td>
              </tr>
            @endforeach  
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{count($pentax_logs)}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
           
          </div>
        </div>
      </div>
  </div>
</div> 


<script type="text/javascript">
  
  $(document).ready(function(){

  
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      
    });


    
    
});



</script> 
  