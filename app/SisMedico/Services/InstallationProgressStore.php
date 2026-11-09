<?php

namespace Sis_medico\Services;

class InstallationProgressStore
{
    public function read()
    {
        $path = $this->statePath();
        if (!is_file($path)) {
            return $this->defaults();
        }

        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return $this->defaults();
        }

        @flock($handle, LOCK_SH);
        $contents = stream_get_contents($handle);
        @flock($handle, LOCK_UN);
        fclose($handle);

        $state = json_decode((string) $contents, true);
        return is_array($state) ? array_merge($this->defaults(), $state) : $this->defaults();
    }

    public function write(array $state)
    {
        $this->ensureDirectory();
        $state = array_merge($this->defaults(), $state);
        $state['updated_at'] = date('c');
        file_put_contents(
            $this->statePath(),
            json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
        return $state;
    }

    /**
     * Only one request may modify the installation at a time. A second tab or
     * a refreshed page can read progress without starting duplicate migrations.
     */
    public function acquireLock()
    {
        $this->ensureDirectory();
        $handle = @fopen($this->lockPath(), 'c+');
        if (!$handle || !@flock($handle, LOCK_EX | LOCK_NB)) {
            if ($handle) {
                fclose($handle);
            }
            return null;
        }

        ftruncate($handle, 0);
        fwrite($handle, (string) getmypid());
        fflush($handle);
        return $handle;
    }

    public function releaseLock($handle)
    {
        if (is_resource($handle)) {
            @flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public function isResumable(array $state)
    {
        return in_array($state['status'], array('running', 'pending'), true)
            && !empty($state['phase'])
            && empty($state['completed']);
    }

    public function clear()
    {
        if (is_file($this->statePath())) {
            @unlink($this->statePath());
        }
    }

    private function defaults()
    {
        return array(
            'status' => 'idle',
            'phase' => null,
            'offset' => 0,
            'total' => 0,
            'percent' => 0,
            'current_table' => null,
            'message' => null,
            'completed' => false,
            'started_at' => null,
            'migration_start_applied' => null,
            'migration_total' => null,
            'error_code' => null,
            'user_message' => null,
            'technical_message' => null,
            'resolution' => null,
            'retryable' => true,
            'auto_fix' => false,
            'updated_at' => null,
        );
    }

    private function ensureDirectory()
    {
        $directory = dirname($this->statePath());
        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('No fue posible preparar el almacenamiento del instalador.');
        }
    }

    private function statePath()
    {
        return storage_path('framework/cache/aitrol-installation-progress.json');
    }

    private function lockPath()
    {
        return storage_path('framework/cache/aitrol-installation-progress.lock');
    }
}
