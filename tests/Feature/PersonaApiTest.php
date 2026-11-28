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

    public function test_consulta_la_identificacion_exacta_y_devuelve_solo_los_datos_del_documento_y_nombre(): void
    {
        $usuario = $this->administrador();
        $token = $usuario->createToken('Integración', ['personas:leer'])->plainTextToken;
        Persona::query()->create([
            'identificacion' => '0920000001', 'id_tipo_identificacion' => 1,
            'primer_nombre' => 'Ana', 'segundo_nombre' => 'Inés', 'primer_apellido' => 'Reyes', 'segundo_apellido' => 'Soto',
            'email' => 'privado@example.test', 'direccion' => 'Dato privado',
        ]);

        $this->withToken($token)->postJson(route('api.v1.personas.consultar'), ['identificacion' => '0920000001'])
            ->assertOk()->assertExactJson(['data' => [
                'identificacion' => '0920000001', 'tipo_identificacion' => 'cedula',
                'nombres' => 'Ana Inés', 'apellidos' => 'Reyes Soto',
                'primer_nombre' => 'Ana', 'segundo_nombre' => 'Inés', 'primer_apellido' => 'Reyes', 'segundo_apellido' => 'Soto',
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
    }

    public function test_responde_409_si_el_documento_coincide_con_dos_tipos(): void
    {
        $usuario = $this->administrador();
        $token = $usuario->createToken('Integración', ['personas:leer'])->plainTextToken;
        Persona::query()->create(['identificacion' => '0920000001', 'id_tipo_identificacion' => 1, 'nombre' => 'Persona con cédula']);
        Persona::query()->create(['identificacion' => '0920000001', 'id_tipo_identificacion' => 3, 'nombre' => 'Persona con pasaporte']);
        $ruta = route('api.v1.personas.consultar');

        $this->withToken($token)->postJson($ruta, ['identificacion' => '0920000001'])->assertConflict();
        $this->postJson($ruta, ['identificacion' => '0920000001', 'tipo_identificacion' => 'pasaporte'])
            ->assertOk()->assertJsonPath('data.nombres', 'Persona con pasaporte');
    }

    private function administrador(bool $global = true): User
    {
        $empresa = EmpresaModel::create(['ruc' => '1273300000001', 'razon_social' => 'Empresa de prueba']);

        return User::factory()->for($empresa, 'empresa')->create(['is_admin' => true, 'is_admin_global' => $global]);
    }
}
