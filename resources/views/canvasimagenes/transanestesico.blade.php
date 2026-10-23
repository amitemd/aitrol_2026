<div>
    <input type="hidden" id="id" value="{{$id}}">


    <input type="hidden" id="tipo" value="{{$tipo}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div style="display: flex;">
        <div class="col-auto">
            <button type="button" id="drawing-mode" class="btn btn-danger"><i class="fa fa-pencil"></i></button>

            <button type="button" id="delete_select" class="btn btn-primary"> <i class="fa fa-trash"></i> {{trans('etodos.BorrarSeleccionado')}} </button>

            <button type="button" id="clear-canvas" class="btn btn-warning"> <i class="fa fa-remove"></i> {{trans('etodos.Borrar')}}</button>

            <button type="button" class="btn btn-success" onclick="saveCan()"> <i class="fa fa-save"></i> {{trans('etodos.Guardar')}}</button>
        </div>
    </div>
</div>
<br>

<div style="display: flex;">
    <canvas id="c" width="1450px" height="500px"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    var canvas = new fabric.Canvas('c', {
        isDrawingMode: false
    });

    var tipoValue = document.getElementById('tipo').value;

    var tipo = parseInt(tipoValue);



    if (tipo === 0) {
        @if(!is_null($hc_anes->tran_anastesico_path))
        fabric.Image.fromURL("{{asset('trans_record')}}/{{$hc_anes->tran_anastesico_path}}", function(img) {
            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                scaleX: canvas.width / img.width,
                scaleY: canvas.height / img.height
            });
        });
        @else
        fabric.Image.fromURL("{{asset('/imagenes/imagen_referencia.jpeg')}}", function(img) {
            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                scaleX: canvas.width / img.width,
                scaleY: canvas.height / img.height
            });
        });
        @endif
    } else {
        @if(!is_null($hc_anes->post_anastesico_path))
        fabric.Image.fromURL("{{asset('trans_record')}}/{{$hc_anes->post_anastesico_path}}", function(img) {
            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                scaleX: canvas.width / img.width,
                scaleY: canvas.height / img.height
            });
        });
        @else
            
            @if(!is_null($hc_anes->url_imagen))
                 fabric.Image.fromURL("{{asset('hc_ima')}}/{{$hc_anes->url_imagen}}", function(img) {   
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / img.width,
                    scaleY: canvas.height / img.height
                });
                });
            @else
                fabric.Image.fromURL("{{asset('/imagenes/imagen_post.jpeg')}}", function(img) {   
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / img.width,
                    scaleY: canvas.height / img.height
                });
                });
            @endif
                    
        @endif
    }

    fabric.Object.prototype.transparentCorners = false;
    var a = function(id) {
        return document.getElementById(id);
    };
    var drawingModeEl = a('drawing-mode'),
        drawingOptionsEl = a('drawing-mode-options'),
        drawingColorEl = a('drawing-color'),
        drawingShadowColorEl = a('drawing-shadow-color'),
        drawingLineWidthEl = a('drawing-line-width'),
        drawingShadowWidth = a('drawing-shadow-width'),
        drawingShadowOffset = a('drawing-shadow-offset'),
        clearEl = a('clear-canvas');

    clearEl.onclick = function() {
        canvas.clear();
        if (tipo === 0) {
            fabric.Image.fromURL("{{asset('/imagenes/imagen_referencia.jpeg')}}", function(img) {
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / img.width,
                    scaleY: canvas.height / img.height
                });
            });
        } else {
            fabric.Image.fromURL("{{asset('/imagenes/imagen_post.jpeg')}}", function(img) {
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / img.width,
                    scaleY: canvas.height / img.height
                });
            });
        }
    };
    var clxz = a('delete_select');
    clxz.onclick = function() {
        deleteSelectedObjectsFromCanvas()
    }
    drawingModeEl.onclick = function() {
        canvas.isDrawingMode = !canvas.isDrawingMode;
        if (canvas.isDrawingMode) {
            drawingModeEl.innerHTML = '<i class="fa fa-remove"> </i>';
        } else {
            drawingModeEl.innerHTML = '<i class="fa fa-pencil"> </i>';

        }
    };
    canvas.freeDrawingBrush.width = 1;

    var clxz = a('delete_select');
    clxz.onclick = function() {
        deleteSelectedObjectsFromCanvas()
    }

    function deleteSelectedObjectsFromCanvas() {
        var selection = canvas.getActiveObject();
        if (selection.type === 'activeSelection') {
            selection.forEachObject(function(element) {
                console.log(element);
                canvas.remove(element);
            });
        } else {
            canvas.remove(selection);
        }
        canvas.discardActiveObject();
        canvas.requestRenderAll();
    }

    function tosaveCanvas(id) {
        var canvas = document.getElementById('c');
        var blob = canvas.toDataURL();
        let tipo = $("#tipo").val();
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('my-file', blob, 'filename.png');
            $.ajax({
                url: "{{ route('preeanestesico.saveCanvas') }}?id=" + id + "&tipo=" + tipo,
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function(data) {
                    if (data == 'ok') {
                        swal.fire({
                            title: "Success!",
                            text: `Imagen Guardada`,
                            icon: "success"
                        });
                    } else {
                        swal.fire({
                            title: "Error!",
                            text: `Ha ocurrido un error en el guardado`,
                            icon: "error"
                        });
                    }

                },
                error: function(xhr, status, error) {
                    console.log(xhr, status, error);
                }
            });
        });
    }

    function hayContenidoDibujado() {
        return canvas.getObjects().length > 0;
    }

    function saveCan() {
        let id = $("#id").val();
        if (hayContenidoDibujado()) {
            tosaveCanvas(id);
        } else {
            swal.fire({
                title: "Error!",
                text: "La imagen esta vacia!",
                icon: "error"
            });
        }

    }
</script>