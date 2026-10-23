<?php

namespace App\Compatibility\Excel;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class LegacyReader
{
    private ?Spreadsheet $spreadsheet = null;

    /** @var list<int> */
    private array $sheetIndexes = [0];

    private bool $formatDates = false;

    private string $dateFormat = 'Y-m-d H:i:s';

    public function filter(string $filter): self
    {
        return $this;
    }

    public function selectSheetsByIndex(int ...$indexes): self
    {
        $this->sheetIndexes = $indexes ?: [0];

        return $this;
    }

    public function load(string $path, ?Closure $callback = null): self
    {
        $resolved = $this->resolvePath($path);
        $this->spreadsheet = IOFactory::load($resolved);

        if ($callback) {
            $callback($this);
        }

        return $this;
    }

    public function formatDates(bool $enabled = true, string $format = 'Y-m-d H:i:s'): self
    {
        $this->formatDates = $enabled;
        $this->dateFormat = $format;

        return $this;
    }

    public function setActiveSheetIndex(int $index): self
    {
        $this->spreadsheet()->setActiveSheetIndex($index);
        $this->sheetIndexes = [$index];

        return $this;
    }

    public function getHighestRow(): int
    {
        return $this->spreadsheet()->getActiveSheet()->getHighestRow();
    }

    public function get(): Collection
    {
        return $this->rows();
    }

    public function toArray(): array
    {
        return $this->rows()->toArray();
    }

    public function chunk(int $size, Closure $callback): self
    {
        $this->rows()->chunk($size)->each(fn (Collection $rows) => $callback($rows));

        return $this;
    }

    private function rows(): Collection
    {
        $rows = collect();

        foreach ($this->sheetIndexes as $sheetIndex) {
            $sheet = $this->spreadsheet()->getSheet($sheetIndex);
            $rawRows = $sheet->toArray(null, true, true, false);

            if ($rawRows === []) {
                continue;
            }

            $headers = array_map(
                fn (mixed $header, int $index) => $this->header($header, $index),
                array_shift($rawRows),
                array_keys($rawRows[0] ?? []),
            );

            foreach ($rawRows as $rawRow) {
                $row = [];
                foreach ($headers as $index => $header) {
                    $row[$header] = $rawRow[$index] ?? null;
                }
                $rows->push((object) $row);
            }
        }

        return $rows;
    }

    private function header(mixed $header, int $index): string
    {
        $normalized = Str::of((string) $header)->ascii()->snake()->toString();

        return $normalized !== '' ? $normalized : 'column_'.($index + 1);
    }

    private function spreadsheet(): Spreadsheet
    {
        return $this->spreadsheet ?? throw new \LogicException('Primero debe cargar un archivo de Excel.');
    }

    private function resolvePath(string $path): string
    {
        foreach ([$path, base_path($path), storage_path('app/'.$path), public_path($path)] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        throw new \RuntimeException("No se encontró el archivo de Excel: {$path}");
    }
}
