<?php

namespace Tests\Feature\v3;

use Tests\TestCase;

/**
 * @group v3
 */
class AccesoProtegidoTest extends TestCase
{
    /**
     * @dataProvider rutasAdministrativas
     */
    public function testLasRutasAdministrativasRedirigenSinSesion($ruta)
    {
        $response = $this->get($ruta);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function rutasAdministrativas()
    {
        return [
            ['/v3/matriz-madurez'],
            ['/v3/documentacion'],
            ['/v3/documentacion/readme'],
            ['/v3/operacion-monitoreo'],
        ];
    }

    public function testLaApiTributariaRespondeJsonSinSesion()
    {
        $response = $this->get('/api/v1/tributario');

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No autenticado.',
        ]);
    }
}
