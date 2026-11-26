<?php

namespace Sis_medico\Exceptions;

class ClinicalDocumentIncompleteException extends \RuntimeException
{
    private $missingFields;

    public function __construct(array $missingFields)
    {
        $this->missingFields = $missingFields;
        parent::__construct('No se puede firmar el documento. Complete: '.implode(', ', $missingFields).'.');
    }

    public function missingFields()
    {
        return $this->missingFields;
    }
}
