@extends('laboratorio.protocolo.base')

@section('action-content')

<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Agregar Protocolo</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('protocolo.store') }}">
                        {{ csrf_field() }}
                    
                        
                
                        <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre</label>
                            <div class="col-md-3">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>


                        <!--id_agrupador-->
                        <div class="form-group col-md-12{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="col-md-4 control-label">Ambulatorio / Hospitalizado</label>
                            <div class="col-md-3">
                                <select id="est_amb_hos" name="est_amb_hos" class="form-control" required>
                                        <option @if(old('est_amb_hos')== '0') selected @endif value="0">Ambulatorio</option>
                                        <option @if(old('est_amb_hos')== '1') selected @endif value="1">Hospitalizado</option>
                                </select>  
                                
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
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
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/protocolo')}}"></i> Protocolo</a></li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');
           

    });

    

</script>
@endsection
