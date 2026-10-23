<?php

namespace Sis_medico\Http\Controllers\Apis;

use Illuminate\Support\Facades\Auth;
use Sis_medico\Firma_Usuario;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Sis_medico\LogDocumentoFirmado;
use Sis_medico\User;

class FirmadorController extends Controller
{
    public static function signPDFJarGet($fileName,$dirFile, $ruta_token, $pin_token, $algoritmo, $signPos, $coordenadas, $location, $reason, $qrVisible, $pageSig, $description = 'Archivo firmado')
    {
        //dd($fileName,$dirFile, $ruta_token, $pin_token, $algoritmo, $signPos, $coordenadas, $location, $reason, $qrVisible, $pageSig);
        $ip_usuario = $_SERVER["REMOTE_ADDR"];
        $id_usuario  = Auth::user()->id;
        $accion = "1";
        /****** Algoritmos Permitidos*******/
        /*
            SHA-1
            SHA-256
            SHA-384
            SHA-512
            RIPEMD160
        */

        /******Posicion Firma*******/
        /*
            0:top left
            1:top center
            2:top right
            3:bottom left
            4:bottom center
            5:bottom right
            6:custom
        */
        // Establecer límite de tiempo de ejecución
        ini_set('max_execution_time', 300);
        
        // Verificar si el certificado existe
        $certificado_path = $ruta_token;
        $isExiste = file_exists($certificado_path);
        if (!$isExiste) {
            $arr_log= [
                'documento' => $fileName,
                'ruta_firma' => $ruta_token,
                'pos_sign' => $signPos,
                'razon' => $reason,
                'ubicacion' => $location,
                'algoritmo' => $algoritmo,
                'descripcion' => 'no existe la el cerftificado de la firma ' . $certificado_path,
                'id_usuario' => $id_usuario,
                'ip_usuario' => $ip_usuario,
            ];
            LogDocumentoFirmado::create($arr_log);
            return 'No se encontro el certificado p12: ' . $certificado_path;
        }
        
        // Ruta al archivo JAR
        $jar = base_path() . '/botIESS_server/dist/botIESS_server.jar';
       
        $jar = escapeshellarg($jar);
        $key = '@Dm1n13C3d';
        $decoded_data = base64_decode($pin_token);
        $encrypted_decoded = substr($decoded_data, openssl_cipher_iv_length('aes-256-cbc'));
        $iv_decoded = substr($decoded_data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $decrypted = openssl_decrypt($encrypted_decoded, 'aes-256-cbc', $key, 0, $iv_decoded);

        // Construir el comando para ejecutar el JAR
        $command = "java -jar $jar $accion \"$dirFile\" \"$ruta_token\" \"$decrypted\" \"$algoritmo\" $signPos \"$coordenadas\" \"$location\" \"$reason\" $qrVisible $pageSig 2>&1";
        // dd($command);
        // Ejecutar el comando y obtener la respuesta
        $respuestaCmd = exec($command, $output, $return_value);
        // Verificar si hubo un error relacionado con el certificado caducado
        $resultado = strpos($respuestaCmd, 'El certificado firmante ha caducado');
        //dd($respuestaCmd,$resultado);
        if ($resultado !== FALSE) {
            $arr_log= [
                'documento' => $fileName,
                'ruta_firma' => $ruta_token,
                'pos_sign' => $signPos,
                'razon' => $reason,
                'ubicacion' => $location,
                'algoritmo' => $algoritmo,
                'descripcion' => 'El certificado firmante ha caducado para firmar: ' . $fileName,
                'id_usuario' => $id_usuario,
                'ip_usuario' => $ip_usuario,
            ];
            LogDocumentoFirmado::create($arr_log);
            return 'Certificado caducado'; // Si el certificado ha caducado, no hay nada para descargar
        }

        // Leer la respuesta del comando
        $response = "";
        if ($return_value == -1) {
            $response = "";
            foreach ($output as $line)
                $response .= $line . "\n";
        } else {
            // Concatenar la respuesta del comando
            $response = implode("\n", $output);
        }

        // Verificar si hubo un error relacionado con el certificado caducado
        $cadena = trim($response);
        $buscar = "El certificado firmante ha caducado:";
        $resultado = strpos($cadena, $buscar);
        if ($resultado !== FALSE) {
            echo '<pre>';
            print_r($response);
            $arr_log= [
                'documento' => $fileName,
                'ruta_firma' => $ruta_token,
                'pos_sign' => $signPos,
                'razon' => $reason,
                'ubicacion' => $location,
                'algoritmo' => $algoritmo,
                'descripcion' => $response,
                'id_usuario' => $id_usuario,
                'ip_usuario' => $ip_usuario,
            ];
            LogDocumentoFirmado::create($arr_log);
            return $buscar;
        }

        $arr_log= [
            'documento' => $fileName,
            'ruta_firma' => $ruta_token,
            'pos_sign' => $signPos,
            'razon' => $reason,
            'ubicacion' => $location,
            'algoritmo' => $algoritmo,
            'descripcion' => $description,
            'id_usuario' => $id_usuario,
            'ip_usuario' => $ip_usuario,
        ];
        LogDocumentoFirmado::create($arr_log);
        // Devolver el contenido del PDF firmado
        return $response;
    }
    
    public function signPDFJarPost(Request $request)
    {
        $ip_usuario = $_SERVER["REMOTE_ADDR"];
        $id_usuario  = Auth::user()->id;
        /****** Algoritmos Permitidos*******/
        /*
            SHA-1
            SHA-256
            SHA-384
            SHA-512
            RIPEMD160
        */

        /******Posicion Firma*******/
        /*
            0:top left
            1: top center
            2:top right
            3: bottom left
            4:bottom center
            5: bottom right
        */
        ini_set('max_execution_time', 300);
        $accion =  $request['accion'];
        $fileName = $request['fileName'];
        $dirFile =  $request['dirFile'];
        $ruta_token =  $request['ruta_token']; 
        $pin_token =  $request['pin_token'];
        $algoritmo =  $request['algoritmo'];
        $signPos =  $request['signPos'];
        $coordenadas = $request('coordenadas');
        $location =  $request['location'];
        $reason =  $request['reason'];
        $qrVisible = $request['qrVisible'];//0: OCULTAR; 1: VISIBLE
        $pageSign = $request['pageSign'];//0:Primera Pagina; 1:ultima hoja; 2:todas
        $certificado_path = $ruta_token;
        $isExiste = file_exists($certificado_path);
        
        if (!$isExiste) {
            $arr_log= [
                'documento' => $fileName,
                'ruta_firma' => $ruta_token,
                'pos_sign' => $signPos,
                'razon' => $reason,
                'ubicacion' => $location,
                'algoritmo' => $algoritmo,
                'descripcion' => 'no existe la el cerftificado de la firma ' . $certificado_path,
                'id_usuario' => $id_usuario,
                'ip_usuario' => $ip_usuario,
            ];
            LogDocumentoFirmado::create($arr_log);
            return 'No se encontro el certificado p12: ' . $certificado_path;
        }

        // Ruta al archivo JAR
        $jar = base_path() . '/botIESS_server/dist/botIESS_server.jar';
        $jar = escapeshellarg($jar);

        // Construir el comando para ejecutar el JAR
        $command = "java -jar $jar $accion \"$dirFile\" \"$ruta_token\" \"$pin_token\" \"$algoritmo\" $signPos \"$coordenadas\" \"$location\" \"$reason\" \"$qrVisible\" \"$pageSign\" 2>&1";
        // dd($command);
        // Ejecutar el comando y obtener la respuesta
        $respuestaCmd = exec($command, $output, $return_value);
        // Verificar si hubo un error relacionado con el certificado caducado
        $resultado = strpos($respuestaCmd, 'El certificado firmante ha caducado');
        if ($resultado !== FALSE) {
            echo 'El certificado firmante ha caducado para firmar: ' . $fileName . ' <br/>';
            $arr_log= [
                'documento' => $fileName,
                'ruta_firma' => $ruta_token,
                'pos_sign' => $signPos,
                'razon' => $reason,
                'ubicacion' => $location,
                'algoritmo' => $algoritmo,
                'descripcion' => 'El certificado firmante ha caducado para firmar: ' . $fileName,
                'id_usuario' => $id_usuario,
                'ip_usuario' => $ip_usuario,
            ];
            LogDocumentoFirmado::create($arr_log);
            return 'No se encontro el certificado p12: ' . $ruta_token; // Si el certificado ha caducado, no hay nada para descargar
        }

        // Leer la respuesta del comando
        $response = "";
        if ($return_value == -1) {
            $response = "";
            foreach ($output as $line)
                $response .= $line . "\n";
        } else {
            // Concatenar la respuesta del comando
            $response = implode("\n", $output);
        }

        // Verificar si hubo un error relacionado con el certificado caducado
        $cadena = trim($response);
        $buscar = "El certificado firmante ha caducado:";
        $resultado = strpos($cadena, $buscar);
        if ($resultado !== FALSE) {
            echo '<pre>';
            print_r($response);
            $arr_log= [
                'documento' => $fileName,
                'ruta_firma' => $ruta_token,
                'pos_sign' => $signPos,
                'razon' => $reason,
                'ubicacion' => $location,
                'algoritmo' => $algoritmo,
                'descripcion' => 'El certificado firmante ha caducado para firmar: ' . $fileName,
                'id_usuario' => $id_usuario,
                'ip_usuario' => $ip_usuario,
            ];
            LogDocumentoFirmado::create($arr_log);
            return $buscar;
        }

        $arr_log= [
            'documento' => $fileName,
            'ruta_firma' => $ruta_token,
            'pos_sign' => $signPos,
            'razon' => $reason,
            'ubicacion' => $location,
            'algoritmo' => $algoritmo,
            'descripcion' => 'Archivo firmado',
            'id_usuario' => $id_usuario,
            'ip_usuario' => $ip_usuario,
        ];
        LogDocumentoFirmado::create($arr_log);
        return $response;
    }

    public static function crearcarpeta($ruta)
    {
        if (!file_exists($ruta)) {
            mkdir($ruta, 777, true);
        }
    }

    public static function index(){
        $firmas = Firma_Usuario::all();
        return view ('firmador_electronico/index',['firmas' => $firmas]);
    }

    public static function create(){
        return view ('firmador_electronico/create');
    }

    public static function edit($id){
        $firma = Firma_Usuario::find($id);
        $usuario = User::find($firma->id_usuario);
        return view ('firmador_electronico/edit',['firma' => $firma,'usuario' => $usuario]);
    }

    public static function store(Request $request){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $firma = Firma_Usuario::find($request['id_firma']);
        $key = '@Dm1n13C3d';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($request['password'], 'aes-256-cbc', $key, 0, $iv);
        $encrypted_data = base64_encode($iv . $encrypted);
        $nombre_original = $request['archivo']->getClientOriginalName();
        $ruta = base_path() . '/storage/app/p12/' . $nombre_original;
        $r1 = Storage::disk('p12')->put($nombre_original, File::get($request['archivo']));
        $arr = [
            'password' => $encrypted_data,
            'algoritmo' => 'SHA-512',
            'tipo' => 0,
            'ruta_p12' => $ruta,
            'id_usuariocrea' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];
        $firma->update($arr);
        return ['status' => 'success', 'msj' => 'Actualizado con exito'];
    }

    public static function update(Request $request){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $firma = Firma_Usuario::find($request['id_firma']);
        $key = '@Dm1n13C3d';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($request['password'], 'aes-256-cbc', $key, 0, $iv);
        $encrypted_data = base64_encode($iv . $encrypted);
        $nombre_original = $request['archivo']->getClientOriginalName();
        $ruta = base_path() . '/storage/app/p12/' . $nombre_original;
        $r1 = Storage::disk('p12')->put($nombre_original, File::get($request['archivo']));
        $arr = [
            'password' => $encrypted_data,
            'algoritmo' => 'SHA-512',
            'tipo' => 0,
            'ruta_p12' => $ruta,
            'id_usuariomod' => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];
        $firma->update($arr);
        return ['status' => 'success', 'msj' => 'Actualizado con exito'];
    }

    public static function decryp_password($password_encrypted){
        $key = '@Dm1n13C3d';
        $decoded_data = base64_decode($password_encrypted);
        $encrypted_decoded = substr($decoded_data, openssl_cipher_iv_length('aes-256-cbc'));
        $iv_decoded = substr($decoded_data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $decrypted = openssl_decrypt($encrypted_decoded, 'aes-256-cbc', $key, 0, $iv_decoded);
        return $decrypted;
    }

    public function decryp_password_post(Request $req){
        $password_encrypted = $req['password'];
        $key = '@Dm1n13C3d';
        $decoded_data = base64_decode($password_encrypted);
        $encrypted_decoded = substr($decoded_data, openssl_cipher_iv_length('aes-256-cbc'));
        $iv_decoded = substr($decoded_data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $decrypted = openssl_decrypt($encrypted_decoded, 'aes-256-cbc', $key, 0, $iv_decoded);
        return $decrypted;
    }
}
