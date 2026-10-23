@extends('contable.ventas.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">

<!-- Main content -->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Ventas</a></li>
            <li class="breadcrumb-item active">Registro de Factura de Ventas</li>

        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">Configuraciones Globales</h3>
            </div>
        </div>

        <!-- /.box-header -->
        @foreach($modulos as $value)

        <div class="box-header header_new">
            <div class="col-md-12">
                <h3 class="box-title">{{strtoupper($value->nombre)}}</h3>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="col-md-12">
                <div class="row">
                    @foreach($value->globales as $x)

                    <div class="col-md-12">
                        <span>{{$x->nombre}}</span>
                    </div>
                    @if($x->tipo==0)
                    <div class="card col-md-12">
                        <div class="col-md-6">
                            <label for="debe">Debe</label>
                            @php
                            $habilitar ="";
                            if($x->activo_debe == 0){
                                $habilitar="disabled";
                            }
                            //dd($x);
                            @endphp
                            
                            <select {{$habilitar}} class="form-control select2" name="debe[]" onchange="editDebe(this,1, {{$x->id}})">
                                <option value="">Seleccione...</option>
                                @foreach($plan_cuentas as $v)
                                <option @if($x->debe==$v->id) selected="" @endif value="{{$v->id}}">{{$v->plan}} | {{$v->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="haber">Haber</label>
                            @php
                            $habilitar ="";
                            if($x->activo_haber == 0){
                            $habilitar="disabled";
                            }
                            @endphp
                            
                            <select {{$habilitar}} class="form-control select2" name="haber[]" onchange="editDebe(this,2, {{$x->id}})">
                                <option value="">Seleccione...</option>
                                @foreach($plan_cuentas as $z)
                                <option @if($x->haber==$z->id) selected @endif value="{{$z->id}}">{{$z->plan}} | {{$z->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @endif
                    @endforeach
                </div>

            </div>
        </div>
        @endforeach


    </div>
</section>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.select2').select2({
            tags: false
        });
    });



    function editDebe(cuenta, tipo, id) {
        let cuentas = $(cuenta).val();

        //console.log(id)
        $.ajax({
            url: "{{route('diario.edit_cuentas')}}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'cuenta': cuentas,
                'id': id,
                'tipo': tipo
            },
            success: function(data) {
                console.log(data);
                if (data.respuesta == 'ok') {
                    alertas('success', 'Exito', 'Guardado Correctamente')
                } else {
                    alertas('error', 'Error', 'Ocurrio un error')
                }
            },
            error: function(data) {
                console.error(data.responseText);
            }
        });
    }

    function alertas(icon, title, text) {
        Swal.fire({
            icon: `${icon}`,
            title: `${title}`,
            text: `${text}`,
            showConfirmButton: false,
            timer: 1200

        })
    }
</script>
@endsection