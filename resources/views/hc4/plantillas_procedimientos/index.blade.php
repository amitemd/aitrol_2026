<style>
  .v3-procedure-list{color:#294d63;font-family:"Helvetica Neue",Arial,sans-serif;padding:0}.v3-procedure-list *{box-sizing:border-box}
  .v3-procedure-list__card{background:#fff;border:1px solid #d2e3eb;border-radius:12px;box-shadow:0 7px 22px rgba(22,77,107,.11);overflow:hidden;width:100%}
  .v3-procedure-list__head{align-items:center;background:linear-gradient(116deg,#086c98 0%,#118caf 58%,#18aaa7 100%);color:#fff;display:flex;justify-content:space-between;min-height:70px;padding:13px 20px}
  .v3-procedure-list__title{align-items:center;display:flex;min-width:0}.v3-procedure-list__icon{align-items:center;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.28);border-radius:9px;display:flex;flex:0 0 42px;font-size:18px;height:42px;justify-content:center;margin-right:12px}
  .v3-procedure-list__eyebrow{color:rgba(255,255,255,.78);font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase}.v3-procedure-list__head h3{color:#fff;font-size:18px;font-weight:700;margin:1px 0 2px}.v3-procedure-list__head p{color:rgba(255,255,255,.84);font-size:11px;margin:0}
  .v3-procedure-list__create{align-items:center;background:rgba(255,255,255,.13);border:1px solid rgba(255,255,255,.55);border-radius:7px;color:#fff!important;cursor:pointer;display:inline-flex;font-size:11px;font-weight:700;height:35px;justify-content:center;padding:0 13px}.v3-procedure-list__create:hover{background:#fff;color:#08749b!important}.v3-procedure-list__create i{margin-right:6px}
  .v3-procedure-list__toolbar{align-items:end;background:#f7fbfd;border-bottom:1px solid #dbe8ee;display:grid;gap:12px;grid-template-columns:minmax(280px,560px) auto 1fr;padding:13px 18px}.v3-procedure-list__field label{color:#41677d;display:block;font-size:10px;font-weight:700;letter-spacing:.2px;margin:0 0 5px;text-transform:uppercase}.v3-procedure-list__field .select2-container{display:block;width:100%!important}.v3-procedure-list__field .select2-selection{border:1px solid #cbdde6!important;border-radius:7px!important;min-height:36px!important}.v3-procedure-list__field .select2-selection__rendered{color:#294f65!important;font-size:12px!important;line-height:34px!important}.v3-procedure-list__field .select2-selection__arrow{height:34px!important}
  .v3-procedure-list__search{align-items:center;background:#087ca7;border:0;border-radius:7px;color:#fff;cursor:pointer;display:inline-flex;font-size:11px;font-weight:700;height:36px;justify-content:center;padding:0 16px}.v3-procedure-list__search i{margin-right:6px}.v3-procedure-list__count{align-self:center;color:#688597;font-size:11px;justify-self:end}.v3-procedure-list__count strong{color:#087ca7}
  .v3-procedure-list__table-wrap{overflow-x:auto;width:100%}.v3-procedure-list__table{border-collapse:collapse;margin:0;min-width:760px;table-layout:fixed;width:100%}.v3-procedure-list__table th{background:#edf5f8;border-bottom:1px solid #d5e4eb;color:#456c83;font-size:10px;font-weight:700;padding:10px 13px;text-align:left;text-transform:uppercase}.v3-procedure-list__table td{border-bottom:1px solid #e5eef2;color:#3b5e73;font-size:11px;padding:11px 13px;vertical-align:middle}.v3-procedure-list__table tbody tr:hover{background:#f1fafb}.v3-procedure-list__name{color:#086f99;font-weight:700}.v3-procedure-list__finding{color:#58768a;display:block;line-height:1.4;max-height:46px;overflow:hidden;overflow-wrap:anywhere}.v3-procedure-list__group{background:#e4f6f3;border-radius:12px;color:#167c72;display:inline-block;font-size:10px;font-weight:700;padding:4px 8px}.v3-procedure-list__muted{color:#8a9ca7;font-size:10px}.v3-procedure-list__badge{border-radius:12px;display:inline-block;font-size:10px;font-weight:700;min-width:54px;padding:4px 8px;text-align:center}.v3-procedure-list__badge--yes,.v3-procedure-list__badge--active{background:#e1f6ea;color:#198153}.v3-procedure-list__badge--no{background:#edf2f5;color:#657d8b}.v3-procedure-list__badge--inactive{background:#fbe8e7;color:#b74642}
  .v3-procedure-list__edit{align-items:center;background:#fff7e8;border:1px solid #f0d18f;border-radius:6px;color:#99680f!important;cursor:pointer;display:inline-flex;font-size:11px;font-weight:700;height:30px;justify-content:center;min-width:76px;padding:0 10px}.v3-procedure-list__edit:hover{background:#ffefca}.v3-procedure-list__edit i{margin-right:5px}.v3-procedure-list__empty{color:#748d9d!important;padding:30px!important;text-align:center}.v3-procedure-list__empty i{color:#8fb0c2;display:block;font-size:25px;margin-bottom:7px}
  .v3-procedure-list__footer{align-items:center;background:#fbfdfe;display:flex;justify-content:space-between;min-height:48px;padding:8px 16px}.v3-procedure-list__footer .pagination{margin:0}.v3-procedure-list__footer .pagination>li>a,.v3-procedure-list__footer .pagination>li>span{border-color:#d4e3ea;color:#247b9e;font-size:11px;padding:5px 9px}.v3-procedure-list__footer .pagination>.active>span{background:#118eae;border-color:#118eae;color:#fff}
  @media(max-width:767px){.v3-procedure-list__head{align-items:flex-start;flex-direction:column;gap:12px}.v3-procedure-list__create{width:100%}.v3-procedure-list__toolbar{grid-template-columns:1fr}.v3-procedure-list__search{width:100%}.v3-procedure-list__count{justify-self:start}.v3-procedure-list__footer{align-items:flex-start;flex-direction:column;gap:8px}}
</style>
@php($isPaginated=is_object($tecnicas_quirurgicas) && method_exists($tecnicas_quirurgicas,'total'))
<section class="content v3-procedure-list">
  <div class="v3-procedure-list__card">
    <header class="v3-procedure-list__head">
      <div class="v3-procedure-list__title"><div class="v3-procedure-list__icon"><i class="fas fa-file-medical-alt" aria-hidden="true"></i></div><div><div class="v3-procedure-list__eyebrow">Catálogo clínico · V3</div><h3>Procedimientos completos</h3><p>Consulta y administra las plantillas clínicas disponibles.</p></div></div>
      <button type="button" class="v3-procedure-list__create" onclick="crear_plantilla_proc();"><i class="fas fa-plus" aria-hidden="true"></i>Nuevo procedimiento</button>
    </header>
    <form method="POST" id="proc_plantilla" class="v3-procedure-list__toolbar" onsubmit="buscar_plantilla_proc();return false;">
      {{csrf_field()}}
      <div class="v3-procedure-list__field"><label for="proc_com">Buscar procedimiento</label><select id="proc_com" class="form-control select2" name="proc_com"><option value="">Todos los procedimientos</option>@foreach($proc_completo as $value)<option value="{{$value->id}}" {{(string)$value->id===(string)$procedimiento_completo?'selected':''}}>{{$value->nombre_general}}</option>@endforeach</select></div>
      <button type="submit" class="v3-procedure-list__search"><i class="fas fa-search" aria-hidden="true"></i>Buscar</button>
      <div class="v3-procedure-list__count">
        @if($isPaginated)
          Mostrando <strong>{{$tecnicas_quirurgicas->firstItem()?:0}}–{{$tecnicas_quirurgicas->lastItem()?:0}}</strong> de <strong>{{$tecnicas_quirurgicas->total()}}</strong>
        @else
          <strong>{{$tecnicas_quirurgicas->count()}}</strong> procedimientos
        @endif
      </div>
    </form>
    <div class="v3-procedure-list__table-wrap">
      <table class="v3-procedure-list__table">
        <colgroup><col style="width:22%"><col style="width:37%"><col style="width:16%"><col style="width:8%"><col style="width:8%"><col style="width:9%"></colgroup>
        <thead><tr><th>Nombre</th><th>Hallazgo / técnica</th><th>Grupo</th><th>Récord</th><th>Estado</th><th>Acción</th></tr></thead>
        <tbody>
          @forelse($tecnicas_quirurgicas as $value)
            @php($hallazgo=trim(strip_tags($value->tecnica_quirurgica)))
            <tr>
              <td><span class="v3-procedure-list__name">{{$value->nombre_completo?:$value->nombre_general}}</span></td>
              <td><span class="v3-procedure-list__finding">{{$hallazgo!==''?$hallazgo:'Sin hallazgo o técnica registrada'}}</span></td>
              <td>@if(!is_null($value->id_grupo_procedimiento) && !is_null($value->grupo_procedimiento))<span class="v3-procedure-list__group">{{$value->grupo_procedimiento->nombre}}</span>@else<span class="v3-procedure-list__muted">Sin grupo</span>@endif</td>
              <td><span class="v3-procedure-list__badge {{$value->estado_anestesia?'v3-procedure-list__badge--yes':'v3-procedure-list__badge--no'}}">{{$value->estado_anestesia?'Sí':'No'}}</span></td>
              <td><span class="v3-procedure-list__badge {{$value->estado?'v3-procedure-list__badge--active':'v3-procedure-list__badge--inactive'}}">{{$value->estado?'Activo':'Inactivo'}}</span></td>
              <td><button type="button" class="v3-procedure-list__edit" onclick="editar_plantilla({{$value->id}});"><i class="fas fa-edit" aria-hidden="true"></i>Editar</button></td>
            </tr>
          @empty
            <tr><td colspan="6" class="v3-procedure-list__empty"><i class="fas fa-file-medical-alt" aria-hidden="true"></i>No se encontraron procedimientos con ese criterio.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($isPaginated)
      <footer class="v3-procedure-list__footer"><span>Resultados paginados para facilitar la revisión.</span><div class="procedure-pagination" data-search-mode="{{!is_null($procedimiento_completo)?'1':'0'}}">{!!$tecnicas_quirurgicas->appends(array('proc_com'=>$procedimiento_completo))->links()!!}</div></footer>
    @endif
  </div>
</section>
<script>
  $('.select2').select2({tags:false,width:'100%'});
  function cargarVistaProcedimientos(opciones){var area=$('#area_trabajo').css('opacity','.55').attr('aria-busy','true');return $.ajax(opciones).done(function(html){area.html(html);}).fail(function(){if(window.Swal){Swal.fire({icon:'error',title:'No se pudo cargar',text:'Inténtalo nuevamente.'});}else{alert('Error al cargar');}}).always(function(){area.css('opacity','1').attr('aria-busy','false');});}
  function buscar_plantilla_proc(){cargarVistaProcedimientos({type:'POST',url:"{{route('hc4/plantilla_proc.search')}}",data:$('#proc_plantilla').serialize()});}
  function editar_plantilla(id){cargarVistaProcedimientos({type:'GET',url:"{{route('hc4/plantilla_proc.edit')}}/"+id});}
  function crear_plantilla_proc(){cargarVistaProcedimientos({type:'POST',url:"{{route('hc4/plantilla_proc.create')}}",data:{_token:$('#proc_plantilla input[name="_token"]').val()}});}
  $('.procedure-pagination a').off('click.procedurePagination').on('click.procedurePagination',function(event){event.preventDefault();var link=$(this).attr('href'),pagination=$(this).closest('.procedure-pagination');if(pagination.data('search-mode')==1){var page=(link.match(/[?&]page=([^&]+)/)||[])[1]||1;cargarVistaProcedimientos({type:'POST',url:"{{route('hc4/plantilla_proc.search')}}",data:{_token:$('#proc_plantilla input[name="_token"]').val(),proc_com:$('#proc_com').val(),page:page}});}else{cargarVistaProcedimientos({type:'GET',url:link});}});
</script>
