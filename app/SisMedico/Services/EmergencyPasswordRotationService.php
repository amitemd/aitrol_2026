<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Sis_medico\Support\RequestSchema as Schema;
use Illuminate\Support\Str;

class EmergencyPasswordRotationService
{
    private $lockPath;

    public function __construct($lockPath = null)
    {
        $this->lockPath = $lockPath ?: storage_path('framework/emergency-password-rotation.lock');
    }

    /**
     * Replace every account password with an independent, undisclosed secret.
     *
     * The query builder is intentional: User has a company scope, while this
     * emergency operation must cover the global users table exactly once.
     */
    public function rotateAll()
    {
        @set_time_limit(0);

        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'password')) {
            throw new \RuntimeException('La tabla global de usuarios no está disponible.');
        }

        $lock = $this->acquireLock();

        try {
            return DB::transaction(function () {
                $ids = DB::table('users')->orderBy('id')->lockForUpdate()->pluck('id');
                $columns = array_flip(Schema::getColumnListing('users'));
                $changedAt = date('Y-m-d H:i:s');
                $affected = 0;

                foreach ($ids as $id) {
                    // The plaintext only exists in memory for this iteration and
                    // is never returned, logged, persisted or shown to an operator.
                    $values = array(
                        'password' => Hash::make(Str::random(96)),
                    );

                    if (isset($columns['remember_token'])) {
                        $values['remember_token'] = Str::random(60);
                    }
                    if (isset($columns['password_changed_at'])) {
                        $values['password_changed_at'] = $changedAt;
                    }
                    if (isset($columns['updated_at'])) {
                        $values['updated_at'] = $changedAt;
                    }

                    $affected += DB::table('users')->where('id', $id)->update($values);
                }

                // Previously issued email-reset links must not survive an
                // emergency credential rotation.
                foreach (array('password_resets', 'password_reset_tokens') as $resetTable) {
                    if (Schema::hasTable($resetTable)) {
                        DB::table($resetTable)->delete();
                    }
                }

                if ($affected !== count($ids)) {
                    throw new \RuntimeException('No fue posible rotar todas las cuentas; no se aplicó ningún cambio parcial.');
                }

                return $affected;
            }, 3);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function acquireLock()
    {
        $directory = dirname($this->lockPath);
        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('No fue posible preparar el bloqueo de seguridad.');
        }

        $lock = @fopen($this->lockPath, 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) {
                fclose($lock);
            }
            throw new \RuntimeException('Ya existe una rotación global de claves en ejecución.');
        }

        return $lock;
    }
}
