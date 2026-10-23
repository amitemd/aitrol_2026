<?php

namespace App\Compatibility\Excel;

use Closure;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class LegacyWorksheet
{
    public function __construct(private readonly Worksheet $worksheet) {}

    public function cell(string $coordinate, mixed $value): self
    {
        $range = new LegacyCellRange($this->worksheet, $coordinate);

        if ($value instanceof Closure) {
            $value($range);
        } else {
            $range->setValue($value);
        }

        return $this;
    }

    public function cells(string $range, Closure $callback): self
    {
        $callback(new LegacyCellRange($this->worksheet, $range));

        return $this;
    }

    public function row(int $row, array|Closure $values): self
    {
        if ($values instanceof Closure) {
            $values(new LegacyCellRange($this->worksheet, $row.':'.$row));
        } else {
            $this->worksheet->fromArray($values, null, 'A'.$row);
        }

        return $this;
    }

    public function fromArray(array $data, mixed $nullValue = null, string $startCell = 'A1', bool $strictNullComparison = false, bool $headingGeneration = false): self
    {
        $this->worksheet->fromArray($data, $nullValue, $startCell, $strictNullComparison);

        return $this;
    }

    public function mergeCells(string $range): self
    {
        $this->worksheet->mergeCells($range);

        return $this;
    }

    public function setWidth(string|array $column, float|int|null $width = null): self
    {
        foreach (is_array($column) ? $column : [$column => $width] as $name => $value) {
            $this->worksheet->getColumnDimension((string) $name)->setWidth((float) $value);
        }

        return $this;
    }

    public function setHeight(int|array $row, float|int|null $height = null): self
    {
        foreach (is_array($row) ? $row : [$row => $height] as $number => $value) {
            $this->worksheet->getRowDimension((int) $number)->setRowHeight((float) $value);
        }

        return $this;
    }

    public function setAutoSize(bool|array $columns = true): self
    {
        if (is_array($columns)) {
            foreach ($columns as $column) {
                $this->worksheet->getColumnDimension((string) $column)->setAutoSize(true);
            }

            return $this;
        }

        if ($columns) {
            foreach (range('A', $this->worksheet->getHighestColumn()) as $column) {
                $this->worksheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        return $this;
    }

    public function setColumnFormat(array $formats): self
    {
        foreach ($formats as $range => $format) {
            $this->worksheet->getStyle($range)->getNumberFormat()->setFormatCode($format);
        }

        return $this;
    }

    public function setCellValueExplicit(string $coordinate, mixed $value, string $dataType = DataType::TYPE_STRING): self
    {
        $this->worksheet->setCellValueExplicit($coordinate, $value, $dataType);

        return $this;
    }

    public function setFreeze(string $coordinate): self
    {
        $this->worksheet->freezePane($coordinate);

        return $this;
    }

    public function setAutoFilter(string $range): self
    {
        $this->worksheet->setAutoFilter($range);

        return $this;
    }

    public function setStyle(array $style): self
    {
        $this->worksheet->getStyle($this->worksheet->calculateWorksheetDimension())->applyFromArray($style);

        return $this;
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->worksheet->{$method}(...$arguments);
    }
}
