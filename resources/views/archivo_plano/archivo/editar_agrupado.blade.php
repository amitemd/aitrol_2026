<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
} 
.dropdown-menu>li>a{
    color:white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
  }
 
  .dropdown-menu>li>a:hover{
    background-color:#008d4c !important;
  }
  .cot>li>a:hover{
    background-color:#00acd6 !important;
  }
</style>

<div class="modal-content" style="width: 100%;">
  <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
   
    <div class="row" style="border-bottom: 1px solid black;">
      <div class="col-md-2">
        <a class="btn btn-light" data-dismiss="modal" id="cerrar">
          <h1 style="font-size: 12px; margin:0;">
              <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
              <label style="font-size: 14px">Cerrar</label>
          </h1> 
        </a>
      </div>
      <div class="col-md-6">
      </div>
      <div class="col-md-4">
        <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Ingresar Tipo Seguro</span>
      </div>
    </div>
  </div>
  
  <div class="modal-body">
    <div class="box-body">
      <form id="agrupado{{$agrupado->id}}">
        {{ csrf_field() }}
        <input type="hidden" name="id" id="id" value="{{$agrupado->id}}">
        <div class="form-group col-md-6 col-xs-6">
          
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Mes Plano:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="mes_plano{{$agrupado->id}}" name="mes_plano{{$agrupado->id}}" class="form-control " value="{{$agrupado->mes_plano}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12 ">
            <label for="seguro" class="form-group col-md-12">Seguros:</label>
            <div class="col-md-12">
              <select id="seguro{{$agrupado->id}}" name="seguro{{$agrupado->id}}" class="form-control input-sm" >
                @foreach($seguros as $seguro)
                  <option @if($seguro->id==$agrupado->seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12 ">
            <label for="empresa" class="form-group col-md-12">Empresas:</label>
            <div class="col-md-12">
              <select id="empresa{{$agrupado->id}}" name="empresa{{$agrupado->id}}" class="form-control input-sm" >
                @foreach($empresas as $empresa)
                  <option @if($empresa->id == $agrupado->empresa) selected @endif value="{{$empresa->id}}">{{$empresa->nombrecomercial}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12 ">
            <label for="id_tipo_seg" class="form-group col-md-12">Tipo Seguro:</label>
            <div class="col-md-12">
              <select id="id_tipo_seg{{$agrupado->id}}" name="id_tipo_seg{{$agrupado->id}}" class="form-control input-sm" >
                @foreach($tipos as $tipo)
                  <option @if($tipo->tipo_principal == $agrupado->id_tipo_seg ) selected @endif value="{{$tipo->tipo_principal}}">{{$tipo->tipo_principal}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Base 0:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="base_0{{$agrupado->id}}" name="base_0{{$agrupado->id}}" class="form-control " value="{{$agrupado->base_0}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Base 12:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="base_iva{{$agrupado->id}}" name="base_iva{{$agrupado->id}}" class="form-control " value="{{$agrupado->base_iva}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Valor Iva:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="total_iva{{$agrupado->id}}" name="total_iva{{$agrupado->id}}" class="form-control " value="{{$agrupado->total_iva}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Gasto Administrativo:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="gast_amd10{{$agrupado->id}}" name="gast_amd10{{$agrupado->id}}" class="form-control " value="{{$agrupado->gast_amd10}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Valor Presentado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_cobrado{{$agrupado->id}}" name="valor_cobrado{{$agrupado->id}}" class="form-control " value="{{$agrupado->valor_cobrado}}">
            </div>
          </div>
          
        </div>  
        <div class="form-group col-md-6 col-xs-6">
          
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Tramite Nro:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="cod_proceso{{$agrupado->id}}" name="cod_proceso{{$agrupado->id}}" class="form-control " value="{{$agrupado->cod_proceso}}">
            </div>
            
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Estado:</label>
            <div class="col-md-12">
              <select class="form-control input-sm" id="estado{{$agrupado->id}}" name="estado{{$agrupado->id}}" class="form-control ">
                <option @if($agrupado->estado_pago == 0) selectedd @endif  value="0">ENTREGADO</option>
                <option @if($agrupado->estado_pago == 1) selectedd @endif  value="1">PENDIENTE DE RESPONDER</option>
                <option @if($agrupado->estado_pago == 2) selectedd @endif  value="2">POR ENVIAR</option>
                <option @if($agrupado->estado_pago == 3) selectedd @endif  value="3">SE ACEPTA OBJECION</option>
                <option @if($agrupado->estado_pago == 4) selectedd @endif  value="4">PENDIENTE DE RECIBIR LIQUIDACION</option>
                <option @if($agrupado->estado_pago == 5) selectedd @endif  value="5">PAGADO</option>
              </select>  
            </div>
            
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Facturado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_facturado{{$agrupado->id}}" name="valor_facturado{{$agrupado->id}}" class="form-control " value="{{$agrupado->valor_facturado}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Objetado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_objetado{{$agrupado->id}}" name="valor_objetado{{$agrupado->id}}" class="form-control " value="{{$agrupado->valor_objetado}}">
            </div>
            
          </div>
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Glosas(%):</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="porcentaje_glosa{{$agrupado->id}}" name="porcentaje_glosa{{$agrupado->id}}" class="form-control " value="{{$agrupado->porcentaje_glosa}}">
            </div>
            
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Levantar:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_levantar{{$agrupado->id}}" name="valor_levantar{{$agrupado->id}}" class="form-control " value="{{$agrupado->valor_levantar}}">
            </div>
            
          </div>
         
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Aceptado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_aceptado" name="valor_aceptado" class="form-control " value="{{$agrupado->valor_aceptado}}">
            </div>
            
          </div>
        </div>  
        <div class="form-group col-md-6 col-xs-6">
          
          <div class="col-md-6">
            
            <button id="guardar_agrupado" type="button" class="btn btn-info btn-xs" onclick="guardar('{{$agrupado->id}}')">GUARDAR</button>
          </div>
          
        </div>        
      </form>
        
    </div>
  </div>
  <div class="modal-footer">
    <!--<button type="button" class="btn btn-primary"  data-dismiss="modal">Close</button>-->
  </div>
</div> 

<script type="text/javascript">

  function guardar(id){

    $.ajax({
        type: 'post',
        url:"{{route('aparchivo.total_agrupado_update')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#agrupado"+id).serialize(),
        success: function(data){
            alert("guardado correctamente");
            $('#cerrar').click();  
            
        },
        error: function(data){
                
            }
    })

  }
  
</script>