<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PersonaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_consulta_la_cedula_y_devuelve_la_identidad_y_los_datos_de_contacto(): void
    {
        $usuario = $this->administrador();
        $token = $usuario->createToken('Integración', ['personas:leer'])->plainTextToken;
        Persona::query()->create([
            'identificacion' => '0920000001', 'id_tipo_identificacion' => 1,
            'primer_nombre' => 'Ana', 'segundo_nombre' => 'Inés', 'primer_apellido' => 'Reyes', 'segundo_apellido' => 'Soto',
            'email' => 'ana@example.test', 'direccion' => 'Dirección de prueba',
            'fecha_nacimiento' => '1990-06-15', 'telefono' => '042000000', 'celular' => '0900000000',
        ]);

        $this->withToken($token)->postJson(route('api.v1.personas.consultar'), ['identificacion' => '0920000001'])
            ->assertOk()->assertExactJson(['data' => [
                'identificacion' => '0920000001', 'tipo_identificacion' => 'cedula',
                'nombres' => 'Ana Inés', 'apellidos' => 'Reyes Soto',
                'primer_nombre' => 'Ana', 'segundo_nombre' => 'Inés', 'primer_apellido' => 'Reyes', 'segundo_apellido' => 'Soto',
                'direccion' => 'Dirección de prueba', 'fecha_nacimiento' => '1990-06-15',
                'email' => 'ana@example.test', 'telefono' => '042000000', 'celular' => '0900000000',
            ]]);
    }

    public function test_responde_401_sin_credencial_y_403_sin_el_alcance(): void
    {
        $usuario = $this->administrador();
        $token = $usuario->createToken('Otra API', ['clientes:leer'])->plainTextToken;
        $ruta = route('api.v1.personas.consultar');

        $this->postJson($ruta, ['identificacion' => '0920000001'])->assertUnauthorized();
        $this->withToken($token)->postJson($ruta, ['identificacion' => '0920000001'])->assertForbidden();
    }

    public function test_responde_403_a_un_administrador_que_solo_tiene_acceso_a_su_empresa(): void
    {
        $usuario = $this->administrador(false);
        $token = $usuario->createToken('Alcance global', ['personas:leer'])->plainTextToken;

        $this->withToken($token)->postJson(route('api.v1.personas.consultar'), ['identificacion' => '0920000001'])->assertForbidden();
    }

    public function test_responde_404_a_documentos_ausentes_y_422_a_documentos_dados_como_numero(): void
    {
        $usuario = $this->administrador();
        $token = $usuario->createToken('Integración', ['personas:leer'])->plainTextToken;
        $ruta = route('api.v1.personas.consultar');

        $this->withToken($token)->postJson($ruta, ['identificacion' => '0920000001'])->assertNotFound();
        $this->postJson($ruta, ['identificacion' => 920000001])->assertUnprocessable()->assertJsonValidationErrors('identificacion');
        $this->postJson($ruta, ['identificacion' => '0920000001001'])->assertUnprocessable()
            ->assertJsonPath('errors.identificacion.0', 'La cédula debe tener exactamente 10 dígitos.');
    }

    public function test_consulta_solo_cedulas_aunque_haya_pasaportes_con_el_mismo_numero(): void
    {
        $usuario = $this->administrador();
        $token = $usuario->createToken('Integración', ['personas:leer'])->plainTextToken;
        Persona::query()->create(['identificacion' => '0920000001', 'id_tipo_identificacion' => 1, 'nombre' => 'Persona con cédula']);
        Persona::query()->create(['identificacion' => '0920000001', 'id_tipo_identificacion' => 3, 'nombre' => 'Persona con pasaporte']);
        Persona::query()->create(['identificacion' => '0920000002', 'id_tipo_identificacion' => 3, 'nombre' => 'Solo pasaporte']);
        $ruta = route('api.v1.personas.consultar');

        $this->withToken($token)->postJson($ruta, ['identificacion' => '0920000001'])
            ->assertOk()->assertJsonPath('data.nombres', 'Persona con cédula')->assertJsonPath('data.fecha_nacimiento', null);
        $this->postJson($ruta, ['identificacion' => '0920000002', 'tipo_identificacion' => 'pasaporte'])
            ->assertNotFound();
    }

    private function administrador(bool $global = true): User
    {
        $empresa = EmpresaModel::create(['ruc' => '1273300000001', 'razon_social' => 'Empresa de prueba']);

        return User::factory()->for($empresa, 'empresa')->create(['is_admin' => true, 'is_admin_global' => $global]);
    }
}
