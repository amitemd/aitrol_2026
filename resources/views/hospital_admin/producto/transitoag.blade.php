<style type="text/css">
    .ui-autocomplete {
      z-index:2147483647;
    }
</style>

<div class="modal-header">
  <h4 class="modal-title" id="myModalLabel">Ingresar producto a Empleado</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>

<div class="modal-body">
    <form method="GET" action="{{route('htransito.agregartransito')}}" id="enviar2" >
        {{csrf_field()}}
        <!--Nombre del Empleado-->                        
        <div class="form-group col-xs-6{{ $errors->has('nombre_encargado') ? ' has-error' : '' }}">
            <label for="nombre_encargado" class="col-md-6 control-label">Nombre del empleado</label>
            <div class="col-md-12">
                <input id="nombre_encargado"  type="text" class="form-control" name="nombre_encargado"  required autofocus>
            </div>
        </div>
        <!--Cedula del Empleado--> 
        <div class="form-group col-xs-6{{ $errors->has('nombre_encargado') ? ' has-error' : '' }}">
            <label for="nombre_encargado" class="col-md-6 control-label">Cedula del Empleado</label>
            <div class="col-md-12">
                <input id="id_encargado" type="hidden" name="id_encargado" >
                <input id='mostrarCedula' type="text" class="form-control"  disabled="disabled">
            </div>
        </div>
        <!--Codigo de Serie-->
        <div class="form-group col-xs-6{{ $errors->has('serie') ? ' has-error' : '' }}">
            <label for="serie" class="col-md-6 control-label">Codigo de Serie</label>
            <div class="col-md-12">
                <input id="serie" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  type="text" class="form-control" name="serie"  required>
            </div>
        </div>
        <div class="form-group col-xs-6{{ $errors->has('nombre_encargado') ? ' has-error' : '' }}">
            <label for="nombre_producto" class="col-md-6 control-label">Nombre del Producto</label>
            <div class="col-md-12"> 
                <input id="valida" type="hidden" >
                <input id="nombre_producto" type="hidden" name="mostrarproducto" >
                <input id='mostrarproducto' type="text" class="form-control"  disabled="disabled">
            </div>
        </div>
        <button type="button" id="guardar" class="btn btn-primary">Agregar</button>
    </form>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>
  <script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
    
    $(document).ready(function(){
        src2 = "{{route('htransito.nombre')}}";
          $("#nombre_encargado").autocomplete({
              source: function( request, response ) {
                
                $.ajax({
                    url:"{{route('htransito.nombre')}}",
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

          $("#nombre_encargado").change( function(){
              $.ajax({
                type: 'get',
                url:"{{route('htransito.nombre2')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#nombre_encargado"),
                success: function(data){
                    $('#mostrarCedula').val(data);
                    $('#id_encargado').val(data);
                    console.log(data);
                },
                error: function(data){
                    console.log(data);
                }
            })
          });

          $("#serie").change( function(){
            var elemento = document.getElementById("serie").value;
            
            $.ajax({
                type: 'get',
                url:"{{route('htransito.codigo')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#serie"),
                success: function(data){
                    $('#mostrarproducto').val(data);
                    $('#nombre_producto').val(data);
                    $('#nombre_producto').val("1");
                    console.log(data);
                    },
                error: function(data){
                    console.log(data);
                }
            });
          });

          $("#guardar").click( function(){
                    var nombre = document.getElementById("id_encargado").value;
                var validacion = document.getElementById("valida").value;
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
