@extends('contable.rol_anticipo_quincena.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item active" aria-current="page">Anticipo 1era Quincena
            </li>
        </ol>
    </nav>
    <div class="box">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">ANTICIPOS 1ERA QUINCENA EMPLEADOS</label>
            </div>
        </div>
        @php
            $meses = array('Todos', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        @endphp
        <div class="box-body">
            <form id="form_buscador" method="POST" action="{{ route('nominaquincena.busca_quincena')}}">
                {{ csrf_field() }}
                <div class="form-group col-md-3">
                    <label for="anio" class="texto col-md-2 control-label">Año:</label>
                    <div class="col-md-9">
                        @php $x = date('Y'); @endphp
                        <select id="anio" name="anio" class="form-control">
                            @for($i=2021;$i<=$x;$i++) <option @if($anio==$i) selected @endif value='{{$i}}'>{{$i}}</option>;
                                @endfor

                        </select>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="mes" class="texto col-md-2 control-label">Mes:</label>
                    <div class="col-md-9">
                        
                        <select id="mes" name="mes" class="form-control">
                            @foreach($meses as $key => $imes) 
                                <option @if($mes == $key) selected @endif value='{{$key}}'>{{$imes}}</option>;
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
                </div>
                <div class="form-group col-md-2">
                    <a class="btn btn-info" onclick="crea_anticipo();">Crear</a>
                </div>
                <div class="form-group col-md-12">
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>MES</th>
                                        <th>N° EMPLEADOS</th>
                                        <th>TOTAL</th>
                                        <th>ASIENTO</th>
                                        <th>CREADO POR:</th>
                                        <th>FECHA DE CREACION:</th>
                                        <th>ACCIÓN</th>
                                    </tr>

                                </thead>
                                <tbody>
                                    
                                    @foreach ($cab_anticipo as $cab)
                                    @php
                                    $ms = intval($cab->mes);

                                    $detalle = Sis_medico\Ct_Rh_Valor_Anticipos::where('id_valida',$cab->id)->where('id_empresa',$cab->id_empresa)->where('estado','1')->get();

                                    $cont_det = count($detalle);
                                    $suma_det = 0;$sum_ok = 0;$arr_asientos = [];
                                    foreach ($detalle as $det){
                                        if($det->id_asiento_cabecera != null){
                                            $sum_ok += $det->valor_anticipo;
                                            $arr_asientos[$det->id_asiento_cabecera] = 1;
                                            
                                        }
                                        $suma_det += $det->valor_anticipo;
                                    }
                                    
                                    @endphp
                                    <tr>
                                        <td>{{$meses[$ms]}}</td>
                                        <td>{{$cont_det}}</td>
                                        <td>{{$sum_ok}}/{{$suma_det}}</td>
                                        <td>@if(!is_null($cab->asiento)) {{$cab->asiento}} @else 
                                            @foreach($arr_asientos as $key => $data)
                                            #{{$key}}, 
                                            @endforeach
                                            @endif</td>
                                        @php $usuario = \Sis_medico\User::where('id', $cab->id_usuariocrea)->first(); @endphp
                                        <td>{{$usuario->nombre1}} {{$usuario->apellido1}} {{$usuario->apellido2}}</td>
                                        <td>{{$cab->created_at}}</td>
                                        <td>
                                            @if(!is_null($cab->asiento))
                                            <a class="btn btn-info btn-sm" target="_blank" href="{{route('descarga_pdf_anticipo.quincena2',['id' => $cab->id])}}"><i class="fa fa-file-pdf-o">&nbsp;&nbsp;PDF</i></a>
                                            <a class="btn btn-success" href="{{route('descarga_excel_anticipo.quincena2',['id' => $cab->id])}}"> <i class="fa fa-file-excel-o">&nbsp;&nbsp;Excel</i> </a>
                                            <a class="btn btn-primary btn-sm" href="{{route('librodiario.edit',['id'=>$cab->asiento])}}" target="_blank">Asiento</a>
                                            @else
                                                @if($arr_asientos != [])
                                                    <a class="btn btn-info btn-sm" target="_blank" href="{{route('descarga_pdf_anticipo.quincena2',['id' => $cab->id])}}"><i class="fa fa-file-pdf-o">&nbsp;&nbsp;PDF</i></a>
                                                    <a class="btn btn-success" href="{{route('descarga_excel_anticipo.quincena2',['id' => $cab->id])}}"> <i class="fa fa-file-excel-o">&nbsp;&nbsp;Excel</i> </a>
                                                @endif
                                                @foreach($arr_asientos as $key => $data)
                                                    <a class="btn btn-primary btn-sm" href="{{route('librodiario.edit',['id'=>$key])}}" target="_blank">Asiento {{$key}}</a>
                                                @endforeach
                                            @endif
                                            <a class="btn btn-warning btn-sm" target="_blank" href="{{route('nominaquincena.edit_anticipo',['id' => $cab->id])}}">Editar</a>
                                            <a class="btn btn-success" href="{{route('nominaotrosanticipos.new_excel_anticipo_quincena',['id' => $cab->id])}}"> <i class="fa fa-file-excel-o">&nbsp;&nbsp;Detalles Excel</i> </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function crea_anticipo() {

        var msn = '';
        var mes = $('#mes').val();
        if(mes == ''){
            msn = 'Seleccione el mes para crear';
        }

        if(mes < 1){
            msn = 'Seleccione el mes para crear';
        }
        console.log(mes, msn);
        if(msn == ''){
            var confirmar = confirm("Desea Crear Anticipo");
            if(confirmar){
                $.ajax({
                    type: 'post',
                    url: "{{ route('nominaquincena.crea_anticipo') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    datatype: 'json',
                    data: $("#form_buscador").serialize(),
                    success: function(data) {
                        console.log(data);
                        if (data.respuesta == 'success') {
                            window.location.href = `{{url('contable/nomina/edit_anticipo/${data.id_valida}')}}`;
                        } else {
                            alertas(data.respuesta, "Error...", data.msj)
                        }
                    },
                    error: function(data) {

                    }
                });
            }    
        }else{
            alert(msn);
        }    

    }
</script>

@endsection