<?php

namespace App\Tests\unit\Domain\City\Service;

use App\Domain\City\Service\CheckReport;
use PHPUnit\Framework\TestCase;

class CheckReportTest extends TestCase
{
    public function testName()
    {
        $name = 'test';
        $checkReport = new CheckReport($name, true);

        $result = $checkReport->name();
        $this->assertEquals($name, $result);
    }

    /**
     * @dataProvider statusDataProvider
     */
    public function testStatus($checkReportStatus, $boolExpected)
    {
        $checkReport = new CheckReport('test', $checkReportStatus);

        $result = $checkReport->status();
        $this->assertEquals($boolExpected, $result);
    }

    public function statusDataProvider()
    {
        return [
            'status is true' => [true, true],
            'status is false' => [false, false]
        ];
    }
}
