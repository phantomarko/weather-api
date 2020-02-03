<?php

namespace App\Infrastructure\Common\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController
{
    protected function sendResponse($data = null, int $code = Response::HTTP_OK)
    {
        return new JsonResponse($data, $code);
    }
}