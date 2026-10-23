@extends('control_temperatura.control_nevera.base')
@section('action-content')
    @php
        $hora = date('H:i:s');
        $user = Auth::user();
        $id_usuario = $user->id;
        $nomina = Sis_medico\Ct_Nomina::where('id_user', $id_usuario)
            ->where('estado', 1)
            ->first();
        $datos = $user->nombre1;
        $datos1 = $user->nombre2;
        $datos2 = $user->apellido1;
        $datos3 = $user->apellido2;
        
    @endphp

    <section class="content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">{{ trans('control_temperatura.controlt') }}</a></li>
                <li class="breadcrumb-item"><a href="../ambiente">{{ $controlugar->nombre }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ trans('control_temperatura.crear') }}</li>
            </ol>
        </nav>
        <form id="formulario" class="form-vertical" role="form">
            {{ csrf_field() }}
            <input type="hidden" name="id_area" id="area" value="{{ $controlugar->id }}">
            <input type="hidden" name="id_bo_ne" id="id_bo_ne" value="{{$id_n_b}}">
            <input type="hidden" id="lugar" value={{ $empresa->id }}>
            <div class="box">
                <div class="row box-header">
                    <div class="col-sm-9">
                        <h5><b>{{ trans('control_temperatura.lugar') }}: {{ $controlugar->nombre }}
                                ({{ $empresa->razonsocial }})</b></h5>
                    </div>

                    <div class="col-sm-1">
                        <button type="button" onclick="goBack()" class="btn btn-danger">
                            <i class="glyphicon glyphicon-arrow-left"
                                aria-hidden="true"></i>&nbsp;&nbsp;{{ trans('control_temperatura.regresar') }}
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    {{-- INICIO --}}
                    <div class="row">
                        @if ($empresa->id === '0993075000001')
                            <div class="col-sm-1 my-1">
                                <label for="nevera">Lugar</label>
                            </div>
                            <div class="col-sm-3 my-1">
                                <select name="nevera" id="nevera" class="form-control">
                                    <option value="">Seleccione</option>
                                    @foreach ($bodegas_nevera as $val)
                                        <option value="{{ $val->id }}">{{ $val->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-sm-1 my-1">
                            <label for="temp_primerhorario">{{ trans('control_temperatura.temperatura') }} °C</label>
                        </div>
                        <div class="col-sm-3 my-1">
                            <select name="temp_primerhorario" id="temp_primerhorario" class="form-control" required>
                                <option value="">Seleccione</option>
                                @foreach ($temperatura_neveras as $value)
                                    <option value="{{ $value->id }}">{{ $value->temperatura }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-1 my-1">
                            <label for="responsable">{{ trans('control_temperatura.responsable') }}</label>
                        </div>
                        <div class="col-sm-3 my-1">
                            <input type="text"
                                value="{{ $datos }} {{ $datos1 }} {{ $datos2 }} {{ $datos3 }}"
                                class="form-control" id="responsable" name="responsable" readonly>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-sm-1 my-1">
                            <label for="observaciones"
                                id="titulo">{{ trans('control_temperatura.observacion') }}</label>
                        </div>
                        <div class="col-sm-3 my-1">
                            <input id="observaciones" type="text" class="form-control" name="observaciones"
                                style="text-transform:uppercase;">
                        </div>
                        <div class="col-sm-1 my-1">
                            <label for="empresa">Empresa</label>
                        </div>
                        <div class="col-sm-3 my-1">
                            <input type="hidden" value="{{ $empresa->id }}" name="id_empresa" id="id_empresa">
                            <input type="text" value="{{ $empresa->razonsocial }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-row text-center" style="margin-top:5px;">
                        <button type="button" onclick="guardarForm()" class="btn btn-primary" id="guardar">
                            {{ trans('tecnicof.save') }}
                        </button>
                    </div>
                    {{-- FIN --}}
                </div>
            </div>
        </form>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">
        function guardarForm() {
            
            $primer_horario = $('#primer_horario').val();
            $temp_primerhorario = $("#temp_primerhorario").val();
         
            if ($primer_horario == "" || $temp_primerhorario == "") {
                swal("Error!", "Campos Vacios", "error");
            } else {
                document.getElementById("guardar").disabled = true;
                $.ajax({
                    url: "{{ route('control_temperatura.store') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: $('#formulario').serialize(),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {

                        let id = encodeURIComponent(data.registro.id);
                        let url = "{{ route('control_temperatura.index') }}?id=" + id;

                        if (data.status) {
                            setTimeout(function() {
                                swal("Guardado!", "Correcto", "success");
                                window.location = url;
                            }, 1000);
                        }
                        document.getElementById("guardar").disabled = false;
                    },
                    error: function(xhr, status) {
                        alert(`{{ trans('limpieza_equipof.problem') }}`);
                        document.getElementById("guardar").disabled = false;
                        //console.log(xhr);
                    },
                });
            }

        }

        const port = () => {
            document.getElementById("primer_horario").disabled = false;
            document.getElementById("temp_primerhorario").disabled = false;
            document.getElementById("observaciones").disabled = false;
        }

        function goBack() {
            let lugar = document.getElementById("lugar").value;
            if (lugar === '0993075000001') {
                window.location.replace("{{ route('control_temperatura.index_labs') }}");
            } else {
            window.location.replace("{{ route('control_temperatura.index_contemp') }}");
            }
        }
    </script>
@endsection
