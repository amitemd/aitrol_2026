<style>
    .evolution-editor {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 61, 91, .1);
        overflow: hidden;
    }

    .evolution-editor__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        background: linear-gradient(105deg, #064b7a 0%, #087db5 60%, #1db8c8 100%);
        color: #fff;
    }

    .evolution-editor__title {
        margin: 0;
        color: #fff;
        font-size: 18px;
        font-weight: 700;
    }

    .evolution-editor__subtitle {
        display: block;
        margin-top: 3px;
        color: rgba(255, 255, 255, .8);
        font-size: 12px;
    }

    .evolution-editor__back {
        border-color: rgba(255, 255, 255, .7) !important;
        color: #fff !important;
        white-space: nowrap;
    }

    .evolution-editor__body {
        padding: 20px !important;
    }

    .evolution-editor label {
        margin-bottom: 6px;
        color: #163c57;
        font-weight: 600;
    }

    .evolution-editor textarea,
    .evolution-editor input,
    .evolution-editor select {
        border-color: #cddde7;
        border-radius: 8px;
    }

    .evolution-editor textarea:focus,
    .evolution-editor input:focus,
    .evolution-editor select:focus {
        border-color: #159dbb;
        box-shadow: 0 0 0 3px rgba(21, 157, 187, .12);
    }

    .evolution-editor #form_evolucion .col-md-12 + .col-md-12 {
        margin-top: 14px;
    }

    .evolution-editor__section {
        padding: 26px 0;
        border-bottom: 1px solid #e4edf2;
    }

    .evolution-editor__section:first-child {
        padding-top: 0;
    }

    .evolution-editor__section:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .evolution-section-heading {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 18px;
    }

    .evolution-section-heading__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #e9f6fa;
        color: #087fa7;
        font-size: 15px;
    }

    .evolution-section-heading h4 {
        margin: 0;
        color: #123d59;
        font-size: 16px;
        font-weight: 700;
    }

    .evolution-section-heading small {
        display: block;
        margin-top: 2px;
        color: #718593;
    }

    .evolution-editor__save-row {
        justify-content: flex-end;
    }

    .evolution-editor__save {
        min-width: 140px;
        border-radius: 9px;
        font-weight: 700;
    }

    .evolution-editor .badge-danger {
        padding: 7px 10px;
        border-radius: 999px;
        white-space: normal;
    }

    .evolution-allergy-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 18px;
        padding: 11px 14px;
        border: 1px solid #f3c7c7;
        border-radius: 10px;
        background: #fff7f7;
    }

    .evolution-allergy-alert label {
        margin: 0;
    }

    .evolution-editor .table-responsive {
        border: 1px solid #e0eaf0;
        border-radius: 10px;
        overflow: hidden;
    }

    .evolution-editor table {
        margin-bottom: 0;
    }

    .evolution-editor table thead th {
        border-top: 0;
        background: #f1f7fa;
        color: #31566d;
        font-size: 11px;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .evolution-editor table td {
        vertical-align: middle;
    }

    .diagnosis-type {
        display: inline-flex;
        padding: 4px 8px;
        border-radius: 999px;
        background: #e8f4fb;
        color: #096c9e;
        font-size: 11px;
        font-weight: 700;
    }

    .evolution-icon-button {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 9px;
    }

    .evolution-empty-state {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 72px;
        border: 1px dashed #bfd0db;
        border-radius: 10px;
        background: #fbfdfe;
        color: #728591;
    }

    .evolution-medication-picker {
        align-items: flex-end;
        margin-bottom: 16px;
    }

    .evolution-medication-picker .select2-container {
        width: 100% !important;
    }

    #btn-agregar-medicina,
    #agregar_cie {
        min-height: 38px;
        border-radius: 8px;
        font-weight: 700;
    }

    .ui-autocomplete {
        z-index: 1100 !important;
        max-height: 260px;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 6px;
        border: 1px solid #cddde7;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 12px 30px rgba(16, 55, 79, .16);
        list-style: none;
    }

    .ui-autocomplete .ui-menu-item-wrapper {
        padding: 8px 10px;
        border-radius: 7px;
        color: #294f66;
        cursor: pointer;
    }

    .ui-autocomplete .ui-state-active {
        border: 0;
        background: #e8f5fa;
        color: #087aa5;
    }

    @media (max-width: 575.98px) {
        .evolution-editor__header {
            align-items: flex-start;
            flex-direction: column;
        }

        .evolution-editor__back,
        .evolution-editor__save {
            width: 100%;
        }
    }
</style>

<div class="card evolution-editor">
    <div class="evolution-editor__header">
        <div>
            <h3 class="evolution-editor__title">Evolución clínica No. {{ $evolucion->id }}</h3>
            <span class="evolution-editor__subtitle">Complete la evolución, el examen físico, diagnósticos y medicamentos.</span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-light evolution-editor__back" onclick="volver_listado_evoluciones();">
            <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver al listado
        </button>
    </div>
    <div class="card-body evolution-editor__body">
        <form id="form_evolucion" class="evolution-editor__section" method="POST">
            {{ csrf_field() }}
            <div class="evolution-section-heading">
                <span class="evolution-section-heading__icon"><i class="fa fa-stethoscope" aria-hidden="true"></i></span>
                <div><h4>Valoración clínica</h4><small>Registre el estado y los cambios observados en el paciente.</small></div>
            </div>
            <div class="row" style="padding-top: 10px;">
                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{$solicitud->id}}">

                <div class="col-md-12">
                    <label><b> {{trans('hospitalizacion.Motivo')}} </b></label>
                    <textarea name="motivo" id="motivo" class="form-control input-sm" rows="3" placeholder="Describa el motivo de la evolución">@if(!is_null($evolucion)){{$evolucion->motivo}}@endif</textarea>
                </div>               
                <div class="col-md-12">
                    <label><b> {{trans('hospitalizacion.Evolución')}} </b> </label>
                    <textarea name="n_evolucion" id="n_evolucion" class="form-control input-sm" rows="4" placeholder="Registre los cambios y la condición actual del paciente">@if(!is_null($evolucion)){{$evolucion->cuadro_clinico}}@endif</textarea>
                </div>
                <div class="col-md-12">
                    <label><b>{{trans('hospitalizacion.ExamenFisico')}}</b> </label>
                    <textarea name="examen_fisico" id="examen_fisico" class="form-control input-sm" rows="7">@if(!is_null($child_pugh)){{$child_pugh->examen_fisico}}@endif</textarea>
                </div>
            </div>
            <div class="row evolution-editor__save-row" style="padding-top: 14px;">
                <div class="col-12 col-sm-auto">
                    <button class="btn btn-primary evolution-editor__save" type="button" id="guardar_evolucion_btn" onclick="guardar_evolucion(this);">
                        <i class="fa fa-save" aria-hidden="true"></i> {{trans('hospitalizacion.Guardar')}}
                    </button>
                </div>
            </div>
        </form>
        <section class="evolution-editor__section">
            <div class="evolution-section-heading">
                <span class="evolution-section-heading__icon"><i class="fa fa-notes-medical" aria-hidden="true"></i></span>
                <div><h4>Diagnósticos</h4><small>Seleccione el código CIE-10 y su clasificación.</small></div>
            </div>
        <div class="evolution-allergy-alert">
            @php
                $nombresAlergias = [];
                foreach($solicitud->paciente->a_alergias as $alergia){
                    $principio_activo = $alergia->principio_activo;
                    if (is_null($principio_activo)) { continue; }
                    $nombresAlergias[] = $principio_activo->nombre;
                }
                $txt_al = count($nombresAlergias) > 0 ? implode(' • ', array_slice($nombresAlergias, 0, 3)) : 'No registra';
                if (count($nombresAlergias) > 3) { $txt_al .= ' y más'; }
            @endphp
            <label><b>{{trans('hospitalizacion.Alergias')}}:</b></label>
            <span class="badge badge-danger"> {{$txt_al}} </span>
        </div>
        <div>
            <form id="form_diagnostico">
                {{ csrf_field() }}
            <div class="row" style="padding-top: 10px;">
                <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$solicitud->id}}">
                <div class="col-md-6 mb-1">
                    <label >{{trans('paso2.Diagnostico')}}</label>
                    <input type="hidden" name="codigo" id="codigo" class="form-control input-sm">
                    <input type="text" name="cie10" id="cie10" class="form-control input-sm">
                </div>
                <div class="col-md-4 mb-1">
                    <label for="pre_def">Clasificación</label>
                    <select name="pre_def" id="pre_def" class="form-control"> 
                        <option value="">{{trans('paso2.Seleccione')}}</option>
                        <option value="PRESUNTIVO">{{trans('paso2.PRESUNTIVO')}}</option>
                        <option value="DEFINITIVO">{{trans('paso2.DEFINITIVO')}}</option>
                    </select>
                </div>
                <div class="col-md-2 mb-1 d-flex align-items-end">
                    <button name="agregar_cie" id="agregar_cie" type="button" class="btn btn-primary btn-sm btn-block"><i class="fa fa-plus" aria-hidden="true"></i> {{trans('paso2.Agregar')}}</button>
                </div>
                <br>
                <div class="form-group col-12" style="margin-bottom: 0;">
                    <div id="diagnosis-empty" class="evolution-empty-state">
                        <i class="fa fa-notes-medical" aria-hidden="true"></i>
                        <span>No hay diagnósticos registrados.</span>
                    </div>
                    <div id="diagnosis-table-wrapper" class="table-responsive" style="display: none;">
                        <table class="table table-hover" style="font-size: 12px;">
                            <thead><tr><th width="15%">Código</th><th>Diagnóstico</th><th width="18%">Clasificación</th><th width="10%">Acción</th></tr></thead>
                            <tbody id="tdiagnostico"></tbody>
                        </table>
                    </div>
                </div>
                
            </div>
            </form>
        </div>
        </section>
        <section class="evolution-editor__section">
            <div class="evolution-section-heading">
                <span class="evolution-section-heading__icon"><i class="fa fa-pills" aria-hidden="true"></i></span>
                <div><h4>Medicación</h4><small>Agregue medicamentos y complete cantidad y posología.</small></div>
            </div>
        <div>
            <form id="form_medicina">
                {{ csrf_field() }}
                @php
                    $form008 = $solicitud->form008->first();
                @endphp
                <input type="hidden" name="solicitud_id" id="solicitud_id" value="{{$solicitud->id}}">
                <input type="hidden" name="id_seguro" id="id_seguro" value="{{$solicitud->id_seguro_publico}}">
                <input type="hidden" name="contador" id="contador" value="1">
                <div class="form-group">
                    <div class="row evolution-medication-picker">
                        <div class="col-md-9 col-sm-8 col-12 mb-1">
                            <label for="generico" class="control-label">{{trans('paso2.Medicina')}}</label>
                            <select name="generico" id="generico" class="form-control select2" >
                                <option value="">{{trans('paso2.Seleccione')}}</option>
                            </select>
                            
                        </div>
                        <div class="col-md-3 col-sm-4 col-12 mb-1">
                            <button type="button" id="btn-agregar-medicina" class="btn btn-primary btn-block" onclick="buscar_nombre_medicina(this)" @if(is_null($receta)) disabled @endif>
                                <span class="fa fa-plus"></span> {{trans('paso2.Agregar')}}
                            </button>
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
                        </div>
                        <div class="col-md-12" id="tabla_detalle">
                            @if(count($detalles) > 0)
                            <div class="table-responsive"><table role="table" aria-busy="false" aria-colcount="4" class="table table-hover b-table">
                                <thead role="rowgroup" class="">
                                    <tr role="row" class="">
                                        <th width="20%" role="columnheader" scope="col" aria-colindex="1" class=""><div>{{trans('paso2.MEDICAMENTO')}}</div></th>
                                        <th width="10%" role="columnheader" scope="col" aria-colindex="2" class=""><div>{{trans('paso2.Cantidad')}}</div></th>
                                        <th width="55%" role="columnheader" scope="col" aria-colindex="3" class=""><div>{{trans('paso2.POSOLOGIA')}}</div></th>
                                        <th width="15%" role="columnheader" scope="col" aria-colindex="4" class=""><div>{{trans('paso2.ACCION')}}</div></th>
                                    </tr>
                                </thead>
                                <tbody role="rowgroup">
                                    @foreach($detalles as $detalle)
                                        @php
                                            $desabilitar ="";
                                            if($detalle->descargo == 1){
                                                $desabilitar = "disabled";
                                            }
                                        @endphp
                                        <tr role="row" class="">
                                            <td aria-colindex="1" role="cell" class="b-table-sticky-column"><span class="text-info">@if(!is_null($detalle)){{$detalle->nombre}}@endif</span></td>
                                            <td aria-colindex="2" role="cell" class=""><input class="form-control" min="1" type="number" name="cantidad{{$detalle->id}}" id="cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" {{$desabilitar}} onchange="editar_medicina('{{$detalle->id}}')" ></td>
                                            <td aria-colindex="3" role="cell" class="">
                                                <textarea wrap="soft" class="form-control" rows="3" name="dosis{{$detalle->id}}" id="dosis{{$detalle->id}}" {{$desabilitar}} onchange="editar_medicina('{{$detalle->id}}')">@if(!is_null($detalle)){{$detalle->dosis}}@endif</textarea>
                                            </td>
                                            <td aria-colindex="4" role="cell" class="">
                                                @if($detalle->descargo == 0)
                                                    <button id="del{{$detalle->id}}" type="button" class="btn btn-danger btn-sm evolution-icon-button" title="Eliminar medicina" onclick="eliminar_medicina('{{$detalle->id}}');">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @else
                                                    <span class="text-success">{{trans('paso2.Entregado')}}</span>
                                                @endif
                                                        
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table></div>
                            @else
                                <div class="evolution-empty-state"><i class="fa fa-prescription-bottle-alt" aria-hidden="true"></i><span>No hay medicinas agregadas.</span></div>
                            @endif
                        </div>
                                               
                    </div>
                </div>

            </form>
        </div>
        </section>

    </div>
</div>

<script src="{{ asset ('/js/jquery-ui.js') }}"></script>

<script type="text/javascript">

    function obtenerMensajeAjax(xhr, fallback){
        if (xhr && xhr.responseJSON) {
            return xhr.responseJSON.message || xhr.responseJSON.msn || fallback;
        }
        return fallback;
    }

    $('#cie10').autocomplete({
        source: function( request, response )
        {
            $.ajax({
                url:"{{route('epicrisis.cie10_nombre')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                      },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                }
            })
        },
        minLength: 2,
        select: function(event, ui) {
            $('#codigo').val(ui.item.id || '');
        }
    });


    $('#cie10').change( function()
        {
        var selectedText = $('#cie10').val().trim();
        var codeMatch = selectedText.match(/^\(([^)]+)\)/);
        if (codeMatch) {
            $('#codigo').val(codeMatch[1]);
            return;
        }
        $('#codigo').val('');
        $.ajax({
            type: 'post',
            url:"{{route('epicrisis.cie10_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            dataType: 'json',
            data: $('#cie10'),
            success: function(data){
                if(data!='0'){

                    $('#codigo').val(data.id);
                }
            },
            error: function(data){
            }
        })
    });

    $('#agregar_cie').click( function(){
        if ($('#cie10').val().trim() === '' || $('#codigo').val().trim() === '') {
            Swal.fire({icon: 'warning', title: 'Seleccione un diagnóstico', text: 'Escriba al menos dos caracteres y elija una opción del listado CIE-10.'});
            return;
        }
        if ($('#pre_def').val() === '') {
            Swal.fire({icon: 'warning', title: 'Falta el tipo', text: 'Seleccione Presuntivo o Definitivo.'});
            return;
        }
        guardar_cie10_consulta();
    });

    function guardar_cie10_consulta(){
        var $button = $('#agregar_cie');
        var originalHtml = $button.html();
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Agregando...');

        $.ajax({
         
            type: 'post',
            url:"{{route('formulario005.agregar_cie10')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            dataType: 'json',
            data:  $("#form_diagnostico").serialize(),
            success: function(data){
                if (data.estado === 'err') {
                    Swal.fire({icon: 'warning', title: 'No se agregó el diagnóstico', text: data.msn || 'Revise el diagnóstico seleccionado.'});
                    return;
                }
                if (!data.id || !data.cie10 || !data.descripcion) {
                    Swal.fire({icon: 'error', title: 'Respuesta incompleta', text: 'El servidor no devolvió los datos completos del diagnóstico.'});
                    return;
                }

                $('#codigo, #cie10').val('');
                $('#pre_def').val('');
                cargar_tabla_cie({{$solicitud->id_hcproc}});
                Swal.fire({icon: 'success', title: 'Diagnóstico agregado', timer: 1400, showConfirmButton: false});
            },
            error: function(xhr){
                Swal.fire({icon: 'error', title: 'No se pudo agregar', text: obtenerMensajeAjax(xhr, 'No fue posible registrar el diagnóstico.')});
            },
            complete: function(){
                $button.prop('disabled', false).html(originalHtml);
            }
        })
    }

    cargar_tabla_cie({{$solicitud->id_hcproc}});


    function cargar_tabla_cie(id_hcproc){
        $.ajax({
            url:`{{route('hospital.cargar_tabla_cie_hos')}}`,
            dataType: "json",
            type: 'get',
            data:{
                'id_hcproc' : id_hcproc 
            },
            success: function(data){
                var table = document.getElementById("tdiagnostico");
                table.innerHTML = '';

                $.each(data, function (index, value) {
                    var row = table.insertRow(index);
                    row.id = 'tdiag'+value.id;

                    var cell1 = row.insertCell(0);
                    cell1.setAttribute('data-label', 'Código');
                    var code = document.createElement('strong');
                    code.textContent = value.cie10 || '';
                    cell1.appendChild(code);

                    var cell2 = row.insertCell(1);
                    cell2.setAttribute('data-label', 'Diagnóstico');
                    cell2.textContent = value.descripcion || 'Sin descripción';

                    var cell3 = row.insertCell(2);
                    cell3.setAttribute('data-label', 'Tipo');
                    var type = document.createElement('span');
                    type.className = 'diagnosis-type';
                    type.textContent = value.pre_def || '';
                    cell3.appendChild(type);

                    var cell4 = row.insertCell(3);
                    cell4.setAttribute('data-label', 'Acción');
                    var remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'btn btn-sm btn-danger evolution-icon-button';
                    remove.title = 'Eliminar diagnóstico';
                    remove.innerHTML = '<i class="fa fa-trash"></i>';
                    remove.onclick = function(){ eliminar_ing(value.id, id_hcproc); };
                    cell4.appendChild(remove);
                });

                $('#diagnosis-empty').toggle(data.length === 0);
                $('#diagnosis-table-wrapper').toggle(data.length > 0);
            },
            error: function(xhr){
                Swal.fire({icon: 'error', title: 'No se cargaron los diagnósticos', text: obtenerMensajeAjax(xhr, 'Intente nuevamente.')});
            }
        })
    }

    function eliminar_ing(id, idHcproc){
        Swal.fire({
            icon: 'warning',
            title: '¿Eliminar diagnóstico?',
            text: 'Esta acción quitará el diagnóstico de la evolución.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result){
            if (!result.isConfirmed) return;
            $.ajax({
                type: 'DELETE',
                url: "{{url('cie10/eliminar')}}/"+id,
                headers: {'X-CSRF-TOKEN': $('input[name=_token]').first().val()},
                success: function(){
                    cargar_tabla_cie(idHcproc);
                    Swal.fire({icon: 'success', title: 'Diagnóstico eliminado', timer: 1200, showConfirmButton: false});
                },
                error: function(xhr){
                    Swal.fire({icon: 'error', title: 'No se pudo eliminar', text: obtenerMensajeAjax(xhr, 'Intente nuevamente.')});
                }
            });
        });
    }

    function volver_listado_evoluciones(){
        $('.hospitalization-workspace').removeClass('is-editor-open');
        if ($("#pasos").length && typeof evolucion === 'function') {
            evolucion();
            return;
        }
        $.ajax({
            type: 'get',
            url: "{{route('formulario005.f5_evolucion', ['id' => $solicitud->id])}}",
            dataType: 'html',
            success: function(datahtml){
                $("#content").html(datahtml);
            },
            error: function(){
                Swal.fire({icon: 'error', title: 'No se pudo volver al listado', text: 'Actualice la página e intente nuevamente.'});
            }
        });
    }

    function guardar_evolucion(button){
        var $button = $(button || '#guardar_evolucion_btn');
        var originalHtml = $button.html();
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            type: 'post',
            url:"{{route('formulario005.guardar_evolucion',['id_evol' => $evolucion->id])}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            dataType: 'html',
            data:  $("#form_evolucion").serialize(),
            success: function(data){
                Swal.fire({
                    icon: 'success',
                    title: `{{trans('proforma.GuardadoCorrectamente')}}`,
                    timer: 1800,
                    showConfirmButton: false
                });
            },
            error: function(xhr){
                var message = xhr.status === 419
                    ? 'La sesión expiró. Actualice la página e intente nuevamente.'
                    : 'No fue posible guardar la evolución. Revise los datos e intente nuevamente.';
                Swal.fire({icon: 'error', title: 'No se guardaron los cambios', text: message});
            },
            complete: function(){
                $button.prop('disabled', false).html(originalHtml);
            }
        })
    }


    $('.select2').select2({
    ajax: {
      url: '{{route("hospitalizacion.genericos")}}',
      data: function(params) {
        var query = {
          search: params.term,
          type: 'public'
        }
        return query;
      },
      processResults: function(data) {
        // Transforms the top-level key of the response object from 'items' to 'results'
        return {
          results: data
        };
      }

    }
  });

  function buscar_nombre_medicina(button){
    @if(is_null($receta))
      Swal.fire({icon: 'error', title: 'Receta no disponible', text: 'No existe una receta asociada a esta evolución.'});
      return;
    @endif
    var id_solicitud = $('#solicitud_id').val();
    var idGenerico = $('#generico').val();
    if (!idGenerico) {
      Swal.fire({icon: 'warning', title: 'Seleccione una medicina', text: 'Busque y seleccione una medicina antes de agregar.'});
      return;
    }
    var $button = $(button || '#btn-agregar-medicina');
    var originalHtml = $button.html();
    $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Agregando...');

      $.ajax({
        type: 'post',
        @if(!is_null($receta))
        url:"{{route('formulario005.f05_medicina_guardar',['id_rec' => $receta->id])}}",
        @endif
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        dataType: 'html',
        data: {
          'id_generico' : idGenerico,
          'id_solicitud': id_solicitud,
        },

          success: function(data){
            $('#tabla_detalle').html(data);
            $('#generico').val(null).trigger('change');
            Swal.fire({icon: 'success', title: 'Medicina agregada', timer: 1400, showConfirmButton: false});
            },
            error: function(xhr){
              Swal.fire({icon: 'error', title: 'No se pudo agregar', text: obtenerMensajeAjax(xhr, 'No fue posible registrar la medicina.')});
            },
            complete: function(){
              $button.prop('disabled', false).html(originalHtml);
            }
      })
  }

  function eliminar_medicina(id_detalle){
    var id_solicitud = $('#solicitud_id').val();
    Swal.fire({
      icon: 'warning',
      title: '¿Eliminar medicina?',
      text: 'La medicina se quitará de esta receta.',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(function(result){
      if (!result.isConfirmed) return;
      $.ajax({
          async: true,
          type: "DELETE",
          url: "{{url('hospital/formu_005/eliminar_medicina')}}/"+id_detalle,
          headers: {'X-CSRF-TOKEN': $('input[name=_token]').first().val()},
          data: {'id_solicitud': id_solicitud},
          dataType: "html",
          success: function(datahtml){
              $("#tabla_detalle").html(datahtml);
              Swal.fire({icon: 'success', title: 'Medicina eliminada', timer: 1200, showConfirmButton: false});
          },
          error: function(xhr){
              Swal.fire({icon: 'error', title: 'No se pudo eliminar', text: obtenerMensajeAjax(xhr, 'Intente nuevamente.')});
          }
      });
    });
  }

  function editar_medicina(id_detalle){

    $.ajax({
        type: 'post',
        url: "{{url('hospital/form005/editar_medicina')}}/"+id_detalle,
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        dataType: 'text',
        data: $("#form_medicina").serialize(),
        success: function(data){

        },
        error: function(xhr){
            Swal.fire({icon: 'error', title: 'No se guardó la medicina', text: obtenerMensajeAjax(xhr, 'Revise cantidad y posología.')});
        }
    });
  }





</script>

        

