@php
$emplace=asset('/');
$remplace= str_replace('public','storage',$emplace);
@endphp
<div class="modal-content">
    <div class="modal-header">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">Previsualizar Archivo</h3>
    </div>
    
    <div style="margin-top: 5px;" class="col-md-12">
        <div>
            <iframe src="{{$remplace.'app/hc_ima/'.$id_imagen->nombre_archivo}}" style="width:100%;height:560px;" frameborder="2"></iframe>
        </div>
    </div>

    <div class="modal-footer">

    </div>
</div>