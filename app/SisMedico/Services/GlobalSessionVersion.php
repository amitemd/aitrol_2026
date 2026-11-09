<?php

namespace Sis_medico\Services;

/**
 * Server-wide generation used to revoke every authenticated session.
 */
class GlobalSessionVersion
{
    private $path;

    public function __construct($path = null)
    {
        $this->path = $path ?: storage_path('framework/global-session-version');
    }

    public function current()
    {
        return $this->withLockedFile(LOCK_EX, function ($handle, $value) {
            return $value;
        });
    }

    public function rotate()
    {
        return $this->withLockedFile(LOCK_EX, function ($handle, $value) {
            $next = $value + 1;
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, (string) $next);
            fflush($handle);

            return $next;
        });
    }

    private function withLockedFile($lockType, callable $callback)
    {
        $directory = dirname($this->path);
        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('No fue posible preparar el control global de sesiones.');
        }

        $handle = @fopen($this->path, 'c+');
        if (!$handle) {
            throw new \RuntimeException('No fue posible abrir el control global de sesiones.');
        }

        try {
            if (!flock($handle, $lockType)) {
                throw new \RuntimeException('No fue posible bloquear el control global de sesiones.');
            }

            rewind($handle);
            $stored = trim((string) stream_get_contents($handle));
            $value = ctype_digit($stored) && (int) $stored > 0 ? (int) $stored : 1;

            if ($stored === '') {
                rewind($handle);
                fwrite($handle, (string) $value);
                fflush($handle);
            }

            return $callback($handle, $value);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
