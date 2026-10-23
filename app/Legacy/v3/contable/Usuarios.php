<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\User;
use Sis_medico\v3\contable\LogUsuario;
use Sis_medico\v3\contable\UserEspe;

class Usuarios extends Model
{
    private static $modelo = 'Usuarios';
    protected $table = 'users';
    protected $primaryKey  = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $keyType = 'string';

    public static function saveUsuario($r)
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
            if ($usuarios == '') {
                $input = [
                    'id'                    => $r->id,
                    'nombre1'               => strtoupper($r->nombre1),
                    'nombre2'               => strtoupper($r->nombre2),
                    'apellido1'             => strtoupper($r->apellido1),
                    'apellido2'             => strtoupper($r->apellido2),
                    'id_pais'               => $r->pais,
                    'ciudad'                => $ciudad,
                    'id_ciudad_usuario'     => $r->id_ciudad,
                    'direccion'             => strtoupper($r->direccion),
                    'telefono1'             => $r->telefono1,
                    'telefono2'             => $r->telefono2,
                    'ocupacion'             => strtoupper($r->ocupacion),
                    'fecha_nacimiento'      => $r->fecha_nacimiento,
                    'id_tipo_usuario'       => $r->id_tipo_usuario,
                    'email'                 => $r->email,
                    'color'                 => $r->color,
                    'new_system'            => $permiso_v3,
                    'urbanizacion'          => strtoupper($r->urbanizacion),
                    'etapa'                 => strtoupper($r->etapa),
                    'manzana'               => strtoupper($r->manzana),
                    'villa'                 => strtoupper($r->villa),
                    'referencia_direccion'  => strtoupper($r->referencia),
                    'password'              => bcrypt($r->password),
                    'tipo_documento'        => 1,
                    'estado'                => 1,
                    'imagen_url'            => ' ',
                    'ip_creacion'           => $ip,
                    'ip_modificacion'       => $ip,
                    'id_usuariocrea'        => $idusuario,
                    'id_usuariomod'         => $idusuario,
                ];
                Usuarios::insertGetId($input);
                $log_usuario = [
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip,
                    'descripcion' => "CREA NUEVO USUARIO",
                    'dato_ant1'   => $r->id,
                    'dato1'       => strtoupper($r->nombre1) . " " . strtoupper($r->nombre2) . " " . strtoupper($r->apellido1) . " " . strtoupper($r->apellido2),
                    'dato_ant2'   => "TIPO USUARIO: " . $r->id_tipo_usuario,
                ];
                LogUsuario::insertGetId($log_usuario);
                if ($r->id_tipo_usuario == '3') {
                    $user_espe = [
                        'usuid'            => $r->id,
                        'espid'            => $r->especialidad,
                        'ip_creacion'      => $ip,
                        'ip_modificacion'  => $ip,
                        'id_usuariocrea'   => $idusuario,
                        'id_usuariomod'    => $idusuario,
                    ];
                    UserEspe::insertGetId($user_espe);
                }
                $r->c = 'Configuraciones';
                $r->s = 'saveUsuario';
                $r->d = $origin['d'];
                $r->m = Usuarios::$modelo;
                $r->o = 'Se creo el menu con id.: ' . $r->id;
                $cont++;
            } else {
                $tipoUsuario = TipoUsuario::where('id', $r->id_tipo_usuario)
                    ->where('nombre', 'LIKE', '%PACIENT%')
                    ->first();
                if ($tipoUsuario != '') {
                    PermisosUsuario::where('id_usuario_permiso', $r->id)
                        ->where('id_empresa_permiso', session('id_empresa'))
                        ->update(['id_submenu_permiso' => null]);
                }
                if ($r->password == '') {
                    $input_act = [
                        'id'                    => $r->id,
                        'nombre1'               => strtoupper($r->nombre1),
                        'nombre2'               => strtoupper($r->nombre2),
                        'apellido1'             => strtoupper($r->apellido1),
                        'apellido2'             => strtoupper($r->apellido2),
                        'id_pais'               => $r->pais,
                        'color'                 => $r->color,
                        'new_system'            => $permiso_v3,
                        'urbanizacion'          => strtoupper($r->urbanizacion),
                        'etapa'                 => strtoupper($r->etapa),
                        'manzana'               => strtoupper($r->manzana),
                        'villa'                 => strtoupper($r->villa),
                        'referencia_direccion'  => strtoupper($r->referencia),
                        'ciudad'                => $ciudad,
                        'id_ciudad_usuario'     => $r->id_ciudad,
                        'direccion'             => strtoupper($r->direccion),
                        'telefono1'             => $r->telefono1,
                        'telefono2'             => $r->telefono2,
                        'ocupacion'             => strtoupper($r->ocupacion),
                        'fecha_nacimiento'      => $r->fecha_nacimiento,
                        'id_tipo_usuario'       => $r->id_tipo_usuario,
                        'email'                 => $r->email,
                        'estado'                => $r->estado,
                        'ip_modificacion'       => $ip,
                        'id_usuariomod'         => $idusuario,
                    ];
                } else {
                    $input_act = [
                        'id'                    => $r->id,
                        'nombre1'               => strtoupper($r->nombre1),
                        'nombre2'               => strtoupper($r->nombre2),
                        'apellido1'             => strtoupper($r->apellido1),
                        'apellido2'             => strtoupper($r->apellido2),
                        'id_pais'               => $r->pais,
                        'color'                 => $r->color,
                        'new_system'            => $permiso_v3,
                        'urbanizacion'          => strtoupper($r->urbanizacion),
                        'etapa'                 => strtoupper($r->etapa),
                        'manzana'               => strtoupper($r->manzana),
                        'villa'                 => strtoupper($r->villa),
                        'referencia_direccion'  => strtoupper($r->referencia),
                        'password'              => bcrypt($r->password),
                        'ciudad'                => $ciudad,
                        'id_ciudad_usuario'     => $r->id_ciudad,
                        'direccion'             => strtoupper($r->direccion),
                        'telefono1'             => $r->telefono1,
                        'telefono2'             => $r->telefono2,
                        'ocupacion'             => strtoupper($r->ocupacion),
                        'fecha_nacimiento'      => $r->fecha_nacimiento,
                        'id_tipo_usuario'       => $r->id_tipo_usuario,
                        'email'                 => $r->email,
                        'estado'                => $r->estado,
                        'ip_modificacion'       => $ip,
                        'id_usuariomod'         => $idusuario,
                    ];
                }
                Usuarios::where('id', $r->id)->update($input_act);
                $Log_usuario_act = [
                    'id_usuario'  => $idusuario,
                    'ip_usuario'  => $ip,
                    'descripcion' => "ACTUALIZA DATOS DE USUARIO",
                    'dato_ant1'   => $usuarios->id,
                    'dato1'       => $r->id,
                    'dato_ant2'   => $usuarios->nombre1 . " " . $usuarios->nombre2 . " " . $usuarios->apellido1 . " " . $usuarios->apellido2,
                    'dato2'       => strtoupper($r->nombre1) . " " . strtoupper($r->nombre2) . " " . strtoupper($r->apellido1) . " " . strtoupper($r->apellido2),
                    'dato_ant3'   => "ACTUALIZA " . $usuarios->id . "ESTADO: " . $usuarios->estado . " TIPO USUARIO: " . $usuarios->id_tipo_usuario,
                    'dato3'       => "ACTUALIZA " . $r->id . "ESTADO: " . $r->estado . " TIPO USUARIO: " . $r->id_tipo_usuario,
                ];
                LogUsuario::insertGetId($Log_usuario_act);
                if ($r->id_tipo_usuario == '3') {
                    $especialidad_user = UserEspe::where('usuid', $data['id'])->first();
                    if ($especialidad_user != '') {
                        $user_espe = [
                            'espid'            => $r->especialidad,
                            'ip_modificacion'  => $ip,
                            'id_usuariomod'    => $idusuario,
                        ];
                        UserEspe::where('usuid', $r->id)->update($user_espe);
                    } else {
                        $user_espe = [
                            'usuid'            => $r->id,
                            'espid'            => $r->especialidad,
                            'ip_creacion'      => $ip,
                            'ip_modificacion'  => $ip,
                            'id_usuariocrea'   => $idusuario,
                            'id_usuariomod'    => $idusuario,
                        ];
                        UserEspe::insertGetId($user_espe);
                    }
                }
                $r->c = 'Configuraciones';
                $r->s = 'saveUsuario';
                $r->d = $origin['d'];
                $r->m = Usuarios::$modelo;
                $r->o = 'Se actualizo el usuario con id.: ' . $r->id;
                $cont++;
            }
            $roles = RolesUsuarios::find($r->id_tipo_rol);
            if ($roles != '') {
                $array = json_decode($roles->id_submodulos_rol);
                $search = ["crear_", "actualizar_", "leer_"];
                $replace = ["", "", ""];
                foreach ($array as &$value) {
                    $value = str_replace($search, $replace, $value);
                }
                unset($value);
                $array = array_unique($array);
                $array = array_values($array);
                $pemisos = json_encode($array);
                PermisosUsuario::where('id_empresa_permiso', session('id_empresa'))
                    ->where('id_usuario_permiso', session('id_usuario'))
                    ->update(['id_submenu_permiso' => $pemisos]);
            }
            Auditorias::saveAuditoria($r, DB::getQueryLog());
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
            $r->s = 'saveUsuario';
            $r->d =  $origin['d'];
            $r->m = Usuarios::$modelo;
            $r->o = 'Ocurrio un error: ' . $e->getMessage();
            Auditorias::saveAuditoria($r, DB::getQueryLog());
            return json_encode($arrayRespuesta);
        }
        if ($cont > 0) {
            DB::commit();
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Usuario guardado correctamente',
                'data' => $r->input(),
            ];
            return json_encode($arrayRespuesta);
        }
    }
    public static function estadoUsuario($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Usuarios::where('id', $r->id)->update(['estado' => $estado])) {
                $arrayRespuesta = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Registro guardado correctamente',
                    'data' => $r->input(),
                ];
            } else {
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Error al actualizar el estado del ',
                    'data' => $r->input(),
                ];
            }
        } catch (Exception $e) {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage(),
                'linea' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }
    public static function  buscarUsuario($term = '', $todo = '')
    {
        return User::selectRaw('id, CONCAT(id," | ",nombre1," ",nombre2," ",apellido1," ",apellido2) as text')
            ->where(function ($q) use ($todo) {
                if ($todo == '')
                    $q->where('id_tipo_usuario', 1);
            })
            ->where(function ($q) use ($term) {
                $q->orWhereRaw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido1," ",nombre1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido1," ",nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido2," ",nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido2," ",nombre1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre1," ",apellido1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre1," ",apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre2," ",apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre1," ",nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre2," ",apellido1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido1," ",apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido2," ",apellido1) like "%' . $term . '%"');
                $q->orWhere('nombre1', 'like', '%' . $term . '%');
                $q->orWhere('nombre2', 'like', '%' . $term . '%');
                $q->orWhere('apellido1', 'like', '%' . $term . '%');
                $q->orWhere('apellido2', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })->get();
    }
    public static function getUsuarios($id = '', $r)
    {
        if ($id == '') {
            $columns = ['users.id', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'direccion', 'tu.nombre'];
            $query = self::leftjoin('tipousuario as tu', 'users.id_tipo_usuario', 'tu.id')->select([
                'users.id',
                'users.nombre1',
                'users.nombre2',
                'users.apellido1',
                'users.apellido2',
                'users.direccion',
                'users.email',
                'tu.nombre as tipo_usuario',
                'users.estado',
                DB::raw('if(trim(users.imagen_url) = "", null, users.imagen_url) as imagen_url')
            ]);
            if ($r->input('search.value')) {
                $search = $r->input('search.value');
                $query->where(function ($q) use ($search, $columns) {
                    foreach ($columns as $column) {
                        if (in_array($column, ['users.id', 'users.nombre1', 'users.nombre2', 'users.apellido1', 'users.apellido2', 'users.direccion'])) {
                            $q->orWhereRaw("CONCAT(users.nombre1, ' ', users.nombre2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.id) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido1, ' ', users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.nombre1, ' ', users.nombre2,' ',users.apellido1,' ',users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.nombre1, ' ',users.apellido1,' ',users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.nombre1, ' ', users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.nombre2, ' ', users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido1, ' ', users.apellido2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido1, ' ', users.apellido2,' ',users.nombre1,' ',users.nombre2) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido1, ' ', users.apellido2,' ',users.nombre2,' ',users.nombre1) LIKE ?", ["%{$search}%"])
                                ->orWhereRaw("CONCAT(users.apellido2, ' ', users.apellido1,' ',users.nombre1,' ',users.nombre2) LIKE ?", ["%{$search}%"]);
                        } else {
                            $q->orWhere($column, 'like', "%{$search}%");
                        }
                    }
                });
            }
            $totalFiltered = $query->count();
            if ($r->input('order.0.column') !== null) {
                $orderColumn = $columns[$r->input('order.0.column')];
                $orderDirection = $r->input('order.0.dir');
                $query->orderBy($orderColumn, $orderDirection);
            }
            $start = $r->input('start');
            $length = $r->input('length');
            $data = $query->offset($start)->limit($length)->get();
            return response()->json([
                'draw' => $r->input('draw'),
                'recordsTotal' => self::count(),
                'recordsFiltered' => $totalFiltered,
                'data' => $data
            ]);
        } else {
            return User::leftjoin('new_db_ciudades as c', 'c.id_ciudad', 'users.id_ciudad_usuario')
                ->leftjoin('pais as p', 'p.id', 'c.id_pais_ciudad')
                ->leftjoin('new_db_provincias as pr', 'pr.id_provincia', 'c.id_provincia_ciudad')
                ->leftjoin('new_db_archivos as cedula', function ($q) {
                    $q->on('users.id', 'cedula.id_usuario_archivo')
                        ->where('cedula.tipo_archivo', 'usuarioCedula');
                })
                ->leftjoin('new_db_archivos as certificado', function ($q) {
                    $q->on('users.id', 'certificado.id_usuario_archivo')
                        ->where('certificado.tipo_archivo', 'usuarioVotacion');
                })
                ->leftjoin('user_espe as ue', 'ue.usuid', 'users.id')
                ->leftjoin('especialidad as espe', 'espe.id', 'ue.espid')
                ->where('users.id', $id)
                ->first([
                    'users.id',
                    'users.nombre1',
                    'users.nombre2',
                    'users.apellido1',
                    'users.apellido2',
                    'users.telefono1',
                    'users.telefono2',
                    'users.direccion',
                    'users.email',
                    'users.estado',
                    'users.id_ciudad_usuario',
                    'c.id_pais_ciudad',
                    'c.id_provincia_ciudad',
                    'users.urbanizacion',
                    'users.etapa',
                    'users.manzana',
                    'users.villa',
                    'users.id_tipo_usuario',
                    'users.fecha_nacimiento',
                    'p.nombre as pais',
                    'c.nombre_ciudad as ciudad',
                    'users.cargo',
                    'users.ocupacion',
                    'users.id_pais',
                    'users.ciudad',
                    'users.urbanizacion',
                    'users.etapa',
                    'users.manzana',
                    'users.villa',
                    'users.referencia_direccion',
                    'espe.id as especialidad',
                    'users.color',
                    'users.new_system',
                    'cedula.ruta_archivo as ruta_cedula',
                    'certificado.ruta_archivo as ruta_certificado',
                    'users.imagen_url'
                ]);
        }
    }
    public static function getUsuario()
    {
        $arrayMensajesGenerales = [];
        try {
            return User::leftjoin('new_db_ciudades as c', 'c.id_ciudad', 'users.id_ciudad_usuario')
                ->leftjoin('pais as p', 'p.id', 'c.id_pais_ciudad')
                ->leftjoin('new_db_provincias as pr', 'pr.id_provincia', 'c.id_provincia_ciudad')
                ->leftjoin('new_db_archivos as a', 'users.id', 'a.id_usuario_archivo')
                ->where('users.id', Auth::user()->id)->first([
                    'users.id',
                    'users.nombre1',
                    'users.nombre2',
                    'users.apellido1',
                    'users.apellido2',
                    'users.telefono1',
                    'users.telefono2',
                    'users.direccion',
                    'users.email',
                    'users.estado',
                    'users.id_ciudad_usuario',
                    'c.id_pais_ciudad',
                    'c.id_provincia_ciudad',
                    'users.urbanizacion',
                    'users.etapa',
                    'users.manzana',
                    'users.villa',
                    'users.id_tipo_usuario',
                    'users.fecha_nacimiento',
                    'p.nombre as pais',
                    'c.nombre_ciudad as ciudad',
                    'users.cargo',
                    'users.ocupacion',
                    'users.imagen_url',
                    'a.ruta_archivo as foto'
                ]);
        } catch (Exception $e) {
            $dato = explode('\'', $e->getMessage());
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'model' => Usuarios::$modelo,
                'table' => $dato[1],
                'message' => 'La empresa no tiene configurado el catalogo general',
            ];
            // Obtén el arreglo de mensajes generales desde la configuración
            $mensajesGenerales = config('data.mensajesGenerales', []);

            // Agrega el nuevo mensaje al arreglo
            $mensajesGenerales[] = $arrayRespuesta;

            // Actualiza el valor en la configuración
            config(['data.mensajesGenerales' => $mensajesGenerales]);
        }
        //$data['mensajesGenerales'] = $arrayMensajesGenerales;
    }
    public static function getVendedor($term)
    {
        return Usuarios::selectRaw('id, CONCAT(id," | " , CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2)) as text')
            ->where(function ($q) use ($term) {
                $q->orWhereRaw('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido1," ",apellido2," ",nombre1," ",nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido1," ",nombre1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido1," ",nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido2," ",nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido2," ",nombre1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre1," ",apellido1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre1," ",apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre2," ",apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre1," ",nombre2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(nombre2," ",apellido1) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido1," ",apellido2) like "%' . $term . '%"');
                $q->orWhereRaw('CONCAT(apellido2," ",apellido1) like "%' . $term . '%"');
                $q->orWhere('nombre1', 'like', '%' . $term . '%');
                $q->orWhere('nombre2', 'like', '%' . $term . '%');
                $q->orWhere('apellido1', 'like', '%' . $term . '%');
                $q->orWhere('apellido2', 'like', '%' . $term . '%');
                $q->orWhere('id', 'like', '%' . $term . '%');
            })->where('id_tipo_usuario', 17)->where('estado', 1)->get();
    }
    public static function VendedorCombo($id)
    {
        $combo = '';
        $vendedor = Usuarios::where('id', $id)->first();
        if ($vendedor != '') {
            $combo = '<option value="' . $vendedor->id . '" selected>' . $vendedor->id . ' | ' . $vendedor->apellido1 . ' ' . $vendedor->apellido2 . ' ' . $vendedor->nombre1 . ' ' . $vendedor->nombre2 . '</option>';
        }
        return $combo;
    }
    public static function usuarioRecaudador()
    {
        return Usuarios::where('id_tipo_usuario', 18)
            ->where('estado', 1)->get();
    }
    public static function usuarioVendedor()
    {
        return  Usuarios::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
    }
    public static function buscarCiudad($id)
    {
        $combo = '';
        $ciudad = Ciudades::where('id_ciudad', $id)->first();
        if ($ciudad != '') {
            $combo = '<option value="' . $ciudad->id . '" selected>' . $ciudad->nombre_ciudad . '</option>';
        }
        return $combo;
    }
    public static function buscarProvincia($id)
    {
        $combo = '';
        $provincia = Provincias::where('id_provincia', $id)->first();
        if ($provincia != '') {
            $combo = '<option value="' . $provincia->id . '" selected>' . $provincia->nombre_provincia . '</option>';
        }
        return $combo;
    }
    public static function verificarCorreo($r)
    {
        $arrayRespuesta = [];
        $email = Usuarios::where('email', $r->email)->first([
            'email'
        ]);
        if ($email != '') {
            $arrayRespuesta = [
                'status' => 'error',
                'code' => 500,
                'message' => 'El correo ya se encuentra registrado...',
                'data' => $r->input(),
            ];
        } else {
            $arrayRespuesta = [
                'status' => 'success',
                'code' => 200,
                'message' => '',
                'data' => $r->input(),
            ];
        }
        return json_encode($arrayRespuesta);
    }
}
