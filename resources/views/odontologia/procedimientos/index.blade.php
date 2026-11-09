<style>
    .odont-proc-shell { overflow: hidden; margin: 0; border: 1px solid #c9dee8; border-radius: 12px; background: linear-gradient(145deg, #fbfeff, #edf7fa); box-shadow: 0 8px 24px rgba(0,74,117,.08); }
    .odont-proc-header { min-height: 72px; padding: 12px 20px; background: linear-gradient(105deg, #07547e 0%, #087eae 68%, #16aaa5 100%); color: #fff; }
    .odont-proc-header h3 { margin: 6px 0; color: #fff; font-size: 18px; }
    #area_trabajo .odont-proc-add {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        margin: 0;
        padding: 8px 14px;
        border: 1px solid rgba(255,255,255,.42) !important;
        border-radius: 9px !important;
        color: #fff !important;
        background: rgba(255,255,255,.13) !important;
        box-shadow: none !important;
        font-size: 13px;
        font-weight: 600;
    }
    #area_trabajo .odont-proc-add:hover,
    #area_trabajo .odont-proc-add:focus { background: rgba(255,255,255,.24) !important; }
    .odont-proc-paciente { margin-top: 12px; text-align: center; font-size: 16px; }
    .odont-proc-list { max-height: 720px; overflow-y: auto; padding: 12px 18px; }
    .odont-proc-item { overflow: hidden; margin-bottom: 12px; border: 1px solid #c9dee8; border-radius: 10px; background: #fff; box-shadow: 0 5px 14px rgba(0,74,117,.08); }
    .odont-proc-resumen { position: relative; min-height: 84px; padding: 14px 58px 12px 14px; }
    .odont-proc-resumen-grid { display: grid; grid-template-columns: 1fr 1.6fr 1fr; gap: 18px; }
    .odont-proc-resumen strong { display: block; color: #1f2d3d; }
    .odont-proc-toggle { position: absolute; top: 10px; right: 12px; width: 38px; height: 38px; padding: 0; }
    .odont-proc-eliminar { margin-top: 8px; }
    .odont-proc-edicion { display: none; padding: 12px; border-top: 1px solid #d5e5ec; background: #f5fafc; }
    .odont-proc-vacio { padding: 70px 20px; text-align: center; color: #4d5d6c; background: #fff; }
    @media (max-width: 768px) {
        .odont-proc-resumen-grid { grid-template-columns: 1fr; gap: 8px; }
    }
</style>

<section class="odont-proc-shell">
    <header class="odont-proc-header">
        <div class="row">
            <div class="col-md-7">
                <h3><i class="fa fa-medkit"></i> PROCEDIMIENTOS ODONTOLOGICOS</h3>
            </div>
            <div class="col-md-5 text-right">
                <button type="button" class="btn odont-proc-add" onclick="odontologiaProcedimientoSeleccionar();">
                    <i class="fa fa-file-text-o"></i> AGREGAR PROCEDIMIENTO
                </button>
            </div>
        </div>
        <div class="odont-proc-paciente">PACIENTE: {{ strtoupper(trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2)) }}</div>
    </header>

    <div class="odont-proc-list">
        @forelse($procedimientos as $procedimiento)
            @php
                $detalles = $procedimiento->detalles_odont;
                $detalle = $detalles->first();
                $nombres = $detalles->pluck('procedimiento')->filter()->unique()->implode(' + ');
                $doctor = !is_null($detalle)
                    ? trim($detalle->doctor_nombre1.' '.$detalle->doctor_nombre2.' '.$detalle->doctor_apellido1.' '.$detalle->doctor_apellido2)
                    : '';
            @endphp
            <article class="odont-proc-item" id="odont-proc-item-{{ $procedimiento->id }}">
                <div class="odont-proc-resumen">
                    <div class="odont-proc-resumen-grid">
                        <div>
                            <strong>Fecha</strong>
                            {{ !is_null($detalle) && $detalle->fecha ? date('d/m/Y', strtotime($detalle->fecha)) : '-' }}
                            <div>
                                <button type="button" class="btn btn-danger btn-sm odont-proc-eliminar" onclick="odontologiaProcedimientoEliminar({{ $procedimiento->id }});">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                        <div>
                            <strong>Procedimiento</strong>
                            {{ $nombres !== '' ? $nombres : $procedimiento->nombre }}
                        </div>
                        <div>
                            <strong>Dr(a)</strong>
                            {{ $doctor !== '' ? $doctor : '-' }}
                        </div>
                    </div>
                    <button type="button" class="btn btn-info odont-proc-toggle" id="odont-proc-toggle-{{ $procedimiento->id }}" onclick="odontologiaProcedimientoEditar({{ $procedimiento->id }});" title="Abrir procedimiento">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                <div class="odont-proc-edicion" id="odont-proc-edicion-{{ $procedimiento->id }}"></div>
            </article>
        @empty
            <div class="odont-proc-vacio">
                <i class="fa fa-medkit fa-3x"></i>
                <p style="margin-top: 12px;">El paciente no tiene procedimientos odontologicos registrados.</p>
            </div>
        @endforelse
    </div>
</section>

<script>
    function odontologiaProcedimientoSeleccionar() {
        $.ajax({
            type: 'GET',
            url: "{{ route((isset($prefijoModulo) ? $prefijoModulo : 'odontologia').'.procedimientos.seleccionar', ['id_paciente' => $paciente->id]) }}",
            dataType: 'html',
            success: function (html) {
                $('#area_trabajo').html(html);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo abrir el selector de procedimientos.', 'error');
            }
        });
    }

    function odontologiaProcedimientoEditar(id) {
        var contenedor = $('#odont-proc-edicion-' + id);
        var boton = $('#odont-proc-toggle-' + id);
        var abierto = contenedor.is(':visible');

        if (typeof window.odontologiaCapturaActivaCerrar === 'function') {
            window.odontologiaCapturaActivaCerrar();
            window.odontologiaCapturaActivaCerrar = null;
        }
        $('.odont-proc-edicion').not(contenedor).slideUp(120).empty();
        $('.odont-proc-toggle i').removeClass('fa-minus').addClass('fa-plus');
        if (abierto) {
            contenedor.slideUp(120).empty();
            return;
        }

        boton.find('i').removeClass('fa-plus').addClass('fa-minus');
        contenedor.html('<div class="text-center" style="padding: 25px;"><i class="fa fa-spinner fa-spin"></i> Cargando...</div>').slideDown(120);
        $.ajax({
            type: 'GET',
            url: "{{ url(isset($prefijoModulo) ? $prefijoModulo : 'odontologia') }}/procedimientos/editar/" + id,
            dataType: 'html',
            success: function (html) {
                contenedor.html(html);
            },
            error: function (xhr) {
                var mensaje = xhr.status === 404
                    ? 'El procedimiento ya no está disponible.'
                    : 'No se pudo cargar el procedimiento (error ' + xhr.status + ').';
                contenedor.html('<div class="alert alert-danger">' + mensaje + '</div>');
            }
        });
    }

    function odontologiaProcedimientoEliminar(id) {
        Swal.fire({
            title: 'Eliminar procedimiento',
            text: 'El registro dejara de mostrarse en el historial odontologico.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (resultado) {
            if (!resultado.value && !resultado.isConfirmed) {
                return;
            }
            $.ajax({
                type: 'POST',
                url: "{{ url(isset($prefijoModulo) ? $prefijoModulo : 'odontologia') }}/procedimientos/eliminar/" + id,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                dataType: 'json',
                success: function () {
                    cargarProcedimientosOdontologicos();
                },
                error: function (xhr) {
                    var respuesta = xhr.responseJSON || {};
                    Swal.fire('Error', respuesta.mensaje || 'No se pudo eliminar el procedimiento.', 'error');
                }
            });
        });
    }

    @if($abrirId > 0)
        setTimeout(function () {
            if ($('#odont-proc-item-{{ $abrirId }}').length) {
                odontologiaProcedimientoEditar({{ $abrirId }});
            }
        }, 0);
    @endif
</script>
