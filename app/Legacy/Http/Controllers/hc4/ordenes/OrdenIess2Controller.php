<?php

namespace Sis_medico\Http\Controllers\hc4\ordenes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Convenio;
use Sis_medico\Empresa;
use Sis_medico\Examen;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Protocolo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_usuario;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Protocolo;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Examen_Nivel;
use Sis_medico\Agenda;

class OrdenIess2Controller extends Controller
{

    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion)
    {
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
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

    
    
    ////CREA ORDENES PUBLICAS COMO EN HOSPITAL

    public function crear($id_paciente)
    {

        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $id_doctor    = Auth::user()->id;
        $paciente     = Paciente::find($id_paciente);
       
        $id_seguro = 2;
        $empresa  = Empresa::where('prioridad', 1)->get()->first();
        $convenio = Convenio::where('id_empresa', $empresa->id)->where('id_seguro', $id_seguro)->get()->first();
        $nivel    = $convenio->id_nivel;

        $input_ex = [
            'id_paciente'     => $id_paciente,
            'anio'            => substr(date('Y-m-d'), 0, 4),
            'mes'             => substr(date('Y-m-d'), 5, 2),
            //'id_protocolo'    => $id_protocolo,
            'id_seguro'       => $id_seguro,
            'id_nivel'        => $nivel,
            'id_empresa'      => $empresa->id,

            'est_amb_hos'     => '0',
            'id_doctor_ieced' => $id_doctor,

            'cantidad'        => '0',
            'estado'          => '-1',
            'valor'           => '0',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
            'fecha_orden'     => date('Y-m-d H:i:s'),

        ];
        $id_examen_orden = Examen_Orden::insertGetId($input_ex);

        $ordenes = Examen_Orden::where('id_paciente', $id_paciente)
            ->where('estado', '<>', '0')
            ->OrderBy('created_at', 'desc')
            ->get();

        Log_usuario::create([
            'id_usuario'  => $id_doctor,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "GENERA ORDEN DOCTOR",
            'dato_ant1'   => 'orden: ' . $id_examen_orden . '- paciente: ' . $paciente->id,
            'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2),
            //'dato_ant2'   => $etxt,

        ]);

        return view('hc4/ordenes/orden_labs/index2', ['ordenes' => $ordenes, 'paciente' => $paciente, 'editar' => $id_examen_orden]);
    }

    public function edit($id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $protocolos  = Protocolo::where('estado', '1')->get();
        $agrupadores = Examen_Agrupador::where('estado', '1')->get();
        $examenes    = Examen::where('publico_privado', '0')->orderBy('id_agrupador')->get();
        $orden       = Examen_Orden::find($id);
        //dd($id,$orden);

        $empresa  = Empresa::where('prioridad', 1)->get()->first();
        $usuarios = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        //$examenes = Examen::where('estado','1')->orderBy('id_agrupador')->get();
        $examenes      = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->orderBy('a10.id_agrupador')->get();
        $hematologia   = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','1')->orderBy('a10.id_agrupador')->get();
        $inmunologia   = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','8')->orderBy('a10.id_agrupador')->get();
        $citologia     = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','14')->orderBy('a10.id_agrupador')->get();
        $cardiacos     = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','15')->orderBy('a10.id_agrupador')->get();
        $hormonas      = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','16')->orderBy('a10.id_agrupador')->get();
        $heces         = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','4')->orderBy('a10.id_agrupador')->get();
        $tumorales     = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','12')->orderBy('a10.id_agrupador')->get();
        $quimica       = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','2')->orderBy('a10.id_agrupador')->get();
        $farmacos      = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','20')->orderBy('a10.id_agrupador')->get();
        $serologia     = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','5')->orderBy('a10.id_agrupador')->get();
        $biologia      = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','6')->orderBy('a10.id_agrupador')->get();
        $coagulacion   = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','13')->orderBy('a10.id_agrupador')->get();
        $orina         = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','3')->orderBy('a10.id_agrupador')->get();
        $inmunosupresor= Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','17')->orderBy('a10.id_agrupador')->get();
        $gases         = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','18')->orderBy('a10.id_agrupador')->get();
        $trasfusional  = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','19')->orderBy('a10.id_agrupador')->get();
        $vih           = Examen::where('examen.estado', '1')->where('examen.publico_privado','0')->join('examen_agrupador_010 as a10','a10.id_examen','examen.id')->where('a10.estado','1')->select('examen.*','a10.id_agrupador')->where('a10.id_agrupador','24')->orderBy('a10.id_agrupador')->get();
        
        
        $agrupadores   = Examen_Agrupador::where('estado', '1')->get();
        $orden         = DB::table('examen_orden as eo')->where('eo.id', $id)->join('paciente as p', 'p.id', 'eo.id_paciente')->leftjoin('users as d', 'd.id', 'eo.id_doctor_ieced')->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1', 'p.fecha_nacimiento as pfecha_nacimiento','p.sexo as psexo', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'd.apellido2 as dapellido2')->first();
        $detalles      = Examen_Detalle::where('id_examen_orden', $id)->where('estado', '1')->get();
        $seleccionados = [];

        foreach ($detalles as $detalle) {

            $seleccionados[] = $detalle->id_examen;
        }

        $seguro     = Seguro::find($orden->id_seguro)->where('inactivo', '1');
        $protocolos = Protocolo::where('estado', '1')->get();

        $empresa = Empresa::find($orden->id_empresa);

        //dd($orden);
        $arreglo    = [];
        $arreglo1   = [];
        $nro        = 0;
        $agrupa_ant = 0;
        foreach ($examenes as $examen) {

            //dd($examen);
            if ($agrupa_ant != $examen->id_agrupador) {
                $nro        = 0;
                $agrupa_ant = $examen->id_agrupador;
                $arreglo1   = [];
            }
            $arreglo1[$nro] = $examen->id;

            $arreglo[$examen->id_agrupador] = $arreglo1;
            $nro++;
        }

        //return view('hc4.ordenes.orden_labs.editar_orden', ['id_editar' => $id, 'protocolos' => $protocolos, 'agrupadores' => $agrupadores, 'examenes' => $examenes, 'orden' => $orden]);
        return view('hc4.ordenes.orden_labs.editar_orden_publica', [
            'orden' => $orden, 
            'usuarios'=> $usuarios,
            'examenes'=> $examenes,
            'agrupadores'=> $agrupadores,
            'detalles'=> $detalles,
            'empresa'=> $empresa,
            'seguro'=> $seguro,
            'protocolos'=> $protocolos, 
            'arreglo'=> $arreglo,
            'seleccionados'=> $seleccionados,
            'hematologia'=> $hematologia,
            'quimica'=> $quimica,
            'inmunologia'=> $inmunologia,
            'heces'=> $heces,
            'tumorales'=> $tumorales,
            'citologia'=> $citologia,
            'cardiacos'=> $cardiacos,
            'hormonas'=> $hormonas,
            'farmacos'=> $farmacos,
            'serologia'=> $serologia,
            'biologia'=> $biologia,
            'coagulacion'=> $coagulacion,
            'orina'=> $orina,
            'inmunosupresor'=> $inmunosupresor,
            'gases'=> $gases,
            'trasfusional'=> $trasfusional,  
            'vih' => $vih  
        ]);
      
    }

    public function crear_examen_publico($id_orden, $id_examen){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $empresa  = Empresa::where('prioridad',1)->first();
        $seguro   = Seguro::find(2);

        $convenio = Convenio::where('id_seguro',$seguro->id)->where('id_empresa',$empresa->id)->first();

        $orden = Examen_Orden::find($id_orden);

        $detalles = $orden->detalles;

        if($detalles->where('id_examen',$id_examen)->first() == null){

            $examen = Examen::find($id_examen);       
            $valor  = $examen->valor;
            
            if (!is_null($convenio)) {
                $nivel    = $convenio->id_nivel;
                $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
                if (!is_null($ex_nivel)) {
                    $valor = $ex_nivel->valor1;
                }
            }

            $input_det = [
                'id_examen_orden' => $id_orden,
                'id_examen'       => $examen->id,
                'valor'           => $valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'p_descuento'     => 0,
                'valor_descuento' => 0,
            ];

            Examen_detalle::create($input_det);

            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "AGREGA EXAMEN ORDEN",
                'dato_ant1'   => $id_orden,
                'dato1'       => $orden->paciente->apellido1.' '.$orden->paciente->apellido2.' '.$orden->paciente->nombre1.' '.$orden->paciente->nombre2,
                'dato_ant4'   => $examen->nombre,
            ]);


        }

        $this->recalcular($id_orden);

        return "ok";


    }

    public function eliminar_examen_publico($id_orden, $id_examen){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $empresa  = Empresa::where('prioridad',1)->first();
        $seguro   = Seguro::find(2);

        $convenio = Convenio::where('id_seguro',$seguro->id)->where('id_empresa',$empresa->id)->first();

        $orden    = Examen_Orden::find($id_orden);

        $detalles = $orden->detalles;

        $detalle  = $detalles->where('id_examen',$id_examen)->first(); //dd($detalle);

        if( $detalle != null){

            $examen = Examen::find($id_examen);  
            
            $detalle->delete();
            
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "ELIMINA EXAMEN ORDEN",
                'dato_ant1'   => $id_orden,
                'dato1'       => $orden->paciente->apellido1.' '.$orden->paciente->apellido2.' '.$orden->paciente->nombre1.' '.$orden->paciente->nombre2,
                'dato_ant4'   => $examen->nombre,
            ]);


        }

        $this->recalcular($id_orden);

        return "ok";

    }

    public function recalcular($id_orden)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
       
        $orden = Examen_Orden::find($id_orden);
        
        //RECALCULAR
        $total    = 0;
        $cantidad = 0;
        
        $detalles = $orden->detalles;
       
        $total           = $orden->detalles->sum('valor');
        $cantidad        = $orden->detalles->count();
        $total           = round($total, 2);
        $descuento_total = $orden->detalles->sum('valor_descuento');
        
        $input_ex = [
           
            'descuento_p'      => 0,
            'descuento_valor'  => $descuento_total,
            'total_valor'      => $total,
            'cantidad'         => $cantidad,
            'valor'            => $total,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
        ];

        $orden->update($input_ex);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "RECALCULAR ORDEN PUBLICA",
        ]);

        return "ok";
    }

    public function crear_agenda($id_agenda)
    {

        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $id_doctor    = Auth::user()->id;
        $agenda       = Agenda::find($id_agenda);//dd($agenda);
       
        if(!is_null($agenda)){
            if($agenda->seguro->tipo == '0'){

                $examen_orden = Examen_Orden::where('id_agenda',$agenda->id)->where('estado','!=','0')->first();//dd($examen_orden);
                
                if(is_null($examen_orden)){
                    $id_seguro = $agenda->id_seguro;
                    $paciente = $agenda->paciente;

                    $empresa  = $agenda->empresa;
                    $convenio = Convenio::where('id_empresa', $empresa->id)->where('id_seguro', $id_seguro)->get()->first();
                    $nivel    = $convenio->id_nivel;

                    $input_ex = [
                        'id_paciente'     => $agenda->paciente->id,
                        'anio'            => substr(date('Y-m-d'), 0, 4),
                        'mes'             => substr(date('Y-m-d'), 5, 2),
                        
                        'id_seguro'       => $id_seguro,
                        'id_nivel'        => $nivel,
                        'id_empresa'      => $empresa->id,

                        'est_amb_hos'     => '0',
                        'id_doctor_ieced' => $id_doctor,
                        'id_agenda'       => $agenda->id,

                        'cantidad'        => '0',
                        'estado'          => '-1',
                        'valor'           => '0',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_doctor,
                        'id_usuariomod'   => $id_doctor,
                        'fecha_orden'     => date('Y-m-d H:i:s'),

                    ];
                    $id_examen_orden = Examen_Orden::insertGetId($input_ex);

                    Log_usuario::create([
                        'id_usuario'  => $id_doctor,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "GENERA ORDEN PUBLICA DOCTOR",
                        'dato_ant1'   => 'orden: ' . $id_examen_orden . '- paciente: ' . $paciente->id,
                        'dato1'       => strtoupper($paciente->nombre1 . " " . $paciente->nombre2 . " " . $paciente->apellido1 . " " . $paciente->apellido2),
                        //'dato_ant2'   => $etxt,

                    ]);

                    return redirect()->route('laboratorio.ordenIess.edit',['id' => $id_examen_orden]);
                } else{
                    return redirect()->route('laboratorio.ordenIess.edit',['id' => $examen_orden->id]);  
                }   
            }else{
                $mensaje = "SOLO PARA SEGUROS PUBLICOS";
            }
            
        }else{
            $mensaje = "NO TIENE UNA AGENDA ASOCIADA";
        } 
        
        return $mensaje;

        //return view('hc4/ordenes/orden_labs/index2', ['ordenes' => $ordenes, 'paciente' => $paciente, 'editar' => $id_examen_orden]);
    }


}
