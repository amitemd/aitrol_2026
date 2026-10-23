<div class="modal-content" style="width:auto">
    <div class="modal-header">
        <h1 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">Eliminar Registro de Cierre Contable</h1>
    </div>
    <div class="modal-body">
        <div class="box-body">
            <form id="frm_eliminar" class="form-vertical" role="form" method="POST" >
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <!--Codigo-->
                    <div class="form-group col-xs-12{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="col-md-12 alert-danger control-label" style="text-align: center;">Al eliminar el registro podran realizar ingresos en los diferentes modulos.</label>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-5 control-label" style="text-align: right;">Por favor ingrese su contraseña</label>
                        <div class="col-md-3">
                            <input id="password" type="password" class="form-control" name="password"  required autofocus >
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="button" onclick="eliminar_pedido()" class="btn btn-primary">
                            {{trans('winsumos.eliminar')}}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function eliminar_pedido(){
        $.ajax({
          type: 'put',
          url:"{{route('cierre.mensual.update', ['id' => $id])}}", // hc4/HistoriaPacienteController->ingreso
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},

          datatype: 'json',
          data: $("#frm_eliminar").serialize(),
          success: function(data){
            if(data.status == 'error'){
                alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)         
            }else{
                var dato_url = "{{ route('cierre.mensual.index')}}";
                alertas(data.status, `${data.status.toUpperCase()}...`, data.msj)   
                setTimeout(function(){ location.href = dato_url; }, 1000);
            }
          },
          error: function(data){
             //console.log(data);
          }
        });
    }
</script>