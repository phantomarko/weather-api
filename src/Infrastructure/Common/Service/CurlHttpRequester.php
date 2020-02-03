<?php

namespace App\Infrastructure\Common\Service;

use App\Domain\Common\Service\HttpRequesterInterface;

class CurlHttpRequester implements HttpRequesterInterface
{
    public function get(string $endPoint)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }
}