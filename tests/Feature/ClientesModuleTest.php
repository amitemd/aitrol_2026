<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ClientesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_requires_login_and_can_register_and_edit_a_client(): void
    {
        $this->get('/clientes')->assertRedirect(route('login'));

        $usuario = User::factory()->create();
        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])
            ->assertRedirect(route('clientes.index'));

        $this->get('/clientes/create')->assertOk()->assertSee('Nuevo cliente');
        $this->post('/clientes', $this->datos())->assertRedirect(route('clientes.index'));
        $this->post('/clientes', $this->datos())->assertSessionHasErrors('identificacion');
        $this->get('/clientes?q=Ejemplo')->assertOk()->assertSee('Cliente Ejemplo');

        $this->get('/clientes/1/edit')->assertOk()->assertSee('Cliente Ejemplo');
        $this->put('/clientes/1', [...$this->datos(), 'activo' => '0'])->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('erp_clientes', ['id' => 1, 'activo' => false]);
        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_api_requires_a_valid_token_even_when_the_browser_is_authenticated(): void
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario)->getJson('/api/v1/clientes')->assertUnauthorized();

        $vencido = $usuario->createToken('Vencido', ['clientes:leer'], now()->subMinute());
        $this->getJson('/api/v1/clientes', ['Authorization' => 'Bearer '.$vencido->plainTextToken])
            ->assertUnauthorized();

        $revocado = $usuario->createToken('Revocado', ['clientes:leer'], now()->addDay());
        $revocado->accessToken->delete();
        $this->getJson('/api/v1/clientes', ['Authorization' => 'Bearer '.$revocado->plainTextToken])
            ->assertUnauthorized();
    }

    public function test_a_read_only_token_cannot_write(): void
    {
        $usuario = User::factory()->create();
        $token = $usuario->createToken('Consulta', ['clientes:leer'], now()->addDay())->plainTextToken;
        $cabeceras = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/v1/clientes', $cabeceras)->assertOk();
        $this->postJson('/api/v1/clientes', $this->datos(), $cabeceras)->assertForbidden();
        $this->assertDatabaseCount('erp_clientes', 0);
    }

    public function test_integration_credentials_can_be_issued_and_revoked_by_cli(): void
    {
        $usuario = User::factory()->create();

        $this->artisan('clientes:token', [
            'email' => $usuario->email,
            'nombre' => 'Integración',
            '--escritura' => true,
            '--dias' => '30',
        ])->assertSuccessful();

        $token = $usuario->tokens()->sole();
        $this->assertSame(['clientes:leer', 'clientes:escribir'], $token->abilities);
        $this->assertTrue($token->expires_at->isFuture());

        $this->artisan('clientes:revocar-token', [
            'email' => $usuario->email,
            'id' => (string) $token->getKey(),
        ])->assertSuccessful();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_api_reuses_customer_rules_and_returns_standard_responses(): void
    {
        $usuario = User::factory()->create();
        $token = $usuario->createToken('ERP externo', ['clientes:leer', 'clientes:escribir'], now()->addDay())->plainTextToken;
        $cabeceras = ['Authorization' => 'Bearer '.$token];

        $this->postJson('/api/v1/clientes', [...$this->datos(), 'identificacion' => '123'], $cabeceras)
            ->assertUnprocessable()->assertJsonValidationErrors('identificacion');

        $this->postJson('/api/v1/clientes', $this->datos(), $cabeceras)
            ->assertCreated()->assertHeader('Location', route('api.v1.clientes.show', 1))
            ->assertJsonPath('data.identificacion', '0123456789')
            ->assertJsonPath('data.activo', true);

        $this->postJson('/api/v1/clientes', $this->datos(), $cabeceras)->assertConflict();
        $this->getJson('/api/v1/clientes?q=Ejemplo&per_page=1', $cabeceras)
            ->assertOk()->assertJsonPath('meta.total', 1)->assertJsonPath('data.0.nombre', 'Cliente Ejemplo');
        $this->getJson('/api/v1/clientes/999', $cabeceras)->assertNotFound();
        $this->putJson('/api/v1/clientes/1', [...$this->datos(), 'activo' => false], $cabeceras)
            ->assertOk()->assertJsonPath('data.activo', false);
    }

    /** @return array{tipo_identificacion: string, identificacion: string, nombre: string} */
    private function datos(): array
    {
        return [
            'tipo_identificacion' => 'cedula',
            'identificacion' => '0123456789',
            'nombre' => 'Cliente Ejemplo',
        ];
    }
}
