<?php

namespace Sis_medico\Support;

final class InvoiceAdditionalInformation
{
    public static function fromJson($value)
    {
        $items = self::decode($value);
        if (!is_array($items)) {
            return array();
        }

        // También admite la estructura usada por los XML del SRI.
        if (isset($items['campoAdicional'])) {
            $items = $items['campoAdicional'];
        }

        if (self::isInformationItem($items)) {
            $items = array($items);
        }

        $result = array();
        foreach ($items as $item) {
            if (is_object($item)) {
                $item = (array) $item;
            }
            if (!is_array($item)) {
                continue;
            }

            $name = self::firstValue($item, array('nombre', 'name', 'campo', 'descripcion'));
            $itemValue = self::firstValue($item, array('valor', 'value', 'contenido', 'content'));
            if ($name === '' || $itemValue === '') {
                continue;
            }

            $result[] = array('name' => $name, 'value' => $itemValue);
        }

        return $result;
    }

    private static function decode($value)
    {
        if (is_object($value)) {
            return (array) $value;
        }
        if (is_array($value)) {
            return $value;
        }

        $decoded = trim((string) $value);
        if ($decoded === '') {
            return array();
        }

        // Algunos registros históricos quedaron codificados más de una vez.
        // Se limita la recursión para no alterar contenido de texto legítimo.
        for ($attempt = 0; $attempt < 3 && is_string($decoded); $attempt++) {
            $candidate = json_decode($decoded, true);
            if (json_last_error() !== JSON_ERROR_NONE && $attempt === 0) {
                $candidate = json_decode(stripslashes($decoded), true);
            }
            if (json_last_error() !== JSON_ERROR_NONE) {
                return array();
            }
            $decoded = $candidate;
        }

        if (is_object($decoded)) {
            return (array) $decoded;
        }

        return is_array($decoded) ? $decoded : array();
    }

    private static function isInformationItem(array $item)
    {
        return isset($item['nombre']) || isset($item['name'])
            || isset($item['campo']) || isset($item['descripcion']);
    }

    private static function firstValue(array $item, array $keys)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $item)) {
                continue;
            }

            $value = trim((string) $item[$key]);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    public static function merge(array $first, array $second)
    {
        $result = array();
        $seen = array();

        foreach (array_merge($first, $second) as $item) {
            if (!is_array($item)) {
                continue;
            }

            $name = trim((string) (isset($item['name']) ? $item['name'] : (isset($item['nombre']) ? $item['nombre'] : '')));
            $value = trim((string) (isset($item['value']) ? $item['value'] : (isset($item['valor']) ? $item['valor'] : '')));
            if ($name === '' || $value === '') {
                continue;
            }

            $key = mb_strtolower($name, 'UTF-8').'\0'.mb_strtolower($value, 'UTF-8');
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $result[] = array('name' => $name, 'value' => $value);
        }

        return $result;
    }
}
