@extends('hc_admision.visita.base')

@section('action-content')

<section class="content" >
    
    <div class="box">
        <div class="box-header">
            <div class="form-group col-md-12" >
               
                <div class="col-md-12">
                   
                    <form method="POST" action="#" >
                      {{ csrf_field() }}
                      <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                          <label for="fecha" class="col-md-3 control-label" style="padding:0px;">Desde</label>
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

                      <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                          <label for="fecha_hasta" class="col-md-3 control-label" style="padding-left: 0;" >Hasta</label>
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

                      <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                        <label for="nombres" class="col-md-3 control-label">Paciente</label>
                        <div class="col-md-9">
                          <div class="input-group">
                            <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="APELLIDOS - NOMBRES" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            <div class="input-group-addon">
                              <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                            </div>
                          </div>  
                        </div>
                      </div>
                   

                      

                      <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;" >
                        
                          <label for="id_doctor1" class="col-md-3 control-label">Doctor</label>
                          <div class="col-md-9">
                            <select class="form-control form-control-sm input-sm" name="id_doctor1" id="id_doctor1">
                              <option value="">Seleccione ...</option>
                            @foreach($doctores as $doctor)
                              <option @if($doctor->id==$id_doctor1) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->nombre1}}</option>
                            @endforeach  
                            </select>
                          </div>      
                      
                      </div>

                      <div class="form-group col-md-3 col-xs-6" >
                        
                          <label for="id_seguro" class="col-md-3 control-label">Seguro</label>
                          <div class="col-md-9">
                            <select class="form-control form-control-sm input-sm" name="id_seguro" id="id_seguro">
                              <option value="">Seleccione ...</option>
                            @foreach($seguros as $seguro)
                              <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                            @endforeach  
                            </select>
                          </div>                  
                        
                      </div>

                      <div class="form-group col-md-1 col-xs-2" >
                        <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                          <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
                      </div>

                      
                    </form>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive col-md-12 col-xs-12">
          <table id="example2" class="table table-striped table-hover" style="font-size: 12px;">
            
            <thead>
              <th width="10">nro.</th>
              <th width="10">Año</th>
              <th width="10">Mes</th>
              <th width="10">Fecha</th>
              <th width="10">Convenio</th>
              <th width="10">Cedula</th>
              <th width="10">Paciente</th>
              <th width="10">Procedimientos Dr</th>
              <th width="10">Procedimientos Px</th>
              <th width="10">Valor</th>
             
            </thead>
            <tbody>
            @php  
            	$mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; $i=1;
				
            @endphp  
            @foreach ($procedimientos as $val)
            	@php 
                	$px = Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos',$val->hpid)->get(); $i++;
                	$seguro = Sis_medico\Seguro::find($val->id_seguro);
                	$empresa = Sis_medico\Empresa::find($val->hpempresa);
                  $pentax = Sis_medico\Pentax::where('id_agenda',$val->agenda)->first();
                  if(!is_null($pentax)){
                    $pproc = Sis_medico\PentaxProc::where('id_pentax',$pentax->id)->get();  
                  }
                @endphp
              	<td>{{$val->hpid}}</td>
                <td>{{$val->year}}</td>
                <td>{{$mes[$val->month - 1]}}</td>
                <td>{{substr($val->fechaini,0,10)}}</td>
                <td>{{$seguro->nombre}}@if(!is_null($empresa))/{{$empresa->nombre_corto}}@else**REVISAR{{$val->hpid}}@endif</td>
                <td>{{$val->id_paciente}}</td>
                <td>{{$val->apellido1}} {{$val->nombre1}} </td>
                <td>@foreach($px as $p) <span class="label @if($p->procedimiento->id_grupo_procedimiento != null) label-success @else label-danger  @endif">{{$p->procedimiento->nombre}}</span> @endforeach</td>
                <td>@if(!is_null($pentax)) @foreach($pproc as $p1) <span class="label @if($p1->procedimiento->id_grupo_procedimiento != null) label-success @else label-danger  @endif">{{$p1->procedimiento->nombre}}</span> @endforeach @endif</td>
                <td>{{$val->estimado_minimo}}</td>                   
              </tr>
            @endforeach
            </tbody>
           
          </table>
          
        </div>  
            <!--div id="calendar" ></div-->
        </div>
    </div>
</section>
<script type="text/javascript">
  $(function () {
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



@endsection