@extends('hc_admision.controldoc.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">

                <div class="panel-heading">Agregar Documento</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('controldoc.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre</label>

                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required autofocus style="text-transform:uppercase;">

                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                            <label for="codigo" class="col-md-4 control-label">Código</label>

                            <div class="col-md-6">
                                <input id="codigo" type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" required autofocus style="text-transform:uppercase;" maxlength="6">

                                @if ($errors->has('codigo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('codigo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--proc/consulta-->
                        <div class="form-group {{ $errors->has('proc_consul') ? ' has-error' : '' }}">
                            <label for="proc_consul" class="col-md-4 control-label">Procedimiento/Consulta</label>
                            <div class="col-md-6">
                            <select id="proc_consul" name="proc_consul" class="form-control" required >
                                    <option @if(old('proc_consul')=='0') selected @endif value="0">Consulta</option>
                                    <option @if(old('proc_consul')=='1') selected @endif value="1">Procedimiento</option>
                                </select>      
                                @if ($errors->has('proc_consul'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('proc_consul') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--tipo seguro-->
                        <div class="form-group {{ $errors->has('tipo_seguro') ? ' has-error' : '' }}">
                            <label for="tipo_seguro" class="col-md-4 control-label">Tipo Seguro</label>
                            <div class="col-md-6">
                            <select id="tipo_seguro" name="tipo_seguro" class="form-control" onchange="crear_select()">
                                    <option @if(old('tipo_seguro')=='') selected @endif value="">Seleccione ...</option>
                                    <option @if(old('tipo_seguro')=='2') selected @endif value="2">Particular</option>
                                    <option @if(old('tipo_seguro')=='0') selected @endif value="0">Público</option>
                                    <option @if(old('tipo_seguro')=='1') selected @endif value="1">Privado</option>
                                </select>      
                                @if ($errors->has('tipo_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo_seguro') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--seguro-->
                        <div class="form-group {{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="col-md-4 control-label">Seguro</label>
                            <div class="col-md-6">
                            <select id="id_seguro" name="id_seguro" class="form-control"  onchange="crear_select2()">
                                
                                </select>      
                                @if ($errors->has('id_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_seguro') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--sub-seguro-->
                        <div class="form-group {{ $errors->has('id_subseguro') ? ' has-error' : '' }}">
                            <label for="id_subseguro" class="col-md-4 control-label">Sub-Seguro</label>
                            <div class="col-md-6">
                            <select id="id_subseguro" name="id_subseguro" class="form-control" >
                                
                                </select>      
                                @if ($errors->has('id_subseguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_subseguro') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--id_tipo_usuario-->
                        <div class="form-group {{ $errors->has('id_tipo_usuario') ? ' has-error' : '' }}">
                            <label for="id_tipo_usuario" class="col-md-4 control-label">Departamento que Entrega</label>
                            <div class="col-md-6">
                            <select id="id_tipo_usuario" name="id_tipo_usuario" class="form-control" required>
                                    <option @if(old('id_tipo_usuario')=='') selected @endif value="">Seleccione ...</option>
                                    @foreach($tiposusuario as $tipousuario)
                                        <option @if(old('id_tipo_usuario')=='0') selected @endif value="{{$tipousuario->id}}">{{$tipousuario->nombre}}</option>
                                    @endforeach                                    
                                </select>      
                                @if ($errors->has('id_tipo_usuario'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_tipo_usuario') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Crear
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

    $(document).ready(function() 
    {
        crear_select();
        crear_select2();

    });    
    
    function crear_select () {


        var sel_seguro = document.getElementById("id_seguro");
            $('option[class^="cl_seg"]').remove();
            var option = document.createElement("option");
            option.value = "";
            option.text = "Seleccione..";
            option.setAttribute("class", "cl_seg");
            sel_seguro.add(option);
        
        var js_tipo = document.getElementById("tipo_seguro").value;
        if(js_tipo!=""){
             
            
            @foreach($seguros as $seguro)
            if(js_tipo=={{$seguro->tipo}}){
            
                var option = document.createElement("option");
                option.value = "{{$seguro->id}}";
                option.text = "{{$seguro->nombre}}";
                option.setAttribute("class", "cl_seg");
                sel_seguro.add(option); 
            }
            @endforeach             
        }
    }    

    function crear_select2 () {
        
        var js_seguro = document.getElementById("id_seguro").value;
        var sel_sub = document.getElementById("id_subseguro");
        $('option[class^="cl_sub"]').remove();
        var option2 = document.createElement("option");
        option2.value = "";
        option2.text = "Seleccione..";
        option2.setAttribute("class", "cl_sub");
        sel_sub.add(option2);

        if(js_seguro!=""){
            
            @foreach($subseguros as $subseguro)
            if(js_seguro=={{$subseguro->id_seguro}}){
            
                var option2 = document.createElement("option");
                option2.value = "{{$subseguro->id}}";
                option2.text = "{{$subseguro->nombre}}";
                option2.setAttribute("class", "cl_sub");
                sel_sub.add(option2); 
            }
            @endforeach             
        }
    }    


</script>
@endsection

<style>
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div {
        height: 30px;
    }
</style>
