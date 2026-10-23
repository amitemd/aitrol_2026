@extends('hc_admision.nota_de_entrega.base')

@section('action-content')


<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">

<style type="text/css">
    .icheckbox_flat-orange.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    td {
        padding: 3px !important;

    }

    div.formgroup.col-md-4 {
        margin-bottom: 0px !important;
    }
</style>
<style type="text/css">
    .ro {
        color: black;
        font-weight: bolder;
    }

    .ro_2 {
        color: black;
    }

    .radio_origen_2.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    .radio_origen_3.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }

    .box-title,
    .form-group,
    .box {
        margin: 0;
    }

    h3 {
        margin: 0;
    }
</style>

<div class="modal fade" id="Subir" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog " role="document" >
    <div class="modal-content"  id="imprimir3">
    </div>
  </div>
</div>
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}

                <!-- <div class="form-group col-md-2" style="padding-left: 0px;padding-right: 0px;">
                    <label for="id" class="col-md-2 control-label"> {{trans('contableM.id')}} </label>
                    <div class="col-md-9">
                        <div class="input-group">
                        <input value="@if($id!=''){{$id}}@endif" type="text" class="form-control input-sm" name="id" id="id" placeholder="{{trans('econsultam.ingreseid')}}" >
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('id').value = ''; buscar();"></i>
                            </div>
                            </div>
                    </div>
                </div> -->
                <div class="row" style="padding-bottom: 20px;">
                <div class="form-group col-md-3 col-xs-5">
                    <label for="fecha" class="col-md-3 control-label">{{trans('contableM.Desde')}}</label>
                    <div class="col-md-9">
                    <div class="input-group date">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="date" value="{{$fecha}}" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                    </div>
                    </div>
                </div>


                <div class="form-group col-md-3 col-xs-5">
                    <label for="fecha_hasta" class="col-md-3 control-label">{{trans('contableM.Hasta')}}</label>
                    <div class="col-md-9">
                    <div class="input-group date">
                        <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                        </div>
                        <input type="date" value="{{$fecha_hasta}}" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                    </div>
                    </div>
                </div>
                
                    <div class="form-group col-md-3 col-xs-5">
                        <label for="secuencia" class="col-md-4 control-label"> Secuencia </label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input value="@if($secuencia!=''){{$secuencia}}@endif" type="text" class="form-control input-sm" name="secuencia" id="secuencia" placeholder="ingrese secuencia">
                                <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('secuencia').value = ''; buscar();"></i>
                                </div>
                            </div>
                    </div>
                </div>
                

                    
                </div>
                <div class="row" style="padding-bottom: 20px;">
                    <div class="form-group col-md-3 col-xs-5">
                        <label for="cedula" class="col-md-3 control-label"> {{trans('agenda.cedula')}} </label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="{{trans('laboratorio.ingresecedula')}}">
                                <div class="input-group-addon">
                                    <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
                                </div>
                            </div>
                    </div>
                </div>

                    <div class="form-group col-md-3 col-xs-5">
                        <label class="control-label col-md-3">{{trans('winsumos.Paciente')}}</label>
                        <div class="col-md-9">
                            <input type="text" name="paciente" id="paciente" class="form-control autocom input-sm" placeholder="{{trans('winsumos.Ingrese_paciente')}}" value="@if(isset($paciente)){{$paciente}}@endif">
                        </div>
                    </div>

                    <div class="form-group col-md-3 col-xs-5">
                        <button type="submit" formaction="{{ route('receta.index_nota_entrega')}}" class="btn btn-primary btn-xl" id="boton_buscar">
                            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px"> {{trans('winsumos.Buscar')}}&nbsp;</span>
                        </button>
                    </div>

                    <div class="form-group col-md-3 col-xs-5">
                        <button formaction="{{route('receta.excel_nota_entrega')}}" target="_blank" class="bg-green btn btn-primary btn-xl">
                            <i class="fa fa-file-excel-o" aria-hidden="true"> Excel</i>
                        </button>
                    </div>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th> Accion </th>
                                        <th> Secuencia </th>
                                        <th> Fecha </th>
                                        <th> Empresa </th>
                                        <th> {{trans('agenda.cedula')}} </th>
                                        <th> {{trans('winsumos.Paciente')}} </th>
                                        <th style="text-align: center;"> {{trans('winsumos.accion')}} </th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($notas_entrega as $nota_entrega)
                                    <tr> 
                                        <td>
                                            @php
                                            $rol_usuario = \Sis_medico\User::find(Auth::user()->id)->id_tipo_usuario;
                                            @endphp
                                            @if($nota_entrega->estado == '1' )
                                            <a style="padding: 5% 10%;" target="_blank" class="btn btn-primary btn-xs" formtarget="_blank" href="{{route('nota_entrega.imprimir_pdf_nota_entrega',['id_nota' => $nota_entrega->id])}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span></a>
                                            @endif

                                            <a style="padding: 5% 10%;" target="_blank" href="{{ route('nota_entrega.editar', ['id' =>$nota_entrega->id_hc_receta]) }}" class="btn btn-warning  btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
                                        </td>
                                        <td>{{$nota_entrega->numero}}</td>
                                        <td> @if(isset($nota_entrega->created_at)){{substr($nota_entrega->created_at, 0,10)}}@endif </td>
                                        <td> @if(isset($nota_entrega->empresa->id)){{$nota_entrega->empresa->nombrecomercial}}@endif </td>
                                        <td>@if(isset($nota_entrega->historia->paciente->id)){{$nota_entrega->historia->paciente->id}} @endif</td>
                                        <td>@if(isset($nota_entrega->historia->paciente)) {{$nota_entrega->historia->paciente->apellido1}} {{$nota_entrega->historia->paciente->apellido2}} {{$nota_entrega->historia->paciente->nombre1}} {{$nota_entrega->historia->paciente->nombre2}} @endif</td>
                                        @php
                                        $doc_scan = \Sis_medico\Nota_Entrega_Escaneados::where('id_nota_entrega', $nota_entrega->id)->where('estado', '1')->first();

                                        $detalles = \Sis_medico\Nota_entrega_detalle::where('id_nota_entrega', $nota_entrega->id)->where('estado', '1')->get();

                                        $i=0;
                                        if(!is_null($detalles)){
                                        foreach($detalles as $det){
                                        if($det->cantidad_receta != $det->cantidad){
                                        $i++;
                                        }
                                        }
                                        }
                                        @endphp
                                        <td style="text-align: center;">
                                            <a href="{{url('nota_entrega/sube/archivo')}}/{{$nota_entrega->id_hc_receta}}/{{$nota_entrega->id}}" data-toggle="modal" data-target="#Subir">
                                              <button class="btn btn-success btn-xs"> @if(!is_null($doc_scan)) Actualizar @else Subir @endif</button>
                                            </a>
                                            
                                            @if(!is_null($doc_scan))
                                            <a target="_blank" href="{{route('receta.visualizar_nota_entrega_pdf',['id'=>$nota_entrega->id])}}" class="btn btn-primary btn-xs">Vizualizar</a>
                                            @endif <br>
                                            @if($i >= 1)
                                                <span style="color: orange; font-weight: bold;">! PENDIENTE DE ENTREGAR MEDICINA !</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-sm-5">
                    <!-- <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 {{trans('winsumos.a')}} {{count($notas_entrega)}} {{trans('winsumos.de')}} {{$notas_entrega->total()}} {{trans('winsumos.registros')}}</div> -->
                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($notas_entrega->currentPage()-1)*$notas_entrega->perPage()}}  / @if(($notas_entrega->currentPage()*$notas_entrega->perPage())<$notas_entrega->total()){{($notas_entrega->currentPage()*$notas_entrega->perPage())}} @else {{$notas_entrega->total()}} @endif de {{$notas_entrega->total()}} registros</div>
                </div>
                <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        {{ $notas_entrega->appends(Request::only(['fecha', 'fecha_hasta', 'id', 'cedula','paciente', 'empresa']))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
<script type="text/javascript">
    $('#Subir').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });


    // function excel_nota_entrega() {


    //     $.ajax({
    //         type: 'post',
    //         headers: {
    //             'X-CSRF-TOKEN': $('input[name=_token]').val()
    //         },
    //         url: "{{route('receta.excel_nota_entrega')}}",
    //         datatype: 'json',
    //         data: $('#form_pro_agrup').serialize(),

    //         success: function(data) {

    //         },
    //         error: function(data) {

    //         }
    //     });
    // }
</script>