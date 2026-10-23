<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <!--left-->
            <div class="col-md-12">
                <div class="box box-primary"> 
                    <div class="box-header with-border"><h3 class="box-title">Editar</h3></div>
                    <form name="f1" class="form-vertical" role="form" method="POST" action="{{ route('procedimiento.procedimientoguardar') }}">
                        <div class="box-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">          
                            <input type="hidden" name="id_procedimiento1" value="{{ $id }}"> 
                            <!--participantes-->
                            <div id="especialidades" class="col-md-12">
                            <div class="form-group col-xs-12">
                                <label  class="col-md-12 control-label">Seleccione a sugerencias del procedimiento {{$actual->nombre}}:</label>
                            </div>
                            @foreach($totales as $value)
                            <div class="form-group col-xs-4">
                                <label for="password-confirm" class="col-md-10 control-label" style="font-weight: 400;">{{$value->nombre}}</label>
                                <div class="col-md-2">
                                    <input name="lista[]" type="checkbox" 
                                    @foreach($actuales as $value2)
                                        @if($value->id == $value2->id_procedimiento2)
                                            checked
                                        @endif
                                    @endforeach
                                     value="{{$value->id}}">
                                </div>
                            </div>
                            @endforeach
                             </div>
                            <div class="form-group col-xs-12">
                                <div class="col-md-4">
                                    <button type="button" onclick="seleccionar_todo()" class="btn btn-primary">
                                    Seleccionar Todo
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" onclick="deseleccionar_todo()" class="btn btn-primary">
                                    Deseleccionar Todo
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                    Guardar
                                    </button>
                                </div>
                            </div>
                        </div>    
                    </form>
                </div>
            </div> 
        </div>
    </div> 
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
    function seleccionar_todo(){ 
       for (i=0;i<document.f1.elements.length;i++) 
          if(document.f1.elements[i].type == "checkbox")    
             document.f1.elements[i].checked=1 
    }
    function deseleccionar_todo(){ 
       for (i=0;i<document.f1.elements.length;i++) 
          if(document.f1.elements[i].type == "checkbox")    
             document.f1.elements[i].checked=0 
    }   
</script>