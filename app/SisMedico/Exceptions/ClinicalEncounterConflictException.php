<?php

namespace Sis_medico\Exceptions;

class ClinicalEncounterConflictException extends \RuntimeException
{
    private $conflictingEncounterId;

    public function __construct($message, $conflictingEncounterId)
    {
        parent::__construct($message);
        $this->conflictingEncounterId = $conflictingEncounterId;
    }

    public function conflictingEncounterId()
    {
        return $this->conflictingEncounterId;
    }
}
