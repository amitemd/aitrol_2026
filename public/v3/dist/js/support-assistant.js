(function (window, document, $) {
  'use strict';

  var root = document.getElementById('aitrol-support-assistant');
  if (!root) return;

  var avatarButton = root.querySelector('.aitrol-assistant__avatar');
  var helpButton = root.querySelector('.aitrol-assistant__help-trigger');
  var avatarImage = avatarButton.querySelector('img');
  var panel = root.querySelector('.aitrol-assistant__panel');
  var panelStatus = document.getElementById('aitrol-assistant-panel-status');
  var bubble = document.getElementById('aitrol-assistant-status');
  var closeButton = root.querySelector('.aitrol-assistant__close');
  var userName = (root.getAttribute('data-user-name') || '').trim();
  var userId = (root.getAttribute('data-user-id') || 'guest').trim();
  var assistantName = (root.getAttribute('data-assistant-name') || 'Walter').trim();
  var translations = {};
  try {
    translations = JSON.parse(root.getAttribute('data-translations') || '{}');
  } catch (error) {
    translations = {};
  }
  function translated(path, fallback) {
    var value = translations;
    path.split('.').forEach(function (key) {
      value = value && Object.prototype.hasOwnProperty.call(value, key) ? value[key] : null;
    });
    return value === null || value === undefined || value === '' ? fallback : value;
  }
  var isFreshLogin = root.getAttribute('data-login-greeting') === '1';
  var sources = {
    idle: root.getAttribute('data-src-idle'),
    thinking: root.getAttribute('data-src-thinking'),
    book: root.getAttribute('data-src-book'),
    support: root.getAttribute('data-src-support')
  };
  var labels = translations.labels || {
    idle: assistantName + ' esperando para ayudar',
    thinking: assistantName + ' pensando y buscando una solución',
    book: assistantName + ' consultando una sugerencia en un libro',
    support: assistantName + ' trabajando en una computadora'
  };
  var messages = translations.messages || {
    idle: 'Aquí estoy por si necesitas ayuda.',
    thinking: 'No encuentro eso todavía; déjame pensarlo mejor.',
    book: 'Estoy consultando una sugerencia para ayudarte.',
    support: 'Estoy trabajando en tu solicitud.'
  };
  var phraseGroups = translations.groups || {
    idle: [
      '¿Qué deseas hacer ahora?',
      'Estoy aquí y listo para ayudarte.',
      '¿Buscamos algo en el sistema?',
      'Podemos continuar cuando quieras.',
      'Aitrol Hospitalario está listo. ¿Con qué módulo seguimos?',
      '¿Hay alguna tarea en la que pueda acompañarte?'
    ],
    proactive: [
      '¿Cómo te puedo ayudar?',
      '¿Necesitas encontrar algo en Aitrol?',
      'Estoy atento por si quieres revisar alguna tarea.',
      '¿Hay algo pendiente que quieras resolver?',
      'Si necesitas apoyo con el sistema, aquí estoy.',
      '¿Quieres que busquemos algo juntos?',
      'Puedes tocarme para abrir las opciones de ayuda.',
      'Si algo no funciona, toca el avatar y crea un ticket de soporte.',
      'Escribe en los buscadores del sistema y te acompañaré durante la consulta.'
    ],
    searching: [
      'Estoy buscando una respuesta y revisando sugerencias.',
      'Déjame consultar las opciones disponibles.',
      'Estoy revisando la información del sistema.',
      'Un momento, voy a buscar la mejor coincidencia.'
    ],
    noResults: [
      'No encontré resultados. Déjame pensar en otra opción.',
      'No veo coincidencias todavía. Voy a revisar otra posibilidad.',
      'Esa búsqueda no devolvió datos. ¿Probamos con otro término?',
      'No apareció información. Estoy pensando cómo ayudarte mejor.'
    ],
    working: [
      'Estoy trabajando en tu solicitud.',
      'Un momento, estoy procesando la información.',
      'Ya estoy atendiendo esa tarea.',
      'Estoy revisando los datos en la computadora.'
    ],
    completed: [
      'Listo, el proceso terminó correctamente.',
      'He terminado. ¿Continuamos con otra tarea?',
      'Todo listo por aquí.',
      'La tarea quedó procesada. ¿Qué hacemos ahora?',
      'Trabajo terminado. Estoy disponible para lo siguiente.'
    ]
  };
  var busyCount = 0;
  var idleTimer = null;
  var bubbleTimer = null;
  var searchTimer = null;
  var motionTimer = null;
  var motionEndTimer = null;
  var proactiveTimer = null;
  var lastMotion = '';
  var lastSearchInput = null;
  var currentState = 'idle';
  var userInteracted = false;
  var wasDragged = false;
  var dragState = null;
  var lastPhraseByGroup = {};
  var IDLE_DELAY = 30000;
  var POSITION_KEY = 'aitrol.supportAssistant.position.v2';
  var PRESENTATION_KEY = 'aitrol.supportAssistant.presentation.v1.' + userId;
  var VIEWPORT_MARGIN = 8;
  var motionClasses = [
    'motion-shift', 'motion-nod', 'motion-attentive', 'motion-stretch', 'motion-glance',
    'motion-wave', 'motion-bounce', 'motion-curious', 'motion-sway', 'motion-peek'
  ];
  var reactionClasses = [
    'reaction-hello', 'reaction-celebrate', 'reaction-shrug',
    'reaction-focus', 'reaction-working', 'reaction-searching'
  ];
  var reactionTimer = null;
  var lastReaction = '';
  var motionDurations = {
    'motion-shift': 3400, 'motion-nod': 2600, 'motion-attentive': 3000,
    'motion-stretch': 3700, 'motion-glance': 2800, 'motion-wave': 2200,
    'motion-bounce': 2000, 'motion-curious': 2800, 'motion-sway': 3200,
    'motion-peek': 2500
  };

  function addressPhrase(phrase) {
    if (!userName) return phrase;
    if (phrase.charAt(0) === '¿') {
      return userName + ', ¿' + phrase.charAt(1).toLowerCase() + phrase.slice(2);
    }
    return userName + ', ' + phrase.charAt(0).toLowerCase() + phrase.slice(1);
  }

  function pickPhrase(group) {
    var phrases = phraseGroups[group] || phraseGroups.idle;
    var available = phrases.filter(function (phrase) { return phrase !== lastPhraseByGroup[group]; });
    var selected = available[Math.floor(Math.random() * available.length)] || phrases[0];
    lastPhraseByGroup[group] = selected;
    return selected;
  }

  function greetingByTime() {
    var hour = new Date().getHours();
    var greeting = hour >= 5 && hour < 12
      ? translated('greetings.morning', 'Buenos días')
      : hour >= 12 && hour < 19
        ? translated('greetings.afternoon', 'Buenas tardes')
        : translated('greetings.evening', 'Buenas noches');
    return greeting + (userName ? ', ' + userName : '') + '. ' + translated('greetings.question', '¿Qué deseas hacer hoy?');
  }

  function showBubble(duration) {
    if (root.classList.contains('is-collapsed')) return;
    window.clearTimeout(bubbleTimer);
    root.classList.add('is-speaking');
    bubbleTimer = window.setTimeout(function () {
      root.classList.remove('is-speaking');
    }, typeof duration === 'number' ? duration : 4200);
  }

  function setState(state, message, duration) {
    if (!sources[state]) state = 'idle';
    currentState = state;
    root.setAttribute('data-state', state);
    var nextMessage = message || messages[state];
    bubble.textContent = nextMessage;
    panelStatus.textContent = nextMessage;

    if (avatarImage.getAttribute('src') !== sources[state]) {
      avatarImage.classList.add('is-swapping');
      window.setTimeout(function () {
        avatarImage.setAttribute('src', sources[state]);
        avatarImage.setAttribute('alt', labels[state]);
        avatarImage.classList.remove('is-swapping');
      }, 130);
    }
    showBubble(duration);
    if (state === 'idle') scheduleRandomMotion();
    else clearRandomMotion();
    return state;
  }

  function clearRandomMotion() {
    window.clearTimeout(motionTimer);
    window.clearTimeout(motionEndTimer);
    motionClasses.forEach(function (motion) { root.classList.remove(motion); });
  }

  function scheduleRandomMotion() {
    window.clearTimeout(motionTimer);
    if (currentState !== 'idle' || root.classList.contains('is-collapsed')) return;
    motionTimer = window.setTimeout(function () {
      if (currentState !== 'idle' || root.classList.contains('is-open')) {
        scheduleRandomMotion();
        return;
      }
      var available = motionClasses.filter(function (motion) { return motion !== lastMotion; });
      var motion = available[Math.floor(Math.random() * available.length)];
      lastMotion = motion;
      root.classList.add(motion);
      motionEndTimer = window.setTimeout(function () {
        root.classList.remove(motion);
        scheduleRandomMotion();
      }, motionDurations[motion] || 3000);
    }, 4500 + Math.floor(Math.random() * 18500));
  }

  function scheduleProactivePhrase() {
    window.clearTimeout(proactiveTimer);
    if (root.classList.contains('is-collapsed')) return;
    proactiveTimer = window.setTimeout(function () {
      var canSpeak = currentState === 'idle' && busyCount === 0 &&
        !root.classList.contains('is-open') && !document.hidden;
      if (canSpeak) {
        setState('idle', addressPhrase(pickPhrase('proactive')), 5200);
      }
      scheduleProactivePhrase();
    }, 45000 + Math.floor(Math.random() * 65000));
  }

  function scheduleIdle() {
    window.clearTimeout(idleTimer);
    if (busyCount > 0) return;
    idleTimer = window.setTimeout(function () {
      userInteracted = false;
      setState('idle', addressPhrase(pickPhrase('idle')), 5000);
    }, IDLE_DELAY);
  }

  function noteActivity(event) {
    if (!event || event.type !== 'scroll') userInteracted = true;
    if (currentState === 'idle') root.classList.remove('is-speaking');
    scheduleIdle();
    scheduleProactivePhrase();
  }

  function openPanel(open) {
    var shouldOpen = typeof open === 'boolean' ? open : !root.classList.contains('is-open');
    root.classList.toggle('is-open', shouldOpen);
    panel.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
    avatarButton.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    if (shouldOpen) {
      root.classList.remove('is-speaking');
      closeButton.focus();
    }
    scheduleProactivePhrase();
  }

  function setCollapsed(collapsed) {
    var shouldCollapse = collapsed !== false;
    if (shouldCollapse) {
      openPanel(false);
      root.classList.remove('is-speaking');
      clearRandomMotion();
      window.clearTimeout(proactiveTimer);
    }
    root.classList.toggle('is-collapsed', shouldCollapse);
    helpButton.setAttribute('aria-expanded', shouldCollapse ? 'false' : 'true');
    if (!shouldCollapse) {
      openPanel(true);
      scheduleRandomMotion();
      scheduleProactivePhrase();
    }
  }

  function hasSeenPresentation() {
    try {
      return window.localStorage.getItem(PRESENTATION_KEY) === '1';
    } catch (error) {
      return false;
    }
  }

  function rememberPresentation() {
    try {
      window.localStorage.setItem(PRESENTATION_KEY, '1');
    } catch (error) {
      // La presentación sigue funcionando si el navegador bloquea storage.
    }
  }

  function startPresentation() {
    var presentationName = userName ? ', ' + userName : '';
    var presentation = [
      {
        delay: 700,
        message: translated('presentation.0', '¡Hola:name! Soy Walter.').replace(':name', presentationName),
        reaction: 'reaction-hello',
        duration: 3900
      },
      {
        delay: 4700,
        message: translated('presentation.1', 'Estoy aquí para ayudarte y guiarte mientras utilizas Aitrol Hospitalario.'),
        reaction: 'reaction-focus',
        duration: 4300
      },
      {
        delay: 9100,
        message: translated('presentation.2', 'Puedo acompañarte en búsquedas, procesos y tareas del sistema.'),
        reaction: 'reaction-searching',
        duration: 4300
      },
      {
        delay: 13500,
        message: translated('presentation.3', 'Tócame para ver mis opciones o arrástrame para colocarme donde prefieras.'),
        reaction: 'reaction-celebrate',
        duration: 5600
      }
    ];

    rememberPresentation();
    presentation.forEach(function (step) {
      window.setTimeout(function () {
        if (root.classList.contains('is-dragging')) return;
        setState('idle', step.message, step.duration);
        playReaction(step.reaction, Math.min(step.duration - 500, 2200));
      }, step.delay);
    });
  }

  function playReaction(reaction, duration) {
    window.clearTimeout(reactionTimer);
    reactionClasses.forEach(function (name) { root.classList.remove(name); });
    // Reinicia la animación incluso cuando se repite la misma reacción.
    void root.offsetWidth;
    root.classList.add(reaction);
    reactionTimer = window.setTimeout(function () {
      root.classList.remove(reaction);
      if (currentState === 'idle') scheduleRandomMotion();
    }, duration || 1800);
  }

  function playRandomReaction(reactions, duration) {
    var available = reactions.filter(function (reaction) { return reaction !== lastReaction; });
    var reaction = available[Math.floor(Math.random() * available.length)] || reactions[0];
    lastReaction = reaction;
    playReaction(reaction, duration);
  }

  function clamp(value, minimum, maximum) {
    return Math.min(Math.max(value, minimum), Math.max(minimum, maximum));
  }

  function updateDockDirection(left, top) {
    var centerX = left + (root.offsetWidth / 2);
    var centerY = top + (root.offsetHeight / 2);
    root.classList.toggle('is-docked-left', centerX < window.innerWidth / 2);
    root.classList.toggle('is-docked-top', centerY < window.innerHeight / 2);
  }

  function placeAssistant(left, top, persist) {
    var maxLeft = window.innerWidth - root.offsetWidth - VIEWPORT_MARGIN;
    var maxTop = window.innerHeight - root.offsetHeight - VIEWPORT_MARGIN;
    var safeLeft = clamp(Math.round(left), VIEWPORT_MARGIN, maxLeft);
    var safeTop = clamp(Math.round(top), VIEWPORT_MARGIN, maxTop);

    root.style.left = safeLeft + 'px';
    root.style.top = safeTop + 'px';
    root.style.right = 'auto';
    root.style.bottom = 'auto';
    updateDockDirection(safeLeft, safeTop);

    if (persist) {
      try {
        window.localStorage.setItem(POSITION_KEY, JSON.stringify({ left: safeLeft, top: safeTop }));
      } catch (error) {
        // El asistente sigue siendo movible aunque el navegador bloquee storage.
      }
    }
  }

  function restoreAssistantPosition() {
    var saved = null;
    try {
      saved = JSON.parse(window.localStorage.getItem(POSITION_KEY));
    } catch (error) {
      saved = null;
    }
    if (saved && isFinite(saved.left) && isFinite(saved.top)) {
      placeAssistant(Number(saved.left), Number(saved.top), false);
      return;
    }
    var rect = root.getBoundingClientRect();
    updateDockDirection(rect.left, rect.top);
  }

  function beginDrag(event) {
    if (event.button !== undefined && event.button !== 0) return;
    var rect = root.getBoundingClientRect();
    dragState = {
      pointerId: event.pointerId,
      target: event.currentTarget,
      startX: event.clientX,
      startY: event.clientY,
      originLeft: rect.left,
      originTop: rect.top,
      moved: false
    };
    dragState.target.setPointerCapture(event.pointerId);
    root.classList.add('is-dragging');
  }

  function moveDrag(event) {
    if (!dragState || event.pointerId !== dragState.pointerId) return;
    var deltaX = event.clientX - dragState.startX;
    var deltaY = event.clientY - dragState.startY;
    if (!dragState.moved && Math.sqrt(deltaX * deltaX + deltaY * deltaY) < 5) return;
    dragState.moved = true;
    wasDragged = true;
    openPanel(false);
    clearRandomMotion();
    placeAssistant(dragState.originLeft + deltaX, dragState.originTop + deltaY, false);
    event.preventDefault();
  }

  function endDrag(event) {
    if (!dragState || event.pointerId !== dragState.pointerId) return;
    var moved = dragState.moved;
    dragState = null;
    root.classList.remove('is-dragging');
    var dragTarget = dragState && dragState.target ? dragState.target : event.currentTarget;
    if (dragTarget.hasPointerCapture(event.pointerId)) dragTarget.releasePointerCapture(event.pointerId);
    if (moved) {
      var rect = root.getBoundingClientRect();
      placeAssistant(rect.left, rect.top, true);
      scheduleRandomMotion();
      window.setTimeout(function () { wasDragged = false; }, 0);
    }
  }

  function isVisible(element) {
    return !!(element && (element.offsetWidth || element.offsetHeight || element.getClientRects().length));
  }

  function hasNoResults() {
    var selectors = '.dataTables_empty, .no-results, .search-no-results, .empty-state, [data-empty="true"]';
    var candidates = document.querySelectorAll(selectors);
    for (var i = 0; i < candidates.length; i += 1) {
      if (isVisible(candidates[i])) return true;
    }
    return false;
  }

  function isSearchField(element) {
    if (!element || element.tagName !== 'INPUT') return false;
    var signature = [element.type, element.name, element.id, element.className, element.placeholder]
      .join(' ')
      .toLowerCase();
    return /search|buscar|busqueda|búsqueda|pesquisar|pesquisa|filtro|filter/.test(signature);
  }

  function finishWork() {
    busyCount = Math.max(0, busyCount - 1);
    if (busyCount > 0) return;
    if (!userInteracted) {
      scheduleIdle();
      return;
    }
    window.setTimeout(function () {
      if (lastSearchInput && lastSearchInput.value.trim() && hasNoResults()) {
        setState('thinking', pickPhrase('noResults'), 6000);
        playReaction('reaction-shrug', 1900);
      } else {
        setState('idle', pickPhrase('completed'), 3800);
        playReaction('reaction-celebrate', 2100);
      }
      scheduleIdle();
    }, 450);
  }

  function beginWork(message, force) {
    busyCount += 1;
    if (!userInteracted && !force) return;
    var workState = currentState === 'book' ? 'book' : 'support';
    var workMessage = message || (workState === 'book'
      ? pickPhrase('searching')
      : pickPhrase('working'));
    setState(workState, workMessage, 3600);
    playRandomReaction(
      workState === 'book' ? ['reaction-searching', 'reaction-focus'] : ['reaction-working', 'reaction-focus'],
      1900 + Math.floor(Math.random() * 900)
    );
  }

  avatarButton.addEventListener('pointerdown', beginDrag);
  avatarButton.addEventListener('pointermove', moveDrag);
  avatarButton.addEventListener('pointerup', endDrag);
  avatarButton.addEventListener('pointercancel', endDrag);
  helpButton.addEventListener('pointerdown', beginDrag);
  helpButton.addEventListener('pointermove', moveDrag);
  helpButton.addEventListener('pointerup', endDrag);
  helpButton.addEventListener('pointercancel', endDrag);
  avatarButton.addEventListener('click', function () {
    if (wasDragged) {
      wasDragged = false;
      return;
    }
    openPanel();
    if (root.classList.contains('is-open')) playReaction('reaction-hello', 1700);
  });
  helpButton.addEventListener('click', function () {
    if (wasDragged) {
      wasDragged = false;
      return;
    }
    setCollapsed(false);
    if (isFreshLogin) {
      setState('idle', greetingByTime(), 6200);
      playReaction('reaction-hello', 1800);
    }
  });
  closeButton.addEventListener('click', function () { setCollapsed(true); helpButton.focus(); });
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !root.classList.contains('is-collapsed')) {
      setCollapsed(true);
      helpButton.focus();
    }
  });

  root.addEventListener('click', function (event) {
    var ticketLink = event.target.closest('[data-assistant-ticket]');
    if (ticketLink) {
      event.preventDefault();
      setState('support', translated('messages.ticket', 'Estoy preparando tu ticket en la computadora.'), 4000);
      openPanel(false);
      window.setTimeout(function () { window.location.href = ticketLink.href; }, 850);
      return;
    }
    var demo = event.target.closest('[data-assistant-demo]');
    if (!demo) return;
    setState(demo.getAttribute('data-assistant-demo'), translated('messages.consult', 'Voy a consultar una sugerencia para ti.'), 5000);
    openPanel(false);
    scheduleIdle();
  });

  document.addEventListener('input', function (event) {
    if (!isSearchField(event.target)) return;
    userInteracted = true;
    lastSearchInput = event.target;
    window.clearTimeout(searchTimer);
    if (!event.target.value.trim()) {
      setState('idle', translated('messages.new_search', 'Listo para una nueva búsqueda.'), 2600);
      scheduleIdle();
      return;
    }
    setState('book', pickPhrase('searching'), 3500);
    playRandomReaction(['reaction-searching', 'reaction-focus'], 1800 + Math.floor(Math.random() * 700));
    searchTimer = window.setTimeout(function () {
      if (hasNoResults()) {
        setState('thinking', pickPhrase('noResults'), 6000);
        playReaction('reaction-shrug', 1900);
      }
    }, 900);
    scheduleIdle();
  }, true);

  document.addEventListener('submit', function () {
    userInteracted = true;
    setState('support', translated('messages.processing', 'Estoy procesando la información en el sistema.'), 3600);
    playRandomReaction(['reaction-working', 'reaction-focus'], 2100 + Math.floor(Math.random() * 700));
  }, true);

  document.addEventListener('click', function (event) {
    var action = event.target.closest('button, a, input[type="button"], input[type="submit"]');
    if (!action || root.contains(action)) return;
    var actionText = [action.textContent, action.value, action.title, action.getAttribute('aria-label')]
      .join(' ')
      .toLowerCase();
    if (/buscar|consultar|sugerencia|recomendar|ayuda|search|find|suggest|help|pesquisar|consultar|sugestão|ajuda/.test(actionText)) {
      setState('book', translated('messages.search', 'Voy a buscar y consultar una sugerencia.'), 3600);
      playRandomReaction(['reaction-searching', 'reaction-focus'], 1900 + Math.floor(Math.random() * 600));
    } else if (/guardar|actualizar|procesar|generar|reparar|corregir|soporte|save|update|process|generate|repair|support|salvar|atualizar|processar|gerar|corrigir|suporte/.test(actionText)) {
      setState('support', translated('messages.working', 'Estoy trabajando en esa tarea.'), 3600);
      playRandomReaction(['reaction-working', 'reaction-focus'], 2100 + Math.floor(Math.random() * 700));
    } else if (/nuevo|crear|agregar|añadir|editar|abrir|ver|detalle|new|create|add|edit|open|view|detail|novo|criar|adicionar|editar|abrir|ver|detalhe/.test(actionText)) {
      playRandomReaction(['reaction-hello', 'reaction-focus'], 1600 + Math.floor(Math.random() * 600));
    }
  }, true);

  ['pointerdown', 'keydown', 'scroll'].forEach(function (eventName) {
    document.addEventListener(eventName, noteActivity, { passive: true, capture: true });
  });

  if ($ && $.fn) {
    $(document)
      .on('ajaxStart.aitrolAssistant', function () { beginWork(); })
      .on('ajaxStop.aitrolAssistant', finishWork)
      .on('draw.dt.aitrolAssistant', function () {
        if (lastSearchInput && lastSearchInput.value.trim() && hasNoResults()) {
          setState('thinking', pickPhrase('noResults'), 6000);
        }
      });
  }

  if (typeof window.fetch === 'function') {
    var nativeFetch = window.fetch;
    window.fetch = function () {
      beginWork();
      var request;
      try {
        request = nativeFetch.apply(this, arguments);
      } catch (error) {
        finishWork();
        throw error;
      }
      return request.then(function (response) {
        finishWork();
        return response;
      }, function (error) {
        finishWork();
        throw error;
      });
    };
  }

  window.AitrolAssistant = {
    setState: setState,
    idle: function (message) { setState('idle', message); scheduleIdle(); },
    thinking: function (message) { setState('thinking', message); scheduleIdle(); },
    searching: function (message) { setState('book', message); scheduleIdle(); },
    working: function (message) { beginWork(message, true); },
    done: finishWork,
    open: function () { setCollapsed(false); },
    close: function () { setCollapsed(true); }
  };

  window.addEventListener('aitrol:assistant', function (event) {
    var detail = event.detail || {};
    setState(detail.state || 'idle', detail.message, detail.duration);
    scheduleIdle();
  });

  window.addEventListener('resize', function () {
    if (dragState) return;
    var rect = root.getBoundingClientRect();
    placeAssistant(rect.left, rect.top, false);
  });

  restoreAssistantPosition();
  setCollapsed(true);
})(window, document, window.jQuery);
