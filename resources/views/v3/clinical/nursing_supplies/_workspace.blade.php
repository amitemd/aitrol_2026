<?php
    $activeProcedures = $procedures->where('estado', 1);
    $approvedPlans = $plans->filter(function ($plan) {
        return (int) $plan->aprobado === 1;
    });
    $openProcedures = $activeProcedures->filter(function ($procedure) use ($plans) {
        $plan = $plans->get($procedure->id);
        return !$plan || (int) $plan->aprobado !== 1;
    });
    $firstOpenProcedure = $openProcedures->first();
?>

<section class="co-card ns-command-card">
    <header class="co-head">
        <div class="co-heading">
            <i class="fas fa-route"></i>
            <div>
                <h2>Control de la atención</h2>
                <p>Selecciona la bodega y registra equipos e insumos sin salir de esta pantalla.</p>
            </div>
        </div>
        <div class="ns-workflow-badges">
            <span class="ns-status ns-status--success"><i class="fas fa-check-circle"></i> Atención realizada</span>
            <span class="ns-status {{$approvedPlans->count()?'ns-status--locked':'ns-status--open'}}">
                <i class="fas {{$approvedPlans->count()?'fa-lock':'fa-clipboard-list'}}"></i>
                {{$approvedPlans->count()}} de {{$activeProcedures->count()}} planilla(s) aprobada(s)
            </span>
        </div>
    </header>
    <div class="ns-command-grid">
        <form class="ns-warehouse-form js-ns-async" method="post" action="{{route('v3.nursing-supplies.warehouse.store',array('agenda'=>$agenda->id))}}" data-auto-submit="1" data-focus="{{$firstOpenProcedure?'supply-'.$firstOpenProcedure->id:'equipment'}}">
            {{csrf_field()}}
            <label for="nsWarehouse"><i class="fas fa-warehouse mr-1"></i> Bodega de descargo</label>
            <div class="ns-inline-control">
                <select id="nsWarehouse" class="form-control" name="id_bodega" required>
                    <option value="">Seleccione una bodega</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{$warehouse->id}}" {{(string)$selectedWarehouseId===(string)$warehouse->id?'selected':''}}>{{$warehouse->nombre}}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Guardar</button>
            </div>
            <small>{{$selectedWarehouseId?'La bodega está lista para registrar insumos.':'Debes seleccionar una bodega antes de escanear insumos.'}}</small>
        </form>
        <div class="ns-help">
            <span class="ns-help__icon"><i class="fas fa-barcode"></i></span>
            <div><strong>Registro rápido</strong><p>Escanea el código y presiona Enter. La información se actualizará sin recargar la página.</p></div>
        </div>
    </div>
</section>

@if(!$history)
<section class="co-card">
    <div class="co-empty ns-empty-state"><i class="fas fa-exclamation-triangle"></i><div><strong>Historia clínica pendiente</strong><span>La atención debe estar admitida antes de registrar equipos o insumos.</span></div></div>
</section>
@else
<div class="ns-two-columns">
    <section class="co-card ns-equipment-card">
        <header class="co-head">
            <div class="co-heading"><i class="fas fa-procedures"></i><div><h2>Equipos utilizados</h2><p>Equipos asociados a la atención del paciente.</p></div></div>
            <span class="co-count">{{$equipment->count()}} equipo(s)</span>
        </header>
        <div class="co-table-wrap">
            <table class="table co-table ns-table">
                <thead><tr><th>Código</th><th>Equipo</th><th>Fecha</th><th>Responsable</th><th class="text-right">Acción</th></tr></thead>
                <tbody>
                @forelse($equipment as $record)
                    <tr>
                        <td><span class="co-name">{{$record->equipo?$record->equipo->serie:'—'}}</span></td>
                        <td>{{$record->equipo?$record->equipo->nombre:'Equipo no disponible'}}</td>
                        <td>{{$record->created_at?date('d/m/Y H:i',strtotime($record->created_at)):'—'}}</td>
                        <td>{{trim(($record->usuario_crea?$record->usuario_crea->apellido1:'').' '.($record->usuario_crea?$record->usuario_crea->nombre1:''))?:'—'}}</td>
                        <td class="text-right">
                            <form class="js-ns-async js-ns-confirm" method="post" action="{{route('v3.nursing-supplies.equipment.destroy',array('agenda'=>$agenda->id,'equipmentHistory'=>$record->id))}}" data-confirm="¿Eliminar este equipo de la atención?">
                                {{csrf_field()}}{{method_field('DELETE')}}
                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar equipo"><i class="fas fa-trash-alt mr-1"></i> Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="co-empty">No se han registrado equipos.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <form class="ns-scan-bar js-ns-async" method="post" action="{{route('v3.nursing-supplies.equipment.store',array('agenda'=>$agenda->id))}}" data-focus="equipment">
            {{csrf_field()}}
            <label class="sr-only" for="nsEquipmentCode">Código o serie del equipo</label>
            <input id="nsEquipmentCode" class="form-control" name="codigo" maxlength="100" autocomplete="off" placeholder="Escanea o escribe la serie del equipo" autofocus required>
            <button class="btn btn-primary" type="submit"><i class="fas fa-plus-circle mr-1"></i> Registrar equipo</button>
        </form>
    </section>

    <section class="co-card ns-observation-card">
        <header class="co-head">
            <div class="co-heading"><i class="fas fa-comment-medical"></i><div><h2>Observaciones</h2><p>Notas de enfermería asociadas a esta atención.</p></div></div>
        </header>
        <form class="ns-observation js-ns-async" method="post" action="{{route('v3.nursing-supplies.observation.update',array('agenda'=>$agenda->id))}}">
            {{csrf_field()}}{{method_field('PATCH')}}
            <textarea class="form-control" name="observacion" maxlength="5000" rows="7" placeholder="Escribe una observación clínica o administrativa…">{{$history->observaciones_enfermeria}}</textarea>
            <div class="ns-observation__footer"><small>Máximo 5000 caracteres</small><button class="btn btn-success" type="submit"><i class="fas fa-save mr-1"></i> Guardar observación</button></div>
        </form>
    </section>
</div>

<div class="ns-section-title">
    <div><span class="ns-section-title__icon"><i class="fas fa-boxes"></i></span><div><h2>Insumos por procedimiento</h2><p>Cada procedimiento conserva su propia planilla, bodega y trazabilidad.</p></div></div>
    <span class="co-count">{{$procedures->count()}} procedimiento(s)</span>
</div>

@forelse($procedures as $procedure)
<?php
    $items = $supplies->get($procedure->id, collect());
    $names = $finalNames->get($procedure->id, collect());
    $plan = $plans->get($procedure->id);
    $locked = $plan && (int) $plan->aprobado === 1;
    $procedureTitle = $names->count() ? $names->pluck('nombre')->filter()->implode(' + ') : ($procedure->nombre_general ?: 'Procedimiento clínico registrado');
    $grouped = $items->groupBy(function ($item) {
        $movement = $item->movimiento;
        $product = $movement ? $movement->producto : null;
        return $product && isset($product->tipo) ? (int) $product->tipo : 0;
    });
    $totalQuantity = $items->sum(function ($item) {
        return $item->cantidad ?: 1;
    });
?>
<section class="co-card ns-procedure {{$locked?'ns-procedure--locked':''}}" data-procedure="{{$procedure->id}}">
    <header class="co-head ns-procedure__head">
        <div class="co-heading">
            <i class="fas fa-stethoscope"></i>
            <div>
                <h2>{{$procedureTitle}}</h2>
                <p>Procedimiento #{{$procedure->id}} @if((int)$procedure->estado===0)<span class="ns-inline-alert">Inactivado por el médico</span>@endif</p>
            </div>
        </div>
        <div class="ns-head-actions">
            <span class="ns-plan-state {{$locked?'ns-plan-state--locked':'ns-plan-state--open'}}">
                <i class="fas {{$locked?'fa-lock':'fa-edit'}}"></i>
                <span><strong>{{$locked?'Planilla guardada':'Planilla abierta'}}</strong><small>{{$locked?'No se puede modificar':$items->count().' registro(s) · '.$totalQuantity.' unidad(es)'}}</small></span>
            </span>
            <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="{{route('hc4.imprimir_etiquetas_frascos',array('id'=>$procedure->id))}}"><i class="fas fa-barcode mr-1"></i> Imprimir etiquetas</a>
        </div>
    </header>

    @forelse($grouped as $category=>$categoryItems)
    <div class="ns-category">
        <div class="ns-category__title"><span>{{isset($categoryLabels[$category])?$categoryLabels[$category]:$categoryLabels[0]}}</span><small>{{$categoryItems->count()}} registro(s)</small></div>
        <div class="co-table-wrap">
            <table class="table co-table ns-table">
                <thead><tr><th>Serie / código</th><th>Insumo</th><th>Cantidad</th><th>Bodega</th><th>Fecha</th><th>Responsable</th>@if(!$locked)<th class="text-right">Acciones</th>@endif</tr></thead>
                <tbody>
                @foreach($categoryItems as $item)
                <?php $movement=$item->movimiento; $product=$movement?$movement->producto:null; $user=$item->usuario_crea; $warehouse=$item->bodega; ?>
                    <tr>
                        <td><span class="co-name">{{$movement?$movement->serie:'—'}}</span><span class="co-meta">{{$product?$product->codigo:''}}</span></td>
                        <td>{{$product?$product->nombre:'Producto no disponible'}}</td>
                        <td><span class="ns-quantity">{{$item->cantidad?:1}}</span></td>
                        <td>{{$warehouse?$warehouse->nombre:'Sin bodega'}}</td>
                        <td>{{$item->created_at?date('d/m/Y H:i',strtotime($item->created_at)):'—'}}</td>
                        <td>{{trim(($user?$user->apellido1:'').' '.($user?$user->nombre1:''))?:'—'}}</td>
                        @if(!$locked)
                        <td><div class="ns-row-actions">
                            @if($procedures->where('estado',1)->count()>1)
                            <form class="js-ns-async" method="post" action="{{route('v3.nursing-supplies.supply.move',array('agenda'=>$agenda->id,'procedure'=>$procedure->id,'movement'=>$item->id))}}">
                                {{csrf_field()}}{{method_field('PATCH')}}
                                <select class="form-control form-control-sm" name="target_procedure" required aria-label="Procedimiento de destino">
                                    <option value="">Mover a…</option>
                                    @foreach($procedures as $target)
                                        @if((int)$target->estado===1 && (int)$target->id!==(int)$procedure->id)<option value="{{$target->id}}">Procedimiento #{{$target->id}}</option>@endif
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-warning" type="submit" title="Mover insumo"><i class="fas fa-exchange-alt"></i></button>
                            </form>
                            @endif
                            <form class="js-ns-async js-ns-confirm" method="post" action="{{route('v3.nursing-supplies.supply.destroy',array('agenda'=>$agenda->id,'procedure'=>$procedure->id,'movement'=>$item->id))}}" data-confirm="¿Retirar este insumo de la atención y liberar su compromiso de inventario?">
                                {{csrf_field()}}{{method_field('DELETE')}}
                                <button class="btn btn-sm btn-outline-danger" type="submit" title="Eliminar insumo"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div></td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
        <div class="co-empty ns-empty"><i class="fas fa-box-open fa-2x d-block mb-2"></i>Este procedimiento todavía no registra insumos.</div>
    @endforelse

    @if(!$locked && (int)$procedure->estado===1)
    <form class="ns-scan-bar ns-scan-bar--supply js-ns-async" method="post" action="{{route('v3.nursing-supplies.supply.store',array('agenda'=>$agenda->id,'procedure'=>$procedure->id))}}" data-focus="supply-{{$procedure->id}}">
        {{csrf_field()}}
        <input type="hidden" name="id_bodega" value="{{$selectedWarehouseId}}">
        <label class="sr-only" for="nsSupply{{$procedure->id}}">Código del insumo</label>
        <input id="nsSupply{{$procedure->id}}" class="form-control" name="codigo" maxlength="100" autocomplete="off" placeholder="Escanea una serie o escribe el código del insumo" {{$selectedWarehouseId?'':'disabled'}} required>
        <button class="btn btn-primary" type="submit" {{$selectedWarehouseId?'':'disabled'}}><i class="fas fa-plus-circle mr-1"></i> Registrar insumo</button>
        @if(!$selectedWarehouseId)<small><i class="fas fa-info-circle mr-1"></i>Selecciona primero la bodega de descargo.</small>@endif
    </form>
    @elseif(!$locked)
        <div class="ns-readonly-note"><i class="fas fa-ban"></i> El procedimiento está inactivo; sus registros se conservan únicamente para consulta.</div>
    @endif
</section>
@empty
<section class="co-card"><div class="co-empty ns-empty-state"><i class="fas fa-exclamation-circle"></i><div><strong>Sin procedimientos</strong><span>La atención no tiene procedimientos clínicos vinculados.</span></div></div></section>
@endforelse
@endif
