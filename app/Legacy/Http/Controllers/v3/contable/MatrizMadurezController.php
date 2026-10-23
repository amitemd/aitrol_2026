<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\MatrizMadurez;

class MatrizMadurezController extends Controller
{
    /**
     * Muestra la linea base de madurez tecnica y funcional de la version v3.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $evaluacion = MatrizMadurez::calcular(MatrizMadurez::dimensiones());
        $revisiones = MatrizMadurez::revisiones();
        $modulos = MatrizMadurez::aplicarRevisiones(MatrizMadurez::modulos(), $revisiones);
        $resumenModulos = MatrizMadurez::resumirModulos($modulos);

        $data = [
            'controlador' => 'matriz-madurez',
            'carpeta' => 'contable',
            'submodulo_' => 'matriz-madurez',
            'title' => 'Matriz de madurez | Sistema v3',
            'vista' => 'v3.contable.madurez.view_matriz',
            'obseraudi' => 'Consulto la matriz de madurez del sistema v3',
            'dimensionesMadurez' => $evaluacion['dimensiones'],
            'modulosMadurez' => $modulos,
            'resumenModulos' => $resumenModulos,
            'revisionesMadurez' => array_reverse($revisiones),
            'planMejora' => MatrizMadurez::planMejora(),
            'madurezGlobal' => $evaluacion['total_redondeado'],
            'avanceFuncional' => 74,
            'preparacionProduccion' => 45,
            'fechaEvaluacion' => '15/07/2026',
            'nivelMadurez' => 'Funcional pero fragil',
            'prioridadesMadurez' => [
                'P1: ejecutar v3:api-check y PHPUnit v3 en el servidor antes de aprobar la API tributaria.',
                'P1: certificar con pruebas Contabilidad, Ventas, Compras, Caja/Bancos, Inventario y Tributario.',
                'P1: eliminar interrupciones de depuracion y reemplazar respuestas echo/exit por manejo uniforme de errores.',
                'P1: validar archivos, entradas, permisos por operacion, secuenciales e idempotencia de documentos.',
                'P2: dividir las clases principales en servicios pequenos y documentar responsables y evidencias.',
            ],
            'panelOperacionUrl' => route('v3.operacion-monitoreo'),
            'documentacionUrl' => route('v3.documentacion'),
            'puedeVerPanelOperacion' => Auth::check() && (int) Auth::user()->id_tipo_usuario === 1,
        ];

        $data['contenido'] = view($data['vista'], $data);
        $newData = array_merge(config('data', []), $data);
        config(['data' => $newData]);

        return view('v3.layouts.view_child');
    }
}
