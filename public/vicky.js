(() => {
  const widget = document.querySelector('[data-vicky-widget]');
  if (window !== window.top || !widget) return;

  widget.replaceWith(widget.content);
  const chat = document.querySelector('#vicky-chat');

  const launcher = document.querySelector('[data-vicky-open]');
  const conversation = chat.querySelector('.vicky-conversation');
  const welcome = chat.querySelector('.vicky-welcome');
  const form = chat.querySelector('.vicky-form');
  const input = form.querySelector('textarea');
  const send = form.querySelector('button');
  const shortcuts = [...chat.querySelectorAll('[data-vicky-question]')];
  const csrf = form.querySelector('[name="_token"]').value;
  let activeRequest = null;
  let previousTopic = null;
  let previousGuide = null;

  function appendInline(element, text) {
    let end = 0;
    for (const match of text.matchAll(/\*\*([^*\n]+)\*\*|__([^_\n]+)__|`([^`\n]+)`|\*([^*\n]+)\*/gu)) {
      element.append(text.slice(end, match.index));
      const inline = document.createElement(match[3] ? 'code' : match[4] ? 'em' : 'strong');
      inline.textContent = match[1] ?? match[2] ?? match[3] ?? match[4];
      element.append(inline);
      end = match.index + match[0].length;
    }
    element.append(text.slice(end));
  }

  function tableCells(line) {
    const cells = (line ?? '').trim().split(/(?<!\\)\|/u).map((cell) => cell.trim().replaceAll('\\|', '|'));
    if (cells[0] === '' && cells.length > 1) cells.shift();
    if (cells.at(-1) === '' && cells.length > 1) cells.pop();
    return cells;
  }

  function appendAnswer(message, text) {
    const lines = String(text).replace(/\r\n?/gu, '\n').split('\n');
    const contents = document.createElement('div');
    contents.className = 'vicky-answer';
    const headingPattern = /^\s*(#{1,6})\s+(.+)$/u;
    const bulletPattern = /^\s*(?:[-+*]|(\d{1,5})[.)])\s+(.+)$/u;
    const tableHeader = (index) => {
      const headers = tableCells(lines[index]);
      const rules = tableCells(lines[index + 1]);
      return headers.length > 1 && rules.length > 1 && rules.length <= headers.length
        && rules.every((rule) => /^:?-{3,}:?$/u.test(rule)) ? headers : null;
    };

    for (let index = 0; index < lines.length;) {
      const line = lines[index];
      if (!line.trim()) {
        index += 1;
        continue;
      }

      const headers = tableHeader(index);
      if (headers) {
        const region = document.createElement('div');
        region.className = 'vicky-answer-table';
        region.tabIndex = 0;
        region.setAttribute('role', 'region');
        region.setAttribute('aria-label', 'Tabla de la respuesta');
        const table = document.createElement('table');
        const header = table.createTHead().insertRow();
        headers.forEach((label) => {
          const cell = document.createElement('th');
          cell.scope = 'col';
          appendInline(cell, label);
          header.append(cell);
        });
        const body = table.createTBody();
        index += 2;
        while (index < lines.length && !tableHeader(index)) {
          const values = tableCells(lines[index]);
          if (values.length < 2 && !/^\s*\|/u.test(lines[index])) break;
          const row = body.insertRow();
          headers.forEach((_, column) => {
            const value = column === headers.length - 1 ? values.slice(column).join(' | ') : (values[column] ?? '');
            appendInline(row.insertCell(), value);
          });
          index += 1;
        }
        region.append(table);
        contents.append(region);
        continue;
      }

      const heading = line.match(headingPattern);
      if (heading) {
        const title = document.createElement(`h${Math.min(5, Math.max(3, heading[1].length))}`);
        appendInline(title, heading[2]);
        contents.append(title);
        index += 1;
        continue;
      }

      let bullet = line.match(bulletPattern);
      if (bullet) {
        const ordered = bullet[1] !== undefined;
        const list = document.createElement(ordered ? 'ol' : 'ul');
        if (ordered) list.start = Number(bullet[1]);
        do {
          const item = document.createElement('li');
          appendInline(item, bullet[2]);
          list.append(item);
          index += 1;
          bullet = lines[index]?.match(bulletPattern);
        } while (bullet && (bullet[1] !== undefined) === ordered);
        contents.append(list);
        continue;
      }

      const paragraph = document.createElement('p');
      const first = index++;
      while (lines[index]?.trim() && !headingPattern.test(lines[index])
        && !bulletPattern.test(lines[index]) && !tableHeader(index)) index += 1;
      appendInline(paragraph, lines.slice(first, index).join('\n'));
      contents.append(paragraph);
    }

    message.append(contents);
  }

  function addMessage(text, type) {
    welcome.hidden = true;
    const message = document.createElement('div');
    message.classList.add('vicky-message', `vicky-message--${type}`);
    if (type === 'assistant') {
      appendAnswer(message, text);
    } else {
      const paragraph = document.createElement('p');
      paragraph.textContent = text;
      message.append(paragraph);
    }
    conversation.append(message);
    conversation.scrollTop = conversation.scrollHeight;
    return message;
  }

  function reportToCsv(report) {
    const rows = [report.columnas, ...report.filas];
    const contents = rows.map((row) => row.map((cell) => {
      const value = String(cell ?? '');
      const safe = /^[\s\u0000-\u001f]*[=+\-@]|^[\t\r\n]/u.test(value) ? "'" + value : value;
      return '"' + safe.replaceAll('"', '""') + '"';
    }).join(',')).join('\r\n');
    const url = URL.createObjectURL(new Blob(['\ufeff', contents], { type: 'text/csv;charset=utf-8;' }));
    const link = document.createElement('a');
    link.href = url;
    link.download = 'vicky-reporte.csv';
    link.click();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
  }

  function addReport(message, report) {
    const container = document.createElement('div');
    container.className = 'vicky-report';
    container.tabIndex = 0;
    container.setAttribute('role', 'region');
    container.setAttribute('aria-label', report.titulo);

    const table = document.createElement('table');
    table.createCaption().textContent = report.origen === 'base_datos_autorizada'
      ? report.titulo + ' · Base de datos de Aitrol (empresa activa)'
      : report.titulo;
    const header = table.createTHead().insertRow();
    report.columnas.forEach((label) => {
      const cell = document.createElement('th');
      cell.scope = 'col';
      cell.textContent = label;
      header.append(cell);
    });
    const body = table.createTBody();
    report.filas.forEach((values) => {
      const row = body.insertRow();
      values.forEach((value) => { row.insertCell().textContent = value; });
    });

    container.append(table);
    const download = document.createElement('button');
    download.type = 'button';
    download.className = 'vicky-export';
    download.textContent = 'Descargar CSV';
    download.addEventListener('click', () => reportToCsv(report));
    message.append(container, download);
    conversation.scrollTop = message.offsetTop;
  }

  function addDestination(message, destination) {
    const url = new URL(destination.url, location.href);
    const form = destination.modal ? new URL(destination.modal, location.href) : null;
    if (url.origin !== location.origin || (form && form.origin !== location.origin)) return;

    const link = document.createElement('a');
    link.className = 'vicky-export d-inline-block';
    link.href = form?.href || url.href;
    link.textContent = destination.titulo;

    function openDestination() {
      chat.close('navigate');
      const opened = !window.dispatchEvent(new CustomEvent('aitrol:abrir-destino', {
        cancelable: true,
        detail: { key: destination.ruta, label: destination.titulo, url, form },
      }));
      if (!opened) location.assign(link.href);
    }

    link.addEventListener('click', (event) => {
      if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
      event.preventDefault();
      openDestination();
    });
    message.append(link);
    conversation.scrollTop = conversation.scrollHeight;
    if (destination.abrir && chat.open) openDestination();
  }

  function setBusy(busy) {
    send.disabled = busy;
    shortcuts.forEach((shortcut) => { shortcut.disabled = busy; });
  }

  function errorMessage(status, payload) {
    if (status === 401 || status === 419) return 'La sesión caducó. Recarga Aitrol para volver a consultar.';
    if (status === 403) return 'Tu usuario no tiene permiso de lectura para esta consulta.';
    if (status === 429) return 'Llegaste al límite de consultas. Espera un minuto antes de volver a intentar.';
    if ([404, 422, 503].includes(status) && payload?.message) return payload.message;
    if (status >= 500) return 'El servidor de Aitrol no pudo procesar la consulta. Intenta de nuevo.';
    return 'No pude completar la consulta. Intenta de nuevo.';
  }

  function addGuideFeedback(message, payload) {
    const agentNames = {
      accounting: 'Contabilidad', finance: 'Finanzas', tax: 'Tributación', secretary: 'Secretaría',
      erp: 'Procesos del ERP', nursing: 'Enfermería', physician: 'Coordinación médica',
    };
    const controls = document.createElement('section');
    controls.className = 'vicky-feedback';
    controls.setAttribute('aria-label', 'Valorar la respuesta');
    const agent = document.createElement('small');
    agent.textContent = Object.hasOwn(agentNames, payload.agente)
      ? agentNames[payload.agente] : 'Especialista de Vicky';
    message.prepend(agent);
    const status = document.createElement('p');
    status.setAttribute('role', 'status');
    const actions = document.createElement('div');
    actions.className = 'vicky-feedback-actions';
    const buttons = [];
    let sending = false;
    let rated = false;

    async function rate(valoracion, correccion) {
      if (sending) return false;
      sending = true;
      buttons.forEach((button) => { button.disabled = true; });
      status.textContent = 'Guardando…';
      try {
        const response = await fetch(chat.dataset.valoracion, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
          body: JSON.stringify({
            consulta_id: payload.consulta_id,
            valoracion,
            ...(correccion ? { correccion, confirmada: true } : {}),
          }),
        });
        const result = await response.json().catch(() => null);
        if (!response.ok || !result?.guardada) {
          status.textContent = errorMessage(response.status, result);
          return false;
        }
        rated = true;
        status.textContent = correccion ? 'Corrección confirmada y guardada.' : 'Valoración guardada.';
        return true;
      } catch {
        status.textContent = 'No pude guardar la valoración. Intenta de nuevo.';
        return false;
      } finally {
        sending = false;
        buttons.forEach((button) => { button.disabled = rated; });
      }
    }

    for (const [rating, label] of [['helpful', 'Me sirvió'], ['needs_review', 'Necesita revisión']]) {
      const button = document.createElement('button');
      button.type = 'button';
      button.textContent = label;
      button.addEventListener('click', () => rate(rating));
      buttons.push(button);
      actions.append(button);
    }
    controls.append(actions);

    if (payload.pasos_correccion && Object.keys(payload.pasos_correccion).length) {
      const details = document.createElement('details');
      const summary = document.createElement('summary');
      summary.textContent = 'Aportar una corrección';
      const fields = document.createElement('form');
      const labeledSelect = (label, options) => {
        const field = document.createElement('label');
        field.className = 'vicky-feedback-field';
        field.textContent = label;
        const select = document.createElement('select');
        select.setAttribute('data-select2-nativo', '');
        Object.entries(options).forEach(([value, text]) => { select.add(new Option(text, value)); });
        field.append(select);
        fields.append(field);
        return select;
      };
      const step = labeledSelect('Paso del proceso', payload.pasos_correccion);
      const relation = labeledSelect('Regla', {
        requires_review: 'Requiere revisión', requires_approval: 'Requiere aprobación',
        before: 'Va antes de', after: 'Va después de',
      });
      const relatedStep = labeledSelect('Otro paso', payload.pasos_correccion);
      relatedStep.parentElement.hidden = true;
      relation.addEventListener('change', () => {
        relatedStep.parentElement.hidden = !['before', 'after'].includes(relation.value);
      });
      const checkLabel = document.createElement('label');
      const check = document.createElement('input');
      check.type = 'checkbox';
      check.required = true;
      checkLabel.append(check, ' Confirmo que esta regla aplica en mi empresa.');
      const save = document.createElement('button');
      save.type = 'submit';
      save.textContent = 'Guardar corrección';
      fields.append(checkLabel, save);
      fields.addEventListener('submit', async (event) => {
        event.preventDefault();
        const correction = { step: step.value, relation: relation.value };
        if (!relatedStep.parentElement.hidden) {
          if (step.value === relatedStep.value) {
            status.textContent = 'Selecciona dos pasos distintos.';
            return;
          }
          correction.relatedStep = relatedStep.value;
        }
        save.disabled = true;
        if (await rate('needs_review', correction)) {
          details.remove();
        } else {
          save.disabled = false;
        }
      });
      details.append(summary, fields);
      controls.append(details);
    }
    controls.append(status);
    message.append(controls);
    conversation.scrollTop = conversation.scrollHeight;
  }

  async function ask(question) {
    question = question.trim();
    if (!question || activeRequest) return;

    const controller = new AbortController();
    activeRequest = controller;
    input.value = '';
    setBusy(true);
    addMessage(question, 'user');
    const pending = addMessage('Consultando…', 'status');

    try {
      const response = await fetch(chat.dataset.endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({ pregunta: question, tema_anterior: previousTopic, guia_anterior: previousGuide }),
        signal: controller.signal,
      });
      const payload = await response.json().catch(() => null);
      if (controller.signal.aborted) return;

      pending.remove();
      if (!response.ok || !payload?.respuesta) {
        addMessage(errorMessage(response.status, payload), 'status');
        input.value ||= question;
        return;
      }

      previousTopic = payload.tema ?? null;
      previousGuide = payload.consulta_id ?? null;
      const reply = addMessage(payload.respuesta, 'assistant');
      if (payload.reporte) addReport(reply, payload.reporte);
      if (payload.destino) addDestination(reply, payload.destino);
      if (Array.isArray(payload.sugerencias)) {
        payload.sugerencias.forEach((destination) => addDestination(reply, { ...destination, abrir: false }));
      }
      if (previousGuide) addGuideFeedback(reply, payload);
    } catch (error) {
      if (controller.signal.aborted) return;
      pending.remove();
      addMessage('No pude conectar con Aitrol. Intenta de nuevo.', 'status');
      input.value ||= question;
    } finally {
      if (activeRequest === controller) {
        activeRequest = null;
        setBusy(false);
      }
    }
  }

  launcher.addEventListener('click', () => {
    chat.returnValue = '';
    chat.showModal();
    input.focus();
  });
  chat.querySelector('[data-vicky-close]').addEventListener('click', () => chat.close());
  chat.addEventListener('close', () => {
    if (chat.returnValue !== 'navigate') launcher.focus();
  });
  chat.querySelector('[data-vicky-reset]').addEventListener('click', () => {
    activeRequest?.abort();
    activeRequest = null;
    previousTopic = null;
    previousGuide = null;
    setBusy(false);
    input.value = '';
    conversation.replaceChildren(welcome);
    welcome.hidden = false;
    input.focus();
  });
  form.addEventListener('submit', (event) => {
    event.preventDefault();
    ask(input.value);
  });
  shortcuts.forEach((button) => {
    button.addEventListener('click', () => ask(button.dataset.vickyQuestion));
  });
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && (event.ctrlKey || event.metaKey) && !event.isComposing) {
      event.preventDefault();
      form.requestSubmit();
    }
  });
})();
