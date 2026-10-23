<?php

namespace App\Compatibility\Excel;

use Closure;

class LegacyExcel
{
    private static ?LegacyReader $pendingReader = null;

    public static function create(string $filename, Closure $callback): LegacyWorkbook
    {
        $workbook = new LegacyWorkbook($filename);
        $callback($workbook);

        return $workbook;
    }

    public static function filter(string $filter): self
    {
        self::$pendingReader = (new LegacyReader)->filter($filter);

        return new self;
    }

    public static function selectSheetsByIndex(int ...$indexes): self
    {
        self::$pendingReader = (new LegacyReader)->selectSheetsByIndex(...$indexes);

        return new self;
    }

    public static function load(string $path, ?Closure $callback = null): LegacyReader
    {
        $reader = self::$pendingReader ?? new LegacyReader;
        self::$pendingReader = null;

        return $reader->load($path, $callback);
    }

    public static function download(mixed $export, string $type = 'xlsx', ?array $headers = null)
    {
        if ($export instanceof LegacyWorkbook) {
            return $export->download($type, $headers ?? []);
        }

        if (is_object($export) && method_exists($export, 'download')) {
            return $export->download($type, $headers ?? []);
        }

        throw new \InvalidArgumentException('El objeto recibido no es una exportación compatible.');
    }
}
