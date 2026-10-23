<?php

namespace Sis_medico\Services {
    class GatewayTransportFixture
    {
        public static $url;
        public static $options = [];
    }
    function config($key) { return $key === 'gateway.url' ? 'https://gateway.example.test' : 'test-token'; }
    function curl_init($url) { GatewayTransportFixture::$url = $url; GatewayTransportFixture::$options = []; return new \stdClass(); }
    function curl_setopt_array($handle, $options) { GatewayTransportFixture::$options = $options; return true; }
    function curl_setopt($handle, $key, $value) { GatewayTransportFixture::$options[$key] = $value; return true; }
    function curl_exec($handle) { return '{"data":{"id":"b1dcabaf-ec2d-4b5c-964b-f0e0e3f9403a","status":"assigned"}}'; }
    function curl_getinfo($handle, $key) { return 200; }
    function curl_errno($handle) { return 0; }
    function curl_close($handle) {}
}

namespace Tests\Unit {
    use PHPUnit\Framework\TestCase;
    use Sis_medico\Services\GatewayTicketService;
    use Sis_medico\Services\GatewayTransportFixture;

    require_once __DIR__ . '/../../app/Services/GatewayTicketService.php';

    class GatewayTicketTransportTest extends TestCase
    {
        public function testStatusLookupUsesAuthenticatedGetWithoutResendingTicket(): void
        {
            $id = 'b1dcabaf-ec2d-4b5c-964b-f0e0e3f9403a';
            $result = (new GatewayTicketService())->get($id);
            $this->assertSame('https://gateway.example.test/api/v1/ticketing/tickets/' . $id, GatewayTransportFixture::$url);
            $this->assertArrayNotHasKey(CURLOPT_POST, GatewayTransportFixture::$options);
            $this->assertArrayNotHasKey(CURLOPT_POSTFIELDS, GatewayTransportFixture::$options);
            $this->assertContains('Authorization: Bearer test-token', GatewayTransportFixture::$options[CURLOPT_HTTPHEADER]);
            $this->assertTrue(GatewayTransportFixture::$options[CURLOPT_SSL_VERIFYPEER]);
            $this->assertSame('assigned', $result['data']['status']);
        }

        public function testSendingStillPostsOriginalReferenceAndContent(): void
        {
            $payload = ['external_id' => 'SUC-example', 'title' => 'Prueba', 'description' => 'Descripción', 'priority' => 'normal'];
            (new GatewayTicketService())->send($payload);
            $this->assertSame('https://gateway.example.test/api/v1/ticketing/tickets', GatewayTransportFixture::$url);
            $this->assertTrue(GatewayTransportFixture::$options[CURLOPT_POST]);
            $this->assertSame($payload, json_decode(GatewayTransportFixture::$options[CURLOPT_POSTFIELDS], true));
        }

        public function testRejectsInvalidIdentifierBeforeNetworkRequest(): void
        {
            $this->expectException(\RuntimeException::class);
            (new GatewayTicketService())->get('../management/tickets');
        }
    }
}
