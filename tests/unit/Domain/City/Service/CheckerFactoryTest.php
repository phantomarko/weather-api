<?php

namespace App\Tests\unit\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\City\Exception\CheckerAliasNotFoundException;
use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\City\Service\CheckerFactory;
use App\Domain\City\Service\OddNameLengthChecker;
use App\Domain\City\Service\TemperatureBetweenValuesChecker;
use App\Domain\City\Service\WarmerThanRivalChecker;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class CheckerFactoryTest extends TestCase
{
    private $prophet;
    private $cityRepositoryMock;
    private $rivalName;
    private $checkerFactory;

    protected function setUp()
    {
        $this->prophet = new Prophet();
        $this->cityRepositoryMock = $this->prophet->prophesize(CityRepositoryInterface::class);
        $this->rivalName = 'Köln';

        $this->checkerFactory = new CheckerFactory($this->cityRepositoryMock->reveal(), $this->rivalName);
    }

    public function testCheckerByAliasOddNameLength()
    {
        $response = $this->checkerFactory->getCheckerByAlias(OddNameLengthChecker::ALIAS);
        $this->assertInstanceOf(OddNameLengthChecker::class, $response);
    }

    public function testCheckerByAliasTemperatureBetweenValues()
    {
        $response = $this->checkerFactory->getCheckerByAlias(TemperatureBetweenValuesChecker::ALIAS);
        $this->assertInstanceOf(TemperatureBetweenValuesChecker::class, $response);
    }

    public function testCheckerByAliasWarmerThanRival()
    {
        $cityMock = $this->prophet->prophesize(City::class);
        $this->cityRepositoryMock->findCityByName($this->rivalName)->willReturn($cityMock->reveal());

        $response = $this->checkerFactory->getCheckerByAlias(WarmerThanRivalChecker::ALIAS);

        $this->assertInstanceOf(WarmerThanRivalChecker::class, $response);
    }

    public function testCheckerByAliasWarmerThanRivalNotFound()
    {
        $this->cityRepositoryMock->findCityByName($this->rivalName)->willReturn(null);

        $this->expectException(CityNotFoundException::class);
        $this->checkerFactory->getCheckerByAlias(WarmerThanRivalChecker::ALIAS);
    }

    public function testCheckerByAliasInvalid()
    {
        $this->expectException(CheckerAliasNotFoundException::class);
        $this->checkerFactory->getCheckerByAlias('invalid alias');
    }

    public function testCheckerByAliasParentChecker()
    {
        $checker = $this->prophet->prophesize(OddNameLengthChecker::class);

        $response = $this->checkerFactory->getCheckerByAlias(TemperatureBetweenValuesChecker::ALIAS, $checker->reveal());
        $this->assertInstanceOf(TemperatureBetweenValuesChecker::class, $response);
        $this->assertInstanceOf(OddNameLengthChecker::class, $response->parentChecker());
    }
}
