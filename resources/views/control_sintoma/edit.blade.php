@extends('control_sintoma.base')
@section('action-content')
@php $flag = false; @endphp
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title rojo">Edición de Formulario</h3>
            <div class="box-tools pull-right">
                <a class="label label-primary" onclick="history.back()">Retroceder</a>
            </div>
        </div>
        <div class="box-body">
            <form id="formulario">
                <input type="hidden" name="id" value="{{$user->id}}">
                <div class="col-md-12">
                    <div class="col-md-2">
                        <label for="fecha" class="form-label">Fecha</label>
                    </div>
                    <div class="col-md-3">

                        <input type="date" class="form-control" value="{{$user->fecha_registro}}" name="fecha" id="fecha" autocomplete="off">
                    </div>
                    <div class="col-md-2">

                    </div>
                    <div class="col-md-2">
                        <label for="contacto" class="form-label">Ha tenido contacto estrecho con covid</label>
                    </div>
                    <div class="col-md-3">
                        <input type="checkbox" class="form-check-input" name="contacto" id="contacto" @if($user->contacto_paciente == 1) checked @endif>
                    </div>
                </div>

                <div class="col-md-12 space">
                    <div class="col-md-2">
                        <label for="sexo" class="form-label">Sexo</label>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="sexo" id="sexo">
                            <option value="">Seleccione</option>
                            <option @if($user->sexo == '1') selected @endif value="1">Mujer</option>
                            <option @if($user->sexo == '3') selected @endif value="3">Hombre</option>
                            <option @if($user->sexo == '4') selected @endif value="4">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-2">

                        <label for="general" class="form-label">Tos en general</label>
                    </div>
                    <div class="col-md-3">
                        <input type="checkbox" class="form-check-input" name="general" id="general" @if($user->tos == 1) checked @endif>
                    </div>

                </div>




                <div class="col-md-12 space">
                    <div class="col-md-2">
                        <label for="nombresApellido" class="form-label">Nombres y Apellidos</label>
                    </div>
                    <div class="col-md-3">
                        <select name="usuarios" class="js-data-example-ajax form-control">
                            @if($user->cedula)
                            <option value="{{$user->cedula}}">{{$user->usuario->nombre1}} {{$user->usuario->apellido1}}</option>
                            @else

                            @endif
                        </select>
                    </div>

                    <div class="col-md-2">


                    </div>
                    <div class="col-md-2">
                        <label for="diffrespira" class="form-label">Dificultad Respiratoria</label>
                    </div>
                    <div class="col-md-3">
                        <input type="checkbox" class="form-check-input" name="diffrespira" id="diffrespira" @if($user->dificultad_respiratoria == 1) checked @endif>
                    </div>




                </div>


                <div class="col-md-12 space">
                    <div class="col-md-2">
                        <label for="edad" class="form-label">Edad</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" value="{{$user->edad}}" class="form-control" name="edad" id="edad">
                    </div>

                </div>

                <div class="col-md-12 space">
                    <div class="col-md-2">
                        <label for="pais" class="form-label">Ciuad o pais que visito recientemente</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" value="{{$user->ciudad_pais}}" class="form-control" name="pais" id="pais" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-12 space">
                    <div class="col-md-2">
                        <label for="temperatura" class="form-label">Temperatura</label>
                    </div>
                    <div class="col-md-3">
                        <input type="number" value="{{$user->temperatura}}" class="form-control" name="temperatura" id="temperatura" autocomplete="off">
                    </div>


                </div>

                <div class="col-md-12 space">
                    <div class="col-md-2">
                        <label for="dosi" class="form-label">Ultima dose de la vacuna</label>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" value="{{$user->ultima_dosis}}" name="dosi" id="dosi">
                    </div>
                </div>


                <div class="col-md-12 space">
                    <label for="sintomas" class="form-label">Indique si ha tenido o tiene algun otros sintoma compatible al covid como: sin gusto u olfato ,dolor de cabeza,dearrea,malestar general,escalofrios</label>
                    <input type="text" class="form-control" name="sintomas" id="sintomas" autocomplete="off" value="{{$user->sintoma_compatible}}">
                </div>

             


            </form>
        </div>

        <div class="box-footer text-center">
            @if($flag) <i class="fa fa-refresh fa-spin"></i> @else<button type="submit" onclick="edit()" class="btn btn-primary">Editar</button>@endif
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
        } else if (forms['pais'].value == '') {
            alert('El pais no pude estar vacio');
            return false;
        } else if (forms['dosi'].value == '') {
            alert('Ingrese la fecha');
            return false;
        }

        return true
    }




    function edit() {

        if (isValidate()) {
            <?php $flag = true ?>
            $.ajax({
                url: "{{route('editar_control')}}",
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