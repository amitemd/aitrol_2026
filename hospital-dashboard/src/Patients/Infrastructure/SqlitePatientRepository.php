<?php

declare(strict_types=1);

namespace Hospital\Patients\Infrastructure;

use Hospital\Patients\Domain\Patient;
use Hospital\Patients\Domain\PatientRepository;
use PDO;

final readonly class SqlitePatientRepository implements PatientRepository
{
    public function __construct(private PDO $database) {}

    public function add(Patient $patient): void
    {
        $statement = $this->database->prepare('INSERT INTO patients (name, registered_on) VALUES (?, ?)');
        $statement->execute([$patient->name, date('Y-m-d')]);
    }

    public function count(): int
    {
        return (int) $this->database->query('SELECT COUNT(*) FROM patients')->fetchColumn();
    }

    public function search(string $query): array
    {
        $statement = $this->database->prepare('SELECT name, registered_on FROM patients WHERE instr(lower(name), lower(?)) > 0 OR ? = \'\' ORDER BY id DESC LIMIT 12');
        $statement->execute([$query, $query]);

        return $statement->fetchAll();
    }

    public function monthlyActivity(int $year): array
    {
        $statement = $this->database->prepare('SELECT month, total, admitted FROM patient_activity WHERE year = ? ORDER BY month');
        $statement->execute([$year]);

        return $statement->fetchAll();
    }
}
