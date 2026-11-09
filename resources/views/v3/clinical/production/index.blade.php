@include('v3.clinical.operations._styles')
@include('v3.clinical.production._styles')
@php($months = array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'))
<div class="co-page pp-page">
  <section class="co-hero">
    <div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-chart-line"></i></div><div><div class="co-eyebrow">ANÁLISIS CLÍNICO · V3</div><h1>Producción de médicos</h1><p>Consulta órdenes de procedimientos, valores y distribución por tipo de cobertura.</p></div></div>
    <span class="btn btn-light"><i class="far fa-calendar-alt mr-1"></i> {{$year}}</span>
  </section>

  <div class="pp-summary">
    <div class="pp-stat"><i class="fas fa-clipboard-check"></i><strong>{{number_format($totals['orders'])}}</strong><span>Órdenes registradas</span></div>
    <div class="pp-stat"><i class="fas fa-landmark"></i><strong>{{number_format($totals['public_orders'])}}</strong><span>Cobertura pública</span></div>
    <div class="pp-stat"><i class="fas fa-building"></i><strong>{{number_format($totals['private_orders'])}}</strong><span>Cobertura privada</span></div>
    <div class="pp-stat"><i class="fas fa-user"></i><strong>{{number_format($totals['particular_orders'])}}</strong><span>Atención particular</span></div>
    <div class="pp-stat"><i class="fas fa-dollar-sign"></i><strong>${{number_format($totals['value'],2)}}</strong><span>Valor total</span></div>
  </div>

  <section class="co-card">
    <header class="co-head"><div class="co-heading"><i class="fas fa-chart-bar"></i><div><h2>Producción mensual</h2><p>Valores asociados a la empresa activa y al año seleccionado.</p></div></div><span class="co-count">{{$rows->count()}} mes(es) con actividad</span></header>
    <div class="pp-toolbar">
      <form method="get" action="{{route('v3.procedure-production.index')}}"><div><label for="production-year">Año</label><select id="production-year" class="form-control" name="anio">@forelse($years as $availableYear)<option value="{{$availableYear}}" {{$year===$availableYear?'selected':''}}>{{$availableYear}}</option>@empty<option value="{{$year}}">{{$year}}</option>@endforelse</select></div><button class="btn btn-primary" type="submit"><i class="fas fa-filter mr-1"></i> Consultar</button></form>
      <p class="pp-note"><i class="fas fa-info-circle mr-1"></i> Las cantidades cuentan órdenes únicas; los valores corresponden a los rubros vinculados a cada orden.</p>
    </div>
    <div class="pp-table-wrap"><table class="table pp-table"><thead><tr><th>Mes</th><th>Público</th><th>Privado</th><th>Particular</th><th>Total órdenes</th><th>Valor público</th><th>Valor privado</th><th>Valor particular</th><th>Valor total</th><th>Detalle</th></tr></thead><tbody>
      @forelse($rows as $row)<tr><td><span class="pp-month"><b>{{str_pad($row->mes,2,'0',STR_PAD_LEFT)}}</b>{{$months[(int)$row->mes]}}</span></td><td>{{number_format($row->public_orders)}}</td><td>{{number_format($row->private_orders)}}</td><td>{{number_format($row->particular_orders)}}</td><td><strong>{{number_format($row->orders_total)}}</strong></td><td class="pp-money">${{number_format($row->public_value,2)}}</td><td class="pp-money">${{number_format($row->private_value,2)}}</td><td class="pp-money">${{number_format($row->particular_value,2)}}</td><td class="pp-total">${{number_format($row->value_total,2)}}</td><td><button class="btn btn-outline-primary pp-detail-btn js-production-detail" type="button" data-month="{{$months[(int)$row->mes]}}" data-url="{{route('v3.procedure-production.doctors',array('year'=>$year,'month'=>$row->mes))}}"><i class="fas fa-user-md mr-1"></i> Por médico</button></td></tr>
      @empty<tr><td colspan="10" class="pp-empty"><i class="fas fa-chart-line fa-2x d-block mb-2"></i>No existen valores de producción para {{$year}} en la empresa activa.</td></tr>@endforelse
    </tbody></table></div>
    @if($rows->count())<div class="pp-chart"><div class="pp-chart-inner"><canvas id="procedureProductionChart" aria-label="Gráfico de producción mensual"></canvas></div></div>@endif
  </section>

  <section class="co-card pp-detail" id="productionDoctorDetail" aria-live="polite"><header class="co-head"><div class="co-heading"><i class="fas fa-user-md"></i><div><h2>Producción por médico</h2><p id="productionDetailSubtitle">Detalle del mes seleccionado.</p></div></div><button class="btn btn-sm btn-light" id="closeProductionDetail" type="button"><i class="fas fa-times mr-1"></i> Cerrar</button></header><div id="productionDetailContent"></div></section>
</div>
<script>
(function(){
  var detail=document.getElementById('productionDoctorDetail'),content=document.getElementById('productionDetailContent'),subtitle=document.getElementById('productionDetailSubtitle');
  document.addEventListener('click',function(event){var button=event.target.closest('.js-production-detail');if(!button)return;detail.classList.add('is-visible');subtitle.textContent='Detalle de '+button.getAttribute('data-month')+' de {{$year}}.';content.innerHTML='<div class="pp-detail-loading"><i class="fas fa-circle-notch fa-spin mr-1"></i> Cargando producción médica…</div>';detail.scrollIntoView({behavior:'smooth',block:'start'});fetch(button.getAttribute('data-url'),{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(function(response){if(!response.ok)throw new Error('request');return response.text();}).then(function(html){content.innerHTML=html;}).catch(function(){content.innerHTML='<div class="alert alert-danger pp-detail-error">No se pudo cargar el detalle. Intenta nuevamente.</div>';});});
  document.getElementById('closeProductionDetail').addEventListener('click',function(){detail.classList.remove('is-visible');content.innerHTML='';});
  @if($rows->count())var canvas=document.getElementById('procedureProductionChart');if(canvas&&window.Chart){new Chart(canvas.getContext('2d'),{type:'bar',data:{labels:{!!json_encode($rows->map(function($row)use($months){return $months[(int)$row->mes];})->values())!!},datasets:[{label:'Órdenes',data:{!!json_encode($rows->pluck('orders_total')->map(function($value){return (int)$value;})->values())!!},backgroundColor:'rgba(11,159,194,.72)',borderColor:'#087fbd',borderWidth:1}]},options:{responsive:true,maintainAspectRatio:false,legend:{display:false},scales:{yAxes:[{ticks:{beginAtZero:true,precision:0},gridLines:{color:'rgba(32,83,108,.08)'}}],xAxes:[{gridLines:{display:false}}]}}});}@endif
})();
</script>
