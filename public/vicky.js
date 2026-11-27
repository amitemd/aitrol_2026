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

  function addMessage(text, type) {
    welcome.hidden = true;
    const message = document.createElement('div');
    message.classList.add('vicky-message', `vicky-message--${type}`);
    const paragraph = document.createElement('p');
    paragraph.textContent = text;
    message.append(paragraph);
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
    table.createCaption().textContent = report.titulo;
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

  function setBusy(busy) {
    send.disabled = busy;
    shortcuts.forEach((shortcut) => { shortcut.disabled = busy; });
  }

  function errorMessage(status, payload) {
    if (status === 401 || status === 419) return 'La sesión caducó. Recarga Aitrol para volver a consultar.';
    if (status === 403) return 'Tu usuario no tiene permiso de lectura para esta consulta.';
    if (status === 429) return 'Llegaste al límite de consultas. Espera un minuto antes de volver a intentar.';
    return [422, 503].includes(status) && payload?.message
      ? payload.message
      : 'No pude completar la consulta. Intenta de nuevo.';
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
        body: JSON.stringify({ pregunta: question, tema_anterior: previousTopic }),
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
      const reply = addMessage(payload.respuesta, 'assistant');
      if (payload.reporte) addReport(reply, payload.reporte);
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
    chat.showModal();
    input.focus();
  });
  chat.querySelector('[data-vicky-close]').addEventListener('click', () => chat.close());
  chat.addEventListener('close', () => launcher.focus());
  chat.querySelector('[data-vicky-reset]').addEventListener('click', () => {
    activeRequest?.abort();
    activeRequest = null;
    previousTopic = null;
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
