@extends('archivo_plano.factura_mesplano.base')
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
<div class="modal fade" id="codigo_preparacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-11">
                    <h3 class="box-title">Subir nueva Factura </h3>
                </div>
                <div class="col-sm-11 text-right">
                    <button onclick="goBack()" class="btn btn-default btn-gray">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('mcaja.regresar')}}
                    </button>
                </div>
            </div>
        </div>
        <br>

        <form id="frm" method="post" action="{{route('nuevoArchivoPlano.store_archivo')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-md-2 col-xs-2">
                            <label class="texto" for="mes_plano">Mes Plano: </label>
                        </div>
                        <div class="form-group col-md-2 col-xs-6">
                            <input type="text" name="mes_plano" class="form-control" id="mes_plano" required>
                        </div>
                        <div class="form-group col-md-2 col-xs-2">
                            <label class="texto" for="mes_plano">Empresa: </label>
                        </div>
                        <div class="form-group col-md-2 col-xs-6">
                            <select class="form-control" id="id_empresa" name="id_empresa" required>
                            <option value="">Seleccione...</option>
                            @foreach ($empresas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombrecomercial }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="from-group col-md-2 col-xs-2">
                    <label class="texto" for="archivo">Agregar archivo</label>
                </div>
                <div class="from-group col-md-8 container-5" style="padding-left:15px;">
                    <input type="file" name="archivo" id="archivo" class="archivo form-control" required accept=".pdf, .doc, .docx, .xls, .xlsx" value="old('archivo') }}">
                </div>
                <div class="container">
                    <div class="form-group col-md-5 ">
                        <button id="boton_guardar" name="boton_guardar" type="button" onclick="guardar()" class="btn btn-primary btn-sm">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
</section>

</html>
@endsection

<script type="text/javascript">
    function goBack() {
        window.history.back();
    }

    function guardar() {
        document.getElementById('boton_guardar').disabled = true;
        var formData = new FormData();
        formData.append('archivo', $('#archivo')[0].files[0]); // Añade el archivo al FormData
        formData.append('mes_plano', $('#mes_plano').val()); // Añade otros campos del formulario según su ID
        formData.append('id_empresa', $('#id_empresa').val()); // Añade otros campos del formulario según su ID

        var mes_plano = $('#mes_plano').val();
        var id_empresa = $('#id_empresa').val();
        var archivo = $('#archivo').val();
        var msj = "";

        if(mes_plano==""){
            msj = msj + "Ingrese el mes plano <br>";
        }

        if(id_empresa==""){
            msj = msj + "Seleccione una empresa <br>";
        }

        if(archivo==""){
            msj = msj + "Seleccione un archivo <br>";
        }
        
        if (msj == "") {
            $.ajax({
                type: 'post',
                url: "{{route('nuevoArchivoPlano.store_archivo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                processData: false, // Evita que jQuery procese los datos
                contentType: false, // Evita que jQuery establezca el tipo de contenido
                data: formData,
                success: function(data) {
                    if (data.status == "success") {
                        Swal.fire("Success!", data.msj, "success");
                        setTimeout(function() {
                            window.location.href = "{{ route('nuevoArchivoPlano.index_archivo_factura')}}";
                        }, 1500)
                    } else {
                        Swal.fire("Error!", data.msj, "error");
                        document.getElementById('boton_guardar').disabled = false;
                    }
                },
                error: function(data) {
                    document.getElementById('boton_guardar').disabled = false;
                }
            });
        } else {
            alertas("error", "Porfavor llenes los siguientes campos", msj);
            document.getElementById('boton_guardar').disabled = false;
        }
    }
</script>