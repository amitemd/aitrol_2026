@php $cont = 0; @endphp
<div class="row">
@foreach($recibos as $recibo)
@php $cont ++; @endphp

<div class="col-md-12">&nbsp;</div>
<div class="col-md-4">
{{ $cont }}    
</div>
<div class="col-md-4">
Fecha: {{$recibo->fecha_emision}}
Estado: @if($recibo->estado == 1) EMITIDA @else Por Emitir @endif    
</div>
<div class="col-md-2">
Total: {{$recibo->total}}    
</div>
<div class="col-md-2">
<button type="button" class="btn btn-warning btn-sm" onclick="editar('{{$recibo->id}}')">Editar</button>
<button type="button" class="btn btn-danger btn-sm" onclick="eliminar('{{$recibo->id}}')">X</button>    
</div>
@endforeach
</div>
<script type="text/javascript">

    function editar(id){
        $('#id_recibo').val(id);
        $.ajax({
            url: "{{ url('rc_hospital/editar') }}/"+id,
           
            type: 'GET',
            dataType: 'html',
            beforeSend: function() {

                //$('#loading').show();
            },
            success: function(datahtml, data) {
                //$('#loading').hide();
                $("#editar_recibo").empty().html(datahtml).fadeIn();
            },
            error: function(xhr, status) {
                alert('Existió un problema en la Tabla');

            },
        });    
    }

</script>
