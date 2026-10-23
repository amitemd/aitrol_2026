<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sis_medico\Services\GatewayTicketService;

require_once __DIR__ . '/../../app/Services/GatewayTicketService.php';

class GatewayTicketServiceTest extends TestCase
{
    public function testConnectionFailuresIdentifyCauseAndPreserveRetryGuidance(): void
    {
        $service = new GatewayTicketService();
        foreach ([6 => 'DNS', 7 => 'puerto', 28 => 'tiempo', 60 => 'certificado', 77 => 'certificados', 99 => 'comunicación'] as $code => $cause) {
            $message = $service->connectionErrorMessage($code);
            $this->assertNotFalse(strpos($message, $cause));
            $this->assertNotFalse(strpos($message, 'cURL ' . $code));
            $this->assertNotFalse(strpos($message, 'sin duplicados'));
        }
    }

    public function testAcceptsCreatedAndDuplicateTickets(): void
    {
        $service = new GatewayTicketService();
        foreach ([201 => false, 200 => true] as $status => $duplicate) {
            $response = ['data' => ['id' => 'ticket-123'], 'meta' => ['duplicate' => $duplicate]];
            $this->assertSame($response, $service->decodeResponse($status, json_encode($response)));
        }
    }

    public function testRejectsErrorsAndUnconfirmedResponsesWithoutExposingRemoteBody(): void
    {
        $service = new GatewayTicketService();
        $cases = [
            [401, 'private remote details'], [403, 'private remote details'],
            [409, 'private remote details'], [422, 'private remote details'],
            [429, 'private remote details'], [500, 'private remote details'],
            [302, 'private remote details'], [200, '<html>private remote details</html>'],
            [201, '{"data":{}}'], [200, 'null'],
        ];
        foreach ($cases as $case) {
            try {
                $service->decodeResponse($case[0], $case[1]);
                $this->fail('Debe rechazar respuestas sin un ticket confirmado.');
            } catch (\RuntimeException $exception) {
                $this->assertNotSame('', $exception->getMessage());
                $this->assertFalse(strpos($exception->getMessage(), 'private remote details'));
            }
        }
    }
}
