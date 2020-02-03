<?php

namespace App\Domain\City\Service;

use App\Domain\City\Exception\CheckerAliasNotFoundException;
use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Repository\CityRepositoryInterface;

class CheckerFactory
{
    private $cityRepository;
    private $rivalName;

    public function __construct(CityRepositoryInterface $cityRepository, string $rivalName)
    {
        $this->cityRepository = $cityRepository;
        $this->rivalName = $rivalName;
    }

    public function getCheckerByAlias(string $alias, ?AbstractChecker $parentChecker = null): AbstractChecker
    {
        if ($alias === OddNameLengthChecker::ALIAS) {
            $checker = $this->getOddNameLengthChecker($parentChecker);
        } elseif ($alias === WarmerThanRivalChecker::ALIAS) {
            $checker = $this->getWarmerThanRivalChecker($parentChecker);
        } elseif ($alias === TemperatureBetweenValuesChecker::ALIAS) {
            $checker = $this->getTemperatureBetweenValuesChecker($parentChecker);
        } else {
            throw new CheckerAliasNotFoundException();
        }

        return $checker;
    }

    private function getOddNameLengthChecker(?AbstractChecker $parentChecker): OddNameLengthChecker
    {
        return new OddNameLengthChecker($parentChecker);
    }

    private function getWarmerThanRivalChecker(?AbstractChecker $parentChecker): WarmerThanRivalChecker
    {
        $rival = $this->cityRepository->findCityByName($this->rivalName);
        if (empty($rival)) {
            throw new CityNotFoundException();
        }

        return new WarmerThanRivalChecker(
            $parentChecker, $rival
        );
    }

    private function getTemperatureBetweenValuesChecker(
        ?AbstractChecker $parentChecker
    ): TemperatureBetweenValuesChecker {
        return new TemperatureBetweenValuesChecker($parentChecker);
    }
}