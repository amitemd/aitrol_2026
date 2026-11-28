<?php

namespace App\Http\Controllers;

use App\Services\ContextoOperativo;
use App\Services\PanelAdministrativo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class SesionController extends Controller
{
    private const MAX_FALLOS = 3;

    private const BLOQUEO_SEGUNDOS = 300;

    public function inicio(Request $request, ContextoOperativo $contexto, PanelAdministrativo $panel): View|RedirectResponse
    {
        $usuario = Auth::user();
        if ($usuario === null) {
            return view('auth.login');
        }
        if (! $usuario->empresa()->exists()) {
            return view('auth.empresa-inicial');
        }
        $sucursal = $contexto->resolverSucursalWeb();
        if ($sucursal === null) {
            return to_route('configuracion.contexto.index');
        }
        if ($request->header('X-Aitrol-Fragmento') === 'menu') {
            return view('partials.adminlte-sidebar');
        }

        $productosRecientes = null;
        if (Gate::allows('permiso', ['inventarios.productos.index', 'leer'])
            && Route::has(['inventarios.productos.index', 'inventarios.productos.show'])
            && Schema::hasColumn('inv_productos', 'precio_venta')) {
            $consulta = DB::table('inv_productos')->where('id_empresa', $usuario->id_empresa)
                ->select(['id', 'codigo', 'nombre', 'descripcion', 'precio_venta']);

            if (Route::has('inventarios.productos.imagen') && Schema::hasTable('inv_producto_imagenes')) {
                $consulta->addSelect(['id_imagen' => DB::table('inv_producto_imagenes')
                    ->whereColumn('id_empresa', 'inv_productos.id_empresa')
                    ->whereColumn('id_producto', 'inv_productos.id')->orderBy('orden')->orderBy('id')->limit(1)->select('id')]);
            }

            $productosRecientes = $consulta->orderByDesc('created_at')->orderByDesc('id')->limit(4)->get();
        }

        $periodo = $contexto->periodo() ?? $contexto->anioActual();

        return view('adminlte.dashboard-v2', [
            ...$panel->datos($periodo, $sucursal->id),
            'productosRecientes' => $productosRecientes,
            'personalizable' => $panel->personalizable(),
            'preferencias' => $panel->preferencias($usuario->id),
            'ordenInicial' => PanelAdministrativo::ORDEN,
            'tiposGrafico' => PanelAdministrativo::GRAFICOS,
            'periodo' => $periodo,
            'sucursal' => $sucursal,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'string', 'email:filter'],
            'password' => ['required', 'string', 'max:255', function ($atributo, $clave, $fallar): void {
                if (is_string($clave) && (strlen($clave) > 72 || str_contains($clave, "\0"))) {
                    $fallar('Las credenciales son incorrectas.');
                }
            }],
        ]);

        $llave = 'aitrol:fallos-login:'.hash('sha256', Str::lower($credenciales['email']));
        $bloqueo = $llave.':bloqueo';
        if (RateLimiter::tooManyAttempts($bloqueo, 1)) {
            return $this->accesoBloqueado($request, RateLimiter::availableIn($bloqueo));
        }

        if (! Auth::attempt($credenciales)) {
            $fallos = RateLimiter::hit($llave, self::BLOQUEO_SEGUNDOS);
            if ($fallos >= self::MAX_FALLOS) {
                RateLimiter::hit($bloqueo, self::BLOQUEO_SEGUNDOS);

                return $this->accesoBloqueado($request, RateLimiter::availableIn($bloqueo));
            }

            throw ValidationException::withMessages(['email' => 'Las credenciales son incorrectas.'])
                ->redirectTo(route('dashboard'));
        }

        RateLimiter::clear($llave);
        $request->session()->regenerate();

        if (! $request->user()->empresa()->exists()) {
            return to_route('dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }

    private function accesoBloqueado(Request $request, int $segundos): RedirectResponse|JsonResponse
    {
        $minutos = max(1, (int) ceil($segundos / 60));
        $tiempo = $minutos === 1 ? '1 minuto' : $minutos.' minutos';
        $mensaje = 'Acceso bloqueado temporalmente. Intenta de nuevo en '.$tiempo.'.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $mensaje, 'errors' => ['email' => [$mensaje]]], 429)
                ->header('Retry-After', (string) max(1, $segundos));
        }

        return to_route('dashboard')->withInput($request->only('email'))->withErrors(['email' => $mensaje]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('idioma', app()->getLocale());

        return to_route('dashboard');
    }
}
