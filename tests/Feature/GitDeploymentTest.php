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
    public function test_reports_the_repository_as_ready_using_direct_git_commands(): void
    {
        config()->set([
            'deployment.git.helper' => '/usr/local/sbin/aitrol-git-pull',
            'deployment.git.user' => 'aitrol-deploy',
        ]);

        $runner = $this->mockReadyRepository();

        $status = (new GitDeployment($runner))->status();

        $this->assertSame('direct', $status['mode']);
        $this->assertTrue($status['available']);
        $this->assertTrue($status['writable']);
        $this->assertNull($status['error']);
    }

    public function test_pull_uses_git_directly_without_sudo(): void
    {
        $runner = Mockery::mock(CommandRunner::class);
        $runner->shouldReceive('run')
            ->once()
            ->with(
                ['git', '-c', 'safe.directory='.base_path(), 'pull', '--ff-only', 'origin', 'main'],
                base_path(),
                300.0,
            )
            ->andReturn($this->process(successful: true, output: 'Already up to date.'));

        $deployment = Mockery::mock(GitDeployment::class, [$runner])->makePartial();
        $deployment->shouldReceive('status')->once()->andReturn($this->readyStatus());

        $result = $deployment->pull();

        $this->assertTrue($result['successful']);
        $this->assertStringContainsString('$ git -c safe.directory=', $result['output']);
        $this->assertStringNotContainsString('sudo', $result['output']);
        $this->assertStringContainsString('Already up to date.', $result['output']);
    }

    private function mockReadyRepository(): MockInterface
    {
        $runner = Mockery::mock(CommandRunner::class);
        $runner->shouldReceive('run')->once()->andReturn($this->process(successful: true, output: 'true'));
        $runner->shouldReceive('run')->once()->andReturn($this->process(successful: true, output: 'main'));
        $runner->shouldReceive('run')->once()->andReturn($this->process(successful: true, output: 'abc1234'));
        $runner->shouldReceive('run')->once()->andReturn($this->process(successful: true));

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
     *     mode: 'direct',
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
            'mode' => 'direct',
            'branch' => 'main',
            'remote' => 'origin',
            'target' => 'main',
            'commit' => 'abc1234',
            'error' => null,
        ];
    }
}
