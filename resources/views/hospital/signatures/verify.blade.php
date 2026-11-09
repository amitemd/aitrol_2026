<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validación de firma clínica</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef5f8; color: #17324d; font-family: Arial, Helvetica, sans-serif; }
        .page { display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 620px; overflow: hidden; border: 1px solid #d4e2e9; border-radius: 18px; background: #fff; box-shadow: 0 18px 50px rgba(21, 66, 92, .14); }
        .header { padding: 28px; color: #fff; background: linear-gradient(120deg, #07527f, #16a8bd); }
        .header h1 { margin: 0 0 6px; font-size: 22px; }
        .header p { margin: 0; opacity: .9; }
        .body { padding: 28px; }
        .status { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 22px; padding: 9px 14px; border-radius: 999px; font-size: 13px; font-weight: 800; letter-spacing: .03em; }
        .valid { color: #087443; background: #dcf7e9; }
        .invalid { color: #a32323; background: #fde5e5; }
        .row { padding: 13px 0; border-bottom: 1px solid #e5edf1; }
        .row:last-child { border-bottom: 0; }
        .label { display: block; margin-bottom: 4px; color: #69808e; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: 700; overflow-wrap: anywhere; }
        .fingerprint { font-family: Consolas, monospace; font-size: 12px; font-weight: 400; }
        .note { margin-top: 20px; color: #5f7684; font-size: 13px; line-height: 1.5; }
    </style>
</head>
<body>
<main class="page">
    <section class="card">
        <header class="header">
            <h1>Validación de firma clínica</h1>
            <p>{{$recordLabel}}</p>
        </header>
        <div class="body">
            <div class="status {{$verification['valid'] ? 'valid' : 'invalid'}}">
                {{$verification['valid'] ? '✓ FIRMA VALIDADA POR AITROL' : '✕ FIRMA NO VÁLIDA'}}
            </div>
            <div class="row"><span class="label">Resultado</span><span class="value">{{$verification['reason']}}</span></div>
            @if($verification['author_id'])
                <div class="row"><span class="label">Profesional</span><span class="value">{{$doctorName}}</span></div>
                <div class="row"><span class="label">Especialidad</span><span class="value">{{$doctorSpecialties}}</span></div>
                <div class="row"><span class="label">Fecha del registro</span><span class="value">{{$verification['recorded_at']}}</span></div>
                <div class="row"><span class="label">Huella SHA-256</span><span class="value fingerprint">{{$verification['fingerprint']}}</span></div>
            @endif
            <p class="note">AITROL recalculó la huella del registro clínico y comprobó su firma HMAC-SHA256. Cualquier modificación posterior invalida esta verificación.</p>
        </div>
    </section>
</main>
</body>
</html>
