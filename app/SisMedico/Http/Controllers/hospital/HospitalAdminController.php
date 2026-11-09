<?php

namespace Sis_medico\Http\Controllers\hospital;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Response;
use Sis_medico\Cama;
use Sis_medico\CamaPaciente;
use Sis_medico\CamaTransaccion;
use Sis_medico\Habitacion;
use Sis_medico\Piso;
use Sis_medico\Paciente;
use Sis_medico\Tipo_Habitacion;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Hospital;
use Sis_medico\AgendaQ;
use Sis_medico\Hospital_Producto;
use Sis_medico\Pais;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Illuminate\Support\Facades\Storage;

class HospitalAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol_new($opcion)
    {
        if (Auth::user()->isSystemUser()) {
            return false;
        }
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin/index');
    }
    public function modalfarmacia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin/modalfarmacia');
    }
    public function gestionh()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        
        $habitacion = $this->empresaQuery(Habitacion::with(['tipo', 'piso']), 'habitacion')
            ->orderBy('id_piso')->orderBy('codigo')->get();
        $todasCamas = $this->empresaQuery(Cama::with(['transaccion' => function ($query) {
            // Solo el estado más reciente; el historial completo no se muestra aquí.
            $query->whereIn('id', CamaTransaccion::selectRaw('MAX(id)')->groupBy('id_cama'));
        }]), 'cama')->get();

        // Las tarjetas y los contadores reutilizan los mismos modelos y relaciones.
        $habitacionesPorId = $habitacion->keyBy('id');
        $camasPorHabitacion = $todasCamas->groupBy('id_habitacion');
        foreach ($habitacion as $cuarto) {
            $cuarto->setRelation('cama', $camasPorHabitacion->get($cuarto->id, new \Illuminate\Database\Eloquent\Collection));
        }
        foreach ($todasCamas as $camita) {
            $camita->setRelation('habitacion', $habitacionesPorId->get($camita->id_habitacion));
        }
        $cama = $todasCamas->filter(function ($camita) {
            return $camita->estado !== null && (string) $camita->estado !== '0';
        })->values();

        $bedIds = $todasCamas->modelKeys();
        $hospitalizaciones = Ho_Hospitalizacion::where('estado', 1)->whereIn('id_cama', $bedIds)
            ->whereIn('id', Ho_Hospitalizacion::where('estado', 1)->whereIn('id_cama', $bedIds)
                ->selectRaw('MAX(id)')->groupBy('id_cama'))
            ->get()->keyBy('id_cama');
        $asignaciones = CamaPaciente::where('estado', 1)->whereIn('id_cama', $bedIds)->get();
        $traspasos = Ho_Traspaso_Sala008::whereIn('id', $hospitalizaciones->pluck('id_traspaso')->filter()->unique())->get()->keyBy('id');

        // Incluye las asignaciones antiguas para evitar consultas por paciente desde Blade.
        $patientIds = $traspasos->pluck('id_paciente')->merge($asignaciones->pluck('id_paciente'))->filter()->unique();
        $patients = $patientIds->isEmpty() ? collect() : Paciente::whereIn('id', $patientIds)->get()->keyBy('id');
        foreach ($traspasos as $traspaso) $traspaso->setRelation('paciente', $patients->get($traspaso->id_paciente));
        foreach ($asignaciones as $asignacion) $asignacion->setRelation('paciente', $patients->get($asignacion->id_paciente));
        $asignacionesLegadas = $asignaciones->groupBy('id_cama');
        $estadosCamas = [];
        foreach ($todasCamas as $camita) {
            $estadosCamas[$camita->id] = $this->estado_cama_actual($camita, $hospitalizaciones->get($camita->id), $asignacionesLegadas->get($camita->id, collect()));
        }
        $resumen = ['total' => 0, 'disponible' => 0, 'ocupada' => 0, 'preparacion' => 0, 'no_disponible' => 0];
        $resumenEspacios = ['quirofanos' => 0, 'consultorios' => 0];

        foreach ($habitacion as $cuarto) {
            $categoria = $cuarto->tipo ? $cuarto->tipo->categoria : null;
            if ($categoria === 'quirofano') $resumenEspacios['quirofanos']++;
            if ($categoria === 'consultorio') $resumenEspacios['consultorios']++;
        }

        foreach ($cama as $camita) {
            $tipoEspacio = $camita->habitacion ? $camita->habitacion->tipo : null;
            $categoriaEspacio = $tipoEspacio ? $tipoEspacio->categoria : null;
            if (in_array($categoriaEspacio, ['quirofano', 'consultorio'], true)) {
                continue;
            }

            $estado = $estadosCamas[$camita->id];
            $resumen['total']++;
            $resumen[$estado]++;
        }

        return view('hospital_admin/gestionc/gestionh', [
            'habitacion' => $habitacion,
            'cama' => $cama,
            'hospitalizaciones' => $hospitalizaciones,
            'asignacionesLegadas' => $asignacionesLegadas,
            'traspasos' => $traspasos,
            'resumen' => $resumen,
            'resumenEspacios' => $resumenEspacios,
            'estadosCamas' => $estadosCamas,
        ]);
    
    }

    private function estado_cama_actual($cama, $hospitalizacion = null, $asignacionesLegadas = null)
    {
        if (!is_null($hospitalizacion) || ($asignacionesLegadas && $asignacionesLegadas->isNotEmpty())) {
            return 'ocupada';
        }

        $estado = 'disponible';
        if (isset($cama->estado)) {
            if ($cama->estado == 2) {
                $estado = 'preparacion';
            } elseif ($cama->estado == 3) {
                $estado = 'ocupada';
            } elseif ($cama->estado == 4) {
                $estado = 'no_disponible';
            }
        }

        $transaccion = $cama->transaccion->where('id_cama', $cama->id)->first();
        if (!is_null($transaccion) && isset($transaccion->estado_cama)) {
            $texto = strtolower(trim($transaccion->estado_cama));
            $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', ' ' => '_', '-' => '_']);

            if (strpos($texto, 'ocup') !== false) {
                $estado = 'ocupada';
            } elseif (strpos($texto, 'prepar') !== false || strpos($texto, 'limpieza') !== false) {
                $estado = 'preparacion';
            } elseif (strpos($texto, 'bloq') !== false || strpos($texto, 'no') !== false || strpos($texto, 'manten') !== false) {
                $estado = 'no_disponible';
            } elseif (strpos($texto, 'disp') !== false || strpos($texto, 'libre') !== false) {
                $estado = 'disponible';
            }
        }

        return $estado;
    }
    public function gestion_c()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        
        $habitacion = Habitacion::paginate(5);
        $cama       = DB::table('cama')->join('habitacion', 'cama.id_habitacion', '=', 'habitacion.id')->get();
        //dd($cama);

        return view('hospital_admin/gestion_camillas/gestion_c', ['habitacion' => $habitacion, 'cama' => $cama]);
    
    }
    

    public function gestionqui()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        $paises = Pais::all();
        $agenda= AgendaQ::paginate(5);
        return view('hospital_admin.gestionqui',['paises'=>$paises,'agenda'=>$agenda]);
    }

    public function resultadoquirofano($id){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
         $agenda = AgendaQ::find($id);
         //dd($agenda);
        return view('hospital_admin.resultadoquirofano',['agenda'=>$agenda]);
    } 
    public function editar($id,Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
        //return redirect('/');
        }
      $agenda = AgendaQ::find($id);
      $ip_cliente    = $_SERVER["REMOTE_ADDR"];
      $idusuario     = Auth::user()->id;
      $editar= [
        'estado' =>$request['estado'],
        'costo' =>$request['costo'],
        ];
        $marcate= $agenda->update($editar);
    return redirect()->route('hospital_admin.gestionqui', ['agenda' =>$agenda]);
     
    }

     public function buscadorfa(Request $request){
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        $farmacia=Hospital_Producto::all();
        if(($request->codigo)!=""){
            $farmacia= Hospital_Producto::where("codigo","like",$request->codigo."%")->get();
        }
        elseif(($request->nombre)!=""){
            $farmacia= Hospital_Producto::where("nombre","like",$request->nombre."%")->get();    
        }
          
        return view('hospital_admin/buscadorfa',['farmacia'=>$farmacia]);
    }

    public function dashboard()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin.dashboard');
    }
    public function farmacia()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }
        $farmacia= Hospital_Producto::paginate(10);

        return view('hospital_admin/farmacia',['farmacia'=>$farmacia]);
    }
   
     
    
    public function insumo()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin.insumo');

    }
    public function emergenciadmin()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');
        }

        return view('hospital_admin.emergenciadmin');

    } 
    

    public function modalagcuarto()
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }

        $nombre_piso = $this->empresaQuery(Piso::when(Schema::hasColumn('piso', 'estado'), function ($query) {
            return $query->where('estado', 1);
        }), 'piso')->orderBy('nombre_piso')->get();
        $tipo = $this->empresaQuery(Tipo_Habitacion::when(Schema::hasColumn('tipo_habitacion', 'estado'), function ($query) {
            return $query->where('estado', 1);
        }), 'tipo_habitacion')->orderBy('nombre')->get();

        return view('hospital_admin/gestionc/modalagcuarto', ['nombre_piso' => $nombre_piso, 'tipo' => $tipo]);
    }

    public function catalogoshabitacion()
    {
        $pisos = $this->empresaQuery(Piso::query(), 'piso')->orderBy('nombre_piso')->get();
        $tipos = $this->empresaQuery(Tipo_Habitacion::query(), 'tipo_habitacion')->orderBy('nombre')->get();
        $habitacionesPorTipo = $this->empresaQuery(Habitacion::select('id_tipo', DB::raw('COUNT(*) as total')), 'habitacion')
            ->groupBy('id_tipo')
            ->pluck('total', 'id_tipo');
        $tieneEstadoPiso = Schema::hasColumn('piso', 'estado');
        $tieneEstadoTipo = Schema::hasColumn('tipo_habitacion', 'estado');
        $tieneCantidadCamas = Schema::hasColumn('tipo_habitacion', 'cantidad_camas');

        return view('hospital_admin/gestionc/catalogos', compact(
            'pisos', 'tipos', 'habitacionesPorTipo', 'tieneEstadoPiso', 'tieneEstadoTipo', 'tieneCantidadCamas'
        ));
    }

    public function guardarpiso(Request $request)
    {
        $this->validate($request, [
            'nombre_piso' => 'required|string|max:80',
        ], [
            'nombre_piso.required' => 'Escriba el nombre del piso.',
            'nombre_piso.unique' => 'Ya existe un piso con ese nombre.',
        ]);

        if ($this->empresaQuery(Piso::query(), 'piso')->where('nombre_piso', trim($request->input('nombre_piso')))->exists()) {
            return back()->withErrors(['nombre_piso' => 'Ya existe un piso con ese nombre en la empresa activa.'])->withInput();
        }

        $datos = $this->datosAuditoria('piso');
        $datos['nombre_piso'] = strtoupper(trim($request->input('nombre_piso')));
        if (Schema::hasColumn('piso', 'estado')) {
            $datos['estado'] = 1;
        }
        if (Schema::hasColumn('piso', 'id_empresa')) $datos['id_empresa'] = (string) session('id_empresa');
        DB::table('piso')->insert($datos);

        return back()->with('success', 'El piso fue creado y ya puede seleccionarse en una habitación.');
    }

    public function guardartipohabitacion(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:60',
            'descripcion' => 'required|string|max:150',
            'cantidad_camas' => 'required|integer|between:0,10',
            'categoria' => 'required|in:habitacion,consultorio,quirofano',
            'icono' => 'nullable|string|max:80',
            'imagen' => 'nullable|string|max:120',
        ], [
            'nombre.required' => 'Escriba el nombre del tipo de habitación.',
            'nombre.unique' => 'Ya existe un tipo de habitación con ese nombre.',
            'cantidad_camas.between' => 'La cantidad de camas debe estar entre 0 y 10.',
        ]);

        if ($this->empresaQuery(Tipo_Habitacion::query(), 'tipo_habitacion')->where('nombre', trim($request->input('nombre')))->exists()) {
            return back()->withErrors(['nombre' => 'Ya existe un tipo de habitación con ese nombre en la empresa activa.'])->withInput();
        }

        if (!Schema::hasColumn('tipo_habitacion', 'cantidad_camas')) {
            return back()->withErrors(['cantidad_camas' => 'Ejecute las migraciones para habilitar la cantidad configurable de camas.'])->withInput();
        }

        $datos = $this->datosAuditoria('tipo_habitacion');
        $datos['nombre'] = strtoupper(trim($request->input('nombre')));
        $datos['descripcion'] = strtoupper(trim($request->input('descripcion')));
        $datos['cantidad_camas'] = (int) $request->input('cantidad_camas');
        if (Schema::hasColumn('tipo_habitacion', 'categoria')) {
            $datos['categoria'] = $request->input('categoria', 'habitacion');
            $datos['icono'] = trim((string) $request->input('icono', '')) ?: null;
            $datos['imagen'] = trim((string) $request->input('imagen', '')) ?: null;
        }
        if (Schema::hasColumn('tipo_habitacion', 'estado')) {
            $datos['estado'] = 1;
        }
        if (Schema::hasColumn('tipo_habitacion', 'id_empresa')) $datos['id_empresa'] = (string) session('id_empresa');
        DB::table('tipo_habitacion')->insert($datos);

        return back()->with('success', 'El tipo de habitación fue creado correctamente.');
    }

    public function actualizarpiso(Request $request, $id)
    {
        $piso = $this->empresaQuery(Piso::query(), 'piso')->where('id', $id)->firstOrFail();
        $request->merge(['nombre_piso' => strtoupper(trim($request->input('nombre_piso')))]);

        $this->validate($request, [
            'nombre_piso' => 'required|string|max:80',
        ], [
            'nombre_piso.required' => 'Escriba el nombre del piso.',
            'nombre_piso.unique' => 'Ya existe otro piso con ese nombre.',
        ]);

        if ($this->empresaQuery(Piso::query(), 'piso')->where('nombre_piso', $request->input('nombre_piso'))->where('id', '<>', $piso->id)->exists()) {
            return back()->withErrors(['nombre_piso' => 'Ya existe otro piso con ese nombre en la empresa activa.'])->withInput();
        }

        $datos = $this->datosModificacion('piso');
        $datos['nombre_piso'] = $request->input('nombre_piso');
        $this->empresaQuery(DB::table('piso'), 'piso')->where('id', $piso->id)->update($datos);

        return back()->with('success', 'El piso fue actualizado correctamente.');
    }

    public function actualizartipohabitacion(Request $request, $id)
    {
        $tipo = $this->empresaQuery(Tipo_Habitacion::query(), 'tipo_habitacion')->where('id', $id)->firstOrFail();
        $request->merge([
            'nombre' => strtoupper(trim($request->input('nombre'))),
            'descripcion' => strtoupper(trim($request->input('descripcion'))),
        ]);

        $this->validate($request, [
            'nombre' => 'required|string|max:60',
            'descripcion' => 'required|string|max:150',
            'cantidad_camas' => 'required|integer|between:0,10',
            'categoria' => 'required|in:habitacion,consultorio,quirofano',
            'icono' => 'nullable|string|max:80',
            'imagen' => 'nullable|string|max:120',
        ], [
            'nombre.required' => 'Escriba el nombre del tipo de habitación.',
            'nombre.unique' => 'Ya existe otro tipo de habitación con ese nombre.',
            'cantidad_camas.between' => 'La cantidad de camas debe estar entre 0 y 10.',
        ]);

        if ($this->empresaQuery(Tipo_Habitacion::query(), 'tipo_habitacion')->where('nombre', $request->input('nombre'))->where('id', '<>', $tipo->id)->exists()) {
            return back()->withErrors(['nombre' => 'Ya existe otro tipo de habitación con ese nombre en la empresa activa.'])->withInput();
        }

        if (!Schema::hasColumn('tipo_habitacion', 'cantidad_camas')) {
            return back()->withErrors(['cantidad_camas' => 'Ejecute las migraciones para habilitar la cantidad configurable de camas.'])->withInput();
        }

        $nuevaCantidad = (int) $request->input('cantidad_camas');
        $cantidadActual = (int) $tipo->cantidad_camas;
        if ($nuevaCantidad !== $cantidadActual && $this->empresaQuery(Habitacion::where('id_tipo', $tipo->id), 'habitacion')->exists()) {
            return back()->withErrors([
                'cantidad_camas' => 'No se puede cambiar la cantidad de camas porque este tipo ya está asignado a una habitación. Puede modificar el nombre y la descripción.',
            ])->withInput();
        }

        $datos = $this->datosModificacion('tipo_habitacion');
        $datos['nombre'] = $request->input('nombre');
        $datos['descripcion'] = $request->input('descripcion');
        $datos['cantidad_camas'] = $nuevaCantidad;
        if (Schema::hasColumn('tipo_habitacion', 'categoria')) {
            $datos['categoria'] = $request->input('categoria', 'habitacion');
            $datos['icono'] = trim((string) $request->input('icono', '')) ?: null;
            $datos['imagen'] = trim((string) $request->input('imagen', '')) ?: null;
        }
        $this->empresaQuery(DB::table('tipo_habitacion'), 'tipo_habitacion')->where('id', $tipo->id)->update($datos);

        return back()->with('success', 'El tipo de habitación fue actualizado correctamente.');
    }

    public function estadocatalogohabitacion(Request $request, $catalogo, $id)
    {
        $this->validate($request, ['estado' => 'required|in:0,1']);

        if ($catalogo === 'piso') {
            $registro = $this->empresaQuery(Piso::query(), 'piso')->where('id', $id)->firstOrFail();
            $tabla = 'piso';
        } elseif ($catalogo === 'tipo') {
            $registro = $this->empresaQuery(Tipo_Habitacion::query(), 'tipo_habitacion')->where('id', $id)->firstOrFail();
            $tabla = 'tipo_habitacion';
        } else {
            abort(404);
        }

        if (!Schema::hasColumn($tabla, 'estado')) {
            return back()->withErrors(['estado' => 'El catálogo todavía no dispone del campo estado.']);
        }

        $actualizacion = ['estado' => (int) $request->input('estado')];
        if (Schema::hasColumn($tabla, 'id_usuariomod')) {
            $actualizacion['id_usuariomod'] = Auth::user()->id;
        }
        if (Schema::hasColumn($tabla, 'ip_modificacion')) {
            $actualizacion['ip_modificacion'] = $_SERVER['REMOTE_ADDR'];
        }
        $this->empresaQuery(DB::table($tabla), $tabla)->where('id', $registro->id)->update($actualizacion);

        return back()->with('success', 'El estado del catálogo fue actualizado.');
    }

    private function datosAuditoria($tabla)
    {
        $datos = [];
        foreach (['id_usuariocrea', 'id_usuariomod'] as $campo) {
            if (Schema::hasColumn($tabla, $campo)) {
                $datos[$campo] = Auth::user()->id;
            }
        }
        foreach (['ip_creacion', 'ip_modificacion'] as $campo) {
            if (Schema::hasColumn($tabla, $campo)) {
                $datos[$campo] = $_SERVER['REMOTE_ADDR'];
            }
        }

        return $datos;
    }

    private function datosModificacion($tabla)
    {
        $datos = [];
        if (Schema::hasColumn($tabla, 'id_usuariomod')) {
            $datos['id_usuariomod'] = Auth::user()->id;
        }
        if (Schema::hasColumn($tabla, 'ip_modificacion')) {
            $datos['ip_modificacion'] = request()->ip();
        }

        return $datos;
    }

    /** Aplica el tenant actual solo cuando la migración ya está instalada. */
    private function empresaQuery($query, $table)
    {
        if (Schema::hasColumn($table, 'id_empresa') && session('id_empresa') !== null) {
            $query->where($table.'.id_empresa', (string) session('id_empresa'));
        }
        return $query;
    }

    private function crearHabitacionConCamas(Request $request)
    {
        $tipo = $this->empresaQuery(Tipo_Habitacion::query(), 'tipo_habitacion')->find($request->input('id_tipo'));
        $cantidadEsperada = $tipo && isset($tipo->cantidad_camas)
            ? max(0, (int) $tipo->cantidad_camas)
            : $this->cantidadCamasLegacy((int) $request->input('id_tipo'));
        if (!$request->has('camas')) {
            $request->merge(['camas' => []]);
        }

        $this->validate($request, [
            'id_tipo' => 'required|integer|exists:tipo_habitacion,id',
            'id_piso' => 'required|integer|exists:piso,id',
            'estado' => 'required|in:1,2,3,4',
            'numhabitacion' => 'required|integer|between:1,9999',
            'camas' => ($cantidadEsperada > 0 ? 'required|' : 'nullable|').'array|size:'.$cantidadEsperada,
            'camas.*.codigo' => 'required|string|max:10|distinct',
            'camas.*.estado' => 'required|in:1,2,3,4',
        ], [
            'required' => 'Complete los datos obligatorios de la habitación y sus camas.',
            'exists' => 'El piso o tipo de habitación seleccionado ya no está disponible.',
            'camas.size' => 'El tipo seleccionado requiere exactamente '.$cantidadEsperada.' cama(s).',
            'camas.*.codigo.distinct' => 'Los códigos de las camas no pueden repetirse.',
        ]);

        if ($this->empresaQuery(Piso::query(), 'piso')->where('id', $request->input('id_piso'))->doesntExist()) {
            return back()->withErrors(['id_piso' => 'El piso seleccionado no pertenece a la empresa activa.'])->withInput();
        }
        if (is_null($tipo)) {
            return back()->withErrors(['id_tipo' => 'El tipo de habitación no pertenece a la empresa activa.'])->withInput();
        }

        $duplicada = $this->empresaQuery(Habitacion::where('id_piso', $request->input('id_piso')),
            'habitacion')
            ->where('codigo', $request->input('numhabitacion'))
            ->exists();
        if ($duplicada) {
            return back()->withErrors(['numhabitacion' => 'Ya existe una habitación con esa numeración en el piso seleccionado.'])->withInput();
        }

        $codigosCama = collect($request->input('camas', []))->pluck('codigo')->map(function ($codigo) {
            return strtoupper(trim($codigo));
        });
        if ($this->empresaQuery(Cama::whereIn('codigo', $codigosCama), 'cama')->exists()) {
            return back()->withErrors(['camas' => 'Uno de los codigos de cama ya se encuentra registrado.'])->withInput();
        }

        DB::transaction(function () use ($request) {
            $auditoriaHabitacion = $this->datosAuditoria('habitacion');
            $habitacionData = array_merge($auditoriaHabitacion, [
                'id_tipo' => (int) $request->input('id_tipo'),
                'id_piso' => (int) $request->input('id_piso'),
                'estado' => (int) $request->input('estado'),
                'codigo' => (string) $request->input('numhabitacion'),
            ]);
            if (Schema::hasColumn('habitacion', 'id_empresa')) $habitacionData['id_empresa'] = (string) session('id_empresa');
            $idHabitacion = Habitacion::insertGetId($habitacionData);

            foreach ($request->input('camas', []) as $datosCama) {
                $estado = (int) $datosCama['estado'];
                $camaData = array_merge($this->datosAuditoria('cama'), [
                    'estado' => $estado,
                    'codigo' => strtoupper(trim($datosCama['codigo'])),
                    'id_habitacion' => $idHabitacion,
                ]);
                if (Schema::hasColumn('cama', 'id_empresa')) $camaData['id_empresa'] = (string) session('id_empresa');
                $idCama = Cama::insertGetId($camaData);

                $transaccion = array_merge($this->datosAuditoria('cama_transaccion'), [
                    'id_cama' => $idCama,
                    'id_imagen' => $this->imagenInicialCama((int) $request->input('id_tipo'), $estado),
                ]);
                if (Schema::hasColumn('cama_transaccion', 'estado_cama')) {
                    $transaccion['estado_cama'] = $this->nombreEstadoCama($estado);
                }
                CamaTransaccion::create($transaccion);
            }
        });

        return redirect()->route('hospital_admin.gestionh')->with('success', 'La habitación y sus camas se crearon correctamente.');
    }

    private function cantidadCamasLegacy($idTipo)
    {
        if ($idTipo === 2) {
            return 2;
        }
        if ($idTipo === 4) {
            return 3;
        }
        return 1;
    }

    private function imagenInicialCama($idTipo, $estado)
    {
        if ($idTipo === 3) {
            return $estado + 1 + $idTipo;
        }
        if ($idTipo === 5) {
            return $estado + 3 + $idTipo;
        }
        return $estado;
    }

    private function nombreEstadoCama($estado)
    {
        $estados = [1 => 'disponible', 2 => 'preparacion', 3 => 'ocupada', 4 => 'no_disponible'];
        return isset($estados[$estado]) ? $estados[$estado] : 'disponible';
    }

    public function aghabitaciones(Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        if ($request->has('catalogo_recurso')) {
            return $this->crearHabitacionConCamas($request);
        }
        $tipoSolicitado = (int) $request->input('id_tipo');
        $reglas = [
            'id_tipo' => 'required|integer|exists:tipo_habitacion,id',
            'id_piso' => 'required|integer|exists:piso,id',
            'estado' => 'required|in:1,2,3,4',
            'numhabitacion' => 'required|integer|between:1,9999',
            'codigo' => 'required|string|max:4',
            'estado_uno' => 'required|in:1,2,3,4',
        ];

        if ($tipoSolicitado === 2 || $tipoSolicitado === 4) {
            $reglas['codigodos'] = 'required|string|max:4';
            $reglas['estado_dos'] = 'required|in:1,2,3,4';
        }

        if ($tipoSolicitado === 4) {
            $reglas['codigotres'] = 'required|string|max:4';
            $reglas['estado_tres'] = 'required|in:1,2,3,4';
        }

        $this->validate($request, $reglas, [
            'required' => 'Complete todos los datos obligatorios de la habitación y sus camas.',
            'exists' => 'El piso o tipo de habitación seleccionado ya no está disponible.',
            'numhabitacion.between' => 'El número de habitación debe estar entre 1 y 9999.',
        ]);

        $habitacionDuplicada = Habitacion::where('id_piso', $request->input('id_piso'))
            ->where('codigo', $request->input('numhabitacion'))
            ->exists();

        if ($habitacionDuplicada) {
            return back()->withErrors(['numhabitacion' => 'Ya existe una habitación con ese número en el piso seleccionado.'])->withInput();
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $input      = [
            'id_tipo'         => $request['id_tipo'],
            'id_piso'         => $request['id_piso'],
            'estado'          => $request['estado'],
            'codigo'          => $request['numhabitacion'],
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,

        ];
        $id_habitacion = Habitacion::insertGetId($input);
        $estado        = $request['estado_uno'];
        $estadodos     = $request['estado_dos'];
        $estadotres    = $request['estado_tres'];
        $codigo        = $request['codigo'];
        $codigodos     = $request['codigodos'];
        $codigotres    = $request['codigotres'];
        $tipo          = $request['id_tipo'];
        if ($tipo != 0) {
            if ($tipo == 1) {
                $input_cama = [
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama = Cama::insertGetId($input_cama);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama,
                    'id_imagen'       => $estado,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);

            } elseif ($tipo == 2) {
                $input_cama = [
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama = Cama::insertGetId($input_cama);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama,
                    'id_imagen'       => $estado,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
                $input_cama2 = [
                    'estado'          => $estadodos,
                    'codigo'          => $codigodos,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama2 = Cama::insertGetId($input_cama2);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama2,
                    'id_imagen'       => $estadodos,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            } elseif ($tipo == 4) {
                $input_cama = [
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama = Cama::insertGetId($input_cama);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama,
                    'id_imagen'       => $estado,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
                $input_cama2 = [
                    'estado'          => $estadodos,
                    'codigo'          => $codigodos,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama2 = Cama::insertGetId($input_cama2);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama2,
                    'id_imagen'       => $estadodos,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);

                $input_cama3 = [
                    'estado'          => $estadotres,
                    'codigo'          => $codigotres,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama3 = Cama::insertGetId($input_cama3);
                CamaTransaccion::create([
                    'id_cama'         => $id_cama3,
                    'id_imagen'       => $estadotres,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            } elseif ($tipo == 3) {
                $input_cama = [
                   
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                $id_cama = Cama::insertGetId($input_cama);
                $suite= $estado+1+$tipo;
                CamaTransaccion::create([
                    'id_cama'         => $id_cama,
                    'id_imagen'       => $suite,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            } elseif ($tipo == 5) {
                $input_cama = [
                    
                    'estado'          => $estado,
                    'codigo'          => $codigo,
                    'id_habitacion'   => $id_habitacion,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ];
                
                $id_camaej = Cama::insertGetId($input_cama);
                $ejecutiva= $estado+3+$tipo;
                CamaTransaccion::create([
                    'id_cama'         => $id_camaej,
                    'id_imagen'       => $ejecutiva,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            }

        }

        return back()->with('success', 'La habitación y sus camas se crearon correctamente.');
    }
    public function editarh($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        $habitacionedit = Habitacion::where('id', $id)->get();
        $habitacionid   = Habitacion::find($id);
        $id_dada        = $habitacionid->id;
        $nombre_piso    = DB::table('piso')->get();
        $tipo           = DB::table('tipo_habitacion')->get();
        $cama           = Cama::where('id_habitacion', '=', $id_dada)->get();
        $ocupadas       = Ho_Hospitalizacion::whereIn('id_cama', $cama->pluck('id'))->where('estado', 1)->pluck('id_cama')->all();
        $ocupadasPaciente = CamaPaciente::whereIn('id_cama', $cama->pluck('id'))->where('estado', 1)->pluck('id_cama')->all();
        $camasOcupadas = array_unique(array_merge($ocupadas, $ocupadasPaciente));
        $ocupaciones = Ho_Hospitalizacion::whereIn('id_cama', $cama->pluck('id'))->where('estado', 1)->get()->keyBy('id_cama');
        $asignacionesLegadas = CamaPaciente::with('paciente')->whereIn('id_cama', $cama->pluck('id'))->where('estado', 1)->get()->groupBy('id_cama');

        return view('hospital_admin/gestionc/editarh', ['habitacionedit' => $habitacionedit, 'habitacionid' => $habitacionid, 'nombre_piso' => $nombre_piso, 'tipo' => $tipo, 'cama' => $cama, 'camasOcupadas' => $camasOcupadas, 'ocupaciones' => $ocupaciones, 'asignacionesLegadas' => $asignacionesLegadas]);
    }
  //Camillas
    public function editarc($id)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        $habitacionedit = Habitacion::where('id', $id)->get();
        $habitacionid   = Habitacion::find($id);
        $id_dada        = $habitacionid->id;
        $nombre_piso    = DB::table('piso')->get();
        $tipo           = DB::table('tipo_habitacion')->get();
        $cama           = Cama::where('id_habitacion', '=', $id_dada)->get();
        return view('hospital_admin/gestion_camillas/editarc', ['habitacionedit' => $habitacionedit, 'habitacionid' => $habitacionid, 'nombre_piso' => $nombre_piso, 'tipo' => $tipo, 'cama' => $cama]);
    }

    public function updateh($id, Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            //return redirect('/');

        }
        $id_habitacion = Habitacion::findOrFail($id);
        $ip_cliente    = $_SERVER["REMOTE_ADDR"];
        $idusuario     = Auth::user()->id;
        $id_tipo       = $id_habitacion->id_tipo;
        $camas           = Cama::where('id_habitacion', '=', $id_habitacion->id)->get();

        $reglas = [
            'codigo' => 'required|string|max:30',
            'estado' => 'required|in:1,2,3,4',
        ];
        foreach ($camas as $indice => $cama) {
            $numero = $indice + 1;
            $reglas['estadoC'.$numero] = 'required|string|max:30';
            $reglas['estado'.$numero] = 'required|in:1,2,3,4';
        }
        $this->validate($request, $reglas, [
            'required' => 'Completa todos los datos de la habitación y sus camas.',
            'in' => 'Selecciona un estado válido.',
            'max' => 'La numeración no puede superar los 30 caracteres.',
        ]);

        DB::transaction(function () use ($request, $id_habitacion, $camas, $id_tipo, $idusuario, $ip_cliente) {
            $id_habitacion->update([
                'estado'          => $request['estado'],
                'codigo'          => trim($request['codigo']),
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ]);

            foreach ($camas as $indice => $cama) {
                $numero = $indice + 1;
                $estadoCama = (int) $request['estado'.$numero];
                $tieneHospitalizacion = Ho_Hospitalizacion::where('id_cama', $cama->id)->where('estado', 1)->lockForUpdate()->exists();
                $tieneAsignacionLegada = CamaPaciente::where('id_cama', $cama->id)->where('estado', 1)->lockForUpdate()->exists();
                if (($tieneHospitalizacion || $tieneAsignacionLegada) && $estadoCama !== 3) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'cama' => 'La cama '.$cama->codigo.' tiene un paciente activo. Complete el alta o el traslado antes de cambiarla a un estado libre.',
                    ]);
                }
                $cama->update([
                    'codigo' => trim($request['estadoC'.$numero]),
                    'estado' => $estadoCama,
                ]);

                $idImagen = $estadoCama;
                if ((int) $id_tipo === 3) {
                    $idImagen = $estadoCama + 1 + $id_tipo;
                } elseif ((int) $id_tipo === 5) {
                    $idImagen = $estadoCama + 3 + $id_tipo;
                }

                $estadoTransaccion = [
                    1 => 'disponible',
                    2 => 'preparacion',
                    3 => 'ocupada',
                    4 => 'no_disponible',
                ][$estadoCama];

                $transacciones = $cama->transaccion;
                foreach ($transacciones as $transaccion) {
                    $transaccion->update([
                        'estado_cama' => $estadoTransaccion,
                        'id_imagen' => $idImagen,
                        'id_usuariomod' => $idusuario,
                        'ip_modificacion' => $ip_cliente,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                if ($transacciones->isEmpty()) {
                    CamaTransaccion::create([
                        'id_cama' => $cama->id,
                        'estado_cama' => $estadoTransaccion,
                        'id_imagen' => $idImagen,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ]);
                }
            }
        });

        return redirect()->route('hospital_admin.gestionh')->with('success', 'La habitación y sus camas se actualizaron correctamente.');

    }

    public function eliminarcama($id, Request $request)
    {
        $opcion = '1';
        if ($this->rol_new($opcion)) {
            abort(403);
        }

        try {
            $resultado = DB::transaction(function () use ($id, $request) {
                $cama = Cama::where('id', $id)->lockForUpdate()->firstOrFail();

                $hospitalizacion = Ho_Hospitalizacion::where('id_cama', $cama->id)->where('estado', 1)->lockForUpdate()->first();
                $asignacionLegada = CamaPaciente::with('paciente')->where('id_cama', $cama->id)->where('estado', 1)->lockForUpdate()->first();
                if ($hospitalizacion || $asignacionLegada) {
                    $detalle = $hospitalizacion ? 'hospitalización activa' : 'asignación activa de paciente';
                    throw new \RuntimeException('No se puede eliminar la cama '.$cama->codigo.': existe '.$detalle.'. Complete el alta o el traslado antes de eliminarla.');
                }

                $totalCamas = Cama::where('id_habitacion', $cama->id_habitacion)->lockForUpdate()->count();
                if ($totalCamas <= 1) {
                    throw new \RuntimeException('No se puede eliminar la última cama de la habitación. Cree otra cama o elimine la habitación completa desde base si corresponde.');
                }

                // Una cama con historial no se borra físicamente: se desactiva
                // para conservar trazabilidad y evitar errores de claves foráneas.
                $tieneHistorial = CamaTransaccion::where('id_cama', $cama->id)->exists()
                    || CamaPaciente::where('id_cama', $cama->id)->exists()
                    || Ho_Hospitalizacion::where('id_cama', $cama->id)->exists();

                if ($tieneHistorial) {
                    $cama->update([
                        'estado' => 0,
                        'id_usuariomod' => Auth::user()->id,
                        'ip_modificacion' => $request->ip(),
                    ]);

                    return ['mode' => 'deactivated', 'code' => $cama->codigo];
                }

                $cama->delete();
                return ['mode' => 'deleted', 'code' => $cama->codigo];
            }, 3);

            $message = $resultado['mode'] === 'deactivated'
                ? 'La cama '.$resultado['code'].' fue desactivada para conservar su historial clínico.'
                : 'La cama '.$resultado['code'].' fue eliminada correctamente.';

            if ($request->expectsJson()) {
                return response()->json(['state' => 'success', 'message' => $message]);
            }
            return redirect()->route('hospital_admin.gestionh')->with('success', $message);
        } catch (\RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json(['state' => 'error', 'message' => $exception->getMessage()], 422);
            }
            return back()->withErrors(['cama' => $exception->getMessage()]);
        } catch (\Exception $exception) {
            report($exception);
            if ($request->expectsJson()) {
                return response()->json(['state' => 'error', 'message' => 'No se pudo eliminar la cama.'], 500);
            }
            return back()->withErrors(['cama' => 'No se pudo eliminar la cama.']);
        }
    }

    //
    ///}

    /*
public function buscar(Request $request) {
$opcion = '1';
if($this->rol_new($opcion)){
//return redirect('/');
$id_habitacion =\DB::table('id_habitacion')->select('id','id_tipo')->get();
return $id_habitacion;

}

} */

}
