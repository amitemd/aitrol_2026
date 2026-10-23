@extends('hospital/prioridademergencia/base')
@section('action-content')
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        @page {
            margin: 45px 80px;
        }

        .table>tbody>tr>td {
            padding: 0;
        }

        #paginacion {
            border: 1px solid #CCC;
            background-color: #E0E0E0;
            padding: .5em;
            overflow: hidden;
        }

        .texto {
            font-size: 15.0px;
            font-family: Arial;
        }
    </style>
</head>

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-11">
                    <h3 class="box-title">Editar Prioridad de Emergencia</h3>
               
        </div>
        </div>
        <form id="form_actualizarprioridad" method="POST">
        {{ csrf_field() }}
        <input type="hidden" value="{{$tipo->id}}" name="idtipo">
            <div class="row justify-content-center">
        	    <div class="card-body">
        	    	<div class="form-group col-md-10">
                        <label for="nombre">Nombre</label>
        				<input type="text" name="nombre" size="20" class="form-control" value="{{$tipo->nombre}}" required  maxlength="100">
        			</div>
        		</div>
             </div>  
             <div class="row justify-content-center">
        	    <div class="card-body">
        	    	<div class="form-group col-md-10">
                        <label for="prioridad">Prioridad</label>
        				<input type="double" name="prioridad" size="20" class="form-control" value="{{$tipo->prioridad}}" required  maxlength="10">
        			</div>
        		</div>
             </div>  

             <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
             <label for="color" class="col-md-10 control-label">Color</label>
                <div class="col-md-10 colorpicker">
                     <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{$tipo->color }}" required>
                     <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span> 
                     @if ($errors->has('color'))
                     <span class="help-block">
                     <strong>{{ $errors->first('color') }}</strong>
                     </span>
                      @endif
                </div>
            </div>

             <div class="container">
            <div class="form-group col-md-5 " >
            <button type="button" onclick="actualizar_prioridad();" class="btn btn-primary btn-sm">Actualizar</button>         
            </div>
        </div>

 </form>
 </div>
 </div>

 </section> 

 <script type="text/javascript">

    function actualizar_prioridad(){

        //alert('asdfff');
        $.ajax({
            type: 'post',
            url:"{{route('prioridad.actualizar_pri')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_actualizarprioridad").serialize(),
            success: function(data){
                if (data == 'ok') {
                    location.href="{{route('prioridademergencia.index')}}";
                }
            },

            error: function(data){

              //console.log();
            }
        });
    }
</script>

@endsection
