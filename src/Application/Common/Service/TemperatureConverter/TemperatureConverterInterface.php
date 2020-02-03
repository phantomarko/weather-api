<?php

namespace App\Application\Common\Service\TemperatureConverter;

use App\Domain\Common\ValueObject\Temperature;

interface TemperatureConverterInterface
{
    public function convert(Temperature $temperature): Temperature;
}