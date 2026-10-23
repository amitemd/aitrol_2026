<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
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
.table>tbody>tr>td,
.table>tbody>tr>th {
    padding: 0.4%;
}

.dropdown-menu>li>a {
    color: white !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 12px !important;
}

.dropdown-menu>li>a:hover {
    background-color: #008d4c !important;
}

.cot>li>a:hover {
    background-color: #00acd6 !important;
}
</style>

<div class="modal-content" style="width: 100%;" id="div_listado_prefacturas">
    <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">

        <div class="row" style="border-bottom: 1px solid black;">
            <div class="col-md-2">
                <a class="btn btn-light" data-dismiss="modal" id="cerrar">
                    <h1 style="font-size: 12px; margin:0;">
                        <img style="width: 30px;height:23px" src="{{asset('/')}}hc4/img/exit.png">
                        <label style="font-size: 14px">Cerrar</label>
                    </h1>
                </a>
            </div>
        </div>

        <div class="modal-body">
            <div class="box-body">

                <div class="table-responsive col-md-12">
                    <form id="prefactura_contable">
                      {{csrf_field()}}
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
                            aria-describedby="example2_info" style="text-align: center;">
                            <thead style="background-color: #4682B4">
                                <tr>
                                    <th style="width: 3%;height:8px;color: white;text-align: center; font-size: 12px;">
                                        SEGURO </th>
                                    <th style="width: 3%;height:8px;color: white;text-align: center; font-size: 12px;">
                                        MES - AÑO </th>
                                    <th style="width: 3%;height:8px;color: white;text-align: center; font-size: 12px;">
                                        TIPO SEGURO</th>
                                    <th style="width: 3%;height:8px;color: white;text-align: center; font-size: 12px;">
                                        CONCEPTO </th>
                                    <th style="width: 3%;height:8px;color: white;text-align: center; font-size: 12px;">
                                        VALOR </th>
                                        <th style="width: 3%;height:8px;color: white;text-align: center; font-size: 12px;">
                                        VALOR IVA </th>
                                    <th style="width: 3%;height:8px;color: white;text-align: center; font-size: 12px;">
                                        ACCION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($det_prefacturas as $det_prefactura)
                                <tr>
                                    <!--td style="text-align: left;">@if(!is_null($det_prefactura->id)){{$det_prefactura->id}}@endif</td-->
                                    <td style="text-align: left;" id="{{$det_prefactura->id}}">@if(!is_null($det_prefactura->fx_seguro->nombre)){{$det_prefactura->fx_seguro->nombre}}@endif</td>
                                    <td style="text-align: left;">@if(!is_null($det_prefactura->mes_anio)){{$det_prefactura->mes_anio}}@endif</td>
                                    <td style="text-align: left;">@if(!is_null($det_prefactura->id_tipo_seg)){{$det_prefactura->id_tipo_seg}}@endif</td>
                                    <!--td style="text-align: left;">@if(!is_null($det_prefactura->cobertura)){{$det_prefactura->cobertura}}@endif</td-->
                                    <td style="text-align: left;">@if(!is_null($det_prefactura->concepto)){{$det_prefactura->concepto}}@endif</td>
                                    <td style="text-align: left;">@if(!is_null($det_prefactura->concepto)){{$det_prefactura->valor}}@endif</td>
                                    <td style="text-align: left;">@if(!is_null($det_prefactura->concepto)){{$det_prefactura->valor_iva}}@endif</td>

                                    <td style="text-align: center;">
                                        <button class="btn btn-danger btn-xs"
                                            onclick="prefactura_delete('{{$det_prefactura->id}}')">ELIMINAR</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>

                </div>

                <div class="form-group col-md-6 col-xs-6">
                    <div class="col-md-6">
                        <button id="guardar_prefactura" type="button" class="btn btn-info btn-xs"
                            onclick="enviar_prefactura_1(event)">Enviar Prefactura</button>
                    </div>
                </div>

                </form>


            </div>
        </div>

    </div>


</div>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
function enviar_prefactura_1(e) {
    e.preventDefault();
     


    console.log(mes_plano, seguro, empresa);
    $.ajax({
        type: 'post',
        url: "{{route('aparchivo.enviar_prefactura')}}",
        headers: {
            'X-CSRF-TOKEN': $('input[name=_token]').val()
        },
        datatype: 'json',
        data: {
                "mes_plano":"{{$mes_anio}}",
                "seguro":"{{$seguro}}",
                "empresa":"{{$empresa}}",
           
        },
        success: function(data) {
            console.log(data)
            if(data.status == "success"){
                swal({
                    title: "Exito",
                    icon: data.status,
                    text: data.msj
                })
            }else{
                swal({
                    title: "Error",
                    icon: data.status,
                    text: data.msj
                })
            }
            },
        error: function(data) {

        }
    })

}
</script>