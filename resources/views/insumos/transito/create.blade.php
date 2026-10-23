<style type="text/css">
    .ui-autocomplete {
      z-index:2147483647;
    }
</style>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel">Ingresar producto a Empleado</h4>
</div>

<div class="modal-body">
    <form method="post" action="{{route('transito.store')}}" id="enviar2" >
        {{csrf_field()}}
        <!--Nombre del Empleado-->                        
        <div class="form-group col-xs-6{{ $errors->has('nombre_encargado') ? ' has-error' : '' }}">
            <label for="nombre_encargado" class="col-md-4 control-label">Nombre del empleado</label>
            <div class="col-md-7">
                <input id="nombre_encargado"  type="text" class="form-control" name="nombre_encargado"  required autofocus>
                @if ($errors->has('nombre_encargado'))
                    <span class="help-block">
                        <strong>{{ $errors->first('nombre_encargado') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <!--Cedula del Empleado--> 
        <div class="form-group col-xs-6{{ $errors->has('nombre_encargado') ? ' has-error' : '' }}">
            <label for="nombre_encargado" class="col-md-4 control-label">Cedula del Empleado</label>
            <div class="col-md-7">
                <input id="id_encargado" type="hidden" name="id_encargado" >
                <input id='mostrarCedula' type="text" class="form-control"  disabled="disabled" >
                @if ($errors->has('nombre_encargado'))
                    <span class="help-block">
                        <strong>{{ $errors->first('nombre_encargado') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <!--Codigo de Serie-->
        <div class="form-group col-xs-6{{ $errors->has('serie') ? ' has-error' : '' }}">
            <label for="serie" class="col-md-4 control-label">Codigo de Serie</label>
            <div class="col-md-7">
                <input id="serie" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  type="text" class="form-control" name="serie"  required >
                @if ($errors->has('serie'))
                    <span class="help-block">
                        <strong>{{ $errors->first('serie') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="form-group col-xs-6{{ $errors->has('nombre_encargado') ? ' has-error' : '' }}">
            <label for="nombre_producto" class="col-md-4 control-label">Nombre del Producto</label>
            <div class="col-md-7"> 
                <input id="valida" type="hidden" >
                <input id="nombre_producto" type="hidden" name="mostrarproducto" >
                <input id='mostrarproducto' type="text" class="form-control"  disabled="disabled" >
                @if ($errors->has('nombre_encargado'))
                    <span class="help-block">
                        <strong>{{ $errors->first('nombre_encargado') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="form-group col-xs-6{{ $errors->has('bodega') ? ' has-error' : '' }}">
            <label for="nombre_producto" class="col-md-4 control-label">Bodega</label>
            <div class="col-md-7"> 
               <select class="form-control" name="bodega" id="bodega" required>
                  <option value="">Seleccione ...</option>
                  <option value="1">Bodega Pentax</option>
               </select>
                @if ($errors->has('nombre_encargado'))
                    <span class="help-block">
                        <strong>{{ $errors->first('nombre_encargado') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <div class="col-md-12" style="text-align: center;">
        <button type="button" id="guardar" class="btn btn-primary btn-gray">Agregar</button>
        </div>
        
    </form>
</div>

<div class="modal-footer">
 
</div>

<script type="text/javascript">
    $(document).ready(function(){

        src2 = "{{route('transito.nombre')}}";
          $("#nombre_encargado").autocomplete({
              source: function( request, response ) {
                
                $.ajax({
                    url:"{{route('transito.nombre')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},    
                    data: {
                        term: request.term
                      },
                    dataType: "json",
                    type: 'post',
                    success: function(data){
                        response(data);
                    }
                })
              },
              minLength: 3,
            } );

          $("#nombre_encargado").change( function(){
              $.ajax({
                type: 'post',
                url:"{{route('transito.nombre2')}}",
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
                type: 'post',
                url:"{{route('transito.codigo')}}",
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
                var nombre2 = document.getElementById("bodega").value;
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
                if(nombre2 == ""){
                    msj+=  "Porfavor ingrese la bodega \n";
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
