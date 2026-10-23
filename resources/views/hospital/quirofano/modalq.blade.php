<!-- Modal content-->
<style>
h4, .close{
  color: white;
}
label, input{
  font-family: "Montserrat Bold";
}
.ui-autocomplete {
    z-index:2147483647;
    position: absolute;
    top: 100%;
    left: 0;
   
    float: left;
    display: none;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
    *border-right-width: 2px;
    *border-bottom-width: 2px;
}
.ui-menu-item > a.ui-corner-all {
    display: block;
    padding: 3px 15px;
    clear: both;
    font-weight: normal;
    line-height: 18px;
    color: #555555;
    white-space: nowrap;
    text-decoration: none;
}

.ui-state-hover, .ui-state-active {
    color: #ffffff;
    text-decoration: none;
    background-color: #0088cc;
    border-radius: 0px;
    -webkit-border-radius: 0px;
    -moz-border-radius: 0px;
    background-image: none;
}
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<div class="modal-content" id="change">
  <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px; background-color: #004AC1;">
    <h4 class="modal-title">ASIGNACIÓN DE QUIR&Oacute;FANO AL PACIENTE</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>

  <div class="modal-body">
    <form  method="GET" action="{{route('hospital.agenda')}}" id="enviar2">
      <div class="row">     
        <div class="col-8 form-group {{ $errors->has('apellido') ? ' has-error' : '' }}">
          <label for="apellido" class = "col-form-label">APELLIDOS Y NOMBRE</label>
          <input type="text" class = "form-control" id="apellido" name="apellido" placeholder="ESCRIBA AL MENOS TRES LETRAS DEL APELLIDO" required autofocus>
          <input id="id_paciente" type="hidden" name="id_paciente">
        </div>
        <div class="col-4 form-group {{ $errors->has('fecha_nacimiento') ? ' has-error' : '' }}">
          <label for="fecha-nacimiento" class = "col-form-label">FECHA DE NACIMIENTO</label>
          <input type="text" class = "form-control" id = "fecha_nacimiento" name="fecha_nacimiento" placeholder="FECHA DE NACIMIENTO" readonly>
        </div>
      </div>
      @php
        $fechaini= $_GET['fechaini']; $fechatotal=substr($fechaini,0,10); $horaini= substr($fechaini,11,18);
        $fechafin= $_GET['fechafin']; $horafin= substr($fechafin,11,18);
      @endphp
      <div class="row">
        <div class="col-3 form-group">
          <label for="sexo" class = "col-form-label">SEXO</label>
          <input type="text" id="sexo" class="form-control" readonly>
        </div>
        <div class="col-3 form-group">
          <label for="telefono" class = "col-form-label">TEL&Eacute;FONO</label>
          <input type="number" class = "form-control" id = "telefono" placeholder="TELEFONO" readonly>
        </div>
        <div class="col-3 form-group">
          <label for="celular" class = "col-form-label">CELULAR</label>
          <input type="number" class = "form-control" id = "celular" placeholder="CELULAR" readonly>
        </div>
        <div class="col-3 form-group">
          <label for="seguro" class = "col-form-label">SEGURO</label>
          <input type="text" class = "form-control" id = "seguro" placeholder="SEGURO" readonly>
          
        </div>
      </div>
      <div class="row">
        <div class="col-4 form-group">
          <label for="fecha-operacion" class = "col-form-label">FECHA DE OPERACI&Oacute;N</label>
          <label>{{$fechatotal}}</label>
          <input id="fechaini" value="{{$fechaini}}" type="hidden" name="fechaini" >
          <input id="fechafin" value="{{$fechafin}}" type="hidden" name="fechafin" >
        </div>
        <div class="col-4 form-group">
          <label for="hora" class = "col-form-label">HORA</label>
          <input type="text" value="{{$horaini}}" name="horaini" id="horaini" readonly> Hasta 
          <input type="text" value="{{$horafin}}" name="horafin" id="horafin" readonly>
        </div>
        <div class="col-4 form-group">
           <label for="hora" class = "col-form-label">PRECIO OPERACION:</label>
          <input type="text" class="col-form-label" name="costo" placeholder="PRECIO">
      </div>
      </div>
      <div class="form-group">
        <label for="descripcion">DESCRIPCI&Oacute;N OPERATORIA</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
      </div>
      <div id="demo">
      </div>
    </form>
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-primary" id="guardar" data-dismiss="modal">AGENDAR</button>
  </div>
</div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>    
<script type="text/javascript">

  $(function () {
    $('#fecha').datetimepicker({
      format: 'YYYY/MM/DD',
      
      @php
      $fechaini= $_GET['fechaini'];
      $horaini= substr($fechaini,0,10);
      @endphp
      defaultDate: '{{$fechaini}}',
      
    });
    $("#fecha").on("dp.change", function (e) {
      buscar();
    });
    $('#fecha_hasta').datetimepicker({
      @php
      $fechafin= $_GET['fechafin'];
      @endphp
      format: 'YYYY/MM/DD',
      defaultDate: '{{$fechafin}}',
      });
  });
  function buscar(){

  }
</script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
    
    $(document).ready(function(){
        src2 = "{{route('hospital.autocomplete')}}";
          $("#apellido").autocomplete({
              source: function( request, response ) {
                
                $.ajax({
                    url:"{{route('hospital.autocomplete')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                    data: {
                        term: request.term
                      },
                    dataType: "json",
                    type: 'get',
                    success: function(data){
                    response(data);
                    console.log(data);
                    }
                })
              },
              minLength: 3,
            } );

          $("#apellido").change( function(){
            
              $.ajax({
                type: 'get',
                url:"{{route('hospital.autocomplete2')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#apellido"),
                success: function(data){
               // an *array* that contains the user
                  var seguro;
                  var sexo;
                  var user = data[0];         // a simple user
                    if((user.seguro)==1){
                      seguro= 'PARTICULAR';
                    }
                    else if((user.seguro)==2){
                      seguro= 'IESS';
                    }
                    else if((user.seguro)==3){
                      seguro='ISSFA';
                    }
                    else if((user.seguro)==4){
                      seguro='HUMANA';
                    }
                    else if((user.seguro)==5){
                      seguro='MSP';
                    }
                    else if((user.seguro)==6){
                      seguro='ISSPOL';
                    }

                    if((user.sexo)==1){
                      sexo= 'HOMBRE';
                    }
                    else if((user.sexo)==2){
                      sexo= 'MUJER';
                    }
                    $('#fecha_nacimiento').val(user.fecha);
                    $('#telefono').val(user.telefono1);
                    $('#celular').val(user.telefono2);
                    $('#seguro').val(seguro);    
                    $('#sexo').val(sexo);
                    $('#id_paciente').val(user.id);
                    console.log(user.fecha);
                },
                error: function(data){
                    console.log(data);
                }
            })
          });


          $("#guardar").click( function(){
            var nombre = document.getElementById("apellido").value;
            var msj = ""
            if(nombre == ""){
              msj =  "Porfavor ingrese la persona encargada \n";
            }
            if(nombre == ""){
              msj =  "Porfavor ingrese la persona encargada \n";
            }
            if(nombre == ""){
              msj+=  "Porfavor ingrese el numero de serie del producto \n";
            }
            if(msj == ""){
              $('#enviar2').submit();   
            }
            else{
              alert(msj)
            }
          });
    });
</script>
