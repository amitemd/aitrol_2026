<?php

namespace App\Http\Controllers;

use App\Services\ContextoOperativo;
use App\Services\EmpresaActiva;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class ImportacionesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EmpresaActiva $empresa, ContextoOperativo $contexto): View
    {
        $idEmpresa = $empresa->id();
        $sucursal = $contexto->resolverSucursalWeb();
        $productos = DB::table('inv_productos')->where('id_empresa', $idEmpresa)->where('activo', true)->count();
        $personas = Gate::allows('administrar-personas')
            ? DB::table('personas')->whereNotNull('identificacion')->exists()
            : null;

        $sucursales = $contexto->opciones()->count();
        $bodegas = $sucursal === null ? 0 : DB::table('inv_bodegas')->where('id_empresa', $idEmpresa)
            ->where('id_sucursal', $sucursal->id)->where('activo', true)->count();
        $clientes = DB::table('erp_clientes')->where('id_empresa', $idEmpresa)->where('activo', true)->whereNull('deleted_at')->count();
        $proveedores = DB::table('erp_proveedores')->where('id_empresa', $idEmpresa)->where('activo', true)->whereNull('deleted_at')->count();
        $estadoCategorias = DB::table('inv_categorias as categoria')
            ->leftJoin('inv_plantillas_contables as plantilla', fn ($union) => $union
                ->on('plantilla.id', '=', 'categoria.id_plantilla_contable')->on('plantilla.id_empresa', '=', 'categoria.id_empresa')
                ->where('plantilla.activo', true))
            ->where('categoria.id_empresa', $idEmpresa)->where('categoria.activo', true)
            ->selectRaw('COUNT(*) as total, COUNT(CASE WHEN plantilla.id IS NULL THEN 1 END) as sin_plantilla')->first();
        $categorias = (int) $estadoCategorias->total;
        $sinPlantilla = (int) $estadoCategorias->sin_plantilla;
        $rutaCategorias = match (true) {
            $sucursal === null => 'configuracion.contexto.index',
            $categorias === 0 => 'inventarios.categorias.create',
            default => 'inventarios.categorias.index',
        };
        $integracion = DB::table('co_integraciones')->where('id_empresa', $idEmpresa)->first(['activa', 'fecha_inicio', 'reglas']);
        $reglas = count(array_filter(json_decode($integracion->reglas ?? '{}', true, flags: JSON_THROW_ON_ERROR)));
        $integracionActiva = (bool) ($integracion->activa ?? false) && filled($integracion->fecha_inicio ?? null) && $reglas > 0;
        $detalleIntegracion = match (true) {
            $integracionActiva => trans_choice(':count regla con cuenta asignada. Revisa las que apliquen a tus operaciones.|:count reglas con cuenta asignada. Revisa las que apliquen a tus operaciones.', $reglas),
            ! ($integracion->activa ?? false) => 'Activa la contabilización automática y vincula las cuentas.',
            blank($integracion->fecha_inicio) => 'Indica desde qué fecha contabilizar las operaciones.',
            default => 'Vincula las cuentas de las operaciones que vas a registrar.',
        };

        $pasos = [
            ['Sucursales', $sucursales > 0, $sucursales > 0 ? trans_choice(':count sucursal registrada.|:count sucursales registradas.', $sucursales) : 'Registra la matriz o tu primer establecimiento.', 'configuracion.sucursales.index'],
            ['Bodegas', $bodegas > 0, $sucursal === null ? 'Selecciona una sucursal para revisar sus bodegas.'
                : ($bodegas > 0 ? trans_choice(':count bodega activa en :sucursal.|:count bodegas activas en :sucursal.', $bodegas, ['sucursal' => $sucursal->nombre])
                    : 'Registra una bodega activa en '.$sucursal->nombre.'.'), $sucursal === null ? 'configuracion.contexto.index' : 'inventarios.bodegas.index'],
            ['Categorías de productos', $categorias > 0, $categorias > 0
                ? trans_choice(':count categoría activa.|:count categorías activas.', $categorias)
                : 'Crea tu primera categoría para organizar los productos.', $rutaCategorias],
            ['Plantillas contables por categoría', $categorias > 0 && $sinPlantilla === 0, match (true) {
                $categorias === 0 => 'Crea una categoría antes de asignarle una plantilla.',
                $sinPlantilla > 0 => trans_choice(':count categoría activa necesita una plantilla.|:count categorías activas necesitan una plantilla.', $sinPlantilla),
                default => 'Todas las categorías activas tienen una plantilla activa.',
            }, $categorias === 0 || $sucursal === null ? $rutaCategorias : 'inventarios.plantillas-contables.index'],
            ['Clientes', $clientes > 0, $clientes > 0 ? trans_choice(':count cliente activo.|:count clientes activos.', $clientes) : 'Vincula tu primer cliente con una persona.', 'clientes.index'],
            ['Proveedores', $proveedores > 0, $proveedores > 0 ? trans_choice(':count proveedor activo.|:count proveedores activos.', $proveedores) : 'Registra tu primer proveedor.', 'proveedores.index'],
            ['Integración contable', $integracionActiva, $detalleIntegracion, $sucursal === null ? 'configuracion.contexto.index' : 'contabilidad.integraciones.index'],
        ];

        return view('configuracion::importaciones', compact('pasos', 'productos', 'personas'));
    }
}
