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
        <a class="btn btn-light" data-dismiss="modal" id="cerrar{{$agrupado->id}}">
          <h1 style="font-size: 12px; margin:0;">
              <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
              <label style="font-size: 14px">Cerrar</label>
          </h1> 
        </a>
      </div>
      <div class="col-md-3">
      </div>
      <div class="col-md-7">
        <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Ingresar Tipo Seguro: {{ $agrupado->fx_seguro->nombre }} {{ $agrupado->id_tipo_seg }}</span>
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
            <label class="form-group col-md-12"> Tramite Nro:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="text" id="cod_proceso{{$agrupado->id}}" name="cod_proceso{{$agrupado->id}}" class="form-control " value="{{$agrupado->cod_proceso}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Valor Por Cobrar(Facturado): </label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_facturado{{$agrupado->id}}" name="valor_facturado{{$agrupado->id}}" class="form-control " value="{{$agrupado->valor_facturado}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Valor Iva Cobrar:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_iva_cobrar{{$agrupado->id}}" name="valor_iva_cobrar{{$agrupado->id}}" class="form-control " value="{{$agrupado->valor_iva_cobrar}}">
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-6"> Valor Objetado</label>
            <div class="col-md-6" style="text-align: right;">
             {{number_format($agrupado->valor_objetado, 2, ',', ' ')}}
            </div>
          </div>       
          
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-6"> Porcentaje Glosa </label>
            <div class="col-md-6" style="text-align: right;">
             {{number_format($agrupado->porcentaje_glosa, 2, ',', ' ')}}
            </div>
          </div>

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-6"> Base 0 Prefactura </label>
            <div class="col-md-6" style="text-align: right;">
             {{number_format($agrupado->base_0_pref, 2, ',', ' ')}}
            </div>
          </div> 

          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-6"> Base 12 Prefactura </label>
            <div class="col-md-6" style="text-align: right;">
             {{number_format($agrupado->base_12_pref, 2, ',', ' ')}}
            </div>
          </div>

          <!--div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Levantar:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_levantar{{$agrupado->id}}" name="valor_levantar{{$agrupado->id}}" class="form-control " value="{{$agrupado->valor_levantar}}">
            </div>
          </div-->
         
          <!--div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Aceptado:</label>
            <div class="col-md-12">
              <input class="form-control input-sm" type="number" id="valor_aceptado{{$agrupado->id}}" name="valor_aceptado{{$agrupado->id}}" class="form-control " value="{{$agrupado->valor_aceptado}}">
            </div>
          </div-->
          
        </div>  

            
        <div class="form-group col-md-6 col-xs-6">           
            <div class="form-group col-md-12 col-xs-12">
              <label class="form-group col-md-6"> Base 0</label>
              <div class="col-md-6" style="text-align: right;">
              $  {{number_format($agrupado->base_0, 2, ',', ' ')}}
              </div>
            </div>

            <div class="form-group col-md-12 col-xs-12">
              <label class="form-group col-md-6"> Base 12</label>
              <div class="col-md-6" style="text-align: right;">
              $  {{number_format($agrupado->base_iva, 2, ',', ' ')}}
              </div>
            </div>

            <div class="form-group col-md-12 col-xs-12">
              <label class="form-group col-md-6"> Iva</label>
              <div class="col-md-6" style="text-align: right;">
              $  {{number_format($agrupado->total_iva, 2, ',', ' ')}}
              </div>
            </div>

            <div class="form-group col-md-12 col-xs-12">
              <label class="form-group col-md-6"> Administrativo</label>
              <div class="col-md-6" style="text-align: right;">
              $  {{number_format($agrupado->gast_amd10, 2, ',', ' ')}}
              </div>
            </div>      

            <div class="form-group col-md-12 col-xs-12">
              <label class="form-group col-md-6"> Valor Reclamado</label>
              <div class="col-md-6" style="text-align: right;">
              $  {{number_format($agrupado->valor_cobrado, 2, ',', ' ')}}
              </div>
            </div>

          <!--div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-6"> Valor Por Cobrar</label>
            <div class="col-md-6" style="text-align: right;">
            $  {{number_format($agrupado->valor_facturado, 2, ',', ' ')}}
            </div>
          </div-->


          <!--div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-6"> Objetado </label>
            <div class="col-md-6" style="text-align: right;">
            $  {{number_format($agrupado->valor_objetado, 2, ',', ' ')}}
            </div>
          </div-->

          <!--div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-6"> Objetado 0 </label>
            <div class="col-md-6" style="text-align: right;">
            $  {{number_format($agrupado->objetado_0, 2, ',', ' ')}}
            </div>
          </div-->
   
           
          <!--div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-6"> Objetado 12 </label>
            <div class="col-md-6" style="text-align: right;">
            $  {{number_format($agrupado->objetado_12, 2, ',', ' ')}}
            </div>
          </div-->
 
                   
          <div class="form-group col-md-12 col-xs-12">
            <label class="form-group col-md-12"> Estado:</label>
            <div class="col-md-12">
              <select class="form-control input-sm" id="estado{{$agrupado->id}}" name="estado{{$agrupado->id}}" class="form-control ">
                <option @if($agrupado->estado_pago == '0') selected @endif value="0">ENTREGADO</option>
                <option @if($agrupado->estado_pago == '1') selected @endif value="1">PENDIENTE DE RESPONDER</option>
                <option @if($agrupado->estado_pago == '2') selected @endif value="2">POR ENVIAR</option>
                <option @if($agrupado->estado_pago == '3') selected @endif value="3">SE ACEPTA OBJECION</option>
                <option @if($agrupado->estado_pago == '4') selected @endif value="4">PENDIENTE DE RECIBIR LIQUIDACION</option>
                <option @if($agrupado->estado_pago == '5') selected @endif value="5">PAGADO</option>
              </select>  
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
        url:"{{route('archivo.guardar_agrupado_vt')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#agrupado"+id).serialize(),
        success: function(data){
            alert(`{{trans('proforma.GuardadoCorrectamente')}}`);
            $('#cerrar'+id).click();  
            
        },
        error: function(data){
                
            }
    })

  }
  
</script>