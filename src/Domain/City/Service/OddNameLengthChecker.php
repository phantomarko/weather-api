<?php

namespace App\Domain\City\Service;

use App\Domain\City\Entity\City;

class OddNameLengthChecker extends AbstractChecker
{
    public const ALIAS = 'naming';

    public function check(City $city): array
    {
        $checkReport = new CheckReport(
            self::ALIAS,
            strlen($city->name()) % 2
        );

        return empty($this->parentChecker())
            ? [$checkReport]
            : array_merge($this->parentChecker()->check($city), [$checkReport]);
    }
}