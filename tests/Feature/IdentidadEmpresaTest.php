<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class IdentidadEmpresaTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guarda_logo_y_estado_y_protege_el_certificado_de_empresa(): void
    {
        $empresa = EmpresaModel::create(['ruc' => '1200000000001', 'razon_social' => 'Empresa propia']);
        $usuario = User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]);
        $this->actingAs($usuario);
        $ruta = '/configuraciones/empresas/'.$empresa->id;
        $this->get($ruta.'/editar')->assertOk()->assertSee('Logo de la empresa')->assertSee('Empresa activa');
        $logo = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jX1cAAAAASUVORK5CYII=', true);
        $this->put($ruta, [
            'ruc' => $empresa->ruc, 'razon_social' => $empresa->razon_social, 'activo' => '0',
            'logo' => UploadedFile::fake()->createWithContent('logo.png', $logo),
        ])->assertRedirect('/configuraciones/empresas');
        $this->assertDatabaseHas('erp_empresas', ['id' => $empresa->id, 'activo' => false]);
        $this->get($ruta.'/logo')->assertOk()->assertHeader('Content-Type', 'image/png');

        $clave = 'Clave de certificado de prueba';
        $certificado = $this->certificado($clave);
        $this->put($ruta, [
            'ruc' => $empresa->ruc, 'razon_social' => $empresa->razon_social,
            'firma' => UploadedFile::fake()->createWithContent('firma.p12', $certificado),
            'clave_firma' => 'Incorrecta',
        ])->assertSessionHasErrors('firma')->assertSessionMissing('_old_input.clave_firma');
        $this->put($ruta, [
            'ruc' => $empresa->ruc, 'razon_social' => $empresa->razon_social,
            'firma' => UploadedFile::fake()->createWithContent('firma.p12', $certificado),
            'clave_firma' => $clave,
        ])->assertRedirect('/configuraciones/empresas');
        $guardado = DB::table('erp_identidad_empresas')->where('id_empresa', $empresa->id)->value('certificado_cifrado');
        $this->assertStringNotContainsString($clave, $guardado);
        $this->assertSame(['pkcs12' => base64_encode($certificado), 'clave' => $clave], json_decode(Crypt::decryptString($guardado), true));
        $this->get($ruta.'/editar')->assertOk()->assertSee('Vencimiento')->assertDontSee($clave);
        $this->get($ruta.'/logo')->assertOk();

        $externa = EmpresaModel::create(['ruc' => '1200000000002', 'razon_social' => 'Empresa externa']);
        $this->actingAs(User::factory()->create(['id_empresa' => $externa->id, 'is_admin' => true]))
            ->get($ruta.'/logo')->assertNotFound();
    }

    public function test_registra_parametros_fiscales_por_vigencia_desde_una_modal(): void
    {
        $empresa = EmpresaModel::create(['ruc' => '1200000000003', 'razon_social' => 'Empresa fiscal']);
        $this->actingAs(User::factory()->create(['id_empresa' => $empresa->id, 'is_admin' => true]));
        $ruta = '/configuraciones/empresas/parametros';
        $this->get($ruta.'/crear')->assertOk()->assertSee('data-ajax-form')->assertSee('IRBP');
        $tarifa = ['concepto' => 'iva', 'codigo' => 'PRUEBA', 'nombre' => 'Tarifa de prueba', 'unidad' => 'porcentaje', 'valor' => '10.25', 'vigente_desde' => '2026-11-01'];
        $this->postJson($ruta, $tarifa)->assertOk();
        $this->postJson($ruta, $tarifa)->assertUnprocessable()->assertJsonValidationErrors('vigente_desde');
        $this->postJson($ruta, array_replace($tarifa, ['vigente_desde' => '2026-12-01', 'valor' => '11.00']))->assertOk();
        $this->postJson($ruta, array_replace($tarifa, ['vigente_desde' => '2027-01-01', 'unidad' => 'usd-unidad']))
            ->assertUnprocessable()->assertJsonValidationErrors('unidad');
        $this->get($ruta)->assertOk()->assertSee('10.25 %')->assertSee('2026-12-01');
        $this->assertDatabaseCount('erp_parametros_fiscales', 2);
        $this->get('/configuraciones/sucursales/crear')->assertOk()->assertSee('data-ajax-form');
    }

    private function certificado(string $clave): string
    {
        $config = tempnam(sys_get_temp_dir(), 'rh-p12-');
        file_put_contents($config, "[req]\ndistinguished_name = dn\n[dn]\n");
        try {
            $opciones = ['config' => $config, 'private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048];
            $privada = openssl_pkey_new($opciones);
            $solicitud = openssl_csr_new(['commonName' => 'Empresa de prueba'], $privada, $opciones);
            $x509 = openssl_csr_sign($solicitud, null, $privada, 10, $opciones);
            $this->assertTrue(openssl_pkcs12_export($x509, $pkcs12, $privada, $clave));

            return $pkcs12;
        } finally {
            unlink($config);
        }
    }
}
