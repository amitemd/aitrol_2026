<style type="text/css">
    p{
        font-size: 12px;
    }    
</style>

<div class="form-group col-md-6" >
	<div class="box box-primary box-solid" >
        <div class="box-header with-border" style="padding-top: 0;padding-bottom: 0px;">
            <div class="col-md-10"><b style="font-size: 16px;">SOLICITUDES</b></div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm" id="boton_plus" style="border: none;">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button> 
            </div>
        </div>
        <div class="box-body">
            <div>
                <p><b>Ultima Ingresada</b></p>
                <p><b>Contacto: </b>{{substr($solicitud->fecha_contacto,0,10)}}</p>
                <p><b>Nombre: </b></p>
                <p>{{$solicitud->apellido1}} {{$solicitud->apellido2}} {{$solicitud->nombre1}} {{$solicitud->nombre2}}</p>
                <p><b>Teléfono: </b> {{$solicitud->telefono1}}</p>
                <p><b>Mail: </b> {{$solicitud->mail}}</p>
                <p><b>Respuesta: </b> {{substr($solicitud->fecha_respuesta,0,10)}}</p>
                <p><b>Estado: </b> @if(!is_null($solicitud->bo_estado)){{$solicitud->bo_estado->descripcion}}@endif</p>    
            </div>
        </div>      
    </div>
</div>