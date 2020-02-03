<?php

namespace App\Infrastructure\City\Controller;

use App\Application\City\Service\CheckCity\CheckCityRequest;
use App\Application\City\Service\CheckCity\CheckCityService;
use App\Infrastructure\Common\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCityController extends AbstractController
{
    private $checkCityService;

    public function __construct(CheckCityService $checkCityService)
    {
        $this->checkCityService = $checkCityService;
    }

    public function index(Request $request)
    {
        try {
            $response = $this->checkCityService->execute(new CheckCityRequest($request->query->get('city')));
            return $this->sendResponse([
                'check' => $response->check(),
                'criteria' => $response->criteria()
            ]);

        } catch (\Exception $exception) {
            return $this->sendResponse('error=true', Response::HTTP_BAD_REQUEST);
        }
    }
}