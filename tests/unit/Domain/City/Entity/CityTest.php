<?php

namespace App\Tests\unit\Domain\City\Entity;

use App\Domain\City\Entity\City;
use App\Domain\Common\ValueObject\Temperature;
use DateTime;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class CityTest extends TestCase
{
    private $prophet;
    private $id;
    private $name;
    private $temperatureMock;
    private $sunriseDateTimeMock;
    private $sunsetDateTimeMock;
    private $city;

    protected function setUp()
    {
        $this->prophet = new Prophet();
        $this->id = 123;
        $this->name = 'Berlin';
        $this->temperatureMock = $this->prophet->prophesize(Temperature::class);
        $this->sunriseDateTimeMock = $this->prophet->prophesize(DateTime::class);
        $this->sunsetDateTimeMock = $this->prophet->prophesize(DateTime::class);

        $this->city = new City(
            $this->id,
            $this->name,
            $this->temperatureMock->reveal(),
            $this->sunriseDateTimeMock->reveal(),
            $this->sunsetDateTimeMock->reveal()
        );
    }

    public function testId()
    {
        $result = $this->city->id();

        $this->assertEquals($this->id, $result);
    }

    public function testName()
    {
        $result = $this->city->name();

        $this->assertEquals($this->name, $result);
    }

    public function testTemperature()
    {
        $result = $this->city->temperature();

        $this->assertInstanceOf(Temperature::class, $result);
    }

    /**
     * @dataProvider isDayTimeDataProvider
     */
    public function testIsDayTime($givenTimestamp, $sunriseTimestamp, $sunsetTimestamp, $boolExpected)
    {
        $this->sunriseDateTimeMock->getTimestamp()->willReturn($sunriseTimestamp);
        $this->sunsetDateTimeMock->getTimestamp()->willReturn($sunsetTimestamp);
        $givenDateTimeMock = $this->prophet->prophesize(DateTime::class);
        $givenDateTimeMock->getTimestamp()->willReturn($givenTimestamp);

        $result = $this->city->isDayTime($givenDateTimeMock->reveal());
        $this->assertEquals($boolExpected, $result);
    }

    public function isDayTimeDataProvider()
    {
        return [
            'between sunrise and sunset' => [1572256800, 1572224400, 1572303600, true],
            'equal sunrise' => [1572224400, 1572224400, 1572303600, true],
            'not between sunrise and sunset' => [1572177600, 1572224400, 1572303600, false]
        ];
    }
}
