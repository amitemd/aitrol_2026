<?php

namespace Sis_medico\Exceptions;

use RuntimeException;

class ClinicalClosureBlockedException extends RuntimeException
{
    private $blockers;

    public function __construct(array $blockers)
    {
        parent::__construct('El episodio no puede cerrarse hasta completar los controles obligatorios.');
        $this->blockers = $blockers;
    }

    public function blockers()
    {
        return $this->blockers;
    }
}
