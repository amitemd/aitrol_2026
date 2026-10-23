@extends('hc_admision.visita.base')

@section('action-content')

<section class="content" >
    
    <div class="box">
        <div class="box-header">
          <div class="col-md-6"><h4>Procedimientos Facturados</h4></div> 
          <div class="col-md-6"><input type="text" name="texto" id="texto" readonly style="border: none;width: 100%;"> </div>
          <div class="form-group col-md-12" >
            
            <div class="col-md-12" style="padding: 0;">
              <form method="POST" action="{{route('valores.master')}}" >
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

                <div class="form-group col-md-2 col-xs-6" style="padding-left: 0px;padding-right: 0px;" >
                  <label for="id_empresa" class="col-md-12 control-label">Empresa</label>
                  <div class="col-md-12">
                    <select class="form-control form-control-sm input-sm" name="id_empresa" id="id_empresa">
                      <option value="">Seleccione ...</option>
                    @foreach($empresas as $empresa)
                      <option @if($empresa->id==$id_empresa) selected @endif value="{{$empresa->id}}">{{$empresa->nombre_corto}}</option>
                    @endforeach  
                    </select>
                  </div>      
                </div>

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

                <div class="form-group col-md-1 col-xs-2" style="padding-left: 0px;padding-right: 0px;" >
                  <label for="tipo_seguro" class="col-md-12 control-label">Tipo</label>
                  <div class="col-md-12">
                    <select class="form-control form-control-sm input-sm" name="tipo_seguro" id="tipo_seguro">
                      <option value="">Todos ...</option>
                      <option @if($tipo_seguro=='0') selected @endif value="0">Publicos</option>
                      <option @if($tipo_seguro=='1') selected @endif value="1">Privados</option>
                      <option @if($tipo_seguro=='2') selected @endif value="2">Particulares</option>
                    </select>
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
            <div class="table-responsive col-md-12 col-xs-12">
          <table id="example2" class="table table-striped table-hover" style="font-size: 12px;" >
            
            <thead>
              <th width="10">No.</th>
              <th width="10">Id</th>
              <th width="10">Año</th>
              <th width="10">Mes</th>
              <th width="10">Fecha</th>
              <th width="10">Seguro/Convenio</th>
              <th width="10">Paciente</th>
              <th width="10">Procedimientos</th>
              <th width="10">Valores</th>
              <th width="10">Orden</th>
            </thead>
            <tbody>
            @php  
            	$mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; $i=0; $cant=[];
            @endphp  
            @foreach ($valores as $val)
              @php 
                $orden_valor = DB::table('orden_valores as ov')->where('ov.id_excel_valores',$val->id)->first(); $i++;
                if(!is_null($orden_valor)){
                  $cant[$orden_valor->id_orden]='1';
                }
              @endphp
              <tr>
                <td>{{$i}}</td>
                <td>{{$val->id}}</td>
              	<td>{{$val->anio}}</td>
                <td>{{$mes[$val->mes - 1]}}</td>
                <td>{{$val->fecha}}</td>
                <td>{{$val->seguro}}</td>
                <td>{{$val->id_paciente}} - {{$val->paciente}}</td>
                <td>{{$val->procedimiento}}</td>
                <td>{{$val->valor}}</td>
                <td>@if(!is_null($orden_valor)){{$orden_valor->id_orden}}@endif</td>
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


  $( "#example2" ).click(function() {
    $('#texto').val('Cantidad de Ordenes encontradas: {{count($cant)}}');
  });
</script>



@endsection