<?php

namespace App\Tests\unit\Application\City\CheckCity;

use App\Application\City\Service\CheckCity\CheckCityResponse;
use App\Domain\City\Service\CheckReport;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class CheckCityResponseTest extends TestCase
{
    private $prophet;
    private $check;
    private $criteria;
    private $checkCityResponse;

    protected function setUp()
    {
        $this->prophet = new Prophet();
        $this->check = true;
        $this->criteria = ['alias' => true];
        $this->checkCityResponse = new CheckCityResponse($this->check, $this->criteria);
    }

    public function testCheck()
    {
        $result = $this->checkCityResponse->check();
        $this->assertEquals($this->check, $result);
    }

    public function testCriteria()
    {
        $result = $this->checkCityResponse->criteria();
        $this->assertIsArray($result);
        $this->assertEquals(1, count($result));
        $key = 'alias';
        $this->assertTrue(isset($result[$key]));
        $this->assertEquals($this->criteria[$key], $result[$key]);
    }

    /**
     * @dataProvider staticFromCheckReportsDataProvider
     */
    public function testStaticFromCheckReports(
        $reportAliasOne,
        $reportStatusOne,
        $reportAliasTwo,
        $reportStatusTwo,
        $expectedCheck
    ) {
        $checkReportMockOne = $this->prophet->prophesize(CheckReport::class);
        $checkReportMockOne->name()->willReturn($reportAliasOne);
        $checkReportMockOne->status()->willReturn($reportStatusOne);

        $checkReportMockTwo = $this->prophet->prophesize(CheckReport::class);
        $checkReportMockTwo->name()->willReturn($reportAliasTwo);
        $checkReportMockTwo->status()->willReturn($reportStatusTwo);

        $result = CheckCityResponse::fromCheckReports([$checkReportMockOne->reveal(), $checkReportMockTwo->reveal()]);
        $this->assertEquals($expectedCheck, $result->check());

        $this->assertIsArray($result->criteria());
        $criteriaLength = 2;
        $this->assertEquals($criteriaLength, count($result->criteria()));

        $this->assertTrue(isset($result->criteria()[$reportAliasOne]));
        $this->assertEquals($reportStatusOne, $result->criteria()[$reportAliasOne]);

        $this->assertTrue(isset($result->criteria()[$reportAliasTwo]));
        $this->assertEquals($reportStatusTwo, $result->criteria()[$reportAliasTwo]);
    }

    public function staticFromCheckReportsDataProvider()
    {
        return [
            'both status true' => ['one', true, 'two', true, true],
            'both status false' => ['one', false, 'two', false, false],
            'one false and two true' => ['one', false, 'two', true, false]
        ];
    }
}
