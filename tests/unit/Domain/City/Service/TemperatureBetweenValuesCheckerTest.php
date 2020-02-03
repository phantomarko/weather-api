<?php

namespace App\Tests\unit\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\City\Service\CheckReport;
use App\Domain\City\Service\OddNameLengthChecker;
use App\Domain\City\Service\TemperatureBetweenValuesChecker;
use App\Domain\Common\Exception\TemperaturesUnitNotMatchException;
use App\Domain\Common\ValueObject\Temperature;
use App\Domain\Common\ValueObject\TemperatureUnit;
use DateTime;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class TemperatureBetweenValuesCheckerTest extends TestCase
{
    private $prophet;

    protected function setUp()
    {
        $this->prophet = new Prophet();
    }

    /**
     * @dataProvider temperatureCheckerWithoutParentIsCelsiusDataProvider
     */
    public function testTemperatureCheckerWithoutParentIsCelsius(
        $minimumDegreess,
        $greatherThanMinimum,
        $maximumDegrees,
        $lessThanMaximum,
        $isDayTime,
        $expectedStatus
    ) {
        $temperatureChecker = new TemperatureBetweenValuesChecker();

        $cityMock = $this->prophet->prophesize(City::class);
        $temperatureMock = $this->prophet->prophesize(Temperature::class);

        $temperatureMock->unitEquals(TemperatureUnit::CELSIUS)->willReturn(true);

        $minimumTemperature = new Temperature($minimumDegreess, new TemperatureUnit(TemperatureUnit::CELSIUS));
        $temperatureMock->greaterThanOrEqual($minimumTemperature)->willReturn($greatherThanMinimum);

        $maximumTemperature = new Temperature($maximumDegrees, new TemperatureUnit(TemperatureUnit::CELSIUS));
        $temperatureMock->lessThanOrEqual($maximumTemperature)->willReturn($lessThanMaximum);

        $cityMock->temperature()->willReturn($temperatureMock->reveal());

        $cityMock->isDayTime(DateTime::createFromFormat('U', time()))->willReturn($isDayTime);
        $cityMock->isDayTime(DateTime::createFromFormat('U', time()))->willReturn($isDayTime);

        $result = $temperatureChecker->check($cityMock->reveal());

        $this->assertIsArray($result);
        $expectedArrayCount = 1;
        $this->assertEquals($expectedArrayCount, count($result));
        $this->assertInstanceOf(CheckReport::class, $result[0]);
        $this->assertEquals($expectedStatus, $result[0]->status());
    }

    public function temperatureCheckerWithoutParentIsCelsiusDataProvider()
    {
        return [
            'day time and between temperatures' => [17, true, 25, true, true, true],
            'night time and between temperatures' => [10, true, 15, true, false, true],
            'night time and not between temperatures' => [10, false, 15, false, false, false],
            'day time and not between temperatures' => [17, false, 25, false, true, false]
        ];
    }

    public function testTemperatureCheckerUnitNotMatch()
    {
        $temperatureChecker = new TemperatureBetweenValuesChecker();

        $cityMock = $this->prophet->prophesize(City::class);
        $temperatureMock = $this->prophet->prophesize(Temperature::class);
        $temperatureMock->unitEquals(TemperatureUnit::CELSIUS)->willReturn(false);
        $cityMock->temperature()->willReturn($temperatureMock->reveal());

        $this->expectException(TemperaturesUnitNotMatchException::class);
        $temperatureChecker->check($cityMock->reveal());
    }

    public function testCheckWithParent()
    {
        $oddChecker = $this->prophet->prophesize(OddNameLengthChecker::class);
        $temperatureChecker = new TemperatureBetweenValuesChecker($oddChecker->reveal());

        $cityMock = $this->prophet->prophesize(City::class);
        $temperatureMock = $this->prophet->prophesize(Temperature::class);

        $temperatureMock->unitEquals(TemperatureUnit::CELSIUS)->willReturn(true);

        $minimumTemperature = new Temperature(17, new TemperatureUnit(TemperatureUnit::CELSIUS));
        $temperatureMock->greaterThanOrEqual($minimumTemperature)->willReturn(true);

        $maximumTemperature = new Temperature(25, new TemperatureUnit(TemperatureUnit::CELSIUS));
        $temperatureMock->lessThanOrEqual($maximumTemperature)->willReturn(true);

        $cityMock->temperature()->willReturn($temperatureMock->reveal());

        $cityMock->isDayTime(DateTime::createFromFormat('U', time()))->willReturn(true);
        $cityMock->isDayTime(DateTime::createFromFormat('U', time()))->willReturn(true);

        $checkReportMock = $this->prophet->prophesize(CheckReport::class);
        $oddChecker->check($cityMock)->willReturn([$checkReportMock]);
        $result = $temperatureChecker->check($cityMock->reveal());

        $this->assertIsArray($result);
        $expectedArrayCount = 2;
        $this->assertEquals($expectedArrayCount, count($result));
        $this->assertInstanceOf(CheckReport::class, $result[0]);
        $this->assertInstanceOf(CheckReport::class, $result[1]);
    }
}
