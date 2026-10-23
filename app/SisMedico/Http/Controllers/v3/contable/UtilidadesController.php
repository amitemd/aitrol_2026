<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\complaint;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Notificacion;
use Sis_medico\v3\contable\ActivosFijos;
use Sis_medico\v3\contable\Auditorias;
use Sis_medico\v3\contable\Ciudades;
use Sis_medico\v3\contable\Clientes;
use Sis_medico\v3\contable\Contabilidad;
use Sis_medico\v3\contable\Eventos;
use Sis_medico\v3\contable\Productos;
use Sis_medico\v3\contable\Provincias;
use Sis_medico\v3\contable\PuntoEmision;
use Sis_medico\v3\contable\Utilidades;
use Sis_medico\v3\contable\Proveedor;
use Response;
use Sis_medico\v3\contable\Usuarios;

class UtilidadesController extends Controller
{
    private $controlador = 'utilidades';
    private $carpeta = 'contable';

    public function descargaArchivo($filename)
    {
        $path = storage_path('app/v3/compras/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)->header('Content-Type', $type);
    }
    
    public function activomostrar($filename)
    {
       
        $path = storage_path('app/v3/compras/archivos_activos/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)->header('Content-Type', $type);
    }

    

    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        //echo '<pre>';print_r($r->submodulo);exit;
        switch ($r->submodulo) {
            case 'verArchivo':
                return Utilidades::verArchivo($r->ruta);
                break;
            case 'saveAuditoria':
                return Auditorias::saveAuditoria($r);
                break;
            case 'buscarTodo':
                $data['title'] = 'Buscador general | ' . $info->nombre_info . ' V.' . $info->version_info;
                $data['busqueda'] = Utilidades::buscarTodo($r->textBuscador);
                $data['text'] = $r->textBuscador;
                $data['contenido'] = view('v3.layouts.view_resultado_busqueda');
                break;
            case 'provinciaCombo':
                return Provincias::getProvinciaCombo($r);
                break;
            case 'ciudadCombo':
                return Ciudades::getCiudadCombo($r);
                break;
            case 'clienteCombo':
                return Clientes::getClienteCombo($r->term);
                break;
            case 'proveedorCombo':
                return Proveedor::getProveedorCombo($r->term);
                break;
            case 'periodo':
                session(['periodo' => $r->anio]);
                return redirect('/v3/admin');
                break;
            case 'empresa':
                session(['id_empresa' => $r->id]);
                return redirect('/v3/admin');
                break;
            case 'guardarArchivo':
                return Utilidades::guardarArchivo($r);
                break;
            case 'validarFirma':
                return Utilidades::validarFirma($r);
                break;
            case 'leerTour':
                return Utilidades::leerTour($r);
                break;
            case 'savePlanillaPrincipal':
                return Utilidades::savePlanillaPrincipal($r);
                break;
            case 'validar_punto':
                return PuntoEmision::validarPunto($r->e);
                break;
            case 'viewPdf':
                $data['title'] = 'Visualizador de archivos | ' . $info->nombre_info . ' V.' . $info->version_info;
                $data['archivo'] = Utilidades::getarchivoView($r->id, $r->t);
                return view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_pdf', $data);
                break;
            case 'verAsiento':
                return Contabilidad::verAsiento($r->id);
                break;
            case 'obtenerPuntoEmision':
                return PuntoEmision::obtenerPuntoEmision($r->id);
                break;
            case 'movimientoProducto':
                return Productos::getMovimientos($r->codigo);
                break;
            case 'cambiodeclave':
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_cambio_clave';
                $data['obseraudi'] = 'Ingreso un cambiar la clave de usuario';
                $data['title'] = 'Cambio de clave | ' . $info->nombre_info . ' V.' . $info->version_info;
                $data['info'] = $info;
                return view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_cambio_clave', $data);
                break;
            case 'cambiarclave':
                $mensaje = '';
                session(['mensaje' => '']);
                if ($r->clave != $r->clave_) {
                    $mensaje = 'Las claves no coinciden';
                } elseif (strlen($r->clave) < 6) {
                    $mensaje = 'Las clave es menor de 6 dígitos';
                }
                if ($mensaje == '') {
                    $arrayDatos = [
                        'password' => bcrypt($r->clave)
                    ];
                    Usuarios::where('id',  Auth::user()->id)->update($arrayDatos);
                    $mensaje = 'Su clave fue cambiada correctamente, sera redirigido en: ';
                }
                $data['vista'] = 'v3.' . $this->controlador . '.view_cambio_clave';
                $data['obseraudi'] = 'Ingreso un cambiar la clave de usuario';
                $data['title'] = 'Cambio de clave | ' . $info->nombre_info . ' V.' . $info->version_info;
                $data['info'] = $info;
                session(['mensaje' => $mensaje]);
                return redirect('v3.' . $this->carpeta . '.' . $this->controlador . '.cambiodeclave');
                break;
            case 'calendario':
                $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
                $data['calendar'] = true;
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_calendario';
                $data['obseraudi'] = 'Ingreso a calendario';
                $data['titulo'] = "Nueva comprobante de egreso";
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_calendario');
                break;
            case 'traerNotificacion':
                return Eventos::notificaciones();
                break;
            case 'saveEvento':
                return Eventos::saveEvento($r);
                break;
            case 'listareventos':
                return Eventos::listarEventos();
                break;
            case 'modificarEvento':
                return Eventos::modificarEvento($r);
                break;
            case 'verNotificacion':
                return Eventos::verNotificacion($r);
                break;
            case 'revisarNotificacion':
                return Eventos::revisarNotificacion($r);
                break;
            case 'leerArchivoXMLCompra':
                return Utilidades::leerArchivoXMLCompra($r);
                break;
            case 'verNotificacionesGenerales':
                return Eventos::verNotificacionesGenerales();
                break;
            case 'guardarArchivoCompra':
                return Utilidades::guardarArchivoCompra($r);
                break;
            case 'eliminarArchivoCompra':
                return Utilidades::eliminarArchivoCompra($r);
                break;
            case 'eliminarArchivoActivo';
            return Utilidades::eliminarArchivoActivo($r);
                break;
            case 'subirActivo':
                return Utilidades::subirActivo($r);
                break;
            case 'cambiarEstadoImagen':
                return Utilidades::cambiarEstadoImagen($r);
                break;
            case 'obtenerImagenes':
                return ActivosFijos::obtenerImagenes($r);
                break;
            case 'dropzone':
                return Utilidades::dropzone($r->id);
                break;
            case 'guardarArchivoActivo':
                return Utilidades::guardarArchivoActivo($r);
                break;
            default:
                $data['contenido'] = view('v3.errors.view_404');
                break;
        }
        $newData = array_merge(config("data"), $data);
        config(['data' => $newData]);
        return view('v3.layouts.view_child');
    }
}
