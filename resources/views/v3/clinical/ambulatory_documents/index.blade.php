@include('v3.clinical.ambulatory_documents._styles')
<div class="ad-page">
  <section class="ad-hero">
    <div class="ad-hero__main"><div class="ad-hero__icon"><i class="fas fa-folder-open"></i></div><div><div class="ad-eyebrow">ATENCIÓN AL PACIENTE · AMBULATORIO</div><h1>Documentos ambulatorios</h1><p>Busca un paciente y revisa los documentos generados en cada consulta o procedimiento.</p></div></div>
  </section>
  <section class="ad-card">
    <div class="ad-card__head"><h2><i class="fas fa-search mr-2"></i>Buscar pacientes</h2><span class="ad-pill">Empresa activa</span></div>
    <div class="ad-card__body">
      <form class="ad-search" method="get" action="{{ route('v3.ambulatory-documents.index') }}">
        <input name="q" value="{{ $term }}" placeholder="Cédula, apellidos o nombres" autocomplete="off">
        <button class="ad-btn ad-btn--primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
        <a class="ad-btn" href="{{ route('v3.ambulatory-documents.index') }}"><i class="fas fa-eraser"></i> Limpiar</a>
      </form>
    </div>
  </section>
  <section class="ad-card">
    <div class="ad-card__head"><h2>Pacientes con consultas ambulatorias</h2><span class="ad-pill">{{ $patients->total() }} paciente(s)</span></div>
    <div class="table-responsive">
      <table class="ad-table"><thead><tr><th>Paciente</th><th>Identificación</th><th>Última atención</th><th>Consultas</th><th></th></tr></thead><tbody>
      @forelse($patients as $patient)
        <tr><td><span class="ad-name">{{ trim($patient->apellido1.' '.$patient->apellido2.' '.$patient->nombre1.' '.$patient->nombre2) }}</span></td><td>{{ $patient->id }}</td><td>{{ $patient->last_attention ? date('d/m/Y H:i', strtotime($patient->last_attention)) : 'Sin fecha' }}</td><td><span class="ad-pill">{{ $patient->consultation_count }}</span></td><td class="text-right"><a class="ad-btn ad-btn--primary" href="{{ route('v3.ambulatory-documents.patient', $patient->id) }}"><i class="fas fa-notes-medical"></i> Ver consultas</a></td></tr>
      @empty
        <tr><td colspan="5"><div class="ad-empty"><i class="fas fa-user-injured fa-2x mb-2"></i><br>No se encontraron pacientes con consultas ambulatorias.</div></td></tr>
      @endforelse
      </tbody></table>
    </div>
    @if($patients->hasPages())<div class="ad-card__body">{{ $patients->appends(['q' => $term])->links() }}</div>@endif
  </section>
</div>
