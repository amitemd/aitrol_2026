@php
$emplace=asset('/');
$remplace= str_replace('public','storage',$emplace);
@endphp
<div class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{trans('tecnicof.evidence')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" style="text-align: center;">
        @if($tipo == 1)
        <img  class="foto" src="{{$remplace.'app/avatars/'.$registro->path_antes}}" style="height:350px;" >
        @else
        <img  class="foto" src="{{$remplace.'app/avatars/'.$registro->path_despues}}" style="height:350px;" >
        @endif
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
