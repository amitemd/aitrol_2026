<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;

class MinifyHtmlResponse
{
    /**
     * Reduce indentation whitespace in V3 HTML responses without modifying
     * whitespace-sensitive elements or executable inline content.
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!config('performance.html_minify', false)
            || !method_exists($response, 'getContent')
            || !method_exists($response, 'setContent')
            || !isset($response->headers)
            || stripos((string) $response->headers->get('Content-Type'), 'text/html') === false) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || $content === '' || stripos($content, '<html') === false) {
            return $response;
        }

        $response->setContent($this->minify($content));
        $response->headers->remove('Content-Length');

        return $response;
    }

    public function minify($html)
    {
        $blocks = array();
        $protected = preg_replace_callback(
            '#<(pre|textarea|script|style)\b[^>]*>.*?</\1\s*>#is',
            function ($match) use (&$blocks) {
                $token = '___AITROL_V3_HTML_BLOCK_'.count($blocks).'___';
                $blocks[$token] = $match[0];

                return $token;
            },
            $html
        );

        if (!is_string($protected)) {
            return $html;
        }

        // Keep one separator between tags: this avoids joining text from
        // adjacent inline elements while eliminating Blade indentation.
        $protected = preg_replace('/>\s+</', '> <', $protected);
        $protected = preg_replace('/(?:\r\n|\r|\n)[\t ]+/', "\n", $protected);
        $protected = preg_replace('/(?:\r\n|\r|\n){2,}/', "\n", $protected);

        return strtr(trim($protected), $blocks);
    }
}
