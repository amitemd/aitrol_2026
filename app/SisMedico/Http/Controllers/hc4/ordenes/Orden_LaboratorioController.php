<?php

namespace Sis_medico\Http\Controllers\hc4\ordenes;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\laboratorio\OrdenController;
use Sis_medico\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Procedimiento;
use Sis_medico\Paciente;
use Sis_medico\Orden;
use Sis_medico\Orden_Tipo;
use Sis_medico\Orden_Procedimiento;
use Carbon\Carbon;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Examen_Orden;
use Sis_medico\Log_usuario;
use Sis_medico\Firma_Usuario;
use Sis_medico\Protocolo;
use Sis_medico\Examen_Protocolo;
use Response;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen;
use Illuminate\Support\Str;

class Orden_LaboratorioController extends Controller
{

    private function perfilFavoritoDelUsuario($id)
    {
        return Protocolo::where('id', $id)
            ->where('estado', '2')
            ->where('id_usuariocrea', Auth::id())
            ->firstOrFail();
    }

    private function campoLaboratorioNormalizado($campo)
    {
        return "lower(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace(replace({$campo}, 'Á', 'A'), 'É', 'E'), 'Í', 'I'), 'Ó', 'O'), 'Ú', 'U'), 'Ü', 'U'), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'))";
    }

    private function consultaAgrupadoresLaboratorio()
    {
        return DB::table('examen_agrupador as agrupador')
            ->where('agrupador.estado', '1')
            ->orderBy('agrupador.nombre')
            ->orderBy('agrupador.id');
    }

    private function consultaExamenesLaboratorio($idOrden = null)
    {
        $query = is_null($idOrden)
            ? DB::table('examen as e')
            : DB::table('examen_detalle as ed')
                ->where('ed.id_examen_orden', $idOrden)
                ->join('examen as e', 'e.id', 'ed.id_examen');

        return $query
            ->join('examen_agrupador as agrupador', 'agrupador.id', 'e.id_agrupador')
            ->where('e.estado', '1')
            ->where('agrupador.estado', '1')
            ->select(
                'e.id as ex_id',
                'e.descripcion',
                'e.nombre',
                'e.valor',
                'e.estado',
                'e.id_agrupador',
                'agrupador.nombre as lnombre'
            )
            ->orderBy('e.id_agrupador')
            ->orderBy('e.descripcion');
    }

    private function consultaExamenesProtocolo($idProtocolo)
    {
        return DB::table('examen_protocolo as ep')
            ->where('ep.id_protocolo', $idProtocolo)
            ->join('examen as e', 'e.id', 'ep.id_examen')
            ->join('examen_agrupador as agrupador', 'agrupador.id', 'e.id_agrupador')
            ->where('e.estado', '1')
            ->where('agrupador.estado', '1')
            ->select(
                'e.id as ex_id',
                'e.descripcion',
                'e.nombre',
                'e.valor',
                'e.estado',
                'e.id_agrupador',
                'agrupador.nombre as lnombre'
            )
            ->orderBy('e.id_agrupador')
            ->orderBy('e.descripcion');
    }

    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion){ 
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        }
    }

    protected $redirectTo = '/';

         /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function index($id_paciente){
        
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      $paciente = Paciente::find($id_paciente);

      return view('hc4/ordenes/orden_labs/index', ['paciente' => $paciente]);

    }

    public function index2($id_paciente){
        
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      $paciente = Paciente::find($id_paciente);

      $ordenes = Examen_Orden::where('id_paciente',$id_paciente)
        ->where('estado','<>','0')   
        ->OrderBy('created_at', 'desc')
        ->get();
  

      return view('hc4/ordenes/orden_labs/index2', ['ordenes' => $ordenes, 'paciente' => $paciente, 'editar' => '0']);

    }

    public function crear($id_paciente){
        
     
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }

      $ip_cliente= $_SERVER["REMOTE_ADDR"];
      $id_doctor = Auth::user()->id;
      $id_empresa = trim((string) session('id_empresa'));
      if ($id_empresa === '') {
          return response('Seleccione una empresa antes de crear la orden.', 422);
      }

      $paciente = Paciente::find($id_paciente);
      if (is_null($paciente)) {
          return response('El paciente no pertenece a la empresa activa.', 404);
      }

      $protocolo = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->first(); 
      if(!is_null($protocolo)){
        $examen_proto = Examen_Protocolo::where('id_protocolo',$protocolo->id)->get();
       
      }

      $input_ex = [
            'id_paciente' => $id_paciente,
            'anio' => substr(date('Y-m-d'),0,4),
            'mes' => substr(date('Y-m-d'),5,2),
            
            'id_seguro' => '1',
            'id_empresa' => $id_empresa,
            
            'est_amb_hos' => '0',
            'id_doctor_ieced' => $id_doctor,
            
            'cantidad' => '0',
            'estado' => '-1',
            'valor' => '0',
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $id_doctor,
            'id_usuariomod' => $id_doctor,
            'fecha_orden' => date('Y-m-d h:i:s'),
                        
        ];
        $id_examen_orden = Examen_Orden::insertGetId($input_ex);    

        Log_usuario::create([
            'id_usuario' => $id_doctor,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "GENERA ORDEN DOCTOR",
            'dato_ant1' => 'orden: '.$id_examen_orden.' paciente: '.$paciente->id,
            'dato1' => strtoupper($paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2),
            
                ]); 

        $ordenes = Examen_Orden::where('id_paciente',$id_paciente)
        ->where('estado','<>','0')   
        ->OrderBy('created_at', 'desc')
        ->get(); 

       if(!is_null($protocolo)){
        $total=0;
        $cantidad=0;

        foreach ($examen_proto as $examen_p) {


                $examen = Examen::find($examen_p->id_examen);
                if (is_null($examen)) {
                    continue;
                }
                //return $examen;
                $valor    = $examen->valor;
                $cubre    = 'NO';

                $input_det = [
                    'id_examen_orden' => $id_examen_orden,
                    'id_examen'       => $examen->id,
                    'valor'           => $valor,
                    'cubre'           => $cubre,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_doctor,
                    'id_usuariomod'   => $id_doctor,
                    

                ];
              
                Examen_detalle::create($input_det);
                /// orden
                $total       = $total+$valor;
                $total       = round($total, 2);
                $cantidad++;
              
            }
            $input_or=[
                  'cantidad'   => $cantidad,
                  'valor'      => $total,
          'total_valor'      => $total,
                ];
            $orden = Examen_Orden::find($id_examen_orden);
        
            $orden->update($input_or);
       
      }
       
         
        return view('hc4/ordenes/orden_labs/index2', ['ordenes' => $ordenes, 'paciente' => $paciente, 'editar' => $id_examen_orden]);
      
        }

      
    

    public function editar($id_orden)
    {

        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $id_doctor = Auth::user()->id;

        $orden = Examen_Orden::find($id_orden);

        if ($orden->seguro->tipo == 0) {

            /*$protocolos  = Protocolo::where('estado', '1')->get();
            $agrupadores = Examen_Agrupador::where('estado', '1')->get();
            $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
            return view('hc4.ordenes.orden_labs.editar-publico', ['id_editar' => $id, 'protocolos' => $protocolos, 'agrupadores' => $agrupadores, 'examenes' => $examenes, 'orden' => $orden]);*/
            return redirect()->route('laboratorio.ordenIess.edit',['id' => $id_orden]);
            
        }

        $agrupador_labs = $this->consultaAgrupadoresLaboratorio()->get();
        $examenes_labs = $this->consultaExamenesLaboratorio()->get();

        $detalles_ch = [];
        $i           = 0;

        $nuevo_detalles = $orden->detalles;

        if (!is_null($nuevo_detalles)) {
            foreach ($nuevo_detalles as $nuevo_detalle) {
                $detalles_ch[$i] = $nuevo_detalle->id_examen;
                $i               = $i + 1;
            }

        }

        $protocolos = Protocolo::where('estado', '2')->where('id_usuariocrea', $id_doctor)->get();

        return view('hc4/ordenes/orden_labs/editar', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden, 'protocolos' => $protocolos]);

    }

    public function autocompleteExamenes(Request $request)
    {
      $opcion = '2';
      if ($this->rol_new($opcion)) {
        return response()->json([], 403);
      }

      $busqueda = trim((string) $request->input('term'));
      if (Str::length($busqueda) < 2) {
        return response()->json([]);
      }

      $busquedaNormalizada = Str::lower(Str::ascii($busqueda));
      $consultar = function ($termino) {
        return DB::table('examen as e')
          ->join('examen_agrupador as agrupador', 'agrupador.id', 'e.id_agrupador')
          ->where('e.estado', '1')
          ->where('agrupador.estado', '1')
          ->where(function ($query) use ($termino) {
            $query->whereRaw($this->campoLaboratorioNormalizado('e.descripcion') . ' like ?', [$termino])
              ->orWhereRaw($this->campoLaboratorioNormalizado('e.nombre') . ' like ?', [$termino]);
          })
          ->select('e.id', 'e.descripcion', 'e.nombre')
          ->orderBy('e.descripcion')
          ->limit(20)
          ->get();
      };

      $examenes = $consultar('%' . $busquedaNormalizada . '%');
      if ($examenes->isEmpty() && Str::length($busquedaNormalizada) > 4) {
        $examenes = $consultar('%' . Str::substr($busquedaNormalizada, 0, 4) . '%');
      }

      $resultados = $examenes->map(function ($examen) {
        $nombre = trim((string) ($examen->descripcion ?: $examen->nombre));
        return [
          'id' => $examen->id,
          'label' => $nombre,
          'value' => $nombre,
        ];
      })->unique('id')->values();

      return response()->json($resultados);
    }

    public function autocompleteAgrupadores(Request $request)
    {
      $opcion = '2';
      if ($this->rol_new($opcion)) {
        return response()->json([], 403);
      }

      $busqueda = trim((string) $request->input('term'));
      if (Str::length($busqueda) < 2) {
        return response()->json([]);
      }

      $termino = '%' . Str::lower(Str::ascii($busqueda)) . '%';
      $agrupadores = DB::table('examen_agrupador as agrupador')
        ->where('agrupador.estado', '1')
        ->whereRaw($this->campoLaboratorioNormalizado('agrupador.nombre') . ' like ?', [$termino])
        ->select('agrupador.id', 'agrupador.nombre')
        ->orderBy('agrupador.nombre')
        ->limit(20)
        ->get()
        ->map(function ($agrupador) {
          return [
            'id' => $agrupador->id,
            'label' => trim((string) $agrupador->nombre),
            'value' => trim((string) $agrupador->nombre),
          ];
        })
        ->values();

      return response()->json($agrupadores);
    }

    public function actualizarExamen(Request $request, $idOrden, $idExamen)
    {
      $opcion = '2';
      if ($this->rol_new($opcion)) {
        return response()->json(['message' => 'No tiene permisos para modificar esta orden.'], 403);
      }

      $orden = Examen_Orden::findOrFail($idOrden);
      $examen = Examen::where('id', $idExamen)->where('estado', '1')->firstOrFail();
      $seleccionar = $request->boolean('seleccionar');
      $ipCliente = $request->ip();
      $idUsuario = Auth::id();

      DB::transaction(function () use ($orden, $examen, $seleccionar, $ipCliente, $idUsuario) {
        $detalle = Examen_Detalle::where('id_examen_orden', $orden->id)
          ->where('id_examen', $examen->id)
          ->first();

        if ($seleccionar && is_null($detalle)) {
          $valor = (float) $examen->valor;
          $cubre = 'NO';
          $nivel = Examen_Nivel::where('id_examen', $examen->id)
            ->where('nivel', $orden->id_nivel)
            ->first();
          if (!is_null($nivel) && (float) $nivel->valor1 !== 0.0) {
            $valor = (float) $nivel->valor1;
            $cubre = 'SI';
          }

          $cobrarPaciente = is_null($orden->cobrar_pac_pct)
            ? 100
            : (float) $orden->cobrar_pac_pct;
          $valorConOda = $cobrarPaciente < 100
            ? round($valor * $cobrarPaciente / 100, 4)
            : 0;

          Examen_Detalle::create([
            'id_examen_orden' => $orden->id,
            'id_examen' => $examen->id,
            'valor' => $valor,
            'cubre' => $cubre,
            'cobrar_pac_pct' => $cobrarPaciente,
            'valor_con_oda' => $valorConOda,
            'ip_creacion' => $ipCliente,
            'ip_modificacion' => $ipCliente,
            'id_usuariocrea' => $idUsuario,
            'id_usuariomod' => $idUsuario,
          ]);
        } elseif (!$seleccionar && !is_null($detalle)) {
          $detalle->delete();
        }

        $orden->refresh();
        $cantidad = $orden->detalles()->count();
        $valor = round((float) $orden->detalles()->sum('valor'), 4);
        $descuentoValor = round((float) $orden->detalles()->sum('valor_descuento'), 4);
        $subtotal = $valor - $descuentoValor;
        $recargoValor = round($subtotal * ((float) $orden->recargo_p) / 100, 4);
        $valorConOda = round((float) $orden->detalles()->sum('valor_con_oda'), 4);
        $totalConOda = round($valorConOda + $recargoValor, 4);

        $orden->update([
          'cantidad' => $cantidad,
          'valor' => $valor,
          'descuento_valor' => $descuentoValor,
          'recargo_valor' => $recargoValor,
          'total_valor' => round($subtotal + $recargoValor, 4),
          'valor_con_oda' => $valorConOda,
          'total_con_oda' => $totalConOda,
          'ip_modificacion' => $ipCliente,
          'id_usuariomod' => $idUsuario,
        ]);
      });

      $orden->refresh();
      return response()->json([
        'guardado' => true,
        'seleccionado' => $seleccionar,
        'cantidad' => $orden->cantidad,
        'valor' => $orden->valor,
        'descuento_valor' => $orden->descuento_valor,
        'recargo_valor' => $orden->recargo_valor,
        'total_valor' => $orden->total_valor,
      ]);
    }

    public function buscar(Request $request, $id_orden){
        
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      
      $orden = Examen_Orden::findOrFail($id_orden);

      $buscarExamen = trim((string) $request->input('buscador'));
      $buscarAgrupador = trim((string) $request->input('buscador2'));

      $agrupadoresQuery = $this->consultaAgrupadoresLaboratorio();
      $examenes_labs = $this->consultaExamenesLaboratorio();

      if($request->seleccionados=='1' && $buscarExamen === '' && $buscarAgrupador === '' ){

        $examenes_labs = $this->consultaExamenesLaboratorio($id_orden);
      }

      if ($buscarExamen !== '') {
        $termino = '%' . Str::lower(Str::ascii($buscarExamen)) . '%';
        $examenes_labs->where(function ($query) use ($termino) {
          $query->whereRaw($this->campoLaboratorioNormalizado('e.descripcion') . ' like ?', [$termino])
            ->orWhereRaw($this->campoLaboratorioNormalizado('e.nombre') . ' like ?', [$termino]);
        });
      }

      if ($buscarAgrupador !== '') {
        $termino = '%' . Str::lower(Str::ascii($buscarAgrupador)) . '%';
        $agrupadoresQuery->whereRaw(
          $this->campoLaboratorioNormalizado('agrupador.nombre') . ' like ?',
          [$termino]
        );
      }

      if($request->firma_dr==1){
        $orden->update(['doctor_firma' => '1307189140']);
      }else{
        $orden->update(['doctor_firma' => $orden->id_doctor_ieced]);  
      }

      $agrupador_labs = $agrupadoresQuery->get();
      $examenes_labs = $examenes_labs->get();

      $detalles_ch = [];
      $i=0;
       
      $nuevo_detalles = $orden->detalles;
      if(!is_null($nuevo_detalles)){
        foreach ($nuevo_detalles as $nuevo_detalle) {
          $detalles_ch[$i] = $nuevo_detalle->id_examen; 
          $i = $i + 1;       
        }  
      }
       
      return view('hc4/ordenes/orden_labs/listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden]);

    }

    public function cambia_perfil(Request $request, $id_orden){
        
      $ip_cliente= $_SERVER["REMOTE_ADDR"];
      $id_doctor = Auth::user()->id; 
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      
      $orden = Examen_Orden::findOrFail($id_orden);
      $detalles = $orden->detalles;

      if($request->id_protocolo!=null){
        $protocolo = $this->perfilFavoritoDelUsuario($request->id_protocolo);
        foreach ($detalles as $value) {
          $value->delete();
        }
        if(!is_null($protocolo)){
          $examen_proto = Examen_Protocolo::where('id_protocolo',$protocolo->id)->get();
          $id_protocolo = $protocolo->id; 
          $total=0;
          $cantidad=0;
          $etxt = '';

          foreach ($examen_proto as $examen_p) {
            $examen = Examen::find($examen_p->id_examen);
            //return $examen;
            $valor    = $examen->valor;
            $cubre    = 'NO';

            $input_det = [
                'id_examen_orden' => $orden->id,
                'id_examen'       => $examen->id,
                'valor'           => $valor,
                'cubre'           => $cubre,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $id_doctor,
                'id_usuariomod'   => $id_doctor,
            ];
          
            Examen_detalle::create($input_det);
            /// orden
            $total       = $total+$valor;
            $total       = round($total, 2);
            $cantidad++;
            $etxt = $etxt.'-'.$examen->id;
                
          }

          $input_or=[
            'id_protocolo' => $id_protocolo,
            'cantidad'   => $cantidad,
            'valor'      => $total,
            'total_valor' => $total
          ];
      
          $orden->update($input_or);

          Log_usuario::create([
            'id_usuario' => $id_doctor,
            'ip_usuario' => $ip_cliente,
            'descripcion' => "ACTUALIZA ORDEN DOCTOR - PERFIL",
            'dato_ant1' => 'orden: '.$orden->id.'- paciente: '.$orden->id,
            'dato1' => strtoupper($orden->paciente->nombre1." ".$orden->paciente->nombre2." ".$orden->paciente->apellido1." ".$orden->paciente->apellido2),
            'dato_ant2' => $etxt,
            
          ]); 
        }

      }

      $agrupador_labs = $this->consultaAgrupadoresLaboratorio()->get();
      $examenes_labs = $this->consultaExamenesLaboratorio()->get();
  

      $detalles_ch = [];
      $i=0;

      $orden = Examen_Orden::find($id_orden);
       
      $nuevo_detalles = $orden->detalles;
      if(!is_null($nuevo_detalles)){
        foreach ($nuevo_detalles as $nuevo_detalle) {
          $detalles_ch[$i] = $nuevo_detalle->id_examen; 
          $i = $i + 1;       
        }  
      }
       
      return view('hc4/ordenes/orden_labs/listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden]);

    }

    public function deseleccionar_perfil($id_orden){
        
      $ip_cliente= $_SERVER["REMOTE_ADDR"];
      $id_doctor = Auth::user()->id; 
      $opcion = '2';
      if($this->rol_new($opcion)){
          return redirect('/');
      }
      
      $orden = Examen_Orden::find($id_orden);

      $detalles = $orden->detalles;

      foreach ($detalles as $value) {
        $value->delete();
      } 
   
      $agrupador_labs = $this->consultaAgrupadoresLaboratorio()->get();
      $examenes_labs = $this->consultaExamenesLaboratorio()->get();
  

      $detalles_ch = [];
      $i=0;

      $orden->update([
        'valor'           => 0,
        'cantidad'        => 0,
        'descuento_p'     => 0,
        'descuento_valor' => 0,
        'recargo_p'       => 0,
        'recargo_valor'   => 0,
        'total_valor'     => 0
      ]);
       
      return view('hc4/ordenes/orden_labs/listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'orden' => $orden]);

    }



    public function examenes_favoritos(){

      $id_doctor = Auth::user()->id; 
      $protocolos = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->get();

      return view('hc4/ordenes/examenes_favoritos/index', ['protocolos' => $protocolos, 'nombre' => null]);

    }

    public function examenes_favoritos_buscar(Request $request){

      $id_doctor = Auth::user()->id; 
      $protocolos = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->where('nombre','like', '%'.$request->nombre.'%')->get();

      return view('hc4/ordenes/examenes_favoritos/index', ['protocolos' => $protocolos, 'nombre' => $request->nombre]);

    }

    public function examenes_favoritos_editar($id){

      $protocolo = $this->perfilFavoritoDelUsuario($id);
      $agrupador_labs = $this->consultaAgrupadoresLaboratorio()->get();
      // La edición inicia con el catálogo completo, igual que la creación;
      // los exámenes ya asociados se resaltan mediante $detalles_ch.
      $examenes_labs = $this->consultaExamenesLaboratorio()->get();
      
      $detalles_ch = [];
      $i=0;
       
      $pexamenes = $protocolo->examenes;
      if(!is_null($pexamenes)){
        foreach ($pexamenes as $pexamen) {
          $detalles_ch[$i] = $pexamen->id_examen; 
          $i = $i + 1;       
        }  
      }

      return view('hc4/ordenes/examenes_favoritos/editar',['protocolo' => $protocolo, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'agrupador_labs' => $agrupador_labs ]);        
    
    }

    public function examenes_favoritos_ver($id){
      $protocolo = $this->perfilFavoritoDelUsuario($id);
      $agrupador_labs = $this->consultaAgrupadoresLaboratorio()->get();
      $examenes_labs = $this->consultaExamenesProtocolo($id)->get();
      
      $detalles_ch = [];
      $i=0;
       

      return view('hc4/ordenes/examenes_favoritos/ver',['protocolo' => $protocolo, 'examenes_labs' => $examenes_labs, 'detalles_ch' => $detalles_ch, 'agrupador_labs' => $agrupador_labs ]);   
    }

    public function examenes_favoritos_crear(){  
      
      return view('hc4/ordenes/examenes_favoritos/crear');

    }

    
    public function examenes_favoritos_guardar(Request $request){

      $id_doctor = Auth::user()->id;
      $ip_cliente= $_SERVER["REMOTE_ADDR"]; 
      
      if($request->nombre==null){
        return [ 'estado' => 'err', 'respuesta' => 'INGRESE EL NOMBRE'];
      }
      
      $protocolo = Protocolo::where('estado','2')->where('id_usuariocrea',$id_doctor)->where('nombre',$request->nombre)->first();
      if(!is_null($protocolo)){
        return [ 'estado' => 'err', 'respuesta' => 'NOMBRE DEL PERFIL YA SE ENCUENTRA INGRESADO'];
      }

      $arr = [
        'nombre' => $request->nombre,
        'est_amb_hos' => '0',
        'estado' => '2',
        'pre_post' => null,
        'id_usuariocrea' => $id_doctor,
        'id_usuariomod' => $id_doctor,
        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente
      ];

      $id_protocolo = Protocolo::insertGetId($arr);

      return [ 'estado' => 'ok', 'respuesta' => $id_protocolo];

    }

    public function examenes_favoritos_listado($id){

      $this->perfilFavoritoDelUsuario($id);

      $agrupador_labs = $this->consultaAgrupadoresLaboratorio()->get();
      $examenes_labs = $this->consultaExamenesLaboratorio()->get();
      
      return view('hc4/ordenes/examenes_favoritos/listado',['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'id' => $id]);

    }

    public function examenes_favoritos_actualizar(Request $request){

      $id_doctor = Auth::user()->id;
      $ip_cliente= $_SERVER["REMOTE_ADDR"]; 

      $protocolo = $this->perfilFavoritoDelUsuario($request->xid);
      
      if($request->nombre==null){
        return [ 'estado' => 'err', 'respuesta' => 'INGRESE EL NOMBRE'];
      }

      $repetido = Protocolo::where('estado', '2')
        ->where('id_usuariocrea', $id_doctor)
        ->where('nombre', $request->nombre)
        ->where('id', '<>', $protocolo->id)
        ->exists();
      if ($repetido) {
        return [ 'estado' => 'err', 'respuesta' => 'NOMBRE DEL PERFIL YA SE ENCUENTRA INGRESADO'];
      }
      
      $arr = [
        'nombre' => $request->nombre,
        'id_usuariomod' => $id_doctor,
        'ip_modificacion' => $ip_cliente
      ];

      $protocolo->update($arr);

      return [ 'estado' => 'ok', 'respuesta' => $protocolo->id];

    }

    public function examenes_favoritos_buscador(Request $request, $id){
        
      $protocolo = $this->perfilFavoritoDelUsuario($id);

      $agrupador_labs = $this->consultaAgrupadoresLaboratorio()->get();
      $examenes_labs = $this->consultaExamenesLaboratorio();

      if($request->seleccionados=='1' && $request->buscador ==null && $request->buscador2==null ){

        $examenes_labs = $this->consultaExamenesProtocolo($id);
      }

      if($request->buscador!=null){
        $examenes_labs = $examenes_labs->where('e.descripcion','like','%'.$request->buscador.'%');
      }

      if($request->buscador2!=null){
        $examenes_labs = $examenes_labs->where('agrupador.nombre','like','%'.$request->buscador2.'%');
      }

      $examenes_labs = $examenes_labs->get();

      $detalles_ch = [];
      $i=0;
       
      $pexamenes = $protocolo->examenes;
      if(!is_null($pexamenes)){
        foreach ($pexamenes as $pexamen) {
          $detalles_ch[$i] = $pexamen->id_examen; 
          $i = $i + 1;       
        }  
      }
       
      return view('hc4/ordenes/examenes_favoritos/xlistado',['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'id' => $id, 'detalles_ch' => $detalles_ch]);

    }

    public function examenes_favoritos_seleccionar($protocolo, $id){
      $id_doctor = Auth::user()->id;
      $ip_cliente= $_SERVER["REMOTE_ADDR"];

      $this->perfilFavoritoDelUsuario($protocolo);
      Examen::where('id', $id)->where('estado', '1')->firstOrFail();

      $examen_protocolo = Examen_Protocolo::where('id_protocolo',$protocolo)->where('id_examen',$id)->first();
      if(is_null($examen_protocolo)){
        $arr = [
          'id_protocolo' => $protocolo,
          'id_examen' => $id,
          'ip_creacion' => $ip_cliente,
          'ip_modificacion' => $ip_cliente,
          'id_usuariocrea' => $id_doctor,
          'id_usuariomod' => $id_doctor 
        ];

        Examen_Protocolo::create($arr);
      }  

      return "ok";  
      
    }

    public function examenes_favoritos_eliminar($protocolo, $id){
      $id_doctor = Auth::user()->id;
      $ip_cliente= $_SERVER["REMOTE_ADDR"];

      $this->perfilFavoritoDelUsuario($protocolo);

      $examen_protocolo = Examen_Protocolo::where('id_protocolo',$protocolo)->where('id_examen',$id)->first();
      if (!is_null($examen_protocolo)) {
        $examen_protocolo->delete();
      }

      return "ok";  
      
    }

    
    

    
   
}


