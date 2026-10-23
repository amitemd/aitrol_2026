@extends('cortesia.base')

@section('action-content')
<div class="container-fluid" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary ">
                <div class="box-header with-border "><h3 class="box-title">Agregar Nueva Cortesia</h3></div>
                <div class="box-body ">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('cortesia.store') }}">
                        {{ csrf_field() }}
                   
                        <!--cedula-->
                        <div class="form-group col-md-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">Cédula Paciente</label>
                            <div class="col-md-7">
                                <input maxlength="10" id="id" type="text" class="form-control" name="id" value="@if(old('id')!=''){{ old('id') }}@elseif($id!=''){{$id}}@endif" onchange="teclaEnter2(event);" required autofocus>
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>    
                        </div>
                        <!--nombre-->
                        <div class="form-group col-md-6">           
                            <label for="nombre" class="col-md-4 control-label">Nombre </label>
                            <div class="col-md-7">
                                <input maxlength="10" id="nombre" type="text" class="form-control" name="nombre" value="@if(!is_null($paciente)){{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}} @endif" readonly>
                            </div>       
                        </div>
                    
                        <!--cortesia-->
                        <div class="form-group col-md-6{{ $errors->has('cortesia') ? ' has-error' : '' }}">
                            <label for="cortesia" class="col-md-4 control-label">Cortesia</label>
                            <div class="col-md-7">
                                <select id="cortesia"  class="form-control" name="cortesia" required autofocus>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>    
                                @if ($errors->has('cortesia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cortesia') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--ilimitado-->
                        <div class="form-group col-md-6{{ $errors->has('ilimitado') ? ' has-error' : '' }}">
                            <label for="ilimitado" class="col-md-4 control-label">Ilimitada</label>
                            <div class="col-md-7">
                                <select id="ilimitado"  class="form-control" name="ilimitado" required autofocus>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>    
                                @if ($errors->has('ilimitado'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ilimitado') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Agregar
                                </button>
                            </div>
                        </div>

                
                    </form>
                </div>    
            </div>
        </div>
    </div>
</div> 

<script type="text/javascript">
function teclaEnter2(e)
{
    vcedula = document.getElementById("id").value;
        
        vcedula =  vcedula.trim();
        if (vcedula != ""){
             
             
             location.href = "{{ route('cortesia.crear3') }}/"+vcedula;

        }

}

</script>

@endsection
