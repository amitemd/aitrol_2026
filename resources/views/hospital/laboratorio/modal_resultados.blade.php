<div class="modal-content" style="width:auto">
    <div class="modal-header">
        <input type="hidden">
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title"></h3>
    </div>
    <div class="modal-body">
        <div class="box-body">
            <embed type="text/html" src="{{route('hospitalizacion.imprimir',['id' =>$id])}}" style="width: 100%;height: 80vh;">
        </div>
    </div>
</div>