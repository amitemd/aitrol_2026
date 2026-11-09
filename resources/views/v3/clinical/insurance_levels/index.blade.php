@include('v3.clinical.operations._styles')
@php($groupNames = array(0 => 'Público', 1 => 'Grupo', 2 => 'Convenio', 3 => 'Promoción'))
<div class="co-page il-page">
    <section class="co-hero">
        <div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-layer-group"></i></div><div><div class="co-eyebrow">CONFIGURACIÓN CLÍNICA · V3</div><h1>Niveles de seguro</h1><p>Crea niveles y asígnalos a los seguros de la empresa activa.</p></div></div>
        <a class="btn btn-light" href="{{route('form_enviar_seguro.index')}}"><i class="fas fa-arrow-left"></i> Volver a seguros</a>
    </section>

    <div id="il-feedback" class="alert {{session('success')?'alert-success':'alert-danger'}}" role="status" @if(!session('success')&&!$errors->any()) hidden @endif>@if(session('success'))<i class="fas fa-check-circle mr-1"></i>{{session('success')}}@elseif($errors->any())<strong>Revisa los datos:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul>@endif</div>
    <div id="il-workspace">
    <div class="il-note"><i class="fas fa-info-circle"></i><span>El catálogo de niveles es compartido. Cada vínculo con seguro, descuento y estado pertenece exclusivamente a la empresa activa.</span></div>

    <div class="il-grid">
        <section class="co-card il-card">
            <header><span><i class="fas {{$editingLevel?'fa-edit':'fa-plus'}}"></i></span><div><h2>{{$editingLevel?'Editar nivel':'Crear nivel'}}</h2><p>Define el nombre que se mostrará en el recibo.</p></div></header>
            <form class="js-il-form" method="post" action="{{$editingLevel?route('v3.insurance-levels.levels.update',array('level'=>$editingLevel->id)):route('v3.insurance-levels.levels.store')}}">
                {{csrf_field()}}@if($editingLevel){{method_field('PATCH')}}@endif
                <div class="form-group"><label>Nombre</label><input class="form-control" name="nombre" maxlength="255" required value="{{old('nombre',$editingLevel?$editingLevel->nombre:'')}}" placeholder="Ej. Nivel 1"></div>
                <div class="il-two"><div class="form-group"><label>Nombre corto</label><input class="form-control" name="nombre_corto" maxlength="40" value="{{old('nombre_corto',$editingLevel?$editingLevel->nombre_corto:'')}}" placeholder="Ej. N1"></div><div class="form-group"><label>Grupo</label><select class="form-control" name="grupo">@foreach($groupNames as $key=>$label)<option value="{{$key}}" {{(string)old('grupo',$editingLevel?$editingLevel->grupo:2)===(string)$key?'selected':''}}>{{$label}}</option>@endforeach</select></div></div>
                <div class="form-group"><label>Estado</label><select class="form-control" name="estado"><option value="1" {{(string)old('estado',$editingLevel?$editingLevel->estado:1)==='1'?'selected':''}}>Activo</option><option value="0" {{(string)old('estado',$editingLevel?$editingLevel->estado:1)==='0'?'selected':''}}>Inactivo</option></select></div>
                <div class="il-actions"><button class="btn btn-primary"><i class="fas fa-save"></i> {{$editingLevel?'Guardar cambios':'Crear nivel'}}</button>@if($editingLevel)<a class="btn btn-light" href="{{route('v3.insurance-levels.index')}}">Cancelar</a>@endif</div>
            </form>
        </section>

        <section class="co-card il-card">
            <header><span><i class="fas {{$editingAssignment?'fa-link':'fa-plus'}}"></i></span><div><h2>{{$editingAssignment?'Editar asignación':'Asignar nivel a seguro'}}</h2><p>Configura el nivel para la empresa activa.</p></div></header>
            <form class="js-il-form" method="post" action="{{$editingAssignment?route('v3.insurance-levels.assignments.update',array('assignment'=>$editingAssignment->id)):route('v3.insurance-levels.assignments.store')}}">
                {{csrf_field()}}@if($editingAssignment){{method_field('PATCH')}}@endif
                <div class="form-group"><label>Seguro / convenio</label><select class="form-control" name="id_seguro" required><option value="">Seleccione...</option>@foreach($insurances as $insurance)<option value="{{$insurance->id}}" {{(string)old('id_seguro',$editingAssignment?$editingAssignment->id_seguro:'')===(string)$insurance->id?'selected':''}}>{{$insurance->nombre}}</option>@endforeach</select></div>
                <div class="form-group"><label>Nivel</label><select class="form-control" name="id_nivel" required><option value="">Seleccione...</option>@foreach($levels as $level)<option value="{{$level->id}}" {{(string)old('id_nivel',$editingAssignment?$editingAssignment->id_nivel:'')===(string)$level->id?'selected':''}}>{{$level->nombre}}{{$level->estado?'':' · Inactivo'}}</option>@endforeach</select></div>
                <div class="il-two"><div class="form-group"><label>Descuento (%)</label><input class="form-control" type="number" min="0" max="100" step="0.01" name="descuento" value="{{old('descuento',$editingAssignment?$editingAssignment->descuento:0)}}" required></div><div class="form-group"><label>Estado</label><select class="form-control" name="estado"><option value="1" {{(string)old('estado',$editingAssignment?$editingAssignment->estado:1)==='1'?'selected':''}}>Activa</option><option value="0" {{(string)old('estado',$editingAssignment?$editingAssignment->estado:1)==='0'?'selected':''}}>Inactiva</option></select></div></div>
                <div class="il-actions"><button class="btn btn-primary"><i class="fas fa-save"></i> {{$editingAssignment?'Guardar cambios':'Asignar nivel'}}</button>@if($editingAssignment)<a class="btn btn-light" href="{{route('v3.insurance-levels.index')}}">Cancelar</a>@endif</div>
            </form>
        </section>
    </div>

    <section class="co-card il-list">
        <header><div><h2>Asignaciones de la empresa</h2><p>Niveles disponibles en los recibos según el seguro seleccionado.</p></div><span class="il-count">{{$assignments->count()}} registros</span></header>
        <div class="table-responsive"><table class="table"><thead><tr><th>Seguro</th><th>Nivel</th><th>Descuento</th><th>Estado</th><th>Acción</th></tr></thead><tbody>@forelse($assignments as $item)<tr><td><strong>{{$item->seguro_nombre}}</strong></td><td>{{$item->nivel_nombre}}</td><td>{{number_format($item->descuento,2)}}%</td><td><span class="badge {{$item->estado?'badge-success':'badge-secondary'}}">{{$item->estado?'Activa':'Inactiva'}}</span></td><td><a class="btn btn-sm btn-outline-primary" href="{{route('v3.insurance-levels.index',array('editar_asignacion'=>$item->id))}}"><i class="fas fa-edit"></i> Editar</a></td></tr>@empty<tr><td colspan="5" class="il-empty">No hay niveles asignados a seguros para esta empresa.</td></tr>@endforelse</tbody></table></div>
    </section>

    <section class="co-card il-list">
        <header><div><h2>Catálogo de niveles</h2><p>Niveles compartidos disponibles para asignar.</p></div><span class="il-count">{{$levels->count()}} registros</span></header>
        <div class="table-responsive"><table class="table"><thead><tr><th>Nombre</th><th>Nombre corto</th><th>Grupo</th><th>Estado</th><th>Acción</th></tr></thead><tbody>@forelse($levels as $level)<tr><td><strong>{{$level->nombre}}</strong></td><td>{{$level->nombre_corto?:'—'}}</td><td>{{$groupNames[(int)$level->grupo]}}</td><td><span class="badge {{$level->estado?'badge-success':'badge-secondary'}}">{{$level->estado?'Activo':'Inactivo'}}</span></td><td><a class="btn btn-sm btn-outline-primary" href="{{route('v3.insurance-levels.index',array('editar_nivel'=>$level->id))}}"><i class="fas fa-edit"></i> Editar</a></td></tr>@empty<tr><td colspan="5" class="il-empty">No hay niveles creados.</td></tr>@endforelse</tbody></table></div>
    </section>
    </div>
</div>
<style>
.il-page{padding:13px}.il-note{display:flex;gap:9px;align-items:center;margin-bottom:12px;padding:10px 13px;border:1px solid #cce3ea;border-radius:10px;background:#f1fafc;color:#416779;font-size:11px}.il-note i{color:#0787a7;font-size:16px}.il-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}.il-card,.il-list{overflow:hidden}.il-card header,.il-list>header{display:flex;align-items:center;gap:10px;padding:12px 15px;border-bottom:1px solid #dae8ed;background:#f1f9fb}.il-card header>span{display:grid;place-items:center;width:34px;height:34px;border-radius:9px;background:#d9f2f6;color:#0784a5}.il-card h2,.il-list h2{margin:0;color:#153e58;font-size:15px;font-weight:800}.il-card p,.il-list p{margin:2px 0 0;color:#6e8693;font-size:10px}.il-card form{padding:15px}.il-page label{color:#31556a;font-size:10.5px;font-weight:800}.il-page .form-control{min-height:37px;border-color:#c8dce4;border-radius:8px;box-shadow:none}.il-two{display:grid;grid-template-columns:1fr 1fr;gap:10px}.il-actions{display:flex;gap:7px}.il-page .btn{border-radius:8px;font-weight:700}.il-list{margin-bottom:14px}.il-list>header{justify-content:space-between}.il-count{padding:5px 9px;border-radius:99px;background:#daf2f6;color:#087d9c;font-size:9.5px;font-weight:800}.il-list .table{margin:0;font-size:11px}.il-list th{border-top:0;background:#f7fafb;color:#496978;font-size:9.5px;text-transform:uppercase}.il-list td,.il-list th{padding:9px 12px;vertical-align:middle}.il-empty{text-align:center;color:#718894;padding:26px!important}@media(max-width:850px){.il-grid{grid-template-columns:1fr}.co-hero{align-items:flex-start;flex-direction:column}}
</style>
<script>
(function(){
    var pageUrl=@json(route('v3.insurance-levels.index'));
    function feedback(message, success) {
        var box=document.getElementById('il-feedback');
        if(!box)return;
        box.className='alert '+(success?'alert-success':'alert-danger');
        box.innerHTML=(success?'<i class="fas fa-check-circle mr-1"></i>':'<i class="fas fa-exclamation-triangle mr-1"></i>')+message;
        box.hidden=false;
        box.scrollIntoView({behavior:'smooth',block:'nearest'});
    }
    function refreshWorkspace(){
        return fetch(pageUrl,{credentials:'same-origin',headers:{Accept:'text/html','X-Requested-With':'XMLHttpRequest'}})
            .then(function(response){if(!response.ok)throw new Error('No se pudo actualizar el listado.');return response.text();})
            .then(function(html){
                var doc=new DOMParser().parseFromString(html,'text/html');
                var fresh=doc.getElementById('il-workspace'),current=document.getElementById('il-workspace');
                if(!fresh||!current)throw new Error('No se pudo actualizar el contenido.');
                current.replaceWith(fresh);
            });
    }
    document.addEventListener('submit',function(event){
        var form=event.target.closest('.js-il-form');
        if(!form||!window.fetch)return;
        event.preventDefault();
        var button=form.querySelector('button[type="submit"],button:not([type])');
        if(button)button.disabled=true;
        form.classList.add('il-saving');
        fetch(form.action,{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body:new FormData(form)})
            .then(function(response){return response.json().catch(function(){return {};}).then(function(data){if(!response.ok)throw data;return data;});})
            .then(function(data){return refreshWorkspace().then(function(){feedback(data.message||'Cambios guardados correctamente.',true);});})
            .catch(function(error){
                var messages=[];
                if(error.errors)Object.keys(error.errors).forEach(function(key){messages=messages.concat(error.errors[key]);});
                feedback(messages.join(' ')||error.message||'No se pudo guardar. Revisa los datos.',false);
            }).then(function(){if(button)button.disabled=false;form.classList.remove('il-saving');});
    });
})();
</script>
