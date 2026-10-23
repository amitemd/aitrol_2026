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
      <form id="agrupado">
        {{ csrf_field() }}
      
        <div class="form-group col-md-6 col-xs-6">
          
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Mes Plano:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="mes_plano" name="mes_plano" class="form-control " value="">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12 ">
            <label for="seguro" class="form-group col-md-12">Seguros:</label>
            <div class="col-md-12">
              <select id="seguro" name="seguro" class="form-control input-sm" >
                @foreach($seguros as $seguro)
                  <option  value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12 ">
            <label for="empresa" class="form-group col-md-12">Empresas:</label>
            <div class="col-md-12">
              <select id="empresa" name="empresa" class="form-control input-sm" >
                @foreach($empresas as $empresa)
                  <option  value="{{$empresa->id}}">{{$empresa->nombrecomercial}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12 ">
            <label for="id_tipo_seg" class="form-group col-md-12">Tipo Seguro:</label>
            <div class="col-md-12">
              <select id="id_tipo_seg" name="id_tipo_seg" class="form-control input-sm" >
                @foreach($tipos as $tipo)
                  <option  value="{{$tipo->tipo_principal}}">{{$tipo->tipo_principal}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Base 0:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="base_0" name="base_0" class="form-control " value="">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Base 12:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="base_iva" name="base_iva" class="form-control " value="">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Valor Iva:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="total_iva" name="total_iva" class="form-control " value="">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Gasto Administrativo:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="gast_amd10" name="gast_amd10" class="form-control " value="">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Valor Presentado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_cobrado" name="valor_cobrado" class="form-control " value="">
            </div>
          </div>
          
        </div>  
        <div class="form-group col-md-6 col-xs-6">
          
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Tramite Nro:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="cod_proceso" name="cod_proceso" class="form-control " value="">
            </div>
            
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Estado:</label>
            <div class="col-md-12">
              <select class="form-control input-sm" id="estado" name="estado" class="form-control ">
                <option  value="0">ENTREGADO</option>
                <option  value="1">PENDIENTE DE RESPONDER</option>
                <option  value="2">POR ENVIAR</option>
                <option  value="3">SE ACEPTA OBJECION</option>
                <option  value="4">PENDIENTE DE RECIBIR LIQUIDACION</option>
                <option  value="5">PAGADO</option>
              </select>  
            </div>
            
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Facturado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_facturado" name="valor_facturado" class="form-control " value="">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Objetado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_objetado" name="valor_objetado" class="form-control " value="">
            </div>
            
          </div>
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Glosas(%):</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="porcentaje_glosa" name="porcentaje_glosa" class="form-control " value="">
            </div>
            
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Levantar:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_levantar" name="valor_levantar" class="form-control " value="">
            </div>
            
          </div>
         
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Aceptado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_aceptado" name="valor_aceptado" class="form-control " value="">
            </div>
            
          </div>
        </div>  
        <div class="form-group col-md-6 col-xs-6">
          
          <div class="col-md-6">
            
            <button id="guardar_agrupado" type="button" class="btn btn-info btn-xs" onclick="guardar('')">GUARDAR</button>
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

  function guardar(){

    $.ajax({
        type: 'post',
        url:"{{route('aparchivo.total_agrupado_store')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#agrupado").serialize(),
        success: function(data){
            alert("guardado correctamente");
            $('#cerrar').click();  
            
        },
        error: function(data){
                
            }
    })

  }
  
</script>