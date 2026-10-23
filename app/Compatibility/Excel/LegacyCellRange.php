<?php

namespace App\Compatibility\Excel;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class LegacyCellRange
{
    public function __construct(
        private readonly Worksheet $worksheet,
        private readonly string $range,
    ) {}

    public function setValue(mixed $value): self
    {
        $coordinate = str_contains($this->range, ':') ? explode(':', $this->range)[0] : $this->range;
        $this->worksheet->setCellValue($coordinate, $value);

        return $this;
    }

    public function setBorder(string|array $top = 'thin', ?string $right = null, ?string $bottom = null, ?string $left = null): self
    {
        if (is_array($top)) {
            $this->style()->applyFromArray(['borders' => $top]);

            return $this;
        }

        $this->style()->getBorders()->applyFromArray([
            'top' => ['borderStyle' => $this->borderStyle($top)],
            'right' => ['borderStyle' => $this->borderStyle($right ?? $top)],
            'bottom' => ['borderStyle' => $this->borderStyle($bottom ?? $top)],
            'left' => ['borderStyle' => $this->borderStyle($left ?? $top)],
        ]);

        return $this;
    }

    public function setAlignment(string $alignment): self
    {
        $this->style()->getAlignment()->setHorizontal(strtolower($alignment));

        return $this;
    }

    public function setValignment(string $alignment): self
    {
        $this->style()->getAlignment()->setVertical(strtolower($alignment));

        return $this;
    }

    public function setFontWeight(string $weight): self
    {
        $this->style()->getFont()->setBold(strtolower($weight) === 'bold');

        return $this;
    }

    public function setFontColor(string $color): self
    {
        $this->style()->getFont()->getColor()->setARGB($this->argb($color));

        return $this;
    }

    public function setFontSize(float|int $size): self
    {
        $this->style()->getFont()->setSize((float) $size);

        return $this;
    }

    public function setBackground(string $color, string $type = Fill::FILL_SOLID): self
    {
        $fill = $this->style()->getFill();
        $fill->setFillType($type === 'solid' ? Fill::FILL_SOLID : $type);
        $fill->getStartColor()->setARGB($this->argb($color));

        return $this;
    }

    public function setTextIndent(int $indent): self
    {
        $this->style()->getAlignment()->setIndent($indent);

        return $this;
    }

    public function setDataType(string $type): self
    {
        $coordinate = str_contains($this->range, ':') ? explode(':', $this->range)[0] : $this->range;
        $this->worksheet->getCell($coordinate)->setDataType($type);

        return $this;
    }

    public function getStyle()
    {
        return $this->style();
    }

    public function getNumberFormat()
    {
        return $this->style()->getNumberFormat();
    }

    private function style()
    {
        return $this->worksheet->getStyle($this->range);
    }

    private function argb(string $color): string
    {
        $hex = strtoupper(ltrim($color, '#'));

        return strlen($hex) === 6 ? 'FF'.$hex : $hex;
    }

    private function borderStyle(?string $style): string
    {
        return match (strtolower((string) $style)) {
            'none' => Border::BORDER_NONE,
            'medium' => Border::BORDER_MEDIUM,
            'thick' => Border::BORDER_THICK,
            'double' => Border::BORDER_DOUBLE,
            default => Border::BORDER_THIN,
        };
    }
}
