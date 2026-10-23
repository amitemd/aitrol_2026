<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\User;

class Nomina extends Model
{
    private static $modelo = 'Nomina';
    protected $table = 'ct_nomina';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function getNomina($r)
    {
        //dd($r);
        if ($r->id == '') {
            return Nomina::where('ct_nomina.id_empresa', session('id_empresa'))
                ->join('users as u','u.id','ct_nomina.id_user')
                ->leftjoin('ct_sucursales as s','s.id','ct_nomina.id_sucursal')
                ->where('ct_nomina.estado', 1)
                ->get([
                    'ct_nomina.id',
                    'ct_nomina.id_user',
                    'ct_nomina.estado',
                    'ct_nomina.cargo',
                    DB::raw('concat(s.codigo_sucursal,"-", s.nombre_sucursal) as codigo_sucursal'),
                    'ct_nomina.cargo',
                    'ct_nomina.sueldo_neto',
                    DB::raw('CASE WHEN ct_nomina.jornada = 0 THEN "COMPLETA"  WHEN ct_nomina.jornada = 1 THEN "MEDIA" ELSE "PARCIAL" END as jornada'),
                    DB::raw('CASE WHEN ct_nomina.fecha_ant IS NULL THEN ct_nomina.fecha_ingreso ELSE ct_nomina.fecha_ant END as fecha_ingreso'),
                    DB::raw('concat(u.apellido1," ",u.apellido2," ",u.nombre1," ",u.nombre2) as nombres'),
                    DB::raw('(SELECT SUM(saldo_total) FROM ct_rh_prestamos WHERE ct_rh_prestamos.id_empl = ct_nomina.id_user) as total_prestamos'),
                    DB::raw('(SELECT SUM(saldo_res) FROM ct_rh_saldos_iniciales WHERE ct_rh_saldos_iniciales.id_empl = ct_nomina.id_user) as total_saldos')
                ]);
        } else {
            return Nomina::find($r->id);
        }
    }
    public static function getEmpleados($id = '')
    {
        if ($id == '') {
            return Nomina::join('users as u','u.id','ct_nomina.id_user')
                ->join('pais as p','p.id','ct_nomina.pais')
                ->join('new_db_catalogo as c','c.valor','ct_nomina.sexo')
                ->leftjoin('ct_rh_nivel_academico as na','na.id','ct_nomina.nivel_academico')
                ->leftjoin('ct_rh_estado_civil as ec','ec.id','ct_nomina.estado_civil')
                ->leftjoin('ct_sucursales as suc','suc.id','ct_nomina.id_sucursal')
                ->leftjoin('ct_rh_valores as val','val.id','ct_nomina.aporte_personal')
                ->where('val.tipo','1')
                ->where('c.id_catalogo_pertenece','1')
                ->where('suc.uso_nomina','1')
                ->where('ct_nomina.id_empresa', session('id_empresa'))
                ->where('ct_nomina.estado', 1)
                ->get([
                    'ct_nomina.id_user as id_user',
                    'ct_nomina.nombres',
                    'ct_nomina.mail_opcional',
                    'ct_nomina.area',
                    'ct_nomina.cargo',
                    'ct_nomina.fecha_ingreso',
                    'ct_nomina.fecha_ant',
                    'ct_nomina.pago_fondo_reserva',
                    'ct_nomina.decimo_tercero',
                    'ct_nomina.decimo_cuarto',
                    'ct_nomina.horario',
                    'ct_nomina.sueldo_neto',
                    'ct_nomina.extension_conyuge',
                    'ct_nomina.pasante',
                    'ct_nomina.sueldo_pasante',
                    'ct_nomina.numero_cuenta',
                    'ct_nomina.funciones',
                    'ct_nomina.jornada',
                    'ct_nomina.horas_mes',
                    'ct_nomina.dias_laborables',
                    'ct_nomina.bono',
                    'ct_nomina.bono_imputable',
                    'ct_nomina.impuesto_renta',
                    'ct_nomina.alimentacion',
                    'ct_nomina.parqueo',
                    'ct_nomina.movilizacion',
                    'ct_nomina.seguro_privado',
                    'ct_nomina.id_sucursal',
                    'val.id as aporte_personal',
                    'ct_nomina.banco',
                    //'suc.id as id_sucursal',
                    'u.email',
                    'u.nombre1',
                    'u.nombre2',
                    'u.apellido1',
                    'u.apellido2',
                    'ct_nomina.estado',
                    'ct_nomina.etnia',
                    'u.ocupacion',
                    'u.telefono1',
                    'u.telefono2',
                    'u.etapa',
                    'u.manzana',
                    'u.villa',
                    'u.fecha_nacimiento',
                    'c.valor as genero',
                    'c.catalogo as sexo',
                    'ct_nomina.check_discapacidad as discapacidad',
                    'na.descripcion as nivel_academico',
                    'ec.descripcion as estado_civil',
                    'p.id as pais',
                    'ct_nomina.numero_cargas',
                    'u.direccion',
                    DB::raw('CONCAT(SUBSTRING(u.nombre1, 1, 1), ". ", u.apellido1) AS usuariocrea'),
                    'ct_nomina.created_at'
                ]);
        } else {
            return Nomina::join('users as u','u.id','ct_nomina.id_user')
                ->join('pais as p','p.id','ct_nomina.pais')
                ->join('new_db_catalogo as c','c.valor','ct_nomina.sexo')
                ->leftjoin('ct_rh_nivel_academico as na','na.id','ct_nomina.nivel_academico')
                ->leftjoin('ct_rh_estado_civil as ec','ec.id','ct_nomina.estado_civil')
                ->leftjoin('ct_sucursales as suc','suc.id','ct_nomina.id_sucursal')
                ->leftjoin('ct_rh_valores as val','val.id','ct_nomina.aporte_personal')
                ->leftjoin('new_db_archivos as cedula', function ($q) {
                    $q->on('u.id', 'cedula.id_usuario_archivo')
                        ->where('cedula.tipo_archivo', 'empleadoCedula');
                })
                ->leftjoin('new_db_archivos as ficha_ocupacional', function ($q) {
                    $q->on('u.id', 'ficha_ocupacional.id_usuario_archivo')
                        ->where('ficha_ocupacional.tipo_archivo', 'empleadoFichaOcupacional');
                })
                ->leftjoin('new_db_archivos as ficha_tecnica', function ($q) {
                    $q->on('u.id', 'ficha_tecnica.id_usuario_archivo')
                        ->where('ficha_tecnica.tipo_archivo', 'empleadoFichaTecnica');
                })
                ->where('val.tipo','1')
                ->where('ct_nomina.id', $id)
                //->where('suc.uso_nomina','1')
                ->where('c.id_catalogo_pertenece','1')
                ->where('ct_nomina.id_empresa', session('id_empresa'))
                ->where('ct_nomina.estado', 1)
                ->first([
                    'ct_nomina.id_user as id_user',
                    'ct_nomina.nombres',
                    'ct_nomina.mail_opcional',
                    'ct_nomina.cargo',
                    'ct_nomina.area',
                    'ct_nomina.fecha_ingreso',
                    'ct_nomina.fecha_ant',
                    'ct_nomina.pago_fondo_reserva',
                    'ct_nomina.decimo_tercero',
                    'ct_nomina.decimo_cuarto',
                    'ct_nomina.horario',
                    'ct_nomina.sueldo_neto',
                    'ct_nomina.extension_conyuge',
                    'ct_nomina.pasante',
                    'ct_nomina.sueldo_pasante',
                    'ct_nomina.numero_cuenta',
                    'ct_nomina.funciones',
                    'ct_nomina.jornada',
                    'ct_nomina.horas_mes',
                    'ct_nomina.dias_laborables',
                    'ct_nomina.bono',
                    'ct_nomina.bono_imputable',
                    'ct_nomina.impuesto_renta',
                    'ct_nomina.alimentacion',
                    'ct_nomina.parqueo',
                    'ct_nomina.movilizacion',
                    'ct_nomina.seguro_privado',
                    'ct_nomina.id_sucursal',
                    'val.id as aporte_personal',
                    'ct_nomina.banco',
                    //'suc.id as id_sucursal',
                    'u.email',
                    'u.nombre1',
                    'u.nombre2',
                    'u.apellido1',
                    'u.apellido2',
                    'ct_nomina.estado',
                    'ct_nomina.etnia',
                    'u.ocupacion',
                    'u.telefono1',
                    'u.telefono2',
                    'u.etapa',
                    'u.manzana',
                    'u.villa',
                    'u.fecha_nacimiento',
                    'c.valor as genero',
                    'c.catalogo as sexo',
                    'ct_nomina.check_discapacidad as discapacidad',
                    'na.id as nivel_academico',
                    'ec.id as estado_civil',
                    'p.id as pais',
                    'ct_nomina.numero_cargas',
                    'cedula.ruta_archivo as ruta_cedula',
                    'ficha_ocupacional.ruta_archivo as ruta_ficha_ocupacional',
                    'ficha_tecnica.ruta_archivo as ruta_ficha_tecnica',
                    'u.direccion',
                ]);
        }
    }
    public static function saveEmpleado($r)
    {
        DB::enableQueryLog();
        DB::beginTransaction();
        $cont = 0;
        try {
            $data = $r->input();
            $origin = $r->input();
            unset($data['d']);
            unset($data['_token']);

            $ip = Utilidades::getRealIP();
            $idusuario = Auth::user()->id;
            $usuarios = Usuarios::find($data['id']);
            $id_empresa = session('id_empresa');
            $ciudad = '';
            if ($r->id_ciudad != '') {
                $ciudades = Ciudades::where('id_ciudad', $r->id_ciudad)->first();
                if ($ciudades) {
                    $ciudad = $ciudades->nombre_ciudad;
                }
            }
            if (is_null($r->new_system)) {
                $permiso_v3 = $usuarios->new_system;
            } else {
                $permiso_v3 = $r->new_system;
            }
            $horas_dia = $data['horas_mes'] / $data['dias_laborables'];
            if ($usuarios == '') {
                $empleado['id_user'] = $data['id_user'];
                $empleado['nombres'] = strtoupper($data['nombre1']) . " " . strtoupper($data['nombre2']) . " " . strtoupper($data['apellido1']) . " " . strtoupper($data['apellido2']);
                $empleado['id_empresa'] = $id_empresa;
                $empleado['sexo'] = $data['genero'];
                $empleado['etnia'] = $data['etnia'];
                $empleado['check_discapacidad'] = $data['discapacidad'];
                $empleado['numero_cargas'] = $data['numero_cargas'];
                $empleado['nivel_academico'] = $data['nivel_academico'];
                $empleado['mail_opcional'] = $data['mail_opcional'];
                $empleado['estado_civil'] = $data['estado_civil'];
                $empleado['area'] = $data['area'];
                $empleado['cargo'] = $data['cargo'];
                $empleado['fecha_ingreso'] = $data['fecha_ingreso'];
                $empleado['pago_fondo_reserva'] = $data['pago_beneficio'];
                $empleado['decimo_cuarto'] = $data['decimo_cuarto'];
                $empleado['decimo_tercero'] = $data['decimo_tercero'];
                $empleado['seguro_privado'] = $data['seguro_privado'];
                $empleado['horario'] = $data['horario'];
                $empleado['bono'] = $data['bono'];
                $empleado['impuesto_renta'] = $data['impuesto_renta'];
                $empleado['alimentacion'] = $data['alimentacion'];
                $empleado['banco'] = $data['banco'];
                $empleado['numero_cuenta'] = $data['numero_cuenta'];
                $empleado['sueldo_neto'] = $data['sueldo_neto'];
                $empleado['estado'] = 1;
                $empleado['ip_creacion']        = $ip;
                $empleado['ip_modificacion']    = $ip;
                $empleado['id_usuariocrea']     = $idusuario;
                $empleado['id_usuariomod']      = $idusuario;
                $empleado['bono_imputable'] = $data['bono_imputable'];
                $empleado['fondos_reseva'] = $data['fondos_reseva'];
                $empleado['aporte_personal'] = $data['aporte_personal']; 
                $empleado['parqueo'] = $data['parqueo']; 
                $empleado['id_sucursal'] = $data['codigo_sucursal']; 
                $empleado['fecha_ant'] = $data['fecha_ant']; 
                $empleado['extension_conyuge'] = $data['extension_conyuge']; 
                $empleado['movilizacion'] = $data['movilizacion']; 
                $empleado['pasante'] = $data['pasante']; 
                $empleado['jornada'] = $data['jornada']; 
                $empleado['funciones'] = $data['funciones'];
                $empleado['sueldo_pasante'] = $data['sueldo_pasante'];
                $empleado['cantidad_horas'] = $horas_dia;
                $empleado['horas_mes'] = $data['horas_mes'];
                $empleado['dias_laborables'] = $data['dias_laborables'];
                $empleado['id_ciudad'] = $data['id_ciudad'];      
                $empleado['pais'] = $data['pais'];
                $empleado['id_provincia'] = $data['id_provincia'];
                                            
                   
                $usuario['id_ciudad_usuario'] = $data['id_ciudad'];        
                $usuario['urbanizacion'] = $data['urbanizacion'];
                $usuario['etapa'] = $data['etapa'];
                $usuario['manzana'] = $data['manzana'];
                $usuario['villa'] = $data['villa'];
                            
                
                $usuario['id'] = $data['id_user'];
                $usuario['nombre1'] = strtoupper($data['nombre1']);
                $usuario['nombre2'] = strtoupper($data['nombre2']);
                $usuario['apellido1'] = strtoupper($data['apellido1']);
                $usuario['apellido2'] = strtoupper($data['apellido2']);
                $usuario['tipo_documento'] = 1;
                $usuario['id_pais'] = 1;
                $usuario['ciudad'] = $data['id_ciudad'];   /// REVISAR GUARDA EL NOMBRE DE LA CIUDAD
                $usuario['direccion'] = $data['direccion'];
                $usuario['email'] = $data['email'];
                $usuario['telefono1'] = $data['telefono1'];
                $usuario['telefono2'] = $data['telefono2'];
                $usuario['ocupacion'] = $data['ocupacion'];
                $usuario['estado'] = 1;
                $usuario['fecha_nacimiento'] = $data['fecha_nacimiento'];
                $usuario['password'] = bcrypt($data['password']);
                $usuario['id_tipo_usuario'] = 19;
                $usuario['uso_laboratorio'] = 1;
                $usuario['cargo'] = $data['cargo'];
                $usuario['app'] = 0 ;

                $usuario['ip_creacion']        = $ip;
                $usuario['ip_modificacion']    = $ip;
                $usuario['id_usuariocrea']     = $idusuario;
                $usuario['id_usuariomod']      = $idusuario;
                
                
                $usuario['tipo_rimpe'] = $data['tipo_rimpe'];
                $usuario['ruta_firma'] = $data['ruta_firma'];
                $usuario['clave_firma'] = $data['clave_firma'];

            }
        } catch (Exception $e) {
            DB::rollBack();
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
            $r->c = 'Configuraciones';
            $r->s = 'saveMenu';
            $r->d = $origin['d'];;
            $r->m = Empresas::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Registro guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
}
