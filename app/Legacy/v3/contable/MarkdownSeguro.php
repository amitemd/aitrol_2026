<?php

namespace Sis_medico\v3\contable;

class MarkdownSeguro
{
    /**
     * Convierte el subconjunto documental de Markdown a HTML seguro.
     * Compatible con PHP 7.1 y sin dependencias externas.
     *
     * @param  string  $markdown
     * @return string
     */
    public static function render($markdown)
    {
        $lineas = preg_split('/\r\n|\r|\n/', (string) $markdown);
        $html = [];
        $enCodigo = false;
        $tipoLista = null;
        $total = count($lineas);

        for ($indice = 0; $indice < $total; $indice++) {
            $linea = rtrim($lineas[$indice]);
            $limpia = trim($linea);

            if (preg_match('/^```/', $limpia)) {
                self::cerrarLista($html, $tipoLista);
                if ($enCodigo) {
                    $html[] = '</code></pre>';
                    $enCodigo = false;
                } else {
                    $html[] = '<pre class="bg-light border rounded p-3"><code>';
                    $enCodigo = true;
                }
                continue;
            }

            if ($enCodigo) {
                $html[] = htmlspecialchars($linea, ENT_QUOTES, 'UTF-8') . "\n";
                continue;
            }

            if ($indice + 1 < $total && self::esFilaTabla($linea) && self::esSeparadorTabla($lineas[$indice + 1])) {
                self::cerrarLista($html, $tipoLista);
                $html[] = '<div class="table-responsive"><table class="table table-sm table-bordered table-hover">';
                $html[] = '<thead class="thead-light"><tr>';
                foreach (self::celdas($linea) as $celda) {
                    $html[] = '<th>' . self::inline($celda) . '</th>';
                }
                $html[] = '</tr></thead><tbody>';
                $indice += 2;
                while ($indice < $total && self::esFilaTabla($lineas[$indice])) {
                    $html[] = '<tr>';
                    foreach (self::celdas($lineas[$indice]) as $celda) {
                        $html[] = '<td>' . self::inline($celda) . '</td>';
                    }
                    $html[] = '</tr>';
                    $indice++;
                }
                $html[] = '</tbody></table></div>';
                $indice--;
                continue;
            }

            if ($limpia === '') {
                self::cerrarLista($html, $tipoLista);
                continue;
            }

            if (preg_match('/^(#{1,4})\s+(.+)$/', $limpia, $coincidencia)) {
                self::cerrarLista($html, $tipoLista);
                $nivel = strlen($coincidencia[1]) + 2;
                $html[] = '<h' . $nivel . ' class="mt-4 mb-3">' . self::inline($coincidencia[2]) . '</h' . $nivel . '>';
                continue;
            }

            if (preg_match('/^[-*]\s+(.+)$/', $limpia, $coincidencia)) {
                self::abrirLista($html, $tipoLista, 'ul');
                $html[] = '<li>' . self::inline($coincidencia[1]) . '</li>';
                continue;
            }

            if (preg_match('/^\d+\.\s+(.+)$/', $limpia, $coincidencia)) {
                self::abrirLista($html, $tipoLista, 'ol');
                $html[] = '<li>' . self::inline($coincidencia[1]) . '</li>';
                continue;
            }

            if (preg_match('/^>\s*(.+)$/', $limpia, $coincidencia)) {
                self::cerrarLista($html, $tipoLista);
                $html[] = '<blockquote class="blockquote border-left pl-3">' . self::inline($coincidencia[1]) . '</blockquote>';
                continue;
            }

            self::cerrarLista($html, $tipoLista);
            $html[] = '<p>' . self::inline($limpia) . '</p>';
        }

        self::cerrarLista($html, $tipoLista);
        if ($enCodigo) {
            $html[] = '</code></pre>';
        }

        return implode("\n", $html);
    }

    private static function inline($texto)
    {
        $seguro = htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
        $seguro = preg_replace('/`([^`]+)`/', '<code>$1</code>', $seguro);
        $seguro = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $seguro);

        return $seguro;
    }

    private static function abrirLista(array &$html, &$tipoActual, $nuevoTipo)
    {
        if ($tipoActual === $nuevoTipo) {
            return;
        }
        self::cerrarLista($html, $tipoActual);
        $html[] = '<' . $nuevoTipo . ' class="pl-4">';
        $tipoActual = $nuevoTipo;
    }

    private static function cerrarLista(array &$html, &$tipoActual)
    {
        if ($tipoActual !== null) {
            $html[] = '</' . $tipoActual . '>';
            $tipoActual = null;
        }
    }

    private static function esFilaTabla($linea)
    {
        $linea = trim($linea);
        return $linea !== '' && strpos($linea, '|') !== false && !self::esSeparadorTabla($linea);
    }

    private static function esSeparadorTabla($linea)
    {
        $linea = trim($linea);
        return (bool) preg_match('/^\|?\s*:?-{3,}:?\s*(\|\s*:?-{3,}:?\s*)+\|?$/', $linea);
    }

    private static function celdas($linea)
    {
        return array_map('trim', explode('|', trim(trim($linea), '|')));
    }
}
