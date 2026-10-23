<div class="modal-header">
    <div class="col-md-10"><h3>Pagos En Línea Por Gestionar</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">      
    <form id="frm_pagoonline">
        <div class="form-group col-md-3 col-xs-5">
          <label for="fecha_desde" class="col-md-3 control-label">Desde</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar_pl();"></i>
              </div>   
            </div>
          </div>  
        </div>

        <div class="form-group col-md-3 col-xs-5">
          <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
          <div class="col-md-9">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
              <div class="input-group-addon">
                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar_pl();"></i>
              </div>   
            </div>
          </div>  
        </div>  

        <div class="form-group col-md-3 col-xs-5">
            <label for="nombres" class="col-md-3 control-label">Paciente</label>
            <div class="col-md-9">
              <div class="input-group">
                <input value="{{$cedula}}" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = '';"></i>
                </div>
              </div>  
            </div>
        </div> 
        <button type="button" class="btn btn-info btn-xs" onclick="buscar_pl()">Buscar</button>  
    </form>

    <div id="pl_ges">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12" style="min-height: 210px;">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
                <thead>
                  <tr role="row">
                    <th width="5">Fecha</th>
                    <th width="5">Fecha Disponibilidad</th>
                    <th width="5">Cedula</th>
                    <th width="10">Nombres</th>
                    <th width="10">Pres/Dom</th>
                    <th width="5">Cant.</th>
                    <th width="5">Valor</th> 
                    <th width="15">Ver</th>                
                    <th width="10">Acción</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach ($ordenes as $value) <!--FOR EACH PARA LOS PENDIENTES PUBLICOS ABAJO HAY OTRO-->
                    <tr role="row">
                        <td style="font-size: 11px;">{{substr($value->updated_at,0,10)}}</td>
                        <td style="font-size: 11px;">{{substr($value->fecha_tentativa,0,10)}}</td>
                        <td style="font-size: 11px;">{{$value->id_paciente}}</td>
                        <td style="font-size: 11px;">{{$value->paciente->apellido1}} @if($value->paciente->apellido2=='N/A'||$value->paciente->apellido2=='(N/A)') @else{{ $value->paciente->apellido2 }} @endif {{$value->paciente->nombre1}} @if($value->paciente->nombre2=='N/A'||$value->paciente->nombre2=='(N/A)') @else{{ $value->paciente->pnombre2 }} @endif </td>
                        <td>@if($value->pres_dom=='1') DOMICILIO @else  PRESENCIAL @endif</td>
                        <td>{{$value->cantidad}}</td>
                        <td>{{$value->total_valor}}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-xs" onclick="window.open('{{ route('cotizador.imprimir', ['id' => $value->id]) }}','_blank');">
                                <span style=" font-size: 9px">Cotización</span>
                            </button>
                        </td> 
                        <td>
                          <a data-toggle="modal" data-target="#gestionar_orden" class="btn btn-warning btn-xs" href="{{ route('orden.pagoenlinea_gestionar_orden',['id' => $value->id])}}">
                            <span class="glyphicon glyphicon-credit-card"></span> Gestionar
                          </a>
                        </td>         
                    </tr>
                    @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>    
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>
<script type="text/javascript">
  $('#fecha_desde').datetimepicker({
    format: 'YYYY/MM/DD',
    defaultDate: '{{$fecha_desde}}',
  });
  $('#fecha_hasta').datetimepicker({
    format: 'YYYY/MM/DD',
    defaultDate: '{{$fecha_hasta}}',
  });
  function buscar_pl(){
    $.ajax({
      type: 'post',
      url : "{{ route('orden.pagoenlinea_gestionar_js') }}", 
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      
      datatype: 'json',
      data: $("#frm_pagoonline").serialize(),
      success: function(data){
        $('#pl_ges').empty().html(data);
      },
      error: function(data){   
      }
    });    
  }
</script>