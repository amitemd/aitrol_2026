@extends('laboratorio.exa_agrupadores.base')

@section('action-content')

<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Editar Código AGRUPADORES DE EXAMENES</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('exa_agrupadores.update',['id' => $examen_agrupador->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        {{ csrf_field() }}
                        
                    
                        
                        
                        <div class="form-group col-md-12{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">Código</label>
                            <div class="col-md-3">
                                <input id="id" class="form-control input-sm" type="text" name="id" readonly value=@if(old('id')!='')"{{old('id')}}"@else"{{$examen_agrupador->id}}"@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre Agrupador</label>
                            <div class="col-md-7">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value=@if(old('nombre')!='')"{{old('nombre')}}" @else "{{$examen_agrupador->nombre}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        

                        
                    

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
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/cie_10_3')}}"></i> Cie 10 3</a></li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');
           

    });

    

</script>
@endsection
