<style>
  .v3-generic-list { color:#294d63; font-family:"Helvetica Neue",Arial,sans-serif; padding:0; }
  .v3-generic-list * { box-sizing:border-box; }
  .v3-generic-list__card { background:#fff; border:1px solid #d2e3eb; border-radius:12px; box-shadow:0 7px 22px rgba(22,77,107,.11); overflow:hidden; width:100%; }
  .v3-generic-list__head { align-items:center; background:linear-gradient(116deg,#08719c 0%,#118eae 60%,#17aaa5 100%); color:#fff; display:flex; justify-content:space-between; min-height:70px; padding:13px 20px; }
  .v3-generic-list__title { align-items:center; display:flex; min-width:0; }
  .v3-generic-list__icon { align-items:center; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.28); border-radius:9px; display:flex; flex:0 0 42px; font-size:18px; height:42px; justify-content:center; margin-right:12px; }
  .v3-generic-list__eyebrow { color:rgba(255,255,255,.78); font-size:9px; font-weight:700; letter-spacing:1px; text-transform:uppercase; }
  .v3-generic-list__head h3 { color:#fff; font-size:18px; font-weight:700; margin:1px 0 2px; }
  .v3-generic-list__head p { color:rgba(255,255,255,.84); font-size:11px; margin:0; }
  .v3-generic-list__actions { display:flex; gap:8px; position:relative; z-index:1; }
  .v3-generic-list__button { align-items:center; background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.55); border-radius:7px; color:#fff!important; cursor:pointer; display:inline-flex; font-size:11px; font-weight:700; height:35px; justify-content:center; padding:0 13px; }
  .v3-generic-list__button:hover,.v3-generic-list__button:focus { background:#fff; color:#08749b!important; }
  .v3-generic-list__button i { margin-right:6px; }
  .v3-generic-list__toolbar { align-items:end; background:#f7fbfd; border-bottom:1px solid #dbe8ee; display:grid; gap:12px; grid-template-columns:minmax(260px,520px) auto 1fr; padding:13px 18px; }
  .v3-generic-list__field label { color:#41677d; display:block; font-size:10px; font-weight:700; letter-spacing:.2px; margin:0 0 5px; text-transform:uppercase; }
  .v3-generic-list__input { position:relative; }
  .v3-generic-list__input i { color:#5e879d; left:12px; position:absolute; top:10px; }
  .v3-generic-list__input .form-control { border:1px solid #cbdde6; border-radius:7px; box-shadow:none; color:#294f65; font-size:12px; height:36px; padding-left:35px; width:100%; }
  .v3-generic-list__input .form-control:focus { border-color:#269fbe; box-shadow:0 0 0 3px rgba(25,147,180,.11); }
  .v3-generic-list__search { align-items:center; background:#087ca7; border:0; border-radius:7px; color:#fff; cursor:pointer; display:inline-flex; font-size:11px; font-weight:700; height:36px; justify-content:center; padding:0 16px; }
  .v3-generic-list__search i { margin-right:6px; }
  .v3-generic-list__count { align-self:center; color:#688597; font-size:11px; justify-self:end; }
  .v3-generic-list__count strong { color:#087ca7; }
  .v3-generic-list__table-wrap { overflow-x:auto; width:100%; }
  .v3-generic-list__table { border-collapse:collapse; margin:0; min-width:620px; table-layout:fixed; width:100%; }
  .v3-generic-list__table th { background:#edf5f8; border-bottom:1px solid #d5e4eb; color:#456c83; font-size:10px; font-weight:700; padding:10px 14px; text-align:left; text-transform:uppercase; }
  .v3-generic-list__table td { border-bottom:1px solid #e5eef2; color:#3b5e73; font-size:11px; padding:11px 14px; vertical-align:middle; }
  .v3-generic-list__table tbody tr:hover { background:#f1fafb; }
  .v3-generic-list__name { color:#086f99; font-weight:700; }
  .v3-generic-list__description { color:#58768a; line-height:1.4; overflow-wrap:anywhere; }
  .v3-generic-list__missing { color:#8a9ca7; font-size:10px; }
  .v3-generic-list__edit { align-items:center; background:#fff7e8; border:1px solid #f0d18f; border-radius:6px; color:#99680f!important; cursor:pointer; display:inline-flex; font-size:11px; font-weight:700; height:30px; justify-content:center; min-width:76px; padding:0 10px; }
  .v3-generic-list__edit:hover { background:#ffefca; }
  .v3-generic-list__edit i { margin-right:5px; }
  .v3-generic-list__empty { color:#748d9d!important; padding:30px!important; text-align:center; }
  .v3-generic-list__empty i { color:#8fb0c2; display:block; font-size:25px; margin-bottom:7px; }
  .v3-generic-list__footer { align-items:center; background:#fbfdfe; display:flex; justify-content:space-between; min-height:48px; padding:8px 16px; }
  .v3-generic-list__footer .pagination { margin:0; }
  .v3-generic-list__footer .pagination>li>a,.v3-generic-list__footer .pagination>li>span { border-color:#d4e3ea; color:#247b9e; font-size:11px; padding:5px 9px; }
  .v3-generic-list__footer .pagination>.active>span { background:#118eae; border-color:#118eae; color:#fff; }
  @media(max-width:767px){.v3-generic-list__head{align-items:flex-start;flex-direction:column;gap:12px}.v3-generic-list__actions{width:100%}.v3-generic-list__button{flex:1}.v3-generic-list__toolbar{grid-template-columns:1fr}.v3-generic-list__search{width:100%}.v3-generic-list__count{justify-self:start}.v3-generic-list__footer{align-items:flex-start;flex-direction:column;gap:8px}}
</style>
@php($isPaginated=is_object($genericos) && method_exists($genericos,'total'))
<section class="content v3-generic-list">
  <div class="v3-generic-list__card">
    <header class="v3-generic-list__head">
      <div class="v3-generic-list__title">
        <div class="v3-generic-list__icon"><i class="fas fa-atom" aria-hidden="true"></i></div>
        <div><div class="v3-generic-list__eyebrow">Catálogo clínico · V3</div><h3>Medicinas genéricas</h3><p>Administra los principios activos disponibles para las medicinas.</p></div>
      </div>
      <div class="v3-generic-list__actions"><button type="button" class="v3-generic-list__button" onclick="crear_generico();"><i class="fas fa-plus" aria-hidden="true"></i>Nuevo genérico</button></div>
    </header>
    <form id="form_generico" class="v3-generic-list__toolbar" method="POST" onsubmit="buscar_generico();return false;">
      {{csrf_field()}}
      <div class="v3-generic-list__field">
        <label for="nombre_generico_busqueda">Buscar por nombre</label>
        <div class="v3-generic-list__input"><i class="fas fa-search" aria-hidden="true"></i><input value="{{$nombre}}" type="search" class="form-control" name="nombre" id="nombre_generico_busqueda" placeholder="Escriba el nombre del genérico" autocomplete="off"></div>
      </div>
      <button class="v3-generic-list__search" type="submit"><i class="fas fa-search" aria-hidden="true"></i>Buscar</button>
      <div class="v3-generic-list__count">
        @if($isPaginated)
          Mostrando <strong>{{$genericos->firstItem()?:0}}–{{$genericos->lastItem()?:0}}</strong> de <strong>{{$genericos->total()}}</strong>
        @else
          <strong>{{$genericos->count()}}</strong> genéricos
        @endif
      </div>
    </form>
    <div class="v3-generic-list__table-wrap">
      <table class="v3-generic-list__table">
        <colgroup><col style="width:30%"><col style="width:58%"><col style="width:12%"></colgroup>
        <thead><tr><th>Nombre</th><th>Descripción</th><th>Acción</th></tr></thead>
        <tbody>
          @forelse($genericos as $generico)
            <tr>
              <td><span class="v3-generic-list__name">{{$generico->nombre}}</span></td>
              <td class="v3-generic-list__description">{{trim(strip_tags($generico->descripcion))!==''?trim(strip_tags($generico->descripcion)):'Sin descripción registrada'}}</td>
              <td><button type="button" class="v3-generic-list__edit" onclick="editar_generico({{$generico->id}});"><i class="fas fa-edit" aria-hidden="true"></i>Editar</button></td>
            </tr>
          @empty
            <tr><td colspan="3" class="v3-generic-list__empty"><i class="fas fa-atom" aria-hidden="true"></i>No se encontraron genéricos con ese criterio.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($isPaginated)
      <footer class="v3-generic-list__footer"><span>Resultados paginados para facilitar la revisión.</span><div class="generic-pagination" data-search-mode="{{!is_null($nombre)?'1':'0'}}">{!!$genericos->appends(array('nombre'=>$nombre))->links()!!}</div></footer>
    @endif
  </div>
</section>
<script>
  function cargarVistaGenericos(opciones){var area=$('#area_trabajo').css('opacity','.55').attr('aria-busy','true');$.ajax(opciones).done(function(html){area.html(html);}).fail(function(){if(window.Swal){Swal.fire({icon:'error',title:'No se pudo cargar',text:'Inténtalo nuevamente.'});}else{alert('Error al cargar');}}).always(function(){area.css('opacity','1').attr('aria-busy','false');});}
  function crear_generico(){cargarVistaGenericos({type:'GET',url:"{{route('crear.generico_hc4')}}"});}
  function editar_generico(id){cargarVistaGenericos({type:'GET',url:"{{route('edit.generico')}}/"+id});}
  function buscar_generico(){cargarVistaGenericos({type:'POST',url:"{{route('generico.buscar_hc4')}}",data:$('#form_generico').serialize()});}
  $('.generic-pagination a').off('click.genericPagination').on('click.genericPagination',function(event){event.preventDefault();var link=$(this).attr('href'),pagination=$(this).closest('.generic-pagination');if(pagination.data('search-mode')==1){var page=(link.match(/[?&]page=([^&]+)/)||[])[1]||1;cargarVistaGenericos({type:'POST',url:"{{route('generico.buscar_hc4')}}",data:{_token:$('#form_generico input[name="_token"]').val(),nombre:$('#nombre_generico_busqueda').val(),page:page}});}else{cargarVistaGenericos({type:'GET',url:link});}});
</script>
