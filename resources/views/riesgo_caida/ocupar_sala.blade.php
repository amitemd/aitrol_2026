@php
$date = date("Y-m-d");
@endphp
<div class="modal-content">
    <div class="modal-header" style="background: #3c8dbc;">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">Ocupar Sala</h3>
    </div>
    <div class="modal-body">
        <form method="post" id="formulario" action="#">
            <div class="row">
                <div class="col-md-6">
                    <label for="">Desde</label>
                    <input type="date" name="desde" onchange="tablita()" value="{{$date}}" id="desde" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="">Hasta</label>
                    <input type="date" onchange="tablita()" name="hasta" value="{{$date}}" id="hasta" class="form-control">
                </div>
                <div class="col-md-12" style="margin-top:5px;">
                    <div id="tablita">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
</div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'responsive': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'sInfoEmpty': false,
            'sInfoFiltered': false,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
    $(document).ready(function() {
        tablita();
    });

    function tablita() {
        $.ajax({
            url: "{{route('buscar_estado_sin_cama')}}",
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
</script>