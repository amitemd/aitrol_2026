<?php

namespace Sis_medico\Services;

use Sis_medico\Services\Security\SecureOutboundHttpClient;

class GatewayTicketService
{
    private SecureOutboundHttpClient $http;

    public function __construct(SecureOutboundHttpClient $http)
    {
        $this->http = $http;
    }

    public function send(array $payload)
    {
        return $this->request('POST', '', $payload['external_id'], $payload);
    }

    public function get($id, $externalId)
    {
        if (!is_string($id) || !preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $id)) {
            throw new \RuntimeException('Identificador de ticket invalido.');
        }
        $data = $this->request('GET', '/' . $id, $externalId);
        if ($data['id'] !== $id) {
            throw new \RuntimeException('Gateway devolvio un ticket diferente al solicitado.');
        }
        return $data;
    }

    private function request($method, $path, $externalId, array $payload = array())
    {
        $url = rtrim((string) config('gateway.url'), '/');
        $token = (string) config('gateway.token');
        if (!$url || !$token) {
            throw new \RuntimeException('Configure GATEWAY_URL y GATEWAY_TOKEN para enviar tickets.');
        }
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new \RuntimeException('El ticket contiene caracteres no validos.');
        }
        $response = $this->http->request(
            $method,
            $url . '/api/v1/ticketing/tickets' . $path,
            array('Authorization' => 'Bearer '.$token, 'Content-Type' => 'application/json'),
            $method === 'POST' ? $json : ''
        );

        return $this->decodeResponse($response['status'], $response['body'], $externalId);
    }

    public function decodeResponse($status, $body, $externalId)
    {
        $messages = array(
            401 => 'El token de Gateway es invalido o ha caducado.',
            403 => 'La aplicacion no tiene permiso para enviar tickets al Gateway.',
            404 => 'El ticket no esta disponible en Gateway.',
            409 => 'La referencia del ticket ya existe con otro contenido.',
            422 => 'Gateway rechazo los datos del ticket.',
            429 => 'Gateway recibio demasiadas solicitudes. Reintente mas tarde.',
        );
        if (isset($messages[$status])) {
            throw new \RuntimeException($messages[$status]);
        }
        $result = json_decode($body, true);
        $data = isset($result['data']) && is_array($result['data']) ? $result['data'] : array();
        if (!in_array($status, array(200, 201), true)
            || !isset($data['id'], $data['external_id'], $data['status']) || $data['external_id'] !== $externalId
            || !in_array($data['status'], array('open', 'assigned', 'resolved'), true)
            || !is_string($data['id']) || !preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $data['id'])) {
            throw new \RuntimeException('Gateway no confirmo la recepcion de este ticket. Reintente el mismo ticket.');
        }
        return $data;
    }
}
