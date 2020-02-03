<?php

namespace App\Application\City\Service\CheckCity;

use App\Domain\City\Service\CheckReport;

class CheckCityResponse
{
    private $check;
    private $criteria;

    public function __construct(bool $check, array $criteria)
    {
        $this->check = $check;
        $this->criteria = $criteria;
    }

    public function check(): bool
    {
        return $this->check;
    }

    public function criteria(): array
    {
        return $this->criteria;
    }

    public static function fromCheckReports(array $checkReports): self
    {
        $array = [];
        foreach ($checkReports as $checkReport) {
            if ($checkReport instanceof CheckReport) {
                $array[$checkReport->name()] = $checkReport->status();
            }
        }

        $criteria = $array;
        $check = array_reduce($criteria, function ($carry, $status) {
            return $carry ? $status : false;
        }, true);

        return new self($check, $criteria);
    }
}