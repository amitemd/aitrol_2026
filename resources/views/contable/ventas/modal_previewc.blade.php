<div class="container-fluid">
    <div class="row">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
        <br>
        <hr>
        <form id="loginForm" target="myFrame" action="{{route('ventas.pdf_nuevo2')}}" method="POST">
            {{ csrf_field() }}
            <input type="text" name="arrayPreview" value="{{json_encode($arrayGroup[0])}}" />
            <input type="text" name="getCabecera" value="{{json_encode($arrayGroup[1])}}" />
            <input type="submit">
        </form>
        <div class="col-md-12">
            <iframe name="myFrame" id="myFrame" style="width: 100%; height: 500px;" src="#">
                Your browser does not support inline frames.
            </iframe>
        </div>




        <div class="col-md-12 col-md-offset-8" style="margin:20px 0; text-align: center;">
            <button type="button" id="eqinox" class="btn btn-success btn-gray btn_save"> <i class="fa fa-save"></i></button>
        </div>

    </div>

</div>

<script>
    $(document).ready(function() {
        var loginform = document.getElementById("loginForm");
        loginform.style.display = "none";
        loginform.submit();

    });
</script>
<script>
    $('#foto').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });
    $('#video').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $("#eqinox").click(function() {
        //actualizarCodigosProductos();
        console.log("aqui entrass")
        if ($("#form").valid()) {
            $(".print").css('visibility', 'visible');
            //$(".btn_add").attr("disabled", true);
            $(".btn_add").attr("disabled", "disabled");
            $(".btn_save").attr("disabled", "disabled");
            $("#mifila").html("");

            $.ajax({
                url: "{{route('ventas_storeVarios')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#form").serialize(),
                success: function(data) {
                    console.log(data);
                    $("#asiento").val(data.idasiento);
                    $("#id").val(data.idventa);
                    $("#numero").val(data.idventa);
                    swal("Guardado con Exito!");
                },
                error: function(data) {
                    console.log(data.responseText);
                    swal("Ocurrio un error!");
                }
            });
        } else {
            swal("Tiene campos vacios");
            console.log($("#form").serialize());
        }

    });
</script>