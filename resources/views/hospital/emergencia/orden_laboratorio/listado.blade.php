<div class="hospital-lab-results" data-result-count="{{ $examenes_labs->count() }}" data-selected-count="{{ count($detalles_ch) }}">
    <p class="lab-catalog-summary">{{ $examenes_labs->count() }} exámenes mostrados · {{ count($detalles_ch) }} seleccionados en la orden.</p>
    @forelse($examenes_labs->groupBy(function ($exam) { return $exam->lnombre ?: 'Sin agrupador'; }) as $groupName => $exams)
        <section class="lab-exam-group">
            <h5>{{ $groupName }} <small>({{ $exams->count() }})</small></h5>
            <div class="lab-exam-grid">
                @foreach($exams as $exam)
                    @php $selected = in_array($exam->ex_id, $detalles_ch); @endphp
                    <label class="lab-exam-option {{ $selected ? 'is-selected' : '' }}">
                        <input type="checkbox" class="js-lab-exam" value="{{ $exam->ex_id }}" @if($selected) checked @endif @if((int) $orden->estado !== -1) disabled @endif>
                        <span>{{ $exam->descripcion ?: $exam->nombre }} <small>#{{ $exam->ex_id }} @if((int) $exam->estado !== 1 || (isset($exam->group_active) && (int) $exam->group_active !== 1)) · Inactivo @endif</small></span>
                    </label>
                @endforeach
            </div>
        </section>
    @empty
        <div class="lab-catalog-empty" role="status">No hay exámenes que coincidan con los filtros. Pruebe con parte del nombre o pulse «Limpiar filtros». Los perfiles son opcionales.</div>
    @endforelse
</div>
