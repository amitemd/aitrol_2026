<?php

namespace Aitrol\Inventarios\Infrastructure\Persistence;

use App\Services\EmpresaActiva;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final readonly class GaleriaProducto
{
    public const MAXIMO = 8;

    public function __construct(private EmpresaActiva $empresa) {}

    public function ficha(?int $producto): array
    {
        $fotos = $producto === null ? collect() : $this->fotos($producto);

        return [
            'version' => $this->version($fotos),
            'imagenes' => $fotos->values()->map(fn (stdClass $foto) => [
                'id' => $foto->id,
                'url' => route('inventarios.productos.imagen', [$foto->id_producto, $foto->id], absolute: false),
            ]),
        ];
    }

    public function imagen(int $producto, int $imagen): StreamedResponse
    {
        $registro = $this->tabla($producto)->where('id', $imagen)->first();
        $disco = Storage::disk('local');
        abort_unless($registro && $disco->exists($registro->ruta), 404);

        return $disco->response($registro->ruta, headers: ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function guardar(Closure $guardarProducto, ?int $producto, string $version, array $orden, array $archivos): stdClass
    {
        $disco = Storage::disk('local');
        $nuevas = [];
        try {
            foreach ($archivos as $indice => $archivo) {
                $ruta = $archivo->store('inventarios/'.$this->empresa->id().'/productos', 'local');
                $nuevas[$indice] = $ruta ?: throw new RuntimeException('No se pudo guardar la imagen.');
            }

            [$registro, $retiradas] = $this->empresa->transaccion(function () use ($guardarProducto, $producto, $version, $orden, $nuevas): array {
                $actuales = $producto === null ? collect() : $this->fotos($producto);
                if (! hash_equals($this->version($actuales), $version)) {
                    throw ValidationException::withMessages(['orden_fotos' => 'La galería cambió. Vuelve a abrir la ficha para actualizar su orden.']);
                }

                $existentes = $actuales->keyBy(fn (stdClass $foto) => 'e:'.$foto->id);
                $entrantes = collect($nuevas)->keyBy(fn ($ruta, $indice) => 'n:'.$indice);
                if (count($orden) > self::MAXIMO || count(array_unique($orden)) !== count($orden)
                    || array_diff($orden, $existentes->keys()->merge($entrantes->keys())->all()) !== []
                    || array_diff($entrantes->keys()->all(), $orden) !== []) {
                    throw ValidationException::withMessages(['orden_fotos' => 'Revisa las imágenes y vuelve a ordenar la galería.']);
                }

                $registro = $guardarProducto();
                $filas = $this->tabla($registro->id);
                $quitar = $existentes->except($orden);
                if ($quitar->isNotEmpty()) {
                    (clone $filas)->whereIn('id', $quitar->pluck('id'))->delete();
                }
                foreach ($orden as $posicion => $clave) {
                    if ($existentes->has($clave)) {
                        (clone $filas)->where('id', $existentes[$clave]->id)->update(['orden' => $posicion, 'updated_at' => now()]);
                    } else {
                        DB::table('inv_producto_imagenes')->insert([
                            'id_empresa' => $this->empresa->id(), 'id_producto' => $registro->id, 'orden' => $posicion,
                            'ruta' => $entrantes[$clave], 'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                }

                return [$registro, $quitar->pluck('ruta')->all()];
            }, intentos: 1);
        } catch (Throwable $error) {
            if ($nuevas !== []) $disco->delete(array_values($nuevas));
            throw $error;
        }

        if ($retiradas !== []) $disco->delete($retiradas);

        return $registro;
    }

    private function tabla(int $producto): Builder
    {
        return DB::table('inv_producto_imagenes')->where('id_empresa', $this->empresa->id())->where('id_producto', $producto);
    }

    private function fotos(int $producto): Collection
    {
        return $this->tabla($producto)->orderBy('orden')->orderBy('id')->get();
    }

    private function version(Collection $fotos): string
    {
        return hash('sha256', $fotos->pluck('id')->implode(','));
    }
}
