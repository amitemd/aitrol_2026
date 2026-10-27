<?php

namespace Tests\Feature;

use App\Services\GitDeployment;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery\MockInterface;
use Sis_medico\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DeploymentPanelTest extends TestCase
{
    public function test_superadministrator_sees_the_git_update_action(): void
    {
        $administrator = $this->createUser(role: 1);

        $this->mock(GitDeployment::class, function (MockInterface $mock): void {
            $mock->shouldReceive('status')
                ->once()
                ->andReturn($this->readyGitStatus());
        });

        $this->actingAs($administrator)
            ->get(route('deployment.index'))
            ->assertOk()
            ->assertSee('Actualizar desde Git')
            ->assertSee('origin/main');
    }

    public function test_panel_explains_how_to_enable_direct_git_access_when_php_cannot_write(): void
    {
        $administrator = $this->createUser(role: 1);
        $status = $this->readyGitStatus();
        $status['writable'] = false;
        $status['mode'] = 'direct';
        $status['error'] = 'El usuario de PHP no tiene permisos de escritura.';

        $this->mock(GitDeployment::class, function (MockInterface $mock) use ($status): void {
            $mock->shouldReceive('status')->once()->andReturn($status);
        });

        $this->actingAs($administrator)
            ->get(route('deployment.index'))
            ->assertOk()
            ->assertSee('Acceso directo de Git no configurado')
            ->assertSee('PHP directo, sin sudo')
            ->assertSee('install-git-direct.sh')
            ->assertSee('bash install-git-direct.sh')
            ->assertDontSee('chmod +x install-git-direct.sh')
            ->assertDontSee('install-git-helper.sh');
    }

    public function test_superadministrator_can_update_the_repository_from_the_deployment_panel(): void
    {
        $administrator = $this->createUser(role: 1);

        $this->mock(GitDeployment::class, function (MockInterface $mock): void {
            $mock->shouldReceive('pull')
                ->once()
                ->andReturn([
                    'successful' => true,
                    'output' => '$ git pull --ff-only origin main'.PHP_EOL.'Already up to date.',
                ]);
        });

        $this->actingAs($administrator)
            ->post(route('deployment.execute'), [
                'operation' => 'git-pull',
                'confirmation' => 'ACTUALIZAR',
            ])
            ->assertRedirect()
            ->assertSessionHas('deployment_result', function (array $result): bool {
                return $result['successful'] === true
                    && $result['title'] === 'Actualizar desde Git'
                    && str_contains($result['output'], 'git pull --ff-only origin main');
            });
    }

    public function test_git_update_requires_the_exact_confirmation(): void
    {
        $administrator = $this->createUser(role: 1);

        $this->mock(GitDeployment::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('pull');
        });

        $this->actingAs($administrator)
            ->post(route('deployment.execute'), [
                'operation' => 'git-pull',
                'confirmation' => 'actualizar',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('confirmation');
    }

    public function test_non_administrator_cannot_update_the_repository(): void
    {
        $user = $this->createUser(role: 2);

        $this->mock(GitDeployment::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('pull');
        });

        $this->withoutExceptionHandling();
        $this->expectException(HttpException::class);

        $this->actingAs($user)
            ->post(route('deployment.execute'), [
                'operation' => 'git-pull',
                'confirmation' => 'ACTUALIZAR',
            ]);
    }

    private function createUser(int $role): User
    {
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('email');
            $table->string('password');
            $table->unsignedInteger('id_tipo_usuario');
        });

        User::query()->insert([
            'id' => 'user-'.$role,
            'email' => "role-{$role}@example.test",
            'password' => bcrypt('password'),
            'id_tipo_usuario' => $role,
        ]);

        return User::query()->findOrFail('user-'.$role);
    }

    /**
     * @return array{
     *     enabled: bool,
     *     available: bool,
     *     clean: bool,
     *     writable: bool,
     *     mode: 'direct',
     *     branch: string,
     *     remote: string,
     *     target: string,
     *     commit: string,
     *     error: null
     * }
     */
    private function readyGitStatus(): array
    {
        return [
            'enabled' => true,
            'available' => true,
            'clean' => true,
            'writable' => true,
            'mode' => 'direct',
            'branch' => 'main',
            'remote' => 'origin',
            'target' => 'main',
            'commit' => 'abc1234',
            'error' => null,
        ];
    }
}
