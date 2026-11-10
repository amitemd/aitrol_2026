@php
  $medicalTourArea = isset($medicalTourArea) ? $medicalTourArea : 'medical';
@endphp

<div class="medical-tour" data-medical-tour-area="{{$medicalTourArea}}">
  <button type="button" class="medical-tour-launcher" aria-label="Abrir guía de esta pantalla" title="Guía de esta pantalla">
    <i class="fa fa-question" aria-hidden="true"></i>
    <span>Guía rápida</span>
  </button>
  <div class="medical-tour-backdrop" hidden></div>
  <div class="medical-tour-spotlight" hidden></div>
  <section class="medical-tour-panel" role="dialog" aria-modal="true" aria-live="polite" aria-labelledby="medical-tour-title" hidden>
    <div class="medical-tour-panel-head">
      <span class="medical-tour-section"><i class="fa fa-compass" aria-hidden="true"></i> Recorrido guiado</span>
      <button type="button" class="medical-tour-close" aria-label="Cerrar recorrido"><i class="fa fa-times" aria-hidden="true"></i></button>
    </div>
    <div class="medical-tour-progress"><span></span></div>
    <div class="medical-tour-copy">
      <span class="medical-tour-counter"></span>
      <h3 id="medical-tour-title"></h3>
      <p></p>
    </div>
    <div class="medical-tour-actions">
      <button type="button" class="medical-tour-skip">Salir</button>
      <div>
        <button type="button" class="medical-tour-prev"><i class="fa fa-arrow-left" aria-hidden="true"></i> Anterior</button>
        <button type="button" class="medical-tour-next">Siguiente <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
      </div>
    </div>
  </section>
</div>

<style>
  .medical-tour {
    --tour-navy: #07375f;
    --tour-blue: #0b6fac;
    --tour-cyan: #22b8cf;
    --tour-border: #d7e8ef;
    --tour-muted: #647b8e;
    font-family: Arial, Helvetica, sans-serif;
  }

  .medical-tour [hidden] { display: none !important; }

  .medical-tour-launcher {
    position: fixed;
    right: 22px;
    bottom: 82px;
    z-index: 2147482000;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 43px;
    height: 43px;
    padding: 0;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, .58);
    border-radius: 14px;
    background: linear-gradient(145deg, rgba(7, 55, 95, .88), rgba(11, 111, 172, .88));
    box-shadow: 0 8px 22px rgba(7, 55, 95, .18);
    color: #fff;
    font-size: 15px;
    opacity: .72;
    transition: width .22s ease, opacity .22s ease, transform .22s ease, box-shadow .22s ease;
    backdrop-filter: blur(8px);
  }

  .medical-tour-launcher span {
    width: 0;
    margin-left: 0;
    overflow: hidden;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    opacity: 0;
    transition: width .22s ease, margin .22s ease, opacity .18s ease;
  }

  .medical-tour-launcher:hover,
  .medical-tour-launcher:focus {
    width: 122px;
    opacity: 1;
    outline: none;
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(7, 55, 95, .27);
  }

  .medical-tour-launcher:hover span,
  .medical-tour-launcher:focus span { width: 68px; margin-left: 7px; opacity: 1; }

  .medical-tour-launcher.is-hinting { animation: medicalTourHint 1.8s ease 2; }
  @keyframes medicalTourHint {
    0%, 100% { box-shadow: 0 8px 22px rgba(7, 55, 95, .18); }
    50% { box-shadow: 0 8px 22px rgba(7, 55, 95, .2), 0 0 0 9px rgba(34, 184, 207, .12); }
  }

  .medical-tour-backdrop {
    position: fixed;
    inset: 0;
    z-index: 2147483000;
    background: transparent;
    cursor: default;
  }

  .medical-tour-spotlight {
    position: fixed;
    z-index: 2147483001;
    border: 3px solid #55d7e6;
    border-radius: 13px;
    box-shadow: 0 0 0 9999px rgba(4, 22, 37, .66), 0 0 0 7px rgba(85, 215, 230, .2);
    pointer-events: none;
    transition: top .24s ease, left .24s ease, width .24s ease, height .24s ease;
  }

  .medical-tour-panel {
    position: fixed;
    z-index: 2147483002;
    width: min(370px, calc(100vw - 28px));
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, .7);
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 24px 64px rgba(4, 22, 37, .3);
    color: #17324d;
  }

  .medical-tour-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px 9px;
  }

  .medical-tour-section {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    color: var(--tour-blue);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .055em;
    text-transform: uppercase;
  }

  .medical-tour-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    padding: 0;
    border: 0;
    border-radius: 9px;
    background: #eef7fa;
    color: var(--tour-navy);
  }

  .medical-tour-progress { height: 3px; background: #e8f1f5; }
  .medical-tour-progress span {
    display: block;
    width: 0;
    height: 100%;
    border-radius: 0 999px 999px 0;
    background: linear-gradient(90deg, var(--tour-blue), var(--tour-cyan));
    transition: width .22s ease;
  }

  .medical-tour-copy { padding: 17px 18px 14px; }
  .medical-tour-counter {
    display: block;
    margin-bottom: 6px;
    color: #7c91a2;
    font-size: 11px;
    font-weight: 700;
  }
  .medical-tour-copy h3 {
    margin: 0;
    color: var(--tour-navy);
    font-size: 20px;
    font-weight: 800;
    line-height: 1.25;
  }
  .medical-tour-copy p {
    margin: 9px 0 0;
    color: var(--tour-muted);
    font-size: 13px;
    font-weight: 500;
    line-height: 1.58;
  }

  .medical-tour-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 16px;
    border-top: 1px solid var(--tour-border);
    background: #f8fbfc;
  }
  .medical-tour-actions > div { display: flex; gap: 7px; }
  .medical-tour-actions button {
    min-height: 35px;
    padding: 7px 11px;
    border: 1px solid var(--tour-border);
    border-radius: 9px;
    background: #fff;
    color: var(--tour-navy);
    font-size: 12px;
    font-weight: 700;
  }
  .medical-tour-actions .medical-tour-next {
    border-color: var(--tour-blue);
    background: var(--tour-blue);
    color: #fff;
  }
  .medical-tour-actions .medical-tour-skip { border: 0; background: transparent; color: #708596; }
  .medical-tour-actions button:disabled { cursor: not-allowed; opacity: .38; }

  @media (max-width: 600px) {
    .medical-tour-launcher { right: 14px; bottom: 72px; }
    .medical-tour-panel { left: 14px !important; right: 14px; bottom: 14px !important; top: auto !important; width: auto; }
    .medical-tour-spotlight { border-radius: 9px; }
    .medical-tour-actions { align-items: stretch; flex-direction: column-reverse; }
    .medical-tour-actions > div { display: grid; grid-template-columns: 1fr 1fr; }
    .medical-tour-actions button { width: 100%; }
  }

  @media (prefers-reduced-motion: reduce) {
    .medical-tour *, .medical-tour *::before, .medical-tour *::after { animation: none !important; transition: none !important; }
  }
</style>

<script>
(function () {
  'use strict';

  var root = document.currentScript.previousElementSibling.previousElementSibling;
  if (!root || !root.classList.contains('medical-tour')) {
    root = document.querySelector('.medical-tour[data-medical-tour-area="{{$medicalTourArea}}"]');
  }
  if (!root || root.getAttribute('data-ready') === '1') return;
  root.setAttribute('data-ready', '1');

  var area = root.getAttribute('data-medical-tour-area');
  var launcher = root.querySelector('.medical-tour-launcher');
  var backdrop = root.querySelector('.medical-tour-backdrop');
  var spotlight = root.querySelector('.medical-tour-spotlight');
  var panel = root.querySelector('.medical-tour-panel');
  var title = panel.querySelector('h3');
  var copy = panel.querySelector('.medical-tour-copy p');
  var counter = panel.querySelector('.medical-tour-counter');
  var progress = panel.querySelector('.medical-tour-progress span');
  var previous = panel.querySelector('.medical-tour-prev');
  var next = panel.querySelector('.medical-tour-next');
  var close = panel.querySelector('.medical-tour-close');
  var skip = panel.querySelector('.medical-tour-skip');
  var activeSteps = [];
  var current = 0;
  var active = false;

  var hospitalHome = [
    { selector: '[data-tour="hospital-welcome"]', title: 'Tu punto de entrada hospitalario', text: 'Aquí comienzas la jornada clínica. Desde esta pantalla puedes entrar a áreas críticas, servicios de apoyo y procesos de hospitalización.' },
    { selector: '[data-tour="hospital-reception"]', title: 'Recepción y gestión de camas', text: 'Úsala para revisar disponibilidad, ubicar pacientes en espera, asignar camas, moverlas entre habitaciones y liberarlas al finalizar la atención.' },
    { selector: '[data-tour="hospital-emergency"]', title: 'Emergencia', text: 'Abre la atención inmediata: ingreso, triaje, formularios, evolución y seguimiento del paciente atendido por emergencia.' },
    { selector: '[data-tour="hospital-surgery"]', title: 'Cirugía', text: 'Concentra los procesos quirúrgicos y el seguimiento relacionado con procedimientos y quirófano.' },
    { selector: '[data-tour="hospital-laboratory"]', title: 'Laboratorio', text: 'Consulta y gestiona órdenes de análisis clínicos asociadas a la atención hospitalaria.' },
    { selector: '[data-tour="hospital-pharmacy"]', title: 'Farmacia', text: 'Permite buscar medicamentos, revisar existencias por bodega y acceder a los movimientos utilizados durante la hospitalización.' },
    { selector: '[data-tour="hospital-images"]', title: 'Imágenes', text: 'Accede a estudios diagnósticos por imagen y a la información relacionada con sus órdenes y resultados.' },
    { selector: '[data-tour="hospital-ucin"]', title: 'UCIN', text: 'Área destinada al cuidado intensivo neonatal y al seguimiento especializado de sus pacientes.' },
    { selector: '[data-tour="hospital-uci"]', title: 'UCI', text: 'Acceso a cuidados intensivos, control clínico y seguimiento de pacientes críticos.' },
    { selector: '[data-tour="hospital-inpatient"]', title: 'Hospitalización', text: 'Aquí se realiza el seguimiento del paciente ingresado, su documentación clínica y las acciones propias de su estancia.' }
  ];

  var hospitalRooms = [
    { selector: '[data-tour="hospital-navigation"]', title: 'Regresa al Área médica', text: 'Este enlace está disponible en las pantallas hospitalarias para volver de forma directa al menú principal del área médica.' },
    { selector: '.bed-command-hero', title: 'Centro de gestión de cuartos', text: 'Los accesos superiores permiten consultar pacientes en habitación, configurar habitaciones y liberar camas.' },
    { selector: '.bed-summary-grid', title: 'Disponibilidad en tiempo real', text: 'Estos indicadores resumen camas libres, ocupadas, en preparación o no disponibles.' },
    { selector: '.bed-panel .patient-finder', title: 'Localiza un paciente', text: 'Busca por nombre, identificación o sala y abre rápidamente la cama donde está hospitalizado.' },
    { selector: '.bed-panel .bed-filter-grid', title: 'Filtra el mapa', text: 'Reduce la vista por tipo, piso, habitación, cama o paciente para encontrar lo necesario sin recorrer todo el tablero.' },
    { selector: '.bed-panel-body .room-grid, .bed-setup-empty', title: 'Mapa de camas', text: 'Cada tarjeta representa una habitación y sus camas. El color indica disponibilidad; al seleccionar una cama aparecen sus acciones.' },
    { selector: '#example2', title: 'Pacientes en espera', text: 'Desde esta lista seleccionas al paciente pendiente antes de asignarlo a una cama libre.' }
  ];

  var hospitalAdmin = [
    { selector: '[data-tour="hospital-navigation"]', title: 'Regresa al Área médica', text: 'Usa este enlace cuando quieras volver al menú principal hospitalario, sin depender del botón Atrás del navegador.' },
    { selector: '.admin-bed-header', title: 'Configuración de habitaciones', text: 'Desde aquí creas habitaciones y regresas al mapa operativo de camas.' },
    { selector: '.admin-empty-state, .admin-summary-grid', title: 'Estado de la configuración', text: 'Si es la primera vez, sigue los pasos indicados. Cuando existan habitaciones verás el resumen de capacidad y ocupación.' },
    { selector: '.admin-room-list', title: 'Habitaciones y camas', text: 'Cada bloque muestra el piso, tipo, camas registradas y su estado. Usa Editar para actualizar numeración o disponibilidad.' }
  ];

  var hospitalGeneric = [
    { selector: '[data-tour="hospital-navigation"]', title: 'Navegación hospitalaria', text: 'Aquí puedes identificar la sección actual y regresar al Área médica con un solo clic.' },
    { selector: '.content-header, .bed-command-hero, .hospital-hero', title: 'Ubicación actual', text: 'Este encabezado identifica el proceso hospitalario en el que estás trabajando.' },
    { selector: '.card, .bed-panel, .hospital-panel', title: 'Área de trabajo', text: 'La información y acciones principales se organizan en tarjetas para mantener separado cada proceso clínico.' },
    { selector: 'form', title: 'Filtros y registro', text: 'Completa los campos obligatorios y usa las acciones de la pantalla para buscar, guardar o actualizar información.' },
    { selector: '.table-responsive, table', title: 'Resultados', text: 'Las tablas muestran los registros disponibles. Revisa la columna Acción para abrir, editar o continuar el proceso.' }
  ];

  var ambulatoryHome = [
    { selector: '.hc4-medical-header-row', title: 'Consulta y atención ambulatoria', text: 'Este es el inicio del trabajo médico ambulatorio. El botón Sistema hospitalario cambia al entorno de internación cuando sea necesario.' },
    { selector: '[data-tour="ambulatory-agenda"]', title: 'Agenda del día', text: 'Muestra consultas y procedimientos programados. Selecciónala para cargar el listado completo de pacientes de la jornada.' },
    { selector: '[data-tour="ambulatory-schedule"]', title: 'Horario laboral', text: 'Consulta el horario configurado para el profesional y los espacios disponibles de atención.' },
    { selector: '[data-tour="ambulatory-patients"]', title: 'Pacientes del día', text: 'Revisa rápidamente las consultas y procedimientos asignados para la fecha actual.' },
    { selector: '[data-tour="ambulatory-lab"]', title: 'Órdenes de laboratorio', text: 'Abre las órdenes de laboratorio vinculadas a tus pacientes y revisa su estado.' },
    { selector: '[data-tour="ambulatory-favorites"]', title: 'Exámenes favoritos', text: 'Accede a los exámenes utilizados con mayor frecuencia para agilizar nuevas solicitudes.' },
    { selector: '[data-tour="ambulatory-medicines"]', title: 'Medicamentos', text: 'Permite crear o editar el catálogo médico utilizado en recetas y tratamientos.' },
    { selector: '[data-tour="ambulatory-templates"]', title: 'Plantillas de procedimientos', text: 'Administra textos y estructuras reutilizables para documentar procedimientos de forma consistente.' },
    { selector: '[data-tour="ambulatory-search"]', title: 'Historia clínica por paciente', text: 'Busca por fechas, tipo de atención, especialidad, doctor, seguro, apellidos, nombres o identificación. Puedes combinar varios filtros.' },
    { selector: '[data-tour="ambulatory-results"]', title: 'Área de resultados', text: 'Aquí se cargan pacientes, agenda, laboratorio y demás opciones sin abandonar la pantalla principal.' }
  ];

  var ambulatoryGeneric = [
    { selector: '.content-header', title: 'Área médica ambulatoria', text: 'El encabezado indica el módulo clínico actual y permite cambiar al sistema hospitalario.' },
    { selector: 'form', title: 'Datos clínicos y filtros', text: 'Completa los campos necesarios antes de buscar, registrar o actualizar la atención.' },
    { selector: '#area_trabajo, .box, .content', title: 'Área de trabajo', text: 'Los resultados y formularios del paciente se cargan en esta zona.' }
  ];

  function chooseSteps() {
    if (area === 'hospital') {
      if (document.querySelector('[data-tour="hospital-welcome"]')) return hospitalHome;
      if (document.querySelector('.bed-command-hero')) return hospitalRooms;
      if (document.querySelector('.admin-bed-page')) return hospitalAdmin;
      return hospitalGeneric;
    }
    if (document.querySelector('[data-tour="ambulatory-agenda"]')) return ambulatoryHome;
    return ambulatoryGeneric;
  }

  function visibleElement(selector) {
    var candidates;
    try { candidates = document.querySelectorAll(selector); } catch (error) { return null; }
    for (var i = 0; i < candidates.length; i++) {
      var rect = candidates[i].getBoundingClientRect();
      var style = window.getComputedStyle(candidates[i]);
      if (rect.width > 8 && rect.height > 8 && style.display !== 'none' && style.visibility !== 'hidden') return candidates[i];
    }
    return null;
  }

  function buildSteps() {
    return chooseSteps().map(function (step) {
      return { element: visibleElement(step.selector), title: step.title, text: step.text };
    }).filter(function (step) { return !!step.element; });
  }

  function positionTour() {
    if (!active || !activeSteps[current]) return;
    var element = activeSteps[current].element;
    var rect = element.getBoundingClientRect();
    var pad = 7;
    spotlight.style.top = Math.max(5, rect.top - pad) + 'px';
    spotlight.style.left = Math.max(5, rect.left - pad) + 'px';
    spotlight.style.width = Math.min(window.innerWidth - 10, rect.width + pad * 2) + 'px';
    spotlight.style.height = Math.min(window.innerHeight - 10, rect.height + pad * 2) + 'px';

    if (window.innerWidth <= 600) return;
    var panelWidth = panel.offsetWidth || 370;
    var panelHeight = panel.offsetHeight || 260;
    var gap = 18;
    var left;
    var top;

    if (rect.right + gap + panelWidth <= window.innerWidth - 12) {
      left = rect.right + gap;
      top = rect.top + Math.min(18, Math.max(0, (rect.height - panelHeight) / 2));
    } else if (rect.left - gap - panelWidth >= 12) {
      left = rect.left - gap - panelWidth;
      top = rect.top + Math.min(18, Math.max(0, (rect.height - panelHeight) / 2));
    } else {
      left = Math.max(14, Math.min(rect.left, window.innerWidth - panelWidth - 14));
      top = rect.bottom + gap;
      if (top + panelHeight > window.innerHeight - 14) top = rect.top - panelHeight - gap;
    }

    panel.style.left = Math.max(14, Math.min(left, window.innerWidth - panelWidth - 14)) + 'px';
    panel.style.top = Math.max(14, Math.min(top, window.innerHeight - panelHeight - 14)) + 'px';
  }

  function showStep(index) {
    current = Math.max(0, Math.min(index, activeSteps.length - 1));
    var step = activeSteps[current];
    step.element.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
    title.textContent = step.title;
    copy.textContent = step.text;
    counter.textContent = 'Paso ' + (current + 1) + ' de ' + activeSteps.length;
    progress.style.width = (((current + 1) / activeSteps.length) * 100) + '%';
    previous.disabled = current === 0;
    next.innerHTML = current === activeSteps.length - 1 ? 'Finalizar <i class="fa fa-check" aria-hidden="true"></i>' : 'Siguiente <i class="fa fa-arrow-right" aria-hidden="true"></i>';
    window.setTimeout(positionTour, 330);
  }

  function startTour() {
    activeSteps = buildSteps();
    if (!activeSteps.length) return;
    active = true;
    launcher.hidden = true;
    backdrop.hidden = false;
    spotlight.hidden = false;
    panel.hidden = false;
    document.body.classList.add('medical-tour-active');
    showStep(0);
    close.focus();
  }

  function finishTour(completed) {
    active = false;
    backdrop.hidden = true;
    spotlight.hidden = true;
    panel.hidden = true;
    launcher.hidden = false;
    document.body.classList.remove('medical-tour-active');
    if (completed) {
      try { window.localStorage.setItem('medical-tour-completed-' + area, '1'); } catch (error) {}
    }
    launcher.focus();
  }

  launcher.addEventListener('click', startTour);
  close.addEventListener('click', function () { finishTour(false); });
  skip.addEventListener('click', function () { finishTour(false); });
  previous.addEventListener('click', function () { if (current > 0) showStep(current - 1); });
  next.addEventListener('click', function () {
    if (current >= activeSteps.length - 1) finishTour(true); else showStep(current + 1);
  });
  window.addEventListener('resize', positionTour);
  window.addEventListener('scroll', positionTour, true);
  document.addEventListener('keydown', function (event) {
    if (!active) return;
    if (event.key === 'Escape') finishTour(false);
    if (event.key === 'ArrowRight') next.click();
    if (event.key === 'ArrowLeft' && current > 0) previous.click();
  });

  try {
    if (!window.localStorage.getItem('medical-tour-hint-' + area)) {
      window.setTimeout(function () {
        launcher.classList.add('is-hinting');
        window.setTimeout(function () { launcher.classList.remove('is-hinting'); }, 3800);
      }, 1800);
      window.localStorage.setItem('medical-tour-hint-' + area, '1');
    }
  } catch (error) {}
})();
</script>
