<?php

namespace Sis_medico\Support;

use Closure;
use Illuminate\Support\Facades\DB;

final class LegacyMysqlCompatibility
{
    public static function enableForCurrentSession(): void
    {
        if (! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        $originalSqlMode = (string) DB::scalar('SELECT @@SESSION.sql_mode');
        $compatibleSqlMode = self::withoutLegacyZeroDateRestrictions($originalSqlMode);

        if ($compatibleSqlMode !== $originalSqlMode) {
            DB::statement('SET SESSION sql_mode = ?', [$compatibleSqlMode]);
        }
    }

    public static function run(Closure $operation): void
    {
        if (! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            $operation();

            return;
        }

        $originalSqlMode = (string) DB::scalar('SELECT @@SESSION.sql_mode');
        $compatibleSqlMode = self::withoutLegacyZeroDateRestrictions($originalSqlMode);

        if ($compatibleSqlMode === $originalSqlMode) {
            $operation();

            return;
        }

        DB::statement('SET SESSION sql_mode = ?', [$compatibleSqlMode]);

        try {
            $operation();
        } finally {
            DB::statement('SET SESSION sql_mode = ?', [$originalSqlMode]);
        }
    }

    private static function withoutLegacyZeroDateRestrictions(string $sqlMode): string
    {
        return collect(explode(',', $sqlMode))
            ->map(static fn (string $mode): string => trim($mode))
            ->reject(static fn (string $mode): bool => in_array(strtoupper($mode), [
                'NO_ZERO_DATE',
                'NO_ZERO_IN_DATE',
            ], true))
            ->implode(',');
    }
}
