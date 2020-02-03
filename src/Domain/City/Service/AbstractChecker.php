<?php

namespace App\Domain\City\Service;

use App\Domain\City\Entity\City;

abstract class AbstractChecker
{
    private $parentChecker;

    public function __construct(?AbstractChecker $parentChecker = null)
    {
        $this->parentChecker = $parentChecker;
    }

    abstract public function check(City $city): array;

    public function parentChecker(): ?AbstractChecker
    {
        return $this->parentChecker;
    }
}