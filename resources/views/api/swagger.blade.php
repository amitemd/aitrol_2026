<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentación API · Aitrol</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.36.0/swagger-ui.css">
    <style>
        body { margin: 0; color: #19363b; background: #f5f9f8; font-family: system-ui, sans-serif; }
        .api-header { padding: 1.3rem clamp(1rem, 5vw, 4rem); background: #123a3c; color: #fff; }
        .api-header h1 { margin: 0 0 .4rem; font-size: 1.25rem; }
        .api-header a { color: #c5f3e4; text-underline-offset: .2em; }
        #api-status { margin: 1.5rem; }
    </style>
</head>
<body>
    <header class="api-header">
        <h1>Aitrol ERP · Documentación API</h1>
        <a href="{{ route('swagger.spec') }}">Abrir contrato OpenAPI JSON</a>
    </header>
    <main>
        <p id="api-status" role="status">Cargando Swagger UI…</p>
        <div id="swagger-ui" data-spec="{{ route('swagger.spec') }}"></div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5.36.0/swagger-ui-bundle.js"></script>
    <script>
        const panel = document.getElementById('swagger-ui');
        const statusMessage = document.getElementById('api-status');

        if (typeof SwaggerUIBundle === 'function') {
            statusMessage.hidden = true;
            SwaggerUIBundle({
                url: panel.dataset.spec,
                dom_id: '#swagger-ui',
                validatorUrl: null,
                persistAuthorization: false,
                queryConfigEnabled: false,
            });
        } else {
            statusMessage.textContent = 'Swagger UI no está disponible. Puedes abrir el contrato OpenAPI JSON.';
        }
    </script>
</body>
</html>
