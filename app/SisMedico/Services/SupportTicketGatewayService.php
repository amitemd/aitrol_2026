<?php

namespace Sis_medico\Services;

use Illuminate\Support\Str;
use Sis_medico\SupportTicketDelivery;
use Sis_medico\Ticket_Soporte_Tecnico;

class SupportTicketGatewayService
{
    public function prepare(Ticket_Soporte_Tecnico $ticket, $user, $companyId)
    {
        $existing = SupportTicketDelivery::where('ticket_id', $ticket->id)->first();
        if ($existing) {
            return $existing;
        }
        $externalId = 'aitrol-borja-' . Str::uuid();
        $name = preg_replace('/\s+/u', ' ', trim($user->nombre1 . ' ' . $user->nombre2 . ' ' . $user->apellido1 . ' ' . $user->apellido2));
        $payload = array(
            'external_id' => $externalId,
            'title' => mb_substr('Soporte #' . $ticket->id . ' - ' . $ticket->area . ': ' . $ticket->requerimientos, 0, 200, 'UTF-8'),
            'description' => 'Ticket local: #' . $ticket->id . "\nEmpresa: " . $companyId
                . "\nArea: " . $ticket->area . "\n\n" . $ticket->requerimientos . "\n" . $ticket->observacion
                . "\n\nCreado por: " . $name . "\nCédula: " . $user->id
                . "\n\nURL de origen: " . rtrim(config('app.url'), '/'),
            'priority' => 'normal',
        );
        return SupportTicketDelivery::create(array(
            'ticket_id' => $ticket->id, 'id_empresa' => $companyId, 'external_id' => $externalId, 'payload' => $payload,
        ));
    }

    public function deliver(SupportTicketDelivery $delivery)
    {
        if ($delivery->gateway_id) {
            return $delivery->gateway_id;
        }
        // Retry the immutable snapshot: gateway deduplicates by external_id.
        $data = app(GatewayTicketService::class)->send($delivery->payload);
        $delivery->update(array('gateway_id' => $data['id'], 'sent_at' => date('Y-m-d H:i:s'),
            'snapshot' => $data, 'synced_at' => date('Y-m-d H:i:s')));
        return $data['id'];
    }

    public function addEvidence(SupportTicketDelivery $delivery, array $files)
    {
        $attachments = app(SupportTicketAttachmentService::class)->prepare($delivery, $files);
        if (!$attachments) {
            return;
        }
        $payload = $delivery->payload;
        $payload['description'] .= "\n\nEvidencia adjunta (enlaces privados de descarga):\n";
        foreach ($attachments as $attachment) {
            $payload['description'] .= $attachment->name . ': ' . $attachment->download_url . "\n";
        }
        $payload['description'] = rtrim($payload['description']);
        if (mb_strlen($payload['description'], 'UTF-8') > 20000) {
            throw new \RuntimeException('El ticket y sus evidencias superan el limite aceptado por Gateway.');
        }
        $delivery->update(array('payload' => $payload));
    }

    public function refresh(SupportTicketDelivery $delivery)
    {
        if (!$delivery->gateway_id) {
            return $this->deliver($delivery);
        }
        $data = app(GatewayTicketService::class)->get($delivery->gateway_id, $delivery->external_id);
        $delivery->update(array('snapshot' => $data, 'synced_at' => date('Y-m-d H:i:s')));
        return $delivery->gateway_id;
    }
}
