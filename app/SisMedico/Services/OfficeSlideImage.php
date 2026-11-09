<?php
namespace Sis_medico\Services;

use Illuminate\Validation\ValidationException;

class OfficeSlideImage
{
    public function clean($uri)
    {
        if (!is_string($uri) || !preg_match('~^data:image/(png|jpeg|svg\+xml);base64,([A-Za-z0-9+/=]+)$~D', $uri, $m)) $this->invalid();
        $bytes = base64_decode($m[2], true);
        if ($bytes === false || strlen($bytes) > 1000000) $this->invalid();
        if ($m[1] !== 'svg+xml') {
            $info = @getimagesizefromstring($bytes);
            if (!$info || $info['mime'] !== 'image/'.$m[1]) $this->invalid();
            return $uri;
        }
        if (str_contains($bytes, "\0") || preg_match('/<!DOCTYPE|<!ENTITY/i', $bytes)) $this->invalid();
        $dom = new \DOMDocument;
        $old = libxml_use_internal_errors(true);
        try { $valid = $dom->loadXML($bytes, LIBXML_NONET); }
        finally { libxml_clear_errors(); libxml_use_internal_errors($old); }
        if (!$valid || $dom->documentElement->localName !== 'svg' || $dom->documentElement->namespaceURI !== 'http://www.w3.org/2000/svg') $this->invalid();
        $tags = ['svg','g','defs','path','rect','circle','ellipse','line','polyline','polygon','linearGradient','radialGradient','stop','clipPath','mask','title','desc','style'];
        $attributes = ['id','class','x','y','x1','x2','y1','y2','cx','cy','r','rx','ry','width','height','viewBox','d','points','transform','fill','fill-rule','fill-opacity','stroke','stroke-width','stroke-linecap','stroke-linejoin','stroke-opacity','stroke-miterlimit','stroke-dasharray','opacity','offset','stop-color','stop-opacity','gradientUnits','gradientTransform','spreadMethod','clip-path','clip-rule','mask','maskUnits','maskContentUnits','preserveAspectRatio','overflow','style'];
        foreach (iterator_to_array($dom->getElementsByTagName('*')) as $node) {
            if (!in_array($node->localName, $tags) || $node->namespaceURI !== 'http://www.w3.org/2000/svg') { $node->parentNode?->removeChild($node); continue; }
            foreach (iterator_to_array($node->attributes) as $attribute) {
                if (!in_array($attribute->name, $attributes) || !$this->safeCss($attribute->value)) $node->removeAttributeNode($attribute);
            }
            if ($node->localName === 'style' && !$this->safeCss($node->textContent)) $node->parentNode->removeChild($node);
        }
        // Return only the root: excludes processing instructions and external stylesheets.
        return 'data:image/svg+xml;base64,'.base64_encode($dom->saveXML($dom->documentElement));
    }

    private function safeCss($value)
    {
        if (preg_match('~[\\\\@<>]|/\*|(?:https?|data|javascript):|expression\s*\(|-moz-binding|behavior\s*:~i', $value)) return false;
        return !preg_match('/url\s*\(/i', preg_replace('/url\(\s*#[a-zA-Z0-9_-]+\s*\)/i', '', $value));
    }

    private function invalid() { throw ValidationException::withMessages(['content' => 'Imagen de diapositiva inválida. Usa PNG, JPG o SVG seguro de hasta 1 MB.']); }
}
