<?php

namespace Sis_medico\Http\Controllers;

use Excel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Response;
use Sis_medico\AfFacturaActivoCabecera;
use Sis_medico\ApPlantillaItem;
use Sis_medico\ApProcedimiento;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ap_Procedimiento;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_ventas;
use Sis_medico\Examen;
use Sis_medico\Examenes;
use Sis_medico\Examen_Agrupador_Sabana;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Resultado;
use Sis_medico\Http\Controllers\Inventario\InvProcesosController;
use Sis_medico\Insumo_Plantilla_Control;
use Sis_medico\Insumo_Plantilla_Item_Control;
use Sis_medico\InvCosto;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\Log_usuario;
use Sis_medico\Medicina;
use Sis_medico\Medicina_Principio;
use Sis_medico\Paciente;
use Sis_medico\PrecioProducto;
use Sis_medico\Principio_Activo;
use Sis_medico\Producto;
use Sis_medico\SCI_Pacientes;
use Sis_medico\User;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_productos_paquete;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Empresa;
use Sis_medico\Insumo_Plantilla_Tipo;
use Sis_medico\LogConfig;
use Sis_medico\Movimiento;
use Sis_medico\Pedido;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Detalle;
use Sis_medico\Ct_Rh_Acumulados;
use Sis_medico\Ct_Nomina;
use Sis_medico\Examen_Comision;
use Sis_medico\InvKardexNuevo;
use Sis_medico\ApProcedimientoNivel;
use Sis_medico\Procedimiento;


class ImportarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }
    public function importar()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        $contador = 0;

        Excel::filter('chunk')->load('cie10.xlsx')->chunk(250, function ($reader) use ($contador) {

            foreach ($reader as $book) {

                //dd($book);
                $cie         = trim($book['codigo_cie_10']);
                $descripcion = $book['descripcion'];
                $long        = strlen($cie);

                //dd($long);
                //dd($descripcion);
                if ($long == '3') {
                    $cie10_3 = Cie_10_3::find($cie);
                    if (!is_null($cie10_3)) {

                        $input = [
                            'id'              => $cie,
                            'descripcion'     => $descripcion,
                            'id_usuariomod'   => '9999999999',
                            'ip_modificacion' => 'CARGA_MASIVA',
                        ];
                        $cie10_3->update($input);
                        $contador = $contador + 1;
                    } else {

                        $input = [
                            'id'              => $cie,
                            'descripcion'     => $descripcion,
                            'id_usuariocrea'  => '9999999999',
                            'id_usuariomod'   => '9999999999',
                            'ip_creacion'     => 'CARGA_MASIVA',
                            'ip_modificacion' => 'CARGA_MASIVA',
                        ];
                        Cie_10_3::create($input);
                        $contador = $contador + 1;
                    }
                } else if ($long > 3) {

                    $cie10_4 = Cie_10_4::find($cie);
                    if (!is_null($cie10_4)) {

                        $input = [
                            'id'              => $cie,
                            'id_cie_10_3'     => substr($cie, 0, 3),
                            'descripcion'     => $descripcion,
                            'id_usuariomod'   => '9999999999',
                            'ip_modificacion' => 'CARGA_MASIVA',
                        ];
                        $cie10_4->update($input);
                        $contador = $contador + 1;
                    } else {

                        $input = [
                            'id'              => $cie,
                            'id_cie_10_3'     => substr($cie, 0, 3),
                            'descripcion'     => $descripcion,
                            'id_usuariocrea'  => '9999999999',
                            'id_usuariomod'   => '9999999999',
                            'ip_creacion'     => 'CARGA_MASIVA',
                            'ip_modificacion' => 'CARGA_MASIVA',
                        ];
                        Cie_10_4::create($input);
                        $contador = $contador + 1;
                    }
                }
            }
        });

        return $contador;
    }

    public function importar_generico()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('genericos.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {

                foreach ($book as $b) {

                    //dd($book);
                    $generico = strtoupper(trim($b['gen']));
                    //dd($generico);
                    $genericos = Principio_Activo::where('nombre', $generico)->first();
                    //dd($genericos);
                    if (is_null($genericos)) {
                        //dd($generico);
                        $input = [
                            'nombre'          => $generico,
                            'descripcion'     => $generico,
                            'id_usuariomod'   => '9999999999',
                            'ip_modificacion' => 'CARGA_MASIVA',
                            'id_usuariocrea'  => '9999999999',
                            'ip_creacion'     => 'CARGA_MASIVA',
                        ];
                        Principio_Activo::create($input);
                    }
                }
            }
        });

        return "ok";
    }

    public function importar_examenes()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('examenes1.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {

                //dd($book->valor);

                $input = [
                    'nombre' => $book->examen,
                    'valor'  => round($book->valor, 2),

                ];
                //dd($input);
                Examenes::create($input);
            }
        });

        return "ok";
    }

    public function sci_importar()
    {

        ini_set('max_execution_time', 10000000000);

        //return "hola";
        $sci          = SCI_Pacientes::limit(25000)->get();
        $actualizados = 0;
        $noexisten    = 0;
        $creados      = 0;
        //dd($sci);
        foreach ($sci as $value) {
            //dd ($value);

            $cedula = str_pad($value->numficha, 10, "0", STR_PAD_LEFT);
            //dd($cedula);
            $paciente = Paciente::find($cedula);
            if (!is_null($paciente)) {

                if ($value->histclin != null) {
                    $actualizados = $actualizados + 1;
                    $input        = [
                        'historia_clinica' => $value->histclin,
                        'id_usuariomod'    => '9999999999',
                        'ip_modificacion'  => 'AUTOM',
                    ];
                    //$paciente->update($input);
                }
            } else {
                $noexisten = $noexisten + 1;
                //return $value;
                $cedula = $value->numficha;
                $len    = strlen($cedula);
                if ($value->sexo == 'Masculino') {
                    $sexo = '1';
                } elseif ($value->sexo == 'Femenino') {
                    $sexo = '2';
                } else {
                    $sexo = null;
                }
                if ($value->estcivil == 'Casado(a)') {
                    $estacivil = 2;
                } elseif ($value->estcivil == 'Divorciado(a)') {
                    $estacivil = 4;
                } elseif ($value->estcivil == 'Soltero(a)') {
                    $estacivil = 1;
                } elseif ($value->estcivil == 'Unión Libre') {
                    $estacivil = 5;
                } elseif ($value->estcivil == 'Viudo(a)') {
                    $estacivil = 3;
                } else {
                    $estacivil = null;
                }
                if ($value->teldom != null) {
                    $telefono1 = $value->teldom;
                } else {
                    $telefono1 = '1';
                }

                if ($len == '10' || $len == '9') {
                    //return $len;
                    $cedula    = str_pad($value->numficha, 10, "0", STR_PAD_LEFT);
                    $creados   = $creados + 1;
                    $nombres   = explode(' ', $value->nombrepac);
                    $apellidos = explode(' ', $value->apellidopac);
                    $nombre2   = null;
                    if (isset($nombres[1])) {
                        $nombre2 = $nombres[1];
                    }
                    if (isset($apellidos[1])) {
                        $apellido2 = $apellidos[1];
                    }
                    $input_pac = [
                        'id'                 => str_pad($value->numficha, 10, "0", STR_PAD_LEFT),
                        'nombre1'            => $nombres[0],
                        'nombre2'            => $nombre2,
                        'apellido1'          => $apellidos[0],
                        'apellido2'          => $apellido2,
                        'tipo_documento'     => '1',
                        'id_pais'            => '1',
                        'ciudad'             => $value->numciud,
                        'direccion'          => $value->direccion,
                        'telefono1'          => $telefono1,
                        'telefono2'          => $value->telcel,
                        'id_seguro'          => '1',
                        'imagen_url'         => ' ',
                        'fecha_nacimiento'   => $value->fechnac,
                        'sexo'               => $sexo,
                        'estadocivil'        => $estacivil,
                        'gruposanguineo'     => $value->tipsang,
                        'id_usuario'         => str_pad($value->numficha, 10, "0", STR_PAD_LEFT),
                        'menoredad'          => 0,
                        'alergias'           => null,
                        'cedulafamiliar'     => str_pad($value->numficha, 10, "0", STR_PAD_LEFT),
                        'nombre1familiar'    => $nombres[0],
                        'nombre2familiar'    => $nombre2,
                        'apellido1familiar'  => $apellidos[0],
                        'apellido2familiar'  => $apellido2,
                        'parentesco'         => 'Principal',
                        'parentescofamiliar' => 'Principal',
                        'referido'           => $value->referencia,
                        'vacuna'             => $value->vacunas,
                        'telefono3'          => $value->teltrab,
                        'id_usuariocrea'     => '9999999999',
                        'id_usuariomod'      => '9999999999',
                        'ip_creacion'        => 'AUTOM1',
                        'ip_modificacion'    => 'AUTOM1',
                        'antecedentes_pat'   => $value->antpatpers,
                        'antecedentes_fam'   => $value->antpatfam,
                        'antecedentes_quir'  => $value->antquir,
                        'alcohol'            => $value->habitos,
                        'historia_clinica'   => $value->histclin,

                    ];
                    $uemail = User::where('email', $value->email)->first();
                    if (is_null($uemail)) {
                        $email = $value->email;
                    } else {
                        $email = $cedula . '@mail.com';
                    }

                    $input_us = [
                        'id'                => str_pad($value->numficha, 10, "0", STR_PAD_LEFT),
                        'nombre1'           => $nombres[0],
                        'nombre2'           => $nombre2,
                        'apellido1'         => $apellidos[0],
                        'apellido2'         => $apellido2,
                        'tipo_documento'    => '1',
                        'id_pais'           => '1',
                        'ciudad'            => $value->numciud,
                        'direccion'         => $value->direccion,
                        'email'             => $email,
                        'telefono1'         => $telefono1,
                        'telefono2'         => $value->telcel,
                        'estado'            => '1',
                        'fecha_nacimiento'  => $value->fechnac,
                        'password'          => bcrypt('123456'),
                        'imagen_url'        => ' ',
                        'id_tipo_usuario'   => '2',
                        'max_procedimiento' => '0',
                        'max_consulta'      => '0',
                        'id_usuariocrea'    => '9999999999',
                        'id_usuariomod'     => '9999999999',
                        'ip_creacion'       => 'AUTOM1',
                        'ip_modificacion'   => 'AUTOM1',

                    ];

                    $user = User::find($cedula);

                    if (is_null($user)) {
                        User::create($input_us);
                    }

                    Paciente::create($input_pac);
                }
            }
        }

        return [$actualizados, $noexisten, $creados];
    }

    public function importar_sabana()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('SABANA.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {

                foreach ($book as $value) {

                    if ($value->examen != null) {
                        $examen = Examen::where('nombre', $value->examen)->first();
                        if (is_null($examen)) {

                            //1. crea examen
                            $input = [
                                'nombre'          => $value->examen,
                                'descripcion'     => $value->examen,
                                'valor'           => $value->particular,
                                'id_agrupador'    => '7',
                                'tarifario'       => substr($value->examen, 0, 4),
                                'publico_privado' => '1',
                                'id_usuariocrea'  => '0922290697',
                                'id_usuariomod'   => '0922290697',
                                'ip_creacion'     => 'CARG_SABANA',
                                'ip_modificacion' => 'CARG_SABANA',
                            ];
                            //dd($input);

                            $id_examen = Examen::insertGetId($input);
                            //2. crea valor humana
                            if ($id_examen != null && $value->humana != null) {
                                $input_ex_ni = [
                                    'nivel'           => '4',
                                    'id_examen'       => $id_examen,
                                    'valor1'          => $value->humana,
                                    'id_usuariocrea'  => '0922290697',
                                    'id_usuariomod'   => '0922290697',
                                    'ip_creacion'     => 'CARG_SABANA',
                                    'ip_modificacion' => 'CARG_SABANA',

                                ];
                                Examen_Nivel::create($input_ex_ni);
                            }
                            //3. crea agrupador sabana
                            if ($id_examen != null) {

                                $input_sabana = [
                                    'id_examen_agrupador_labs' => $value->grupo,
                                    'id_examen'                => $id_examen,
                                    'nro_orden'                => $value->orden,
                                    'id_usuariocrea'           => '0922290697',
                                    'id_usuariomod'            => '0922290697',
                                    'ip_creacion'              => 'CARG_SABANA',
                                    'ip_modificacion'          => 'CARG_SABANA',
                                ];
                                Examen_Agrupador_Sabana::create($input_sabana);
                            }
                        }
                    }
                }
            }
        });

        return "ok";
    }

    public function importar_examenes_abril()
    {
        //dd('Hola');
        Excel::filter('chunk')->load('tarifario.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $value) {
                //dd($value->valores);

                $examen = Examen_Nivel::where('id_examen', $value->id)
                    ->where('nivel', '12')
                    ->first();
                //dd($examen);
                if (is_null($examen)) {
                    Examen_Nivel::create([
                        'nivel'           => '12',
                        'id_examen'       => $value->id,
                        'valor1'          => $value->valores,
                        'estado'          => 1,
                        'id_usuariocrea'  => '0957258056',
                        'id_usuariomod'   => '0957258056',
                        'ip_creacion'     => 'actuths2120',
                        'ip_modificacion' => 'actuths2120',
                    ]);
                } else {
                    $updates = [
                        'valor1'          => $value->valores,
                        'id_usuariomod'   => '0957258056',
                        'ip_creacion'     => 'actuths2120',
                        'ip_modificacion' => 'actuths2120',
                    ];
                    $examen->update($updates);
                }

                $examen13 = Examen_Nivel::where('id_examen', $value->id)
                    ->where('nivel', '13')
                    ->first();
                //dd($examen);
                //aquiesta
                if (is_null($examen13)) {
                    Examen_Nivel::create([
                        'nivel'           => '13',
                        'id_examen'       => $value->id,
                        'valor1'          => $value->valores,
                        'estado'          => 1,
                        'id_usuariocrea'  => '0957258056',
                        'id_usuariomod'   => '0957258056',
                        'ip_creacion'     => 'actuths2120',
                        'ip_modificacion' => 'actuths2120',
                    ]);
                } else {
                    $updates13 = [
                        'valor1'          => $value->valores,
                        'id_usuariomod'   => '0957258056',
                        'ip_creacion'     => 'actuths2120',
                        'ip_modificacion' => 'actuths2120',
                    ];
                    $examen13->update($updates13);
                }
            }
        });

        return "ok";
    }

    public function importar_referido()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        $oks = 0;
        $nof = 0;

        Excel::filter('chunk')->load('REFERIDO.xlsx')->chunk(250, function ($reader) use ($oks, $nof) {

            foreach ($reader as $book) {

                $examen = Examen::find($book->id);
                if (!is_null($examen)) {
                    $oks++;
                    $input = [
                        'valor_interlab' => $book->interlab,
                        'valor_referido' => $book->referido,
                    ];
                    $examen->update($input);
                } else {
                    $nof++;
                }
            }

            Log_usuario::create([
                'id_usuario'  => '0922290697',
                'ip_usuario'  => 'SISTEMA',
                'descripcion' => 'CARGA REFERIDOS',
                'dato_ant1'   => 'oks: ' . $oks . ' -nofound: ' . $nof,
            ]);
        });

        return "listo";
    }

    /*//VALORES CON LABS Y HUMANA
    public function importar_valores2019()
    {

    if($this->rol()){
    return response()->view('errors.404');
    }

    ini_set('max_execution_time', 200);

    Excel::filter('chunk')->load('val2019_priv.xlsx')->chunk(250, function($reader) {

    $humana = '0'; $total = '0';
    $salud5 = '0'; $salud6 = '0'; $salud7 = '0';
    $ehg = '0';
    $vumi = '0';

    foreach ($reader as $book) {
    $examen = Examen::find($book->id);

    if(!is_null($examen)){

    /*$input = [
    'valor' => round($book->labs,2) ,
    'codigo_inter' => $book->codigo,
    'ip_modificacion' => 'ACT2019',
    ];

    $examen->update($input);
    $cactualizados ++; *
    //BLOQUE HUMANA
    if($book->humana_nuevo!='0'){
    $examen_humana = Examen_Nivel::where('nivel','4')->where('id_examen',$book->id)->first();
    //dd($book,"EXISTE NIVEL");
    if(is_null($examen_humana)){
    $input_hum = [
    'nivel' => '4',
    'id_examen' => $book->id,
    'valor1' => round( $book->humana_nuevo,2),
    'id_usuariocrea' => '0922290697',
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'HUMANA_CREA',
    'ip_modificacion' => '::1'
    ];
    Examen_Nivel::create($input_hum);
    $humana ++;

    }else{
    $input_hum = [
    'valor1' => round( $book->humana_nuevo,2),
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'HUMANA_MODIFICA',
    'ip_modificacion' => '::1'
    ];
    $examen_humana->update($input_hum);
    $humana ++;
    }

    }
    //BLOQUE SALUD nivel 5
    if($book->salud_nuevo!='0'){
    $examen_salud5 = Examen_Nivel::where('nivel','5')->where('id_examen',$book->id)->first();

    if(is_null($examen_salud5)){
    $input_hum = [
    'nivel' => '5',
    'id_examen' => $book->id,
    'valor1' => round( $book->salud_nuevo,2),
    'id_usuariocrea' => '0922290697',
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'SALUD5_CREA',
    'ip_modificacion' => '::1'
    ];
    Examen_Nivel::create($input_hum);
    $salud5 ++;

    }else{
    $input_hum = [
    'valor1' => round( $book->salud_nuevo,2),
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'SALUD5_MODIFICA',
    'ip_modificacion' => '::1'
    ];
    $examen_salud5->update($input_hum);
    $salud5 ++;

    }
    //nivel 6
    $examen_salud6 = Examen_Nivel::where('nivel','6')->where('id_examen',$book->id)->first();

    if(is_null($examen_salud6)){
    $input_hum = [
    'nivel' => '6',
    'id_examen' => $book->id,
    'valor1' => round( $book->salud_nuevo,2),
    'id_usuariocrea' => '0922290697',
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'SALUD6_CREA',
    'ip_modificacion' => '::1'
    ];
    Examen_Nivel::create($input_hum);
    $salud6 ++;

    }else{
    $input_hum = [
    'valor1' => round( $book->salud_nuevo,2),
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'SALUD6_MODIFICA',
    'ip_modificacion' => '::1'
    ];
    $examen_salud6->update($input_hum);
    $salud6 ++;

    }

    //nivel 7
    $examen_salud7 = Examen_Nivel::where('nivel','7')->where('id_examen',$book->id)->first();

    if(is_null($examen_salud7)){
    $input_hum = [
    'nivel' => '7',
    'id_examen' => $book->id,
    'valor1' => round( $book->salud_nuevo,2),
    'id_usuariocrea' => '0922290697',
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'SALUD7_CREA',
    'ip_modificacion' => '::1'
    ];
    Examen_Nivel::create($input_hum);
    $salud7 ++;

    }else{
    $input_hum = [
    'valor1' => round( $book->salud_nuevo,2),
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'SALUD7_MODIFICA',
    'ip_modificacion' => '::1'
    ];
    $examen_salud7->update($input_hum);
    $salud7 ++;

    }

    }

    //BLOQUE EHG
    if($book->ehg_nuevo!='0'){
    $examen_ehg = Examen_Nivel::where('nivel','18')->where('id_examen',$book->id)->first();
    //dd($book,"EXISTE NIVEL");
    if(is_null($examen_ehg)){
    $input_hum = [
    'nivel' => '18',
    'id_examen' => $book->id,
    'valor1' => round( $book->ehg_nuevo,2),
    'id_usuariocrea' => '0922290697',
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'EHG_CREA',
    'ip_modificacion' => '::1'
    ];
    Examen_Nivel::create($input_hum);
    $ehg ++;

    }else{
    $input_hum = [
    'valor1' => round( $book->ehg_nuevo,2),
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'EHG_MODIFICA',
    'ip_modificacion' => '::1'
    ];
    $examen_ehg->update($input_hum);
    $ehg ++;
    }

    }

    //BLOQUE VUMI
    if($book->vumi!='0'){
    $examen_vumi = Examen_Nivel::where('nivel','20')->where('id_examen',$book->id)->first();
    //dd($book,"EXISTE NIVEL");
    if(is_null($examen_vumi)){
    $input_hum = [
    'nivel' => '20',
    'id_examen' => $book->id,
    'valor1' => round( $book->vumi,2),
    'id_usuariocrea' => '0922290697',
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'VUMI_CREA',
    'ip_modificacion' => '::1'
    ];
    Examen_Nivel::create($input_hum);
    $vumi ++;

    }else{
    $input_hum = [
    'valor1' => round( $book->vumi,2),
    'id_usuariomod' => '0922290697',
    'ip_creacion' => 'VUMI_MODIFICA',
    'ip_modificacion' => '::1'
    ];
    $examen_vumi->update($input_hum);
    $vumi ++;
    }

    }

    }

    }

    Log_usuario::create([
    'id_usuario' => '0922290697',
    'ip_usuario' => 'SISTEMA',
    'descripcion' => 'CARGA VALORES NUEVOS',
    'dato_ant1' => 'HUMANA: '.$humana.' SALUD: '.$salud5.' - '.$salud6.' -a '.$salud7.' -EHG: '.$ehg.' -VUMI: '.$vumi,
    ]);

    });

    return "listo";
    }*/

    //VALORES SALUD 2020
    public function importar_valores2019()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 200);

        Excel::filter('chunk')->load('val2020.xlsx')->chunk(250, function ($reader) {

            $salud5 = '0';
            $salud6 = '0';
            $salud7 = '0';

            foreach ($reader as $book) {
                $examen = Examen::find($book->id);

                if (!is_null($examen)) {

                    //BLOQUE SALUD nivel 5
                    if ($book->salud_nuevo != '0') {
                        $examen_salud5 = Examen_Nivel::where('nivel', '5')->where('id_examen', $book->id)->first();

                        if (is_null($examen_salud5)) {
                        } else {
                            $input_hum = [
                                'valor1'          => round($book->salud_nuevo, 2),
                                'id_usuariomod'   => '0922290697',
                                'ip_creacion'     => 'SALUD5_MODIFICA2020',
                                'ip_modificacion' => '::1',
                            ];
                            $examen_salud5->update($input_hum);
                            $salud5++;
                        }
                        //nivel 6
                        $examen_salud6 = Examen_Nivel::where('nivel', '6')->where('id_examen', $book->id)->first();

                        if (is_null($examen_salud6)) {
                        } else {
                            $input_hum = [
                                'valor1'          => round($book->salud_nuevo, 2),
                                'id_usuariomod'   => '0922290697',
                                'ip_creacion'     => 'SALUD6_MODIFICA2020',
                                'ip_modificacion' => '::1',
                            ];
                            $examen_salud6->update($input_hum);
                            $salud6++;
                        }

                        //nivel 7
                        $examen_salud7 = Examen_Nivel::where('nivel', '7')->where('id_examen', $book->id)->first();

                        if (is_null($examen_salud7)) {
                        } else {
                            $input_hum = [
                                'valor1'          => round($book->salud_nuevo, 2),
                                'id_usuariomod'   => '0922290697',
                                'ip_creacion'     => 'SALUD7_MODIFICA2020',
                                'ip_modificacion' => '::1',
                            ];
                            $examen_salud7->update($input_hum);
                            $salud7++;
                        }
                    }
                }
            }

            Log_usuario::create([
                'id_usuario'  => '0922290697',
                'ip_usuario'  => 'SISTEMA',
                'descripcion' => 'CARGA VALORES NUEVOS',
                'dato_ant1'   => 'HUMANA: ' . $humana . ' SALUD: ' . $salud5 . ' - ' . $salud6 . ' -a ' . $salud7 . ' -EHG: ' . $ehg . ' -VUMI: ' . $vumi,
            ]);
        });

        return "listo";
    }

    //SOLO LABS
    /*public function importar_valores2019()
    {

    if($this->rol()){
    return response()->view('errors.404');
    }

    ini_set('max_execution_time', 100);

    Excel::filter('chunk')->load('subelabs.xlsx')->chunk(250, function($reader) {

    $cactualizados = '0';
    $cnoactualizados = '0';

    foreach ($reader as $book) {

    //dd($book);
    $examen = Examen::find($book->id);
    if(!is_null($examen)){

    $input = [
    'valor' => round($book->labs,2) ,
    'ip_modificacion' => 'ACT2019_2',
    ];

    $examen->update($input);
    $cactualizados ++;

    }else{
    $cnoactualizados ++;
    }

    }

    Log_usuario::create([
    'id_usuario' => '0922290697',
    'ip_usuario' => 'SISTEMA',
    'descripcion' => 'CARGA REFERIDOS',
    'dato_ant1' => 'examenes actu: '.$cactualizados.' -no actualizados: '.$cnoactualizados,
    ]);

    });

    return "listo";
    }*/

    /*public function cargar_estad(){
    $ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;

    $procedimientos = Procedimiento::all();

    $historia_clinica = Historiaclinica::where('examenes_realizar','!=',null)->get();

    $arr = null; $arr2 = null; $arr3 = null; $arr4 = null;
    foreach ($procedimientos as $p) {
    $hist = Historiaclinica::where('examenes_realizar','like','%'.$p->nombre.'%')->where('estad','0')->get();

    foreach ($hist as $h) {

    $hist_prox = Historiaclinica::where('id_paciente',$h->id_paciente)->where('created_at','>',$h->created_at)->get();
    $arr[] = $h->hcid;
    if($hist_prox->count()>0){
    $arr2[] = $h->hcid;
    }
    foreach ($hist_prox as $h2) {
    $agenda = Agenda::find($h2->id_agenda);
    if($agenda->proc_consul=='1'){
    $arr3[] = $h->hcid;
    $pentax = Pentax::where('id_agenda',$h2->id_agenda)->first();
    if(!is_null($pentax)){
    if($pentax->estado_pentax=='4'){
    $arr4[] = $h->hcid;
    $procs = PentaxProc::where('id_pentax',$pentax->id)->get();
    $xi=1;
    foreach ($procs as $px) {
    if($xi){
    $txt_px = ''; $xi =0;
    }else{
    $txt_px = '+'.$txt_px;
    }
    $px->procedimiento
    }
    Hc_estadistica_procedimiento::create([
    'hcid_orden' => $h->hcid,
    'hcid_proc' => $h2->hcid,
    'fecha_proc' => $h2->created_at,
    'anio' => substr($h2->created_at,0,4),
    'mes' => substr($h2->created_at, 5,2),
    'id_doctor1' => $h->id_doctor1,
    'id_seguro' => $h->id_seguro,
    'ip_creacion' => $ip_cliente,
    'ip_modificacion' => $ip_cliente,
    'id_usuariocrea' => $idusuario,
    'id_usuariomod' => $idusuario

    ]);

    }
    }

    }
    }
    }

    }
    //dd($arr3,$arr4);

    $fecha_d = date('Y/m/d');
    Excel::create('Procedimintos-'.$fecha_d, function($excel) use($historia_clinica, $arr, $arr2, $arr3, $arr4) {

    $excel->sheet('Examenes', function($sheet) use($historia_clinica, $arr, $arr2, $arr3, $arr4) {
    $fecha_d = date('Y/m/d');
    $i = 5;
    $sheet->mergeCells('A2:V2');

    $mes = substr($fecha_d, 5, 2);
    if($mes == 01){ $mes_letra = "ENERO";}
    if($mes == 02){ $mes_letra = "FEBRERO";}
    if($mes == 03){ $mes_letra = "MARZO";}
    if($mes == 04){ $mes_letra = "ABRIL";}
    if($mes == 05){ $mes_letra = "MAYO";}
    if($mes == 06){ $mes_letra = "JUNIO";}
    if($mes == 07){ $mes_letra = "JULIO";}
    if($mes == '08'){ $mes_letra = "AGOSTO";}
    if($mes == '09'){ $mes_letra = "SEPTIEMBRE";}
    if($mes == '10'){ $mes_letra = "OCTUBRE";}
    if($mes == '11'){ $mes_letra = "NOVIEMBRE";}
    if($mes == '12'){ $mes_letra = "DICIEMBRE";}
    $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
    $sheet->cell('A2', function($cell) use($fecha2){
    // manipulate the cel
    $cell->setValue('VALORES DE PROCEDIMIENTO'.' - '.$fecha2);

    $cell->setFontWeight('bold');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    });

    $sheet->cells('A1:V3', function($cells) {
    // manipulate the range of cells
    $cells->setAlignment('center');
    });

    $sheet->cells('A4:V4', function($cells) {
    // manipulate the range of cells
    $cells->setFontWeight('bold');
    });
    $sheet->cell('A4', function($cell) {
    // manipulate the cel
    $cell->setValue('HCID');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('B4', function($cell) {
    // manipulate the cel
    $cell->setValue('CEDULA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    });

    $sheet->cell('C4', function($cell) {
    // manipulate the cel
    $cell->setValue('PACIENTE');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    });

    $sheet->cell('D4', function($cell) {
    // manipulate the cel
    $cell->setValue('DOCTOR');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    });

    $sheet->cell('E4', function($cell) {
    // manipulate the cel
    $cell->setValue('EXAMEN SOLICITADO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    });

    $sheet->cell('F4', function($cell) {
    // manipulate the cel
    $cell->setValue('CREADO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    });

    foreach($historia_clinica as $value){

    $sheet->cell('A'.$i, function($cell) use($value, $arr, $arr2, $arr3, $arr4){
    // manipulate the cel
    $cell->setValue($value->hcid);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    if(in_array($value->hcid, $arr2)){
    $cell->setBackground('#FFFF00');
    }
    if(in_array($value->hcid, $arr3)){
    $cell->setBackground('blue');
    }
    if(in_array($value->hcid, $arr4)){
    $cell->setBackground('#ccFF00');
    }

    });

    $sheet->cell('B'.$i, function($cell) use($value){
    // manipulate the cel
    $cell->setValue($value->id_paciente);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('C'.$i, function($cell) use($value) {
    // manipulate the cel
    $cell->setValue($value->paciente->apellido1.' '.$value->paciente->nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('D'.$i, function($cell) use($value) {
    // manipulate the cel
    $cell->setValue($value->doctor_1->apellido1.' '.$value->doctor_1->nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('E'.$i, function($cell) use($value) {
    // manipulate the cel
    $cell->setValue($value->examenes_realizar);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('F'.$i, function($cell) use($value) {
    // manipulate the cel
    $cell->setValue($value->created_at);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $i= $i+1;
    }

    });

    })->export('xlsx');

    //return view('hc_admision.historia.estad',['historia_clinica' => $historia_clinica, 'arr' => $arr]);
    //dd($historia_clinica);
    }*/

    //FUNCION QUE SUBE NUEVOS CONVENIOS DE LABORATORIO
    //ruta importar_valores_seguro


    //actualizar nombre
    public function importar_medicinas()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('medicinas_nombre.xlsx')->chunk(250, function ($reader) {

            $creado      = 0;
            $actualizado = 0;
            foreach ($reader as $book) {
                foreach ($book as $value) {
                    $medicina          = Medicina::find($value->id);
                    $medicina_anterior = $medicina->cantidad;
                    if (!is_null($medicina)) {
                        $input_med = [
                            'cantidad'        => $value->cantidad,
                            'id_usuariomod'   => '0922290697',
                            'ip_modificacion' => 'CALIDAD',
                        ];
                        $medicina->update($input_med);
                    }
                    Log_usuario::create([
                        'id_usuario'  => '0922290697',
                        'ip_usuario'  => 'SISTEMA',
                        'descripcion' => 'ACTUALIZAR MEDICINA: ' . $value->id . '-' . $medicina_anterior,
                        'dato_ant1'   => 'CANTIDAD: ' . $value->cantidad,
                    ]);
                    $actualizado++;
                }
            }
        });
        Excel::filter('chunk')->load('medicinas_nombre1.xlsx')->chunk(250, function ($reader) {

            $creado      = 0;
            $actualizado = 0;
            foreach ($reader as $book) {
                foreach ($book as $value) {
                    $medicina          = Medicina::find($value->id);
                    $medicina_anterior = $medicina->nombre;
                    if (!is_null($medicina)) {
                        $input_med = [
                            'nombre'          => $value->nombre,
                            'id_usuariomod'   => '0922290697',
                            'ip_modificacion' => 'CALIDAD',
                        ];
                        $medicina->update($input_med);
                    }
                    Log_usuario::create([
                        'id_usuario'  => '0922290697',
                        'ip_usuario'  => 'SISTEMA',
                        'descripcion' => 'ACTUALIZAR MEDICINA: ' . $value->id . '-' . $medicina_anterior,
                        'dato_ant1'   => 'NOMBRE: ' . $value->nombre,
                    ]);
                    $actualizado++;
                }
            }
        });
        Excel::filter('chunk')->load('medicinas_nombre2.xlsx')->chunk(250, function ($reader) {

            $creado      = 0;
            $actualizado = 0;
            foreach ($reader as $book) {
                foreach ($book as $value) {
                    $medicina          = Medicina::find($value->id);
                    $medicina_anterior = $medicina->dosis;
                    if (!is_null($medicina)) {
                        $input_med = [
                            'dosis'           => $value->dosis,
                            'id_usuariomod'   => '0922290697',
                            'ip_modificacion' => 'CALIDAD',
                        ];
                        $medicina->update($input_med);
                    }
                    Log_usuario::create([
                        'id_usuario'  => '0922290697',
                        'ip_usuario'  => 'SISTEMA',
                        'descripcion' => 'ACTUALIZAR MEDICINA: ' . $value->id . '-' . $medicina_anterior,
                        'dato_ant1'   => 'DOSIS: ' . $value->dosis,
                    ]);
                    $actualizado++;
                }
            }
        });

        return "listo";
    }

    /* public function importar_medicinas()
    {

    if($this->rol()){
    return response()->view('errors.404');
    }

    ini_set('max_execution_time', 100);

    Excel::filter('chunk')->load('medicinas_nombre.xlsx')->chunk(250, function($reader) {

    $creado = 0; $actualizado = 0;
    foreach ($reader as $book) {
    $medicina = Medicina::where('id', $book->id)->first();
    //dd($book);

    if(!is_null($medicina)){
    $input_med = [
    'dosis' => $book->dosis,
    //'dosis' => $book->pr,
    //'cantidad' => $book->cantidad,
    'id_usuariomod' => '0922290697',
    'ip_modificacion' => 'CALIDAD',
    ];
    //dd($input_med);
    $medicina->update($input_med);
    }
    $actualizado ++;
    }
    Log_usuario::create([
    'id_usuario' => '0922290697',
    'ip_usuario' => 'SISTEMA',
    'descripcion' => 'ACTUALIZAR MEDICINA: '.$book->id,
    'dato_ant1' => 'DOSIS: '.$book->dosis,
    ]);
    });

    return "listo";
    }*/

    /*public function importar_medicinas()
    {

    if($this->rol()){
    return response()->view('errors.404');
    }

    ini_set('max_execution_time', 100);

    Excel::filter('chunk')->load('medicinas_nombre.xlsx')->chunk(250, function($reader) {

    $creado = 0; $actualizado = 0;
    foreach ($reader as $book) {
    $medicina = Medicina::where('id', $book->id)->first();
    //dd($book);

    if(!is_null($medicina)){
    $input_med = [
    'cantidad' => $book->cantidad,
    //'dosis' => $book->pr,
    //'cantidad' => $book->cantidad,
    'id_usuariomod' => '0922290697',
    'ip_modificacion' => 'CALIDAD',
    ];
    //dd($input_med);
    $medicina->update($input_med);
    }
    $actualizado ++;
    }
    Log_usuario::create([
    'id_usuario' => '0922290697',
    'ip_usuario' => 'SISTEMA',
    'descripcion' => 'ACTUALIZAR MEDICINA: '.$book->id,
    'dato_ant1' => 'CANTIDAD: '.$book->cantidad,
    ]);
    });

    return "listo";
    }*/

    public function verificar_medicinas()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('medicinas_iess_final.xlsx')->chunk(250, function ($reader) {
            $repetida     = "";
            $nombre_final = "";
            foreach ($reader as $book) {
                $nombre_final = str_replace("á", "a", $book->nombre);
                $nombre_final = str_replace("é", "e", $nombre_final);
                $nombre_final = str_replace("í", "i", $nombre_final);
                $nombre_final = str_replace("ó", "o", $nombre_final);
                $nombre_final = str_replace("ú", "u", $nombre_final);

                $nombre_final = str_replace("Á", "a", $nombre_final);
                $nombre_final = str_replace("É", "e", $nombre_final);
                $nombre_final = str_replace("Í", "i", $nombre_final);
                $nombre_final = str_replace("Ó", "o", $nombre_final);
                $nombre_final = str_replace("Ú", "u", $nombre_final);

                $medicina = Medicina::where('nombre', $nombre_final)->first();
                //dd($book);
                if (!is_null($medicina)) {
                    $repetida = $medicina->nombre;
                    Log_usuario::create([
                        'id_usuario'  => '0951561075',
                        'ip_usuario'  => 'verificar_medicinas',
                        'descripcion' => 'Repetida: ' . $repetida,
                    ]);
                }
            }
        });

        return "ok";
    }

    public function subir_medicinas_iess()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('medicinas_iess_terminada.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {

                $nombre_generico = str_replace("á", "a", $book->generico);
                $nombre_generico = str_replace("é", "e", $nombre_generico);
                $nombre_generico = str_replace("í", "i", $nombre_generico);
                $nombre_generico = str_replace("ó", "o", $nombre_generico);
                $nombre_generico = str_replace("ú", "u", $nombre_generico);

                $nombre_generico = str_replace("Á", "A", $nombre_generico);
                $nombre_generico = str_replace("É", "E", $nombre_generico);
                $nombre_generico = str_replace("Í", "I", $nombre_generico);
                $nombre_generico = str_replace("Ó", "O", $nombre_generico);
                $nombre_generico = str_replace("Ú", "U", $nombre_generico);

                $generico_iess = Principio_Activo::where('nombre', $nombre_generico)->first();

                if (is_null($generico_iess)) {
                    Principio_Activo::create([
                        'nombre'          => $nombre_generico,
                        'descripcion'     => $nombre_generico,
                        'id_usuariocrea'  => '0951561075',
                        'id_usuariomod'   => '0951561075',
                        'ip_creacion'     => 'medicinas_iess',
                        'ip_modificacion' => '1',
                    ]);

                    $principio_activo    = DB::table('principio_activo')->select('id')->where('nombre', $nombre_generico)->first();
                    $id_principio_activo = $principio_activo->id;
                } else {
                    $id_principio_activo = $generico_iess->id;

                    $act_generico_iess = [
                        'nombre'      => $nombre_generico,
                        'descripcion' => $nombre_generico,
                    ];
                    Principio_Activo::where('id', $id_principio_activo)->update($act_generico_iess);
                }

                $nombre_medicina = str_replace("á", "a", $book->nombre);
                $nombre_medicina = str_replace("é", "e", $nombre_medicina);
                $nombre_medicina = str_replace("í", "i", $nombre_medicina);
                $nombre_medicina = str_replace("ó", "o", $nombre_medicina);
                $nombre_medicina = str_replace("ú", "u", $nombre_medicina);

                $nombre_medicina = str_replace("Á", "A", $nombre_medicina);
                $nombre_medicina = str_replace("É", "E", $nombre_medicina);
                $nombre_medicina = str_replace("Í", "I", $nombre_medicina);
                $nombre_medicina = str_replace("Ó", "O", $nombre_medicina);
                $nombre_medicina = str_replace("Ú", "U", $nombre_medicina);

                $medicina_iess = Medicina::where('nombre', $nombre_medicina)->first();

                if (is_null($medicina_iess)) {
                    Medicina::create([
                        'nombre'          => $nombre_medicina,
                        'dosis'           => $book->prescripcion,
                        'cantidad'        => $book->cantidad,
                        'nombre_generico' => $nombre_generico,
                        'id_usuariocrea'  => '0951561075',
                        'id_usuariomod'   => '0951561075',
                        'ip_creacion'     => 'medicinas_iess',
                        'ip_modificacion' => '1',
                        'iess'            => '1',
                    ]);
                    $medicina    = DB::table('medicina')->select('id')->where('nombre', $nombre_medicina)->first();
                    $id_medicina = $medicina->id;
                } else {
                    $id_medicina = $medicina_iess->id;

                    $act_medicinas_iess = [
                        'nombre'          => $nombre_medicina,
                        'dosis'           => $book->prescripcion,
                        'cantidad'        => $book->cantidad,
                        'nombre_generico' => $nombre_generico,
                    ];
                    Medicina::where('id', $id_medicina)->update($act_medicinas_iess);
                }

                $medicina_principio_iess = DB::table('medicina_principio')->select('*')->where('id_medicina', $id_medicina)->where('id_principio_activo', $id_principio_activo)->first();

                if (is_null($medicina_principio_iess)) {
                    Medicina_Principio::create([
                        'id_medicina'         => $id_medicina,
                        'id_principio_activo' => $id_principio_activo,
                        'id_usuariocrea'      => '0951561075',
                        'id_usuariomod'       => '0951561075',
                        'ip_creacion'         => 'medicinas_iess',
                        'ip_modificacion'     => '1',
                    ]);
                }
            }
        });

        return "ok";
    }

    public function subir_humanlabs()
    {

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('hlabs.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {

                $examen = Examen::find($book->id);
                if (!is_null($examen)) {
                    $arr = [
                        'humanlabs' => '1',
                    ];
                    $examen->update($arr);
                }
            }
        });

        return "ok";
    }

    public function vademecum()
    {
        $principio_activo = DB::table('vade_principio_producto')->get();
        foreach ($principio_activo as $value) {
            $buscar = DB::table('medicina_principio')->where('id_medicina', $value->id_producto)->where('id_principio_activo', $value->id_prin_activo)->first();
            if (is_null($buscar)) {
                DB::table('medicina_principio')->insert(
                    [
                        'id_medicina'         => $value->id_producto,
                        'id_principio_activo' => $value->id_prin_activo,
                        'id_usuariocrea'      => '0922729587',
                        'id_usuariomod'       => '0922729587',
                        'ip_creacion'         => 'VADEMECUM',
                        'ip_modificacion'     => 'VADEMECUM',
                    ]
                );
            }
        }
        dd('final ');
    }
    public function importar_precios()
    {

        Excel::filter('chunk')->load('precios.xls')->chunk(314, function ($reader) {
            // dd($reader);

            foreach ($reader as $value) {
                //dd($value);
                // dd($value->precio1);
                if ($value['codigo'] != null) {
                    //dd($variable);
                    $producto = Ct_productos::where('codigo', $value['codigo'])->first();
                    if (is_null($producto)) {
                        $input = [
                            'codigo'          => $value['codigo'],
                            'nombre'          => $value['nombre'],
                            'descripcion'     => $value['descripcion'],
                            'modelo'          => $value['dmodelo'],
                            'stock_minimo'    => $value['stock'],
                            'estado_tabla'    => '1',
                            'precio1'         => $value['precio1'],
                            'precio2'         => $value['precio2'],
                            'precio3'         => $value['precio3'],
                            'precio4'         => $value['precio4'],
                            'precio5'         => $value['precio5'],
                            'precio6'         => $value['precio6'],
                            'precio7'         => $value['precio7'],
                            'precio8'         => $value['precio8'],
                            'id_usuariocrea'  => '0928572205',
                            'id_usuariomod'   => '0928572205',
                            'ip_creacion'     => 'precios',
                            'ip_modificacion' => 'precios',

                        ];
                        //dd($input);
                        Ct_productos::create($input);
                    } else {

                        $inputs = [
                            'codigo' => $value['codigo'],
                        ];
                        $producto->update($inputs);
                    }
                }
                if (!is_null($value->precio1)) {
                    $variable = [
                        'codigo_producto' => $value['codigo'],
                        'precio'          => $value['precio1'],
                        'id_usuariocrea'  => '0928572205',
                        'id_usuariomod'   => '0928572205',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => '192.168.76.65',
                        'nivel'           => '1',

                    ];
                    //dd($variable);
                    PrecioProducto::insertGetId($variable);
                }
                if (!is_null($value->precio2)) {
                    $variable2 = [
                        'codigo_producto' => $value['codigo'],
                        'precio'          => $value['precio2'],
                        'id_usuariocrea'  => '0928572205',
                        'id_usuariomod'   => '0928572205',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => '192.168.76.65',
                        'nivel'           => '2',

                    ];

                    PrecioProducto::insertGetId($variable2);
                }
                if (!is_null($value->precio3)) {
                    $variable3 = [
                        'codigo_producto' => $value['codigo'],
                        'precio'          => $value['precio3'],
                        'id_usuariocrea'  => '0928572205',
                        'id_usuariomod'   => '0928572205',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => '192.168.76.65',
                        'nivel'           => '3',

                    ];

                    PrecioProducto::insertGetId($variable3);
                }
                if (!is_null($value->precio4)) {
                    $variable4 = [
                        'codigo_producto' => $value['codigo'],
                        'precio'          => $value['precio4'],
                        'id_usuariocrea'  => '0928572205',
                        'id_usuariomod'   => '0928572205',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => '192.168.76.65',
                        'nivel'           => '4',

                    ];

                    PrecioProducto::insertGetId($variable4);
                }
                if (!is_null($value->precio5)) {
                    $variable5 = [
                        'codigo_producto' => $value['codigo'],
                        'precio'          => $value['precio5'],
                        'id_usuariocrea'  => '0928572205',
                        'id_usuariomod'   => '0928572205',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => '192.168.76.65',
                        'nivel'           => '5',

                    ];

                    PrecioProducto::insertGetId($variable5);
                }
                if (!is_null($value->precio6)) {
                    $variable6 = [
                        'codigo_producto' => $value['codigo'],
                        'precio'          => $value['precio6'],
                        'id_usuariocrea'  => '0928572205',
                        'id_usuariomod'   => '0928572205',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => '192.168.76.65',
                        'nivel'           => '6',

                    ];

                    PrecioProducto::insertGetId($variable6);
                }
                if (!is_null($value->precio7)) {
                    $variable7 = [
                        'codigo_producto' => $value['codigo'],
                        'precio'          => $value['precio7'],
                        'id_usuariocrea'  => '0928572205',
                        'id_usuariomod'   => '0928572205',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => '192.168.76.65',
                        'nivel'           => '7',

                    ];

                    PrecioProducto::insertGetId($variable7);
                }
                if (!is_null($value->precio8)) {
                    $variable8 = [
                        'codigo_producto' => $value['codigo'],
                        'precio'          => $value['precio8'],
                        'id_usuariocrea'  => '0928572205',
                        'id_usuariomod'   => '0928572205',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => '192.168.76.65',
                        'nivel'           => '8',

                    ];

                    PrecioProducto::insertGetId($variable8);
                }
            }
        });

        return "ok gracias amigo";
    }
    /*public function importar_nombreiess()
    {

    //SUBRE NOMBRE IESS
    Excel::filter('chunk')->load('nombre_iess.xls')->chunk(314, function ($reader) {
    //dd($reader);

    foreach ($reader as $value) {
    //dd($value);
    foreach ($value as $value2) {
    //dd($value2);
    $examen = Examen::find($value2->id);
    if(!is_null($examen)){
    $examen->update(['nombre_iess' => $value2->nombre_iess]);
    }
    }
    }
    });
    //ACTUALIZA DETALLE CABECERA NOMBRE IESS

    $detalles_cabecera = Archivo_Plano_Detalle::where('estado','1')->where('tipo','EX')->get();
    foreach ($detalles_cabecera as $value) {
    $tarifario = $value->codigo;
    $examen = Examen::where('tarifario',$tarifario)->first();
    if(!is_null($examen)){

    $descripcion = $examen->nombre_iess;
    $value->update(['descripcion' => $descripcion, 'ip_modificacion' => 'SYS']);
    //dd($descripcion);
    }

    }
    //actualiza valores y niveles de convenio iess + crm
    $cabeceras = Examen_Orden::where('estado','1')->where('id_empresa','1307189140001')->where('id_seguro','2')->where('anio','2020')->where('mes','8')->where('id_nivel','1')->get();

    foreach ($cabeceras as $cabecera) {
    $total_valor = 0;
    $convenio = Convenio::where('id_empresa',$cabecera->id_empresa)->where('id_seguro',$cabecera->id_seguro)->first();
    $detalles = $cabecera->detalles;
    foreach ($detalles as $detalle) {
    $examen = Examen::find($detalle->id_examen);
    if(!is_null($examen)){
    $valor = $examen->valor;
    if (!is_null($convenio)) {
    $nivel    = $convenio->id_nivel;
    $ex_nivel = Examen_Nivel::where('id_examen', $examen->id)->where('nivel', $nivel)->first();
    if (!is_null($ex_nivel)) {
    $valor = $ex_nivel->valor1;
    }
    }

    $input_det = [
    'valor'           => $valor,
    'ip_modificacion' => 'SYS_1',
    ];

    $detalle->update($input_det);
    $total_valor += $valor;
    }

    }

    $input_cabecera = [
    'id_nivel'        => $convenio->id_nivel,
    'valor'           => $total_valor,
    'total_valor'     => $total_valor,
    'ip_modificacion' => 'SYS_2',
    ];

    $cabecera->update($input_cabecera);
    //dd($detalles);

    }

    //$detalles_cabecera = Archivo_Plano_Detalle::where('porcent_10','>','0')->get();
    $detalles_cabecera = DB::table('archivo_plano_detalle')->where('porcent_10','>','0')->whereRaw(' round(cantidad * valor - subtotal + porcentaje10,2) ')->paginate(5);
    dd($detalles_cabecera);
    foreach ($detalles_cabecera as $value) {
    //dd($value);
    $cuadre = $value->cantidad * $value->valor;
    $cuadre2 = $value->subtotal + $value->porcentaje10;

    if($cuadre2 <> $cuadre){

    dd($cuadre,$cuadre2,$value);
    $nuevo_10 = ($value->valor - $value->valor_unitario)*$value->cantidad;
    //$nuevo_10 = round($value->subtotal * 0.1,2);

    $arr = [

    //'porcentaje10'         => $nuevo_10,
    'ip_modificacion'      => 'SYS_N10_2'
    ];
    $actu = Archivo_Plano_Detalle::find($value->id);
    //$actu->update($arr);
    //dd($actu);

    }else{

    //dd($cuadre,$cuadre2,$value);
    }

    return "ok gracias amigo";
    }
    }
    public function importar_nombreiess(){
    $cont = 50;
    $plantillas = ApPlantilla::all();
    foreach ($plantillas as $value) {
    if($value->codigo==22 || $value->codigo==23 || $value->codigo==25 || $value->codigo==26 || $value->codigo==39 || $value->codigo==40 || $value->codigo==44 || $value->codigo==45 || $value->codigo==46 || $value->codigo==42){

    }else{
    $arr = [
    'desc_comp' => $value->desc_comp.' NIVEL 2',
    ];
    $value->update($arr);
    $arr_nuevo = [
    'codigo'            => $cont,
    'descripcion'       => $value->descripcion,
    'desc_comp'         => $value->descripcion.' NIVEL 3',
    'estado'            => 1,
    'id_usuariocrea'    => '0922290697',
    'id_usuariomod'     => '0922290697',
    'ip_creacion'       => 'SISTEMA',
    'ip_modificacion'   => 'SISTEMA',
    ];
    ApPlantilla::create($arr_nuevo);

    //dd($arr_nuevo);
    $items = ApPlantillaItem::where('cod_plantilla',$value->codigo)->get();
    foreach ($items as $item) {
    $nuevo_cod = $item->id_procedimiento;
    if($item->id_procedimiento=='395162'){
    $nuevo_cod = '395173';
    }
    if($item->id_procedimiento=='395272'){
    $nuevo_cod = '395281';
    }
    if($item->id_procedimiento=='396021'){
    $nuevo_cod = '';
    }
    if($item->id_procedimiento=='396043'){
    $nuevo_cod = '';
    }
    if($item->id_procedimiento=='394043'){
    $nuevo_cod = '';
    }
    if($nuevo_cod!=''){
    $arr_item_new = [
    'cod_plantilla'     => $cont,
    'tipo_item'         => $item->tipo_item,
    'id_procedimiento'  => $nuevo_cod,
    'cantidad'          => $item->cantidad,
    'orden'             => $item->orden,
    'honorario'         => $item->honorario,
    'separado'          => $item->honorario,
    'estado'            => '1',
    'id_usuariocrea'    => '0922290697',
    'id_usuariomod'     => '0922290697',
    'ip_modificacion'   => 'SIST_ITEM',
    'ip_creacion'       => 'SIST_ITEM'
    ];
    ApPlantillaItem::create($arr_item_new);

    }
    }
    $cont++;

    }
    }

    }*/
    public function importar_nombreiess()
    {
        $cabeceras = Archivo_Plano_Cabecera::whereNull('nombres')->where('estado', '1')->where('parentesco', '<>', 'TITULAR')->get();
        //dd($cabeceras);
        foreach ($cabeceras as $cabecera) {
            /*
            $nombre = $cabecera->paciente->apellido1;
            if($cabecera->paciente->apellido2!='(N/A)' || $cabecera->paciente->apellido2!='N/A'){
            $nombre = $nombre.' '.$cabecera->paciente->apellido2;
            }
            $nombre = $nombre.' '.$cabecera->paciente->nombre1;
            if($cabecera->paciente->nombre2!='(N/A)' || $cabecera->paciente->nombre2!='N/A'){
            $nombre = $nombre.' '.$cabecera->paciente->nombre2;
            }
            $cabecera->update(['nombres' => $nombre]);
             */
            $usuario = User::find($cabecera->id_usuario);
            if (!is_null($usuario)) {
                $nombre = $usuario->apellido1;
                if ($usuario->apellido2 != '(N/A)' || $usuario->apellido2 != 'N/A') {
                    $nombre = $nombre . ' ' . $usuario->apellido2;
                }
                $nombre = $nombre . ' ' . $usuario->nombre1;
                if ($usuario->nombre2 != '(N/A)' || $usuario->nombre2 != 'N/A') {
                    $nombre = $nombre . ' ' . $usuario->nombre2;
                }
                $cabecera->update(['nombres' => $nombre]);
            }
        }
        return "ok";
    }
    public function comisiones_labs()
    {
        //$anio = '2019';
        /*$ordenes = Examen_Orden::where('anio','2020')->where('mes','<','11')->where('estado','1')->where('proceso','0')->whereNotNull('codigo')->where('id_seguro','<>','2')->where('id_seguro','<>','3')->where('id_seguro','<>','5')->where('id_seguro','<>','6')->get();
        foreach ($ordenes as $orden) {
        $orden->update(['proceso' => '1']);

        $detalles = $orden->detalles;
        foreach ($detalles as $detalle) {
        $comision = 0.10;

        $detalle->update(['p_comision' => $comision]);

        }

        }*/
        $ordenes = Examen_Orden::where('estado', '1')->where('codigo', '1270')->get();
        foreach ($ordenes as $orden) {
            $detalles = $orden->detalles;
            foreach ($detalles as $detalle) {

                $detalle->update(['p_comision' => null]);
            }
        }
        return "gracias amigo, att manzanita voladora";
    }

    public function proceso_nivel2()
    {
        $ordenes = Examen_Orden::where('anio', '2021')->where('mes', '2')->where('estado', '1')->join('seguros as s', 's.id', 'examen_orden.id_seguro')->where('s.tipo', '0')->select('examen_orden.*')->get();
        //dd($ordenes);
        foreach ($ordenes as $value) {
            $detalles     = $value->detalles;
            $nivel        = 3;
            $total_nivel2 = 0;
            foreach ($detalles as $detalle) {
                $ex_nivel = Examen_Nivel::where('id_examen', $detalle->id_examen)->where('nivel', $nivel)->first();
                if (!is_null($ex_nivel)) {
                    $valor_nivel2 = round($ex_nivel->valor1 * 0.95, 2);
                    $total_nivel2 += $valor_nivel2;
                }
                $detalle->update(['valor_nivel2' => $valor_nivel2]);
            }
            $value->update(['total_nivel2' => $total_nivel2]);
        }

        return "disculpa ya termine";
    }

    public function ventas($nombre)
    {
        Excel::filter('chunk')->load('public/NOVIEMBRE.xlsx')->chunk(314, function ($reader) {
            $contador = 0;
            foreach ($reader as $value) {
                //dd($value);
                /*if ($contador > 5) {
                dd("termino 1");
                }*/
                if (!is_null($value['cedula'])) {
                    $id_empresa  = '0993075000001';
                    $comprobante = $value['numero_de_factura'];
                    $partes      = explode("-", $comprobante);
                    $c_sucursal  = $partes['0'];
                    $c_caja      = $partes['1'];
                    $nfactura    = $partes['2'];
                    $number      = $nfactura;
                    $length      = 9;
                    $nfactura    = substr(str_repeat(0, $length) . $number, -$length);
                    $comprobante = $c_sucursal . '-' . $c_caja . '-' . $nfactura;
                    $ct_venta    = ct_ventas::where('nro_comprobante', $comprobante)->where('id_empresa', $id_empresa)->first();
                    if (is_null($ct_venta)) {
                        //dd('entra');
                        //dd($ct_venta);
                        if (strlen($value['cedula']) == 13 && substr($value['cedula'], -3) == '001') {
                            $tipo = "04";
                        } else {
                            $tipo = "05";
                        }

                        $data['empresa']             = $id_empresa;
                        $cliente['cedula']           = $value['cedula'];
                        $cliente['nro_autorizacion'] = $value['numero_de_autorizacion'];
                        $cliente['tipo']             = $tipo;
                        $cliente['nombre']           = $value['nombre_del_cliente'];
                        $cliente['apellido']         = "";
                        $cliente['email']            = $value['cedula'] . "@mail.com";
                        $cliente['telefono']         = "0";
                        $direccion['calle']          = 'Guayaquil';
                        $direccion['ciudad']         = 'Guayaquil';
                        $cliente['direccion']        = $direccion;
                        $data['cliente']             = $cliente;

                        if (!is_null($value['no_orden'])) {
                        }
                        //busco los productos labs
                        $ordenes     = explode('-', $value['no_orden']);
                        $valor_1     = 0;
                        $valor_fee   = 0;
                        $valor_total = 0;
                        $descuento   = 0;
                        $productos   = array();

                        if (!is_null($value['no_orden'])) {
                            foreach ($ordenes as $key => $d_orden) {
                                $orden = Examen_Orden::find($d_orden);
                                $valor_1 += $orden->valor;
                                $valor_fee += $orden->recargo_valor;
                                $valor_total += $orden->total_valor;
                                $descuento += $orden->descuento_valor;

                                $orden->comprobante = $comprobante;
                                $orden->fecha_envio = date('Y-m-d');
                                $orden->save();
                            }
                        } else {
                            $valor_1     = $value['valor'];
                            $valor_fee   = 0;
                            $valor_total = $valor_1;
                        }
                        $subtotal = $valor_1 - $descuento;
                        //se envian los productos
                        $producto['sku']       = "EXAMEN LABS";
                        $producto['nombre']    = "EXAMENES DE LABORATORIO";
                        $producto['copago']    = 0;
                        $producto['cantidad']  = "1";
                        $producto['precio']    = $valor_1;
                        $producto['descuento'] = $descuento;
                        $producto['subtotal']  = $valor_1 - $descuento;
                        $producto['tax']       = 0;
                        $producto['total']     = $subtotal;

                        $productos[0] = $producto;

                        if ($valor_fee > 0) {
                            $producto_fee['sku']       = "COMISION";
                            $producto_fee['nombre']    = "FEE ADMINISTRATIVO";
                            $producto_fee['copago']    = 0;
                            $producto_fee['cantidad']  = "1";
                            $producto_fee['precio']    = $valor_fee;
                            $producto_fee['descuento'] = 0;
                            $producto_fee['subtotal']  = $valor_fee;
                            $producto_fee['tax']       = 0;
                            $producto_fee['total']     = $valor_fee;

                            $productos[1] = $producto_fee;
                        }

                        $data['productos'] = $productos;

                        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
                        15  COMPENSACIÓN DE DEUDAS
                        16  TARJETA DE DÉBITO
                        17  DINERO ELECTRÓNICO
                        18  TARJETA PREPAGO
                        19  TARJETA DE CRÉDITO
                        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
                        21  ENDOSO DE TÍTULOS
                         */
                        if (!is_null($value['no_orden'])) {
                            $paciente                      = Paciente::find($orden->id_paciente);
                            $usuario                       = User::find($paciente->id_usuario);
                            $info_adicional['nombre']      = "correo";
                            $info_adicional['valor']       = $usuario->email;
                            $info[0]                       = $info_adicional;
                            $pago['informacion_adicional'] = $info;
                            $pago['forma_pago']            = '01';
                            $pago['dias_plazo']            = '30';
                            $data['pago']                  = $pago;
                            $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                            $data['laboratorio']           = 1;
                            $data['fecha']                 = $value['fecha'];
                            $data['paciente']              = $orden->id_paciente;
                            $data['concepto']              = 'Ingreso de Facturacion Masivo';
                            $data['copago']                = 0;
                            $data['id_seguro']             = $orden->id_seguro; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                            $data['total_factura']         = $valor_total;
                        } else {
                            $info_adicional['nombre']      = "correo";
                            $info_adicional['valor']       = "";
                            $info[0]                       = $info_adicional;
                            $pago['informacion_adicional'] = $info;
                            $pago['forma_pago']            = '01';
                            $pago['dias_plazo']            = '30';
                            $data['pago']                  = $pago;
                            $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                            $data['laboratorio']           = 1;
                            $data['fecha']                 = $value['fecha'];
                            $data['paciente']              = '';
                            $data['concepto']              = 'Ingreso de Facturacion Masivo';
                            $data['copago']                = 0;
                            $data['id_seguro']             = 1; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                            $data['total_factura']         = $valor_total;
                        }
                        $pagos = array();

                        $pago_factura = $valor_total;

                        if (!is_null($value['banco_1'])) {
                            if ($value['tipo_de_tarjeta'] == "CREDITO") {
                                $tipos_pago['id_tipo'] = 4; //metodo de pago efectivo, tarjeta, etc
                            } else {
                                $tipos_pago['id_tipo'] = 6;
                            }
                            $banco = Ct_Bancos::where('nombre', 'like', '%' . $value['banco_1'] . '%')->first();
                            if (!is_null($banco)) {
                                $id_banco = $banco->id;
                            } else {
                                $id_banco = 2;
                            }
                            if (!is_null($value['valor'])) {
                                $valor_pagar = $value['valor'];
                            } else {
                                $valor_pagar = $valor_total;
                            }

                            $tipos_pago['fecha']              = $value['fecha'];
                            $tipos_pago['tipo_tarjeta']       = 2; //si es efectivo no se envia
                            $tipos_pago['numero_transaccion'] = $value['lote_tarjeta']; //si es efectivo no se envia
                            $tipos_pago['id_banco']           = $id_banco; //si es efectivo no se envia
                            $tipos_pago['cuenta']             = null; //si es efectivo no se envia
                            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                            $tipos_pago['valor']              = $valor_pagar; //valor a pagar de total
                            $tipos_pago['valor_base']         = $valor_pagar;
                            $pagos[0]                         = $tipos_pago;
                            $pago_factura                     = $pago_factura - $valor_pagar;
                        } elseif (!is_null($value['banco_2'])) {
                            $tipos_pago['id_tipo'] = 2;
                            $banco                 = Ct_Bancos::where('nombre', 'like', '%' . $value['banco_2'] . '%')->first();
                            if (!is_null($banco)) {
                                $id_banco = $banco->id;
                            } else {
                                $id_banco = 2;
                            }

                            if (!is_null($value['valor'])) {
                                $valor_pagar = $value['valor'];
                            } else {
                                $valor_pagar = $valor_total;
                            }

                            $tipos_pago['fecha']              = $value['fecha'];
                            $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                            $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
                            $tipos_pago['id_banco']           = $id_banco; //si es efectivo no se envia
                            $tipos_pago['cuenta']             = $value['numero_cheque']; //si es efectivo no se envia
                            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                            $tipos_pago['valor']              = $valor_pagar; //valor a pagar de total
                            $tipos_pago['valor_base']         = $valor_pagar;
                            $pagos[0]                         = $tipos_pago;
                            $pago_factura                     = $pago_factura - $valor_pagar;
                        } elseif (!is_null($value['banco_3'])) {
                            $tipos_pago['id_tipo'] = 5;
                            $banco                 = Ct_Bancos::where('nombre', 'like', '%' . $value['banco_3'] . '%')->first();
                            if (!is_null($banco)) {
                                $id_banco = $banco->id;
                            } else {
                                $id_banco = 2;
                            }
                            if (!is_null($value['valor'])) {
                                $valor_pagar = $value['valor'];
                            } else {
                                $valor_pagar = $valor_total;
                            }

                            $tipos_pago['fecha']              = $value['fecha'];
                            $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                            $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
                            $tipos_pago['id_banco']           = $id_banco; //si es efectivo no se envia
                            $tipos_pago['cuenta']             = null; //si es efectivo no se envia
                            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                            $tipos_pago['valor']              = $valor_pagar; //valor a pagar de total
                            $tipos_pago['valor_base']         = $valor_pagar;
                            $pagos[0]                         = $tipos_pago;
                            $pago_factura                     = $pago_factura - $valor_pagar;
                        } else {
                            if (!is_null($value['valor'])) {
                                $valor_pagar = $value['valor'];
                            } else {
                                $valor_pagar = $valor_total;
                            }
                            $tipos_pago['id_tipo']            = 1;
                            $tipos_pago['fecha']              = $value['fecha'];
                            $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                            $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
                            $tipos_pago['id_banco']           = null; //si es efectivo no se envia
                            $tipos_pago['cuenta']             = null; //si es efectivo no se envia
                            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                            $tipos_pago['valor']              = $pago_factura; //valor a pagar de total
                            $tipos_pago['valor_base']         = $pago_factura;
                            $pagos[0]                         = $tipos_pago;

                            $pago_factura = $pago_factura - $valor_pagar;
                        }

                        if ((!is_null($value['banco_3']) or !is_null($value['banco_2']) or !is_null($value['banco_1'])) and $pago_factura > 0) {
                            $tipos_pago['id_tipo']            = 1;
                            $tipos_pago['fecha']              = $value['fecha'];
                            $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                            $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
                            $tipos_pago['id_banco']           = null; //si es efectivo no se envia
                            $tipos_pago['cuenta']             = null; //si es efectivo no se envia
                            $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                            $tipos_pago['valor']              = $pago_factura; //valor a pagar de total
                            $tipos_pago['valor_base']         = $pago_factura;
                            $pagos[1]                         = $tipos_pago;
                        }

                        $data['formas_pago'] = $pagos;
                        $envio               = ApiFacturacionController::crea_factura_noelec($data, $comprobante);
                        $contador++;
                    } else {

                        //dd('entra else');

                        /*if (strlen($value['cedula']) == 13 && substr($value['cedula'], -3) == '001') {
                        $tipo = "04";
                        } else {
                        $tipo = "05";
                        }

                        $data['empresa'] = $id_empresa;
                        $data['fecha']   = $value['fecha'];

                        $envio = ApiFacturacionController::reproceso_fecha($data, $comprobante);
                        /*dd($envio);
                        $cliente['cedula']           = $value['cedula'];
                        $cliente['nro_autorizacion'] = $value['numero_de_autorizacion'];
                        $cliente['tipo']             = $tipo;
                        $cliente['nombre']           = $value['nombre_del_cliente'];
                        $cliente['apellido']         = "";
                        $cliente['email']            = $value['cedula'] . "@mail.com";
                        $cliente['telefono']         = "0";
                        $direccion['calle']          = 'Guayaquil';
                        $direccion['ciudad']         = 'Guayaquil';
                        $cliente['direccion']        = $direccion;
                        $data['cliente']             = $cliente;

                        if (!is_null($value['no_orden'])) {

                        }
                        //busco los productos labs
                        $ordenes     = explode('-', $value['no_orden']);
                        $valor_1     = 0;
                        $valor_fee   = 0;
                        $valor_total = 0;
                        $descuento   = 0;
                        $productos   = array();

                        if (!is_null($value['no_orden'])) {
                        foreach ($ordenes as $key => $d_orden) {
                        $orden              = Examen_Orden::find($d_orden);
                        $orden->comprobante = $comprobante;
                        $orden->fecha_envio = date('Y-m-d');
                        $orden->save();

                        $valor_1 += $orden->valor;
                        $valor_fee += $orden->recargo_valor;
                        $valor_total += $orden->total_valor;
                        $descuento += $orden->descuento_valor;
                        }
                        } else {
                        $valor_1     = $value['valor'];
                        $valor_fee   = 0;
                        $valor_total = $valor_1;
                        }
                        /*$subtotal = $valor_1 - $descuento;
                        //se envian los productos
                        $producto['sku']       = "EXAMEN LABS";
                        $producto['nombre']    = "EXAMENES DE LABORATORIO";
                        $producto['copago']    = 0;
                        $producto['cantidad']  = "1";
                        $producto['precio']    = $valor_1;
                        $producto['descuento'] = $descuento;
                        $producto['subtotal']  = $valor_1 - $descuento;
                        $producto['tax']       = 0;
                        $producto['total']     = $subtotal;

                        $productos[0] = $producto;

                        if ($valor_fee > 0) {
                        $producto_fee['sku']       = "COMISION";
                        $producto_fee['nombre']    = "FEE ADMINISTRATIVO";
                        $producto_fee['copago']    = 0;
                        $producto_fee['cantidad']  = "1";
                        $producto_fee['precio']    = $valor_fee;
                        $producto_fee['descuento'] = 0;
                        $producto_fee['subtotal']  = $valor_fee;
                        $producto_fee['tax']       = 0;
                        $producto_fee['total']     = $valor_fee;

                        $productos[1] = $producto_fee;
                        }

                        $data['productos'] = $productos;

                        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
                        15  COMPENSACIÓN DE DEUDAS
                        16  TARJETA DE DÉBITO
                        17  DINERO ELECTRÓNICO
                        18  TARJETA PREPAGO
                        19  TARJETA DE CRÉDITO
                        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
                        21  ENDOSO DE TÍTULOS
                         */
                        /*if (!is_null($value['no_orden'])) {
                        $paciente                      = Paciente::find($orden->id_paciente);
                        $usuario                       = User::find($paciente->id_usuario);
                        $info_adicional['nombre']      = "correo";
                        $info_adicional['valor']       = $usuario->email;
                        $info[0]                       = $info_adicional;
                        $pago['informacion_adicional'] = $info;
                        $pago['forma_pago']            = '01';
                        $pago['dias_plazo']            = '30';
                        $data['pago']                  = $pago;
                        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                        $data['laboratorio']           = 1;
                        $data['paciente']              = $orden->id_paciente;
                        $data['concepto']              = 'Ingreso de Facturacion Masivo';
                        $data['copago']                = 0;
                        $data['id_seguro']             = $orden->id_seguro; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                        $data['total_factura']         = $valor_total;
                        } else {
                        $info_adicional['nombre']      = "correo";
                        $info_adicional['valor']       = "";
                        $info[0]                       = $info_adicional;
                        $pago['informacion_adicional'] = $info;
                        $pago['forma_pago']            = '01';
                        $pago['dias_plazo']            = '30';
                        $data['pago']                  = $pago;
                        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
                        $data['laboratorio']           = 1;
                        $data['fecha']                 = $value['fecha'];
                        $data['paciente']              = '';
                        $data['concepto']              = 'Ingreso de Facturacion Masivo';
                        $data['copago']                = 0;
                        $data['id_seguro']             = 1; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
                        $data['total_factura']         = $valor_total;
                        }
                        $pagos = array();

                        $pago_factura = $valor_total;

                        if (!is_null($value['banco_1'])) {
                        if ($value['tipo_de_tarjeta'] == "CREDITO") {
                        $tipos_pago['id_tipo'] = 4; //metodo de pago efectivo, tarjeta, etc
                        } else {
                        $tipos_pago['id_tipo'] = 6;
                        }
                        $banco = Ct_Bancos::where('nombre', 'like', '%' . $value['banco_1'] . '%')->first();
                        if (!is_null($banco)) {
                        $id_banco = $banco->id;
                        } else {
                        $id_banco = 2;
                        }
                        if (!is_null($value['valor'])) {
                        $valor_pagar = $value['valor'];
                        } else {
                        $valor_pagar = $valor_total;

                        }

                        $tipos_pago['fecha']              = $value['fecha'];
                        $tipos_pago['tipo_tarjeta']       = 2; //si es efectivo no se envia
                        $tipos_pago['numero_transaccion'] = $value['lote_tarjeta']; //si es efectivo no se envia
                        $tipos_pago['id_banco']           = $id_banco; //si es efectivo no se envia
                        $tipos_pago['cuenta']             = null; //si es efectivo no se envia
                        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                        $tipos_pago['valor']              = $valor_pagar; //valor a pagar de total
                        $tipos_pago['valor_base']         = $valor_pagar;
                        $pagos[0]                         = $tipos_pago;
                        $pago_factura                     = $pago_factura - $valor_pagar;

                        } elseif (!is_null($value['banco_2'])) {
                        $tipos_pago['id_tipo'] = 2;
                        $banco                 = Ct_Bancos::where('nombre', 'like', '%' . $value['banco_2'] . '%')->first();
                        if (!is_null($banco)) {
                        $id_banco = $banco->id;
                        } else {
                        $id_banco = 2;
                        }

                        if (!is_null($value['valor'])) {
                        $valor_pagar = $value['valor'];
                        } else {
                        $valor_pagar = $valor_total;

                        }

                        $tipos_pago['fecha']              = $value['fecha'];
                        $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                        $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
                        $tipos_pago['id_banco']           = $id_banco; //si es efectivo no se envia
                        $tipos_pago['cuenta']             = $value['numero_cheque']; //si es efectivo no se envia
                        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                        $tipos_pago['valor']              = $valor_pagar; //valor a pagar de total
                        $tipos_pago['valor_base']         = $valor_pagar;
                        $pagos[0]                         = $tipos_pago;
                        $pago_factura                     = $pago_factura - $valor_pagar;
                        } elseif (!is_null($value['banco_3'])) {
                        $tipos_pago['id_tipo'] = 5;
                        $banco                 = Ct_Bancos::where('nombre', 'like', '%' . $value['banco_3'] . '%')->first();
                        if (!is_null($banco)) {
                        $id_banco = $banco->id;
                        } else {
                        $id_banco = 2;
                        }
                        if (!is_null($value['valor'])) {
                        $valor_pagar = $value['valor'];
                        } else {
                        $valor_pagar = $valor_total;

                        }

                        $tipos_pago['fecha']              = $value['fecha'];
                        $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                        $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
                        $tipos_pago['id_banco']           = $id_banco; //si es efectivo no se envia
                        $tipos_pago['cuenta']             = null; //si es efectivo no se envia
                        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                        $tipos_pago['valor']              = $valor_pagar; //valor a pagar de total
                        $tipos_pago['valor_base']         = $valor_pagar;
                        $pagos[0]                         = $tipos_pago;
                        $pago_factura                     = $pago_factura - $valor_pagar;
                        } else {
                        if (!is_null($value['valor'])) {
                        $valor_pagar = $value['valor'];
                        } else {
                        $valor_pagar = $valor_total;

                        }
                        $tipos_pago['id_tipo']            = 1;
                        $tipos_pago['fecha']              = $value['fecha'];
                        $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                        $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
                        $tipos_pago['id_banco']           = null; //si es efectivo no se envia
                        $tipos_pago['cuenta']             = null; //si es efectivo no se envia
                        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                        $tipos_pago['valor']              = $pago_factura; //valor a pagar de total
                        $tipos_pago['valor_base']         = $pago_factura;
                        $pagos[0]                         = $tipos_pago;

                        $pago_factura = $pago_factura - $valor_pagar;
                        }

                        if ((!is_null($value['banco_3']) or !is_null($value['banco_2']) or !is_null($value['banco_1'])) and $pago_factura > 0) {
                        $tipos_pago['id_tipo']            = 1;
                        $tipos_pago['fecha']              = $value['fecha'];
                        $tipos_pago['tipo_tarjeta']       = null; //si es efectivo no se envia
                        $tipos_pago['numero_transaccion'] = null; //si es efectivo no se envia
                        $tipos_pago['id_banco']           = null; //si es efectivo no se envia
                        $tipos_pago['cuenta']             = null; //si es efectivo no se envia
                        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
                        $tipos_pago['valor']              = $pago_factura; //valor a pagar de total
                        $tipos_pago['valor_base']         = $pago_factura;
                        $pagos[1]                         = $tipos_pago;
                        }

                        $data['formas_pago'] = $pagos;

                        $envio = ApiFacturacionController::reproceso_factura_noelec($data, $comprobante); */
                        $contador++;
                    }
                }
            }
        });
        return 'EXITO';
    }

    public function insumos2021()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('insumos21.xlsx')->chunk(250, function ($reader) {

            foreach ($reader as $book) {
                $insumo = ApProcedimiento::find($book->id);
                if (!is_null($insumo)) {
                    $insumo->update(['valor' => $book->valor, 'ip_creacion' => 'VAL21']);
                }
            }
        });

        return "ok";
    }

    public function covid_marzo()
    {
        $arr = [
            27409,
        ];
        //dd($arr);

        foreach ($arr as $xor) {

            $orden  = Examen_Orden::find($xor);
            $ordenf = Examen_Orden::find($xor);
            $covid  = $ordenf->detalles->where('id_examen', '1191');
            if ($covid->count() > 0) {
                $nivel    = $orden->id_nivel;
                $detalles = $orden->detalles;
                foreach ($detalles as $value) {
                    if ($value->id_examen == '1191') {
                        $ex_nivel  = Examen_Nivel::where('id_examen', '1191')->where('nivel', $nivel)->first();
                        $input_det = [
                            'id_examen' => '1225',
                            'valor'     => $ex_nivel->valor1,
                        ];
                        $value->update($input_det);
                        $parametros = Examen_parametro::where('id_examen', '1191')->get();
                        foreach ($parametros as $par) {
                            $resultado = Examen_Resultado::where('id_orden', $ordenf->id)->where('id_parametro', $par->id)->where('certificado', '1')->first();
                            //dd($resultado);
                            if (!is_null($resultado)) {
                                if ($resultado->id_parametro == '584') {
                                    if ($resultado->valor == 'NEGATIVO') {
                                        $a_resul = ['valor' => rand(10, 90) / 100, 'id_parametro' => '785', 'id_examen' => '1225'];
                                        $resultado->update($a_resul);
                                        //dd(rand(10, 90)/100);
                                    }
                                    if ($resultado->valor == 'POSITIVO') {
                                        $a_resul = ['valor' => rand(110, 130) / 100, 'id_parametro' => '785', 'id_examen' => '1225'];
                                        $resultado->update($a_resul);
                                        //dd(rand(110, 130)/100);
                                    }
                                }
                                if ($resultado->id_parametro == '585') {
                                    if ($resultado->valor == 'NEGATIVO') {
                                        $a_resul = ['valor' => rand(200, 900) / 100, 'id_parametro' => '784', 'id_examen' => '1225'];
                                        $resultado->update($a_resul);
                                        //dd(rand(200, 900)/100);
                                    }
                                    if ($resultado->valor == 'POSITIVO') {
                                        $a_resul = ['valor' => rand(1100, 1300) / 100, 'id_parametro' => '784', 'id_examen' => '1225'];
                                        $resultado->update($a_resul);
                                        //dd(rand(1100, 1300)/100);
                                    }
                                }
                            }
                        }
                        //dd("no entro");
                    }
                }

                $orden2   = Examen_Orden::find($orden->id);
                $valor2   = $orden2->detalles->sum('valor');
                $input_ex = [
                    'total_valor' => $valor2,
                    'valor'       => $valor2,
                ];
                $orden2->update($input_ex);

                $arr[$orden->id] = $nivel . '-' . $covid->first()->valor . '-' . $orden->total_valor;
                //$i++;
            }
        }

        /*
    $anio = '2021'; $mes = '03';
    $ordenes = Examen_Orden::where('anio',$anio)->where('mes',$mes)->join('seguros as s','s.id','examen_orden.id_seguro')->where('s.tipo','0')->where('examen_orden.estado','1')
    //->where('fecha_orden','<','2021/03/26  0:00:00')
    ->select('examen_orden.*')->orderBy('fecha_orden','acs')->get();
    //dd($ordenes);
    $i = 0;
    foreach($ordenes as $orden){

    $ordenf = Examen_Orden::find($orden->id);
    $covid = $ordenf->detalles->where('id_examen','1191');
    if($covid->count() > 0){
    $nivel = $orden->id_nivel;
    $detalles = $orden->detalles;
    foreach ($detalles as $value) {
    if($value->id_examen=='1191'){
    $ex_nivel = Examen_Nivel::where('id_examen', '1191')->where('nivel', $nivel)->first();
    $input_det = [
    'id_examen' => '1225',
    'valor'     => $ex_nivel->valor1,
    ];
    $value->update($input_det);
    $parametros = Examen_parametro::where('id_examen','1191')->get();
    foreach ($parametros as $par) {
    $resultado = Examen_Resultado::where('id_orden',$ordenf->id)->where('id_parametro',$par->id)->where('certificado','1')->first();
    //dd($resultado);
    if(!is_null($resultado)){
    if($resultado->id_parametro=='584'){
    if($resultado->valor == 'NEGATIVO'){
    $a_resul = [ 'valor' => rand(10, 90)/100, 'id_parametro' => '785', 'id_examen' => '1225' ];
    $resultado->update($a_resul);
    //dd(rand(10, 90)/100);
    }
    if($resultado->valor == 'POSITIVO'){
    $a_resul = [ 'valor' => rand(110, 130)/100, 'id_parametro' => '785', 'id_examen' => '1225' ];
    $resultado->update($a_resul);
    //dd(rand(110, 130)/100);
    }
    }
    if($resultado->id_parametro=='585'){
    if($resultado->valor == 'NEGATIVO'){
    $a_resul = [ 'valor' => rand(200, 900)/100, 'id_parametro' => '784', 'id_examen' => '1225' ];
    $resultado->update($a_resul);
    //dd(rand(200, 900)/100);
    }
    if($resultado->valor == 'POSITIVO'){
    $a_resul = [ 'valor' => rand(1100, 1300)/100, 'id_parametro' => '784', 'id_examen' => '1225' ];
    $resultado->update($a_resul);
    //dd(rand(1100, 1300)/100);
    }
    }
    }
    }
    //dd("no entro");
    }

    }

    $orden2 = Examen_Orden::find($orden->id);
    $valor2 = $orden2->detalles->sum('valor');
    $input_ex = [
    'total_valor'     => $valor2,
    'valor'           => $valor2,
    ];
    $orden2->update($input_ex);

    $arr[$orden->id] =  $nivel.'-'.$covid->first()->valor.'-'.$orden->total_valor;
    $i++;
    }

    }
    dd($arr);
     */
    }

    /********Nueva funcion*********/
    public function masivo_carga_excel()
    {

        ini_set('max_execution_time', 100);

        $contador = 0;

        Excel::filter('chunk')->load('TARIFARIO_PROC.xlsx')->chunk(250, function ($reader) use ($contador) {

            foreach ($reader as $book) {
                $ap_proc = ApProcedimiento::where('codigo', $book->codigo)
                    ->get();

                foreach ($ap_proc as $value) {
                    $proc = [
                        'tipo_procedimiento' => $book->tipo,
                        'procedimiento'      => $book->procedimiento,
                    ];
                    //dd($ap_proc);
                    $value->update($proc);
                    // $contador++;
                }
                //dd($ap_proc);

            }
        });
        return 'ok';
    }

    public function nuevo_seguro_convenio()
    {

        /*  NIVELES DE SALUD
        5   SALUD 3
        6   SALUD 4
        7   SALUD 5 */
        Excel::filter('chunk')->load('subirsalud.xlsx')->chunk(600, function ($reader) {
            foreach ($reader as $book) {
                //dd($book);
                $examen = Examen::find($book->id);

                //dd($examen);
                if (!is_null($examen)) {
                    $examen->update(['codigo_salud' => $book->codigo_salud]);
                    $examen_nivel5 = Examen_Nivel::where('nivel', 5)->where('id_examen', $examen->id)->first();
                    $examen_nivel6 = Examen_Nivel::where('nivel', 6)->where('id_examen', $examen->id)->first();
                    $examen_nivel7 = Examen_Nivel::where('nivel', 7)->where('id_examen', $examen->id)->first();
                    if (!is_null($examen_nivel5)) {

                        $examen_nivel5->update([
                            'valor1'          => round($book->valor_salud, 2),
                            'estado'          => 1,
                            'id_usuariomod'   => '0922290697',
                            'ip_modificacion' => 'SALUD2007',
                        ]);
                    } else {

                        Examen_Nivel::create([
                            'nivel'           => '5',
                            'id_examen'       => $examen->id,
                            'valor1'          => round($book->valor_salud, 2),
                            'estado'          => 1,
                            'id_usuariocrea'  => '0922290697',
                            'id_usuariomod'   => '0922290697',
                            'ip_creacion'     => 'SALUD2007',
                            'ip_modificacion' => 'SALUD2007',
                        ]);
                    }
                    if (!is_null($examen_nivel6)) {

                        $examen_nivel6->update([
                            'valor1'          => round($book->valor_salud, 2),
                            'estado'          => 1,
                            'id_usuariomod'   => '0922290697',
                            'ip_modificacion' => 'SALUD2007',
                        ]);
                    } else {

                        Examen_Nivel::create([
                            'nivel'           => '6',
                            'id_examen'       => $examen->id,
                            'valor1'          => round($book->valor_salud, 2),
                            'estado'          => 1,
                            'id_usuariocrea'  => '0922290697',
                            'id_usuariomod'   => '0922290697',
                            'ip_creacion'     => 'SALUD2007',
                            'ip_modificacion' => 'SALUD2007',
                        ]);
                    }
                    if (!is_null($examen_nivel7)) {

                        $examen_nivel7->update([
                            'valor1'          => round($book->nuevo_valor_n5, 2),
                            'estado'          => 1,
                            'id_usuariomod'   => '0922290697',
                            'ip_modificacion' => 'SALUD2007',
                        ]);
                    } else {

                        Examen_Nivel::create([
                            'nivel'           => '7',
                            'id_examen'       => $examen->id,
                            'valor1'          => round($book->nuevo_valor_n5, 2),
                            'estado'          => 1,
                            'id_usuariocrea'  => '0922290697',
                            'id_usuariomod'   => '0922290697',
                            'ip_creacion'     => 'SALUD2007',
                            'ip_modificacion' => 'SALUD2007',
                        ]);
                    }
                }
            }
        });
        dd("ok");
    }

    public function masivo_inv($nombre)
    {
        $x     = 1;
        $serie = 0;

        $cab  = Insumo_Plantilla_Control::where('id', 24)->first();
        $item = Insumo_Plantilla_Item_Control::where('id_plantilla', 24)->get();

        Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) use ($x, $serie) {
            // dd($reader);]
            DB::table('inv_carga_inventario')->truncate();
            foreach ($reader as $key => $book) {
                //dd($book);
                $fecha_exp = $book->fecha_exp;

                $tipo = $book->tipo;

                if ($book->fecha != "" || !is_null($book->fecha_exp) || strtotime($fecha_exp) == false) {
                    $unix_date  = ($book->fecha_exp - 25569) * 86400;
                    $excel_date = 25569 + ($unix_date / 86400);
                    $unix_date  = ($excel_date - 25569) * 86400;
                    $fecha_exp  = gmdate("Y-m-d", intval($unix_date));
                } else if ($book->fecha == "" || is_null($book->fecha) || $book->fecha == ' ' || is_null($book->fecha)) {
                    $fecha_exp = "2022-12-31";
                }

                //dd($fecha_exp);
                if (is_null($book->serie) || $book->serie == "" || $book->serie == "0" || $book->serie == 0 || trim($book->serie) == '0') {
                    $serie = $this->generar_serie($x);
                } else {
                    if (substr($book->serie, -1, 1) == '-') {
                        $serie = substr($book->serie, 0, -1);
                    } else {
                        $serie = $book->serie;
                    }
                }
                $bodega = "";
                if ($book->bodega == 1) {
                    $bodega = "PENTAX";
                } else if ($book->bodega == 2) {
                    $bodega = "COMPRAS";
                }

                if (substr($book->referencia, -1, 1) == '-') {
                    $referencia = substr($book->referencia, 0, -1);
                } else {
                    $referencia = $book->referencia;
                }
                // if($book->tipo == 'CONSIGNA'){
                DB::table('inv_carga_inventario')->insert([
                    'marca'        => $book->marca, //marca
                    'descripcion'  => $book->detalle, //detalle
                    'descripcion1' => $book->descripcion2, //detalle
                    'codigo'       => $referencia, //referencia
                    'lote'         => $book->lote,
                    'cantidad'     => $book->cantidad,
                    'fecha_exp'    => $fecha_exp, //fecha vencimiento
                    'serie'        => $serie, //codigo de barras
                    'estado'       => $book->estado,
                    'pedido'       => $book->pedido, //numero pedido
                    'tipo'         => $book->tipo, //facturado o consignado
                    'caducado'     => $book->caducado, //NO SE
                    'precio'       => round(floatval($book->precio), 2), //NO SE
                    'lugar'        => $bodega,
                    'bodega'       => $book->bodega,
                    'creado'       => 'REVISAR',
                ]);
                $x++;
                // }
            }
        });

        echo ".:: CARGA A INVENTARIO ::. <BR>";
        InvProcesosController::carga_inicial();
        // dd("ok");
    }
    public function generar_serie($id)
    {
        $serie = null;
        if ($id != null) {

            $serie = date('YmdHis') . $id;
        } else {
            $serie = date('YmdHis') . $id;
        }
        return $serie;

        // $carga = InvCargaInventario::where('bodega', 2)->get();
        // foreach ($carga as $value){
        //     $edit = InvCargaInventario::where('id', $carga->id)->first();

        //     $edit->lugar = 'COMPRAS';
        // }
    }

    public function cambiarProducto()
    {

        //195  717  76   763  712  716  717  712  717  719  721  764  767  774  249 ANT
        //100  108  527  527  88   112  108  88   108  190  110  110  100  192  110 new
        /*94->201 *,
        769->196 *,
        768->209 *,
        223->210* ,
        720->248*,
        744->408*,
        752->406*,
        753->405*,
        535->405*
        349->310,*
        714->79*
        715->213*
        97->806*
        783->399*
        759->399*
        299->137*
        669->751*
         */
        //108123
        //108128
        $ant   = 699;
        $nuevo = 751;

        $det_movi  = InvDetMovimientos::where('id_producto', $ant)->get();
        $invInv    = InvInventario::where('id_producto', $ant)->get();
        $invSerie  = InvInventarioSerie::where('id_producto', $ant)->get();
        $invKardex = InvKardex::where('id_producto', $ant)->get();
        $invCosto  = InvCosto::where('id_producto', $ant)->get();

        $det    = 0;
        $inv    = 0;
        $serie  = 0;
        $kardex = 0;
        $costo  = 0;

        DB::beginTransaction();

        try {
            foreach ($det_movi as $value) {
                $value->id_producto     = $nuevo;
                $value->ip_modificacion = "eliminacion {$ant}";
                $value->save();
                $det++;
            }

            foreach ($invInv as $value) {
                $value->id_producto     = $nuevo;
                $value->ip_modificacion = "eliminacion {$ant}";
                $value->save();
                $inv++;
            }

            foreach ($invSerie as $value) {
                $value->id_producto     = $nuevo;
                $value->ip_modificacion = "eliminacion {$ant}";
                $value->save();
                $serie++;
            }

            foreach ($invKardex as $value) {
                $value->id_producto     = $nuevo;
                $value->ip_modificacion = "eliminacion {$ant}";
                $value->save();
                $kardex++;
            }

            foreach ($invCosto as $value) {
                $value->id_producto = $nuevo;
                $value->detalle     = "eliminacion {$ant}";
                $value->save();
                $costo++;
            }
            DB::commit();
            dd("ant: {$ant} -- nuevo: {$nuevo} -- det_mov:{$det} - Inv:{$inv} - Serie: {$serie} - Kardex: {$kardex} - Costo: {$costo}");
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    public static function masivo_arreglar($id_empresa)
    {
        $compras = Ct_compras::where('id_empresa', $id_empresa)->where('tipo', 2)->get();
        foreach ($compras as $c) {
            $af = AfFacturaActivoCabecera::find($c->tipo_gasto);
            if (!is_null($af)) {
                if ($c->iva_total == $c->subtotal_12) {
                    $c->subtotal_12 = 0;
                }
                if ($c->subtotal_12 == null && $c->iva_total > 0) {
                    $c->subtotal_12 = $c->subtotal;
                    $c->save();
                }
                if ($c->iva_total == 0) {
                    $c->subtotal_0 = 0;
                }
                $c->save();
            } else {
                if ($c->iva_total == $c->subtotal_12) {
                    $c->subtotal_12 = 0;
                }
                $c->save();
            }
        }
        return 'ok AF';
    }

    public function subir_plantillas_ap()
    {

        ini_set('max_execution_time', 100);

        $contador = 0;

        Excel::filter('chunk')->load('subir_plantilla.xlsx')->chunk(250, function ($reader) use ($contador) {

            foreach ($reader as $book) {

                //dd($book['id_plantilla']);
                $cod_plantilla = $book['id_plantilla'];
                $id_proc       = $book['id_proc'];
                $cantidad      = $book['cantidad'];
                $precio        = $book['precio'];

                $ap_procedimiento = ApProcedimiento::find($id_proc);

                if (!is_null($ap_procedimiento)) {

                    $codigo    = $ap_procedimiento->codigo;
                    $tipo_item = $ap_procedimiento->tipo;
                    $array     = [
                        'cod_plantilla'    => $cod_plantilla,
                        'tipo_item'        => $tipo_item,
                        'id_procedimiento' => $id_proc,
                        'cantidad'         => $cantidad,
                        'orden'            => 0,
                        'honorario'        => 'N',
                        'separado'         => 'No',
                        'procedimiento'    => $id_proc,
                        'estado'           => '1',
                        'id_usuariocrea'   => '0922290697',
                        'id_usuariomod'    => '0922290697',
                        'ip_creacion'      => '::1',
                        'ip_modificacion'  => '::1',
                    ];
                    ApPlantillaItem::create($array);
                    $ap_procedimiento->update(['valor' => $precio]);
                }
            }
        });

        return "gracias amigo";
    }
    public function actualizar_valores_publicos_2022()
    {

        Excel::filter('chunk')->load('asubir.xlsx')->chunk(250, function ($reader) {
            foreach ($reader as $book) {
                $id    = $book['id'];
                $valor = $book['valor'];
                //dd($id,$valor);

                $procedimiento = ApProcedimiento::find($id);
                //dd($procedimiento);
                if (!is_null($procedimiento)) {
                    $input = [
                        'valor'           => $valor,
                        'ip_modificacion' => 'VAL_PB_22',
                    ];
                    $procedimiento->update($input);

                    $detalles = Archivo_Plano_Detalle::join('archivo_plano_cabecera as apc', 'apc.id', 'archivo_plano_detalle.id_ap_cabecera')
                        ->select('archivo_plano_detalle.*')
                        ->where('apc.mes_plano', '012022')
                        ->where('archivo_plano_detalle.codigo', $procedimiento->codigo)
                        ->get();
                    foreach ($detalles as $value) {

                        $id_detalle = $value->id;

                        $id_cabecera = $value->id_ap_cabecera;
                        //dd($id_detalle, $id_cabecera, $detalles);
                        $detalle = Archivo_Plano_Detalle::find($id_detalle);
                        if (!is_null($detalle)) {
                            $valor_unitario = $valor / 1.1;
                            $valor_unitario = round($valor_unitario, 2);
                            $porcentaje10   = $valor - $valor_unitario;
                            $subtotal       = $valor_unitario * $value->cantidad;
                            $iva            = $subtotal * 0.12;
                            $total          = $subtotal + ($porcentaje10 * $value->cantidad) + $iva;

                            $input = [
                                'valor_unitario'       => $valor_unitario,
                                'porcentaje10'         => $porcentaje10,
                                'subtotal'             => $subtotal,
                                'iva'                  => $iva,
                                'total'                => $total,
                                'total_solicitado_usd' => $total,
                                'ip_creacion'          => 'VAL_PB_22',
                            ];
                            $detalle->update($input);
                            //dd($detalle);
                        }
                        $cabecera = Archivo_Plano_Cabecera::find($id_cabecera);
                        if (!is_null($cabecera)) {
                            $input = [
                                'ip_creacion' => 'VAL_PB_22',
                            ];
                            $cabecera->update($input);
                        }
                        //dd($detalle);
                    }
                }
            }
        });
    }

    public function cuadre_compras($nombre)
    {
        $visitors = DB::table('inv_carga_inventario')->truncate();

        Excel::load('public/' . $nombre . '.xlsx', function ($reader) use ($nombre) {
            $id_empresa    = Session::get('id_empresa');
            $array_general = array();
            foreach ($reader->get() as $book) {
                if ($book->getTitle() == "PENTAX" or $book->getTitle() == "COMPRAS") {
                    if ($book->getTitle() == "PENTAX") {
                        $tipo = '2';
                    } else {
                        $tipo = '1';
                    }
                    foreach ($book->toArray() as $key => $cells) {

                        if ($tipo == 2) {
                            //dd($cells);
                        }
                        //dd($cells['detalle']);

                        //dd($cells);
                        if ($cells['descripcion'] != 'DESCRIPCION' && $key < 296) {
                            if (!in_array($cells['referencia'], $array_general)) {
                                array_push($array_general, $cells['referencia']);
                                $producto = Producto::where('codigo', $cells['referencia'])->first();
                                $kardex   = InvKardex::where('id_empresa', $id_empresa)
                                    ->where('id_producto', $producto->id)
                                    ->delete();
                                $inventario = InvInventario::where('id_empresa', $id_empresa)
                                    ->where('id_producto', $producto->id)
                                    ->delete();

                                $inventario_serie = InvInventarioSerie::where('id_empresa', $id_empresa)
                                    ->where('id_producto', $producto->id)
                                    ->delete();
                                //dd($inventario_serie);
                                //dd($array_general);c
                            }
                            /*if ($cells['fecha_de_vencimiento'] != "" || !is_null($cells['fecha_de_vencimiento']) || strtotime($fecha_de_vencimiento) == false) {
                            $unix_date  = ($cells['fecha_de_vencimiento'] - 25569) * 86400;
                            $excel_date = 25569 + ($unix_date / 86400);
                            $unix_date  = ($excel_date - 25569) * 86400;
                            $fecha_exp  = gmdate("Y-m-d", intval($unix_date));
                            } else if ($cells['fecha_de_vencimiento'] == "" || is_null($cells['fecha_de_vencimiento']) || $cells['fecha_de_vencimiento'] == ' ' || is_null($cells['fecha_de_vencimiento'])) {
                            $fecha_exp = "2025-12-31";
                            }*/
                            $fecha_exp = $cells['fecha_exp'];
                            if ($fecha_exp <= date('Y-m-d')) {
                                $fecha_exp = '2025-12-31';
                            }
                            //dd($cells);

                            if (is_null($cells['codigo']) || $cells['codigo'] == "" || $cells['codigo'] == "0" || $cells['codigo'] == 0 || trim($cells['codigo']) == '0') {
                                $serie = $this->generar_serie($key);
                            } else {
                                if (substr($cells['codigo'], -1, 1) == '-') {
                                    $serie = substr($cells['codigo'], 0, -1);
                                } else {
                                    $serie = $cells['codigo'];
                                }
                            }
                            //dd($cells);

                            DB::table('inv_carga_inventario')->insert([
                                'marca'        => $nombre, //marca
                                'descripcion'  => $cells['descripcion'], //detalle
                                'descripcion1' => $cells['descripcion'], //detalle
                                'codigo'       => $cells['referencia'], //referencia
                                'lote'         => $cells['lote'],
                                'cantidad'     => $cells['cantidad'],
                                'fecha_exp'    => $fecha_exp, //fecha vencimiento
                                'serie'        => $serie, //codigo de barras
                                'estado'       => 1,
                                'pedido'       => $cells['pedido'], //numero pedido
                                'tipo'         => $cells['consignia'], //facturado o consignado
                                'caducado'     => $fecha_exp, //NO SE
                                'precio'       => round(floatval($cells['precio']), 2), //NO SE
                                'lugar'        => $book->getTitle(),
                                'bodega'       => $tipo,
                                'creado'       => 'REVISAR',
                            ]);
                        }
                    }
                }
            }
            //dd($reader);
            echo "terminado <br> gracias amigo";
        });
    }

    public function crearProductoPortoviejo($nombre)
    {

        $x = 0;
        $guardado = [];
        $error = [];
        Excel::filter('chunk')->load("{$nombre}.xlsx")->chunk(200, function ($reader) use ($guardado, $error) {
            $id_empresa = Session::get('id_empresa');
            //DB::beginTransaction();
            // try{
            $cont = 0;
            foreach ($reader as $key => $book) {
                //dd($book);
                //$producto = Ct_productos::where('id_empresa', $id_empresa)->where('codigo', $book->codigo)->first();
                $producto = Ct_productos::where('id', $book->id)->first();
                $gasto = Plan_Cuentas_Empresa::where("id_plan", $book->gasto)->orWhere("plan", $book->gasto)->first();
                $ventas = Plan_Cuentas_Empresa::where("id_plan", $book->ventas)->orWhere("plan", $book->ventas)->first();
                $costo = Plan_Cuentas_Empresa::where("id_plan", $book->costo)->orWhere("plan", $book->costo)->first();
                $devolucion = Plan_Cuentas_Empresa::where("id_plan", $book->devolucion)->orWhere("plan", $book->devolucion)->first();
                //dd($gasto, $ventas, $costo, $devolucion);
                // dd($producto);
                if (is_null($producto)) {

                    //   Ct_Productos::create([
                    //       'codigo'                    => trim(strtoupper($book->codigo)),
                    //       'nombre'                    => trim(strtoupper($book->producto)),
                    //       'descripcion'               => trim(strtoupper($book->producto)),
                    //       'iva'                       => intval($book->iva),
                    //       'precio1'                   => round($book->pvp,2),
                    //       'id_empresa'                => "{$id_empresa}",
                    //       'tipo'                      => 0,
                    //       'grupo'                     => 1,
                    //       'cta_gastos'                => $gasto->id_plan,
                    //       'cta_ventas'                => $ventas->id_plan,
                    //       'cta_costos'                => $costo->id_plan,	
                    //       'cta_devolucion'            => $devolucion->id_plan,
                    //       'reg_serie'                 => '0',
                    //       'mod_precio'                => intval($book->mod_precio),
                    //       'mod_desc'                  => intval($book->mod_desc),
                    //       'stock_minimo'              => 1,
                    //       'impuesto_iva_compras'      => '',
                    //       'impuesto_iva_ventas'       => '',
                    //       'impuesto_servicio'         => intval($book->servicio),
                    //       'precio2'                   => !is_null($book->pvp2) ? $book->pvp2 : 0 ,
                    //       'precio3'                   => !is_null($book->pvp3) ? $book->pvp2 : 0 ,
                    //       'precio4'                   => !is_null($book->pvp4) ? $book->pvp2 : 0 ,
                    //       'ident_paquete'             => 0,
                    //       'estado_tabla'              => 1,
                    //       'id_usuariocrea'            => '0931047997',
                    //       'id_usuariomod'             => '0931047997',
                    //       'ip_creacion'               => 'masivo_quito',
                    //       'ip_modificacion'           => '::1',
                    //   ]);


                } else {
                    //     $impuesto_iva_compras = Plan_Cuentas_Empresa::where("id_plan", $book->impuesto_iva_compras)->orWhere("plan", $book->impuesto_iva_compras)->first();

                    //     $impuesto_iva_ventas = Plan_Cuentas_Empresa::where("id_plan", $book->impuesto_iva_ventas)->orWhere("plan", $book->impuesto_iva_ventas)->first();
                    //    // dd($impuesto_iva_compras, $impuesto_iva_ventas);
                    //     $producto->impuesto_iva_compras = $impuesto_iva_compras->id_plan;
                    //     $producto->impuesto_iva_ventas = $impuesto_iva_ventas->id_plan;
                    $producto->valor_total_paq = $book->pvp;
                    $producto->ip_modificacion = 'act_vic';

                    //    / dd($producto);
                    $producto->save();
                    $cont++;
                }
                if (!is_null($book->pvp)) {
                    $variable = [
                        'codigo_producto' => trim(strtoupper($producto->codigo)),
                        'precio'          => $book->pvp,
                        'id_usuariocrea'  => '0957258056',
                        'id_usuariomod'   => '0957258056',
                        'estado'          => '1',
                        'ip_creacion'     => '192.168.76.65',
                        'ip_modificacion' => 'act_vic',
                        'nivel'           => '1',

                    ];

                    PrecioProducto::insertGetId($variable);
                }
            }
            echo "<h1>OK GRACIAS AMIGO, Archivos cambiados {$cont}</h1>";
        });
    }


    public function guardarExcelAle($nombre, $funcion)
    {

        // $funcion = 1;
        if ($funcion == "1") {
            ImportarController::ctProducto($nombre);
        }

        Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {
            // dd($reader);
            $id_usuario = "0957258056";
            $insumo = array();
            $existe = array();
            $id_productos = 0;
            $creado = [];
            foreach ($reader as $book) {
                // dd($book);
                $producto = Producto::where('codigo', $book->codigo)->where('estado', 1)->first();

                $producto_exis = Producto::where('codigo', $book->codigo)->where('estado', 1)->where('ip_modificacion', '<>', 'masivo_j')->first();
                //dd($producto, $book->codigo);
                $ct_producto = Ct_productos::where('codigo', $book->codigo)->where('id_empresa', '0705968998001')->first();
                //dd($producto, $ct_producto, $book->codigo);
                if (!is_null($producto_exis)) {
                    array_push($existe, $producto_exis->codigo);
                }
                if (is_null($producto)) {
                    // $data = [
                    //     'nombre'            => trim($book->nombre),
                    //     'descripcion'       => $book->descripcion,
                    //     'estado'            => 1,
                    //     'medida'            => 0, 
                    //     'minimo'            => 0, 
                    //     'precio_compra'     => $book->precio,
                    //     'precio_venta'      => $book->precio,
                    //     'codigo'            => $book->codigo,
                    //     'codigo_iess'       => $book->codigo_iess,
                    //     'despacho'          => is_null($book->despacho) ? 0 : $book->despacho,
                    //     'cantidad'          => $book->cantidad,
                    //     'tipo_producto'     => $book->tipo_producto,
                    //     'cantidad_unidad'   => $book->cantidad_unidad,
                    //     'id_marca'          => $book->id_marca,
                    //     'registro_sanitario' => is_null($book->registro_sanitario) ? 0 : $book->registro_sanitario,
                    //     'usos'              => $book->usos,
                    //     'codigo_siempre'    => $book->codigo_siempre,
                    //     'id_usuariocrea'    => $id_usuario,
                    //     'id_usuariomod'     => $id_usuario,
                    //     'ip_creacion'       => $book->ip_creacion,
                    //     'ip_modificacion'   => 'masivo_alex',
                    //     'iva'               => is_null($book->iva) ? 0 : $book->iva,
                    //     'descuento'         => $book->descuento,
                    //     'tipo'              => is_null($book->tipo) ? 0 : $book->tipo,
                    // ];

                    $data = [
                        'nombre'            => trim($book->nombre),
                        'descripcion'       => $book->nombre,
                        'estado'            => 1,
                        'medida'            => 0,
                        'minimo'            => 0,
                        'precio_compra'     => $book->precio,
                        'precio_venta'      => $book->precio,
                        'codigo'            => $book->codigo,
                        'codigo_iess'       => $book->codigo,
                        'despacho'          => 0,
                        'cantidad'          => 1,
                        'tipo_producto'     => 1,
                        'cantidad_unidad'   => 1,
                        'id_marca'          => 298,
                        'registro_sanitario' =>  0,
                        'usos'              => 1,
                        'codigo_siempre'    => 1,
                        'id_usuariocrea'    => "0957258056",
                        'id_usuariomod'     => "0957258056",
                        'ip_creacion'       => $book->grupo,
                        'ip_modificacion'   => 'masivo_j',
                        'iva'               => 1,
                        'descuento'         => 0,
                        'tipo'              => 1,
                    ];
                    //dd($producto, $ct_producto, $data);

                    $id_producto = Producto::insertGetId($data);
                    $id_productos = $id_producto;
                    array_push($creado, $book->codigo);
                } else {
                    $id_productos = $producto->id;
                }

                if (!is_null($ct_producto)) {
                    $producto_insumo = Ct_productos_insumos::where('id_insumo', $id_productos)->where('id_producto', $ct_producto->id)->first();
                    if (is_null($producto_insumo)) {
                        $insumo = [
                            'codigo_producto'       => $book->codigo,
                            'id_insumo'             => $id_productos,
                            'id_producto'           => $ct_producto->id,
                            'id_usuariocrea'        => $book->id_usuariocrea,
                            'id_usuariomod'         => $book->id_usuariocrea,
                            'ip_creacion'           => '::1',
                            'ip_modificacion'       => 'masivo_alex',
                        ];
                        Ct_productos_insumos::create($insumo);
                    }
                }
            }
            dd("Ok gracias amigo", $existe, $creado);
        });
    }

    public static function ctProducto($nombre)
    {
        //dd(Excel::filter('chunk')->load($nombre . '.xlsx'));
        Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {
            // dd($reader);
            $id_usuario = "0950839209";
            $insumo = array();
            $existe = array();
            $id_productos = 0;
            $no_vinc = array();
            $creado =  [];
            $id_empresa = "0705968998001";
            foreach ($reader as $book) {
                //dd($book);
                $producto = Producto::where('codigo', $book->codigo)->where('estado', 1)->first();

                $ct_producto = Ct_productos::where('codigo', $book->codigo)->where('id_empresa', $id_empresa)->first();

                if (!is_null($ct_producto)) {
                    array_push($existe, $ct_producto->codigo);
                }

                if (is_null($ct_producto)) {
                    $producto_a_usar = Ct_productos::find(1772);

                    // $data = [
                    //     'codigo'                       => $book->codigo,
                    //     'nombre'                       => $book->nombre,
                    //     'codigo_barra'                 => $book->codigo_barra,
                    //     'tipo'                         => $book->tipo,
                    //     'descripcion'                  => $book->descripcion,
                    //     'clase'                        => $book->clase,
                    //     'grupo'                        => $book->grupo,
                    //     'proveedor'                    => $book->proveedor,
                    //     'cta_gastos'                   => $book->cta_gastos,
                    //     'cta_ventas'                   => $book->cta_ventas,
                    //     'cta_costos'                   => $book->cta_costos,
                    //     'cta_devolucion'               => $book->cta_devolucion,
                    //     'reg_serie'                    => $book->reg_serie,
                    //     'mod_precio'                   => $book->mod_precio,
                    //     'mod_desc'                     => $book->mod_desc,
                    //     'marca'                        => $book->marca,
                    //     'modelo'                       => $book->modelo,
                    //     'stock_minimo'                 => $book->stock_minimo,
                    //     'fecha_expiracion'             => $book->fecha_expiracion,
                    //     'impuesto_iva_compras'         => $book->impuesto_iva_compras,
                    //     'impuesto_iva_ventas'          => $book->impuesto_iva_ventas,
                    //     'impuesto_servicio'            => $book->impuesto_servicio,
                    //     'impuesto_ice'                 => $book->impuesto_ice,
                    //     'clasificacion_impuesto_ice'   => $book->clasificacion_impuesto_ice,
                    //     'promedio'                     => $book->promedio,
                    //     'reposicion'                   => $book->reposicion,
                    //     'lista'                        => $book->lista,
                    //     'ultima_compra'                => $book->ultima_compra,
                    //     'precio1'                      => $book->precio1,
                    //     'precio2'                      => $book->precio2,
                    //     'precio3'                      => $book->precio3,
                    //     'precio4'                      => $book->precio4,
                    //     'promocion'                    => $book->promocion,
                    //     'descuento'                    => $book->descuento,
                    //     'financiero'                   => $book->financiero,
                    //     'ident_paquete'                => $book->ident_paquete,
                    //     'valor_total_paq'              => $book->valor_total_paq,
                    //     'estado_tabla'                 => $book->estado_tabla,
                    //     'id_usuariocrea'               => $book->id_usuariocrea,
                    //     'id_usuariomod'                => $book->id_usuariomod,
                    //     'ip_creacion' => $book->ip_creacion,
                    //     'ip_modificacion' => $book->ip_modificacion,
                    //     'iva'                       => $book->iva,
                    //     'id_empresa' => $id_empresa,
                    // ];
                    $data = [
                        'codigo'                       => $book->codigo,
                        'nombre'                       => $book->nombre,
                        'codigo_barra'                 => $book->codigo,
                        'tipo'                         => 1,
                        'descripcion'                  => $book->nombre,
                        'clase'                        => $producto_a_usar->clase,
                        'grupo'                        => $producto_a_usar->grupo,
                        'proveedor'                    => $producto_a_usar->proveedor,
                        'cta_gastos'                   => $producto_a_usar->cta_gastos,
                        'cta_ventas'                   => $producto_a_usar->cta_ventas,
                        'cta_costos'                   => $producto_a_usar->cta_costos,
                        'cta_devolucion'               => $producto_a_usar->cta_devolucion,
                        'reg_serie'                    => $producto_a_usar->reg_serie,
                        'mod_precio'                   => $producto_a_usar->mod_precio,
                        'mod_desc'                     => $producto_a_usar->mod_desc,
                        'marca'                        => 298,
                        'modelo'                       => $book->codigo,
                        'stock_minimo'                 => 1,
                        'fecha_expiracion'             => null,
                        'impuesto_iva_compras'         => $producto_a_usar->impuesto_iva_compras,
                        'impuesto_iva_ventas'          => $producto_a_usar->impuesto_iva_ventas,
                        'impuesto_servicio'            => $producto_a_usar->impuesto_servicio,
                        'impuesto_ice'                 => $producto_a_usar->impuesto_ice,
                        'clasificacion_impuesto_ice'   => $producto_a_usar->clasificacion_impuesto_ice,
                        'promedio'                     => $producto_a_usar->promedio,
                        'reposicion'                   => $producto_a_usar->reposicion,
                        'lista'                        => $producto_a_usar->lista,
                        'ultima_compra'                => $producto_a_usar->ultima_compra,
                        'precio1'                      => $book->precio,
                        'precio2'                      => $book->precio,
                        'precio3'                      => $book->precio,
                        'precio4'                      => $book->precio,
                        'promocion'                    => $producto_a_usar->promocion,
                        'descuento'                    => $producto_a_usar->descuento,
                        'financiero'                   => $producto_a_usar->financiero,
                        'ident_paquete'                => $producto_a_usar->ident_paquete,
                        'valor_total_paq'              => $producto_a_usar->valor_total_paq,
                        'estado_tabla'                 => $producto_a_usar->estado_tabla,
                        'id_usuariocrea'               => $producto_a_usar->id_usuariocrea,
                        'id_usuariomod'                => $producto_a_usar->id_usuariomod,
                        'ip_creacion'                  => $book->grupo,
                        'ip_modificacion'              => "masivo_jumejato",
                        'iva'                          => 1,
                        'id_empresa'                   => $id_empresa,
                    ];
                    //dd($producto, $ct_producto, $data);

                    $id_producto = Ct_productos::insertGetId($data);
                    $id_productos = $id_producto;
                    array_push($creado, $book->codigo);
                } else {
                    $id_productos = $ct_producto->id;
                }

                if (!is_null($producto)) {
                    $producto_insumo = Ct_productos_insumos::where('id_insumo', $producto->id)->where('id_producto', $id_productos)->first();
                    if (is_null($producto_insumo)) {
                        $insumo = [
                            'codigo_producto'       => $book->codigo,
                            'id_insumo'             => $producto->id,
                            'id_producto'           => $id_productos,
                            'id_usuariocrea'        => $book->id_usuariocrea,
                            'id_usuariomod'         => $book->id_usuariocrea,
                            'ip_creacion'           => '::1',
                            'ip_modificacion'       => 'masivo_alex',
                        ];
                        Ct_productos_insumos::create($insumo);
                    }
                } else {
                    array_push($no_vinc, $book->codigo);
                }
            }
            dd("Ok gracias amigo", $existe, $creado);
        });
    }

    public function valores_guayaquil()
    {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'GET',
            ),
        );
        $url     = 'http://186.70.157.2/sis_medico/public/examenes/laboratorio/dato?token=dd96a4a6c3dd2119f27e3414a06a8ad8';
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $valores = json_decode($response);
        foreach ($valores as $value) {
            $exa = Examen::find($value->id_examen);
            if (!is_null($exa)) {
                $exa->valor = $value->valor;
                $exa->save();
            }
        }
        dd("Okay, gracias amigo");
    }

    public function importar_valores_seguro()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('humana21102024.xlsx')->chunk(500, function ($reader) {

            foreach ($reader as $book) {
                //dd($book);
                $nivel_seguro = '4'; //BASE NIVEL Y CONVENIO       
                $examen       = Examen::find($book->id);
                if (!is_null($examen)) {

                    $examen_humana = Examen_Nivel::where('nivel', $nivel_seguro)->where('id_examen', $book->id)->first();
                    if (is_null($examen_humana)) {
                        $input_hum = [
                            'nivel'           => $nivel_seguro,
                            'id_examen'       => $book->id,
                            'valor1'          => round($book->valor, 2),
                            'id_usuariocrea'  => '0951561075',
                            'id_usuariomod'   => '0951561075',
                            'ip_creacion'     => 'CREA_21102024' . $nivel_seguro,
                            'ip_modificacion' => '::1',
                        ];
                        //dd($input_hum);
                        Examen_Nivel::create($input_hum);
                        //$creado++;
                    } else {
                        $input_hum = [
                            'valor1'          => round($book->valor, 2),
                            'id_usuariomod'   => '0951561075',
                            'ip_creacion'     => 'MODIFICA_21102024' . $nivel_seguro,
                            'ip_modificacion' => '::1',
                        ];
                        $examen_humana->update($input_hum);
                        //$actualizado++;
                    }
                }
            }

            /*Log_usuario::create([
        'id_usuario'  => '0922290697',
        'ip_usuario'  => 'SISTEMA',
        'descripcion' => 'CARGA REFERIDOS',
        'dato_ant1'   => 'creados: ' . $creado . ' -actualizados: ' . $actualizado,
        ]);*/
        });

        return "oki walter 21102024";
    }

    /*public function importar_valores_seguro()
    {
 
        if ($this->rol()) {
            return response()->view('errors.404');
        }
 
        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('examenes0403.xlsx')->chunk(500, function ($reader) {

            foreach ($reader as $book) {
                //dd($book);
               // $nivel_seguro = '92'; //BASE NIVEL Y CONVENIO       
                $examen       = Examen::find($book->id);
                if (!is_null($examen)) {

                    //$examen_humana = Examen_Nivel::where('nivel', $nivel_seguro)->where('id_examen', $book->id)->first();
                    // if (is_null($examen_humana)) {
                    //     $input_hum = [
                    //         'nivel'           => $nivel_seguro,
                    //         'id_examen'       => $book->id,
                    //         'valor1'          => round($book->valor, 2),
                    //         'id_usuariocrea'  => '0951561075',
                    //         'id_usuariomod'   => '0951561075',
                    //         'ip_creacion'     => 'CREA_29022024_conf' . $nivel_seguro,
                    //         'ip_modificacion' => '::1',
                    //     ];
                    //     //dd($input_hum);
                    //     Examen_Nivel::create($input_hum);
                    //     //$creado++;
                    // } else { 
                        $input_hum = [
                            'valor'          => round($book->valor, 2),
                            'id_usuariomod'   => '0951561075',
                            'ip_creacion'     => 'Precios_0403',
                            'ip_modificacion' => '::1',
                        ];
                        $examen->update($input_hum);
                        //$actualizado++;
                   // }
                }
            }

            /*Log_usuario::create([
        'id_usuario'  => '0922290697',
        'ip_usuario'  => 'SISTEMA',
        'descripcion' => 'CARGA REFERIDOS',
        'dato_ant1'   => 'creados: ' . $creado . ' -actualizados: ' . $actualizado,
        ]);
        });

        return "examenes actualizados 0403";
    }*/

    // public function importar_valores_seguro()
    // {

    //     if ($this->rol()) {
    //         return response()->view('errors.404');
    //     }

    //     ini_set('max_execution_time', 100);

    //     Excel::filter('chunk')->load('ecuasanitas.xlsx')->chunk(500, function ($reader) {
    //         $creado = 0;
    //         $actualizado = 0;
    //         foreach ($reader as $book) {
    //             //dd($book);

    //             $id_seguro = '10';  
    //             $nivel = '10';

    //             $id_empresa = Session::get('id_empresa');
    //             $ct_productos       = Ct_productos::where('codigo', $book->codigo)->where('id_empresa', $id_empresa)->where('estado_tabla', '1')->first();
    //             if (!is_null($ct_productos)) { 

    //                 $prod_tarifario = Ct_Productos_Tarifario::where('nivel', $nivel)->where('id_seguro', $id_seguro)->where('id_producto', $ct_productos->id)->first();
    //                 if (is_null($prod_tarifario)) {
    //                     $input = [
    //                         'id_seguro'       => $id_seguro,
    //                         'nivel'           => $nivel,
    //                         'id_producto'     => $ct_productos->id,
    //                         'precio_producto' => round($book->valor, 2),
    //                         'id_usuariocrea'  => '0922290697',
    //                         'id_usuariomod'   => '0922290697',
    //                         'ip_creacion'     => 'CREA_02102023',
    //                         'ip_modificacion' => '::1',
    //                     ];
    //                     //dd($input);
    //                     Ct_Productos_Tarifario::create($input);
    //                     $creado++;
    //                 } else {
    //                     $input_hum = [
    //                         'precio_producto' => round($book->valor, 2),
    //                         'id_usuariomod'   => '0922290697',
    //                         'ip_creacion'     => 'MODIFICA_02102023',
    //                         'ip_modificacion' => '::1',
    //                     ];
    //                     $prod_tarifario->update($input_hum);
    //                     $actualizado++;
    //                 }
    //             }
    //         }

    //     Log_usuario::create([
    //     'id_usuario'  => '0922290697',
    //     'ip_usuario'  => 'SISTEMA',
    //     'descripcion' => 'TARIFARIO ECUASANITAS SANTO',
    //     'dato_ant1'   => 'creados: ' . $creado . ' - actualizados: ' . $actualizado,
    //     ]);
    //     });

    //     return "oki walter 02102023";
    // }

    // public function importar_valores_seguro()
    // { 

    //     if ($this->rol()) {
    //         return response()->view('errors.404');
    //     }

    //     ini_set('max_execution_time', 100);

    //    // Excel::filter('chunk')->load('pablo_minda.xlsx')->chunk(500, function ($reader) {
    //         $examen       = Examen::where('estado', '1')->get(); 
    //         foreach ($examen as $book) {
    //             //dd($book);
    //             $nivel_seguro = '88'; //BASE NIVEL Y CONVENIO       
    //             $examen       = Examen::find($book->id);
    //             if (!is_null($examen)) { 

    //                 $examen_nivel = Examen_Nivel::where('nivel', $nivel_seguro)->where('id_examen', $book->id)->first();
    //                 if (is_null($examen_nivel)) {
    //                     $vt = 0;
    //                     $desc = ($book->valor * 10)/100;
    //                     $vt = $book->valor - $desc;

    //                     $input_hum = [
    //                         'nivel'           => $nivel_seguro,
    //                         'id_examen'       => $book->id,
    //                         'valor1'          => round($vt, 2),
    //                         'id_usuariocrea'  => '0951561075',
    //                         'id_usuariomod'   => '0951561075',
    //                         'ip_creacion'     => 'CREA_31012023_' . $nivel_seguro,
    //                         'ip_modificacion' => '::1',
    //                     ];
    //                     //dd($input_hum);
    //                     Examen_Nivel::create($input_hum);
    //                     //$creado++;
    //                 } else {
    //                     $vt = 0;
    //                     $desc = ($book->valor * 10)/100;
    //                     $vt = $book->valor - $desc;
    //                     $input_hum = [
    //                         'valor1'          => round($vt, 2),
    //                         'id_usuariomod'   => '0951561075',
    //                         'ip_creacion'     => 'MODIFICA_310120123_' . $nivel_seguro,
    //                         'ip_modificacion' => '::1',
    //                     ];
    //                     $examen_nivel->update($input_hum);
    //                     //$actualizado++;
    //                 }
    //             }
    //         }
    //  // });

    //     return "oki walter 29052023";
    // }

    // public function importar_valores_seguro()
    // {

    //     if ($this->rol()) {
    //         return response()->view('errors.404');
    //     }

    //     ini_set('max_execution_time', 100);

    //     Excel::filter('chunk')->load('tiempoexa_quito.xlsx')->chunk(500, function ($reader) {

    //         foreach ($reader as $book) {
    //             //dd($book);
    //             //$nivel_seguro = '11'; //BASE NIVEL Y CONVENIO       
    //             $examen       = Examen::find($book->id);
    //             if (!is_null($examen)) { 

    //                 //$examen_humana = Examen_Nivel::where('nivel', $nivel_seguro)->where('id_examen', $book->id)->first();
    //                 // if (is_null($examen_humana)) {
    //                 //     $input_hum = [
    //                 //         'nivel'           => $nivel_seguro,
    //                 //         'id_examen'       => $book->id,
    //                 //         'valor1'          => round($book->valor, 2),
    //                 //         'id_usuariocrea'  => '0951561075',
    //                 //         'id_usuariomod'   => '0951561075', 
    //                 //         'ip_creacion'     => 'CREA_' . $nivel_seguro,
    //                 //         'ip_modificacion' => '::1',
    //                 //     ];
    //                 //     //dd($input_hum);
    //                 //     Examen_Nivel::create($input_hum);
    //                 //     //$creado++;
    //                 // } else {
    //                     $input_hum = [
    //                         'tiempos'          => $book->tiempo,
    //                         'id_usuariomod'   => '0951561075',
    //                         'ip_creacion'     => 'MODIFICA_TIEMPO_MSV',

    //                     ];
    //                     $examen->update($input_hum);
    //                     //$actualizado++;
    //                 //}
    //             }
    //         }

    //         /*Log_usuario::create([
    //     'id_usuario'  => '0922290697',
    //     'ip_usuario'  => 'SISTEMA',
    //     'descripcion' => 'CARGA REFERIDOS',
    //     'dato_ant1'   => 'creados: ' . $creado . ' -actualizados: ' . $actualizado,
    //     ]);*/
    //     });

    //     return "oki walter tiempo examen 29052023";
    // }


    /* public function importar_valores_seguro()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $n93 = Examen_Nivel::where('nivel',87)->get();
        foreach($n93 as $n){
            $examen = Examen_Nivel::where('nivel', 89)->where('id_examen', $n->id_examen)->first();
            $e = Examen::find($n->id_examen);
            if (is_null($examen)) {
                $input_hum = [
                    'nivel'           => 89,
                    'id_examen'       => $n->id_examen,
                    'valor1'          => round($e->valor * 0.9, 2),
                    'id_usuariocrea'  => '0922290697',
                    'id_usuariomod'   => '0922290697',
                    'ip_creacion'     => 'CREA_310123_89',
                    'ip_modificacion' => '::1',   
                ];
                //dd($input_hum); 
                Examen_Nivel::create($input_hum);
                //$creado++;
            }    
        }
        dd($n93);

        //ini_set('max_execution_time', 100);
 
        Excel::filter('chunk')->load('subir_velez.xlsx')->chunk(500, function ($reader) {

            foreach ($reader as $book) { 
                //dd($book);
                $nivel_seguro = '97'; //BASE NIVEL Y CONVENIO       
                $examen       = Examen::find($book->id);
                if (!is_null($examen)) { 

                    $examen_humana = Examen_Nivel::where('nivel', $nivel_seguro)->where('id_examen', $book->id)->first();
                    if (is_null($examen_humana)) {
                        $input_hum = [
                            'nivel'           => $nivel_seguro,
                            'id_examen'       => $book->id,
                            'valor1'          => round($book->valor, 2),
                            'id_usuariocrea'  => '0922290697',
                            'id_usuariomod'   => '0922290697',
                            'ip_creacion'     => 'CREA_220323_' . $nivel_seguro,
                            'ip_modificacion' => '::1', 
                        ];
                        //dd($input_hum); 
                        Examen_Nivel::create($input_hum);
                        //$creado++;
                    } else {
                        $input_hum = [
                            'valor1'          => round($book->valor, 2),
                            'id_usuariomod'   => '0922290697',
                            'ip_creacion'     => 'MOD_220323_' . $nivel_seguro,
                            'ip_modificacion' => '::1',
                        ];
                        $examen_humana->update($input_hum);
                        //$actualizado++;
                    }
                }
            }

        });

        return "oki walter 1702";
    }*/

    // public function importar_valores_seguro()
    // {

    //     if ($this->rol()) {
    //         return response()->view('errors.404');
    //     }

    //     ini_set('max_execution_time', 100);

    //     Excel::filter('chunk')->load('pvp_quito.xlsx')->chunk(500, function ($reader) {

    //         foreach ($reader as $book) {
    //             //dd($book);

    //             $examen       = Examen::find($book->id);
    //             if (!is_null($examen)) {

    //                     $input_hum = [
    //                         'valor'          =>  round($book->valor, 2),
    //                         'id_usuariomod'   => '0951561075',
    //                         'ip_creacion'     => 'MODIFICA_PVP',
    //                         'ip_modificacion' => '::1',
    //                     ];
    //                     $examen->update($input_hum);
    //                     //$actualizado++;

    //             }
    //         }

    //         /*Log_usuario::create([
    //     'id_usuario'  => '0922290697',
    //     'ip_usuario'  => 'SISTEMA',
    //     'descripcion' => 'CARGA REFERIDOS',
    //     'dato_ant1'   => 'creados: ' . $creado . ' -actualizados: ' . $actualizado,
    //     ]);*/
    //     });

    //     return "oki";
    // }


    public function traslados()
    {
        $empPrueba = "0916293723001";
        $empQuito = "1793135579001";
        $productos = Ct_productos::where('id_empresa', $empPrueba)->get();
        foreach ($productos as $producto) {
            $produc = Ct_productos::where("codigo", $producto->codigo)->where("id_empresa", $empQuito)->first();
            if (is_null($produc)) {
                $arrayProduc = $producto["attributes"];

                unset($arrayProduc["id"]);
                $arrayProduc["id_empresa"] = $empQuito;
                //dd($arrayProduc);
                Ct_productos::create($arrayProduc);
            }
        }

        dd("ok gracias amigo");
    }

    public function masivoArreglarPedido()
    {

        $movimiento = Movimiento::all();

        foreach ($movimiento as $mov) {
            $detmov = InvDetMovimientos::where("id_detalle_pedido", $mov->id)->get();

            foreach ($detmov as $det) {
                $det->id_pedido = $mov->id_pedido;
                $det->ip_modificacion = "masivo arregla id_pedido";
                $det->save();
            }
        }
        dd('ok gracias amigo');
    }

    public function traslado_productos()
    {
        $empPrueba = "0916293723001";
        $empQuito = "1793135579001";
        $productos = Ct_productos::where('id_empresa', $empQuito)->where('ip_creacion', '!=', 'masivo_quito')->get();
        $mover = [];
        foreach ($productos as $value) {

            $bandera = false;
            $ventas = DB::table('ct_ventas as vent')
                ->join('ct_detalle_venta as det_v', 'vent.id', 'det_v.id_ct_ventas')
                ->where('det_v.id_ct_productos', $value->codigo)
                ->where('vent.id_empresa', $empQuito)
                ->first();

            if (!is_null($ventas)) {
                $bandera = true;
            }
            if ($bandera == false) {
                $tarifarios = Ct_Productos_Tarifario::where('id_producto', $value->id)->first();
                if (!is_null($tarifarios)) {
                    $bandera = true;
                }
            }

            if ($bandera == false) {
                $paquetes = Ct_productos_paquete::where('id_producto', $value->id)->first();
                if (!is_null($paquetes)) {
                    $bandera = true;
                }
            }

            if ($bandera == false) {
                $ven_orden =  DB::table('ct_ven_orden as ven_ord')
                    ->join('ct_ven_orden_detalle as detalle', 'ven_ord.id', 'detalle.id_ct_ven_orden')
                    ->where('detalle.id_ct_productos', $value->codigo)
                    ->where('ven_ord.id_empresa', $empQuito)
                    ->first();
                if (!is_null($ven_orden)) {
                    $bandera = true;
                }
            }

            if ($bandera == false) {
                $value->ip_creacion = $value->ip_creacion . "-Mover";
                $value->id_empresa  = $empPrueba;
                $value->codigo  = $value->codigo . "-MOVER";

                $value->save();
                array_push($mover, $value->id);
            }
        }
        dd("ok gracias amigo", $mover);
    }

    public function masivoProductoRe()
    {
        $productos = Producto::where('estado', 1)->get();
        Excel::create('Productos', function ($excel) use ($productos) {
            $excel->sheet('Productos', function ($sheet) use ($productos) {

                $tipos = Insumo_Plantilla_Tipo::all();
                //$comienzo = 2;
                $titulo['background-color'] = "#000000";
                $titulo['color'] = "#FCFCFC";
                $titulo["data"] = ['ID', 'TIPO'];
                excelCreate::details($sheet, $titulo);
                $comienzo = 2;

                foreach ($tipos as $tipo) {
                    $tipos_body["comienzo"] = $comienzo;
                    $tipos_body["data"] = [$tipo->id, $tipo->nombre];
                    excelCreate::details($sheet, $tipos_body);
                    $comienzo++;
                }

                $tipos_body["data"] = ['0', 'OTROS'];
                excelCreate::details($sheet, $tipos_body);
                $comienzo += 2;


                $data['comienzo'] = $comienzo;
                $data['background-color'] = "#000000";
                $data['color'] = "#FCFCFC";
                $data["data"] = ['ID', 'Cod. Producto', 'Nombre Producto', 'Descrip. Producto', 'ID TIPO', 'Tipo Producto', 'ID Contable', 'Cod. Contable', 'Nombre Contable', 'Descip. Contable'];
                excelCreate::details($sheet, $data);
                $comienzo++;
                foreach ($productos as $producto) {

                    $plantilla_tipo = Insumo_Plantilla_Tipo::find($producto->tipo);
                    $tipo = "Otros";
                    $id_tipo = 0;
                    if (!is_null($plantilla_tipo)) {
                        $tipo = $plantilla_tipo->nombre;
                        $id_tipo = $plantilla_tipo->id;
                    }

                    $ct_producto = ['N/A', 'NO SE ENCUENTRA LIGADO CON CONTABLE', 'NO SE ENCUENTRA LIGADO CON CONTABLE', 'NO SE ENCUENTRA LIGADO CON CONTABLE'];

                    $ct_producto_insumos = Ct_productos_insumos::where('id_insumo', $producto->id)->get();
                    foreach ($ct_producto_insumos as $ct_producto_insumo) {
                        if (!is_null($ct_producto_insumo)) {
                            $ct_contable = Ct_productos::find($ct_producto_insumo->id_producto);
                            if (!is_null($ct_contable)) {
                                $ct_producto = [$ct_contable->id, $ct_contable->codigo, $ct_contable->nombre, $ct_contable->descripcion];
                            } else {
                                $producto['background-color'] = "#D77E73";
                            }
                        } else {
                            $producto['background-color'] = "#D77E73";
                        }

                        $producto["comienzo"] = $comienzo;
                        $producto['data'] = [$producto->id, $producto->codigo, $producto->nombre, $producto->descripcion, $id_tipo, $tipo, $ct_producto[0], $ct_producto[1], $ct_producto[2], $ct_producto[3]];
                        excelCreate::details($sheet, $producto);
                        $comienzo++;
                    }
                }
            });
        })->export('xlsx');
    }


    public function plan_cuentas()
    {
        $empmilagro = "0993342696001";
        $empant = "1391927177001";

        $cuentas = Plan_Cuentas_Empresa::where("id_empresa", $empant)->get();

        foreach ($cuentas as $value) {
            $value->id_empresa = $empmilagro;
            $value->ip_modificacion = "masivo cuentas ruc milagro";
            $value->save();
        }
        dd('oki');
    }


    public function masivo_cuentas_empresa($id_empresa)
    {
        $plan_cuentas = Plan_Cuentas::all();
        $empresa = Empresa::all();
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        foreach ($plan_cuentas as $p) {

            Plan_Cuentas_Empresa::create([
                'id_plan'         => $p->id,
                'id_padre'        => $p->id_padre,
                'nombre'          => $p->nombre,
                'plan'            => $p->id,
                'estado'          => $p->estado,
                'id_empresa'      => $id_empresa,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }

        dd("ok");
    }

    public function excel_cuenta_empresa($nombre)
    {
        Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $empresa = '0993375469001';

            foreach ($reader as $book) {
                $cuenta_empresa = Plan_Cuentas_Empresa::where('id_empresa', $empresa)->where('id_plan', $book->id_plan)->first();

                $arr = [
                    'plan' => $book->plan,
                    'ip_modificacion' => "cambio plan",
                ];

                $cuenta_empresa->update($arr);
            }

            return "ok";
        });
    }

    public function productoSerieDiferente()
    {
        $prod_problem = [];
        $det_movimiento = InvDetMovimientos::get();
        foreach ($det_movimiento as $det) {

            $movimiento = Movimiento::where('serie', $det->serie)->first();
            if (!is_null($movimiento)) {
                if ($movimiento->id_producto != $det->id_producto) {
                    dd("Aquii");

                    array_push($prod_problem, ["serie" => $det->serie, "producto" => "Producto Det: {$det->id_producto} - Producto Mov: {$movimiento->id_producto}"]);
                }
            }
        }
        dd($prod_problem);
    }
    public function masivoguardar_id_cab()
    {
        $id_empresa    = Session::get('id_empresa');
        $series = InvInventarioSerie::where('id_empresa', $id_empresa)->whereNull('id_cab_movimientos')->get();
        foreach ($series as $serie) {
            $det_movimiento = InvDetMovimientos::where('serie', $serie->serie)->first();
            if (!is_null($det_movimiento)) {
                $cabecera_movimiento = $det_movimiento->cabecera;
                $id_cabecera = $cabecera_movimiento->id;
                $array = [
                    'id_cab_movimientos' => $id_cabecera,
                ];
                $serie->update($array);
            }
        }
        dd("oki, gracias amigo");
    }

    public function ncAsientos()
    {
        $c_notas = Ct_Nota_Credito_Clientes::whereIn('id', [572, 573, 574, 575, 576, 577])->get();
        foreach ($c_notas as $c_nota) {
            //asasas
            $asientos = Ct_Asientos_Detalle::where('id_asiento_cabecera', $c_nota->id_asiento_cabecera)->delete();
            $asientos = Ct_Asientos_Cabecera::find($c_nota->id_asiento_cabecera);
            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
            $plan_nota_credito = Plan_Cuentas::find($id_plan_confg);
            $idusuario = $c_nota->id_usuariomod;

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $c_nota->id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_nota_credito->id,
                'descripcion'         => $plan_nota_credito->nombre,
                'fecha'               => $asientos->fecha_asiento,
                'haber'               => $c_nota->total_credito,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => "1",
                'ip_modificacion'     => "1",
            ]);

            //$plan_nota_credito2 = Plan_Cuentas::where('id', '4.1.07.01')->first();.
            $id_plan_confg = LogConfig::busqueda('4.1.07.01');
            $plan_nota_credito2 = Plan_Cuentas::find($id_plan_confg);

            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $c_nota->id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_nota_credito2->id,
                'descripcion'         => $plan_nota_credito2->nombre,
                'fecha'               => $asientos->fecha_asiento,
                'debe'                => $c_nota->sub_sin_imp,
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => "1",
                'ip_modificacion'     => "1",
            ]);

            if ($c_nota->impuesto > 0) {
                // 2.01.07.01.01
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $c_nota->id_asiento_cabecera,
                    'id_plan_cuenta'      => '2.01.07.01.01',
                    'descripcion'         => 'IVA en Ventas de Bienes/Servicios',
                    'fecha'               => $asientos->fecha_asiento,
                    'debe'                => $c_nota->impuesto,
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => "1",
                    'ip_modificacion'     => "1",
                ]);
            }
        }

        dd("Oki gracias amigo");
    }

    public function actualizar_valores_ap()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('valores_ap.xlsx')->chunk(250, function ($reader) {
            foreach ($reader as $book) {
                // dd($book);
                $procedimiento = Ap_Procedimiento::find($book->id);
                if (!is_null($procedimiento)) {
                    $data_insumos = [
                        'descripcion' => $book->descripcion,
                        'valor' => $book->valor,
                        'ip_creacion' => 'ACT2022',
                        'id_usuariomod' => '0941096372',
                    ];
                    $procedimiento->update($data_insumos);
                }
            }
        });
        return "oki";
    }

    public function masivo_medicinas()
    {

        Excel::filter('chunk')->load('medicinas.xlsx')->chunk(600, function ($reader) {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;

            foreach ($reader as $book) {
                $id = $book->id;
                $precio = $book->precio;
                //dd($book);
                $ap_procedimiento = ApProcedimiento::find($id);
                if (!is_null($ap_procedimiento)) {

                    $ap_procedimiento->update([
                        'valor' => $precio,
                        'ip_creacion' => 'ACTU06012023',
                    ]);
                }
            }
        });

        return "ok";
    }
    public function vt_masivo_productos()
    {
        $detalles = Planilla_Detalle::all();
        foreach ($detalles as $detalle) {
            $producto = Producto::where('codigo', $detalle->codigo)->where('estado', 1)->first();
            if (!is_null($producto)) {
                $id_producto = $producto->id;
                $productos_insumos = Ct_productos_insumos::where('id_insumo', $id_producto)->first();
                if (!is_null($productos_insumos)) {
                    $id_ct_producto = $productos_insumos->id_producto;
                    $ap_procedimiento = ApProcedimiento::where('id_producto', $id_ct_producto)->where('estado', 1)->first();
                    if (!is_null($ap_procedimiento)) {
                        $detalle->update([
                            'id_ct_producto'        => $id_ct_producto,
                            'id_producto'           => $id_producto,
                            'id_ap_procedimiento'   => $ap_procedimiento->id,
                            'valor_ap_procedimiento' => $ap_procedimiento->valor,
                            'total_proyeccion'      => $detalle->cantidad * $ap_procedimiento->valor,
                        ]);
                    }
                }
            }
        }
        $planillas = Planilla::all();
        foreach ($planillas as $planilla) {
            $suma_detalles = $planilla->detalles->where('estado', 1)->where('check', 1)->sum('total_proyeccion');
            $planilla->update([
                'total_estimado' => $suma_detalles,
            ]);
        }
    }

    public function importar_valorespago()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        ini_set('max_execution_time', 100);

        Excel::filter('chunk')->load('valor_pago.xlsx')->chunk(600, function ($reader) {


            foreach ($reader as $book) {
                //dd($book->cedula);
                $nomina = Ct_Nomina::where('id_user', $book->cedula)->where('estado', 1)->first();
                if (!is_null($nomina)) {
                    $acumulados = Ct_Rh_Acumulados::where('id_empresa', $nomina->id_empresa)
                        ->where('estado', 1)
                        ->where('id_nomina', $nomina->id)
                        ->first();

                    //dd($book->valor);
                    if (!is_null($acumulados)) {

                        $acumulados->valor_pago  = round($book->valor_pago, 2);
                        $acumulados->save();
                        //dd($input);
                        // $acumulados->update($input);
                    }
                }
            }
        });

        return "ok";
    }

    public function importar_examenes_comisiones($id_doctor, $tipo = 'e')
    {
        try {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            ini_set('max_execution_time', 300);
            $contador = 0;
            $total_filas = 0;
            DB::beginTransaction();
            //$total_filas = Excel::filter('chunk')->load('Lista_examen.xlsx')->setActiveSheetIndex(0)->getHighestRow();
            Excel::filter('chunk')->load('Lista_Examenes.xlsx')->chunk(100, function ($reader) use ($contador, $id_doctor, $tipo) {
                foreach ($reader as $book) {
                    $id_examen         = (int)$book['id'];
                    $pvp         = $book['pvp_particular'];
                    $convenio_de_comision = $book['convenio_de_comision'];
                    if ($pvp > 0) {
                        $porcentaje = $book['porcentaje']; //($convenio_de_comision * 100)/$pvp;
                        if (!is_null($id_examen) && $id_examen != '') {
                            if ($tipo == 'S' || $tipo == 's') { //staff
                                $examen_comision = Examen_Comision::where('id_examen', $id_examen)->where('id_doctor', $id_doctor)->first();
                                if (!is_null($examen_comision)) {
                                    $input = [
                                        'valor_comision'  => $porcentaje,
                                        'id_usuariomod'   => '0950457978',
                                        'ip_modificacion' => 'CARGA_MASIVA',
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ];
                                    $examen_comision->update($input);
                                } else {
                                    $input = [
                                        'id_examen' => $id_examen,
                                        'id_doctor_externo' => null,
                                        'id_doctor' => $id_doctor,
                                        'valor_comision'  => $porcentaje,
                                        'estado'          => 1,
                                        'id_usuariocrea'  => '0950457978',
                                        'id_usuariomod'   => '0950457978',
                                        'ip_creacion'     => 'CARGA_MASIVA',
                                        'ip_modificacion' => 'CARGA_MASIVA',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ];
                                    Examen_Comision::create($input);
                                }
                            } else { //externo
                                $examen_comision = Examen_Comision::where('id_examen', $id_examen)->where('id_doctor_externo', $id_doctor)->first();
                                if (!is_null($examen_comision)) {
                                    $input = [
                                        'valor_comision'  => $porcentaje,
                                        'id_usuariomod'   => '0950457978',
                                        'ip_modificacion' => 'CARGA_MASIVA',
                                        'updated_at' => date('Y-m-d H:i:s'),
                                    ];
                                    $examen_comision->update($input);
                                } else {
                                    $input = [
                                        'id_examen' => $id_examen,
                                        'id_doctor_externo' => $id_doctor,
                                        'id_doctor' => null,
                                        'valor_comision'  => $porcentaje,
                                        'estado'          => 1,
                                        'id_usuariocrea'  => '0950457978',
                                        'id_usuariomod'   => '0950457978',
                                        'ip_creacion'     => 'CARGA_MASIVA',
                                        'ip_modificacion' => 'CARGA_MASIVA',
                                    ];
                                    Examen_Comision::create($input);
                                }
                            }
                        } else {
                            // DB::commit();
                            // return ($contador) .' Registros procesados con exito';
                            break;
                        }
                    }
                    $contador = $contador + 1;
                }
            });
            DB::commit();
            return 'Registros procesados con exito';
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine(), 'numero registro' => $contador];
        }
    }

    public function masivo_examenes_tubo()
    {


        try {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            ini_set('max_execution_time', 900);
            $contador = 0;
            $total_filas = 0;
            DB::beginTransaction();

            Excel::filter('chunk')->load('examen.xlsx')->chunk(800, function ($reader) {
                foreach ($reader as $examen) {
                    Examen::where('nombre', $examen->nombre)->first()->update([
                        'cantidad_tubos' => $examen->cantidad_tubos,
                        'indice_tubos' => $examen->indice_tubos,
                        'id_labs_tubo' => $examen->id_labs_tubo,
                        'ip_creacion' => 'TUBOS5523',
                    ]);
                }
            });
            DB::commit();
            return 'Registros procesados con exito';
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
    public static function crear_producto_contable($nombre)
    {
        $contador = 0;
        DB::beginTransaction();
        try {
            Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {
                $insumo = array();
                $existe = array();
                $id_productos = 0;
                $no_vinc = array();
                $creado =  [];
                //$fecha = date('Y-m-d');
                $id_empresa = Session::get('id_empresa');
                foreach ($reader as $book) {
                    //dd($book);
                    $producto = Producto::where('codigo', $book->codigo)->where('estado', 1)->first();
                    $ct_producto = Ct_productos::where('codigo', $book->codigo)->where('id_empresa', $id_empresa)->first();
                    if (!is_null($ct_producto)) {
                        array_push($existe, $ct_producto->codigo);
                    }
                    if (is_null($ct_producto)) {
                        $data = [
                            'codigo'            => $book->codigo,
                            'nombre'            => $book->nombre,
                            //'codigo_barra'      => $book->codigo_barra,
                            'tipo'              => $book->tipo,
                            'descripcion'       => $book->descripcion,
                            //'clase'             => $book->clase,
                            'grupo'             => $book->grupo,
                            //'proveedor'         => $book->proveedor,
                            //'cta_gastos'        => $book->cta_gastos,
                            //'cta_ventas'        => $book->cta_ventas,
                            //'cta_costos'        => $book->cta_costos,
                            //'cta_devolucion' => $book->cta_devolucion,
                            //'reg_serie' => $book->reg_serie,
                            //'mod_precio' => $book->mod_precio,
                            //'mod_desc' => $book->mod_desc,
                            //'marca' => $book->marca,
                            //'modelo' => $book->modelo,
                            //'stock_minimo' => $book->stock_minimo,
                            //'fecha_expiracion' => $book->fecha_expiracion,
                            //'impuesto_iva_compras' => $book->impuesto_iva_compras,
                            //'impuesto_iva_ventas' => $book->impuesto_iva_ventas,
                            //'impuesto_servicio' => $book->impuesto_servicio,
                            //'impuesto_ice' => $book->impuesto_ice,
                            //'clasificacion_impuesto_ice' => $book->clasificacion_impuesto_ice,
                            //'promedio' => $book->promedio,
                            //'reposicion' => $book->reposicion,
                            //'lista' => $book->lista,
                            //'ultima_compra' => $book->ultima_compra,
                            'precio1' => $book->precio,
                            //'precio2' => $book->precio2,
                            //'precio3' => $book->precio3,
                            //'precio4' => $book->precio4,
                            //'promocion' => $book->promocion,
                            //'descuento' => $book->descuento,
                            //'financiero' => $book->financiero,
                            //'ident_paquete' => $book->ident_paquete,
                            'valor_total_paq' => $book->valor_total_paq,
                            'estado_tabla' => 1,
                            'id_usuariocrea' => $book->id_usuariocrea,
                            'id_usuariomod' => $book->id_usuariocrea,
                            'tipo_producto' => $book->tipo_contable,

                            'ip_creacion' => 'Masi_Crear_Prod' . date('Y-m-d'),
                            'ip_modificacion' => 'Masi_Crear_Prod' . date('Y-m-d'),
                            'iva' => $book->iva,
                            'id_empresa' =>  $id_empresa,
                        ];
                        //dd($producto, $ct_producto, $data);
                        $id_producto = Ct_productos::insertGetId($data);
                        /*$codigo = date("ymdHis") . rand(100, 999);
                        $arr_medicamento = [
                            'descripcion'          => $book->nombre,
                            'tipo'                 => 'M',
                            'valor'                => $book->precio,
                            'estado'               => 1,
                            'tipo_i'               => 'F',
                            'codigo'               => $codigo,
                            'ip_creacion'           => '::1',
                            'ip_modificacion'       =>  'Masi_Crear_Prod' . date('Y-m-d'),
                            'id_usuariocrea'       => $book->id_usuariocrea,
                            'id_usuariomod'        => $book->id_usuariocrea,
                            'id_producto'          => $id_producto,
                        ];
                        ApProcedimiento::create($arr_medicamento);*/
                        $id_productos = $id_producto;
                        //array_push($creado, $book->codigo);
                    } else {
                        $id_productos = $ct_producto->id;
                    }
                    if (!is_null($producto)) {
                        try {
                            $producto_insumo = Ct_productos_insumos::where('id_insumo', $producto->id)->where('id_producto', $id_productos)->first();
                            if (is_null($producto_insumo)) {
                                $insumo = [
                                    'codigo_producto'       => $book->codigo,
                                    'id_insumo'             => $producto->id,
                                    'id_producto'           => $id_productos,
                                    'id_usuariocrea'        => $book->id_usuariocrea,
                                    'id_usuariomod'         => $book->id_usuariocrea,
                                    'ip_creacion'           => '::1',
                                    'ip_modificacion'       =>  'Masi_Crear_Prod' . date('Y-m-d'),
                                ];
                                Ct_productos_insumos::create($insumo);
                            }
                        } catch (Exception $e) {
                            echo $e->getMessage() . ' - ' . $e->getLine() . ' - ' . $e->getFile();
                            exit;
                        }
                    } else {
                        //array_push($no_vinc, $book->codigo);
                    }
                }
                //dd("Ok gracias amigo", $existe, $creado);
                DB::commit();
                return ['msj' => 'ok gracias amigo', $existe, $creado];
            });
        } catch (\Exception $e) {
            DB::rollBack();
            echo '<pre>';
            print_r(['error' => $e->getMessage(), 'linea' => $e->getLine(), 'funcion' => 'crea contable', 'numero registro' => $contador]);
            exit;
        }
    }
    public function guardar_excel_producto($nombre, $funcion)
    {
        $msj = '';
        // $funcion = 1;
        if ($funcion == 1) {
            $msj = ImportarController::crear_producto_contable($nombre);
        }
        $contador = 0;
        DB::beginTransaction();
        try {
            $existe = array();
            $creado = [];
            Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {
                //$id_usuario = "0950839209";
                $insumo = array();
                $id_productos = 0;
                foreach ($reader as $book) {
                    //dd($book);
                    $producto = Producto::where('codigo', $book->codigo)->where('estado', 1)->first();
                    $producto_exis = Producto::where('codigo', $book->codigo)->where('estado', 1)->where('ip_modificacion', '<>', 'Masi_Crear_Prod' . date('Y-m-d'))->first();
                    //dd($producto, $book->codigo);
                    $id_empresa = Session::get('id_empresa');
                    $ct_producto = Ct_productos::where('codigo', $book->codigo)->where('id_empresa', $id_empresa)->first();
                    //dd($producto, $ct_producto, $book->codigo);
                    if (!is_null($producto_exis)) {
                        //array_push($existe, $producto_exis->codigo);
                    }
                    if ($producto == '') {
                        $data = [
                            'nombre'            => trim($book->nombre),
                            'descripcion'       => $book->descripcion,
                            'estado'            => 1,
                            'medida'            => is_null($book->medida) ? 0 : $book->medida,
                            'minimo'            => is_null($book->stock_minimo) ? 0 : $book->stock_minimo,
                            'precio_compra'     => $book->precio,
                            //'precio_venta'      => $book->precio_venta,
                            'codigo'            => $book->codigo,
                            'codigo_iess'       => $book->codigo_iess,
                            'despacho'          => is_null($book->despacho) ? 0 : $book->despacho,
                            'cantidad'          => $book->cantidad,
                            'tipo_producto'     => $book->tipo_producto,
                            'cantidad_unidad'   => $book->cantidad_unidad,
                            'id_marca'          => $book->id_marca,
                            'registro_sanitario' => is_null($book->registro_sanitario) ? 0 : $book->registro_sanitario,
                            'usos'              => $book->usos,
                            'kardex_uso'        => $book->kardex_uso,
                            'cantidad_usos_kardex' => $book->cantidad_usos_kardex,
                            'codigo_siempre'    => $book->codigo_siempre,
                            'id_usuariocrea'    => $book->id_usuariocrea,
                            'id_usuariomod'     => $book->id_usuariocrea,
                            'ip_creacion'       =>  'Masi_Crear_Prod' . date('Y-m-d'),
                            'ip_modificacion'   =>  'Masi_Crear_Prod' . date('Y-m-d'),
                            'iva'               => $book->iva,
                            'descuento'         => is_null($book->descuento) ? 0 : $book->descuento,
                            'tipo'              => is_null($book->tipo) ? 0 : $book->tipo,
                            'farmacia'          => is_null($book->farmacia) ? 0 : $book->farmacia,
                        ];
                        //dd($producto, $ct_producto, $data);
                        $id_producto = Producto::insertGetId($data);
                        $id_productos = $id_producto;
                    } else {
                        $id_productos = $producto->id;
                    }
                    if (!is_null($ct_producto)) {
                        $producto_insumo = Ct_productos_insumos::where('id_insumo', $id_productos)->where('id_producto', $ct_producto->id)->first();
                        if (is_null($producto_insumo)) {
                            $insumo = [
                                'codigo_producto'       => $book->codigo,
                                'id_insumo'             => $id_productos,
                                'id_producto'           => $ct_producto->id,
                                'id_usuariocrea'        => $book->id_usuariocrea,
                                'id_usuariomod'         => $book->id_usuariocrea,
                                'ip_creacion'           => '::1',
                                'ip_modificacion'       =>  'Masi_Crear_Prod' . date('Y-m-d'),
                            ];
                            Ct_productos_insumos::create($insumo);
                        }
                    }
                }
                //dd("Ok gracias amigo", $existe, $creado);
            });
            DB::commit();
            return ['msj' => 'ok gracias amigo', $existe, $creado, 'mensaje' => $msj];
        } catch (\Exception $e) {
            DB::rollBack();
            echo '<pre>';
            print_r(['error' => $e->getMessage(), 'linea' => $e->getLine(), 'funcion' => 'guardar excel insumo', 'numero registro' => $contador]);
        }
    }

    public function actualiza_serie_human()
    {
        $id_empresa    = Session::get('id_empresa');
        $resp       = array();
        DB::beginTransaction();
        try {

            $series = InvInventarioSerie::where('ip_creacion', 'KXU')->where('id_empresa', $id_empresa)->select('serie', 'id_producto')->groupBy('serie', 'id_producto')->get();
            //dd($series);
            //$series = InvInventarioSerie::where('serie','20230624175936112')->where('ip_creacion','KXU')->where('id_empresa',$id_empresa)->select('serie','id_producto')->groupBy('serie','id_producto')->get();
            //dd($series);
            foreach ($series as $serie) {
                $random = rand(1000, 9999);
                $serie_nueva = $serie->serie . $random;
                //VALIDA EXISTE LA NUEVA SERIE
                $nserie = InvInventarioSerie::where('serie', $serie_nueva)->where('id_producto', $serie->id_producto)->where('id_empresa', $id_empresa)->first();
                $ndetmov = InvDetMovimientos::where('serie', $serie_nueva)->where('id_producto', $serie->id_producto)->first();
                $nmov = Movimiento::where('serie', $serie_nueva)->where('id_producto', $serie->id_producto)->first();
                $nkar = InvKardexNuevo::where('serie', $serie_nueva)->where('id_producto', $serie->id_producto)->where('id_empresa', $id_empresa)->first();

                if (is_null($nserie) and is_null($ndetmov) and is_null($nmov) and is_null($nkar)) {

                    $series1 = InvInventarioSerie::where('serie', $serie->serie)->where('id_producto', $serie->id_producto)->where('id_empresa', $id_empresa)->get();
                    foreach ($series1 as $seri) {
                        $seri->update([
                            'serie' => $serie_nueva,
                            'ip_creacion' => 'KXU_2',
                        ]);
                    }


                    $detalles_mov = InvDetMovimientos::where('serie', $serie->serie)->where('id_producto', $serie->id_producto)->get();
                    foreach ($detalles_mov as $det_mov) {
                        $det_mov->update([
                            'serie' => $serie_nueva,
                            'ip_creacion' => 'KXU_2',
                        ]);
                    }

                    $movimientos = Movimiento::where('serie', $serie->serie)->where('id_producto', $serie->id_producto)->get();
                    foreach ($movimientos as $mov) {
                        $mov->update([
                            'serie' => $serie_nueva,
                            'ip_creacion' => 'KXU_2',
                        ]);
                    }

                    $kardexes = InvKardexNuevo::where('serie', $serie->serie)->where('id_producto', $serie->id_producto)->where('id_empresa', $id_empresa)->get();
                    foreach ($kardexes as $kardex) {
                        $kardex->update([
                            'serie' => $serie_nueva,
                            'ip_creacion' => 'KXU_2',
                        ]);
                    }
                }
            }
            DB::commit();
            $resp['status']  = 'oki';
            $resp['message'] = 'Oki';
        } catch (\Exception $e) {
            DB::rollBack();
            $resp['status'] = 'error';
            $resp['message'] = 'error: ' . $e->getMessage() . ' line:' . $e->getLine();
        }
        return response()->json($resp);
    }
    public function actualiza_valores_ap_nivel_2023()
    {
        try {

            DB::beginTransaction();

            Excel::filter('chunk')->load('tarifario_ap.xlsx')->chunk(800, function ($reader) {
                //dd($reader);
                foreach ($reader as $book) {
                    //dd($book);
                    $id_ap = $book->id_ap;
                    $nivel = $book->id_nivel_ap;
                    $uvr1 = $book->uvr1;
                    $tarifario = $book->tarifario;

                    //$ap_nivel = ApProcedimientoNivel::where('id_procedimiento', $id_ap)->where('cod_conv', $nivel)->first();
                    $ap_nivel = ApProcedimientoNivel::where('codigo', $tarifario)->where('cod_conv', $nivel)->first();

                    if (!is_null($ap_nivel)) {
                        $input = [
                            'uvr1'           => $uvr1,
                            'ip_creacion'    => 'ACT_VAL_ACT_',
                        ];
                        $ap_nivel->update($input);
                    }
                }
            });
            DB::commit();
            return 'Registros procesados con exito';
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function actualiza_valores_exam_nivel_2023()
    {
        try {

            DB::beginTransaction();

            Excel::filter('chunk')->load('tarifario_exam.xlsx')->chunk(800, function ($reader) {
                foreach ($reader as $book) {
                    $id_ex = $book->id_ex;
                    $nivel = $book->nivel;
                    $valor1 = $book->valor1;

                    $ex_nivel = Examen_Nivel::where('id_examen', $id_ex)->where('nivel', $nivel)->first();

                    if (!is_null($ex_nivel)) {
                        $input = [
                            'valor1'           => round($valor1, 4),
                            'ip_creacion'    => 'ACT_VAL_ACT_',
                        ];
                        $ex_nivel->update($input);
                    }
                }
            });
            DB::commit();
            return 'Registros procesados con exito';
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function subir_procedimientos()
    {
        DB::beginTransaction();
        try {
            Excel::filter('chunk')->load('procedimiento.xlsx')->chunk(800, function ($reader) {
                foreach ($reader as $book) {
                    $procedimiento = Procedimiento::where('nombre', 'like', '%' . $book->nombre . '%')->where('estado', 1)->first();
                    if (is_null($procedimiento)) {
                        Procedimiento::create([
                            'nombre'    => $book->nombre,
                            'observacion' => $book->nombre,
                            'id_grupo_procedimiento' => $book->id_grupo_procedimiento,
                            'id_usuariocrea' => Auth::user()->id,
                            'id_usuariomod' => Auth::user()->id,
                            'ip_creacion' => 'masivo procedimientos 1412',
                            'ip_modificacion' => 'masivo procedimientos 1412',
                        ]);
                    }
                }
            });
            DB::commit();
            return "Creados 14/12/2023";
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function importar_valor_contable()
    {
        try {
            if ($this->rol()) {
                return response()->view('errors.404');
            }
            ini_set('max_execution_time', 100);
            $fileName = 'public/valor_contable15.xlsx';
            $contador = 0;
            $total_filas = 0;
            DB::beginTransaction();
            $total_filas = Excel::filter('chunk')->load($fileName)->setActiveSheetIndex(0)->getHighestRow();
            Excel::filter('chunk')->load($fileName)->chunk($total_filas, function ($reader) use ($contador) {
                foreach ($reader as $book) {
                    $numero_fac = $book['numero'];
                    $nuevo_valor = $book['valor'];
                    if ($nuevo_valor > 0) {
                        $factura = Ct_ventas::where('nro_comprobante', $numero_fac)->first();
                        if (!is_null($factura)) {
                            $input = [
                                'valor_contable' =>  round($nuevo_valor, 2),
                                'id_usuariomod' =>  Auth::user()->id,
                                'ip_modificacion' => 'CARGA_MASIVA_' . date('YmdHi'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                            $factura->update($input);
                            $contador = $contador + 1;
                        }
                    }
                }
            });
            DB::commit();
            //echo 'Total registros procesados con exito ' . $contador;
            return 'Registros procesados con exito';
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine(), 'numero registro' => $contador];
        }
    }
}
