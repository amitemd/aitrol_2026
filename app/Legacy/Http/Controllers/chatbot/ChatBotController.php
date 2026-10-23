<?php

namespace Sis_medico\Http\Controllers\chatbot;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Pedido;
use Sis_medico\Producto;

class ChatBotController extends Controller
{

    public $APIurl;
    public $token;

    public function __construct()
    {
        $this->middleware('auth');
        $this->APIurl = config('app.url_api');
        $this->token = config('app.token_api');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function enviar_mensajes()
    {
        dd($this->APIurl . ' -- ' . $this->token);
    }


    public function sendFile($chatId, $text, $file, $fileName)
    {
        $data = array('phone' => $chatId, 'body' => $file, 'caption' => $text, 'filename' => $fileName);
        $response = $this->sendRequest('sendFile', $data);
        return $response;
    }

    public function uploadImage($file)
    {
        $data = array('body' => $file);
        $response = $this->sendRequest('uploadMedia', $data);
        return $response;
    }

    public function sendMessage($chatId, $text)
    {
        $data = array('phone' => $chatId, 'body' => $text);
        $response = $this->sendRequest('sendMessage', $data);
        return $response;
    }

    public function sendRequest($method, $data)
    {
        $url = $this->APIurl . $method . '?token=' . $this->token;
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $options = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => $data
        ]]);
        $response = file_get_contents($url, false, $options);
        return $response;
    }

    public function sendTemplate($templateName, $chatId, $file, $message, $link, $tipo)
    {
        $language = array('policy' => 'deterministic', 'code' => 'es');
        $header = array(
            'type' => 'header',
            'parameters' => [array(
                'type' => 'image',
                'image' => array(
                    'id' => $file
                )
            )]
        );

        $body = array(
            'type' => 'body',
            'parameters' => [
                array('type' => 'text', 'text' => $message),
                array('type' => 'text', 'text' => $link)
            ]
        );


        // Define el objeto principal data
        $data = array(
            'namespace' => '771b44ca_357f_4206_a7cd_e37cd137f995',
            'template' => $templateName,
            'language' => $language,
            'params' => [$header, $body],
            'phone' => $chatId
        );

        $response = $this->sendRequest('sendTemplate', $data);
        return $response;
    }
}
