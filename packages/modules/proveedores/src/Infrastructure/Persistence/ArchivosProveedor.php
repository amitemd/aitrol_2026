<?php

namespace Aitrol\Proveedores\Infrastructure\Persistence;

use Aitrol\Proveedores\Domain\ProveedorNoEncontrado;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class ArchivosProveedor
{
    public function guardar(Closure $guardarProveedor, ?UploadedFile $foto, array $adjuntos): void
    {
        if ($foto === null && $adjuntos === []) {
            $guardarProveedor();

            return;
        }

        $disco = Storage::disk('local');
        $nuevos = [];
        $fotoAnterior = null;

        try {
            (new ProveedorModel)->getConnection()->transaction(function () use ($guardarProveedor, $foto, $adjuntos, &$nuevos, &$fotoAnterior): void {
                $id = $guardarProveedor()->id;
                $proveedor = ProveedorModel::query()->lockForUpdate()->findOrFail($id);

                if ($foto !== null) {
                    $fotoAnterior = $proveedor->foto;
                    $proveedor->foto = $nuevos[] = $this->guardarArchivo($foto, 'proveedores/fotos');
                }

                if ($adjuntos !== []) {
                    $documentos = $proveedor->documentos ?? [];
                    foreach ($adjuntos as $adjunto) {
                        $ruta = $nuevos[] = $this->guardarArchivo($adjunto, 'proveedores/documentos');
                        $documentos[] = [
                            'id' => (string) Str::uuid(),
                            'nombre' => Str::limit(preg_replace('/[\x00-\x1F\x7F]/u', '', $adjunto->getClientOriginalName()) ?: 'Documento', 180, ''),
                            'ruta' => $ruta,
                        ];
                    }
                    $proveedor->documentos = $documentos;
                }
                $proveedor->save();
            });
        } catch (Throwable $error) {
            $disco->delete($nuevos);

            throw $error;
        }

        if ($fotoAnterior !== null) {
            $disco->delete($fotoAnterior);
        }
    }

    public function ficha(int $id): array
    {
        $proveedor = ProveedorModel::query()->find($id)
            ?? throw new ProveedorNoEncontrado('Proveedor no encontrado.');

        return [
            'tieneFoto' => $proveedor->foto !== null,
            'documentos' => $proveedor->documentos ?? [],
        ];
    }

    public function foto(int $id): StreamedResponse
    {
        $ruta = $this->modelo($id)->foto;
        $disco = Storage::disk('local');
        abort_unless($ruta && $disco->exists($ruta), 404);

        return $disco->response($ruta, headers: ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function documento(int $id, string $archivo): StreamedResponse
    {
        $registro = collect($this->modelo($id)->documentos)->firstWhere('id', $archivo);
        $disco = Storage::disk('local');
        abort_unless($registro && $disco->exists($registro['ruta']), 404);

        return $disco->download($registro['ruta'], $registro['nombre'], ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    private function modelo(int $id): ProveedorModel
    {
        return ProveedorModel::query()->findOrFail($id);
    }

    private function guardarArchivo(UploadedFile $archivo, string $directorio): string
    {
        $ruta = $archivo->store($directorio, 'local');

        return $ruta ?: throw new RuntimeException('No se pudo guardar el archivo del proveedor.');
    }
}
