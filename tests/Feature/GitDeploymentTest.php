<?php

namespace Tests\Feature;

use App\Services\CommandRunner;
use App\Services\GitDeployment;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class GitDeploymentTest extends TestCase
{
    public function test_rejects_a_privileged_helper_without_a_deployment_user(): void
    {
        config()->set([
            'deployment.git.helper' => '/usr/local/sbin/aitrol-git-pull',
            'deployment.git.user' => null,
        ]);

        $runner = Mockery::mock(CommandRunner::class);
        $runner->shouldNotReceive('run');

        $status = (new GitDeployment($runner))->status();

        $this->assertFalse($status['available']);
        $this->assertFalse($status['writable']);
        $this->assertSame(
            'Defina DEPLOY_GIT_USER cuando configure DEPLOY_GIT_HELPER.',
            $status['error'],
        );
    }

    public function test_reports_the_privileged_helper_as_ready_when_its_check_succeeds(): void
    {
        config()->set([
            'deployment.git.helper' => '/usr/local/sbin/aitrol-git-pull',
            'deployment.git.user' => 'aitrol-deploy',
        ]);

        $runner = $this->mockReadyRepository(helperCheck: $this->process(successful: true));

        $status = (new GitDeployment($runner))->status();

        $this->assertSame('helper', $status['mode']);
        $this->assertTrue($status['writable']);
        $this->assertNull($status['error']);
    }

    public function test_reports_the_helper_error_when_privileged_access_is_not_configured(): void
    {
        config()->set([
            'deployment.git.helper' => '/usr/local/sbin/aitrol-git-pull',
            'deployment.git.user' => 'aitrol-deploy',
        ]);

        $runner = $this->mockReadyRepository(helperCheck: $this->process(
            successful: false,
            error: 'sudo: a password is required',
        ));

        $status = (new GitDeployment($runner))->status();

        $this->assertSame('helper', $status['mode']);
        $this->assertFalse($status['writable']);
        $this->assertSame(
            'El ayudante privilegiado no está listo: sudo: a password is required',
            $status['error'],
        );
    }

    public function test_pull_uses_only_the_configured_privileged_helper(): void
    {
        config()->set([
            'deployment.git.helper' => '/usr/local/sbin/aitrol-git-pull',
            'deployment.git.user' => 'aitrol-deploy',
        ]);

        $runner = Mockery::mock(CommandRunner::class);
        $runner->shouldReceive('run')
            ->once()
            ->with(
                ['sudo', '-n', '-H', '-u', 'aitrol-deploy', '/usr/local/sbin/aitrol-git-pull'],
                base_path(),
                300.0,
            )
            ->andReturn($this->process(successful: true, output: 'Already up to date.'));

        $deployment = Mockery::mock(GitDeployment::class, [$runner])->makePartial();
        $deployment->shouldReceive('status')->once()->andReturn($this->readyStatus());

        $result = $deployment->pull();

        $this->assertTrue($result['successful']);
        $this->assertStringContainsString('$ sudo -n -H -u aitrol-deploy /usr/local/sbin/aitrol-git-pull', $result['output']);
        $this->assertStringContainsString('Already up to date.', $result['output']);
    }

    private function mockReadyRepository(Process $helperCheck): MockInterface
    {
        $runner = Mockery::mock(CommandRunner::class);
        $runner->shouldReceive('run')->once()->andReturn($this->process(successful: true, output: 'true'));
        $runner->shouldReceive('run')->once()->andReturn($this->process(successful: true, output: 'main'));
        $runner->shouldReceive('run')->once()->andReturn($this->process(successful: true, output: 'abc1234'));
        $runner->shouldReceive('run')->once()->andReturn($this->process(successful: true));
        $runner->shouldReceive('run')
            ->once()
            ->with(
                ['sudo', '-n', '-H', '-u', 'aitrol-deploy', '/usr/local/sbin/aitrol-git-pull', '--check'],
                base_path(),
                10.0,
            )
            ->andReturn($helperCheck);

        return $runner;
    }

    private function process(bool $successful, string $output = '', string $error = ''): Process
    {
        $process = Mockery::mock(Process::class);
        $process->shouldReceive('isSuccessful')->andReturn($successful);
        $process->shouldReceive('getOutput')->andReturn($output);
        $process->shouldReceive('getErrorOutput')->andReturn($error);

        return $process;
    }

    /**
     * @return array{
     *     enabled: true,
     *     available: true,
     *     clean: true,
     *     writable: true,
     *     mode: 'helper',
     *     branch: 'main',
     *     remote: 'origin',
     *     target: 'main',
     *     commit: 'abc1234',
     *     error: null
     * }
     */
    private function readyStatus(): array
    {
        return [
            'enabled' => true,
            'available' => true,
            'clean' => true,
            'writable' => true,
            'mode' => 'helper',
            'branch' => 'main',
            'remote' => 'origin',
            'target' => 'main',
            'commit' => 'abc1234',
            'error' => null,
        ];
    }
}
