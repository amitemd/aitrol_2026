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
      color:#FFF;
    }

    .head-title{
      background-color: #888;
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
        <div class="col-md-5 size_text">
             <h5 class="modal-title">EGRESOS DE EMPLEADOS</h5>
        </div>
        <div class="col-md-7">
            <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
        </div>  
    </div>
    <div class="box-body dobra ">
      <div class="row head-title size_text">
        <div class="col-md-12">
          <label class="color_texto" for="title">EGRESOS CARGADOS AL EMPLEADO</label>
        </div>
      </div>
      <form id="guardar_egresos" method="post">

        <input type="hidden" name="id_empl" id="id_empl" value="{{$id_empleado}}">
        <!--Tipo Rol-->
        <div class="form-group  col-xs-12">
          <label for="tipo_rol" class="col-md-3 texto">Descontar en Tipo Rol:</label>
          <div class="col-md-7">

            <select id="tipo_rol" name="tipo_rol" class="form-control">
              <option>Seleccione...</option>
              @foreach($ct_tipo_rol as $value)
                <option value="{{$value->id}}">{{$value->descripcion}}</option>
              @endforeach  
            </select>
          </div>
        </div>
        <!--Monto a Descontar-->
        <div class="form-group  col-xs-12">
          <label for="monto_descontar" class="col-md-3 texto">Monto a Descontar:</label>
          <div class="col-md-7">
            <input id="monto_descontar" name="monto_descontar" type="text" class="form-control" value="0.00" onkeypress="return isNumberKey(event)" onblur="checkformat(this)">
          </div>
        </div>
        <!--Detalle de Descuento-->
        <div class="form-group  col-xs-12">
          <label for="detalle_descuento" class="col-md-3 texto">Detalles del Descuento:</label>
          <div class="col-md-7">
            <textarea id="detalle_descuento" name="detalle_descuento" type="text" class="form-control" style="height:10%"></textarea>
          </div>
          <button type="button" id="btn_egreso_emp" class="btn btn-success btn-gray">
            Agregar
          </button>
        </div>
        <div id="contenedor">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
            <div class="col-md-12">              
              <div class="table-responsive col-md-12">
                <input name="contador_egreso" id="contador_egreso" type="hidden" value="0">
                <table id="example2" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr class='well-dark'>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">TIPO MOV</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">DETALLES</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">REFLEJAR EN</th>
                      <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">MONTO</th>
                      <th width="15%" class="" tabindex="0" aria-controls="example2" rowspan="5" colspan="5" aria-label="Codigo: activate to sort column ascending">ACCIONES</th>
                    </tr>
                  </thead>
                  <tbody id="datos_egreso">
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="separator1"></div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        <button id="guarda_egr"  class="btn btn-primary"  onclick="store_egreso_empleado()">Guardar</button>
        <!--<button type="button" onclick="#" class="btn btn-primary">Guardar</button>-->
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>


<script type="text/javascript">

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function checkformat(entry) { 
      
      var test = entry.value;

      if (!isNaN(test)) {
          entry.value=parseFloat(entry.value).toFixed(2);
      }
      
      if (isNaN(entry.value) == true){      
          entry.value='0.00';        
      }
      if (test < 0) {
 
          entry.value = '0.00';
      }
    
    }


    $('#btn_egreso_emp').click(function(event){

      id= document.getElementById('contador_egreso').value;

      var tipo = $("#tipo_rol").val();

      var monto = $("#monto_descontar").val();

      var detalle = $("#detalle_descuento").val();

      /* miCampoTexto = document.getElementById("detalle_descuento").value;

      if (miCampoTexto.length > 0) {
          
          alert(miCampoTexto.length);
      }*/


      var tipo_m = 'DESCUENTOS, OTROS';


      if((tipo>0)&&(monto>0)&&(detalle != "")&&(detalle.length>0)){

        if(tipo == '1'){

          refleja = 'QUINCENA';

        }else{

            if(tipo == '2')
            {
              refleja = 'FIN DE MES';   

            }
        
        }

        var midiv_egreso = document.createElement("tr")
        midiv_egreso.setAttribute("id","dato"+id);

        midiv_egreso.innerHTML = '<input required type="hidden" id="visibilidad_egreso'+id+'" name="visibilidad_egreso'+id+'" value="1"></td><td><input class="form-control" name="tipo_mov'+id+'" id="tipo_mov'+id+'" value="'+tipo_m+'" readonly></td> <td><input class="form-control" name="detalle'+id+'" id="detalle'+id+'" readonly></td> <td> <input class="form-control" name="refleja'+id+'" id="refleja'+id+'"  value="'+refleja+'" readonly ></td> <td> <input class="form-control" name="monto'+id+'" id="monto'+id+'" readonly></td>  <td><button type="button" onclick="eliminar_egresos_empl('+id+')" class="btn btn-danger btn-gray">Eliminar</button></td>';
                
        document.getElementById('datos_egreso').appendChild(midiv_egreso);
        id = parseInt(id);
        
        $("#detalle"+id).val(detalle);
        $("#monto"+id).val(monto);

        id = id+1;
        document.getElementById('contador_egreso').value = id;

        /*Seteo de Valores*/
        $('#tipo_rol').val("");
        $('#monto_descontar').val("");
        $('#detalle_descuento').val("");

      }else{
  
         swal("¡Error!","Ingresa primero los datos","error");


      }
      
    });

    //Eliminar Registros de la Tabla Egresos Empleados
    function eliminar_egresos_empl(valor)
    {
        var dato_egreso1 = "dato"+valor;
        var nombre_egreso2 = 'visibilidad_egreso'+valor;
        document.getElementById(dato_egreso1).style.display='none';
        document.getElementById(nombre_egreso2).value = 0;
    }


    /*Guardado de Egresos del Empleado*/
    function store_egreso_empleado(){

      cont_tabla = document.getElementById('contador_egreso').value;

      if(cont_tabla == 0)
      {
        swal("¡Error!","Ingrese datos en la tabla","error");

        return false;
      }

      $.ajax({
            type: 'post',
            url:"{{route('nomina.store_egresos')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#guardar_egresos").serialize(),
            success: function(data){
                //console.log(data);
                location.href ="{{route('nomina.index')}}";
            },
            error: function(data){
                console.log(data);

            }
      });
 
    }




</script>