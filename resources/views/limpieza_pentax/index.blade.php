@extends('limpieza_pentax.base')
@section('action-content')
@php $fechaHoy = date('Y-m-d'); @endphp
<style>
    .btn {
        font-size: 15px;
        font-weight: bold;
    }

    .salas:hover {
        background-color: #4192C2;
    }
</style>
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="editar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="foto" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 80%;">
        </div>
    </div>
</div>
<div id="fotito">
</div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="box-header">
                <form id="form_fecha" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group col-md-4 col-xs-6">
                        <label for="fecha" class="col-md-3 control-label">{{trans('limpieza_equipof.from')}}</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="fechaA" value="{{$fechaHoy}}" id="fechaA">
                        </div>
                    </div>
                    <div class="form-group col-md-4 col-xs-6">
                        <label for="fecha" class="col-md-3 control-label">{{trans('limpieza_equipof.to')}}</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="fechaD" value="{{$fechaHoy}}" id="fechaD">
                        </div>
                    </div>
                </form>

                <form method="POST" action="{{route('excel_limpiezaPentax')}}" id="formi">
                    {{ csrf_field() }}
                    <input type="hidden" id="tipoexcel" name="tipoexcel" >
                    <input type="hidden" name="fechaAntes" id="fechaAntes">
                    <input type="hidden" name="fechaDespues" id="fechaDespues">
                    <input type="hidden" name="salaId" id="salaId">
                    <div class="form-group col-md-4 col-xs-6">
                        <div class="col-md-6">
                            <button class="btn btn-success btn-md" onclick="getValue(1)" type="button" id="excel" name="excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i>EndoscopyNet</button>
                        </div>
                        <!---<div class="col-md-6">
                            <button class="btn btn-success btn-md" onclick="getValue(2)" type="button" id="excel" name="excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel CRM</button>
                        </div> -->


                    </div>
                </form>


            </div>


            <div class="box-body">
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

                    <table id="example2" class="table table-bordered table-hover dataTable">
                        <tbody>
                            @foreach($sala as $sala)
                            <div class="col-md-3" style="padding: 5px;">
                                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                                    <a id="boton_salas{{$sala->id}}" class="btn btn-primary" style="width: 100%; height: 60px; line-height: 40px; font-size: 20px; text-align: center" onClick="componentDidMount({{$sala->id}});changeInput({{$sala->id}})">{{$sala->nombre_sala}}
                                    </a>

                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="box">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="demo">
                                    <i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="display: block;">
                            <div class="col-md-12" id="index_form"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    const changeInput = (id) => {


    $( "#salaId").val(id);

    }
    $( document ).ready(function() {
      $( "#salaId").val(10);
    });

    function getValue(tipo) {
        $("#fechaAntes").val($("#fechaA").val());
        $("#fechaDespues").val($("#fechaD").val());
        $("#tipoexcel").val(tipo);
        document.getElementById('formi').submit();
    }


    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'responsive': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'sInfoEmpty': false,
            'sInfoFiltered': false,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
    async function componentDidMount(id_sala) {
        var result = await this.index_limpieza(id_sala);
        document.getElementById("index_form").innerHTML = await result.text();
    }

    async function index_limpieza(id_sala) {
        var fechaBuscara = document.getElementById("fechaA").value;
        var fechaBuscarD = document.getElementById("fechaD").value;
        var data = {
            fechaa: fechaBuscara,
            fechad: fechaBuscarD,
            sala_id: id_sala,
        };
        const resultado = await fetch("{{asset('limpieza/pentax/buscar/sala')}}", {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        });
        return resultado;

    }

    function modalfoto(id, tipo) {
        console.log(this);
        $.ajax({
            type: 'get',
            url: "{{route('modal_foto_pentax_limpieza')}}",
            data: {
                'id': id,
                'tipo': tipo,
            },
            datatype: 'html',
            success: function(data) {

                $("#fotito").html(data);
                $("#fotito").children().modal();

            },
            error: function(data) {
                // console.log(data);
            }

        });
    }
</script>


@endsection
