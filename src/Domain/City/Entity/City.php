<?php

namespace App\Domain\City\Entity;

use App\Domain\Common\ValueObject\Temperature;
use DateTime;

class City
{
    private $id;
    private $name;
    private $temperature;
    private $sunrise;
    private $sunset;

    public function __construct(
        int $id,
        string $name,
        Temperature $temperature,
        DateTime $sunrise,
        DateTime $sunset
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->temperature = $temperature;
        $this->sunrise = $sunrise;
        $this->sunset = $sunset;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function temperature(): Temperature
    {
        return $this->temperature;
    }

    public function isDayTime(DateTime $dateTime): bool
    {
        return $dateTime->getTimestamp() >= $this->sunrise->getTimestamp()
            && $dateTime->getTimestamp() < $this->sunset->getTimestamp();
    }
}