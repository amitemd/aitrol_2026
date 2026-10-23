@extends('contable.informe_varios.base')
@section('action-content')

<style>
    .contenedor-body{
        box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
        -webkit-box-shadow: 0px 0px 5px 0px rgb(0 0 0 / 35%);
        -moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.35);
        padding: 18px 0px;
        border-radius: 7px;
        margin-bottom: 12px;
    }
    .titulo{
        background: rgba(43, 114, 165, 0.3);
        margin: 0px;
        padding: 10px;
        border-radius: 10px;
    }
</style>

<section class="content" style="background-color: #ffffff;">

    <div class="box-header">
        <div class="col-md-12">
            <h3><b>COSTO VS VENTA, UTILIDAD</b></h3>
        </div>
    </div>



    <div class="box-body dobra">
        <form action="" name="formulario" id="formulario">
            <div class="col-md-12">
                <div class="col-md-3">
                    <label for="">Fecha Desde: </label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{date('Y-m-d')}}">
                </div>

                <div class="col-md-3">
                    <label for="">Fecha Hasta: </label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{date('Y-m-d')}}">
                </div>

                {{--<div class="col-md-3">
                    <label for="">Estado: </label>
                    <select class="form-control" name="estado" id="estado">
                        <option value="T">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>--}}
            </div>
            <div class="col-md-12" style="text-align: end;margin-top: 10px;">
                <button type="submit" class="btn btn-success" formaction="" target="print_popup">
                    <span class="fa fa-download"></span> Excel
                </button>
                <button type="button" class="btn btn-primary" onclick="buscar()"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
            </div>
        </form>
    </div>

    <div class="col-md-12 text-center"  >
        <h3 class="titulo"></h3> 
    </div>

    <div class="box-body">
        <div class="col-md-12" id="contenedor" style="text-align: center; overflow-x: scroll; overflow-y: scroll; max-height:100vh; font-size: 11px;">
            <h4>No se encontraron resultados</h4>
        </div>
    </div>


</section>

<script>
    Swal.fire({
        title: 'Cargando por favor espere',
        width: 600,
        padding: '3em',
        color: '#716add',
        background: '#fff url(/images/trees.png)',
        backdrop: `
        rgba(177, 200, 227,0.8)
        url("/images/nyan-cat.gif")
        left top
        no-repeat`,
        showDenyButton: false,
        showCancelButton: false,
    })
    window.onload = function() {
        Swal.close()
    }


    const buscar = () => {
        let fecha_desde = document.getElementById("fecha_desde").value;
        let fecha_hasta = document.getElementById("fecha_hasta").value;

        let texto = document.querySelector(".titulo") 
        texto.innerHTML = `<b>Desde ${fecha_desde} - Hasta ${fecha_hasta}</b>`
        $.ajax({
            url: `{{route("compra.declaracionTributacion.busqueda")}}`,
            type: "get",
            dataType: "json",
            beforeSend: function() {
                Swal.fire({
                    text: "Cargando...",
                    imageUrl: 'https://c.tenor.com/XK37GfbV0g8AAAAi/loading-cargando.gif',
                    imageWidth: 200,
                    imageHeight: 200,
                    imageAlt: 'Custom image',
                })
            },
            data: $("#formulario").serialize(),
            success: function(data) {
                Swal.close();
                console.log(data);
                if (data.status != "success") {
                    $("#contenedor").empty();
                    $("#contenedor").append(`<h4>${data.msj}</h4>`);
                    return;
                }

                $("#contenedor").empty();
                $("#contenedor").append(data.table);

            },
            error: function(data) {

            }
        })
    }
</script>



@endsection