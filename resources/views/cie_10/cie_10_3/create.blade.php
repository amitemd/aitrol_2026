@extends('cie_10.cie_10_3.base')

@section('action-content')

<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Agregar CODIGO CIE 10_3</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('cie_10_3.store') }}">
                        {{ csrf_field() }}
                    
                        
                
                        <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Código</label>
                            <div class="col-md-3">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-4 control-label">Nombre Largo</label>
                            <div class="col-md-7">
                                <input id="descripcion" class="form-control input-sm" type="text" name="descripcion" value="{{ old('descripcion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--id_agrupador-->
                        <!--<div class="form-group col-md-12{{ $errors->has('id_agrupador') ? ' has-error' : '' }}">
                            <label for="id_agrupador" class="col-md-4 control-label">Agrupador</label>
                            <div class="col-md-3">
                                <select id="id_agrupador" name="id_agrupador" class="form-control" required>
                                        <option value="">Seleccione ..</option>
                                    @foreach($agrupadores as $agrupador) 
                                        <option @if(old('id_agrupador')== $agrupador->id) selected @endif value="{{$agrupador->id}}">{{$agrupador->nombre}}</option>   
                                    @endforeach
                                </select>  
                                
                                @if ($errors->has('id_agrupador'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_agrupador') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>-->

                        <!--<div class="form-group col-md-12{{ $errors->has('valor') ? ' has-error' : '' }}">
                            <label for="valor" class="col-md-4 control-label">Valor</label>
                            <div class="col-md-3">
                                <input id="valor" class="form-control input-sm" name="valor" value="{{ old('valor') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('valor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>-->

                    

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Agregar
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
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/examen')}}"></i> Examen</a></li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');
           

    });

    

</script>
@endsection
