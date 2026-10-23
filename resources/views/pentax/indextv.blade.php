<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="Pentax_log" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<style type="text/css">
td {
    font-weight: bold;
    padding: 1px !important;
  }

</style> 

  
  <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="row">
        <div class="table-responsive col-md-12">
          <table id="example2" class="table table-bordered table-hover">
            <thead>
              <tr  >
                <th >Cédula</th>
                <th >Paciente</th>
                <th >Procedimientos</th>
                <th >Sala del Procedimiento</th>
                <!--th >Hora del Procedimiento</th-->
                <th >Estado</th>
              </tr>
            </thead>
            <tbody >
            @foreach ($pentax as $value)
              @php $agprocedimientos = Sis_medico\AgendaProcedimiento::where('id_agenda',$value->id)->get(); 
                 $pantallapentax =  Sis_medico\Pentax::where('id_agenda',$value->id)->first();
              @endphp
                @if(is_null($pantallapentax))
                <tr @if($value->estado_cita != 0) @if($value->paciente_dr == 1) style="color: {{$value->dcolor}};" @else style="color: {{$value->scolor}};" @endif @endif>
                  <td >{{ $value->id_paciente}}</td>
                  <td >{{ substr($value->papellido1,0,1)}}. @if($value->papellido2!='(N/A)'){{ substr($value->papellido2,0,1)}}.@endif {{ substr($value->pnombre1,0,1)}}. @if($value->pnombre2!='(N/A)'){{ substr($value->pnombre2,0,1)}}.@endif</td>
                  <td >{{ $value->pobservacion}}@if(!$agprocedimientos->isEmpty()) @foreach($agprocedimientos as $agendaproc)+ {{Sis_medico\Procedimiento::find($agendaproc->id_procedimiento)->nombre}} @endforeach @endif</td>
                  <td >{{ $value->nombre_sala }}</td>
                  <!--td >{{substr($value->fechaini, 11, 5)}}</td-->
                  <td >&nbsp;No Admisionado </td>
              </tr>
              @else
              @php
                $pentaxproc =  Sis_medico\PentaxProc::where('id_pentax',$pantallapentax->id)->get();
                $flag=0;
              @endphp 
              @if($pantallapentax->estado_pentax!='4' && $pantallapentax->estado_pentax!='5') 
              <tr @if($pantallapentax->estado_pentax == '-1') @elseif($pantallapentax->estado_pentax == '0') style="background-color: #ffffe6;" @elseif($pantallapentax->estado_pentax < '3') style="background-color: #ffe6e6;" @else style="background-color: #ccf5ff;" @endif  @if($value->estado_cita != 0) @if($value->paciente_dr == 1) style="color: {{$value->dcolor}};" @else style="color: {{$value->scolor}};" @endif @endif>
                  <td >{{ $value->id_paciente}}</td>
                  <td >{{ substr($value->papellido1,0,1)}}. @if($value->papellido2!='(N/A)'){{ substr($value->papellido2,0,1)}}.@endif {{ substr($value->pnombre1,0,1)}}. @if($value->pnombre2!='(N/A)'){{ substr($value->pnombre2,0,1)}}.@endif</td>
                  <td >@if(!is_null($pentaxproc))  
                        @foreach($pentaxproc as $proc) @if($flag!='0') + @endif @php $flag=1; @endphp {{$procedimientos->where('id',$proc->id_procedimiento)->first()->nombre}}    
                           @endforeach 
                        </a>
                    @endif                  
                  </td>
                  <td>{{$salas->find($pantallapentax->id_sala)->nombre_sala}}</td>  
                  <!--td >{{substr($value->fechaini, 11, 5)}}</td-->
                                  
                  <td>@if($pantallapentax->estado_pentax=='-1') PRE - ADMISION @endif
                      @if($pantallapentax->estado_pentax=='0') EN ESPERA @endif
                      @if($pantallapentax->estado_pentax=='1') PREPARACIÓN @endif
                      @if($pantallapentax->estado_pentax=='2') EN PROCEDIMIENTO @endif
                      @if($pantallapentax->estado_pentax=='3') RECUPERACION @endif
                      @if($pantallapentax->estado_pentax=='4') ALTA @endif
                      @if($pantallapentax->estado_pentax=='5') SUSPENDER @endif 
                  </td>      
              </tr>
              @endif
              @endif
            @endforeach
              <a id="cambios_pentax" style="display: none;" data-toggle="modal" data-target="#Estados_pentax"></a>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{count($pentax)}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            
          </div>
        </div>
      </div>
  </div>


<script type="text/javascript">
  $('#Pentax_log').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 

  $('#Pentax_log').on('show.bs.modal', function (e) {
    clearInterval(vartiempo);
    console.log(vartiempo);

  
  })

  $(document).ready(function(){
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 4, "desc" ]]
    });
  });

</script>
  