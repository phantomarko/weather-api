<?php

namespace App\Tests\unit\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\City\Service\CheckReport;
use App\Domain\City\Service\OddNameLengthChecker;
use App\Domain\City\Service\TemperatureBetweenValuesChecker;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class OddNameLengthCheckerTest extends TestCase
{
    private $prophet;

    protected function setUp()
    {
        $this->prophet = new Prophet();
    }

    /**
     * @dataProvider checkWithoutParentDataProvider
     */
    public function testCheckWithoutParent($name, $expectedStatus)
    {
        $oddChecker = new OddNameLengthChecker();
        $cityMock = $this->prophet->prophesize(City::class);
        $cityMock->name()->willReturn($name);

        $result = $oddChecker->check($cityMock->reveal());
        $this->assertIsArray($result);
        $expectedArrayCount = 1;
        $this->assertEquals($expectedArrayCount, count($result));
        $this->assertInstanceOf(CheckReport::class, $result[0]);
        $this->assertEquals($expectedStatus, $result[0]->status());
    }

    public function checkWithoutParentDataProvider()
    {
        return [
            'odd name' => ['Hamburg', true],
            'not odd name' => ['Berlin', false]
        ];
    }

    public function testCheckWithParent()
    {
        $temperatureChecker = $this->prophet->prophesize(TemperatureBetweenValuesChecker::class);
        $oddChecker = new OddNameLengthChecker($temperatureChecker->reveal());
        $cityMock = $this->prophet->prophesize(City::class);
        $cityMock->name()->willReturn('test');
        $checkReportMock = $this->prophet->prophesize(CheckReport::class);
        $temperatureChecker->check($cityMock)->willReturn([$checkReportMock]);

        $result = $oddChecker->check($cityMock->reveal());
        $this->assertIsArray($result);
        $expectedArrayCount = 2;
        $this->assertEquals($expectedArrayCount, count($result));
        $this->assertInstanceOf(CheckReport::class, $result[0]);
        $this->assertInstanceOf(CheckReport::class, $result[1]);
    }
}
