@extends('contable.debito_bancario.base')
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
    th{
        text-align: center;
    }
    .icons{
        text-align: center;
        font-weight: bold;
    }
    .icons>i{
        font-size: 19px;
    }

    .fa-check-circle{
        color: green;
    }
    .fa-times{
        color: red;
    }
</style>

<section class="content" style="background-color: #ffffff;">

    <div class="box-header">
        <div class="col-md-12">
            <h3><b>DECLARACION TRIBUTARIA COMPRA-VENTAS RETENCIONES</b></h3>
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

                <div class="col-md-3">
                    <label for="">Tipo: </label>
                    <select name="tipo" id="tipo" class="form-control">
                        <option value="T">TODOS</option>
                        <option value="C">FACT. COMPRA</option>
                        <option value="V">FACT. VENTA</option>
                        <option value="RC">RET. CLIENTES</option>
                        <option value="RA">RET. ACREEDOR</option>
                        <!-- <option value="CR">COMPRA - RETENCION</option>
                        <option value="VR">VENTA - RETENCION</option> -->
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="">Estado: </label>
                    <select class="form-control" name="estado" id="estado">
                        <option value="T">Todos</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>                                                                                                                   
                </div>
            </div>
            <div class="col-md-12" style="text-align: end;margin-top: 10px;">
                <button type="submit" class="btn btn-success" formaction="{{route('compra.declaracionTributacion.descargarExcel')}}" target="print_popup">
                    <span class="fa fa-download"></span> Excel
                </button>
                <button type="button" class="btn btn-primary" onclick="buscar()"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
            </div>
        </form>
    </div>

    <div class="col-md-12 text-center">
        <h3 class="titulo"></h3> 
    </div>

    <div class="box-body">
        <div class="col-md-12" id="contenedor" style="text-align: center; overflow-x: scroll; overflow-y: scroll; max-height:100vh; font-size: 11px;">
            <!-- <h4>No se encontraron resultados</h4>
            <div class="container" style="display: flex; justify-content: center; width: 40%;">
                <table class="table">
                    <thead>
                        <tr>
                        <th style="width: 30%">Modulo</th>
                        <th style="width: 20%">Vista</th>
                        <th style="width: 20%">Excel</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>TODOS</td>
                            <td class="icons"><i class="fa fa-times" aria-hidden="true"></i></td>                    
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>                    
                        </tr>
                        <tr>
                            <td>FACT. COMPRA</td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>FACT. VENTA</td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>RET. CLIENTES</td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>RET. ACREEDOR</td>
                            <td class="icons"><i class="fa fa-times" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-times" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>COMPRA - RETENCION</td=>
                            <td class="icons"><i class="fa fa-times" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-times" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>VENTA - RETENCION</td=>
                            <td class="icons"><i class="fa fa-times" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-times" aria-hidden="true"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div> -->
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
        Swal.close();
        buscar();
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
                if (data.status != "success") {
                    $("#contenedor").empty();
                    $("#contenedor").append(`<h4>${data.msj}</h4>`);
                    $("#contenedor").append(tableInformativa());
                    return;
                }

                $("#contenedor").empty();
                $("#contenedor").append(data.table);


            },
            error: function(data) {

            }
        })
    }

    const tableInformativa = () => {
        return `<div class="container" style="display: flex; justify-content: center; width: 40%;">
                <table class="table">
                    <thead>
                        <tr>
                        <th style="width: 30%">Modulo</th>
                        <th style="width: 20%">Vista</th>
                        <th style="width: 20%">Excel</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>TODOS</td>
                            <td class="icons"><i class="fa fa-times" aria-hidden="true"></i></td>                    
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>                    
                        </tr>
                        <tr>
                            <td>FACT. COMPRA</td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>FACT. VENTA</td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>RET. CLIENTES</td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>RET. ACREEDOR</td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                            <td class="icons"><i class="fa fa-check-circle" aria-hidden="true"></i></td>
                        </tr>
                       
                    </tbody>
                </table>
            </div>`
    }
</script>



@endsection