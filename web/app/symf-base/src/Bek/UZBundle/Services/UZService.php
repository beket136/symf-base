<?php
/**
 * Created by PhpStorm.
 * User: sbeke
 * Date: 11/24/17
 * Time: 3:43 PM
 */

namespace Bek\UZBundle\Services;

use GuzzleHttp\Client;


class UZService
{
    private $apiMainPoint = 'https://booking.uz.gov.ua/en/purchase/station';

    function __construct($baseUrl)
    {
        $this->client = new \GuzzleHttp\Client(['base_uri' => $baseUrl]);
    }

    function getStationsByTerm(string $term)
    {

        $res = $this->client->request('GET', '/en/purchase/station/?term=' . $term);

        return $res->getBody();
    }

    function matchTerm($term, $terms)
    {

    }

}