<style>
  .order012-page{padding:22px}.order012-hero{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:28px 30px;margin-bottom:18px;border-radius:18px;color:#fff;background:linear-gradient(115deg,#0b5e89,#138fb4 62%,#16aaa5);box-shadow:0 13px 30px rgba(20,91,122,.16)}
  .order012-hero__main{display:flex;align-items:center;gap:18px}.order012-hero__icon{display:grid;place-items:center;width:62px;height:62px;border:1px solid rgba(255,255,255,.3);border-radius:16px;background:rgba(255,255,255,.11);font-size:27px}.order012-hero h1{margin:2px 0 5px;font-size:28px;font-weight:800}.order012-hero p{margin:0;opacity:.94}.order012-eyebrow{font-size:12px;font-weight:800;letter-spacing:1.2px}
  .order012-card{overflow:hidden;border:1px solid #d4e5ed;border-radius:16px;background:#fff;box-shadow:0 8px 22px rgba(29,76,99,.06)}.order012-card__header{display:flex;align-items:center;gap:12px;padding:17px 20px;border-bottom:1px solid #d4e5ed;background:#eef9fc}.order012-card__header i{display:grid;place-items:center;width:38px;height:38px;border-radius:11px;color:#0788b7;background:#d9f3f8}.order012-card__header h2{margin:0;font-size:18px;font-weight:800;color:#0b3554}.order012-card__header p{margin:2px 0 0;color:#65839a;font-size:13px}
  .order012-filters{display:grid;grid-template-columns:repeat(5,minmax(150px,1fr)) auto;gap:13px;align-items:end;padding:20px}.order012-field label{display:block;margin-bottom:6px;color:#234b67;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.35px}.order012-field .form-control{height:40px;border-color:#c7dce6;border-radius:9px}.order012-actions{display:flex;gap:8px}.order012-actions .btn{height:40px;border-radius:9px;font-weight:700}
  .order012-table-wrap{padding:0 20px 20px;overflow:auto}.order012-table{min-width:980px;margin:0;border:1px solid #d6e3e9}.order012-table thead th{padding:12px;background:#f1f6f8;color:#173f5c;font-size:12px;text-transform:uppercase}.order012-table td{padding:13px 12px;vertical-align:middle;color:#294a61}.order012-patient{font-weight:800;color:#0b4266}.order012-meta{display:block;color:#7690a2;font-size:12px}.order012-empty{padding:45px 20px!important;text-align:center;color:#708da1}.order012-empty i{display:block;margin-bottom:10px;font-size:30px;color:#a9c6d4}.order012-row-actions{display:flex;gap:7px;white-space:nowrap}.order012-row-actions .btn{border-radius:8px}.order012-pagination{display:flex;justify-content:flex-end;padding:0 20px 18px}
  @media(max-width:1200px){.order012-filters{grid-template-columns:repeat(3,1fr)}}@media(max-width:720px){.order012-page{padding:12px}.order012-hero{padding:22px 18px}.order012-hero h1{font-size:23px}.order012-filters{grid-template-columns:1fr}.order012-actions{justify-content:stretch}.order012-actions .btn{flex:1}}
</style>

<div class="order012-page">
  <section class="order012-hero">
    <div class="order012-hero__main">
      <div class="order012-hero__icon"><i class="fas fa-file-medical-alt"></i></div>
      <div><div class="order012-eyebrow">ATENCIÓN CLÍNICA · V3</div><h1>Órdenes ingresadas – Formato 012</h1><p>Consulta, edita y descarga las solicitudes de diagnóstico por imagen.</p></div>
    </div>
  </section>

  <section class="order012-card">
    <header class="order012-card__header">
      <i class="fas fa-list-ul"></i><div><h2>Listado de órdenes</h2><p>Los resultados están limitados a la empresa activa.</p></div>
    </header>

    <form method="get" action="{{route('v3.order012.index')}}" class="order012-filters">
      <div class="order012-field"><label for="desde">Desde</label><input class="form-control" type="date" id="desde" name="desde" value="{{$filters['desde']}}"></div>
      <div class="order012-field"><label for="hasta">Hasta</label><input class="form-control" type="date" id="hasta" name="hasta" value="{{$filters['hasta']}}"></div>
      <div class="order012-field"><label for="cedula">Cédula</label><input class="form-control" id="cedula" name="cedula" value="{{$filters['cedula']}}" placeholder="Identificación"></div>
      <div class="order012-field"><label for="paciente">Paciente</label><input class="form-control" id="paciente" name="paciente" value="{{$filters['paciente']}}" placeholder="Nombres o apellidos"></div>
      <div class="order012-field"><label for="doctor">Médico</label><select class="form-control" id="doctor" name="doctor"><option value="">Todos</option>@foreach($doctors as $doctor)<option value="{{$doctor->id}}" {{$filters['doctor']==$doctor->id?'selected':''}}>{{$doctor->apellido1}} {{$doctor->apellido2!='(N/A)'?$doctor->apellido2:''}} {{$doctor->nombre1}}</option>@endforeach</select></div>
      <div class="order012-actions"><button class="btn btn-primary" type="submit"><i class="fas fa-search mr-1"></i> Buscar</button><a class="btn btn-outline-secondary" href="{{route('v3.order012.index')}}" title="Limpiar filtros"><i class="fas fa-eraser"></i></a></div>
    </form>

    <div class="order012-table-wrap">
      <table class="table table-hover order012-table">
        <thead><tr><th>Creación</th><th>Fecha de orden</th><th>Cédula</th><th>Paciente</th><th>Empresa</th><th>Médico</th><th>Acciones</th></tr></thead>
        <tbody>
          @forelse($orders as $order)
            <?php
              $patientName=trim($order->paciente_apellido1.' '.($order->paciente_apellido2=='(N/A)'?'':$order->paciente_apellido2).' '.$order->paciente_nombre1.' '.($order->paciente_nombre2=='(N/A)'?'':$order->paciente_nombre2));
              $doctorName=trim($order->doctor_apellido1.' '.($order->doctor_apellido2=='(N/A)'?'':$order->doctor_apellido2).' '.$order->doctor_nombre1);
            ?>
            <tr>
              <td>{{$order->created_at?date('d/m/Y H:i',strtotime($order->created_at)):'Sin fecha'}}</td>
              <td>{{$order->fecha_orden?date('d/m/Y H:i',strtotime($order->fecha_orden)):'Sin fecha'}}</td>
              <td><strong>{{$order->id_paciente}}</strong></td>
              <td><span class="order012-patient">{{$patientName?:'Paciente sin nombre'}}</span><span class="order012-meta">Orden #{{$order->id}}</span></td>
              <td>{{$order->empresa_nombre?:'Sin empresa registrada'}}</td>
              <td>{{$doctorName?:'Sin médico asignado'}}</td>
              <td><div class="order012-row-actions"><a class="btn btn-sm btn-outline-primary" href="{{route('v3.order012.edit',array('id'=>$order->id))}}"><i class="fas fa-edit mr-1"></i> Editar</a><a class="btn btn-sm btn-outline-success" href="{{route('v3.order012.export',array('id'=>$order->id))}}"><i class="fas fa-file-excel mr-1"></i> Descargar</a></div></td>
            </tr>
          @empty
            <tr><td colspan="7" class="order012-empty"><i class="fas fa-inbox"></i><strong>No hay órdenes para los filtros seleccionados.</strong><br>Amplía el rango de fechas o limpia la búsqueda.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="order012-pagination">{{$orders->appends($filters)->links()}}</div>
  </section>
</div>
