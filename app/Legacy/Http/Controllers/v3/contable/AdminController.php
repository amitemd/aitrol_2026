<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\Contabilidad;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Ventas;

class AdminController extends Controller
{
    private $controlador = 'admin';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        if (!isset(config('data.permisos')->id_submenu_permiso)) {
            $data['vista'] = 'v3.errors.view_404';
            $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
            $data['contenido'] = view('v3.errors.view_404');
            $newData = array_merge(config("data"), $data);
            config(['data' => $newData]);
            return view('v3.layouts.view_child');
        } elseif (config('data.permisos')->id_submenu_permiso == '') {
            $data['vista'] = 'v3.errors.view_404';
            $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
            $data['contenido'] = view('v3.errors.view_404');
            $newData = array_merge(config("data"), $data);
            config(['data' => $newData]);
            return view('v3.layouts.view_child');
        }
        try {
            $data['controlador'] = $this->controlador;
            $data['carpeta'] = $this->carpeta;
            $data['title'] = 'Bienvenid@ | ' . $info->nombre_info . ' V.' . $info->version_info;
            $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
            switch ($r->submodulo) {
                case '':
                    $data['chart'] = $r->chart;
                    $data['vista'] = 'v3.layouts.view_inicio';
                    $data['obseraudi'] = 'Ingreso al inicio';
                    //$data['accesos'] = Utilidades::accesos();
                    $data['ventas'] = Ventas::where('id_empresa', session('id_empresa'))
                        ->whereYear('fecha', session('periodo'))
                        ->orderBy('id', 'desc')
                        ->limit(5)
                        ->get();
                    $data['compras'] = Compras::join('proveedor as p', 'ct_compras.proveedor', 'p.id')
                        ->where('id_empresa', session('id_empresa'))
                        ->whereYear('fecha', session('periodo'))
                        ->orderBy('id', 'desc')
                        ->limit(5)
                        ->get([
                            'ct_compras.*',
                            'p.razonsocial as proveedor'
                        ]);
                    $data['contenido'] = view('v3.layouts.view_inicio');
                    break;
                case 'valorVentas':
                    return Ventas::valoresVentas();
                    break;
                case 'valorCompras':
                    //return Compras::valoresCompras();
                    return [];
                    break;
                case 'valorUtilidad':
                    //return Contabilidad::valorUtilidad();
                    return [];
                    break;
                default:
                    $data['vista'] = 'v3.errors.view_404';
                    $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
                    $data['contenido'] = view('v3.errors.view_404');
                    break;
            }
        } catch (\Exception $e) {
            echo '<pre>';
            print_r(config('data.mensajesGenerales'));
            exit;
        }
        $newData = array_merge(config("data"), $data);
        config(['data' => $newData]);
        return view('v3.layouts.view_child');
    }
}
