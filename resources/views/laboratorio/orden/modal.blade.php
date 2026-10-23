<div class="modal-header">
    <div class="col-md-10"><h3>Actualizar {{$parametro->nombre}}:</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">
    
        
    
            
    <form id="frm">
        <div class="box-body">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">          
            <input type="hidden" name="id_parametro" value="{{ $parametro->id }}">
            <input type="hidden" name="id_orden" value="{{ $id_orden }}">
            <!--participantes-->
            <div id="especialidades" class="col-md-12">
            
            <div id="dvalor" class="form-group col-xs-12{{ $errors->has('valor') ? ' has-error' : '' }}">
                <label for="valor" class="col-md-4 control-label">Valor</label>
                <div class="col-md-7">
                    @if($parametro->id_examen=='661')
                    <input id="valor" type="text" class="form-control" name="valor" value="@if($resultado != ''){{$resultado->valor}}@else{{''}}@endif" required autofocus onchange="guardar();" autocomplete="off">
                    @else
                      @if($parametro->id == '785')
                        <button type="button" class="btn btn-info btn-xs" onclick="covid_igm('P')">POSITIVO</button>
                        <button type="button" class="btn btn-danger btn-xs" onclick="covid_igm('N')">NEGATIVO</button>
                      @endif
                      @if($parametro->id == '784')
                        <button type="button" class="btn btn-info btn-xs" onclick="covid_igg('P')">POSITIVO</button>
                        <button type="button" class="btn btn-danger btn-xs" onclick="covid_igg('N')">NEGATIVO</button>
                      @endif
                    <input id="valor" type="text" class="form-control" name="valor" value="@if($resultado != ''){{$resultado->valor}}@else{{'0'}}@endif" required autofocus onchange="guardar();" autocomplete="off">
                    @endif
                    @if ($errors->has('valor'))
                        <span class="help-block">
                            <strong>{{ $errors->first('valor') }}</strong>
                        </span>
                    @endif 
                </div>
            </div>
            <!--div class="form-group col-xs-12">
                
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                    Guardar
                    </button>
                </div>
            </div-->
        </div>    
    </form>
     
        
     
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
    
    function covid_igm(signo){
      if(signo=='P'){
        var valor = Math.random() * (130 - 110) + 110;
        valor = valor / 100;
        valor = Math.round(valor * 100) / 100;
        $('#valor').val(valor);guardar();
      }
      if(signo=='N'){
        var valor = Math.random() * (90 - 10) + 10;
        valor = valor / 100;
        valor = Math.round(valor * 100) / 100;
        $('#valor').val(valor);guardar();
      }
      

    }

    function covid_igg(signo){
      if(signo=='N'){
        var valor = Math.random() * (130 - 110) + 110;
        valor = valor / 100;
        valor = Math.round(valor * 100) / 100;
        $('#valor').val(valor);guardar();
      }
      if(signo=='P'){
        var valor = Math.random() * (1300 - 1100) + 1100;
        valor = valor / 100;
        valor = Math.round(valor * 100) / 100;
        $('#valor').val(valor);guardar();
      }
      

    }

    function guardar(){

        $.ajax({
          type: 'post',
          url:"{{ route('resultados.guardar_actualizar_resultados') }}", 
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            
            //alert(data);
            rango(data[0]);
            $('#'+data[0]).text( data[1] );
            $('.close').click();
            //console.log($('#ch'+data[0]));
            $('#ch'+data[0]).iCheck('enable');
            

          },


          error: function(data){

            if(data.responseJSON.valor!=null){
                $('#dvalor').addClass('has-error');
                alert(data.responseJSON.valor[0]);
            }
            
             
          }
        });
    }

    function rango(id){
        $.ajax({
          type: 'post',
          url:"{{ route('resultados.validacion_maximos') }}", 
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#frm").serialize(),
          success: function(data){
            if(data=='mayor'){
                $('#rg'+id).text('SUPERIOR AL RANGO');
            }
            if(data=='menor'){
                $('#rg'+id).text('INFERIOR AL RANGO');
            }
            $('#rg2'+id).hide();
          },


          error: function(data){

            if(data.responseJSON.valor!=null){
                $('#dvalor').addClass('has-error');
                alert(data.responseJSON.valor[0]);
            }
            
             
          }
        });    
    }

</script>