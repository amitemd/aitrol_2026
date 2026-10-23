<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;
use Illuminate\Support\Facades\Auth;
use Sis_medico\v3\contable\Catalogos;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\v3\contable\ConfigSistema;
use Sis_medico\v3\contable\Configuraciones;
use Sis_medico\v3\contable\Empresas;
use Sis_medico\v3\contable\Eventos;
use Sis_medico\v3\contable\Info;
use Sis_medico\v3\contable\Menu;
use Sis_medico\v3\contable\PermisosUsuario;
use Sis_medico\v3\contable\Usuarios;
use Sis_medico\v3\contable\Utilidades;

class General
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($r, Closure $next)
    {
        if (Auth::user() == null) {
            if ($r->expectsJson() || $r->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No autenticado.',
                ], 401);
            }
            return redirect('/');
        }
        $arrayMensajesGenerales = [];
        $info = Info::getInfo();
        if ($info == '') {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'model' => 'Utilidades',
                'table' => 'new_db_info',
                'message' => 'La empresa no tiene configurado la tabla de información',
            ];
            array_push($arrayMensajesGenerales, $arrayRespuesta);
        }
        $data['submodulo'] = $r->submodulo;
        $data['info'] = $info;
        $data['menu'] = Menu::getMenu();
        $data['usuario'] = Usuarios::getUsuario();
        $data['permisos'] = PermisosUsuario::getPermisos(Auth::user()->id);
        $data['fechaCierreContable'] = Ct_Cierre_Cabecera::ultima_fecha();
        $data['empresa'] = Empresas::getEmpresa(session('id_empresa'));
        $data['empresas_'] = Empresas::getEmpresa('', 1);
        $data['ultimaConexion'] = Utilidades::ultimaConexion();
        $data['configSistema'] = ConfigSistema::getConfiguraciones();
        $existeIva = 0;
        try {
            $data['countNotificacion'] = Eventos::getCounNotificaciones();
            $data['datosNotificacion'] = Eventos::getdatosNotificacion();
            $arrayImpuestos = Configuraciones::getImpuestoIva();
            if ($arrayImpuestos != '') {
                $data['impuestoIvaPorcentaje'] = $arrayImpuestos->iva * 100;
                $data['impuestoIva'] = $arrayImpuestos->iva;
                $data['impuestoIcePorcentaje'] = $arrayImpuestos->ice * 100;
                $data['impuestoIce'] = $arrayImpuestos->ice;
                $existeIva = 1;
                if ($existeIva == 0) {
                    $arrayRespuesta = [
                        'status' => 'error',
                        'code' => 500,
                        'message' => 'La empresa no tiene configurado los impuestos (IVA)',
                    ];
                    array_push($arrayMensajesGenerales, $arrayRespuesta);
                }
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'La empresa no tiene configurado los impuestos (IVA)',
                ];
                array_push($arrayMensajesGenerales, $arrayRespuesta);
            }
        } catch (\Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'model' => 'Utilidades',
                'table' => 'new_db_catalogo',
                'message' => 'La empresa no tiene configurado el catalogo general',
                'message_' => $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine(),
            ];
            array_push($arrayMensajesGenerales, $arrayRespuesta);
        }
        $data['mensajesGenerales'] = $arrayMensajesGenerales;
        if (session('periodo') == date('Y'))
            session(['periodo' => date('Y')]);
        config(['data' => $data]);
        return $next($r);
    }
}
