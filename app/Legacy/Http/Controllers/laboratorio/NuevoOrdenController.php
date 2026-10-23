<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Carbon\Carbon;
use Cookie;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Orden_Seguro;
use Sis_medico\Convenio;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Labs_doc_externos;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Protocolo;
use Sis_medico\User;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Detalle_Forma_Pago;
use Sis_medico\Examen_Detalle_Seguro;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden_Agenda;
use Sis_medico\Labs_Grupo_Familiar;
use Sis_medico\Log_usuario;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\Examen_Forma_Pago_Seguro;

class NuevoOrdenController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol_new($opcion)
    {
        //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {

            return true;
        }
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 16, 20)) == false) {
            return true;
        }
    }
    private function rol_sis()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1), true)) {
            return true;
        }
    }
    private function rol_supervision()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 12, 11, 3, 16, 22)) == false && $id_auth != '1307189140') {
            return true;
        }
    }

    private function rol_control()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 10, 11, 16)) == false) {
            return true;
        }
    }

    private function rol_control2()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 10, 16)) == false) {
            return true;
        }
    }

    private function rol_doctor()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 3)) == false) {
            return true;
        }
    }

    public function cotizador_editar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $orden = Examen_Orden::find($id);
        $asesor = null;
        if (!is_null($orden->asesor_venta)) {
            $asesor = User::find($orden->asesor_venta);
        }

        //$usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->where('training', '<>', '1')->orderBy('nombre1')->get();
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->where('uso_laboratorio', '1')->get();

        $formas = DB::table('forma_de_pago')->where('estado', '1')->get();
        //dd($formas);

        $examenes = Examen::orderBy('id_agrupador')->get();
        //dd($examenes);
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros1    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.estado_labs', '1')->where('s.tipo', '>', '1')->orderBy('s.nombre');
        $seguros2    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.estado_labs', '1')->where('s.tipo', '>', '0')->join('convenio as c', 'c.id_seguro', 's.id')->select('s.*')->orderBy('s.nombre');
        $seguros     = $seguros1->union($seguros2)->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        $protocolos2 = Protocolo::where('estado', '3')->get();
        $codigo      = Labs_doc_externos::where('estado', '1')->get();

        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
        $empresa_labs = Empresa::where('prioridad_labs', 1)->first();
        $ct_sucursales = Ct_Sucursales::whereIn("codigo_sucursal", ['001', '007'])->where('id_empresa', $empresa_labs->id)->get();
        /*return view('laboratorio/orden/create_particular',['usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'protocolos' => $protocolos, 'empresas' => $empresas]);*/
        //$this->actualiza_tiene_membresia($orden); 

        return view('laboratorio/orden/nuevo_editar_cotizacion', ['ct_sucursales' => $ct_sucursales, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden' => $orden, 'formas' => $formas, 'protocolos' => $protocolos, 'codigo' => $codigo, 'protocolos2' => $protocolos2, 'asesor' => $asesor]);
    }

    public function dividir_orden($id_orden){

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        if(!$this->rol_sis()){
            return ['status' => 'error', 'msj' => 'Usuario no autorizado para utilizar'];
        }

        $orden      = Examen_Orden::find($id_orden);
        $detalles   = $orden->detalles;
        $seguro     = $orden->seguro;
        $nivel = $orden->nivel;


        if($seguro->tipo!=1){
            return ['status' => 'error', 'msj' => 'Esta orden no se puede dividir, seguro: ' .$seguro->nombre];
        }
        
        if($orden->estado!=1){
            return ['status' => 'error', 'msj' => 'Esta orden no esta emitida, no se puede dividir'];
        }
        
        $resp['status'] = 'success';
        //$resp = $this->verificar_division($orden,$detalles);
        if($resp['status'] == 'error'){
            if($resp['contPrivada']==0){
                $msj = '. No tiene examenes que el seguro no cubre';
            }
            else{
                $msj = '. No tiene examenes que el seguro cubre';
            }
            return ['status' => 'error', 'msj' => $resp['msj'] . $msj];
        }

        $array_orden_particular = [
            'id_examen_orden' => $orden->id,
            'id_paciente' => $orden->id_paciente,
            'anio' => $orden->anio,
            'mes' => $orden->mes,
            'estado' => '-1',
            'estado_pago' => '0',
            'id_doctor_ieced' => $orden->id_doctor_ieced,
            'doctor_txt' => $orden->doctor_txt,
            'id_seguro' => 1,
            //'id_protocolo' => $orden->id_protocolo,
            //'id_nivel' => $orden->id_nivel,
            'est_amb_hos' => $orden->est_amb_hos,
            'cantidad' => 0,
            'valor' => 0,
            'id_empresa' => $orden->id_empresa,
            'hcid' => $orden->hcid,
            'id_agenda' => $orden->id_agenda,
            'realizado' => $orden->realizado,
            'observacion' => $orden->observacion,
            'fecha_convenios' => $orden->fecha_convenios,
            'id_forma_de_pago' => $orden->id_forma_de_pago,
            'motivo_descuento' => $orden->motivo_descuento,
            'descuento_p' => 0,
            'descuento_valor' => 0,
            'recargo_p' => 0,
            'recargo_valor' => 0,
            'total_valor' => 0,
            'tarjeta' => $orden->tarjeta,
            'fecha_orden' => $orden->fecha_orden,
            'er_biometria' => $orden->er_biometria,
            'er_bioquimica' => $orden->er_bioquimica,
            'er_manual' => $orden->er_manual,
            'doctor_firma' => $orden->doctor_firma,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'completo' => $orden->completo,
            'numorden_iess' => $orden->numorden_iess,
            'historia_iess' => $orden->historia_iess,
            'estado_orden_iess' => $orden->estado_orden_iess,
            'codigo' => $orden->codigo,
            'enviado_doctor_externo' => $orden->enviado_doctor_externo,
            'pres_dom' => $orden->pres_dom,
            'pago_online' => $orden->pago_online,
            'fecha_tentativa' => $orden->fecha_tentativa,
            'tipo_documento' => $orden->tipo_documento,
            'cedula_factura' => $orden->cedula_factura,
            'nombre_factura' => $orden->nombre_factura,
            'apellido_factura' => $orden->apellido_factura,
            'direccion_factura' => $orden->direccion_factura,
            'ciudad_factura' => $orden->ciudad_factura,
            'email_factura' => $orden->email_factura,
            'telefono_factura' => $orden->telefono_factura,
            'ticket' => $orden->ticket,
            'gestion' => $orden->gestion,
            'proceso' => $orden->proceso,
            //'total_nivel2' => $orden->total_nivel2,
            'toma_muestra' => $orden->toma_muestra,
            'comprobante' => $orden->comprobante,
            'fecha_envio' => $orden->fecha_envio,
            'orden_agrupado' => $orden->orden_agrupado,
            'venta_gastro' => $orden->venta_gastro,
            /*'numero_oda' => $orden->numero_oda,
            'cobrar_pac_pct' => $orden->cobrar_pac_pct,
            'valor_con_oda' => $orden->valor_con_oda,
            'total_con_oda' => $orden->total_con_oda,*/
            'estado_venta' => $orden->estado_venta,
            'estado_comision' => $orden->estado_comision,
            'membresia_id' => $orden->membresia_id,
            'membresia_detalle_id' => $orden->membresia_detalle_id,
            'resultados_estado' => $orden->resultados_estado,
            'asesor_venta' => $orden->asesor_venta,
            'puntos_aplicados' => $orden->puntos_aplicados,
            'total_planilla' => $orden->total_planilla,
            'sucursal' => $orden->sucursal,
            'punto_emision' => $orden->punto_emision,
            'pct_resultado' => $orden->pct_resultado,
            'observacion_app' => $orden->observacion_app,
            'estado_comision2' => $orden->estado_comision2,
            'total_comision' => $orden->total_comision,
            'observacion_adicional' => $orden->observacion_adicional,
            'revisado' => $orden->revisado,
            'comentario' => $orden->comentario,
            'leido_bd' => $orden->leido_bd,
            'otros' => $orden->otros,
            'prioridad_orden' => $orden->prioridad_orden,
            'id_agenda_consulta' => $orden->id_agenda_consulta,
            'proceso_aut' => $orden->proceso_aut,
            'cobrar_pac_pct' => 100,
        ];

        $array_orden_seguro = [
            'id_examen_orden' => $orden->id,
            'id_paciente' => $orden->id_paciente,
            'anio' => $orden->anio,
            'mes' => $orden->mes,
            'estado' => '-1',
            'estado_pago' => '0',
            'id_doctor_ieced' => $orden->id_doctor_ieced,
            'doctor_txt' => $orden->doctor_txt,
            'id_seguro' => $orden->id_seguro,
            'id_protocolo' => $orden->id_protocolo,
            'id_nivel' => $orden->id_nivel,
            'est_amb_hos' => $orden->est_amb_hos,
            'cantidad' => 0,
            'valor' => 0,
            'id_empresa' => $orden->id_empresa,
            'hcid' => $orden->hcid,
            'id_agenda' => $orden->id_agenda,
            'realizado' => $orden->realizado,
            'observacion' => $orden->observacion,
            'fecha_convenios' => $orden->fecha_convenios,
            'id_forma_de_pago' => $orden->id_forma_de_pago,
            'motivo_descuento' => $orden->motivo_descuento,
            'descuento_p' => 0,
            'descuento_valor' => 0,
            'recargo_p' => 0,
            'recargo_valor' => 0,
            'total_valor' => 0,
            'tarjeta' => $orden->tarjeta,
            'fecha_orden' => $orden->fecha_orden,
            'er_biometria' => $orden->er_biometria,
            'er_bioquimica' => $orden->er_bioquimica,
            'er_manual' => $orden->er_manual,
            'doctor_firma' => $orden->doctor_firma,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'completo' => $orden->completo,
            'numorden_iess' => $orden->numorden_iess,
            'historia_iess' => $orden->historia_iess,
            'estado_orden_iess' => $orden->estado_orden_iess,
            'codigo' => $orden->codigo,
            'enviado_doctor_externo' => $orden->enviado_doctor_externo,
            'pres_dom' => $orden->pres_dom,
            'pago_online' => $orden->pago_online,
            'fecha_tentativa' => $orden->fecha_tentativa,
            'tipo_documento' => $orden->tipo_documento,
            'cedula_factura' => $orden->cedula_factura,
            'nombre_factura' => $orden->nombre_factura,
            'apellido_factura' => $orden->apellido_factura,
            'direccion_factura' => $orden->direccion_factura,
            'ciudad_factura' => $orden->ciudad_factura,
            'email_factura' => $orden->email_factura,
            'telefono_factura' => $orden->telefono_factura,
            'ticket' => $orden->ticket,
            'gestion' => $orden->gestion,
            'proceso' => $orden->proceso,
            'total_nivel2' => $orden->total_nivel2,
            'toma_muestra' => $orden->toma_muestra,
            'comprobante' => $orden->comprobante,
            'fecha_envio' => $orden->fecha_envio,
            'orden_agrupado' => $orden->orden_agrupado,
            'venta_gastro' => $orden->venta_gastro,
            'numero_oda' => $orden->numero_oda,
            'cobrar_pac_pct' => $orden->cobrar_pac_pct,
            'valor_con_oda' => $orden->valor_con_oda,
            'total_con_oda' => $orden->total_con_oda,
            'estado_venta' => $orden->estado_venta,
            'estado_comision' => $orden->estado_comision,
            'membresia_id' => $orden->membresia_id,
            'membresia_detalle_id' => $orden->membresia_detalle_id,
            'resultados_estado' => $orden->resultados_estado,
            'asesor_venta' => $orden->asesor_venta,
            'puntos_aplicados' => $orden->puntos_aplicados,
            'total_planilla' => $orden->total_planilla,
            'sucursal' => $orden->sucursal,
            'punto_emision' => $orden->punto_emision,
            'pct_resultado' => $orden->pct_resultado,
            'observacion_app' => $orden->observacion_app,
            'estado_comision2' => $orden->estado_comision2,
            'total_comision' => $orden->total_comision,
            'observacion_adicional' => $orden->observacion_adicional,
            'revisado' => $orden->revisado,
            'comentario' => $orden->comentario,
            'leido_bd' => $orden->leido_bd,
            'otros' => $orden->otros,
            'prioridad_orden' => $orden->prioridad_orden,
            'id_agenda_consulta' => $orden->id_agenda_consulta,
            'proceso_aut' => $orden->proceso_aut,
        ];

        $orden_particular = Examen_Orden_Seguro::where('id_examen_orden', $id_orden)->where('id_seguro',1)->where('estado','<>',0)->first();
        if(is_null($orden_particular)){
            if($orden->fecha_envio != null){
                return ['status' => 'error', 'msj' => 'Esta orden no se puede dividir, ya fue enviada al SRI'];    
            }
            $orden_particular = Examen_Orden_Seguro::create($array_orden_particular);
        }
        
        $orden_privada = Examen_Orden_Seguro::where('id_examen_orden', $id_orden)->where('id_seguro',$orden->id_seguro)->where('estado','<>',0)->first();
        if(is_null($orden_privada)){
            if($orden->fecha_envio != null){
                return ['status' => 'error', 'msj' => 'Esta orden no se puede dividir, ya fue enviada al SRI'];    
            }
            //$array_orden['id_seguro']  = $orden->id_seguro;
            $orden_privada = Examen_Orden_Seguro::create($array_orden_seguro);
        }
        
        foreach ($detalles as $detalle) {
            $examen = $detalle->examen;
            $e_valor = $examen->valor;
            $cubre = true;
            $examen_valor = null;
            if ($nivel != null) {
                $examen_valor = Examen_Nivel::where('nivel', $nivel->id)->where('id_examen', $examen->id)->first();
                if (!is_null($examen_valor)) {
                    if ($seguro->id != '1' && ($examen_valor->valor1 == '0' || $examen_valor->valor1 == null)) {
                        $cubre = false;
                    } else {
                        $cubre = true;
                    }
                } else {
                    $cubre = false;
                }
                
                $arr_detalle= [
                    'id_examen' => $detalle->id_examen,
                    'estado' => '1',
                    'estado_resultado' => $detalle->estado_resultado,
                    'cantidad' => $detalle->cantidad,
                    'cubre' => $detalle->cubre,
                    'valor' => $detalle->valor,
                    'valor_2' => $detalle->valor_2,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'p_comision' => $detalle->p_comision,
                    'human_labs' => $detalle->human_labs,
                    'p_descuento' => $detalle->p_descuento,
                    'valor_descuento' => $detalle->valor_descuento,
                    'valor_nivel2' => $detalle->valor_nivel2,
                    
                ];
                if($cubre==true){
                    $arr_detalle['id_examen_orden_seguro'] = $orden_privada->id;
                    $arr_detalle['cobrar_pac_pct'] = $detalle->cobrar_pac_pct;
                    $arr_detalle['valor_con_oda'] = $detalle->valor_con_oda;
                    $arr_detalle['valor_comision'] = $detalle->valor_comision;
                }
                else{
                    $arr_detalle['id_examen_orden_seguro'] = $orden_particular->id;
                    $arr_detalle['cobrar_pac_pct'] = 100;
                }
                $examen_seguro = Examen_Detalle_Seguro::where('id_examen_orden_seguro',$arr_detalle['id_examen_orden_seguro'])->where('id_examen',$detalle->id_examen)->first();
                if(is_null($examen_seguro)){
                    Examen_Detalle_Seguro::create($arr_detalle);
                }
                
            }
        }
        //return ['status' => 'success', 'msj' => 'Orden dividida con exito', 'orden_privada' => $orden_privada,'orden_particular' => $orden_particular];
        return view('contable/cierre_caja/modal_dividir_orden', ['orden_particular' => $orden_particular,'orden_privada' => $orden_privada]);
    }

    public function verificar_division($orden,$detalles){
        $seguro     = $orden->seguro;
        $nivel      = $orden->nivel;
        $detalles_particular = 0;
        $detalles_privada = 0;

        foreach ($detalles as $detalle) {
            $examen = $detalle->examen;
            $cubre = true;
            $examen_valor = null;

            if ($nivel != null) {
                $examen_valor = Examen_Nivel::where('nivel', $nivel->id)->where('id_examen', $examen->id)->first();
                if (!is_null($examen_valor)) {
                    if ($seguro->id != '1' && ($examen_valor->valor1 == '0' || $examen_valor->valor1 == null)) {
                        $cubre = false;
                    } else {
                        $cubre = true;
                    }
                } else {
                    $cubre = false;
                }
    
                if ($cubre) {
                    $detalles_particular++;
                } else {
                    $detalles_privada++;
                }
            }
        }
    
        if ($detalles_particular == 0 || $detalles_privada == 0) {
            return ['status' => 'error', 'msj' => 'No se puede dividir la orden','contPrivada' => $detalles_privada, 'contParticular' => $detalles_particular];
        }
        else{
            return ['status' => 'success', 'msj' => 'Comprobacion correcta'];
        }
    }
    public function actualiza_membresia($id){

        $orden = Examen_Orden::find($id);
        if(!is_null($orden)){
            if( $orden->estado == '-1' and $orden->membresia_id == null) {
                $membresia_controller = new MembresiasLabsController;
                $res = $membresia_controller->buscar_membresia($orden->id_paciente);
                if ($res['estado'] == 'ok' ) {
                    $orden->update(['membresia_id' => $res['id']]);    
                } 
            } 
        }
          

    }
    
    public function actualiza_membresia_seguro($id)
    {
        $orden = Examen_Orden_Seguro::find($id);
        if(!is_null($orden)){
            if( $orden->estado == '-1' and $orden->membresia_id == null) {
                $membresia_controller = new MembresiasLabsController;
                $res = $membresia_controller->buscar_membresia($orden->id_paciente);
                if ($res['estado'] == 'ok' ) {
                    $orden->update(['membresia_id' => $res['id']]);    
                } 
            } 
        }
    }

    public function agrupador_labs_buscar_seguro(Request $request)
    {
        if ($this->validacion($request) != 'OK') {
            return $this->validacion($request);
        }
        $agrupador_labs = [];
        $examenes_labs = [];
        $orden = Examen_Orden_Seguro::find($request->cotizacion);

        if ($request->buscador != null || $request->buscador2 != null) {
            $agrupador_labs = DB::table('examen_agrupador_labs');
            $examenes_labs  = DB::table('examen_agrupador_sabana as sa')->join('examen as e', 'e.id', 'sa.id_examen')->where('e.estado', '1')->where('sa.estado', '1')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id', 'e.tiempos as tiempo_examen', 'e.sugerencia', 'e.nombre_largo');
            
            if ($request->buscador != null) {
                $examenes_labs = $examenes_labs->where(function ($query) use ($request) {
                    $query->where('e.descripcion', 'like', '%' . $request->buscador . '%')
                        ->orWhere('e.nombre_largo', 'like', '%' . $request->buscador . '%');
                });
            }

            if ($request->buscador2 != null) {
                $agrupador_labs = $agrupador_labs->where('nombre', 'like', '%' . $request->buscador2 . '%');
            }

            $examenes_labs = $examenes_labs->orderBy('sa.nro_orden')->get();
            $agrupador_labs = $agrupador_labs->get();
        }
        return view('laboratorio.orden.nuevo_listado_seguro', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'orden' => $orden ]);
    }


    public function agrupador_labs_buscar(Request $request)
    {

        if ($this->validacion($request) != 'OK') {
            return $this->validacion($request);
        }
        $agrupador_labs = [];
        $examenes_labs = [];

        $orden = Examen_Orden::find($request->cotizacion);

        if ($request->buscador != null || $request->buscador2 != null) {

            $agrupador_labs = DB::table('examen_agrupador_labs');
            $examenes_labs  = DB::table('examen_agrupador_sabana as sa')->join('examen as e', 'e.id', 'sa.id_examen')->where('e.estado', '1')->where('sa.estado', '1')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id', 'e.tiempos as tiempo_examen', 'e.sugerencia', 'e.nombre_largo');
            
            if ($request->buscador != null) {
                $examenes_labs = $examenes_labs->where(function ($query) use ($request) {
                    $query->where('e.descripcion', 'like', '%' . $request->buscador . '%')
                        ->orWhere('e.nombre_largo', 'like', '%' . $request->buscador . '%');
                });
            }

            if ($request->buscador2 != null) {
                $agrupador_labs = $agrupador_labs->where('nombre', 'like', '%' . $request->buscador2 . '%');
            }

            $examenes_labs = $examenes_labs->orderBy('sa.nro_orden')->get();
    
            $agrupador_labs = $agrupador_labs->get();

        }    

        return view('laboratorio.orden.nuevo_listado', ['agrupador_labs' => $agrupador_labs, 'examenes_labs' => $examenes_labs, 'orden' => $orden ]);
    }

    public function validacion(Request $request)
    {

        if ($request->id == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if ($request->nombre1 == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if ($request->apellido1 == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL PACIENTE</h5>";
        }
        if ($request->sexo == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL SEXO</h5>";
        }
        if ($request->fecha_nacimiento == null) {
            return "<h5 style='color: #ff6600;'>INGRESE LA FECHA DE NACIMIENTO</h5>";
        }
        if ($request->id_doctor_ieced == null) {
            return "<h5 style='color: #ff6600;'>INGRESE EL MEDICO</h5>";
        }
        if ($request->pres_dom == null) {
            return "<h5 style='color: #ff6600;'>SELECCIONE DOMICILIO O PRESENCIAL</h5>";
        }

        if ($request->id_seguro != null) {
            $seguro   = Seguro::find($request->id_seguro);
            $convenio = Convenio::where('id_seguro', $request->id_seguro)->where('id_empresa', $request->id_empresa)->get();
            if ($convenio->count() > 1) {
                if ($request->id_nivel == null) {
                    //return $request->all();
                    return "<h5 style='color: #ff6600;'>SELECCIONE EL NIVEL PARA REALIZAR LA COTIZACIÓN</h5>";
                }
            }
        } else {

            return "<h5 style='color: #ff6600;'>SELECCIONE EL SEGURO PARA REALIZAR LA COTIZACIÓN</h5>";
        }

        if ($request->descuento_p > 0) {
            if ($request->motivo_descuento == null) {
                return "<h5 style='color: #ff6600;'>INGRESE QUIEN AUTORIZA EL DESCUENTO Y POR QUE MEDIO LO REALIZA</h5>";
            }
        }

        return "OK";
    }

    public function cargar_detalle_orden($id_orden)
    {
        $orden      = Examen_Orden::find($id_orden);
        $detalles   = DB::table('examen_detalle as ed')
        ->where('ed.id_examen_orden', $id_orden)
        ->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')
        ->select('ed.*', 'e.descripcion', 'e.nombre as examen')
        ->join('examen as e', 'e.id', 'ed.id_examen')
        ->orderBy('ed.id')->get();

        $seguro = $orden->seguro;
        $agenda = Examen_Orden_Agenda::where('id_orden', $orden->id)->first();
        $rolUsuario = Auth::user()->id_tipo_usuario;
        return view('laboratorio.orden.detalle_orden', ['detalles' => $detalles,'orden' => $orden, 'seguro' => $seguro, 'agenda' => $agenda, 'rolUsuario' =>$rolUsuario]);
    }
    
    public function cotizador_editar_seguro($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $orden = Examen_Orden_Seguro::find($id);
        $asesor = null;
        if (!is_null($orden->asesor_venta)) {
            $asesor = User::find($orden->asesor_venta);
        }
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->orderBy('apellido1')->where('uso_laboratorio', '1')->get();
        $formas = DB::table('forma_de_pago')->where('estado', '1')->get();
        $examenes = Examen::orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $seguros1    = Seguro::where('inactivo', '1')->where('estado_labs', '1')->where('tipo', '>', '1')->orderBy('nombre');
        $seguros2    = DB::table('seguros as s')->where('s.inactivo', '1')->where('s.estado_labs', '1')->where('s.tipo', '>', '0')->join('convenio as c', 'c.id_seguro', 's.id')->select('s.*')->orderBy('s.nombre');
        $seguros     = $seguros1->union($seguros2)->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        $protocolos2 = Protocolo::where('estado', '3')->get();
        $codigo      = Labs_doc_externos::where('estado', '1')->get();
        $empresas = Empresa::where('empresa.estado', '1')->join('convenio as c', 'c.id_empresa', 'empresa.id')->select('empresa.id', 'empresa.nombrecomercial')->groupBy('empresa.id', 'empresa.nombrecomercial')->get();
        $empresa_labs = Empresa::where('prioridad_labs', 1)->first();
        $ct_sucursales = Ct_Sucursales::whereIn("codigo_sucursal", ['001', '007'])->where('id_empresa', $empresa_labs->id)->get();
        return view('laboratorio/orden/nuevo_editar_cotizacion_seguro', ['ct_sucursales' => $ct_sucursales, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'seguros' => $seguros, 'empresas' => $empresas, 'orden' => $orden, 'formas' => $formas, 'protocolos' => $protocolos, 'codigo' => $codigo, 'protocolos2' => $protocolos2, 'asesor' => $asesor]);
    }
    public function cotizador_recalcular(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $orden = Examen_Orden::find($request->cotizacion);

        //RECALCULAR
        $total    = 0;
        $cantidad = 0;
        
        $detalles = $orden->detalles;
      
        $pyf_details = array();
        $total_descuento = 0;$total_valor_con_oda = 0;
        foreach ($detalles as $detalle) {
            //$cantidad = $cantidad + 1;
            $examen   = Examen::find($detalle->id_examen);
            $valor    = $examen->valor;
            $cubre    = 'NO';
            $ex_nivel = Examen_Nivel::where('id_examen', $detalle->id_examen)->where('nivel', $request->id_nivel)->first();
            if (!is_null($ex_nivel)) {
                if ($ex_nivel->valor1 != 0) {
                    $valor = $ex_nivel->valor1;
                    $cubre = 'SI';
                }
            }
            if($detalle->sin_cobertura == '1' and $cubre = 'SI' ){
                $valor    = $examen->valor;         
            }
            
            $p_descuento = 0;
            $valor_descuento = $detalle->valor_descuento;
            if($valor < $valor_descuento){
                $valor_descuento = 0;
                $p_descuento = 0;    
            }
            if($valor_descuento > 0){
                $p_descuento = $valor_descuento / $valor ;
                $p_descuento = $p_descuento * 100;
                $p_descuento = round($p_descuento,4);
                $total_descuento += $valor_descuento; 
            }
            
            $cobrar_pac_pct = 100;
            $valor_con_oda = $detalle->valor_con_oda; 

           

            if($valor < $valor_con_oda){
                $valor_con_oda = 0;
                $cobrar_pac_pct = 100;
            }

            if ($valor_con_oda > 0 ) {
                $cobrar_pac_pct = $valor_con_oda / $valor;
                $cobrar_pac_pct = $cobrar_pac_pct * 100;
                $cobrar_pac_pct = round($cobrar_pac_pct,4);
                $total_valor_con_oda += $valor_con_oda; 
            }


            $input_det = [
                'valor'           => $valor,
                'cubre'           => $cubre,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'valor_descuento' => $valor_descuento,
                'p_descuento'     => $p_descuento,
                'cobrar_pac_pct'  => $cobrar_pac_pct,
                'valor_con_oda'   => $valor_con_oda,

            ];
            array_push($pyf_details, array(
                "valor"           => $valor,
                "nombre"          => $detalle->examen->nombre,
                "valor_descuento" => $valor_descuento,
                "p_descuento"     => $p_descuento,
            ));
            $detalle->update($input_det);
            
        }
        $manage          = json_encode($pyf_details);
        $orden           = Examen_Orden::find($request->cotizacion);
        $suma_valor      = $orden->detalles->sum('valor');
        $cantidad        = $orden->detalles->count();
        $suma_valor      = round($suma_valor, 4);

        $total_descuento_p = $total_descuento / $suma_valor;
        $total_descuento_p = $total_descuento_p * 100;
        $total_descuento_p = round($total_descuento_p,4); 

        /*$total_cobrar_pac_pct = 100;
        if($total_valor_con_oda > 0){
            $total_cobrar_pac_pct = $total_valor_con_oda / $suma_valor;
            $total_cobrar_pac_pct = $total_cobrar_pac_pct * 100;
            $total_cobrar_pac_pct = round($total_cobrar_pac_pct,4);
        }*/
         
    
        $total_valor    = $suma_valor - $total_descuento;
        $total_oda      = $total_valor_con_oda - $total_descuento;
        $recargo_p      = 0;$recargo_valor = 0;

        $total_valor = round($total_valor, 2);
        
        //ACTUALIZAR ORDEN
        $input_ex = [
            'motivo_descuento' => $request->motivo_descuento,
            'id_forma_de_pago' => $request->id_forma_pago,
            'descuento_p'      => $total_descuento_p,
            'descuento_valor'  => $total_descuento,
            'recargo_p'        => $recargo_p,
            'recargo_valor'    => $recargo_valor,
            'total_valor'      => $total_valor,
            'id_seguro'        => $request['id_seguro'],
            'id_nivel'         => $request->id_nivel,
            'est_amb_hos'      => $request['est_amb_hos'],
            'id_doctor_ieced'  => $request['id_doctor_ieced'],
            'id_empresa'       => $request['id_empresa'],
            'cantidad'         => $cantidad,
            'valor'            => $suma_valor,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
            //'cobrar_pac_pct'   => $total_cobrar_pac_pct,
            'valor_con_oda'    => $total_valor_con_oda,
            'total_con_oda'    => $total_oda,
        ];

        $orden->update($input_ex);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "RECALCULAR COTIZACION",
            'dato_ant4'   => $manage,
        ]);

        return "ok";
    }
    public function cotizador_seguro_recalcular($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $orden = Examen_Orden_Seguro::find($id);

        //RECALCULAR
        $total    = 0;
        $cantidad = 0;
        
        $detalles = $orden->detalles;
      
        $pyf_details = array();
        $total_descuento = 0;$total_valor_con_oda = 0;
        foreach ($detalles as $detalle) {
            //$cantidad = $cantidad + 1;
            $examen   = Examen::find($detalle->id_examen);
            $valor    = $examen->valor;
            $cubre    = 'NO';
            $ex_nivel = Examen_Nivel::where('id_examen', $detalle->id_examen)->where('nivel', $orden->id_nivel)->first();
            if (!is_null($ex_nivel)) {
                if ($ex_nivel->valor1 != 0) {
                    $valor = $ex_nivel->valor1;
                    $cubre = 'SI';
                }
            }
            
            $p_descuento = 0;
            $valor_descuento = $detalle->valor_descuento;
            if($valor < $valor_descuento){
                $valor_descuento = 0;
                $p_descuento = 0;    
            }
            if($valor_descuento > 0){
                $p_descuento = $valor_descuento / $valor ;
                $p_descuento = $p_descuento * 100;
                $p_descuento = round($p_descuento,4);
                $total_descuento += $valor_descuento; 
            }
            
            $cobrar_pac_pct = 100;
            $valor_con_oda = $detalle->valor_con_oda; 
            if($valor < $valor_con_oda){
                $valor_con_oda = 0;
                $cobrar_pac_pct = 100;
            }
            if ($valor_con_oda > 0 ) {
                $cobrar_pac_pct = $valor_con_oda / $valor;
                $cobrar_pac_pct = $cobrar_pac_pct * 100;
                $cobrar_pac_pct = round($cobrar_pac_pct,4);
                $total_valor_con_oda += $valor_con_oda; 
            }

            $input_det = [
                'valor'           => $valor,
                'cubre'           => $cubre,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'valor_descuento' => $valor_descuento,
                'p_descuento'     => $p_descuento,
                'cobrar_pac_pct'  => $cobrar_pac_pct,
                'valor_con_oda'   => $valor_con_oda,

            ];
            array_push($pyf_details, array(
                "valor"           => $valor,
                "nombre"          => $detalle->examen->nombre,
                "valor_descuento" => $valor_descuento,
                "p_descuento"     => $p_descuento,
            ));
            $detalle->update($input_det);
            
        }
        $manage          = json_encode($pyf_details);
        $orden           = Examen_Orden_Seguro::find($id);
        $suma_valor      = $orden->detalles->sum('valor');
        $cantidad        = $orden->detalles->count();
        $suma_valor      = round($suma_valor, 4);
        $total_descuento_p = 0;
        if($suma_valor>0){
            $total_descuento_p = $total_descuento / $suma_valor;
        }
        
        $total_descuento_p = $total_descuento_p * 100;
        $total_descuento_p = round($total_descuento_p,4); 

        $total_cobrar_pac_pct = 100;
        if($total_valor_con_oda > 0){
            if($suma_valor>0){
                $total_cobrar_pac_pct = $total_valor_con_oda / $suma_valor;
            }
            
            $total_cobrar_pac_pct = $total_cobrar_pac_pct * 100;
            $total_cobrar_pac_pct = round($total_cobrar_pac_pct,4);
        }
         
    
        $total_valor    = $suma_valor - $total_descuento;
        $total_oda      = $total_valor_con_oda - $total_descuento;
        $recargo_p      = 0;$recargo_valor = 0;

        $total_valor = round($total_valor, 2);
        //ACTUALIZAR ORDEN
        $input_ex = [
            'motivo_descuento' => $orden->motivo_descuento,
            'id_forma_de_pago' => $orden->id_forma_pago,
            'descuento_p'      => $total_descuento_p,
            'descuento_valor'  => $total_descuento,
            'recargo_p'        => $recargo_p,
            'recargo_valor'    => $recargo_valor,
            'total_valor'      => $total_valor,
            'cantidad'         => $cantidad,
            'valor'            => $suma_valor,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
            //'cobrar_pac_pct'   => $total_cobrar_pac_pct,
            'valor_con_oda'    => $total_valor_con_oda,
            'total_con_oda'    => $total_oda,
        ];

        $orden->update($input_ex);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "RECALCULAR COTIZACION",
            'dato_ant4'   => $manage,
        ]);
        return "ok";
    }

    public function cargar_detalle_orden_seguro($id_orden)
    {
        ini_set('max_execution_time',300);
        $orden      = Examen_Orden_Seguro::find($id_orden);
        $detalles   = DB::table('examen_detalle_seguro as ed')
        ->where('ed.id_examen_orden_seguro', $id_orden)
        ->join('examen_agrupador_sabana as es', 'es.id_examen', 'ed.id_examen')
        ->select('ed.*', 'e.descripcion', 'e.nombre as examen')
        ->join('examen as e', 'e.id', 'ed.id_examen')
        ->orderBy('es.id_examen_agrupador_labs')->get();

        $seguro = $orden->seguro;
        $agenda = Examen_Orden_Agenda::where('id_orden', $orden->id)->first();
        $rolUsuario = Auth::user()->id_tipo_usuario;
        return view('laboratorio.orden.detalle_orden_seguro', ['detalles' => $detalles,'orden' => $orden, 'seguro' => $seguro, 'agenda' => $agenda, 'rolUsuario' =>$rolUsuario]);
    }
    
    public function guarda_descuento_valor_oda(Request $request){

        $id_detalle = $request->id_detalle; 
        $valor_descuento = $request->valor_descuento;
        $valor_oda = $request->valor_oda;
        $detalle    = Examen_Detalle::find($id_detalle);

        if(!is_null($detalle)){
            $detalle->update([
                'valor_descuento' => $valor_descuento,
                'valor_con_oda'   => $valor_oda,
            ]);
        }

        return "ok";


    }
    public function guarda_descuento_valor_oda_seguro(Request $request)
    {
        $id_detalle = $request->id_detalle; 
        $valor_descuento = $request->valor_descuento;
        $valor_oda = $request->valor_oda;
        $detalle    = Examen_Detalle_Seguro::find($id_detalle);

        if(!is_null($detalle)){
            $detalle->update([
                'valor_descuento' => $valor_descuento,
                'valor_con_oda'   => $valor_oda,
            ]);
        }
        return "ok";
    }

    public function cotizador_seguro_delete($id_orden, $id_examen)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $orden = Examen_Orden_seguro::find($id_orden);
        $detalle = $orden->detalles->where('id_examen', $id_examen)->first();
        $cobrar_pac_pct = 0;
        $valor_con_oda = 0;
        $total_con_oda = 0;
        $tiene_domicilio = 0;
        $tiene_covid = 0;
        $valor_covid = 0;
        if (!is_null($detalle)) {
            $detalle->delete();
            $orden = Examen_Orden_seguro::find($id_orden);
            $total    = $orden->detalles->sum('valor');
            $cantidad = $orden->detalles->count();
            $total    = round($total, 4);
            if ($total < 0) {
                $total = 0;
            }
            $descuento_p = $orden->descuento_p;
            if ($orden->descuento_p == null) {
                $descuento_valor = 0;
                $descuento_p     = 0;
            }

            if ($orden->descuento_p > 100) {
                $descuento_valor = $total;
            } else {
                $descuento_valor = $orden->descuento_p * $total / 100;
                $descuento_valor = round($descuento_valor, 4);
            }

            $subtotal_pagar = $total - $descuento_valor;
            $recargo_p = $orden->recargo_p;
            $recargo_valor = $subtotal_pagar * $recargo_p / 100;
            $recargo_valor = round($recargo_valor, 4);
            $valor_total   = $subtotal_pagar + $recargo_valor;
            $valor_total   = round($valor_total, 4);
            $cobrar_pac_pct = $orden->cobrar_pac_pct;
            $valor_con_oda = 0;
            $total_con_oda = 0;
            if ($cobrar_pac_pct < 100) {
                $valor_con_oda = $orden->detalles->sum('valor_con_oda');
                $valor_con_oda = round($valor_con_oda, 4);
                $total_con_oda = $valor_con_oda - $descuento_valor;
            }

            //ACTUALIZAR ORDEN
            $input_ex = [
                'descuento_valor' => $descuento_valor,
                'recargo_valor'   => $recargo_valor,
                'total_valor'     => $valor_total,
                //'cantidad'        => $orden->cantidad - 1,
                'cantidad'        => $cantidad,
                'valor'           => $total,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'cobrar_pac_pct'  => $cobrar_pac_pct,
                'valor_con_oda'   => $valor_con_oda,
                'total_con_oda'   => $total_con_oda,
            ];

            $orden->update($input_ex);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ACTUALIZA COTIZACION",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant4'   => $id_examen,
            ]);

            $tiene_domicilio = 0;
            $dom             = $orden->detalles->where('id_examen', '1203')->first();
            if (!is_null($dom)) {
                $tiene_domicilio = 1;
            }

            $tiene_covid = 0;
            $valor_covid = 0;
            $covid       = $orden->detalles->where('id_examen', '1191')->first();
            if (is_null($covid)) {
                $covid = $orden->detalles->where('id_examen', '1195')->first();
                if (is_null($covid)) {
                    $covid = $orden->detalles->where('id_examen', '1196')->first();
                }
            }
            if (!is_null($covid)) {
                $tiene_covid = 1;
                $valor_covid = $covid->valor;
            }
        }

        $pvalor       = $orden->valor;
        $ptotal_valor = $orden->total_valor;
        if ($cobrar_pac_pct < 100) {
            $pvalor       = $valor_con_oda;
            $ptotal_valor = $total_con_oda;
        }
        $orden = Examen_Orden_Seguro::find($id_orden);
        return ['cantidad' => $orden->cantidad, 'valor' => $pvalor, 'descuento_valor' => $orden->descuento_valor, 'recargo_valor' => $orden->recargo_valor, 'total_valor' => $ptotal_valor, 'tiene_domicilio' => $tiene_domicilio, 'tiene_covid' => $tiene_covid, 'valor_covid' => $valor_covid];
    }

    public function cotizador_imprimir($id)
    {
        $orden      = Examen_Orden_Seguro::find($id);
        $detalles   = Examen_Detalle_Seguro::where('examen_detalle_seguro.id_examen_orden_seguro', $orden->id)
        ->join('examen_agrupador_sabana as es', 'es.id_examen', 'examen_detalle_seguro.id_examen')
        ->select('examen_detalle_seguro.*', 'e.descripcion', 'e.nombre')
        ->join('examen as e', 'e.id', 'examen_detalle_seguro.id_examen')
        ->orderBy('es.id_examen_agrupador_labs')
        ->get();

        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        $membresia_controller = new MembresiasLabsController;
        $res = $membresia_controller->buscar_membresia($orden->id_paciente);
        $view = View::make('laboratorio.orden.cotizacion_pdf', compact('orden', 'detalles', 'age', 'forma_pago', 'res'))->render();
        $pdf  = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('cotizador-' . $id . '.pdf');
    }

    public function pdf_cotizacion($id)
    {
        $orden      = Examen_Orden_Seguro::find($id);
        $detalles   = Examen_Detalle_Seguro::where('examen_detalle_seguro.id_examen_orden_seguro', $orden->id)
        ->join('examen_agrupador_sabana as es', 'es.id_examen', 'examen_detalle_seguro.id_examen')
        ->select('examen_detalle_seguro.*', 'e.descripcion', 'e.nombre')
        ->join('examen as e', 'e.id', 'examen_detalle_seguro.id_examen')
        ->orderBy('es.id_examen_agrupador_labs')
        ->get();
        $forma_pago = Examen_Detalle_Forma_Pago::where('id_examen_orden', $id)->get();
        $age = Carbon::createFromDate(substr($orden->paciente->fecha_nacimiento, 0, 4), substr($orden->paciente->fecha_nacimiento, 5, 2), substr($orden->paciente->fecha_nacimiento, 8, 2))->age;
        $empresa = Empresa::find('0993075000001');
        
        if(!is_null($orden)){
            $paciente = Paciente::find($orden->id_paciente);
            $grupo_familiar = Labs_Grupo_Familiar::find($orden->id_paciente);
            if (!is_null($grupo_familiar)) {
                //BUSCA SI EL CORREO EXISTE EN OTRO USUARIO
                $usuario_mail = User::find($grupo_familiar->id_usuario);
            } else {
                //BUSCA SI EXISTE EL CORREO
                $usuario_mail = User::where('id', $paciente->id_usuario)->first();
            }
        }
        $forma_pago = [];
        $vistaurl = "laboratorio.compnotributario.pdf_cotizacion";
        $view     = View::make($vistaurl, compact('orden','empresa', 'detalles', 'age', 'forma_pago', 'usuario_mail'))->render();
        $pdf      = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $customPaper = array(0,0,283.80,700.00);
        $pdf->setPaper($customPaper, 'portrait');
        return $pdf->stream('Pdf Cotizacion' . '.pdf');
    }
    
    public function emitir($id_orden){
        $orden = Examen_Orden_Seguro::find($id_orden);
        $arr = [
            'estado' => '1',
        ];

        $orden->update($arr);

        $this->copia_formas_pago($orden->id);

        return ['status' => 'success', 'msj' => 'Orden emitida correctamente'];    
    }

    public function copia_formas_pago($id_orden){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden = Examen_Orden_Seguro::find($id_orden);
        $orden_original =   $orden->orden_original;
        $detalles = $orden_original->detalle_forma_pago->where('estado','1');
        if($orden_original->total_con_oda > 0){
            if($orden->cobrar_pac_pct < 100 ){
                $pct = $orden->total_con_oda / $orden_original->total_con_oda;    
            }else{
                $pct = $orden->total_valor / $orden_original->total_con_oda;
            }
            
            $pct = round($pct,4);
            foreach($detalles as $detalle){

                $detalle_seg = Examen_Forma_Pago_Seguro::where('estado','1')
                    ->where('id_forma_pago',$detalle->id_tipo_pago)
                    ->where('id_examen_orden_seguro',$orden->id)
                    ->first();
                if(is_null($detalle_seg)){
                    $valor = $detalle->valor * $pct;
                    $valor = round($valor, 2);
                    $array = [
                        'id_examen_orden_seguro' => $orden->id,
                        'id_forma_pago' => $detalle->id_tipo_pago,
                        'valor' => $valor,
                        'estado' => '1', 
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_banco' => $detalle->banco,
                        'id_tipo_tarjeta' => $detalle->tipo_tarjeta,
                        'numero' => $detalle->numero,
                        'cuenta' => $detalle->cuenta,
                        'fecha' => $detalle->fecha,
                    ];
                    Examen_Forma_Pago_Seguro::create($array);
                }    

            }
        }else{
            return ['msn' => 'error'];
        }
        
          
    }

    public function anular($id_orden){
        $orden = Examen_Orden_Seguro::find($id_orden);
        $arr = [
            'estado' => '0',
        ];
        $orden->update($arr);
        return ['status' => 'success', 'msj' => 'Orden anulada correctamente'];  
    }

    public function seguros_privados_cuadrar($id){

        $orden = Examen_Orden_Seguro::find($id);
        $detalles = $orden->detalle_forma_pago;
        $total_valor = 0;
        foreach($detalles as $detalle){
            $total_valor += $detalle->valor;

        }
        if($orden->cobrar_pac_pct < 100){

            $total2 = $orden->total_con_oda; 

        }else{
            
            $total2 = $orden->total_valor;

        }
        $total2 = round($total2,2);
        $total_valor = round($total_valor,2);
        //dd($total2, $total_valor);
        if($total2 == $total_valor){
            return "ok";
        }
        return "error";

    }
    public function actualizarSinCobertura(Request $request, $id)
    {
        $detalle = Examen_Detalle::find($id); // Ajusta el modelo si es necesario
        $examen_orden = $detalle->examen_orden; 
        if ($detalle) {
            if( $request->input('sin_cobertura') == '1'){
                $detalle->sin_cobertura = $request->input('sin_cobertura');
                $detalle->cobrar_pac_pct = '100';
                //$detalle->valor_con_oda = $detalle->valor;
            }else{
                $detalle->cobrar_pac_pct = $examen_orden->cobrar_pac_pct;
                $detalle->valor_con_oda =  round($detalle->valor * $examen_orden->cobrar_pac_pct / 100,4);
                $detalle->sin_cobertura = $request->input('sin_cobertura');
            }

            $detalle->save();

            return response()->json(['success' => true, 'message' => 'Sin cobertura actualizado correctamente.']);
        }

        return response()->json(['success' => false, 'message' => 'Detalle no encontrado.'], 404);
    }
    public function nuevo_edit($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $empresas = DB::table('empresa as e')->where('e.estado', '1')->join('convenio as c', 'c.id_empresa', 'e.id')->select('e.id', 'e.nombrecomercial')->groupBy('e.id', 'e.nombrecomercial')->get();
        //$usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->where('training', '<>', '1')->orderby('apellido1')->get();
        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('training', '0')->where('uso_sistema', '0')->orderBy('apellido1')->get(); //3=DOCTORES;
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get(); se muestran todos
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $orden       = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')->first();
        $detalles    = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $seguro      = Seguro::find($orden->id_seguro);
        $seguros     = Seguro::where('inactivo', '1')->get();
        $protocolos  = Protocolo::where('estado', '1')->get();
        $convenios   = DB::table('convenio as c')->join('seguros as s', 's.id', 'c.id_seguro')->join('empresa as e', 'e.id', 'c.id_empresa')->where('s.tipo', '0')->select('c.*', 's.nombre', 'e.nombrecomercial')->orderby('s.nombre')->get();
        $convenio    = Convenio::where('id_seguro', $orden->id_seguro)->where('id_empresa', $orden->id_empresa)->first();

        if (!is_null($orden)) {
            return view('laboratorio/orden/edit_nuevo_jz', ['orden' => $orden, 'usuarios' => $usuarios, 'examenes' => $examenes, 'agrupadores' => $agrupadores, 'detalles' => $detalles, 'seguro' => $seguro, 'protocolos' => $protocolos, 'empresa' => $empresas, 'seguros' => $seguros, 'dir' => 'rec', 'convenios' => $convenios, 'convenio' => $convenio]);
        }
    }

}
