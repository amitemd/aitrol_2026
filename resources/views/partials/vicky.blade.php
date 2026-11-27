<button class="vicky-launcher" type="button" aria-label="Abrir chat con Vicky" aria-haspopup="dialog" aria-controls="vicky-chat" data-vicky-open>
  <i class="bi bi-stars" aria-hidden="true"></i>
  <span>Vicky</span>
</button>

<dialog class="vicky-dialog" id="vicky-chat" aria-labelledby="vicky-title" data-endpoint="{{ route('vicky.consultar') }}">
  <header class="vicky-heading">
    <span class="vicky-icon" aria-hidden="true"><i class="bi bi-stars"></i></span>
    <div class="vicky-heading-text">
      <h2 id="vicky-title">Vicky</h2>
      <p>Asistente de Aitrol</p>
    </div>
    <button class="vicky-icon-button" type="button" data-vicky-reset aria-label="Nueva conversación" title="Nueva conversación">
      <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
    </button>
    <button class="vicky-icon-button" type="button" data-vicky-close aria-label="Cerrar chat" title="Cerrar chat">
      <i class="bi bi-x-lg" aria-hidden="true"></i>
    </button>
  </header>
  <div class="vicky-actions">
    <span class="vicky-access"><i class="bi bi-shield-check" aria-hidden="true"></i> Solo lectura</span>
    <div class="vicky-shortcuts" role="group" aria-label="Consultas rápidas">
      @can('permiso', ['ventas.reportes.index', 'leer'])
        <button type="button" data-vicky-question="¿Cuánto vendí este mes?"><i class="bi bi-bar-chart" aria-hidden="true"></i> Ventas del mes</button>
        <button type="button" data-vicky-question="¿Cuánto vendí este año?"><i class="bi bi-graph-up" aria-hidden="true"></i> Ventas del año</button>
      @endcan
      @can('permiso', ['compras.reportes.index', 'leer'])
        <button type="button" data-vicky-question="¿Cuánto compré este mes?"><i class="bi bi-bag-check" aria-hidden="true"></i> Compras del mes</button>
      @endcan
      @can('permiso', ['caja-bancos.cuentas.index', 'leer'])
        <button type="button" data-vicky-question="¿Cuál es mi saldo bancario?"><i class="bi bi-bank" aria-hidden="true"></i> Bancos</button>
      @endcan
      @can('permiso', ['clientes.reportes.cuentas-por-cobrar', 'leer'])
        <button type="button" data-vicky-question="¿Cuánto debo cobrar este mes?"><i class="bi bi-wallet2" aria-hidden="true"></i> Por cobrar</button>
      @endcan
      @can('permiso', ['proveedores.reportes.cuentas-por-pagar', 'leer'])
        <button type="button" data-vicky-question="¿Cuánto tengo en cuentas por pagar?"><i class="bi bi-receipt" aria-hidden="true"></i> Por pagar</button>
      @endcan
      <button type="button" data-vicky-question="Dame tres ideas para aumentar las ventas."><i class="bi bi-lightbulb" aria-hidden="true"></i> Estrategia de ventas</button>
    </div>
  </div>
  <section class="vicky-conversation" role="log" aria-label="Conversación" aria-relevant="additions text">
    <div class="vicky-welcome">
      <i class="bi bi-chat-square-text" aria-hidden="true"></i>
      <h3>¿Qué quieres consultar?</h3>
      <p>Pregunta por tus ventas, saldos bancarios o cartera. También puedes pedir ideas para vender más.</p>
    </div>
  </section>
  <form class="vicky-form">
    @csrf
    <label class="visually-hidden" for="vicky-pregunta">Pregunta para Vicky</label>
    <textarea id="vicky-pregunta" rows="2" maxlength="1000" placeholder="Escribe una pregunta…" required></textarea>
    <button class="vicky-send" type="submit">Preguntar <i class="bi bi-arrow-up-right" aria-hidden="true"></i></button>
    <small>Ctrl + Enter para enviar</small>
  </form>
</dialog>
