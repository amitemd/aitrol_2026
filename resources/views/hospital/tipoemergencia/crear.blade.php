@extends('hospital/tipoemergencia/base')
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
                    <h3 class="box-title">Crear Nuevo Tipo de Emergencia</h3>
               
        </div>
        </div>
<form method="post"  id="form_tipoemergencia" >
{{ csrf_field() }}

<div class="row justify-content-center">
	    <div class="card-body">
	    	<div class="form-group col-md-10">
                <label for="nombre">Nombre</label>
				<input type="text" name="nombre" size="20" class="form-control" value="" required  maxlength="10">
			</div>
		</div>
     </div>  
     

     <div class="container">
    <div class="form-group col-md-5 " >
    <button onclick="guardar_tipoemergencia();"  class="btn btn-primary btn-sm">Guardar</button>         
    </div>
 </div>

 </form>
 </div>
 </div>
 
</section>
<script type="text/javascript">
    function guardar_tipoemergencia(){
        $.ajax({
            type: 'post',
            url:"{{ route('tipoemergencia.guardar')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#form_tipoemergencia").serialize(),
            success: function(data){
            
            },

            error: function(data){

              console.log();


            }
        });
    }
</script>
@endsection