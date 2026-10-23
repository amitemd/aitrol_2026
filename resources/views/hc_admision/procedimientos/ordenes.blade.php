@extends('hc_admision.visita.base')

@section('action-content')

<section class="content" >
    
    <div class="box">

        


        <div class="box-header">
          <div class="col-md-6">
            <h4>Producción de Procedimientos</h4> 
          </div>  
          
          <div class="col-md-6">
            <a class="btn btn-primary btn-xs" href="{{route('valores.master')}}">Valores facturados</a> 
          </div>
          <div class="form-group col-md-12" >
            <div class="col-md-12">
              <form method="POST" action="{{route('ordenes.master')}}" >
                {{ csrf_field() }}
                <!--div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="col-md-12 control-label" style="padding:0px;">Desde</label>
                    <div class="col-md-12">
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
                </div-->

                <!--div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="col-md-12 control-label" style="padding-left: 0;" >Hasta</label>
                    <div class="col-md-12">
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
                </div-->

                <div class="form-group col-md-2 col-xs-2" style="padding-left: 0px;padding-right: 0px;">
                  <label for="anio" class="col-md-12 control-label">Año</label>
                  <div class="col-md-12">
                    <div class="input-group">
                      <input value="{{$anio}}" type="number" class="form-control input-sm" name="anio" id="anio" >
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('anio').value = '';"></i>
                      </div>
                    </div>  
                  </div>
                </div>

                <div class="form-group col-md-2 col-xs-4" style="padding-left: 0px;padding-right: 0px;" >
                  <label for="mes" class="col-md-12 control-label">Mes</label>
                  <div class="col-md-12">
                    <select class="form-control form-control-sm input-sm" name="mes" id="mes">
                      <option value="">Seleccione ...</option>
                      <option @if($mes=='1') selected @endif value="1">Enero</option>
                      <option @if($mes=='2') selected @endif value="2">Febrero</option>
                      <option @if($mes=='3') selected @endif value="3">Marzo</option>
                      <option @if($mes=='4') selected @endif value="4">Abril</option>
                      <option @if($mes=='5') selected @endif value="5">Mayo</option>
                      <option @if($mes=='6') selected @endif value="6">Junio</option>
                      <option @if($mes=='7') selected @endif value="7">Julio</option>
                      <option @if($mes=='8') selected @endif value="8">Agosto</option>
                      <option @if($mes=='9') selected @endif value="9">Septiembre</option>
                      <option @if($mes=='10') selected @endif value="10">Octubre</option>
                      <option @if($mes=='11') selected @endif value="11">Noviembre</option>
                      <option @if($mes=='12') selected @endif value="12">Diciembre</option>
                    </select>
                  </div>      
                </div>

                <div class="form-group col-md-2 col-xs-6" style="padding-left: 0px;padding-right: 0px;" >
                  <label for="id_doctor1" class="col-md-12 control-label">Doctor</label>
                  <div class="col-md-12">
                    <select class="form-control form-control-sm input-sm" name="id_doctor1" id="id_doctor1">
                      <option value="">Seleccione ...</option>
                    @foreach($doctores as $doctor)
                      <option @if($doctor->id==$id_doctor1) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</option>
                    @endforeach  
                    </select>
                  </div>      
                </div>

                <div class="form-group col-md-2 col-xs-6" style="padding-left: 0px;padding-right: 0px;" >
                  <label for="id_seguro" class="col-md-12 control-label">Seguro</label>
                  <div class="col-md-12">
                    <select class="form-control form-control-sm input-sm" name="id_seguro" id="id_seguro">
                      <option value="">Seleccione ...</option>
                    @foreach($seguros as $seguro)
                      <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                    @endforeach  
                    </select>
                  </div>      
                </div>

                <!--div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                  <label for="nombres" class="col-md-12 control-label">Paciente</label>
                  <div class="col-md-12">
                    <div class="input-group">
                      <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="APELLIDOS - NOMBRES" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                      </div>
                    </div>  
                  </div>
                </div-->

                <div class="form-group col-md-2 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                  <label for="id_paciente" class="col-md-12 control-label">Paciente</label>
                  <div class="col-md-12">
                    <div class="input-group">
                      <input value="{{$id_paciente}}" type="text" class="form-control input-sm" name="id_paciente" id="id_paciente" >
                      <div class="input-group-addon">
                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('id_paciente').value = '';"></i>
                      </div>
                    </div>  
                  </div>
                </div>

                <div class="form-group col-md-1 col-xs-2" >
                  <label class="col-md-12 control-label">&nbsp;</label>
                  <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
                </div>

                
              </form>
            </div>
          </div>
        </div>
        <div class="box-body">
          <div class="col-md-6">
            <div class="box box-default collapsed-box">
              <div class="box-header with-border">
                <h3 class="box-title">Producción Doctores por Año/mes de Orden</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                  </button>
                </div>
                
              </div>
              
              <div class="box-body">
                <div class="table-responsive">
                
                  <table id="example" class="table table-striped table-hover" style="font-size: 12px;">
                    <thead>
                      <th width="10">Año</th>
                      <th width="10">Mes</th>
                      <th width="10">Doctor</th>
                      <th width="10">Cantidad</th>
                      <th width="10">Valor</th>
                    </thead>
                    <tbody>
                    @php  
                      $mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; $i=1;
                    @endphp  
                    @foreach ($ordenes_valores as $val)
                      @php $doctor = Sis_medico\User::find($val->id_doctor); @endphp
                      <tr>
                        <td>{{$val->anio}}</td>
                        <td>{{$mes[$val->mes - 1]}}</td>
                        <td>{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</td>
                        <td>{{$ordenes_valoresx->where('anio', $val->anio)->where('mes', $val->mes)->where('id_doctor',$val->id_doctor)->count()}}</td>
                        <td>{{round($val->total,2)}}</td>
                      </tr>
                    @endforeach
                    </tbody>
                   
                  </table>
                
                </div>
              </div>
              
            </div>
            
          </div>
          <div class="col-md-6">
            <div class="box box-default collapsed-box">
              <div class="box-header with-border">
                <h3 class="box-title">Producción Doctores por Año/mes de Facturación</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                  </button>
                </div>
                
              </div>
              
              <div class="box-body">
                <div class="table-responsive">
                  
                  <table id="example3" class="table table-striped table-hover" style="font-size: 12px;">
                    <thead>
                      <th width="10">Año</th>
                      <th width="10">Mes</th>
                      <th width="10">Doctor</th>
                      <th width="10">Cantidad</th>
                      <th width="10">Valor</th>
                    </thead>
                    <tbody>
                    @php  
                      $mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; $i=1;
                    @endphp  
                    @foreach ($ordenes_valores2 as $val)
                      @php $doctor = Sis_medico\User::find($val->id_doctor); @endphp
                      <tr>
                        <td>{{$val->anio}}</td>
                        <td>{{$mes[$val->mes - 1]}}</td>
                        <td>{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}}</td>
                        <td>{{$ordenes_valores2x->where('anio', $val->anio)->where('mes', $val->mes)->where('id_doctor',$val->id_doctor)->count()}}</td>
                        <td>{{round($val->total,2)}}</td>
                      </tr>
                    @endforeach
                    </tbody>
                   
                  </table>
                
                </div>
              </div>
              
            </div>
            
          </div>
          
            

              
          
          <div class="table-responsive col-md-12 col-xs-12">
             
              <table id="example2" class="table table-striped table-hover" style="font-size: 12px;">
                
                <thead>
                  <th width="10">Id</th>
                  <th width="10">Año</th>
                  <th width="10">Mes</th>
                  <th width="10">Fecha</th>
                  <th width="10">Doctor</th>
                  <th width="10">Paciente</th>
                  <th width="10">Seguro</th>
                  <th width="10">Procedimientos</th>
                  <th width="10">Valor</th>
                </thead>
                <tbody>
                @php  
                	$mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; $i=1;

    				
                @endphp  
                @foreach ($ordenes as $val)
                	@php 
                    $txt_pxs = '';
                    $tipos = $val->orden_tipo;
                    foreach($tipos as $tipo){
                      $pxs = $tipo->orden_procedimiento;
                      foreach($pxs as $px){
                        $txt_pxs = $txt_pxs.'+'.$px->procedimiento->nombre;
                      }
                    }
                    $orden_valor = DB::table('orden_valores as ov')->join('excel_valores as ev','ev.id','ov.id_excel_valores')->where('ov.id_orden',$val->id)->select('ov.id_orden')->selectRaw('sum(ev.valor) as total')->groupBy('ov.id_orden')->first();
                    
                  @endphp
                  <tr>
                    <td>{{$val->id}}</td>
                  	<td>{{$val->anio}}</td>
                    <td>{{$mes[$val->mes - 1]}}</td>
                    <td>{{substr($val->fecha_orden,0,10)}}</td>
                    <td>{{$val->doctor->apellido1}} {{$val->doctor->nombre1}}</td>
                    <td>{{$val->id_paciente}} - {{$val->paciente->apellido1}} {{$val->paciente->nombre1}}</td>
                    <td>{{$val->seguro}}</td>
                    <td>{{$txt_pxs}}</td>
                    <td>@if(!is_null($orden_valor)) {{round($orden_valor->total,2)}} @else 0 @endif</td>
                  </tr>
                  <?php /*@foreach($orden_valor as $o)
                    @php
                      $valor = Sis_medico\Excel_Valores::find($o->id_excel_valores);
                    @endphp
                    @if(!is_null($valor))
                      <tr>
                        <td>{{$valor->anio}}</td>
                        <td>{{$mes[$valor->mes - 1]}}</td>
                        <td>{{$valor->fecha}}</td>
                        <td></td>
                        <td></td>
                        <td>{{$valor->procedimiento}}</td>
                        <td>{{$valor->valor}}</td>
                      </tr>
                    @endif    
                  @endforeach */ ?>
                @endforeach
                </tbody>
               
              </table>
          
          </div>  
            
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
    $('#example').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false,
        
      });
    $('#example3').DataTable({
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