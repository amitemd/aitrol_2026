<?php

namespace Aitrol\Clientes\Infrastructure\Persistence;

use App\Services\EmpresaActiva;
use Aitrol\Clientes\Domain\ClienteNoEncontrado;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class ArchivosCliente
{
    public function guardar(Closure $guardarCliente, ?UploadedFile $foto, array $adjuntos): void
    {
        if ($foto === null && $adjuntos === []) {
            $guardarCliente();

            return;
        }

        $disco = Storage::disk('local');
        $nuevos = [];
        $fotoAnterior = null;

        try {
            app(EmpresaActiva::class)->transaccion(function () use ($guardarCliente, $foto, $adjuntos, &$nuevos, &$fotoAnterior): void {
                $id = $guardarCliente()->id;
                $cliente = ClienteModel::query()->lockForUpdate()->findOrFail($id);
                $persona = $cliente->persona()->lockForUpdate()->firstOrFail();

                if ($foto !== null) {
                    $fotoAnterior = $persona->foto;
                    $persona->foto = $nuevos[] = $this->guardarArchivo($foto, 'clientes/fotos');
                    $persona->save();
                }

                if ($adjuntos !== []) {
                    $documentos = $cliente->documentos ?? [];
                    foreach ($adjuntos as $adjunto) {
                        $ruta = $nuevos[] = $this->guardarArchivo($adjunto, 'clientes/documentos');
                        $documentos[] = [
                            'id' => (string) Str::uuid(),
                            'nombre' => Str::limit(preg_replace('/[\x00-\x1F\x7F]/u', '', $adjunto->getClientOriginalName()) ?: 'Documento', 180, ''),
                            'ruta' => $ruta,
                        ];
                    }
                    $cliente->documentos = $documentos;
                    $cliente->save();
                }
            }, intentos: 1);
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
        $cliente = ClienteModel::query()->with('persona')->find($id)
            ?? throw new ClienteNoEncontrado('Cliente no encontrado.');

        return [
            'tieneFoto' => $cliente->persona?->foto !== null,
            'documentos' => $cliente->documentos ?? [],
        ];
    }

    public function foto(int $id): StreamedResponse
    {
        $ruta = $this->modelo($id)->persona?->foto;
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

    private function modelo(int $id): ClienteModel
    {
        return ClienteModel::query()->findOrFail($id);
    }

    private function guardarArchivo(UploadedFile $archivo, string $directorio): string
    {
        $ruta = $archivo->store($directorio, 'local');

        return $ruta ?: throw new RuntimeException('No se pudo guardar el archivo del cliente.');
    }
}
