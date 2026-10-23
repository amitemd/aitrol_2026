@extends('control_temperatura.control_nevera.base')
@section('action-content')
    @php
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
    @endphp
    <style type="text/css">
        th,
        td {
            text-align: center;
        }
        .buttontext  {
            overflow: hidden;
            white-space: nowrap;
            display: block;
            text-overflow: ellipsis;
        }
    </style>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <div class="col-md-12">
                    <div class="col-md-10">
                        <h4 style="text-align: left;">Empresa: <b>{{ $empresa->nombrecomercial }}</b></h4>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    @foreach ($lugar_control as $value)
                        <div class="col-md-10 col-md-offset-1" align="center">
                            <a style="width:70%; height: 60px;  font-size: 16px; text-align: center;font-weight:bold;margin-top:5px;"
                                onclick="viewModal({{ $value->id }})" class="btn btn-primary"> {{ $value->nombre }} </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h5 class="modal-title" id="exampleModalLabel">Eligia que Control de temperatura que va a
                                realizar</h5>
                        </div>
                        @if ($rolUsuario == 24 || $rolUsuario == 1 || $rolUsuario == 16 || $rolUsuario == 10 || $rolUsuario == 7 || $rolUsuario == 6)
                            <div class="modal-body" id="guardar_busc">
                                <form action="{{ route('eleccion_tipo_control') }}" method="post">
                                    <input type="hidden" id="id" name="id">
                                    <input type="hidden" id="tipo" name="tipo">
                                    <input type="hidden" id="empresa" name="empresa" value="{{ $empresa->id }}" />
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-warning btn-block"
                                                onclick="document.getElementById('tipo').value = 5">Procesamiento</button>
                                        </div>
                                        {{-- <div class="col-sm-4">
                                            <button type="submit" class="btn btn-success btn-block"
                                                onclick="document.getElementById('tipo').value = 6">Bodega</button>
                                        </div> --}}
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-success btn-block esconder"
                                                onclick="document.getElementById('tipo').value = 7">Toma de
                                                Muestra</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                        @endif
                        @if ($rolUsuario == 1 || $rolUsuario == 11 || $rolUsuario == 16 || $rolUsuario == 10 || $rolUsuario == 7 || $rolUsuario == 6)
                            <div class="modal-body" id="buscar">
                                <form action="{{ route('index_registro.see') }}" method="get">
                                    <input type="hidden" id="id" name="id">
                                    <input type="hidden" id="tipo" name="tipo">
                                    <input type="hidden" id="empresa" name="id_empresa" value="{{ $empresa->id }}" />
                                    <div class="row">
                                        <div class="col-sm-4 text-center">
                                            <button type="submit" class="btn btn-warning btn-block"
                                                onclick="$('#buscar #tipo').val(5)"><span>Reportes
                                                    Area de Procesamiento</span></button>
                                        </div>
                                        {{-- <div class="col-sm-4 text-center">
                                            <button type="submit" class="btn btn-success btn-block"
                                                onclick="$('#buscar #tipo').val(6)"><span>Reportes
                                                    de area - bodega</span></button>
                                        </div> --}}
                                        <div class="col-sm-4 text-center">
                                            <button type="submit" class="btn btn-success btn-block buttontext esconder"
                                                onclick="$('#buscar #tipo').val(7)">
                                                   <span>Reportes
                                                    Toma de Muestras</span></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
    </section>
    <script>
        const viewModal = (id) => {
            $('#buscar #id').val(id);
            if(id === 3){
                $(".esconder").hide();;
            }else{
                $(".esconder").show();
            }
            $("#guardar_busc #id").val(id);
            $('#exampleModal').modal('show');

        }
    </script>
@endsection
