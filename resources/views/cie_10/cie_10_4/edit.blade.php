@extends('cie_10.cie_10_4.base')

@section('action-content')

<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Editar Código CIE 10 4</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('cie_10_4.update',['id' => $cie_10_4->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        {{ csrf_field() }}
                        
                    
                        
                
                        <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Código</label>
                            <div class="col-md-3">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value=@if(old('nombre')!='')"{{old('nombre')}}"@else"{{$cie_10_4->id}}"@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                         <div class="form-group col-md-12{{ $errors->has('id_cie_10_3') ? ' has-error' : '' }}">
                            <label for="id_cie_10_3" class="col-md-4 control-label">Cie 10 3</label>
                            <div class="col-md-3">
                                <select id="id_cie_10_3" name="id_cie_10_3" class="form-control input-sm">
                                        @foreach ($cie_10_3c as $value)
                                            <option @if($value->id==$cie_10_4->id_cie_10_3) selected @endif value="{{$value->id}}">{{$value->id}}--{{$value->descripcion}}</option>
                                        @endforeach
                                    </select>      
                                @if ($errors->has('id_cie_10_3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_cie_10_3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-4 control-label">Nombre Largo</label>
                            <div class="col-md-7">
                                <input id="descripcion" class="form-control input-sm" type="text" name="descripcion" value=@if(old('descripcion')!='')"{{old('descripcion')}}" @else "{{$cie_10_4->descripcion}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
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
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/cie_10_4')}}"></i> Cie 10 4</a></li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');
           

    });

    

</script>
@endsection
