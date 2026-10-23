<div class="modal-header">
    <div class="col-md-10"><h3>Gestionar Pago en Linea No. {{$orden->id}}</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">
  <p><b> Paciente: </b>{{$orden->id_paciente}}-{{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif {{$orden->paciente->nombre1}} @if($orden->paciente->nombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif</p>
  <p><b>Dirección: </b>{{$orden->paciente->direccion}}</p>
  <p><b>Telefono: </b>{{$orden->paciente->telefono1}} - {{$orden->paciente->telefono2}} - {{$orden->paciente->telefono3}}</p>
  <p><b> Cantidad: </b>{{$orden->cantidad}} <b>- Valor:</b> {{$orden->total_valor}}</p>
  <p> Ingrese la fecha de la orden (Toma de la Muestra) </p>
  <form id="frm_gestionar">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">          
    <input type="hidden" name="id_ges" value="{{ $orden->id }}">
    <div class="form-group col-md-12 col-xs-12">
      <label for="fecha_orden" class="col-md-3 control-label">Fecha</label>
      <div class="col-md-9">
        <div class="input-group date">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          <input type="text" class="form-control input-sm" name="fecha_orden" id="fecha_orden" autocomplete="off">
          <div class="input-group-addon">
            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_orden').value = ''; buscar_pl();"></i>
          </div>   
        </div>
      </div>  
    </div>
    <br>
    <h4> La fecha solicitada por el Paciente es: {{substr($orden->fecha_tentativa,0,10)}}</h4>
    <button type="button" class="btn btn-info btn-xs" onclick="guardar_ges();ingresar_cierre_caja();">Guardar</button>   
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
  $('#fecha_orden').datetimepicker({
    format: 'YYYY/MM/DD HH:mm:ss',
    defaultDate: '{{$orden->fecha_orden}}',
    //minDate: "{{date('Y/m/d H:i:s')}}",
  });
    
  function guardar_ges(){
    var fecha_or = $("#fecha_orden").val();
    if(fecha_or==''){
      alert("Ingrese la fecha de la orden");
    }else{
      var confirmar = confirm("Confirma la fecha de la orden");
      if(confirmar){
        $.ajax({
          type: 'post',
          url:"{{ route('orden.pagoenlinea_gestionar_guardar') }}", 
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm_gestionar").serialize(),
          success: function(data){
            console.log(data);
            alert("Se guardó la fecha de la orden");
             
            //location.reload();
            $("#gestionar_orden").modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $("#pago_online").removeData('modal');
            //$("a#pago_on").trigger('click');
            buscar_pl();

            
          },

          error: function(data){ 
             
          }
        });
      }
        
    }
  }

  $("#pago_online").on('hidden', function(){
    $(this).removeData('modal');

  });

  function ingresar_cierre_caja(){
    $.ajax({
      type: 'get',
      url:"{{ route('cierrecaja.pago_en_linea_contab',['id' => $orden->id ]) }}", 
      
      datatype: 'json',
      success: function(data){
        

        
      },

      error: function(data){ 
         
      }
    });  
  }
      

</script>