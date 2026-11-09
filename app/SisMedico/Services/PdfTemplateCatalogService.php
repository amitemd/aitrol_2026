<?php

namespace Sis_medico\Services;

use Illuminate\Filesystem\Filesystem;

class PdfTemplateCatalogService
{
    private $files;
    private $templates;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function all()
    {
        if ($this->templates !== null) {
            return $this->templates;
        }
        $root = resource_path('views');
        $templates = array();

        if (!$this->files->isDirectory($root)) {
            return $templates;
        }

        foreach ($this->files->allFiles($root) as $file) {
            $path = str_replace('\\', '/', $file->getPathname());
            if (substr($path, -10) !== '.blade.php') {
                continue;
            }

            $relative = substr($path, strlen(str_replace('\\', '/', $root)) + 1, -10);
            $view = str_replace('/', '.', $relative);
            $contents = null;
            if (!$this->looksLikePdf($relative, $file, $contents)) {
                continue;
            }

            if ($contents === null) {
                $contents = $file->getContents();
            }
            $module = $this->moduleName($relative);
            $metadata = $this->knownMetadata($view);
            $templates[$view] = array(
                'view' => $view,
                'name' => isset($metadata['name']) ? $metadata['name'] : $this->displayName($relative),
                'module' => $module,
                'has_embedded_logo' => $this->hasEmbeddedLogo($contents),
                'source' => $relative . '.blade.php',
                'entry_points' => isset($metadata['entry_points']) ? $metadata['entry_points'] : array(),
            );
        }

        uasort($templates, function ($left, $right) {
            return strcasecmp($left['module'] . $left['name'], $right['module'] . $right['name']);
        });

        $this->templates = array_values($templates);
        return $this->templates;
    }

    public function find($view)
    {
        $view = $this->normalizeView($view);
        foreach ($this->all() as $template) {
            if ($template['view'] === $view) {
                return $template;
            }
        }
        return null;
    }

    public function normalizeView($view)
    {
        return trim(str_replace(array('/', '\\'), '.', (string) $view), '.');
    }

    public function references($view)
    {
        $view = $this->normalizeView($view);
        $needles = array($view, str_replace('.', '/', $view));
        $references = array();

        foreach (array(app_path(), base_path('routes')) as $root) {
            if (!$this->files->isDirectory($root)) {
                continue;
            }
            foreach ($this->files->allFiles($root) as $file) {
                if (strtolower($file->getExtension()) !== 'php') {
                    continue;
                }
                $lines = preg_split('/\R/', $file->getContents());
                foreach ($lines as $number => $line) {
                    foreach ($needles as $needle) {
                        if (strpos($line, $needle) !== false) {
                            $references[] = array(
                                'file' => str_replace('\\', '/', substr($file->getPathname(), strlen(base_path()) + 1)),
                                'line' => $number + 1,
                            );
                            break;
                        }
                    }
                    if (count($references) >= 20) {
                        return $references;
                    }
                }
            }
        }

        return $references;
    }

    private function looksLikePdf($relative, $file, &$contents)
    {
        $haystack = strtolower($relative);
        $tokens = array('pdf', 'print', 'imprimir', 'impresion');
        foreach ($tokens as $token) {
            if (strpos($haystack, $token) !== false) {
                return true;
            }
        }

        if (!preg_match('#(?:^|/)(?:receta|orden(?:es)?|formularios?|reportes?|resultados?|facturacion|proforma|retenciones?)(?:/|$)|(?:comprobante|factura|balance|flujo|libro|nota_)#i', $haystack)) {
            return false;
        }

        $contents = $file->getContents();
        return strpos($contents, '@page') !== false;
    }

    private function hasEmbeddedLogo($contents)
    {
        return (bool) preg_match('/(?:logo|membrete)[^\r\n]{0,180}\.(?:png|jpe?g|gif|svg)|\$[^\r\n]{0,80}->logo/i', $contents);
    }

    private function moduleName($relative)
    {
        $parts = explode('/', $relative);
        return $this->humanize(isset($parts[0]) ? $parts[0] : 'general');
    }

    private function displayName($relative)
    {
        $parts = explode('/', $relative);
        $name = end($parts);
        return $this->humanize($name);
    }

    private function humanize($value)
    {
        $value = preg_replace('/[_\-]+/', ' ', $value);
        return mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8');
    }

    private function knownMetadata($view)
    {
        $metadata = array(
            'hc_admision.receta.menbretadaor' => array(
                'name' => 'Receta médica · Membretada',
                'entry_points' => array('Historial de recetas → Membretada'),
            ),
            'laboratorio.orden.resultados_pdf' => array(
                'name' => 'Resultados de laboratorio',
                'entry_points' => array(
                    '/resultados/imprimir/{id_orden}',
                    '/inicio/laboratorio/hc4resultados/imprimir/{id_orden}',
                    '/api/paciente/resultados/pdf/{id_orden}',
                ),
            ),
        );

        return isset($metadata[$view]) ? $metadata[$view] : array();
    }
}
