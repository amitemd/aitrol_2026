<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class CommandRunner
{
    /**
     * @param  list<string>  $command
     */
    public function run(array $command, string $workingDirectory, float $timeout = 10): Process
    {
        $process = new Process(
            command: $command,
            cwd: $workingDirectory,
            env: ['GIT_TERMINAL_PROMPT' => '0'],
            timeout: $timeout,
        );
        $process->run();

        return $process;
    }
}
