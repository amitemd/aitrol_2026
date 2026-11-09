<?php

namespace Sis_medico\Support;

/**
 * Makes the legacy PHPExcel value binder compatible with modern PHP.
 *
 * PHPExcel 1.8 reads the first character of a value before checking whether
 * it is an integer. On PHP 7.4+ this throws when an integer is assigned to a
 * cell. This binder preserves PHPExcel's original type detection order while
 * checking scalar types safely first.
 */
class SafePHPExcelValueBinder implements \PHPExcel_Cell_IValueBinder
{
    /**
     * Bind a value to a PHPExcel cell without indexing numeric values.
     *
     * @param \PHPExcel_Cell $cell
     * @param mixed $value
     * @return bool
     */
    public function bindValue(\PHPExcel_Cell $cell, $value = null)
    {
        if (is_string($value)) {
            $value = \PHPExcel_Shared_String::SanitizeUTF8($value);
        } elseif (is_object($value)) {
            if ($value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (!($value instanceof \PHPExcel_RichText)) {
                $value = (string) $value;
            }
        }

        $cell->setValueExplicit($value, $this->dataTypeForValue($value));

        return true;
    }

    /**
     * Determine the PHPExcel data type without assuming the value is a string.
     *
     * @param mixed $value
     * @return string
     */
    private function dataTypeForValue($value = null)
    {
        if ($value === null) {
            return \PHPExcel_Cell_DataType::TYPE_NULL;
        }

        if ($value === '') {
            return \PHPExcel_Cell_DataType::TYPE_STRING;
        }

        if ($value instanceof \PHPExcel_RichText) {
            return \PHPExcel_Cell_DataType::TYPE_INLINE;
        }

        if (is_bool($value)) {
            return \PHPExcel_Cell_DataType::TYPE_BOOL;
        }

        if (is_int($value) || is_float($value)) {
            return \PHPExcel_Cell_DataType::TYPE_NUMERIC;
        }

        if (is_string($value) && strlen($value) > 1 && $value[0] === '=') {
            return \PHPExcel_Cell_DataType::TYPE_FORMULA;
        }

        if (is_string($value) && preg_match('/^[\+\-]?([0-9]+\.?[0-9]*|[0-9]*\.?[0-9]+)([Ee][\-\+]?[0-2]?\d{1,3})?$/', $value)) {
            $unsignedValue = ltrim($value, '+-');

            if ($unsignedValue[0] === '0' && strlen($unsignedValue) > 1 && $unsignedValue[1] !== '.') {
                return \PHPExcel_Cell_DataType::TYPE_STRING;
            }

            if (strpos($value, '.') === false && $value > PHP_INT_MAX) {
                return \PHPExcel_Cell_DataType::TYPE_STRING;
            }

            return \PHPExcel_Cell_DataType::TYPE_NUMERIC;
        }

        if (is_string($value) && array_key_exists($value, \PHPExcel_Cell_DataType::getErrorCodes())) {
            return \PHPExcel_Cell_DataType::TYPE_ERROR;
        }

        return \PHPExcel_Cell_DataType::TYPE_STRING;
    }
}
