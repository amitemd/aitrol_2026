<?php

use Aitrol\Configuracion\Domain\MenuRepository;
use Aitrol\Configuracion\Domain\OpcionMenu;
use App\Models\User;
use App\Services\CatalogoGeoNames;
use App\Services\MantenimientoServidor;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

Artisan::command('aitrol:mantenimiento-procesar', function (MantenimientoServidor $mantenimiento): int {
    $id = $mantenimiento->procesarPendiente();
    if ($id !== null) {
        $this->info('Solicitud de mantenimiento procesada #'.$id.'. Consulta su resultado en el panel.');
    }

    return 0;
})->purpose('Procesar una solicitud de mantenimiento con la cuenta de despliegue');

Event::listen(CommandStarting::class, static function (CommandStarting $evento): void {
    if ($evento->command !== 'serve') {
        return;
    }

    $directorios = getenv('PHP_INI_SCAN_DIR');
    $_ENV['PHP_INI_SCAN_DIR'] = $directorios.PATH_SEPARATOR.public_path();
    ServeCommand::$passthroughVariables[] = 'PHP_INI_SCAN_DIR';
});

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('aitrol:menu {--restaurar : Agregar las opciones faltantes a sus grupos existentes}', function (MenuRepository $menu): int {
    $modulos = [
        'inventarios' => ['Inventarios', 'inventarios.secciones'],
        'ventas' => ['Ventas', 'comercial.ventas.secciones'],
        'compras' => ['Compras', 'comercial.compras.secciones'],
        'activos-fijos' => ['Activos fijos', 'activos-fijos.secciones'],
        'caja-bancos' => ['Caja y bancos', 'caja-bancos.secciones'],
        'tributario' => ['Tributario', 'tributario.secciones'],
        'rrhh' => ['Recursos humanos', 'rrhh.secciones'],
    ];
    $catalogo = [];
    $sinRutas = false;

    foreach ($modulos as $modulo => [$grupo, $clave]) {
        $secciones = config($clave, []);
        if ($secciones === []) {
            $this->error("$grupo: no se cargó la configuración $clave.");
            $sinRutas = true;
        }

        $orden = 10;
        foreach ($secciones as $seccion => [$nombre, $icono]) {
            $ruta = "$modulo.$seccion.index";
            if (! Route::has($ruta)) {
                $this->error("$grupo: Laravel no registró la ruta $ruta.");
                $sinRutas = true;
            }
            $catalogo[$grupo][$ruta] = ['nombre' => $nombre, 'icono' => $icono, 'orden' => $orden];
            $orden += 10;
        }
    }

    if (! Schema::hasTable('menu')) {
        $this->error('La tabla del menú todavía no existe. Aplica las migraciones.');

        return 1;
    }

    $restaurar = $this->option('restaurar') && ! $sinRutas;
    $pendientes = [];
    $revisar = function () use ($menu, $catalogo, $restaurar, &$pendientes): void {
        $pendientes = [];
        $opciones = collect($menu->listar());
        $rutas = $opciones->whereNotNull('ruta')->keyBy('ruta');

        foreach ($catalogo as $grupo => $secciones) {
            $padre = $opciones->first(fn (OpcionMenu $opcion): bool => $opcion->ruta === null
                && $opcion->padreId === null && $opcion->nombre === $grupo);

            foreach (array_diff_key($secciones, $rutas->all()) as $ruta => $datos) {
                if ($restaurar && $padre !== null) {
                    $menu->guardar(null, $datos + [
                        'padre_id' => $padre->id, 'ruta' => $ruta, 'activo' => true, 'solo_administrador' => false,
                    ]);
                }
                $pendientes[] = [$grupo, $ruta, match (true) {
                    $padre === null => 'Falta el grupo original',
                    $restaurar => 'Restaurada',
                    default => 'Falta la opción',
                }];
            }
        }
    };

    if ($restaurar) {
        $menu->modificarJerarquia($revisar);
    } else {
        $revisar();
    }

    if ($pendientes !== []) {
        $this->table(['Grupo', 'Ruta', 'Estado'], $pendientes);
    }
    if ($sinRutas) {
        $this->error('Comprueba la revisión de Git y regenera el autoload de Composer antes de restaurar el menú.');

        return 1;
    }
    if (collect($pendientes)->contains(fn (array $fila): bool => $fila[2] !== 'Restaurada')) {
        return 1;
    }

    $this->info('Las rutas y opciones de los módulos revisados están registradas.');

    return 0;
})->purpose('Diagnosticar y restaurar opciones de los módulos del menú');

Artisan::command('ubicaciones:importar {--directorio= : Archivos locales de GeoNames} {--completo : Todos los lugares poblados}', function (CatalogoGeoNames $catalogo): int {
    try {
        $this->info('Cargando catálogo de GeoNames (CC BY 4.0)…');
        $totales = $catalogo->importar($this->option('directorio'), (bool) $this->option('completo'));
        $this->info(sprintf('%d países, %d provincias o estados y %d ciudades.', ...$totales));
    } catch (RuntimeException|\Illuminate\Http\Client\ConnectionException $error) {
        $this->error($error->getMessage());

        return 1;
    }

    return 0;
})->purpose('Importar la jerarquía geográfica internacional');

Artisan::command('aitrol:usuario-crear {email} {nombre} {--administrador : Crear una cuenta administrativa} {--clave-inicial : Admitir una clave inicial de 8 caracteres}', function (): int {
    if (! $this->input->isInteractive()) {
        $this->error('Ejecute el comando en una terminal interactiva para ingresar la contraseña.');

        return 1;
    }

    $administrador = (bool) $this->option('administrador');
    $claveInicial = (bool) $this->option('clave-inicial');

    if ($claveInicial && ! $administrador) {
        $this->error('La opción --clave-inicial requiere --administrador.');

        return 1;
    }

    $validator = Validator::make([
        'email' => $this->argument('email'),
        'name' => $this->argument('nombre'),
        'password' => $this->secret('Contraseña'),
        'password_confirmation' => $this->secret('Repita la contraseña'),
    ], [
        'email' => ['required', 'email:filter', 'max:255', 'unique:users,email'],
        'name' => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', $claveInicial ? 'min:8' : 'min:12', 'max:72', 'confirmed'],
    ]);

    if ($validator->fails()) {
        $this->error($validator->errors()->first());

        return 1;
    }

    $usuario = new User($validator->validated());
    $usuario->is_admin = $administrador;
    $usuario->save();

    $this->info($administrador ? 'Administrador creado.' : 'Usuario creado.');

    return 0;
})->purpose('Crear un usuario del ERP');

Artisan::command('aitrol:enlace-recuperacion {email}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();

    if ($usuario === null) {
        $this->error('No se encontró una cuenta con ese correo.');

        return 1;
    }

    $notificacion = new ResetPassword(Password::createToken($usuario));

    $this->info('Abre este enlace para definir una nueva contraseña. Es de un solo uso.');
    $this->line($notificacion->toMail($usuario)->actionUrl);

    return 0;
})->purpose('Generar un enlace de recuperación desde la consola');

Artisan::command('clientes:token {email} {nombre} {--escritura} {--dias=90}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $dias = filter_var($this->option('dias'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 365]]);

    if ($usuario === null || $dias === false) {
        $this->error('Indique un correo de usuario existente y una vigencia de 1 a 365 días.');

        return 1;
    }

    $permisos = ['clientes:leer'];

    if ($this->option('escritura')) {
        $permisos[] = 'clientes:escribir';
    }

    $token = $usuario->createToken($this->argument('nombre'), $permisos, now()->addDays($dias));
    $this->line($token->plainTextToken);

    return 0;
})->purpose('Emitir una credencial para la API de Clientes');

Artisan::command('clientes:revocar-token {email} {id}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $token = $usuario?->tokens()->find($this->argument('id'));

    if ($token === null || ! in_array('clientes:leer', $token->abilities, true)) {
        $this->error('No se encontró la credencial de Clientes para ese usuario.');

        return 1;
    }

    $token->delete();
    $this->info('Credencial revocada.');

    return 0;
})->purpose('Revocar una credencial de la API de Clientes');

Artisan::command('proveedores:token {email} {nombre} {--escritura} {--eliminacion} {--dias=90}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $dias = filter_var($this->option('dias'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 365]]);
    if ($usuario === null || $dias === false) {
        $this->error('Indique un correo de usuario existente y una vigencia de 1 a 365 días.');

        return 1;
    }

    $alcances = ['proveedores:leer'];
    if ($this->option('escritura')) {
        $alcances[] = 'proveedores:escribir';
    }
    if ($this->option('eliminacion')) {
        $alcances[] = 'proveedores:eliminar';
    }

    $credencial = $usuario->createToken($this->argument('nombre'), $alcances, now()->addDays($dias));
    $this->line($credencial->plainTextToken);

    return 0;
})->purpose('Emitir una credencial para la API de Proveedores');

Artisan::command('proveedores:revocar-token {email} {id}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $credencial = $usuario?->tokens()->find($this->argument('id'));
    if ($credencial === null || ! in_array('proveedores:leer', $credencial->abilities, true)) {
        $this->error('No se encontró la credencial de Proveedores para ese usuario.');

        return 1;
    }

    $credencial->delete();
    $this->info('Credencial revocada.');

    return 0;
})->purpose('Revocar una credencial de la API de Proveedores');

Artisan::command('usuarios:token {email} {nombre} {--escritura} {--dias=30}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->where('is_admin', true)->first();
    $dias = filter_var($this->option('dias'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 90]]);
    if ($usuario === null || $dias === false) {
        $this->error('Indique un administrador existente y una vigencia de 1 a 90 días.');

        return 1;
    }

    $permisos = ['usuarios:leer'];
    if ($this->option('escritura')) {
        $permisos[] = 'usuarios:escribir';
    }

    $token = $usuario->createToken($this->argument('nombre'), $permisos, now()->addDays($dias));
    $this->line($token->plainTextToken);

    return 0;
})->purpose('Emitir una credencial para la API de Usuarios');

Artisan::command('usuarios:revocar-token {email} {id}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $token = $usuario?->tokens()->find($this->argument('id'));
    if ($token === null || ! in_array('usuarios:leer', $token->abilities, true)) {
        $this->error('No se encontró la credencial de Usuarios para ese usuario.');

        return 1;
    }

    $token->delete();
    $this->info('Credencial revocada.');

    return 0;
})->purpose('Revocar una credencial de la API de Usuarios');

Artisan::command('inventarios:token {email} {nombre} {--escritura} {--dias=90}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->whereNotNull('id_empresa')->first();
    $dias = filter_var($this->option('dias'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 365]]);
    if ($usuario === null || $dias === false) {
        $this->error('Indique un usuario con empresa asignada y una vigencia de 1 a 365 días.');
        return 1;
    }

    $alcances = ['inventarios:leer'];
    if ($this->option('escritura')) {
        $alcances[] = 'inventarios:escribir';
    }

    $token = $usuario->createToken($this->argument('nombre'), $alcances, now()->addDays($dias));
    $this->line($token->plainTextToken);

    return 0;
})->purpose('Emitir una credencial para la API de Inventarios');

Artisan::command('inventarios:revocar-token {email} {id}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $token = $usuario?->tokens()->find($this->argument('id'));
    if ($token === null || ! in_array('inventarios:leer', $token->abilities, true)) {
        $this->error('No se encontró la credencial de Inventarios para ese usuario.');
        return 1;
    }

    $token->delete();
    $this->info('Credencial revocada.');

    return 0;
})->purpose('Revocar una credencial de la API de Inventarios');

Artisan::command('comercial:token {modulo} {email} {nombre} {--escritura} {--dias=90}', function (): int {
    $modulo = $this->argument('modulo');
    $usuario = User::query()->where('email', $this->argument('email'))->whereNotNull('id_empresa')->first();
    $dias = filter_var($this->option('dias'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 365]]);
    if (! in_array($modulo, ['ventas', 'compras'], true) || $usuario === null || $dias === false) {
        $this->error('Indique ventas o compras, un usuario con empresa y una vigencia de 1 a 365 días.');
        return 1;
    }

    $alcances = [$modulo.':leer'];
    if ($this->option('escritura')) $alcances[] = $modulo.':escribir';
    $this->line($usuario->createToken($this->argument('nombre'), $alcances, now()->addDays($dias))->plainTextToken);

    return 0;
})->purpose('Emitir una credencial para la API de Ventas o Compras');

Artisan::command('comercial:revocar-token {modulo} {email} {id}', function (): int {
    $modulo = $this->argument('modulo');
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $token = $usuario?->tokens()->find($this->argument('id'));
    if (! in_array($modulo, ['ventas', 'compras'], true) || $token === null || ! in_array($modulo.':leer', $token->abilities, true)) {
        $this->error('No se encontró la credencial comercial para ese usuario.');
        return 1;
    }

    $token->delete();
    $this->info('Credencial revocada.');

    return 0;
})->purpose('Revocar una credencial de la API de Ventas o Compras');

Artisan::command('activos-fijos:token {email} {nombre} {--escritura} {--dias=90}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->whereNotNull('id_empresa')->first();
    $dias = filter_var($this->option('dias'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 365]]);
    if ($usuario === null || $dias === false) {
        $this->error('Indique un usuario con empresa asignada y una vigencia de 1 a 365 días.');
        return 1;
    }

    $alcances = ['activos-fijos:leer'];
    if ($this->option('escritura')) $alcances[] = 'activos-fijos:escribir';
    $this->line($usuario->createToken($this->argument('nombre'), $alcances, now()->addDays($dias))->plainTextToken);

    return 0;
})->purpose('Emitir una credencial para la API de Activos fijos');

Artisan::command('activos-fijos:revocar-token {email} {id}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $token = $usuario?->tokens()->find($this->argument('id'));
    if ($token === null || ! in_array('activos-fijos:leer', $token->abilities, true)) {
        $this->error('No se encontró la credencial de Activos fijos.');
        return 1;
    }

    $token->delete();
    $this->info('Credencial revocada.');

    return 0;
})->purpose('Revocar una credencial de la API de Activos fijos');

Artisan::command('caja-bancos:token {email} {nombre} {--escritura} {--dias=90}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->whereNotNull('id_empresa')->first();
    $dias = filter_var($this->option('dias'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 365]]);
    if ($usuario === null || $dias === false) {
        $this->error('Indique un usuario con empresa asignada y una vigencia de 1 a 365 días.');
        return 1;
    }
    $alcances = ['caja-bancos:leer'];
    if ($this->option('escritura')) $alcances[] = 'caja-bancos:escribir';
    $this->line($usuario->createToken($this->argument('nombre'), $alcances, now()->addDays($dias))->plainTextToken);

    return 0;
})->purpose('Emitir una credencial para Caja y bancos');

Artisan::command('caja-bancos:revocar-token {email} {id}', function (): int {
    $usuario = User::query()->where('email', $this->argument('email'))->first();
    $token = $usuario?->tokens()->find($this->argument('id'));
    if ($token === null || ! in_array('caja-bancos:leer', $token->abilities, true)) {
        $this->error('No se encontró la credencial de Caja y bancos.');
        return 1;
    }
    $token->delete();
    $this->info('Credencial revocada.');

    return 0;
})->purpose('Revocar una credencial para Caja y bancos');

foreach (['contabilidad', 'tributario'] as $moduloFinanciero) {
    Artisan::command($moduloFinanciero.':token {email} {nombre} {--escritura} {--dias=90}', function () use ($moduloFinanciero): int {
        $usuario = User::query()->where('email', $this->argument('email'))->whereNotNull('id_empresa')->first();
        $dias = filter_var($this->option('dias'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 365]]);
        if ($usuario === null || $dias === false) {
            $this->error('Indique un usuario con empresa asignada y una vigencia de 1 a 365 días.');
            return 1;
        }
        $alcances = [$moduloFinanciero.':leer'];
        if ($this->option('escritura')) $alcances[] = $moduloFinanciero.':escribir';
        $this->line($usuario->createToken($this->argument('nombre'), $alcances, now()->addDays($dias))->plainTextToken);

        return 0;
    })->purpose('Emitir una credencial del módulo '.$moduloFinanciero);

    Artisan::command($moduloFinanciero.':revocar-token {email} {id}', function () use ($moduloFinanciero): int {
        $usuario = User::query()->where('email', $this->argument('email'))->first();
        $token = $usuario?->tokens()->find($this->argument('id'));
        if ($token === null || ! in_array($moduloFinanciero.':leer', $token->abilities, true)) {
            $this->error('No se encontró la credencial para ese usuario.');
            return 1;
        }
        $token->delete();
        $this->info('Credencial revocada.');

        return 0;
    })->purpose('Revocar una credencial del módulo '.$moduloFinanciero);
}
