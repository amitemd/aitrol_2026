  <style type="text/css">
    
      .h3{
        font-family: 'BrixSansBlack';
        font-size: 8pt;
        display: block;
        background: #3d7ba8;
        color: #FFF;
        text-align: center;
        padding: 3px;
        margin-bottom: 5px;
      }

      .info_nomina{
        width: 69%;
      }

      .round{
        border-radius: 10px;
        border: 1px solid #3d7ba8;
        overflow: hidden;
        padding-bottom: 15px;
      }

      .datos_nomina
      {
        font-size: 0.8em;
      }

      .mValue{
        width:79%;
        display: inline-block;
        vertical-align: top;
        padding-left:7px;
        font-size: 0.9em;
      }

      #rol_pago{
        width: 100%;
        margin-bottom: 10px;
      }


      .info_nomina .col-xs-8 {
          padding-left:10px;
          font-size: 0.9em;
      }
      .info_nomina .round{
          padding-top:10px;
      }

      .titulo-wrapper{
          width: 100%;
          text-align: center;
      }

      .modal-body .form-group {
          margin-bottom: 0px;
      }

      .h3.modal_h3{
          font-family: 'BrixSansBlack';
          font-size: 8pt;
          display: block;
          background: #3d7ba8;
          color: #FFF;
          text-align: center;
          padding: 3px;
          margin-bottom: 5px;
          padding: 7px;
          font-size: 1em;
          margin-bottom: 15px;
      }
      .h3.modal_h3_2{
          margin-top: -20px !important;
          margin-bottom: 25px !important;
          padding: 7px;
          font-size: 1em;
      }

      .swal-title {
          margin: 0px;
          font-size: 16px;
          box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
          margin-bottom: 28px;
      }

      .separator{
        width:100%;
        height:20px;
        clear: both;
      }

      .separator1{
        width:100%;
        height:5px;
        clear: both;
      }

      
      /* Nuevo CSS*/

      .mLabel{
        color: #777;
        font-size: 0.9rem;
        margin-bottom: 0;
        line-height: 10px;
      }

      .texto {
        color: #777;
        font-size: 0.9rem;
        margin-bottom: 0;
        line-height: 15px;
      }

      .color_texto{
        color:#FFFFFF;
      }

      .head-title{
        background-color: #4682B4;
        margin-left: 0px;
        margin-right: 0px;
        height: 30px;
        line-height: 30px;
        color: #cccccc;
        text-align: center;
      }

      .t9{
        font-size: 0.9rem;
      }

      .well-dark{
        background-color: #cccccc;
      }

  </style>

<div class="modal-content" style="width: 100%;">
    <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
      <div class="row" style="border-bottom: 1px solid black;">
        <div class="col-md-2">
          <button type="button" class="btn btn-light" onclick="store_plano_detalle();" id="store_plano">
            <h1 style="font-size: 12px; margin:0;">
                <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/guardar.png">
                <label style="font-size: 14px">Guardar</label>
            </h1> 
          </button>
        </div>
        <div class="col-md-2">
          <a class="btn btn-light" data-dismiss="modal" id="exit_procedimiento">
            <h1 style="font-size: 12px; margin:0;">
                <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
                <label style="font-size: 14px">Cerrar</label>
            </h1> 
          </a>
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
          <span id="Label8" style="color:#003366;font-family:Arial;font-size:14pt;font-weight:bold;">Ingreso de Procedimientos
        </span>
      </div>
      </div> 
    </div>
    <div class="box-body">
      <form id="list_procedimiento" method="post">
          {{ csrf_field() }}
          <input type="hidden" name="plan_cabecera" value="{{$id_plan_cab}}">
          <input type="hidden" name="nivel_convenio" id="nivel_convenio" value="{{$archivo_plano_cab->id_nivel}}">
          <input type="hidden" name="j_seguro" id="j_seguro" value="{{$archivo_plano_cab->id_seguro}}">
          <div class="form-group col-md-7 col-xs-7">
            <label for="nivel_convenio" class="col-md-4 control-label">Nivel de Convenio:</label>
            <div class="col-md-7">
		          {{$archivo_plano_cab->id_nivel}}
	          </div>
          </div>
          <div class="form-group col-md-8 col-xs-8">
            <label for="fecha" class="col-md-3 control-label">Fecha:</label>
            <div class="col-md-7">
              <!--<input type="date" name="fecha" id="fecha" class="form-control input-sm">-->
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" name="fecha" id="fecha" class="form-control input-sm" value="{{ old('fecha') }}" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = '';"></i>
                </div>               
              </div>
            </div>  
          </div>
          <div class="form-group col-md-4 col-xs-4">
              <label for="codigo_oculto" class="col-md-3 control-label"></label>
              <div class="col-md-7">
              </div>
          </div>
          <div class="form-group col-md-8 col-xs-8">
            <label for="procedimiento" class="col-md-3 control-label">Procedimiento:</label>
            <div class="col-md-7">
                <select id="id_procedimiento" name="id_procedimiento" class="form-control input-sm" >
                  @foreach($lista as $value)
                    <option value="{{ $value->codigo }}">{{$value->desc_comp}}</option>
                  @endforeach
                </select>
            </div>
            <button type="button" onclick="buscar_procedimiento();" class="btn btn-primary" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button>
          </div> 
      </form>
    </div>
    <div style="border-radius: 8px;" id="det_proc">
      <div id="contenedor">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
          <div class="col-md-12">
            <div class="table-responsive col-md-12">
              <table cellspacing="0" cellpadding="3" rules="all" id="grdgitem" style="background-color:White;border-color:#CCCCCC;border-width:1px;border-style:None;font-family:Arial;font-size:10px;width:100%;border-collapse:collapse;">
                <thead>
                  <tr style="color:White;background-color:#006699;font-weight:bold;">
                    @if($archivo_plano_cab->id_seguro == 5)
                    <th>CLASIFICADOR</th>
                    @endif
                    <th>TIPO</th>
                    <th>CODIGO</th>
                    <th>DESCRIPCION</th>
                    <th>CANTIDAD</th>
                    <th>VALOR</th>
                    <th>IVA</th>
                  </tr>
                </thead>
              </table>
            </div> 
          </div> 
        </div>
      </div>
    </div>
    <div class="separator1"></div>
    <div class="modal-footer">
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">

      $(function(){
        
        $('#fecha').datetimepicker({
          useCurrent: false,
          format: 'DD/MM/YYYY',
          @if($archivo_plano_cab->fecha_ing !=null)
              defaultDate: '{{$archivo_plano_cab->fecha_ing}}',
	        @endif
          //format: 'YYYY/MM/DD',
          //defaultDate: '{{date("Y-m-d")}}',
        });

      });

      function buscar_procedimiento(){

        var formulario = document.forms["list_procedimiento"];
        var id_proced = formulario.id_procedimiento.value;
      
        var msj = "";

        if(id_proced == ""){
          msj = msj + "Por favor, Seleccione el Procedimiento<br/>";
        }

        if(msj != ""){
            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
        }

        $.ajax({
          type: 'post',
          url:"{{route('buscar.procedimiento')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#list_procedimiento").serialize(),
          success: function(data){
            $("#det_proc").html(data);
            //console.log(data);
          },
          error: function(data){
            console.log(data);
          }
        });

      }


      function store_plano_detalle(){
        $('#store_plano').prop("disabled",true);
        var fec = document.getElementById('fecha').value;
        var id_proced = document.getElementById("id_procedimiento").value;

        if (fec=='') {
          swal("¡Error!","Seleccione la Fecha","error");
          $('#store_plano').removeAttr('disabled');
          return false;
        }

        if(id_proced == ""){
          swal("¡Error!","Por favor, Seleccione el Procedimiento y presione buscar");
          $('#store_plano').removeAttr('disabled');
          return false;
        }
        
        $.ajax({
          type: 'post',
          url:"{{route('ingreso_lista.procedimiento')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#list_procedimiento").serialize(),
          success: function(data){
            //$("#det_proc").html(data);
            console.log(data);
            //location.reload();
            mostrar_detalle();
            $('#exit_procedimiento').click();
          },
          error: function(data){
            $('#store_plano').removeAttr('disabled');
            console.log(data);
          }
        });

      }



      /*function store_plano_detalle(id_plan_cab){

        var fec = document.getElementById('fecha').value;
        var id_proced = document.getElementById("id_procedimiento").value;

        if (fec=='') {
          swal("¡Error!","Seleccione la Fecha","error");
          return false;
        }

        if(id_proced == ""){
          swal("¡Error!","Por favor, Seleccione el Procedimiento y presione buscar");
          return false;
        }

        $.ajax({
          //type: 'post',
          type: "GET",
          url:"{{ url('archivo_plano/planilla/detalle/procedimiento/ingresar') }}/"+ id_plan_cab + "/"+ id_proced + "/"+ fec,
          //headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          //data: $("#list_procedimiento").serialize(),
          success: function(data){
            //$("#det_proc").html(data);
            $("#det_proc").html(data);
            //console.log(data);
            
            //location.reload();
          },
          error: function(data){
            console.log(data);
          }
        });

      }*/

</script>



