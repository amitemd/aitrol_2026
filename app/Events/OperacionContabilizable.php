<?php

namespace App\Events;

final readonly class OperacionContabilizable
{
    public function __construct(public string $origen, public int $id) {}
}
