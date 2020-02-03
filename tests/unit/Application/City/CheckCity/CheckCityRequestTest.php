<?php

namespace App\Tests\unit\Application\City\CheckCity;

use App\Application\City\Exception\InvalidCheckCityRequestArgumentException;
use App\Application\City\Service\CheckCity\CheckCityRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class CheckCityRequestTest extends TestCase
{
    private $prophet;

    protected function setUp()
    {
        $this->prophet = new Prophet();
    }

    public function testNewValid()
    {
        $request = new CheckCityRequest('Berlin');
        $this->assertInstanceOf(CheckCityRequest::class, $request);
    }

    /**
     * @dataProvider newInvalidDataProvider
     */
    public function testNewInvalid($name)
    {
        $this->expectException(InvalidCheckCityRequestArgumentException::class);
        $request = new CheckCityRequest($name);
    }

    public function newInvalidDataProvider()
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'string 0' => ['0'],
        ];
    }

    public function testCityName()
    {
        $name = 'Berlin';
        $request = new CheckCityRequest($name);

        $this->assertEquals($name, $request->cityName());
    }
}
