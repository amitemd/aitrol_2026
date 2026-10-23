<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Empresa;

class Empresas extends Model
{
    private static $modelo = 'Empresas';
    protected $table = 'empresa';
    protected $keyType = 'string';
    protected $primaryKey  = 'id';

    public static function estadoEmpresa($r)
    {
        $estado = 1;
        if ($r->estado == 1)
            $estado = 0;
        try {
            if (Empresas::where('id', $r->id)->update(['estado' => $estado])) {
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
    public static function getRepresentante($id)
    {
        $combo = '';
        $user = Usuarios::find($id);
        if ($user != '') {
            $combo = '<option value="' . $user->id . '" selected>' . $user->nombre1 . ' ' . $user->nombre2 . ' ' . $user->apellido1 . ' ' . $user->apellido2 . '</option>';
        }
        return $combo;
    }
    public static function saveEmpresa($r)
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
            $empresa['id'] = $data['id'];
            $empresa['razonsocial'] = $data['razonsocial'];
            $empresa['nombrecomercial'] = $data['nombrecomercial'];
            $empresa['nombre_corto'] = $data['nombre_corto'];
            $empresa['email'] = $data['email'];
            $empresa['web'] = $data['web'];
            $empresa['telefono1'] = $data['telefono1'];
            $empresa['telefono2'] = $data['telefono2'];
            $empresa['id_ciudad'] = $data['id_ciudad'];
            $empresa['direccion'] = $data['direccion'];
            $empresa['urbanizacion'] = $data['urbanizacion'];
            $empresa['etapa'] = $data['etapa'];
            $empresa['manzana'] = $data['manzana'];
            $empresa['villa'] = $data['villa'];
            $empresa['referencia'] = $data['referencia'];
            $empresa['id_representante'] = $data['id_representante'];
            $empresa['id_contador'] = $data['id_contador'];
            $empresa['num_registro_contador'] = $data['num_registro_contador'];
            $empresa['estado'] = $data['estado'];
            $deEmpresa['id_empresa'] = $data['id'];
            $deEmpresa['tipo_contribuyente'] = $data['tipo_contribuyente'];
            $deEmpresa['agente_retencion'] = $data['agente_retencion'];
            $deEmpresa['ambiente'] = $data['ambiente'];
            $deEmpresa['contabilidad'] = $data['contabilidad'];
            $deEmpresa['tipo_rimpe'] = $data['tipo_rimpe'];
            $deEmpresa['ruta_firma'] = $data['ruta_firma'];
            $deEmpresa['clave_firma'] = $data['clave_firma'];
            $emp = Empresas::find($data['id']);
            if ($emp == '') {
                unset($data['id']);
                $deEmpresa['id_usuariocrea'] = Auth::user()->id;
                $deEmpresa['id_usuariomod'] = Auth::user()->id;
                $deEmpresa['created_at'] = date('Y-m-d H:i:s');
                $deEmpresa['ip_creacion'] = $ip;
                $deEmpresa['updated_at'] = date('Y-m-d H:i:s');
                $deEmpresa['ip_modificacion'] = $ip;
                $empresa['id_usuariocrea'] = Auth::user()->id;
                $empresa['id_usuariomod'] = Auth::user()->id;
                $empresa['created_at'] = date('Y-m-d H:i:s');
                $empresa['updated_at'] = date('Y-m-d H:i:s');
                $empresa['ip_creacion'] = $ip;
                $empresa['ip_modificacion'] = $ip;
                Empresas::insert($empresa);
                DeEmpresas::insert($deEmpresa);
                $r->c = 'Configuraciones';
                $r->s = 'saveEmpresa';
                $r->d = $origin['d'];
                $r->m = Empresas::$modelo;
                $r->o = 'Se creo la empresa con id.: ' . $empresa['id'];
                $cont++;
            } else {
                $id = $data['id'];
                unset($data['id']);
                $deEmpresa['id_usuariomod'] = Auth::user()->id;
                $deEmpresa['updated_at'] = date('Y-m-d H:i:s');
                $deEmpresa['ip_modificacion'] = $ip;
                Empresas::where('id', $id)->update($empresa);
                $emp = DeEmpresas::where('id_empresa', $empresa['id'])->first();
                if ($emp == '') {
                    $deEmpresa['id_usuariocrea'] = Auth::user()->id;
                    $deEmpresa['ip_creacion'] = $ip;
                    DeEmpresas::insert($deEmpresa);
                } else {
                    DeEmpresas::where('id_empresa', $id)->update($deEmpresa);
                }
                $r->c = 'Configuraciones';
                $r->s = 'saveEmpresa';
                $r->d = $origin['d'];
                $r->m = Empresas::$modelo;
                $r->o = 'Se actualizo la empresa con id.: ' . $id;
                $cont++;
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
    public static function getEmpresa($id = '', $estado = '')
    {
        if ($id == '') {
            return Empresas::where(function ($q) use ($estado) {
                if ($estado != '') {
                    $q->where('estado', $estado);
                }
            })->get([
                'id',
                'razonsocial',
                'nombrecomercial',
                'email',
                'estado',
                'nombre_corto'
            ]);
        } else {
            $arrayMensajesGenerales = [];
            try {
                return Empresas::leftjoin('new_db_ciudades as c', 'c.id_ciudad', 'empresa.id_ciudad')
                    ->leftjoin('de_empresa as e', 'e.id_empresa', 'empresa.id')
                    ->where('empresa.id', $id)
                    ->first(
                        [
                            'empresa.id',
                            'empresa.razonsocial',
                            'empresa.nombrecomercial',
                            'empresa.email',
                            'empresa.estado',
                            'empresa.id_ciudad',
                            'empresa.telefono1',
                            'empresa.telefono2',
                            'empresa.nombre_corto',
                            'empresa.id_ciudad',
                            'empresa.web',
                            'c.id_provincia_ciudad',
                            'c.id_pais_ciudad',
                            'empresa.direccion',
                            'empresa.id_representante',
                            'empresa.id_contador',
                            'empresa.urbanizacion',
                            'empresa.etapa',
                            'empresa.manzana',
                            'empresa.villa',
                            'empresa.referencia',
                            'empresa.num_registro_contador',
                            'e.tipo_contribuyente',
                            'e.tipo_rimpe',
                            'e.ambiente',
                            'e.agente_retencion',
                            'e.contabilidad',
                            'e.contribuyente_especial',
                            'e.clave_firma',
                            'e.ruta_firma',
                            'empresa.egreso_kardex',
                            'empresa.logo',
                            'empresa.persona_nat_jur',
                            'empresa.tipo_representante',
                            'empresa.pref_representante',
                            'empresa.id_contador',
                            'empresa.pref_contador'
                        ]
                    );
            } catch (Exception $e) {
                $dato = explode('\'', $e->getMessage());
                $arrayRespuesta = [
                    'status' => 'error',
                    'code' => 500,
                    'model' => Empresas::$modelo,
                    'table' => $dato[1],
                    'message' => 'La empresa no tiene configurado el catalogo general',
                ];
                /*echo '<pre>';
                print_r($arrayRespuesta);
                exit;*/
                array_push($arrayMensajesGenerales, $arrayRespuesta);
            }
            $data['mensajesGenerales'] = $arrayMensajesGenerales;
        }
    }
    public function pref_repre()
    {
        return $this->belongsTo('Sis_medico\v3\contable\TituloProfesional', 'pref_representante');
    }
    public function pref_cont()
    {
        return $this->belongsTo('Sis_medico\v3\contable\TituloProfesional', 'pref_contador');
    }
    public function usuario_representante()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Usuarios', 'id_representante');
    }
    public function usuario_contador()
    {
        return $this->belongsTo('Sis_medico\v3\contable\Usuarios', 'id_contador');
    }
}
