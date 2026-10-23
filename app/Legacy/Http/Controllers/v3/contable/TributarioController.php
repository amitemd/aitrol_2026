<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Ats;
use Sis_medico\v3\contable\Compras;
use Sis_medico\v3\contable\DePasos;
use Sis_medico\v3\contable\PorcentajeRetenciones;
use Sis_medico\v3\contable\ReporteTributario;
use Sis_medico\v3\contable\Tributario;
use Sis_medico\v3\contable\Ventas;

class TributarioController extends Controller
{
    private $controlador = 'tributario';
    private $carpeta = 'contable';
    public function index(Request $r)
    {
        $info = config('data.info');
        $data['controlador'] = $this->controlador;
        $data['carpeta'] = $this->carpeta;
        $data['title'] = ucwords(strtolower($this->controlador)) . ' | ' . $info->nombre_info . ' V.' . $info->version_info;
        $data['submodulo_'] = $r->submodulo == 'index' ? $data['submodulo_'] = $this->controlador : $r->submodulo;
        switch ($r->submodulo) {
            case 'exportarXml':
                return Tributario::exportarXml($r->id);
                break;
            case 'reenviar':
                $error = $this->validarDocumento($r, true);
                if ($error !== null) {
                    return $error;
                }
                return Tributario::reenviar($r);
            case 'consultarSri':
                $error = $this->validarDocumento($r, true);
                if ($error !== null) {
                    return $error;
                }
                return Tributario::consultarEstadoSri($r);
            case 'verLog':
                $error = $this->validarDocumento($r);
                if ($error !== null) {
                    return $error;
                }
                return Tributario::verLog($r);
            case 'verComprobante':
                $error = $this->validarDocumento($r);
                if ($error !== null) {
                    return $error;
                }
                return Tributario::verComprobante($r);
            case 'verLogDocumento':
                $error = $this->validarDocumento($r);
                if ($error !== null) {
                    return $error;
                }
                return Tributario::verLogDocumento($r);
            case 'buscarDocumentos':
                $error = $this->validarBusquedaDocumentos($r);
                if ($error !== null) {
                    return $error;
                }
                return Tributario::buscarDocumentos($r);
            case 'gestiondocumentos':
                $data['table'] = true;
                $data['titulo'] = 'Gestión documentos';
                $data['pasos'] = DePasos::getDePasos();
                $data['fecha_hoy'] = Carbon::today(config('app.timezone'))->toDateString();
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_gestion_documentos';
                $data['obseraudi'] = 'Ingreso a gestion de documentos electronicos';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_gestion_documentos');
                break;
            case 'declaraciones':
                $data['table'] = true;
                $data['titulo'] = 'Declaraciones';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_declaraciones';
                $data['obseraudi'] = 'Ingreso a declaraciones';
                $data['porcentajes'] = PorcentajeRetenciones::getPorcentajes();
                if ($r->anio != '' && $r->mes != '') {
                    $data['input'] = $r->input();
                    $data['ventas'] = Tributario::getVentasMes($r);
                    $data['compras'] = Tributario::getComprasMes($r);
                    $data['atsGenerados'] = Ats::getAtsGenerados($r);
                    $data['f104'] = Tributario::formualrio104($r);
                } elseif ($r->checkSemestral) {
                    $data['input'] = $r->input();
                    $data['ventas'] = Tributario::getVentasMes($r);
                    $data['compras'] = Tributario::getComprasMes($r);
                    $data['atsGenerados'] = Ats::getAtsGenerados($r);
                    $data['f104'] = Tributario::formualrio104($r);
                }
                $data["mesM"] = $r->mes;
                $data["anioM"] = $r->anio;
                $data["reservation"] = $r->reservation;
                $data["checkSemestral"] = $r->checkSemestral;
                //dd($data);
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_declaraciones');
                break;
            case 'excelDeclaracion':
                return  ReporteTributario::reportesExcel($r);
                break;
            case 'ats':
                $data['table'] = true;
                $data['titulo'] = 'Anexo transaccional simplificado';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_ats';
                $data['obseraudi'] = 'Ingreso a declaraciones';
                $data['ventas'] = Ventas::ventasAts($r);
                $data['compras'] = Compras::comprasAts($r);
                if ($r->periodo != '') {
                    $data['input'] = $r;
                }
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_ats');
                break;
            case 'importardocumentos':
                $data['table'] = true;
                $data['titulo'] = 'Importar documentos';
                $data['vista'] = 'v3.' . $this->carpeta . '.' . $this->controlador . '.view_importar_documentos';
                $data['obseraudi'] = 'Ingreso a declaraciones';
                $data['contenido'] = view('v3.' . $this->carpeta . '.' . $this->controlador . '.view_importar_documentos');
                break;
            case 'subirArchivoSri':
                return  Tributario::subirArchivoSri($r);
                break;
            case 'guardarDeclaracion':
                return  Tributario::guardarDeclaracion($r);
                break;
            case 'excelTributacion':
                return  Excel::download(Tributario::excelDeclararion($r));
                break;
            default:
                $data['vista'] = 'v3.errors.view_404';
                $data['obseraudi'] = 'Ingreso un error por que no existe el submodulo';
                $data['contenido'] = view('v3.errors.view_404');
                break;
        }
        $newData = array_merge(config("data"), $data);
        config(['data' => $newData]);
        return view('v3.layouts.view_child');
    }

    private function validarDocumento(Request $r, $escritura = false)
    {
        if (!$r->isMethod('post')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Metodo no permitido para esta operacion.',
            ], 405);
        }

        $idValido = is_scalar($r->id) && ctype_digit((string) $r->id);
        $tipoValido = is_string($r->tipo)
            && in_array($r->tipo, ['Factura', 'Retención', 'Liquidación de compra', 'Nota de crédito', 'Guía de remisión'], true);
        if (!$idValido || !$tipoValido) {
            return response()->json([
                'status' => 'error',
                'message' => 'El documento solicitado no es valido.',
            ], 422);
        }

        if ($escritura && !auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'La sesion no se encuentra disponible.',
            ], 401);
        }

        return null;
    }

    private function validarBusquedaDocumentos(Request $r)
    {
        if (!$r->isMethod('post')) {
            return response()->json(['status' => 'error', 'message' => 'Metodo no permitido.'], 405);
        }

        $desdeTexto = is_string($r->desde) ? $r->desde : '';
        $hastaTexto = is_string($r->hasta) ? $r->hasta : '';
        $desde = \DateTime::createFromFormat('Y-m-d', $desdeTexto);
        $hasta = \DateTime::createFromFormat('Y-m-d', $hastaTexto);
        $fechasValidas = $desde && $hasta
            && $desde->format('Y-m-d') === $desdeTexto
            && $hasta->format('Y-m-d') === $hastaTexto;

        if (!$fechasValidas || $desde > $hasta || $desde->diff($hasta)->days > 92) {
            return response()->json([
                'status' => 'error',
                'message' => 'Seleccione un rango valido de hasta 92 dias.',
            ], 422);
        }

        if (!is_string($r->tipo) || !in_array($r->tipo, ['', '01', '03', '04', '06', '07'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El tipo de documento todavia no esta disponible en esta consulta.',
            ], 422);
        }

        if ($r->estado !== null && $r->estado !== ''
            && (!is_scalar($r->estado) || !ctype_digit((string) $r->estado))) {
            return response()->json([
                'status' => 'error',
                'message' => 'El estado seleccionado no es valido.',
            ], 422);
        }

        return null;
    }
}
