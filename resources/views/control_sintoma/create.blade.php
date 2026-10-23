@extends('control_sintoma.base')
@section('action-content')
@php $flag = false; @endphp
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title rojo">Creación de Formulario</h3>
            <div class="box-tools pull-right">
                <a class="label label-primary" onclick="history.back()">Retroceder</a>
            </div>
        </div>
        <div class="box-body">
            <div >
                <form id="formulario">
                    <div class="col-md-12">
                        <div class="col-md-2">
                            <label for="fecha" class="form-label">Fecha</label>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" name="fecha" id="fecha" autocomplete="off">
                        </div>
                        <div class="col-md-2">

                        </div>
                        <div class="col-md-2">
                            <label for="general" class="form-label">Tos en general</label>
                        </div>
                        <div class="col-md-3">
                            <input type="checkbox" class="form-check-input" name="general" id="general">
                        </div>
                    </div>
                    <div class="col-md-12 space">
                        <div class="col-md-2">
                            <label for="nombresApellido" class="form-label">Nombres y Apellidos</label>
                        </div>
                        <div class="col-md-3">
                            <select name="usuarios" class="js-data-example-ajax form-control"></select>
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-2">
                            <label for="diffrespira" class="form-label">Dificultad Respiratoria</label>
                        </div>
                        <div class="col-md-3">
                            <input type="checkbox" class="form-check-input" name="diffrespira" id="diffrespira">
                        </div>
                    </div>

                    <div class="col-md-12 space">
                        <div class="col-md-2">
                            <label for="sexo" class="form-label">Sexo</label>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="sexo" id="sexo">
                                <option value="">Seleccione</option>
                                <option value="1">Mujer</option>
                                <option value="3">Hombre</option>
                                <option value="4">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-2">

                        </div>
                        <div class="col-md-2">
                            <label for="contacto" class="form-label">Ha tenido contacto estrecho con covid</label>
                        </div>
                        <div class="col-md-3">
                            <input type="checkbox" class="form-check-input" name="contacto" id="contacto">
                        </div>
                    </div>
                    <div class="col-md-12 space">
                        <div class="col-md-2">
                            <label for="edad" class="form-label">Edad</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="edad" id="edad">
                        </div>
                    </div>
                    <div class="col-md-12 space">
                        <div class="col-md-2">
                            <label for="pais" class="form-label">Ciuad o pais que visito recientemente</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="pais" id="pais" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-12 space">
                        <div class="col-md-2">
                            <label for="temperatura" class="form-label">Temperatura</label>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="temperatura" id="temperatura" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-12 space">
                        <div class="col-md-2">
                            <label for="dosi" class="form-label">Ultima dose de la vacuna</label>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" name="dosi" id="dosi">
                        </div>
                    </div>
                    <div class="col-md-12 space">
                        <label for="sintomas" class="form-label">Indique si ha tenido o tiene algun otros sintoma compatible al covid como: sin gusto u olfato ,dolor de cabeza,dearrea,malestar general,escalofrios</label>
                        <input type="text" class="form-control" name="sintomas" id="sintomas" autocomplete="off">
                    </div>
                </form>
            </div>

            <div class="box-footer text-center">
                @if($flag) <i class="fa fa-refresh fa-spin"></i> @else<button style="margin-top: 10px;" type="submit" onclick="guardar()" class="btn btn-primary">Guardar</button>@endif
            </div>
        </div>
    </div>

</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<link href="{{url('css/stylescontrolsintoma.css')}}" rel="stylesheet" type="text/css">
<script type="text/javascript">
    function isValidate() {
        let forms = document.forms["formulario"];
        console.log(forms['general'].value);
        if (forms['fecha'].value == '') {
            alert('La fecha no pude ser vacia');
            return false;
        } else if (forms['usuarios'].value == '') {
            alert('Los nombres y apellidos no pueden ser vacios');
            return false;
        } else if (forms['pais'].value == '') {
            alert('El pais no pude estar vacio');
            return false;
        } else if (forms['dosi'].value == '') {
            alert('Ingrese la fecha');
            return false;
        }

        return true
    }

    function guardar() {

        if (isValidate()) {
            <?php $flag = true ?>
            $.ajax({
                url: "{{route('save_control')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'formulario': $("#formulario").serialize(),
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    <?php $flag = false ?>
                    if (data == true) {
                        Swal.fire({
                            position: 'top-center',
                            icon: 'success',
                            title: 'Guardado con exito',
                            showConfirmButton: false,
                            timer: 1500
                        });

                        window.location = "{{route('index_control')}}";
                    } else {
                        Swal.fire({
                            position: 'top-center',
                            icon: 'error',
                            title: data,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(xhr, status) {
                    <?php $flag = false ?>
                    alert('Existió un problema');
                },
            });

        }
    }

    $(document).ready(function() {
        var studentSelect = $('.js-data-example-ajax');
        $('.js-data-example-ajax').select2({
            tags: true,
            tokenSeparators: [','],
            minimumInputLength: 4,
            ajax: {
                url: "{{route('trabajo_campo_usuarios')}}",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    }
                },
                processResults: function(data, page) {

                    return {
                        results: $.map(data, function(item) {
                            document.getElementById("edad").value = item.edad;
                            return {
                                text: item.nombreappe,
                                id: item.id
                            }

                            var option = new Option(item.nombreappe, item.id, false, false);
                            studentSelect.append(option).trigger('change');
                        })
                    };
                },
            },
        });
    });
</script>

@endsection