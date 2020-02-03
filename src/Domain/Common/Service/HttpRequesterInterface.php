<?php

namespace App\Domain\Common\Service;

interface HttpRequesterInterface
{
    public function get(string $endPoint);
}