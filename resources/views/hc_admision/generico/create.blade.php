@extends('hc_admision.generico.base')

@section('action-content')

<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><div class="col-md-9"><h3 class="box-title">Crear Generico</h3></div><div class="col-md-3"></div><a type="button" href="{{route('generico.index',['agenda' => $agenda])}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
            </a></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('generico.store') }}">
                        
                        {{ csrf_field() }}
                        <input type="hidden" name="agenda" value="{{$agenda}}">
                        
                        <div class="form-group col-md-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="control-label">Nombre</label>
                            
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value="{{old('nombre')}}" required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            
                        </div>

                        <div class="form-group col-md-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="control-label">Descripción</label>
                            
                                <input id="descripcion" class="form-control input-sm" type="text" name="descripcion" value="{{old('descripcion')}}" required autofocus>
                                @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                                @endif
                            
                        </div>


                        <!--ESTADO-->
                        <div class="form-group col-md-3{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="control-label">Estado</label>
                            
                                <select id="estado" name="estado" class="form-control" required>
                                    <option @if(old('estado')== '1') selected @endif value="1">ACTIVO</option> 
                                    <option @if(old('estado')== '0') selected @endif value="0">INACTIVO</option>
                                      
                                </select>  
                                
                                @if ($errors->has('estado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado') }}</strong>
                                    </span>
                                @endif
                            
                        </div>
                 

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Crear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            
            </div>
        </div>
    </div>
    
</section>



<script type="text/javascript">

    $(document).ready(function() {
       

        
        
           

    });

    

</script>
@endsection
