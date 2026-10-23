<?php

namespace App\Compatibility\Excel;

use Closure;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class LegacyWorkbook
{
    private Spreadsheet $spreadsheet;

    private bool $firstSheetUsed = false;

    public function __construct(private readonly string $filename)
    {
        $this->spreadsheet = new Spreadsheet;
    }

    public function sheet(string $title, Closure $callback): self
    {
        $sheet = $this->firstSheetUsed
            ? $this->spreadsheet->createSheet()
            : $this->spreadsheet->getActiveSheet();

        $this->firstSheetUsed = true;
        $sheet->setTitle(mb_substr($title ?: 'Hoja 1', 0, 31));
        $callback(new LegacyWorksheet($sheet));

        return $this;
    }

    public function getActiveSheet(): LegacyWorksheet
    {
        $this->firstSheetUsed = true;

        return new LegacyWorksheet($this->spreadsheet->getActiveSheet());
    }

    public function setCreator(string $value): self
    {
        $this->spreadsheet->getProperties()->setCreator($value);

        return $this;
    }

    public function setTitle(string $value): self
    {
        $this->spreadsheet->getProperties()->setTitle($value);

        return $this;
    }

    public function setDescription(string $value): self
    {
        $this->spreadsheet->getProperties()->setDescription($value);

        return $this;
    }

    public function export(string $type = 'xlsx', array $headers = []): BinaryFileResponse
    {
        return $this->download($type, $headers);
    }

    public function download(string $type = 'xlsx', array $headers = []): BinaryFileResponse
    {
        $extension = $this->extension($type);
        $path = tempnam(sys_get_temp_dir(), 'aitrol_excel_').'.'.$extension;
        IOFactory::createWriter($this->spreadsheet, $this->writerType($type))->save($path);

        return response()->download($path, $this->filename.'.'.$extension, $headers)->deleteFileAfterSend();
    }

    public function store(string $type = 'xlsx', ?string $path = null): string
    {
        return $this->save($type, $path);
    }

    public function save(string $type = 'xlsx', ?string $path = null): string
    {
        $extension = $this->extension($type);
        $target = $path ?: storage_path('app/'.$this->filename.'.'.$extension);

        if (is_dir($target)) {
            $target = rtrim($target, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->filename.'.'.$extension;
        }

        $directory = dirname($target);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        IOFactory::createWriter($this->spreadsheet, $this->writerType($type))->save($target);

        return $target;
    }

    public function spreadsheet(): Spreadsheet
    {
        return $this->spreadsheet;
    }

    private function writerType(string $type): string
    {
        return match (strtolower($type)) {
            'xls' => 'Xls',
            'csv' => 'Csv',
            'html' => 'Html',
            default => 'Xlsx',
        };
    }

    private function extension(string $type): string
    {
        return match (strtolower($type)) {
            'xls', 'csv', 'html' => strtolower($type),
            default => 'xlsx',
        };
    }
}
