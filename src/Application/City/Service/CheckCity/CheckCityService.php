<?php

namespace App\Application\City\Service\CheckCity;

use App\Application\City\Exception\CheckCityCriteriaEmptyException;
use App\Domain\City\Entity\City;
use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\City\Service\AbstractChecker;
use App\Domain\City\Service\CheckerFactory;

class CheckCityService
{
    private $cityRepository;
    private $criteriaToCheck;
    private $checkerFactory;

    public function __construct(
        CityRepositoryInterface $cityRepository,
        array $criteriaToCheck,
        CheckerFactory $checkerFactory
    ) {
        $this->cityRepository = $cityRepository;
        $this->criteriaToCheck = $criteriaToCheck;
        $this->checkerFactory = $checkerFactory;
    }

    public function execute(CheckCityRequest $request): CheckCityResponse
    {
        $city = $this->getCityByName($request->cityName());
        $checker = $this->getChecker();
        $cityChecks = $checker->check($city);

        return CheckCityResponse::fromCheckReports($cityChecks);
    }

    private function getCityByName(string $name): City
    {
        $city = $this->cityRepository->findCityByName($name);
        if (empty($city)) {
            throw new CityNotFoundException();
        }

        return $city;
    }

    private function getChecker(): AbstractChecker
    {
        if (empty($this->criteriaToCheck)) {
            throw new CheckCityCriteriaEmptyException();
        }

        $checker = null;
        for ($key = 0; $key < count($this->criteriaToCheck); $key++) {
            if (
                isset($this->criteriaToCheck[$key]['active'])
                && $this->criteriaToCheck[$key]['active']
            ) {
                $checker = $this->checkerFactory->getCheckerByAlias($this->criteriaToCheck[$key]['alias'], $checker);
            }
        }

        if (!$checker instanceof AbstractChecker) {
            throw new CheckCityCriteriaEmptyException();
        }

        return $checker;
    }
}