<?php

namespace App\Domain\City\Service;

class CheckReport
{
    private $name;
    private $status;

    public function __construct(string $name, bool $status)
    {
        $this->name = $name;
        $this->status = $status;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function status(): bool
    {
        return $this->status;
    }
}