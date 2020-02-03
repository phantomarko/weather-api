<?php

namespace App\Tests\unit\Application\City\CheckCity;

use App\Application\City\Exception\CheckCityCriteriaEmptyException;
use App\Application\City\Service\CheckCity\CheckCityRequest;
use App\Application\City\Service\CheckCity\CheckCityResponse;
use App\Application\City\Service\CheckCity\CheckCityService;
use App\Domain\City\Entity\City;
use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\City\Service\CheckerFactory;
use App\Domain\City\Service\CheckReport;
use App\Domain\City\Service\OddNameLengthChecker;
use App\Domain\City\Service\TemperatureBetweenValuesChecker;
use App\Domain\City\Service\WarmerThanRivalChecker;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class CheckCityServiceTest extends TestCase
{
    private $prophet;
    private $cityRepositoryMock;
    private $checkerFactoryMock;

    protected function setUp()
    {
        $this->prophet = new Prophet();
        $this->cityRepositoryMock = $this->prophet->prophesize(CityRepositoryInterface::class);
        $this->checkerFactoryMock = $this->prophet->prophesize(CheckerFactory::class);
    }

    public function testExecuteCityNotFound()
    {
        $cityName = 'not found';
        $this->cityRepositoryMock->findCityByName($cityName)->willReturn(null);

        $service = new CheckCityService($this->cityRepositoryMock->reveal(), [], $this->checkerFactoryMock->reveal());
        $checkCityRequestMock = $this->prophet->prophesize(CheckCityRequest::class);
        $checkCityRequestMock->cityName()->willReturn($cityName);

        $this->expectException(CityNotFoundException::class);
        $service->execute($checkCityRequestMock->reveal());
    }

    public function testExecuteEmptyCriteriaToCheck()
    {
        $cityName = 'not found';
        $cityMock = $this->prophet->prophesize(City::class);
        $this->cityRepositoryMock->findCityByName($cityName)->willReturn($cityMock);

        $service = new CheckCityService($this->cityRepositoryMock->reveal(), [], $this->checkerFactoryMock->reveal());
        $checkCityRequestMock = $this->prophet->prophesize(CheckCityRequest::class);
        $checkCityRequestMock->cityName()->willReturn($cityName);

        $this->expectException(CheckCityCriteriaEmptyException::class);
        $service->execute($checkCityRequestMock->reveal());
    }

    public function testExecuteAllCriteriaToCheckNotActive()
    {
        $cityName = 'not found';
        $cityMock = $this->prophet->prophesize(City::class);
        $this->cityRepositoryMock->findCityByName($cityName)->willReturn($cityMock);

        $criteriaToCheck = [
            ['alias' => 'one', 'active' => false],
            ['alias' => 'two', 'active' => false],
            ['alias' => 'three', 'active' => false]
        ];
        $service = new CheckCityService($this->cityRepositoryMock->reveal(), $criteriaToCheck, $this->checkerFactoryMock->reveal());
        $checkCityRequestMock = $this->prophet->prophesize(CheckCityRequest::class);
        $checkCityRequestMock->cityName()->willReturn($cityName);

        $this->expectException(CheckCityCriteriaEmptyException::class);
        $service->execute($checkCityRequestMock->reveal());
    }

    public function testExecuteAllCriteriaToCheckActive()
    {
        $cityName = 'not found';
        $cityMock = $this->prophet->prophesize(City::class);
        $this->cityRepositoryMock->findCityByName($cityName)->willReturn($cityMock->reveal());

        $aliasOne = 'one';
        $aliasTwo = 'two';
        $aliasThree = 'three';
        $criteriaToCheck = [
            ['alias' => $aliasOne, 'active' => true],
            ['alias' => $aliasTwo, 'active' => true],
            ['alias' => $aliasThree, 'active' => true]
        ];

        $oddCheckerMock = $this->prophet->prophesize(OddNameLengthChecker::class);
        $this->checkerFactoryMock->getCheckerByAlias($aliasOne, null)->willReturn($oddCheckerMock->reveal());

        $tempCheckerMock = $this->prophet->prophesize(TemperatureBetweenValuesChecker::class);
        $this->checkerFactoryMock->getCheckerByAlias($aliasTwo, $oddCheckerMock->reveal())->willReturn($tempCheckerMock->reveal());

        $warmerCheckerMock = $this->prophet->prophesize(WarmerThanRivalChecker::class);

        $reportStatus = true;
        $checkReportOne = $this->prophet->prophesize(CheckReport::class);
        $checkReportOne->name()->willReturn($aliasOne);
        $checkReportOne->status()->willReturn($reportStatus);
        $checkReportTwo = $this->prophet->prophesize(CheckReport::class);
        $checkReportTwo->name()->willReturn($aliasTwo);
        $checkReportTwo->status()->willReturn($reportStatus);
        $checkReportThree = $this->prophet->prophesize(CheckReport::class);
        $checkReportThree->name()->willReturn($aliasThree);
        $checkReportThree->status()->willReturn($reportStatus);
        $warmerCheckerMock->check($cityMock)->willreturn([$checkReportOne, $checkReportTwo, $checkReportThree]);
        $this->checkerFactoryMock->getCheckerByAlias($aliasThree, $tempCheckerMock->reveal())->willReturn($warmerCheckerMock->reveal());

        $service = new CheckCityService($this->cityRepositoryMock->reveal(), $criteriaToCheck, $this->checkerFactoryMock->reveal());

        $checkCityRequestMock = $this->prophet->prophesize(CheckCityRequest::class);
        $checkCityRequestMock->cityName()->willReturn($cityName);

        $result = $service->execute($checkCityRequestMock->reveal());

        $this->assertInstanceOf(CheckCityResponse::class, $result);

        $responseCriteriaCount = 3;
        $this->assertEquals($responseCriteriaCount, count($result->criteria()));

        $this->assertTrue(isset($result->criteria()[$aliasOne]));
        $this->assertEquals($reportStatus, $result->criteria()[$aliasOne]);
        $this->assertTrue(isset($result->criteria()[$aliasTwo]));
        $this->assertEquals($reportStatus, $result->criteria()[$aliasTwo]);
        $this->assertTrue(isset($result->criteria()[$aliasThree]));
        $this->assertEquals($reportStatus, $result->criteria()[$aliasThree]);
    }

    public function testExecuteAllCriteriaToCheckBothActivePossibilities()
    {
        $cityName = 'not found';
        $cityMock = $this->prophet->prophesize(City::class);
        $this->cityRepositoryMock->findCityByName($cityName)->willReturn($cityMock->reveal());

        $aliasOne = 'one';
        $aliasTwo = 'two';
        $aliasThree = 'three';
        $criteriaToCheck = [
            ['alias' => $aliasOne, 'active' => true],
            ['alias' => $aliasTwo, 'active' => false],
            ['alias' => $aliasThree, 'active' => true]
        ];

        $oddCheckerMock = $this->prophet->prophesize(OddNameLengthChecker::class);
        $this->checkerFactoryMock->getCheckerByAlias($aliasOne, null)->willReturn($oddCheckerMock->reveal());

        $warmerCheckerMock = $this->prophet->prophesize(WarmerThanRivalChecker::class);

        $reportStatus = true;
        $checkReportOne = $this->prophet->prophesize(CheckReport::class);
        $checkReportOne->name()->willReturn($aliasOne);
        $checkReportOne->status()->willReturn($reportStatus);
        $checkReportThree = $this->prophet->prophesize(CheckReport::class);
        $checkReportThree->name()->willReturn($aliasThree);
        $checkReportThree->status()->willReturn($reportStatus);
        $warmerCheckerMock->check($cityMock)->willreturn([$checkReportOne, $checkReportThree]);
        $this->checkerFactoryMock->getCheckerByAlias($aliasThree, $oddCheckerMock->reveal())->willReturn($warmerCheckerMock->reveal());

        $service = new CheckCityService($this->cityRepositoryMock->reveal(), $criteriaToCheck, $this->checkerFactoryMock->reveal());

        $checkCityRequestMock = $this->prophet->prophesize(CheckCityRequest::class);
        $checkCityRequestMock->cityName()->willReturn($cityName);

        $result = $service->execute($checkCityRequestMock->reveal());

        $this->assertInstanceOf(CheckCityResponse::class, $result);

        $responseCriteriaCount = 2;
        $this->assertEquals($responseCriteriaCount, count($result->criteria()));

        $this->assertTrue(isset($result->criteria()[$aliasOne]));
        $this->assertEquals($reportStatus, $result->criteria()[$aliasOne]);
        $this->assertTrue(isset($result->criteria()[$aliasThree]));
        $this->assertEquals($reportStatus, $result->criteria()[$aliasThree]);
    }
}
