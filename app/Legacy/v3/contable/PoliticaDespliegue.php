<?php

namespace Sis_medico\v3\contable;

class PoliticaDespliegue
{
    public static function versionPhpEsperada()
    {
        return '7.1.33';
    }

    public static function versionLaravelEsperada()
    {
        return '5.4';
    }

    public static function evaluarVersiones($versionPhp, $versionLaravel)
    {
        return [
            'php' => (string) $versionPhp === self::versionPhpEsperada(),
            'laravel' => strpos((string) $versionLaravel, self::versionLaravelEsperada()) !== false,
        ];
    }

    /**
     * Componentes administrados en el servidor que nunca deben sobrescribirse
     * ciegamente desde la copia Git de desarrollo.
     *
     * @return array
     */
    public static function componentesServidor()
    {
        return [
            ['nombre' => 'Entorno', 'ruta' => '.env', 'tipo' => 'archivo'],
            ['nombre' => 'Composer', 'ruta' => 'composer.json', 'tipo' => 'archivo'],
            ['nombre' => 'Dependencias', 'ruta' => 'vendor/autoload.php', 'tipo' => 'archivo'],
            ['nombre' => 'Arranque', 'ruta' => 'bootstrap/app.php', 'tipo' => 'archivo'],
            ['nombre' => 'Recursos publicos', 'ruta' => 'public', 'tipo' => 'directorio'],
            ['nombre' => 'Almacenamiento', 'ruta' => 'storage', 'tipo' => 'directorio'],
        ];
    }
}
