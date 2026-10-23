<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Cierre de Caja {{date('Y/m/d H:i:s')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <form id="pore" method="POST" action="{{route('c_caja.store_salida')}}">
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Fecha</label>
                    <input class="form-control" type="text" name="fechacierre" id="fechacierre">
                    <input type="hidden" name="val" value="0">
                </div>
                <div class="form-group col-md-6">
                    <label>Valor Final</label>
                    <input class="form-control" type="text" name="valorcierre" id="valorcierre" value="{{number_format(round($valor,2),2,'.','')}}" readonly>
                </div>
                <div class="form-group col-md-12">
                    <label>Observacion</label>
                    <textarea class="form-control col-md-12" name="observacioncierre" id="observacioncierre" cols="3" rows="3"></textarea>
                </div>
            </div>

        </form>

    </div>
    <div class="modal-footer">

        <button type="button" onclick="porsumit2()" class="btn btn-primary">Guardar</button>
    </div>
</div>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script>
    $(function() {

        $('#fechacierre').datetimepicker({
            format: 'YYYY/MM/DD H:ss',
            defaultDate: '{{date("Y/m/d H:s")}}',


        });


    });
</script>