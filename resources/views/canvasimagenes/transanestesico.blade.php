<div>
    <style>
        .drawing-color-picker {
            display: inline-block;
            position: relative;
            margin-left: 6px;
            vertical-align: middle;
        }

        .drawing-color-trigger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 34px;
            padding: 0 10px;
            border: 0;
            border-radius: 4px;
            color: #fff;
            background: #0d6efd;
            cursor: pointer;
        }

        .drawing-color-current {
            width: 18px;
            height: 18px;
            border: 2px solid #fff;
            border-radius: 50%;
        }

        .drawing-color-menu {
            display: none;
            position: absolute;
            z-index: 20;
            top: calc(100% + 4px);
            left: 0;
            padding: 6px;
            white-space: nowrap;
            border: 1px solid #d7e2ec;
            border-radius: 4px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .18);
        }

        .drawing-color-picker:hover .drawing-color-menu,
        .drawing-color-picker.is-open .drawing-color-menu {
            display: flex;
            gap: 5px;
        }

        .drawing-color {
            width: 26px;
            height: 26px;
            padding: 0;
            border: 2px solid transparent;
            border-radius: 50%;
            cursor: pointer;
        }

        .drawing-color.is-active {
            border-color: #0d6efd;
        }

        .canvas-toolbar {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }

        .canvas-editor-scroll {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #d7e7ef;
            border-radius: 8px;
            background: #fff;
        }

        .canvas-editor-hint {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 8px 0;
            padding: 6px 9px;
            border-left: 3px solid #149bad;
            color: #496c80;
            background: #f2f9fa;
            font-size: 11px;
        }
    </style>
    <input type="hidden" id="id" value="{{$id}}">


    <input type="hidden" id="tipo" value="{{$tipo}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div style="display: flex;">
        <div class="col-auto canvas-toolbar">
            <button type="button" id="drawing-mode" class="btn btn-primary" title="Activar o desactivar dibujo"><i class="fa fa-pencil"></i> Dibujar</button>

            <div id="drawing-color-picker" class="drawing-color-picker">
                <button type="button" id="drawing-color-trigger" class="drawing-color-trigger" title="Seleccionar color" aria-label="Seleccionar color" aria-expanded="false">
                    <span id="drawing-color-current" class="drawing-color-current" style="background: #000000;"></span>
                    <i class="fa fa-paint-brush"></i>
                </button>
                <div id="drawing-color-menu" class="drawing-color-menu" aria-label="Colores disponibles">
                    <button type="button" class="drawing-color is-active" data-color="#000000" title="Negro" aria-label="Color negro" style="background: #000000;"></button>
                    <button type="button" class="drawing-color" data-color="#dc3545" title="Rojo" aria-label="Color rojo" style="background: #dc3545;"></button>
                    <button type="button" class="drawing-color" data-color="#0d6efd" title="Azul" aria-label="Color azul" style="background: #0d6efd;"></button>
                    <button type="button" class="drawing-color" data-color="#198754" title="Verde" aria-label="Color verde" style="background: #198754;"></button>
                    <button type="button" class="drawing-color" data-color="#ffc107" title="Amarillo" aria-label="Color amarillo" style="background: #ffc107;"></button>
                </div>
            </div>

            <button type="button" id="delete_select" class="btn btn-primary"> <i class="fa fa-trash"></i> {{trans('etodos.BorrarSeleccionado')}} </button>

            <button type="button" id="clear-canvas" class="btn btn-warning"> <i class="fa fa-remove"></i> {{trans('etodos.Borrar')}}</button>

            <button type="button" class="btn btn-success" onclick="saveCan()"> <i class="fa fa-save"></i> {{trans('etodos.Guardar')}}</button>
        </div>
    </div>
</div>
<p class="canvas-editor-hint"><i class="fa fa-hand-pointer-o"></i> Arrastre directamente un signo desde la sección <b>SIMBOLOGÍA</b> del formulario hacia la cuadrícula.</p>
<br>

<div class="canvas-editor-scroll">
    <canvas id="c" width="1450px" height="600px"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    var canvas = new fabric.Canvas('c', {
        isDrawingMode: false
    });
    var selectedCanvasObjects = [];

    // Conserva la selección aunque el botón quite el foco del canvas.
    canvas.on('selection:created', function(event) {
        selectedCanvasObjects = event.selected || (event.target ? [event.target] : []);
    });
    canvas.on('selection:updated', function(event) {
        selectedCanvasObjects = event.selected || (event.target ? [event.target] : []);
    });
    canvas.on('selection:cleared', function() {
        // No limpiar aquí: el botón puede provocar este evento al tomar el foco.
    });

    var tipoValue = document.getElementById('tipo').value;

    var tipo = parseInt(tipoValue);

    @php
        $savedCanvasPath = (int) $tipo === 0 ? $hc_anes->tran_anastesico_path : $hc_anes->post_anastesico_path;
        $savedCanvasFolders = (int) $tipo === 0 ? ['transanestesico', 'trasnanestesico'] : ['postanestesico'];
        $defaultCanvasImage = (int) $tipo === 0 ? 'assets/images/record_transanestesico.png' : 'assets/images/record_postanestesico.png';
        $defaultCanvasVersion = file_exists(public_path($defaultCanvasImage)) ? filemtime(public_path($defaultCanvasImage)) : time();
        $defaultCanvasUrl = asset($defaultCanvasImage).'?v='.$defaultCanvasVersion;
        $savedCanvasFolder = $savedCanvasFolders[0];
        foreach ($savedCanvasFolders as $candidateFolder) {
            if (!empty($savedCanvasPath) && file_exists(public_path('assets/images/'.$candidateFolder.'/'.$savedCanvasPath))) {
                $savedCanvasFolder = $candidateFolder;
                break;
            }
        }
        $localCanvasExists = !empty($savedCanvasPath) && file_exists(public_path('assets/images/'.$savedCanvasFolder.'/'.$savedCanvasPath));
        $initialCanvasUrl = !empty($savedCanvasPath)
            ? ($localCanvasExists ? asset('assets/images/'.$savedCanvasFolder.'/'.$savedCanvasPath) : asset('trans_record/'.$savedCanvasPath))
            : $defaultCanvasUrl;
        $canvasJsonName = !empty($savedCanvasPath) ? pathinfo($savedCanvasPath, PATHINFO_FILENAME).'.json' : null;
        $canvasJsonExists = $canvasJsonName && file_exists(public_path('assets/images/'.$savedCanvasFolder.'/'.$canvasJsonName));
        $canvasJsonUrl = $canvasJsonExists ? asset('assets/images/'.$savedCanvasFolder.'/'.$canvasJsonName) : null;
    @endphp

    var initialCanvasUrl = @json($initialCanvasUrl);
    var defaultCanvasUrl = @json($defaultCanvasUrl);
    var editableCanvasUrl = @json($canvasJsonUrl);
    var canvasReady = false;
    var canvasDirty = false;
    var canvasRevision = 0;
    var autoSaveTimer = null;
    var canvasSaveChain = Promise.resolve();
    var canvasAutosaveSuspended = false;

    function setCanvasBackground(url, callback) {
        fabric.Image.fromURL(url, function(img) {
            canvas.setBackgroundImage(img, function() {
                canvas.renderAll();
                if (callback) callback();
            }, {
                scaleX: canvas.width / img.width,
                scaleY: canvas.height / img.height
            });
        });
    }

    function configureEditableCanvasObjects() {
        canvas.getObjects().forEach(function(object) {
            if (!object.clinicalSymbol) return;

            object.set({
                hasControls: true,
                fontSize: object.symbolCode === 'torniquete' ? 11 : 14,
                padding: 2,
                cornerStyle: 'circle',
                cornerSize: 6,
                cornerColor: '#0b93ad',
                cornerStrokeColor: '#ffffff',
                transparentCorners: false,
                lockUniScaling: true,
                minScaleLimit: 0.5
            });
            object.setControlsVisibility({
                mt: false,
                mb: false,
                ml: false,
                mr: false,
                mtr: false,
                tl: true,
                tr: true,
                bl: true,
                br: true
            });
            object.setCoords();
        });
    }
            
    function ensureLegacyOxygenLegend() {
        if (tipo !== 0 || canvas.getObjects().some(function(object) { return object.templateLegend; })) {
            return false;
        }

        var scaleX = canvas.getWidth() / 1437;
        var scaleY = canvas.getHeight() / 448;
        canvas.add(new fabric.Text('OXIGENO LITROSXMINUTOS', {
            left: 161.5 * scaleX,
            top: 38.5 * scaleY,
            originX: 'center',
            originY: 'center',
            fontFamily: 'Arial Narrow, Arial, sans-serif',
            fontSize: 6.5 * scaleY,
            fontWeight: 'bold',
            fill: '#000000',
            selectable: false,
            evented: false,
            hasControls: false,
            templateLegend: true
        }));
        return true;
    }

    function finishCanvasLoad(shouldAutosave) {
        configureEditableCanvasObjects();
        canvas.renderAll();
        canvasReady = true;
        if (shouldAutosave) scheduleCanvasAutosave();
    }

    function loadCanvasState() {
        if (!editableCanvasUrl) {
            var legacyFlattenedCanvas = tipo === 0 && initialCanvasUrl !== defaultCanvasUrl;
            setCanvasBackground(initialCanvasUrl, function() {
                var legendAdded = legacyFlattenedCanvas ? ensureLegacyOxygenLegend() : false;
                finishCanvasLoad(legendAdded);
            });
            return;
        }

        fetch(editableCanvasUrl, { credentials: 'same-origin' })
            .then(function(response) {
                if (!response.ok) throw new Error('No se pudo cargar el estado editable');
                return response.json();
            })
            .then(function(serializedCanvas) {
                canvas.loadFromJSON(serializedCanvas, function() {
                    var backgroundSource = serializedCanvas.backgroundImage && serializedCanvas.backgroundImage.src
                        ? serializedCanvas.backgroundImage.src
                        : '';
                    var usesBaseTransanestheticTemplate = tipo === 0 && backgroundSource.indexOf('record_transanestesico.png') !== -1;

                    if (usesBaseTransanestheticTemplate) {
                        canvas.getObjects().filter(function(object) {
                            return object.templateLegend;
                        }).forEach(function(object) {
                            canvas.remove(object);
                        });
                        setCanvasBackground(defaultCanvasUrl, function() {
                            finishCanvasLoad(false);
                        });
                        return;
                    }

                    var legendAdded = ensureLegacyOxygenLegend();
                    finishCanvasLoad(legendAdded);
                });
            })
            .catch(function() {
                var legacyFlattenedCanvas = tipo === 0 && initialCanvasUrl !== defaultCanvasUrl;
                setCanvasBackground(initialCanvasUrl, function() {
                    var legendAdded = legacyFlattenedCanvas ? ensureLegacyOxygenLegend() : false;
                    finishCanvasLoad(legendAdded);
                });
            });
    }

    loadCanvasState();

    fabric.Object.prototype.transparentCorners = false;
    var a = function(id) {
        return document.getElementById(id);
    };
    var drawingModeEl = a('drawing-mode'),
        drawingOptionsEl = a('drawing-mode-options'),
        drawingColorEl = a('drawing-color'),
        drawingShadowColorEl = a('drawing-shadow-color'),
        drawingLineWidthEl = a('drawing-line-width'),
        drawingShadowWidth = a('drawing-shadow-width'),
        drawingShadowOffset = a('drawing-shadow-offset'),
        clearEl = a('clear-canvas');

    clearEl.onclick = function() {
        canvasAutosaveSuspended = true;
        canvas.clear();
        selectedCanvasObjects = [];

        var blankCanvasUrl = defaultCanvasUrl;

        fabric.Image.fromURL(blankCanvasUrl, function(img) {
            canvas.setBackgroundImage(img, function() {
                canvas.discardActiveObject();
                canvas.requestRenderAll();
                canvasAutosaveSuspended = false;
                scheduleCanvasAutosave();
            }, {
                scaleX: canvas.width / img.width,
                scaleY: canvas.height / img.height
            });
        });
    };
    var clxz = a('delete_select');
    drawingModeEl.onclick = function() {
        canvas.isDrawingMode = !canvas.isDrawingMode;
        canvas.selection = !canvas.isDrawingMode;
        if (canvas.isDrawingMode) {
            drawingModeEl.innerHTML = '<i class="fa fa-stop"> </i> Detener dibujo';
            drawingModeEl.className = 'btn btn-warning';
        } else {
            drawingModeEl.innerHTML = '<i class="fa fa-pencil"> </i> Dibujar';
            drawingModeEl.className = 'btn btn-primary';

        }
    };
    // Un trazo de 1 px desaparece al reducir el lienzo de 1450 px dentro del PDF.
    // 2.5 px conserva precisión clínica y sigue siendo legible en el 018A impreso.
    canvas.freeDrawingBrush.width = 2.5;
    canvas.freeDrawingBrush.color = '#000000';

    var clinicalSymbols = {
        inicio_anestesia: { glyph: '↓', label: 'Inicio anestesia' },
        induccion: { glyph: '×', label: 'Inducción' },
        inicio_cirugia: { glyph: '⊙', label: 'Inicio cirugía' },
        fin_cirugia: { glyph: '⊙', label: 'Fin de cirugía' },
        fin_anestesia: { glyph: '⊗', label: 'Fin de anestesia' },
        tas: { glyph: '∨', label: 'TAS' },
        tad: { glyph: '∧', label: 'TAD' },
        tam: { glyph: '×', label: 'TAM' },
        frecuencia_cardiaca: { glyph: '●', label: 'Frecuencia cardíaca' },
        temperatura: { glyph: '△', label: 'Temperatura' },
        pvc: { glyph: '†', label: 'PVC' },
        respiracion_espontanea: { glyph: '○', label: 'Respiración espontánea' },
        respiracion_asistida: { glyph: '⊘', label: 'Respiración asistida' },
        respiracion_controlada: { glyph: '●', label: 'Respiración controlada' },
        torniquete: { glyph: 'T°', label: 'Torniquete' },
        feto: { glyph: '♀♂', label: 'Feto' }
    };

    function stopDrawingMode() {
        canvas.isDrawingMode = false;
        canvas.selection = true;
        drawingModeEl.innerHTML = '<i class="fa fa-pencil"></i> Dibujar';
        drawingModeEl.className = 'btn btn-primary';
    }

    function addClinicalSymbol(symbolCode, position) {
        var definition = clinicalSymbols[symbolCode];
        if (!definition) return;

        stopDrawingMode();
        var symbol = new fabric.Text(definition.glyph, {
            left: position && Number.isFinite(position.x) ? position.x : canvas.getWidth() / 2,
            top: position && Number.isFinite(position.y) ? position.y : canvas.getHeight() / 2,
            originX: 'center',
            originY: 'center',
            fontFamily: 'Arial',
            fontSize: symbolCode === 'torniquete' ? 11 : 14,
            fontWeight: symbolCode === 'tam' ? 'normal' : 'bold',
            fill: '#111111',
            clinicalSymbol: true,
            symbolCode: symbolCode,
            hasControls: true,
            hasRotatingPoint: false,
            lockScalingFlip: true,
            lockUniScaling: true,
            minScaleLimit: 0.5,
            borderColor: '#0b93ad',
            padding: 2,
            cornerStyle: 'circle',
            cornerSize: 6,
            cornerColor: '#0b93ad',
            cornerStrokeColor: '#ffffff',
            transparentCorners: false
        });

        symbol.setControlsVisibility({
            mt: false,
            mb: false,
            ml: false,
            mr: false,
            mtr: false,
            tl: true,
            tr: true,
            bl: true,
            br: true
        });

        canvas.add(symbol);
        canvas.setActiveObject(symbol);
        canvas.requestRenderAll();
        return symbol;
    }

    // La leyenda forma parte de la imagen oficial. Estas zonas convierten
    // cada fila impresa en un origen de arrastre sin agregar otra paleta.
    var legendSymbolRows = tipo === 0 ? [
        { code: 'inicio_anestesia', y: 188 },
        { code: 'induccion', y: 199 },
        { code: 'inicio_cirugia', y: 210 },
        { code: 'fin_cirugia', y: 221 },
        { code: 'fin_anestesia', y: 232 },
        { code: 'tas', y: 243 },
        { code: 'tad', y: 254 },
        { code: 'tam', y: 265 },
        { code: 'frecuencia_cardiaca', y: 276 },
        { code: 'temperatura', y: 287 },
        { code: 'pvc', y: 298 },
        { code: 'respiracion_espontanea', y: 309 },
        { code: 'respiracion_asistida', y: 320 },
        { code: 'respiracion_controlada', y: 331 },
        { code: 'torniquete', y: 342 },
        { code: 'feto', y: 353 }
    ] : [
        { code: 'tas', y: 34 },
        { code: 'tad', y: 47 },
        { code: 'tam', y: 60 },
        { code: 'frecuencia_cardiaca', y: 74 },
        { code: 'temperatura', y: 87 },
        { code: 'pvc', y: 101 }
    ];
    var legendSourceHeight = tipo === 0 ? 448 : 391;
    var legendSourceWidth = tipo === 0 ? 1437 : 1445;
    var legendSourceMaxX = tipo === 0 ? 275 : 170;
    var legendGridStartX = tipo === 0 ? 323 : 221;
    var legendDrag = null;

    function legendSymbolAt(pointer) {
        if (pointer.x < 0 || pointer.x > (legendSourceMaxX * canvas.getWidth() / legendSourceWidth)) {
            return null;
        }

        var scaleY = canvas.getHeight() / legendSourceHeight;
        var tolerance = 6.5 * scaleY;
        for (var index = 0; index < legendSymbolRows.length; index++) {
            if (Math.abs(pointer.y - (legendSymbolRows[index].y * scaleY)) <= tolerance) {
                return legendSymbolRows[index].code;
            }
        }
        return null;
    }

    canvas.on('mouse:down:before', function(options) {
        if (options.target) return;
        var pointer = canvas.getPointer(options.e);
        if (legendSymbolAt(pointer)) {
            stopDrawingMode();
        }
    });

    canvas.on('mouse:down', function(options) {
        if (options.target) return;
        var pointer = canvas.getPointer(options.e);
        var symbolCode = legendSymbolAt(pointer);
        if (!symbolCode) return;

        canvasAutosaveSuspended = true;
        var symbol = addClinicalSymbol(symbolCode, pointer);
        symbol.set({ selectable: false, evented: false });
        legendDrag = {
            object: symbol,
            startX: pointer.x,
            startY: pointer.y,
            previousSelection: canvas.selection
        };
        canvas.selection = false;
        canvas.setCursor('grabbing');
    });

    canvas.on('mouse:move', function(options) {
        var pointer = canvas.getPointer(options.e);
        if (legendDrag) {
            legendDrag.object.set({ left: pointer.x, top: pointer.y });
            legendDrag.object.setCoords();
            canvas.setCursor('grabbing');
            canvas.requestRenderAll();
            return;
        }

        if (!options.target && legendSymbolAt(pointer)) {
            canvas.setCursor('grab');
        }
    });

    canvas.on('mouse:up', function(options) {
        if (!legendDrag) return;

        var pointer = canvas.getPointer(options.e);
        var gridStartX = legendGridStartX * canvas.getWidth() / legendSourceWidth;
        var moved = Math.abs(pointer.x - legendDrag.startX) > 4 || Math.abs(pointer.y - legendDrag.startY) > 4;
        var symbol = legendDrag.object;
        canvas.selection = legendDrag.previousSelection;

        if (!moved || pointer.x < gridStartX) {
            canvas.remove(symbol);
            canvas.discardActiveObject();
        } else {
            symbol.set({ selectable: true, evented: true });
            symbol.setCoords();
            canvas.setActiveObject(symbol);
        }

        canvasAutosaveSuspended = false;
        if (moved && pointer.x >= gridStartX) {
            scheduleCanvasAutosave();
        }
        legendDrag = null;
        canvas.setCursor('default');
        canvas.requestRenderAll();
    });

    canvas.on('mouse:out', function() {
        if (legendDrag) {
            canvas.selection = legendDrag.previousSelection;
            canvas.remove(legendDrag.object);
            legendDrag = null;
            canvasAutosaveSuspended = false;
            canvas.requestRenderAll();
        }
        canvas.setCursor('default');
    });

    var colorPicker = document.getElementById('drawing-color-picker');
    var colorTrigger = document.getElementById('drawing-color-trigger');
    var currentColor = document.getElementById('drawing-color-current');

    colorTrigger.addEventListener('click', function(event) {
        event.stopPropagation();
        var isOpen = colorPicker.classList.toggle('is-open');
        colorTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.querySelectorAll('.drawing-color').forEach(function(colorButton) {
        colorButton.addEventListener('click', function() {
            var color = this.getAttribute('data-color');
            canvas.freeDrawingBrush.color = color;
            currentColor.style.backgroundColor = color;
            document.querySelectorAll('.drawing-color').forEach(function(button) {
                button.style.borderColor = 'transparent';
            });
            this.style.borderColor = '#0d6efd';
            colorPicker.classList.remove('is-open');
            colorTrigger.setAttribute('aria-expanded', 'false');
        });
    });

    document.addEventListener('click', function() {
        colorPicker.classList.remove('is-open');
        colorTrigger.setAttribute('aria-expanded', 'false');
    });

    // Para seleccionar un trazo primero hay que salir del modo dibujo.
    // De lo contrario, el clic sobre el lienzo crea otro trazo en vez de seleccionar.
    clxz.onclick = function(event) {
        event.preventDefault();
        canvas.isDrawingMode = false;
        canvas.selection = true;
        drawingModeEl.innerHTML = '<i class="fa fa-pencil"></i> Dibujar';
        drawingModeEl.className = 'btn btn-primary';

        deleteSelectedObjectsFromCanvas();
    }

    function deleteSelectedObjectsFromCanvas() {
        var selection = canvas.getActiveObject();
        var objects = selection
            ? (selection.type === 'activeSelection' ? selection.getObjects() : [selection])
            : selectedCanvasObjects.slice();

        if (!objects.length) {
            return;
        }

        objects.forEach(function(element) {
            canvas.remove(element);
        });
        selectedCanvasObjects = [];
        canvas.discardActiveObject();
        canvas.requestRenderAll();
        scheduleCanvasAutosave();
    }

    function performCanvasSave(id, silent) {
        var revisionAtStart = canvasRevision;
        canvas.discardActiveObject();
        canvas.getObjects().forEach(function(object) {
            if (object.type === 'path' && Number(object.strokeWidth || 0) < 2.5) {
                object.set('strokeWidth', 2.5);
                object.setCoords();
            }
        });
        canvas.requestRenderAll();
        var serializedCanvas = JSON.stringify(canvas.toDatalessJSON(['clinicalSymbol', 'symbolCode', 'templateLegend']));
        var exportCanvas = canvas.toCanvasElement(1);
        var tipo = $("#tipo").val();

        return new Promise(function(resolve, reject) {
            exportCanvas.toBlob(function(blob) {
                if (!blob) {
                    reject(new Error('No se pudo generar la imagen del lienzo.'));
                    return;
                }

                const formData = new FormData();
                formData.append('my-file', blob, 'filename.png');
                formData.append('canvas_json', serializedCanvas);
                $.ajax({
                    url: "{{ route('preeanestesico.saveCanvas') }}?id=" + id + "&tipo=" + tipo,
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(data) {
                        if (data == 'ok') {
                            if (canvasRevision === revisionAtStart) {
                                canvasDirty = false;
                            }
                            if (!silent) {
                                swal.fire({
                                    title: "Success!",
                                    text: `Imagen Guardada`,
                                    icon: "success"
                                });
                            }
                            resolve();
                            return;
                        }

                        if (!silent) {
                            swal.fire({
                                title: "Error!",
                                text: `Ha ocurrido un error en el guardado`,
                                icon: "error"
                            });
                        }
                        reject(new Error('No se pudo guardar el lienzo.'));
                    },
                    error: function() {
                        if (!silent) {
                            swal.fire({
                                title: "Error!",
                                text: `Ha ocurrido un error en el guardado`,
                                icon: "error"
                            });
                        }
                        reject(new Error('No se pudo guardar el lienzo.'));
                    }
                });
            }, 'image/png');
        });
    }

    function tosaveCanvas(id, silent) {
        canvasSaveChain = canvasSaveChain
            .catch(function() {})
            .then(function() {
                return performCanvasSave(id, !!silent);
            });
        return canvasSaveChain;
    }

    function scheduleCanvasAutosave() {
        if (!canvasReady || canvasAutosaveSuspended) return;

        canvasDirty = true;
        canvasRevision++;
        window.clearTimeout(autoSaveTimer);
        autoSaveTimer = window.setTimeout(function() {
            autoSaveTimer = null;
            tosaveCanvas($("#id").val(), true).catch(function() {});
        }, 1200);
    }

    canvas.on('path:created', scheduleCanvasAutosave);
    canvas.on('object:added', scheduleCanvasAutosave);
    canvas.on('object:modified', scheduleCanvasAutosave);
    canvas.on('object:removed', scheduleCanvasAutosave);

    function waitForCanvasReady() {
        return new Promise(function(resolve) {
            if (canvasReady) {
                resolve();
                return;
            }

            var attempts = 0;
            var readyInterval = window.setInterval(function() {
                attempts++;
                if (canvasReady || attempts >= 50) {
                    window.clearInterval(readyInterval);
                    resolve();
                }
            }, 100);
        });
    }

    var canvasPdfLink = document.querySelector('.anestesico-v3-pdf a');
    if (canvasPdfLink && !canvasPdfLink.dataset.canvasAutosaveBound) {
        canvasPdfLink.dataset.canvasAutosaveBound = 'true';
        canvasPdfLink.addEventListener('click', function(event) {
            event.preventDefault();
            window.clearTimeout(autoSaveTimer);
            autoSaveTimer = null;

            var pdfUrl = this.href;
            var pdfWindow = this.target === '_blank' ? window.open('about:blank', '_blank') : null;
            waitForCanvasReady()
                .then(function() {
                    return tosaveCanvas($("#id").val(), true);
                })
                .catch(function() {})
                .then(function() {
                    if (pdfWindow) {
                        pdfWindow.location.href = pdfUrl;
                    } else {
                        window.location.href = pdfUrl;
                    }
                });
        });
    }

    function hayContenidoDibujado() {
        return canvas.getObjects().length > 0;
    }

    function saveCan() {
        let id = $("#id").val();
        // El fondo puede ser una imagen ya guardada y no cuenta como objeto Fabric.
        // Siempre se permite guardar para actualizar el registro existente.
        tosaveCanvas(id, false).catch(function() {});

    }
</script>
