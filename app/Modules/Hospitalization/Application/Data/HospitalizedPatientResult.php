<?php

namespace App\Modules\Hospitalization\Application\Data;

use stdClass;

final readonly class HospitalizedPatientResult
{
    public function __construct(
        public string $date,
        public string $patientId,
        public ?string $cause,
        public ?string $doctorId,
        public string $roomName,
        public string $status,
        public string $requestId,
        public ?string $firstName,
        public ?string $middleName,
        public ?string $lastName,
        public ?string $secondLastName,
    ) {}

    public function toLegacyRecord(): stdClass
    {
        return (object) [
            'fecha' => $this->date,
            'id_paciente' => $this->patientId,
            'causa' => $this->cause,
            'id_doctor' => $this->doctorId,
            'nombre_sala' => $this->roomName,
            'estado' => $this->status,
            'id_solicitud' => $this->requestId,
            'paciente' => (object) [
                'nombre1' => $this->firstName,
                'nombre2' => $this->middleName,
                'apellido1' => $this->lastName,
                'apellido2' => $this->secondLastName,
            ],
        ];
    }
}
