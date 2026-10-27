<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_root_url_shows_the_new_system_login_for_guests(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Iniciar sesión')
            ->assertSee('Acceso seguro')
            ->assertSee('Ingresa tus credenciales para acceder a tu cuenta.')
            ->assertSee('Acceso exclusivo para usuarios autorizados')
            ->assertSee('Gestiona toda tu organización')
            ->assertSee('Contabilidad y finanzas conectadas')
            ->assertSee('Gestión médica integral')
            ->assertSee('action="'.route('new-system.login.store').'"', false)
            ->assertDontSee('Laravel 13');

        $this->assertFileExists(public_path('images/login/erp-administracion.png'));
        $this->assertFileExists(public_path('images/login/erp-contabilidad.png'));
        $this->assertFileExists(public_path('images/login/erp-medicina.png'));
    }

    public function test_the_root_url_shows_the_new_system_dashboard_for_authenticated_users(): void
    {
        $user = $this->createNewSystemRecords();

        $response = $this->actingAs($user)->get('/');

        $response
            ->assertOk()
            ->assertSee('Sistema Médico')
            ->assertSee('Plataforma Laravel 13')
            ->assertSee('Los módulos conservan sus permisos, rutas y operación')
            ->assertSee('class="ieced-user-email"', false)
            ->assertSee('admin@example.com')
            ->assertSee('Cuenta activa')
            ->assertSee('.ieced-user-menu {', false)
            ->assertSeeInOrder(['js/new-system-navigation.js', 'js/custom.js'], false)
            ->assertDontSee('Ingresa tus credenciales para acceder a tu cuenta.');
    }

    public function test_successful_login_returns_to_the_root_url(): void
    {
        $this->createNewSystemRecords();

        $response = $this->post('/acceder', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirectToRoute('new-system.dashboard');
        $this->assertAuthenticatedAs(User::query()->findOrFail('admin'));
    }

    public function test_login_keeps_a_default_company_for_legacy_modules_when_user_has_multiple_companies(): void
    {
        $this->createNewSystemRecords();
        $this->createSecondaryCompany();

        $this->post('/acceder', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirectToRoute('new-system.dashboard');

        $this->assertSame('empresa-2', session('id_empresa'));
    }

    public function test_dashboard_recovers_a_missing_company_context_for_legacy_modules(): void
    {
        $user = $this->createNewSystemRecords();
        $this->createSecondaryCompany();

        $this->actingAs($user)
            ->withSession([])
            ->get('/')
            ->assertOk();

        $this->assertSame('empresa-2', session('id_empresa'));
    }

    public function test_new_system_pages_redirect_guests_to_the_root_url(): void
    {
        $this->get('/usuarios')->assertRedirect('/');
    }

    private function createNewSystemRecords(): User
    {
        Schema::dropIfExists('usuario_empresa');
        Schema::dropIfExists('empresa');
        Schema::dropIfExists('log_usuario');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('nombre1');
            $table->string('apellido1');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedInteger('id_tipo_usuario');
            $table->unsignedTinyInteger('estado');
        });
        Schema::create('empresa', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('razonsocial');
            $table->string('nombrecomercial');
            $table->string('nombre_corto')->nullable();
            $table->string('ciudad')->nullable();
            $table->unsignedTinyInteger('estado');
            $table->integer('prioridad')->default(0);
        });
        Schema::create('usuario_empresa', function (Blueprint $table): void {
            $table->string('id_usuario');
            $table->string('id_empresa');
        });
        Schema::create('log_usuario', function (Blueprint $table): void {
            $table->id();
            $table->string('id_usuario');
            $table->string('ip_usuario');
            $table->string('descripcion');
            $table->timestamps();
        });
        DB::table('users')->insert([
            'id' => 'admin',
            'nombre1' => 'Administrador',
            'apellido1' => 'AITROL',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'id_tipo_usuario' => 1,
            'estado' => 1,
        ]);
        DB::table('empresa')->insert([
            'id' => 'empresa-1',
            'razonsocial' => 'AITROL S.A.',
            'nombrecomercial' => 'AITROL',
            'nombre_corto' => 'AITROL',
            'ciudad' => 'Quito',
            'estado' => 1,
            'prioridad' => 1,
        ]);
        DB::table('usuario_empresa')->insert([
            'id_usuario' => 'admin',
            'id_empresa' => 'empresa-1',
        ]);

        return User::query()->findOrFail('admin');
    }

    private function createSecondaryCompany(): void
    {
        DB::table('empresa')->insert([
            'id' => 'empresa-2',
            'razonsocial' => 'AITROL DOS S.A.',
            'nombrecomercial' => 'AITROL DOS',
            'nombre_corto' => 'AITROL 2',
            'ciudad' => 'Guayaquil',
            'estado' => 1,
            'prioridad' => 2,
        ]);
    }
}
