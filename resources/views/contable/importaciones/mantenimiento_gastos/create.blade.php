@extends('contable.importaciones.mantenimiento_gastos.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.importaciones')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{trans('contableM.gastos')}}</li>
        </ol>
    </nav>
    <form id="form_gasto" class="form-horizontal" role="form" >
    	{{ csrf_field() }}
    	<div class="box">
    		<div class="box-header header_new">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-9 col-sm-9 col-6">

                            <div class="box-title"><b>{{trans('contableM.mangastos')}}</b></div>
                        </div>
                        <div class="col-3" style="text-align:center">
                            <div class="row">
                                <a class="btn btn-success btn-gray " style="margin-left: 3px;" href="javascript:goBack()">
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body dobra">
            	<div class="form-group">
                <div class="col-md-10">
	                    <label for="nombre" class="texto col-md-2 control-label">{{trans('contableM.codigo')}}:</label>
	                    <div class="col-md-3">
	                        <input type="text" class="form-control" id="codigo" name="codigo"  required/>
	                    </div>
	                </div>
            		<div class="col-md-10">
	                    <label for="nombre" class="texto col-md-2 control-label">{{trans('contableM.nombre')}}:</label>
	                    <div class="col-md-6">
	                        <input type="text" class="form-control" id="nombre" name="nombre"  required/>
	                    </div>
	                </div>
            	</div>
                <!--div class="form-group">
                	<div class="col-md-10">
	                    <label for="cuenta" class="texto col-md-2 control-label">Cuenta:</label>
	                    <div class="col-md-3">
	                        <select id="id_plan_cuenta" name="id_plan_cuenta"  class="form-control select2_cuentas" style="width: 100%;" required>
	                            <option value="">Seleccione...</option>
	                            @foreach($plan as $plan)
	                                <option value="{{$plan->id}}">{{$plan->nombre}}</option>
	                            @endforeach
	                        </select>
	                    </div>
	                    
	                </div>
                </div-->

                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-success btn-gray" onclick="guardar_gasto(event);" id="btn_guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>{{trans('laboratorio.guardar')}}
                    </button>
                </div>

            </div>
    	</div>
    </form>

</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
	function goBack() {
        window.history.back();
    }

    $(document).ready(function(){

        $('.select2_cuentas').select2({
            tags: false
        });


    });


    function guardar_gasto(e){
        e.preventDefault();
        $('#btn_guardar').prop("disabled", true);
    	$.ajax({

	        type: 'post',
	        url: "{{route('gastosimportacion.store')}}",
	        headers: {
	            'X-CSRF-TOKEN': $('input[name=_token]').val()
	        },
	        datatype: 'json',
	        data: $('#form_gasto').serialize(),
	        success: function(data) {
	            console.log(data);
                if (data.respuesta == 'si') {
                    swal("Exito!", data.msj, "success");
                    setTimeout(function() {
                        location.href="{{route('gastosimportacion.index')}}";
                    }, 1000); 
                }else{
                    swal("Error!", data.msj, "error");
                    $('#btn_guardar').prop("disabled", false);
                }	          
	        },
	        error: function(data) {
	            console.log(data);
	            swal("Error!", data, "error");
	        }
	    });
    }
</script>

@endsection