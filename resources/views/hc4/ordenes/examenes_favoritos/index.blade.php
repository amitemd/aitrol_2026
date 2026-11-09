@include('hc4.partials.catalog_list_styles')
<div class="row v3-clinical-list">
  <div class="col-12" id="xarea_compartida">
    <div class="v3-clinical-list__card">
      <header class="v3-clinical-list__head">
        <div class="v3-clinical-list__title">
          <div class="v3-clinical-list__icon"><i class="fas fa-clipboard-check" aria-hidden="true"></i></div>
          <div><div class="v3-clinical-list__eyebrow">Laboratorio clínico · V3</div><h3>Lista de exámenes favoritos</h3><p>Consulta y administra tus grupos de exámenes reutilizables.</p></div>
        </div>
        <div class="v3-clinical-list__actions"><button type="button" class="v3-clinical-list__button" onclick="crear_favorito()"><i class="fas fa-plus" aria-hidden="true"></i>Nuevo favorito</button></div>
      </header>
      <form method="POST" id="proc_plantilla" class="v3-clinical-list__toolbar" onsubmit="buscar_favorito();return false;">
        {{ csrf_field() }}
        <div class="v3-clinical-list__field"><label for="nombre_favorito">Buscar por nombre</label><div class="v3-clinical-list__input"><i class="fas fa-search" aria-hidden="true"></i><input id="nombre_favorito" class="form-control" type="search" name="nombre" value="{{$nombre}}" placeholder="Escriba el nombre del favorito" autocomplete="off"></div></div>
        <button type="submit" class="v3-clinical-list__search"><i class="fas fa-search" aria-hidden="true"></i>Buscar</button>
        <div class="v3-clinical-list__count"><strong>{{$protocolos->count()}}</strong> favoritos</div>
      </form>
      <div class="v3-clinical-list__table-wrap">
        <table id="example2_fav" class="v3-clinical-list__table dataTable" cellspacing="0" width="100%">
          <thead><tr><th width="50%">Nombre</th><th width="30%">Cantidad</th><th width="20%">Acción</th></tr></thead>
          <tbody>
            @forelse ($protocolos as $value)
              <tr><td><span class="v3-clinical-list__name">{{$value->nombre}}</span></td><td><span class="v3-clinical-list__badge">{{$value->examenes->count()}}</span></td><td><button type="button" class="v3-clinical-list__action v3-clinical-list__action--edit" onclick="editar_favoritos({{$value->id}})"><i class="fas fa-edit" aria-hidden="true"></i>Editar</button><button type="button" class="v3-clinical-list__action v3-clinical-list__action--view" onclick="ver_favoritos({{$value->id}})"><i class="fas fa-eye" aria-hidden="true"></i>Ver</button></td></tr>
            @empty
              <tr><td colspan="3" class="v3-clinical-list__empty"><i class="fas fa-clipboard-check" aria-hidden="true"></i>No se encontraron listas de exámenes favoritos.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <footer class="v3-clinical-list__footer"><span>Total de registros</span><strong>{{$protocolos->count()}}</strong></footer>
    </div>
  </div>
</div>

<style>
  #modal_ver_favorito{z-index:2147483647!important}#modal_ver_favorito .modal-dialog{margin:24px auto;max-width:1500px;width:92%}#modal_ver_favorito .modal-content{border:0;border-radius:12px;box-shadow:0 18px 55px rgba(12,58,83,.28);overflow:hidden}#modal_ver_favorito .modal-header{align-items:center;background:linear-gradient(116deg,#08719c 0%,#118eae 60%,#17aaa5 100%);border:0;color:#fff;display:flex;justify-content:space-between;padding:14px 18px}#modal_ver_favorito .modal-title{align-items:center;display:flex;font-size:16px;font-weight:700;gap:8px;margin:0;text-align:left}#modal_ver_favorito .close{color:#fff;float:none;font-size:24px;margin:0 0 0 18px;opacity:.9;order:2;text-shadow:none}#modal_ver_favorito .modal-body{background:#f7fbfd;max-height:78vh;overflow:auto;padding:14px}body.modal-open>.modal-backdrop.v3-favorite-modal-backdrop{z-index:2147483600!important}
</style>
<div class="modal fade" id="modal_ver_favorito" tabindex="-1" role="dialog" aria-labelledby="modal_ver_favorito_titulo" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-header"><h4 class="modal-title" id="modal_ver_favorito_titulo"><i class="fas fa-clipboard-check" aria-hidden="true"></i><span>Detalle de exámenes favoritos</span></h4><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div><div class="modal-body" id="modal_ver_favorito_contenido"><div class="v3-clinical-list__empty"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i>Cargando perfil...</div></div></div></div>
</div>

 <script type="text/javascript">
    
    $(document).ready(function(){

      $('body > #modal_ver_favorito').remove();
      $('#modal_ver_favorito').appendTo('body').on('show.bs.modal',function(){window.setTimeout(function(){$('.modal-backdrop').last().addClass('v3-favorite-modal-backdrop');},0);});

      $('.select2').select2({
        tags: false
      });

      $('#example2_fav').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : true
      });

  
      $(".breadcrumb").append('<li class="active">Procedimientos</li>');
 
    });

  function buscar_favorito(){
   	//alert("entro");
      $.ajax({
        type: 'post',
        url:"{{route('hc4_examenes.buscar')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: $("#proc_plantilla").serialize(),
        success: function(data){
        	//console.log(data);
          $("#area_trabajo").html(data);
          //console.log(data);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
  }

	function crear_favorito(){
   	//alert("entro");
    $.ajax({
      type: 'get',
      url:"{{route('hc4_examenes.crear')}}",
      datatype: 'json',
      success: function(data){
        $("#area_trabajo").html(data);
        //console.log(data);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function editar_favoritos(id){
    $.ajax({
      type: 'get',
      url:"{{url('hc4/laboratorio/examenes/favoritos/editar')}}/" + id,
      datatype: 'json',
      success: function(data){
        $("#area_trabajo").html(data);
        //console.log(data);
      },
      error:  function(){
        alert('error al cargar');
      }
    });
  }

  function ver_favoritos(id){
    $('#modal_ver_favorito_contenido').html('<div class="v3-clinical-list__empty"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i>Cargando perfil...</div>');
    $('#modal_ver_favorito').modal('show');
    $.ajax({
      type: 'get',
      url:"{{url('hc4/laboratorio/examenes/favoritos/ver')}}/"+id,
      datatype: 'json',
      success: function(data){
        $('#modal_ver_favorito_contenido').html(data);
      },
      error:  function(xhr){
        $('#modal_ver_favorito_contenido').html('<div class="alert alert-danger">No se pudo cargar el perfil seleccionado.</div>');
        if(typeof mostrarErrorAmbulatorio==='function'){mostrarErrorAmbulatorio(xhr,'cargar el perfil favorito');}
      }
    });  
  }

  </script>
