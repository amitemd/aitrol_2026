<?php

namespace Sis_medico\Services;

class GatewayTicketService
{
    public function send(array $payload)
    {
        return $this->request('POST', '', $payload);
    }

    public function get($id)
    {
        if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $id)) {
            throw new \RuntimeException('Identificador de ticket inválido.');
        }
        return $this->request('GET', '/' . $id);
    }

    private function request($method, $path, array $payload = [])
    {
        $url = rtrim((string) config('gateway.url'), '/');
        $token = (string) config('gateway.token');
        if (!$url || !$token) {
            throw new \RuntimeException('El envío no está configurado. Solicite configurar GATEWAY_URL y GATEWAY_TOKEN.');
        }
        if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
            throw new \RuntimeException('La dirección configurada para Gateway no es válida.');
        }
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('El servidor necesita habilitar la extensión cURL de PHP para enviar tickets.');
        }
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new \RuntimeException('El ticket contiene caracteres no válidos. Revise el texto.');
        }

        $curl = curl_init($url . '/api/v1/ticketing/tickets' . $path);
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Accept: application/json', 'Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        }
        $body = curl_exec($curl);
        $errorNumber = curl_errno($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($body === false) {
            throw new \RuntimeException($this->connectionErrorMessage($errorNumber));
        }
        return $this->decodeResponse($status, $body);
    }

    public function connectionErrorMessage($errorNumber)
    {
        $messages = [
            5 => 'No se pudo resolver la dirección del proxy del servidor.',
            6 => 'No se pudo resolver el dominio de Gateway. Revise GATEWAY_URL y el DNS del servidor.',
            7 => 'No se pudo conectar con Gateway. Revise GATEWAY_URL, el puerto y que el servicio esté disponible.',
            28 => 'Gateway no respondió dentro del tiempo de espera.',
            35 => 'No se pudo establecer la conexión TLS con Gateway. Revise la configuración HTTPS del servidor.',
            60 => 'No se pudo verificar el certificado HTTPS de Gateway. Revise el certificado y las autoridades de confianza del servidor.',
            77 => 'No se pudo leer el archivo de certificados de confianza del servidor. Revise la configuración de PHP/cURL.',
        ];
        $message = isset($messages[$errorNumber]) ? $messages[$errorNumber] : 'Falló la comunicación con Gateway.';
        return $message . ' (cURL ' . (int) $errorNumber . '). No se pudo confirmar el envío; conserve este formulario para reintentar sin duplicados.';
    }

    public function decodeResponse($status, $body)
    {
        $messages = [
            401 => 'El token de Gateway es inválido o ha caducado. Contacte al administrador.',
            403 => 'La aplicación no tiene permiso para enviar tickets a Gateway.',
            409 => 'Esta referencia ya existe con otro contenido. Restaure el contenido original o abra un nuevo ticket.',
            422 => 'Gateway rechazó los datos. Revise el título, la descripción y la prioridad.',
            429 => 'Gateway recibió demasiadas solicitudes. Espere un momento y reintente.',
        ];
        if (isset($messages[$status])) {
            throw new \RuntimeException($messages[$status]);
        }
        $result = json_decode($body, true);
        if (!in_array($status, [200, 201], true) || !is_array($result) || empty($result['data']['id'])) {
            throw new \RuntimeException('No se pudo confirmar el envío en Gateway. Reintente con este mismo formulario para evitar duplicados.');
        }
        return $result;
    }
}
