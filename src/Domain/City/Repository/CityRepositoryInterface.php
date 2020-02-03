<?php

namespace App\Domain\City\Repository;

use App\Domain\City\Entity\City;

interface CityRepositoryInterface
{
    public function findCityByName(string $name): ?City;
}