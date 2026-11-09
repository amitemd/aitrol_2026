<style>
  .v3-medicine-list { color:#294d63; font-family:"Helvetica Neue",Arial,sans-serif; padding:0; }
  .v3-medicine-list * { box-sizing:border-box; }
  .v3-medicine-list__card { background:#fff; border:1px solid #d2e3eb; border-radius:12px; box-shadow:0 7px 22px rgba(22,77,107,.11); overflow:hidden; }
  .v3-medicine-list__head { align-items:center; background:linear-gradient(116deg,#08719c 0%,#118eae 60%,#17aaa5 100%); color:#fff; display:flex; justify-content:space-between; min-height:70px; padding:13px 20px; }
  .v3-medicine-list__title { align-items:center; display:flex; min-width:0; }
  .v3-medicine-list__icon { align-items:center; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.28); border-radius:9px; display:flex; flex:0 0 42px; font-size:18px; height:42px; justify-content:center; margin-right:12px; }
  .v3-medicine-list__eyebrow { color:rgba(255,255,255,.78); font-size:9px; font-weight:700; letter-spacing:1px; text-transform:uppercase; }
  .v3-medicine-list__head h3 { color:#fff; font-size:18px; font-weight:700; margin:1px 0 2px; }
  .v3-medicine-list__head p { color:rgba(255,255,255,.84); font-size:11px; margin:0; }
  .v3-medicine-list__actions { display:flex; gap:8px; position:relative; z-index:1; }
  .v3-medicine-list__button { align-items:center; background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.55); border-radius:7px; color:#fff!important; cursor:pointer; display:inline-flex; font-size:11px; font-weight:700; height:35px; justify-content:center; padding:0 13px; }
  .v3-medicine-list__button:hover,.v3-medicine-list__button:focus { background:#fff; color:#08749b!important; }
  .v3-medicine-list__button i { margin-right:6px; }
  .v3-medicine-list__toolbar { align-items:end; background:#f7fbfd; border-bottom:1px solid #dbe8ee; display:grid; gap:12px; grid-template-columns:minmax(260px,520px) auto 1fr; padding:13px 18px; }
  .v3-medicine-list__field label { color:#41677d; display:block; font-size:10px; font-weight:700; letter-spacing:.2px; margin:0 0 5px; text-transform:uppercase; }
  .v3-medicine-list__input { position:relative; }
  .v3-medicine-list__input i { color:#5e879d; left:12px; position:absolute; top:10px; }
  .v3-medicine-list__input .form-control { border:1px solid #cbdde6; border-radius:7px; box-shadow:none; color:#294f65; font-size:12px; height:36px; padding-left:35px; width:100%; }
  .v3-medicine-list__input .form-control:focus { border-color:#269fbe; box-shadow:0 0 0 3px rgba(25,147,180,.11); }
  .v3-medicine-list__search { align-items:center; background:#087ca7; border:0; border-radius:7px; color:#fff; cursor:pointer; display:inline-flex; font-size:11px; font-weight:700; height:36px; justify-content:center; padding:0 16px; }
  .v3-medicine-list__search i { margin-right:6px; }
  .v3-medicine-list__count { align-self:center; color:#688597; font-size:11px; justify-self:end; }
  .v3-medicine-list__count strong { color:#087ca7; }
  .v3-medicine-list__table-wrap { overflow-x:auto; width:100%; }
  .v3-medicine-list__table { border-collapse:collapse; margin:0; min-width:760px; table-layout:fixed; width:100%; }
  .v3-medicine-list__table th { background:#edf5f8; border-bottom:1px solid #d5e4eb; color:#456c83; font-size:10px; font-weight:700; padding:10px 12px; text-align:left; text-transform:uppercase; }
  .v3-medicine-list__table td { border-bottom:1px solid #e5eef2; color:#3b5e73; font-size:11px; padding:10px 12px; vertical-align:middle; }
  .v3-medicine-list__table tbody tr:hover { background:#f1fafb; }
  .v3-medicine-list__name { color:#086f99; font-weight:700; }
  .v3-medicine-list__dose { color:#58768a; line-height:1.35; overflow-wrap:anywhere; }
  .v3-medicine-list__quantity { background:#eaf6fb; border-radius:12px; color:#087ca7; display:inline-block; font-weight:700; min-width:30px; padding:4px 8px; text-align:center; }
  .v3-medicine-list__generic { background:#e4f6f3; border-radius:12px; color:#167c72; display:inline-block; font-size:10px; font-weight:700; margin:2px 3px 2px 0; padding:4px 8px; }
  .v3-medicine-list__missing { color:#8a9ca7; font-size:10px; }
  .v3-medicine-list__edit { align-items:center; background:#fff7e8; border:1px solid #f0d18f; border-radius:6px; color:#99680f!important; cursor:pointer; display:inline-flex; font-size:11px; font-weight:700; height:30px; justify-content:center; min-width:76px; padding:0 10px; }
  .v3-medicine-list__edit:hover { background:#ffefca; }
  .v3-medicine-list__edit i { margin-right:5px; }
  .v3-medicine-list__empty { color:#748d9d!important; padding:30px!important; text-align:center; }
  .v3-medicine-list__empty i { color:#8fb0c2; display:block; font-size:25px; margin-bottom:7px; }
  .v3-medicine-list__footer { align-items:center; background:#fbfdfe; display:flex; justify-content:space-between; min-height:48px; padding:8px 16px; }
  .v3-medicine-list__footer .pagination { margin:0; }
  .v3-medicine-list__footer .pagination>li>a,.v3-medicine-list__footer .pagination>li>span { border-color:#d4e3ea; color:#247b9e; font-size:11px; padding:5px 9px; }
  .v3-medicine-list__footer .pagination>.active>span { background:#118eae; border-color:#118eae; color:#fff; }
  @media(max-width:767px){.v3-medicine-list__head{align-items:flex-start;flex-direction:column;gap:12px}.v3-medicine-list__actions{width:100%}.v3-medicine-list__button{flex:1}.v3-medicine-list__toolbar{grid-template-columns:1fr}.v3-medicine-list__search{width:100%}.v3-medicine-list__count{justify-self:start}.v3-medicine-list__footer{align-items:flex-start;flex-direction:column;gap:8px}}
</style>
@php($isPaginated=is_object($medicinas) && method_exists($medicinas,'total'))
<section class="content v3-medicine-list">
  <div class="v3-medicine-list__card">
    <header class="v3-medicine-list__head">
      <div class="v3-medicine-list__title"><div class="v3-medicine-list__icon"><i class="fas fa-capsules" aria-hidden="true"></i></div><div><div class="v3-medicine-list__eyebrow">Catálogo clínico · V3</div><h3>Listado de medicinas</h3><p>Consulta y administra las medicinas disponibles para prescripción.</p></div></div>
      <div class="v3-medicine-list__actions"><button type="button" class="v3-medicine-list__button" onclick="crear_medicina();"><i class="fas fa-plus" aria-hidden="true"></i>Nueva medicina</button><button type="button" class="v3-medicine-list__button" onclick="crear_generico();"><i class="fas fa-list" aria-hidden="true"></i>Genéricos</button></div>
    </header>
    <form id="form" class="v3-medicine-list__toolbar" method="POST" onsubmit="buscar_medicina();return false;">
      {{csrf_field()}}
      <div class="v3-medicine-list__field"><label for="nombre_medicina_busqueda">Buscar por nombre</label><div class="v3-medicine-list__input"><i class="fas fa-search" aria-hidden="true"></i><input value="{{$nombre}}" type="search" class="form-control" name="nombre" id="nombre_medicina_busqueda" placeholder="Escriba el nombre de la medicina" autocomplete="off"></div></div>
      <button class="v3-medicine-list__search" type="submit"><i class="fas fa-search" aria-hidden="true"></i>Buscar</button>
      <div class="v3-medicine-list__count">
        @if($isPaginated)
          Mostrando <strong>{{$medicinas->firstItem()?:0}}–{{$medicinas->lastItem()?:0}}</strong> de <strong>{{$medicinas->total()}}</strong>
        @else
          <strong>{{$medicinas->count()}}</strong> medicinas
        @endif
      </div>
    </form>
    <div class="v3-medicine-list__table-wrap">
      <table class="v3-medicine-list__table">
        <colgroup><col style="width:24%"><col style="width:27%"><col style="width:10%"><col style="width:29%"><col style="width:10%"></colgroup>
        <thead><tr><th>Nombre</th><th>Dosis</th><th>Cantidad</th><th>Genéricos</th><th>Acción</th></tr></thead>
        <tbody>
          @forelse($medicinas as $medicina)
          <tr><td><span class="v3-medicine-list__name">{{$medicina->nombre}}</span></td><td class="v3-medicine-list__dose">{{trim(strip_tags($medicina->dosis))!==''?trim(strip_tags($medicina->dosis)):'Sin dosis registrada'}}</td><td><span class="v3-medicine-list__quantity">{{$medicina->cantidad}}</span></td><td>@forelse($medicina->genericos as $relacion)@if(!is_null($relacion->generico))<span class="v3-medicine-list__generic">{{$relacion->generico->nombre}}</span>@else<span class="v3-medicine-list__missing">Genérico no disponible</span>@endif @empty<span class="v3-medicine-list__missing">Sin principio activo</span>@endforelse</td><td><button type="button" class="v3-medicine-list__edit" onclick="editar_medicina({{$medicina->id}});"><i class="fas fa-edit" aria-hidden="true"></i>Editar</button></td></tr>
          @empty
          <tr><td colspan="5" class="v3-medicine-list__empty"><i class="fas fa-capsules" aria-hidden="true"></i>No se encontraron medicinas con ese criterio.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($isPaginated)
      <footer class="v3-medicine-list__footer"><span>Resultados paginados para facilitar la revisión.</span><div class="medicine-pagination" data-search-mode="{{!is_null($nombre)?'1':'0'}}">{!!$medicinas->appends(array('nombre'=>$nombre))->links()!!}</div></footer>
    @endif
  </div>
</section>
<script>
  function cargarVistaMedicinas(opciones){var area=$('#area_trabajo').css('opacity','.55').attr('aria-busy','true');$.ajax(opciones).done(function(html){area.html(html);}).fail(function(){if(window.Swal){Swal.fire({icon:'error',title:'No se pudo cargar',text:'Inténtalo nuevamente.'});}else{alert('Error al cargar');}}).always(function(){area.css('opacity','1').attr('aria-busy','false');});}
  function crear_medicina(){cargarVistaMedicinas({type:'GET',url:"{{route('crear.medicina_hc4')}}"});}
  function crear_generico(){cargarVistaMedicinas({type:'GET',url:"{{route('listar.medicina_generica.hc4')}}"});}
  function editar_medicina(id){cargarVistaMedicinas({type:'GET',url:"{{route('edit.medicina')}}/"+id});}
  function buscar_medicina(){cargarVistaMedicinas({type:'POST',url:"{{route('medicina.buscar_hc4')}}",data:$('#form').serialize()});}
  $('.medicine-pagination a').off('click.medicinePagination').on('click.medicinePagination',function(event){event.preventDefault();var link=$(this).attr('href'),pagination=$(this).closest('.medicine-pagination');if(pagination.data('search-mode')==1){var page=(link.match(/[?&]page=([^&]+)/)||[])[1]||1;cargarVistaMedicinas({type:'POST',url:"{{route('medicina.buscar_hc4')}}",data:{_token:$('#form input[name="_token"]').val(),nombre:$('#nombre_medicina_busqueda').val(),page:page}});}else{cargarVistaMedicinas({type:'GET',url:link});}});
</script>
