@php
$date = date("Y-m-d");


@endphp
<style>
    .alto {
        margin-top: 5px;
    }
</style>

<div class="modal-content modal-lg modal-dialog">
    <div class="modal-header" style="background: #3c8dbc;">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" type="button" class="close" id="boton" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">FORMULARIO</h3>
    </div>
    <form id="formulario">
        {{csrf_field()}}
        <input type="hidden" name="camilla_id" id="camilla" value="{{$camilla->id}}">
        <input type="hidden" name="hospital_id" id="hospital" value="{{$hospi->id}}">
        <div class="form-group col-sm-4 alto">
            <label for="fecha" class="col-md-3 texto">Desde:</label>
            <div class="col-md-9">
                <input id="fecha1" name="desde" type="date" class="form-control" value="{{$date}}" onchange="tablita()">
            </div>
        </div>
        <div class="form-group col-sm-4 alto">
            <label for="fecha" class="col-md-3 texto">Hasta:</label>
            <div class="col-md-9">
                <input id="fecha2" name="hasta" type="date" class="form-control" value="{{$date}}" onchange="tablita()">
            </div>
        </div>
        <div style="margin-top: 5px;" class="col-md-12">
            <div class="form-row" id="tablita">

            </div>
        </div>
        <div style="margin-top: 2px;" class="col-md-12">
            <div class="form-row">
                <div class="form-group col-md-4">
                </div>
            </div>
        </div>
    </form>

    <div class="modal-footer">
        <!--
        <button type="submit" formtarget="_blank" class="btn btn-primary" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>Guardar</button>

-->
    </div>
    <script type="text/javascript">
       
        function nombre() {
            var select = $("#sala_id").val();
            $.ajax({
                url: "{{route('riesgo.verificar')}}",
                data: {
                    'term': select
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    //console.log(data);
                    $("#paciente").empty();
                    $("#paciente").append('<option value="">Seleccione...</option>');
                    for (var i = 0; i < data.length; i++) {
                        //console.log(data[i]);
                        $("#paciente").append('<option value=' + data[i].id + '>' + data[i].paciente + '</option>');
                    }
                },
                error: function(xhr, status) {
                    alert('Disculpe, existió un problema');
                    //console.log(xhr);
                },

            });
        }

        function calc_edad() {
            var id_paciente = $("#paciente").val();
            var camilla = $("#camilla").val();
            var hospital = $("#hospital").val();
            $.ajax({
                url: "{{route('riesgo.calc_edad')}}",
                data: {
                    'term': id_paciente
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data < 12) {
                        //$('input[name="formulario"]').val("Formulario Cargado");
                        var url = '{{url("gestion/camilla/form_menor/")}}' + "/" + id_paciente + "/" + camilla;
                        window.location = url;

                    } else {
                        //$('input[name="formulario"]').val("Formulario Cargado");
                        var url = '{{url("gestion/camilla/form_mayor/")}}' + "/" + id_paciente + "/" + camilla;
                        window.location = url;
                    }

                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }
        $(document).ready(function() {
            tablita();
        });

        function tablita() {
            $.ajax({
                url: "{{route('riesgo.buscar_estado')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'html',
                success: function(datahtml, data) {
                    console.log(data);
                    $("#tablita").html(datahtml);

                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }

        function cerrar() {
            location.href = "{{ route('camilla.index') }}";
        }

        jQuery(document).ready(function() {
            jQuery('#boton').on('hidden.bs.modal', function(e) {
                jQuery(this).removeData('bs.modal');
                jQuery(this).find('.modal-content').empty();
            })

        })
    </script>