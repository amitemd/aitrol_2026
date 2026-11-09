<?php

namespace Sis_medico\Services;

/** Restricts stored documents to passive formatting; never stores executable HTML. */
class OfficeHtmlSanitizer
{
    public function clean($html)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.(string) $html.'</body></html>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $body = $dom->getElementsByTagName('body')->item(0);
        $this->walk($body);
        $result = '';
        foreach ($body->childNodes as $node) {
            $result .= $dom->saveHTML($node);
        }
        return $result;
    }

    private function walk(\DOMNode $parent)
    {
        $allowed = explode(' ', 'p br div span strong b em i u s strike sub sup h1 h2 h3 h4 h5 h6 blockquote pre code ul ol li table thead tbody tfoot tr th td caption colgroup col hr a img');
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node->nodeType === XML_TEXT_NODE) {
                continue;
            }
            if (!$node instanceof \DOMElement || in_array(strtolower($node->nodeName), ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'template', 'form', 'input', 'button', 'link', 'meta', 'base'])) {
                $parent->removeChild($node);
                continue;
            }
            $this->walk($node);
            $tag = strtolower($node->nodeName);
            if (!in_array($tag, $allowed)) {
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
                continue;
            }
            foreach (iterator_to_array($node->attributes) as $attribute) {
                $name = strtolower($attribute->name);
                $value = trim($attribute->value);
                $safe = in_array($name, ['title', 'alt']);
                if (in_array($name, ['colspan', 'rowspan', 'width', 'height', 'start', 'border', 'cellpadding', 'cellspacing'])) {
                    $safe = (bool) preg_match('/^\d{1,4}%?$/D', $value);
                }
                if ($tag === 'a' && $name === 'href') {
                    $safe = (bool) preg_match('~^(https?://|mailto:)[^\s<>]+$~iD', $value);
                }
                if ($tag === 'img' && $name === 'src') {
                    // Embedded raster images only: documents make no external image requests.
                    $safe = (bool) preg_match('~^data:image/(png|jpeg|gif|webp);base64,[a-zA-Z0-9+/=]+$~D', $value);
                }
                if ($name === 'style') {
                    $value = $this->style($value);
                    $safe = $value !== '';
                }
                if ($safe) {
                    $node->setAttribute($name, $value);
                } else {
                    $node->removeAttribute($attribute->name);
                }
            }
            if ($tag === 'img' && !$node->hasAttribute('src')) {
                $parent->removeChild($node);
            }
        }
    }

    private function style($style)
    {
        $allowed = explode(' ', 'color background-color font-family font-size font-weight font-style text-align text-decoration line-height margin-left margin-right margin-top margin-bottom padding padding-left padding-right padding-top padding-bottom width height border border-width border-style border-color border-collapse vertical-align list-style-type');
        $clean = [];
        foreach (explode(';', $style) as $declaration) {
            $pair = explode(':', $declaration, 2);
            if (count($pair) !== 2) continue;
            $property = strtolower(trim($pair[0]));
            $value = trim($pair[1]);
            if (in_array($property, ['color', 'background-color', 'border-color']) && preg_match('/^rgba?\(\s*[\d.%,\s]+\)$/iD', $value)) {
                $clean[] = $property.':'.$value;
                continue;
            }
            if (in_array($property, $allowed) && preg_match('/^[a-zA-Z0-9\s#.,%\-\x27"]+$/D', $value)) {
                $clean[] = $property.':'.$value;
            }
        }
        return implode(';', $clean);
    }
}
