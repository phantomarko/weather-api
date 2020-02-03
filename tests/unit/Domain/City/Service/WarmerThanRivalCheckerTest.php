<?php

namespace App\Tests\unit\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\City\Service\CheckReport;
use App\Domain\City\Service\OddNameLengthChecker;
use App\Domain\City\Service\WarmerThanRivalChecker;
use App\Domain\Common\Exception\TemperaturesUnitNotMatchException;
use App\Domain\Common\ValueObject\Temperature;
use App\Domain\Common\ValueObject\TemperatureUnit;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class WarmerThanRivalCheckerTest extends TestCase
{
    private $prophet;
    private $rivalMock;

    protected function setUp()
    {
        $this->prophet = new Prophet();
        $this->rivalMock = $this->prophet->prophesize(City::class);
    }

    /**
     * @dataProvider warmerCheckerDataProvider
     */
    public function testWarmerChecker($rivalDegrees, $cityDegrees, $expectedStatus)
    {
        $unit = TemperatureUnit::CELSIUS;

        $rivalTemperatureMock = $this->prophet->prophesize(Temperature::class);
        $rivalTemperatureMock->unitValue()->willReturn($unit);
        $rivalTemperatureMock->degrees()->willReturn($rivalDegrees);
        $this->rivalMock->temperature()->willReturn($rivalTemperatureMock->reveal());

        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->unitEquals($unit)->willReturn(true);
        $temperatureMock->degrees()->willReturn($cityDegrees);

        $cityMock = $this->prophet->prophesize(City::class);
        $cityMock->temperature()->willReturn($temperatureMock->reveal());

        $warmerChecker = new WarmerThanRivalChecker(null, $this->rivalMock->reveal());

        $result = $warmerChecker->check($cityMock->reveal());

        $this->assertIsArray($result);
        $expectedArrayCount = 1;
        $this->assertEquals($expectedArrayCount, count($result));
        $this->assertInstanceOf(CheckReport::class, $result[0]);
        $this->assertEquals($expectedStatus, $result[0]->status());
    }

    public function warmerCheckerDataProvider()
    {
        return [
            'is warmer' => [10, 11, true],
            'is not warmer' => [10, 9, false],
            'equal degrees' => [10, 10, false]
        ];
    }

    public function warmerCheckerUnitNotMatch()
    {
        $unit = TemperatureUnit::CELSIUS;

        $rivalTemperatureMock = $this->prophet->prophesize(Temperature::class);
        $rivalTemperatureMock->unitValue()->willReturn($unit);
        $this->rivalMock->temperature()->willReturn($rivalTemperatureMock->reveal());

        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->unitEquals($unit)->willReturn(false);

        $cityMock = $this->prophet->prophesize(City::class);
        $cityMock->temperature()->willReturn($temperatureMock->reveal());

        $warmerChecker = new WarmerThanRivalChecker(null, $this->rivalMock->reveal());

        $this->expectException(TemperaturesUnitNotMatchException::class);
        $warmerChecker->check($cityMock->reveal());
    }

    public function testWarmerCheckerWithParent()
    {
        $unit = TemperatureUnit::CELSIUS;

        $rivalTemperatureMock = $this->prophet->prophesize(Temperature::class);
        $rivalTemperatureMock->unitValue()->willReturn($unit);
        $rivalTemperatureMock->degrees()->willReturn(10);
        $this->rivalMock->temperature()->willReturn($rivalTemperatureMock->reveal());

        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->unitEquals($unit)->willReturn(true);
        $temperatureMock->degrees()->willReturn(20);

        $cityMock = $this->prophet->prophesize(City::class);
        $cityMock->temperature()->willReturn($temperatureMock->reveal());

        $oddChecker = $this->prophet->prophesize(OddNameLengthChecker::class);
        $checkReportMock = $this->prophet->prophesize(CheckReport::class);
        $oddChecker->check($cityMock)->willReturn([$checkReportMock]);

        $warmerChecker = new WarmerThanRivalChecker($oddChecker->reveal(), $this->rivalMock->reveal());

        $result = $warmerChecker->check($cityMock->reveal());
        $this->assertIsArray($result);
        $expectedArrayCount = 2;
        $this->assertEquals($expectedArrayCount, count($result));
        $this->assertInstanceOf(CheckReport::class, $result[0]);
        $this->assertInstanceOf(CheckReport::class, $result[1]);
    }
}
