<?php

namespace Sis_medico\Exceptions;

use RuntimeException;

class ClinicalMedicationBlockedException extends RuntimeException
{
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct(implode(' ', $errors));
    }

    public function errors()
    {
        return $this->errors;
    }
}
