@if($pentax->count()>0)
<style type="text/css">
.pentax>tbody>tr>td, .pentax>thead>tr>th {
    padding: 0.1% ;
} 
</style>
<div class="box box-default collapsed-box">
  <div class="box-header with-border">
    <h4>Control Pentax</h4>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
    </div>
  </div>
  <div class="box-body">
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover pentax" style="font-size: 12px;">
                <thead>
                  <tr  >
                    <th >Paciente</th>
                    <th >Procedimientos</th>
                    <th >Inicio</th>
                    <th >Amb/Hos</th>
                    <th >Doctor</th>
                    <th >Seguro</th>
                    <th >Estado</th>
                    <th >PRE</th>
                    <th >POST</th>
                    <!--th >Log</th-->
                  </tr>
                </thead>
                <tbody >
                @foreach ($pentax as $value)
                  @php 
                    $p_color1="black";
                    $pentaxproc =  DB::table('pentax_procedimiento')->where('id_pentax',$value->pentax)->get();
                    $ptx_seg=null;
                    $flag=0;
                    $p_color2="black"; 
                  @endphp 
                  <tr @if($value->estado_pentax < '2' || $value->estado_pentax > '2') style="background-color: #ffe6e6; color: {{$p_color2}}; font-weight: bold;" @else style="background-color: #ccf5ff; font-weight: bold;" @endif  @if($value->estado_cita != 0) @if($value->paciente_dr == 1) style="color: {{$value->dcolor}};" @else style="color: {{$value->scolor}};" @endif @endif>
                      <td >{{ $value->papellido1}} @if($value->papellido2!='(N/A)'){{ $value->papellido2}}@endif {{ $value->pnombre1}} @if($value->pnombre2!='(N/A)'){{ $value->pnombre2}}@endif</td>
                      <td >@if(!is_null($pentaxproc))  
                            @foreach($pentaxproc as $proc) @if($flag!='0') + @endif @php $flag=1; @endphp {{$procedimientos->where('id',$proc->id_procedimiento)->first()->observacion}}    
                            @endforeach 
                        @endif                  
                      </td>
                      <td >{{substr($value->fechaini, 11, 5)}}</td>
                      <td>@if($value->est_amb_hos == 0)AMBULATORIO @else HOSPITALIZADO @endif</td>
                      <td>{{$value->dnombre1}} {{$value->dapellido1}}</td>      
                      <td>{{$value->snombre}}</td>
                      <td>
                            @if($value->estado_pentax=='0') EN ESPERA @endif
                            @if($value->estado_pentax=='1') PREPARACIÓN @endif
                            @if($value->estado_pentax=='2') EN PROCEDIMIENTO @endif
                            @if($value->estado_pentax=='3') RECUPERACION @endif
                            @if($value->estado_pentax=='4') ALTA @endif
                            @if($value->estado_pentax=='5') SUSPENDER @endif
                             
                      </td>
                      <td>@if(array_key_exists($value->id,$pentax_pend)) <?php echo $pentax_pend[$value->id]['1']; ?>@endif</td>
                      <td>@if(array_key_exists($value->id,$pentax_pend)) <?php echo $pentax_pend[$value->id]['2']; ?>@endif</td>

                 
                      
                  </tr>
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
  </div>
</div>    
@endif