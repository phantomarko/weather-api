<?php

namespace App\Infrastructure\City\Repository;

use App\Application\Common\Service\TemperatureConverter\TemperatureConverterInterface;
use App\Domain\Common\Service\HttpRequesterInterface;
use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Common\ValueObject\Temperature;
use App\Domain\Common\ValueObject\TemperatureUnit;
use DateTime;

class OpenWeatherCityRepository implements CityRepositoryInterface
{
    private $requester;
    private $apiUrl;
    private $apiKey;
    private $temperatureConverter;

    public function __construct(
        HttpRequesterInterface $requester,
        string $apiUrl,
        string $apiKey,
        TemperatureConverterInterface $temperatureConverter
    ) {
        $this->requester = $requester;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->temperatureConverter = $temperatureConverter;
    }

    public function findCityByName(string $name): ?City
    {
        $data = $this->requester->get($this->apiUrl . '?appid=' . $this->apiKey . '&q=' . $name . ',de');

        if (
            is_array($data)
            && isset($data['main']['temp'])
            && isset($data['id'])
            && isset($data['name'])
            && isset($data['name'])
            && isset($data['sys']['sunrise'])
            && isset($data['sys']['sunset'])
        ) {
            $kelvinTemperature = new Temperature(
                $data['main']['temp'],
                new TemperatureUnit(TemperatureUnit::KELVIN)
            );
            $temperature = $this->temperatureConverter->convert($kelvinTemperature);

            $city = new City(
                $data['id'],
                $data['name'],
                $temperature,
                DateTime::createFromFormat('U', $data['sys']['sunrise']),
                DateTime::createFromFormat('U', $data['sys']['sunset'])
            );
        } else {
            $city = null;
        }

        return $city;
    }
}