<?php

namespace Aitrol\Configuracion\Domain;

final readonly class OpcionMenu
{
    public function __construct(
        public int $id,
        public ?int $padreId,
        public string $nombre,
        public ?string $ruta,
        public string $icono,
        public int $orden,
        public bool $activo,
        public bool $soloAdministrador,
        public bool $protegido,
    ) {}
}
